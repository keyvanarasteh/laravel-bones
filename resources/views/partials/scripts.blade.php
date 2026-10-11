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
            return '<span class="bones-badge ' + cls + '">' + esc(method) + '</span>';
        }).join('');
    }

    function tally(text) {
        var t = document.querySelector('[data-toast]');
        t.textContent = text;
        t.hidden = false;
        clearTimeout(t._timer);
        t._timer = setTimeout(function () { t.hidden = true; }, 1800);
    }

    function copy(text) {
        if (navigator.clipboard) {
            navigator.clipboard.writeText(text).then(function () { tally('Copied ' + text); });
        }
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
        node.textContent = label;
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
        node.className = 'bones-filters';
        choices.forEach(function (choice) {
            var b = document.createElement('button');
            b.type = 'button';
            b.innerHTML = icon(choice.icon) + ' ' + esc(choice.label);
            b.setAttribute('aria-pressed', choice.value === current ? 'true' : 'false');
            b.addEventListener('click', function () { onPick(choice.value); });
            node.appendChild(b);
        });
        filters(key).appendChild(node);
        return node;
    }

    function empty(message) {
        return '<div class="bones-empty">' + icon('search') + '<div>' + esc(message) + '</div></div>';
    }

    /* ------------------------------------------------------------------ tabs */

    var rendered = {};
    var renderers = {};

    function activate(tab) {
        state.tab = tab;
        document.querySelectorAll('.bones-tab').forEach(function (button) {
            button.setAttribute('aria-selected', button.dataset.tab === tab ? 'true' : 'false');
        });
        document.querySelectorAll('.bones-panel').forEach(function (section) {
            section.classList.toggle('is-hidden', section.dataset.panel !== tab);
        });
        if (!rendered[tab] && renderers[tab]) {
            renderers[tab]();
            rendered[tab] = true;
        }
    }

    document.querySelectorAll('.bones-tab').forEach(function (button) {
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

    /* -------------------------------------------------------------- overview */

    function statCard(iconName, value, label) {
        return '<div class="bones-stat"><div class="bones-stat__icon">' + icon(iconName) + '</div>' +
            '<div><div class="bones-stat__value">' + esc(value) + '</div>' +
            '<div class="bones-stat__label">' + esc(label) + '</div></div></div>';
    }

    function summaryOf(key) {
        return (skeleton[key] && skeleton[key].data && skeleton[key].data.summary) || {};
    }

    renderers.overview = function () {
        var routes = summaryOf('routes');
        var views = summaryOf('views');
        var packages = summaryOf('packages');
        var issues = routes.issues || 0;

        var html = '<p class="bones-hint">Bones read <code>' + (routes.total || 0) + '</code> routes, ' +
            '<code>' + (views.total || 0) + '</code> view nodes and <code>' + (packages.total || 0) + '</code> packages.</p>';

        html += '<div class="bones-grid bones-grid--stats">';
        html += statCard('route', routes.total || 0, 'Routes');
        html += statCard('code', routes.controllers || 0, 'Controllers');
        html += statCard('layers', views.total || 0, 'View nodes');
        html += statCard('layout', views.layouts || 0, 'Layouts');
        html += statCard('package', packages.total || 0, 'Packages');
        html += statCard('sparkles', packages.laravel || 0, 'Laravel packages');
        html += '</div>';

        html += '<h3 class="bones-section-title">Health</h3>';
        if (issues > 0) {
            html += '<div class="bones-card bones-card--issue"><div class="bones-card__top">' +
                '<span class="bones-badge bones-badge--bad">' + icon('warn') + ' ' + issues + ' route issue' + (issues === 1 ? '' : 's') + '</span>' +
                '<span class="bones-card__name">open the Routes tab to inspect</span></div>' +
                '<div class="bones-card__meta">Routes that are shadowed by an earlier wildcard, or point at a controller or method that no longer exists.</div></div>';
        } else {
            html += '<div class="bones-card"><div class="bones-card__top">' +
                '<span class="bones-badge bones-badge--good">' + icon('check') + ' All clear</span></div>' +
                '<div class="bones-card__meta">Every route resolves to a live controller and none are shadowed.</div></div>';
        }

        html += '<h3 class="bones-section-title">How to read this</h3>';
        html += '<div class="bones-grid bones-grid--cards">' +
            legend('route', 'Routes', 'HTTP endpoints, with method, action, middleware and health.') +
            legend('layout', 'Views', 'Blade nodes as a graph — what extends and includes what.') +
            legend('package', 'Packages', 'Composer and npm dependencies, flagged direct, dev or Laravel-aware.') +
            '</div>';

        panel('overview').innerHTML = html;
    };

    function legend(iconName, title, text) {
        return '<div class="bones-card"><div class="bones-card__top"><span class="bones-stat__icon">' + icon(iconName) + '</span>' +
            '<strong>' + esc(title) + '</strong></div><div class="bones-card__meta">' + esc(text) + '</div></div>';
    }

    /* ---------------------------------------------------------------- routes */

    renderers.routes = function () {
        var data = skeleton.routes.data;
        var items = data.items || [];
        var methods = Array.from(new Set(items.flatMap(function (r) { return r.methods; }))).sort();

        var f = filters('routes');
        f.innerHTML = '';
        select('routes', '<select>' + options(methods, state.routes.method, 'All methods') + '</select>', function (value) {
            state.routes.method = value; refresh('routes');
        });
        toggle('routes', 'Issues only', state.routes.issues, function (value) {
            state.routes.issues = value; refresh('routes');
        });
        segmented('routes', state.routes.view, [
            { value: 'cards', label: 'Cards', icon: 'list' },
            { value: 'tree', label: 'Tree', icon: 'tree' }
        ], function (value) { state.routes.view = value; refresh('routes'); });

        var view = state.routes.view;
        var filtered = items.filter(function (route) {
            if (state.routes.method && route.methods.indexOf(state.routes.method) < 0) { return false; }
            if (state.routes.issues && route.health.ok) { return false; }
            return matches(state.routes.query, [route.uri, route.name, route.action.full, (route.middleware || []).join(' ')]);
        });

        meta('routes').textContent = filtered.length + ' of ' + items.length + ' routes';

        body('routes').innerHTML = filtered.length === 0
            ? empty('No routes match.')
            : (view === 'tree' ? routesTree(filtered) : '<div class="bones-grid bones-grid--cards">' + filtered.map(routeCard).join('') + '</div>');

        wireTree(body('routes'));

        body('routes').querySelectorAll('[data-copy]').forEach(function (button) {
            button.addEventListener('click', function () { copy(button.dataset.copy); });
        });
    };

    function routeCard(route) {
        var issues = route.health && !route.health.ok
            ? '<div class="bones-issues">' + route.health.issues.map(function (issue) {
                return '<div class="bones-issue">' + icon('warn') + '<span>' + esc(issue) + '</span></div>';
            }).join('') + '</div>'
            : '';

        var middleware = (route.middleware || []).map(function (name) {
            return '<span class="bones-badge bones-badge--purple bones-badge--ghost">' + esc(name) + '</span>';
        }).join('');

        return '<div class="bones-card' + (route.health && !route.health.ok ? ' bones-card--issue' : '') + '">' +
            '<div class="bones-card__top"><div class="bones-badges">' + methodBadges(route.methods) + '</div>' +
            (route.name ? '<span class="bones-card__name">' + esc(route.name) + '</span>' : '') + '</div>' +
            '<div class="bones-card__uri">' + esc('/' + route.uri) +
            '<button class="bones-copy" data-copy="/' + esc(route.uri) + '" title="Copy">' + icon('copy') + '</button></div>' +
            '<div class="bones-card__meta">' +
            (route.action.full ? '<span>' + icon('code') + ' <code>' + esc(route.action.full) + '</code></span>' : '<span>' + icon('code') + ' <code>Closure</code></span>') +
            (route.domain ? '<span>' + icon('external') + ' ' + esc(route.domain) + '</span>' : '') +
            '</div>' +
            (middleware ? '<div class="bones-card__meta"><div class="bones-badges">' + middleware + '</div></div>' : '') +
            issues +
            '</div>';
    }

    function routesTree(items) {
        var root = {};
        items.forEach(function (route) {
            var parts = route.uri.split('/');
            var node = root;
            parts.forEach(function (part, index) {
                var key = part === '' ? '/' : part;
                node.children = node.children || {};
                node.children[key] = node.children[key] || { children: {}, routes: [] };
                node = node.children[key];
                if (index === parts.length - 1) { node.routes.push(route); }
            });
        });
        return '<div class="bones-wrap"><div class="bones-tree">' + treeNodes(root.children) + '</div></div>';
    }

    function treeNodes(children) {
        return Object.keys(children).sort().map(function (key) {
            var node = children[key];
            var hasChildren = Object.keys(node.children).length > 0;
            var routeRows = node.routes.map(function (route) {
                return '<div class="bones-node__head">' + methodBadges(route.methods) +
                    '<span class="bones-node__label">' + esc(key) + '</span>' +
                    (route.name ? '<span class="bones-card__name">' + esc(route.name) + '</span>' : '') + '</div>';
            }).join('');
            return '<div class="bones-node' + (hasChildren ? '' : ' is-leaf') + '">' +
                '<div class="bones-node__head">' +
                (hasChildren ? icon('chevron', 'bones-node__chevron') : '<span style="width:14px"></span>') +
                icon(hasChildren ? 'layout' : 'route') +
                '<span class="bones-node__label">' + esc(key) + '</span></div>' +
                '<div class="bones-node__children">' + routeRows + (hasChildren ? treeNodes(node.children) : '') + '</div></div>';
        }).join('');
    }

    function wireTree(scope) {
        scope.querySelectorAll('.bones-node').forEach(function (node) {
            var head = node.querySelector(':scope > .bones-node__head');
            var children = node.querySelector(':scope > .bones-node__children');
            if (!head || !children) { return; }
            head.addEventListener('click', function () { node.classList.toggle('is-open'); });
        });
    }

    /* ----------------------------------------------------------------- views */

    renderers.views = function () {
        var data = skeleton.views.data;
        var items = data.items || [];
        var kinds = ['layout', 'view', 'component'];

        var f = filters('views');
        f.innerHTML = '';
        select('views', '<select>' + options(kinds, state.views.kind, 'All kinds') + '</select>', function (value) {
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

        meta('views').textContent = filtered.length + ' of ' + items.length + ' views';

        if (filtered.length === 0) {
            body('views').innerHTML = empty('No views match.');
            return;
        }

        if (state.views.view === 'graph') {
            body('views').innerHTML = '<div class="bones-graph" data-graph></div>';
            drawGraph(body('views').querySelector('[data-graph]'), data.graph, filtered);
            return;
        }

        body('views').innerHTML = '<div class="bones-grid bones-grid--cards">' + filtered.map(viewCard).join('') + '</div>';
    };

    function viewCard(view) {
        var kindBadge = { layout: 'accent', component: 'purple', view: 'ghost' }[view.kind] || 'ghost';
        var refs = []
            .concat(view.extends.map(function (n) { return '<span class="bones-badge bones-badge--accent bones-badge--ghost">extends ' + esc(n) + '</span>'; }))
            .concat(view.includes.map(function (n) { return '<span class="bones-badge bones-badge--good bones-badge--ghost">includes ' + esc(n) + '</span>'; }));

        return '<div class="bones-card">' +
            '<div class="bones-card__top"><span class="bones-badge bones-badge--' + kindBadge + '">' + esc(view.kind) + '</span>' +
            (view.package ? '<span class="bones-badge bones-badge--ghost">' + esc(view.package) + '</span>' : '') +
            '<span class="bones-card__name">' + esc(view.lines) + ' lines</span></div>' +
            '<div class="bones-card__uri" style="font-size:13px">' + esc(view.name) +
            '<button class="bones-copy" data-copy="' + esc(view.name) + '" title="Copy">' + icon('copy') + '</button></div>' +
            '<div class="bones-card__meta"><span class="bones-mono">' + esc(view.path) + '</span></div>' +
            (refs.length ? '<div class="bones-card__meta"><div class="bones-badges">' + refs.join('') + '</div></div>' : '') +
            (view.used_by.length ? '<div class="bones-card__meta"><span class="bones-dim">used by ' + view.used_by.length + '</span></div>' : '') +
            '</div>';
    }

    function drawGraph(container, graph, visible) {
        graph = graph || { nodes: [], edges: [] };
        var allowed = {};
        visible.forEach(function (node) { allowed[node.id] = node; });

        var nodes = graph.nodes.filter(function (node) { return allowed[node.id]; });
        var edges = graph.edges.filter(function (edge) { return allowed[edge.from] && allowed[edge.to]; });

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

        var maxLayer = Math.max.apply(null, [0].concat(Object.values(layer)));
        var columns = [];
        nodes.forEach(function (node) {
            var l = maxLayer - layer[node.id];
            (columns[l] = columns[l] || []).push(node);
        });

        var nodeW = 200, nodeH = 34, gapY = 26, gapX = 60, pad = 24;
        var pos = {}; var width = 0;
        columns.forEach(function (column, ci) {
            var x = pad + ci * (nodeW + gapX);
            width = Math.max(width, x + nodeW + pad);
            column.forEach(function (node, ri) {
                pos[node.id] = { x: x, y: pad + ri * (nodeH + gapY) };
            });
        });
        var height = Math.max.apply(null, [200].concat(nodes.map(function (n) { return pos[n.id].y + nodeH; }))) + pad;

        var edgePaths = edges.map(function (edge) {
            var a = pos[edge.from], b = pos[edge.to];
            var x1 = a.x + nodeW / 2, y1 = a.y + nodeH, x2 = b.x + nodeW / 2, y2 = b.y;
            var d = 'M' + x1 + ',' + y1 + ' C' + x1 + ',' + (y1 + (y2 - y1) / 2) + ' ' + x2 + ',' + (y2 - (y2 - y1) / 2) + ' ' + x2 + ',' + y2;
            return '<path class="edge" data-from="' + esc(edge.from) + '" data-to="' + esc(edge.to) + '" d="' + d + '" marker-end="url(#bones-arrow)"></path>';
        }).join('');

        var nodeGroups = nodes.map(function (node) {
            var p = pos[node.id];
            return '<g class="gnode" data-id="' + esc(node.id) + '" data-kind="' + esc(node.kind) + '">' +
                '<rect x="' + p.x + '" y="' + p.y + '" width="' + nodeW + '" height="' + nodeH + '" rx="8"></rect>' +
                '<text x="' + (p.x + 12) + '" y="' + (p.y + 21) + '">' + esc(truncate(node.label, 26)) + '</text></g>';
        }).join('');

        var svg = '<svg viewBox="0 0 ' + width + ' ' + height + '" width="' + width + '" height="' + height + '" preserveAspectRatio="xMidYMin meet">' +
            '<defs><marker id="bones-arrow" viewBox="0 0 10 10" refX="8" refY="5" markerWidth="6" markerHeight="6" orient="auto-start-reverse">' +
            '<path d="M0,0 L10,5 L0,10 z" fill="#3b82f6"></path></marker></defs>' +
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
        var data = skeleton.packages.data;
        var items = data.items || [];

        var f = filters('packages');
        f.innerHTML = '';
        select('packages', '<select>' + options(['composer', 'npm'], state.packages.ecosystem, 'All sources') + '</select>', function (value) {
            state.packages.ecosystem = value; refresh('packages');
        });
        toggle('packages', 'Direct only', state.packages.direct, function (value) {
            state.packages.direct = value; refresh('packages');
        });
        toggle('packages', 'Laravel only', state.packages.laravel, function (value) {
            state.packages.laravel = value; refresh('packages');
        });

        var filtered = items.filter(function (pkg) {
            if (state.packages.ecosystem && pkg.ecosystem !== state.packages.ecosystem) { return false; }
            if (state.packages.direct && !pkg.direct) { return false; }
            if (state.packages.laravel && !pkg.laravel) { return false; }
            return matches(state.packages.query, [pkg.name, pkg.description, pkg.license]);
        });

        meta('packages').textContent = filtered.length + ' of ' + items.length + ' packages';

        if (filtered.length === 0) {
            body('packages').innerHTML = empty('No packages match.');
            return;
        }

        body('packages').innerHTML = '<div class="bones-wrap"><table class="bones-table"><thead><tr>' +
            '<th>Package</th><th>Version</th><th>Source</th><th>Flags</th><th>Description</th></tr></thead><tbody>' +
            filtered.map(packageRow).join('') + '</tbody></table></div>';
    };

    function packageRow(pkg) {
        var flags = [];
        if (pkg.direct) { flags.push('<span class="bones-badge bones-badge--accent">direct</span>'); }
        if (pkg.dev) { flags.push('<span class="bones-badge">dev</span>'); }
        if (pkg.laravel) { flags.push('<span class="bones-badge bones-badge--good">laravel</span>'); }
        if (pkg.abandoned) { flags.push('<span class="bones-badge bones-badge--bad">abandoned</span>'); }
        var name = pkg.homepage
            ? '<a href="' + esc(pkg.homepage) + '" target="_blank" rel="noopener">' + esc(pkg.name) + '</a>'
            : esc(pkg.name);

        return '<tr class="bones-row">' +
            '<td class="bones-mono">' + name + '</td>' +
            '<td class="bones-mono bones-dim">' + esc(pkg.version) + '</td>' +
            '<td><span class="bones-badge bones-badge--ghost">' + esc(pkg.ecosystem) + '</span></td>' +
            '<td><div class="bones-badges">' + (flags.join('') || '<span class="bones-dim">—</span>') + '</div></td>' +
            '<td class="bones-dim">' + esc(pkg.description || '') + '</td></tr>';
    }

    /* --------------------------------------------------------------- explore */

    renderers.explore = function () {
        var categories = Array.from(new Set(explore.map(function (item) { return item.category; }))).sort();

        var f = filters('explore');
        f.innerHTML = '';
        select('explore', '<select>' + options(categories, state.explore.category, 'All categories') + '</select>', function (value) {
            state.explore.category = value; refresh('explore');
        });
        toggle('explore', 'Installed only', state.explore.installed, function (value) {
            state.explore.installed = value; refresh('explore');
        });

        var filtered = explore.filter(function (item) {
            if (state.explore.category && item.category !== state.explore.category) { return false; }
            if (state.explore.installed && !item.installed) { return false; }
            return matches(state.explore.query, [item.name, item.description, item.category, (item.tags || []).join(' ')]);
        });

        meta('explore').textContent = filtered.length + ' of ' + explore.length + ' packages';

        body('explore').innerHTML = filtered.length === 0
            ? empty('Nothing on the shelf matches.')
            : '<div class="bones-grid bones-grid--cards">' + filtered.map(exploreCard).join('') + '</div>';
    };

    function exploreCard(item) {
        var tags = (item.tags || []).map(function (tag) {
            return '<span class="bones-badge bones-badge--ghost">' + esc(tag) + '</span>';
        }).join('');
        return '<div class="bones-card">' +
            '<div class="bones-card__top">' +
            '<span class="bones-badge bones-badge--ghost">' + esc(item.category) + '</span>' +
            (item.installed
                ? '<span class="bones-badge bones-badge--good">installed ' + esc(item.installed_version || '') + '</span>'
                : '<span class="bones-badge">not installed</span>') +
            '</div>' +
            '<div class="bones-card__uri" style="font-size:13px">' + esc(item.name) +
            '<a href="' + esc(item.url) + '" target="_blank" rel="noopener" class="bones-copy" title="Open">' + icon('external') + '</a></div>' +
            '<div class="bones-card__meta">' + esc(item.description) + '</div>' +
            '<div class="bones-card__meta"><div class="bones-badges">' + tags + '</div></div>' +
            '</div>';
    }

    /* ------------------------------------------------------------------ boot */

    Object.keys(skeleton).forEach(function (key) {
        var count = (skeleton[key].data.items || []).length;
        var slot = document.querySelector('[data-count="' + key + '"]');
        if (slot) { slot.textContent = count; }
    });

    renderers.overview();
    rendered.overview = true;
    activate('overview');
})();
</script>
