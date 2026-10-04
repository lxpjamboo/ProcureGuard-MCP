import { spawn } from "node:child_process";
import { fileURLToPath } from "node:url";
import { RunAuditBody, RunAuditResponse } from "@workspace/api-zod";
import { Router, type IRouter } from "express";

const router: IRouter = Router();
const runnerPath = fileURLToPath(new URL("../audit_runner.py", import.meta.url));
const runnerCwd = fileURLToPath(new URL("..", import.meta.url));
const MAX_CONCURRENT_AUDITS = 2;
const RUNNER_TIMEOUT_MS = 25_000;
let activeAudits = 0;

class AuditRunnerError extends Error {
  constructor(
    message: string,
    readonly statusCode: number,
  ) {
    super(message);
  }
}

function callLocalMcpRunner(request: unknown): Promise<unknown> {
  return new Promise((resolve, reject) => {
    const python = process.env["PYTHON_EXECUTABLE"] ?? "python3";
    const childEnv: NodeJS.ProcessEnv = {};
    for (const key of ["PATH", "HOME", "PYTHONPATH"]) {
      const value = process.env[key];
      if (value) childEnv[key] = value;
    }
    childEnv["PYTHONUNBUFFERED"] = "1";

    const child = spawn(python, ["-u", runnerPath], {
      cwd: runnerCwd,
      env: childEnv,
      stdio: ["pipe", "pipe", "pipe"],
    });

    let stdout = "";
    let settled = false;

    const finish = (callback: () => void) => {
      if (settled) return;
      settled = true;
      clearTimeout(timeout);
      callback();
    };

    const timeout = setTimeout(() => {
      child.kill("SIGKILL");
      finish(() =>
        reject(
          new AuditRunnerError(
            "The local MCP audit exceeded its execution limit.",
            504,
          ),
        ),
      );
    }, RUNNER_TIMEOUT_MS);

    child.stdout.setEncoding("utf8");
    child.stdout.on("data", (chunk: string) => {
      stdout += chunk;
      if (stdout.length > 1_000_000) {
        child.kill("SIGKILL");
        finish(() =>
          reject(
            new AuditRunnerError(
              "The local MCP audit response exceeded its size limit.",
              502,
            ),
          ),
        );
      }
    });
    child.stderr.resume();
    child.on("error", () => {
      finish(() =>
        reject(
          new AuditRunnerError(
            "The local Python MCP runtime could not be started.",
            503,
          ),
        ),
      );
    });
    child.on("close", (code) => {
      if (settled) return;
      let result: unknown;
      try {
        result = JSON.parse(stdout);
      } catch {
        finish(() =>
          reject(
            new AuditRunnerError(
              "The local MCP audit returned an unreadable response.",
              502,
            ),
          ),
        );
        return;
      }

      if (code !== 0) {
        const errorCode =
          typeof result === "object" &&
          result !== null &&
          "code" in result &&
          result.code === "UNKNOWN_TENDER";
        finish(() =>
          reject(
            new AuditRunnerError(
              errorCode
                ? "That tender is not in the local sample catalog."
                : "The local MCP audit tools could not complete.",
              errorCode ? 404 : 502,
            ),
          ),
        );
        return;
      }

      finish(() => resolve(result));
    });

    child.stdin.on("error", () => {
      child.kill("SIGTERM");
    });
    child.stdin.end(JSON.stringify(request));
  });
}

router.post("/audit", async (req, res): Promise<void> => {
  const parsed = RunAuditBody.safeParse(req.body);
  if (!parsed.success) {
    res.status(400).json({ error: "Invalid audit request." });
    return;
  }

  if (activeAudits >= MAX_CONCURRENT_AUDITS) {
    res.status(503).json({ error: "The local audit service is busy." });
    return;
  }

  activeAudits += 1;
  try {
    const rawReport = await callLocalMcpRunner(parsed.data);
    res.json(RunAuditResponse.parse(rawReport));
  } catch (error) {
    if (error instanceof AuditRunnerError) {
      if (error.statusCode >= 500) {
        req.log.error(
          { statusCode: error.statusCode },
          "Local MCP audit request failed",
        );
      }
      res.status(error.statusCode).json({ error: error.message });
      return;
    }

    req.log.error("Local MCP audit response did not match its contract");
    res.status(502).json({ error: "The local MCP audit tools could not complete." });
  } finally {
    activeAudits -= 1;
  }
});

export default router;