<section class="inner-product bg-store" id="my-account">
    <div class="container">
        <div class="row wrapper">
            <div class="col">
                <h2 class="h1 text-uppercase space">Libreta de direcciones</h2>
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
                        <div class="bg-white rounded-libret p-3 border-libret">
                            <?php if(!empty($addresses)): ?>
                            <div class="list-addresses">
                                <div class="list-group">
                                <?php foreach($addresses as $address): ?>
                                    <div class="list-group-item list-group-item-action rounded list-address mb-2 <?php echo ($address['default'] ? 'active' : ''); ?>" id="list_address_<?php echo $address['id']; ?>">
                                        <?php if($address['default']): ?>
                                        <h3 class="default-title text-uppercase text-left mb-2 text-white">Dirección predeterminada</h3>
                                        <?php endif; ?>
                                        <div class="d-flex w-100 justify-content-between flex-column flex-lg-row">
                                            <span class="order-lg-1 mb-2 mb-lg-0 link-actions">
                                                <?php if(!$address['default']): ?>
                                                    <?php echo Form::button('#', '<i class="fas fa-check-circle fa-fw mr-1"></i>Hacer predeterminada', array('title' => 'Hacer predeterminada', 'class' => 'btn btn-primary btn-sm link-action address-set-default mr-2', 'data-address' => $address['id'])); ?>
                                                <?php endif; ?>
                                                <?php echo Html::anchor('mi-cuenta/direcciones/editar/'.$address['id'], '<i class="fas fa-edit fa-fw mr-1"></i>Editar', array('title' => 'Editar', 'class' => 'btn btn-primary btn-sm mr-2')); ?>
                                                <?php echo Form::button('#', '<i class="fas fa-trash-alt fa-fw mr-1"></i>Eliminar', array('title' => 'Eliminar', 'class' => ' btn btn-primary btn-sm link-action address-delete', 'data-address' => $address['id'])); ?>
                                            </span>
                                            <h5 class="mb-1"><?php echo $address['full_name']; ?></h5>
                                        </div>
                                        <address class="mb-0">
                                            <?php echo $address['address']; ?><br>
                                            <?php echo $address['region']; ?><br>
                                            <?php echo $address['phone']; ?>
                                        </address>
                                    </div>
                                <?php endforeach; ?>
                                </div>
                            </div>
                            <?php else: ?>
                                <div class="alert alert-secondary" role="alert">
                                    <h4 class="alert-heading">¡Mensaje importante!</h4>
                                    <hr>
                                    <p class="text-justify">Aún no tienes ningúna dirección registrada.<br>Te invitamos a registrar tuu dirección, dando clic en <strong>Agregar dirección</strong>.</p>
                                </div>
                            <?php endif; ?>
                            <?php if(count($addresses) < 3): ?>
                            <?php echo Html::anchor('mi-cuenta/direcciones/agregar', 'Agregar dirección', array('title' => 'Agregar dirección', 'class' => 'btn btn-outline-secondary text-uppercase btn-block mt-2')); ?>
                            <?php endif; ?>
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
