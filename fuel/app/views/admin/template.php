<!DOCTYPE html>
<html lang="es">

<head>
	<!-- META -->
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
	<meta name="description" content="Panel administrativo desarrollado por Set Soluciones">
	<meta name="author" content="Setsoluciones ">

	<!-- TITLE -->
	<title><?php echo $title; ?></title>
	<link rel="icon" href="<?php echo Uri::base(false).'assets/img/admin/favicon.png'; ?>" type="image/png">

	<!-- GOOGLE FONTS -->
	<link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Open+Sans:300,400,600,700">

	<!-- ASSETS -->
	<?php echo Asset::css('nucleo/css/nucleo.css'); ?>
	<?php echo Asset::css('@fortawesome/fontawesome-free/css/all.min.css'); ?>
	<?php echo Asset::css('animate.css/animate.min.css'); ?>
	<?php echo Asset::css('select2/dist/css/select2.min.css'); ?>
	<?php echo Asset::css('fullcalendar/dist/fullcalendar.min.css'); ?>
	<?php echo Asset::css('sweetalert2/dist/sweetalert2.min.css'); ?>
	<?php echo Asset::css('admin/argon.css'); ?>
	<?php echo Asset::css('admin/main.css'); ?>
</head>

<body>
	<!-- URL-LOCATION -->
	<span id="url-location" data-url="<?php echo Uri::create('admin/ajax/'); ?>" data-id="<?php echo Auth::get('id'); ?>" data-token="<?php echo md5(Auth::get('login_hash')); ?>"></span>
	<span id="url" data-url="<?php echo Uri::base(false);?>"></span>
	<!-- SIDENAV -->
	<nav class="sidenav navbar navbar-vertical fixed-left navbar-expand-xs navbar-light bg-white" id="sidenav-main">
		<div class="scrollbar-inner">
			<!-- BRAND -->
			<div class="sidenav-header d-flex align-items-center">
				<?php echo Html::anchor('admin', Asset::img('admin/logo.png', array('class' => 'navbar-brand-img')), array('class' => 'navbar-brand')); ?>
				<div class="ml-auto">
					<!-- SIDENAV TOGGLER -->
					<div class="sidenav-toggler d-none d-xl-block" data-action="sidenav-unpin" data-target="#sidenav-main">
						<div class="sidenav-toggler-inner">
							<i class="sidenav-toggler-line"></i>
							<i class="sidenav-toggler-line"></i>
							<i class="sidenav-toggler-line"></i>
						</div>
					</div>
				</div>
			</div>
			<div class="navbar-inner">
				<!-- COLLAPSE -->
				<div class="collapse navbar-collapse" id="sidenav-collapse-main">
					<!-- NAV ITEMS -->
					<ul class="navbar-nav">
						<li class="nav-item">
							<?php echo Html::anchor('admin', '<i class="ni ni-shop text-primary"></i><span class="nav-link-text">Dashboard</span>', array('class' => 'nav-link')); ?>
						</li>
						<?php if(Auth::member(100)  || Auth::member(50)): ?>
						<li class="nav-item">
							<?php echo Html::anchor('admin/slides', '<i class="ni ni-image text-orange"></i><span class="nav-link-text">Slides</span>', array('class' => 'nav-link')); ?>
						</li>
						<li class="nav-item">
							<?php echo Html::anchor('admin/baners', '<i class="ni ni-album-2 text-green"></i><span class="nav-link-text">Baners</span>', array('class' => 'nav-link')); ?>
						</li>
						<li class="nav-item">
						<a class="nav-link <?php echo (Uri::segment(3) == 'productos' || Uri::segment(3) == 'marcas' || Uri::segment(3) == 'categorias' || Uri::segment(3) == 'subcategorias') ? 'active' : ''; ?>" href="#navbar-products" data-toggle="collapse" role="button" aria-expanded="false" aria-controls="navbar-products">
								<i class="ni ni-single-copy-04 text-info"></i>
								<span class="nav-link-text">Catálogo Productos</span>
							</a>
							<div class="collapse <?php echo (Uri::segment(3) == 'productos' || Uri::segment(3) == 'marcas' || Uri::segment(3) == 'categorias' || Uri::segment(3) == 'subcategorias') ? 'show' : ''; ?>" id="navbar-products">
								<ul class="nav nav-sm flex-column">
									<li class="nav-item">
										<?php echo Html::anchor('admin/catalogo/productos', 'Productos', array('class' => 'nav-link')); ?>
									</li>
									<li class="nav-item">
										<?php echo Html::anchor('admin/catalogo/marcas', 'Marcas', array('class' => 'nav-link')); ?>
									</li>
									<li class="nav-item">
										<?php echo Html::anchor('admin/catalogo/categorias', 'Categorías', array('class' => 'nav-link')); ?>
									</li>
									<li class="nav-item">
										<?php echo Html::anchor('admin/catalogo/subcategorias', 'Grupos', array('class' => 'nav-link')); ?>
									</li>
								</ul>
							</div>
						</li>
						<?php endif; ?>
						<?php if(Auth::member(100)  || Auth::member(50)): ?>
						<li class="nav-item">
							<a class="nav-link <?php echo (Uri::segment(3) == 'orders' || Uri::segment(3) == 'paqueterias') ? 'active' : ''; ?>" href="#navbar-logistics" data-toggle="collapse" role="button" aria-expanded="false" aria-controls="navbar-logistics">
								<i class="ni ni-delivery-fast text-primary"></i>
								<span class="nav-link-text">Catálogo Logística</span>
							</a>
							<div class="collapse <?php echo (Uri::segment(3) == 'orders' || Uri::segment(3) == 'paqueterias') ? 'show' : ''; ?>" id="navbar-logistics">
								<ul class="nav nav-sm flex-column">
									<li class="nav-item">
										<?php echo Html::anchor('admin/catalogo/orders', 'Estatus pedido', array('class' => 'nav-link')); ?>
									</li>
									<li class="nav-item">
										<?php echo Html::anchor('admin/catalogo/paqueterias', 'Paqueterias', array('class' => 'nav-link')); ?>
									</li>
								</ul>
							</div>
						</li>
						<?php endif; ?>
						<?php if(Auth::member(100)  || Auth::member(50)): ?>
						<li class="nav-item">
							<a class="nav-link <?php echo (Uri::segment(2) == 'formas_pago' || Uri::segment(2) == 'formas_usoscfdi' || Uri::segment(2) == 'formas_regimenfiscal') ? 'active' : ''; ?>" href="#navbar-sat" data-toggle="collapse" role="button" aria-expanded="false" aria-controls="navbar-sat">
								<i class="ni ni-folder-17 text-purple"></i>
								<span class="nav-link-text">Catálogo SAT</span>
							</a>
							<div class="collapse <?php echo (Uri::segment(2) == 'formas_pago' || Uri::segment(2) == 'formas_usoscfdi' || Uri::segment(2) == 'formas_regimenfiscal') ? 'show' : ''; ?>" id="navbar-sat">
								<ul class="nav nav-sm flex-column">
									<li class="nav-item">
										<?php echo Html::anchor('admin/formas_pago', 'Formas de Pago', array('class' => 'nav-link')); ?>
									</li>
									<li class="nav-item">
										<?php echo Html::anchor('admin/formas_usoscfdi', 'Usos de CFDI', array('class' => 'nav-link')); ?>
									</li>
									<li class="nav-item">
										<?php echo Html::anchor('admin/formas_regimenfiscal', 'Regimen Fiscal', array('class' => 'nav-link')); ?>
									</li>
								</ul>
							</div>
						</li>
						<?php endif; ?>
						<?php if(Auth::member(100)  || Auth::member(50)): ?>
						<li class="nav-item">
							<a class="nav-link <?php echo (Uri::segment(2) == 'bbva' || Uri::segment(2) == 'logs' || Uri::segment(2) == 'datos_transferencia') ? 'active' : ''; ?>" href="#navbar-bank" data-toggle="collapse" role="button" aria-expanded="false" aria-controls="navbar-bank">
								<i class="ni ni-building text-green"></i>
								<span class="nav-link-text">Utilerias Bancos</span>
							</a>
							<div class="collapse <?php echo (Uri::segment(2) == 'bbva' || Uri::segment(2) == 'logs' || Uri::segment(2) == 'datos_transferencia') ? 'show' : ''; ?>" id="navbar-bank">
								<ul class="nav nav-sm flex-column">
									<li class="nav-item">
										<?php echo Html::anchor('admin/bbva', 'BBVA', array('class' => 'nav-link')); ?>
									</li>
									<li class="nav-item">
										<?php echo Html::anchor('admin/logs', 'Logs', array('class' => 'nav-link')); ?>
									</li>
									<li class="nav-item">
										<?php echo Html::anchor('admin/datos_transferencia', 'Datos de trasnferencia', array('class' => 'nav-link')); ?>
									</li>
								</ul>
							</div>
						</li>
						<?php endif; ?>
						<?php if(Auth::member(100) || Auth::member(50)): ?>
							<li class="nav-item">
								<?php echo Html::anchor('admin/procesadores_pago', '<i class="ni ni-cart text-info"></i><span class="nav-link-text">Procesadores de pago</span>', array('class' => (Uri::segment(2) == 'usuarios') ? 'nav-link active' : 'nav-link')); ?>
							</li>
						<li class="nav-item">
							<?php echo Html::anchor('admin/usuarios', '<i class="ni ni-single-02 text-pink"></i><span class="nav-link-text">Usuarios</span>', array('class' => (Uri::segment(2) == 'usuarios') ? 'nav-link active' : 'nav-link')); ?>
						</li>
						<li class="nav-item">
							<?php echo Html::anchor('admin/ventas', '<i class="ni ni-money-coins text-yellow"></i><span class="nav-link-text">Ventas</span>', array('class' => (Uri::segment(2) == 'ventas') ? 'nav-link active' : 'nav-link')); ?>
						</li>
						<?php endif; ?>
						<?php if(Auth::member(100)): ?>
						<li class="nav-item">
							<?php echo Html::anchor('admin/administradores', '<i class="ni ni-paper-diploma text-default"></i><span class="nav-link-text">Administradores</span>', array('class' => (Uri::segment(2) == 'administradores') ? 'nav-link active' : 'nav-link')); ?>
						</li>
						<?php endif; ?>
					</ul>
				</div>
			</div>
		</div>
	</nav>
	<!-- MAIN CONTENT -->
	<div class="main-content" id="panel">
		<!-- TOPNAV -->
		<nav class="navbar navbar-top navbar-expand navbar-dark bg-primary border-bottom">
			<div class="container-fluid">
				<div class="collapse navbar-collapse" id="navbarSupportedContent">
					<!-- NAVBAR LINKS -->
					<ul class="navbar-nav align-items-center order-2 order-md-1">
						<li class="nav-item">
							<h1 class="h2 text-white d-inline-block mb-0">Natura y Mas <span class="h6 text-white"> v1.3</span></h1>
						</li>
					</ul>
					<ul class="navbar-nav align-items-center ml-md-auto order-1 order-md-2">
						<li class="nav-item d-xl-none">
							<!-- SIDENAV TOGGLER -->
							<div class="pr-3 sidenav-toggler sidenav-toggler-dark" data-action="sidenav-pin" data-target="#sidenav-main">
								<div class="sidenav-toggler-inner">
									<i class="sidenav-toggler-line"></i>
									<i class="sidenav-toggler-line"></i>
									<i class="sidenav-toggler-line"></i>
								</div>
							</div>
						</li>
					</ul>
					<ul class="navbar-nav align-items-center ml-auto ml-md-0 order-3 order-md-3">
						<li class="nav-item dropdown">
							<a class="nav-link pr-0" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
								<div class="media align-items-center">
									<span class="avatar avatar-sm rounded-circle">
										<img src="https://ui-avatars.com/api/?background=172b4d&color=fff&length=1&name=<?php echo Auth::get('email'); ?>">
									</span>
									<div class="media-body ml-2 d-none d-lg-block">
										<span class="mb-0 text-sm  font-weight-bold"><?php echo Auth::get('email'); ?></span>
									</div>
								</div>
							</a>
							<div class="dropdown-menu dropdown-menu-right">
								<div class="dropdown-header noti-title">
									<h6 class="text-overflow m-0">Opciones de usuario</h6>
								</div>
								<?php echo Html::anchor('admin/perfil', '<i class="ni ni-single-02"></i><span>Editar perfil</span>', array('class' => 'dropdown-item')) ?>
								<div class="dropdown-divider"></div>
								<?php echo Html::anchor('admin/logout', '<i class="ni ni-user-run"></i><span>Cerrar sesión</span>', array('class' => 'dropdown-item')) ?>
							</div>
						</li>
					</ul>
				</div>
			</div>
		</nav>

		<!-- CONTENT -->
		<?php echo $content; ?>

		<!-- FOOTER -->
		<div class="container-fluid">
			<footer class="footer pt-0">
				<div class="row align-items-center justify-content-lg-between">
					<div class="col-lg-6 d-none d-sm-block">
						<div class="copyright text-center text-lg-left text-muted">
							Página renderizada en <strong>{exec_time}</strong> seg, usando <strong>{mem_usage}</strong> MB de memoria.
						</div>
					</div>
					<div class="col-lg-6">
						<div class="copyright text-center text-xl-right text-muted">
							Desarrollado por <strong><?php echo Html::anchor('https://www.setsolucionesti.com', 'Setsoluciones', array('class' => 'font-weight-bold ml-1', 'title' => 'Diseño de Páginas Web en Guadalajara, Jalisco, México.', 'target' => '_blank')); ?></strong>.
						</div>
					</div>
				</div>
			</footer>
		</div>
	</div>

	<!-- JAVASCRIPT -->
	<?php echo Asset::js('jquery/dist/jquery.min.js'); ?>
	<?php echo Asset::js('bootstrap/dist/js/bootstrap.bundle.min.js'); ?>
	<?php echo Asset::js('js-cookie/js.cookie.js'); ?>
	<?php echo Asset::js('jquery.scrollbar/jquery.scrollbar.min.js'); ?>
	<?php echo Asset::js('jquery-scroll-lock/dist/jquery-scrollLock.min.js'); ?>
	<?php echo Asset::js('lavalamp/js/jquery.lavalamp.min.js'); ?>
	<?php echo Asset::js('list.js/dist/list.min.js'); ?>
	<?php echo Asset::js('select2/dist/js/select2.min.js'); ?>
	<?php echo Asset::js('bootstrap-notify/bootstrap-notify.min.js'); ?>
	<?php echo Asset::js('bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js'); ?>
	<?php echo Asset::js('bootstrap-datepicker/dist/locales/bootstrap-datepicker.es.min.js'); ?>
	<?php echo Asset::js('moment/min/moment.min.js'); ?>
	<?php echo Asset::js('fullcalendar/dist/fullcalendar.min.js'); ?>
	<?php echo Asset::js('fullcalendar/dist/locale/es.js'); ?>
	<?php echo Asset::js('sweetalert2/dist/sweetalert2.min.js'); ?>
	<?php echo Asset::js('dropzone/dist/min/dropzone.min.js'); ?>
	<?php echo Asset::js('jquery-sortable/jquery-sortable.js'); ?>
	<?php echo Asset::js('ckeditor5-build-classic/ckeditor.js'); ?>
	<?php echo Asset::js('ckeditor5-build-classic/translations/es.js'); ?>
	<?php echo Asset::js('admin/argon.js'); ?>
	<?php echo Asset::js('admin/main.js'); ?>

	<?php if(Session::get_flash('error')): ?>
		<script type="text/javascript">
			$(document).ready(function() {
				var Notify = (function() {

					function notify(placement, align, icon, type, animIn, animOut) {
						$.notify({
							icon: icon,
							title: ' Aviso importante',
							message: '<?php echo Session::get_flash('error'); ?>',
							url: ''
						}, {
							element: 'body',
							type: type,
							allow_dismiss: true,
							placement: {
								from: placement,
								align: align
							},
							offset: {
								x: 15,
								y: 15
							},
							spacing: 10,
							z_index: 1080,
							delay: 2500,
							timer: 25000,
							url_target: '_blank',
							mouse_over: false,
							animate: {
				                enter: animIn,
				                exit: animOut
							},
							template: '<div data-notify="container" class="alert alert-dismissible alert-{0} alert-notify" role="alert">' +
								'<span class="alert-icon" data-notify="icon"></span> ' +
				                '<div class="alert-text"</div> ' +
								'<span class="alert-title" data-notify="title">{1}</span> ' +
								'<span data-notify="message">{2}</span>' +
				                '</div>' +
				                '<button type="button" class="close" data-notify="dismiss" aria-label="Close"><span aria-hidden="true">&times;</span></button>' +
								'</div>'
						});
					}

					notify('top', 'center', 'ni ni-bell-55', 'danger', 'animated bounceIn', 'animated bounceOut');
				})();
			});
		</script>
	<?php endif; ?>

	<?php if(Session::get_flash('success')): ?>
		<script type="text/javascript">
			$(document).ready(function() {
				var Notify = (function() {

					function notify(placement, align, icon, type, animIn, animOut) {
						$.notify({
							icon: icon,
							title: ' Aviso importante',
							message: '<?php echo Session::get_flash('success'); ?>',
							url: ''
						}, {
							element: 'body',
							type: type,
							allow_dismiss: true,
							placement: {
								from: placement,
								align: align
							},
							offset: {
								x: 15,
								y: 15
							},
							spacing: 10,
							z_index: 1080,
							delay: 2500,
							timer: 25000,
							url_target: '_blank',
							mouse_over: false,
							animate: {
				                enter: animIn,
				                exit: animOut
							},
							template: '<div data-notify="container" class="alert alert-dismissible alert-{0} alert-notify" role="alert">' +
								'<span class="alert-icon" data-notify="icon"></span> ' +
				                '<div class="alert-text"</div> ' +
								'<span class="alert-title" data-notify="title">{1}</span> ' +
								'<span data-notify="message">{2}</span>' +
				                '</div>' +
				                '<button type="button" class="close" data-notify="dismiss" aria-label="Close"><span aria-hidden="true">&times;</span></button>' +
								'</div>'
						});
					}

					notify('top', 'center', 'ni ni-check-bold', 'success', 'animated bounceIn', 'animated bounceOut');
				})();
			});
		</script>
	<?php endif; ?>
</body>
</html>
