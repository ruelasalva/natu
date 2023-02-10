<section class="bg-store" id="checkout">>
    <div class="container">
        <div class="row wrapper">
            <div class="col">
                <h2 class="text-uppercase space h2-checkout">Datos de facturación</h2>
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
                <?php if (Session::get_flash('info')): ?>
                    <div class="alert alert-info alert-dismissable">
                        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                        <?php echo Session::get_flash('info'); ?>
                    </div>
                <?php endif; ?>
                <div class="mb-3">
                    <ul class="breadcrumb-custom text-uppercase mb-0">
                        <?php if(!Session::get('type_delivery')): ?>
                        <li>Enviar a</li>
                        <?php endif; ?>
                        <li class="active">Factura</li>
                        <li>Pago</li>
                        <li>Pedido realizado</li>
                    </ul>
                </div>
                <div class="row">
                    <div class="col-lg-8">
                        <div class="alert alert-secondary" role="alert">
                            <h4 class="alert-heading">¡Mensaje importante!</h4>
                            <hr>
                            <p class="text-justify">Selecciona o registra un RFC para que podamos facturar tu pedido. De manera general siempre mostramos el RFC predeterminado y este mismo puede ser sustituido por otro desde la sección <?php echo Html::anchor('mi-cuenta/facturacion', '<strong>Datos de facturación</strong>') ?>.<br> Recuerda que puedes registrar todos los RFC que requieras y administrarlos en <?php echo Html::anchor('mi-cuenta/facturacion', '<strong>Datos de facturación</strong>') ?>.</p>
                        </div>
                        <div class="rounded bg-white border p-3">
                            <?php if(!empty($tax_data)): ?>
                            <?php echo Form::open(array(
                                'action'     => '',
                                'method'     => 'post',
                                'class'      => '',
                                'id'         => 'form_set_tax_datum',
                                'novalidate' => true
                            )); ?>
                                <div class="list-tax_data">
                                    <div class="list-group">
                                        <?php foreach($tax_data as $tax_datum): ?>
                                        <?php echo Form::radio('tax_datum', $tax_datum['id'], ($tax_datum['default']) ? true : false, array('id' => 'form_tax_datum_'.$tax_datum['id'], 'hidden' => true)); ?>
                                        <?php echo Form::label('
                                            <a href="#" class="list-group-item list-group-item-action rounded list-tax_datum disabled" data-radio="form_tax_datum_'.$tax_datum['id'].'">
                                                '.($tax_datum['default'] ? '<h2 class="text-uppercase text-left mb-2 text-white"><small>RFC predeterminado</small></h2>' : '').'
                                                <div class="d-flex w-100 justify-content-between">
                                                    <h5 class="mb-1">'.$tax_datum['rfc'].'</h5>
                                                    <span class="status"></span>
                                                </div>
                                                <address class="mb-0">
                                                    '.$tax_datum['business_name'].'<br>
                                                    '.$tax_datum['address'].'
                                                </address>
                                            </a>
                                        ', 'tax_datum_'.$tax_datum['id']); ?>
                                        <?php endforeach; ?>
                                    </div>
                                </div>
                                <?php echo Form::button('submit', 'Siguiente', array('class' => 'btn btn-primary btn-block mt-2 text-uppercase', 'value' => 'set_tax_datum')); ?>
                            <?php echo Form::close(); ?>
                            <?php endif; ?>
                            <button type="button" class="btn btn-outline-secondary btn-block mt-2 text-uppercase" data-toggle="modal" data-target="#modalTaxdatumAdd">
                                Nuevo RFC
                            </button>
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
                                                        <?php echo Html::anchor('producto/'.$product['slug'], Asset::img(Uri::base(false).'assets/uploads/thumb_'.$product['image'], array('alt' => $product['name'], 'class' => 'img-fluid d-block mx-auto')), array('title' => $product['name'], 'class' => '')); ?>
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

<!-- Modal -->
<div class="modal fade" id="modalTaxdatumAdd" tabindex="-1" role="dialog" aria-labelledby="modalTaxdatumAddLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
        <div class="modal-content border-0 rounded-0">
            <div class="modal-header">
                <h5 class="modal-title text-primary">Datos de facturación</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <?php echo Form::open(array(
                'action'     => '',
                'method'     => 'post',
                'class'      => '',
                'id'         => 'form_add_tax_datum',
                'enctype'    => 'multipart/form-data',
                'novalidate' => true
            )); ?>
                <div class="modal-body">
                    <div class="form-row">
                        <div class="form-group col-sm">
                            <?php echo Form::label('Razón social', 'business_name', array('class' => 'text-primary')); ?>
                            <?php echo Form::input('business_name', (isset($business_name) ? $business_name : ''), array('class' => 'form-control '.$classes['business_name'], 'placeholder' => 'Razón social')); ?>
                            <?php if(isset($errors['business_name'])): ?>
                            <div class="invalid-feedback"><?php echo $errors['business_name']; ?></div>
                            <?php endif; ?>
                        </div><!-- form-group -->
                        <div class="form-group col-sm">
                            <?php echo Form::label('RFC', 'rfc', array('class' => 'text-primary')); ?>
                            <?php echo Form::input('rfc', (isset($rfc) ? $rfc : ''), array('class' => 'form-control '.$classes['rfc'], 'placeholder' => 'RFC')); ?>
                            <?php if(isset($errors['rfc'])): ?>
                            <div class="invalid-feedback"><?php echo $errors['rfc']; ?></div>
                            <?php endif; ?>
                        </div><!-- form-group -->
                    </div>
                    <div class="form-row">
                        <div class="form-group col-lg-8">
                            <?php echo Form::label('Calle', 'street', array('class' => 'text-primary')); ?>
                            <?php echo Form::input('street', (isset($street) ? $street : ''), array('class' => 'form-control '.$classes['street'], 'placeholder' => 'Calle')); ?>
                            <?php if(isset($errors['street'])): ?>
                            <div class="invalid-feedback"><?php echo $errors['street']; ?></div>
                            <?php endif; ?>
                        </div><!-- form-group -->
                        <div class="form-group col">
                            <?php echo Form::label('# Exterior', 'number', array('class' => 'text-primary')); ?>
                            <?php echo Form::input('number', (isset($number) ? $number : ''), array('class' => 'form-control '.$classes['number'], 'placeholder' => '# Exterior')); ?>
                            <?php if(isset($errors['number'])): ?>
                            <div class="invalid-feedback"><?php echo $errors['number']; ?></div>
                            <?php endif; ?>
                        </div><!-- form-group -->
                        <div class="form-group col">
                            <?php echo Form::label('# Interior', 'internal_number', array('class' => 'text-primary')); ?>
                            <?php echo Form::input('internal_number', (isset($internal_number) ? $internal_number : ''), array('class' => 'form-control '.$classes['internal_number'], 'placeholder' => '# Interior')); ?>
                            <?php if(isset($errors['internal_number'])): ?>
                            <div class="invalid-feedback"><?php echo $errors['internal_number']; ?></div>
                            <?php endif; ?>
                        </div><!-- form-group -->
                    </div>
                    <div class="form-row">
                        <div class="form-group col-lg-8">
                            <?php echo Form::label('Colonia', 'colony', array('class' => 'text-primary')); ?>
                            <?php echo Form::input('colony', (isset($colony) ? $colony : ''), array('class' => 'form-control '.$classes['colony'], 'placeholder' => 'Colonia')); ?>
                            <?php if(isset($errors['colony'])): ?>
                            <div class="invalid-feedback"><?php echo $errors['colony']; ?></div>
                            <?php endif; ?>
                        </div><!-- form-group -->
                        <div class="form-group col-sm">
                            <?php echo Form::label('Código postal', 'zipcode', array('class' => 'text-primary')); ?>
                            <?php echo Form::input('zipcode', (isset($zipcode) ? $zipcode : ''), array('class' => 'form-control '.$classes['zipcode'], 'placeholder' => 'Código postal')); ?>
                            <?php if(isset($errors['zipcode'])): ?>
                            <div class="invalid-feedback"><?php echo $errors['zipcode']; ?></div>
                            <?php endif; ?>
                        </div><!-- form-group -->
                    </div>
                    <div class="form-row">
                        <div class="form-group col-lg">
                            <?php echo Form::label('Ciudad', 'city', array('class' => 'text-primary')); ?>
                            <?php echo Form::input('city', (isset($city) ? $city : ''), array('class' => 'form-control '.$classes['city'], 'placeholder' => 'Ciudad')); ?>
                            <?php if(isset($errors['city'])): ?>
                            <div class="invalid-feedback"><?php echo $errors['city']; ?></div>
                            <?php endif; ?>
                        </div><!-- form-group -->
                        <div class="form-group col-lg">
                            <?php echo Form::label('Estado', 'state', array('class' => 'text-primary')); ?>
                            <?php echo Form::select('state', (isset($state) ? $state : 'none'), $states_opts, array('class' => 'states-opts form-control '.$classes['state'])); ?>
                            <?php if(isset($errors['state'])): ?>
                            <div class="invalid-feedback">Selecciona una opción.</div>
                            <?php endif; ?>
                        </div><!-- form-group -->
                    </div>
                    <div class="form-row">
                        <div class="form-group col-lg">
                            <?php echo Form::label('Forma de pago', 'payment_method', array('class' => 'text-primary')); ?>
                            <?php echo Form::select('payment_method', (isset($payment_method) ? $payment_method : 'none'), $payment_methods_opts, array('class' => 'payment_methods-opts form-control '.$classes['payment_method'])); ?>
                            <?php if(isset($errors['payment_method'])): ?>
                            <div class="invalid-feedback">Selecciona una opción.</div>
                            <?php endif; ?>
                        </div><!-- form-group -->
                        <div class="form-group col-lg">
                            <?php echo Form::label('Uso del CFDI', 'cfdi', array('class' => 'text-primary')); ?>
                            <?php echo Form::select('cfdi', (isset($cfdi) ? $cfdi : 'none'), $cfdis_opts, array('class' => 'cfdis-opts form-control '.$classes['cfdi'])); ?>
                            <?php if(isset($errors['cfdi'])): ?>
                            <div class="invalid-feedback">Selecciona una opción.</div>
                            <?php endif; ?>
                        </div><!-- form-group -->
                    </div>
                    <div class="form-group">
                        <?php echo Form::label('Régimen fiscal', 'sat_tax_regime', array('class' => 'text-primary')); ?>
                        <?php echo Form::select('sat_tax_regime', (isset($sat_tax_regime) ? $sat_tax_regime : 'none'), $sat_tax_regimes_opts, array('class' => 'sat_tax_regimes-opts form-control '.$classes['sat_tax_regime'])); ?>
                        <?php if(isset($errors['sat_tax_regime'])): ?>
                        <div class="invalid-feedback">Selecciona una opción.</div>
                        <?php endif; ?>
                    </div><!-- form-group -->
                    <div class="form-group upload_box">
                        Por disposición oficial de las autoridades del SAT, si requieres factura debes proporcionar tu Constancia de Situación Fiscal (CSF) en formato PDF.<br>
                        En caso de no contar con ella en este momento, puedes realizar tu compra sin problema y enviar la CSF posteriormente por correo electrónico a la cuenta de contacto atencionaclientes@naturaymas.com.mx.
                        <?php echo Form::file('csf'); ?>
                        <small>
                            <strong>NOTA:</strong> El tamaño del archivo PDF no debe exceder de 15 Mb.
                        </small>
                    </div><!-- form-group -->
                    <div class="form-group">
                        <div class="custom-control custom-checkbox">
                            <?php echo Form::checkbox('default', true, (isset($default) ? $default : ''), array('class' => 'custom-control-input text-secondary')); ?>
                            <?php echo Form::label('Hacer este registro mi RFC predeterminado', 'default', array('class' => 'custom-control-label text-primary')); ?>
                        </div>
                    </div><!-- form-group -->
                </div>
                <div class="modal-footer">
                    <?php echo Form::button('submit', 'Agregar', array('class' => 'btn btn-primary btn-block text-uppercase', 'value' => 'add_tax_datum')); ?>
                </div>
            <?php echo Form::close(); ?>
        </div>
    </div>
</div>
