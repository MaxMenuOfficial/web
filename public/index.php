<!DOCTYPE html>
<html lang="en" data-theme="dark">
<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MaxMenu — Invisible Menu Infrastructure</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@600;700&family=DM+Sans:wght@400;500;600&family=Inter:wght@400;500&family=Space+Grotesk:wght@500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="styles/footer.css">
<style>
/* ========================================= */
/* ROOT VARIABLES — ESTILO SOBRIO            */
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

/* 🔥 Nuevo: bloquear overflow horizontal global */
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

/* 🔥 Nuevo: imágenes de contenido responsivas, sin desbordar */
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
/* MEDIA FRAME (IMÁGENES + VÍDEOS)           */
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

.media-inner video {
    background: #000;
}

/* ========================================= */
/* HERO SECTION                              */
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

/* HERO MOCKUP: AHORA ES UN FRAME DE VIDEO + UI */
.hero-mockup {
    margin-top: 80px;
    display: grid;
    grid-template-columns: minmax(0, 1.1fr) minmax(0, 0.9fr);
    gap: 24px;
    align-items: stretch;
}

.hero-side-copy {
    text-align: left;
}

.hero-side-copy h3 {
    font-size: 1.3rem;
}

.hero-side-copy p {
    margin-top: 10px;
}

/* ========================================= */
/* GRID SECTION (CARDS SOBRIAS)              */
/* ========================================= */

.cards-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(260px, 1fr));
    gap: 32px;
    margin-top: 60px;
}

.card {
    background: var(--color-panel);
    border: 1px solid var(--color-border);
    padding: 32px;
    border-radius: var(--radius-soft);
}

.card h3 {
    font-size: 1.3rem;
}

.card p {
    margin-top: 10px;
}

/* ========================================= */
/* DESIGN SYSTEM SECTION (COLOURS, FONTS)    */
/* ========================================= */

.design-grid {
    display: grid;
    grid-template-columns: minmax(0, 2fr) minmax(0, 1.6fr);
    gap: 32px;
}

.design-card {
    background: var(--color-panel);
    border-radius: var(--radius-soft);
    border: 1px solid var(--color-border);
    padding: 26px;
}

.palette-row {
    display: flex;
    gap: 14px;
    margin-top: 22px;
}

.palette-color {
    flex: 1;
    height: 52px;
    border-radius: 999px;
    border: 1px solid rgba(255,255,255,0.06);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 0.8rem;
    opacity: 0.9;
}

[data-theme="light"] .palette-color {
    border: 1px solid rgba(0,0,0,0.06);
}

.palette-color--bg    { background: #000; color: #fff; }
.palette-color--panel { background: #111; color: #fff; }
.palette-color--panel2{ background: #181818; color: #fff; }
.palette-color--accent{ background: #b463ff; color: #fff; }

[data-theme="light"] .palette-color--bg {
    background: #ffffff; color:#000;
}
[data-theme="light"] .palette-color--panel {
    background: #f7f7f7; color:#000;
}
[data-theme="light"] .palette-color--panel2 {
    background: #ededed; color:#000;
}

.font-row {
    margin-top: 18px;
    padding: 16px 18px;
    border-radius: var(--radius-soft);
    border: 1px solid var(--color-border);
    background: var(--color-panel-2);
}

.font-row small {
    display: block;
    font-size: 0.75rem;
    opacity: 0.7;
    margin-bottom: 4px;
}

.font-row span {
    display: block;
    font-size: 1.1rem;
}

/* ========================================= */
/* BORDERS & LAYOUT                          */
/* ========================================= */

.border-grid {
    display: grid;
    grid-template-columns: repeat(3, minmax(0, 1fr));
    gap: 18px;
    margin-top: 18px;
}

.border-box {
    background: var(--color-panel-2);
    border-radius: 8px;
    border: 1px solid var(--color-border);
    height: 64px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 0.9rem;
}

/* ========================================= */
/* ALLERGENS SECTION                         */
/* ========================================= */

.allergens-layout {
    display: grid;
    grid-template-columns: minmax(0, 1.7fr) minmax(0, 2fr);
    gap: 40px;
    align-items: stretch;
}

.allergens-panel {
    background: var(--color-panel);
    border-radius: var(--radius-strong);
    border: 1px solid var(--color-border);
    padding: 26px;
}

.badge-grid {
    display: flex;
    flex-wrap: wrap;
    gap: 10px;
    margin-top: 18px;
}

.badge {
    padding: 6px 12px;
    border-radius: 999px;
    border: 1px solid var(--color-border);
    font-size: 0.8rem;
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

/* ========================================= */
/* TECH SECTION                              */
/* ========================================= */

.tech-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(260px, 1fr));
    gap: 26px;
    margin-top: 40px;
}

.tech-card {
    background: var(--color-panel);
    border-radius: var(--radius-soft);
    border: 1px solid var(--color-border);
    padding: 22px;
}

.tech-label {
    font-size: 0.8rem;
    opacity: 0.8;
    text-transform: uppercase;
    letter-spacing: 0.08em;
    margin-bottom: 6px;
}

/* ========================================= */
/* CTA FINAL                                 */
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
    .hero-mockup {
        grid-template-columns: 1fr;
    }
    .design-grid,
    .widget-grid,
    .allergens-layout {
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
/* PLATFORMS SECTION                         */
/* ========================================= */

.platforms-layout {
    display: grid;
    grid-template-columns: minmax(0, 1.6fr) minmax(0, 1.4fr);
    gap: 32px;
    align-items: stretch;
}

.platforms-card {
    background: var(--color-panel);
    border-radius: var(--radius-soft);
    border: 1px solid var(--color-border);
    padding: 26px;
}

.platforms-logos {
    display: flex;
    flex-wrap: wrap;
    gap: 12px;
    margin-top: 18px;
}

.platform-pill {
    padding: 8px 14px;
    border-radius: 999px;
    border: 1px solid var(--color-border);
    font-size: 0.85rem;
    opacity: 0.9;
}

/* ========================================= */
/* TRANSLATIONS SECTION                      */
/* ========================================= */

.translation-layout {
    display: grid;
    grid-template-columns: minmax(0, 1.7fr) minmax(0, 1.5fr);
    gap: 32px;
    align-items: stretch;
}

.translation-card {
    background: var(--color-panel);
    border-radius: var(--radius-soft);
    border: 1px solid var(--color-border);
    padding: 26px;
}

.language-badges {
    display: flex;
    flex-wrap: wrap;
    gap: 10px;
    margin-top: 16px;
}

.language-badge {
    padding: 6px 12px;
    border-radius: 999px;
    border: 1px solid var(--color-border);
    font-size: 0.8rem;
}

/* ========================================= */
/* MENU STRUCTURE SECTION                    */
/* ========================================= */

.structure-grid {
    display: grid;
    grid-template-columns: minmax(0, 1.8fr) minmax(0, 1.4fr);
    gap: 32px;
}

.structure-card {
    background: var(--color-panel);
    border-radius: var(--radius-soft);
    border: 1px solid var(--color-border);
    padding: 26px;
}

.structure-tree {
    background: var(--color-panel-2);
    border-radius: var(--radius-soft);
    border: 1px solid var(--color-border);
    padding: 18px;
    font-size: 0.9rem;
    line-height: 1.7;
    font-family: "SF Mono", ui-monospace, Menlo, monospace;
}

.structure-tree .level-0 { margin-left: 0; }
.structure-tree .level-1 { margin-left: 14px; }
.structure-tree .level-2 { margin-left: 28px; }
.structure-tree .level-3 { margin-left: 42px; }

/* ========================================= */
/* IMAGES SECTION (TRANSPARENT)              */
/* ========================================= */

.images-layout {
    display: grid;
    grid-template-columns: minmax(0, 1.6fr) minmax(0, 1.4fr);
    gap: 32px;
    align-items: stretch;
}

.images-card {
    background: var(--color-panel);
    border-radius: var(--radius-soft);
    border: 1px solid var(--color-border);
    padding: 26px;
}

.images-notes {
    margin-top: 16px;
    font-size: 0.85rem;
    opacity: 0.85;
}

/* responsive para estas secciones */
@media (max-width: 960px) {
    .platforms-layout,
    .translation-layout,
    .structure-grid,
    .images-layout {
        grid-template-columns: 1fr;
    }
}
</style>
    <!-- Solo sincroniza el tema guardado; el toggle vive en el header -->
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
<!-- HERO SECTION              -->
<!-- ========================= -->
<div class="section hero">
    <div class="section-inner">

        <div class="section-tag">MaxMenu · Menu Infrastructure</div>

        <h1 class="hero-title">
            The invisible infrastructure<br>
            for restaurant menus.
        </h1>

        <p class="hero-sub">
       Over 1 billion possible combinations. High-quality menu design, ultra-fast delivery, and agency-ready ownership transfer. MaxMenu turns every restaurant menu into a large-scale, integrated interface.
        </p>

        <div class="hero-cta">
            <a href="login.php">
            <button>Get started in minutes</button>
            </a>
        </div>

        <!-- HERO MOCKUP: VIDEO + COPY -->
        <div class="hero-mockup" >

            <!-- Frame de vídeo / demo -->
            <div class="" style="backgroud-color:transparent;">
               
                     <img src="img/bg1.png" style="width: 500px;">
             
            </div>

            <!-- Copy al lado del vídeo -->
            <div class="hero-side-copy">
               <h3>From static PDFs to a live menu surface.</h3>
                    <p>
                        Most restaurant menus still live as PDFs, images or printed cards that are hard to update
                        and impossible to personalise. MaxMenu turns that static file into a live surface that
                        understands colors, typography, borders, allergens, languages and layout.
                    </p>
                    <p>
                        Every screen you see here is driven by the same control panel: change a font, a border
                        radius, a color palette or a language, and every menu updates instantly across devices
                        and locations. You stop sending files, and start operating a real piece of infrastructure
                        that can be embedded into any website.
                    </p>
            </div>
        </div>

    </div>
</div>


<!-- ========================= -->
<!-- CORE VALUE CARDS          -->
<!-- ========================= -->
<div class="section">
    <div class="section-inner">

        <div class="section-header">
            <div class="section-tag">Why MaxMenu</div>
                <h2>Built like a menu system, not like a one-off template.</h2>
                <p>
                    MaxMenu doesn’t treat your menu as a static page or a PDF. It treats it as a structured
                    model: categories, subcategories, items, prices, allergens, languages and styles that stay
                    perfectly coherent across every device and location. You design once, and the system keeps
                    everything in sync, fast and predictable, wherever the menu is displayed.
                </p>
        </div>

        <div class="cards-grid">
          <div class="card">
            <h3>Quantum Delivery</h3>
            <p>
                Quantum Delivery comes into play when you embed the widget in your website. 
                You paste the snippet once, never touch the code again, and every change you
                make from the MaxMenu panel is pushed remotely to the menu, keeping it fast,
                coherent and globally updated.
            </p>
        </div>

            <div class="card">
                <h3>Design-grade controls</h3>
                <p>Colors, typography, borders, spacing, iconography, allergens: every visual parameter
                   is configurable, reproducible and safe.</p>
            </div>

            <div class="card">
                <h3>Ownership transfer</h3>
                <p>Agencies create “development restaurants” and hand off ownership in a single step.
                   The client keeps legal & fiscal control, the partner keeps collaboration access.</p>
            </div>

           <div class="card">
                <h3>Create for free, pay only when you publish</h3>
                <p>
                    You can design the entire menu in MaxMenu without paying anything: categories, items,
                    images, allergens, languages, colors, borders and typography. All of it lives in your
                    private area and you can preview it as many times as you want.
                </p>
                <p>
                    You only start paying when you decide to publish the menu for your guests — either on
                    a public MaxMenu URL or embedded as a widget in your own website.
                </p>
            </div>
            
        </div>

    </div>
</div>


<!-- ========================= -->
<!-- DESIGN SYSTEM: COLORS & FONTS + IMAGEN -->
<!-- ========================= -->
<div class="section">
    <div class="section-inner">

        <div class="section-header">
            <div class="section-tag">Design System</div>
            <h2>Every restaurant gets a design-grade menu system.</h2>
            <p>
                MaxMenu doesn’t just render text and prices. It exposes a full design system:
                color palettes, typography scales and border presets that survive redesigns,
                rebrands and full agency takeovers.
            </p>
        </div>

        <div class="design-grid">
            <!-- Colores + tipografías -->
            <div>
                <div class="design-card">
                    <h3>Color layers</h3>
                    <p>
                        Each menu uses layered neutrals: base background, panel and elevated surfaces.
                        In dark or light mode, the contrast ratio and hierarchy are preserved automatically.
                    </p>

                    <div class="palette-row">
                        <div class="palette-color palette-color--bg">Background</div>
                        <div class="palette-color palette-color--panel">Panel</div>
                        <div class="palette-color palette-color--panel2">Raised</div>
                        <div class="palette-color palette-color--accent">Accent</div>
                    </div>
                </div>

                <div class="design-card" style="margin-top:24px;">

                   <h3>Typography system</h3>
<p>
    Titles, descriptions, prices, categories and subcategories don’t have to share the same
    font. In MaxMenu you can assign a different family, size and weight to each layer, so the
    hierarchy of the menu feels intentional, not accidental.
</p>

<div class="font-grid">
    <div class="font-row font-row--category">
        <small>Category / section</small>
        <span>PASTA · RISOTTO · SEASONAL</span>
    </div>

    <div class="font-row font-row--item">
        <small>Item title</small>
        <span>Rigatoni alla Norma</span>
    </div>

    <div class="font-row font-row--description">
        <small>Description</small>
        <span>Fresh tomato sauce, fried eggplant, ricotta salata and basil.</span>
    </div>

    <div class="font-row font-row--price">
        <small>Price line</small>
        <span>€18.00 · €23.00 · €29.00</span>
    </div>

    <div class="font-row font-row--note">
        <small>Chef’s note / highlight</small>
        <span>“Available gluten free and vegan on request.”</span>
    </div>
</div>
                    </div>
          
            </div>

            <!-- Imagen de preview del panel de diseño -->
            <div class="">
                <img src="img/web-color.png" style="width: 300px;">
            </div>
        </div>

        <!-- Bordes -->
        <div class="design-card" style="margin-top:32px;">
            <h3>Borders, radius & density</h3>
            <p>
                Menus can feel editorial, brutalist or ultra-minimal just by changing border weight,
                radius and vertical rhythm—without touching a single line of CSS.
            </p>

            <div class="border-grid">
                <div class="border-box"><img src="img/cuadrado.svg" style="width:40px;" alt=""></div>
                <div class="border-box"><img src="img/semi.svg" style="width:40px;" alt=""></div>
                <div class="border-box"><img src="img/redondo.svg" style="width:40px;" alt=""></div>
            </div>
        </div>

    </div>
</div>


<!-- ========================= -->
<!-- ALLERGENS & LABELS        -->
<!-- ========================= -->
<div class="section">
    <div class="section-inner">

        <div class="section-header">
            <div class="section-tag">Allergens & Labels</div>
            <h2>Allergen-aware by design, not as an afterthought.</h2>
            <p>
                Icons, labels and badges for allergens and dietary constraints are built into the
                data model and the design system. Restaurants can stay compliant and clear without
                turning their menus into unreadable walls of symbols.
            </p>
        </div>

        <div class="allergens-layout">

            <div class="allergens-panel">
                <h3>Structured allergen layer</h3>
                <p>
                    Each item can map to a consistent set of allergens and dietary tags. Translations
                    and iconography are handled centrally, and rendered contextually in every language.
                </p>

                <div class="badge-grid">
                    <div class="badge">Lupins</div>
                    <div class="badge">Celery</div>
                    <div class="badge">Peanuts</div>
                    <div class="badge">Crustaceans</div>
                    <div class="badge">Tree nuts</div>

                    <div class="badge">Gluten</div>
                    <div class="badge">Eggs</div>
                    <div class="badge">Dairy</div>
                    <div class="badge">Molluscs</div>
                    <div class="badge">Mustard</div>

                    <div class="badge">Fish</div>
                    <div class="badge">Sesame</div>
                    <div class="badge">Soy</div>
                    <div class="badge">Sulphites</div>
                </div>
            </div>

            <!-- Imagen/preview de ítems con iconos de alérgenos -->
            <div class="media-frame">
                <img src="img/alergen.png" style="width: 300px;">
            </div>

        </div>

    </div>
</div>


<!-- ========================= -->
<!-- WIDGET INTEGRATION        -->
<!-- ========================= -->
<div class="section">
    <div class="section-inner">

        <div class="section-header">
            <div class="section-tag">Widget Integration</div>
            <h2>A single snippet turns any website into a living menu surface.</h2>
            <p>
                MaxMenu is dropped into existing websites as a small, versioned widget script.
                Every style choice you make — colors, fonts, borders, allergens — travels with it,
                without asking developers to rebuild anything.
            </p>
        </div>

        <div class="widget-grid">

            <div>
                <div class="code-block" style="color:#9474FF;">
&lt;!-- MaxMenu Quantum Widget --&gt;
&lt;script
  src="https://menu.maxmenu.com/widget.js"
  data-restaurant="your-restaurant-id"
  async&gt;
&lt;/script&gt;

&lt;div id="maxmenu-menuContainer"&gt;&lt;/div&gt;
                </div>

                <p style="margin-top:18px;">
                  
                    Edge caching does the heavy lifting; your infrastructure stays quiet.
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

                <div class="widget-preview-body">
                    <div class="widget-col">
                        <div class="widget-line"></div>
                        <div class="widget-line"></div>
                        <div class="widget-line"></div>
                    </div>
                    <div class="widget-col">
                        <div class="widget-line"></div>
                        <div class="widget-line"></div>
                        <div class="widget-line"></div>
                    </div>
                </div>
            </div>

        </div>

    </div>
</div>

<!-- ========================= -->
<!-- RELIABILITY / OPERATIONS  -->
<!-- ========================= -->
<div class="section">
    <div class="section-inner">

        <div class="section-header">
            <div class="section-tag">Reliability</div>
            <h2>Works every day</h2>
            <p>
                MaxMenu is built so you can keep changing it
                without breaking the experience.
            </p>
        </div>

        <div class="tech-grid">
            <div class="tech-card">
                <div class="tech-label">Uptime & speed</div>
                <h3>Always ready when guests arrive</h3>
                <p>
                    No PDFs to download and no “we’re updating the menu” screens — just a fast,
                    readable menu, even on busy nights.
                </p>
            </div>

            <div class="tech-card">
                <div class="tech-label">Safe editing</div>
                <h3>Change today, publish in one click</h3>
                <p>
                    Prepare new prices or dishes in advance and publish when you’re ready, so the
                    menu never appears half-edited mid-service.
                </p>
            </div>

            <div class="tech-card">
                <div class="tech-label">Chains & access</div>
                <h3>Made for groups and teams</h3>
                <p>
                    Reuse the same structure across locations, keep local differences, and give
                    each role the right level of access — owners, staff and agencies.
                </p>
            </div>
        </div>

    </div>
</div>


<!-- ========================= -->
<!-- DELIVERY LINK HUB         -->
<!-- ========================= -->
<div class="section">
    <div class="section-inner">

        <div class="section-header">
            <div class="section-tag">Delivery links</div>
            <h2>From your online menu to “Order now” in one tap.</h2>
            <p>
                When someone checks your menu from home — through your website, a story, a link in bio
                or a QR — MaxMenu becomes the place where they discover that you also deliver. From that
                same menu they can jump straight to your profile on each delivery platform.
            </p>
        </div>

        <div class="platforms-layout">

            <div class="platforms-card">
                <h3>One menu, clear paths to order</h3>
                <p>
                    Guests browse your MaxMenu as usual. As soon as they decide to order from home,
                    they tap “Order on Glovo”, “Order on Uber Eats”, “Order on Just Eat” or any
                    other platform you connect — and land directly in your restaurant page, ready
                    to add items to the cart.
                </p>

                <p style="margin-top:12px;">
                    You manage these delivery links from the panel: add, change or remove platforms
                    whenever you need. The menu doesn’t change; the buttons guide customers to where
                    the actual purchase happens.
                </p>

                <div class="platforms-logos">
                    <div class="platform-pill">Glovo</div>
                    <div class="platform-pill">Uber Eats</div>
                    <div class="platform-pill">Just Eat</div>
                    <div class="platform-pill">Deliveroo</div>
                    <div class="platform-pill">Other delivery links</div>
                </div>
            </div>

            <div class="">
               <img src="img/platforms.png" style="width: 300px;"
                    alt="Menu with buttons linking to multiple delivery platforms">
            </div>

        </div>

    </div>
</div>

<!-- ========================= -->
<!-- AUTOMATIC TRANSLATIONS    -->
<!-- ========================= -->
<div class="section">
    <div class="section-inner">

        <div class="section-header">
            <div class="section-tag">Translations</div>
            <h2>Automatic translations with character limits under control.</h2>
            <p>
                MaxMenu translates your menu into multiple languages, but never blindly. Each
                translation is stored, versioned and counted against a clear character budget,
                so you always know what you’re spending.
            </p>
        </div>

        <div class="translation-layout">

            <div class="translation-card">
                <h3>Structured multilingual menus</h3>
                <p>
                    Items, descriptions, categories and allergens live in a structured model. When
                    you trigger automatic translations, MaxMenu writes them into the existing
                    structure and reuses them for every future update.
                </p>

              <div class="language-badges">
                    <div class="language-badge">English</div>
                    <div class="language-badge">Español</div>
                    <div class="language-badge">Français</div>
                    <div class="language-badge">Deutsch</div>
                    <div class="language-badge">Italiano</div>

                    <div class="language-badge">Português</div>
                    <div class="language-badge">Català</div>
                    <div class="language-badge">Euskara</div>
                    <div class="language-badge">Galego</div>
                    <div class="language-badge">Română</div>

                    <div class="language-badge">Nederlands</div>
                    <div class="language-badge">简体中文</div>
                    <div class="language-badge">العربية</div>
                    <div class="language-badge">日本語</div>
                    <div class="language-badge">Русский</div>

                    <div class="language-badge">한국어</div>
                    <div class="language-badge">Svenska</div>
                    <div class="language-badge">Dansk</div>
                    <div class="language-badge">Norsk Bokmål</div>
                    <div class="language-badge">Ελληνικά</div>

                    <div class="language-badge">Polski</div>
                    <div class="language-badge">Čeština</div>
                </div>

                <p style="margin-top:16px;">
                    Before translating, MaxMenu validates the remaining character quota so you
                    don’t trigger unnecessary costs. Once translated, the menu behaves as if it
                    had been written manually in each language.
                </p>
            </div>

            <div class="">
               <img src="img/language.png" style="width: 300px;">
            </div>

        </div>

    </div>
</div>

<!-- ========================= -->
<!-- MENU STRUCTURE            -->
<!-- ========================= -->
<div class="section">
    <div class="section-inner">

        <div class="section-header">
            <div class="section-tag">Menu structure</div>
            <h2>Brunch, daily menu, categories and subcategories under one structure.</h2>
            <p>
                MaxMenu models your menu with categories, subcategories and items. Brunch, tasting
                menus or “menú del día” are just structured variations of the same core system,
                not separate, fragile pages.
            </p>
        </div>

        <div class="structure-grid">

            <div class="structure-card">
                <h3>One schema, many menu types</h3>
                <p>
                    Whether you’re configuring the main à la carte menu, a Sunday brunch or a daily
                    lunch menu, everything relies on the same hierarchy. Categories group items,
                    subcategories refine them and daily menus reuse existing dishes.
                </p>

                <div class="structure-tree" style="margin-top:18px;">
                    <div class="level-0">Restaurant</div>
                    <div class="level-1">─ Main Menu</div>
                    <div class="level-2">├ Category: Starters</div>
                    <div class="level-3">└ Subcategory: Cold · Hot</div>
                    <div class="level-2">├ Category: Mains</div>
                    <div class="level-3">└ Subcategory: Pasta · Fish · Meat</div>
                    <div class="level-2">└ Category: Desserts</div>
                    <div class="level-1">─ Brunch</div>
                    <div class="level-2">└ Category: Eggs · Sweet · Drinks</div>
                    <div class="level-1">─ Daily Menu</div>
                    <div class="level-2">└ Category: Starter · Main · Dessert</div>
                </div>
            </div>

            <!-- Imagen de UI mostrando categorías/subcategorías -->
            <div class="media-frame">
             
                    <img src="img/subcategory.png" style="width: 380px; height:400px;"
                         alt="Category and subcategory structure for brunch and daily menu">
        
            </div>

        </div>

    </div>
</div>

<!-- ========================= -->
<!-- IMAGES WITH TRANSPARENT BG -->
<!-- ========================= -->
<div class="section">
    <div class="section-inner">

        <div class="section-header">
            <div class="section-tag">Imagery</div>
            <h2>Transparent images that feel native to your layout.</h2>
            <p>
                MaxMenu accepts images with transparent backgrounds so dishes can float above
                neutral surfaces. The widget respects that transparency in every theme and device.
            </p>
        </div>

        <div class="images-layout">

            <div class="images-card">
                <h3>PNG, WebP and brand-safe rendering</h3>
                <p>
                    Dishes can be uploaded as transparent PNG or WebP assets. The menu system
                    places them over the existing color layers without adding unwanted frames
                    or shadows, keeping the layout clean and editorial.
                </p>

                <p class="images-notes">
                   
                    Light and dark themes preserve the same composition, only changing the
                    underlying neutrals.
                </p>
            </div>

            <div class="">
              <img src="img/bg0.png" style="width: 380px;">
            </div>

        </div>

    </div>
</div>
<!-- ========================= -->
<!-- ON-SITE ORDERING / PAYMENTS (ROADMAP) -->
<!-- ========================= -->
<div class="section">
    <div class="section-inner">

        <div class="section-header">
            <div class="section-tag">On-site ordering · 2026</div>
            <h2>Take orders and payments directly on your website.</h2>
            <p>
                Starting in Q1–Q2 2026, MaxMenu will let guests order and pay from the same menu
                they are already viewing on your website — no extra systems, no separate flows.
            </p>
        </div>

        <div class="tech-grid">

            <div class="tech-card">
                <div class="tech-label">From menu to checkout</div>
                <h3>Orders directly from the menu</h3>
                <p>
                    Guests browsing your MaxMenu will be able to select dishes, send the order and
                    pay without leaving your site. The menu stops being a catalogue and becomes a
                    complete ordering interface.
                </p>
            </div>

            <div class="tech-card">
                <div class="tech-label">Dining room & staff</div>
                <h3>Less friction on the floor</h3>
                <p>
                    Fewer trips just to take orders or process card payments. Staff can focus on
                    service and presentation, while the menu handles item selection and checkout
                    with the same design quality you already see on screen.
                </p>
            </div>

            <div class="tech-card">
                <div class="tech-label">Pricing</div>
                <h3>Transparent transaction fees </h3>
                <p>
                    No setup fees or hidden surcharges. Each successful payment has a clear fee of
                    2.9% + 0.29 per transaction — and that includes cards like American Express.
                    You don’t pay extra for Amex: MaxMenu absorbs that additional cost so your
                    pricing stays simple and predictable.
                </p>
            </div>

           <div class="tech-card">
                <div class="tech-label">Fixed costs</div>
                <h3>Lower fixed pressure, same revenue</h3>
                <p>
                    By moving part of the ordering flow into the menu, you can handle more tables
                    with the same team — or avoid hiring extra staff just to take orders. The system
                    helps you keep service quality high while your fixed costs stay under control.
                </p>
            </div>

            <div class="tech-card">
                <div class="tech-label">Control</div>
                <h3>Turn table payments on and off</h3>
                <p>
                    You decide when guests can order and pay from the table: activate it on busy
                    nights, during staff shortages or specific services, and disable it when you
                    prefer fully guided service. It’s a switch in the panel, not a new project.
                </p>
            </div>

            <div class="tech-card">
                <div class="tech-label">New openings</div>
                <h3>Start lean, grow when you’re ready</h3>
                <p>
                    For new restaurants, MaxMenu lets you operate with a smaller team at the
                    beginning, taking orders and payments from the menu itself. As the business
                    grows and you hire more staff, you can keep the system active as a profitable,
                    beautiful layer on top of your service.
                </p>
            </div>

        </div>

    </div>
</div>
<!-- ========================= -->
<!-- CTA FINAL                 -->
<!-- ========================= -->
<div class="section final-cta">
    <div class="section-inner">
        <h2>Ready to turn menus into infrastructure?</h2>
        <p style="max-width:580px;margin:12px auto 32px;">
            Start with a single restaurant, or onboard your entire portfolio of clients.
            MaxMenu is built for the agencies, operators and founders that are tired of fragile stacks.
        </p>

        <button>Start with MaxMenu</button>
    </div>
</div>

<?php include 'templates/footer.php'; ?>

</body>
</html>