<style>
:root {
    --bg: #0a0e14;
    --bg-glow: radial-gradient(900px 480px at 12% -12%, rgba(59, 130, 246, .16), transparent 60%),
                radial-gradient(800px 420px at 100% 0%, rgba(16, 185, 129, .10), transparent 55%);
    --surface: #0f1620;
    --surface-2: #131d2b;
    --surface-3: #172232;
    --border: #223044;
    --border-soft: #1a2534;
    --text: #e6edf6;
    --muted: #94a6bd;
    --faint: #64748b;
    --accent: #3b82f6;
    --accent-strong: #2563eb;
    --accent-soft: rgba(59, 130, 246, .14);
    --good: #10b981;
    --warn: #f59e0b;
    --bad: #ef4444;
    --radius: 14px;
    --radius-sm: 10px;
    --shadow: 0 18px 40px -24px rgba(0, 0, 0, .9);
    --mono: ui-monospace, SFMono-Regular, "SF Mono", Menlo, Consolas, monospace;
    --sans: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Inter, Helvetica, Arial, sans-serif;
    color-scheme: light dark;
}

@media (prefers-color-scheme: light) {
    :root:not([data-theme="dark"]) {
        --bg: #f8fafc;
        --bg-glow: radial-gradient(900px 480px at 12% -12%, rgba(59, 130, 246, .08), transparent 60%),
                    radial-gradient(800px 420px at 100% 0%, rgba(16, 185, 129, .06), transparent 55%);
        --surface: #f1f5f9;
        --surface-2: #e2e8f0;
        --surface-3: #cbd5e1;
        --border: #94a3b8;
        --border-soft: #d0d9e5;
        --text: #0f172a;
        --muted: #64748b;
        --faint: #94a3b7;
        --accent: #3b82f6;
        --accent-strong: #2563eb;
        --accent-soft: rgba(59, 130, 246, .12);
        --good: #059669;
        --warn: #d97706;
        --bad: #dc2626;
    }
}

[data-theme="dark"] {
    --bg: #0a0e14;
    --bg-glow: radial-gradient(900px 480px at 12% -12%, rgba(59, 130, 246, .16), transparent 60%),
                radial-gradient(800px 420px at 100% 0%, rgba(16, 185, 129, .10), transparent 55%);
    --surface: #0f1620;
    --surface-2: #131d2b;
    --surface-3: #172232;
    --border: #223044;
    --border-soft: #1a2534;
    --text: #e6edf6;
    --muted: #94a6bd;
    --faint: #64748b;
    --accent: #3b82f6;
    --accent-strong: #2563eb;
    --accent-soft: rgba(59, 130, 246, .14);
    --good: #10b981;
    --warn: #f59e0b;
    --bad: #ef4444;
}

[data-theme="light"] {
    --bg: #f8fafc;
    --bg-glow: radial-gradient(900px 480px at 12% -12%, rgba(59, 130, 246, .08), transparent 60%),
                radial-gradient(800px 420px at 100% 0%, rgba(16, 185, 129, .06), transparent 55%);
    --surface: #f1f5f9;
    --surface-2: #e2e8f0;
    --surface-3: #cbd5e1;
    --border: #94a3b8;
    --border-soft: #d0d9e5;
    --text: #0f172a;
    --muted: #64748b;
    --faint: #94a3b7;
    --accent: #3b82f6;
    --accent-strong: #2563eb;
    --accent-soft: rgba(59, 130, 246, .12);
    --good: #059669;
    --warn: #d97706;
    --bad: #dc2626;
}

* { box-sizing: border-box; }

html, body { height: 100%; }

body {
    margin: 0;
    background: var(--bg-glow), var(--bg);
    color: var(--text);
    font-family: var(--sans);
    font-size: 14px;
    line-height: 1.5;
    -webkit-font-smoothing: antialiased;
}

a { color: var(--accent); text-decoration: none; }
a:hover { text-decoration: underline; }

svg use, svg { display: inline-block; }

.bones-shell { min-height: 100%; display: flex; flex-direction: column; }

.bones-header {
    position: sticky;
    top: 0;
    z-index: 20;
    background: rgba(10, 14, 20, .82);
    backdrop-filter: blur(12px);
    border-bottom: 1px solid var(--border-soft);
}

.bones-header__inner {
    max-width: 1180px;
    margin: 0 auto;
    padding: 14px 24px;
    display: flex;
    align-items: center;
    gap: 24px;
}

.bones-brand { display: flex; align-items: center; gap: 12px; flex-shrink: 0; }
.bones-brand__mark {
    width: 38px; height: 38px;
    display: grid; place-items: center;
    border-radius: 12px;
    background: linear-gradient(160deg, rgba(59,130,246,.22), rgba(16,185,129,.14));
    border: 1px solid var(--border);
    color: var(--accent);
}
.bones-brand__mark svg { width: 22px; height: 22px; }
.bones-brand__text { display: flex; flex-direction: column; line-height: 1.2; }
.bones-brand__text strong { font-size: 15px; letter-spacing: .2px; }
.bones-brand__text small { color: var(--faint); font-size: 11px; }

.bones-tabs { display: flex; gap: 4px; margin-left: 8px; flex-wrap: wrap; }
.bones-tab {
    display: inline-flex; align-items: center; gap: 7px;
    padding: 8px 12px;
    border: 1px solid transparent;
    border-radius: 999px;
    background: transparent;
    color: var(--muted);
    font: inherit; font-size: 13px; font-weight: 500;
    cursor: pointer;
    transition: .15s ease;
}
.bones-tab svg { width: 16px; height: 16px; }
.bones-tab:hover { color: var(--text); background: var(--surface-2); }
.bones-tab[aria-selected="true"] {
    color: var(--text);
    background: var(--accent-soft);
    border-color: rgba(59,130,246,.35);
}
.bones-tab__count {
    font-size: 11px; font-weight: 600;
    padding: 1px 7px; border-radius: 999px;
    background: var(--surface-3); color: var(--muted);
}
.bones-tab[aria-selected="true"] .bones-tab__count { background: var(--accent); color: #fff; }

.bones-actions { margin-left: auto; display: flex; gap: 8px; }

.bones-btn {
    display: inline-flex; align-items: center; gap: 7px;
    padding: 8px 14px; border-radius: var(--radius-sm);
    font: inherit; font-size: 13px; font-weight: 600;
    border: 1px solid var(--border);
    cursor: pointer; transition: .15s ease;
}
.bones-btn svg { width: 15px; height: 15px; }
.bones-btn--ghost { background: var(--surface-2); color: var(--muted); }
.bones-btn--ghost:hover { color: var(--text); border-color: var(--accent); text-decoration: none; }
.bones-btn--primary { background: var(--accent-strong); border-color: var(--accent-strong); color: #fff; }
.bones-btn--primary:hover { background: var(--accent); text-decoration: none; }

.bones-main { max-width: 1180px; width: 100%; margin: 0 auto; padding: 24px; flex: 1; }

.bones-panel { animation: bones-in .28s ease both; }
.bones-panel.is-hidden { display: none; }
@keyframes bones-in { from { opacity: 0; transform: translateY(6px); } to { opacity: 1; transform: none; } }

.bones-toolbar {
    display: flex; align-items: center; gap: 12px; flex-wrap: wrap;
    padding: 12px; margin-bottom: 18px;
    background: var(--surface); border: 1px solid var(--border-soft);
    border-radius: var(--radius); box-shadow: var(--shadow);
}
.bones-search {
    display: flex; align-items: center; gap: 8px; flex: 1 1 260px;
    padding: 8px 12px; background: var(--surface-2);
    border: 1px solid var(--border); border-radius: var(--radius-sm);
}
.bones-search svg { width: 16px; height: 16px; color: var(--faint); }
.bones-search input {
    flex: 1; border: 0; background: transparent; color: var(--text);
    font: inherit; font-size: 13px; outline: none;
}
.bones-filters { display: flex; gap: 8px; flex-wrap: wrap; }
.bones-filters select, .bones-filters button {
    padding: 8px 10px; border-radius: var(--radius-sm);
    background: var(--surface-2); color: var(--muted);
    border: 1px solid var(--border); font: inherit; font-size: 12.5px;
    cursor: pointer;
}
.bones-filters button[aria-pressed="true"] { color: #fff; background: var(--accent-strong); border-color: var(--accent-strong); }
.bones-toolbar__meta { margin-left: auto; color: var(--faint); font-size: 12.5px; white-space: nowrap; }

.bones-grid { display: grid; gap: 14px; }
.bones-grid--stats { grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); }
.bones-grid--cards { grid-template-columns: repeat(auto-fill, minmax(320px, 1fr)); }

.bones-stat {
    padding: 16px; border-radius: var(--radius);
    background: var(--surface); border: 1px solid var(--border-soft);
    display: flex; align-items: center; gap: 14px;
}
.bones-stat__icon {
    width: 42px; height: 42px; border-radius: 12px;
    display: grid; place-items: center; flex-shrink: 0;
    background: var(--accent-soft); color: var(--accent);
}
.bones-stat__icon svg { width: 20px; height: 20px; }
.bones-stat__value { font-size: 24px; font-weight: 700; line-height: 1.1; }
.bones-stat__label { color: var(--muted); font-size: 12.5px; }

.bones-card {
    padding: 16px; border-radius: var(--radius);
    background: var(--surface); border: 1px solid var(--border-soft);
    transition: .16s ease;
}
.bones-card:hover { border-color: var(--border); transform: translateY(-2px); box-shadow: var(--shadow); }
.bones-card--issue { border-color: rgba(239,68,68,.4); }
.bones-card__top { display: flex; align-items: center; gap: 8px; margin-bottom: 10px; }
.bones-card__name { margin-left: auto; color: var(--faint); font-size: 12px; font-family: var(--mono); }
.bones-card__uri {
    display: flex; align-items: center; gap: 8px;
    font-family: var(--mono); font-size: 14px; font-weight: 600;
    word-break: break-all;
}
.bones-card__meta { margin-top: 8px; color: var(--muted); font-size: 12.5px; display: flex; flex-direction: column; gap: 4px; }
.bones-card__meta code { font-family: var(--mono); color: #cbd5e1; }

.bones-badges { display: flex; gap: 6px; flex-wrap: wrap; }
.bones-badge {
    display: inline-flex; align-items: center; gap: 5px;
    padding: 2px 9px; border-radius: 999px;
    font-size: 11px; font-weight: 700; letter-spacing: .3px;
    background: var(--surface-3); color: var(--muted);
    border: 1px solid transparent;
}
.bones-badge--ghost { font-weight: 600; letter-spacing: 0; font-family: var(--mono); }
.bones-badge--accent { color: #bfdbfe; background: rgba(59,130,246,.16); }
.bones-badge--good { color: #6ee7b7; background: rgba(16,185,129,.16); }
.bones-badge--warn { color: #fcd34d; background: rgba(245,158,11,.16); }
.bones-badge--bad  { color: #fca5a5; background: rgba(239,68,68,.16); }
.bones-badge--purple { color: #d8b4fe; background: rgba(168,85,247,.16); }

.m-get { color: #6ee7b7; background: rgba(16,185,129,.16); }
.m-post { color: #93c5fd; background: rgba(59,130,246,.18); }
.m-put { color: #fcd34d; background: rgba(245,158,11,.16); }
.m-patch { color: #fdba74; background: rgba(249,115,22,.16); }
.m-delete { color: #fca5a5; background: rgba(239,68,68,.16); }
.m-options { color: #cbd5e1; background: rgba(100,116,139,.2); }
.m-head { color: #d8b4fe; background: rgba(168,85,247,.16); }

.bones-issues { margin-top: 12px; display: flex; flex-direction: column; gap: 6px; }
.bones-issue {
    display: flex; align-items: flex-start; gap: 8px;
    padding: 8px 10px; border-radius: var(--radius-sm);
    background: rgba(239,68,68,.08); border-left: 3px solid var(--bad);
    color: #fecaca; font-size: 12.5px;
}
.bones-issue svg { width: 15px; height: 15px; flex-shrink: 0; margin-top: 1px; }

.bones-copy {
    border: 0; background: transparent; color: var(--faint);
    cursor: pointer; padding: 2px; border-radius: 6px; line-height: 0;
    opacity: 0; transition: .15s ease;
}
.bones-copy svg { width: 14px; height: 14px; }
.bones-card:hover .bones-copy, .bones-row:hover .bones-copy { opacity: 1; }
.bones-copy:hover { color: var(--accent); background: var(--accent-soft); }

.bones-table { width: 100%; border-collapse: collapse; font-size: 13px; }
.bones-table th {
    text-align: left; font-size: 11px; text-transform: uppercase; letter-spacing: .6px;
    color: var(--faint); font-weight: 700; padding: 10px 14px;
    border-bottom: 1px solid var(--border);
}
.bones-table td { padding: 11px 14px; border-bottom: 1px solid var(--border-soft); vertical-align: middle; }
.bones-row { transition: background .12s ease; }
.bones-row:hover { background: var(--surface-2); }
.bones-mono { font-family: var(--mono); }
.bones-dim { color: var(--muted); }
.bones-wrap { background: var(--surface); border: 1px solid var(--border-soft); border-radius: var(--radius); overflow: hidden; box-shadow: var(--shadow); }

.bones-empty { padding: 56px 24px; text-align: center; color: var(--faint); }
.bones-empty svg { width: 34px; height: 34px; opacity: .5; margin-bottom: 10px; }

.bones-tree { display: flex; flex-direction: column; gap: 2px; padding: 6px; }
.bones-node { border-radius: var(--radius-sm); }
.bones-node__head {
    display: flex; align-items: center; gap: 8px; padding: 8px 10px;
    cursor: pointer; border-radius: var(--radius-sm); transition: background .12s ease;
}
.bones-node__head:hover { background: var(--surface-2); }
.bones-node__chevron { width: 14px; height: 14px; color: var(--faint); transition: transform .18s ease; }
.bones-node.is-open > .bones-node__head > .bones-node__chevron { transform: rotate(90deg); }
.bones-node__children { margin-left: 16px; display: none; border-left: 1px dashed var(--border); padding-left: 8px; }
.bones-node.is-open > .bones-node__children { display: block; }
.bones-node__label { font-family: var(--mono); font-size: 13px; }

.bones-graph { width: 100%; height: 70vh; min-height: 460px; background: var(--surface); border: 1px solid var(--border-soft); border-radius: var(--radius); overflow: hidden; }
.bones-graph svg { width: 100%; height: 100%; }
.bones-graph .edge { fill: none; stroke: var(--border); stroke-width: 1.4; }
.bones-graph .edge.is-lit { stroke: var(--accent); stroke-width: 2; }
.bones-graph .gnode rect { fill: var(--surface-2); stroke: var(--border); rx: 8; }
.bones-graph .gnode text { fill: var(--muted); font-family: var(--mono); font-size: 11px; }
.bones-graph .gnode.is-lit rect { stroke: var(--accent); fill: var(--accent-soft); }
.bones-graph .gnode.is-lit text { fill: var(--text); }
.bones-graph .gnode[data-kind="layout"] rect { stroke: rgba(59,130,246,.6); }
.bones-graph .gnode[data-kind="component"] rect { stroke: rgba(168,85,247,.6); }

.bones-section-title { font-size: 13px; text-transform: uppercase; letter-spacing: .8px; color: var(--faint); margin: 26px 0 12px; font-weight: 700; }
.bones-section-title:first-child { margin-top: 0; }

.bones-footer {
    max-width: 1180px; width: 100%; margin: 0 auto; padding: 20px 24px 32px;
    color: var(--faint); font-size: 12px; display: flex; gap: 8px; align-items: center;
}
.bones-dot { opacity: .5; }

.bones-toast {
    position: fixed; right: 20px; bottom: 20px; z-index: 60;
    padding: 11px 16px; border-radius: var(--radius-sm);
    background: var(--surface-3); border: 1px solid var(--border); color: var(--text);
    box-shadow: var(--shadow); font-size: 13px;
    animation: bones-in .2s ease both;
}
.bones-toast[hidden] { display: none; }

.bones-hint { color: var(--faint); font-size: 12.5px; margin: 0 0 16px; }
.bones-hint code { font-family: var(--mono); color: var(--muted); }

@media (max-width: 860px) {
    .bones-header__inner { flex-wrap: wrap; gap: 12px; }
    .bones-tabs { order: 3; width: 100%; margin-left: 0; }
    .bones-actions { margin-left: auto; }
    .bones-toolbar__meta { width: 100%; margin-left: 0; }
}
</style>
