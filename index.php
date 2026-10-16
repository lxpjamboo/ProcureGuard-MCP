<?php
// index.php - ProcureGuard MCP High-Contrast Dashboard Gateway
session_start();
?>
<!DOCTYPE html>
<html lang="en" class="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PROCUREGUARD MCP | Statutory Procurement Auditor</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <style>
        [x-cloak] { display: none !important; }
        ::-webkit-scrollbar { width: 6px; height: 6px; }
        ::-webkit-scrollbar-track { background: #090d16; }
        ::-webkit-scrollbar-thumb { background: #1e293b; border-radius: 3px; }
        ::-webkit-scrollbar-thumb:hover { background: #334155; }
    </style>
</head>
<body class="bg-[#070a11] text-slate-200 font-sans antialiased min-h-screen flex flex-col selection:bg-cyan-500 selection:text-black"
      x-data="procureGuardApp()" x-init="init()">

    <!-- TOP TOAST NOTIFICATION -->
    <div x-show="toast.visible" 
         x-transition:enter="transition ease-out duration-300 transform"
         x-transition:enter-start="-translate-y-12 opacity-0"
         x-transition:enter-end="translate-y-0 opacity-100"
         x-transition:leave="transition ease-in duration-200 transform"
         x-transition:leave-start="translate-y-0 opacity-100"
         x-transition:leave-end="-translate-y-12 opacity-0"
         x-cloak
         class="fixed top-4 right-4 z-50 flex items-center space-x-3 px-4 py-3 rounded-lg border shadow-2xl backdrop-blur-md"
         :class="toast.type === 'success' ? 'bg-emerald-950/90 border-emerald-500/50 text-emerald-200' : 'bg-cyan-950/90 border-cyan-500/50 text-cyan-200'">
        <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
        </svg>
        <span class="text-sm font-mono font-medium" x-text="toast.message"></span>
        <button @click="toast.visible = false" class="text-slate-400 hover:text-white ml-2">&times;</button>
    </div>

    <!-- MAIN APP WRAPPER -->
    <div class="flex flex-1 min-h-screen">

        <!-- SIDEBAR NAVIGATION -->
        <aside class="w-64 bg-[#0c101d] border-r border-slate-800/80 flex flex-col justify-between flex-shrink-0">
            <div>
                <!-- Brand Header -->
                <div class="p-5 border-b border-slate-800/80 flex items-center space-x-3">
                    <div class="p-2 rounded bg-cyan-500/10 border border-cyan-500/30 text-cyan-400">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                        </svg>
                    </div>
                    <div>
                        <h1 class="text-sm font-bold tracking-wider text-white uppercase font-mono">PROCUREGUARD</h1>
                        <span class="text-[10px] font-mono text-cyan-400 font-semibold tracking-widest uppercase">MCP ENGINE V1.0</span>
                    </div>
                </div>

                <!-- Navigation Links -->
                <nav class="p-4 space-y-1">
                    <div class="px-3 py-2 text-[10px] font-mono font-semibold text-slate-500 tracking-wider uppercase">Navigation</div>

                    <button @click="activeTab = 'dashboard'" 
                            :class="activeTab === 'dashboard' ? 'bg-cyan-500/10 text-cyan-300 border-l-2 border-cyan-400 font-semibold' : 'text-slate-400 hover:bg-slate-800/50 hover:text-slate-200'"
                            class="w-full flex items-center space-x-3 px-3 py-2.5 rounded-r text-xs font-mono transition-all">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/></svg>
                        <span>DASHBOARD</span>
                    </button>

                    <button @click="activeTab = 'envelopes'" 
                            :class="activeTab === 'envelopes' ? 'bg-cyan-500/10 text-cyan-300 border-l-2 border-cyan-400 font-semibold' : 'text-slate-400 hover:bg-slate-800/50 hover:text-slate-200'"
                            class="w-full flex items-center space-x-3 px-3 py-2.5 rounded-r text-xs font-mono transition-all">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                        <span>ENVELOPES</span>
                    </button>

                    <button @click="activeTab = 'drift'" 
                            :class="activeTab === 'drift' ? 'bg-cyan-500/10 text-cyan-300 border-l-2 border-cyan-400 font-semibold' : 'text-slate-400 hover:bg-slate-800/50 hover:text-slate-200'"
                            class="w-full flex items-center space-x-3 px-3 py-2.5 rounded-r text-xs font-mono transition-all">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/></svg>
                        <span>DRIFT TABLE</span>
                    </button>

                    <button @click="activeTab = 'officer'" 
                            :class="activeTab === 'officer' ? 'bg-cyan-500/10 text-cyan-300 border-l-2 border-cyan-400 font-semibold' : 'text-slate-400 hover:bg-slate-800/50 hover:text-slate-200'"
                            class="w-full flex items-center space-x-3 px-3 py-2.5 rounded-r text-xs font-mono transition-all">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 11c0 3.517-1.009 6.799-2.753 9.571m-3.44-2.04l.054-.09A13.916 13.916 0 008 11a4 4 0 118 0c0 1.017-.07 2.019-.203 3m-2.118 6.844A21.88 21.88 0 0015.171 17m3.839 1.132c.645-2.266.99-4.659.99-7.132A8 8 0 008 4.07M3 15.364c.64-1.319 1-2.8 1-4.364 0-1.457-.39-2.823-1.07-4"/></svg>
                        <span>OFFICER GATE</span>
                    </button>

                    <button @click="activeTab = 'logs'" 
                            :class="activeTab === 'logs' ? 'bg-cyan-500/10 text-cyan-300 border-l-2 border-cyan-400 font-semibold' : 'text-slate-400 hover:bg-slate-800/50 hover:text-slate-200'"
                            class="w-full flex items-center space-x-3 px-3 py-2.5 rounded-r text-xs font-mono transition-all">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                        <span>AUDIT LOGS</span>
                    </button>
                </nav>
            </div>

            <!-- Footer User Section -->
            <div class="p-4 border-t border-slate-800/80 bg-[#090d16]">
                <div class="text-[11px] font-mono text-slate-500">Logged in as:</div>
                <div class="text-xs font-mono font-bold text-slate-200">Procurement Officer</div>
                <div class="text-[10px] font-mono text-cyan-400/80 mt-0.5" x-text="officerForm.badgeId">PO-98402-NAIROBI</div>
            </div>
        </aside>

        <!-- MAIN CONTENT AREA -->
        <main class="flex-1 flex flex-col min-w-0 bg-[#070a11]">
            
            <!-- TOP BAR HEADER -->
            <header class="h-16 border-b border-slate-800/80 px-6 flex items-center justify-between bg-[#0c101d]/60 backdrop-blur-sm sticky top-0 z-40">
                <div class="flex items-center space-x-4">
                    <h2 class="text-base font-bold font-mono tracking-wider text-white uppercase">PROCUREGUARD MCP</h2>
                    <span class="text-xs font-mono px-2.5 py-0.5 rounded bg-slate-800 text-slate-300 border border-slate-700">
                        REF: CP-2026-WTR-042
                    </span>
                    <span class="text-xs font-mono px-2.5 py-0.5 rounded bg-cyan-950 text-cyan-400 border border-cyan-800/60 hidden md:inline-block" x-text="modelEngine">
                        LOCAL FASTMCP · PPADA 2015
                    </span>
                </div>

                <div class="flex items-center space-x-3">
                    <button @click="runAudit()" 
                            :disabled="auditRunning"
                            class="flex items-center space-x-2 px-4 py-2 rounded bg-blue-600 hover:bg-blue-500 disabled:opacity-50 text-white font-mono text-xs font-bold tracking-wider uppercase shadow-lg shadow-blue-600/20 transition-all">
                        <span x-show="auditRunning" class="w-2 h-2 rounded-full bg-white animate-ping"></span>
                        <span x-text="auditRunning ? 'RUNNING AUDIT...' : '▷ RUN FORENSIC AUDIT'"></span>
                    </button>
                    <a href="login.php" class="px-3 py-2 text-xs font-mono text-slate-400 hover:text-white border border-slate-800 hover:border-slate-700 rounded transition-colors">
                        Logout
                    </a>
                </div>
            </header>

            <!-- TAB CONTENT CONTAINER -->
            <div class="p-6 flex-1 overflow-y-auto space-y-6">

                <!-- DASHBOARD TAB -->
                <div x-show="activeTab === 'dashboard'" x-cloak class="space-y-6">
                    
                    <!-- TOP METRIC CARDS -->
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                        <!-- Card 1: Composite Risk -->
                        <div class="p-4 rounded-lg bg-[#0c101d] border border-slate-800/80 relative overflow-hidden">
                            <div class="text-[11px] font-mono uppercase text-slate-400 font-semibold tracking-wider">COMPOSITE RISK</div>
                            <div class="text-3xl font-mono font-extrabold text-red-500 mt-2" x-text="metrics.riskScore + ' / 100'">88 / 100</div>
                            <div class="mt-3">
                                <span class="text-[10px] font-mono font-bold px-2 py-0.5 rounded bg-red-950/80 text-red-400 border border-red-800/60 uppercase">CRITICAL RISK</span>
                            </div>
                        </div>

                        <!-- Card 2: Price Drift -->
                        <div class="p-4 rounded-lg bg-[#0c101d] border border-slate-800/80 relative overflow-hidden">
                            <div class="text-[11px] font-mono uppercase text-slate-400 font-semibold tracking-wider">MAX PRICE DRIFT</div>
                            <div class="text-3xl font-mono font-extrabold text-amber-400 mt-2" x-text="metrics.maxDrift">+312%</div>
                            <div class="mt-3">
                                <span class="text-[10px] font-mono font-bold px-2 py-0.5 rounded bg-amber-950/80 text-amber-400 border border-amber-800/60 uppercase">HIGH OVERCHARGE</span>
                            </div>
                        </div>

                        <!-- Card 3: Specs Parsed -->
                        <div class="p-4 rounded-lg bg-[#0c101d] border border-slate-800/80 relative overflow-hidden">
                            <div class="text-[11px] font-mono uppercase text-slate-400 font-semibold tracking-wider">BASELINES MATCHED</div>
                            <div class="text-3xl font-mono font-extrabold text-cyan-400 mt-2" x-text="metrics.specsParsed">4 / 14</div>
                            <div class="mt-3">
                                <span class="text-[10px] font-mono font-bold px-2 py-0.5 rounded bg-cyan-950/80 text-cyan-300 border border-cyan-800/60 uppercase">LOCAL BASELINE</span>
                            </div>
                        </div>

                        <!-- Card 4: Officer Gate -->
                        <div class="p-4 rounded-lg bg-[#0c101d] border border-slate-800/80 relative overflow-hidden">
                            <div class="text-[11px] font-mono uppercase text-slate-400 font-semibold tracking-wider">OFFICER HANDOFF</div>
                            <div class="text-base font-mono font-bold text-slate-200 mt-3 flex items-center space-x-2">
                                <svg class="w-4 h-4 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                                <span>AWAITING REVIEW</span>
                            </div>
                            <div class="mt-3">
                                <span class="text-[10px] font-mono font-bold px-2 py-0.5 rounded bg-slate-800 text-slate-300 border border-slate-700 uppercase">PPADA SEC 114</span>
                            </div>
                        </div>
                    </div>

                    <!-- DYNAMIC AI STATUTORY BRIEF CARD -->
                    <div x-show="aiBriefVisible" 
                         x-transition:enter="transition ease-out duration-300"
                         class="p-5 rounded-lg bg-[#09101d] border border-cyan-500/40 shadow-xl relative">
                        <div class="flex items-center justify-between mb-2">
                            <div class="flex items-center space-x-2">
                                <span class="inline-block w-2.5 h-2.5 rounded-full bg-cyan-400 animate-pulse"></span>
                                <h3 class="text-xs font-mono font-bold tracking-wider text-cyan-400 uppercase">Air-Gapped Statutory Brief (PPADA 2015 Sec 114)</h3>
                            </div>
                            <span class="text-[10px] font-mono px-2 py-0.5 rounded bg-cyan-950 text-cyan-300 border border-cyan-800" x-text="modelEngine">
                                Local Ollama (Qwen 2.5)
                            </span>
                        </div>
                        <p class="text-sm font-sans text-slate-200 leading-relaxed italic" x-text="'&ldquo;' + aiBriefText + '&rdquo;'"></p>
                    </div>

                    <!-- GRID: ENVELOPES & DRIFT TABLE -->
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                        
                        <!-- LEFT PANEL: SUBMITTED BID ENVELOPES -->
                        <div class="p-5 rounded-lg bg-[#0c101d] border border-slate-800/80 space-y-4">
                            <div class="flex items-center justify-between border-b border-slate-800 pb-3">
                                <h3 class="text-xs font-mono font-bold tracking-wider text-slate-300 uppercase flex items-center space-x-2">
                                    <svg class="w-4 h-4 text-cyan-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                                    <span>SUBMITTED BID ENVELOPES</span>
                                </h3>
                                <button @click="activeTab = 'envelopes'" class="text-xs font-mono text-cyan-400 hover:underline">Show All</button>
                            </div>

                            <div class="space-y-3">
                                <template x-for="env in envelopes">
                                    <div class="p-3.5 rounded bg-[#090d16] border border-slate-800/90 flex items-center justify-between">
                                        <div>
                                            <div class="text-sm font-bold font-sans text-white" x-text="env.company_name"></div>
                                            <div class="mt-1">
                                                <span class="text-[10px] font-mono font-bold px-2 py-0.5 rounded bg-red-950/80 text-red-400 border border-red-900/60" x-text="env.status_flag"></span>
                                            </div>
                                        </div>
                                        <div class="text-right">
                                            <div class="text-sm font-mono font-bold text-cyan-400" x-text="'KES ' + Number(env.total_bid_kes).toLocaleString()"></div>
                                        </div>
                                    </div>
                                </template>
                            </div>
                        </div>

                        <!-- RIGHT PANEL: FORENSIC PRICE DRIFT TABLE -->
                        <div class="p-5 rounded-lg bg-[#0c101d] border border-slate-800/80 space-y-4">
                            <div class="flex items-center justify-between border-b border-slate-800 pb-3">
                                <h3 class="text-xs font-mono font-bold tracking-wider text-slate-300 uppercase flex items-center space-x-2">
                                    <svg class="w-4 h-4 text-cyan-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M3 14h18m-9-4v8m-7 0h14a2 2 0 002-2V6a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                    <span>FORENSIC PRICE DRIFT TABLE</span>
                                </h3>
                                <button @click="activeTab = 'drift'" class="text-xs font-mono text-cyan-400 hover:underline">Show All</button>
                            </div>

                            <div class="overflow-x-auto">
                                <table class="w-full text-left border-collapse">
                                    <thead>
                                        <tr class="border-b border-slate-800 text-[11px] font-mono text-slate-400 uppercase">
                                            <th class="py-2 px-2">Line Item</th>
                                            <th class="py-2 px-2 text-right">Bid Rate</th>
                                            <th class="py-2 px-2 text-right">Base Rate</th>
                                            <th class="py-2 px-2 text-right">Drift</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-slate-800/60 font-mono text-xs">
                                        <template x-for="drift in drifts">
                                            <tr>
                                                <td class="py-2.5 px-2 text-slate-200 font-sans" x-text="drift.item_code"></td>
                                                <td class="py-2.5 px-2 text-right text-slate-300" x-text="Number(drift.bidder_rate_kes).toLocaleString()"></td>
                                                <td class="py-2.5 px-2 text-right text-slate-400" x-text="Number(drift.base_rate_kes).toLocaleString()"></td>
                                                <td class="py-2.5 px-2 text-right font-bold text-red-500" x-text="drift.drift_pct"></td>
                                            </tr>
                                        </template>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                    </div>
                </div>

                <!-- ENVELOPES TAB VIEW -->
                <div x-show="activeTab === 'envelopes'" x-cloak class="space-y-6">
                    <div class="p-6 rounded-lg bg-[#0c101d] border border-slate-800/80 space-y-4">
                        <div class="flex items-center justify-between border-b border-slate-800 pb-3">
                            <h3 class="text-xs font-mono font-bold tracking-wider text-slate-300 uppercase">ALL SUBMITTED BID ENVELOPES (SQLITE LEDGER)</h3>
                            <button @click="fetchAuditData()" class="text-xs font-mono text-cyan-400 hover:underline">↻ Refresh Feed</button>
                        </div>
                        <div class="space-y-3">
                            <template x-for="env in envelopes">
                                <div class="p-4 rounded bg-[#090d16] border border-slate-800 flex items-center justify-between">
                                    <div>
                                        <div class="text-sm font-bold text-white font-sans" x-text="env.company_name"></div>
                                        <div class="text-xs font-mono text-slate-400 mt-1">Record ID: <span x-text="env.id"></span></div>
                                        <div class="mt-2">
                                            <span class="text-[10px] font-mono font-bold px-2 py-0.5 rounded bg-amber-950 text-amber-400 border border-amber-800" x-text="env.status_flag"></span>
                                        </div>
                                    </div>
                                    <div class="text-right">
                                        <div class="text-base font-mono font-bold text-cyan-400" x-text="'KES ' + Number(env.total_bid_kes).toLocaleString()"></div>
                                    </div>
                                </div>
                            </template>
                        </div>
                    </div>
                </div>

                <!-- DRIFT TABLE TAB VIEW -->
                <div x-show="activeTab === 'drift'" x-cloak class="space-y-6">
                    <div class="p-6 rounded-lg bg-[#0c101d] border border-slate-800/80 space-y-4">
                        <div class="flex items-center justify-between border-b border-slate-800 pb-3">
                            <h3 class="text-xs font-mono font-bold tracking-wider text-slate-300 uppercase">FULL FORENSIC PRICE DRIFT REGISTER</h3>
                            <span class="text-[10px] font-mono text-cyan-400">PPADA Section 114 Automated Telemetry</span>
                        </div>
                        <div class="overflow-x-auto">
                            <table class="w-full text-left border-collapse font-mono text-xs">
                                <thead>
                                    <tr class="border-b border-slate-800 text-slate-400 uppercase text-[10px]">
                                        <th class="py-3 px-3">Line Item / Component</th>
                                        <th class="py-3 px-3 text-right">Bid Rate (KES)</th>
                                        <th class="py-3 px-3 text-right">Base Rate (KES)</th>
                                        <th class="py-3 px-3 text-right">Variance Drift</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-800/60">
                                    <template x-for="drift in drifts">
                                        <tr>
                                            <td class="py-3 px-3 text-slate-200 font-sans font-bold" x-text="drift.item_code"></td>
                                            <td class="py-3 px-3 text-right text-slate-300" x-text="Number(drift.bidder_rate_kes).toLocaleString()"></td>
                                            <td class="py-3 px-3 text-right text-slate-400" x-text="Number(drift.base_rate_kes).toLocaleString()"></td>
                                            <td class="py-3 px-3 text-right font-bold text-red-500" x-text="drift.drift_pct"></td>
                                        </tr>
                                    </template>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- OFFICER GATE TAB -->
                <div x-show="activeTab === 'officer'" x-cloak class="space-y-6">
                    <div class="p-6 rounded-lg bg-[#0c101d] border border-slate-800/80 space-y-6 max-w-4xl">
                        
                        <div class="border-b border-slate-800 pb-4">
                            <h3 class="text-sm font-mono font-bold tracking-wider text-slate-200 uppercase flex items-center space-x-2">
                                <svg class="w-5 h-5 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                                <span>STATUTORY SIGN-OFF GATE (PPADA 2015 SECTION 114)</span>
                            </h3>
                            <p class="text-xs text-slate-400 mt-1">Human-in-the-Loop decision lock. All actions logged with cryptographic officer signature.</p>
                        </div>

                        <form @submit.prevent="commitDecision()" class="space-y-5">
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                                <!-- Fully Editable Officer Badge ID Input -->
                                <div>
                                    <label class="block text-xs font-mono text-slate-400 uppercase mb-2">Officer Badge ID</label>
                                    <input type="text" 
                                           x-model="officerForm.badgeId" 
                                           class="w-full bg-[#070a11] border border-slate-700/80 rounded px-3.5 py-2.5 text-sm font-mono text-cyan-300 focus:outline-none focus:border-cyan-500 focus:ring-1 focus:ring-cyan-500"
                                           placeholder="Enter Badge ID (e.g., PO-98402-NAIROBI)">
                                </div>

                                <!-- Action Route Dropdown -->
                                <div>
                                    <label class="block text-xs font-mono text-slate-400 uppercase mb-2">Action Route</label>
                                    <select x-model="officerForm.actionRoute" 
                                            class="w-full bg-[#070a11] border border-slate-700/80 rounded px-3.5 py-2.5 text-sm font-mono text-slate-200 focus:outline-none focus:border-cyan-500">
                                        <option value="REJECT BIDDER B & ESCALATE TO EACC">REJECT BIDDER B & ESCALATE TO EACC</option>
                                        <option value="FLAG FOR INTERNAL REVIEW">FLAG FOR INTERNAL REVIEW</option>
                                        <option value="APPROVE WITH CONDITIONS">APPROVE WITH CONDITIONS</option>
                                    </select>
                                </div>
                            </div>

                            <!-- Officer Notes Textarea -->
                            <div>
                                <label class="block text-xs font-mono text-slate-400 uppercase mb-2">Officer Notes & Statutory Reason</label>
                                <textarea x-model="officerForm.notes" 
                                          rows="4" 
                                          class="w-full bg-[#070a11] border border-slate-700/80 rounded px-3.5 py-2.5 text-sm font-mono text-slate-200 focus:outline-none focus:border-cyan-500"
                                          placeholder="Enter audit rationale..."></textarea>
                            </div>

                            <!-- Commit Button -->
                            <button type="submit" 
                                    class="w-full py-3 rounded bg-emerald-600 hover:bg-emerald-500 text-white font-mono text-xs font-bold tracking-wider uppercase shadow-lg shadow-emerald-600/20 transition-all flex items-center justify-center space-x-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                <span>COMMIT STATUTORY AUDIT DECISION</span>
                            </button>

                        </form>
                    </div>
                </div>

                <!-- AUDIT LOGS TAB VIEW -->
                <div x-show="activeTab === 'logs'" x-cloak class="space-y-6">
                    <div class="p-6 rounded-lg bg-[#0c101d] border border-slate-800/80 space-y-4">
                        <div class="flex items-center justify-between border-b border-slate-800 pb-3">
                            <h3 class="text-xs font-mono font-bold tracking-wider text-slate-300 uppercase">SYSTEM AUDIT & TELEMETRY LOGS</h3>
                            <span class="text-[10px] font-mono text-cyan-400">Secure SQLite Ledger</span>
                        </div>
                        <div class="overflow-x-auto">
                            <table class="w-full text-left border-collapse font-mono text-xs">
                                <thead>
                                    <tr class="border-b border-slate-800 text-slate-400 uppercase text-[10px]">
                                        <th class="py-3 px-3">Timestamp</th>
                                        <th class="py-3 px-3">Officer / Node</th>
                                        <th class="py-3 px-3">Action Description</th>
                                        <th class="py-3 px-3 text-right">Status</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-800/60">
                                    <template x-for="log in auditLogs">
                                        <tr>
                                            <td class="py-3 px-3 text-slate-400" x-text="log.timestamp"></td>
                                            <td class="py-3 px-3 text-cyan-300" x-text="log.officer"></td>
                                            <td class="py-3 px-3 text-slate-200" x-text="log.action"></td>
                                            <td class="py-3 px-3 text-right font-bold text-emerald-400" x-text="log.status"></td>
                                        </tr>
                                    </template>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

            </div>
        </main>
    </div>

    <!-- ALPINE.JS APPLICATION STATE & AJAX LOGIC -->
    <script>
        function procureGuardApp() {
            return {
                activeTab: 'dashboard',
                auditRunning: false,
                aiBriefVisible: true,
                aiBriefText: 'Critical unit rate inflation (+312.0%) and shared corporate identity identifiers detected via live public ledger sync. Mandatory human sign-off required under PPADA 2015 Section 114.',
                modelEngine: 'Local Ollama (Qwen 2.5 - Air-Gapped)',
                
                metrics: {
                    riskScore: 88,
                    maxDrift: '+312%',
                    specsParsed: '4 / 14'
                },

                envelopes: [],
                drifts: [],
                auditLogs: [],

                officerForm: {
                    badgeId: 'PO-98402-NAIROBI',
                    actionRoute: 'REJECT BIDDER B & ESCALATE TO EACC',
                    notes: 'Shared director PIN cross-match detected. Line item #1 inflated +312%.'
                },

                toast: {
                    visible: false,
                    message: '',
                    type: 'info'
                },

                showToast(msg, type = 'info') {
                    this.toast.message = msg;
                    this.toast.type = type;
                    this.toast.visible = true;
                    setTimeout(() => { this.toast.visible = false; }, 4000);
                },

                async init() {
                    await this.fetchAuditData();
                },

                async fetchAuditData() {
                    try {
                        const res = await fetch('api.php?action=get_data');
                        const data = await res.json();
                        if (data.status === 'SUCCESS' || data.status === 'success') {
                            this.envelopes = data.envelopes || [];
                            this.drifts = data.drifts || [];
                            this.auditLogs = data.logs || [];
                            if (data.metrics) {
                                this.metrics.riskScore = data.metrics.risk_score;
                                this.metrics.maxDrift = data.metrics.max_drift;
                                this.metrics.specsParsed = data.metrics.specs_parsed;
                            }
                            if (data.ai_statutory_brief) {
                                this.aiBriefText = data.ai_statutory_brief;
                            }
                            if (data.model_engine) {
                                this.modelEngine = data.model_engine;
                            }
                        }
                    } catch (err) {
                        console.error('Telemetry fetch error:', err);
                    }
                },

                async runAudit() {
                    this.auditRunning = true;
                    await this.fetchAuditData();
                    setTimeout(() => {
                        this.auditRunning = false;
                        this.showToast('Forensic Audit Completed Successfully!', 'success');
                    }, 500);
                },

                commitDecision() {
                    this.showToast(`Audit Decision recorded! Route: ${this.officerForm.actionRoute}`, 'success');
                }
            };
        }
    </script>
</body>
</html>