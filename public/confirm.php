<?php
$success = $_GET['success'] ?? null;
$message = $_GET['message'] ?? '';
$email   = $_GET['email'] ?? '';

// Si alguien entra directamente sin parámetros, no mostramos nada
if ($success === null) {
    http_response_code(404);
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>

    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- Título de la Página -->
    <title>MaxMenu | Verify Email</title>

    <!-- Meta Descripción refinada -->
    <meta name="description" content="Live example of MaxMenu embedded in a website. Fully responsive, multilingual, and elegant. No plugins. No installation. Just copy, paste and go.">

    <!-- Palabras Clave -->
    <meta name="keywords" content="maxmenu example, digital menu demo, embeddable QR menu, multilingual menu widget, responsive menu iframe, demo menú digital, MaxMenu widget example, restaurant digital experience">

    <!-- Canonical Tag -->
    <link rel="canonical" href="https://www.maxmenu.com/confirm">

    <!-- Open Graph para redes sociales -->
    <meta property="og:title" content="MaxMenu | Live Example" />
    <meta property="og:description" content="This is a live example of MaxMenu fully integrated in a website. Embeddable, elegant and multilingual." />
    <meta property="og:image" content="https://cdn.maxmenu.com/w/img/wpp.png" />
    <meta property="og:url" content="https://www.maxmenu.com/confirm" />

    <!-- Twitter Card -->
    <meta name="twitter:card" content="summary_large_image" />
    <meta name="twitter:title" content="MaxMenu | Live Example" />
    <meta name="twitter:description" content="See how MaxMenu looks and works when embedded in a real website. Try it now." />
    <meta name="twitter:image" content="https://cdn.maxmenu.com/w/img/wpp.png" />


    <!-- Robots Meta Tag -->
    <meta name="robots" content="index, follow">

    <!-- Favicon -->
    <link rel="icon"  href="https://cdn.maxmenu.com/w/img/logoaa.ico">
     <link rel="apple-touch-icon" href="https://cdn.maxmenu.com/w/img/apple.png">

    <!-- Estilos CSS -->
    <link rel="stylesheet" href="styles/header.css">
	<link rel="stylesheet" href="styles/view-confirm.css">
    <link rel="stylesheet" href="styles/footer.css">

</head>

<body>
		
    <?php include_once ('templates/header.php'); ?>
	
	<div id="banner">

		<div class="banner-content">
			<?php if ($success === 'true'): ?>
			  <h1 class="success">¡Account verified!</h1>
			  <br />
			  <p>Your account has been successfully verified. You can now log in.</p>
			  <br />
			  <div class="banner-enlace-img" style="">
			  <a href="login" class="btn-banner" >Login</a>
			  </div>
			 <?php else: ?>
			  <h1 class="error">¡Error verifying!</h1>
			  <p><?= htmlspecialchars($message ?: 'El enlace ha expirado o ya fue utilizado.') ?></p>
			  <div class="banner-enlace-img" style="">
			  <a href="login" class="btn">Try again</a>
			  </div>
			<?php endif; ?>
		</div>

	</div>


   
	<?php include_once ('footer.php'); ?>

	
</body>
</html>

<!-- home.php (por ejemplo) -->


