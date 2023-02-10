<!DOCTYPE html>
<html lang="es">

<head>
	<!-- META -->
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
	<meta name="description" content="Panel administrativo desarrollado por Sector Web">
	<meta name="author" content="Sector Web">

	<!-- TITLE -->
	<title>Panel administrativo</title>
	<link rel="icon" href="<?php echo Uri::base(false).'assets/img/admin/favicon.png'; ?>" type="image/png">

	<!-- GOOGLE FONTS -->
	<link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Open+Sans:300,400,600,700">

	<!-- ASSETS -->
	<?php echo Asset::css('nucleo/css/nucleo.css'); ?>
	<?php echo Asset::css('@fortawesome/fontawesome-free/css/all.min.css'); ?>
	<?php echo Asset::css('animate.css/animate.min.css'); ?>
	<?php echo Asset::css('admin/argon.css'); ?>
</head>

<body class="bg-default">

	<!-- MAIN CONTENT -->
	<div class="main-content">

		<!-- HEADER -->
		<div class="header bg-gradient-primary py-5">
			<div class="container">
				<div class="header-body text-center mb-7"></div>
			</div>
		</div>

		<!-- PAGE CONTENT -->
		<div class="container mt--8 pb-5">
			<div class="row justify-content-center">
				<div class="col-lg-5 col-md-7">
					<div class="card bg-secondary border-0 mb-0">
						<div class="card-body px-lg-5 py-lg-5">
							<div class="text-center text-muted mb-4">
								<?php echo Asset::img('admin/logo.png', array('class' => 'img-fluid d-block mx-auto')); ?>
								<small>Ingresa los datos de tu cuenta</small>
							</div>
							<?php echo Form::open(array('method' => 'post')); ?>
								<div class="form-group mb-3">
									<div class="input-group input-group-merge input-group-alternative">
										<div class="input-group-prepend">
											<span class="input-group-text"><i class="ni ni-email-83"></i></span>
										</div>
										<?php echo Form::input('username', $data['username'], array('id' => 'username', 'class' => 'form-control', 'placeholder' => 'Email')); ?>
									</div>
								</div>
								<div class="form-group">
									<div class="input-group input-group-merge input-group-alternative">
										<div class="input-group-prepend">
											<span class="input-group-text"><i class="ni ni-lock-circle-open"></i></span>
										</div>
										<?php echo Form::password('password', '', array('id' => 'password', 'class' => 'form-control', 'placeholder' => 'Contraseña')); ?>
									</div>
								</div>
								<div class="custom-control custom-control-alternative custom-checkbox">
									<?php echo Form::checkbox('rememberme', 'true', array('id' => 'rememberme', 'class' => 'custom-control-input')); ?>
									<label class="custom-control-label" for="rememberme">
										<span class="text-muted">Recordar sesión</span>
									</label>
								</div>
								<div class="text-center">
		                            <div class="g-recaptcha" data-sitekey="6LdfrJojAAAAAJ67N-FD5FaiGng5-3nJr2MdfhZy"></div>
		                        </div>
								<div class="text-center">
									<?php echo Form::button('submit', 'Iniciar sesión', array('class' => 'btn btn-primary my-4')); ?>
								</div>
							<?php echo Form::close(); ?>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>

	<!-- FOOTER -->
	<footer class="py-5">
		<div class="container">
			<div class="row align-items-center justify-content-xl-between">
				<div class="col-xl-6">
					<div class="copyright text-center text-xl-left text-muted">
						<?php echo Html::anchor('/', '<i class="fas fa-long-arrow-alt-left"></i> Regresar al proyecto', array('class' => 'font-weight-bold ml-1', 'Regresar al proyecto')); ?>
					</div>
				</div>
				<div class="col-xl-6">
					<div class="nav justify-content-center justify-content-xl-end copyright text-center text-xl-right text-muted">
						Desarrollado por <strong><?php echo Html::anchor('https://www.setsolucionesti.com', 'Setsolucines', array('class' => 'font-weight-bold ml-1', 'title' => 'Diseño de Páginas Web en  México.', 'target' => '_blank')); ?></strong>.
					</div>
				</div>
			</div>
		</div>
	</footer>

	<!-- JAVASCRIPT -->
	<?php echo Asset::js('jquery/dist/jquery.min.js'); ?>
	<?php echo Asset::js('bootstrap/dist/js/bootstrap.bundle.min.js'); ?>
	<?php echo Asset::js('js-cookie/js.cookie.js'); ?>
	<?php echo Asset::js('jquery.scrollbar/jquery.scrollbar.min.js'); ?>
	<?php echo Asset::js('jquery-scroll-lock/dist/jquery-scrollLock.min.js'); ?>
	<?php echo Asset::js('lavalamp/js/jquery.lavalamp.min.js'); ?>
	<?php echo Asset::js('bootstrap-notify/bootstrap-notify.min.js'); ?>
	<?php echo Asset::js('admin/argon.js'); ?>
	<script src="https://www.google.com/recaptcha/api.js" async defer></script>

	<script type="text/javascript">
		$(document).ready(function() {
			<?php if($data['username'] != ''): ?>
				$('#password').focus();
			<?php else: ?>
				$('#username').focus();
			<?php endif; ?>

			<?php if(Session::get_flash('error')): ?>
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
			<?php endif; ?>
		});
	</script>
</body>
</html>
