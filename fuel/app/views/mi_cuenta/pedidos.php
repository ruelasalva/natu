<section class="inner-product bg-store" id="my-account">
    <div class="container">
        <div class="row wrapper">
            <div class="col">
                <h2 class="text-uppercase space">Historial de pedidos</h2>
                <div class="row">
                    <div class="col-lg-8 card-info">
                        <div class="bg-white rounded border p-3 ">
                            <table class="table">
                                <thead class="text-uppercase ">
                                    <tr>
                                        <th scope="col" class="title p-1"><small class="font-weight-bold">Pedido #</small></th>
                                        <th scope="col" class="title p-1"><small class="font-weight-bold">Fecha</small></th>
                                        <th scope="col" class="title p-1"><small class="font-weight-bold">Total</small></th>
                                        <th scope="col" class="title p-1"><small class="font-weight-bold">Estado</small></th>
                                        <th scope="col" class="title p-1"><small class="font-weight-bold">Acciones</small></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if(!empty($sales_history)): ?>
                                    <?php foreach($sales_history as $sale): ?>
                                    <tr class="<?php echo ($sale['status'] == 'Cancelada') ? 'text-danger' : ''; ?>">
                                        <th data-th="Pedido #:" scope="row"><?php echo $sale['id']; ?></th>
                                        <td data-th="Fecha:"><?php echo $sale['date']; ?></td>
                                        <td data-th="Total:">$<?php echo $sale['total']; ?> MXN</td>
                                        <td data-th="Estado:"><?php echo $sale['status']; ?></td>
                                        <td data-th="Acciones:"><?php echo Html::anchor('mi-cuenta/pedido/'.$sale['id'], 'Ver pedido', array('title' => 'Ver detalles del pedido', 'class' => 'link blue')); ?></td>
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
                    <div class="col-lg-4">
                        <div class="list-group mt-5 mt-lg-0">
                            <?php echo Html::anchor('mi-cuenta', '<i class="fas fa-user fa-fw mr-2"></i>Mi cuenta', array('title' => 'Mi cuenta', 'class' => 'list-group-item list-group-item-action')); ?>
                            <?php echo Html::anchor('mi-cuenta/editar', '<i class="fas fa-user-edit fa-fw mr-2"></i>Editar perfil', array('title' => 'Editar perfil', 'class' => 'list-group-item list-group-item-action')); ?>
                            <?php echo Html::anchor('mi-cuenta/pedidos', '<i class="fas fa-receipt fa-fw mr-2"></i>Historial de pedidos', array('title' => 'Historial de pedidos', 'class' => 'list-group-item list-group-item-action active')); ?>
                            <?php echo Html::anchor('mi-cuenta/direcciones', '<i class="fas fa-address-book fa-fw mr-2"></i>libreta de direcciones', array('title' => 'libreta de direcciones', 'class' => 'list-group-item list-group-item-action')); ?>
                            <?php echo Html::anchor('mi-cuenta/facturacion', '<i class="fas fa-file-alt fa-fw mr-2"></i>Datos de facturación', array('title' => 'Datos de facturación', 'class' => 'list-group-item list-group-item-action')); ?>
                            <?php echo Html::anchor('cerrar-sesion', '<i class="fas fa-power-off fa-fw mr-2"></i>Cerrar sesión', array('title' => 'Cerrar sesión', 'class' => 'list-group-item list-group-item-action')); ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
