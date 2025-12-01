<header class="mm-header">
    <div class="mm-header-inner">

        <div class="mm-logo">
            <img style="width:50px; height:45px;" src="https://cdn.maxmenu.com/w/img/logomm.png" alt="MaxMenu logo">
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
                <img src="https://cdn.maxmenu.com/w/img/user.svg" alt="Login">
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
<script>
    document.addEventListener("DOMContentLoaded", () => {
        const html = document.documentElement;
        const saved = localStorage.getItem("maxmenu-theme");
        if (saved) html.setAttribute("data-theme", saved);
    });
</script>
