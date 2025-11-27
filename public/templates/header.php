<style>
    :root {
        --font-main: "Inter", -apple-system, BlinkMacSystemFont, sans-serif;

        --bg-dark: #000;
        --bg-light: #ffffff;

        --text-dark: #ffffff;
        --text-light: #000000;

        --header-height: 70px;
        --transition: 0.25s ease;

        /* Por defecto (modo oscuro) */
        --color-bg: var(--bg-dark);
        --color-text: var(--text-dark);
        --color-header-bg: var(--bg-dark);
        --color-header-text: var(--text-dark);
    }

    /* Light mode */
    [data-theme="light"] {
        --color-bg: var(--bg-light);
        --color-text: var(--text-light);
        --color-header-bg: var(--bg-light);
        --color-header-text: var(--text-light);
    }

    /* RESET PREMIUM */
    * { margin: 0; padding: 0; box-sizing: border-box; }
    body {
        font-family: var(--font-main);
        background: var(--color-bg);
        color: var(--color-text);
        transition: background var(--transition), color var(--transition);
        padding-top: var(--header-height);
    }

    body.mm-menu-open {
        overflow: hidden; /* bloquea scroll cuando el menú móvil está abierto */
    }

    /* ========================== */
    /* HEADER                     */
    /* ========================== */
    header.mm-header {
        position: fixed;
        top: 0; left: 0;
        width: 100%;
        height: var(--header-height);
        background: var(--color-header-bg);
        display: flex;
        justify-content: center;
        align-items: center;
        border-bottom: 1px solid rgba(255,255,255,0.06);
        transition: background var(--transition), border-color var(--transition);
        z-index: 10000;
    }

    [data-theme="light"] header.mm-header {
        border-bottom: 1px solid rgba(0,0,0,0.06);
    }

    .mm-header-inner {
        width: 100%;
        max-width: 1400px;
        padding: 0 32px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 24px;
    }

    .mm-logo {
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .mm-logo span {
        font-size: 1.7rem;
        font-weight: 700;
        letter-spacing: 0.4px;
    }

    /* ========================== */
    /* NAV DESKTOP                */
    /* ========================== */
    nav.mm-nav {
        display: flex;
        gap: 32px;
    }

    nav.mm-nav a {
        text-decoration: none;
        color: var(--color-header-text);
        opacity: 0.85;
        font-size: 0.95rem;
        letter-spacing: 0.03em;
        text-transform: uppercase;
        transition: opacity var(--transition);
    }
    nav.mm-nav a:hover { opacity: 1; }

    /* ========================== */
    /* THEME TOGGLE               */
    /* ========================== */
    .mm-theme-toggle {
        width: 50px;
        height: 26px;
        border: none;
        background: rgba(255,255,255,0.15);
        border-radius: 25px;
        cursor: pointer;
        position: relative;
        transition: background var(--transition);
    }

    [data-theme="light"] .mm-theme-toggle {
        background: rgba(0,0,0,0.12);
    }

    .toggle-circle {
        width: 20px;
        height: 20px;
        background: var(--color-header-text);
        border-radius: 50%;
        position: absolute;
        top: 3px;
        left: 4px;
        transition: transform var(--transition), background var(--transition);
    }
    [data-theme="light"] .toggle-circle {
        transform: translateX(24px);
    }

    /* ========================== */
    /* ACTIONS + HAMBURGER        */
    /* ========================== */
    .mm-actions {
        display: flex;
        align-items: center;
        gap: 12px;
    }

    
    /* ========================== */
    /* MOBILE MENU (OVERLAY FULL) */
    /* ========================== */
    .mm-mobile-menu {
        position: fixed;
        inset: 0;
        background: radial-gradient(circle at top, rgba(180,99,255,0.18), transparent 55%),
                    radial-gradient(circle at bottom, rgba(79,209,197,0.15), transparent 60%),
                    var(--color-bg);
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        padding: 96px 24px 32px;
        opacity: 0;
        pointer-events: none;
        transform: translateY(-8px);
        transition: opacity var(--transition), transform var(--transition), background var(--transition);
        z-index: 9999;
    }

    .mm-mobile-menu.is-open {
        opacity: 1;
        pointer-events: auto;
        transform: translateY(0);
    }

    .mm-mobile-menu-top {
        display: flex;
        flex-direction: column;
        gap: 20px;
    }

    .mm-mobile-menu a.mm-mobile-link {
        text-decoration: none;
        color: var(--color-text);
        font-size: 1.6rem;
        font-weight: 600;
        letter-spacing: 0.03em;
        text-transform: uppercase;
        opacity: 0.9;
    }

    .mm-mobile-menu a.mm-mobile-link span {
        display: block;
        font-size: 0.78rem;
        font-weight: 400;
        opacity: 0.7;
        text-transform: none;
        letter-spacing: 0;
        margin-top: 4px;
    }

    .mm-mobile-menu-bottom {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 16px;
        font-size: 0.9rem;
        opacity: 0.8;
    }

    .mm-mobile-login {
        text-decoration: none;
        color: var(--color-text);
        font-weight: 600;
    }

    .mm-mobile-meta {
        font-size: 0.78rem;
        opacity: 0.65;
        text-align: right;
    }

    /* ========================== */
    /* RESPONSIVE RULES           */
    /* ========================== */
    @media (max-width: 850px) {

        nav.mm-nav { display: none; }

        .mm-hamburger { display: flex; }

        .mm-theme-toggle {
            margin-left: 4px;
        }

        .mm-header-inner {
            padding: 0 18px;
        }
    }

/* ========================== */
/* HAMBURGER (GRID 2x2)       */
/* ========================== */

.mm-hamburger {
    display: none;                 /* se muestra solo en mobile con el @media */
    width: 32px;
    height: 32px;
    border-radius: 10px;
    padding: 4px;
    position: relative;
    cursor: pointer;
    background: rgba(255,255,255,0.06);
    transition: background var(--transition), transform var(--transition), box-shadow var(--transition);

    display: grid;
    grid-template-columns: repeat(2, 1fr);
    grid-template-rows: repeat(2, 1fr);
    gap: 3px;
    /* IMPORTANTE: dejamos ver la expansión */
    overflow: visible;
}

[data-theme="light"] .mm-hamburger {
    background: rgba(0,0,0,0.06);
}

/* Los 4 “tiles” del grid */
.mm-hamburger span {
    width: 100%;
    height: 100%;
    border-radius: 6px;
    background: var(--color-header-text);
    transition:
        transform 0.25s ease,
        border-radius 0.25s ease,
        background 0.25s ease;
}

/* Estado abierto: expansión hacia afuera */
.mm-hamburger.is-open {
    background: radial-gradient(circle at top left,
                    rgba(180,99,255,0.4), transparent 60%);
    box-shadow: 0 0 16px rgba(0,0,0,0.55);
    transform: scale(1.06);
}

/* TL */
.mm-hamburger.is-open span:nth-child(1) {
    transform: translate(-2px, -2px) scale(1.05);
    border-radius: 10px;
}
/* TR */
.mm-hamburger.is-open span:nth-child(2) {
    transform: translate(2px, -2px) scale(1.05);
    border-radius: 10px;
}
/* BL */
.mm-hamburger.is-open span:nth-child(3) {
    transform: translate(-2px, 2px) scale(1.05);
    border-radius: 10px;
}
/* BR */
.mm-hamburger.is-open span:nth-child(4) {
    transform: translate(2px, 2px) scale(1.05);
    border-radius: 10px;
}

/* En mobile lo mostramos */
@media (max-width: 850px) {
    .mm-hamburger { display: grid; }
}
</style>

<script>
    document.addEventListener("DOMContentLoaded", () => {
        /* ================
           THEME TOGGLE
           ================ */
        const html = document.documentElement;
        const saved = localStorage.getItem("maxmenu-theme");
        if (saved) html.setAttribute("data-theme", saved);

        const toggleTheme = () => {
            const current = html.getAttribute("data-theme") || "dark";
            const next = current === "dark" ? "light" : "dark";
            html.setAttribute("data-theme", next);
            localStorage.setItem("maxmenu-theme", next);
        };

        document.addEventListener("click", e => {
            if (e.target.closest("#themeToggle")) {
                toggleTheme();
            }
        });

        /* ================
           MOBILE MENU
           ================ */
        const hamburger  = document.getElementById("mmHamburger");
        const mobileMenu = document.getElementById("mmMobileMenu");

        const toggleMobileMenu = () => {
            if (!mobileMenu || !hamburger) return;
            const isOpen = mobileMenu.classList.toggle("is-open");
            hamburger.classList.toggle("is-open", isOpen);
            document.body.classList.toggle("mm-menu-open", isOpen);
        };

        if (hamburger && mobileMenu) {
            hamburger.addEventListener("click", toggleMobileMenu);

            // Cerrar al pulsar un enlace del menú
            mobileMenu.querySelectorAll("a").forEach(link => {
                link.addEventListener("click", () => {
                    if (mobileMenu.classList.contains("is-open")) {
                        toggleMobileMenu();
                    }
                });
            });

            // Cerrar con ESC
            document.addEventListener("keydown", e => {
                if (e.key === "Escape" && mobileMenu.classList.contains("is-open")) {
                    toggleMobileMenu();
                }
            });
        }
    });
</script>

<header class="mm-header">
    <div class="mm-header-inner">

        <div class="mm-logo">
            <img style="width:50px; height:45px;" src="img/logoaa.png" alt="MaxMenu logo">
               <button id="themeToggle" class="mm-theme-toggle">
                <div class="toggle-circle"></div>
            </button>
        </div>

        <!-- DESKTOP NAV -->
        <nav class="mm-nav">
            <a href="index">Home</a>
            <a href="agency">Agency</a>
            <a href="pricing">Pricing</a>
            <a href="embedded">Embedded</a>
        </nav>

        <!-- ACTIONS -->
        <div class="mm-actions">


            <a href="login">
                <img src="img/user.svg" alt="Login">
            </a>

           <div id="mmHamburger" class="mm-hamburger">
                <span></span>
                <span></span>
                <span></span>
                <span></span>
            </div>
        </div>

    </div>
</header>

<!-- MOBILE NAV OVERLAY -->
<div id="mmMobileMenu" class="mm-mobile-menu">
    <div class="mm-mobile-menu-top">
        <a href="index" class="mm-mobile-link">
            Home
            <span>See what MaxMenu can do for your restaurant.</span>
        </a>
        <a href="embedded" class="mm-mobile-link">
            Embedded
            <span>Embed the menu once and control everything from the panel.</span>
        </a>
        <a href="agency" class="mm-mobile-link">
            Agency
            <span>Multi-restaurant panel for your clients.</span>
        </a>
        <a href="pricing" class="mm-mobile-link">
            Pricing
            <span>Choose how far you want to go with design and embedding.</span>
        </a>
    </div>

    <div class="mm-mobile-menu-bottom">
        <a href="login" class="mm-mobile-login">Log in</a>
        <div class="mm-mobile-meta">
            MaxMenu — Invisible menu infrastructure for restaurants and agencies.
        </div>
    </div>
</div>