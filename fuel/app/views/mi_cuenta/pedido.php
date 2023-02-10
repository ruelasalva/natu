<section class="inner-product bg-store" id="my-account">
    <div class="container">
        <div class="row wrapper">
            <div class="col">
                <h2 class="text-uppercase space">Historial de pedidos</h2>
                <?php if (Session::get_flash('error')): ?>
                <div class="alert alert-danger alert-dismissable">
                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                    <?php echo Session::get_flash('error'); ?>
                </div>
                <?php endif; ?>
                <?php if (Session::get_flash('success')): ?>
                <div class="alert alert-success alert-dismissable">
                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                    <?php echo Session::get_flash('success'); ?>
                </div>
                <?php endif; ?>
                <div class="row">
                    <div class="col-lg-8">
                        <div class="bg-white rounded p-3 mb-4 border card-info">
                            <div class="d-flex justify-content-between flex-column flex-md-row mb-4">
                                <p class="h3 text-uppercase">
                                    <span class="font-weight-bold"># Pedido:</span>
                                    <span class="text-secondary"><?php echo $sale_history['id']; ?></span>
                                    <br>
                                    <span class="font-weight-bold">Estado:</span>
                                    <span class="text-capitalize text-secondary"><?php echo $sale_history['status']; ?></span>
                                    <br>
                                    <span class="font-weight-bold">Estatus pedido:</span>
                                    <span class="text-capitalize text-secondary"><?php echo $sale_history['order']; ?></span>
                                    <br>
                                    <span class="font-weight-bold">Paqueteria:</span>
                                    <span class="text-capitalize text-secondary"><?php echo $package; ?></span>
                                    <br>
                                    <span class="font-weight-bold">Guia de rastreo:</span>
                                    <span class="text-capitalize text-secondary"><?php echo $sale_history['guide']; ?></span>
                                </p>
                                <p class="h3 text-md-right text-uppercase">
                                    <span class="font-weight-bold">Total del pedido:</span>
                                    <br>
                                    <span class="text-capitalize text-secondary">$<?php echo $sale_history['total']; ?></span>
                                </p>
                            </div>
                            <?php if($sale_history['status_id'] == 2): ?>
                                <div class="mb-3">
                                    <?php echo Form::open(array(
                                        'action'     => '',
                                        'method'     => 'post',
                                        'class'      => 'voucher',
                                        'id'         => '',
                                        'enctype'    => 'multipart/form-data',
                                        'novalidate' => true
                                    )); ?>
                                        <div class="form-group upload_box">
                                            Sube el comprobante de la transferencia o depósito para que podamos autorizar la venta.
                                            <?php echo Form::file('voucher'); ?>
                                            <small>
                                                <strong>NOTA:</strong> El tamaño del archivo JPG/JPEG/PNG no debe exceder de 15 Mb.
                                            </small>
                                        </div>
                                        <?php echo Form::button('submit', 'Enviar', array('class' => 'btn btn-primary btn-block text-uppercase')); ?>
                                    <?php echo Form::close(); ?>
                                </div>
                            <?php endif; ?>
                            <div class="mb-3">
                                <span class="text-uppercase font-weight-bold">Fecha:</span>
                                <span class="d-block text-secondary"><?php echo $sale_history['date']; ?></span>
                            </div>
                            <div class="mb-3">
                                <span class="text-uppercase font-weight-bold">Total:</span>
                                <span class="d-block text-secondary">$<?php echo $sale_history['total']; ?></span>
                            </div>
                            <?php if(!empty($sale_history['shipping_address'])): ?>
                            <div class="mb-3">
                                <span class="text-uppercase font-weight-bold">Información de envío:</span>
                                <address class="mb-0">
                                    <?php echo $sale_history['shipping_address']['full_name']; ?><br>
                                    <?php echo $sale_history['shipping_address']['address']; ?><br>
                                    <?php echo $sale_history['shipping_address']['region']; ?><br>
                                    <?php echo $sale_history['shipping_address']['phone']; ?>
                                </address>
                            </div>
                            <?php endif; ?>
                            <?php if($sale_history['bill_id'] != 0): ?>
                            <div>
                                <span class="text-uppercase font-weight-bold">Factura:</span>
                                <address class="mb-0">
                                    <?php echo Html::anchor('mi-cuenta/descarga_pdf/'.$sale_history['id'], 'Archivo PDF', array('class' => 'btn btn-sm btn-secondary d-inline')); ?>
                                    <?php echo Html::anchor('mi-cuenta/descarga_xml/'.$sale_history['id'], 'Archivo XML', array('class' => 'btn btn-sm btn-secondary d-inline')); ?>
                                </address>
                            </div>
                            <?php endif; ?>
                        </div>
                        <div class="bg-white rounded p-3 mb-4 border">
                            <?php foreach($sale_history['products'] as $product): ?>
                            <div class="row">
                                <div class="col-12">
                                    <div class="py-3">
                                        <div class="row">
                                            <div class="col-3 pr-0 pr-sm-3">
                                                <?php
                                                 if (file_exists(DOCROOT.'assets/uploads/thumb_'.$product['image']))
                                                 {
                                                     echo Html::anchor('producto/'.$product['slug'], Asset::img('thumb_'.$product['image'], array('alt' => $product['name'], 'class' => 'img-fluid d-block mx-auto')), array('title' => $product['name'], 'class' => ''));
                                                     }else{
                                                         echo Html::anchor('producto/'.$product['slug'], Asset::img('thumb_no_image.png', array('alt' => $product['name'], 'class' => 'img-fluid d-block mx-auto')), array('title' => $product['name'], 'class' => ''));
                                                     }
                                                ?>
                                            </div>
                                            <div class="col-9 text-left">
                                                <h2 class="h5 mb-2"><?php echo $product['name']; ?></h2>
                                                <p class="mb-3">
                                                    <span class="font-weight-bold">Cantidad:</span><span class="text-secondary"> <?php echo $product['quantity']['current']; ?></span>
                                                    <br>
                                                    <span class="font-weight-bold">Precio unitario:</span><span class="text-secondary"> $<?php echo $product['price']['current']['formatted']; ?></span>
                                                </p>
                                                <p class="mb-0">
                                                    <span class="font-weight-bold">Total:</span><span class="text-secondary"> $<?php echo $product['price']['total']['formatted']; ?></span>
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    <div class="col-lg-4">
                        <div class="list-group mt-5 mt-lg-0">
                            <?php echo Html::anchor('mi-cuenta', '<i class="fas fa-user fa-fw mr-2"></i>Mi cuenta', array('title' => 'Mi cuenta', 'class' => 'list-group-item list-group-item-action')); ?>
                            <?php echo Html::anchor('mi-cuenta/editar', '<i class="fas fa-user-edit fa-fw mr-2"></i>Editar perfil', array('title' => 'Editar perfil', 'class' => 'list-group-item list-group-item-action')); ?>
                            <?php echo Html::anchor('mi-cuenta/pedidos', '<i class="fas fa-receipt fa-fw mr-2"></i>Historial de pedidos', array('title' => 'Historial de pedidos', 'class' => 'list-group-item list-group-item-action active')); ?>
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
