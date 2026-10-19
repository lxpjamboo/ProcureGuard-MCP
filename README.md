# PROCUREGUARD MCP | Statutory Procurement Auditor

> **Air-gapped forensic audit middleware for public sector procurement integrity under Kenya's PPADA 2015 Section 114.**

![License](https://img.shields.io/badge/license-MIT-blue.svg)
![Stack](https://img.shields.io/badge/stack-PHP%20%7C%20SQLite%20%7C%20FastMCP%20%7C%20Ollama-cyan)
![Jurisdiction](https://img.shields.io/badge/jurisdiction-PPADA%202015-emerald)

---

## 1. Problem Statement

Public procurement systems across emerging markets face systemic risks from structural overpricing, price drift, and covert cartel collusion (such as shared directorships or matching KRA PINs among seemingly distinct bidders). Standard digital procurement portals lack real-time, local forensic checks to catch line-item inflation before contract execution, leading to public revenue leakage.

---

## 2. Solution Overview

**ProcureGuard MCP** is an air-gapped, sovereign forensic auditing tool. It intercepts submitted bid envelopes, evaluates line-item rates against local statutory baselines, and flags price drift and corporate identity overlap. 

### Key Features
* **Zero-Leak Air-Gapped Intelligence**: Runs locally using `qwen2.5-instruct:3b` via Ollama to synthesize statutory briefs without leaking public procurement data to cloud APIs.
* **FastMCP Telemetry**: Connects local SQLite ledgers directly to high-contrast frontend dashboards.
* **Human-in-the-Loop Sign-Off**: Enforces Kenya’s **Public Procurement and Asset Disposal Act (PPADA) 2015 Section 114**, requiring cryptographic officer authentication before escalating or clearing flagged tenders.

---

## 3. Target Users

* **Public Procurement Officers**: Conducting initial bid evaluation and statutory compliance checks.
* **Forensic Internal Auditors**: Reviewing historical price drift across municipal supply chains.
* **Regulatory & Anti-Corruption Bodies**: Inspecting immutable audit logs and officer decision trails.

---

## 4. Architecture
[ Public Ledger / Tender Data ]
│
▼
[ Python Ingestion Sync ]
│
▼
[ SQLite Ledger ] (procureguard.db)
│
├──────► [ FastMCP Server ] ◄───► [ Local Ollama Engine ]
│             │                     (qwen2.5-instruct:3b)
│             ▼
└──────► [ PHP / Alpine.js Dashboard ]
│
▼
[ PPADA Sec 114 Officer Gate ]

---

## 5. Setup & Installation Instructions

### Prerequisites
* **XAMPP / PHP 8.x** running locally.
* **Python 3.10+** with SQLite support.
* **Ollama** installed locally with `qwen2.5-instruct:3b` pulled:
  ```bash
  ollama pull qwen2.5-instruct:3b

  ### Local Setup
1. Clone the repository into your local web server root (e.g., `htdocs`):
   ```bash
   git clone [https://github.com/lxpjamboo/ProcureGuard-MCP.git](https://github.com/lxpjamboo/ProcureGuard-MCP.git)
   cd ProcureGuard-MCP

   ###
2. Initialize the local SQLite database and seed forensic text baselines 
  ```bash
   python_init_db.py

   ###
3. Ensure Apache is active on XAMPP, then access the local dashboard gateway
