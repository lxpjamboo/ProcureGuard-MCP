const views = ['dashboard', 'envelopes', 'drift_table', 'collusion', 'officer_gate', 'audit_logs'];

function switchView(selectedView) {
    views.forEach(view => {
        const el = document.getElementById(`view-${view}`);
        if (el) el.classList.add('hidden');
    });
    
    // Show active view
    const activeEl = document.getElementById(`view-${selectedView}`);
    if (activeEl) activeEl.classList.remove('hidden');
}