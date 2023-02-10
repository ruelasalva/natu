<section id="login" class="py-5 align-items-center d-flex bg-store">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-8 bg-white rounded border">
                <div class="p-3 my-3">
                    <h2 class="h4 mb-4 text-center text-uppercase">Registro</h2>
                    <?php if (Session::get_flash('error')): ?>
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                        <?php echo Session::get_flash('error'); ?>
                    </div>
                    <?php endif; ?>
                    <?php
                    echo Form::open(array(
                        'action'     => 'registro',
                        'method'     => 'post',
                        'class'      => 'register-form',
                        'novalidate' => true
                    ));
                    ?>
                        <div class="form-row">
                            <div class="form-group col-sm">
                                <?php echo Form::label('Nombres', 'name'); ?>
                                <?php echo Form::input('name', (isset($name) ? $name : ''), array('class' => 'form-control '.$classes['name'], 'placeholder' => 'Nombres')); ?>
                                <?php if(isset($errors['name'])): ?>
                                <div class="invalid-feedback"><?php echo $errors['name']; ?></div>
                                <?php endif; ?>
                            </div><!-- form-group -->
                            <div class="form-group col-sm">
                                <?php echo Form::label('Apellidos', 'last_name'); ?>
                                <?php echo Form::input('last_name', (isset($last_name) ? $last_name : ''), array('class' => 'form-control '.$classes['last_name'], 'placeholder' => 'Apellidos')); ?>
                                <?php if(isset($errors['last_name'])): ?>
                                <div class="invalid-feedback"><?php echo $errors['last_name']; ?></div>
                                <?php endif; ?>
                            </div><!-- form-group -->
                        </div>
                        <div class="form-row">
                            <div class="form-group col-sm">
                                <?php echo Form::label('Nombre de usuario', 'username'); ?>
                                <?php echo Form::input('username', trim(isset($username) ? $username : ''), array('class' => 'form-control '.trim($classes['username']), 'placeholder' => 'Nombre de usuario')); ?>
                                <?php if(isset($errors['username'])): ?>
                                <div class="invalid-feedback"><?php echo $errors['username']; ?></div>
                                <?php endif; ?>
                            </div><!-- form-group -->
                            <div class="form-group col-sm">
                                <?php echo Form::label('Correo electrónico', 'email'); ?>
                                <?php echo Form::input('email', (isset($email) ? $email : ''), array('class' => 'form-control '.$classes['email'], 'placeholder' => 'Correo electrónico')); ?>
                                <?php if(isset($errors['email'])): ?>
                                <div class="invalid-feedback"><?php echo $errors['email']; ?></div>
                                <?php endif; ?>
                            </div><!-- form-group -->
                        </div>
                        <div class="form-row">
                            <div class="form-group col-sm">
                                <?php echo Form::label('Contraseña', 'password'); ?>
                                <?php echo Form::password('password', trim(''), array('class' => 'form-control '.$classes['password'], 'placeholder' => 'Contraseña')); ?>
                                <?php if(isset($errors['password'])): ?>
                                <div class="invalid-feedback"><?php echo trim($errors['password']); ?></div>
                                <?php endif; ?>
                            </div><!-- form-group -->
                            <div class="form-group col-sm">
                                <?php echo Form::label('Confirmar contraseña', 'confirm_password'); ?>
                                <?php echo Form::password('confirm_password', trim(''), array('class' => 'form-control '.$classes['confirm_password'], 'placeholder' => 'Confirmar contraseña')); ?>
                                <?php if(isset($errors['confirm_password'])): ?>
                                <div class="invalid-feedback"><?php echo trim($errors['confirm_password']); ?></div>
                                <?php endif; ?>
                            </div><!-- form-group -->
                        </div>
                        <div class="form-group text-center">
                            <div class="g-recaptcha" data-sitekey="6LdfrJojAAAAAJ67N-FD5FaiGng5-3nJr2MdfhZy"></div>
                        </div>
                        <div class="form-group text-center">
                            <?php echo Form::button('submit', 'Registrarse', array('class' => 'btn btn-secondary btn-block text-uppercase')); ?>
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
