<?php

/**
* CONTROLADOR CHECKOUT
*
* @package  app
* @extends  Controller_Base
*/
class Controller_Checkout extends Controller_Base
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

		# SI SE OBTIENE EL CARRITO
		if(Session::get('cart'))
		{
			# SI EL CARRITO NO TIENE PRODUCTOS
			if(empty(Session::get('cart')))
			{
				# SE ELIMINA LA SESION DEL CARRITO
				Session::delete('cart');

				# SE ESTABLECE UNA SESION FLASH CON UN MENSAJE DE INFO
				Session::set_flash('general_info', '<h4 class="alert-heading">Tu carrito de compras está vacío.</h4><p>Te invitamos a agregar productos y finalizar tu compra.</p>');

				# SE REDIRECCIONA AL INDEX
				Response::redirect('/tienda');
			}
			else
			{
				# SI NO HAY USUARIO LOGEADO
				if(!Auth::check())
				{
					# SE REDIRECCIONA A INICIAR SESION
					Response::redirect('iniciar-sesion');
				}

				# SE INDICAN LAS UTILIDADES DE LOS METODOS DE LA CLASE
				$this->auth = Auth::instance();
			}
		}
		else
		{
			# SE ESTABLECE UNA SESION FLASH CON UN MENSAJE DE INFO
			Session::set_flash('general_info', '<h4 class="alert-heading">Tu carrito de compras está vacío.</h4><p>Te invitamos a agregar productos y finalizar tu compra.</p>');

			# SE REDIRECCIONA AL INDEX
			Response::redirect('/tienda');
		}
	}


	/**
	* INDEX
	*
	* CARGA LA VISTA DE CHECKOUT
	*
	* @access  public
	* @return  void
	*/
	public function action_index()
	{
		# SE INICIALIZAN LAS VARIABLES
		$data = array();

		# SE OBTIENE LA SESION DEL CARRITO
		$cart = Session::get('cart');

		# SE RECORRE PRODUCTO POR PRODUCTO
		foreach($cart as $id => $array)
		{
			# SE BUSCA EL PRODUCTO
			$product_cart = Model_Product::find($id);

			# SI SE OBTUVO EL RESULTADO
			if(!empty($product_cart))
			{
				# SE OBTIENE LA CANTIDAD SOLICITADA
				$product_quantity = $array['quantity'];
			}
		}

		# SE CARGA LA VISTA
		$this->template->title       = 'Checkout | Natura y Mas';
		$this->template->description = 'Checkout de Natura y Mas.';
		$this->template->content     = View::forge('checkout/index', $data);
	}


	/**
	* ENVIO
	*
	* MUESTRA LA PAGINA DE CHECKOUT / ENVIO
	*
	* @access  public
	* @return  Void
	*/
	public function action_envio()
	{
		# SE INICIALIZA LAS VARIABLES
		$data           = array();
		$addresses_info = array();

		# SE OBTIENE EL CUSTOMER
		$customer = Model_Customer::get_one(array('id_user' => $this->auth->get('id')));

		# SE OBTIENE EL ULTIMO PEDIDO NO ENVIADO
		$sale = Model_Sale::get_last_order_not_sent($customer->id);

		# SI EL TOTAL DEL PEDIDO ES MENOR A 1000
		if($sale->total < 1000)
		{
			# SE REDIRECCIONA AL USUARIO
			Response::redirect('checkout');
		}

		# SE DECLARA EL ARREGLO QUE CONTIENE LAS CLASES HTML DE LOS CAMPOS DEL FORM
		$classes = array(
			'state'           => null,
			'name'            => null,
			'last_name'       => null,
			'phone'           => null,
			'street'          => null,
			'number'          => null,
			'internal_number' => null,
			'colony'          => null,
			'zipcode'         => null,
			'city'            => null,
			'details'         => null,
			'default'         => null,
		);

		# SE OBTIENELA INFORMACION DEL CLIENTE
		$customer = Model_Customer::get_one(array('id_user' => $this->auth->get('id')));

		# SI SE UTILIZO EL METODO POST
		if(Input::method() == 'POST')
		{
			# DEPENDIENDO DEL CASO
			switch(Input::post('submit'))
			{
				# INDICAR DIRECCION
				case 'set_address':
					# SE CREA LA VALIDACION DE LOS CAMPOS
					$val = Validation::forge('set_address');
					$val->add_callable('rules');
					$val->add_field('address', 'Dirección de envío', 'required|valid_string[numeric]');

					# SI NO HAY NINGUN PROBLEMA CON LA VALIDACION
					if($val->run())
					{
						# SE OBTIENE LA DIRECCION
						$query = array(
							'id'          => $val->validated('address'),
							'id_customer' => $customer->id
						);
						$address = Model_Customers_Address::get_one($query);

						# SI SE OBTUVO LA INFORMACION
						if(!empty($address))
						{
							# SE CREA UN ARREGLO CON LOS DATOS
							$request = array(
								'state_id'        => $address->state_id,
								'name'            => $address->name,
								'last_name'       => $address->last_name,
								'phone'           => $address->phone,
								'street'          => $address->street,
								'number'          => $address->number,
								'internal_number' => $address->internal_number,
								'colony'          => $address->colony,
								'zipcode'         => $address->zipcode,
								'city'            => $address->city,
								'details'         => $address->details,
							);

							# SE OBTIENE EL ULTIMO PEDIDO NO ENVIADO
							$sale = Model_Sale::get_last_order_not_sent($customer->id);

							# SI LA ORDEN TIENE UNA DIRECCION RELACIONADA
							if(!empty($sale->address))
							{
								# SE ACTUALIZA LA DIRECCION RELACIONADA
								Model_Sales_Address::do_update($request, $sale->address_id);
							}
							else
							{
								# SE GUARDA LA NUEVA DIRECCION
								$new_address = Model_Sales_Address::set_new_record($request);

								# SI SE GUARDO LA INFORMACION
								if($new_address)
								{
									# SE RELACIONA LA VENTA CON LA NUEVA DIRECCION
									Model_Sale::do_update(array('address_id' => $new_address->id), $sale->id);
								}
								else
								{
									# SE ESTABLECE EL MENSAJE DE ERROR
									Session::set_flash('error', '<p>Algo inesperado ha ocurrido.</p>');
								}
							}

							# SI EXISTE LA SESION DE FACTURA
							if(Session::get('bill') == 1)
							{
								# SE REDIRECCIONA AL USUARIO
								Response::redirect('checkout/factura');
							}
							else
							{
								# SE REDIRECCIONA AL SIGUIENTE PASO
								Response::redirect('checkout/pagar');
							}
						}
						else
						{
							# SE ESTABLECE EL MENSAJE DE ERROR
							Session::set_flash('error', '<p>No se pudo encontrar la dirección seleccionada.</p>');
						}
					}
					else
					{
						# SE ESTABLECE EL MENSAJE DE ERROR
						Session::set_flash('error', '<p>Hemos encontrado errores en el formulario, intenta nuevamente.</p>');
					}
				break;

				# AGREGAR DIRECCION
				case 'add_address':
					# SE VERIFICA QUE EL CUSTOMER TENGA MENOS DE 3 DIRECCIONES ASOCIADAS
					if(count($customer->addresses) < 3)
					{
						# SE CREA LA VALIDACION DE LOS CAMPOS
						$val = Validation::forge('add_address');
						$val->add_callable('rules');

						$val->add_field('name', 'Nombres', 'required|min_length[1]|max_length[255]');
						$val->add_field('last_name', 'Apellidos', 'required|min_length[1]|max_length[255]');
						$val->add_field('phone', 'Teléfono', 'required|min_length[1]|max_length[255]');
						$val->add_field('street', 'Calle', 'required|min_length[1]|max_length[255]');
						$val->add_field('number', '# Exterior', 'required|min_length[1]|max_length[255]');
						$val->add_field('internal_number', '# Interior', 'min_length[1]|max_length[255]');
						$val->add_field('colony', 'Colonia', 'required|min_length[1]|max_length[255]');
						$val->add_field('zipcode', 'Código postal', 'required|min_length[1]|max_length[255]');
						$val->add_field('city', 'Ciudad', 'required|min_length[1]|max_length[255]');
						$val->add_field('state', 'Estado', 'required|valid_string[numeric]|numeric_between[1,32]');
						$val->add_field('details', 'Información adicional', 'min_length[1]|max_length[255]');

						# SI NO HAY NINGUN PROBLEMA CON LA VALIDACION
						if($val->run())
						{
							# SI LA DIRECCION FUE MARCADA COMO PREDETERMINADA
							if(Input::post('default'))
							{
								# SE BUSCA SI HAY UNA DIRECCION POR DEFECTO
								$query = array(
									'id_customer' => $customer->id,
									'default'     => 1
								);

								$default_address = Model_Customers_Address::get_one($query);

								# SI SE ENCONTRO LA INFORMACION
								if(!empty($default_address))
								{
									# SE CAMBIA EL VALOR DEFAULT A 0
									Model_Customers_Address::do_update(array('default' => 0), $default_address->id);
								}
							}

							# SE CREA UN ARREGLO CON LOS DATOS DEL PERFIL
							$request = array(
								'customer_id'     => $customer->id,
								'state_id'        => $val->validated('state'),
								'name'            => $val->validated('name'),
								'last_name'       => $val->validated('last_name'),
								'phone'           => $val->validated('phone'),
								'street'          => $val->validated('street'),
								'number'          => $val->validated('number'),
								'internal_number' => $val->validated('internal_number'),
								'colony'          => $val->validated('colony'),
								'zipcode'         => $val->validated('zipcode'),
								'city'            => $val->validated('city'),
								'details'         => $val->validated('details'),
								'default'         => (Input::post('default')) ? 1 : 0
							);

							# SI SE GUARDO CORRECTAMENTE
							if(Model_Customers_Address::set_new_record($request))
							{
								# SE ALMACENA EL MENSAJE DE EXITO EN LA SESION
								Session::set_flash('success', '<p>La dirección ha sido agregada correctamente.</p>');

								# SE REFRESCA LA PAGINA
								Response::redirect('checkout/envio');
							}
							else
							{
								# SE ESTABLECE EL MENSAJE DE ERROR
								Session::set_flash('error', '<p>Algo inesperado ha ocurrido.</p>');
							}
						}
						else
						{
							# SE ENVIAN LOS ERRORES
							$data['errors'] = $val->error();

							# SE ALMACENAN LAS CLASES HTML DE LOS CAMPOS VALIDADOS
							$classes['name']            = ($val->error('name')) ? 'is-invalid': 'is-valid';
							$classes['last_name']       = ($val->error('last_name')) ? 'is-invalid': 'is-valid';
							$classes['phone']           = ($val->error('phone')) ? 'is-invalid': 'is-valid';
							$classes['street']          = ($val->error('street')) ? 'is-invalid': 'is-valid';
							$classes['number']          = ($val->error('number')) ? 'is-invalid': 'is-valid';
							$classes['internal_number'] = ($val->error('intetnal_number')) ? 'is-invalid': 'is-valid';
							$classes['colony']          = ($val->error('colony')) ? 'is-invalid': 'is-valid';
							$classes['zipcode']         = ($val->error('zipcode')) ? 'is-invalid': 'is-valid';
							$classes['city']            = ($val->error('city')) ? 'is-invalid': 'is-valid';
							$classes['state']           = ($val->error('state')) ? 'is-invalid': 'is-valid';
							$classes['details']         = ($val->error('details')) ? 'is-invalid': 'is-valid';

							# SE ESTABLECE EL MENSAJE DE ERROR
							Session::set_flash('error', '<p>Hemos encontrado errores en el formulario, intenta nuevamente.</p>');
						}
					}
					else
					{
						# SE ESTABLECE EL MENSAJE DE ERROR
						Session::set_flash('error', '<p>Este perfil ya alcanzó el limite de direcciones asociadas.</p>');
					}
				break;

				default:
					# SE EJECUTA EL MODULO DE DESLOGUEO
					Request::forge('sectorweb/user/logout', false)->execute();

					# SE REDIRECCIONA AL USUARIO
					Response::redirect('/');
				break;
			}
		}

		# SI EL CLIENTE TIENE DIRECCIONES
		if(!empty($customer->addresses))
		{
			# SE RECORRE ELEMENTO POR ELEMENTO
			foreach($customer->addresses as $address)
			{
				# SE ALMACENA LA INFORMACION
				$addresses_info[] = array(
					'id'        => $address->id,
					'full_name' => $address->name.' '.$address->last_name,
					'phone'     => $address->phone,
					'address'   => $address->street.', #'.$address->number.','.($address->internal_number != '' ? ' #Int '.$address->internal_number.',' : '').' '.$address->colony.', CP:'.$address->zipcode,
					'region'    => $address->city.', '.$address->state->name,
					'default'   => $address->default
				);
			}

			# SE ORDENA EL ARREGLO
			$addresses_info = Arr::sort($addresses_info, 'default', 'desc');
		}

		# SE ALMACENAN LOS DATOS EN EL ARREGLO DATA
		$data['classes']     = $classes;
		$data['states_opts'] = Model_State::get_for_input();
		$data['addresses']   = $addresses_info;
		$data['email']       = $this->auth->get('email');
		$data['name']        = $customer->name;
		$data['last_name']   = $customer->last_name;

		# SE CARGA LA VISTA DE INICIO
		$this->template->title       = 'Checkout - Envío | Natura y Mas';
		$this->template->description = 'Checkout de Natura y Mas.';
		$this->template->content     = View::forge('checkout/envio', $data, false);
	}


	/**
	* FACTURA
	*
	* MUESTRA LA PAGINA DE CHECKOUT / FACTURA
	*
	* @access  public
	* @return  Void
	*/
	public function action_factura()
	{
		# SE INICIALIZA LAS VARIABLES
		$data            = array();
		$tax_data_info   = array();
		$business_name   = '';
		$rfc             = '';
		$street          = '';
		$number          = '';
		$internal_number = '';
		$colony          = '';
		$zipcode         = '';
		$city            = '';
		$state           = 'none';
		$payment_method  = 'none';
		$cfdi            = 'none';
		$sat_tax_regime  = 'none';

		# SI NO EXISTE LA SESION DE FACTURA
		if(!Session::get('bill'))
		{
			# SE REDIRECCIONA AL USUARIO
			Response::redirect('checkout/pagar');
		}

		# SE DECLARA EL ARREGLO QUE CONTIENE LAS CLASES
		$classes = array(
            'business_name'   => null,
            'rfc'             => null,
            'street'          => null,
            'number'          => null,
            'internal_number' => null,
            'colony'          => null,
            'zipcode'         => null,
			'city'            => null,
            'state'           => null,
			'payment_method'  => null,
            'cfdi'            => null,
            'sat_tax_regime'  => null,
			'default'         => null,
        );

		# SE OBTIENELA INFORMACION DEL CLIENTE
		$customer = Model_Customer::get_one(array('id_user' => $this->auth->get('id')));

		# SI SE UTILIZO EL METODO POST
		if(Input::method() == 'POST')
		{
			# DEPENDIENDO DEL CASO
			switch(Input::post('submit'))
			{
				# INDICAR RFC
				case 'set_tax_datum':
					# SE CREA LA VALIDACION DE LOS CAMPOS
					$val = Validation::forge('set_tax_datum');
					$val->add_callable('rules');
					$val->add_field('tax_datum', 'RFC', 'required|valid_string[numeric]');

					# SI NO HAY NINGUN PROBLEMA CON LA VALIDACION
					if($val->run())
					{
						# SE OBTIENE EL RFC
						$query = array(
							'id'          => $val->validated('tax_datum'),
							'id_customer' => $customer->id
						);
						$tax_datum = Model_Customers_Tax_Datum::get_one($query);

						# SI SE OBTUVO LA INFORMACION
						if(!empty($tax_datum))
						{
							# SE INICIALIZA LA VARIABLE PARA EL NOMBRE DE LA CSF
							$csf_name = '';

							# SI EXISTE LA CONSTANCIA DE SITUACION FISCAL
							if($tax_datum->csf != '')
							{
								# SE GENERA EL NOMBRE DEL ARCHIVO
								$csf_name = md5($tax_datum->csf.time());

								# SI EL ARCHIVO EXISTE
								if(file_exists(DOCROOT.'assets/csf/'.$tax_datum->csf))
								{
									# SE COPIA LA CONSTANCIA DE SITUACION FISCAL
									File::copy(DOCROOT.'assets/csf/'.$tax_datum->csf, DOCROOT.'assets/csf_final/'.$csf_name.'.pdf');
								}
							}

							# SE OBTIENE EL ULTIMO PEDIDO NO ENVIADO
							$sale = Model_Sale::get_last_order_not_sent($customer->id);

							# SE CREA UN ARREGLO CON LOS DATOS
							$request = array(
								'sale_id'           => $sale->id,
								'payment_method_id' => $tax_datum->payment_method_id,
								'cfdi_id'           => $tax_datum->cfdi_id,
								'sat_tax_regime_id' => $tax_datum->sat_tax_regime_id,
								'state_id'          => $tax_datum->state_id,
								'business_name'     => $tax_datum->business_name,
								'rfc'               => $tax_datum->rfc,
								'street'            => $tax_datum->street,
								'number'            => $tax_datum->number,
								'internal_number'   => $tax_datum->internal_number,
								'colony'            => $tax_datum->colony,
								'zipcode'           => $tax_datum->zipcode,
								'city'              => $tax_datum->city,
								'csf'               => $csf_name,
							);

							# SI LA ORDEN TIENE UN RFC RELACIONADA
							if(!empty($sale->tax_data))
							{
								# SE ACTUALIZA EL RFC RELACIONADO
								Model_Sales_Tax_Datum::do_update($request, $sale->id);
							}
							else
							{
								# SE GUARDA EL NUEVO RFC
								$new_tax_datum = Model_Sales_Tax_Datum::set_new_record($request);
							}

							# SE CREA LA SESION DE CONFIRMACION DE FACTURA
							Session::set('bill_confirmation', 1);

							# SE REDIRECCIONA AL USUARIO
							Response::redirect('checkout/pagar');
						}
						else
						{
							# SE ESTABLECE EL MENSAJE DE ERROR
							Session::set_flash('error', '<p>No se pudo encontrar la dirección seleccionada.</p>');
						}
					}
					else
					{
						# SE ESTABLECE EL MENSAJE DE ERROR
						Session::set_flash('error', '<p>Hemos encontrado errores en el formulario, intenta nuevamente.</p>');
					}
				break;

				# AGREGAR RFC
				case 'add_tax_datum':
					# SE INICIALIZAN LAS VARIABLES
					$csf = '';

					# SE OBTIENE LA REFERENCIA DEL CV
					$csf = $_FILES['csf']['name'];

					# SI EL USUARIO SUBE EL CV
					if(!empty($csf))
					{
						# SE ESTABLECE LA CONFIGURACION PARA LOS ARCHIVOS
						$csf_config = array(
							'auto_process'        => false,
							'path'                => DOCROOT.DS.'assets/csf',
							'randomize'           => true,
							'auto_rename'         => true,
							'normalize'           => true,
							'normalize_separator' => '-',
							'ext_whitelist'       => array('pdf'),
							'max_size'            => 20971520,
						);

						# SE INICIALIZA EL PROCESO UPLOAD CON LA CONFIGURACION ESTABLECIDA
						Upload::process($csf_config);

						# SI EL ARCHIVO ES VALIDO
						if(Upload::is_valid())
						{
							# SE SUBE EL ARCHIVO
							Upload::save();

							# SE OBTIENE LA INFORMACION DE LOS ARCHIVOS
							$value = Upload::get_files();

							# SE ALMACENA EL NOMBRE DEL ARCHIVO
							$csf = (isset($value[0]['saved_as'])) ? $value[0]['saved_as'] : '';
						}
					}

					# SE CREA LA VALIDACION DE LOS CAMPOS
					$val = Validation::forge('tax_data');
					$val->add_callable('rules');
					$val->add_field('business_name', 'Razón social', 'required|min_length[1]|max_length[255]');
					$val->add_field('rfc', 'RFC', 'required|min_length[1]|max_length[255]');
					$val->add_field('street', 'Calle', 'required|min_length[1]|max_length[255]');
					$val->add_field('number', '# Exterior', 'required|min_length[1]|max_length[255]');
					$val->add_field('internal_number', '# Interior', 'min_length[1]|max_length[255]');
					$val->add_field('colony', 'Colonia', 'required|min_length[1]|max_length[255]');
					$val->add_field('zipcode', 'Código postal', 'required|min_length[1]|max_length[255]');
					$val->add_field('city', 'Ciudad', 'required|min_length[1]|max_length[255]');
					$val->add_field('state', 'Estado', 'required|valid_string[numeric]|numeric_between[1,32]');
					$val->add_field('payment_method', 'Forma de pago', 'required|valid_string[numeric]|numeric_min[1]');
					$val->add_field('cfdi', 'Uso del CFDI', 'required|valid_string[numeric]|numeric_min[1]');
					$val->add_field('sat_tax_regime', 'Régimen fiscal', 'required|valid_string[numeric]|numeric_min[1]');

					# SI NO HAY NINGUN PROBLEMA CON LA VALIDACION
					if($val->run())
					{
						# SI EL RFC FUE MARCADO COMO PREDETERMINADO
						if(Input::post('default'))
						{
							# SE BUSCA SI HAY UNA REGISTRO POR DEFECTO
							$query = array(
								'id_customer' => $customer->id,
								'default'     => 1
							);

							# SE OBTIENE LA INFORMACION A TRAVES DEL MODELO
							$default_tax_datum = Model_Customers_Tax_Datum::get_one($query);

							# SI SE OBTUVO INFORMACION
							if(!empty($default_tax_datum))
							{
								# SE CAMBIA EL VALOR DEFAULT A 0
								Model_Customers_Tax_Datum::do_update(array('default' => 0), $default_tax_datum->id);
							}
						}

						# SE CREA UN ARREGLO CON LOS DATOS
						$request = array(
							'customer_id'       => $customer->id,
							'payment_method_id' => $val->validated('payment_method'),
							'cfdi_id'           => $val->validated('cfdi'),
							'sat_tax_regime_id' => $val->validated('sat_tax_regime'),
							'state_id'          => $val->validated('state'),
							'business_name'     => $val->validated('business_name'),
							'rfc'               => $val->validated('rfc'),
							'street'            => $val->validated('street'),
							'number'            => $val->validated('number'),
							'internal_number'   => $val->validated('internal_number'),
							'colony'            => $val->validated('colony'),
							'zipcode'           => $val->validated('zipcode'),
							'city'              => $val->validated('city'),
							'csf'               => $csf,
							'default'           => (Input::post('default')) ? 1 : 0
						);

						# SI SE GUARDO CORRECTAMENTE
						if(Model_Customers_Tax_Datum::set_new_record($request))
						{
							# SE ALMACENA EL MENSAJE DE EXITO
							Session::set_flash('success', '<p>El RFC se ha agregado correctamente.</p>');

							# SE REDIRECCIONA AL USUARIO
							Response::redirect('checkout/factura');
						}
						else
						{
							# SE ESTABLECE EL MENSAJE DE ERROR
							Session::set_flash('error', '<p>Algo inesperado ha ocurrido. Por favor vuelve a intentarlo.</p>');
						}
					}
					else
					{
						# SI SE SUBIO EL CV
						if($csf != '')
						{
							# SI EL ARCHIVO RECIEN SUBIDO EXISTE
							if(file_exists(DOCROOT.'assets/csf/'.$csf))
							{
								# SE ELIMINAN EL ARCHIVO
								File::delete(DOCROOT.'assets/csf/'.$csf);
							}

							# SE ESTABLECE EL MENSAJE DE ERROR
							Session::set_flash('error', '<p>Hemos encontrado errores en el formulario, por favor inténtalo nuevamente. No olvides volver a subir tu <b>Constancia de Situación Fiscal</b>.</p>');
						}
						else
						{
							# SE ESTABLECE EL MENSAJE DE ERROR
							Session::set_flash('error', '<p>Hemos encontrado errores en el formulario, por favor inténtalo nuevamente.</p>');
						}

						# SE ENVIAN LOS ERRORES
						$data['errors'] = $val->error();

						# SE ALMACENAN LAS CLASES HTML DE LOS CAMPOS VALIDADOS
						$classes['business_name']   = ($val->error('business_name')) ? 'is-invalid': 'is-valid';
						$classes['rfc']             = ($val->error('rfc')) ? 'is-invalid' : 'is-valid';
						$classes['street']          = ($val->error('street')) ? 'is-invalid' : 'is-valid';
						$classes['number']          = ($val->error('number')) ? 'is-invalid' : 'is-valid';
						$classes['internal_number'] = ($val->error('internal_number')) ? 'is-invalid' : 'is-valid';
						$classes['colony']          = ($val->error('colony')) ? 'is-invalid' : 'is-valid';
						$classes['zipcode']         = ($val->error('zipcode')) ? 'is-invalid' : 'is-valid';
						$classes['city']            = ($val->error('city')) ? 'is-invalid' : 'is-valid';
						$classes['state']           = ($val->error('state')) ? 'is-invalid' : 'is-valid';
						$classes['payment_method']  = ($val->error('payment_method')) ? 'is-invalid' : 'is-valid';
						$classes['cfdi']            = ($val->error('cfdi')) ? 'is-invalid' : 'is-valid';
						$classes['sat_tax_regime']  = ($val->error('sat_tax_regime')) ? 'is-invalid' : 'is-valid';

						# SE REGRESAN LOS VALORES DEL FORMULARIO
						$data['business_name']   = Input::post('business_name');
						$data['rfc']             = Input::post('rfc');
						$data['street']          = Input::post('street');
						$data['number']          = Input::post('number');
						$data['internal_number'] = Input::post('internal_number');
						$data['colony']          = Input::post('colony');
						$data['zipcode']         = Input::post('zipcode');
						$data['city']            = Input::post('city');
						$data['state']           = Input::post('state');
						$data['payment_method']  = Input::post('payment_method');
						$data['cfdi']            = Input::post('cfdi');
						$data['sat_tax_regime']  = Input::post('sat_tax_regime');
						$data['default']         = (Input::post('default')) ? true : false;
					}
				break;

				default:
					# SE EJECUTA EL MODULO DE DESLOGUEO
					Request::forge('sectorweb/user/logout', false)->execute();

					# SE REDIRECCIONA AL USUARIO
					Response::redirect('/');
				break;
			}
		}

		# SI EL CLIENTE TIENE DATOS DE FACTURACION
		if(!empty($customer->tax_data))
		{
			# SE RECORRE ELEMENTO POR ELEMENTO
			foreach($customer->tax_data as $tax_datum)
			{
				# SE ALMACENA LA INFORMACION
				$tax_data_info[] = array(
					'id'              => $tax_datum->id,
					'rfc'             => $tax_datum->rfc,
					'business_name'   => $tax_datum->business_name,
					'address'         => ($tax_datum->internal_number != '') ? $tax_datum->street.' #'.$tax_datum->number.', Int.'.$tax_datum->internal_number : $tax_datum->street.' #'.$tax_datum->number,
					'default'         => $tax_datum->default
				);
			}

			# SE ORDENA EL ARREGLO
			$tax_data_info = Arr::sort($tax_data_info, 'default', 'desc');
		}

		# SE ALMACENAN LOS DATOS EN EL ARREGLO DATA
		$data['classes']              = $classes;
		$data['tax_data']             = $tax_data_info;
		$data['states_opts']          = Model_State::get_for_input();
		$data['payment_methods_opts'] = Model_Payments_Method::get_for_input();
		$data['cfdis_opts']           = Model_Cfdi::get_for_input();
		$data['sat_tax_regimes_opts'] = Model_Sat_Tax_Regime::get_for_input();

		# SE CARGA LA VISTA DE INICIO
		$this->template->title       = 'Checkout - Factura | Datos';
		$this->template->description = 'Checkout - Factura';
		$this->template->content     = View::forge('checkout/factura', $data, false);
	}


	/**
	* FACTURA
	*
	* MUESTRA LA PAGINA DE CHECKOUT / FACTURA
	*
	* @access  public
	* @return  Void
	*/
	public function action_facturax()
	{
		# SE INICIALIZA LAS VARIABLES
		$data            = array();
		$business_name   = '';
		$rfc             = '';
		$street          = '';
		$number          = '';
		$internal_number = '';
		$colony          = '';
		$zipcode         = '';
		$city            = '';
		$state           = 'none';
		$payment_method  = 'none';
		$cfdi            = 'none';
		$sat_tax_regime  = 'none';

		# SI NO EXISTE LA SESION DE FACTURA
		if(!Session::get('bill'))
		{
			# SE REDIRECCIONA AL USUARIO
			Response::redirect('checkout/pagar');
		}

		# SE DECLARA EL ARREGLO QUE CONTIENE LAS CLASES HTML DE LOS CAMPOS DEL FORM
		$classes = array(
            'business_name'   => null,
            'rfc'             => null,
            'street'          => null,
            'number'          => null,
            'internal_number' => null,
            'colony'          => null,
            'zipcode'         => null,
			'city'            => null,
            'state'           => null,
			'payment_method'  => null,
            'cfdi'            => null,
            'sat_tax_regime'  => null,
        );

		# SE OBTIENEN LAS DIRECCIONES
		$customer = Model_Customer::get_one(array('id_user' => $this->auth->get('id')));

		# SI SE UTILIZO EL METODO POST
		if(Input::method() == 'POST')
		{
			# SE INICIALIZAN LAS VARIABLES
			$csf = '';

			# SE OBTIENE LA REFERENCIA DEL CV
			$csf = $_FILES['csf']['name'];

			# SI EL USUARIO SUBE EL CV
			if(!empty($csf))
			{
				# SE ESTABLECE LA CONFIGURACION PARA LOS ARCHIVOS
				$csf_config = array(
					'auto_process'        => false,
					'path'                => DOCROOT.DS.'assets/csf',
					'randomize'           => true,
					'auto_rename'         => true,
					'normalize'           => true,
					'normalize_separator' => '-',
					'ext_whitelist'       => array('pdf'),
					'max_size'            => 20971520,
				);

				# SE INICIALIZA EL PROCESO UPLOAD CON LA CONFIGURACION ESTABLECIDA
				Upload::process($csf_config);

				# SI EL ARCHIVO ES VALIDO
				if(Upload::is_valid())
				{
					# SE SUBE EL ARCHIVO
					Upload::save();

					# SE OBTIENE LA INFORMACION DE LOS ARCHIVOS
					$value = Upload::get_files();

					# SE ALMACENA EL NOMBRE DEL ARCHIVO
					$csf = (isset($value[0]['saved_as'])) ? $value[0]['saved_as'] : '';
				}
			}

			# SE CREA LA VALIDACION DE LOS CAMPOS
			$val = Validation::forge('tax_data');
			$val->add_callable('rules');

			$val->add_field('business_name', 'Razón social', 'required|min_length[1]|max_length[255]');
			$val->add_field('rfc', 'RFC', 'required|min_length[1]|max_length[255]');
			$val->add_field('street', 'Calle', 'required|min_length[1]|max_length[255]');
			$val->add_field('number', '# Exterior', 'required|min_length[1]|max_length[255]');
			$val->add_field('internal_number', '# Interior', 'min_length[1]|max_length[255]');
			$val->add_field('colony', 'Colonia', 'required|min_length[1]|max_length[255]');
			$val->add_field('zipcode', 'Código postal', 'required|min_length[1]|max_length[255]');
			$val->add_field('city', 'Ciudad', 'required|min_length[1]|max_length[255]');
			$val->add_field('state', 'Estado', 'required|valid_string[numeric]|numeric_between[1,32]');
			$val->add_field('payment_method', 'Forma de pago', 'required|valid_string[numeric]|numeric_min[1]');
			$val->add_field('cfdi', 'Uso del CFDI', 'required|valid_string[numeric]|numeric_min[1]');
			$val->add_field('sat_tax_regime', 'Régimen fiscal', 'required|valid_string[numeric]|numeric_min[1]');

			# SI NO HAY NINGUN PROBLEMA CON LA VALIDACION
			if($val->run())
			{
				# SI EL CLIENTE TIENE DATOS DE FACTURACION
				if(!empty($customer->tax_data))
				{
					# SI EXISTE LA CONSTANCIA DE SITUACION FISCAL
					if($customer->tax_data->csf != '')
					{
						# SI EL ARCHIVO RECIEN SUBIDO EXISTE
						if(file_exists(DOCROOT.'assets/csf/'.$customer->tax_data->csf))
						{
							# SE ELIMINAN EL ARCHIVO
							File::delete(DOCROOT.'assets/csf/'.$customer->tax_data->csf);
						}
					}

					# SE CREA UN ARREGLO CON LOS DATOS
					$request = array(
						'payment_method_id' => $val->validated('payment_method'),
						'cfdi_id'           => $val->validated('cfdi'),
						'sat_tax_regime_id' => $val->validated('sat_tax_regime'),
						'state_id'          => $val->validated('state'),
						'business_name'     => $val->validated('business_name'),
						'rfc'               => $val->validated('rfc'),
						'street'            => $val->validated('street'),
						'number'            => $val->validated('number'),
						'internal_number'   => $val->validated('internal_number'),
						'colony'            => $val->validated('colony'),
						'zipcode'           => $val->validated('zipcode'),
						'city'              => $val->validated('city'),
						'csf'               => $csf,
					);

					# SI SE GUARDO CORRECTAMENTE
					if(Model_Customers_Tax_Datum::do_update($request, $customer->tax_data->id))
					{
						# SE CREA LA SESION DE CONFIRMACION DE FACTURA
						Session::set('bill_confirmation', 1);

						# SE REDIRECCIONA AL USUARIO
						Response::redirect('checkout/pagar');
					}
					else
					{
						# SE ESTABLECE EL MENSAJE DE ERROR
						Session::set_flash('error', '<p>Algo inesperado ha ocurrido.</p>');
					}
				}
				else
				{
					# SE CREA UN ARREGLO CON LOS DATOS
					$request = array(
						'customer_id'       => $customer->id,
						'payment_method_id' => $val->validated('payment_method'),
						'cfdi_id'           => $val->validated('cfdi'),
						'sat_tax_regime_id' => $val->validated('sat_tax_regime'),
						'state_id'          => $val->validated('state'),
						'business_name'     => $val->validated('business_name'),
						'rfc'               => $val->validated('rfc'),
						'street'            => $val->validated('street'),
						'number'            => $val->validated('number'),
						'internal_number'   => $val->validated('internal_number'),
						'colony'            => $val->validated('colony'),
						'zipcode'           => $val->validated('zipcode'),
						'city'              => $val->validated('city'),
						'csf'               => $csf,
					);

					# SI SE GUARDO CORRECTAMENTE
					if(Model_Customers_Tax_Datum::set_new_record($request))
					{
						# SE CREA LA SESION DE CONFIRMACION DE FACTURA
						Session::set('bill_confirmation', 1);

						# SE REDIRECCIONA AL USUARIO
						Response::redirect('checkout/pagar');
					}
					else
					{
						# SE ESTABLECE EL MENSAJE DE ERROR
						Session::set_flash('error', '<p>Algo inesperado ha ocurrido.</p>');
					}
				}
			}
			else
			{
				# SI SE SUBIO EL CV
				if($csf != '')
				{
					# SI EL ARCHIVO RECIEN SUBIDO EXISTE
					if(file_exists(DOCROOT.'assets/csf/'.$csf))
					{
						# SE ELIMINAN EL ARCHIVO
						File::delete(DOCROOT.'assets/csf/'.$csf);
					}

					# SE ESTABLECE EL MENSAJE DE ERROR
					Session::set_flash('error', '<p>Hemos encontrado errores en el formulario, intenta nuevamente. No olvides volver a subir tu <b>Constancia de Situación Fiscal</b>.</p>');
				}
				else
				{
					# SE ESTABLECE EL MENSAJE DE ERROR
					Session::set_flash('error', '<p>Hemos encontrado errores en el formulario, intenta nuevamente.</p>');
				}

				# SE ENVIAN LOS ERRORES
				$data['errors'] = $val->error();

				# SE ALMACENAN LAS CLASES HTML DE LOS CAMPOS VALIDADOS
				$classes['business_name']   = ($val->error('business_name')) ? 'is-invalid': 'is-valid';
				$classes['rfc']             = ($val->error('rfc')) ? 'is-invalid' : 'is-valid';
				$classes['street']          = ($val->error('street')) ? 'is-invalid' : 'is-valid';
				$classes['number']          = ($val->error('number')) ? 'is-invalid' : 'is-valid';
				$classes['internal_number'] = ($val->error('internal_number')) ? 'is-invalid' : 'is-valid';
				$classes['colony']          = ($val->error('colony')) ? 'is-invalid' : 'is-valid';
				$classes['zipcode']         = ($val->error('zipcode')) ? 'is-invalid' : 'is-valid';
				$classes['city']            = ($val->error('city')) ? 'is-invalid' : 'is-valid';
				$classes['state']           = ($val->error('state')) ? 'is-invalid' : 'is-valid';
				$classes['payment_method']  = ($val->error('payment_method')) ? 'is-invalid' : 'is-valid';
				$classes['cfdi']            = ($val->error('cfdi')) ? 'is-invalid' : 'is-valid';
				$classes['sat_tax_regime']  = ($val->error('sat_tax_regime')) ? 'is-invalid' : 'is-valid';

				# SE REGRESAN LOS VALORES DEL FORMULARIO
				$business_name   = Input::post('business_name');
				$rfc             = Input::post('rfc');
				$street          = Input::post('street');
				$number          = Input::post('number');
				$internal_number = Input::post('internal_number');
				$colony          = Input::post('colony');
				$zipcode         = Input::post('zipcode');
				$city            = Input::post('city');
				$state           = Input::post('state');
				$payment_method  = Input::post('payment_method');
				$cfdi            = Input::post('cfdi');
				$sat_tax_regime  = Input::post('sat_tax_regime');
			}
		}
		else
		{
			# SI EL CLIENTE TIENE DATOS DE FACTURACION
			if(!empty($customer->tax_data))
			{
				# SE ALMACENA LA INFORMACION
				$business_name   = $customer->tax_data->business_name;
				$rfc             = $customer->tax_data->rfc;
				$street          = $customer->tax_data->street;
				$number          = $customer->tax_data->number;
				$internal_number = $customer->tax_data->internal_number;
				$colony          = $customer->tax_data->colony;
				$city            = $customer->tax_data->city;
				$zipcode         = $customer->tax_data->zipcode;
				$state           = $customer->tax_data->state_id;
				$payment_method  = $customer->tax_data->payment_method_id;
				$cfdi            = $customer->tax_data->cfdi_id;
				$sat_tax_regime  = $customer->tax_data->sat_tax_regime_id;
			}
		}

		# SE ALMACENAN LOS DATOS EN EL ARREGLO DATA
		$data['classes']              = $classes;
		$data['states_opts']          = Model_State::get_for_input();
		$data['payment_methods_opts'] = Model_Payments_Method::get_for_input();
		$data['cfdis_opts']           = Model_Cfdi::get_for_input();
		$data['sat_tax_regimes_opts'] = Model_Sat_Tax_Regime::get_for_input();
		$data['business_name']        = $business_name;
		$data['rfc']                  = $rfc;
		$data['street']               = $street;
		$data['number']               = $number;
		$data['internal_number']      = $internal_number;
		$data['colony']               = $colony;
		$data['city']                 = $city;
		$data['zipcode']              = $zipcode;
		$data['state']                = $state;
		$data['payment_method']       = $payment_method;
		$data['cfdi']                 = $cfdi;
		$data['sat_tax_regime']       = $sat_tax_regime;

		# SE CARGA LA VISTA DE INICIO
		$this->template->title       = 'Checkout - Factura | Datos';
		$this->template->description = 'Checkout - Factura';
		$this->template->content     = View::forge('checkout/factura', $data, false);
	}


	/**
	* PAGAR
	*
	* MUESTRA LA PAGINA DE PAGAR
	*
	* @access  public
	* @return  Void
	*/
	public function action_pagar()
	{
		# SE INICIALIZA LAS VARIABLES
		$data            = array();
		$address         = array();
		$msg             = '';
		$url             = '';
		$transfer_data   = '';
		$flag_bbva       = false;
		$flag_transfer   = false;
		$active_bbva     = false;
		$active_transfer = false;

		# SI EXISTE LA SESION DE FACTURA
		if(Session::get('bill') == 1)
		{
			# SI NO EXISTE LA SESION DE CONFIRMACION DE FACTURA
			if(!Session::get('bill_confirmation'))
			{
				# SE REDIRECCIONA AL USUARIO
				Response::redirect('checkout/factura');
			}
		}

		# SE OBTIENE EL CUSTOMER
		$customer = Model_Customer::get_one(array('id_user' => $this->auth->get('id')));

		# SE OBTIENE EL ULTIMO PEDIDO NO ENVIADO
		$sale = Model_Sale::get_last_order_not_sent($customer->id);

		# SI EL TOTAL DEL PEDIDO ES MENOR A 1000
		if($sale->total < 1000)
		{
			# SE REDIRECCIONA AL USUARIO
			Response::redirect('checkout');
		}

		# SI LA ORDEN NO TIENE RELACIONADO UNA DIRECCION DE ENTREGA
		if(empty($sale->address))
		{
			# SE INDICA EL MENSAJE DE ERROR EN SESION FLASH
			Session::set_flash('info', '<p>Selecciona una dirección de envío para tu orden.</p>');

			# SE REDIRECCIONA A CHECKOUT - ENVIO
			Response::redirect('checkout/envio');
		}

	/*	# SE ESTABLECEN PRODUCTIVO O SANDBOX
		$bbva = Bbva::setProductionMode(false);

		# SE ESTABLECEN LAS LLAVES DE BBVA
		$bbva = Bbva::getInstance('kjsdakdkajsk', 'sk_sdadasda');

		# SE GENERA EL CARGO
		$chargeRequest = array(
			'affiliation_bbva' => '1234567',
			'amount'           => $sale->total,
			'description'      => 'Pedido #'.$sale->id.' en Natura y Mas',
			'currency'         => 'MXN',
			'order_id'         => $sale->id.'-'.time(),
			'redirect_url'     => 'https://www.naturaymas.com.mx/checkout/revisar/'.$sale->id,
			'customer'         => array(
				'name'         => $sale->customer->name,
				'last_name'    => $sale->customer->last_name,
				'email'        => $sale->customer->user->email,
				'phone_number' => $sale->customer->phone
			)
		);

		# SE ALMACENA LA RESPUESTA
		$charge = $bbva->charges->create($chargeRequest);

		# SI EXISTE LA URL DE BBVA
		if(isset($charge->payment_method->url))
		{
			# SE ACTUALIZA EL VALOR EN EL MODELO DE LA VENTA
			$sale->transaction = $charge->id;

			# SE ACTUALIZA EL REGISTRO EN LA BASE DE DATOS
			$sale->save();

			# SE ESTABLECE LA URL DE PAGO
			$url = $charge->payment_method->url;

			# SE ACTUALIZA EL VALOR DE LA BANDERA
			$flag_bbva = true;
		}
*/
		# SE BUSCA LA INFORMACION A TRAVES DEL MODELO
		$transfer_data = Model_Transfer_Datum::query()
		->where('id', 1)
		->get_one();

		# SI SE OBTIENE INFORMACION
		if(!empty($transfer_data))
		{
			# SE ALMACENA LA INFORMACION PARA LA VISTA
			$transfer_data = $transfer_data->info;

			# SE ACTUALIZA EL VALOR DE LA BANDERA
			$flag_transfer = true;
		}

		# SE ALMACENA LA INFORMACION DE LA DIRECCION
		$address = array(
			'full_name' => $sale->address->name.' '.$sale->address->last_name,
			'phone'     => $sale->address->phone,
			'address'   => $sale->address->street.', #'.$sale->address->number.','.($sale->address->internal_number != '' ? ' #Int '.$sale->address->internal_number.',' : '').' '.$sale->address->colony.', CP:'.$sale->address->zipcode,
			'region'    => $sale->address->city.', '.$sale->address->state->name,
		);

		# SE BUSCA LA INFORMACION A TRAVES DEL MODELO
		$payments_processors = Model_Payments_Processor::query()
		->get();

		# SI SE OBTIENE INFORMACION
		if(!empty($payments_processors))
		{
            # SE RECORRE ELEMENTO POR ELEMENTO
            foreach($payments_processors as $payments_processor)
            {
                # DEPENDIENDO DEL ID
                switch($payments_processor->id)
                {
					# BBVA
                    case 1:
						# SE ACTUALIZA EL VALOR DE LA BANDERA
						$active_bbva = ($payments_processor->status == 0) ? false : true;
                    break;

					# TRANSFERENCIA/DEPOSITO
                    case 2:
						# SE ACTUALIZA EL VALOR DE LA BANDERA
						$active_transfer = ($payments_processor->status == 0) ? false : true;
                    break;
                }
            }
		}

		# SE ALMACENAN LA INFORMACION DE LA VISTA
		$data['msg']             = $msg;
		$data['sale_id']         = $sale->id;
		$data['address']         = $address;
		$data['url']             = $url;
		$data['transfer_data']   = $transfer_data;
		$data['flag_bbva']       = $flag_bbva;
		$data['flag_transfer']   = $flag_transfer;
		$data['active_bbva']     = $active_bbva;
		$data['active_transfer'] = $active_transfer;

		# SE CARGA LA VISTA DE INICIO
		$this->template->title       = 'Checkout - Pagar | Natura y Mas';
		$this->template->description = 'Checkout de Natura y Mas.';
		$this->template->content     = View::forge('checkout/pagar', $data, false);
	}


	/**
	* REVISAR
	*
	* REVISA EL PAGO DE UN PEDIDO
	*
	* @access  public
	* @return  Void
	*/
	public function action_revisar($sale_id = 0)
	{
		# SE OBTIENE EL ID DE TRANSACCION
		$transaction = Input::get('id');

		# SE OBTIENE EL CUSTOMER
		$customer = Model_Customer::get_one(array('id_user' => $this->auth->get('id')));

		# SE OBTIENE EL ULTIMO PEDIDO NO ENVIADO
		$sale = Model_Sale::check_transaction($sale_id, $transaction);

		# SI SE OBTIENE LA INFORMACION
		if(!empty($sale))
		{
		    # SE ESTABLECEN PRODUCTIVO O SANDBOX
		    $bbva = Bbva::setProductionMode(true);

		    # SE ESTABLECEN LAS LLAVES DE BBVA
		    $bbva = Bbva::getInstance('mt2rah8yd5pizoza4rv5', 'sk_2b17754df5a24ec6b44ef47bace07732');

			# SE OBTIENE LA INFORMACION DEL CARGO
			$charge = $bbva->charges->get($transaction);

			# SI EXISTE LA PROPIEDAD SERIALIZABLEDATA
			if(property_exists($charge, 'serializableData'))
			{
				# SE SERIALIZA LA RESPUESTA
				$response = serialize($charge->serializableData);
			}
			else
			{
				# SE INICIALIZA EL ARREGLO
				$status = array();

				# SE ALMACENA EL ESTATUS
				$status['status'] = $charge->status;

				# SE SERIALIZA EL ESTATUS
				$response = serialize($status);
			}

			# SE CREA EL MODELO CON LA INFORMACION
			$log = new Model_Providers_Log(array(
				'provider'    => 'BBVA',
				'type'        => 'Enviado por BBVA',
				'transaction' => $transaction,
				'response'    => $response
			));

			# SI SE ALMACENA EL REGISTRO EN LA BASE DE DATOS
			$log->save();

			# SI EL PAGO ES EXITOSO
			if($charge->status == 'completed')
			{
				# SE ALMACENA LA INFORMACION
                $request = array(
					'type_id' => 1, // BBVA
                    'token'   => $charge->authorization,
                    'total'   => $charge->amount
                );

				# SE CREA UN REGISTRO DEL PAGO
                $new_payment = Model_Payment::set_new_record($request);

				# SE ALMACENA LA INFORMACION
                $request = array(
                    'payment_id' => $new_payment->id,
                    'status'     => 1,
                    'sale_date'  => time()
                );

				# SE ACTUALIZA EL REGISTRO DEL PAGO
                Model_Sale::do_update($request, $sale->id);

				# SE RECORRE ELEMENTO POR ELEMENTO
				foreach($sale->products as $sale_product)
				{
					# SE BUSCA LA INFORMACION A TRAVES DEL MODELO
					$product = Model_Product::query()
					->where('id', $sale_product->product_id)
					->get_one();

					# SE RESTA LA CANTIDAD COMPRADA
					$product->available -= $sale_product->quantity;

					# SE GUARDA EL REGISTRO EN LA BASE DE DATOS
					$product->save();
				}

				# SE ELIMINA LA SESION DEL CARRITO
                Session::delete('cart');

				# SE ELIMINA LA SESION DE FACTURA
				Session::delete('bill');

				# SE ELIMINA LA SESION DE CONFIRMACION DE FACTURA
				Session::delete('bill_confirmation');

				# SE ENVIA EL CORREO AL USUARIO
				$this->send_user_mail($sale->id);

				# SE ENVIA EL CORREO AL ADMINISTRADOR
				$this->send_admin_mail($sale->id);

				# SE ENVIA EL CORREO AL DESARROLLADOR
				$this->send_dev_mail($sale->id, 'BBVA', 'Pagado');

				if($charge->status == 'completed')
			    {
    				# SE REDIRECCIONA AL USUARIO
    				Response::redirect('gracias');
			    }
			    else
			    {
			        # SE REDIRECCIONA AL USUARIO
    				Response::redirect('/');
			    }
			}
		}

		# SE REDIRECCIONA AL USUARIO
		Response::redirect('checkout');
	}


	/**
	* TRANSFERENCIA
	*
	* CAMBIA EL ESTATUS DEL PEDIDO
	*
	* @access  public
	* @return  Void
	*/
	public function action_transferencia()
	{
		# SE INICIALIZA LAS VARIABLES
		$data = array();

		# SE OBTIENE EL CUSTOMER
		$customer = Model_Customer::get_one(array('id_user' => $this->auth->get('id')));

		# SE OBTIENE EL ULTIMO PEDIDO NO ENVIADO
		$sale = Model_Sale::get_last_order_not_sent($customer->id);

		# SE ALMACENA LA INFORMACION
		$request = array(
			'type_id' => 2, // TRANSFERENCIA
			'token'   => 'Transferencia',
			'total'   => 0
		);

		# SE CREA UN REGISTRO DEL PAGO
		$new_payment = Model_Payment::set_new_record($request);

		# SE ALMACENA LA INFORMACION
		$request = array(
			'payment_id' => $new_payment->id,
			'status'     => 2,
			'sale_date'  => time()
		);

		# SE ACTUALIZA EL REGISTRO DEL PAGO
		Model_Sale::do_update($request, $sale->id);

		# SE ELIMINA LA SESION DEL CARRITO
		Session::delete('cart');

		# SE ELIMINA LA SESION DE FACTURA
		Session::delete('bill');

		# SE ELIMINA LA SESION DE CONFIRMACION DE FACTURA
		Session::delete('bill_confirmation');

		# SI SE ACTUALIZA LA INFORMACION EN LA BASE DE DATOS
		if($sale->save())
		{
			# SE REDIRECCIONA GRACIAS
			Response::redirect('gracias/transferencia');
		}
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
		$data          = array();
		$address_html  = '';
		$products_html = '';
        $totals_html   = '';

		# SE BUSCA LA INFORMACION A TRAVES DEL MODELO
		$sale = Model_Sale::query()
		->related('products')
		->related('customer')
		->where('id', $sale_id)
		->where('status', 1)
		->get_one();

		# SI SE OBTIENE INFORMACION
		if(!empty($sale))
		{
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
                $address_html .= '<h1 style="margin: 0 0 10px 0; font-family: sans-serif; font-size: 25px; line-height: 30px; color: #333333; font-weight: normal;">Dirección de envío</h1>
                <p>
                    <strong style="display: block;">Calle:</strong>
                    <span style="display: block; margin-bottom: 15px; color: #ee3530">'.$sale->address->street.'</span>
                    <strong style="display: block;">Número:</strong>
                    <span style="display: block; margin-bottom: 15px; color: #ee3530">'.$sale->address->number.'</span>
                    <strong style="display: block;">Número interior:</strong>
                    <span style="display: block; margin-bottom: 15px; color: #ee3530">'.$sale->address->internal_number.'</span><br>
                    <strong style="display: block;">Colonia:</strong>
                    <span style="display: block; margin-bottom: 15px; color: #ee3530">'.$sale->address->colony.'</span><br>
                    <strong style="display: block;">Código postal:</strong>
                    <span style="display: block; margin-bottom: 15px; color: #ee3530">'.$sale->address->zipcode.'</span><br>
                    <strong style="display: block;">Ciudad:</strong>
                    <span style="display: block; margin-bottom: 15px; color: #ee3530">'.$sale->address->city.'</span><br>
                    <strong style="display: block;">Estado:</strong>
                    <span style="display: block; margin-bottom: 15px; color: #ee3530">'.$sale->address->state->name.'</span><br>
                    <strong style="display: block;">Detalles:</strong>
                    <span style="display: block; margin-bottom: 15px; color: #ee3530">'.$sale->address->details.'</span><br>
                    <strong style="display: block;">Recibe:</strong>
                    <span style="display: block; margin-bottom: 15px; color: #ee3530">'.$sale->address->name.' '.$sale->address->last_name.'</span>
                    <strong style="display: block;">Teléfono:</strong>
                    <span style="display: block; margin-bottom: 15px; color: #ee3530">'.$sale->address->phone.'</span>
                    <hr>
                </p>';
            }

            # SE CREA EL HTML DEL TOTAL DE PRODUCTOS
            $totals_html .= '<strong style="display: block;">Total:</strong>
            <span style="display: block; margin-bottom: 15px; color: #ee3530">$'.number_format($sale->total, 2, '.', ',').'</span>';

			# SE CREA EL CUERPO DEL CORREO
            $data['body'] =
            '<tr>
                <td style="background-color: #ffffff;">
                    <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%">
                        <tr>
                            <td style="padding: 20px; font-family: sans-serif; font-size: 15px; line-height: 20px; color: #555555;">
                                <h1 style="margin: 0 0 10px 0; font-family: sans-serif; font-size: 25px; line-height: 30px; color: #333333; font-weight: normal;">¡Gracias por comprar en Natura y Mas!</h1>
                                <p style="margin: 0;">Este mesaje ha sido enviado automáticamente desde la página web de <strong>Natura y Mas</strong>, para confirmar tu pedido, a continuación te dejamos la información registrada:</p>
                                <p>
                                    <strong style="display: block;">Nombre:</strong>
                                    <span style="display: block; margin-bottom: 15px; color: #ee3530">'.$sale->customer->name.' '.$sale->customer->last_name.'</span>
                                    <strong style="display: block;">Correo electrónico:</strong>
                                    <span style="display: block; margin-bottom: 15px; color: #ee3530">'.$sale->customer->user->email.'</span>
									<strong style="display: block;">Pedido:</strong>
                                    <span style="display: block; margin-bottom: 15px; color: #ee3530">#'.$sale->id.'</span>
									'.$totals_html.'
                                    <hr>
                                </p>
								'.$address_html.'
								<h1 style="margin: 0 0 10px 0; font-family: sans-serif; font-size: 25px; line-height: 30px; color: #333333; font-weight: normal;">Productos</h1>
                                <p>
                                    '.$products_html.'
                                </p>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>';

            # SE ENVIA UN EMAIL CON EL MENSAJE
            $email = Email::forge();
            $email->from('sistemas@naturaymas.com.mx', 'Natura y Mas');
            $email->reply_to('sistemas@naturaymas.com.mx', 'Natura y Mas');
            $email->to(array(
                $sale->customer->user->email => $sale->customer->name.' '.$sale->customer->last_name,
                //'omar@naturaymas.com.mx' => 'Sector Web',
                //'sistemas@naturaymas.com.mx' => 'Natura y Mas',
            ));
            $email->subject('Natura y Mas - Pedido #'.$sale->id);
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
     * ENVIA POR EMAIL UN MENSAJE DEL PEDIDO AL ADMINISTRADOR
     *
     * @access  private
     * @return  Boolean
     */
    private function send_admin_mail($sale_id = 0)
    {
		# SE INICIALIZAN LAS VARIABLES
		$data          = array();
		$address_html  = '';
		$products_html = '';
        $totals_html   = '';

		# SE BUSCA LA INFORMACION A TRAVES DEL MODELO
		$sale = Model_Sale::query()
		->related('products')
		->related('customer')
		->where('id', $sale_id)
		->where('status', 1)
		->get_one();

		# SI SE OBTIENE INFORMACION
		if(!empty($sale))
		{
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
                $address_html .= '<h1 style="margin: 0 0 10px 0; font-family: sans-serif; font-size: 25px; line-height: 30px; color: #333333; font-weight: normal;">Dirección de envío</h1>
                <p>
                    <strong style="display: block;">Calle:</strong>
                    <span style="display: block; margin-bottom: 15px; color: #ee3530">'.$sale->address->street.'</span>
                    <strong style="display: block;">Número:</strong>
                    <span style="display: block; margin-bottom: 15px; color: #ee3530">'.$sale->address->number.'</span>
                    <strong style="display: block;">Número interior:</strong>
                    <span style="display: block; margin-bottom: 15px; color: #ee3530">'.$sale->address->internal_number.'</span>
                    <strong style="display: block;">Colonia:</strong>
                    <span style="display: block; margin-bottom: 15px; color: #ee3530">'.$sale->address->colony.'</span>
                    <strong style="display: block;">Código postal:</strong>
                    <span style="display: block; margin-bottom: 15px; color: #ee3530">'.$sale->address->zipcode.'</span>
                    <strong style="display: block;">Ciudad:</strong>
                    <span style="display: block; margin-bottom: 15px; color: #ee3530">'.$sale->address->city.'</span>
                    <strong style="display: block;">Estado:</strong>
                    <span style="display: block; margin-bottom: 15px; color: #ee3530">'.$sale->address->state->name.'</span>
                    <strong style="display: block;">Detalles:</strong>
                    <span style="display: block; margin-bottom: 15px; color: #ee3530">'.$sale->address->details.'</span>
                    <strong style="display: block;">Recibe:</strong>
                    <span style="display: block; margin-bottom: 15px; color: #ee3530">'.$sale->address->name.' '.$sale->address->last_name.'</span>
                    <strong style="display: block;">Teléfono:</strong>
                    <span style="display: block; margin-bottom: 15px; color: #ee3530">'.$sale->address->phone.'</span>
                    <hr>
                </p>';
            }

            # SE CREA EL HTML DEL TOTAL DE PRODUCTOS
            $totals_html .= '<strong style="display: block;">Total:</strong>
            <span style="display: block; margin-bottom: 15px; color: #ee3530">$'.number_format($sale->total, 2, '.', ',').'</span>';

			# SE CREA EL CUERPO DEL CORREO
            $data['body'] =
            '<tr>
                <td style="background-color: #ffffff;">
                    <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%">
                        <tr>
                            <td style="padding: 20px; font-family: sans-serif; font-size: 15px; line-height: 20px; color: #555555;">
                                <h1 style="margin: 0 0 10px 0; font-family: sans-serif; font-size: 25px; line-height: 30px; color: #333333; font-weight: normal;">Nueva venta en línea.</h1>
                                <p style="margin: 0;">Este mesaje ha sido enviado desde la página web de <strong>Natura y Mas</strong>, con la siguiente información:</p>
                                <p>
                                    <strong style="display: block;">Nombre:</strong>
                                    <span style="display: block; margin-bottom: 15px; color: #ee3530">'.$sale->customer->name.' '.$sale->customer->last_name.'</span>
                                    <strong style="display: block;">Correo electrónico:</strong>
                                    <span style="display: block; margin-bottom: 15px; color: #ee3530">'.$sale->customer->user->email.'</span>
									<strong style="display: block;">Pedido:</strong>
                                    <span style="display: block; margin-bottom: 15px; color: #ee3530">#'.$sale->id.'</span>
									'.$totals_html.'
                                    <hr>
                                </p>
                                '.$address_html.'
								<h1 style="margin: 0 0 10px 0; font-family: sans-serif; font-size: 25px; line-height: 30px; color: #333333; font-weight: normal;">Productos</h1>
                                <p>
                                    '.$products_html.'
                                </p>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>';

            # SE ENVIA UN EMAIL CON EL MENSAJE
            $email = Email::forge();
            $email->from('sistemas@naturaymas.com.mx', 'Natura y Mas');
            $email->reply_to($sale->customer->user->email, $sale->customer->name.' '.$sale->customer->last_name);
            $email->to(array(
                'sistemas@naturaymas.com.mx' => 'Natura y Mas',
                //'omar@naturaymas.com.mx' => 'Sector Web',
            ));
            $email->subject('Natura y Mas - Venta en línea #'.$sale->id);
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
     * SEND DEV MAIL
     *
     * ENVIA POR EMAIL UN MENSAJE DEL PEDIDO AL DESARROLLADOR
     *
     * @access  private
     * @return  Boolean
     */
    private function send_dev_mail($sale_id = 0, $payment_type = '', $payment_status = '')
    {
		# SE INICIALIZAN LAS VARIABLES
		$data          = array();
        $address_html  = '';
		$products_html = '';
        $totals_html   = '';

		# SE BUSCA LA INFORMACION A TRAVES DEL MODELO
		$sale = Model_Sale::query()
		->related('products')
		->related('customer')
		->where('id', $sale_id)
		->where('status', 1)
		->get_one();

		# SI SE OBTIENE INFORMACION
		if(!empty($sale))
		{
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
                $address_html .= '<h1 style="margin: 0 0 10px 0; font-family: sans-serif; font-size: 25px; line-height: 30px; color: #333333; font-weight: normal;">Dirección de envío</h1>
                <p>
                    <strong style="display: block;">Calle:</strong>
                    <span style="display: block; margin-bottom: 15px; color: #ee3530">'.$sale->address->street.'</span>
                    <strong style="display: block;">Número:</strong>
                    <span style="display: block; margin-bottom: 15px; color: #ee3530">'.$sale->address->number.'</span>
                    <strong style="display: block;">Número interior:</strong>
                    <span style="display: block; margin-bottom: 15px; color: #ee3530">'.$sale->address->internal_number.'</span>
                    <strong style="display: block;">Colonia:</strong>
                    <span style="display: block; margin-bottom: 15px; color: #ee3530">'.$sale->address->colony.'</span>
                    <strong style="display: block;">Código postal:</strong>
                    <span style="display: block; margin-bottom: 15px; color: #ee3530">'.$sale->address->zipcode.'</span>
                    <strong style="display: block;">Ciudad:</strong>
                    <span style="display: block; margin-bottom: 15px; color: #ee3530">'.$sale->address->city.'</span>
                    <strong style="display: block;">Estado:</strong>
                    <span style="display: block; margin-bottom: 15px; color: #ee3530">'.$sale->address->state->name.'</span>
                    <strong style="display: block;">Detalles:</strong>
                    <span style="display: block; margin-bottom: 15px; color: #ee3530">'.$sale->address->details.'</span>
                    <strong style="display: block;">Recibe:</strong>
                    <span style="display: block; margin-bottom: 15px; color: #ee3530">'.$sale->address->name.' '.$sale->address->last_name.'</span>
                    <strong style="display: block;">Teléfono:</strong>
                    <span style="display: block; margin-bottom: 15px; color: #ee3530">'.$sale->address->phone.'</span>
                    <hr>
                </p>';
            }

            # SE CREA EL HTML DEL TOTAL DE PRODUCTOS
            $totals_html .= '<strong style="display: block;">Total:</strong>
            <span style="display: block; margin-bottom: 15px; color: #ee3530">$'.number_format($sale->total, 2, '.', ',').'</span>';

			# SE CREA EL CUERPO DEL CORREO
            $data['body'] =
            '<tr>
                <td style="background-color: #ffffff;">
                    <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%">
                        <tr>
                            <td style="padding: 20px; font-family: sans-serif; font-size: 15px; line-height: 20px; color: #555555;">
                                <h1 style="margin: 0 0 10px 0; font-family: sans-serif; font-size: 25px; line-height: 30px; color: #333333; font-weight: normal;">Nueva venta en línea.</h1>
                                <p style="margin: 0;">Este mesaje ha sido enviado desde la página web de <strong>Natura y Mas</strong>, con la siguiente información:</p>
                                <p>
                                    <strong style="display: block;">Nombre:</strong>
                                    <span style="display: block; margin-bottom: 15px; color: #ee3530">'.$sale->customer->name.' '.$sale->customer->last_name.'</span>
                                    <strong style="display: block;">Correo electrónico:</strong>
                                    <span style="display: block; margin-bottom: 15px; color: #ee3530">'.$sale->customer->user->email.'</span>
									<strong style="display: block;">Pedido:</strong>
                                    <span style="display: block; margin-bottom: 15px; color: #ee3530">#'.$sale->id.'</span>
									'.$totals_html.'
									<strong style="display: block;">Método de pago:</strong>
                                    <span style="display: block; margin-bottom: 15px; color: #ee3530">'.$payment_type.'</span>
									<strong style="display: block;">Status:</strong>
                                    <span style="display: block; margin-bottom: 15px; color: #ee3530">'.$payment_status.'</span>
                                    <hr>
                                </p>
								'.$address_html.'
								<h1 style="margin: 0 0 10px 0; font-family: sans-serif; font-size: 25px; line-height: 30px; color: #333333; font-weight: normal;">Productos</h1>
                                <p>
                                    '.$products_html.'
                                </p>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>';

            # SE ENVIA UN EMAIL CON EL MENSAJE
            $email = Email::forge();
            $email->from('sistemas@naturaymas.com.mx', 'Natura y Mas');
            $email->reply_to($sale->customer->user->email, $sale->customer->name.' '.$sale->customer->last_name);
            $email->to(array(
                'omar@naturaymas.com.mx' => 'Sector Web',
				'ysolis@naturaymas.com.mx' => 'Yaneth'
            ));
            $email->subject('Natura y Mas - Dev #'.$sale->id);
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
