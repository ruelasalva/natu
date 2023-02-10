<section>
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12 px-0">
                <?php if(!empty($slides)): ?>
                <div id="slider" class="carousel slide" data-ride="carousel">
                    <ol class="carousel-indicators">
                        <?php foreach($slides as $key => $slide): ?>
                        <li data-target="#slider" data-slide-to="<?php echo $key ?>" class="<?php echo ($key == 0) ? 'active' : '' ?>"></li>
                        <?php endforeach; ?>
                    </ol>
                    <div class="carousel-inner">
                        <?php foreach($slides as $key => $slide): ?>
                        <div class="carousel-item <?php echo ($key == 0) ? 'active' : '' ?>">
                            <?php 
                                if (file_exists(DOCROOT.'assets/uploads/'.$slide['image']))
                                {
                                    echo Html::anchor($slide['url'], Asset::img($slide['image'], array('alt' => 'Natura y Mas', 'class' => 'img-fluid d-block mx-auto w-100')), array('title' => 'Natura y Mas', 'target' => '_blank'));  
                                }else{
                                    echo Html::anchor($slide['url'], Asset::img('sw_no_slider.jpg', array('alt' => 'Natura y Mas', 'class' => 'img-fluid d-block mx-auto w-100')), array('title' => 'Natura y Mas', 'target' => '_blank'));
                                }
                            ?>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>
<section>
    <div class="container">
        <div class="row wrapper-top">
            <div class="col-12">
                <h3 class="text-center title-primary-1 h1 space">Productos por marca</h3>
            </div>
        </div>
    </div>
    <div class="bg-brands py-3">
        <div class="container brands">
            <div class="row">
                <div class="col-12 px-xl-0 pxs-3">
                    <div class="types-wrapper owl-types owl-carousel owl-theme my-3">
                        <?php if(!empty($brands)): ?>
                            <?php foreach($brands as $brand): ?>
                                <div class="type-item gray-img">
                                    <?php 
                                        if (file_exists(DOCROOT.'assets/uploads/'.$brand['image']))
                                        {
                                            echo Html::anchor('tienda/marca/'.$brand['slug'], Asset::img($brand['image'], array('alt' => $brand['name'], 'class' => 'img-fluid')), array('title' => $brand['name']));
                                        }else{
                                            echo Html::anchor('tienda/marca/'.$brand['slug'], Asset::img('sw_no_brand.png', array('alt' => $brand['name'], 'class' => 'img-fluid')), array('title' => $brand['name']));
                                        }
                                    ?>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                    <div class="owl-controls-types"></div>
                </div>
            </div>

    	</div>
    </div>
</section>
<section>
    <div class="container">
        <div class="row pb-3 wrapper-top">
            <div class="col col-xs-card">
                <h3 class="text-center title-primary-1 h1 space">Productos destacados</h3>
                <?php if(!empty($featured_products)): ?>
                    <?php $cont = 0; ?>
                    <?php foreach($featured_products as $key => $product): ?>
                        <?php if($cont == 0): ?>
                            <div class="row mx-n2 mx-md-n3">
                        <?php endif; ?>
                                <div class="px-2 px-md-3 col-6 col-lg-3">
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
                                            <?php if($product['price']['original'] != ''): ?>
                                                <span class="original">$<?php echo $product['price']['original']; ?>&nbsp;MXN <?php echo  Asset::img('promo.png', array('alt' => 'Promo', 'class' => ''));?></span>
                                            <?php else: ?>
                                                <span class="original_space">&nbsp;</span>
                                            <?php endif; ?>
                                            <span class="current">$<?php echo $product['price']['current']; ?>&nbsp;MXN</span>
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
                        <?php $cont++; ?>
                        <?php if($cont == 4 or $key == count($featured_products)-1): ?>
                            <?php $cont = 0; ?>
                            </div>
                        <?php endif; ?>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p>No hay productos destacados disponibles.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>
<section id="years-experience">
    <div class="container" >
        <div class="row py-5 px-movil">
            <div class="col-md-6 col-12 bg-white pr-0 radio-year">
                <div class="text-years p-year">
                    <p class="text-justify">Bienvenido a nuestra tienda de productos naturistas. Ofrecemos una amplia variedad de opciones para cuidar tu salud de manera natural. Desde suplementos alimenticios hasta productos orgánicos, todo lo que encontrarás en nuestra tienda ha sido cuidadosamente seleccionado para asegurar su calidad y eficacia.</p>
                    <p class="text-justify">Apoyamos la medicina alternativa y creemos en el poder curativo de la naturaleza. Si tienes alguna pregunta sobre nuestros productos, no dudes en preguntar a nuestro equipo de expertos. ¡Gracias por visitarnos!</p>

                </div>
            </div>
            <div class="col-md-6 col-12 px-0">
                <?php echo Asset::img('inicio.jpg', array('alt' => 'Natura y Mas', 'class' => 'img-fluid d-block radio-year-img')); ?>
            </div>
        </div>
    </div>
</section>
<section>
    <div class="container">
        <div class="row wrapper-top pb-3">
            <div class="col col-xs-card">
                <h3 class="text-center title-primary-1 h1 space">Últimos productos</h3>
                <?php if(!empty($new_products)): ?>
                    <?php $cont = 0; ?>
                    <?php foreach($new_products as $key => $product): ?>
                        <?php if($cont == 0): ?>
                            <div class="row mx-n2 mx-md-n3">
                        <?php endif; ?>
                                <div class="px-2 px-md-3 col-6 col-lg-3">
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
                                            <?php if($product['price']['original'] != ''): ?>
                                                <span class="original">$<?php echo $product['price']['original']; ?>&nbsp;MXN <?php echo  Asset::img('promo.png', array('alt' => 'Promo', 'class' => ''));?></span>
                                            <?php else: ?>
                                                <span class="original_space">&nbsp;</span>
                                            <?php endif; ?>
                                            <span class="current">$<?php echo $product['price']['current']; ?>&nbsp;MXN</span>
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
                        <?php $cont++; ?>
                        <?php if($cont == 4 or $key == count($new_products)-1): ?>
                            <?php $cont = 0; ?>
                            </div>
                        <?php endif; ?>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p>No hay productos destacados disponibles.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>
<section id="search-special">
    <div class="container">
        <div class="row wrapper-top">
            <div class="col-12">
                <h3 class="text-center title-primary-1 h1 space">¿Buscas algún producto en especial?</h3>
            </div>
        </div>
        <div class="row pb-5 pl-3">
            <div class="col-lg-6 col-12">
                <div class="row">
                    <div class="col-6 bg-white pr-0 radio-text box-sombra">
                        <div class="text-search-special p-special">
                            <span>Descarga nuestro <strong>Catálogo</strong></span>
                            <?php echo Html::anchor(Uri::base(false).'assets/catalogos/1.pdf', 'Descargar', array('title' => 'Descargar catálogo Productos', 'class' => 'btn btn-primary d-flex align-items-center justify-content-center border-0 mt-4', 'target' => '_blank')); ?>
                        </div>
                    </div>
                    <div class="col-6 pl-0 ">
                        <?php echo Asset::img('descargar-natura.jpg', array('alt' => 'Catálogo Natura', 'class' => 'img-fluid d-block radio-text-img box-sombra-img')); ?>
                    </div>
                </div>
            </div>
            <div class="col-lg-6 col-12 mt-movil">
                <div class="row">
                    <div class="col-6 bg-white pr-0 radio-text box-sombra">
                        <div class="text-search-special p-special">
                            <span>Catálogo Productos <strong>Suplementos</strong></span>
                            <?php echo Html::anchor(Uri::base(false).'assets/catalogos/2.pdf', 'Descargar', array('title' => 'Descargar catálogo suplementos', 'class' => 'btn btn-saira btn-primary d-flex align-items-center justify-content-center border-0 mt-4', 'target' => '_blank')); ?>
                        </div>
                    </div>
                    <div class="col-6 pl-0 b">
                        <?php echo Asset::img('descargar-natura2.jpg', array('alt' => 'Catálogo Natura', 'class' => 'img-fluid d-block radio-text-img box-sombra-img')); ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
