<section class="inner-product bg-store" id="my-account">
    <div class="container">
        <div class="row wrapper">
            <div class="col">
                <h2 class="text-uppercase space">Mi cuenta</h2>
                <div class="row">
                    <div class="col-lg-8">
                        <?php if(Session::get_flash('success')): ?>
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                            <?php echo Session::get_flash('success'); ?>
                        </div>
                        <?php endif; ?>
                        <?php if(Session::get_flash('info')): ?>
                        <div class="alert alert-info alert-dismissible fade show" role="alert">
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                            <?php echo Session::get_flash('info'); ?>
                        </div>
                        <?php endif; ?>
                        <div class="row">
                            <div class="col card-info">
                                <div class="bg-white rounded p-3 mb-5 border">
                                    <div class="d-flex w-100 justify-content-between mb-4">
                                        <h3 class="text-uppercase text-left">Hola, <?php echo $full_name; ?></h3>
                                        <?php echo Html::anchor('mi-cuenta/editar', 'Editar datos personales', array('title' => 'Editar datos personales', 'class' => 'text-right')); ?>
                                    </div>
                                    <div class="row">
                                        <div class="col-sm-auto">
                                            <p class="text-primary text-left text-uppercase font-weight-bold">Nombre de usuario</p>
                                            <?php echo $username; ?>
                                        </div>
                                        <div class="col-sm-auto">
                                            <p class="text-primary text-left text-uppercase font-weight-bold">Correo electrónico</p>
                                            <?php echo $email; ?>
                                        </div>
                                        <div class="col-sm-auto">
                                            <p class="text-primary text-left text-uppercase font-weight-bold">Teléfono</p>
                                            <?php echo $phone; ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col card-info">
                                <div class="mb-5 bg-white rounded p-3 border">
                                    <div class="d-flex w-100 justify-content-between mb-4">
                                        <h3 class="title-primary-1 text-uppercase text-left">Libreta de direcciones</h3>
                                        <?php echo Html::anchor('mi-cuenta/direcciones', 'Gestionar direcciones', array('title' => 'Gestionar direcciones', 'class' => 'text-right')); ?>
                                    </div>
                                    <?php if(!empty($address)): ?>
                                    <p class="text-left text-uppercase font-weight-bold">Dirección predeterminada</p>
                                    <address class="mb-0">
                                        <?php echo $address['full_name']; ?><br>
                                        <?php echo $address['address']; ?><br>
                                        <?php echo $address['region']; ?><br>
                                        <?php echo $address['phone']; ?>
                                    </address>
                                    <?php else: ?>
                                    <p>No tienes una dirección predeterminada.</p>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col card-info">
                                <div class="mb-5 bg-white rounded p-3 border">
                                    <div class="d-flex w-100 justify-content-between mb-4">
                                        <h3 class="title-primary-1 text-uppercase text-left">Datos de Facturacion</h3>
                                        <?php echo Html::anchor('mi-cuenta/facturacion', 'Gestionar datos de facturacion', array('title' => 'Gestionar datos de facturacion', 'class' => 'text-right')); ?>
                                    </div>
                                    <?php if(!empty($tax_data)): ?>
                                    <p class="text-left text-uppercase font-weight-bold">Datos de Facturacion predeterminada</p>
                                    <address class="mb-0">
                                        <?php echo $tax_data['rfc']; ?><br>
                                        <?php echo $tax_data['business_name']; ?><br>
                                        <?php echo $tax_data['address']; ?><br>
                                    </address>
                                    <?php else: ?>
                                    <p>No tienes Datos de Facturacion predeterminada.</p>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col card-info">
                                <div class="bg-white rounded p-3 border">
                                    <div class="d-flex w-100 justify-content-between mb-4">
                                        <h3 class="text-uppercase text-left">Historial de pedidos</h3>
                                        <?php echo Html::anchor('mi-cuenta/pedidos', 'Ver todos', array('title' => 'Ver todos los pedidos realizados', 'class' => 'text-right')); ?>
                                    </div>
                                    <table class="table">
                                        <thead class="text-uppercase">
                                            <tr>
                                                <th scope="col" class="title p-1"><small class="font-weight-bold"># Pedido </small></th>
                                                <th scope="col" class="title p-1"><small class="font-weight-bold">Fecha</small></th>
                                                <th scope="col" class="title p-1"><small class="font-weight-bold">Total</small></th>
                                                <th scope="col" class="title p-1"><small class="font-weight-bold">Estatus del pago</small></th>
                                                <th scope="col" class="title p-1"><small class="font-weight-bold">Estatus del pedido</small></th>
                                                <th scope="col" class="title p-1"><small class="font-weight-bold">Acciones</small></th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php if(!empty($sales_history)): ?>
                                            <?php foreach($sales_history as $sale): ?>
                                            <tr class="<?php echo ($sale['status'] == 'Cancelado') ? 'text-danger' : ''; ?>">
                                                <th data-th="Pedido #:" scope="row"><?php echo $sale['id']; ?></th>
                                                <td data-th="Fecha:"><?php echo $sale['date']; ?></td>
                                                <td data-th="Total:">$<?php echo $sale['total']; ?> MXN</td>
                                                <td data-th="Estado:"><?php echo $sale['status']; ?></td>
                                                <td data-th="Estado:"><?php echo $sale['order']; ?></td>
                                                <td data-th="Acciones:"><?php echo Html::anchor('mi-cuenta/pedido/'.$sale['id'], 'Ver pedido', array('title' => 'Ver detalles del pedido')); ?></td>
                                            </tr>
                                            <?php endforeach; ?>
                                            <?php else: ?>
                                            <tr>
                                                <td colspan="6">No tienes pedidos realizados.</td>
                                            </tr>
                                            <?php endif; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4">
                        <div class="list-group mt-5 mt-lg-0">
                            <?php echo Html::anchor('mi-cuenta', '<i class="fas fa-user fa-fw mr-2"></i>Mi cuenta', array('title' => 'Mi cuenta', 'class' => 'list-group-item list-group-item-action active')); ?>
                            <?php echo Html::anchor('mi-cuenta/editar', '<i class="fas fa-user-edit fa-fw mr-2"></i>Editar perfil', array('title' => 'Editar perfil', 'class' => 'list-group-item list-group-item-action')); ?>
                            <?php echo Html::anchor('mi-cuenta/pedidos', '<i class="fas fa-receipt fa-fw mr-2"></i>Historial de pedidos', array('title' => 'Historial de pedidos', 'class' => 'list-group-item list-group-item-action')); ?>
                            <?php echo Html::anchor('mi-cuenta/direcciones', '<i class="fas fa-address-book fa-fw mr-2"></i>Libreta de direcciones', array('title' => 'Libreta de direcciones', 'class' => 'list-group-item list-group-item-action')); ?>
                            <?php echo Html::anchor('mi-cuenta/facturacion', '<i class="fas fa-file-alt fa-fw mr-2"></i>Datos de facturación', array('title' => 'Datos de facturación', 'class' => 'list-group-item list-group-item-action')); ?>
                            <?php echo Html::anchor('cerrar-sesion', '<i class="fas fa-power-off fa-fw mr-2"></i>Cerrar sesión', array('title' => 'Cerrar sesión', 'class' => 'list-group-item list-group-item-action')); ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
