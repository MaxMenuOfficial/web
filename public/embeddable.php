<!DOCTYPE html>
<html lang="en" data-theme="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>MaxMenu — Embeddable Menu Control</title>
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

/* 🔥 Bloqueo global de scroll horizontal */
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
/* MEDIA FRAME (para posibles imágenes/vídeo) */
/* ========================================= */

.media-frame {
    border-radius: 18px;
    padding: 1px;
    background: radial-gradient(circle at top left, rgba(180,99,255,0.25), transparent 55%),
                radial-gradient(circle at bottom right, rgba(79,209,197,0.20), transparent 55%);
    border: 1px solid rgba(255,255,255,0.06);
    display:flex;
    justify-content:center;
    align-items:center;
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
/* HERO EMBEDDABLE                           */
/* ========================================= */

.hero {
    text-align: center;
    padding-top: 160px;
    padding-bottom: 160px;
}

.hero-title {
    font-size: clamp(2.8rem, 5vw, 4rem);
    max-width: 900px;
    margin: auto;
}

.hero-sub {
    font-size: 1.2rem;
    margin-top: 25px;
    max-width: 800px;
    margin-left: auto;
    margin-right: auto;
}

.hero-cta {
    margin-top: 40px;
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

/* Hero layout: copy + embed snippet visual */
.hero-embed-layout {
    margin-top: 80px;
    display: grid;
    grid-template-columns: minmax(0, 1.2fr) minmax(0, 1.1fr);
    gap: 28px;
    align-items: stretch;
}

.hero-embed-copy {
    text-align: left;
}

.hero-embed-copy h3 {
    font-size: 1.3rem;
}

.embed-code-block {
    background: var(--color-panel);
    border-radius: var(--radius-soft);
    border: 1px solid var(--color-border);
    padding: 20px;
    font-family: "SF Mono", ui-monospace, Menlo, monospace;
    font-size: 0.85rem;
    white-space: pre;
    overflow-x: auto;
}

/* ========================================= */
/* STACK-AGNOSTIC SECTION                    */
/* ========================================= */

.stack-grid {
    display: grid;
    grid-template-columns: minmax(0, 1.7fr) minmax(0, 1.3fr);
    gap: 32px;
    align-items: stretch;
}

.stack-card {
    background: var(--color-panel);
    border-radius: var(--radius-soft);
    border: 1px solid var(--color-border);
    padding: 26px;
}

.stack-pills {
    display: flex;
    flex-wrap: wrap;
    gap: 10px;
    margin-top: 16px;
}

.stack-pill {
    padding: 6px 12px;
    border-radius: 999px;
    border: 1px solid var(--color-border);
    font-size: 0.8rem;
}

/* ========================================= */
/* REMOTE CONTROL SECTION                    */
/* ========================================= */

.remote-grid {
    display: grid;
    grid-template-columns: minmax(0, 1.7fr) minmax(0, 1.4fr);
    gap: 32px;
    align-items: stretch;
}

.remote-card {
    background: var(--color-panel);
    border-radius: var(--radius-soft);
    border: 1px solid var(--color-border);
    padding: 26px;
}

.remote-list {
    margin-top: 18px;
    display: grid;
    gap: 10px;
}

.remote-item {
    background: var(--color-panel-2);
    border-radius: 10px;
    border: 1px solid var(--color-border);
    padding: 10px 14px;
    font-size: 0.9rem;
}

/* ========================================= */
/* VERSIONING / SPEED SECTION                */
/* ========================================= */

.version-grid {
    display: grid;
    grid-template-columns: minmax(0, 1.7fr) minmax(0, 1.4fr);
    gap: 32px;
    align-items: stretch;
}

.version-card {
    background: var(--color-panel);
    border-radius: var(--radius-soft);
    border: 1px solid var(--color-border);
    padding: 26px;
}

.version-badge-row {
    display: flex;
    flex-wrap: wrap;
    gap: 10px;
    margin-top: 18px;
}

.version-badge {
    padding: 6px 11px;
    border-radius: 999px;
    border: 1px solid var(--color-border);
    font-size: 0.8rem;
}

/* ========================================= */
/* DOMAIN CONTROL / SECURITY SECTION         */
/* ========================================= */

.domain-grid {
    display: grid;
    grid-template-columns: minmax(0, 1.7fr) minmax(0, 1.4fr);
    gap: 32px;
    align-items: stretch;
}

.domain-card {
    background: var(--color-panel);
    border-radius: var(--radius-soft);
    border: 1px solid var(--color-border);
    padding: 26px;
}

.domain-list {
    margin-top: 16px;
    font-family: "SF Mono", ui-monospace, Menlo, monospace;
    font-size: 0.85rem;
    line-height: 1.7;
    background: var(--color-panel-2);
    border-radius: 10px;
    border: 1px solid var(--color-border);
    padding: 14px;
}

/* ========================================= */
/* FINAL SECTION / CTA                       */
/* ========================================= */

.final-cta {
    text-align: center;
    padding: 160px 32px;
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
    .hero-embed-layout,
    .stack-grid,
    .remote-grid,
    .version-grid,
    .domain-grid,
    .widget-grid {
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


/* ========================================= */
/* WIDGET INTEGRATION SECTION                */
/* ========================================= */

.widget-grid {
    display: grid;
    grid-template-columns: minmax(0, 1.7fr) minmax(0, 1.5fr);
    gap: 32px;
}

.code-block {
    background: var(--color-panel);
    border-radius: var(--radius-soft);
    border: 1px solid var(--color-border);
    padding: 20px;
    font-family: "SF Mono", ui-monospace, Menlo, monospace;
    font-size: 0.85rem;
    overflow-x: auto;
    white-space: pre;
}

.widget-preview {
    background: var(--color-panel);
    border-radius: var(--radius-strong);
    border: 1px solid var(--color-border);
    padding: 22px;
    display: grid;
    grid-template-rows: auto 1fr;
    gap: 14px;
}

.widget-preview-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.widget-preview-dot-group {
    display: flex;
    gap: 6px;
}

.widget-dot {
    width: 8px;
    height: 8px;
    border-radius: 999px;
    background: rgba(255,255,255,0.3);
}
[data-theme="light"] .widget-dot {
    background: rgba(0,0,0,0.35);
}

.widget-preview-body {
    background: var(--color-panel-2);
    border-radius: 12px;
    padding: 16px;
    display: grid;
    grid-template-columns: minmax(0, 1.1fr) minmax(0, 1.2fr);
    gap: 12px;
}

.widget-col {
    display: grid;
    gap: 8px;
}

.widget-line {
    height: 26px;
    border-radius: 10px;
    background: rgba(255,255,255,0.04);
}
[data-theme="light"] .widget-line {
    background: rgba(0,0,0,0.04);
}
</style>

<style>
.remote-list {
    margin-top: 18px;
    display: grid;
    gap: 10px;
}

.remote-item {
    background: var(--color-panel-2);
    border-radius: 10px;
    border: 1px solid var(--color-border);
    padding: 10px 14px;
    font-size: 0.9rem;
    display: flex;
    align-items: center;
    gap: 10px;
}

.remote-item img {
    width: 35px;
    height: 35px;
    flex-shrink: 0;
}

.remote-item span {
    display: inline-block;
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
<!-- HERO: EMBED ONCE, CONTROL FOREVER -->
<!-- ========================= -->
<div class="section hero">
    <div class="section-inner">

        <div class="section-tag">Embeddable</div>

        <h1 class="hero-title">
            Embed once.<br>
            Control everything from the panel.
        </h1>

        <p class="hero-sub">
            MaxMenu turns your menu into an embeddable surface you can control remotely.
            Colors, typography, sizes, borders and every visual detail are managed from the
            dashboard — without touching the code again.
        </p>

        <div class="hero-cta">
            <button>Generate your embed snippet</button>
        </div>

        <div class="hero-embed-layout">

            <div class="hero-embed-copy">
                <h3>A remote control for your menu, inside any website.</h3>
                <p>
                    You paste a single snippet into your website, and that’s it. From that moment on,
                    every change you make — colors, fonts, typography sizes, borders, allergens,
                    images — is pushed from the MaxMenu panel directly into the embedded menu.
                </p>
                <p>
                    No new deploys, no new plugins, no extra integration work. The embed behaves like
                    a remote-controlled interface: the code stays still, the design keeps evolving.
                </p>
            </div>

            <div style="color:#019353;"class="embed-code-block">
&lt;!-- MaxMenu embeddable menu --&gt;
&lt;script
  src="https://cdn.maxmenu.com/widget.js"
  data-restaurant="your-restaurant-id"
  async&gt;
&lt;/script&gt;

&lt;div id="maxmenu-menuContainer"&gt;&lt;/div&gt;
            </div>

        </div>

    </div>
</div>


<!-- ========================= -->
<!-- STACK-AGNOSTIC            -->
<!-- ========================= -->
<div class="section">
    <div class="section-inner">

        <div class="section-header">
            <div class="section-tag">Stack agnostic</div>
            <h2>Works in any stack. No negotiations with your tech.</h2>
            <p>
                MaxMenu doesn’t ask you to rebuild your website. It simply embeds itself: whether
                your stack is WordPress, Shopify, Webflow, custom React, Laravel, static HTML or
                something in-between, the menu behaves the same way.
            </p>
        </div>

        <div class="stack-grid">

            <div class="stack-card">
                <h3>Drop it anywhere</h3>
                <p>
                    As long as you can insert a small script tag and a container, MaxMenu can live
                    there. The rendering logic, the design system and the performance layer are all
                    managed on our side.
                </p>

           <div class="stack-pills">
    <!-- CMS / E-commerce grandes -->
    <div class="stack-pill">WordPress</div>
    <div class="stack-pill">WooCommerce</div>
    <div class="stack-pill">Shopify</div>
    <div class="stack-pill">Shopify Plus</div>
    <div class="stack-pill">Webflow</div>
    <div class="stack-pill">Wix</div>
    <div class="stack-pill">Squarespace</div>
    <div class="stack-pill">Framer</div>
    <div class="stack-pill">Ghost</div>
    <div class="stack-pill">Drupal</div>
    <div class="stack-pill">Joomla</div>
    <div class="stack-pill">Magento</div>
    <div class="stack-pill">PrestaShop</div>
    <div class="stack-pill">BigCommerce</div>
    <div class="stack-pill">OpenCart</div>
    <div class="stack-pill">HubSpot CMS</div>
    <div class="stack-pill">Craft CMS</div>
    <div class="stack-pill">TYPO3</div>

    <!-- No-code / low-code donde hay bloque de código -->
    <div class="stack-pill">Carrd</div>
    <div class="stack-pill">Tilda</div>
    <div class="stack-pill">Bubble</div>
    <div class="stack-pill">ClickFunnels</div>
    <div class="stack-pill">Leadpages</div>
    <div class="stack-pill">Dorik</div>

    <!-- Frameworks / apps propias (siempre permiten script) -->
    <div class="stack-pill">Custom React</div>
    <div class="stack-pill">Next.js</div>
    <div class="stack-pill">Remix</div>
    <div class="stack-pill">Gatsby</div>
    <div class="stack-pill">Vue / Nuxt</div>
    <div class="stack-pill">Svelte / SvelteKit</div>
    <div class="stack-pill">Angular</div>
    <div class="stack-pill">Astro</div>

    <!-- Estáticos / JAMstack -->
    <div class="stack-pill">Static HTML</div>
    <div class="stack-pill">Eleventy</div>
    <div class="stack-pill">Hugo</div>
    <div class="stack-pill">Jekyll</div>

    <!-- Genérico -->
    <div class="stack-pill">Headless CMS + frontend</div>
    <div class="stack-pill">Any custom stack</div>
</div>
                <p style="margin-top:16px;">
                    The website keeps its own codebase, hosting and deployment process. MaxMenu
                    focuses solely on the menu surface, so you don’t have to refactor your stack
                    just to get a better menu.
                </p>
            </div>

            <div class="media-frame">
              <img src="img/stack-3.png" style="width:400px;" alt="">
            </div>


        </div>

    </div>
</div>


<!-- ========================= -->
<!-- REMOTE CONTROL PANEL      -->
<!-- ========================= -->
<div class="section">
    <div class="section-inner">

        <div class="section-header">
            <div class="section-tag">Remote control</div>
            <h2>The menu feels native to your brand, but you never touch the code.</h2>
            <p>
                Every detail you see in the embedded menu — from colors and typography to borders,
                spacing, icons, allergens and transparent images — is configured from a single
                control panel. The page only needs to be integrated once.
            </p>
        </div>

        <div class="remote-grid">

            <div class="remote-card">
                <h3>Design once, update in seconds</h3>
                <p>
                    You can change themes, fonts, weights, text sizes, corner radius, border
                    thickness, layout density and more, directly from the MaxMenu interface. The
                    changes propagate automatically to all embeds for that restaurant.
                </p>

             
             <div class="remote-list">
                    <div class="remote-item">
                        <img src="img/color.svg" alt="Colors">
                        <span>Colors: backgrounds, panels, accents and neutrals.</span>
                    </div>

                    <div class="remote-item">
                        <img src="img/typography.svg" alt="Typography">
                        <span>Typography: families, weights and sizes for titles, descriptions and prices.</span>
                    </div>

                    <div class="remote-item">
                        <img src="img/border.svg" alt="Borders & radius">
                        <span>Borders & radius: card edges, separators and outlines.</span>
                    </div>

                    <div class="remote-item">
                        <img src="img/space.svg" alt="Spacing">
                        <span>Spacing: vertical rhythm between categories, items and sections.</span>
                    </div>

                    <div class="remote-item">
                        <img src="img/circle.svg" alt="Allergens & icons">
                        <span>Allergens & icons: labels, badges and iconography.</span>
                    </div>

                    <div class="remote-item">
                        <img src="img/image.svg" alt="Images">
                        <span>Images: full-bleed or transparent dishes over neutral surfaces.</span>
                    </div>
                </div>
                <p style="margin-top:16px;">
                    Once the code is in place, you never ask a developer to “change the menu layout”
                    again. The control lives fully in the panel.
                </p>
            </div>

               <div class="media-frame">
                    <img src="img/widget.png" style="width:500px; border-radius:20p;"  alt="">
                </div>


          

        </div>

    </div>
</div>

<!-- ========================= -->
<!-- VERSIONING & SPEED        -->
<!-- ========================= -->
<div class="section">
    <div class="section-inner">

        <div class="section-header">
            <div class="section-tag">Speed & updates</div>
            <h2>Versioned delivery: cached by default, refreshed only when needed.</h2>
            <p>
                MaxMenu serves your menu from a cached version that only changes when you change it.
                Every update in the panel creates a new internal version; we invalidate the old one
                remotely and your site starts serving the new snapshot – without you touching any code.
            </p>
        </div>

        <div class="version-grid">

            <div class="version-card">
                <h3>Always fast, even while you keep editing</h3>
                <p>
                    Prices, dishes, languages, allergens and design tweaks are converted into lightweight
                    snapshots. The embed simply asks: “What version should I show?” If the version is the
                    same, it serves the cached menu. If it has changed, it refreshes once and goes back
                    to cache.
                </p>

                <div class="version-badge-row">
                    <div class="version-badge">Change in panel: new version created</div>
                    <div class="version-badge">First load after change: verify &amp; refresh</div>
                    <div class="version-badge">Next loads: served from cache, no DB reads</div>
                </div>

                <p style="margin-top:16px;">
                    For restaurants and agencies, this means the menu feels native-fast inside your site,
                    while the database is only touched when something really changes — not on every visit.
                </p>
            </div>

        </div>

    </div>
</div>

<!-- ========================= -->
<!-- DOMAIN CONTROL & SECURITY -->
<!-- ========================= -->
<div class="section">
    <div class="section-inner">

        <div class="section-header">
            <div class="section-tag">Domain control</div>
            <h2>Only authorised domains can display your menu.</h2>
            <p>
                The embed snippet is public, but who can actually show the menu is not. MaxMenu
                validates the domain of the page before rendering anything, so only authorised
                websites can use your menu.
            </p>
        </div>

        <div class="domain-grid">

            <div class="domain-card">
                <h3>Controlled distribution of your menu</h3>
                <p>
                    In the panel, you decide which domains are allowed to embed your menu: your main
                    website, a landing page, a microsite for an event, or an agency preview domain.
                    If a domain is not on the list, the menu simply doesn’t load.
                </p>

                <div class="domain-list">
                    allowed-domains:<br>
                    &nbsp;&nbsp;• restaurant.com<br>
                    &nbsp;&nbsp;• order.restaurant.com<br>
                    &nbsp;&nbsp;• agency-preview.com<br>
                    &nbsp;&nbsp;• custom-landing.com
                </div>

                <p style="margin-top:16px;">
                    This prevents unauthorised copies of your menu from being embedded on random
                    sites, and keeps the brand experience and analytics under your control.
                </p>
            </div>


              <div class="widget-preview">
                <div class="widget-preview-header">
                    <div class="widget-preview-dot-group">
                        <div style="background-color:#c52b00" class="widget-dot"></div>
                        <div style="background-color:#ffe205;" class="widget-dot"></div>
                        <div style="background-color:#27d41a" class="widget-dot"></div>
                    </div>
                    <span style="font-size:0.8rem; opacity:0.8;">Restaurant website · Embedded menu</span>
                </div>

                <div class="media-frame">
                    <img src="img/domain.png" style="width:500px; border-radius:20p;"  alt="">
                </div>

            </div>

           
        </div>

    </div>
</div>


<!-- ========================= -->
<!-- FINAL CTA                 -->
<!-- ========================= -->
<div class="section final-cta">
    <div class="section-inner">
        <h2>Embed once, and evolve the menu forever.</h2>
        <p style="max-width:580px;margin:12px auto 32px;">
            Add the MaxMenu snippet to your website once, and move all future changes into the
            dashboard. The code stays the same. The experience keeps getting better.
        </p>

        <button>Create your embeddable menu</button>
    </div>
</div>

<?php include 'templates/footer.php'; ?>
</body>
</html>