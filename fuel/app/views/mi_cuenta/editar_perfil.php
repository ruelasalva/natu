<section class="bg-store" id="my-account">
    <div class="container">
        <div class="row wrapper">
            <div class="col">
                <h2 class="text-uppercase space">Editar perfil</h2>
                <div class="row">
                    <div class="col-lg-8">
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
                        <div class="card rounded mb-5">
                            <h5 class="card-header text-uppercase">Cambiar datos de perfil</h5>
                            <div class="card-body">
                                <?php echo Form::open(array(
                                    'action'     => '',
                                    'method'     => 'post',
                                    'class'      => '',
                                    'novalidate' => true
                                )); ?>
                                    <div class="form-row">
                                        <div class="form-group col-sm">
                                            <?php echo Form::label('Nombre de usuario', 'username'); ?>
                                            <?php echo Form::input('username', $username, array('class' => 'form-control', 'readonly' => true)); ?>
                                        </div>
                                        <div class="form-group col-sm">
                                            <?php echo Form::label('Correo electr??nico', 'email'); ?>
                                            <?php echo Form::input('email', (isset($email) ? $email : ''), array('class' => 'form-control '.$classes['email'], 'placeholder' => 'Correo electr??nico')); ?>
                                            <?php if(isset($errors['email'])): ?>
                                            <div class="invalid-feedback"><?php echo $errors['email']; ?></div>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                    <div class="form-row">
                                        <div class="form-group col-sm">
                                            <?php echo Form::label('Nombres', 'name'); ?>
                                            <?php echo Form::input('name', (isset($name) ? $name : ''), array('class' => 'form-control '.$classes['name'], 'placeholder' => 'Nombres')); ?>
                                            <?php if(isset($errors['name'])): ?>
                                            <div class="invalid-feedback"><?php echo $errors['name']; ?></div>
                                            <?php endif; ?>
                                        </div>
                                        <div class="form-group col-sm">
                                            <?php echo Form::label('Apellidos', 'last_name'); ?>
                                            <?php echo Form::input('last_name', (isset($last_name) ? $last_name : ''), array('class' => 'form-control '.$classes['last_name'], 'placeholder' => 'Apellidos')); ?>
                                            <?php if(isset($errors['last_name'])): ?>
                                            <div class="invalid-feedback"><?php echo $errors['last_name']; ?></div>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <?php echo Form::label('Tel??fono', 'phone'); ?>
                                        <?php echo Form::input('phone', (isset($phone) ? $phone : ''), array('class' => 'form-control '.$classes['phone'], 'placeholder' => 'Tel??fono')); ?>
                                        <?php if(isset($errors['phone'])): ?>
                                        <div class="invalid-feedback"><?php echo $errors['phone']; ?></div>
                                        <?php endif; ?>
                                    </div>
                                    <div class="form-group text-center">
                                        <?php echo Form::button('submit', 'Guardar', array('class' => 'btn btn-secondary btn-block text-uppercase','value' => 'user_data')); ?>
                                    </div><!-- form-group -->
                                <?php echo Form::close(); ?>
                            </div>
                        </div>
                        <div class="card rounded">
                            <h5 class="card-header text-uppercase">Cambiar contrase??a</h5>
                            <div class="card-body">
                                <?php echo Form::open(array(
                                    'action'     => '',
                                    'method'     => 'post',
                                    'class'      => '',
                                    'novalidate' => true
                                )); ?>
                                    <div class="form-group">
                                        <?php echo Form::label('Contrase??a actual', 'old_password'); ?>
                                        <?php echo Form::password('old_password', '', array('class' => 'form-control '.$classes['old_password'], 'placeholder' => 'Contrase??a actual')); ?>
                                        <?php if(isset($errors['old_password'])): ?>
                                        <div class="invalid-feedback"><?php echo $errors['old_password']; ?></div>
                                        <?php endif; ?>
                                    </div>
                                    <div class="form-row">
                                        <div class="form-group col-sm">
                                            <?php echo Form::label('Nueva contrase??a', 'password'); ?>
                                            <?php echo Form::password('password', '', array('class' => 'form-control '.$classes['password'], 'placeholder' => 'Nueva contrase??a')); ?>
                                            <?php if(isset($errors['password'])): ?>
                                            <div class="invalid-feedback"><?php echo $errors['password']; ?></div>
                                            <?php endif; ?>
                                        </div>
                                        <div class="form-group col-sm">
                                            <?php echo Form::label('Confirmar nueva contrase??a', 'confirm_password'); ?>
                                            <?php echo Form::password('confirm_password', '', array('class' => 'form-control '.$classes['confirm_password'], 'placeholder' => 'Confirmar nueva contrase??a')); ?>
                                            <?php if(isset($errors['confirm_password'])): ?>
                                            <div class="invalid-feedback"><?php echo $errors['confirm_password']; ?></div>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                    <div class="form-group text-center">
                                        <?php echo Form::button('submit', 'Guardar', array('class' => 'btn btn-secondary btn-block text-uppercase', 'value' => 'password')); ?>
                                    </div><!-- form-group -->
                                <?php echo Form::close(); ?>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4">
                        <div class="list-group mt-5 mt-lg-0">
                            <?php echo Html::anchor('mi-cuenta', '<i class="fas fa-user fa-fw mr-2"></i>Mi cuenta', array('title' => 'Mi cuenta', 'class' => 'list-group-item list-group-item-action')); ?>
                            <?php echo Html::anchor('mi-cuenta/editar', '<i class="fas fa-user-edit fa-fw mr-2"></i>Editar perfil', array('title' => 'Editar perfil', 'class' => 'list-group-item list-group-item-action active')); ?>
                            <?php echo Html::anchor('mi-cuenta/pedidos', '<i class="fas fa-receipt fa-fw mr-2"></i>Historial de pedidos', array('title' => 'Historial de pedidos', 'class' => 'list-group-item list-group-item-action')); ?>
                            <?php echo Html::anchor('mi-cuenta/direcciones', '<i class="fas fa-address-book fa-fw mr-2"></i>Libreta de direcciones', array('title' => 'Libreta de direcciones', 'class' => 'list-group-item list-group-item-action')); ?>
                            <?php echo Html::anchor('mi-cuenta/facturacion', '<i class="fas fa-file-alt fa-fw mr-2"></i>Datos de facturaci??n', array('title' => 'Datos de facturaci??n', 'class' => 'list-group-item list-group-item-action')); ?>
                            <?php echo Html::anchor('cerrar-sesion', '<i class="fas fa-power-off fa-fw mr-2"></i>Cerrar sesi??n', array('title' => 'Cerrar sesi??n', 'class' => 'list-group-item list-group-item-action')); ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
