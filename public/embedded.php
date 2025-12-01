<!DOCTYPE html>
<html lang="en" data-theme="dark">
<head>
    <!-- Basic -->
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- Title + Description -->
    <title>MaxMenu — Embeddable Menu Control for Restaurants</title>
    <meta name="description" content="Embed a fully customizable, multilingual digital menu inside any website. MaxMenu gives you instant control over items, design, translations and pricing without touching code.">

    <!-- Canonical (sin www, consistente con todo lo demás) -->
    <link rel="canonical" href="https://maxmenu.com/embedded-menu-control">

    <!-- Robots: esta sí nos interesa para SEO -->
    <meta name="robots" content="index,follow">

    <!-- Open Graph -->
    <meta property="og:type" content="website">
    <meta property="og:site_name" content="MaxMenu">
    <meta property="og:title" content="MaxMenu — Embeddable Menu Control for Restaurants">
    <meta property="og:description" content="Embed a fully customizable, multilingual digital menu inside any website, with instant control over items, design, translations and pricing.">
    <meta property="og:url" content="https://maxmenu.com/embedded">
    <meta property="og:image" content="https://cdn.maxmenu.com/w/img/wpp.png">

    <!-- Twitter Card -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="MaxMenu — Embeddable Menu Control for Restaurants">
    <meta name="twitter:description" content="An embeddable digital menu widget for restaurants. Control design, items and translations from a single dashboard, without touching the website code.">
    <meta name="twitter:image" content="https://cdn.maxmenu.com/w/img/wpp.png">

    <!-- Favicon / theme -->
    <!-- Favicon -->
    <link rel="icon"  href="https://cdn.maxmenu.com/w/img/logoaa.ico">
        <link rel="apple-touch-icon" href="https://cdn.maxmenu.com/w/img/apple.png">

    <meta name="theme-color" content="#000000">

    <!-- JSON-LD: Organization + Website + WebPage -->
    <script type="application/ld+json">
    {
      "@context": "https://schema.org",
      "@graph": [
        {
          "@type": "Organization",
          "name": "MaxMenu Inc.",
          "url": "https://maxmenu.com",
          "logo": "https://maxmenu.com/assets/logo/maxmenu-logo.png"
        },
        {
          "@type": "WebSite",
          "@id": "https://maxmenu.com#website",
          "url": "https://maxmenu.com",
          "name": "MaxMenu"
        },
        {
          "@type": "WebPage",
          "url": "https://maxmenu.com/embedded-menu-control",
          "name": "MaxMenu — Embeddable Menu Control for Restaurants",
          "description": "Embed a fully customizable, multilingual digital menu inside any website. MaxMenu gives you instant control over items, design, translations and pricing without touching code.",
          "inLanguage": "en",
          "isPartOf": {
            "@id": "https://maxmenu.com#website"
          }
        }
      ]
    }
    </script>

    <!-- CSS -->
    <link rel="stylesheet" href="styles/footer.css">
    <link rel="stylesheet" href="styles/header.css">
    <link rel="stylesheet" href="styles/view-embedded.css">

    <!-- Theme sync with localStorage (el toggle vive en el header) -->
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

<?php include 'views/view-embedded.php'; ?>

<?php include 'templates/footer.php'; ?>

</body>
</html>