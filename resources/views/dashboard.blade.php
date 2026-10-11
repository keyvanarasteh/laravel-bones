<!DOCTYPE html>
<html lang="en" data-theme="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="robots" content="noindex, nofollow">
    <title>Laravel Bones</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <script>
        (function() {
            var html = document.documentElement;
            var savedTheme = localStorage.getItem('bones-theme');
            var themeToggle = document.getElementById('theme-toggle');
            
            function updateTheme(theme) {
                html.setAttribute('data-theme', theme);
                localStorage.setItem('bones-theme', theme);
            }
            
            if (!savedTheme) {
                var prefersLight = window.matchMedia('(prefers-color-scheme: light)').matches;
                updateTheme(prefersLight ? 'light' : 'dark');
            } else {
                updateTheme(savedTheme);
            }
            
            if (themeToggle) {
                themeToggle.addEventListener('click', function() {
                    var current = html.getAttribute('data-theme');
                    var next = current === 'dark' ? 'light' : 'dark';
                    updateTheme(next);
                });
            }
        })();
    </script>
    @include('bones::partials.styles')
</head>
<body>
    @include('bones::partials.icons')

    <div class="bones-shell">
        <header class="bones-header">
            <div class="bones-header__inner">
                <div class="bones-brand">
                    <span class="bones-brand__mark">
                        <svg viewBox="0 0 24 24" aria-hidden="true"><use href="#i-bone"></use></svg>
                    </span>
                    <span class="bones-brand__text">
                        <strong>Laravel Bones</strong>
                        <small>a skeleton of this application</small>
                    </span>
                </div>

                <nav class="bones-tabs" role="tablist" aria-label="Sections">
                    <button class="bones-tab" role="tab" data-tab="overview" aria-selected="true">
                        <svg viewBox="0 0 24 24" aria-hidden="true"><use href="#i-grid"></use></svg>
                        Overview
                    </button>
                    @foreach ($skeleton as $section)
                        <button class="bones-tab" role="tab" data-tab="{{ $section['key'] }}"
                                aria-selected="false">
                            <svg viewBox="0 0 24 24" aria-hidden="true"><use href="#i-{{ $section['icon'] }}"></use></svg>
                            {{ $section['label'] }}
                            <span class="bones-tab__count" data-count="{{ $section['key'] }}">0</span>
                        </button>
                    @endforeach
                    <button class="bones-tab" role="tab" data-tab="explore" aria-selected="false">
                        <svg viewBox="0 0 24 24" aria-hidden="true"><use href="#i-sparkles"></use></svg>
                        Explore
                    </button>
                </nav>

                 <div class="bones-actions">
                     <a class="bones-btn bones-btn--ghost" href="{{ route('bones.data') }}" target="_blank" rel="noopener">
                         <svg viewBox="0 0 24 24" aria-hidden="true"><use href="#i-code"></use></svg>
                         JSON
                     </a>
                     <button class="bones-btn" id="theme-toggle" type="button">
                         <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                             <circle cx="12" cy="12" r="5"/>
                             <line x1="12" y1="1" x2="12" y2="3"/>
                             <line x1="12" y1="21" x2="12" y2="23"/>
                             <line x1="4" y1="12" x2="20" y2="12"/>
                         </svg>
                         Theme
                     </button>
                     <a class="bones-btn bones-btn--primary" href="?fresh=1">
                         <svg viewBox="0 0 24 24" aria-hidden="true"><use href="#i-refresh"></use></svg>
                         Rescan
                     </a>
                 </div>
            </div>
        </header>

        <main class="bones-main">
            <section class="bones-panel" data-panel="overview" role="tabpanel"></section>

            @foreach ($skeleton as $section)
                <section class="bones-panel is-hidden" data-panel="{{ $section['key'] }}" role="tabpanel">
                    <div class="bones-toolbar">
                        <div class="bones-search">
                            <svg viewBox="0 0 24 24" aria-hidden="true"><use href="#i-search"></use></svg>
                            <input type="search" data-search="{{ $section['key'] }}" placeholder="Filter {{ strtolower($section['label']) }}…" autocomplete="off">
                        </div>
                        <div class="bones-filters" data-filters="{{ $section['key'] }}"></div>
                        <div class="bones-toolbar__meta" data-meta="{{ $section['key'] }}"></div>
                    </div>

                    <div data-body="{{ $section['key'] }}"></div>
                </section>
            @endforeach

            <section class="bones-panel is-hidden" data-panel="explore" role="tabpanel">
                <div class="bones-toolbar">
                    <div class="bones-search">
                        <svg viewBox="0 0 24 24" aria-hidden="true"><use href="#i-search"></use></svg>
                        <input type="search" data-search="explore" placeholder="Search the shelf…" autocomplete="off">
                    </div>
                    <div class="bones-filters" data-filters="explore"></div>
                    <div class="bones-toolbar__meta" data-meta="explore"></div>
                </div>
                <div data-body="explore"></div>
            </section>
        </main>

        <footer class="bones-footer">
            <span>{{ count($skeleton) }} probes</span>
            <span class="bones-dot">·</span>
            <a href="https://github.com/keyvanarasteh/laravel-bones" target="_blank" rel="noopener">keyvanarasteh/laravel-bones</a>
            <span class="bones-dot">·</span>
            <span>MIT</span>
        </footer>
    </div>

    <div class="bones-toast" data-toast hidden></div>

    <script type="application/json" id="bones-data">@json(['skeleton' => $skeleton, 'explore' => $explore])</script>
    @include('bones::partials.scripts')
</body>
</html>
