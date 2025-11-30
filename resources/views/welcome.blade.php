<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Billstack | Modern Billing That Scales</title>
    <style>
        :root {
            color-scheme: light dark;
            --primary: #4338ca;
            --primary-dark: #312e81;
            --accent: #06b6d4;
        }

        body {
            --bg-gradient: radial-gradient(circle at top, rgba(67, 56, 202, 0.25), transparent 55%), #0f172a;
            --text-color: #ffffff;
            --muted-color: #ffffff;
            --eyebrow-color: #ffffff;
            --nav-text: #ffffff;
            --card-bg: rgba(15, 23, 42, 0.65);
            --border-color: rgba(148, 163, 184, 0.25);
            --progress-track: rgba(148, 163, 184, 0.2);
            --progress-fill: linear-gradient(90deg, var(--accent), var(--primary));
            --glow-highlight: rgba(6, 182, 212, 0.25);
            --stat-subtle: #f8fafc;
            --surface-contrast: rgba(12, 20, 36, 0.8);
        }

        body[data-theme="light"] {
            --bg-gradient: radial-gradient(circle at top, rgba(148, 163, 184, 0.25), transparent 50%), #f4f6fb;
            --text-color: #0f172a;
            --muted-color: #475569;
            --eyebrow-color: #2563eb;
            --nav-text: #475569;
            --card-bg: rgba(255, 255, 255, 0.92);
            --border-color: rgba(15, 23, 42, 0.08);
            --progress-track: rgba(148, 163, 184, 0.35);
            --progress-fill: linear-gradient(90deg, var(--primary), var(--accent));
            --glow-highlight: rgba(59, 130, 246, 0.3);
            --stat-subtle: #0f172a;
            --surface-contrast: rgba(255, 255, 255, 0.92);
        }

        @keyframes fadeUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes floatCard {
            0% {
                transform: translateY(0);
            }
            50% {
                transform: translateY(-8px);
            }
            100% {
                transform: translateY(0);
            }
        }

        @keyframes pulseGlow {
            0% {
                opacity: 0.35;
                transform: scale(0.95);
            }
            50% {
                opacity: 0.7;
                transform: scale(1.05);
            }
            100% {
                opacity: 0.35;
                transform: scale(0.95);
            }
        }

        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            font-family: 'Inter', 'Segoe UI', system-ui, -apple-system, BlinkMacSystemFont, sans-serif;
            background: var(--bg-gradient);
            color: var(--text-color);
            min-height: 100vh;
            transition: background 0.4s ease, color 0.2s ease;
        }

        a {
            color: inherit;
            text-decoration: none;
        }

        header {
            padding: 24px 5vw;
            display: flex;
            align-items: center;
            justify-content: space-between;
            position: sticky;
            top: 0;
            z-index: 50;
            backdrop-filter: blur(12px);
            background: var(--surface-contrast);
            border-bottom: 1px solid var(--border-color);
            box-shadow: 0 12px 30px rgba(2, 6, 23, 0.35);
        }

        body[data-theme="light"] header {
            box-shadow: 0 10px 25px rgba(15, 23, 42, 0.1);
        }

        .logo {
            font-weight: 700;
            font-size: 1.25rem;
            letter-spacing: 0.05em;
        }

        nav {
            display: flex;
            gap: 24px;
            font-size: 0.95rem;
            color: var(--nav-text);
        }

        .cta {
            display: flex;
            gap: 12px;
        }

        .scroll-progress {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 4px;
            background: var(--progress-track);
            z-index: 60;
        }

        .scroll-progress__bar {
            height: 100%;
            width: 0;
            background: var(--progress-fill);
            transition: width 0.15s ease-out;
        }

        .btn {
            border-radius: 999px;
            padding: 0.85rem 1.6rem;
            border: 1px solid rgba(255, 255, 255, 0.4);
            font-weight: 600;
            transition: transform 0.2s ease, box-shadow 0.2s ease, background 0.2s ease;
            color: var(--text-color);
        }

        body[data-theme="light"] .btn {
            border-color: rgba(15, 23, 42, 0.15);
            color: #0f172a;
        }

        .btn-primary {
            background: linear-gradient(90deg, var(--primary), var(--accent));
            border: none;
            color: #fff;
            box-shadow: 0 15px 30px rgba(67, 56, 202, 0.35);
        }

        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 20px 35px rgba(6, 182, 212, 0.35);
        }

        .theme-switcher {
            position: relative;
        }

        .theme-icon {
            width: 42px;
            height: 42px;
            border-radius: 999px;
            border: 1px solid rgba(255, 255, 255, 0.5);
            background: transparent;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.2rem;
            cursor: pointer;
            transition: transform 0.2s ease, background 0.2s ease;
            color: var(--text-color);
        }

        body[data-theme="light"] .theme-icon {
            border-color: rgba(15, 23, 42, 0.35);
            color: #0f172a;
        }

        .theme-icon:hover {
            background: rgba(255, 255, 255, 0.12);
            transform: translateY(-1px);
        }

        .theme-menu {
            position: absolute;
            top: 120%;
            right: 0;
            background: var(--card-bg);
            border: 1px solid var(--border-color);
            border-radius: 14px;
            padding: 8px;
            min-width: 150px;
            box-shadow: 0 20px 35px rgba(2, 6, 23, 0.4);
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
            background: rgba(59, 130, 246, 0.2);
        }

        main {
            padding: 0 5vw 80px 5vw;
        }

        .hero {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 40px;
            align-items: center;
            padding: 80px 0 40px;
        }

        .hero-visuals {
            display: flex;
            flex-direction: column;
            gap: 24px;
        }

        @keyframes shimmerLine {
            0% {
                background-position: 0% 50%;
            }
            100% {
                background-position: 200% 50%;
            }
        }

        @keyframes floatSlow {
            0% {
                transform: translateY(0);
            }
            50% {
                transform: translateY(-10px);
            }
            100% {
                transform: translateY(0);
            }
        }

        @keyframes orbitOne {
            0% {
                transform: translate(-50%, -50%) rotate(0deg) translateX(85px);
            }
            100% {
                transform: translate(-50%, -50%) rotate(360deg) translateX(85px);
            }
        }

        @keyframes orbitTwo {
            0% {
                transform: translate(-50%, -50%) rotate(0deg) translateY(70px);
            }
            100% {
                transform: translate(-50%, -50%) rotate(360deg) translateY(70px);
            }
        }

        @keyframes orbitThree {
            0% {
                transform: translate(-50%, -50%) rotate(0deg) translateX(-70px);
            }
            100% {
                transform: translate(-50%, -50%) rotate(360deg) translateX(-70px);
            }
        }

        .hero h1 {
            font-size: clamp(2.5rem, 5vw, 3.75rem);
            margin-bottom: 1rem;
            color: var(--text-color);
        }

        .hero p {
            font-size: 1.1rem;
            color: var(--muted-color);
            margin-bottom: 2rem;
            line-height: 1.6;
        }

        .glow-card {
            background: var(--card-bg);
            border: 1px solid var(--border-color);
            border-radius: 24px;
            padding: 32px;
            position: relative;
            overflow: hidden;
            animation: floatCard 8s ease-in-out infinite;
        }

        .glow-card::before {
            content: '';
            position: absolute;
            inset: 0;
            background: radial-gradient(circle, var(--glow-highlight), transparent 60%);
            opacity: 0.5;
            pointer-events: none;
            animation: pulseGlow 6s ease-in-out infinite;
        }

        .illustration {
            position: relative;
            border-radius: 24px;
            padding: 28px;
            background: radial-gradient(circle at top, rgba(99, 102, 241, 0.3), rgba(14, 165, 233, 0.08)) #0b152d;
            border: 1px solid var(--border-color);
            overflow: hidden;
            min-height: 260px;
        }

        body[data-theme="light"] .illustration {
            background: radial-gradient(circle at top, rgba(99, 102, 241, 0.12), rgba(14, 165, 233, 0.06)) #f8fafc;
        }

        .invoice-card {
            border-radius: 18px;
            padding: 18px 20px;
            background: rgba(15, 23, 42, 0.85);
            border: 1px solid rgba(255, 255, 255, 0.08);
            box-shadow: 0 20px 35px rgba(15, 23, 42, 0.45);
            position: relative;
            z-index: 1;
            animation: floatSlow 7s ease-in-out infinite;
            color: #fff;
        }

        body[data-theme="light"] .invoice-card {
            background: rgba(15, 23, 42, 0.85);
            color: #fff;
        }

        .invoice-card.secondary {
            width: 70%;
            margin-top: 20px;
            margin-left: auto;
            opacity: 0.9;
            animation-delay: 0.4s;
        }

        .invoice-card .card-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 16px;
            font-size: 0.95rem;
        }

        .invoice-card .badge {
            padding: 4px 12px;
            border-radius: 999px;
            background: rgba(16, 185, 129, 0.2);
            color: #fff;
            font-size: 0.8rem;
            font-weight: 600;
        }

        .invoice-card .amount {
            font-size: 1.25rem;
            font-weight: 600;
        }

        .invoice-card .line {
            height: 10px;
            border-radius: 999px;
            background: linear-gradient(90deg, rgba(148, 163, 184, 0.2), rgba(94, 234, 212, 0.6), rgba(148, 163, 184, 0.2));
            background-size: 200% 100%;
            margin-bottom: 10px;
            animation: shimmerLine 2.2s ease infinite;
        }

        .invoice-card .line.short {
            width: 60%;
        }

        .invoice-card .line:last-child {
            margin-bottom: 0;
        }

        .invoice-card .line.delay {
            animation-delay: 0.4s;
        }

        .orbit-dot {
            position: absolute;
            top: 50%;
            left: 50%;
            width: 12px;
            height: 12px;
            border-radius: 50%;
            background: var(--accent);
            box-shadow: 0 0 12px rgba(6, 182, 212, 0.8);
            opacity: 0.85;
        }

        .dot-1 {
            animation: orbitOne 12s linear infinite;
        }

        .dot-2 {
            animation: orbitTwo 16s linear infinite;
        }

        .dot-3 {
            animation: orbitThree 13s linear infinite;
        }

        .illustration::after {
            content: '';
            position: absolute;
            inset: 18px;
            border: 1px dashed var(--border-color);
            border-radius: 24px;
            pointer-events: none;
        }

        .stats {
            display: flex;
            flex-wrap: wrap;
            gap: 24px;
            margin-top: 32px;
        }

        .stat {
            flex: 1;
            min-width: 160px;
        }

        .stat h3 {
            font-size: 2rem;
            margin: 0;
            color: var(--text-color);
        }

        .stat p {
            margin: 0.35rem 0 0;
            color: var(--muted-color);
        }

        .features {
            margin-top: 80px;
        }

        .features-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 24px;
            margin-top: 32px;
        }

        .feature-card {
            background: var(--card-bg);
            border: 1px solid var(--border-color);
            border-radius: 20px;
            padding: 24px;
            transition: transform 0.2s ease, border-color 0.2s ease;
            opacity: 0;
            transform: translateY(24px);
            animation: fadeUp 1s ease forwards;
        }

        .feature-card:hover {
            transform: translateY(-4px);
            border-color: rgba(94, 234, 212, 0.8);
        }

        .feature-card h4 {
            margin: 0 0 12px;
            font-size: 1.1rem;
            color: var(--text-color);
        }

        .feature-card p {
            margin: 0;
            color: var(--muted-color);
            line-height: 1.5;
        }

        .testimonial {
            margin-top: 80px;
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 32px;
            align-items: center;
        }

        .testimonial blockquote {
            margin: 0;
            font-size: 1.25rem;
            line-height: 1.7;
            color: var(--text-color);
        }

        .testimonial cite {
            display: block;
            margin-top: 16px;
            font-style: normal;
            color: var(--muted-color);
        }

        .animate-in {
            opacity: 0;
            transform: translateY(28px);
            animation: fadeUp 1s ease forwards;
        }

        .delay-1 {
            animation-delay: 0.15s;
        }

        .delay-2 {
            animation-delay: 0.3s;
        }

        .delay-3 {
            animation-delay: 0.45s;
        }

        .features-grid .feature-card:nth-child(1) { animation-delay: 0.1s; }
        .features-grid .feature-card:nth-child(2) { animation-delay: 0.2s; }
        .features-grid .feature-card:nth-child(3) { animation-delay: 0.3s; }
        .features-grid .feature-card:nth-child(4) { animation-delay: 0.4s; }

        footer {
            padding: 40px 5vw 60px;
            text-align: center;
            color: var(--muted-color);
            font-size: 0.95rem;
        }

        .scroll-top {
            position: fixed;
            bottom: 32px;
            right: 32px;
            width: 52px;
            height: 52px;
            border-radius: 50%;
            border: none;
            background: linear-gradient(135deg, var(--primary), var(--accent));
            color: #fff;
            font-size: 1.4rem;
            box-shadow: 0 15px 30px rgba(15, 23, 42, 0.4);
            cursor: pointer;
            opacity: 0;
            pointer-events: none;
            transition: opacity 0.3s ease, transform 0.3s ease;
            z-index: 70;
        }

        .scroll-top.visible {
            opacity: 1;
            pointer-events: auto;
            transform: translateY(-4px);
        }

        @media (max-width: 640px) {
            header {
                flex-direction: column;
                gap: 16px;
            }

            nav {
                flex-wrap: wrap;
                justify-content: center;
                gap: 12px;
            }

            .hero-visuals {
                width: 100%;
            }

            .hero {
                grid-template-columns: 1fr;
                gap: 32px;
                padding-top: 56px;
            }

            .glow-card,
            .illustration {
                padding: 22px;
            }

            .illustration {
                min-height: 220px;
            }

            .cta {
                width: 100%;
                flex-direction: column;
            }

            .btn,
            .theme-icon {
                width: 100%;
                text-align: center;
            }

            .stats {
                flex-direction: column;
            }
        }
    </style>
</head>
<body data-theme="dark">
    <div class="scroll-progress">
        <div class="scroll-progress__bar" id="scroll-progress-bar"></div>
    </div>

    <header>
        <div class="logo">BILLSTACK</div>
        <nav>
            <a href="#features">Features</a>
            <a href="#testimonials">Testimonials</a>
            <a href="#contact">Contact</a>
        </nav>
        <div class="cta">
            <div class="theme-switcher" data-theme-switcher>
                <button class="theme-icon" id="theme-toggle" type="button" aria-haspopup="true" aria-expanded="false" aria-label="Toggle theme" data-theme-toggle>
                    ☾
                </button>
                <div class="theme-menu" id="theme-menu" role="menu">
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
            <a class="btn" href="{{ route('login') }}">Sign In</a>
            <a class="btn btn-primary" href="{{ route('login') }}">Launch App</a>
        </div>
    </header>

    <main>
        <section class="hero">
            <div class="animate-in">
                <p style="color:var(--eyebrow-color); letter-spacing: 0.15em; font-size:0.85rem; text-transform: uppercase; margin:0 0 12px;">
                    Automated invoicing for ambitious teams
                </p>
                <h1>
                    Build trust with every invoice you send.
                </h1>
                <p>
                    Billstack streamlines customer onboarding, recurring invoices, and payment tracking so your crew
                    can focus on delivering remarkable work instead of chasing receipts.
                </p>
                <div class="cta">
                    <a class="btn btn-primary" href="{{ route('login') }}">Start Billing Smarter</a>
                    <a class="btn" href="#features">See Features</a>
                </div>
                <div class="stats">
                    <div class="stat">
                        <h3 class="counter" data-target="4800" data-suffix="+">0</h3>
                        <p>Recurring invoices automated monthly</p>
                    </div>
                    <div class="stat">
                        <h3 class="counter" data-target="98" data-suffix="%">0%</h3>
                        <p>Faster payment collection times</p>
                    </div>
                    <div class="stat">
                        <h3 class="counter" data-target="24" data-suffix="/7">0</h3>
                        <p>Audit-ready financial overview</p>
                    </div>
                </div>
            </div>
            <div class="hero-visuals">
                <div class="glow-card animate-in delay-1">
                    <h3 style="margin-top:0; color:var(--text-color);">Real-time financial clarity</h3>
                    <ul style="list-style:none; padding:0; margin:24px 0 0; color:var(--muted-color);">
                        <li style="margin-bottom:18px;">
                            <strong>Smart templates</strong> keep branding consistent, automatically applying your
                            color palette, logo, and billing terms.
                        </li>
                        <li style="margin-bottom:18px;">
                            <strong>Predictive revenue</strong> forecasts cash flow based on accepted quotes and active
                            subscriptions.
                        </li>
                        <li>
                            <strong>Collaboration-ready</strong> approvals let managers review, annotate, and release invoices
                            from anywhere.
                        </li>
                    </ul>
                </div>
                <div class="illustration animate-in delay-2" aria-hidden="true">
                    <div class="invoice-card">
                        <div class="card-header">
                            <span class="badge">Paid</span>
                            <span class="amount">$4,200.00</span>
                        </div>
                        <div class="line"></div>
                        <div class="line delay"></div>
                        <div class="line short"></div>
                    </div>
                    <div class="invoice-card secondary">
                        <div class="card-header">
                            <span class="badge" style="background: rgba(249, 115, 22, 0.2); color: #fff;">Due</span>
                            <span class="amount">$1,150.00</span>
                        </div>
                        <div class="line short"></div>
                        <div class="line"></div>
                        <div class="line delay"></div>
                    </div>
                    <span class="orbit-dot dot-1"></span>
                    <span class="orbit-dot dot-2"></span>
                    <span class="orbit-dot dot-3"></span>
                </div>
            </div>
        </section>

        <section id="features" class="features">
            <p style="color:var(--eyebrow-color); letter-spacing: 0.15em; font-size:0.85rem; text-transform: uppercase; margin:0;">
                Powering every finance moment
            </p>
            <h2 style="margin-top:8px; font-size:2.2rem; color:var(--text-color);">Designed for scale, ready today.</h2>
            <div class="features-grid">
                <article class="feature-card">
                    <h4>Recurring Profiles</h4>
                    <p>Generate subscription invoices in seconds with flexible billing cycles and automated reminders.</p>
                </article>
                <article class="feature-card">
                    <h4>Customer Portals</h4>
                    <p>Give clients real-time access to invoices, payment history, and statements in a secure hub.</p>
                </article>
                <article class="feature-card">
                    <h4>Insightful Reports</h4>
                    <p>Visual dashboards spotlight MRR, ARR, and outstanding revenue without wrestling spreadsheets.</p>
                </article>
                <article class="feature-card">
                    <h4>Team Workflows</h4>
                    <p>Role-based permissions and audit logs keep everyone aligned while preserving compliance.</p>
                </article>
            </div>
        </section>

        <section id="testimonials" class="testimonial">
            <div class="glow-card animate-in delay-2">
                <blockquote>
                    “Billstack replaced three disconnected tools and cut our billing prep time by 70%.
                    Clients experience beautifully branded invoices and our finance team finally gets to make strategic moves.”
                </blockquote>
                <cite>Olivia Hughes · CFO, Northwind Studios</cite>
            </div>
            <div class="glow-card animate-in delay-3">
                <h3 style="margin-top:0; color:var(--text-color);">Built on Laravel 12</h3>
                <p style="margin-bottom:1.5rem; color:var(--muted-color);">
                    Blazing fast performance, rock-solid security, and familiar developer ergonomics mean you can extend the
                    platform without friction.
                </p>
                <ul style="list-style:none; padding:0; margin:0; color:var(--muted-color);">
                    <li style="margin-bottom:14px;">✓ Feature-rich invoice templates</li>
                    <li style="margin-bottom:14px;">✓ Payment reconciliation flows</li>
                    <li style="margin-bottom:14px;">✓ Business and customer management</li>
                    <li>✓ Ready-to-run dashboard and reporting</li>
                </ul>
            </div>
        </section>

        <section id="contact" style="margin-top:80px; text-align:center;">
            <p style="color:var(--eyebrow-color); letter-spacing: 0.15em; font-size:0.85rem; text-transform: uppercase; margin:0;">
                Ready when you are
            </p>
            <h2 style="margin:12px 0; font-size:2.5rem; color:var(--text-color);">Your billing workflow deserves better.</h2>
            <p style="margin:0 auto 32px; max-width:640px; color:var(--muted-color);">
                Move from spreadsheets to a delightful all-in-one billing experience. Connect your business profile,
                invite your team, and automate finance rituals in a single afternoon.
            </p>
            <a class="btn btn-primary" href="{{ route('login') }}">Go to Dashboard</a>
        </section>
    </main>

    <footer>
        © {{ now()->year }} Billstack. Built for growing service businesses.
    </footer>

    <button class="scroll-top" id="scroll-top" aria-label="Scroll to top">↑</button>

    <script>
        const themeKey = 'billstack-theme';
        const switcher = document.querySelector('[data-theme-switcher]');
        const toggleButton = document.querySelector('[data-theme-toggle]');
        const themeMenu = document.getElementById('theme-menu');
        const themeOptions = document.querySelectorAll('[data-theme-option]');

        const updateToggleIcon = (theme) => {
            if (!toggleButton) {
                return;
            }
            toggleButton.textContent = theme === 'dark' ? '☾' : '☀️';
            toggleButton.setAttribute('aria-label', theme === 'dark' ? 'Switch to light mode' : 'Switch to dark mode');
            toggleButton.setAttribute('aria-expanded', switcher?.classList.contains('open') ? 'true' : 'false');
            themeOptions.forEach(option => {
                option.classList.toggle('active', option.dataset.themeOption === theme);
            });
        };

        const applyTheme = (theme) => {
            document.body.setAttribute('data-theme', theme);
            updateToggleIcon(theme);
        };

        const storedTheme = localStorage.getItem(themeKey) || 'dark';
        applyTheme(storedTheme);

        toggleButton?.addEventListener('click', () => {
            if (!switcher) {
                return;
            }
            switcher.classList.toggle('open');
            toggleButton.setAttribute('aria-expanded', switcher.classList.contains('open') ? 'true' : 'false');
        });

        themeOptions.forEach(option => {
            option.addEventListener('click', () => {
                const selectedTheme = option.dataset.themeOption;
                if (!selectedTheme) {
                    return;
                }
                localStorage.setItem(themeKey, selectedTheme);
                applyTheme(selectedTheme);
                switcher?.classList.remove('open');
                toggleButton?.setAttribute('aria-expanded', 'false');
            });
        });

        document.addEventListener('click', (event) => {
            if (!switcher || !toggleButton) {
                return;
            }
            if (!switcher.contains(event.target)) {
                switcher.classList.remove('open');
                toggleButton.setAttribute('aria-expanded', 'false');
            }
        });

        const counters = document.querySelectorAll('.counter');
        const observer = new IntersectionObserver(entries => {
            entries.forEach(entry => {
                if (entry.isIntersecting && !entry.target.dataset.animated) {
                    const el = entry.target;
                    const target = Number(el.dataset.target);
                    const suffix = el.dataset.suffix ?? '';
                    const duration = 1500;
                    const startTime = performance.now();
                    const formatValue = value => {
                        const rounded = Math.round(value);
                        if (suffix === '%') {
                            return `${rounded}%`;
                        }
                        if (suffix === '/7') {
                            return `${rounded}/7`;
                        }
                        const formatted = rounded.toLocaleString();
                        return suffix ? `${formatted}${suffix}` : formatted;
                    };

                    const animate = time => {
                        const progress = Math.min((time - startTime) / duration, 1);
                        const current = target * progress;
                        el.textContent = formatValue(current);
                        if (progress < 1) {
                            requestAnimationFrame(animate);
                        }
                    };

                    el.dataset.animated = 'true';
                    requestAnimationFrame(animate);
                }
            });
        }, { threshold: 0.5 });

        counters.forEach(counter => observer.observe(counter));

        const progressBar = document.getElementById('scroll-progress-bar');
        const scrollTopButton = document.getElementById('scroll-top');

        const handleScroll = () => {
            const scrollTop = window.scrollY;
            const docHeight = document.body.scrollHeight - window.innerHeight;
            const progress = docHeight ? (scrollTop / docHeight) * 100 : 0;
            progressBar.style.width = `${progress}%`;

            if (scrollTop > 200) {
                scrollTopButton.classList.add('visible');
            } else {
                scrollTopButton.classList.remove('visible');
            }
        };

        window.addEventListener('scroll', handleScroll, { passive: true });

        scrollTopButton.addEventListener('click', () => {
            window.scrollTo({ top: 0, behavior: 'smooth' });
        });
    </script>
</body>
</html>
