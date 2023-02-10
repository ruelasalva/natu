<section class="bg-store" id="checkout">
    <div class="container">
        <div class="row wrapper">
            <div class="col">
                <h2 class="text-uppercase space h2-checkout">Método de pago</h2>
                <div class="mb-3">
                    <ul class="breadcrumb-custom text-uppercase mb-0">
                        <li>Enviar a</li>
                        <?php if(Session::get('bill') == 1): ?>
                        <li>Factura</li>
                        <?php endif; ?>
                        <li class="active">Pago</li>
                        <li>Pedido realizado</li>
                    </ul>
                </div>
                <div class="row">
                    <div class="col-lg-8">
                        <div class="rounded bg-white border p-3 mb-3">
                            <?php if(!empty($address)): ?>
                                <address class="mb-0">
                                    <span class="h4"><?php echo $address['full_name']; ?></span><br>
                                    <?php echo $address['address']; ?><br>
                                    <?php echo $address['region']; ?><br>
                                    <?php echo $address['phone']; ?>
                                </address>
                                <?php echo Html::anchor('checkout/envio', '<i class="fas fa-cog fa-fw mr-1"></i>Cambiar dirección de envío', array('title' => 'Cambiar dirección de envío', 'class' => 'mt-3 d-inline-block')); ?>
                            <?php else: ?>
                                <p>No tienes una dirección predeterminada.</p>
                            <?php endif; ?>
                        </div>
                        <?php if($msg != ''): ?>
                            <div class="alert alert-success" role="alert">
                                <h4 class="alert-heading">¡Atención!</h4>
                                <p><?php echo $msg; ?></p>
                            </div>
                        <?php endif; ?>
                        <div class="accordion rounded" id="accordion">
                            <?php if($active_bbva): ?>
                                <div class="card">
                                    <div class="card-header" id="heading-bbva">
                                        <h2 class="payament-method">
                                            <button class="btn btn-link" type="button" data-toggle="collapse" data-target="#collapse-bbva" aria-expanded="true" aria-controls="collapse-bbva">
                                                Cargo directo en tarjeta de crédito/débito
                                            </button>
                                        </h2>
                                    </div>
                                    <div id="collapse-bbva" class="collapse show" aria-labelledby="heading-bbva" data-parent="#accordion">
                                        <div class="card-body">
                                            <?php if($flag_bbva): ?>
                                                <?php echo Html::anchor($url, Asset::img('bbva.jpg', array('alt' => 'Realizar pago con tarjeta', 'class' => 'img-fluid mx-auto d-block')), array('title' => 'Realizar pago con tarjeta')); ?>
                                            <?php else: ?>
                                                <h4 class="alert-heading">¡Mensaje importante!</h4>
                                                <hr>
                                                <p class="text-justify">No ha sido posible generar el enlace hacia el método de pago, por favor vuelve a intentarlo más tarde.</p>
                                                <p class="text-justify">Si el problema persiste comunicate con nosotros a través del correo <b>sistemas@naturaymas.com.mx</b> y proporciona el ID #<b><?php echo $sale_id; ?></b>.</p>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                            <?php endif; ?>
                            <?php if($active_transfer): ?>
                                <div class="card">
                                    <div class="card-header" id="heading-transfer">
                                        <h2 class="payament-method">
                                            <button class="btn btn-link" type="button" data-toggle="collapse" data-target="#collapse-transfer" aria-expanded="true" aria-controls="collapse-transfer">
                                                Transferencia/Depósito
                                            </button>
                                        </h2>
                                    </div>
                                    <div id="collapse-transfer" class="collapse show" aria-labelledby="heading-transfer" data-parent="#accordion">
                                        <div class="card-body">
                                            <?php if($flag_transfer): ?>
                                                <div class="row">
                                                    <div class="col-lg-6 col-md-12">
                                                        <?php echo $transfer_data; ?>
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="col-12">
                                                        <hr>
                                                        <p>
                                                            <strong>Instrucciones:</strong><br>
                                                            1.- Da click en el botón <strong>"Enviar pedido"</strong>.<br>
                                                            2.- Deposita la cantidad de <strong>$<?php echo $total['formatted']; ?> MXN</strong> en nuestra cuenta.<br>
                                                            3.- Enviar el comprobante junto con el ID de referencia <strong><?php echo $sale_id; ?></strong> al correo <strong>atencionaclientes@naturaymas.com.mx</strong><br>
                                                            4.- Esperar la <strong>confirmación</strong>por parte de nuestro equipo en un plazo máximo de <strong>24 hrs en dias laborales</strong>.
                                                        </p>
                                                        <?php echo Html::anchor('checkout/transferencia', 'Enviar pedido', array('class' => 'btn btn-primary btn-block btn-lg text-uppercase')); ?>
                                                    </div>
                                                </div>
                                            <?php else: ?>
                                                <h4 class="alert-heading">¡Mensaje importante!</h4>
                                                <hr>
                                                <p class="text-justify">No ha sido posible generar los pasos para el pago por transferencia/depósito, por favor vuelve a intentarlo más tarde.</p>
                                                <p class="text-justify">Si el problema persiste comunicate con nosotros a través del correo <b>sistemas@naturaymas.com.mx</b> y proporciona el ID #<b><?php echo $sale_id; ?></b>.</p>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                    <aside class="col-lg-4">
                        <div class="rounded border bg-white p-3 mt-3 mt-lg-0" id="checkout_sidebar">
                            <h2 class="text-uppercase mb-3">Resumen del pedido</h2>
                            <div class="border-bottom w-100 my-4"></div>
                            <div class="row justify-content-between mb-2">
                                <div class="col-auto">
                                    <p class="h5 mb-0">Total (incluye IVA)</p>
                                </div>
                                <div class="col-auto">
                                    <p class="h5 mb-0 text-primary font-weight-bold">$<span class="total-price"><?php echo $total['formatted']; ?></span> MXN</p>
                                </div>
                            </div>
                            <div class="border-bottom w-100 mt-4"></div>
                            <div class="row">
                                <div class="col cart-wrapper">
                                    <?php foreach($cart_data as $product): ?>
                                        <div class="row product-<?php echo $product['id']; ?>">
                                            <div class="col-12">
                                                <div class="py-3 border-bottom">
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
                                                            <h2 class="h2-product mb-2"><?php echo $product['name']; ?></h2>
                                                            <p class="h5 font-weight-bold">$<span class="total-product-price"><?php echo $product['price']['total']['formatted']; ?></span> MXN</p>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        </div>
                    </aside>
                </div>
            </div>
        </div>
    </div>
</section>
