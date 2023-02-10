<?php

/**
 * CONTROLADOR GRACIAS
 *
 * @package  app
 * @extends  Controller_Base
 */
class Controller_Gracias extends Controller_Base
{
	/**
	 * BEFORE
	 *
	 * REVISA SI EL USUARIO TIENE UNA SESION,
	 * SI EXISTE REDIRECCIONA A MI CUENTA
	 *
	 * @return Void
	 */
	public function before()
	{
		# REQUERIDA PARA EL TEMPLATING
        parent::before();

        # SI EL USUARIO NO ESTA LOGEADO
        if(!Auth::check())
        {
            # SE REDIRECCIONA A INICIAR SESION
            Response::redirect_back('/', 'refresh');
        }

        # SE CREA LA INSTANCIA DE AUTH
        $this->auth = Auth::instance();
	}


	/**
    * INDEX
    *
    * MUESTRA LA PAGINA DE GRACIAS
    *
    * @access  public
    * @return  Void
    */
    public function action_index()
    {
        # SE INICIALIZA EL ARREGLO DATA
        $data = array();

        # SE INICIALIZAN LAS VARIABLES
        $total_products_price    = 0;
        $total_products_quantity = 0;

        # SE OBTIENE EL CUSTOMER
        $customer = Model_Customer::get_one(array('id_user' => $this->auth->get('id')));

        # SE OBTIENE EL ULTIMO PEDIDO CON STATUS 1
        $sale = Model_Sale::get_last_order_purchased($customer->id);

        # SE GUARDAN LOS ARTICULOS COMPRADOS
        foreach($sale->products as $sale_product)
        {
            # SE DECLARAN LAS VARIABLES
            $product_quantity = $sale_product->quantity;
            $price_cart       = $sale_product->price;

            # SE OBTIENE EL TOTAL DEL PRODUCTO
            $total_product_price = $price_cart * $product_quantity;

            # SE CALCULA EL TOTAL EN CANTIDAD DE PRODUCTOS
            $total_products_quantity += $product_quantity;

            # SE CALCULA EL TOTAL DEL PRECIO DE LOS PRODUCTOS
            $total_products_price += $total_product_price;

            # SE ALMACENA LA INFORMACION EN EL CARRITO
            $cart_data[] = array(
                'id'                => $sale_product->product->id,
                'slug'              => $sale_product->product->slug,
                'name'              => $sale_product->product->name,
                'code'              => $sale_product->product->code,
                'image'             => $sale_product->product->image,
                'description'       => $sale_product->product->description,
                'price'             => array(
                    'original'      => array(
                        'regular'   => $sale_product->product->original_price,
                        'formatted' => number_format($sale_product->product->original_price, 2, '.', ',')
                    ),
                    'current'       => array(
                        'regular'   => $price_cart,
                        'formatted' => number_format($price_cart, 2, '.', ',')
                    ),
                    'total'         => array(
                        'regular'   => $total_product_price,
                        'formatted' => number_format($total_product_price, 2, '.', ',')
                    ),
                ),
                'available'         => (int)$sale_product->product->available,
                'quantity'          => array(
                    'valid'         => (int)$product_quantity,
                    'current'       => (int)$product_quantity,
                ),
            );
        }

        # SE ALMACENAN LA INFORMACION DE LA VISTA
        $data['cart_products'] = $cart_data;
        $data['total_price']   = number_format($sale->total, 2, '.', ',');

        # SE CARGA LA VISTA DE INICIO
        $this->template->title       = 'Checkout - Gracias | Natura y Mas';
        $this->template->description = 'Checkout de Natura y Mas.';
        $this->template->content     = View::forge('gracias/index', $data, false);
    }


	/**
    * TRANSFERENCIA
    *
    * MUESTRA LA PAGINA DE GRACIAS
    *
    * @access  public
    * @return  Void
    */
    public function action_transferencia()
    {
        # SE INICIALIZAN LAS VARIABLES
        $data                    = array();
        $total_products_price    = 0;
        $total_products_quantity = 0;

        # SE OBTIENE EL CUSTOMER
        $customer = Model_Customer::get_one(array('id_user' => $this->auth->get('id')));

        # SE OBTIENE EL ULTIMO PEDIDO CON STATUS 2
        $sale = Model_Sale::get_last_order_transfer($customer->id);

        # SE GUARDAN LOS ARTICULOS COMPRADOS
        foreach($sale->products as $sale_product)
        {
            # SE DECLARAN LAS VARIABLES
            $product_quantity = $sale_product->quantity;
            $price_cart       = $sale_product->price;

            # SE OBTIENE EL TOTAL DEL PRODUCTO
            $total_product_price = $price_cart * $product_quantity;

            # SE CALCULA EL TOTAL EN CANTIDAD DE PRODUCTOS
            $total_products_quantity += $product_quantity;

            # SE CALCULA EL TOTAL DEL PRECIO DE LOS PRODUCTOS
            $total_products_price += $total_product_price;

            # SE ALMACENA LA INFORMACION EN EL CARRITO
            $cart_data[] = array(
                'id'                => $sale_product->product->id,
                'slug'              => $sale_product->product->slug,
                'name'              => $sale_product->product->name,
                'code'              => $sale_product->product->code,
                'image'             => $sale_product->product->image,
                'description'       => $sale_product->product->description,
                'price'             => array(
                    'original'      => array(
                        'regular'   => $sale_product->product->original_price,
                        'formatted' => number_format($sale_product->product->original_price, 2, '.', ',')
                    ),
                    'current'       => array(
                        'regular'   => $price_cart,
                        'formatted' => number_format($price_cart, 2, '.', ',')
                    ),
                    'total'         => array(
                        'regular'   => $total_product_price,
                        'formatted' => number_format($total_product_price, 2, '.', ',')
                    ),
                ),
                'available'         => (int)$sale_product->product->available,
                'quantity'          => array(
                    'valid'         => (int)$product_quantity,
                    'current'       => (int)$product_quantity,
                ),
            );
        }

		# SE ENVIA EL CORREO AL USUARIO
		$this->send_user_mail($sale->id);

		# SE ENVIA EL CORREO AL ADMINISTRADOR
		$this->send_admin_mail($sale->id);

        # SE ALMACENAN LA INFORMACION DE LA VISTA
		$data['shipping']             = ($sale->address_id == 0) ? false : true;
        $data['sale_id']              = $sale->id;
        $data['cart_products']        = $cart_data;
		$data['total_products_price'] = number_format($sale->total, 2, '.', ',');
        $data['total_price']          = number_format($sale->total, 2, '.', ',');

        # SE CARGA LA VISTA DE INICIO
        $this->template->title       = 'Checkout - Gracias | naturaymas';
        $this->template->description = 'Checkout - Gracias';
        $this->template->content     = View::forge('gracias/transferencia', $data, false);
    }


	/**
     * SEND USER MAIL
     *
     * ENVIA POR EMAIL UN MENSAJE DEL PEDIDO AL USUARIO
     *
     * @access  private
     * @return  Boolean
     */
    private function send_user_mail($sale_id = 0)
    {
		# SE INICIALIZAN LAS VARIABLES
		$data               = array();
		$address_html       = '';
		$products_html      = '';
		$transfer_data_html = '';

		# SE BUSCA LA INFORMACION A TRAVES DEL MODELO
		$sale = Model_Sale::query()
		->related('products')
		->related('customer')
		->where('id', $sale_id)
		->where('status', 2)
		->get_one();

		# SI SE OBTIENE INFORMACION
		if(!empty($sale))
		{
			# SE BUSCA LA INFORMACION A TRAVES DEL MODELO
			$transfer_data = Model_Transfer_Datum::query()
			->where('id', 1)
			->get_one();

			# SI SE OBTIENE INFORMACION
			if(!empty($transfer_data))
			{
				# SE ALMACENA LA INFORMACION PARA LA VISTA
				$transfer_data_html = $transfer_data->info;
			}

			# SE RECORRE ELEMENTO POR ELEMENTO
			foreach($sale->products as $product)
			{
				# SE CREA EL HTML DE LOS PRODUCTOS
				$products_html .= Asset::img('thumb_'.$product->product->image, array('alt' => $product->product->name)).'
				<strong style="display: block; margin-bottom: 15px">'.$product->product->name.'</strong>
				<strong style="display: block;">Precio unitario:</strong>
				<span style="display: block; margin-bottom: 15px; color: #ee3530">$'.number_format($product->price, 2, '.', ',').'</span>
				<strong style="display: block;">Cantidad:</strong>
				<span style="display: block; margin-bottom: 15px; color: #ee3530">'.$product->quantity.'</span>
				<strong style="display: block;">Total:</strong>
				<span style="display: block; margin-bottom: 15px; color: #ee3530">$'.number_format($product->total, 2, '.', ',').'</span>
				';
			}

			# SI EXISTE DIRECCION
			if($sale->address_id != 0)
			{
				# SE CREA EL HTML DE LA DIRECCION DE ENVIO
				$address_html .= '<h1 style="margin: 0 0 10px 0; font-family: sans-serif; font-size: 25px; line-height: 30px; color: #333333; font-weight: normal;">Datos de envío</h1>
				<p>
					<span style="display: block; margin-bottom: 15px; color: #ee3530"><strong>Nombre: '.$sale->address->name.' '.$sale->address->last_name.'<br>
					Calle: '.$sale->address->street.' '.$sale->address->number.' '.$sale->address->internal_number.'<br>
					Colonia: '.$sale->address->colony.', Código Postal: '.$sale->address->zipcode.'<br>
					'.$sale->address->city.', '.$sale->address->state->name.', México<br>
					Teléfono: '.$sale->address->phone.'</strong></span>
				</p>';
			}

			# SE CREA EL CUERPO DEL CORREO
			$data['body'] =
			'<tr>
				<td style="background-color: #ffffff;">
					<table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%">
						<tr>
							<td style="padding: 20px; font-family: sans-serif; font-size: 15px; line-height: 20px; color: #555555;">
								<h1 style="margin: 0 0 10px 0; font-family: sans-serif; font-size: 25px; line-height: 30px; color: #333333; font-weight: normal;">¡Gracias por comprar en Natura y Mas!</h1>
								<p style="margin: 0;">
									<strong>ID de pedido:</strong> <span style="color: #ee3530">'.$sale->id.'</span><br>
									<strong>Fecha:</strong> <span style="color: #ee3530">'.date('d/m/Y', $sale->sale_date).'</span><br>
									<strong>Total:</strong> <span style="color: #ee3530">$'.number_format($sale->total, 2, '.', ',').'</span>
								</p>
								<p>
									Te recordamos que tu orden de pedido tiene una vigencia de 48 hrs, periodo en el cual deberá ser enviado tu comprobante de pago, en caso de no ser así los productos pasaran a stock disponible nuevamente y tu pedido no podrá ser procesado.
								</p>
								<h1 style="margin: 0 0 10px 0; font-family: sans-serif; font-size: 25px; line-height: 30px; color: #333333; font-weight: normal;">Formas de pago</h1>
								<p>
									<strong>Efectivo:</strong> Acudir al banco y realizar el depósito a la cuenta proporcionada, posteriormente enviar tu comprobante al correo: atencionaclientes@naturaymas.mx, no olvides poner en el asunto del correo tu ID de pedido.<br><br>
									<strong>Transferencia:</strong> Realizar la transferencia por el monto de la compra desde la pagina web o app de tu banco con los datos de la cuenta proporcionada, no olvides colocar tu ID de pedido en la parte de referencia o concepto de compra. Una vez realizado el pago, envía el comprobante al correo atencionaclientes@naturaymas.mx, en el asunto del correo deberás ponertu ID de pedido.<br><br>
									<strong>Datos de la cuenta a depositar:</strong><br><br>
									'.$transfer_data_html.'
								</p>
								'.$address_html.'
								<h1 style="margin: 0 0 10px 0; font-family: sans-serif; font-size: 25px; line-height: 30px; color: #333333; font-weight: normal;">Productos</h1>
								<p>
									'.$products_html.'
								</p>
								<h1 style="margin: 0 0 10px 0; font-family: sans-serif; font-size: 25px; line-height: 30px; color: #333333; font-weight: normal;">¿Necesitas asistencia?, Contáctanos:</h1>
								<p>
								*Vía correo: <span style="color: #ee3530">ventas@naturaymas.mx</span><br>
								*Teléfono: <span style="color: #ee3530">33 3942 7070</span>
								</p>
							</td>
						</tr>
					</table>
				</td>
			</tr>';

            # SE ENVIA UN EMAIL CON EL MENSAJE
            $email = Email::forge();
            $email->from('sistemas@naturaymas.mx', 'Natura y Mas');
            $email->reply_to('sistemas@naturaymas.mx', 'Natura y Mas');
            $email->to(array(
                $sale->customer->user->email => $sale->customer->name.' '.$sale->customer->last_name,
                //'omar@sectorweb.mx' => 'Sector Web',
                //'sistemas@naturaymas.mx' => 'Natura y Mas',
            ));
            $email->subject('naturaymas - Pedido realizado');
            $email->html_body(View::forge('email_templates/default', $data, false), false);

            # SE ENVIA EL EMAIL
            try
			{
                # SE ENVIA EL EMAIL
                if($email->send())
                {
                    # SE DEVUELVE TRUE
                    return true;
                }
            }
            catch(\EmailSendingFailedException $e)
            {
				# SE DEVUELVE FALSE
                return false;
            }
            catch(\EmailValidationFailedException $e)
            {
				# SE DEVUELVE FALSE
                return false;
            }
		}
		else
		{
			# SE DEVUELVE FALSE
			return false;
		}
    }


    /**
     * SEND ADMIN MAIL
     *
     * ENVIA POR EMAIL UN MENSAJE DEL PEDIDO AL USUARIO
     *
     * @access  private
     * @return  Boolean
     */
    private function send_admin_mail($sale_id = 0)
    {
		# SE INICIALIZAN LAS VARIABLES
		$data               = array();
		$address_html       = '';
		$products_html      = '';
		$transfer_data_html = '';

		# SE BUSCA LA INFORMACION A TRAVES DEL MODELO
		$sale = Model_Sale::query()
		->related('products')
		->related('customer')
		->where('id', $sale_id)
		->where('status', 2)
		->get_one();

		# SI SE OBTIENE INFORMACION
		if(!empty($sale))
		{
			# SE BUSCA LA INFORMACION A TRAVES DEL MODELO
			$transfer_data = Model_Transfer_Datum::query()
			->where('id', 1)
			->get_one();

			# SI SE OBTIENE INFORMACION
			if(!empty($transfer_data))
			{
				# SE ALMACENA LA INFORMACION PARA LA VISTA
				$transfer_data_html = $transfer_data->info;
			}

			# SE RECORRE ELEMENTO POR ELEMENTO
			foreach($sale->products as $product)
			{
				# SE CREA EL HTML DE LOS PRODUCTOS
				$products_html .= Asset::img('thumb_'.$product->product->image, array('alt' => $product->product->name)).'
				<strong style="display: block; margin-bottom: 15px">'.$product->product->name.'</strong>
				<strong style="display: block;">Precio unitario:</strong>
				<span style="display: block; margin-bottom: 15px; color: #ee3530">$'.number_format($product->price, 2, '.', ',').'</span>
				<strong style="display: block;">Cantidad:</strong>
				<span style="display: block; margin-bottom: 15px; color: #ee3530">'.$product->quantity.'</span>
				<strong style="display: block;">Total:</strong>
				<span style="display: block; margin-bottom: 15px; color: #ee3530">$'.number_format($product->total, 2, '.', ',').'</span>
				';
			}

			# SI EXISTE DIRECCION
			if($sale->address_id != 0)
			{
				# SE CREA EL HTML DE LA DIRECCION DE ENVIO
				$address_html .= '<h1 style="margin: 0 0 10px 0; font-family: sans-serif; font-size: 25px; line-height: 30px; color: #333333; font-weight: normal;">Datos de envío</h1>
				<p>
					<span style="display: block; margin-bottom: 15px; color: #ee3530"><strong>Nombre: '.$sale->address->name.' '.$sale->address->last_name.'<br>
					Calle: '.$sale->address->street.' '.$sale->address->number.' '.$sale->address->internal_number.'<br>
					Colonia: '.$sale->address->colony.', Código Postal: '.$sale->address->zipcode.'<br>
					'.$sale->address->city.', '.$sale->address->state->name.', México<br>
					Teléfono: '.$sale->address->phone.'</strong></span>
				</p>';
			}

			# SE CREA EL CUERPO DEL CORREO
			$data['body'] =
			'<tr>
				<td style="background-color: #ffffff;">
					<table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%">
						<tr>
							<td style="padding: 20px; font-family: sans-serif; font-size: 15px; line-height: 20px; color: #555555;">
								<h1 style="margin: 0 0 10px 0; font-family: sans-serif; font-size: 25px; line-height: 30px; color: #333333; font-weight: normal;">¡Gracias por comprar en Natura y Mas!</h1>
								<p style="margin: 0;">
									<strong>ID de pedido:</strong> <span style="color: #ee3530">'.$sale->id.'</span><br>
									<strong>Fecha:</strong> <span style="color: #ee3530">'.date('d/m/Y', $sale->sale_date).'</span><br>
									<strong>Total:</strong> <span style="color: #ee3530">$'.number_format($sale->total, 2, '.', ',').'</span>
								</p>
								<p>
									Te recordamos que tu orden de pedido tiene una vigencia de 48 hrs, periodo en el cual deberá ser enviado tu comprobante de pago, en caso de no ser así los productos pasarán a stock disponible nuevamente y tu pedido no podrá ser procesado.
								</p>
								<h1 style="margin: 0 0 10px 0; font-family: sans-serif; font-size: 25px; line-height: 30px; color: #333333; font-weight: normal;">Formas de pago</h1>
								<p>
									<strong>Efectivo:</strong> Acudir al banco y realizar el depósito a la cuenta proporcionada, posteriormente enviar tu comprobante al correo: atencionaclientes@naturaymas.mx, no olvides poner en el asunto del correo tu ID de pedido.<br><br>
									<strong>Transferencia:</strong> Realizar la transferencia por el monto de la compra desde la pagina web o app de tu banco con los datos de la cuenta proporcionada, no olvides colocar tu ID de pedido en la parte de referencia o concepto de compra. Una vez realizado el pago, envía el comprobante al correo atencionaclientes@naturaymas.mx, en el asunto del correo deberás ponertu ID de pedido.<br><br>
									<strong>Datos de la cuenta a depositar:</strong><br><br>
									'.$transfer_data_html.'
								</p>
								'.$address_html.'
								<h1 style="margin: 0 0 10px 0; font-family: sans-serif; font-size: 25px; line-height: 30px; color: #333333; font-weight: normal;">Productos</h1>
								<p>
									'.$products_html.'
								</p>
								<h1 style="margin: 0 0 10px 0; font-family: sans-serif; font-size: 25px; line-height: 30px; color: #333333; font-weight: normal;">¿Necesitas asistencia?, Contáctanos:</h1>
								<p>
								*Vía correo: <span style="color: #ee3530">ventas@naturaymas.mx</span><br>
								*Teléfono: <span style="color: #ee3530">33 3942 7070</span>
								</p>
							</td>
						</tr>
					</table>
				</td>
			</tr>';

            # SE ENVIA UN EMAIL CON EL MENSAJE
            $email = Email::forge();
            $email->from('sistemas@naturaymas.mx', 'Natura y Mas');
            $email->reply_to($sale->customer->user->email, $sale->customer->name.' '.$sale->customer->last_name);
            $email->to(array(
                'sistemas@naturaymas.mx' => 'Natura y Mas',
                //'omar@sectorweb.mx' => 'Sector Web',
            ));
            $email->subject('naturaymas - Para confirmar pedido realizado  por transferencia en la tienda en linea');
            $email->html_body(View::forge('email_templates/default', $data, false), false);

            # SE ENVIA EL EMAIL
            try
			{
                # SE ENVIA EL EMAIL
                if($email->send())
                {
                    # SE DEVUELVE TRUE
                    return true;
                }
            }
            catch(\EmailSendingFailedException $e)
            {
				# SE DEVUELVE FALSE
                return false;
            }
            catch(\EmailValidationFailedException $e)
            {
				# SE DEVUELVE FALSE
                return false;
            }
		}
		else
		{
			# SE DEVUELVE FALSE
			return false;
		}
    }
}
