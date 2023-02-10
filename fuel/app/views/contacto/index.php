<section id="bg-contact" class="d-flex align-items-center">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12 d-flex justify-content-center">
                <h2 class="text-uppercase"></h2>
            </div>
        </div>
    </div>
</section>

<section id="contact" class="bg-brands">
    <div class="container">
        <div class="row py-5 ">
            <div class="col bg-white px-movil rounded border">
                <div class="row">
                    <div class="col-md-6 col-12">
                        <p class="p-contact text-gray text-contact text-justify">Para mayor información acerca de nuestros productos de línea, promociones, descuentos y precios especiales a mayoristas o simplemente necesitas ayuda para hacer tu pedido, ponte en contacto con nosotros a través de los siguientes medios:
                        </p>
                        <ul class="p-ul">
                            <li class="contact-list pb-5">
                                <a href="#" title="Dirección">
                                    <i class="lni-map-marker d-inline pt-2 text-secondary"></i>
                                    <address class="d-inline font-weight-bold text-gray">
                                        <span></span><br>
                                        <span class="ml-4">Guadalajara</span><br>
                                        <span class="ml-4">Jalisco</span>
                                    </address>
                                </a>
                            </li>
                            <li class="contact-list pb-5">
                                <a href="https://wa.me/3321897080" class="font-weight-bold text-gray" title="Whatsapp"><i class="lni-whatsapp mr-1 pt-2 text-secondary"> 33 2189 7080</i></a>
                                <span class="d-block ml-4 text-gray"> Solo Whatsapp</span>
                            </li>
                            <li class="contact-list">
                                <a href="mailto:contacto@naturaymas.com.m" title="Correo electrónico" class="font-weight-bold text-gray">
                                    <i class="lni-envelope mr-1 text-secondary"></i>
                                    contacto@naturaymas.com.mx
                                </a>
                                <span class="d-block ml-4 text-gray">Correo electrónico</span>
                            </li>
                        </ul>
                    </div>
                    <div class="col-md-6 col-12">
                        <div class="card rounded mb-4 p-form">
                            <p class="text-gray text-contact text-justify">En el siguiente formulario nos puedes indicar los productos que necesitas o hacernos tus comentarios, sugerencias y recomendaciones para brindarte un mejor servicio. Introduce tus datos, pulsa el botón enviar y un ejecutivo se pondrá en contacto contigo a la brevedad posible.</p>
                            <div class="card-body">
                                <?php echo Form::open(array(
                                    'action'     => '',
                                    'method'     => 'post',
                                    'class'      => '',
                                    'id'         => 'form_contact',
                                    'novalidate' => true
                                )); ?>
                                <div class="form-row">
                                    <div class="form-group col-sm">
                                        <?php echo Form::label('Nombre*', 'name', array('class' => 'text-gray')); ?>
                                        <?php echo Form::input('name', '', array('class' => 'form-control', 'placeholder' => 'Nombre')); ?>
                                    </div>
                                </div>
                                <div class="form-row">
                                    <div class="form-group col-sm">
                                        <?php echo Form::label('Apellidos*', 'last_name', array('class' => 'text-gray')); ?>
                                        <?php echo Form::input('last_name', '', array('class' => 'form-control', 'placeholder' => 'Apellidos')); ?>
                                    </div>
                                </div>
                                <div class="form-row">
                                    <div class="form-group col-sm">
                                        <?php echo Form::label('Email*', 'email', array('class' => 'text-gray')); ?>
                                        <?php echo Form::input('email', '', array('class' => 'form-control', 'placeholder' => 'Email')); ?>
                                    </div>
                                </div>
                                <div class="form-row">
                                    <div class="form-group col-sm">
                                        <?php echo Form::label('Teléfono*', 'phone', array('class' => 'text-gray')); ?>
                                        <?php echo Form::input('phone', '', array('class' => 'form-control', 'placeholder' => 'Teléfono')); ?>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <?php echo Form::label('Mensaje*', 'message', array('class' => 'text-gray')); ?>
                                    <?php echo Form::textarea('message', '', array('class' => 'form-control', 'placeholder' => 'Mensaje', 'rows' => 5)); ?>
                                </div>
                                <div class="form-group text-center">
                                    <?php echo Form::button('submit', 'Enviar', array('class' => 'btn btn-secondary btn-block text-uppercase', 'type' => 'submit')); ?>
                                </div>
                                <span class="status"></span>
                                <?php echo Form::close(); ?>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-12 px-0">
                        <div id="map"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
