<script>
(function () {
    'use strict';

    var payload = JSON.parse(document.getElementById('bones-data').textContent || '{}');
    var skeleton = payload.skeleton || {};
    var explore = payload.explore || [];

    var state = {
        routes: { query: '', method: '', issues: false, view: 'cards' },
        views: { query: '', kind: '', view: 'list' },
        packages: { query: '', ecosystem: '', direct: false, laravel: false },
        explore: { query: '', category: '', installed: false }
    };

    var panel = function (key) { return document.querySelector('[data-panel="' + key + '"]'); };
    var body = function (key) { return document.querySelector('[data-body="' + key + '"]'); };
    var filters = function (key) { return document.querySelector('[data-filters="' + key + '"]'); };
    var meta = function (key) { return document.querySelector('[data-meta="' + key + '"]'); };
    var active = 'overview';

    function esc(value) {
        return String(value == null ? '' : value)
            .replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;').replace(/'/g, '&#39;');
    }

    function icon(name, cls) {
        return '<svg viewBox="0 0 24 24" class="' + (cls || '') + '" aria-hidden="true"><use href="#i-' + name + '"></use></svg>';
    }

    function methodBadges(methods) {
        return (methods || []).map(function (method) {
            var m = String(method).toLowerCase();
            var known = ['get', 'post', 'put', 'patch', 'delete', 'options', 'head'];
            var cls = known.indexOf(m) >= 0 ? 'm-' + m : 'm-options';
            return '<span class="b-badge ' + cls + '">' + esc(method) + '</span>';
        }).join('');
    }

    function tally(text) {
        var t = document.querySelector('[data-toast]');
        if (!t) { return; }
        t.textContent = text;
        t.hidden = false;
        clearTimeout(t._timer);
        t._timer = setTimeout(function () { t.hidden = true; }, 1600);
    }

    function copy(text) {
        var done = function () { tally('Copied ' + text); };
        if (navigator.clipboard && navigator.clipboard.writeText) {
            navigator.clipboard.writeText(text).then(done, function () { fallbackCopy(text, done); });
            return;
        }
        fallbackCopy(text, done);
    }

    function fallbackCopy(text, done) {
        var area = document.createElement('textarea');
        area.value = text;
        area.setAttribute('readonly', '');
        area.style.position = 'absolute';
        area.style.left = '-9999px';
        document.body.appendChild(area);
        area.select();
        try { document.execCommand('copy'); done(); } catch (e) { /* noop */ }
        document.body.removeChild(area);
    }

    function matches(query, fields) {
        if (!query) { return true; }
        var q = query.toLowerCase();
        return fields.some(function (field) {
            return String(field == null ? '' : field).toLowerCase().indexOf(q) >= 0;
        });
    }

    function options(values, current, allLabel) {
        return '<option value="">' + allLabel + '</option>' + values.map(function (value) {
            return '<option value="' + esc(value) + '"' + (value === current ? ' selected' : '') + '>' + esc(value) + '</option>';
        }).join('');
    }

    function select(key, html, onChange) {
        var wrap = document.createElement('div');
        wrap.innerHTML = html;
        var node = wrap.firstElementChild;
        filters(key).appendChild(node);
        node.addEventListener('change', function () { onChange(node.value); });
        return node;
    }

    function toggle(key, label, active, onClick) {
        var node = document.createElement('button');
        node.type = 'button';
        node.className = 'b-toggle';
        node.innerHTML = icon('check', 'b-toggle__tick') + '<span>' + esc(label) + '</span>';
        node.setAttribute('aria-pressed', active ? 'true' : 'false');
        filters(key).appendChild(node);
        node.addEventListener('click', function () {
            var next = node.getAttribute('aria-pressed') !== 'true';
            node.setAttribute('aria-pressed', next ? 'true' : 'false');
            onClick(next);
        });
        return node;
    }

    function segmented(key, current, choices, onPick) {
        var node = document.createElement('div');
        node.className = 'b-segment';
        choices.forEach(function (choice) {
            var b = document.createElement('button');
            b.type = 'button';
            b.innerHTML = icon(choice.icon) + '<span>' + esc(choice.label) + '</span>';
            b.setAttribute('aria-pressed', choice.value === current ? 'true' : 'false');
            b.addEventListener('click', function () { onPick(choice.value); });
            node.appendChild(b);
        });
        filters(key).appendChild(node);
        return node;
    }

    function empty(message) {
        return '<div class="b-empty">' + icon('search') + '<div>' + esc(message) + '</div></div>';
    }

    /* ------------------------------------------------------------------ tabs */

    var rendered = {};
    var renderers = {};

    function activate(tab) {
        active = tab;
        state.tab = tab;
        document.querySelectorAll('.b-tab').forEach(function (button) {
            button.setAttribute('aria-selected', button.dataset.tab === tab ? 'true' : 'false');
        });
        document.querySelectorAll('.b-panel').forEach(function (section) {
            section.classList.toggle('is-hidden', section.dataset.panel !== tab);
        });
        if (!rendered[tab] && renderers[tab]) {
            renderers[tab]();
            rendered[tab] = true;
        }
    }

    document.querySelectorAll('.b-tab').forEach(function (button) {
        button.addEventListener('click', function () { activate(button.dataset.tab); });
    });

    document.querySelectorAll('[data-search]').forEach(function (input) {
        input.addEventListener('input', function () {
            state[input.dataset.search].query = input.value;
            refresh(input.dataset.search);
        });
    });

    function refresh(key) {
        if (renderers[key]) { renderers[key](); }
    }

    function focusSearch(key) {
        var input = document.querySelector('[data-search="' + (key || active) + '"]');
        if (input) { input.focus(); input.select(); }
    }

    function clearSearch(key) {
        var input = document.querySelector('[data-search="' + (key || active) + '"]');
        if (input && input.value !== '') {
            input.value = '';
            state[(key || active)].query = '';
            refresh(key || active);
        }
    }

    document.addEventListener('keydown', function (event) {
        var tag = (event.target && event.target.tagName || '').toLowerCase();
        if (event.key === '/' && tag !== 'input' && tag !== 'textarea' && tag !== 'select' && !event.metaKey && !event.ctrlKey) {
            event.preventDefault();
            focusSearch();
        } else if (event.key === 'Escape') {
            clearSearch();
        }
    });

    /* ---------------------------------------------------------------- theme */

    function currentTheme() {
        return document.documentElement.getAttribute('data-theme') === 'light' ? 'light' : 'dark';
    }

    function applyTheme(theme) {
        document.documentElement.setAttribute('data-theme', theme);
        try { localStorage.setItem('bones-theme', theme); } catch (e) { /* noop */ }
    }

    var themeButton = document.getElementById('theme-toggle');
    if (themeButton) {
        themeButton.addEventListener('click', function () {
            applyTheme(currentTheme() === 'dark' ? 'light' : 'dark');
        });
    }

    /* -------------------------------------------------------------- overview */

    function statCard(iconName, value, label) {
        return '<div class="b-stat"><div class="b-stat__icon">' + icon(iconName) + '</div>' +
            '<div><div class="b-stat__value">' + esc(value) + '</div>' +
            '<div class="b-stat__label">' + esc(label) + '</div></div></div>';
    }

    function legend(iconName, title, text) {
        return '<div class="b-card"><div class="b-card__top"><span class="b-stat__icon">' + icon(iconName) + '</span>' +
            '<strong>' + esc(title) + '</strong></div><div class="b-card__meta">' + esc(text) + '</div></div>';
    }

    function summaryOf(key) {
        return (skeleton[key] && skeleton[key].data && skeleton[key].data.summary) || {};
    }

    renderers.overview = function () {
        var routes = summaryOf('routes');
        var views = summaryOf('views');
        var packages = summaryOf('packages');
        var issues = routes.issues || 0;

        var html = '<p class="b-hint">Bones read <code>' + (routes.total || 0) + '</code> routes, ' +
            '<code>' + (views.total || 0) + '</code> view nodes and <code>' + (packages.total || 0) + '</code> packages.</p>';

        html += '<div class="b-grid b-grid--stats">';
        html += statCard('route', routes.total || 0, 'Routes');
        html += statCard('code', routes.controllers || 0, 'Controllers');
        html += statCard('layers', views.total || 0, 'View nodes');
        html += statCard('layout', views.layouts || 0, 'Layouts');
        html += statCard('package', packages.total || 0, 'Packages');
        html += statCard('sparkles', packages.laravel || 0, 'Laravel packages');
        html += '</div>';

        html += '<h3 class="b-section-title">Health</h3>';
        if (issues > 0) {
            html += '<div class="b-card b-card--issue"><div class="b-card__top">' +
                '<span class="b-badge b-badge--bad">' + icon('warn') + ' ' + issues + ' route issue' + (issues === 1 ? '' : 's') + '</span>' +
                '<span class="b-card__name">open the Routes tab to inspect</span></div>' +
                '<div class="b-card__meta">Routes shadowed by an earlier wildcard, or pointing at a controller or method that no longer exists.</div></div>';
        } else {
            html += '<div class="b-card"><div class="b-card__top">' +
                '<span class="b-badge b-badge--good">' + icon('check') + ' All clear</span></div>' +
                '<div class="b-card__meta">Every route resolves to a live controller and none are shadowed.</div></div>';
        }

        html += '<h3 class="b-section-title">How to read this</h3>';
        html += '<div class="b-grid b-grid--cards">' +
            legend('route', 'Routes', 'HTTP endpoints, with method, action, middleware and health.') +
            legend('layout', 'Views', 'Blade nodes as a graph — what extends and includes what.') +
            legend('package', 'Packages', 'Composer and npm dependencies, flagged direct, dev or Laravel-aware.') +
            '</div>';

        panel('overview').innerHTML = html;
    };

    /* ---------------------------------------------------------------- routes */

    renderers.routes = function () {
        var data = (skeleton.routes && skeleton.routes.data) || {};
        var items = data.items || [];
        var methods = Array.from(new Set(items.flatMap(function (r) { return r.methods; }))).sort();

        var f = filters('routes');
        f.innerHTML = '';
        select('routes', '<select class="b-select">' + options(methods, state.routes.method, 'All methods') + '</select>', function (value) {
            state.routes.method = value; refresh('routes');
        });
        toggle('routes', 'Issues', state.routes.issues, function (value) {
            state.routes.issues = value; refresh('routes');
        });
        segmented('routes', state.routes.view, [
            { value: 'cards', label: 'Cards', icon: 'list' },
            { value: 'tree', label: 'Tree', icon: 'tree' }
        ], function (value) { state.routes.view = value; refresh('routes'); });

        var filtered = items.filter(function (route) {
            if (state.routes.method && route.methods.indexOf(state.routes.method) < 0) { return false; }
            if (state.routes.issues && route.health && route.health.ok) { return false; }
            return matches(state.routes.query, [route.uri, route.name, route.action.full, (route.middleware || []).join(' ')]);
        });

        meta('routes').textContent = filtered.length + ' / ' + items.length + ' routes';

        body('routes').innerHTML = filtered.length === 0
            ? empty('No routes match.')
            : (state.routes.view === 'tree' ? routesTree(filtered) : '<div class="b-grid b-grid--cards">' + filtered.map(routeCard).join('') + '</div>');

        wireTree(body('routes'));

        body('routes').querySelectorAll('[data-copy]').forEach(function (button) {
            button.addEventListener('click', function (event) {
                event.preventDefault();
                event.stopPropagation();
                copy(button.dataset.copy);
            });
        });
    };

    function routeCard(route) {
        var issues = route.health && !route.health.ok
            ? '<div class="b-issues">' + route.health.issues.map(function (issue) {
                return '<div class="b-issue">' + icon('warn') + '<span>' + esc(issue) + '</span></div>';
            }).join('') + '</div>'
            : '';

        var middleware = (route.middleware || []).map(function (name) {
            return '<span class="b-badge b-badge--purple b-badge--ghost">' + esc(name) + '</span>';
        }).join('');

        return '<div class="b-card' + (route.health && !route.health.ok ? ' b-card--issue' : '') + '">' +
            '<div class="b-card__top"><div class="b-badges">' + methodBadges(route.methods) + '</div>' +
            (route.name ? '<span class="b-card__name">' + esc(route.name) + '</span>' : '') + '</div>' +
            '<div class="b-card__uri">' + esc('/' + route.uri) +
            '<button class="b-copy" data-copy="/' + esc(route.uri) + '" title="Copy route">' + icon('copy') + '</button></div>' +
            '<div class="b-card__meta">' +
            (route.action.full ? '<span>' + icon('code') + ' <code>' + esc(route.action.full) + '</code></span>' : '<span>' + icon('code') + ' <code>Closure</code></span>') +
            (route.domain ? '<span>' + icon('external') + ' ' + esc(route.domain) + '</span>' : '') +
            '</div>' +
            (middleware ? '<div class="b-card__meta"><div class="b-badges">' + middleware + '</div></div>' : '') +
            issues +
            '</div>';
    }

    function routesTree(items) {
        var root = { key: '', children: {}, routes: [] };

        items.forEach(function (route) {
            var parts = route.uri === '' ? ['/'] : String(route.uri).split('/');
            var node = root;
            parts.forEach(function (part, index) {
                var key = part === '' ? '/' : part;
                node.children[key] = node.children[key] || { key: key, children: {}, routes: [] };
                node = node.children[key];
                if (index === parts.length - 1) { node.routes.push(route); }
            });
        });

        return '<div class="b-wrap">' +
            '<div class="b-tree-bar">' +
            '<button type="button" class="b-mini" data-tree="expand">' + icon('expand') + ' Expand all</button>' +
            '<button type="button" class="b-mini" data-tree="collapse">' + icon('collapse') + ' Collapse all</button>' +
            '</div>' +
            '<div class="b-tree">' + treeNodes(root.children) + '</div></div>';
    }

    function countRoutes(node) {
        var total = node.routes.length;
        Object.keys(node.children).forEach(function (key) { total += countRoutes(node.children[key]); });
        return total;
    }

    function routeRow(route) {
        var unhealthy = route.health && !route.health.ok;
        return '<div class="b-route' + (unhealthy ? ' b-route--issue' : '') + '">' +
            '<span class="b-badges">' + methodBadges(route.methods) + '</span>' +
            '<span class="b-route__name">' + esc(route.name || '/' + route.uri) + '</span>' +
            '<code class="b-route__action">' + esc(route.action.full || 'Closure') + '</code>' +
            (unhealthy ? '<span class="b-route__warn" title="' + esc((route.health.issues || []).join(' ')) + '">' + icon('warn') + '</span>' : '') +
            '<button class="b-copy" data-copy="/' + esc(route.uri) + '" title="Copy route">' + icon('copy') + '</button>' +
            '</div>';
    }

    function treeNodes(children) {
        return Object.keys(children).sort().map(function (key) {
            var node = children[key];
            var hasChildren = Object.keys(node.children).length > 0;
            var total = countRoutes(node);
            var rows = node.routes.map(routeRow).join('');

            return '<div class="b-node is-open' + (hasChildren ? '' : ' is-leaf') + '">' +
                '<div class="b-node__head" role="button" tabindex="0">' +
                (hasChildren ? icon('chevron', 'b-node__chevron') : '<span class="b-node__spacer"></span>') +
                icon(hasChildren ? 'layout' : 'route', 'b-node__icon') +
                '<span class="b-node__label">' + esc(key) + '</span>' +
                (total ? '<span class="b-node__count">' + total + '</span>' : '') +
                '</div>' +
                '<div class="b-node__children">' + rows + (hasChildren ? treeNodes(node.children) : '') + '</div>' +
                '</div>';
        }).join('');
    }

    function wireTree(scope) {
        scope.querySelectorAll('.b-node').forEach(function (node) {
            var head = node.querySelector(':scope > .b-node__head');
            if (!head) { return; }
            var flip = function () { node.classList.toggle('is-open'); };
            head.addEventListener('click', flip);
            head.addEventListener('keydown', function (event) {
                if (event.key === 'Enter' || event.key === ' ') { event.preventDefault(); flip(); }
            });
        });

        scope.querySelectorAll('[data-tree]').forEach(function (button) {
            button.addEventListener('click', function () {
                var open = button.dataset.tree === 'expand';
                scope.querySelectorAll('.b-node').forEach(function (node) { node.classList.toggle('is-open', open); });
            });
        });
    }

    /* ----------------------------------------------------------------- views */

    renderers.views = function () {
        var data = (skeleton.views && skeleton.views.data) || {};
        var items = data.items || [];
        var kinds = ['layout', 'view', 'component'];

        var f = filters('views');
        f.innerHTML = '';
        select('views', '<select class="b-select">' + options(kinds, state.views.kind, 'All kinds') + '</select>', function (value) {
            state.views.kind = value; refresh('views');
        });
        segmented('views', state.views.view, [
            { value: 'list', label: 'List', icon: 'list' },
            { value: 'graph', label: 'Graph', icon: 'graph' }
        ], function (value) { state.views.view = value; refresh('views'); });

        var filtered = items.filter(function (view) {
            if (state.views.kind && view.kind !== state.views.kind) { return false; }
            return matches(state.views.query, [view.name, view.path, (view.used_by || []).join(' ')]);
        });

        meta('views').textContent = filtered.length + ' / ' + items.length + ' views';

        if (filtered.length === 0) {
            body('views').innerHTML = empty('No views match.');
            return;
        }

        if (state.views.view === 'graph') {
            body('views').innerHTML = '<div class="b-graph" data-graph></div>';
            drawGraph(body('views').querySelector('[data-graph]'), data.graph, filtered);
            return;
        }

        body('views').innerHTML = '<div class="b-grid b-grid--cards">' + filtered.map(viewCard).join('') + '</div>';
    };

    function viewCard(view) {
        var kindBadge = { layout: 'accent', component: 'purple', view: 'ghost' }[view.kind] || 'ghost';
        var refs = []
            .concat((view.extends || []).map(function (n) { return '<span class="b-badge b-badge--accent b-badge--ghost">extends ' + esc(n) + '</span>'; }))
            .concat((view.includes || []).map(function (n) { return '<span class="b-badge b-badge--good b-badge--ghost">includes ' + esc(n) + '</span>'; }));

        return '<div class="b-card">' +
            '<div class="b-card__top"><span class="b-badge b-badge--' + kindBadge + '">' + esc(view.kind) + '</span>' +
            (view.package ? '<span class="b-badge b-badge--ghost">' + esc(view.package) + '</span>' : '') +
            '<span class="b-card__name">' + esc(view.lines) + ' lines</span></div>' +
            '<div class="b-card__uri">' + esc(view.name) +
            '<button class="b-copy" data-copy="' + esc(view.name) + '" title="Copy view name">' + icon('copy') + '</button></div>' +
            '<div class="b-card__meta"><span class="b-mono b-dim">' + esc(view.path) + '</span></div>' +
            (refs.length ? '<div class="b-card__meta"><div class="b-badges">' + refs.join('') + '</div></div>' : '') +
            ((view.used_by || []).length ? '<div class="b-card__meta"><span class="b-dim">used by ' + view.used_by.length + '</span></div>' : '') +
            '</div>';
    }

    function drawGraph(container, graph, visible) {
        graph = graph || { nodes: [], edges: [] };
        var allowed = {};
        visible.forEach(function (node) { allowed[node.name] = node; });

        var nodes = graph.nodes.filter(function (node) { return allowed[node.id]; });
        var edges = graph.edges.filter(function (edge) { return allowed[edge.from] && allowed[edge.to]; });

        if (nodes.length === 0) {
            container.innerHTML = empty('Nothing to graph.');
            return;
        }

        var out = {};
        edges.forEach(function (edge) { (out[edge.from] = out[edge.from] || []).push(edge.to); });

        var layer = {};
        var visiting = {};
        function depth(id) {
            if (layer[id] != null) { return layer[id]; }
            if (visiting[id]) { return 0; }
            visiting[id] = true;
            var d = 0;
            (out[id] || []).forEach(function (target) { d = Math.max(d, 1 + depth(target)); });
            delete visiting[id];
            return layer[id] = d;
        }
        nodes.forEach(function (node) { depth(node.id); });

        var maxLayer = Math.max.apply(null, [0].concat(Object.keys(layer).map(function (k) { return layer[k]; })));
        var columns = [];
        nodes.forEach(function (node) {
            var l = maxLayer - layer[node.id];
            (columns[l] = columns[l] || []).push(node);
        });

        var nodeW = 190, nodeH = 30, gapY = 20, gapX = 54, pad = 22;
        var pos = {}; var width = 0;
        columns.forEach(function (column, ci) {
            var x = pad + ci * (nodeW + gapX);
            width = Math.max(width, x + nodeW + pad);
            column.forEach(function (node, ri) {
                pos[node.id] = { x: x, y: pad + ri * (nodeH + gapY) };
            });
        });
        var height = Math.max.apply(null, [160].concat(nodes.map(function (n) { return pos[n.id].y + nodeH; }))) + pad;

        var edgePaths = edges.map(function (edge) {
            var a = pos[edge.from], b = pos[edge.to];
            var x1 = a.x + nodeW / 2, y1 = a.y + nodeH, x2 = b.x + nodeW / 2, y2 = b.y;
            var d = 'M' + x1 + ',' + y1 + ' C' + x1 + ',' + (y1 + (y2 - y1) / 2) + ' ' + x2 + ',' + (y2 - (y2 - y1) / 2) + ' ' + x2 + ',' + y2;
            return '<path class="edge" data-from="' + esc(edge.from) + '" data-to="' + esc(edge.to) + '" d="' + d + '" marker-end="url(#bones-arrow)"></path>';
        }).join('');

        var nodeGroups = nodes.map(function (node) {
            var p = pos[node.id];
            return '<g class="gnode" data-id="' + esc(node.id) + '" data-kind="' + esc(node.kind) + '" transform="translate(' + p.x + ',' + p.y + ')">' +
                '<rect width="' + nodeW + '" height="' + nodeH + '" rx="7"></rect>' +
                '<text x="11" y="' + (nodeH / 2 + 4) + '">' + esc(truncate(node.label, 26)) + '</text></g>';
        }).join('');

        var svg = '<svg viewBox="0 0 ' + width + ' ' + height + '" width="' + width + '" height="' + height + '" preserveAspectRatio="xMidYMin meet">' +
            '<defs><marker id="bones-arrow" viewBox="0 0 10 10" refX="8" refY="5" markerWidth="6" markerHeight="6" orient="auto-start-reverse">' +
            '<path d="M0,0 L10,5 L0,10 z" fill="currentColor"></path></marker></defs>' +
            edgePaths + nodeGroups + '</svg>';

        container.innerHTML = svg;

        container.querySelectorAll('.gnode').forEach(function (group) {
            group.addEventListener('mouseenter', function () {
                var id = group.dataset.id;
                container.querySelectorAll('.gnode').forEach(function (n) {
                    n.classList.toggle('is-lit', n.dataset.id === id || connected(id, n.dataset.id));
                });
                container.querySelectorAll('.edge').forEach(function (edge) {
                    edge.classList.toggle('is-lit', edge.dataset.from === id || edge.dataset.to === id);
                });
            });
            group.addEventListener('mouseleave', function () {
                container.querySelectorAll('.is-lit').forEach(function (n) { n.classList.remove('is-lit'); });
            });
        });

        function connected(a, b) {
            return edges.some(function (edge) {
                return (edge.from === a && edge.to === b) || (edge.from === b && edge.to === a);
            });
        }
    }

    function truncate(text, max) {
        text = String(text);
        return text.length > max ? text.slice(0, max - 1) + '…' : text;
    }

    /* -------------------------------------------------------------- packages */

    renderers.packages = function () {
        var data = (skeleton.packages && skeleton.packages.data) || {};
        var items = data.items || [];

        var f = filters('packages');
        f.innerHTML = '';
        select('packages', '<select class="b-select">' + options(['composer', 'npm'], state.packages.ecosystem, 'All sources') + '</select>', function (value) {
            state.packages.ecosystem = value; refresh('packages');
        });
        toggle('packages', 'Direct', state.packages.direct, function (value) {
            state.packages.direct = value; refresh('packages');
        });
        toggle('packages', 'Laravel', state.packages.laravel, function (value) {
            state.packages.laravel = value; refresh('packages');
        });

        var filtered = items.filter(function (pkg) {
            if (state.packages.ecosystem && pkg.ecosystem !== state.packages.ecosystem) { return false; }
            if (state.packages.direct && !pkg.direct) { return false; }
            if (state.packages.laravel && !pkg.laravel) { return false; }
            return matches(state.packages.query, [pkg.name, pkg.description, pkg.license]);
        });

        meta('packages').textContent = filtered.length + ' / ' + items.length + ' packages';

        if (filtered.length === 0) {
            body('packages').innerHTML = empty('No packages match.');
            return;
        }

        body('packages').innerHTML = '<div class="b-wrap"><table class="b-table"><thead><tr>' +
            '<th>Package</th><th>Version</th><th>Source</th><th>Flags</th><th>Description</th></tr></thead><tbody>' +
            filtered.map(packageRow).join('') + '</tbody></table></div>';
    };

    function packageRow(pkg) {
        var flags = [];
        if (pkg.direct) { flags.push('<span class="b-badge b-badge--accent">direct</span>'); }
        if (pkg.dev) { flags.push('<span class="b-badge">dev</span>'); }
        if (pkg.laravel) { flags.push('<span class="b-badge b-badge--good">laravel</span>'); }
        if (pkg.abandoned) { flags.push('<span class="b-badge b-badge--bad">abandoned</span>'); }
        var name = pkg.homepage
            ? '<a href="' + esc(pkg.homepage) + '" target="_blank" rel="noopener">' + esc(pkg.name) + '</a>'
            : esc(pkg.name);

        return '<tr class="b-row">' +
            '<td class="b-mono">' + name + '</td>' +
            '<td class="b-mono b-dim">' + esc(pkg.version) + '</td>' +
            '<td><span class="b-badge b-badge--ghost">' + esc(pkg.ecosystem) + '</span></td>' +
            '<td><div class="b-badges">' + (flags.join('') || '<span class="b-dim">—</span>') + '</div></td>' +
            '<td class="b-dim">' + esc(pkg.description || '') + '</td></tr>';
    }

    /* --------------------------------------------------------------- explore */

    renderers.explore = function () {
        var categories = Array.from(new Set(explore.map(function (item) { return item.category; }))).sort();

        var f = filters('explore');
        f.innerHTML = '';
        select('explore', '<select class="b-select">' + options(categories, state.explore.category, 'All categories') + '</select>', function (value) {
            state.explore.category = value; refresh('explore');
        });
        toggle('explore', 'Installed', state.explore.installed, function (value) {
            state.explore.installed = value; refresh('explore');
        });

        var filtered = explore.filter(function (item) {
            if (state.explore.category && item.category !== state.explore.category) { return false; }
            if (state.explore.installed && !item.installed) { return false; }
            return matches(state.explore.query, [item.name, item.description, item.category, (item.tags || []).join(' ')]);
        });

        meta('explore').textContent = filtered.length + ' / ' + explore.length + ' packages';

        body('explore').innerHTML = filtered.length === 0
            ? empty('Nothing on the shelf matches.')
            : '<div class="b-grid b-grid--cards">' + filtered.map(exploreCard).join('') + '</div>';
    };

    function exploreCard(item) {
        var tags = (item.tags || []).map(function (tag) {
            return '<span class="b-badge b-badge--ghost">' + esc(tag) + '</span>';
        }).join('');
        return '<div class="b-card">' +
            '<div class="b-card__top">' +
            '<span class="b-badge b-badge--ghost">' + esc(item.category) + '</span>' +
            (item.installed
                ? '<span class="b-badge b-badge--good">installed ' + esc(item.installed_version || '') + '</span>'
                : '<span class="b-badge">not installed</span>') +
            '</div>' +
            '<div class="b-card__uri">' + esc(item.name) +
            '<a href="' + esc(item.url) + '" target="_blank" rel="noopener" class="b-copy" title="Open homepage">' + icon('external') + '</a></div>' +
            '<div class="b-card__meta">' + esc(item.description) + '</div>' +
            '<div class="b-card__meta"><div class="b-badges">' + tags + '</div></div>' +
            '</div>';
    }

    /* ------------------------------------------------------------------ boot */

    Object.keys(skeleton).forEach(function (key) {
        var count = ((skeleton[key].data || {}).items || []).length;
        var slot = document.querySelector('[data-count="' + key + '"]');
        if (slot) { slot.textContent = count; }
    });

    renderers.overview();
    rendered.overview = true;
    activate('overview');
})();
</script>
