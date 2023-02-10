<section class="bg-store" id="checkout">>
    <div class="container">
        <div class="row wrapper">
            <div class="col">
                <h2 class="text-uppercase space h2-checkout">Selecciona una dirección de envío</h2>
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
                        <li class="active">Enviar a</li>
                        <?php if(Session::get('bill') == 1): ?>
                        <li>Factura</li>
                        <?php endif; ?>
                        <li>Pago</li>
                        <li>Pedido realizado</li>
                    </ul>
                </div>
                <div class="row">
                    <div class="col-lg-8">
                        <div class="alert alert-secondary" role="alert">
                            <h4 class="alert-heading">¡Mensaje importante!</h4>
                            <hr>
                            <p class="text-justify">Selecciona o registra la dirección física en donde entregaremos tu pedido. De manera general siempre mostramos la dirección predeterminada y esta misma puede ser sustituida por otra elegida desde la <?php echo Html::anchor('mi-cuenta/direcciones', '<strong>Libreta de direcciones</strong>') ?>.<br> Recuerda que puedes registrar todas las direcciones de envío que requieras y administrarlas en la misma  <?php echo Html::anchor('mi-cuenta/direcciones', '<strong>Libreta de direcciones</strong>') ?>.</p>
                        </div>
                        <div class="rounded bg-white border p-3">
                            <?php if(!empty($addresses)): ?>
                            <?php echo Form::open(array(
                                'action'     => '',
                                'method'     => 'post',
                                'class'      => '',
                                'id'         => 'form_set_address',
                                'novalidate' => true
                            )); ?>
                                <div class="list-addresses">
                                    <div class="list-group">
                                        <?php foreach($addresses as $address): ?>
                                        <?php echo Form::radio('address', $address['id'], ($address['default']) ? true : false, array('id' => 'form_address_'.$address['id'], 'hidden' => true)); ?>
                                        <?php echo Form::label('
                                            <a href="#" class="list-group-item list-group-item-action rounded list-address disabled" data-radio="form_address_'.$address['id'].'">
                                                '.($address['default'] ? '<h2 class="text-uppercase text-left mb-2 text-white"><small>Dirección predeterminada</small></h2>' : '').'
                                                <div class="d-flex w-100 justify-content-between">
                                                    <h5 class="mb-1">'.$address['full_name'].'</h5>
                                                    <span class="status"></span>
                                                </div>
                                                <address class="mb-0">
                                                    '.$address['address'].'<br>
                                                    '.$address['region'].'<br>
                                                    '.$address['phone'].'
                                                </address>
                                            </a>
                                        ', 'address_'.$address['id']); ?>
                                        <?php endforeach; ?>
                                    </div>
                                </div>
                                <?php echo Form::button('submit', 'Siguiente', array('class' => 'btn btn-primary btn-block mt-2 text-uppercase', 'value' => 'set_address')); ?>
                            <?php echo Form::close(); ?>
                            <?php endif; ?>
                            <?php if(count($addresses) < 3): ?>
                            <button type="button" class="btn btn-outline-secondary btn-block mt-2 text-uppercase" data-toggle="modal" data-target="#modalAddressAdd">
                                Nueva dirección
                            </button>
                            <?php endif; ?>
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
<div class="modal fade" id="modalAddressAdd" tabindex="-1" role="dialog" aria-labelledby="modalAddressAddLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
        <div class="modal-content border-0 rounded-0">
            <div class="modal-header">
                <h5 class="modal-title text-primary">Dirección de envío</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <?php echo Form::open(array(
                'action'     => '',
                'method'     => 'post',
                'class'      => '',
                'id'         => 'form_add_address',
                'novalidate' => true
            )); ?>
            <div class="modal-body">
                    <div class="form-row">
                        <div class="form-group col-sm">
                            <?php echo Form::label('Nombre', 'name', array('class' => 'text-primary')); ?>
                            <?php echo Form::input('name', (isset($name) ? $name : ''), array('class' => 'form-control '.$classes['name'], 'placeholder' => 'Nombre')); ?>
                            <?php if(isset($errors['name'])): ?>
                            <div class="invalid-feedback"><?php echo $errors['name']; ?></div>
                            <?php endif; ?>
                        </div><!-- form-group -->
                        <div class="form-group col-sm">
                            <?php echo Form::label('Apellidos', 'last_name', array('class' => 'text-primary')); ?>
                            <?php echo Form::input('last_name', (isset($last_name) ? $last_name : ''), array('class' => 'form-control '.$classes['last_name'], 'placeholder' => 'Apellidos')); ?>
                            <?php if(isset($errors['last_name'])): ?>
                            <div class="invalid-feedback"><?php echo $errors['last_name']; ?></div>
                            <?php endif; ?>
                        </div><!-- form-group -->
                    </div>
                    <div class="form-group">
                        <?php echo Form::label('Teléfono', 'phone', array('class' => 'text-primary')); ?>
                        <?php echo Form::input('phone', (isset($phone) ? $phone : ''), array('class' => 'form-control '.$classes['phone'], 'placeholder' => 'Teléfono')); ?>
                        <?php if(isset($errors['phone'])): ?>
                        <div class="invalid-feedback"><?php echo $errors['phone']; ?></div>
                        <?php endif; ?>
                    </div><!-- form-group -->
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
                            <?php echo Form::label('Estado', 'state', array('class' => 'text-primary')); ?>
                            <?php echo Form::select('state', (isset($state) ? $state : ''), $states_opts, array('class' => 'form-control '.$classes['state'])); ?>
                            <?php if(isset($errors['state'])): ?>
                            <div class="invalid-feedback">Selecciona una opción.</div>
                            <?php endif; ?>
                        </div><!-- form-group -->
                        <div class="form-group col-lg">
                            <?php echo Form::label('Ciudad', 'city', array('class' => 'text-primary')); ?>
                            <?php echo Form::input('city', (isset($city) ? $city : ''), array('class' => 'form-control '.$classes['city'], 'placeholder' => 'Ciudad')); ?>
                            <?php if(isset($errors['city'])): ?>
                            <div class="invalid-feedback"><?php echo $errors['city']; ?></div>
                            <?php endif; ?>
                        </div><!-- form-group -->
                    </div>
                    <div class="form-group">
                        <?php echo Form::label('Información adicional (Entre que calles, referencias de negocios o lugares cercanos, etc.)', 'details', array('class' => 'text-primary')); ?>
                        <?php echo Form::textarea('details', (isset($details) ? $details : ''), array('class' => 'form-control '.$classes['details'], 'placeholder' => 'Información adicional', 'rows' => 4)); ?>
                        <?php if(isset($errors['details'])): ?>
                        <div class="invalid-feedback"><?php echo $errors['details']; ?></div>
                        <?php endif; ?>
                    </div><!-- form-group -->
                    <div class="form-group">
                        <div class="custom-control custom-checkbox">
                            <?php echo Form::checkbox('default', '1', false, array('class' => 'custom-control-input')); ?>
                            <?php echo Form::label('Hacer esta mi dirección de envío predeterminada', 'default', array('class' => 'custom-control-label text-primary')); ?>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <?php echo Form::button('submit', 'Agregar', array('class' => 'btn btn-primary btn-block text-uppercase', 'value' => 'add_address')); ?>
                </div>
            <?php echo Form::close(); ?>
        </div>
    </div>
</div>
