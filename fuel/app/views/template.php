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
	<meta name="keywords" content="naturistas, organicos, salud, saludables, suplementos, vitaminas, productos naturales, vitaminas, organicos, salud, bienestar, cuidado personal, belleza, precio accesible, comprar en linea, colageno, depresion, ansiedad, enfermedad"/>
	<meta name="author" content="https://www.setsolucionesti.com/">
	<meta name="theme-color" content="#008ad5">

	<!--==================================
	FAVICONS
	=====================================-->
	<link rel="apple-touch-icon-precomposed" sizes="144x144" href="<?php echo Uri::base(false).'assets/img/ico144.png'; ?>">
	<link rel="apple-touch-icon-precomposed" sizes="114x114" href="<?php echo Uri::base(false).'assets/img/ico114.png'; ?>">
	<link rel="apple-touch-icon-precomposed" sizes="72x72" href="<?php echo Uri::base(false).'assets/img/ico72.png'; ?>">
	<link rel="apple-touch-icon-precomposed" href="<?php echo Uri::base(false).'assets/img/ico57.png'; ?>">
	<link rel="shortcut icon" href="<?php echo Uri::base(false).'assets/img/ico32.png'; ?>">

	<!--==================================
	GOOGLE MAP
	=====================================-->
	<script src="https://maps.googleapis.com/" type="text/javascript"></script>

	<!--==================================
	CSS
	=====================================-->
	<?php echo Asset::css('app.css'); ?>
	<?php echo Asset::css('add.css'); ?>

	<!--==================================
	GOOGLE ANALYTICS
	=====================================-->
	<!-- Google tag (gtag.js) -->
<script async src="https://www.googletagmanager.com/gtag/js?id=G-QN2YNGGVMD"></script>
<script>
  window.dataLayer = window.dataLayer || [];
  function gtag(){dataLayer.push(arguments);}
  gtag('js', new Date());

  gtag('config', 'G-QN2YNGGVMD');
</script>

	<!--==================================
	RECAPTCHA
	=====================================-->
	<script src="https://www.google.com/recaptcha/api.js" async defer></script>

	<!--==================================
	FACEBOOK PIXEL
	=====================================-->
	
	<!--==================================
	TITLE
	=====================================-->
	<title><?php echo $title; ?></title>
</head>
<body>

	<!--==================================
	FACEBOOK MESSENGER
	=====================================-->
	

	<!--==================================
	URL
	=====================================-->
	<span id="url-location" data-url="<?php echo Uri::create('/'); ?>"></span>
	<span id="url-current" data-url="<?php echo Uri::current(); ?>"></span>
	<span id="url" data-url="<?php echo Uri::base(false);?>"></span>

	<!--==================================
	HEADER
	=====================================-->
	<header>
		<div id="megamenu" class="bg-white">
			<div class="container-fluid">
				<div class="row">
					<div class="col-12 p-0">
						<div class="d-flex justify-content-between px-3 pt-3 pb-1">
							<ul class="list-inline d-inline-block mb-1 mb-md-0">
								<li class="list-inline-item mr-3">
									<?php echo Html::anchor('/', Asset::img('logo.jpg', array('alt' => 'Natura y Mas', 'class' => 'img-fluid d-block mx-auto')), array('title' => 'Natura y Mas', 'class' => 'navbar-brand navbar-brand-sm d-inline-block')); ?>
								</li>
							</ul>
							<?php echo Html::anchor('', '<i class="lni lni-close font-weight-bold pr-3 pl-2"></i>', array('title' => 'Cerrar', 'class' => 'megamenu-close text-uppercase bg-transparent border-0 pt-4 text-decoration-none')); ?>
						</div>
						<div class="accordion border" id="accordion">
							<div class="card rounded-0 border-0">
								<div class="card-header rounded-0">
									<?php echo Html::anchor('/', 'Inicio', array('title' => 'Inicio', 'class' => 'font-style-bold pl-3 ')); ?>
								</div>
							</div>
							<div class="card rounded-0 border-0">
								<div class="card-header rounded-0">
									<?php echo Html::anchor('empresa', 'Nosotros', array('title' => 'Nosotros', 'class' => 'font-style-bold pl-3 border-top')); ?>
								</div>
							</div>
							<div class="card rounded-0 border-0">
								<div class="card-header rounded-0">
									<?php echo Html::anchor('distribucion', 'Distribución', array('title' => 'Distribución', 'class' => 'font-style-bold pl-3 border-top')); ?>
								</div>
							</div>
							<div class="card rounded-0 border-0">
								<div class="card-header rounded-0">
									<?php echo Html::anchor('tienda', 'Productos', array('title' => 'Productos', 'class' => 'font-style-bold pl-3 border-top')); ?>
								</div>
							</div>
							<div class="card rounded-0 border-0">
								<div class="card-header rounded-0">
									<?php echo Html::anchor('promociones', 'Promociones', array('title' => 'Promociones', 'class' => 'font-style-bold pl-3 border-top')); ?>
								</div>
							</div>
							<div class="card rounded-0 border-0">
								<div class="card-header rounded-0">
									<?php echo Html::anchor('contacto', 'Contacto', array('title' => 'Contacto', 'class' => 'font-style-bold pl-3 border-top')); ?>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
		<section>
			<div class="container-fluid">
				<div class="row justify-content-center justify-content-lg-between">
					<div class="col-12 contact-info text-center" id="marquee">
						<span class="d-block">Bienvenido a nuestra tienda de productos naturistas. Ofrecemos una amplia variedad de opciones para cuidar tu salud de manera natural.</span>
					</div>
				</div>
			</div>
		</section>
		<section>
			<div class="container">
				<div class="row py-2">
					<div class="col-3 d-none d-lg-block px-0">
						<?php echo Html::anchor('/', Asset::img('logo.jpg', array('alt' => 'Natura y Mas', 'class' => 'img-fluid d-block mx-auto')), array('title' => 'Natura y Mas', 'class' => 'navbar-brand mr-3 mr-xl-5')); ?>
					</div>
					<div class="col-9 d-none d-lg-block">
						<?php echo Form::open(
							array(
								'action'     => 'tienda/busqueda',
								'method'     => 'post',
								'class'      => 'form-inline form-search d-flex justify-content-end',
								'id'         => 'form_search',
								'novalidate' => true
							)
						); ?>
						<div class="navbar">
							<div class="mb-3">
								<div class="text-right">
									<ul class="list-inline d-inline-block mb-1 list-contact">
										<li class="list-inline-item"><?php echo Html::mail_to('contacto@naturaymas.com.mx', '<i class="lni-envelope mr-1"></i>contacto@naturaymas.com.mx', null, array('class' => '')); ?></li>
										<li class="list-inline-item"><a href="https://wa.me/3321897080"><i class="lni-whatsapp mr-1"></i>33 2189 7080</a></li>
									</ul>
								</div>
								<div class="input-group input-group-lg">
									<?php echo Form::input('search', '', array('class' => 'form-control form-control-input border-right-0')); ?>
									<div class="input-group-append">
										<?php echo Form::button('submit', '<span>Buscar </span><i class="lni lni-search"></i>', array('title' => 'Buscar', 'class' => 'btn btn-outline-primary bg-white d-inline-block border-left-0', 'type' => 'submit')); ?>
									</div>
								</div>
							</div>
							<div class="ml-5 mt-2">
								<ul class="navbar-nav d-inline-block">
									<li class="nav-item d-inline-block mr-2">
										<a href="javascript:void(0)" class="dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" title="Tu cuenta">
											<i class="lni lni-user"></i>
											<?php echo ($logged_in) ? Auth::get('username') : 'Tu cuenta'; ?>
										</a>
										<div class="dropdown-menu">
											<?php if($logged_in): ?>
												<?php echo Html::anchor('mi-cuenta', 'Mi cuenta', array('title' => 'Mi cuenta', 'class' => 'dropdown-item')); ?>
												<?php echo Html::anchor('deseados', 'Productos deseados', array('title' => 'Productos deseados', 'class' => 'dropdown-item')); ?>
												<?php echo Html::anchor('cerrar-sesion', 'Cerrar sesión', array('title' => 'Cerrar sesión', 'class' => 'dropdown-item')); ?>
											<?php else: ?>
												<?php echo Html::anchor('iniciar-sesion', 'Iniciar sesión', array('title' => 'Iniciar sesión', 'class' => 'dropdown-item')); ?>
												<?php echo Html::anchor('registro', 'Registrarse', array('title' => 'Registrarse', 'class' => 'dropdown-item')); ?>
											<?php endif; ?>
										</div>
									</li>
									<li class="nav-item cart-container d-inline-block">
										<div class="cart d-inline-block text-center ml-2">
											<?php echo Html::anchor('checkout', '
											<div class="icon pt-1 pt-lg-2 d-inline-block">
											<i class="lni lni-cart"></i>
											<span class="cart-count cart-count-mobile badge cart-qty">'.$total_products_quantity.'</span>
											</div><div class="d-inline-block">
												Carrito
											</div>
											', array('title' => 'Carrito de Compra', 'class' => 'text-primary d-inline cart-link')); ?>
										</div>
									</li>
								</ul>
							</div>
						</div>
						<?php echo Form::close(); ?>
					</div>
				</div>
				<div class="row d-lg-none d-md-block">
					<nav class="navbar navbar-movil navbar-expand-xl flex-fill px-3 pb-3">
						<?php echo Html::anchor('/', Asset::img('logo.jpg', array('alt' => 'Naturaymas', 'class' => 'img-fluid d-block mx-auto')), array('title' => 'Naturaymas', 'class' => 'navbar-brand mr-3 mr-xl-5')); ?>
						<div class="d-block d-xl-none">
							<ul class="navbar-nav d-x-block">
								<li class="nav-item">
									<a class="dropdown-toggle megamenu-open nav-link nav-link-mobile" id="dropdownMenuButton" type="button" aria-haspopup="true" aria-expanded="false">
										<i class="lni lni-menu"></i>
									</a>
								</li>
								<li class="nav-item">
									<div class="btn-group">
										<a href="javascript:void(0)" class="dropdown-toggle nav-link nav-link-mobile" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
											<i class="lni lni-user"></i>
										</a>
										  <div class="dropdown-menu">
										  <?php if($logged_in): ?>
												<?php echo Html::anchor('mi-cuenta', 'Mi cuenta', array('title' => 'Mi cuenta', 'class' => 'dropdown-item')); ?>
												<?php echo Html::anchor('deseados', 'Productos deseados', array('title' => 'Productos deseados', 'class' => 'dropdown-item')); ?>
												<?php echo Html::anchor('cerrar-sesion', 'Cerrar sesión', array('title' => 'Cerrar sesión', 'class' => 'dropdown-item')); ?>
											<?php else: ?>
												<?php echo Html::anchor('iniciar-sesion', 'Iniciar sesión', array('title' => 'Iniciar sesión', 'class' => 'dropdown-item')); ?>
												<?php echo Html::anchor('registro', 'Registrarse', array('title' => 'Registrarse', 'class' => 'dropdown-item')); ?>
											<?php endif; ?>
										  </div>
									  </div>
								  </li>
								<li class="nav-item text-center">
									<ul class="list-inline d-inline-block pb-0 cart-container nav-link nav-link-mobile px-0">
										<li class="list-inline-item">
											<ul class="navbar-nav">
												<li class="nav-item cart-container">
													<div class="cart d-flex text-center">
														<?php echo Html::anchor('checkout', '
														<div class="icon pt-1 pt-lg-2">
														<i class="lni-cart lni-cart-mobile"></i>
														<span class="cart-count cart-count-mobile badge cart-qty">'.$total_products_quantity.'</span>
														</div>', array('title' => 'Carrito de Compra', 'class' => 'text-primary d-inline cart-link')); ?>
													</div>
												</li>
											</ul>
										</li>
									</ul>
								</li>
							</ul>
						</div>
						<div class="d-flex d-xl-none flex-column w-100 pb-3">
							<?php echo Form::open(
								array(
									'action'     => 'tienda/busqueda',
									'method'     => 'post',
									'class'      => 'form-inline form-search flex-fill mt-3',
									'id'         => 'form_search',
									'novalidate' => true
								)
							); ?>
							<div class="input-group input-group-lg w-100">
								<?php echo Form::input('search', '', array('class' => 'form-control form-control-input border-right-0')); ?>
								<div class="input-group-append">
									<?php echo Form::button('submit', '<i class="lni lni-search"></i>', array('title' => 'Buscar', 'class' => 'btn btn-outline-primary bg-white d-inline-block border-left-0', 'type' => 'submit')); ?>
								</div>
							</div>
							<?php echo Form::close(); ?>
						</div>
					</nav>
				</div>
				<div class="row d-flex justify-content-center border-top">
					<nav class="d-none d-lg-block navbar mt-3">
						<div class="col-auto">
							<ul class="list-inline d-flex">
								<li class="nav-item">
									<div class="d-inline-flex">
										<?php echo Html::anchor('/', 'Inicio', array('title' => 'Inicio', 'class' => 'nav-link text-decoration-none text-primary')); ?>
									</div>
								</li>
								<li class="nav-item">
									<div class="d-inline-flex">
										<?php echo Html::anchor('empresa', 'Nosotros', array('title' => 'Nosotros', 'class' => 'nav-link text-decoration-none text-primary')); ?>
									</div>
								</li>
								<li class="nav-item">
									<div class="d-inline-flex">
										<?php echo Html::anchor('distribucion', 'Distribución', array('title' => 'Distribución', 'class' => 'nav-link text-decoration-none text-primary')); ?>
									</div>
								</li>
								<li class="nav-item">
									<div class="d-inline-flex">
										<?php echo Html::anchor('tienda', 'Productos', array('title' => 'Productos', 'class' => 'nav-link text-decoration-none text-primary')); ?>
									</div>
								</li>
								<li class="nav-item">
									<div class="d-inline-flex">
										<?php echo Html::anchor('promociones', 'Promociones', array('title' => 'Promociones', 'class' => 'nav-link text-decoration-none text-primary')); ?>
									</div>
								</li>
								<li class="nav-item">
									<div class="d-inline-flex">
										<?php echo Html::anchor('contacto', 'Contacto', array('title' => 'Contacto', 'class' => 'nav-link text-decoration-none text-primary')); ?>
									</div>
								</li>
							</ul>
						</div>
					</nav>
				</div>
			</div>
		</section>
	</header>

	<!--==================================
	CONTENT
	=====================================-->
	<?php echo $content; ?>

	<!--==================================
	HEADER
	=====================================-->
	<footer>
		<section>
			<div class="container">
				<div class="row">
					<div class="col-12 pt-3 pb-4">
						<span class="invisible">Natura y Mas</span>
					</div>
					<div class="col-12 d-flex justify-content-center border-top">
						<?php echo Html::anchor('/', Asset::img('logo.jpg', array('alt' => 'Natura y Mas', 'class' => 'img-fluid d-block pt-5')), array('title' => 'Natura y Mas')); ?>
					</div>
				</div>
				<div class="row pt-5">
					<div class="col-lg-4 pl-md-0 pl-xd-0">
						<span class="font-weight-bold title-footer">Atención al cliente</span>
						<p class="lh text-justify">En nuestra área de atención al cliente, nos esforzamos por brindar un servicio excepcional. Nuestro equipo está compuesto por expertos en productos naturistas que están disponibles para responder cualquier pregunta o inquietud que pueda tener sobre nuestros productos. Ofrecemos una variedad de canales de atención al cliente, incluyendo correo electrónico, teléfono y chat en vivo. Estamos disponibles para responder sus preguntas durante nuestro horario de atención al cliente.<br> Gracias.</p>
					</div>
					<div class="col-lg-2 pl-xd-0">
						<span class="font-weight-bold title-footer">Productos</span>
						<address class="lh">
							Productos naturales<br>
							Suplementos alimenticios<br>
							Productos orgánicos<br>
							Salud natural
						</address>
					</div>
					<div class="col-lg-2 pl-xd-0">
						<span class="font-weight-bold title-footer">Whatsapp</span>
						<a href="https://wa.me/3321897080" class="d-block mb-1">33 2189 7080</a>
						<span class="d-block">Solo Whatsapp</span>
						<br>
						<span class="font-weight-bold title-footer">Horario de Atencion</span>
						<span class="d-block mb-1">Lunes a Viernes</span>
						<span class="d-block">9:00 a 19:00 hrs.</span>
					</div>
					<div class="col-lg-2 pl-xd-0">
						<span class="font-weight-bold title-footer">Mapa del sitio</span>
						<ul class="list-unstyled">
							<li class="mb-1"><?php echo Html::anchor('/', 'Inicio', array('title' => 'Inicio')); ?></li>
							<li class="mb-1"><?php echo Html::anchor('empresa', 'Empresa', array('title' => 'Empresa')); ?></li>
							<li class="mb-1"><?php echo Html::anchor('distribucion', 'Distribución', array('title' => 'Distribución')); ?></li>
							<li class="mb-1"><?php echo Html::anchor('tienda', 'Productos', array('title' => 'Productos')); ?></li>
							<li class="mb-1"><?php echo Html::anchor('promociones', 'Promociones', array('title' => 'Promociones')); ?></li>
							<li class="mb-1"><?php echo Html::anchor('contacto', 'Contacto', array('title' => 'Contacto')); ?></li>
						</ul>
					</div>
					<div class="col-lg-2 pr-md-0 pl-xd-0">
						<span class="font-weight-bold title-footer">Documentos legales</span>
						<ul class="list-unstyled">
							<li class="mb-1"><?php echo Html::anchor('terminos-y-condiciones', 'Términos y condiciones', array('title' => 'Términos y condiciones')); ?></li>
							<li class="mb-1"><?php echo Html::anchor('aviso-de-privacidad', 'Aviso de privacidad', array('title' => 'Aviso de privacidad')); ?></li>
						</ul>
					</div>
				</div>
				<div class="row">
					<div class="col-12">
						<div class="d-flex justify-content-center pb-4">
							<span class="d-inline-block mr-2 font-weight-bold">Síguenos: </span>
							<ul class="list-unstyled mb-0">
								<li class="d-inline-block">
									<?php echo Html::anchor('https://www.facebook.com/DistribuidoraNaturaymas', Asset::img('icon-fb.png', array('alt' => 'Facebook', 'class' => 'img-fluid mr-2')), array('title' => 'Facebook', 'target' => '_blank')); ?>
								</li>
								<li class="d-inline-block">
									<?php echo Html::anchor('https://www.instagram.com/naturaymasmx', Asset::img('icon-ig.png', array('alt' => 'Instagram', 'class' => 'img-fluid mr-2')), array('title' => 'Instagram', 'target' => '_blank')); ?>
								</li>
								<li class="d-inline-block">
									<?php echo Html::anchor('https://www.youtube.com/@naturaymas', Asset::img('icon-yt.png', array('alt' => 'Youtube', 'class' => 'img-fluid mr-2')), array('title' => 'Youtube', 'target' => '_blank')); ?>
								</li>
							</ul>
						</div>
					</div>
				</div>
			</div>
		</section>
		<section>
			<div class="container">
				<div class="row border-top py-3 text-center">
					<div class="col-md mb-2 mb-md-0 text-md-left pl-md-0">
						<p class="mb-0">Copyright <?php echo date('Y'); ?> © <strong>Natura y Mas</strong></p>
					</div>
					<div class="col-md mb-2 mb-md-0 text-md-right pr-md-0">
						<p class="mb-0">Desarrollado por <?php echo Html::anchor('http://setsolucionesti.com', '<span class="font-weight-bold">Setsoluciones</span>', array('title' => 'Diseño de Páginas Web. Desarrollo Web en México.', 'target' => '_blank', 'class' => 'd-inline-block text-decoration-none')); ?></p>
					</div>
				</div>
			</div>
		</section>
	</footer>

	<!--==================================
	JAVASCRIPT
	=====================================-->
	<?php echo Asset::js('jquery-3.4.1.min.js'); ?>
	<?php echo Asset::js('bootstrap.bundle.min.js'); ?>
	<?php echo Asset::js('owl.carousel.min.js'); ?>
	<?php echo Asset::js('owl.carousel.js'); ?>
	<?php echo Asset::js('jquery.marquee.min.js'); ?>
	<?php echo Asset::js('jquery.validate.min.js'); ?>
	<?php echo Asset::js('jquery.validate.messages_es.min.js'); ?>
	<?php echo Asset::js('jquery.bootstrap-touchspin.min.js'); ?>
	<?php echo Asset::js('alertify.min.js'); ?>
	<?php echo Asset::js('glasscase.js'); ?>
	<?php echo Asset::js('cpr.js'); ?>
	<?php echo Asset::js('app.js'); ?>
	<?php echo Asset::js('cookies.js'); ?>
	<?php echo Asset::js('add.js'); ?>

	<!--==================================
	COOKIES
	=====================================-->
	<div id="overbox3">
		<div id="row border-top py-3 text-center">
			<p>Uso de cookies. Al continuar, acepta. Ver política.
				<a class="mb-1"><?php echo Html::anchor('terminos-y-condiciones', 'Términos y condiciones', array('title' => 'Términos y condiciones')); ?></a>
				<a onclick="aceptar_cookies();" style="cursor:pointer; color:red; font-weight:bold;">X Cerrar</a></p>
		</div>
	</div>

</body>
</html>
