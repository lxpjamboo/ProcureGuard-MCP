# ProcureGuard MCP
*Sovereign Public Procurement Audit & Compliance Engine*

## Overview
ProcureGuard MCP is an edge-native, air-gapped forensic audit middleware designed to evaluate public tender submissions against local market price baselines and corporate registry structures under Kenya's **PPADA 2015** statutory standards. 

Engineered under the **LXP Holdings** architecture, it features a decoupled two-tier design separating the sovereign database core from the agentic skill layer.

---

## Architectural Topology

```text
procureguard-mcp/
├── core/                        # TIER 1: Sovereign SQLite Ledger & PHP Backend
│   ├── procureguard.db
│   ├── init_db.py
│   └── api.php
├── mcp-skill/                   # TIER 2: Agentic Skill Layer & FastMCP Server
│   ├── SKILL.md                 # Agent trigger manifest for LLMs
│   └── mcp_server.py            # FastMCP Stdio bridge & Local Ollama hook
├── IP_DECLARATION.md            # LXP Holdings IP Governance
└── README.md                    # System Documentation
```

---

# Core Capabilities
Price Drift Analysis: Automatically computes percentage price variance against local market baselines and flags overcharges exceeding 15%.

Collusion Detection: Cross-references corporate registry records to catch overlapping director KRA PINs and shell-company relationships.

Statutory Brief Generation: Passes read-only telemetry to local air-gapped Ollama instance (qwen2.5-instruct:3b) to synthesize compliance summaries citing PPADA 2015 Section 11A.

Model-Agnostic Interegration: Plugs natively into any AI Client that supports the Model Context Protocol (MCP).

# Installation & Setup
Clone or initialize the repository

Activate your python virtual environment:
venv\Scripts\Activate.ps1

Install depemdecies
pip install -r mcp-skill/requirements.txt

Run the FastMCP server daemon:
python mcp-skill/mcp_server.py

# License & Intellectual Property
Engineered and maintained exclusively by LXP Holdings. All rights reserved. See IP_DECLARATION.md.

# Confidential Architectural Concept | © 2026 LXP
