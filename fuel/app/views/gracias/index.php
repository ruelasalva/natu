<section class="bg-store" id="checkout">
    <div class="container">
        <div class="row wrapper">
            <div class="col">
                <div class="mb-3">
                    <ul class="breadcrumb-custom text-uppercase mb-0">
                        <li>Enviar a</li>
                        <li>Pago</li>
                        <li class="active">Pedido realizado</li>
                    </ul>
                </div>
                <div class="row">
                    <div class="col-lg-8">
                        <div class="bg-white rounded border text-center p-4">
                            <h2 class="h2-checkout font-weight-bold mb-4">¡Gracias por comprar en Natura y Mas!</h2>
                            <?php echo Asset::img('compra-confirmada.png', array('alt' => '¡Gracias por comprar en Natura y Mas!', 'class' => 'img-fluid d-block mx-auto')); ?>
                            <p class="my-4 text-primary h3 text-justify">Tu compra ha sido realizada con éxito.<br><br>En breve enviaremos un correo con la información de tu pedido, te invitamos a seguir navegando en nuestra tienda en línea.</p>
                            <?php echo Html::anchor('/', '<i class="fas fa-shopping-bag fa-fw mr-1"></i> Seguir comprando', array('title' => 'Seguir comprando', 'class' => 'btn btn-primary text-uppercase')); ?>
                        </div>
                    </div>
                    <aside class="col-lg-4">
                        <div class="rounded border bg-white p-3 mt-3 mt-lg-0" id="checkout_sidebar">
                            <h2 class="text-uppercase mb-3">Resumen del pedido</h2>
                            <div class="border-bottom w-100 my-4"></div>
                            <div class="row justify-content-between">
                                <div class="col-auto">
                                    <p class="h5 mb-0">Total (incluye IVA)</p>
                                </div>
                                <div class="col-auto">
                                    <p class="h5 mb-0 text-primary font-weight-bold">$<span class="total-price"><?php echo $total_price; ?></span></p>
                                </div>
                            </div>
                            <div class="border-bottom w-100 mt-4"></div>
                            <div class="row">
                                <div class="col cart-wrapper">
                                    <?php foreach($cart_products as $product): ?>
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
                                                        <p class="h5 font-weight-bold">$<span class="total-product-price"><?php echo $product['price']['total']['formatted']; ?></span></p>
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
