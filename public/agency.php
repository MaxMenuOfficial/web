<!DOCTYPE html>
<html lang="en" data-theme="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>MaxMenu — Agencies</title>
  <link rel="stylesheet" href="styles/footer.css">
<style>
/* ========================================= */
/* ROOT VARIABLES — ESTÉTICA SOBRIA          */
/* ========================================= */
:root {

    --font-main: "Inter", -apple-system, BlinkMacSystemFont, sans-serif;

    /* DARK MODE COLORS */
    --dark-bg: #000000;
    --dark-panel: #0d0d0d;
    --dark-panel-2: #151515;
    --dark-border: rgba(255,255,255,0.08);
    --dark-text: #ffffff;
    --dark-text-soft: rgba(255,255,255,0.8);

    /* LIGHT MODE COLORS */
    --light-bg: #ffffff;
    --light-panel: #f7f7f7;
    --light-panel-2: #ededed;
    --light-border: rgba(0,0,0,0.08);
    --light-text: #000000;
    --light-text-soft: rgba(0,0,0,0.7);

    /* Default (dark) */
    --color-bg: var(--dark-bg);
    --color-panel: var(--dark-panel);
    --color-panel-2: var(--dark-panel-2);
    --color-border: var(--dark-border);
    --color-text: var(--dark-text);
    --color-text-soft: var(--dark-text-soft);

    --radius-soft: 14px;
    --radius-strong: 18px;

    --transition: 0.25s ease;
}

[data-theme="light"] {
    --color-bg: var(--light-bg);
    --color-panel: var(--light-panel);
    --color-panel-2: var(--light-panel-2);
    --color-border: var(--light-border);
    --color-text: var(--light-text);
    --color-text-soft: var(--light-text-soft);
}

/* =============================== */
/* GLOBAL STYLES                  */
/* =============================== */

* { margin: 0; padding: 0; box-sizing: border-box; }

/* 🔥 Bloqueo de scroll horizontal global */
html, body {
    width: 100%;
    max-width: 100%;
    overflow-x: hidden;
}

body {
    font-family: var(--font-main);
    background: var(--color-bg);
    color: var(--color-text);
    transition: background var(--transition), color var(--transition);
    padding-top: 80px; /* header */
}

.section {
    width: 100%;
    padding: 120px 32px;
    display: flex;
    justify-content: center;
}

.section-inner {
    width: 100%;
    max-width: 1400px;
}

/* 🔥 Imágenes responsivas dentro del contenido */
.section-inner img {
    max-width: 100%;
    height: auto;
    display: block;
}

h1, h2, h3 {
    letter-spacing: -1px;
    font-weight: 700;
}

p {
    color: var(--color-text-soft);
    line-height: 1.6;
    margin-top: 12px;
}

.section-header {
    max-width: 820px;
    margin-bottom: 52px;
}

.section-tag {
    display: inline-flex;
    align-items: center;
    padding: 4px 10px;
    border-radius: 100px;
    border: 1px solid var(--color-border);
    font-size: 0.75rem;
    letter-spacing: 0.08em;
    text-transform: uppercase;
    opacity: 0.8;
    margin-bottom: 16px;
}

/* ========================================= */
/* MEDIA FRAME (IMÁGENES / MOCKUPS)          */
/* ========================================= */

.media-frame {
    border-radius: 18px;
    padding: 1px;
    background: radial-gradient(circle at top left, rgba(180,99,255,0.25), transparent 55%),
                radial-gradient(circle at bottom right, rgba(79,209,197,0.20), transparent 55%);
    border: 1px solid rgba(255,255,255,0.06);
}

[data-theme="light"] .media-frame {
    border: 1px solid rgba(0,0,0,0.06);
    background: radial-gradient(circle at top left, rgba(180,99,255,0.12), transparent 55%),
                radial-gradient(circle at bottom right, rgba(79,209,197,0.12), transparent 55%);
}

.media-inner {
    border-radius: inherit;
    background: var(--color-panel-2);
    overflow: hidden;
    position: relative;
}

.media-inner img,
.media-inner video {
    width: 100%;
    height: 100%;
    display: block;
    object-fit: cover;
}

/* ========================================= */
/* HERO AGENCY                               */
/* ========================================= */

.hero {
    text-align: center;
    padding-top: 160px;
    padding-bottom: 160px;
}

.hero-title {
    font-size: clamp(2.6rem, 5vw, 3.6rem);
    max-width: 900px;
    margin: auto;
}

.hero-sub {
    font-size: 1.15rem;
    margin-top: 25px;
    max-width: 780px;
    margin-left: auto;
    margin-right: auto;
}

.hero-cta {
    margin-top: 38px;
}

.hero-cta button {
    padding: 16px 34px;
    font-size: 1rem;
    border: 1px solid var(--color-border);
    background: var(--color-panel-2);
    color: var(--color-text);
    border-radius: 8px;
    cursor: pointer;
    transition: var(--transition);
}

.hero-cta button:hover {
    background: var(--color-panel);
}

.hero-layout {
    margin-top: 80px;
    display: grid;
    grid-template-columns: minmax(0, 1.8fr) minmax(0, 1.5fr);
    gap: 32px;
    align-items: stretch;
}

.hero-card {
    background: var(--color-panel);
    border-radius: var(--radius-soft);
    border: 1px solid var(--color-border);
    padding: 26px;
    text-align: left;
}

.hero-meta {
    display: flex;
    flex-wrap: wrap;
    gap: 10px;
    margin-top: 14px;
}

.hero-pill {
    padding: 5px 11px;
    border-radius: 999px;
    border: 1px solid var(--color-border);
    font-size: 0.78rem;
    text-transform: uppercase;
    letter-spacing: 0.06em;
    opacity: 0.85;
}

/* ========================================= */
/* AGENCY ECONOMICS                          */
/* ========================================= */

.agency-econ {
    background: var(--color-panel);
    border-radius: var(--radius-soft);
    border: 1px solid var(--color-border);
    padding: 26px;
}

.agency-econ-top {
    display: flex;
    justify-content: space-between;
    align-items: baseline;
    gap: 14px;
    flex-wrap: wrap;
}

.agency-commission {
    display: inline-flex;
    align-items: baseline;
    gap: 6px;
}

.agency-commission-strong {
    font-size: 1.8rem;
    font-weight: 700;
}

.agency-commission-label {
    font-size: 0.95rem;
    opacity: 0.9;
}

.agency-econ-list {
    margin-top: 16px;
    display: grid;
    gap: 8px;
    font-size: 0.95rem;
}

/* ========================================= */
/* AGENCY FLOW GRID                          */
/* ========================================= */

.agency-layout {
    display: grid;
    grid-template-columns: minmax(0, 1.7fr) minmax(0, 1.5fr);
    gap: 32px;
    align-items: stretch;
}

.agency-card {
    background: var(--color-panel);
    border-radius: var(--radius-soft);
    border: 1px solid var(--color-border);
    padding: 26px;
}

.agency-bullets {
    margin-top: 18px;
    display: grid;
    gap: 10px;
    font-size: 0.95rem;
}

.agency-bullet {
    background: var(--color-panel-2);
    border-radius: 10px;
    border: 1px solid var(--color-border);
    padding: 10px 12px;
}

.agency-steps {
    margin-top: 20px;
    font-size: 0.9rem;
    opacity: 0.9;
}

.agency-steps ol {
    margin: 10px 0 0;
    padding-left: 18px;
    line-height: 1.7;
}

/* ========================================= */
/* TRANSFER FLOW (ESTILO SHOPIFY)            */
/* ========================================= */

.transfer-grid {
    display: grid;
    grid-template-columns: minmax(0, 1.7fr) minmax(0, 1.5fr);
    gap: 32px;
    align-items: stretch;
}

.transfer-card {
    background: var(--color-panel);
    border-radius: var(--radius-soft);
    border: 1px solid var(--color-border);
    padding: 26px;
}

.transfer-steps {
    margin-top: 18px;
    font-size: 0.95rem;
}

.transfer-steps ol {
    margin-top: 10px;
    padding-left: 18px;
    line-height: 1.7;
}

.transfer-note {
    margin-top: 14px;
    font-size: 0.9rem;
    opacity: 0.9;
}

/* ========================================= */
/* ROLE CARDS (Owner / Agency)               */
/* ========================================= */

.role-cards {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(260px, 1fr));
    gap: 24px;
    margin-top: 20px;
}

.role-card {
    position: relative;
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 18px;

    padding: 22px 24px;
    border-radius: var(--radius-soft);

    background: var(--color-panel-2);
    border: 1px solid var(--color-border);
    box-shadow: 0 0 0 1px rgba(255,255,255,0.02);

    text-decoration: none;
    color: var(--color-text);
    cursor: pointer;

    transition:
        background var(--transition),
        border-color var(--transition),
        box-shadow var(--transition),
        transform var(--transition);
}

.role-card::before {
    content: "";
    position: absolute;
    inset: 0;
    border-radius: inherit;
    background:
        radial-gradient(circle at top left, rgba(180,99,255,0.18), transparent 55%);
    opacity: 0;
    pointer-events: none;
    transition: opacity var(--transition);
}

.role-card:hover {
    background: var(--color-panel);
    border-color: rgba(180, 99, 255, 0.65);
    box-shadow:
        0 0 0 1px rgba(180, 99, 255, 0.35),
        0 12px 26px rgba(0, 0, 0, 0.6);
    transform: translateY(-2px);
}

.role-card:hover::before {
    opacity: 1;
}

.role-card-text h2 {
    font-size: 1.2rem;
    margin: 0;
}

.role-card-text p {
    margin-top: 6px;
    font-size: 0.9rem;
    color: var(--color-text-soft);
}

.role-card-icon {
    width: 24px;
    flex-shrink: 0;
    opacity: 0.9;
}

/* ========================================= */
/* CTA FINAL                                 */
/* ========================================= */

.final-cta {
    text-align: center;
    padding: 120px 32px 160px;
}

.final-cta button {
    padding: 18px 42px;
    border-radius: 10px;
    border: 1px solid var(--color-border);
    background: var(--color-panel-2);
    color: var(--color-text);
    font-size: 1.15rem;
    cursor: pointer;
    transition: var(--transition);
}

.final-cta button:hover {
    background: var(--color-panel);
}

/* ========================================= */
/* RESPONSIVE                                */
/* ========================================= */

@media (max-width: 960px) {
    .hero-layout,
    .agency-layout,
    .transfer-grid {
        grid-template-columns: 1fr;
    }
}

@media (max-width: 720px) {
    .section {
        padding: 80px 20px;
    }
    .hero {
        padding-top: 120px;
        padding-bottom: 120px;
    }
}
</style>

    <!-- Sincroniza tema con lo guardado; el toggle vive en el header -->
    <script>
        document.addEventListener("DOMContentLoaded", () => {
            const html = document.documentElement;
            const saved = localStorage.getItem("maxmenu-theme");
            if (saved) html.setAttribute("data-theme", saved);
        });
    </script>
</head>
<body>

<?php include 'templates/header.php'; ?>

<!-- ========================= -->
<!-- HERO AGENCIES             -->
<!-- ========================= -->
<div class="section hero">
    <div class="section-inner">

        <div class="section-tag">Agencies</div>

        <h1 class="hero-title">
            One agency panel.<br>
            Unlimited client restaurants and recurring revenue.
        </h1>

        <p class="hero-sub">
          MaxMenu offers agencies a dedicated workspace where they can create, design, and manage menus for multiple restaurants <BR></BR>In addition, they earn a 0.5% commission on each payment processed from their restaurant clients’ customers. This feature will be available starting in Q2 2026.
        </p>

        <div class="hero-cta">
            <button>Become a MaxMenu agency partner</button>
        </div>

        <div class="hero-layout">

            <div class="hero-card">
                <h3>Designed for agencies, not as an afterthought.</h3>
                <div class="hero-meta">
                    <div class="hero-pill">Multi-restaurant dashboard</div>
                    <div class="hero-pill">Client-ready handoff</div>
                    <div class="hero-pill">Stripe Connect</div>
                </div>
                <p>
                    From a single login you can spin up development restaurants, configure menus,
                    connect domains and align the entire design system with each brand. When a
                    restaurant is ready for production, you transfer ownership — just like in
                    Shopify — and keep collaboration access.
                </p>
                <p>
                    Your clients own the restaurant, billing and legal responsibilities. You keep
                    control of the menu experience and a recurring share of every in-menu payment.
                </p>
            </div>


        </div>

    </div>
</div>


<!-- ========================= -->
<!-- ECONOMÍA AGENCIA          -->
<!-- ========================= -->
<div class="section">
    <div class="section-inner">

        <div class="section-header">
            <div class="section-tag">Partner economics</div>
            <h2>An extra revenue line attached to every order you help generate.</h2>
            <p>
                Agencies don’t just charge for design and implementation. With MaxMenu, every time
                a restaurant you manage processes an item payment inside the menu, you earn a share
                — without touching the transaction flow.
            </p>
        </div>

        <div class="agency-econ">
            <div class="agency-econ-top">
                <div>
                    <div class="agency-commission">
                        <span class="agency-commission-strong">0.5%</span>
                        <span class="agency-commission-label">
                            per item payment processed by your client restaurants.
                        </span>
                    </div>
                </div>
                <div class="hero-pill">Paid automatically through Stripe Connect</div>
            </div>

            <div class="agency-econ-list">
                <div>• Clients pay for their subscriptions and item payments as usual.</div>
                <div>• MaxMenu collects a small platform fee per transaction.</div>
                <div>• Your agency automatically receives 0.5% of each item payment routed through the menu.</div>
                <div>• You don’t handle billing, invoices or tax for the restaurant — only your own agency revenue.</div>
            </div>

            <p style="margin-top:16px;">
                The result: the more your restaurants sell through MaxMenu, the more your agency earns,
                without adding friction to the guest or the operator.
            </p>
        </div>

    </div>
</div>


<!-- ========================= -->
<!-- FLUJO PARTNER / CONNECT   -->
<!-- ========================= -->
<div class="section">
    <div class="section-inner">

        <div class="section-header">
            <div class="section-tag">How agencies work with MaxMenu</div>
            <h2>Build menus, transfer ownership and get paid through Stripe Connect.</h2>
            <p>
                Agencies connect their own Stripe account once. From there, every restaurant they
                onboard can go live on their own subscription, while the agency keeps a recurring
                0.5% share on in-menu payments.
            </p>
        </div>

        <div class="agency-layout">

            <!-- Columna texto -->
            <div class="agency-card">
                <h3>Agency panel with Stripe Connect.</h3>
                <p>
                    When you sign up as an agency, MaxMenu walks you through a Stripe Connect flow
                    where you add your business details and payout information. This is how we send
                    you your 0.5% partner fee on every item payment.
                </p>

                <div class="agency-bullets">
                    <div class="agency-bullet">
                        You only configure your Connect account once — we handle the rest of the
                        routing for each restaurant you manage.
                    </div>
                    <div class="agency-bullet">
                        Each client restaurant has its own subscription and Stripe customer; your
                        agency never touches their invoices or tax configuration.
                    </div>
                    <div class="agency-bullet">
                        Commissions are calculated automatically at transaction level and paid out
                        to your Connect account.
                    </div>
                </div>

                <div class="agency-steps">
                    <strong>Agency setup in four steps</strong>
                    <ol>
                        <li>Sign up as an agency from the partner login.</li>
                        <li>Complete your Stripe Connect onboarding with your business details.</li>
                        <li>Create development restaurants and design their menus.</li>
                        <li>Transfer each restaurant to its owner when it goes live.</li>
                    </ol>
                </div>
            </div>


        </div>

    </div>
</div>


<!-- ========================= -->
<!-- TRANSFERENCIA ESTILO SHOPIFY -->
<!-- ========================= -->
<div class="section">
    <div class="section-inner">

        <div class="section-header">
            <div class="section-tag">Ownership transfer</div>
            <h2>Shopify-style restaurant transfers, built for agencies.</h2>
            <p>
                Agencies can work exactly like they do with Shopify stores: create development
                restaurants under their account, build everything, and then transfer the restaurant
                to the real owner in a single, formal step — without losing access.
            </p>
        </div>

        <div class="transfer-grid">

            <div class="transfer-card">
                <h3>From development to production in one handoff.</h3>
                <div class="transfer-steps">
                    <ol>
                        <li>
                            <strong>Create a development restaurant.</strong>
                            You design the entire menu, connect the widget to the client’s website
                            and fine-tune the experience without asking for a payment method.
                        </li>
                        <li>
                            <strong>Invite the owner to take over.</strong>
                            When the project is ready, you trigger an ownership transfer. The owner
                            receives a secure link to claim the restaurant.
                        </li>
                        <li>
                            <strong>The owner adds their billing details.</strong>
                            They connect their Stripe customer and choose the subscription plan.
                            From that moment, they are the legal and fiscal owner of the restaurant.
                        </li>
                        <li>
                            <strong>You stay as a collaborator.</strong>
                            Your agency keeps access through the agency panel, can continue updating
                            the menu, and receives the 0.5% fee on item payments.
                        </li>
                    </ol>
                </div>

                <p class="transfer-note">
                    The result is a clean separation of responsibilities: the restaurant owns the
                    business and payments; the agency owns the experience and earns on top of it.
                </p>
            </div>


        </div>

    </div>
</div>


<!-- ========================= -->
<!-- ACCESO DIRECTO OWNER / AGENCY -->
<!-- ========================= -->
<div class="section">
    <div class="section-inner">

        <div class="section-header" style="margin-bottom:32px;">
            <div class="section-tag">Access</div>
            <h2>Log in as an owner or as an agency.</h2>
            <p>
                Owners manage their own restaurants. Agencies manage many. Each one has a dedicated
                interface, but they share the same menu infrastructure under the hood.
            </p>
        </div>

        <div class="role-cards">
            <!-- OWNER -->
            <a class="role-card" href="https://manage.maxmenu.com/login">
                <div class="role-card-text">
                    <h2>Owner</h2>
                    <p>Manage your restaurants, connect billing and keep full control over your menu.</p>
                </div>
                <img src="img/arrow-r.png" alt="" class="role-card-icon">
            </a>

            <!-- AGENCY -->
            <a class="role-card" href="https://partner.maxmenu.com/login">
                <div class="role-card-text">
                    <h2>Agency</h2>
                    <p>Create and manage restaurants for client.</p>
                </div>
                <img src="img/arrow-r.png" alt="" class="role-card-icon">
            </a>
        </div>

    </div>
</div>
<!-- ========================= -->
<!-- CTA FINAL                 -->
<!-- ========================= -->
<div class="section final-cta">
    <div class="section-inner">
        <h2>Turn your agency into recurring menu infrastructure.</h2>
        <p style="max-width:580px;margin:12px auto 32px;">
            Create development restaurants, design premium menus, transfer ownership and earn a
            recurring 0.5% on every item payment your clients process. All from a single partner panel.
        </p>
        <button>Join MaxMenu as an agency</button>
    </div>
</div>

<?php include 'templates/footer.php'; ?>

</body>
</html>