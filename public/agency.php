<!DOCTYPE html>
<html lang="en" data-theme="dark">
<head>
    <!-- Basic -->
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- Title + Description (SEO principal) -->
    <title>MaxMenu for Agencies — Premium digital menus for restaurant clients</title>
    <meta name="description" content="MaxMenu helps creative agencies launch high-converting digital menus for restaurant clients, turning every restaurant website into a recurring revenue channel.">

    <!-- Canonical -->
    <link rel="canonical" href="https://maxmenu.com/agency">

    <!-- Robots (en producción: index,follow; en staging podrías cambiar a noindex) -->
    <meta name="robots" content="index,follow">

    <!-- Open Graph (para compartir en redes / previews) -->
    <meta property="og:type" content="website">
    <meta property="og:site_name" content="MaxMenu">
    <meta property="og:title" content="MaxMenu for Agencies — Premium digital menus for restaurant clients">
    <meta property="og:description" content="MaxMenu helps creative agencies launch high-converting digital menus for restaurant clients, turning every restaurant website into a recurring revenue channel.">
    <meta property="og:url" content="https://maxmenu.com/agency">
    <meta property="og:image" content="https://cdn.maxmenu.com/w/img/wpp.png">

    <!-- Twitter Card -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="MaxMenu for Agencies — Premium digital menus for restaurant clients">
    <meta name="twitter:description" content="MaxMenu helps creative agencies launch high-converting digital menus for restaurant clients, turning every restaurant website into a recurring revenue channel.">
    <meta name="twitter:image" content="https://cdn.maxmenu.com/w/img/wpp.png">

    <!-- Favicon / theme -->
    <!-- Favicon -->
    <link rel="icon"  href="https://cdn.maxmenu.com/w/img/logoaa.ico">
    <link rel="apple-touch-icon" href="https://cdn.maxmenu.com/w/img/apple.png">
    <meta name="theme-color" content="#000000">

    <!-- JSON-LD: Organization + Website + WebPage (Google rich understanding) -->
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
          "url": "https://maxmenu.com/agencies",
          "name": "MaxMenu for Agencies — Premium digital menus for restaurant clients",
          "description": "MaxMenu helps creative agencies launch high-converting digital menus for restaurant clients, turning every restaurant website into a recurring revenue channel.",
          "inLanguage": "en",
          "isPartOf": {
            "@id": "https://maxmenu.com#website"
          }
        }
      ]
    }
    </script>

    <!-- Your CSS -->
    <link rel="stylesheet" href="styles/footer.css">
    <link rel="stylesheet" href="styles/header.css">
    <link rel="stylesheet" href="styles/view-agency.css">
</head>
<body>

<?php include 'templates/header.php'; ?>

<?php include 'views/view-agency.php'; ?>

<?php include 'templates/footer.php'; ?>

</body>
</html>