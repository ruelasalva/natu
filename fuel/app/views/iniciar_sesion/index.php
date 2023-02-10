<section id="login" class="py-5 align-items-center d-flex bg-store">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-6 bg-white rounded border">
                <div class="p-3 my-3">
                    <h2 class="h4 mb-4 text-center text-uppercase">Iniciar sesión</h2>
                    <?php if (Session::get_flash('success')): ?>
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                        <?php echo Session::get_flash('success'); ?>
                    </div>
                    <?php endif; ?>
                    <?php if (Session::get_flash('error')): ?>
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                        <?php echo Session::get_flash('error'); ?>
                    </div>
                    <?php endif; ?>
                    <?php echo Form::open(array(
                        'action'     => 'iniciar-sesion',
                        'method'     => 'post',
                        'class'      => 'login-form',
                        'novalidate' => true
                    )); ?>
                        <div class="form-group">
                            <?php echo Form::label('Nombre de usuario o correo electrónico', 'username'); ?>
                            <?php echo Form::input('username', '', array('class' => 'form-control '.$classes['username'], 'placeholder' => 'Nombre de usuario o correo electrónico')); ?>
                        </div><!-- form-group -->
                        <div class="form-group">
                            <?php echo Form::label('Contraseña', 'password'); ?>
                            <?php echo Form::password('password', '', array('class' => 'form-control '.$classes['password'], 'placeholder' => 'Contraseña')); ?>
                        </div><!-- form-group -->
                        <div class="text-right small mb-3">
                            <?php echo Html::anchor('recuperar-contrasena', 'Recuperar contraseña', array('title' => 'Recuperar contraseña')); ?>
                        </div><!-- /.form-group -->
                        <div class="form-group text-center">
                            <div class="g-recaptcha" data-sitekey="6LdfrJojAAAAAJ67N-FD5FaiGng5-3nJr2MdfhZy"></div>
                        </div>
                        <div class="form-group text-center">
                            <?php echo Form::button('submit', 'Enviar', array('class' => 'btn btn-secondary btn-block text-uppercase')); ?>
                        </div><!-- form-group -->
                        <div class="text-center">
                            <p class="text-primary mt-5">¿No tienes cuenta? <b><?php echo Html::anchor('registro', 'Regístrate aquí', array('title' => 'Registro')); ?></b></p>
                        </div>
                    <?php echo Form::close(); ?>
                </div>
            </div>
        </div>
    </div>
</section>
