<section class="bg-store" id="checkout">
    <div class="container">
        <div class="row wrapper">
            <div class="col">
                <h2 class="text-uppercase space h2-checkout">Artículos en tu carrito</h2>
                <div class="row">
                    <div class="col-lg-8 checkout-products">
                        <?php if($total['regular'] < 1000): ?>
                            <div class="alert alert-secondary" role="alert">
                                <h4 class="alert-heading">¡Mensaje importante!</h4>
                                <hr>
                                <p class="text-justify">Para poder finalizar tu compra es necesario acumular un total de <strong>$1,000 MXN</strong>, esta cantidad incluye el envío de tu pedido <strong>Gratis</strong>. ¡Te invitamos a agregar más productos a tu carrito!</p>
                            </div>
                        <?php endif; ?>
                        <?php foreach($cart_data as $product): ?>
                            <div class="p-3 border bg-white rounded mb-3 product-<?php echo $product['id']; ?>">
                                <div class="row mx-n3">
                                    <div class="col-3 px-1 px-sm-3">
                                        <?php
                                           if (file_exists(DOCROOT.'assets/uploads/thumb_'.$product['image']))
                                           {
                                               echo Html::anchor('producto/'.$product['slug'], Asset::img('thumb_'.$product['image'], array('alt' => $product['name'], 'class' => 'img-fluid d-block mx-auto')), array('title' => $product['name'], 'class' => ''));
                                               }else{
                                                   echo Html::anchor('producto/'.$product['slug'], Asset::img('thumb_no_image.png', array('alt' => $product['name'], 'class' => 'img-fluid d-block mx-auto')), array('title' => $product['name'], 'class' => ''));
                                               }
                                        ?>
                                    </div>
                                    <div class="col-9">
                                        <div class="row">
                                            <div class="col-md-8">
                                                <h2 class="title-product text-left text-uppercase mb-3"><?php echo $product['name']; ?></h2>
                                            </div>
                                            <div class="col">
                                                <p class="title-price font-weight-bold text-left text-md-center">$<span class="total-product-price"><?php echo $product['price']['total']['formatted']; ?></span> MXN</p>
                                            </div>
                                        </div>
                                        <p class="mb-4">
                                            <?php echo Str::truncate($product['description'], 250); ?>
                                        </p>
                                        <div class="form-row align-items-center">
                                            <div class="col-md">
                                                <div class="d-flex align-items-center">
                                                    <label class="mb-0 mr-3 font-weight-bold text-uppercase" for="form_cart-qty">Cantidad:</label>
                                                    <div class="flex-fill">
                                                        <?php echo Form::input('cart-qty', $product['quantity']['current'], array('class' => 'touchspin touchspin-edit edit-product-cart text-center form-control', 'data-product' => $product['id'])); ?>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md text-right text-md-left mt-3 mt-md-0">
                                                <button type="submit" class="bg-transparent border-0 text-primary font-weight-bold text-uppercase delete-product-cart" data-product="<?php echo $product['id']; ?>">Eliminar</button>
                                            </div>
                                        </div>
                                        <div class="form-row mt-4">
                                            <div class="col-md-6">
                                                <button type="submit" class="btn btn-outline-secondary btn-block my-1 text-uppercase add-product-wishlist" data-product="<?php echo $product['id']; ?>"><i class="fas fa-bookmark fa-fw mr-1"></i>Agregar a deseados</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                    <aside class="col-lg-4">
                        <div class="rounded border bg-white p-3 mt-3 mt-lg-0 mb-3" id="bill_sidebar">
                            <h2 class="text-uppercase mb-3">Factura</h2>
                            <p class="text-justify">Marca la siguiente casilla si requieres factura de venta, en los siguientes pasos se solicitarán tus datos fiscales.</p>
                            <div class="custom-control custom-checkbox mb-4">
                                <?php echo Form::checkbox('bill', 'factura', (!Session::get('bill')) ? false : true, array('class' => 'custom-control-input')); ?>
                                <?php echo Form::label('<strong>Requiero factura de venta</strong>', 'bill', array('class' => 'custom-control-label')); ?>
                            </div>
                        </div>
                        <div class="rounded border bg-white p-3 mt-3 mt-lg-0" id="checkout_sidebar">
                            <h2 class="text-uppercase mb-3">Resumen del pedido</h2>
                            <div class="border-bottom w-100 my-4"></div>
                            <div class="row justify-content-between">
                                <div class="col-auto">
                                    <p class="h5 mb-0">Total (incluye IVA)</p>
                                </div>
                                <div class="col-auto">
                                    <p class="h5 mb-0 text-primary font-weight-bold">$<span class="total-price"><?php echo $total['formatted']; ?></span> MXN</p>
                                </div>
                            </div>
                            <div class="border-bottom w-100 my-4"></div>
                            <h2 class="text-uppercase mb-3">Términos y condiciones</h2>
                            <p class="text-justify">Antes de de realizar tu compra te invitamos a revisar nuestros <?php echo Html::anchor('terminos-y-condiciones', '<strong>términos y condiciones</strong>', array('target' => '_blank')); ?>.</p>
                            <div class="custom-control custom-checkbox mb-4">
                                <?php echo Form::checkbox('shipping_terms', 'acepto', false, array('class' => 'custom-control-input')); ?>
                                <?php echo Form::label('<strong>Acepto</strong>', 'shipping_terms', array('class' => 'custom-control-label')); ?>
                            </div>
                            <?php echo Html::anchor('checkout/envio', 'Continuar', array('title' => 'Continuar', 'class' => 'btn btn-primary btn-block disabled text-uppercase', 'tabindex' => '-1', 'role' => 'button', 'aria-disabled' => 'true', 'id' => 'button_continue_checkout', 'disabled' => true)); ?>
                                 <div>
                               <?php if($total['regular'] < 1000): ?>
                                <div class="alert alert-secondary" role="alert">
                                    <h4 class="alert-heading">¡Mensaje importante!</h4>
                                    <hr>
                                    <p class="text-justify">Para poder finalizar tu compra es necesario acumular un total de <strong>$1,000 MXN</strong>, esta cantidad incluye el envío de tu pedido <strong>Gratis</strong>. ¡Te invitamos a agregar más productos a tu carrito!</p>
                                </div>
                            <?php endif; ?>
                            </div>
                        </div>
                    </aside>
                </div>
            </div>
        </div>
    </div>
</section>
