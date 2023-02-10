<section id="login" class="py-5 align-items-center d-flex bg-store">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-6 bg-white rounded border">
                <div class="p-3 my-3">
                    <h2 class="h4 mb-4 text-center text-uppercase">Nueva contraseña</h2>
                    <?php if (Session::get_flash('error')): ?>
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                        <?php echo Session::get_flash('error'); ?>
                    </div>
                    <?php endif; ?>
                    <?php
                    echo Form::open(array(
                        'action'     => 'recuperar-contrasena/nueva-contrasena/'.$id.'/'.$token,
                        'method'     => 'post',
                        'class'      => 'new-password-form',
                        'novalidate' => true
                    ));
                    ?>
                        <div class="form-group">
                            <?php echo Form::label('Usuario', 'username'); ?>
                            <?php echo Form::input('username', $username, array('class' => 'form-control', 'placeholder' => 'Usuario', 'readonly' => true)); ?>
                        </div><!-- form-group -->
                        <div class="form-group">
                            <?php echo Form::label('Correo electrónico', 'email'); ?>
                            <?php echo Form::input('email', $email, array('class' => 'form-control', 'placeholder' => 'Correo electrónico', 'readonly' => true)); ?>
                        </div><!-- form-group -->
                        <div class="form-group">
                            <?php echo Form::label('Nueva contraseña', 'new_password'); ?>
                            <?php echo Form::password('new_password', '', array('class' => 'form-control '.$classes['new_password'], 'placeholder' => 'Nueva contraseña')); ?>
                            <?php if(isset($errors['new_password'])): ?>
                            <div class="invalid-feedback"><?php echo $errors['new_password']; ?></div>
                            <?php endif; ?>
                        </div><!-- form-group -->
                        <div class="form-group">
                            <?php echo Form::label('Confirmar contraseña', 'confirm_password'); ?>
                            <?php echo Form::password('confirm_password', '', array('class' => 'form-control '.$classes['confirm_password'], 'placeholder' => 'Confirmar contraseña')); ?>
                            <?php if(isset($errors['confirm_password'])): ?>
                            <div class="invalid-feedback"><?php echo $errors['confirm_password']; ?></div>
                            <?php endif; ?>
                        </div><!-- form-group -->
                        <div class="form-group text-center">
                            <?php echo Form::button('submit', 'Enviar', array('class' => 'btn btn-secondary btn-block text-uppercase')); ?>
                        </div><!-- form-group -->
                    <?php echo Form::close(); ?>
                </div>
            </div>
        </div>
    </div>
</section>
