<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Billstack')</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <style>
        :root {
            color-scheme: light dark;
            --primary: #4338ca;
            --accent: #06b6d4;
        }

        body.app-body {
            margin: 0;
            font-family: 'Inter', 'Segoe UI', system-ui, -apple-system, BlinkMacSystemFont, sans-serif;
            --app-bg: radial-gradient(circle at top, rgba(67, 56, 202, 0.35), transparent 55%), #0f172a;
            --text-color: #ffffff;
            --muted-text: #ffffff;
            --brand-subtext: #cbd5f5;
            --header-bg: rgba(12, 20, 36, 0.85);
            --panel-bg: rgba(11, 17, 37, 0.9);
            --border-color: rgba(148, 163, 184, 0.25);
            --nav-link: #f8fafc;
            --input-bg: rgba(15, 23, 42, 0.7);
            --input-border: rgba(148, 163, 184, 0.35);
            --table-header-bg: #1d4ed8;
            --table-header-color: #ffffff;
            background: var(--app-bg);
            color: var(--text-color);
            min-height: 100vh;
        }

        body.app-body[data-theme="light"] {
            --app-bg: #f8fafc;
            --text-color: #0f172a;
            --muted-text: #475569;
            --brand-subtext: #64748b;
            --header-bg: rgba(255, 255, 255, 0.95);
            --panel-bg: rgba(255, 255, 255, 0.92);
            --border-color: rgba(15, 23, 42, 0.1);
            --nav-link: #475569;
            --input-bg: rgba(255, 255, 255, 0.95);
            --input-border: rgba(148, 163, 184, 0.35);
        }

        .app-gradient {
            position: fixed;
            inset: 0;
            background: radial-gradient(circle at 10% 20%, rgba(6, 182, 212, 0.18), transparent 30%),
                radial-gradient(circle at 80% 20%, rgba(67, 56, 202, 0.25), transparent 40%);
            pointer-events: none;
            z-index: 0;
        }

        .app-header {
            position: sticky;
            top: 0;
            z-index: 30;
            background: var(--header-bg);
            border-bottom: 1px solid var(--border-color);
            backdrop-filter: blur(18px);
            box-shadow: 0 12px 30px rgba(2, 6, 23, 0.35);
        }

        .app-header__inner {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 1.25rem;
            flex-wrap: wrap;
            padding: 1rem;
        }

        .brand-group {
            display: flex;
            align-items: center;
            gap: 0.85rem;
        }

        .brand-mark {
            width: 44px;
            height: 44px;
            border-radius: 16px;
            background: linear-gradient(135deg, var(--primary), var(--accent));
            display: grid;
            place-items: center;
            font-weight: 700;
            color: #fff;
            letter-spacing: 0.05em;
        }

        .brand-copy {
            line-height: 1.2;
        }

        .brand-copy span {
            font-weight: 700;
            font-size: 1.1rem;
        }

        .brand-copy p {
            margin: 0;
            font-size: 0.85rem;
            color: var(--brand-subtext);
        }

        .app-nav {
            display: flex;
            flex-wrap: wrap;
            gap: 0.65rem;
        }

        .app-nav__link {
            padding: 0.4rem 0.95rem;
            border-radius: 999px;
            font-weight: 500;
            color: var(--nav-link);
            border: 1px solid transparent;
            transition: border-color 0.2s ease, color 0.2s ease, background 0.2s ease;
        }

        .app-nav__link:hover {
            color: #fff;
            border-color: rgba(148, 163, 184, 0.5);
        }

        body.app-body[data-theme="light"] .app-nav__link:hover {
            color: #0f172a;
        }

        .app-nav__link.is-active {
            background: linear-gradient(120deg, var(--primary), var(--accent));
            color: #fff;
            border-color: transparent;
            box-shadow: 0 8px 18px rgba(6, 182, 212, 0.25);
        }

        .app-actions {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            flex-wrap: wrap;
        }

        .business-switch label {
            font-size: 0.75rem;
            color: var(--brand-subtext);
            margin-right: 0.35rem;
        }

        .business-switch select {
            background: var(--input-bg);
            border: 1px solid var(--input-border);
            border-radius: 12px;
            color: var(--text-color);
            padding: 0.4rem 0.8rem;
            font-size: 0.85rem;
        }

        .user-pill {
            padding: 0.4rem 0.9rem;
            border-radius: 999px;
            background: rgba(148, 163, 184, 0.15);
            font-size: 0.85rem;
            color: var(--text-color);
        }

        .btn-outline {
            border-radius: 999px;
            padding: 0.45rem 1rem;
            border: 1px solid rgba(248, 250, 252, 0.35);
            color: var(--text-color);
            font-size: 0.85rem;
            transition: background 0.2s ease, color 0.2s ease;
            background: transparent;
        }

        body.app-body[data-theme="light"] .btn-outline {
            border-color: rgba(15, 23, 42, 0.2);
            color: #0f172a;
        }

        .btn-outline:hover {
            background: rgba(148, 163, 184, 0.2);
        }
        
        .theme-switcher {
            position: relative;
        }

        .theme-icon {
            width: 38px;
            height: 38px;
            border-radius: 999px;
            border: 1px solid rgba(248, 250, 252, 0.35);
            background: transparent;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.1rem;
            color: var(--text-color);
            transition: transform 0.2s ease, background 0.2s ease;
        }

        body.app-body[data-theme="light"] .theme-icon {
            border-color: rgba(15, 23, 42, 0.2);
            color: #0f172a;
        }

        .theme-icon:hover {
            background: rgba(248, 250, 252, 0.15);
            transform: translateY(-1px);
        }

        .theme-menu {
            position: absolute;
            top: 115%;
            right: 0;
            background: var(--panel-bg);
            border: 1px solid var(--border-color);
            border-radius: 14px;
            padding: 8px;
            min-width: 150px;
            box-shadow: 0 20px 35px rgba(2, 6, 23, 0.45);
            opacity: 0;
            pointer-events: none;
            transform: translateY(6px);
            transition: opacity 0.2s ease, transform 0.2s ease;
        }

        .theme-switcher.open .theme-menu {
            opacity: 1;
            pointer-events: auto;
            transform: translateY(0);
        }

        .theme-menu button {
            width: 100%;
            border: none;
            background: transparent;
            color: var(--text-color);
            padding: 6px 10px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            font-size: 0.9rem;
            cursor: pointer;
            margin-bottom: 4px;
        }

        .theme-menu button span {
            font-size: 1.1rem;
        }

        .theme-menu button:hover,
        .theme-menu button.active {
            background: rgba(59, 130, 246, 0.25);
        }

        .btn-primary-pill {
            padding: 0.5rem 1.2rem;
            border-radius: 999px;
            background: linear-gradient(135deg, var(--primary), var(--accent));
            color: #fff;
            font-weight: 600;
            box-shadow: 0 12px 24px rgba(67, 56, 202, 0.35);
        }

        .app-main {
            position: relative;
            z-index: 1;
        }

        .glass-panel {
            background: var(--panel-bg);
            border: 1px solid var(--border-color);
            border-radius: 18px;
            box-shadow: 0 25px 45px rgba(2, 6, 23, 0.45);
            padding: 1.75rem;
        }

        .page-heading {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 1.5rem;
        }

        .page-heading h1 {
            margin: 0;
            font-size: clamp(1.5rem, 3vw, 2.2rem);
            color: var(--text-color);
        }

        .eyebrow {
            text-transform: uppercase;
            letter-spacing: 0.2em;
            font-size: 0.75rem;
            color: #93c5fd;
            margin-bottom: 0.5rem;
        }

        .content-wrapper {
            margin-top: 1.75rem;
            display: flex;
            flex-direction: column;
            gap: 1.5rem;
        }

        .alert-card,
        .error-card {
            border-radius: 16px;
            padding: 1rem 1.25rem;
            border: 1px solid rgba(248, 250, 252, 0.15);
            box-shadow: 0 15px 30px rgba(15, 23, 42, 0.35);
        }

        .alert-card {
            background: rgba(16, 185, 129, 0.1);
            color: #bbf7d0;
        }

        .error-card {
            background: rgba(248, 113, 113, 0.1);
            color: #fecaca;
        }

        .error-card ul {
            margin: 0.5rem 0 0;
            padding-left: 1.2rem;
        }

        .content-wrapper :is(input, select, textarea) {
            background: var(--input-bg);
            border: 1px solid var(--input-border);
            border-radius: 12px;
            color: var(--text-color);
        }

        .content-wrapper table {
            background: rgba(15, 23, 42, 0.65);
            border-radius: 20px;
            overflow: hidden;
            border: 1px solid var(--border-color);
        }

        body.app-body[data-theme="light"] .content-wrapper table {
            background: rgba(255, 255, 255, 0.95);
        }

        .content-wrapper table thead th {
            background: var(--table-header-bg);
            color: var(--table-header-color);
            border-bottom: 1px solid rgba(255, 255, 255, 0.2);
        }

        .dataTable thead th {
            background: var(--table-header-bg) !important;
            color: #fff !important;
            border-bottom: 1px solid rgba(255, 255, 255, 0.25) !important;
        }

        .bg-white {
            background: var(--panel-bg) !important;
            border: 1px solid var(--border-color);
            color: var(--text-color);
        }

        .text-gray-700,
        .text-gray-600,
        .text-gray-500,
        .text-gray-900,
        .text-gray-800 {
            color: var(--muted-text) !important;
        }

        body.app-body[data-theme="light"] .text-gray-900,
        body.app-body[data-theme="light"] .text-gray-800 {
            color: #0f172a !important;
        }

        .shadow {
            box-shadow: 0 20px 45px rgba(2, 6, 23, 0.45) !important;
        }

        @media (max-width: 960px) {
            .page-heading {
                flex-direction: column;
                align-items: flex-start;
            }
        }

        @media (max-width: 640px) {
            .app-header__inner {
                flex-direction: column;
                align-items: flex-start;
            }

            .app-actions {
                width: 100%;
                justify-content: space-between;
            }

            .app-nav {
                width: 100%;
            }

            .glass-panel {
                padding: 1.25rem;
            }
        }
    </style>
</head>
<body class="app-body" data-theme="dark">
    <div class="app-gradient" aria-hidden="true"></div>
    <header class="app-header">
        @php
            $user = auth()->user();
            $isSuperAdmin = $user?->isSuperAdmin() ?? false;
            $businesses = $isSuperAdmin ? \App\Models\Business::orderBy('name')->get() : collect();
            $activeBusiness = $user?->activeBusiness();
            $navLinks = [
                ['label' => 'Dashboard', 'route' => 'dashboard', 'pattern' => 'dashboard'],
                ['label' => 'Customers', 'route' => 'customers.index', 'pattern' => 'customers.*'],
                ['label' => 'Items', 'route' => 'items.index', 'pattern' => 'items.*'],
                ['label' => 'Invoices', 'route' => 'invoices.index', 'pattern' => 'invoices.*'],
                ['label' => 'Recurring', 'route' => 'recurring-profiles.index', 'pattern' => 'recurring-profiles.*'],
                ['label' => 'Reports', 'route' => 'reports.invoices', 'pattern' => 'reports.*'],
            ];
        @endphp
        <div class="app-header__inner container mx-auto px-4">
            <div class="brand-group">
                <div class="brand-mark">BS</div>
                <div class="brand-copy">
                    <span>Billstack</span>
                    <p>Finance Console</p>
                </div>
            </div>
            @auth
                <nav class="app-nav flex-1">
                    @foreach($navLinks as $link)
                        <a
                            href="{{ route($link['route']) }}"
                            class="app-nav__link {{ request()->routeIs($link['pattern']) ? 'is-active' : '' }}"
                        >
                            {{ $link['label'] }}
                        </a>
                    @endforeach
                </nav>
            @endauth
            <div class="app-actions">
                @if($isSuperAdmin)
                    <form action="{{ route('business.switch') }}" method="POST" class="business-switch flex items-center gap-2">
                        @csrf
                        <label>Business</label>
                        <select name="business_id" onchange="this.form.submit()">
                            @foreach($businesses as $businessOption)
                                <option value="{{ $businessOption->id }}" @selected($activeBusiness?->id === $businessOption->id)>
                                    {{ $businessOption->name }}
                                </option>
                            @endforeach
                        </select>
                    </form>
                @endif
                <div class="theme-switcher" data-theme-switcher>
                    <button class="theme-icon" type="button" aria-label="Toggle theme" aria-expanded="false" data-theme-toggle>☾</button>
                    <div class="theme-menu" role="menu">
                        <button type="button" data-theme-option="dark" role="menuitem">
                            <span>☾</span>
                            <strong>Dark Mode</strong>
                        </button>
                        <button type="button" data-theme-option="light" role="menuitem">
                            <span>☀️</span>
                            <strong>Light Mode</strong>
                        </button>
                    </div>
                </div>
                @auth
                    <span class="user-pill">{{ $user->name }}</span>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button class="btn-outline" type="submit">Logout</button>
                    </form>
                @else
                    <a href="{{ route('login') }}" class="btn-primary-pill">Sign in</a>
                @endauth
            </div>
        </div>
    </header>
    <main class="app-main container mx-auto px-4 py-10">
        <section class="glass-panel page-heading">
            <div>
                <p class="eyebrow">Billstack Platform</p>
                <h1>@yield('page_title', 'Dashboard')</h1>
            </div>
            @hasSection('page_actions')
                <div>@yield('page_actions')</div>
            @endif
        </section>
        <div class="content-wrapper">
            @if(session('success'))
                <div class="alert-card">
                    {{ session('success') }}
                </div>
            @endif
            @if($errors->any())
                <div class="error-card">
                    <strong>We found some issues:</strong>
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
            <div>
                @yield('content')
            </div>
        </div>
    </main>
    <script>
        (function() {
            const themeKey = 'billstack-theme';
            const switchers = document.querySelectorAll('[data-theme-switcher]');
            const toggleButtons = document.querySelectorAll('[data-theme-toggle]');
            const themeOptions = document.querySelectorAll('[data-theme-option]');

            const updateToggleDisplays = (theme) => {
                toggleButtons.forEach(button => {
                    button.textContent = theme === 'dark' ? '☾' : '☀️';
                    const container = button.closest('[data-theme-switcher]');
                    const expanded = container?.classList.contains('open') ? 'true' : 'false';
                    button.setAttribute('aria-expanded', expanded);
                    button.setAttribute('aria-label', theme === 'dark' ? 'Switch to light mode' : 'Switch to dark mode');
                });
                themeOptions.forEach(option => {
                    option.classList.toggle('active', option.dataset.themeOption === theme);
                });
            };

            const applyTheme = (theme) => {
                document.body.setAttribute('data-theme', theme);
                updateToggleDisplays(theme);
            };

            const storedTheme = localStorage.getItem(themeKey) || 'dark';
            applyTheme(storedTheme);

            toggleButtons.forEach(button => {
                button.addEventListener('click', () => {
                    const container = button.closest('[data-theme-switcher]');
                    if (!container) {
                        return;
                    }
                    container.classList.toggle('open');
                    button.setAttribute('aria-expanded', container.classList.contains('open') ? 'true' : 'false');
                });
            });

            themeOptions.forEach(option => {
                option.addEventListener('click', () => {
                    const selectedTheme = option.dataset.themeOption;
                    if (!selectedTheme) {
                        return;
                    }
                    localStorage.setItem(themeKey, selectedTheme);
                    applyTheme(selectedTheme);
                    const container = option.closest('[data-theme-switcher]');
                    const toggle = container?.querySelector('[data-theme-toggle]');
                    container?.classList.remove('open');
                    toggle?.setAttribute('aria-expanded', 'false');
                });
            });

            document.addEventListener('click', (event) => {
                switchers.forEach(container => {
                    if (!container.contains(event.target)) {
                        container.classList.remove('open');
                        const toggle = container.querySelector('[data-theme-toggle]');
                        toggle?.setAttribute('aria-expanded', 'false');
                    }
                });
            });
        })();
    </script>
</body>
</html>
