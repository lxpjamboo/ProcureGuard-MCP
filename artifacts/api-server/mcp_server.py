"""Deterministic, local FastMCP tools for the ProcureGuard sample dashboard.

The stdio transport keeps the tool server private to the API service. The
Express endpoint starts one local MCP session per audit request.
"""

from __future__ import annotations

import re
import sqlite3
import json
import unicodedata
from typing import Any, Literal

from fastmcp import FastMCP
from pydantic import BaseModel, ConfigDict, Field


mcp = FastMCP(
    "ProcureGuard Context Layer",
    instructions=(
        "Local deterministic tools for the ProcureGuard sample dashboard. "
        "All tender records, benchmarks, and indicators are illustrative demo "
        "data, not official procurement findings."
    ),
)

SPEC_CATALOG: dict[str, dict[str, Any]] = {
    "REF: CP-2026-WTR-042": {
        "sourceRequirementCount": 14,
        "requirements": [
            {
                "itemName": "Submersible Pump 7.5KW Heavy Duty",
                "minimumSpecification": "Stainless Steel AISI 316",
            },
            {
                "itemName": "Solar PV Panels 550W Monocrystalline",
                "minimumSpecification": "Tier-1 25-year warranty",
            },
            {
                "itemName": "Hydro-geological Survey & Drilling 200m",
                "minimumSpecification": "Certified hydrologist",
            },
            {
                "itemName": "10,000L Elevated Steel Storage Tank",
                "minimumSpecification": "Galvanized Steel ISO 9001",
            },
        ],
    },
    "REF: HLTH-2026-KIT-019": {
        "sourceRequirementCount": 20,
        "requirements": [
            {
                "itemName": "Surgical Gloves Powder-Free Box",
                "minimumSpecification": "ISO 13485 certified",
            },
            {
                "itemName": "Sterile Gauze Rolls 100m Pack",
                "minimumSpecification": "100% absorbent cotton",
            },
            {
                "itemName": "Amoxicillin 500mg Capsules (1000s)",
                "minimumSpecification": "GMP certified batch",
            },
        ],
    },
    "REF: PWRK-2026-RD-112": {
        "sourceRequirementCount": 25,
        "requirements": [
            {
                "itemName": "Gravel Wearing Course Compaction (m3)",
                "minimumSpecification": "CBR greater than 30%",
            },
            {
                "itemName": "Precast Concrete Box Culverts 900mm",
                "minimumSpecification": "Class 25 concrete",
            },
        ],
    },
}

BENCHMARK_ROWS = [
    ("Submersible Pump 7.5KW Heavy Duty", 216000.0, "KES", "each"),
    ("Solar PV Panels 550W Monocrystalline", 28000.0, "KES", "each"),
    ("Hydro-geological Survey & Drilling 200m", 1300000.0, "KES", "lot"),
    ("10,000L Elevated Steel Storage Tank", 850000.0, "KES", "each"),
    ("Surgical Gloves Powder-Free Box", 950.0, "KES", "box"),
    ("Sterile Gauze Rolls 100m Pack", 2500.0, "KES", "pack"),
    ("Amoxicillin 500mg Capsules (1000s)", 2900.0, "KES", "pack"),
    ("Gravel Wearing Course Compaction (m3)", 1380.0, "KES", "m3"),
    ("Precast Concrete Box Culverts 900mm", 22500.0, "KES", "each"),
]
BENCHMARK_SOURCE = "local sample benchmark catalog supplied with the demo"


def _build_benchmark_database() -> sqlite3.Connection:
    connection = sqlite3.connect(":memory:", check_same_thread=False)
    connection.row_factory = sqlite3.Row
    connection.execute(
        """
        CREATE TABLE market_benchmarks (
            item_key TEXT PRIMARY KEY,
            item_name TEXT NOT NULL,
            benchmark_price REAL NOT NULL CHECK (benchmark_price > 0),
            currency TEXT NOT NULL,
            unit TEXT NOT NULL,
            source TEXT NOT NULL
        )
        """
    )
    connection.executemany(
        """
        INSERT INTO market_benchmarks
            (item_key, item_name, benchmark_price, currency, unit, source)
        VALUES (?, ?, ?, ?, ?, ?)
        """,
        [
            (
                _normalize(item_name),
                item_name,
                price,
                currency,
                unit,
                BENCHMARK_SOURCE,
            )
            for item_name, price, currency, unit in BENCHMARK_ROWS
        ],
    )
    connection.commit()
    return connection


def _normalize(value: str) -> str:
    normalized = unicodedata.normalize("NFKC", value).casefold()
    return re.sub(r"[^a-z0-9]+", " ", normalized).strip()


market_benchmark_db = _build_benchmark_database()


class BidItem(BaseModel):
    model_config = ConfigDict(extra="forbid")

    itemName: str = Field(min_length=1, max_length=200)
    bidPrice: float = Field(gt=0, allow_inf_nan=False)
    currency: str = Field(min_length=1, max_length=8)
    unit: str | None = Field(default=None, max_length=64)


class BidRecord(BaseModel):
    model_config = ConfigDict(extra="forbid")

    bidderName: str = Field(min_length=1, max_length=200)
    directorPins: list[str] = Field(default_factory=list, max_length=40)
    taxRegistration: str | None = Field(default=None, max_length=120)
    metadata: dict[str, Any] = Field(default_factory=dict)


@mcp.tool
def parse_tender_specs(rfq_id: str) -> dict[str, Any]:
    """Return the locally stored sample requirements for an RFQ reference."""
    record = SPEC_CATALOG.get(rfq_id)
    if record is None:
        raise ValueError("RFQ reference is not present in the local sample catalog.")

    requirements = record["requirements"]
    return {
        "rfqId": rfq_id,
        "source": "local sample specification catalog",
        "sampleData": True,
        "displayedRequirementCount": len(requirements),
        "sourceRequirementCount": record["sourceRequirementCount"],
        "requirements": requirements,
        "note": (
            "These are the line items included in the supplied dashboard sample. "
            "No tender document was parsed."
        ),
    }


@mcp.tool
def audit_price_drift(bid_items: list[BidItem]) -> dict[str, Any]:
    """Compare submitted sample bid item prices to the local SQLite benchmark catalog."""
    findings: list[dict[str, Any]] = []
    for item in bid_items:
        benchmark = market_benchmark_db.execute(
            """
            SELECT item_name, benchmark_price, currency, unit, source
            FROM market_benchmarks
            WHERE item_key = ?
            """,
            (_normalize(item.itemName),),
        ).fetchone()

        if benchmark is None:
            findings.append(
                {
                    "itemName": item.itemName,
                    "bidPrice": item.bidPrice,
                    "benchmarkPrice": None,
                    "currency": item.currency,
                    "driftPercent": None,
                    "baselineFound": False,
                    "unit": item.unit,
                    "benchmarkSource": None,
                    "note": "No matching local benchmark is configured for this item.",
                }
            )
            continue

        if item.currency.casefold() != benchmark["currency"].casefold():
            findings.append(
                {
                    "itemName": item.itemName,
                    "bidPrice": item.bidPrice,
                    "benchmarkPrice": None,
                    "currency": item.currency,
                    "driftPercent": None,
                    "baselineFound": False,
                    "unit": item.unit or benchmark["unit"],
                    "benchmarkSource": benchmark["source"],
                    "note": (
                        "Currency does not match the local benchmark; no exchange "
                        "rate is configured."
                    ),
                }
            )
            continue

        drift_percent = (
            (item.bidPrice - benchmark["benchmark_price"])
            / benchmark["benchmark_price"]
            * 100
        )
        findings.append(
            {
                "itemName": item.itemName,
                "bidPrice": item.bidPrice,
                "benchmarkPrice": benchmark["benchmark_price"],
                "currency": benchmark["currency"],
                "driftPercent": round(drift_percent, 2),
                "baselineFound": True,
                "unit": item.unit or benchmark["unit"],
                "benchmarkSource": benchmark["source"],
                "note": None,
            }
        )

    return {
        "currency": "KES",
        "sampleData": True,
        "items": findings,
    }


@mcp.tool
def flag_collusion_risk(bids: list[BidRecord]) -> dict[str, Any]:
    """Find exact shared identifiers and repeated pricing-model metadata in sample bids."""
    indicators: list[dict[str, Any]] = []

    def add_duplicate_groups(
        kind: Literal[
            "shared_director_pin",
            "shared_tax_registration",
            "identical_pricing_model",
        ],
        groups: dict[str, set[str]],
        matched_field: str,
        evidence: str,
    ) -> None:
        for normalized_value in sorted(groups):
            bidder_names = sorted(groups[normalized_value], key=str.casefold)
            if len(bidder_names) < 2:
                continue
            indicators.append(
                {
                    "kind": kind,
                    "bidderNames": bidder_names,
                    "matchedField": matched_field,
                    "evidence": evidence,
                }
            )

    director_groups: dict[str, set[str]] = {}
    tax_groups: dict[str, set[str]] = {}
    pricing_groups: dict[str, set[str]] = {}

    for bid in bids:
        bidder_name = bid.bidderName.strip()
        for pin in bid.directorPins:
            normalized_pin = _normalize(pin)
            if normalized_pin:
                director_groups.setdefault(normalized_pin, set()).add(bidder_name)

        if bid.taxRegistration:
            normalized_tax = _normalize(bid.taxRegistration)
            if normalized_tax:
                tax_groups.setdefault(normalized_tax, set()).add(bidder_name)

        for key, value in bid.metadata.items():
            normalized_key = _normalize(key).replace(" ", "_")
            compact_key = normalized_key.replace("_", "")
            if "pricingmodel" in compact_key:
                pricing_field = "pricing_model"
            elif "pricingmethod" in compact_key:
                pricing_field = "pricing_method"
            elif "pricingtemplate" in compact_key:
                pricing_field = "pricing_template"
            else:
                continue
            if value is None:
                continue
            canonical_value = (
                unicodedata.normalize("NFKC", value).strip().casefold()
                if isinstance(value, str)
                else json.dumps(
                    value,
                    sort_keys=True,
                    separators=(",", ":"),
                    ensure_ascii=False,
                )
            )
            normalized_value = _normalize(canonical_value)
            if normalized_value:
                pricing_groups.setdefault(
                    f"{pricing_field}:{normalized_value}", set()
                ).add(bidder_name)

    add_duplicate_groups(
        "shared_director_pin",
        director_groups,
        "directorPins",
        "An exact director PIN match was found; the identifier is redacted.",
    )
    add_duplicate_groups(
        "shared_tax_registration",
        tax_groups,
        "taxRegistration",
        "An exact tax-registration match was found; the identifier is redacted.",
    )
    add_duplicate_groups(
        "identical_pricing_model",
        pricing_groups,
        "pricing-model-related metadata",
        "Identical pricing-model metadata was found; the value is redacted.",
    )
    indicators.sort(
        key=lambda indicator: (
            indicator["kind"],
            tuple(name.casefold() for name in indicator["bidderNames"]),
            indicator["matchedField"],
        )
    )

    count = len(indicators)
    summary = (
        f"{count} exact-match indicator(s) found in the supplied sample bid data."
        if count
        else "No exact-match indicators found in the supplied sample bid data."
    )
    return {
        "sampleData": True,
        "manualReviewRequired": True,
        "indicators": indicators,
        "summary": summary,
    }


if __name__ == "__main__":
    mcp.run(transport="stdio")