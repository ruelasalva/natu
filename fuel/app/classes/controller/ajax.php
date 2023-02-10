<?php

/**
 * The Ajax Controller.
 *
 * A basic controller example.  Has examples of how to set the
 * response body and status.
 *
 * @package  app
 * @extends  Controller
 */
class Controller_Ajax extends Controller_Rest
{
    /**
     * SEND MAIL
     *
     * ENVIA POR EMAIL UN MENSAJE AL ADMINISTRADOR
     *
     * @access  public
     * @return  Object
     */
    public function post_send_mail()
    {
        # SE INICIALIZAN LAS VARIABLES
        $msg = 'error';

        # SE OBTIENE LOS DATOS ENVIADOS POR AJAX
        $name      = Input::post('name');
        $last_name = Input::post('last_name');
        $phone     = Input::post('phone');
        $mail      = Input::post('email');
        $message   = Input::post('message');

        # SE CREA EL CUERPO DEL CORREO
        $data['body'] =
        '<tr>
            <td style="background-color: #ffffff;">
                <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%">
                    <tr>
                        <td style="padding: 20px; font-family: sans-serif; font-size: 15px; line-height: 20px; color: #555555;">
                            <h1 style="margin: 0 0 10px 0; font-family: sans-serif; font-size: 25px; line-height: 30px; color: #333333; font-weight: normal;">Nuevo mensaje de Sitio Web.</h1>
                            <p style="margin: 0;">Este mesaje ha sido enviado desde la pǵina web de <strong>Natura y mas</strong>, con la siguiente información:</p>
                            <p>
                                <strong style="display: block;">Nombre:</strong>
                                <span style="display: block; margin-bottom: 15px">'.$name.'</span>
								<strong style="display: block;">Apellidos:</strong>
								<span style="display: block; margin-bottom: 15px">'.$last_name.'</span>
                                <strong style="display: block;">Teléfono:</strong>
                                <span style="display: block; margin-bottom: 15px">'.$phone.'</span>
                                <strong style="display: block;">Correo electrónico:</strong>
                                <span style="display: block; margin-bottom: 15px">'.$mail.'</span>
                                <strong style="display: block;">Mensaje:</strong>
                                <span style="display: block; margin-bottom: 15px">'.$message.'</span>
                            </p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>';

        # SE ENVIA UN EMAIL CON EL MENSAJE
        $email = Email::forge();
        $email->from('sistemas@naturaymas.com.mx', 'Natura y mas');
        $email->reply_to($mail, $name);
        $email->to(array(
            //'admin@naturaymas.com.mx' => 'Natura y mas',
            'sistemas@naturaymas.com.mx' => 'Natura y mas',
            'osiris@naturaymas.com.mx' => 'Osiris Lopez'
        ));
        $email->subject('Natura y mas - Mensaje de contacto');
        $email->html_body(View::forge('email_templates/default', $data, false), false);

        # SE ENVIA EL EMAIL
        try{
            # SE ENVIA EL EMAIL
            if($email->send())
            {
                # SE ESTABLECE EL MENSAJE DE EXITO
                $msg = 'ok';
            }
        }
        catch(\EmailSendingFailedException $e)
        {
            $msg = 'error';
        }
        catch(\EmailValidationFailedException $e)
        {
            $msg = 'error';
        }

        # SE ENVIA EL ARREGLO CON LA RESPUESTA
        $this->response(array(
            'msg' => $msg
        ));
    }


    /**
     * SEND MAIL PRODUCT
     *
     * ENVIA POR EMAIL UN MENSAJE DE COTIZACION AL ADMINISTRADOR
     *
     * @access  public
     * @return  Object
     */
    public function post_send_mail_product()
    {
        # SE INICIALIZAN LAS VARIABLES
        $msg = 'error';

        # SE OBTIENE LOS DATOS ENVIADOS POR AJAX
        $name      = Input::post('name');
        $last_name = Input::post('last_name');
        $phone     = Input::post('phone');
        $mail      = Input::post('email');
        $message   = Input::post('message');
        $product   = Input::post('product');

        # SE CREA EL CUERPO DEL CORREO
        $data['body'] =
        '<tr>
            <td style="background-color: #ffffff;">
                <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%">
                    <tr>
                        <td style="padding: 20px; font-family: sans-serif; font-size: 15px; line-height: 20px; color: #555555;">
                            <h1 style="margin: 0 0 10px 0; font-family: sans-serif; font-size: 25px; line-height: 30px; color: #333333; font-weight: normal;">Nuevo mensaje de Sitio Web.</h1>
                            <p style="margin: 0;">Este mesaje ha sido enviado desde la pǵina web de <strong>Natura y mas</strong>, con la siguiente información:</p>
                            <p>
                                <strong style="display: block;">Nombre:</strong>
                                <span style="display: block; margin-bottom: 15px">'.$name.'</span>
								<strong style="display: block;">Apellidos:</strong>
								<span style="display: block; margin-bottom: 15px">'.$last_name.'</span>
                                <strong style="display: block;">Teléfono:</strong>
                                <span style="display: block; margin-bottom: 15px">'.$phone.'</span>
                                <strong style="display: block;">Correo electrónico:</strong>
                                <span style="display: block; margin-bottom: 15px">'.$mail.'</span>
                                <strong style="display: block;">Producto:</strong>
                                <span style="display: block; margin-bottom: 15px">'.$product.'</span>
                                <strong style="display: block;">Mensaje:</strong>
                                <span style="display: block; margin-bottom: 15px">'.$message.'</span>
                            </p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>';

        # SE ENVIA UN EMAIL CON EL MENSAJE
        $email = Email::forge();
        $email->from('sistemas@naturaymas.com.mx', 'Natura y mas');
        $email->reply_to($mail, $name);
        $email->to(array(
            //'admin@naturaymas.com.mx' => 'Natura y mas',
            'sistemas@naturaymas.com.mx' => 'Natura y mas',
            'osiris@naturaymas.com.mx' => 'Osiris Lopez'
        ));
        $email->subject('Natura y mas - Mensaje de contacto');
        $email->html_body(View::forge('email_templates/default', $data, false), false);

        # SE ENVIA EL EMAIL
        try{
            # SE ENVIA EL EMAIL
            if($email->send())
            {
                # SE ESTABLECE EL MENSAJE DE EXITO
                $msg = 'ok';
            }
        }
        catch(\EmailSendingFailedException $e)
        {
            $msg = 'error';
        }
        catch(\EmailValidationFailedException $e)
        {
            $msg = 'error';
        }

        # SE ENVIA EL ARREGLO CON LA RESPUESTA
        $this->response(array(
            'msg' => $msg
        ));
    }


    /**
     * SET DEFAULT ADDRESS
     *
     * INDICA UNA DIRECCION COMO PREDETERMINADA
	 *
     *
     * @access  public
     * @return  Object
     */
    public function post_set_default_address()
    {
        # SE OBTIENE LOS DATOS ENVIADOS POR AJAX
        $address_id = Input::post('address');
        $msg        = 'error';

        # SI EL USUARIO ES VALIDO
        if(Request::forge('sectorweb/user/is_valid', false)->execute()->response->body)
        {
            # SE CREA LA INSTANCIA DE AUTH
            $auth = Auth::instance();

            # SE BUSCA AL CLIENTE
            $customer = Model_Customer::get_one(array('id_user' => $auth->get('id')));

            # SE BUSCA SI HAY UNA DIRECCION POR DEFECTO
            $query = array(
                'id_customer' => $customer->id,
                'default'     => 1
            );
            $default_address = Model_Customers_Address::get_one($query);

            # SI SE OBTUVO LA INFORMACION
            if(!empty($default_address))
            {
                # SE CAMBIA EL VALOR DEFAULT A 0
                Model_Customers_Address::do_update(array('default' => 0), $default_address->id);
            }

            # SE CAMBIA EL VALOR DEFAULT DE LA DIRECCION SELECCIONADA
            $query = array(
                'id'          => $address_id,
                'id_customer' => $customer->id,
                'default'     => 0
            );
            $set_default_address = Model_Customers_Address::get_one($query);

            # SI SE ENCONTRO LA INFORMACION
            if(!empty($set_default_address))
            {
                Model_Customers_Address::do_update(array('default' => 1), $set_default_address->id);

                # SE ESTABLECE EL MENSAJE DE EXITO
                $msg = 'ok';
            }
            else
            {
                $msg = 'not_found';
            }
        }
        else
        {
            # SE ESTABLECE EL MENSAJE DE ERROR
            $msg = 'invalid_user';
        }

        # SE ENVIA EL ARREGLO CON LA RESPUESTA
        $this->response(array(
            'msg' => $msg
        ));
    }


    /**
     * DELETE ADDRESS
     *
     * ELIMINA UNA DIRECCION DE UN CLIENTE
	 *
     *
     * @access  public
     * @return  Object
     */
    public function post_delete_address()
    {
        # SE OBTIENE LOS DATOS ENVIADOS POR AJAX
        $address_id = Input::post('address');
        $msg        = 'error';

        # SI EL USUARIO ES VALIDO
        if(Request::forge('sectorweb/user/is_valid', false)->execute()->response->body)
        {
            # SE CREA LA INSTANCIA DE AUTH
            $auth = Auth::instance();

            # SE BUSCA AL CLIENTE
            $customer = Model_Customer::get_one(array('id_user' => $auth->get('id')));

            # SE BUSCA LA DIRECCION
            $query = array(
                'id'          => $address_id,
                'id_customer' => $customer->id
            );
            $address = Model_Customers_Address::get_one($query);

            # SI SE ENCONTRO LA INFORMACION
            if(!empty($address))
            {
                # SE ELIMINA LA DIRECCION
                Model_Customers_Address::do_delete($address->id);

                # SE ESTABLECE EL MENSAJE DE EXITO
                $msg = 'ok';
            }
            else
            {
                # SE ESTABLECE EL MENSAJE DE ERROR
                $msg = 'not_found';
            }
        }
        else
        {
            # SE ESTABLECE EL MENSAJE DE ERROR
            $msg = 'invalid_user';
        }

        # SE ENVIA EL ARREGLO CON LA RESPUESTA
        $this->response(array(
            'msg' => $msg
        ));
    }


    /**
     * SET DEFAULT TAX DATUM
     *
     * INDICA UN RFC COMO PREDETERMINADO
	 *
     *
     * @access  public
     * @return  Object
     */
    public function post_set_default_tax_datum()
    {
        # SE OBTIENE LOS DATOS ENVIADOS POR AJAX
        $tax_datum_id = Input::post('tax_datum');
        $msg          = 'error';

        # SI EL USUARIO ES VALIDO
        if(Request::forge('sectorweb/user/is_valid', false)->execute()->response->body)
        {
            # SE CREA LA INSTANCIA DE AUTH
            $auth = Auth::instance();

            # SE BUSCA AL CLIENTE
            $customer = Model_Customer::get_one(array('id_user' => $auth->get('id')));

            # SE BUSCA SI HAY UNA RFC POR DEFECTO
            $query = array(
                'id_customer' => $customer->id,
                'default'     => 1
            );
            $default_tax_datum = Model_Customers_Tax_Datum::get_one($query);

            # SI SE OBTUVO LA INFORMACION
            if(!empty($default_tax_datum))
            {
                # SE CAMBIA EL VALOR DEFAULT A 0
                Model_Customers_Tax_Datum::do_update(array('default' => 0), $default_tax_datum->id);
            }

            # SE CAMBIA EL VALOR DEFAULT DEL RFC SELECCIONADO
            $query = array(
                'id'          => $tax_datum_id,
                'id_customer' => $customer->id,
                'default'     => 0
            );
            $set_default_tax_datum = Model_Customers_Tax_Datum::get_one($query);

            # SI SE ENCONTRO LA INFORMACION
            if(!empty($set_default_tax_datum))
            {
                Model_Customers_Tax_Datum::do_update(array('default' => 1), $set_default_tax_datum->id);

                # SE ESTABLECE EL MENSAJE DE EXITO
                $msg = 'ok';
            }
            else
            {
                $msg = 'not_found';
            }
        }
        else
        {
            # SE ESTABLECE EL MENSAJE DE ERROR
            $msg = 'invalid_user';
        }

        # SE ENVIA EL ARREGLO CON LA RESPUESTA
        $this->response(array(
            'msg' => $msg
        ));
    }


    /**
     * DELETE TAX DATUM
     *
     * ELIMINA UNA UN RFC
	 *
     *
     * @access  public
     * @return  Object
     */
    public function post_delete_tax_datum()
    {
        # SE OBTIENE LOS DATOS ENVIADOS POR AJAX
        $tax_datum_id = Input::post('tax_datum');
        $msg          = 'error';

        # SI EL USUARIO ES VALIDO
        if(Request::forge('sectorweb/user/is_valid', false)->execute()->response->body)
        {
            # SE CREA LA INSTANCIA DE AUTH
            $auth = Auth::instance();

            # SE BUSCA AL CLIENTE
            $customer = Model_Customer::get_one(array('id_user' => $auth->get('id')));

            # SE BUSCA EL RFC
            $query = array(
                'id'          => $tax_datum_id,
                'id_customer' => $customer->id
            );
            $tax_datum = Model_Customers_Tax_Datum::get_one($query);

            # SI SE ENCONTRO LA INFORMACION
            if(!empty($tax_datum))
            {
                # SI EXISTE LA CONSTANCIA DE SITUACION FISCAL
				if($tax_datum->csf != '')
				{
					# SI EL ARCHIVO RECIEN SUBIDO EXISTE
					if(file_exists(DOCROOT.'assets/csf/'.$tax_datum->csf))
					{
						# SE ELIMINAN EL ARCHIVO
						File::delete(DOCROOT.'assets/csf/'.$tax_datum->csf);
					}
				}

                # SE ELIMINA EL RFC
                Model_Customers_Tax_Datum::do_delete($tax_datum->id);

                # SE ESTABLECE EL MENSAJE DE EXITO
                $msg = 'ok';
            }
            else
            {
                # SE ESTABLECE EL MENSAJE DE ERROR
                $msg = 'not_found';
            }
        }
        else
        {
            # SE ESTABLECE EL MENSAJE DE ERROR
            $msg = 'invalid_user';
        }

        # SE ENVIA EL ARREGLO CON LA RESPUESTA
        $this->response(array(
            'msg' => $msg
        ));
    }


    /**
	 * ADD CART
	 *
	 * AGREGA UN PRODUCTO AL CARRITO
	 *
	 * @access  public
	 * @return  Object
	 */
	public function post_add_product_cart()
	{
        # SE OBTIENE LOS DATOS ENVIADOS POR AJAX
        $product_id = Input::json('idProduct');
        $quantity   = Input::json('quantity');

        # SE INICIALIZAN LOS ARREGLOS
        $msg                     = 'error';
        $cart_data               = array();
        $cart_unavailable        = array();
        $total_products_quantity = 0;
        $total_products_price    = 0;
		$total                   = 0;

        # SE ESTABLECEN LAS REGLAS DE VALIDACION
        $val = Validation::forge();
        $val->add_field('id_product', 'Id producto', 'required|numeric_min[1]|valid_string[numeric]');
        $val->add_field('quantity', 'Cantidad', 'required|numeric_min[1]|valid_string[numeric]');

        # SI LA VALIDACION ES CORRECTA
        if($val->run(array(
            'id_product' => $product_id,
            'quantity' => $quantity
        ))){
            # SE FORMATEAN NUMEROS A SOLO ENTEROS
            $product_id = (int)$product_id;
            $quantity   = (int)$quantity;

            # SE BUSCA EL PRODUCTO
            $product = Model_Product::get_valid(array('id_product' => $product_id));

			# SI SE OBTIENE LA INFORMACION
            if(!empty($product))
            {
                # SE OBTIENE LA SESION DEL CARRITO
                $cart = Session::get('cart');

                # SI YA EXISTE UN REGISTRO DEL PRODUCTO
                if(isset($cart[$product_id]))
                {
                    # SE ESTABLECE LA CANTIDAD
                    $cart[$product_id]['quantity'] = $cart[$product_id]['quantity'] + $quantity;
                }
                else
                {
                    # SI NO EXISTE EL ARREGLO DEL CARRITO
                    if(!$cart){
                        # SE CREA EL ARREGLO DEL CARRITO
                        $cart = array();
                    }

                    # SE CREA UN REGISTRO DEL PRODUCTO Y SU CANTIDAD
                    Arr::insert_assoc($cart, array($product_id => array('quantity' => $quantity)), count($cart));
                }

                # SE ACTUALIZA LA SESION CART
                Session::set('cart', $cart);

                # SE EJECUTA EL MODULO QUE DEPURA EL CARRITO
                $response = Request::forge('sectorweb/cart/debug', false)->execute()->response->body;

                # SE OBTIENE LA INFORMACION DE LA RESPUESTA
                $cart_data               = $response['cart_data'];
                $cart_unavailable        = $response['cart_unavailable'];
                $cart_session            = $response['cart_session'];
                $total_products_quantity = $response['total_products_quantity'];
                $total_products_price    = $response['total_products_price'];
				$total                   = $response['total'];

                # SE ESTABLECE EL MENSAJE DE EXITO
                $msg = 'ok';
            }
            else
            {
                $msg = 'product_not_found';
            }
        }
        else
        {
            $msg = 'invalid_request';
        }

        # SE ENVIA EL ARREGLO CON LAS OPCIONES
        $this->response(array(
            'msg'                     => $msg,
            'product_id'              => $product_id,
            'quantity'                => $quantity,
            'cart_data'               => $cart_data,
            'cart_unavailable'        => $cart_unavailable,
            'total_products_quantity' => $total_products_quantity,
            'total_products_price'    => $total_products_price,
			'total'                   => $total
        ));
    }


    /**
	 * EDIT CART
	 *
	 * MODIFICA LA CANTIDAD DE UN PRODUCTO EN EL CARRITO
	 *
	 * @access  public
	 * @return  Object
	 */
	public function post_edit_product_cart()
	{
        # SE INICIALIZA MSG
        $msg = 'error';

        # SI EL USUARIO ES VALIDO
        if(Request::forge('sectorweb/user/is_valid', false)->execute()->response->body)
        {
            # SE OBTIENE LOS DATOS ENVIADOS POR AJAX
            $product_id = Input::json('idProduct');
            $quantity   = Input::json('quantity');

            # SE INICIALIZAN LOS ARREGLOS
            $cart_data               = array();
            $cart_unavailable        = array();
            $total_products_quantity = 0;
            $total_products_price    = 0;
			$total                   = 0;
            $delete_product_id       = null;

            # SE ESTABLECEN LAS REGLAS DE VALIDACION
            $val = Validation::forge();
            $val->add_field('id_product', 'Id producto', 'required|numeric_min[0]|valid_string[numeric]');
            $val->add_field('quantity', 'Cantidad', 'required|numeric_min[0]|valid_string[numeric]');

            # SI LA VALIDACION ES CORRECTA
            if($val->run(array(
                'id_product' => $product_id,
                'quantity' => $quantity
            )))
            {
                # SE FORMATEAN NUMEROS A SOLO ENTEROS
                $product_id = (int)$product_id;
                $quantity   = (int)$quantity;

                # SI SE OBTUVO EL CARRITO
                if(Session::get('cart'))
                {
                    # SE OBTIENE LA SESION DEL CARRITO
                    $cart = Session::get('cart');

                    # SE BUSCA EL PRODUCTO
                    $product = Model_Product::get_valid(array('id_product' => $product_id));

                    # SI SE OBTUVO INFORMACION
                    if(!empty($product))
                    {
                        # SI LA CANTIDAD DEL PRODUCTO A MODIFICAR ES 0
                        if($quantity == 0)
                        {
                            # SE ELIMINA EL PRODUCTO DEL CARRITO
                            unset($cart[$product_id]);

                            # SE GUARDA EL ID DEL PRODUCTO A ELIMINAR
                            $delete_product_id = $product_id;
                        }
                        else
                        {
                            # SE ESTABLECE LA CANTIDAD
                            $cart[$product_id]['quantity'] = $quantity;
                        }

                        # SE GUARDA EL CARRITO
                        Session::set('cart', $cart);
                    }

                    # SI HAY UN PRODUCTO A ELIMINAR O EL CARRITO NO ESTA VACIO
                    if($delete_product_id != null or !empty($cart))
                    {
                        # SE EJECUTA EL MODULO QUE DEPURA EL CARRITO
                        $response = Request::forge('sectorweb/cart/debug', false)->execute(array($delete_product_id))->response->body;

                        # SE OBTIENE LA INFORMACION DE LA RESPUESTA
                        $cart_data               = $response['cart_data'];
                        $cart_session            = $response['cart_session'];
                        $cart_unavailable        = $response['cart_unavailable'];
                        $total_products_quantity = $response['total_products_quantity'];
                        $total_products_price    = $response['total_products_price'];
                        $total                   = $response['total'];
                    }

                    # SE CAMBIA EL VALOR DE MENSAJE A OK
                    $msg = 'ok';
                }
                else
                {
                    # SE ESTABLECE EL MENSAJE DE ERROR
                    $msg = 'no_cart';
                }
            }
            else
            {
                # SE ESTABLECE EL MENSAJE DE ERROR
                $msg = 'invalid_request';
            }
        }
        else
        {
            $msg = 'invalid_user';
        }

        # SE ENVIA EL ARREGLO CON LAS OPCIONES
        $this->response(array(
            'msg'                     => $msg,
            'product_id'              => $product_id,
            'quantity'                => $quantity,
            'cart_data'               => $cart_data,
            'cart_unavailable'        => $cart_unavailable,
            'total_products_quantity' => $total_products_quantity,
            'total_products_price'    => $total_products_price,
			'total'                   => $total
        ));
    }


    /**
	 * DELETE CART
	 *
	 * ELIMINA UN PRODUCTO DEL CARRITO
	 *
	 * @access  public
	 * @return  Object
	 */
	public function post_delete_product_cart()
	{
        # SI EL USUARIO ES VALIDO
        if(Request::forge('sectorweb/user/is_valid', false)->execute()->response->body)
        {
            # SE OBTIENE LOS DATOS ENVIADOS POR AJAX
            $product_id = Input::json('idProduct');

            # SE INICIALIZAN LAS VARIABLES
            $msg                     = 'error';
            $cart_data               = array();
            $total_products_quantity = 0;
            $total_products_price    = 0;
			$total                   = 0;
            $delete_product_id       = null;

            # SE ESTABLECEN LAS REGLAS DE VALIDACION
            $val = Validation::forge();
            $val->add_field('id_product', 'Id producto', 'required|numeric_min[0]|valid_string[numeric]');

            # SI LA VALIDACION ES CORRECTA
            if($val->run(array(
                'id_product' => $product_id,
            )))
            {
                # SE FORMATEAN NUMEROS A SOLO ENTEROS
                $product_id = (int)$product_id;

                # SI SE OBTUVO EL CARRITO
                if(Session::get('cart'))
                {
                    # SE OBTIENE LA SESION DEL CARRITO
                    $cart = Session::get('cart');

                    # SE BUSCA EL PRODUCTO
                    $product = Model_Product::get_valid(array('id_product' => $product_id));

                    # SI SE OBTUVO INFORMACION
                    if(!empty($product))
                    {
                        # SE ELIMINA EL PRODUCTO DEL CARRITO
                        unset($cart[$product_id]);

                        # SE GUARDA EL ID EN DELETE PRODUCT ID
                        $delete_product_id = $product_id;
                    }
                    else
                    {
                        # SI EL ID SE ENCUENTRA EN EL CARRITO
                        if(isset($cart[$product_id]))
                        {
                            # SE ELIMINA DEL CARRITO
                            unset($cart[$product_id]);
                        }
                    }

                    # SE GUARDA EL CARRITO
                    Session::set('cart', $cart);

                    # SI HAY UN PRODUCTO A ELIMINAR O EL CARRITO NO ESTA VACIO
                    if($delete_product_id != null or !empty($cart))
                    {
                        # SE EJECUTA EL MODULO QUE DEPURA EL CARRITO
                        $response = Request::forge('sectorweb/cart/debug', false)->execute(array($delete_product_id))->response->body;

                        # SE OBTIENE LA INFORMACION DE LA RESPUESTA
                        $cart_data               = $response['cart_data'];
                        $cart_session            = $response['cart_session'];
                        $cart_unavailable        = $response['cart_unavailable'];
                        $total_products_quantity = $response['total_products_quantity'];
                        $total_products_price    = $response['total_products_price'];
                        $total                   = $response['total'];
                    }

                    # SE CAMBIA EL VALOR DE MENSAJE A OK
                    $msg = 'ok';
                }
                else
                {
                    # SE ESTABLECE EL MENSAJE DE ERROR
                    $msg = 'no_cart';
                }
            }
            else
            {
                # SE ESTABLECE EL MENSAJE DE ERROR
                $msg = 'invalid_request';
            }
        }
        else
        {
            # SE ESTABLECE EL MENSAJE DE ERROR
            $msg = 'invalid_user';
        }

        # SE ENVIA EL ARREGLO CON LAS OPCIONES
        $this->response(array(
            'msg'                     => $msg,
            'product_id'              => $product_id,
            'quantity'                => 0,
            'cart_data'               => $cart_data,
            'cart_unavailable'        => $cart_unavailable,
            'total_products_quantity' => $total_products_quantity,
            'total_products_price'    => $total_products_price,
			'total'                   => $total
        ));
	}


    /**
	 * PRODUCTS AVAILABLES
	 *
	 * CREA O ELIMINA UNA SESION
	 *
	 * @access  public
	 * @return  Object
	 */
	public function post_products_availables()
	{
        # SE OBTIENE LOS DATOS ENVIADOS POR AJAX
        $msg   = 'error';
        $value = Input::post('value');

        # SE ESTABLECEN LAS REGLAS DE VALIDACION
        $val = Validation::forge();
        $val->add_field('value', 'Valor', 'required|valid_string[numeric]|numeric_between[0,1]');

        # SI LA VALIDACION ES CORRECTA
        if($val->run(array(
            'value' => $value
        )))
        {
            # SI EL VALOR ES 1
            if($val->validated('value') == 1)
            {
                # SE CREA LA SESION PRODUCTS_AVALIABLE
                Session::set('products_available', true);
            }
            else
            {
                # SE ELIMINA LA SESION PRODUCTS_AVALIABLE
                Session::delete('products_available');
            }

            # SE ESTABLECE EL MENSAJE DE EXITO
            $msg = 'ok';
        }

        # SE ENVIA EL ARREGLO CON LAS OPCIONES
        $this->response(array(
            'msg' => $msg
        ));
	}


    /**
     * BILL
     *
     * CREA UNA SESION DE FACTURA
     *
     * @access  public
     * @return  Object
     */
    public function post_bill()
    {
        # SE INICIALIZAN LAS VARIABLES
        $msg = 'error';

        # SE OBTIENE LOS DATOS ENVIADOS POR AJAX
        $bill = Input::post('bill');

        # SI LA OPCION ES SI
        if($bill == 1)
        {
            # SI SE CREA LA SESION DE FACTURA
            if(Session::set('bill', $bill))
            {
                # SE ESTABLECE EL MENSAJE DE EXITO
                $msg = 'ok';
            }
        }
        else
        {
            # SI SE ELIMINA LA SESION DE FACTURA
            if(Session::delete('bill'))
            {
                # SE INDICAN LAS UTILIDADES DE LOS METODOS DE LA CLASE
				$this->auth = Auth::instance();
                
                # SE OBTIENELA INFORMACION DEL CLIENTE
        		$customer = Model_Customer::get_one(array('id_user' => $this->auth->get('id')));

                # SE OBTIENE EL ULTIMO PEDIDO NO ENVIADO
                $sale = Model_Sale::get_last_order_not_sent($customer->id);

                # SE ELIMINA EL REGISTRO
                Model_Sales_Tax_Datum::do_delete($sale->id);

                # SE ESTABLECE EL MENSAJE DE EXITO
                $msg = 'ok';
            }
        }

        # SE ENVIA EL ARREGLO CON LA RESPUESTA
        $this->response(array(
            'msg' => $msg
        ));
    }


    /**
	 * ADD PRODUCT WISHLIST
	 *
	 * AGREGA UN PRODUCTO A DESEADOS
	 *
	 * @access  public
	 * @return  Object
	 */
	public function post_add_product_wishlist()
	{
        # SE OBTIENE LOS DATOS ENVIADOS POR AJAX
        $product_id = Input::post('product');

        # SE INICIALIZAN LAS VARIABLES
        $msg = 'ok';

        # SE ESTABLECEN LAS REGLAS DE VALIDACION
        $val = Validation::forge();
        $val->add_field('product_id', 'producto', 'required|numeric_min[1]|valid_string[numeric]');

        # SI LA VALIDACION ES CORRECTA
        if($val->run(array(
            'product_id' => $product_id,
        ))){
            # SE FORMATEAN NUMEROS A SOLO ENTEROS
            $product_id = (int)$product_id;

            # SE BUSCA EL PRODUCTO
            $product = Model_Product::get_valid(array('id_product' => $product_id));

			# SI SE OBTIENE LA INFORMACION
            if(!empty($product))
            {
                # SE HACE UNA INSTANCIA DE AUTH
                $this->auth = Auth::instance();

                # SI EXISTE UNA SESION DE USUARIO
                if($this->auth->check())
                {
                    # SE OBTIENE LA INFORMACION DEL CLIENTE
                    $customer = Model_Customer::get_one(array('id_user' => $this->auth->get('id')));

                    # SI SE OBTIENE INFORMACION
                    if(!empty($customer))
                    {
                        # SE BUSCA LA INFORMACION A TRAVES DEL MODELO
                        $wishlist = Model_Wishlist::query()
                        ->where('customer_id', $customer->id)
                        ->get_one();

                        # SI SE OBTIENE INFORMACION
                        if(!empty($wishlist))
                        {
                            # SE BUSCA LA INFORMACION A TRAVES DEL MODELO
                            $wishlist_product = Model_Wishlists_Product::query()
                            ->where('wishlist_id', $wishlist->id)
                            ->where('product_id', $product->id)
                            ->get_one();

                            # SI SE OBTIENE INFORMACION
                            if(!empty($wishlist_product))
                            {
                                # SE ESTABLECE EL MENSAJE DE ERROR
                                $msg = 'El producto ya existe en deseados.';
                            }
                            else
                            {
                                # SE CREA EL MODELO CON LA INFORMACION
                				$wishlist_product = new Model_Wishlists_Product(array(
                					'wishlist_id' => $wishlist->id,
                					'product_id'  => $product->id
                				));

                				# SE ALMACENA EL REGISTRO EN LA BASE DE DATOS
                				$wishlist_product->save();
                            }
                        }
                        else
                        {
                            # SE CREA EL MODELO CON LA INFORMACION
                            $wishlist = new Model_Wishlist(array(
                                'customer_id' => $customer->id
                            ));

                            # SI SE ALMACENA EL REGISTRO EN LA BASE DE DATOS
                            if($wishlist->save())
                            {
                                # SE CREA EL MODELO CON LA INFORMACION
                				$wishlist_product = new Model_Wishlists_Product(array(
                					'wishlist_id' => $wishlist->id,
                					'product_id'  => $product->id
                				));

                				# SE ALMACENA EL REGISTRO EN LA BASE DE DATOS
                				$wishlist_product->save();
                            }
                        }
                    }
                    else
                    {
                        # SE ESTABLECE EL MENSAJE DE ERROR
                        $msg = 'No se pudo encontrar información del usuario.';
                    }
                }
                else
                {
                    # SE ESTABLECE EL MENSAJE DE ERROR
                    $msg = 'Es necesario iniciar sesión para usar esta función.';
                }
            }
            else
            {
                # SE ESTABLECE EL MENSAJE DE ERROR
                $msg = 'No existe el producto solicitado.';
            }
        }
        else
        {
            # SE ESTABLECE EL MENSAJE DE ERROR
            $msg = 'No se puede agregar el producto con la información enviada.';
        }

        # SE ENVIA EL ARREGLO CON LAS OPCIONES
        $this->response(array(
            'msg' => $msg
        ));
    }


    /**
	 * DELETE PRODUCT WISHLIST
	 *
	 * ELIMINA UN PRODUCTO DE DESEADOS
	 *
	 * @access  public
	 * @return  Object
	 */
	public function post_delete_product_wishlist()
	{
        # SE OBTIENE LOS DATOS ENVIADOS POR AJAX
        $product_id = Input::post('product');

        # SE INICIALIZAN LAS VARIABLES
        $msg      = 'ok';
        $quantity = 0;

        # SE ESTABLECEN LAS REGLAS DE VALIDACION
        $val = Validation::forge();
        $val->add_field('product_id', 'producto', 'required|numeric_min[1]|valid_string[numeric]');

        # SI LA VALIDACION ES CORRECTA
        if($val->run(array(
            'product_id' => $product_id,
        ))){
            # SE FORMATEAN NUMEROS A SOLO ENTEROS
            $product_id = (int)$product_id;

            # SE BUSCA EL PRODUCTO
            $product = Model_Product::get_valid(array('id_product' => $product_id));

			# SI SE OBTIENE LA INFORMACION
            if(!empty($product))
            {
                # SE HACE UNA INSTANCIA DE AUTH
                $this->auth = Auth::instance();

                # SI EXISTE UNA SESION DE USUARIO
                if($this->auth->check())
                {
                    # SE OBTIENE LA INFORMACION DEL CLIENTE
                    $customer = Model_Customer::get_one(array('id_user' => $this->auth->get('id')));

                    # SI SE OBTIENE INFORMACION
                    if(!empty($customer))
                    {
                        # SE BUSCA LA INFORMACION A TRAVES DEL MODELO
                        $wishlist = Model_Wishlist::query()
                        ->where('customer_id', $customer->id)
                        ->get_one();

                        # SI SE OBTIENE INFORMACION
                        if(!empty($wishlist))
                        {
                            # SE BUSCA LA INFORMACION A TRAVES DEL MODELO
                            $wishlist_product = Model_Wishlists_Product::query()
                            ->where('wishlist_id', $wishlist->id)
                            ->where('product_id', $product->id)

                            ->get_one();

                            # SI SE OBTIENE INFORMACION
                            if(!empty($wishlist_product))
                            {
                                # SE ELIMINA EL REGISTRO EN LA BASE DE DATOS
                                $wishlist_product->delete();

                                # SE BUSCA LA INFORMACION A TRAVES DEL MODELO
                                $wishlist_products = Model_Wishlists_Product::query()
                                ->related('product')
                                ->where('wishlist_id', $wishlist->id)
                                ->where('product.status', 1)
                                ->where('product.deleted', 0);

                                # SE ALMACENA EL NUMERO DE PRODUCTOS
                                $quantity = $wishlist_products->count();
                            }
                            else
                            {
                                # SE ESTABLECE EL MENSAJE DE ERROR
                                $msg = 'No existe el producto en deseados.';
                            }
                        }
                        else
                        {
                            # SE ESTABLECE EL MENSAJE DE ERROR
                            $msg = 'No existe una lista de productos deseados.';
                        }
                    }
                    else
                    {
                        # SE ESTABLECE EL MENSAJE DE ERROR
                        $msg = 'No se pudo encontrar información del usuario.';
                    }
                }
                else
                {
                    # SE ESTABLECE EL MENSAJE DE ERROR
                    $msg = 'Es necesario iniciar sesión para usar esta función.';
                }
            }
            else
            {
                # SE ESTABLECE EL MENSAJE DE ERROR
                $msg = 'No existe el producto solicitado.';
            }
        }
        else
        {
            # SE ESTABLECE EL MENSAJE DE ERROR
            $msg = 'No se puede eliminar el producto con la información enviada.';
        }

        # SE ENVIA EL ARREGLO CON LAS OPCIONES
        $this->response(array(
            'msg'      => $msg,
            'quantity' => $quantity
        ));
    }
}
