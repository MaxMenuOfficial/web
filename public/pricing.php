<!DOCTYPE html>
<html lang="en" data-theme="dark">
<head>
    <!-- Basic -->
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- SEO Core -->
    <title>MaxMenu — Pricing for Invisible Menu Infrastructure</title>
    <meta name="description" content="Choose the MaxMenu plan that fits your restaurant or agency. All plans include multilingual menus, real-time control, premium design and embeddable infrastructure.">

    <!-- Canonical -->
    <link rel="canonical" href="https://maxmenu.com/pricing">

    <!-- Robots -->
    <meta name="robots" content="index,follow">

    <!-- Open Graph -->
    <meta property="og:type" content="website">
    <meta property="og:site_name" content="MaxMenu">
    <meta property="og:title" content="MaxMenu — Pricing for Invisible Menu Infrastructure">
    <meta property="og:description" content="Transparent pricing for premium digital menus. Multilingual, embeddable and designed for modern restaurants and agencies. No hidden fees.">
    <meta property="og:url" content="https://maxmenu.com/pricing">
    <meta property="og:image" content="https://maxmenu.com/assets/social/maxmenu-pricing.jpg">

    <!-- Twitter Card -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="MaxMenu — Pricing for Invisible Menu Infrastructure">
    <meta name="twitter:description" content="Explore MaxMenu plans. Multilingual, embeddable digital menus with real-time control and premium design. No surprises.">
    <meta name="twitter:image" content="https://maxmenu.com/assets/social/maxmenu-pricing.jpg">

    <!-- Favicon / theme -->
    <link rel="icon" href="/img/logo-app.png">
    <meta name="theme-color" content="#000000">

    <!-- JSON-LD: Organization + Website + Pricing WebPage -->
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
          "url": "https://maxmenu.com/pricing",
          "name": "MaxMenu — Pricing for Invisible Menu Infrastructure",
          "description": "Choose the MaxMenu plan that fits your restaurant or agency. All plans include multilingual menus, real-time control, premium design and embeddable infrastructure.",
          "inLanguage": "en",
          "isPartOf": {
            "@id": "https://maxmenu.com#website"
          }
        }
      ]
    }
    </script>

    <!-- CSS -->
    <link rel="stylesheet" href="styles/header.css">
    <link rel="stylesheet" href="styles/footer.css">
    <link rel="stylesheet" href="styles/view-pricing.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">

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

<?php include_once 'templates/header.php'; ?>
<?php include_once 'views/view-pricing.php'; ?>
<?php include_once 'templates/footer.php'; ?>

</body>
</html>