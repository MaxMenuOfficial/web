<!DOCTYPE html>
<html lang="en" data-theme="dark">
<head>
    <!-- Basic encoding -->
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- Page Title -->
    <title>MaxMenu | Order & Pay</title>

    <!-- Meta Description -->
    <meta name="description" content="Log in to MaxMenu and access your restaurant’s digital menu dashboard. Manage items, translations and design with speed, control and elegance.">

    <!-- Canonical (sin www) -->
    <link rel="canonical" href="https://maxmenu.com/login">

    <!-- Open Graph -->
    <meta property="og:type" content="website">
    <meta property="og:site_name" content="MaxMenu">
    <meta property="og:title" content="MaxMenu | Login">
    <meta property="og:description" content="Log in to MaxMenu and access your restaurant’s digital menu dashboard. Manage items, translations and design with speed, control and elegance.">
    <meta property="og:url" content="https://maxmenu.com/login">
    <meta property="og:image" content="https://cdn.maxmenu.com/w/img/wpp.png">

    <!-- Twitter Card -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="MaxMenu | Login">
    <meta name="twitter:description" content="Secure access to your restaurant’s digital menu manager. Designed for speed, control and elegance.">
    <meta name="twitter:image" content="https://cdn.maxmenu.com/w/img/wpp.png">

    <!-- Robots: login NO interesa indexarlo -->
    <meta name="robots" content="noindex, nofollow">

    <!-- Favicon / theme -->
    <link rel="icon" href="https://cdn.maxmenu.com/w/img/logoaa.ico">
    <link rel="apple-touch-icon" href="https://cdn.maxmenu.com/w/img/apple.png">
    <meta name="theme-color" content="#000000">

    <!-- CSS -->
    <link rel="stylesheet" href="styles/header.css">
    <link rel="stylesheet" href="styles/view-order-pay.css">
    <link rel="stylesheet" href="styles/footer.css">

    <!-- Theme sync (igual que en el resto de la web) -->
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
    <?php include_once 'views/view-order-pay.php'; ?>

     
   

</body>
</html>