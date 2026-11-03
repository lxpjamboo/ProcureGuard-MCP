# SKILL: ProcureGuard MCP - Air-Gapped Public Procurement Auditor

## Overview
ProcureGuard MCP is a sovereign, edge-native compliance and forensic audit engine designed to evaluate public tender submissions against local market price baselines and corporate registry structures under Kenya's PPADA 2015 standards.

## Target Environment
* **Core Ledger**: SQLite ('procureguard.db')
* **Inference Engine**: Local Ollama ('qwen2.5-instruct:3b') via HTTP bridge ('http://localhost:11434/api/generate')
* **Protocol**: Model Context Protocol (MCP) via FastMCP (Stdio transport)

## Available Tools
1. **'audit_price_drift(item_code, bidder_rate_KES)'**: Computes percentage price variance against baseline rates and flags deviations exceeding 15%.
2. **'flag_collusion_risk(company_a, company_b)'**: Cross-refernces corporate registry records to detect overlapping director KRA PINs or identifiers.
3. **'Generate_statutory_brief(risk_score, max_drift, collusion flagged)'**: Synthesizes read-only telemetry into an executive compliance brief citing PPADA 2015 Section 114.
4. **'get_all_envelopes()'**: Retrieves submitted bid totals and financial summaries.
5. **'get_drift_table()'**: Pulls line-item forensic price variances.
6. **'get_collusion_alerts()'**: Pulls corporate registry database mappings.

## Execution Trigger Rules
Invoke this skill whenever a developer or procurement audito requests:
* Tender price inflation or drift analysis.
* Corporate registry conflict-of-interest or director collusion checks.
* Automated compliance briefs based on local statutory frameworks.