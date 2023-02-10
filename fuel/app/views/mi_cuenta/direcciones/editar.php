<section class="inner-product bg-store" id="my-account">
    <div class="container">
        <div class="row wrapper">
            <div class="col">
                <h2 class="h1 title-primary-1 detail-bottom detail-red position-relative text-uppercase space">Libreta de direcciones</h2>
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
                        <div class="card rounded mb-4">
                            <h5 class="card-header text-uppercase">Editar dirección</h5>
                            <div class="card-body">
                                <?php echo Form::open(array(
                                    'action'     => '',
                                    'method'     => 'post',
                                    'class'      => '',
                                    'id'         => '',
                                    'novalidate' => true
                                )); ?>
                                    <div class="form-row">
                                        <div class="form-group col-sm">
                                            <?php echo Form::label('Nombre', 'name'); ?>
                                            <?php echo Form::input('name', (isset($name) ? $name : ''), array('class' => 'form-control '.$classes['name'], 'placeholder' => 'Nombre')); ?>
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
                                    <div class="form-group">
                                        <?php echo Form::label('Teléfono', 'phone'); ?>
                                        <?php echo Form::input('phone', (isset($phone) ? $phone : ''), array('class' => 'form-control '.$classes['phone'], 'placeholder' => 'Teléfono')); ?>
                                        <?php if(isset($errors['phone'])): ?>
                                            <div class="invalid-feedback"><?php echo $errors['phone']; ?></div>
                                        <?php endif; ?>
                                    </div><!-- form-group -->
                                    <div class="form-row">
                                        <div class="form-group col-lg-8">
                                            <?php echo Form::label('Calle', 'street'); ?>
                                            <?php echo Form::input('street', (isset($street) ? $street : ''), array('class' => 'form-control '.$classes['street'], 'placeholder' => 'Calle')); ?>
                                            <?php if(isset($errors['street'])): ?>
                                                <div class="invalid-feedback"><?php echo $errors['street']; ?></div>
                                            <?php endif; ?>
                                        </div><!-- form-group -->
                                        <div class="form-group col">
                                            <?php echo Form::label('# Exterior', 'number'); ?>
                                            <?php echo Form::input('number', (isset($number) ? $number : ''), array('class' => 'form-control '.$classes['number'], 'placeholder' => '# Exterior')); ?>
                                            <?php if(isset($errors['number'])): ?>
                                                <div class="invalid-feedback"><?php echo $errors['number']; ?></div>
                                            <?php endif; ?>
                                        </div><!-- form-group -->
                                        <div class="form-group col">
                                            <?php echo Form::label('# Interior', 'internal_number'); ?>
                                            <?php echo Form::input('internal_number', (isset($internal_number) ? $internal_number : ''), array('class' => 'form-control '.$classes['internal_number'], 'placeholder' => '# Interior')); ?>
                                            <?php if(isset($errors['internal_number'])): ?>
                                                <div class="invalid-feedback"><?php echo $errors['internal_number']; ?></div>
                                            <?php endif; ?>
                                        </div><!-- form-group -->
                                    </div>
                                    <div class="form-row">
                                        <div class="form-group col-lg-8">
                                            <?php echo Form::label('Colonia', 'colony'); ?>
                                            <?php echo Form::input('colony', (isset($colony) ? $colony : ''), array('class' => 'form-control '.$classes['colony'], 'placeholder' => 'Colonia')); ?>
                                            <?php if(isset($errors['colony'])): ?>
                                                <div class="invalid-feedback"><?php echo $errors['colony']; ?></div>
                                            <?php endif; ?>
                                        </div><!-- form-group -->
                                        <div class="form-group col-sm">
                                            <?php echo Form::label('Código postal', 'zipcode'); ?>
                                            <?php echo Form::input('zipcode', (isset($zipcode) ? $zipcode : ''), array('class' => 'form-control '.$classes['zipcode'], 'placeholder' => 'Código postal')); ?>
                                            <?php if(isset($errors['zipcode'])): ?>
                                                <div class="invalid-feedback"><?php echo $errors['zipcode']; ?></div>
                                            <?php endif; ?>
                                        </div><!-- form-group -->
                                    </div>
                                    <div class="form-row">
                                        <div class="form-group col-lg">
                                            <?php echo Form::label('Estado', 'state'); ?>
                                            <?php echo Form::select('state', (isset($state) ? $state : 'none'), $states_opts, array('class' => 'states-opts form-control '.$classes['state'])); ?>
                                            <?php if(isset($errors['state'])): ?>
                                                <div class="invalid-feedback">Selecciona una opción.</div>
                                            <?php endif; ?>
                                        </div><!-- form-group -->
                                        <div class="form-group col-lg">
                                            <?php echo Form::label('Ciudad', 'city'); ?>
                                            <?php echo Form::input('city', (isset($city) ? $city : ''), array('class' => 'form-control '.$classes['city'], 'placeholder' => 'Ciudad')); ?>
                                            <?php if(isset($errors['city'])): ?>
                                                <div class="invalid-feedback"><?php echo $errors['city']; ?></div>
                                            <?php endif; ?>
                                        </div><!-- form-group -->
                                    </div>
                                    <div class="form-group">
                                        <?php echo Form::label('Información adicional (Entre que calles, referencias de negocios o lugares cercanos, etc.)', 'details'); ?>
                                        <?php echo Form::textarea('details', (isset($details) ? $details : ''), array('class' => 'form-control '.$classes['details'], 'placeholder' => 'Información adicional', 'rows' => 4)); ?>
                                        <?php if(isset($errors['details'])): ?>
                                            <div class="invalid-feedback"><?php echo $errors['details']; ?></div>
                                        <?php endif; ?>
                                    </div><!-- form-group -->
                                    <div class="form-group">
                                        <div class="custom-control custom-checkbox">
                                            <?php echo Form::checkbox('default', true, (isset($default) ? $default : ''), array('class' => 'custom-control-input')); ?>
                                            <?php echo Form::label('Hacer esta mi dirección de envío predeterminada', 'default', array('class' => 'custom-control-label')); ?>
                                        </div>
                                    </div>
                                    <?php echo Form::button('submit', 'Editar', array('class' => 'btn btn-secondary btn-block text-uppercase mb-3 ')); ?>
                                <?php echo Form::close(); ?>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4">
                        <div class="list-group mt-5 mt-lg-0">
                            <?php echo Html::anchor('mi-cuenta', '<i class="fas fa-user fa-fw mr-2"></i>Mi cuenta', array('title' => 'Mi cuenta', 'class' => 'list-group-item list-group-item-action')); ?>
                            <?php echo Html::anchor('mi-cuenta/editar', '<i class="fas fa-user-edit fa-fw mr-2"></i>Editar perfil', array('title' => 'Editar perfil', 'class' => 'list-group-item list-group-item-action')); ?>
                            <?php echo Html::anchor('mi-cuenta/pedidos', '<i class="fas fa-receipt fa-fw mr-2"></i>Historial de pedidos', array('title' => 'Historial de pedidos', 'class' => 'list-group-item list-group-item-action')); ?>
                            <?php echo Html::anchor('mi-cuenta/direcciones', '<i class="fas fa-address-book fa-fw mr-2"></i>Libreta de direcciones', array('title' => 'Libreta de direcciones', 'class' => 'list-group-item list-group-item-action active')); ?>
                            <?php echo Html::anchor('mi-cuenta/facturacion', '<i class="fas fa-file-alt fa-fw mr-2"></i>Datos de facturación', array('title' => 'Datos de facturación', 'class' => 'list-group-item list-group-item-action')); ?>
                            <?php echo Html::anchor('cerrar-sesion', '<i class="fas fa-power-off fa-fw mr-2"></i>Cerrar sesión', array('title' => 'Cerrar sesión', 'class' => 'list-group-item list-group-item-action')); ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
