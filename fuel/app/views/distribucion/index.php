<section id="bg-distribution" class="d-flex align-items-center">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12 d-flex justify-content-center text-center">
                <h2 class="text-uppercase"></h2>
            </div>
        </div>
    </div>
</section>

<section id="distribution" class="bg-brands">
    <div class="container">
        <div class="row py-5 px-movil">
            <div class="col-12 bg-white rounded brands border">
                <div class="row">
                    <div class="col-lg-6 col-12">
                        <div class="p-distribution text-distribution">
                            <div class="d-block">
                                <p class="text-uppercase">En nuestra área de distribución, nos enorgullecemos de ofrecer un servicio rápido y confiable. Trabajamos con proveedores de confianza para garantizar que sus pedidos se entreguen en tiempo y forma.</p>
                                <p class="text-justify">Ofrecemos una variedad de opciones de envío, desde envíos estándar hasta envíos expeditos, para adaptarse a sus necesidades. También ofrecemos envíos internacionales, con tarifas y tiempos de entrega específicos para cada país. Nos esforzamos por asegurar la seguridad y la privacidad de sus datos personales y de su pedido durante todo el proceso de envío. También ofrecemos la opción de seguimiento en línea para que pueda verificar el estado de su pedido en cualquier momento.</p>
                                <p class="text-justify">Si tiene alguna pregunta o inquietud sobre su pedido, no dude en ponerse en contacto con nuestro equipo de atención al cliente. Estaremos encantados de ayudarlo. Gracias por elegirnos como su proveedor de confianza de productos naturistas y esperamos servirle pronto.</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-6 col-12 d-flex justify-content-center">
                        <?php echo Asset::img('distribucion-img-1.jpg', array('alt' => 'Distribucion', 'class' => 'img-fluid')); ?>
                    </div>
                </div>
                <div class="row py-3">
                    <?php if(!empty($brands)): ?>
                        <?php foreach($brands as $brand): ?>
                            <div class="col-md-4 col-6 gray-img d-flex justify-content-center">
                                    <?php 
                                        if (file_exists(DOCROOT.'assets/uploads/'.$brand['image']))
                                        {
                                            echo Html::anchor('tienda/marca/'.$brand['slug'], Asset::img($brand['image'], array('alt' => $brand['name'], 'class' => 'img-fluid pb-4')), array('title' => $brand['name']));
                                        }else{
                                            echo Html::anchor('tienda/marca/'.$brand['slug'], Asset::img('sw_no_brand.png', array('alt' => $brand['name'], 'class' => 'img-fluid pb-4')), array('title' => $brand['name']));
                                        }
                                    ?>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</section>

<div class="bg-brands pb-5">
    <section id="bg-distribution-contact" class="d-flex align-items-center">
        <div class="container-fluid">
            <div class="row ">
                <div class="col-12">
                    <div class="d-flex justify-content-center">
                        <span class="text-white font-weight-bold d-block mt-5 tb-md-0">DERSEAS MAS INFORMACION</span>
                    </div>
                    <div class="d-flex justify-content-center pt-4">
                        <span class="text-white d-block text-center">CONTACTANOS</span>
                    </div>
                    <div class="d-flex justify-content-center pt-3">
                        <?php echo Html::anchor('contacto', 'Contacto', array('title' => 'Contacto', 'class' => 'btn btn-primary text-center d-block mb-5')); ?>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>
