<?php

/**
 * CONTROLADOR MI CUENTA
 *
 * @package  app
 * @extends  Controller_Base
 */
class Controller_Mi_Cuenta extends Controller_Base {

	/**
	 * BEFORE
	 *
	 * REVISA SI EL USUARIO TIENE UNA SESION,
	 * SI NO EXISTE REDIRECCIONA AL LOGIN
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
            Response::redirect('iniciar-sesion');
        }

        # SE CREA LA INSTANCIA DE AUTH
        $this->auth = Auth::instance();
	}


	/**
	 * INDEX
	 *
	 * MUESTRA LA PAGINA DE MI CUENTA
	 *
	 * @access  public
	 * @return  void
	 */
	public function action_index()
	{
        # SE INICIALIZA EL ARREGLO DATA
        $data = array();

        # SE INICIALIZAN LOS ARREGLOS
        $address_info = array();
        $tax_data_info = array();
        $sales_history_info = array();

        # SE BUSCA EL CUSTOMER
        $customer = Model_Customer::get_one(array('id_user' => $this->auth->get('id')));

        # SE OBTIENE LA DIRECCION POR DEFECTO
        $query = array(
            'id_customer' => $customer->id,
            'default'     => 1
        );
        $address = Model_Customers_Address::get_one($query);

        # SI SE OBTUVO LA INFORMACION
        if(!empty($address))
        {
            # SE ALMACENA LA INFORMACION EN ADDRESS_INFO
            $address_info = array(
                'full_name' => $address->name.' '.$address->last_name,
                'phone'     => $address->phone,
                'address'   => $address->street.', #'.$address->number.','.($address->internal_number != '' ? ' #Int '.$address->internal_number.',' : '').' '.$address->colony.', CP:'.$address->zipcode,
                'region'    => $address->city.', '.$address->state->name
            );
        }

        # SE BUSCA EL CUSTOMER
        $customer = Model_Customer::get_one(array('id_user' => $this->auth->get('id')));

        # SE OBTIENE LA DIRECCION POR DEFECTO
        $query = array(
           'id_customer' => $customer->id,
           'default'     => 1
       );

       $tax_data = Model_Customers_Tax_Datum::get_one($query);
        # SI EL CLIENTE TIENE DATOS DE FACTURACION
        if(!empty($tax_data))
        {
                # SE ALMACENA LA INFORMACION
                $tax_data_info = array(
                    'id'              => $tax_data->id,
                    'rfc'             => $tax_data->rfc,
                    'business_name'   => $tax_data->business_name,
                    'address'         => ($tax_data->internal_number != '') ? $tax_data->street.' #'.$tax_data->number.', Int.'.$tax_data->internal_number : $tax_data->street.' #'.$tax_data->number
             
                );
        }

        # SE OBTIENEN LOS ULTIMOS 5 PEDIDOS VENDIDOS
        $query = array(
            'id_customer' => $customer->id,
            'limit' => 5
        );
        $sales = Model_Sale::get_sold($query);

        # SI SE OBTUVO LA INFORMACION
        if(!empty($sales))
        {
            # SE RECORREN LAS VENTAS
            foreach($sales as $sale)
            {
                $order = $sale->order_id;
                if ($order <= 0){
                    $order = 'En espera de asignacion';
                }else{
                    $order = $sale->order->name;
                }

                # SE ALMACENA LA INFORMACION EN SALES_HISTORY_INFO
                $sales_history_info[] = array(
                    'id'     => $sale->id,
                    'date'   => date('d/m/Y', $sale->created_at),
                    'total'  => number_format($sale->total, 2, '.', ','),
                    'order'  => $order,
                    'status' => $sale->status == 1 ? 'Pagado' : ($sale->status == 2 ? 'Por confirmar' : 'Cancelado')
                );
            }
        }

        # SE ALMACENA LA INFORMACION DE LA VISTA
        $data['full_name'] = $customer->name.' '.$customer->last_name;
        $data['email']     = $this->auth->get('email');
        $data['phone']     = ($customer->phone != '') ? $customer->phone : 'No registrado';
        $data['username']  = $this->auth->get('username');

        $data['address']       = $address_info;
        $data['tax_data']      = $tax_data_info;
        $data['sales_history'] = $sales_history_info;

        # SE CARGA LA VISTA DE INICIO
        $this->template->title       = 'Mi cuenta | Distribuidora Sajor';
        $this->template->description = 'Distribuidora Sajor';
        $this->template->content     = View::forge('mi_cuenta/index', $data, false);
    }


    /**
	 * EDITAR
	 *
	 * MUESTRA LA PAGINA DE MI CUENTA - EDITAR
	 *
	 * @access  public
	 * @return  void
	 */
	public function action_editar()
	{
        # SE INICIALIZA EL ARREGLO DATA
        $data = array();

        # SE DECLARA EL ARREGLO QUE CONTIENE LAS CLASES HTML DE LOS CAMPOS DEL FORM
        $classes = array(
            'email'            => null,
            'name'             => null,
            'last_name'        => null,
            'phone'            => null,
            'old_password'     => null,
            'password'         => null,
            'confirm_password' => null,
        );

        # SE OBTIENEN LOS DATOS DEL USUARIO
        $user = Model_User::find($this->auth->get('id'));

        # SE ALMACENA LA INFORMACION DE LA VISTA
        $data['email']     = $user->email;
        $data['name']      = $user->customer->name;
        $data['last_name'] = $user->customer->last_name;
        $data['phone']     = $user->customer->phone;
        $data['username']  = $user->username;

        # SI SE UTILIZO EL METODO POST
        if(Input::method() == 'POST')
        {
            switch (Input::post('submit'))
            {
                // CAMBIO DE INFORMACION DE USUARIO
                case 'user_data':

                    # SE CREA LA VALIDACION DE LOS CAMPOS
                    $val = Validation::forge('update_user_data');
                    $val->add_callable('rules');
                    $val->add_field('email', 'Correo electrónico', 'required|valid_email');
                    $val->add_field('name', 'Nombres', 'required|min_length[1]|max_length[255]');
                    $val->add_field('last_name', 'Apellidos', 'required|min_length[1]|max_length[255]');
                    $val->add_field('phone', 'Teléfono', 'min_length[1]|max_length[255]');

                    # SI NO HAY NINGUN PROBLEMA CON LA VALIDACION
                    if($val->run())
                    {
                        try
                        {
                            # SE ESTEBLECE LA NUEVA INFORMACION DEL USUARIO
                            $data_to_update = array(
                                'email' => $val->validated('email')
                            );

                            # SI SE ACTUALIZO LA INFORMACION DEL USUARIO CORRECTAMENTE
                            if($this->auth->update_user($data_to_update))
                            {
                                # SE CREA UN ARREGLO CON LOS DATOS DEL PERFIL
                                $request = array(
                                    'name'      => $val->validated('name'),
                                    'last_name' => $val->validated('last_name'),
                                    'phone'     => $val->validated('phone'),
                                );

                                # SE CREA EL MODELO DE CUSTOMER
                                $customer = Model_Customer::do_update($request, $user->customer->id);

                                # SI SE GUARDO CORRECTAMENTE
                                if($customer)
                                {
                                    # SE ALMACENA EL MENSAJE DE ÉXITO EN LA SESIÓN
                                    Session::set_flash('success', '<p>Los datos de tu cuenta han sido actualizados correctamente.</p>');
                                }
                                else
                                {
                                    # SE ESTABLECE EL MENSAJE DE ERROR
                                    Session::set_flash('error', '<p>Algo inesperado ha ocurrido.</p>');
                                }
                            }
                            else
                            {
                                # SE ESTABLECE EL MENSAJE DE ERROR
                                Session::set_flash('error', '<p>Algo inesperado ha ocurrido.</p>');
                            }
                        }
                        catch(\SimpleUserUpdateException $e)
                        {
                            # SE ESTABLECE EL MENSAJE DE ERROR
                            Session::set_flash('error', '<p><b>Error:</b> El correo electrónico <b>'.$val->validated('email').'</b> ya está asociado a otra cuenta, por favor escribe una dirección de correo electrónico diferente.</p>');

                            # SE INDICA LA CLASSE DE ERROR EN EL CAMPO
                            $classes['email'] = 'is-invalid';
                        }
                    }
                    else
                    {
                        # SE ENVIAN LOS ERRORES
                        $data['errors'] = $val->error();

                        # SE ALMACENAN LAS CLASES HTML DE LOS CAMPOS VALIDADOS
                        $classes['email'] = ($val->error('email')) ? 'is-invalid': 'is-valid';
                        $classes['name'] = ($val->error('name')) ? 'is-invalid': 'is-valid';
                        $classes['last_name'] = ($val->error('last_name')) ? 'is-invalid': 'is-valid';
                        $classes['phone'] = ($val->error('phone')) ? 'is-invalid': 'is-valid';

                        # SE ESTABLECE EL MENSAJE DE ERROR
                        Session::set_flash('error', '<p>Hemos encontrado errores en el formulario, por favor inténtalo nuevamente.</p>');
                    }

                    # SE ALMACENA LA INFORMACION DEL FORMULARIO
                    $data['email']     = Input::post('email');
                    $data['name']      = Input::post('name');
                    $data['last_name'] = Input::post('last_name');
                    $data['phone']     = Input::post('phone');
                break;

                // CAMBIO DE CONTRASENA
                case 'password':
                    # SE CREA LA VALIDACION DE LOS CAMPOS
                    $val = Validation::forge('update_user_password');
                    $val->add_callable('rules');
                    $val->add_field('old_password', 'Contraseña actual', 'required|min_length[6]');
                    $val->add_field('password', 'Nueva contraseña', 'required|min_length[6]');
                    $val->add_field('confirm_password', 'Confirmar nueva contraseña', 'required|match_field[password]');

                    # SI NO HAY NINGUN PROBLEMA CON LA VALIDACION
                    if($val->run())
                    {
                        try
                        {
                            # SI SE ACTUALIZO LA INFORMACION DEL USUARIO CORRECTAMENTE
                            if($this->auth->change_password($val->validated('old_password'), $val->validated('password')))
                            {
                                # SE ALMACENA EL MENSAJE DE ÉXITO EN LA SESIÓN
                                Session::set_flash('success', '<p>La contraseña ha sido cambiada correctamente.</p>');
                            }
                            else
                            {
                                # SE ESTABLECE EL MENSAJE DE ERROR
                                Session::set_flash('error', '<p>La contraseña es incorrecta.</p>');
                            }
                        }
                        catch(\SimpleUserUpdateException $e)
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
                        $classes['old_password']     = ($val->error('old_password')) ? 'is-invalid' : 'is-valid';
                        $classes['password']         = ($val->error('password')) ? 'is-invalid' : 'is-valid';
                        $classes['confirm_password'] = ($val->error('confirm_password')) ? 'is-invalid' : 'is-valid';

                        # SE ESTABLECE EL MENSAJE DE ERROR
                        Session::set_flash('error', '<p>Hemos encontrado errores en el formulario, por favor inténtalo nuevamente.</p>');
                    }
                break;

                default:
                    # SE EJECUTA EL MODULO DE DESLOGUEO
                    Request::forge('sectorweb/user/logout', false)->execute();

                    # SE REDIRECCIONA AL CONTROLADOR BASE
                    Response::redirect('/');
                break;
            }
        }

        # SE ALMACENA LA INFORMACION DE LA VISTA
        $data['classes'] = $classes;

        # SE CARGA LA VISTA DE INICIO
        $this->template->title       = 'Mi cuenta - Editar perfil | Distribuidora Sajor';
        $this->template->description = 'Distribuidora Sajor';
        $this->template->content     = View::forge('mi_cuenta/editar_perfil', $data, false);
    }


    /**
	 * PEDIDOS
	 *
	 * MUESTRA LA PAGINA DE MI CUENTA - PEDIDOS
	 *
	 * @access  public
	 * @return  void
	 */
	public function action_pedidos()
	{
        # SE INICIALIZA EL ARREGLO DATA
        $data = array();

        # SE INICIALIZAN LOS ARREGLOS
        $sales_history_info = array();

        # SE BUSCA EL CUSTOMER
        $customer = Model_Customer::get_one(array('id_user' => $this->auth->get('id')));

        # SE OBTIENEN LOS ULTIMOS 5 PEDIDOS VENDIDOS
        $sales = Model_Sale::get_sold(array('id_customer' => $customer->id));

        # SI SE OBTUVO LA INFORMACION
        if(!empty($sales))
        {
            # SE RECORREN LAS VENTAS
            foreach($sales as $sale)
            {
				# SE INICIALIZA LA VARIABLE
				$status = '';

				# DEPENDIENDO DEL ESTATUS
				switch($sale->status)
				{
					case 1:
						$status = 'Pagado';
					break;
					case 2:
						$status = 'Por revisar';
					break;
					case 3:
						$status = 'Cancelada';
					break;
				}

                $order = $sale->order_id;
                if ($order <= 0){
                    $order = 'En espera de asignacion';
                }else{
                    $order = $sale->order->name;
                }

                # SE ALMACENA LA INFORMACION EN SALES_HISTORY_INFO
                $sales_history_info[] = array(
                    'id'              => $sale->id,
                    'date'            => date('d/m/Y', $sale->sale_date),
                    'total'           => number_format($sale->total, 2, '.', ','),
                    'order'           => $order,
                    'status'          => $status
                );
            }
        }

        # SE ALMACENA LA INFORMACION DE LA VISTA
        $data['sales_history'] = $sales_history_info;

        # SE CARGA LA VISTA DE INICIO
        $this->template->title       = 'Mi cuenta - Historial de pedidos | Distribuidora Sajor';
        $this->template->description = 'Distribuidora Sajor';
        $this->template->content     = View::forge('mi_cuenta/pedidos', $data, false);
    }


    /**
	 * PEDIDO
	 *
	 * MUESTRA LA PAGINA DE MI CUENTA - PEDIDO
	 *
	 * @access  public
	 * @return  void
	 */
	public function action_pedido($id_sale)
	{
        # SE INICIALIZA EL ARREGLO DATA
        $data                  = array();
		$sale_shipping_address = array();
		$status                = '';

        # SE BUSCA EL CUSTOMER
        $customer = Model_Customer::get_one(array('id_user' => $this->auth->get('id')));

        # SE OBTIENEN LOS ULTIMOS 5 PEDIDOS VENDIDOS
        $sale = Model_Sale::get_one_sold(array('id' => $id_sale, 'id_customer' => $customer->id));

        # SI SE OBTUVO LA INFORMACION
        if(empty($sale))
        {
            # SE EJECUTA UN 404
            throw new HttpNotFoundException;
        }

		# SI EXISTE UNA DIRECCION DE ENVIO
		if(!empty($sale->address))
		{
			# SE OBTIENE LA DIRECCION DE ENVIO
			$sale_shipping_address = array(
				'full_name' => $sale->address->name.' '.$sale->address->last_name,
				'phone'     => $sale->address->phone,
				'address'   => $sale->address->street.', #'.$sale->address->number.','.($sale->address->internal_number != '' ? ' #Int '.$sale->address->internal_number.',' : '').' '.$sale->address->colony.', CP:'.$sale->address->zipcode,
				'region'    => $sale->address->city.', '.$sale->address->state->name
			);
		}

        # SE OBTIENEN LOS PRODUCTOS DE LA VENTA
        foreach($sale->products as $sale_product)
        {
            # SE ALMACENA LA INFORMACION EN SALE_PRODUCTS
            $sale_products[] = array(
                'slug'              => $sale_product->product->slug,
                'name'              => $sale_product->product->name,
                'code'              => $sale_product->product->code,
                'image'             => $sale_product->product->image,
                'price'             => array(
                    'current'       => array(
                        'regular'   => $sale_product->price,
                        'formatted' => number_format($sale_product->price, 2, '.', ',')
                    ),
                    'total'         => array(
                        'regular'   => $sale_product->total,
                        'formatted' => number_format($sale_product->total, 2, '.', ',')
                    ),
                ),
                'available'         => $sale_product->product->available,
                'quantity'          => array(
                    'valid'         => $sale_product->quantity,
                    'current'       => $sale_product->quantity,
                )
            );
        }

		# DEPENDIENDO DEL ESTATUS
		switch($sale->status)
		{
			case 1:
				$status = 'Pagado';
			break;
			case 2:
				$status = 'Por revisar';
			break;
			case 3:
				$status = 'Cancelada';
			break;
		}

         # DEPENDIENDO EL ESTADO DEL PEDIDO
         $order = $sale->order_id;
            if ($order <= 0){
                $order = 'En espera de asignacion';
            }else{
                $order = $sale->order->name;
         }


        # DEPENDIENDO DE LA PAQUETERIA
        $package = $sale->package_id;

            if ($package <= 0){
                $package = 'Sin paqueteria';
            }else{
                $package = $sale->package->name;
        }

        $sale_info = array(
            'id'               => $sale->id,
            'date'             => date('d/m/Y', $sale->sale_date),
            'total'            => number_format($sale->total, 2, '.', ','),
            'status'           => $status,
            'status_id'        => $sale->status,
            'order'            => $order,
            'guide'            => $sale->guide,
            'shipping_address' => $sale_shipping_address,
            'products'         => $sale_products,
			'bill_id'          => (!empty($sale->bill)) ? $sale->bill->id : 0,
			'pdf'              => (!empty($sale->bill)) ? $sale->bill->pdf : '',
			'xml'              => (!empty($sale->bill)) ? $sale->bill->xml : ''
        );

		# SI SE UTILIZO EL METODO POST
		if(Input::method() == 'POST')
		{
			# SE INICIALIZAN LAS VARIABLES
			$voucher = '';

			# SE OBTIENE LA REFERENCIA DEL CV
			$voucher = $_FILES['voucher']['name'];

			# SI EL USUARIO SUBE EL CV
			if(!empty($voucher))
			{
				# SE ESTABLECE LA CONFIGURACION PARA LOS ARCHIVOS
				$voucher_config = array(
					'auto_process'        => false,
					'path'                => DOCROOT.DS.'assets/vouchers',
					'randomize'           => true,
					'auto_rename'         => true,
					'normalize'           => true,
					'normalize_separator' => '-',
					'ext_whitelist'       => array('jpge', 'jpg', 'png'),
					'max_size'            => 20971520,
				);

				# SE INICIALIZA EL PROCESO UPLOAD CON LA CONFIGURACION ESTABLECIDA
				Upload::process($voucher_config);

				# SI EL ARCHIVO ES VALIDO
				if(Upload::is_valid())
				{
					# SE SUBE EL ARCHIVO
					Upload::save();

					# SE OBTIENE LA INFORMACION DE LOS ARCHIVOS
					$value = Upload::get_files();

					# SE ALMACENA EL NOMBRE DEL ARCHIVO
					$voucher = (isset($value[0]['saved_as'])) ? $value[0]['saved_as'] : '';
				}
			}

			# SI EXISTE UN COMPROBANTE
			if($sale->voucher != '')
			{
				# SI EL ARCHIVO RECIEN SUBIDO EXISTE
				if(file_exists(DOCROOT.'assets/vouchers/'.$sale->voucher))
				{
					# SE ELIMINAN EL ARCHIVO
					File::delete(DOCROOT.'assets/vouchers/'.$sale->voucher);
				}
			}

			# SE CREA UN ARREGLO CON LOS DATOS
			$request = array(
				'voucher' => $voucher,
			);

			# SI SE GUARDO CORRECTAMENTE
			if(Model_Sale::do_update($request, $sale->id))
			{
				# SE ESTABLECE EL MENSAJE DE EXITO
				Session::set_flash('success', 'Se agregó el comprobante de pago con éxito.');
			}
			else
			{
				# SE ESTABLECE EL MENSAJE DE ERROR
				Session::set_flash('error', 'No se pudo almacenar el comprobante de pago, por favor ponte en contacto con nosotros.');
			}

			# SE REDIRECCIONA AL USUARIO
			Response::redirect('mi-cuenta/pedido/'.$sale->id);
		}

        # SE ALMACENA LA INFORMACION DE LA VISTA
        $data['sale_history'] = $sale_info;
        $data['package']      = $package;

        # SE CARGA LA VISTA DE INICIO
        $this->template->title       = 'Mi cuenta - Detalle de pedido #'.$sale->id.' | Distribuidora Sajor';
        $this->template->description = 'Distribuidora Sajor';
        $this->template->content     = View::forge('mi_cuenta/pedido', $data, false);
    }


	/**
	 * DESCARGA_PDF
	 *
	 * DESCARGA UN ARCIVO
	 *
	 * @access  public
	 * @return  void
	 */
	public function action_descarga_pdf($id_sale)
	{
		# SE BUSCA EL CUSTOMER
        $customer = Model_Customer::get_one(array('id_user' => $this->auth->get('id')));

        # SE OBTIENEN LOS ULTIMOS 5 PEDIDOS VENDIDOS
        $sale = Model_Sale::get_one_sold(array('id' => $id_sale, 'id_customer' => $customer->id));

		# SI SE OBTUVO LA INFORMACION
        if(empty($sale))
        {
            # SE EJECUTA UN 404
            throw new HttpNotFoundException;
        }
		else
		{
			# SI EXISTE UNA FACTURA
			if(!empty($sale->bill))
			{
				# SE ALMACENA LA RUTA DEL ARCHIVO
				$file = DOCROOT.'assets/descargas/'.$sale->bill->pdf;

				# SE OBTIENE LA INFORMACION DEL ARCHIVO
				$file_url       = stripslashes(trim($file));
				$file_name      = basename($file);
				$file_extension = pathinfo($file_name);
				$file_name_sec  = strtolower($file_url);
				$file_new_name  = $file_name;

				# SE ESTABLECEN LOS HEADERS
				header("Expires: 0");
				header("Cache-Control: no-cache, no-store, must-revalidate");
				header('Cache-Control: pre-check=0, post-check=0, max-age=0', false);
				header("Pragma: no-cache");
				header("Content-type: application/pdf");
				header("Content-Disposition: attachment; filename={$file_new_name}");
				header("Content-Type: application/force-download");

				# MIENTRAS HAYA ANIDAMIENTO EN EL BUFFER
				while(ob_get_level())
				{
					# SE LIMPIA EL BUFFER DE SALIDA
					ob_end_clean();
				}

				# SE LEE EL ARCHIVO
				readfile("{$file_url}");

				# SE TERMINA EL SCRIPT
				exit();
			}
			else
			{
				# SE EJECUTA UN 404
	            throw new HttpNotFoundException;
			}
		}
    }


	/**
	 * DESCARGA_XML
	 *
	 * DESCARGA UN ARCIVO
	 *
	 * @access  public
	 * @return  void
	 */
	public function action_descarga_xml($id_sale)
	{
		# SE BUSCA EL CUSTOMER
        $customer = Model_Customer::get_one(array('id_user' => $this->auth->get('id')));

        # SE OBTIENEN LOS ULTIMOS 5 PEDIDOS VENDIDOS
        $sale = Model_Sale::get_one_sold(array('id' => $id_sale, 'id_customer' => $customer->id));

		# SI SE OBTUVO LA INFORMACION
        if(empty($sale))
        {
            # SE EJECUTA UN 404
            throw new HttpNotFoundException;
        }
		else
		{
			# SI EXISTE UNA FACTURA
			if(!empty($sale->bill))
			{
				# SE ALMACENA LA RUTA DEL ARCHIVO
				$file = DOCROOT.'assets/descargas/'.$sale->bill->xml;

				# SE OBTIENE LA INFORMACION DEL ARCHIVO
				$file_url       = stripslashes(trim($file));
				$file_name      = basename($file);
				$file_extension = pathinfo($file_name);
				$file_name_sec  = strtolower($file_url);
				$file_new_name  = $file_name;

				# SE ESTABLECEN LOS HEADERS
				header("Expires: 0");
				header("Cache-Control: no-cache, no-store, must-revalidate");
				header('Cache-Control: pre-check=0, post-check=0, max-age=0', false);
				header("Pragma: no-cache");
				header("Content-type: text/xml");
				header("Content-Disposition: attachment; filename={$file_new_name}");
				header("Content-Type: application/force-download");

				# SE LEE EL ARCHIVO
				readfile("{$file_url}");

				# SE TERMINA EL SCRIPT
				exit();
			}
			else
			{
				# SE EJECUTA UN 404
	            throw new HttpNotFoundException;
			}
		}
    }


    /**
     * DIRECCIONES
     *
     * MUESTRA LA PAGINA DE MI CUENTA - DIRECCIONES
     *
     * @access  public
     * @return  void
     */
    public function action_direcciones()
    {
        # SE INICIALIZAN LAS VARIABLES
        $data           = array();
        $addresses_info = array();

        # SE OBTIENEN LA INFORMACION DEL CLIENTE
        $customer = Model_Customer::get_one(array('id_user' => $this->auth->get('id')));

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

        # SE ALMACENA LA INFORMACION DE LA VISTA
        $data['addresses'] = $addresses_info;

        # SE CARGA LA VISTA DE INICIO
        $this->template->title       = 'Mi cuenta - Libreta de direcciones | Distribuidora Sajor';
        $this->template->description = 'Distribuidora Sajor';
        $this->template->content     = View::forge('mi_cuenta/direcciones/index', $data, false);
    }


    /**
     * DIRECCIONES - AGREGAR
     *
     * MUESTRA LA PAGINA DE MI CUENTA - DIRECCIONES - AGREGAR
     *
     * @access  public
     * @return  void
     */
    public function action_direcciones_agregar()
    {
        # SE INICIALIZA EL ARREGLO DATA
        $data = array();

        # SE DECLARA EL ARREGLO QUE CONTIENE LAS CLASES
        $classes = array(
            'name'            => null,
            'last_name'       => null,
            'phone'           => null,
            'street'          => null,
            'number'          => null,
            'internal_number' => null,
            'colony'          => null,
            'zipcode'         => null,
            'state'           => null,
            'city'            => null,
            'details'         => null,
            'default'         => null,
        );

        # SE OBTIENEN LA INFORMACION DEL CLIENTE
        $customer = Model_Customer::get_one(array('id_user' => $this->auth->get('id')));

        # SI SE UTILIZO EL METODO POST
        if(Input::method() == 'POST')
        {
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
                $val->add_field('state', 'Estado', 'required|numeric_between[1,32]');
                $val->add_field('city', 'Ciudad', 'required|min_length[1]|max_length[255]');
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

						# SE OBTIENE LA INFORMACION A TRAVES DEL MODELO
                        $default_address = Model_Customers_Address::get_one($query);

                        # SI SE OBTUVO INFORMACION
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
                        # SE ALMACENA EL MENSAJE DE EXITO
                        Session::set_flash('success', '<p>La dirección ha agregado correctamente.</p>');

                        # SE REDIRECCIONA AL USUARIO
                        Response::redirect('mi_cuenta/direcciones');
                    }
                    else
                    {
                        # SE ESTABLECE EL MENSAJE DE ERROR
                        Session::set_flash('error', '<p>Algo inesperado ha ocurrido. Por favor vuelve a intentarlo.</p>');
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
                    $classes['state']           = ($val->error('state')) ? 'is-invalid': 'is-valid';
                    $classes['city']            = ($val->error('city')) ? 'is-invalid': 'is-valid';
                    $classes['details']         = ($val->error('details')) ? 'is-invalid': 'is-valid';

                    # SE REGRESAN LOS VALORES DEL FORMULARIO
                    $data['name']            = Input::post('name');
                    $data['last_name']       = Input::post('last_name');
                    $data['phone']           = Input::post('phone');
                    $data['street']          = Input::post('street');
                    $data['number']          = Input::post('number');
                    $data['internal_number'] = Input::post('internal_number');
                    $data['colony']          = Input::post('colony');
                    $data['zipcode']         = Input::post('zipcode');
                    $data['state']           = Input::post('state');
                    $data['city']            = Input::post('city');
                    $data['details']         = Input::post('details');
                    $data['default']         = (Input::post('default')) ? true : false;

                    # SE ESTABLECE EL MENSAJE DE ERROR
                    Session::set_flash('error', '<p>Hemos encontrado errores en el formulario, por favor inténtalo nuevamente.</p>');
                }
            }
            else
            {
                # SE ESTABLECE EL MENSAJE DE ERROR
                Session::set_flash('error', '<p>Este perfil ya alcanzó el limite de direcciones asociadas.</p>');
            }
        }

        # SE ALMACENA LA INFORMACION DE LA VISTA
        $data['classes']     = $classes;
        $data['states_opts'] = Model_State::get_for_input();
        $data['email']       = $this->auth->get('email');
        $data['name']        = $customer->name;
        $data['last_name']   = $customer->last_name;

        # SE CARGA LA VISTA DE INICIO
        $this->template->title       = 'Mi cuenta - Libreta de direcciones | Distribuidora Sajor';
        $this->template->description = 'Distribuidora Sajor';
        $this->template->content     = View::forge('mi_cuenta/direcciones/agregar', $data, false);
    }


    /**
     * DIRECCIONES - EDITAR
     *
     * MUESTRA LA PAGINA DE MI CUENTA - DIRECCIONES - EDITAR
     *
     * @access  public
     * @return  void
     */
    public function action_direcciones_editar($id_address)
    {
        # SE INICIALIZA EL ARREGLO DATA
        $data = array();

        # SE DECLARA EL ARREGLO QUE CONTIENE LAS CLASES
        $classes = array(
            'name'            => null,
            'last_name'       => null,
            'phone'           => null,
            'street'          => null,
            'number'          => null,
            'internal_number' => null,
            'colony'          => null,
            'zipcode'         => null,
            'state'           => null,
            'city'            => null,
            'details'         => null,
            'default'         => null,
        );

        # SE OBTIENEN LA INFORMACION DEL CLIENTE
        $customer = Model_Customer::get_one(array('id_user' => $this->auth->get('id')));

        # SE BUCA LA DIRECCION
        $query = array(
            'id'          => $id_address,
            'id_customer' => $customer->id
        );
        $address = Model_Customers_Address::get_one($query);

        # SI NO SE OBTUVO LA INFORMACION
        if(empty($address))
        {
            # SE EJECUTA UN 404
            throw new HttpNotFoundException;
        }

        # SE ALMACENA LA INFORMACION DE LA VISTA
        $data['name']            = $address->name;
        $data['last_name']       = $address->last_name;
        $data['phone']           = $address->phone;
        $data['street']          = $address->street;
        $data['number']          = $address->number;
        $data['internal_number'] = $address->internal_number;
        $data['colony']          = $address->colony;
        $data['zipcode']         = $address->zipcode;
        $data['state']           = $address->state->id;
        $data['city']            = $address->city;
        $data['details']         = $address->details;
        $data['default']         = $address->default;

        # SI SE UTILIZO EL METODO POST
        if(Input::method() == 'POST')
        {
            # SE CREA LA VALIDACION DE LOS CAMPOS
            $val = Validation::forge('update_address');
            $val->add_callable('rules');
            $val->add_field('name', 'Nombres', 'required|min_length[1]|max_length[255]');
            $val->add_field('last_name', 'Apellidos', 'required|min_length[1]|max_length[255]');
            $val->add_field('phone', 'Teléfono', 'required|min_length[1]|max_length[255]');
            $val->add_field('street', 'Calle', 'required|min_length[1]|max_length[255]');
            $val->add_field('number', '# Exterior', 'required|min_length[1]|max_length[255]');
            $val->add_field('internal_number', '# Interior', 'min_length[1]|max_length[255]');
            $val->add_field('colony', 'Colonia', 'required|min_length[1]|max_length[255]');
            $val->add_field('zipcode', 'Código postal', 'required|min_length[1]|max_length[255]');
            $val->add_field('state', 'Estado', 'required|numeric_between[1,4121]');
            $val->add_field('city', 'Ciudad', 'required|min_length[1]|max_length[255]');
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

					# SE OBTIENE LA INFORMACION A TRAVES DEL MODELO
                    $default_address = Model_Customers_Address::get_one($query);

                    # SI SE OBTUVO INFORMACION
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
                if(Model_Customers_Address::do_update($request, $address->id))
                {
                    # SE ALMACENA EL MENSAJE DE EXITO
                    Session::set_flash('success', '<p>La dirección ha sido actualizada correctamente.</p>');

                    # SE REDIRECCIONA AL USUARIO
                    Response::redirect('mi-cuenta/direcciones/editar/'.$address->id);
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
                $classes['state']           = ($val->error('state')) ? 'is-invalid': 'is-valid';
                $classes['city']            = ($val->error('city')) ? 'is-invalid': 'is-valid';
                $classes['details']         = ($val->error('details')) ? 'is-invalid': 'is-valid';

                # SE REGRESAN LOS VALORES DEL FORMULARIO
                $data['name']            = Input::post('name');
                $data['last_name']       = Input::post('last_name');
                $data['phone']           = Input::post('phone');
                $data['street']          = Input::post('street');
                $data['number']          = Input::post('number');
                $data['internal_number'] = Input::post('internal_number');
                $data['colony']          = Input::post('colony');
                $data['zipcode']         = Input::post('zipcode');
                $data['state']           = Input::post('state');
                $data['city']            = Input::post('city');
                $data['details']         = Input::post('details');
                $data['default']         = (Input::post('default')) ? true : false;

                # SE ESTABLECE EL MENSAJE DE ERROR
                Session::set_flash('error', '<p>Hemos encontrado errores en el formulario, por favor inténtalo nuevamente.</p>');
            }
        }

        # SE ALMACENA LA INFORMACION DE LA VISTA
        $data['classes']     = $classes;
        $data['states_opts'] = Model_State::get_for_input();
        $data['email']       = $this->auth->get('email');
        $data['name']        = $customer->name;
        $data['last_name']   = $customer->last_name;

        # SE CARGA LA VISTA DE INICIO
        $this->template->title       = 'Mi cuenta - Libreta de direcciones | Distribuidora Sajor';
        $this->template->description = 'Distribuidora Sajor';
        $this->template->content     = View::forge('mi_cuenta/direcciones/editar', $data, false);
    }


	/**
     * FACTURACION
     *
     * MUESTRA LA PAGINA DE MI CUENTA - FACTURACION
     *
     * @access  public
     * @return  void
     */
    public function action_facturacion()
    {
        # SE INICIALIZAN LAS VARIABLES
        $data          = array();
        $tax_data_info = array();

        # SE OBTIENEN LA INFORMACION DEL CLIENTE
        $customer = Model_Customer::get_one(array('id_user' => $this->auth->get('id')));

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

        # SE ALMACENA LA INFORMACION DE LA VISTA
        $data['tax_data'] = $tax_data_info;

        # SE CARGA LA VISTA DE INICIO
        $this->template->title       = 'Mi cuenta - Facturación | Distribuidora Sajor';
        $this->template->description = 'Distribuidora Sajor';
        $this->template->content     = View::forge('mi_cuenta/facturacion/index', $data, false);
    }


    /**
     * FACTURACION - AGREGAR
     *
     * MUESTRA LA PAGINA DE MI CUENTA - FACTURACION - AGREGAR
     *
     * @access  public
     * @return  void
     */
    public function action_facturacion_agregar()
    {
        # SE INICIALIZA EL ARREGLO DATA
        $data = array();

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

        # SE OBTIENEN LA INFORMACION DEL CLIENTE
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
					Response::redirect('mi-cuenta/facturacion');
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
		}

		# SE ALMACENAN LOS DATOS EN EL ARREGLO DATA
		$data['classes']              = $classes;
		$data['states_opts']          = Model_State::get_for_input();
		$data['payment_methods_opts'] = Model_Payments_Method::get_for_input();
		$data['cfdis_opts']           = Model_Cfdi::get_for_input();
		$data['sat_tax_regimes_opts'] = Model_Sat_Tax_Regime::get_for_input();

        # SE CARGA LA VISTA DE INICIO
        $this->template->title       = 'Mi cuenta - Facturación | Distribuidora Sajor';
        $this->template->description = 'Distribuidora Sajor';
        $this->template->content     = View::forge('mi_cuenta/facturacion/agregar', $data, false);
    }


    /**
     * FACTURACION - EDITAR
     *
     * MUESTRA LA PAGINA DE MI CUENTA - FACTURACION - EDITAR
     *
     * @access  public
     * @return  void
     */
    public function action_facturacion_editar($id_tax_datum)
    {
        # SE INICIALIZA EL ARREGLO DATA
        $data = array();

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

        # SE OBTIENEN LA INFORMACION DEL CLIENTE
        $customer = Model_Customer::get_one(array('id_user' => $this->auth->get('id')));

        # SE BUSCA EL RFC
        $query = array(
            'id'          => $id_tax_datum,
            'id_customer' => $customer->id
        );
        $tax_datum = Model_Customers_Tax_Datum::get_one($query);

        # SI NO SE OBTUVO LA INFORMACION
        if(empty($tax_datum))
        {
            # SE EJECUTA UN 404
            throw new HttpNotFoundException;
        }

        # SE ALMACENA LA INFORMACION DE LA VISTA
		$data['business_name']   = $tax_datum->business_name;
		$data['rfc']             = $tax_datum->rfc;
		$data['street']          = $tax_datum->street;
		$data['number']          = $tax_datum->number;
		$data['internal_number'] = $tax_datum->internal_number;
		$data['colony']          = $tax_datum->colony;
		$data['zipcode']         = $tax_datum->zipcode;
		$data['city']            = $tax_datum->city;
		$data['state']           = $tax_datum->state_id;
		$data['payment_method']  = $tax_datum->payment_method_id;
		$data['cfdi']            = $tax_datum->cfdi_id;
		$data['sat_tax_regime']  = $tax_datum->sat_tax_regime_id;
		$data['default']         = $tax_datum->default;

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
                # SI EL RFC FUE MARCADO COMO PREDETERMINADO
                if(Input::post('default'))
                {
                    # SE BUSCA SI HAY UNA REGISTRO POR DEFECTO
                    $query = array(
                        'id_customer' => $customer->id,
                        'default'     => 1
                    );

					# SE OBTIENE LA INFORMACION A TRAVES DEL MODELO
                    $default_address = Model_Customers_Tax_Datum::get_one($query);

                    # SI SE OBTUVO INFORMACION
                    if(!empty($default_address))
                    {
                        # SE CAMBIA EL VALOR DEFAULT A 0
                        Model_Customers_Tax_Datum::do_update(array('default' => 0), $default_address->id);
                    }
                }

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
					'default'           => (Input::post('default')) ? 1 : 0
				);

				# SI SE GUARDO CORRECTAMENTE
				if(Model_Customers_Tax_Datum::do_update($request, $tax_datum->id))
				{
					# SE ALMACENA EL MENSAJE DE EXITO
                    Session::set_flash('success', '<p>El RFC ha sido actualizado correctamente.</p>');

                    # SE REDIRECCIONA AL USUARIO
                    Response::redirect('mi-cuenta/facturacion/editar/'.$tax_datum->id);
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

                # SE ESTABLECE EL MENSAJE DE ERROR
                Session::set_flash('error', '<p>Hemos encontrado errores en el formulario, por favor inténtalo nuevamente.</p>');
            }
        }

        # SE ALMACENA LA INFORMACION DE LA VISTA
		$data['classes']              = $classes;
		$data['states_opts']          = Model_State::get_for_input();
		$data['payment_methods_opts'] = Model_Payments_Method::get_for_input();
		$data['cfdis_opts']           = Model_Cfdi::get_for_input();
		$data['sat_tax_regimes_opts'] = Model_Sat_Tax_Regime::get_for_input();

        # SE CARGA LA VISTA DE INICIO
        $this->template->title       = 'Mi cuenta - Facturación | Distribuidora Sajor';
        $this->template->description = 'Distribuidora Sajor';
        $this->template->content     = View::forge('mi_cuenta/facturacion/editar', $data, false);
    }
}
