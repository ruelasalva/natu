<section id="store" class="bg-store">
    <div class="container">
        <div class="col-md-16 px-0">
                <?php if(!empty($baners)): ?>
                <div id="slider" class="carousel slide pt-5" data-ride="carousel">
                    <ol class="carousel-indicators">
                        <?php foreach($baners as $key => $baner): ?>
                        <li data-target="#slider" data-slide-to="<?php echo $key ?>" class="<?php echo ($key == 0) ? 'active' : '' ?>"></li>
                        <?php endforeach; ?>
                    </ol>
                    <div class="carousel-inner">
                        <?php foreach($baners as $key => $baner): ?>
                        <div class="carousel-item <?php echo ($key == 0) ? 'active' : '' ?>">
                        <?php
                            if (file_exists(DOCROOT.'assets/uploads/'.$baner['image']))
                            {
                                echo Html::anchor($baner['url'], Asset::img($baner['image'], array('alt' => 'Natura y Mas', 'class' => 'img-fluid d-block mx-auto')), array('title' => 'Natura y Mas', 'target' => '_blank'));
                            }else{
                                echo Html::anchor($baner['url'], Asset::img('sw_no_baners.jpg', array('alt' => 'Natura y Mas', 'class' => 'img-fluid d-block mx-auto')), array('title' => 'Natura y Mas', 'target' => '_blank'));
                            }
                        ?>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
                <?php endif; ?>
            </div>
        <div class="row">
            <div class="col">
                <div class="row">
                    <div class="col-12">
                        <h3 class="text-center title-primary-1 h1 space space-top">Productos en promoci&oacute;n</h3>
                    </div>
                </div>
                <div class="row">
                    <div class="col px-sm-0 col-xs-card">
                        <div class="row pb-5">
                            <div class="col-lg-3">
                                <ul class="switch-list">
                                    <label class="cl-switch cl-switch-red cl-switch-large">
                                        <?php $checked = (Session::get('products_available')) ? 'checked="checked"' : 'asds'; ?>
                                        <input id="products-available" type="checkbox" <?php echo $checked; ?>>
                                        <span class="switcher"></span>
                                        <span class="label">Solo productos con existencia</span>
                                    </label>
                                </ul>
                                <ul class="accordion-menu">
                                    <li>
                                        <div class="dropdownlink"><i class="fa fa-angeles-right" aria-hidden="true"></i>
                                            Categoría
                                            <i class="fa fa-chevron-down" aria-hidden="true"></i>
                                        </div>
                                        <ul class="submenuItems">
                                            <?php if(!empty($categories)): ?>
                                            <?php foreach($categories as $category): ?>
                                            <li class="">
                                                <?php echo Html::anchor('tienda/familia/'.$category['slug'], $category['name'], array('title' => $category['name'])); ?>
                                            </li>
                                            <?php endforeach; ?>
                                            <?php else: ?>
                                            <li class="">
                                                Sin categorías
                                            </li>
                                            <?php endif; ?>
                                        </ul>
                                    </li>
                                    <li>
                                        <div class="dropdownlink"><i class="fa fa-angeles-right" aria-hidden="true"></i>
                                            Grupo
                                            <i class="fa fa-chevron-down" aria-hidden="true"></i>
                                        </div>
                                        <ul class="submenuItems">
                                            <?php if(!empty($subcategories)): ?>
                                            <?php foreach($subcategories as $subcategory): ?>
                                            <li class="">
                                                <?php echo Html::anchor('tienda/subfamilia/'.$subcategory['slug'], $subcategory['name'], array('title' => $subcategory['name'])); ?>
                                            </li>
                                            <?php endforeach; ?>
                                            <?php else: ?>
                                            <li class="">
                                                Sin grupo
                                            </li>
                                            <?php endif; ?>
                                        </ul>
                                    </li>
                                    <li>
                                        <div class="dropdownlink"><i class="fa fa-angeles-right" aria-hidden="true"></i>
                                            Marca
                                            <i class="fa fa-chevron-down" aria-hidden="true"></i>
                                        </div>
                                        <ul class="submenuItems">
                                            <ul class="list-group list-group-flush">
                                                <?php if(!empty($brands)): ?>
                                                <?php foreach($brands as $brand): ?>
                                                <li class="">
                                                    <?php echo Html::anchor('tienda/marca/'.$brand['slug'], $brand['name'], array('title' => $brand['name'])); ?>
                                                </li>
                                                <?php endforeach; ?>
                                                <?php else: ?>
                                                <li class="">
                                                    Sin marcas
                                                </li>
                                                <?php endif; ?>
                                            </ul>
                                    </li>
                                </ul>
                            </div>
                            <div class="col-lg-9">
                                <div class="row mx-n2 mx-md-n3">
                                    <div class="col-12">
                                        <div class="d-flex justify-content-end">
                                                <?php echo $pagination; ?>
                                        </div>
                                    </div>
                                    <?php if(!empty($products)): ?>
                                        <?php foreach($products as $product): ?>
                                            <div class="px-2 px-md-3 col-6 col-lg-4">
                                                <div class="card-item bg-white text-center mb-3 mb-md-5">
                                                    <?php
                                                     if (file_exists(DOCROOT.'assets/uploads/thumb_'.$product['image']))
                                                     {
                                                         echo Html::anchor('producto/'.$product['slug'], Asset::img('thumb_'.$product['image'], array('alt' => $product['name'], 'class' => 'img-fluid d-block mx-auto')), array('title' => $product['name'], 'class' => 'anchor-image'));
                                                         }else{
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
                                                            <?php if($product['original_price'] != ''): ?>
                                                                <span class="original">$<?php echo $product['original_price']; ?>&nbsp;MXN <?php echo  Asset::img('promo.png', array('alt' => 'Promo', 'class' => ''));?> </span>
                                                            <?php else: ?>
                                                                <span class="original_space">&nbsp;</span>
                                                            <?php endif; ?>
                                                            <span class="current">$<?php echo $product['price']; ?>&nbsp;MXN</span>
                                                            <br>
                                                            <?php if(!$product['available']): ?>
                                                                <div class="col-xl pb-1">
                                                                    <div class="d-flex align-items-center">
                                                                        <label class="mb-0 mr-1 font-weight-bold text-primary" for="form_cart-qty">Cantidad:</label>
                                                                        <div class="flex-fill">
                                                                            <?php echo Form::input('cart-qty', '1', array('class' => 'touchspin touchspin-add text-center form-control')); ?>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <br>
                                                                <div class="col-xl">
                                                                    <button type="submit" class="btn btn-secondary btn-block my-1 text-uppercase add-product-cart" data-type="multiple" data-product="<?php echo $product['product_id']; ?>"><i class="fas fa-shopping-bag fa-fw mr-1"></i>Agregar al carrito</button>
                                                                </div>
                                                            <?php else: ?>
                                                                <div class="form-row align-items-center pb-1">
                                                                    <div class="col">
                                                                        <button type="button" class="btn btn-secondary btn-block my-1 text-uppercase" data-toggle="modal" data-target="#cotizar">
                                                                            <i class="fas fa-envelope mr-1"></i>Solicitar cotización
                                                                        </button>
                                                                    </div>
                                                                </div>
                                                            <?php endif; ?>
                                                            <div class="form-row align-items-center pb-1">
                                                                <div class="col">
                                                                    <button type="submit" class="btn btn-outline-secondary btn-block my-1 text-uppercase add-product-wishlist" data-product="<?php echo $product['product_id']; ?>"><i class="fas fa-bookmark fa-fw mr-1"></i>Agregar a deseados</button>
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
                                                    </div>
                                                </div>
                                            </div>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <div class="alert alert-secondary" role="alert">
                                            <h4 class="alert-heading">¡Mensaje importante!</h4>
                                            <hr>
                                            <p class="text-justify">Por el momento no tenemos artículos de esta clasificación, estamos trabajando para ofrecerte los mejores productos y publicarlos a la brevedad. Te sugerimos buscar a través del cuadro de búsqueda el artículo que deseas.</p>
                                        </div>
                                    <?php endif; ?>
                                </div>
                                <div class="row">
                                    <div class="col-12">
                                        <div class="d-flex justify-content-end">
                                            <?php echo $pagination; ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
