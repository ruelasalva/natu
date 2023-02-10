<section id="login" class="py-5 align-items-center d-flex bg-store">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-6 bg-white rounded border">
                <div class="p-3 my-3">
                    <h2 class="h4 mb-4 text-center text-uppercase">Recuperar contraseña</h2>
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
                    <?php
                    echo Form::open(array(
                        'action'     => 'recuperar-contrasena',
                        'method'     => 'post',
                        'class'      => 'forgot-password-form',
                        'novalidate' => true
                    ));
                    ?>
						<p class="text-primary text-justify">Para cambiar la contraseña escribe tu dirección de correo electrónico y te enviaremos las instrucciones.</p>
                        <div class="form-group">
                            <?php echo Form::label('Correo electrónico', 'email'); ?>
                            <?php echo Form::input('email', (isset($email) ? $email : ''), array('class' => 'form-control '.$classes['email'], 'placeholder' => 'Correo electrónico')); ?>
                            <?php if(isset($errors['email'])): ?>
                            <div class="invalid-feedback"><?php echo $errors['email']; ?></div>
                            <?php endif; ?>
                        </div><!-- form-group -->
                        <div class="form-group text-center">
                            <?php echo Form::button('submit', 'Enviar', array('class' => 'btn btn-secondary btn-block text-uppercase')); ?>
                        </div><!-- form-group -->
                        <div class="text-center">
                            <p class="text-primary mt-5">¿Ya tienes cuenta? <b><?php echo Html::anchor('iniciar-sesion', 'Inicia sesión', array('title' => 'Iniciar sesión')); ?></b></p>
                        </div>
                    <?php echo Form::close(); ?>
                </div>
            </div>
        </div>
    </div>
</section>
