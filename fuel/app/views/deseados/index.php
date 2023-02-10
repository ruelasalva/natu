<section class="bg-store" id="wishlist">
    <div class="container">
        <div class="row wrapper">
            <div class="col">
                <h2 class="text-uppercase space h2-wishlist">Productos deseados</h2>
                <div class="row">
                    <div class="col-lg-12 wishlist-products">
                        <?php if(!empty($products)): ?>
                            <?php foreach($products as $product): ?>
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
                                                    <h2 class="title-product text-left text-uppercase mb-3"><?php echo Html::anchor('producto/'.$product['slug'], $product['name'], array('title' => $product['name'], 'class' => '')); ?></h2>
                                                </div>
                                                <div class="col">
                                                    <p class="title-price font-weight-bold text-left text-md-center">$<span><?php echo $product['price']; ?></span> MXN</p>
                                                </div>
                                            </div>
                                            <p class="mb-4">
                                                <?php echo Str::truncate($product['description'], 250); ?>
                                            </p>
                                            <?php if(!$product['available']): ?>
                                                <div class="form-row align-items-center pb-4">
                                                    <div class="col-xl-3">
                                                        <div class="d-flex align-items-center">
                                                            <label class="mb-0 mr-1 font-weight-bold text-primary" for="form_cart-qty">Cantidad:</label>
                                                            <div class="flex-fill">
                                                                <?php echo Form::input('cart-qty', '1', array('class' => 'touchspin touchspin-add text-center form-control')); ?>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-xl-3">
                                                        <button type="submit" class="btn btn-secondary btn-block my-1 text-uppercase add-product-cart" data-type="multiple" data-product="<?php echo $product['id']; ?>"><i class="fas fa-shopping-bag fa-fw mr-1"></i>Agregar al carrito</button>
                                                    </div>
                                                </div>
                                            <?php endif; ?>
                                            <div class="form-row align-items-center">
                                                <div class="col-md text-right text-md-left mt-3 mt-md-0">
                                                    <button type="submit" class="bg-transparent border-0 text-primary font-weight-bold text-uppercase delete-product-wishlist" data-product="<?php echo $product['id'] ?>">Eliminar</button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <div class="p-3 border bg-white rounded mb-3"><p class="mb-0">No hay productos en tu lista de deseados.</p></div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
