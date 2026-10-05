# ProcureGuard MCP — Sovereign Institutional Procurement Auditor

[![Build Status](https://img.shields.io/badge/Build-Passing-brightgreen)]()
[![MCP Version](https://img.shields.io/badge/MCP-FastMCP_Python-blue)]()
[![Compliance](https://img.shields.io/badge/Compliance-PPADA_2015_Sec_114-orange)]()
[![Deployment](https://img.shields.io/badge/Deployed-Replit_Live-461358)]()

ProcureGuard MCP is an autonomous, air-gapped agentic AI auditor designed for African public sector procurement governance. Operating within the **Value for Money** framework of Kenya's Public Procurement and Asset Disposal Act (PPADA 2015), ProcureGuard dynamically inspects tender submissions, audits pricing drift against market baselines, detects multi-entity collusion patterns, and enforces strict Human-in-the-Loop (HITL) oversight before actionable decisions are recorded.

---

## 1. Problem Statement
Public sector procurement in East Africa faces significant structural vulnerabilities, resulting in substantial fiscal leakage:
* **Market-Rate Inflation & Price Drift:** Single-source or collusive bids routinely mark up infrastructure items (e.g., heavy-duty solar submersibles) by over 300% above established baseline rates.
* **Corporate Identity Masking & Bid Rigging:** Shell companies sharing directors, tax identifiers (KRA PINs), or identical line-item cost structures manipulate tender evaluations.
* **Opaque Technical Compliance:** Manual evaluation processes struggle to parse voluminous tender catalogs, leading to missed technical mismatches or biased scoring.
* **Lack of Unalterable Audit Lineage:** Conventional AI solutions act as "black box" automated decision-makers without deterministic boundaries or explicit statutory citation chains.

---

## 2. Solution Overview
ProcureGuard MCP addresses procurement leakage by pairing a local reasoning engine with an air-gapped **Model Context Protocol (MCP)** execution context:
* **Deterministic Tool Boundaries:** Separates reasoning from raw tool execution using FastMCP servers over `stdio`.
* **Automated Forensic Line-Item Audit:** Automatically cross-references submitted quantities and unit prices against local market rate baselines (KES).
* **Multi-Bidder Identity Correlation:** Scans metadata across all submitted bidder envelopes to flag duplicate tax registration numbers and director ownership.
* **Statutory Human Gate:** Generates a **Forensic Risk Dossier** and halts state changes until an authorized Procurement Officer inputs a verified Badge ID to approve or escalate the dossier.

---

## 3. Target Users
1. **Public Procurement Officers & Evaluators:** Seeking automated assistance to process dense tender envelopes while maintaining statutory compliance under PPADA 2015 / 2020.
2. **Ethics & Anti-Corruption Oversight Boards (e.g., EACC):** Requiring clear forensic audit trails, price drift metrics, and corporate correlation graphs.
3. **Institutional Accounting Officers:** Requiring verification that tender allocations remain within budgeted regional market baselines.

---
## 4. Architecture

```text
                              +-------------------------------------------------+
                              |     Human-in-the-Loop Audit Dashboard          |
                              |          (React / Vite / Tailwind)              |
                              +------------------------+------------------------+
                                                       |
                                            POST /api/audit (Zod Validated)
                                                       |
                                                       v
                              +-------------------------------------------------+
                              |         Express / Node.js API Gateway           |
                              +------------------------+------------------------+
                                                       |
                                           JSON-RPC over stdio (IPC)
                                                       |
                                                       v
                              +-------------------------------------------------+
                              |           ProcureGuard FastMCP Server           |
                              |                (mcp_server.py)                  |
                              +-------+----------------+----------------+-------+
                                      |                |                |
                                      v                v                v
                   +--------------------+    +-------------------+    +----------------------+
                   | parse_tender_specs |    | audit_price_drift |    | flag_collusion_risk  |
                   +---------+----------+    +---------+---------+    +----------+-----------+
                             |                         |                         |
                             v                         v                         v
                   +--------------------+    +-------------------+    +----------------------+
                   | Local LLM / Qwen   |    | SQLite Market     |    | Corporate Registry   |
                   | Unstructured Parser|    | Price Baselines   |    | Index (KRA / BRS)    |
                   +--------------------+    +-------------------+    +----------------------+
```

## 5. Agent Architecture
ProcureGuard implements a **Goal-Directed Audit Loop**:
1. **Ingestion & Strategy Formulation:** The agent accepts raw tender envelopes (RFQs and bidder submissions).
2. **Sequential Tool Planning:** The agent plans a multi-step audit pipeline:
   * Execute `parse_tender_specs` to extract requirement items and threshold parameters.
   * Execute `audit_price_drift` to compute percentage deviation from SQLite market baselines.
   * Execute `flag_collusion_risk` to scan bidder tax and director registers for cross-entity overlap.
3. **Synthesis & Dossier Generation:** Telemetry is synthesized into a prioritized Risk Score (Low, Medium, High, Critical) annotated with PPADA statutory references.
4. **Human Interruption Gate (`interrupt()`):** The execution thread pauses until explicit sign-off from a badge-authenticated officer.

---

## 6. MCP Implementation
ProcureGuard utilizes the **Model Context Protocol (MCP)** via the Python `FastMCP` framework to enforce strict separation between context generation, data access, and LLM inference:
* **Transport Protocol:** Standard I/O (`stdio`) over JSON-RPC.
* **Server File:** `mcp_server.py` acts as an isolated daemon.
* **Typed Context Isolation:** The LLM cannot access internal database state or market indices directly; all data requests pass through typed, schema-validated MCP tool calls.

---

## 7. MCP Tools & Context Servers

| Tool Name | Type | Description | Inputs | Key Output |
| :--- | :--- | :--- | :--- | :--- |
| `parse_tender_specs` | Parsing / Extraction | Ingests raw RFQ texts and returns technical thresholds, items, and quantities. | `rfq_id: str` | Structured JSON containing line items & technical specs. |
| `audit_price_drift` | Calculation / Audit | Queries market baselines and computes percentage drift per item. | `bid_items: List[Item]` | Itemized percentage drift flags (e.g., +312% variance). |
| `flag_collusion_risk` | Registry Cross-Ref | Compares bidder metadata against corporate registry indices for duplicate PINs or identical pricing structures. | `bids: List[Bid]` | Collusion risk scores and overlapping director/KRA alerts. |

---

## 8. Technology Stack
* **Frontend UI:** React 18, Vite, Tailwind CSS, Lucide React Icons.
* **Backend Gateway:** Node.js, Express, TypeScript, Zod Schema Validation.
* **MCP Framework:** Python 3.11+, FastMCP SDK (`mcp[cli]`).
* **Inference & Models:** Local Qwen2.5-Coder runtime via Ollama / air-gapped local model runner.
* **Data Layer:** SQLite embedded database (containing KES market benchmarks and corporate registries).

---

## 9. Human-in-the-Loop (HITL) Workflow
In strict accordance with statutory governance rules:
1. **Autonomy Guardrail:** The AI agent is strictly barred from awarding contracts, disqualifying vendors autonomously, or signing procurement declarations.
2. **Officer Authentication Gate:** The dashboard renders an interactive **Sign-Off Terminal** demanding a valid Officer Badge ID.
3. **Explicit Decision Routing:** The human officer must choose one of three actions:
   * **Approve & Proceed:** Validates bids meeting baseline tolerances.
   * **Request Clarification:** Dispatches formal query to bidder regarding line-item variance.
   * **Escalate to EACC:** Flags collusion or severe price drift for formal anti-corruption investigation.

---

## 10. Setup & Installation Instructions

### Prerequisites
* Python 3.11+
* Node.js v18+ & npm
* SQLite3

### Step-by-Step Installation

1. **Clone the Repository:**
   ```bash
   git clone [https://github.com/lxpjamboo/ProcureGuard-MCP.git](https://github.com/lxpjamboo/ProcureGuard-MCP.git)
   cd ProcureGuard-MCP

   # Set up Python virtual environment
python3 -m venv venv
source venv/bin/activate

## Install MCP requirements
pip install mcp fastmcp sqlite3

## Install Node dependencies
npm install

## Initialize local SQLite market baselines
python scripts/init_db.py
