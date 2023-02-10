<section class="bg-store">
    <div class="container">
        <div class="row wrapper">
            <div class="col-12 col-xs-card">
                <div class="bg-white rounded-border">
                    <div class="row">
                        <div class="col-lg-7 p-3">
                            <ul id="glasscase" class="gc-start display2">
                                <li class="border-0">
                                    <?php
                                    if (file_exists(DOCROOT.'assets/uploads/'.$image))
                                    {
                                        echo Asset::img($image, array('alt' => $name, 'class' => 'img-fluid d-block', 'data-gc-caption','none' => $name));
                                    } else{
                                        echo Asset::img('sw_no_image.png', array('alt' => $name, 'class' => 'img-fluid d-block', 'data-gc-caption','none' => $name));
                                    }
                                    ?>
                                </li>
                                <?php if(!empty($galleries)): ?>
                                    <?php foreach($galleries as $gallery): ?>
                                        <li class="border-0">
                                            <?php
                                            echo Asset::img($gallery['image'], array('alt' => $name, 'class' => 'img-fluid d-block', 'data-gc-caption' => $name));
                                            ?>
                                        </li>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </ul>
                        </div>
                        <div class="col-lg-5">
                            <div class="product-info px-3 py-5">
                                <h2 class="mb-4"><?php echo $name; ?></h2>
                                <div class="d-flex justify-content-between">
                                    <div class="list-prices">
                                        <?php if(Auth::member(1)): ?>
                                            <?php if($price['original'] != ''): ?>
                                                <span class="original">$<?php echo $price['original']; ?>&nbsp;MXN <?php echo  Asset::img('promo.png', array('alt' => 'Promo', 'class' => ''));?> </span>
                                            <?php else: ?>
                                                <span class="original_space">&nbsp;</span>
                                            <?php endif; ?>
                                            <span class="current">$<?php echo $price['current']; ?>&nbsp;MXN</span>
                                        <?php endif; ?>
                                        <br>
                                        <strong>Incluye IVA</strong>
                                        <br>
                                        <strong>Envío gratis</strong>
                                        <br>
                                    </div>
                                </div>
                                <div itemscope itemtype="http://schema.org/Product">
                                    <?php $char = array('"', "'"); ?>
                                    <meta itemprop="brand" content="<?php echo $brand['name']; ?>">
                                    <meta itemprop="name" content="<?php echo str_replace($char, '', $name); ?>">
                                    <meta itemprop="description" content="<?php echo str_replace($char, '', $description); ?>">
                                    <meta itemprop="productID" content="<?php echo $code; ?>">
                                    <meta itemprop="url" content="<?php echo Uri::current(); ?>">
                                    <meta itemprop="image" content="<?php echo Uri::base(false).'assets/uploads/'.$image; ?>">
                                    <div itemprop="offers" itemscope itemtype="http://schema.org/Offer">
                                        <link itemprop="availability" href="http://schema.org/InStock">
                                        <link itemprop="itemCondition" href="http://schema.org/NewCondition">
                                        <meta itemprop="price" content="<?php echo $price_facebook; ?>">
                                        <meta itemprop="priceCurrency" content="MXN">
                                    </div>
                                </div>
                                <div class="d-block mt-4 mb-4">
                                    <span class="text-primary">Categoría: <strong>
                                        <?php echo Html::anchor('tienda/familia/'.$category['slug'], $category['name'], array('title' => $category['name'])); ?></strong>
                                    </span>
                                    <span class="text-primary px-1">|</span>
                                    <span class="text-primary">Grupo: <strong>
                                        <?php echo Html::anchor('tienda/subfamilia/'.$subcategory['slug'], $subcategory['name'], array('title' => $subcategory['name'])); ?></strong>
                                    </span>
                                    <span class="text-primary px-1">|</span>
                                    <span class="text-primary">Marca: <strong>
                                        <?php echo Html::anchor('tienda/marca/'.$brand['slug'], $brand['name'], array('title' => $brand['name'])); ?></strong>
                                    </span>
                                    <span class="text-primary px-1">|</span>
                                    <span class="text-primary">Código SKU: <strong><?php echo $code; ?></strong></span>
                                </div>
                                <?php if($available): ?>
                                    <span class="text-secondary mb-4 d-block">Agotado temporalmente</span>
                                <?php endif; ?>
                                <?php if(Auth::member(1)): ?>
                                    <?php if(!$available): ?>
                                        <div class="form-row align-items-center pb-4">
                                            <div class="col-xl">
                                                <div class="d-flex align-items-center">
                                                    <label class="mb-0 mr-1 font-weight-bold text-primary" for="form_cart-qty">Cantidad:</label>
                                                    <div class="flex-fill">
                                                        <?php echo Form::input('cart-qty', '1', array('class' => 'touchspin touchspin-add text-center form-control')); ?>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-xl">
                                                <button type="submit" class="btn btn-secondary btn-block my-1 text-uppercase add-product-cart" data-type="multiple" data-product="<?php echo $product_id; ?>"><i class="fas fa-shopping-bag fa-fw mr-1"></i>Agregar al carrito</button>
                                            </div>
                                        </div>
                                    <?php else: ?>
                                        <div class="form-row align-items-center pb-3">
                                            <div class="col">
                                                <button type="button" class="btn btn-secondary btn-block my-1 text-uppercase" data-toggle="modal" data-target="#cotizar">
                                                    <i class="fas fa-envelope mr-1"></i>Solicitar cotización
                                                </button>
                                            </div>
                                        </div>
                                    <?php endif; ?>
                                    <div class="form-row align-items-center pb-4">
                                        <div class="col-xl">
                                            <button type="submit" class="btn btn-outline-secondary btn-block my-1 text-uppercase add-product-wishlist" data-product="<?php echo $product_id; ?>"><i class="fas fa-bookmark fa-fw mr-1"></i>Agregar a deseados</button>
                                        </div>
                                    </div>
                                <?php else: ?>
                                    <div class="form-row align-items-center pb-4">
                                        <div class="col">
                                            <button type="button" class="btn btn-info btn-block my-1 text-uppercase">
                                                <?php echo Html::anchor('iniciar-sesion', '<i class="fas fa-user  mr-1"></i>Inicia sesion para comprar en linea' , array()); ?>
                                            </button>
                                        </div>
                                    </div>
                                <?php endif; ?>
                                <p class="description mb-4 text-primary border-top pt-4">
                                    <?php echo $description; ?>
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<section class="bg-store">
    <div class="container">
        <div class="row pb-3">
            <div class="col col-xs-card">
                <h3 class="text-center title-primary-1 h1 space">Productos relacionados</h3>
                <?php if(!empty($related_products)): ?>
                    <div class="row mx-n2 mx-md-n3">
                        <?php foreach($related_products as $product): ?>
                            <div class="px-2 px-md-3 col-6 col-lg-3">
                                <div class="card-item bg-white text-center mb-3 mb-md-5">
                                    <?php
                                    if (file_exists(DOCROOT.'assets/uploads/thumb_'.$product['image']))
                                    {
                                        echo Html::anchor('producto/'.$product['slug'], Asset::img('thumb_'.$product['image'], array('alt' => $product['name'], 'class' => 'img-fluid d-block mx-auto')), array('title' => $product['name'], 'class' => 'anchor-image'));
                                    } else{
                                        echo Html::anchor('producto/'.$product['slug'], Asset::img('thumb_no_image.png', array('alt' => $product['name'], 'class' => 'img-fluid d-block mx-auto')), array('title' => $product['name'], 'class' => 'anchor-image'));
                                    }
                                    ?>
                                    <div class="name-btn-wrapper">
                                        <h3 class="mb-0 name">
                                            <?php echo Html::anchor('producto/'.$product['slug'], $product['name'], array('title' => $product['name'])); ?>
                                        </h3>
                                    </div>
                                    <div class="price-wrapper">
                                        <div class="d-block mt-4 mb-4">
                                            <span class="text-primary">Código SKU:<strong><?php echo $product['code']; ?></strong></span>
                                        </div>
                                        <?php if(Auth::member(1)): ?>
                                            <?php if($product['available']): ?>
                                                <span class="stock">Agotado temporalmente</span>
                                            <?php else: ?>
                                                <span class="stock"></span>
                                            <?php endif; ?>
                                            <?php if($product['price']['original'] != ''): ?>
                                                <span class="original">$<?php echo $product['price']['original']; ?>&nbsp;MXN <?php echo  Asset::img('promo.png', array('alt' => 'Promo', 'class' => ''));?> </span>
                                            <?php else: ?>
                                                <span class="original_space">&nbsp;</span>
                                            <?php endif; ?>
                                            <span class="current">$<?php echo $product['price']['current']; ?>&nbsp;MXN</span>
                                            <div class="col-xl">
                                                <div class="d-flex align-items-center">
                                                    <label class="mb-0 mr-1 font-weight-bold text-primary" for="form_cart-qty">Cantidad:</label>
                                                    <div class="flex-fill">
                                                        <?php echo Form::input('cart-qty', '1', array('class' => 'touchspin touchspin-add text-center form-control')); ?>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-xl">
                                                <button type="submit" class="btn btn-secondary btn-block my-1 text-uppercase add-product-cart" data-type="multiple" data-product="<?php echo $product['product_id']; ?>"><i class="fas fa-shopping-bag fa-fw mr-1"></i>Agregar al carrito</button>
                                            </div>
                                        <?php else: ?>
                                            <div class="form-row align-items-center pb-4">
                                                <div class="col">
                                                    <button type="button" class="btn btn-info btn-block my-1 text-uppercase">
                                                        <?php echo Html::anchor('iniciar-sesion', '<i class="fas fa-user  mr-1"></i>Inicia sesion para comprar en linea' , array()); ?>
                                                    </button>
                                                </div>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <p>No hay productos relacionados para mostrar.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>

<?php if($available): ?>
    <div class="modal fade" id="cotizar" tabindex="-1" role="dialog" aria-labelledby="cotizar" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title text-primary">Solicitar cotización</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <?php echo Form::open(array(
                        'action'     => '',
                        'method'     => 'post',
                        'class'      => '',
                        'id'         => 'form_modal',
                        'novalidate' => true
                    )); ?>
                    <div class="form-row">
                        <div class="form-group col-sm">
                            <?php echo Form::label('Nombre*', 'name', array('class' => 'text-primary')); ?>
                            <?php echo Form::input('name', '', array('class' => 'form-control', 'placeholder' => 'Nombre')); ?>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group col-sm">
                            <?php echo Form::label('Apellidos*', 'last_name', array('class' => 'text-primary')); ?>
                            <?php echo Form::input('last_name', '', array('class' => 'form-control', 'placeholder' => 'Apellidos')); ?>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group col-sm">
                            <?php echo Form::label('Email*', 'email', array('class' => 'text-primary')); ?>
                            <?php echo Form::input('email', '', array('class' => 'form-control', 'placeholder' => 'Email')); ?>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group col-sm">
                            <?php echo Form::label('Teléfono*', 'phone', array('class' => 'text-primary')); ?>
                            <?php echo Form::input('phone', '', array('class' => 'form-control', 'placeholder' => 'Teléfono')); ?>
                        </div>
                    </div>
                    <div class="form-group">
                        <?php echo Form::label('Mensaje*', 'message', array('class' => 'text-primary')); ?>
                        <?php echo Form::textarea('message', '', array('class' => 'form-control', 'placeholder' => 'Mensaje', 'rows' => 5)); ?>
                    </div>
                    <div class="form-group text-center">
                        <?php echo Form::hidden('product', $product['name'], array('class' => 'form-control', 'placeholder' => 'producto')); ?>
                        <?php echo Form::button('submit', 'Enviar', array('class' => 'btn btn-secondary btn-block text-uppercase', 'type' => 'submit')); ?>
                    </div>
                    <span class="status"></span>
                    <?php echo Form::close(); ?>
                </div>
            </div>
        </div>
    </div>
<?php endif; ?>
