<!DOCTYPE html>
<html lang="en" data-theme="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="robots" content="noindex, nofollow">
    <title>Laravel Bones</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <script>
        (function () {
            var root = document.documentElement;
            var theme;
            try {
                theme = localStorage.getItem('bones-theme');
            } catch (e) {
                theme = null;
            }
            if (theme !== 'light' && theme !== 'dark') {
                theme = window.matchMedia && window.matchMedia('(prefers-color-scheme: light)').matches ? 'light' : 'dark';
            }
            root.setAttribute('data-theme', theme);
        })();
    </script>
    @include('bones::partials.styles')
</head>
<body>
    @include('bones::partials.icons')

    <div class="b-shell">
        <header class="b-header">
            <div class="b-header__inner">
                <a class="b-brand" href="{{ route('bones.dashboard') }}">
                    <span class="b-brand__mark">
                        <svg viewBox="0 0 24 24" aria-hidden="true"><use href="#i-bone"></use></svg>
                    </span>
                    <span class="b-brand__text">
                        <strong>Laravel Bones</strong>
                        <small>skeleton of this application</small>
                    </span>
                </a>

                <nav class="b-tabs" role="tablist" aria-label="Sections">
                    <button class="b-tab" role="tab" data-tab="overview" aria-selected="true" title="Overview">
                        <svg viewBox="0 0 24 24" aria-hidden="true"><use href="#i-grid"></use></svg>
                        <span>Overview</span>
                    </button>
                    @foreach ($skeleton as $section)
                        <button class="b-tab" role="tab" data-tab="{{ $section['key'] }}"
                                aria-selected="false" title="{{ $section['label'] }}">
                            <svg viewBox="0 0 24 24" aria-hidden="true"><use href="#i-{{ $section['icon'] }}"></use></svg>
                            <span>{{ $section['label'] }}</span>
                            <span class="b-tab__count" data-count="{{ $section['key'] }}">0</span>
                        </button>
                    @endforeach
                </nav>

                <div class="b-actions">
                    <a class="b-btn b-btn--ghost" href="{{ route('bones.data') }}" target="_blank" rel="noopener" title="View raw JSON">
                        <svg viewBox="0 0 24 24" aria-hidden="true"><use href="#i-code"></use></svg>
                        <span class="b-btn__label">JSON</span>
                    </a>
                    <button class="b-btn b-btn--ghost b-theme" id="theme-toggle" type="button" title="Toggle theme" aria-label="Toggle theme">
                        <svg class="b-theme__moon" viewBox="0 0 24 24" aria-hidden="true"><use href="#i-moon"></use></svg>
                        <svg class="b-theme__sun" viewBox="0 0 24 24" aria-hidden="true"><use href="#i-sun"></use></svg>
                        <span class="b-btn__label">Theme</span>
                    </button>
                    <a class="b-btn b-btn--primary" href="?fresh=1" title="Rescan the application">
                        <svg viewBox="0 0 24 24" aria-hidden="true"><use href="#i-refresh"></use></svg>
                        <span class="b-btn__label">Rescan</span>
                    </a>
                </div>
            </div>
        </header>

        <main class="b-main">
            <section class="b-panel" data-panel="overview" role="tabpanel"></section>

            @foreach ($skeleton as $section)
                <section class="b-panel is-hidden" data-panel="{{ $section['key'] }}" role="tabpanel">
                    <div class="b-toolbar">
                        <div class="b-search">
                            <svg viewBox="0 0 24 24" aria-hidden="true"><use href="#i-search"></use></svg>
                            <input type="search" data-search="{{ $section['key'] }}" placeholder="Filter {{ strtolower($section['label']) }}…" autocomplete="off" spellcheck="false">
                            <kbd class="b-kbd">/</kbd>
                        </div>
                        <div class="b-filters" data-filters="{{ $section['key'] }}"></div>
                        <div class="b-toolbar__meta" data-meta="{{ $section['key'] }}"></div>
                    </div>

                    <div data-body="{{ $section['key'] }}"></div>
                </section>
            @endforeach
        </main>

        <footer class="b-footer">
            <span>{{ count($skeleton) }} probes</span>
            <span class="b-dot">·</span>
            <a href="https://github.com/keyvanarasteh/laravel-bones" target="_blank" rel="noopener">keyvanarasteh/laravel-bones</a>
            <span class="b-dot">·</span>
            <span>MIT</span>
            <span class="b-footer__spacer"></span>
            <span class="b-hint"><kbd class="b-kbd">/</kbd> search <kbd class="b-kbd">esc</kbd> clear</span>
        </footer>
    </div>

    <div class="b-toast" data-toast hidden></div>

    <script type="application/json" id="bones-data">@json(['skeleton' => $skeleton])</script>
    @include('bones::partials.scripts')
</body>
</html>
