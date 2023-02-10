<!doctype html>
<html lang="es-MX">
<head>
	<!--==================================
	META
	=====================================-->
	<meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
	<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="<?php echo $description; ?>">
	<meta name="author" content="sectorweb.mx">
    <meta name="theme-color" content="#b21d23">

	<!--==================================
	FB META
	=====================================-->
    <meta property="og:url" content="<?php echo Uri::current(); ?>" />
    <meta property="og:type" content="website" />
    <meta property="og:title" content="Distribuidora Sajor" />
    <meta property="og:description" content="Distribuidora Sajor" />
    <meta property="og:image:url" content="<?php echo Uri::base(false).'assets/img/facebook-share.jpg'; ?>" />
    <meta property="og:image:type" content="image/jpeg" />
    <meta property="og:image:width" content="600" />
    <meta property="og:image:height" content="314" />

	<!--==================================
	FAVICON
	=====================================-->
    <link rel="apple-touch-icon-precomposed" sizes="144x144" href="<?php echo Uri::base(false).'assets/img/ico144.png'; ?>">
    <link rel="apple-touch-icon-precomposed" sizes="114x114" href="<?php echo Uri::base(false).'assets/img/ico114.png'; ?>">
    <link rel="apple-touch-icon-precomposed" sizes="72x72" href="<?php echo Uri::base(false).'assets/img/ico72.png'; ?>">
    <link rel="apple-touch-icon-precomposed" href="<?php echo Uri::base(false).'assets/img/ico57.png'; ?>">
    <link rel="shortcut icon" href="<?php echo Uri::base(false).'assets/img/ico32.png'; ?>">

	<!--==================================
	CSS
	=====================================-->
    <?php echo Asset::css('app.css'); ?>

	<!--==================================
	TITLE
	=====================================-->
	<title><?php echo $title; ?></title>
</head>
<body>
	<!--==================================
	URL
	=====================================-->

	<span id="url-location" data-url="<?php echo Uri::create('/'); ?>"></span>

	<!--==================================
	CONTENT
	=====================================-->
    <?php echo $content ?>

	<!--==================================
	JAVASCRIPT
	=====================================-->
	<?php echo Asset::js('jquery-3.4.1.min.js'); ?>
	<?php echo Asset::js('bootstrap.bundle.min.js'); ?>

	<!--==================================
	GOOGLE ANALYTICS
	=====================================-->
	<script async src="https://www.googletagmanager.com/gtag/js?id=UA-108591663-37"></script>
	<script>
		window.dataLayer = window.dataLayer || [];
		function gtag(){dataLayer.push(arguments);}
		gtag('js', new Date());

		gtag('config', 'UA-108591663-37');
	</script>
</body>
</html>
