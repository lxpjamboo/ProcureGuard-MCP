# mcp_server.py - ProcureGuard FastMCP Daemon with Local Air-Gapped Reasoning & Live Queries
import sqlite3
import urllib.request
import json
from fastmcp import FastMCP

mcp = FastMCP("ProcureGuard-Engine")

DB_PATH = "procureguard.db"

@mcp.tool()
def audit_price_drift(item_code: str, bidder_rate_kes: float) -> dict:
    """Calculates percentage price drift against SQLite local market baseline."""
    conn = sqlite3.connect(DB_PATH)
    cursor = conn.cursor()
    
    cursor.execute("SELECT unit_baseline_kes, description FROM market_baselines WHERE item_code = ?", (item_code,))
    row = cursor.fetchone()
    conn.close()

    if not row:
        return {"status": "UNKNOWN_ITEM", "drift_pct": 0.0}

    baseline_rate, description = row
    drift_pct = ((bidder_rate_kes - baseline_rate) / baseline_rate) * 100.0

    return {
        "item_code": item_code,
        "description": description,
        "bidder_rate_kes": bidder_rate_kes,
        "baseline_rate_kes": baseline_rate,
        "drift_pct": round(drift_pct, 2),
        "flagged": drift_pct > 15.0
    }

@mcp.tool()
def flag_collusion_risk(company_a: str, company_b: str) -> dict:
    """Cross-references corporate registry records for overlapping director PINs."""
    conn = sqlite3.connect(DB_PATH)
    cursor = conn.cursor()

    cursor.execute("SELECT kra_pin, director_identifier FROM corporate_registry WHERE company_name = ?", (company_a,))
    rec_a = cursor.fetchone()

    cursor.execute("SELECT kra_pin, director_identifier FROM corporate_registry WHERE company_name = ?", (company_b,))
    rec_b = cursor.fetchone()
    conn.close()

    if rec_a and rec_b and (rec_a[0] == rec_b[0] or rec_a[1] == rec_b[1]):
        return {
            "risk_detected": True,
            "type": "DIRECTOR_PIN_OVERLAP",
            "entity_a": company_a,
            "entity_b": company_b,
            "severity": "CRITICAL"
        }

    return {"risk_detected": False}

@mcp.tool()
def generate_statutory_brief(risk_score: int, max_drift: float, collusion_flagged: bool) -> dict:
    """Passes read-only FastMCP tool telemetry to local Ollama to synthesize a PPADA 2015 executive audit brief."""
    
    prompt = f"""
    You are an air-gapped public procurement compliance officer operating under Kenya's PPADA 2015 Section 114.
    Synthesize the following read-only FastMCP audit telemetry into a 2-sentence executive statutory brief for the Procurement Officer:

    Telemetry Data:
    - Composite Risk Score: {risk_score}/100
    - Max Price Drift: {max_drift}%
    - Collusion Risk Detected: {collusion_flagged}

    Instructions:
    Highlight the statutory violation (unit rate overcharge & corporate identity overlap) and explicitly state that the human officer must execute a final determination gate.
    """

    payload = json.dumps({
        "model": "qwen2.5-instruct:3b",
        "prompt": prompt,
        "stream": False
    }).encode('utf-8')

    try:
        req = urllib.request.Request(
            "http://localhost:11434/api/generate",
            data=payload,
            headers={"Content-Type": "application/json"}
        )
        with urllib.request.urlopen(req) as response:
            res_data = json.loads(response.read().decode('utf-8'))
            return {
                "status": "SUCCESS",
                "engine": "Local Ollama (Qwen 2.5 - Air Gapped)",
                "statutory_brief": res_data.get("response", "").strip()
            }
    except Exception as e:
        return {
            "status": "FALLBACK_MODE",
            "engine": "Deterministic Static Rule Engine",
            "statutory_brief": f"Critical price drift (+{max_drift}%) and shared corporate registry identifiers detected. Immediate escalation required under PPADA Section 114."
        }

@mcp.tool()
def get_all_envelopes() -> list:
    """Retrieves all submitted bid envelopes and their financial totals from SQLite."""
    conn = sqlite3.connect(DB_PATH)
    conn.row_factory = sqlite3.Row
    cursor = conn.cursor()
    cursor.execute("SELECT * FROM bid_envelopes")
    rows = cursor.fetchall()
    conn.close()
    return [dict(row) for row in rows]

@mcp.tool()
def get_drift_table() -> list:
    """Retrieves forensic price drift calculations and line item variances."""
    conn = sqlite3.connect(DB_PATH)
    conn.row_factory = sqlite3.Row
    cursor = conn.cursor()
    cursor.execute("SELECT * FROM forensic_drifts")
    rows = cursor.fetchall()
    conn.close()
    return [dict(row) for row in rows]

@mcp.tool()
def get_collusion_alerts() -> list:
    """Retrieves corporate registry records and collusion flags."""
    conn = sqlite3.connect(DB_PATH)
    conn.row_factory = sqlite3.Row
    cursor = conn.cursor()
    cursor.execute("SELECT * FROM corporate_registry")
    rows = cursor.fetchall()
    conn.close()
    return [dict(row) for row in rows]

if __name__ == "__main__":
    mcp.run(transport="stdio")