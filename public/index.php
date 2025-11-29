<!DOCTYPE html>
<html lang="en" data-theme="dark">
<head>
    <!-- Basic -->
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- SEO Core -->
    <title>MaxMenu — Invisible Menu Infrastructure for Restaurants & Agencies</title>
    <meta name="description" content="MaxMenu is invisible menu infrastructure. An embeddable digital menu system that lets restaurants and agencies control design, items, translations and payments without touching website code.">

    <!-- Canonical -->
    <link rel="canonical" href="https://maxmenu.com">

    <!-- Robots -->
    <meta name="robots" content="index,follow">

    <!-- Open Graph -->
    <meta property="og:type" content="website">
    <meta property="og:site_name" content="MaxMenu">
    <meta property="og:title" content="MaxMenu — Invisible Menu Infrastructure for Restaurants & Agencies">
    <meta property="og:description" content="Embeddable digital menu infrastructure. Design once, deploy everywhere, and control every restaurant menu from a single dashboard.">
    <meta property="og:url" content="https://maxmenu.com">
    <meta property="og:image" content="https://maxmenu.com/assets/social/maxmenu-home.jpg">

    <!-- Twitter Card -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="MaxMenu — Invisible Menu Infrastructure for Restaurants & Agencies">
    <meta name="twitter:description" content="An invisible, embeddable menu layer for restaurants and agencies. Control design, items, translations and payments from one place.">
    <meta name="twitter:image" content="https://maxmenu.com/assets/social/maxmenu-home.jpg">

    <!-- Favicon / theme -->
    <link rel="icon" href="/img/logo-app.png">
    <meta name="theme-color" content="#000000">

    <!-- JSON-LD: Organization + Website + Home WebPage -->
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
          "name": "MaxMenu",
          "potentialAction": {
            "@type": "SearchAction",
            "target": "https://maxmenu.com/search?q={search_term_string}",
            "query-input": "required name=search_term_string"
          }
        },
        {
          "@type": "WebPage",
          "url": "https://maxmenu.com",
          "name": "MaxMenu — Invisible Menu Infrastructure for Restaurants & Agencies",
          "description": "MaxMenu is invisible menu infrastructure. An embeddable digital menu system that lets restaurants and agencies control design, items, translations and payments without touching website code.",
          "inLanguage": "en",
          "isPartOf": {
            "@id": "https://maxmenu.com#website"
          }
        }
      ]
    }
    </script>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@600;700&family=DM+Sans:wght@400;500;600&family=Inter:wght@400;500&family=Space+Grotesk:wght@500;600&display=swap" rel="stylesheet">

    <!-- CSS -->
    <link rel="stylesheet" href="styles/header.css">
    <link rel="stylesheet" href="styles/footer.css">
    <link rel="stylesheet" href="styles/view-index.css">

    <!-- Theme sync -->
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

<?php include 'views/view-index.php'; ?>

<?php include 'templates/footer.php'; ?>

</body>
</html>