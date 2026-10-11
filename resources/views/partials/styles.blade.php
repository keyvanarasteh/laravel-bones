<style>
:root {
    --bg: #0a0e14;
    --bg-glow: radial-gradient(900px 480px at 12% -12%, rgba(59, 130, 246, .14), transparent 60%),
                radial-gradient(800px 420px at 100% 0%, rgba(16, 185, 129, .09), transparent 55%);
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
    --header-bg: rgba(10, 14, 20, .84);
    --radius: 9px;
    --radius-sm: 6px;
    --shadow: 0 14px 34px -26px rgba(0, 0, 0, .95);
    --mono: ui-monospace, SFMono-Regular, "SF Mono", Menlo, Consolas, monospace;
    --sans: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Inter, Helvetica, Arial, sans-serif;
    color-scheme: dark;
}

@media (prefers-color-scheme: light) {
    :root:not([data-theme="dark"]) {
        --bg: #f8fafc;
        --bg-glow: radial-gradient(900px 480px at 12% -12%, rgba(59, 130, 246, .07), transparent 60%),
                    radial-gradient(800px 420px at 100% 0%, rgba(16, 185, 129, .05), transparent 55%);
        --surface: #ffffff;
        --surface-2: #f1f5f9;
        --surface-3: #e2e8f0;
        --border: #cbd5e1;
        --border-soft: #e2e8f0;
        --text: #0f172a;
        --muted: #475569;
        --faint: #94a3b8;
        --accent: #3b82f6;
        --accent-strong: #2563eb;
        --accent-soft: rgba(59, 130, 246, .12);
        --good: #059669;
        --warn: #d97706;
        --bad: #dc2626;
        --header-bg: rgba(248, 250, 252, .86);
        --shadow: 0 12px 30px -24px rgba(15, 23, 42, .45);
        color-scheme: light;
    }
}

[data-theme="dark"] {
    --bg: #0a0e14;
    --bg-glow: radial-gradient(900px 480px at 12% -12%, rgba(59, 130, 246, .14), transparent 60%),
                radial-gradient(800px 420px at 100% 0%, rgba(16, 185, 129, .09), transparent 55%);
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
    --header-bg: rgba(10, 14, 20, .84);
    --shadow: 0 14px 34px -26px rgba(0, 0, 0, .95);
    color-scheme: dark;
}

[data-theme="light"] {
    --bg: #f8fafc;
    --bg-glow: radial-gradient(900px 480px at 12% -12%, rgba(59, 130, 246, .07), transparent 60%),
                radial-gradient(800px 420px at 100% 0%, rgba(16, 185, 129, .05), transparent 55%);
    --surface: #ffffff;
    --surface-2: #f1f5f9;
    --surface-3: #e2e8f0;
    --border: #cbd5e1;
    --border-soft: #e2e8f0;
    --text: #0f172a;
    --muted: #475569;
    --faint: #94a3b8;
    --accent: #3b82f6;
    --accent-strong: #2563eb;
    --accent-soft: rgba(59, 130, 246, .12);
    --good: #059669;
    --warn: #d97706;
    --bad: #dc2626;
    --header-bg: rgba(248, 250, 252, .86);
    --shadow: 0 12px 30px -24px rgba(15, 23, 42, .45);
    color-scheme: light;
}

* { box-sizing: border-box; }

html, body { height: 100%; }

body {
    margin: 0;
    background: var(--bg-glow), var(--bg);
    color: var(--text);
    font-family: var(--sans);
    font-size: 12.5px;
    line-height: 1.45;
    -webkit-font-smoothing: antialiased;
}

svg { width: 14px; height: 14px; flex: 0 0 auto; }

a { color: var(--accent); text-decoration: none; }
a:hover { text-decoration: underline; }

.b-shell { min-height: 100%; display: flex; flex-direction: column; }

/* ----------------------------------------------------------------- header */

.b-header {
    position: sticky;
    top: 0;
    z-index: 30;
    background: var(--header-bg);
    backdrop-filter: blur(12px);
    border-bottom: 1px solid var(--border-soft);
}

.b-header__inner {
    max-width: 1360px;
    margin: 0 auto;
    padding: 8px 18px;
    display: flex;
    align-items: center;
    gap: 14px;
}

.b-brand { display: flex; align-items: center; gap: 9px; flex-shrink: 0; color: inherit; }
.b-brand:hover { text-decoration: none; }
.b-brand__mark {
    width: 28px; height: 28px;
    display: grid; place-items: center;
    border-radius: 8px;
    background: linear-gradient(160deg, rgba(59,130,246,.22), rgba(16,185,129,.14));
    border: 1px solid var(--border);
    color: var(--accent);
}
.b-brand__mark svg { width: 17px; height: 17px; }
.b-brand__text { display: flex; flex-direction: column; line-height: 1.15; }
.b-brand__text strong { font-size: 12.5px; letter-spacing: .2px; }
.b-brand__text small { color: var(--faint); font-size: 10px; }

.b-tabs { display: flex; gap: 2px; margin-left: 6px; flex-wrap: wrap; }
.b-tab {
    display: inline-flex; align-items: center; gap: 6px;
    padding: 5px 9px;
    border: 1px solid transparent;
    border-radius: var(--radius-sm);
    background: transparent;
    color: var(--muted);
    font: inherit; font-size: 12px; font-weight: 500;
    cursor: pointer;
    transition: .14s ease;
}
.b-tab svg { width: 14px; height: 14px; }
.b-tab:hover { color: var(--text); background: var(--surface-2); }
.b-tab[aria-selected="true"] {
    color: var(--text);
    background: var(--accent-soft);
    border-color: rgba(59,130,246,.35);
}
.b-tab__count {
    font-size: 10px; font-weight: 600;
    padding: 0 6px; border-radius: 999px;
    background: var(--surface-3); color: var(--muted);
    line-height: 16px;
}
.b-tab[aria-selected="true"] .b-tab__count { background: var(--accent); color: #fff; }

.b-actions { margin-left: auto; display: flex; gap: 6px; }

.b-btn {
    display: inline-flex; align-items: center; gap: 6px;
    padding: 5px 9px; border-radius: var(--radius-sm);
    font: inherit; font-size: 12px; font-weight: 600;
    border: 1px solid var(--border);
    cursor: pointer; transition: .14s ease;
}
.b-btn svg { width: 13px; height: 13px; }
.b-btn--ghost { background: var(--surface-2); color: var(--muted); }
.b-btn--ghost:hover { color: var(--text); border-color: var(--accent); text-decoration: none; }
.b-btn--primary { background: var(--accent-strong); border-color: var(--accent-strong); color: #fff; }
.b-btn--primary:hover { background: var(--accent); text-decoration: none; }

.b-theme__sun { display: none; }
[data-theme="light"] .b-theme__sun { display: inline-block; }
[data-theme="light"] .b-theme__moon { display: none; }

/* ------------------------------------------------------------------- main */

.b-main { max-width: 1360px; width: 100%; margin: 0 auto; padding: 16px 18px; flex: 1; }

.b-panel { animation: b-in .24s ease both; }
.b-panel.is-hidden { display: none; }
@keyframes b-in { from { opacity: 0; transform: translateY(4px); } to { opacity: 1; transform: none; } }

.b-toolbar {
    display: flex; align-items: center; gap: 8px; flex-wrap: wrap;
    padding: 8px; margin-bottom: 12px;
    background: var(--surface); border: 1px solid var(--border-soft);
    border-radius: var(--radius); box-shadow: var(--shadow);
}
.b-search {
    display: flex; align-items: center; gap: 7px; flex: 1 1 240px;
    padding: 5px 9px; background: var(--surface-2);
    border: 1px solid var(--border); border-radius: var(--radius-sm);
}
.b-search svg { width: 14px; height: 14px; color: var(--faint); }
.b-search input {
    flex: 1; border: 0; background: transparent; color: var(--text);
    font: inherit; font-size: 12px; outline: none; min-width: 0;
}
.b-search input::-webkit-search-cancel-button { -webkit-appearance: none; }
.b-kbd {
    font-family: var(--mono); font-size: 10px; line-height: 15px;
    color: var(--faint); background: var(--surface-3);
    border: 1px solid var(--border); border-radius: 4px; padding: 0 4px;
}
.b-filters { display: flex; gap: 6px; flex-wrap: wrap; align-items: center; }
.b-select, .b-toggle, .b-segment button, .b-mini {
    font: inherit; font-size: 11.5px; cursor: pointer;
}
.b-select {
    padding: 5px 8px; border-radius: var(--radius-sm);
    background: var(--surface-2); color: var(--muted);
    border: 1px solid var(--border);
}
.b-toggle {
    display: inline-flex; align-items: center; gap: 5px;
    padding: 5px 8px; border-radius: var(--radius-sm);
    background: var(--surface-2); color: var(--muted);
    border: 1px solid var(--border);
}
.b-toggle__tick { display: none; width: 12px; height: 12px; }
.b-toggle[aria-pressed="true"] { color: #fff; background: var(--accent-strong); border-color: var(--accent-strong); }
.b-toggle[aria-pressed="true"] .b-toggle__tick { display: inline-block; }

.b-segment { display: inline-flex; background: var(--surface-2); border: 1px solid var(--border); border-radius: var(--radius-sm); overflow: hidden; }
.b-segment button {
    display: inline-flex; align-items: center; gap: 5px;
    padding: 5px 9px; border: 0; border-right: 1px solid var(--border);
    background: transparent; color: var(--muted);
}
.b-segment button svg { width: 12px; height: 12px; }
.b-segment button:last-child { border-right: 0; }
.b-segment button:hover { color: var(--text); }
.b-segment button[aria-pressed="true"] { background: var(--accent-soft); color: var(--text); }

.b-toolbar__meta { margin-left: auto; color: var(--faint); font-size: 11.5px; white-space: nowrap; }

/* ------------------------------------------------------------------ grids */

.b-grid { display: grid; gap: 10px; }
.b-grid--stats { grid-template-columns: repeat(auto-fit, minmax(150px, 1fr)); }
.b-grid--cards { grid-template-columns: repeat(auto-fill, minmax(282px, 1fr)); align-items: start; }

.b-stat {
    padding: 10px 12px; border-radius: var(--radius);
    background: var(--surface); border: 1px solid var(--border-soft);
    display: flex; align-items: center; gap: 10px;
}
.b-stat__icon {
    width: 30px; height: 30px; border-radius: 8px;
    display: grid; place-items: center; flex-shrink: 0;
    background: var(--accent-soft); color: var(--accent);
}
.b-stat__icon svg { width: 15px; height: 15px; }
.b-stat__value { font-size: 18px; font-weight: 700; line-height: 1.1; }
.b-stat__label { color: var(--muted); font-size: 11px; }

.b-card {
    padding: 11px 12px; border-radius: var(--radius);
    background: var(--surface); border: 1px solid var(--border-soft);
    transition: .14s ease;
}
.b-card:hover { border-color: var(--border); box-shadow: var(--shadow); }
.b-card--issue { border-color: rgba(239,68,68,.4); }
.b-card__top { display: flex; align-items: center; gap: 6px; margin-bottom: 7px; flex-wrap: wrap; }
.b-card__name { margin-left: auto; color: var(--faint); font-size: 11px; font-family: var(--mono); }
.b-card__uri {
    display: flex; align-items: center; gap: 7px;
    font-family: var(--mono); font-size: 12.5px; font-weight: 600;
    word-break: break-all; line-height: 1.35;
}
.b-card__meta { margin-top: 6px; color: var(--muted); font-size: 11.5px; display: flex; flex-direction: column; gap: 3px; min-width: 0; }
.b-card__meta span { display: inline-flex; align-items: center; gap: 5px; min-width: 0; }
.b-card__meta svg { width: 12px; height: 12px; }
.b-card__meta code { font-family: var(--mono); color: var(--muted); overflow-wrap: anywhere; word-break: break-word; }

.b-badges { display: flex; gap: 4px; flex-wrap: wrap; align-items: center; }
.b-badge {
    display: inline-flex; align-items: center; gap: 4px;
    padding: 1px 7px; border-radius: 999px; line-height: 16px;
    font-size: 10px; font-weight: 700; letter-spacing: .3px;
    background: var(--surface-3); color: var(--muted);
    border: 1px solid transparent;
}
.b-badge svg { width: 11px; height: 11px; }
.b-badge--ghost { font-weight: 600; letter-spacing: 0; font-family: var(--mono); }
.b-badge--accent { color: #bfdbfe; background: rgba(59,130,246,.16); }
.b-badge--good { color: #6ee7b7; background: rgba(16,185,129,.16); }
.b-badge--warn { color: #fcd34d; background: rgba(245,158,11,.16); }
.b-badge--bad  { color: #fca5a5; background: rgba(239,68,68,.16); }
.b-badge--purple { color: #d8b4fe; background: rgba(168,85,247,.16); }
[data-theme="light"] .b-badge--accent { color: #1d4ed8; }
[data-theme="light"] .b-badge--good { color: #047857; }
[data-theme="light"] .b-badge--warn { color: #b45309; }
[data-theme="light"] .b-badge--bad { color: #b91c1c; }
[data-theme="light"] .b-badge--purple { color: #7e22ce; }

.m-get { color: #6ee7b7; background: rgba(16,185,129,.16); }
.m-post { color: #93c5fd; background: rgba(59,130,246,.18); }
.m-put { color: #fcd34d; background: rgba(245,158,11,.16); }
.m-patch { color: #fdba74; background: rgba(249,115,22,.16); }
.m-delete { color: #fca5a5; background: rgba(239,68,68,.16); }
.m-options { color: #cbd5e1; background: rgba(100,116,139,.2); }
.m-head { color: #d8b4fe; background: rgba(168,85,247,.16); }
[data-theme="light"] .m-get { color: #047857; }
[data-theme="light"] .m-post { color: #1d4ed8; }
[data-theme="light"] .m-put { color: #b45309; }
[data-theme="light"] .m-patch { color: #c2410c; }
[data-theme="light"] .m-delete { color: #b91c1c; }
[data-theme="light"] .m-options { color: #475569; }
[data-theme="light"] .m-head { color: #7e22ce; }

.b-issues { margin-top: 8px; display: flex; flex-direction: column; gap: 5px; }
.b-issue {
    display: flex; align-items: flex-start; gap: 6px;
    padding: 6px 9px; border-radius: var(--radius-sm);
    background: rgba(239,68,68,.08); border-left: 3px solid var(--bad);
    color: #fecaca; font-size: 11.5px;
}
[data-theme="light"] .b-issue { color: #991b1b; }
.b-issue svg { width: 13px; height: 13px; margin-top: 1px; }
.b-issue .b-copy { margin-left: auto; align-self: flex-start; }

.b-copy {
    border: 0; background: transparent; color: var(--faint);
    cursor: pointer; padding: 2px; border-radius: 5px; line-height: 0;
    opacity: 0; transition: .14s ease;
}
.b-copy svg { width: 13px; height: 13px; }
.b-card:hover .b-copy, .b-row:hover .b-copy, .b-route:hover .b-copy { opacity: 1; }
.b-copy:hover { color: var(--accent); background: var(--accent-soft); }

/* ------------------------------------------------------------------ table */

.b-table { width: 100%; border-collapse: collapse; font-size: 12px; }
.b-table th {
    text-align: left; font-size: 10px; text-transform: uppercase; letter-spacing: .6px;
    color: var(--faint); font-weight: 700; padding: 7px 12px;
    border-bottom: 1px solid var(--border); position: sticky; top: 0; background: var(--surface);
}
.b-table td { padding: 7px 12px; border-bottom: 1px solid var(--border-soft); vertical-align: middle; }
.b-row { transition: background .1s ease; }
.b-row:last-child td { border-bottom: 0; }
.b-row:hover { background: var(--surface-2); }
.b-mono { font-family: var(--mono); }
.b-dim { color: var(--muted); }
.b-wrap { background: var(--surface); border: 1px solid var(--border-soft); border-radius: var(--radius); overflow: hidden; box-shadow: var(--shadow); }

.b-empty { padding: 40px 20px; text-align: center; color: var(--faint); font-size: 12px; }
.b-empty svg { width: 24px; height: 24px; opacity: .5; margin-bottom: 8px; }

/* ------------------------------------------------------------------- tree */

.b-tree-bar { display: flex; gap: 6px; padding: 6px 8px; border-bottom: 1px solid var(--border-soft); background: var(--surface-2); }
.b-mini {
    display: inline-flex; align-items: center; gap: 5px;
    padding: 4px 7px; border: 1px solid var(--border); border-radius: var(--radius-sm);
    background: transparent; color: var(--muted);
}
.b-mini svg { width: 12px; height: 12px; }
.b-mini:hover { color: var(--text); border-color: var(--accent); }

.b-tree { display: flex; flex-direction: column; gap: 1px; padding: 6px 8px; }
.b-node { border-radius: var(--radius-sm); }
.b-node__head {
    display: flex; align-items: center; gap: 6px; padding: 4px 6px;
    cursor: pointer; border-radius: var(--radius-sm); transition: background .1s ease;
}
.b-node__head:hover { background: var(--surface-2); }
.b-node__head:focus-visible { outline: 1px solid var(--accent); outline-offset: -1px; }
.b-node__chevron { width: 12px; height: 12px; color: var(--faint); transition: transform .16s ease; }
.b-node.is-open > .b-node__head > .b-node__chevron { transform: rotate(90deg); }
.b-node__spacer { width: 12px; height: 12px; }
.b-node__icon { width: 13px; height: 13px; color: var(--faint); }
.b-node__label { font-family: var(--mono); font-size: 12px; }
.b-node__count {
    font-size: 10px; color: var(--faint); background: var(--surface-2);
    border-radius: 999px; padding: 0 6px; line-height: 15px; border: 1px solid var(--border-soft);
}
.b-node__children { margin-left: 14px; display: none; border-left: 1px dashed var(--border); padding-left: 8px; }
.b-node.is-open > .b-node__children { display: block; }

.b-route {
    display: flex; align-items: center; gap: 8px;
    padding: 3px 6px; border-radius: var(--radius-sm); transition: background .1s ease;
}
.b-route:hover { background: var(--surface-2); }
.b-route--issue { background: rgba(239,68,68,.06); }
.b-route__name { font-family: var(--mono); font-size: 11.5px; color: var(--text); word-break: break-all; }
.b-route__action { font-family: var(--mono); font-size: 11px; color: var(--faint); margin-left: auto; text-align: right; }
.b-route__warn { color: var(--bad); }
.b-route__warn svg { width: 13px; height: 13px; }
.b-route > .b-copy { opacity: 0; }

/* ------------------------------------------------------------------ graph */

.b-graph {
    padding: 12px; background: var(--surface); border: 1px solid var(--border-soft);
    border-radius: var(--radius); overflow: auto; min-height: 180px;
}
.b-graph svg { display: block; max-width: 100%; height: auto; color: var(--border); }
.b-graph .edge { fill: none; stroke: var(--border); stroke-width: 1.2; }
.b-graph .edge.is-lit { stroke: var(--accent); stroke-width: 1.8; }
.b-graph .gnode rect { fill: var(--surface-2); stroke: var(--border); }
.b-graph .gnode text { fill: var(--muted); font-family: var(--mono); font-size: 10.5px; }
.b-graph .gnode.is-lit rect { stroke: var(--accent); fill: var(--accent-soft); }
.b-graph .gnode.is-lit text { fill: var(--text); }
.b-graph .gnode[data-kind="layout"] rect { stroke: rgba(59,130,246,.6); }
.b-graph .gnode[data-kind="component"] rect { stroke: rgba(168,85,247,.6); }

/* ----------------------------------------------------------- misc / footer */

.b-section-title { font-size: 11px; text-transform: uppercase; letter-spacing: .8px; color: var(--faint); margin: 20px 0 10px; font-weight: 700; }
.b-section-title:first-child { margin-top: 0; }

.b-hint { color: var(--faint); font-size: 11.5px; margin: 0 0 12px; }
.b-hint code { font-family: var(--mono); color: var(--muted); }

.b-footer {
    max-width: 1360px; width: 100%; margin: 0 auto; padding: 16px 18px 28px;
    color: var(--faint); font-size: 11.5px; display: flex; gap: 8px; align-items: center; flex-wrap: wrap;
}
.b-dot { opacity: .5; }
.b-footer__spacer { margin-left: auto; }
.b-footer .b-hint { margin: 0; }
.b-footer .b-kbd { margin: 0 2px; }

.b-toast {
    position: fixed; right: 16px; bottom: 16px; z-index: 60;
    padding: 9px 13px; border-radius: var(--radius-sm);
    background: var(--surface-3); border: 1px solid var(--border); color: var(--text);
    box-shadow: var(--shadow); font-size: 12px;
    animation: b-in .18s ease both;
}
.b-toast[hidden] { display: none; }

/* ------------------------------------------------------------- views list */

.b-vlist { display: flex; flex-direction: column; gap: 8px; }
.b-vgroup { background: var(--surface); border: 1px solid var(--border-soft); border-radius: var(--radius); overflow: hidden; box-shadow: var(--shadow); }
.b-vgroup__head {
    display: flex; align-items: center; gap: 8px; width: 100%; text-align: left;
    padding: 7px 10px; background: transparent; border: 0; cursor: pointer;
    color: var(--text); font: inherit; font-size: 12px; font-weight: 600;
}
.b-vgroup__head:hover { background: var(--surface-2); }
.b-vgroup__chevron { width: 12px; height: 12px; color: var(--faint); transition: transform .16s ease; }
.b-vgroup.is-open .b-vgroup__chevron { transform: rotate(90deg); }
.b-vgroup__name { font-family: var(--mono); font-size: 12px; }
.b-vgroup__count {
    margin-left: auto; font-size: 10px; color: var(--faint);
    background: var(--surface-2); border: 1px solid var(--border-soft);
    border-radius: 999px; padding: 0 7px; line-height: 16px;
}
.b-vgroup__body { display: none; border-top: 1px solid var(--border-soft); }
.b-vgroup.is-open .b-vgroup__body { display: block; }
.b-vrow { display: flex; align-items: center; gap: 10px; padding: 5px 10px; border-bottom: 1px solid var(--border-soft); }
.b-vrow:last-child { border-bottom: 0; }
.b-vrow:hover { background: var(--surface-2); }
.b-vrow__path { flex: 1 1 auto; min-width: 0; font-family: var(--mono); font-size: 11.5px; color: var(--text); overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
.b-vrow__refs { max-width: 32%; font-family: var(--mono); font-size: 10.5px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
.b-vrow__meta { margin-left: auto; font-size: 10.5px; white-space: nowrap; }
.b-vrow > .b-copy { opacity: 0; }

/* --------------------------------------------------------------- packages */

.b-pkg { display: flex; flex-direction: column; gap: 1px; min-width: 0; }
.b-pkg__name { font-family: var(--mono); font-size: 12px; font-weight: 600; }
.b-pkg__sub { display: flex; align-items: center; gap: 5px; font-size: 10.5px; color: var(--faint); }
.b-pkg__arrow { color: var(--faint); }
.b-pkg__advisories { margin-top: 3px; display: flex; gap: 8px; flex-wrap: wrap; font-size: 10.5px; font-family: var(--mono); }
.b-pkg__advisories a { color: var(--bad); }
.b-row--bad { background: rgba(239,68,68,.06); }
.b-row--bad:hover { background: rgba(239,68,68,.1); }
.b-row--warn { background: rgba(245,158,11,.05); }
.b-alert { display: flex; align-items: flex-start; gap: 10px; padding: 10px 12px; margin-bottom: 12px; border-radius: var(--radius); font-size: 12px; }
.b-alert svg { width: 16px; height: 16px; margin-top: 1px; }
.b-alert--bad { background: rgba(239,68,68,.1); border: 1px solid rgba(239,68,68,.35); color: #fca5a5; }
[data-theme="light"] .b-alert--bad { color: #991b1b; }

@media (max-width: 880px) {
    .b-header__inner { flex-wrap: wrap; gap: 8px; }
    .b-tabs { order: 3; width: 100%; margin-left: 0; }
    .b-actions { margin-left: auto; }
    .b-toolbar__meta { width: 100%; margin-left: 0; }
    .b-btn__label { display: none; }
    .b-footer .b-hint { display: none; }
}
</style>
