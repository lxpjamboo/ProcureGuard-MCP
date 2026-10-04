"""Run the ProcureGuard tool pipeline through a local MCP stdio session."""

from __future__ import annotations

import asyncio
import json
import sys
from datetime import datetime, timezone
from pathlib import Path
from typing import Any

from mcp import ClientSession, StdioServerParameters
from mcp.client.stdio import stdio_client


SERVER_PATH = Path(__file__).with_name("mcp_server.py")


class UnknownSampleTender(Exception):
    pass


def _contains_unknown_tender(error: BaseException) -> bool:
    if isinstance(error, UnknownSampleTender):
        return True
    if isinstance(error, BaseExceptionGroup):
        return any(_contains_unknown_tender(nested) for nested in error.exceptions)
    return False


def _tool_result_payload(result: Any) -> Any:
    if result.is_error:
        message = "Local MCP tool returned an error."
        for item in result.content:
            if getattr(item, "type", None) == "text":
                message = item.text
                break
        if "not present in the local sample catalog" in message:
            raise UnknownSampleTender()
        raise RuntimeError("Local MCP tool returned an error.")

    structured_content = result.structured_content
    if structured_content is not None:
        return structured_content

    for item in result.content:
        if getattr(item, "type", None) == "text":
            try:
                return json.loads(item.text)
            except json.JSONDecodeError:
                continue
    raise RuntimeError("Local MCP tool returned no structured result.")


async def run_audit(request: dict[str, Any]) -> dict[str, Any]:
    server = StdioServerParameters(
        command=sys.executable,
        args=["-u", str(SERVER_PATH)],
        cwd=str(SERVER_PATH.parent),
    )

    async with stdio_client(server) as (read_stream, write_stream):
        async with ClientSession(read_stream, write_stream) as session:
            await session.initialize()

            specs = _tool_result_payload(
                await session.call_tool(
                    "parse_tender_specs",
                    {"rfq_id": request["rfqId"]},
                )
            )
            price_drift = _tool_result_payload(
                await session.call_tool(
                    "audit_price_drift",
                    {
                        "bid_items": [
                            {
                                "itemName": item["itemName"],
                                "bidPrice": item["bidPrice"],
                                "currency": item["currency"],
                                "unit": item["unit"],
                            }
                            for item in request["bidItems"]
                        ]
                    },
                )
            )
            collusion_risk = _tool_result_payload(
                await session.call_tool(
                    "flag_collusion_risk",
                    {"bids": request["bids"]},
                )
            )

    return {
        "rfqId": request["rfqId"],
        "executedAt": datetime.now(timezone.utc).isoformat(),
        "demoMode": True,
        "toolCalls": [
            {
                "toolName": "parse_tender_specs",
                "status": "success",
                "resultCount": specs["displayedRequirementCount"],
            },
            {
                "toolName": "audit_price_drift",
                "status": "success",
                "resultCount": len(price_drift["items"]),
            },
            {
                "toolName": "flag_collusion_risk",
                "status": "success",
                "resultCount": len(collusion_risk["indicators"]),
            },
        ],
        "tenderSpecs": specs,
        "priceDrift": price_drift,
        "collusionRisk": collusion_risk,
        "notices": [
            "All inputs and outputs in this release are sample data.",
            "Benchmarks are local illustrative values, not current market research.",
            "Exact-match indicators require independent human verification and are not findings of collusion.",
            "This pipeline does not save, sign, or submit an audit record.",
        ],
    }


def _write_error(code: str, message: str) -> None:
    sys.stdout.write(json.dumps({"code": code, "error": message}) + "\n")
    sys.stdout.flush()


def main() -> None:
    try:
        request = json.load(sys.stdin)
        report = asyncio.run(run_audit(request))
    except UnknownSampleTender:
        _write_error(
            "UNKNOWN_TENDER",
            "RFQ reference is not present in the local sample catalog.",
        )
        raise SystemExit(2)
    except Exception as error:
        if _contains_unknown_tender(error):
            _write_error(
                "UNKNOWN_TENDER",
                "RFQ reference is not present in the local sample catalog.",
            )
            raise SystemExit(2)
        _write_error(
            "MCP_PIPELINE_FAILED",
            "The local MCP audit tools could not complete.",
        )
        raise SystemExit(1)

    sys.stdout.write(json.dumps(report, allow_nan=False) + "\n")
    sys.stdout.flush()


if __name__ == "__main__":
    main()