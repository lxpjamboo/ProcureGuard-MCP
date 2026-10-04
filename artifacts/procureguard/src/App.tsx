import { useState } from 'react';
import {
  Activity, AlertTriangle, ArrowUpRight, Check, CheckCheck, CheckCircle2,
  Cpu, FileText, LockKeyhole, Play, RotateCcw, ShieldAlert, Table2, Users,
} from 'lucide-react';

type Bidder = { name: string; price: string; status: string; flagReason: string; badgeColor: 'red' | 'gold' | 'green' };
type Log = { time: string; tool: string; msg: string };
type LineItem = { item: string; rfq: string; bidder: string; baseline: string; drift: string; pass: boolean };
type RedFlag = { type: string; severity: 'red' | 'gold' | 'green'; desc: string };
type Tender = {
  id: string; title: string; budget: string; bidsCount: string; riskScore: string;
  riskLevel: string; maxDrift: string; specMatched: string; bidders: Bidder[];
  mcpLogs: Log[]; lineItems: LineItem[]; redFlags: RedFlag[];
};

const tenders: Record<string, Tender> = {
  tender1: {
    id: 'REF: CP-2026-WTR-042', title: 'Solar-Powered Borehole & Water Kiosk System (Kitui County)',
    budget: 'KES 14,500,000', bidsCount: '3 Envelopes', riskScore: '88/100', riskLevel: 'HIGH RISK',
    maxDrift: '+312%', specMatched: '11 / 14',
    bidders: [
      { name: 'Apex Water Systems Ltd', price: 'KES 14,100,000', status: 'FLAGGED', flagReason: '312% Price Drift & Shared Director PIN with SunTech', badgeColor: 'red' },
      { name: 'SunTech Innovations EA', price: 'KES 13,850,000', status: 'FLAGGED', flagReason: 'Collusion Pattern (Identical Tax Registration)', badgeColor: 'red' },
      { name: 'HydroFlow Hydrogeology Ltd', price: 'KES 12,900,000', status: 'VERIFIED', flagReason: 'Market Baseline Aligned • Standard Specs', badgeColor: 'green' },
    ],
    mcpLogs: [
      { time: '00:00.12', tool: 'mcp.parse_spec_matrix()', msg: 'Extracted 14 technical requirements from Tender PDF Envelope A, B, C.' },
      { time: '00:00.45', tool: 'mcp.query_procurement_act_2015()', msg: 'Cross-referencing Section 66 (Prohibition of Ringing/Collusion).' },
      { time: '00:01.02', tool: 'mcp.fetch_market_price_baselines()', msg: 'Pulled Kitui Q3 Solar Submersible Pump index baselines.' },
      { time: '00:01.88', tool: 'mcp.detect_collusion_telemetry()', msg: 'CRITICAL: Shared Director PIN [KRA-A0039281] between Apex & SunTech.' },
    ],
    lineItems: [
      { item: 'Submersible Pump 7.5KW Heavy Duty', rfq: 'Stainless Steel AISI 316', bidder: 'KES 890,000', baseline: 'KES 216,000', drift: '+312%', pass: false },
      { item: 'Solar PV Panels 550W Monocrystalline', rfq: 'Tier-1 25-yr Warranty', bidder: 'KES 42,000/pc', baseline: 'KES 28,000/pc', drift: '+50%', pass: true },
      { item: 'Hydro-geological Survey & Drilling 200m', rfq: 'Certified Hydrologist', bidder: 'KES 1,450,000', baseline: 'KES 1,300,000', drift: '+11.5%', pass: true },
      { item: '10,000L Elevated Steel Storage Tank', rfq: 'Galvanized Steel ISO 9001', bidder: 'KES 920,000', baseline: 'KES 850,000', drift: '+8.2%', pass: true },
    ],
    redFlags: [
      { type: 'CRITICAL CARTEL COLLUSION DETECTED', severity: 'red', desc: 'Apex Water Systems Ltd and SunTech Innovations EA share identical Tax Director Registration [PIN: KRA-A0039281]. This is a sample finding referencing Section 66 of the Public Procurement Act 2015; no statute has been verified.' },
      { type: 'SEVERE UNJUSTIFIED PRICE DRIFT', severity: 'red', desc: "Line Item 'Submersible Pump 7.5KW' is quoted at KES 890,000 vs sample regional market baseline of KES 216,000 (+312% drift)." },
    ],
  },
  tender2: {
    id: 'REF: HLTH-2026-KIT-019', title: 'Dispensary Medical Supplies & Consumables (Garissa East)',
    budget: 'KES 8,200,000', bidsCount: '2 Envelopes', riskScore: '42/100', riskLevel: 'MODERATE',
    maxDrift: '+68%', specMatched: '18 / 20',
    bidders: [
      { name: 'Farina Medical Supplies', price: 'KES 7,950,000', status: 'MODERATE', flagReason: 'Gauze Packets 68% above KEMSA baseline', badgeColor: 'gold' },
      { name: 'Athi Pharma Distributors', price: 'KES 6,800,000', status: 'VERIFIED', flagReason: 'Full PPB License • Price Match 96%', badgeColor: 'green' },
    ],
    mcpLogs: [
      { time: '00:00.08', tool: 'mcp.parse_spec_matrix()', msg: 'Extracted 20 pharmaceutical line items from RFQ.' },
      { time: '00:00.32', tool: 'mcp.query_procurement_act_2015()', msg: 'Verified Pharmacy & Poisons Board licensing database.' },
      { time: '00:00.91', tool: 'mcp.fetch_market_price_baselines()', msg: 'Compared against KEMSA National Price Catalog 2026.' },
    ],
    lineItems: [
      { item: 'Surgical Gloves Powder-Free Box', rfq: 'ISO 13485 Certified', bidder: 'KES 1,200', baseline: 'KES 950', drift: '+26.3%', pass: true },
      { item: 'Sterile Gauze Rolls 100m Pack', rfq: '100% Absorbent Cotton', bidder: 'KES 4,200', baseline: 'KES 2,500', drift: '+68.0%', pass: false },
      { item: 'Amoxicillin 500mg Capsules (1000s)', rfq: 'GMP Certified Batch', bidder: 'KES 3,100', baseline: 'KES 2,900', drift: '+6.8%', pass: true },
    ],
    redFlags: [{ type: 'MODERATE PRICE INFLATION ON CONSUMABLES', severity: 'gold', desc: 'Farina Medical Supplies quoted Sterile Gauze Rolls at +68% above sample KEMSA bulk purchase baselines.' }],
  },
  tender3: {
    id: 'REF: PWRK-2026-RD-112', title: 'Feeder Road Graveling & Box Culverts (Machakos Ward 4)',
    budget: 'KES 22,000,000', bidsCount: '4 Envelopes', riskScore: '15/100', riskLevel: 'LOW RISK',
    maxDrift: '+12.4%', specMatched: '25 / 25',
    bidders: [
      { name: 'Machakos Civils & Earthworks', price: 'KES 21,200,000', status: 'VERIFIED', flagReason: 'NCA Category 4 Validated • Fully Compliant', badgeColor: 'green' },
      { name: 'Ukambani Heavy Roads Ltd', price: 'KES 21,800,000', status: 'VERIFIED', flagReason: 'Compliant Baseline Pricing', badgeColor: 'green' },
    ],
    mcpLogs: [
      { time: '00:00.10', tool: 'mcp.parse_spec_matrix()', msg: 'Parsed 25 road construction engineering line items.' },
      { time: '00:00.41', tool: 'mcp.fetch_market_price_baselines()', msg: 'Cross-checked gravel compaction rates against Ministry rates.' },
    ],
    lineItems: [
      { item: 'Gravel Wearing Course Compaction (m3)', rfq: 'CBR > 30% Spec', bidder: 'KES 1,450', baseline: 'KES 1,380', drift: '+5.0%', pass: true },
      { item: 'Precast Concrete Box Culverts 900mm', rfq: 'Class 25 Concrete', bidder: 'KES 24,000', baseline: 'KES 22,500', drift: '+6.6%', pass: true },
    ],
    redFlags: [{ type: 'ALL ITEMS COMPLIANT', severity: 'green', desc: 'Sample data indicates no cartel collusion or severe price drift. All shown bidders are within sample variance parameters.' }],
  },
};

const actions: Record<string, string> = {
  REJECT_AND_ESCALATE: 'REJECT BIDDER B & ESCALATE TO ETHICS BOARD (EACC)',
  APPROVE_COMPLIANT_ALT: 'AWARD TO BIDDER A (COMPLIANT / MARKET ALIGNED)',
  CANCEL_RE_TENDER: 'CANCEL TENDER & ORDER PUBLIC RE-ADVERTISEMENT',
};

function App() {
  const [tenderKey, setTenderKey] = useState('tender1');
  const [scanState, setScanState] = useState<'idle' | 'running' | 'complete'>('idle');
  const [scanLog, setScanLog] = useState<string | null>(null);
  const [officerId, setOfficerId] = useState('PO-99482-NAIROBI');
  const [action, setAction] = useState('REJECT_AND_ESCALATE');
  const [notes, setNotes] = useState('');
  const [error, setError] = useState('');
  const [preview, setPreview] = useState(false);
  const tender = tenders[tenderKey];
  const isHigh = tender.riskLevel === 'HIGH RISK';
  const tone = isHigh ? 'red' : tender.riskLevel === 'MODERATE' ? 'gold' : 'green';

  function changeTender(key: string) {
    setTenderKey(key);
    setScanState('idle');
    setScanLog(null);
    setPreview(false);
    setError('');
  }

  function runSimulation() {
    if (scanState === 'running') return;
    setScanState('running');
    setScanLog('LOCAL DEMO: simulated re-scan started. No MCP server or model was contacted.');
    window.setTimeout(() => {
      setScanState('complete');
      setScanLog('LOCAL DEMO: scan preview complete. Sample findings above are unchanged; nothing was sent or verified.');
    }, 950);
  }

  function reviewSignoff() {
    if (!officerId.trim()) {
      setError('Enter an Officer ID / Badge No. to preview this sign-off.');
      setPreview(false);
      return;
    }
    if (!notes.trim()) {
      setError('Add a written justification before reviewing the sign-off preview.');
      setPreview(false);
      return;
    }
    setError('');
    setPreview(true);
  }

  return (
    <div className="app-shell">
      <header className="topbar">
        <div className="topbar-inner">
          <div className="brand">
            <div className="brand-mark" aria-hidden="true">PG</div>
            <div className="brand-copy">
              <div className="brand-line"><h1>ProcureGuard <span>MCP</span></h1><span className="version">GOVERNANCE AUDIT · DEMO</span></div>
              <p>The Bid Box Challenge <span className="dot-sep">/</span> Institutional procurement review</p>
            </div>
          </div>
          <div className="header-context">
            <span className="context-chip"><Cpu size={14} /><b>LOCAL BROWSER SIMULATION</b></span>
            <span className="context-chip context-demo"><span className="status-dot" /> SAMPLE DATA · NOT LIVE</span>
          </div>
          <button type="button" data-testid="button-run-scan" className="button-primary" onClick={runSimulation} disabled={scanState === 'running'}>
            {scanState === 'running' ? <Activity size={15} className="spin" /> : <Play size={15} />}
            {scanState === 'running' ? 'Simulating scan…' : 'Run sample scan'}
          </button>
        </div>
      </header>

      <main className="workspace">
        <div className="demo-banner" role="note">
          <span className="demo-icon"><AlertTriangle size={15} /></span>
          <div><strong>DEMO ENVIRONMENT — ALL RECORDS AND FINDINGS ARE SAMPLE DATA.</strong><span> No live tender source, MCP/model connection, statute check, or market feed is active. Nothing here is an official procurement determination.</span></div>
          <span className="demo-stamp mono">LOCAL ONLY</span>
        </div>

        <section className="overview-grid" aria-label="Tender overview">
          <div className="panel tender-card">
            <label htmlFor="tender-select" className="section-label"><FileText size={14} /> ACTIVE TENDER ENVELOPE <span className="demo-tag">SAMPLE</span></label>
            <select id="tender-select" data-testid="select-tender" value={tenderKey} onChange={(event) => changeTender(event.target.value)}>
              <option value="tender1">REF: CP-2026-WTR-042 • Solar-Powered Borehole & Water Kiosk System (Kitui County)</option>
              <option value="tender2">REF: HLTH-2026-KIT-019 • Dispensary Medical Supplies & Consumables (Garissa East)</option>
              <option value="tender3">REF: PWRK-2026-RD-112 • Feeder Road Graveling & Box Culverts (Machakos Ward 4)</option>
            </select>
            <div className="tender-meta">
              <span>Target budget <b>{tender.budget}</b></span><span>Bids received <b className="cyan">{tender.bidsCount}</b></span><span>Procurement type <b>National Open Tender</b></span>
            </div>
            <div className="selected-title"><span className="mono">{tender.id}</span><h2>{tender.title}</h2></div>
          </div>
          <div className="metric-grid">
            <article className={`panel metric metric-${tone}`} data-testid="value-risk-score">
              <span className="metric-title">FORENSIC RISK SCORE <span className="sample-dot">SAMPLE</span></span>
              <div className="metric-value-row"><b className="metric-value">{tender.riskScore}</b><span className={`badge badge-${tone}`}>{tender.riskLevel}</span></div>
              <small>Illustrative indicator · not a live assessment</small>
            </article>
            <article className="panel metric">
              <span className="metric-title">MAX PRICE DRIFT <span className="sample-dot">SAMPLE</span></span>
              <div className="metric-value-row"><b className="metric-value gold">{tender.maxDrift}</b><ArrowUpRight size={17} className="gold" /></div>
              <small>Compared with shown sample baseline</small>
            </article>
            <article className="panel metric">
              <span className="metric-title">SPEC MATCHING <span className="sample-dot">SAMPLE</span></span>
              <div className="metric-value-row"><b className="metric-value green">{tender.specMatched}</b><CheckCircle2 size={17} className="green" /></div>
              <small>Shown matched / total requirements</small>
            </article>
            <article className="panel metric">
              <span className="metric-title">OFFICER HANDOFF</span>
              <div className="metric-value-row"><b className="handoff"><LockKeyhole size={15} /> AWAITING REVIEW</b></div>
              <small>Preview only · no record will be saved</small>
            </article>
          </div>
        </section>

        <section className="workspace-grid" aria-label="Tender review workspace">
          <div className="left-column">
            <section className="panel bidders">
              <div className="panel-heading">
                <h2><Users size={16} /> SUBMITTED BID ENVELOPES</h2><span className="minor-label">SAMPLE RECORDS</span>
              </div>
              <div className="bidder-list">
                {tender.bidders.map((bidder) => (
                  <article className="bidder-row" key={bidder.name} data-testid={`bidder-${bidder.name.replaceAll(' ', '-').toLowerCase()}`}>
                    <div className="bidder-info"><b>{bidder.name}</b><span>{bidder.flagReason}</span></div>
                    <div className="bidder-price"><b>{bidder.price}</b><span className={`badge badge-${bidder.badgeColor}`}>{bidder.status}</span></div>
                  </article>
                ))}
              </div>
              <p className="data-disclaimer">Names, prices, and status labels shown are illustrative sample records.</p>
            </section>

            <section className="panel terminal-panel">
              <div className="terminal-heading">
                <div><span className="terminal-light" /><h2>CONTEXT TOOL EXECUTION STREAM</h2></div><span className="simulated-pill">SIMULATED</span>
              </div>
              <p className="terminal-subtitle">Sample log lines for interface demonstration only. No MCP tools are invoked.</p>
              <div className="terminal terminal-scan" role="log" aria-live="polite" data-testid="terminal-log">
                {tender.mcpLogs.map((log, index) => (
                  <div className="log-entry" key={`${log.time}-${log.tool}`}>
                    <div className="log-meta"><span>[{log.time}] {log.tool}</span><span>DEMO #{String(index + 1).padStart(3, '0')}</span></div>
                    <p>{log.msg}</p>
                  </div>
                ))}
                {scanLog && <div className={`scan-result ${scanState === 'running' ? 'running' : ''}`}><Activity size={13} />{scanLog}</div>}
              </div>
              <div className="terminal-footer"><span>Sample entries <b>{tender.mcpLogs.length}</b></span><span>Execution <b>NOT CONNECTED</b></span><span>Scan <b>{scanState === 'complete' ? 'LOCAL PREVIEW COMPLETE' : scanState === 'running' ? 'SIMULATING' : 'IDLE'}</b></span></div>
            </section>
          </div>

          <div className="right-column">
            <section className="panel matrix">
              <div className="panel-heading matrix-heading">
                <div><h2><Table2 size={16} /> LINE-ITEM SPEC & PRICE COMPARISON</h2><p>Example unit rates compared with the sample benchmark. Not independently verified.</p></div>
                <div className="legend"><span className="red"><i /> High drift</span><span className="green"><i /> Spec pass</span></div>
              </div>
              <div className="table-wrap">
                <table>
                  <thead><tr><th>Line item / description</th><th>RFQ spec</th><th>Bidder rate</th><th>Sample baseline</th><th>Drift</th><th>Spec pass</th></tr></thead>
                  <tbody>{tender.lineItems.map((item) => (
                    <tr key={item.item} data-testid={`line-item-${item.item.replaceAll(' ', '-').toLowerCase()}`}>
                      <td className="item-name">{item.item}</td><td>{item.rfq}</td><td className="rate">{item.bidder}</td><td>{item.baseline}</td>
                      <td className={parseFloat(item.drift.replace('+', '')) >= 50 ? 'red rate' : parseFloat(item.drift.replace('+', '')) > 25 ? 'gold rate' : 'green rate'}>{item.drift}</td>
                      <td><span className={`badge badge-${item.pass ? 'green' : 'red'}`}>{item.pass ? 'PASS' : 'FAIL'}</span></td>
                    </tr>
                  ))}</tbody>
                </table>
              </div>
              <p className="table-note"><span className="mono">READ ONLY · DEMO DATA</span> Market benchmark values are supplied examples, not current market research.</p>
            </section>

            <section className="panel flags">
              <div className="panel-heading"><h2 className="red"><ShieldAlert size={16} /> FORENSIC ANOMALY & RED-FLAG REVIEW</h2><span className="minor-label">SAMPLE FINDINGS</span></div>
              <div className="flag-list">
                {tender.redFlags.map((flag) => (
                  <article className={`flag flag-${flag.severity}`} key={flag.type}>
                    <div className="flag-heading"><h3><AlertTriangle size={14} />{flag.type}</h3><span>UNVERIFIED DEMO</span></div>
                    <p>{flag.desc}</p>
                  </article>
                ))}
              </div>
              <p className="disclaimer-inline">Risk labels and legal references are not validated by this demo.</p>
            </section>
          </div>
        </section>

        <section className={`panel signoff ${preview ? 'signoff-preview' : ''}`} aria-labelledby="signoff-title">
          <div className="signoff-head">
            <div className="signoff-lock"><LockKeyhole size={21} /></div>
            <div><h2 id="signoff-title">HUMAN-IN-THE-LOOP SIGN-OFF PREVIEW</h2><p>Reviewer oversight demo · sample content only · no award or official determination</p></div>
            <div className="governance"><span>REVIEW GATE · DEMO</span><b>A PERSON MUST MAKE ANY REAL DECISION</b></div>
          </div>

          {!preview ? (
            <div className="signoff-form">
              <div className="form-fields">
                <div className="form-row">
                  <div className="field"><label htmlFor="officer-id">Procurement Officer ID / Badge No. <span>*</span></label><input id="officer-id" data-testid="input-officer-id" value={officerId} onChange={(event) => setOfficerId(event.target.value)} placeholder="e.g. PO-88219-NBO" autoComplete="off" /></div>
                  <div className="field"><label htmlFor="determination-action">Determination action <span>*</span></label><select id="determination-action" data-testid="select-action" value={action} onChange={(event) => setAction(event.target.value)}>{Object.entries(actions).map(([key, label]) => <option value={key} key={key}>{label}</option>)}</select></div>
                </div>
                <div className="field"><label htmlFor="officer-notes">Audit justification & reviewer notes <span>*</span></label><textarea id="officer-notes" data-testid="input-notes" rows={4} value={notes} onChange={(event) => setNotes(event.target.value)} placeholder="Enter sample review notes. Do not include real personal, tender, or confidential information." /></div>
                {error && <p className="form-error" role="alert" data-testid="status-validation-error"><AlertTriangle size={14} />{error}</p>}
              </div>
              <aside className="preview-callout">
                <div className="callout-label"><LockKeyhole size={15} /> LOCAL PREVIEW ONLY</div>
                <div className="no-hash">No cryptographic digest<br />or receipt is generated.</div>
                <p>Reviewing this form does not create, sign, submit, or persist an audit record. This interface has no backend connection.</p>
                <button type="button" data-testid="button-preview-signoff" className="button-review" onClick={reviewSignoff}><CheckCheck size={16} /> REVIEW SIGN-OFF PREVIEW</button>
              </aside>
            </div>
          ) : (
            <div className="preview-result enter" data-testid="signoff-preview">
              <div className="preview-result-heading"><span className="preview-check"><Check size={17} /></span><div><h3>Sign-off preview prepared</h3><p>This is a temporary on-screen preview only.</p></div></div>
              <div className="preview-details">
                <div><span>TENDER SAMPLE</span><b>{tender.id} · {tender.title}</b></div>
                <div><span>OFFICER ID</span><b>{officerId}</b></div>
                <div><span>PROPOSED ACTION</span><b>{actions[action]}</b></div>
                <div><span>REVIEW NOTES</span><b>{notes}</b></div>
              </div>
              <div className="not-saved"><AlertTriangle size={16} /><div><b>NOT SAVED · NOT SIGNED · NO AUDIT RECORD CREATED</b><span>No cryptographic hash, seal, receipt, transmission, or persistent storage has been created.</span></div></div>
              <button type="button" className="button-secondary" data-testid="button-edit-preview" onClick={() => setPreview(false)}><RotateCcw size={14} /> Return to editable form</button>
            </div>
          )}
          <div className="signoff-foot"><span><span className="gold">RULE 88-B · DEMO</span> AI does not award a tender autonomously.</span><span>All inputs remain in this browser session only.</span></div>
        </section>

        <footer className="footer"><span><b>PROCUREGUARD MCP</b> <span className="footer-sep">/</span> REVIEW DEMO</span><span>Sample data · no live procurement, legal, or model verification</span></footer>
      </main>
    </div>
  );
}

export default App;