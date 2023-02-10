<?php

/**
* CONTROLADOR ADMIN_VENTAS
*
* @package  app
* @extends  Controller_Admin
*/
class Controller_Admin_Ventas extends Controller_Admin
{
	/**
	* BEFORE
	*
	* @return Void
	*/
	public function before()
	{
		# REQUERIDA PARA EL TEMPLATING
		parent::before();

		# SI EL USUARIO NO TIENE PERMISOS
		if(!Auth::member(100) && !Auth::member(50))
		{
			# SE ESTABLECE EL MENSAJE DE ERROR
			Session::set_flash('error', 'No tienes los permisos para acceder a esta sección.');

			# SE REDIRECCIONA AL USUARIO
			Response::redirect('admin');
		}
	}


	/**
	* INDEX
	*
	* MUESTRA UNA LISTADO DE REGISTROS
	*
	* @access  public
	* @return  Void
	*/
	public function action_index($search = '')
	{
		# SE INICIALIZAN LAS VARIABLES
		$data       = array();
		$sales_info = array();
		$per_page   = 100;

		# SE BUSCA LA INFORMACION A TRAVES DEL MODELO
		$sales = Model_Sale::query()
		->related('customer')
		->where('status', '>=', 1);

		# SI HAY UNA BUSQUEDA
		if($search != '')
		{
			# SE ALMACENA LA BUSQUEDA ORIGINAL
			$original_search = $search;

			# SE LIMPIA LA CADENA DE BUSQUEDA
			$search = str_replace('+', ' ', rawurldecode($search));

			# SE REEMPLAZA LOS ESPACIOS POR PORCENTAJES
			$search = str_replace(' ', '%', $search);

			# SE AGREGA LA CLAUSULA
			$sales = $sales->where(DB::expr("CONCAT(`t0`.`id`, ' ', `t1`.`name`, ' ', `t1`.`last_name`)"), 'like', '%'.$search.'%');
		}

		# SE ESTABLECE LA CONFIGURACION DE LA PAGINACION
		$config = array(
			'name'           => 'admin',
			'pagination_url' => Uri::current(),
			'total_items'    => $sales->count(),
			'per_page'       => $per_page,
			'uri_segment'    => 'pagina',
		);

		# SE CREA LA INSTANCIA DE LA PAGINACION
		$pagination = Pagination::forge('sales', $config);

		# SE EJECUTA EL QUERY
		$sales = $sales->order_by('sale_date', 'desc')
		->rows_limit($pagination->per_page)
		->rows_offset($pagination->offset)
		->get();

		# SI SE OBTIENE INFORMACION
		if(!empty($sales))
		{
			# SE RECORRE ELEMENTO POR ELEMENTO
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

				# DEPENDIENDO LA ORDEN
				$order = $sale->order_id;
				if ($order <= 0){
					$order = 'En espera de asignacion';
				}else{
					$order = $sale->order->name;
				}


				# SE ALMACENA LA INFORMACION
				$sales_info[] = array(
					'id'            => $sale->id,
					'customer'      => $sale->customer->name.' '.$sale->customer->last_name,
					'email'         => $sale->customer->user->email,
					'type'          => ($sale->status == 2) ? $sale->payment->type->name.' (Por revisar)' : $sale->payment->type->name,
					'total'         => '$'.number_format($sale->total, '2', '.', ','),
					'sale_date'     => date('d/m/Y - H:i', $sale->sale_date),
					'status'        => $status,
					'ordersap'      => $sale->ordersap,
					'factsap'      	=> $sale->factsap,
					'guide'      	=> $sale->guide,
					'order'         => $order
				);
			}
		}

		# SE ALMACENA LA INFORMACION PARA LA VISTA
		$data['sales']      = $sales_info;
		$data['search']     = str_replace('%', ' ', $search);
		$data['pagination'] = $pagination->render();

		# SE CARGA LA VISTA
		$this->template->title   = 'Ventas';
		$this->template->content = View::forge('admin/ventas/index', $data, false);
	}


	/**
	* BUSCAR
	*
	* REDIRECCIONA A LA URL DE BUSCAR REGISTROS
	*
	* @access  public
	* @return  Void
	*/
	public function action_buscar()
	{
		# SI SE UTILIZO EL METODO POST
		if(Input::method() == 'POST')
		{
			# SE OBTIENEN LOS VALORES
			$data = array(
				'search' => ($_POST['search'] != '') ? $_POST['search'] : '',
			);

			# SE CREA LA VALIDACION DE LOS CAMPOS
			$val = Validation::forge('search');
			$val->add_callable('Rules');
			$val->add_field('search', 'search', 'max_length[100]');

			# SI NO HAY NINGUN PROBLEMA CON LA VALIDACION
			if($val->run($data))
			{
				# SE REMPLAZAN ALGUNOS CARACTERES
				$search = str_replace(' ', '+', $val->validated('search'));
				$search = str_replace('*', '', $search);

				# SE ALMACENA LA CADENA DE BUSQUEDA
				$search = ($val->validated('search') != '') ? $search : '';

				# SE REDIRECCIONA A BUSCAR
				Response::redirect('admin/ventas/index/'.$search);
			}
			else
			{
				# SE REDIRECCIONA AL USUARIO
				Response::redirect('admin/ventas');
			}
		}
		else
		{
			# SE REDIRECCIONA AL USUARIO
			Response::redirect('admin/ventas');
		}
	}


	/**
	* INFO
	*
	* MUESTRA LA INFORMACION DE UN REGISTRO DE LA BASE DE DATOS
	*
	* @access  public
	* @return  Void
	*/
	public function action_info($sale_id = 0)
	{
		# SI NO SE RECIBE UN ID O NO ES UN NUMERO
		if($sale_id == 0 || !is_numeric($sale_id))
		{
			# SE REDIRECCIONA AL USUARIO
			Response::redirect('admin/ventas');
		}

		# SE INICIALIZAN LAS VARIABLES
		$data      = array();
		$bill_flag = false;

		# SE BUSCA LA INFORMACION A TRAVES DEL MODELO
		$sale = Model_Sale::query()
		->where('id', $sale_id)
		->where('status', '>=', 1)
		->get_one();

		# SI SE OBTIENE INFORMACION
		if(!empty($sale))
		{
			# SI EXISTEN PRODUCTOS
			if(!empty($sale->products))
			{
				# SE RECORRE ELEMENTO POR ELEMENTO
				foreach($sale->products as $product)
				{
					# SE ALMACENA LA INFORMACION
					$products_info[] = array(
						'id'           => $product->id,
						'code'          => $product->product->code,
						'image'         => 'thumb_'.$product->product->image,
						'name'          => Str::truncate($product->product->name, '60', '...'),
						'name_complete' => $product->product->name,
						'quantity'      => $product->quantity,
						'price'         => '$'.number_format($product->price, '2', '.', ','),
						'total'         => '$'.number_format($product->total, '2', '.', ',')
					);
				}

				# SI EXISTE FACTURA
				if(!empty($sale->tax_data))
				{
					# SE CAMBIA EL VALOR DE LA BANDERA
					$bill_flag = true;
				}
			}
			else
			{
				$products_info = 'Este pedido fue realizado con error y no se cargaron productos';
			}

			# SE ALMACENA LA INFORMACION PARA LA VISTA
			$data['customer']                 = $sale->customer->name.' '.$sale->customer->last_name;
			$data['email']                    = $sale->customer->user->email;
			$data['customer_id']              = $sale->customer->id;
			$data['codigosap']         		  = $sale->customer->codigosap;
			$data['total']                    = '$'.number_format($sale->total, '2', '.', ',');
			$data['type']                     = $sale->payment->type->name;
			$data['name']                     = $sale->customer->name;
			$data['last_name']                = $sale->customer->last_name;
			$data['ordersap']	              = $sale->ordersap;
			$data['factsap']      	          = $sale->factsap;
			$data['guide']      	          = $sale->guide;
			$data['voucher']      	          = $sale->voucher;
			$data['phone']                    = ($sale->customer->phone != '') ? $sale->customer->phone : 'N/A';
			$data['address_flag']             = ($sale->address_id == 0) ? false : true;
			$data['address_name']             = ($data['address_flag']) ? $sale->address->name : '';
			$data['address_last_name']        = ($data['address_flag']) ? $sale->address->last_name : '';
			$data['address_phone']            = ($data['address_flag']) ? $sale->address->phone : '';
			$data['street']                   = ($data['address_flag']) ? $sale->address->street : '';
			$data['number']                   = ($data['address_flag']) ? $sale->address->number : '';
			$data['internal_number']          = ($data['address_flag']) ? $sale->address->internal_number : '';
			$data['colony']                   = ($data['address_flag']) ? $sale->address->colony : '';
			$data['zipcode']                  = ($data['address_flag']) ? $sale->address->zipcode : '';
			$data['city']                     = ($data['address_flag']) ? $sale->address->city : '';
			$data['state']                    = ($data['address_flag']) ? $sale->address->state->name : '';
			$data['details']                  = ($data['address_flag']) ? $sale->address->details : '';
			$data['bill_flag']                = $bill_flag;
			$data['business_name']            = ($bill_flag) ? $sale->tax_data->business_name : '';
			$data['rfc']                      = ($bill_flag) ? $sale->tax_data->rfc : '';
			$data['tax_data_street']          = ($bill_flag) ? $sale->tax_data->street : '';
			$data['tax_data_number']          = ($bill_flag) ? $sale->tax_data->number : '';
			$data['tax_data_internal_number'] = ($bill_flag) ? $sale->tax_data->internal_number : '';
			$data['tax_data_colony']          = ($bill_flag) ? $sale->tax_data->colony : '';
			$data['tax_data_zipcode']         = ($bill_flag) ? $sale->tax_data->zipcode : '';
			$data['tax_data_city']            = ($bill_flag) ? $sale->tax_data->city : '';
			$data['tax_data_state']           = ($bill_flag) ? $sale->tax_data->state->name : '';
			$data['payment_method']           = ($bill_flag) ? $sale->tax_data->payment_method->code.' '.$sale->tax_data->payment_method->name : '';
			$data['cfdi']                     = ($bill_flag) ? $sale->tax_data->cfdi->code.' '.$sale->tax_data->cfdi->name : '';
			$data['sat_tax_regime']           = ($bill_flag) ? $sale->tax_data->sat_tax_regime->code.' '.$sale->tax_data->sat_tax_regime->name : '';
			$data['csf']                      = ($bill_flag) ? $sale->tax_data->csf : '';
			$data['bill_id']                  = (!empty($sale->bill)) ? $sale->bill->id : 0;
			$data['pdf']                      = (!empty($sale->bill)) ? $sale->bill->pdf : '';
			$data['xml']                      = (!empty($sale->bill)) ? $sale->bill->xml : '';
			$data['products']                 = $products_info;
		}
		else
		{
			# SE REDIRECCIONA AL USUARIO
			Response::redirect('admin/ventas');
		}

		# SE INICIALIZA LA VARIABLE
		$status_txt = '';

		# DEPENDIENDO DEL ESTATUS
		switch($sale->status)
		{
			case 1:
				$status_txt = 'Pagado';
			break;
			case 2:
				$status_txt = 'Por revisar';
			break;
			case 3:
				$status_txt = 'Cancelada';
			break;
		}

		# SE DEFINE EL DEFAUL DE PAQUETERIA
		$package = $sale->package_id;

		if ($package <= 0){
			$package = 'No se a registrado paqueteria o se entrego local';
		}else{
			$package = $sale->package->name;
		}

		# SE DEFINE EL DEFAUL DEL ESTATUS DE PEDIDO
		$order = $sale->order_id;

		if ($order <= 0){
			$order = 'En espera de asignacion';
		}else{
			$order = $sale->order->name;
		}

		# SE ALMACENA LA INFORMACION PARA LA VISTA
		$data['id']         = $sale_id;
		$data['status']     = $sale->status;
		$data['status_txt'] = $status_txt;
		$data['package']    = $package;
		$data['order']      = $order;

		# SE CARGA LA VISTA
		$this->template->title   = 'Información de la venta';
		$this->template->content = View::forge('admin/ventas/info', $data);
	}


	/**
	* AGREGAR FACTURA
	*
	* PERMITE AGREGAR UNA FACTURA UN REGISTRO DE LA BASE DE DATOS
	*
	* @access  public
	* @return  Void
	*/
	public function action_agregar_factura($sale_id = 0)
	{
		# SI NO SE RECIBE UN ID O NO ES UN NUMERO
		if($sale_id == 0 || !is_numeric($sale_id))
		{
			# SE REDIRECCIONA AL USUARIO
			Response::redirect('admin/ventas');
		}

		# SE INICIALIZAN LAS VARIABLES
		$data    = array();
		$classes = array();
		$fields  = array('pdf', 'xml');

		# SE RECORRE CAMPO POR CAMPO
		foreach($fields as $field)
		{
			# SE CREAN LAS CLASES DEL CAMPO
			$classes[$field] = array (
				'form-group'   => null,
				'form-control' => null,
			);
		}

		# SE BUSCA LA INFORMACION A TRAVES DEL MODELO
		$sale = Model_Sale::query()
		->where('id', $sale_id)
		->where('status', '>=', 1)
		->get_one();

		# SI SE OBTIENE INFORMACION
		if(!empty($sale))
		{
			# SI SE UTILIZO EL METODO POST
			if(Input::method() == 'POST')
			{
				# SE OBTIENE LA REFERENCIA DEL ARCHIVO
				$pdf = $_FILES['pdf']['name'];
				$xml = $_FILES['xml']['name'];

				# SI EL USUARIO SUBE EL ARCHIVO Y NO ESTA VACIO
				if(!empty($pdf) && !empty($xml))
				{
					# SE ESTABLECE LA CONFIGURACION
					$config = array(
						'path'          => DOCROOT.DS.'assets/descargas',
						'randomize'     => false,
						'auto_rename'   => true,
						'normalize'     => true,
						'ext_whitelist' => array('pdf', 'xml'),
						'max_size'      => 20971520,
					);

					# SE INICIALIZA EL PROCESO UPLOAD CON LA CONFIGURACION ESTABLECIDA
					Upload::process($config);

					# SI EL ARCHIVO ES VALIDO
					if(Upload::is_valid())
					{
						# SE SUBE EL ARCHIVO
						Upload::save();

						# SE OBTIENE LA INFORMACION DEL ARCHIVO
						$value = Upload::get_files();

						# SE ALMACENA EL NOMBRE DE LOS ARCHIVOS
						$pdf = $value[0]['saved_as'];
						$xml = $value[1]['saved_as'];

						# SE CREA EL MODELO CON LA INFORMACION
						$bill = new Model_Bill(array(
							'sale_id' => $sale_id,
							'pdf'     => $pdf,
							'xml'     => $xml
						));

						# SI SE ALMACENO EL REGISTRO EN LA BASE DE DATOS
						if($bill->save())
						{
							# SE ESTABLECE EL MENSAJE DE EXITO
							Session::set_flash('success', 'Se agregó la factura correctamente.');

							# SE REDIRECCIONA AL USUARIO
							Response::redirect('admin/ventas/info/'.$sale_id);
						}
					}
					else
					{
						# SE ESTABLECE EL MENSAJE DE ERROR
						Session::set_flash('error', 'Solo están permitidos archivos con extensión <b>.pdf</b> y <b>.xml</b>.');

						# SE RECORRE CLASE POR CLASE
						foreach($classes as $name => $class)
						{
							# SE ALMACENA LA INFORMACION PARA LA VISTA
							$data[$name] = Input::post($name);
						}
					}
				}
				else
				{
					# SE ESTABLECE EL MENSAJE DE ERROR
					Session::set_flash('error', 'Asegúrate de subir ambos archivos.');

					# SE RECORRE CLASE POR CLASE
					foreach($classes as $name => $class)
					{
						# SE ALMACENA LA INFORMACION PARA LA VISTA
						$data[$name] = Input::post($name);
					}
				}
			}
		}

		# SE ALMACENA LA INFORMACION PARA LA VISTA
		$data['id']      = $sale_id;
		$data['classes'] = $classes;

		# SE CARGA LA VISTA
		$this->template->title   = 'Agregar factura';
		$this->template->content = View::forge('admin/ventas/agregar_factura', $data);
	}


	/**
	* EDITAR FACTURA
	*
	* PERMITE EDITAR UN REGISTRO DE LA BASE DE DATOS
	*
	* @access  public
	* @return  Void
	*/
	public function action_editar_factura($bill_id = 0)
	{
		# SI NO SE RECIBE UN ID O NO ES UN NUMERO
		if($bill_id == 0 || !is_numeric($bill_id))
		{
			# SE REDIRECCIONA AL USUARIO
			Response::redirect('admin/ventas');
		}

		# SE INICIALIZAN LAS VARIABLES
		$data    = array();
		$classes = array();
		$fields  = array('pdf', 'xml');

		# SE RECORRE CAMPO POR CAMPO
		foreach($fields as $field)
		{
			# SE CREAN LAS CLASES DEL CAMPO
			$classes[$field] = array (
				'form-group'   => null,
				'form-control' => null,
			);
		}

		# SE BUSCA LA INFORMACION A TRAVES DEL MODELO
		$bill = Model_Bill::query()
		->where('id', $bill_id)
		->get_one();

		# SI SE OBTIENE INFORMACION
		if(!empty($bill))
		{
			# SE ALMACENA LA INFORMACION PARA LA VISTA
			$data['sale_id'] = $bill->sale_id;
			$data['pdf']     = $bill->pdf;
			$data['xml']     = $bill->xml;
		}
		else
		{
			# SE REDIRECCIONA AL USUARIO
			Response::redirect('admin/ventas');
		}

		# SI SE UTILIZO EL METODO POST
		if(Input::method() == 'POST')
		{
			# SE ALMACENA EL NOMBRE DEL ARCHIVO ACTUAL
			$current_pdf = $bill->pdf;
			$current_xml = $bill->xml;

			# SE INICIALIZA LA VARIABLE ERROR
			$error = 0;

			# SE OBTIENE LA REFERENCIA DEL ARCHIVO
			$pdf = $_FILES['pdf']['name'];
			$xml = $_FILES['xml']['name'];

			# SI EL USUARIO SUBE EL ARCHIVO Y NO ESTA VACIO
			if(!empty($pdf) || !empty($xml))
			{
				# SE ESTABLECE LA CONFIGURACION
				$config = array(
					'path'          => DOCROOT.DS.'assets/descargas',
					'randomize'     => false,
					'auto_rename'   => true,
					'normalize'     => true,
					'ext_whitelist' => array('pdf', 'xml'),
					'max_size'      => 20971520,
				);

				# SE INICIALIZA EL PROCESO UPLOAD CON LA CONFIGURACION ESTABLECIDA
				Upload::process($config);

				# SI EL ARCHIVO ES VALIDO
				if(Upload::is_valid())
				{
					# SE SUBE EL ARCHIVO
					Upload::save();

					# SE OBTIENE LA INFORMACION DEL ARCHIVO
					$value = Upload::get_files();

					# SE ALMACENA EL NOMBRE DE LOS ARCHIVOS
					$pdf = $value[0]['saved_as'];
					$xml = $value[1]['saved_as'];
				}
			}

			# SI NO HAY ERRORES
			if($error == 0)
			{
				# SI SE SUBIO EL ARCHIVO
				if($pdf != '')
				{
					# SE ESTABLECE EL NOMBRE DEL NUEVO ARCHIVO
					$bill->pdf = $pdf;

					# SI EXISTE UN ARCHIVO ACTUAL
					if($current_pdf != '')
					{
						# SE ELIMINA EL ARCHIVO ORIGINAL
						File::delete(DOCROOT.'assets/descargas/'.$current_pdf);
					}
				}

				# SI SE SUBIO EL ARCHIVO
				if($xml != '')
				{
					# SE ESTABLECE EL NOMBRE DEL NUEVO ARCHIVO
					$bill->xml = $xml;

					# SI EXISTE UN ARCHIVO ACTUAL
					if($current_xml != '')
					{
						# SE ELIMINA EL ARCHIVO ORIGINAL
						File::delete(DOCROOT.'assets/descargas/'.$current_xml);
					}
				}

				# SI SE ALMACENO EL REGISTRO EN LA BASE DE DATOS
				if($bill->save())
				{
					# SE ESTABLECE EL MENSAJE DE EXITO
					Session::set_flash('success', 'Se actualizó la información de la factura correctamente.');

					# SE REDIRECCIONA AL USUARIO
					Response::redirect('admin/ventas/info/'.$bill->sale_id);
				}
			}
			else
			{
				# SE ESTABLECE EL MENSAJE DE ERROR
				Session::set_flash('error', 'Solo están permitidos archivos con extensión <b>.pdf</b>.');

				# SE RECORRE CLASE POR CLASE
				foreach($classes as $name => $class)
				{
					# SE ALMACENA LA INFORMACION PARA LA VISTA
					$data[$name] = Input::post($name);
				}
			}
		}

		# SE ALMACENA LA INFORMACION PARA LA VISTA
		$data['id']      = $bill_id;
		$data['classes'] = $classes;

		# SE CARGA LA VISTA
		$this->template->title   = 'Editar factura';
		$this->template->content = View::forge('admin/ventas/editar_factura', $data);
	}


	/**
	* EDITAR VENTA
	*
	* PERMITE EDITAR UN REGISTRO DE LA BASE DE DATOS
	*
	* @access  public
	* @return  Void
	*/
	public function action_editar($sale_id = 0)
	{
		# SI NO SE RECIBE UN ID O NO ES UN NUMERO
		if($sale_id == 0 || !is_numeric($sale_id))
		{
			# SE REDIRECCIONA AL USUARIO
			Response::redirect('admin/ventas');
		}
		# SE INICIALIZAN LAS VARIABLES
		$data          = array();
		$classes       = array();
		$fields        = array('customer','email','codigosap','total','type','name','order','ordersap','factsap','guide','package','id','pdf','xml');
		$package_opts  = array();
		$order_opts    = array();

		# SE RECORRE CAMPO POR CAMPO
		foreach($fields as $field)
		{
			# SE CREAN LAS CLASES DEL CAMPO
			$classes[$field] = array (
				'form-group'   => null,
				'form-control' => null,
			);
		}

		# SE BUSCA LA INFORMACION A TRAVES DEL MODELO
		$sale = Model_Sale::query()
		->where('id', $sale_id)
		->get_one();

		# SI SE OBTIENE INFORMACION
		if(!empty($sale))
		{
			# SE BUSCA LA INFORMACION A TRAVES DEL MODELO
			$customer = Model_Customer::query()
			->where('id', $sale->customer_id)
			->get_one();

			# SE ALMACENA LA INFORMACION PARA LA VISTA
			$data['customer']          = $sale->customer->name.' '.$sale->customer->last_name;
			$data['email']             = $sale->customer->user->email;
			$data['codigosap']         = $sale->customer->codigosap;
			$data['total']             = '$'.number_format($sale->total, '2', '.', ',');
			$data['type']              = $sale->payment->type->name;
			$data['ordersap']	       = $sale->ordersap;
			$data['factsap']      	   = $sale->factsap;
			$data['guide']      	   = $sale->guide;
			$data['order']             = $sale->order_id;
			$data['package']           = $sale->package_id;
			$data['total']             = '$'.number_format($sale->total, '2', '.', ',');
			$data['name']              = $sale->customer->name;
			$data['last_name']         = $sale->customer->last_name;
			$data['phone']             = ($sale->customer->phone != '') ? $sale->customer->phone : 'N/A';
			$data['address_flag']      = ($sale->address_id == 0) ? false : true;
			$data['address_name']      = ($data['address_flag']) ? $sale->address->name : '';
			$data['address_last_name'] = ($data['address_flag']) ? $sale->address->last_name : '';
			$data['address_phone']     = ($data['address_flag']) ? $sale->address->phone : '';
			$data['street']            = ($data['address_flag']) ? $sale->address->street : '';
			$data['number']            = ($data['address_flag']) ? $sale->address->number : '';
			$data['internal_number']   = ($data['address_flag']) ? $sale->address->internal_number : '';
			$data['colony']            = ($data['address_flag']) ? $sale->address->colony : '';
			$data['zipcode']           = ($data['address_flag']) ? $sale->address->zipcode : '';
			$data['city']              = ($data['address_flag']) ? $sale->address->city : '';
			$data['state']             = ($data['address_flag']) ? $sale->address->state->name : '';
			$data['details']           = ($data['address_flag']) ? $sale->address->details : '';
			$data['bill_id']           = (!empty($sale->bill)) ? $sale->bill->id : 0;
			$data['pdf']               = (!empty($sale->bill)) ? $sale->bill->pdf : '';
			$data['xml']               = (!empty($sale->bill)) ? $sale->bill->xml : '';
		}
		else
		{
			# SE REDIRECCIONA AL USUARIO
			Response::redirect('admin/ventas');
		}

		# SI SE UTILIZO EL METODO POST
		if(Input::method() == 'POST')
		{
			# SE CREA LA VALIDACION DE LOS CAMPOS
			$val = Validation::forge('sale');
			$val->add_callable('Rules');
			$val->add_field('codigosap', 'codigosap', 'min_length[1]|max_length[255]');
			$val->add_field('ordersap', 'ordersap', 'min_length[1]|max_length[255]');
			$val->add_field('factsap', 'factsap', 'min_length[1]|max_length[255]');
			$val->add_field('guide', 'guide', 'min_length[1]|max_length[255]');
			$val->add_field('order', 'order', 'required|valid_string[numeric]|numeric_min[1]');
			$val->add_field('package', 'package', 'valid_string[numeric]|numeric_min[1]');
			$val->add_field('status', 'status', 'required|valid_string[numeric]|numeric_min[1]');

			# SI NO HAY NINGUN PROBLEMA CON LA VALIDACION
			if($val->run())
			{
				# SI SE OBTIENE INFORMACION
				if(!empty($customer))
				{
					# SE ESTABLECE LA INFORMACION
					$customer->codigosap = $val->validated('codigosap');

					# SE ALMACENA EL REGISTRO EN LA BASE DE DATOS
					$customer->save();
				}

				# SE ESTABLECE LA INFORMACION
				$sale->ordersap   = $val->validated('ordersap');
				$sale->factsap    = $val->validated('factsap');
				$sale->guide      = $val->validated('guide');
				$sale->order_id   = $val->validated('order');
				$sale->package_id = $val->validated('package');
				$sale->status     = $val->validated('status');

				# SI SE ALMACENO EL REGISTRO EN LA BASE DE DATOS
				if($sale->save())
				{
					# SE ESTABLECE EL MENSAJE DE EXITO
					Session::set_flash('success', 'Se actualizó la información de la venta No. <b>'.$sale->id.'</b> correctamente.');  //sajor

					# SE REDIRECCIONA AL USUARIO
					Response::redirect('admin/ventas/editar/'.$sale_id);
				}
			}
			else
			{
				# SE ESTABLECE EL MENSAJE DE ERROR
				Session::set_flash('error', 'Encontramos algunos errores en el formulario, por favor verifícalo.');

				# SE ALMACENA LOS ERRORES DETECTADOS
				$data['errors'] = $val->error();

				# SE RECORRE CLASE POR CLASE
				foreach($classes as $name => $class)
				{
					# SE ESTABLECE EL VALOR DE LAS CLASES
					$classes[$name]['form-group']   = ($val->error($name)) ? 'has-danger' : 'has-success';
					$classes[$name]['form-control'] = ($val->error($name)) ? 'is-invalid' : 'is-valid';

					# SE ALMACENA LA INFORMACION PARA LA VISTA
					$data[$name] = Input::post($name);
				}
			}

		}

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

		# SE ESTBLECE LA OPCION DE ESTATUS DE PEDIDO POR DEFAULT
		$order_opts += array('0' => 'Selecciona una opción');

		# SE BUSCA LA INFORMACION A TRAVES DEL MODELO
		$orders = Model_Order::query()
		->where('deleted', 0)
		->order_by('name', 'asc')
		->get();

		# SI SE OBTIENE INFORMACION
		if(!empty($orders))
		{
			# SE RECORRE ELEMENTO POR ELEMENTO
			foreach($orders as $order)
			{
				# SE ALMACENA LA OPCION
				$order_opts += array($order->id => $order->name);
			}
		}

		# SE ESTBLECE LA OPCION DE PAQUETERIA POR DEFAULT
		$package_opts += array('0' => 'Selecciona una opción');

		# SE BUSCA LA INFORMACION A TRAVES DEL MODELO
		$packages = Model_Package::query()
		->where('deleted', 0)
		->order_by('name', 'asc')
		->get();

		# SI SE OBTIENE INFORMACION
		if(!empty($packages))
		{
			# SE RECORRE ELEMENTO POR ELEMENTO
			foreach($packages as $package)
			{
				# SE ALMACENA LA OPCION
				$package_opts += array($package->id => $package->name);
			}
		}

		# SE ALMACENA LA INFORMACION PARA LA VISTA
		$data['id']     		= $sale_id;
		$data['classes']    	= $classes;
		$data['package_opts'] 	= $package_opts;
		$data['order_opts']    	= $order_opts;
		$data['status'] 		= $sale->status;

		# SE CARGA LA VISTA
		$this->template->title   = 'Editar venta';
		$this->template->content = View::forge('admin/ventas/editar', $data);
	}


	/**
	 * CONFIRMAR_TRANSFERENCIA
	 *
	 * CAMBIA EL STATUS DE LA VENTA
	 *
	 * @access  public
	 * @return  Void
	 */
	public function action_confirmar_transferencia($sale_id = 0)
	{
		# SI NO SE RECIBE UN ID O NO ES UN NUMERO
		if($sale_id == 0 || !is_numeric($sale_id))
		{
			# SE REDIRECCIONA AL USUARIO
			Response::redirect('admin/ventas');
		}

		# SE BUSCA LA INFORMACION A TRAVES DEL MODELO
		$sale = Model_Sale::query()
		->where('id', $sale_id)
		->where('status', 2)
		->get_one();

		# SI SE OBTIENE INFORMACION
		if(!empty($sale))
		{
			# SE ESTEBLECE LA NUEVA INFORMACION
			$sale->status = 1;

			# SI SE ALMACENO EL REGISTRO EN LA BASE DE DATOS
			if($sale->save())
			{
				# SE RECORRE ELEMENTO POR ELEMENTO
				foreach($sale->products as $sale_product)
				{
					# SE BUSCA LA INFORMACION A TRAVES DEL MODELO
					$product = Model_Product::query()
					->where('id', $sale_product->product_id)
					->get_one();

					# SI SE OBTIENE INFORMACION
					if(!empty($product))
					{
						# SE RESTA LA CANTIDAD COMPRADA
						$product->available -= $sale_product->quantity;

						# SE GUARDA EL REGISTRO EN LA BASE DE DATOS
						$product->save();
					}
				}

				# SE ENVIA EL CORREO AL USUARIO
				$this->send_user_mail($sale->id);

				# SE ENVIA EL CORREO AL ADMINISTRADOR
				$this->send_admin_mail($sale->id);

				# SE ENVIA EL CORREO AL DESARROLLADOR
				$this->send_dev_mail($sale->id, 'Transferencia confirmada', 'Finalizada');

				# SE ESTABLECE EL MENSAJE DE EXITO
				Session::set_flash('success', 'Se actualizó el pedido <b>'.$sale->id.'</b> correctamente.');
			}
		}

		# SE REDIRECCIONA AL USUARIO
		Response::redirect('admin/ventas');
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
		->related('tax_data')
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
                                <h1 style="margin: 0 0 10px 0; font-family: sans-serif; font-size: 25px; line-height: 30px; color: #333333; font-weight: normal;">¡Gracias por comprar en Distribuidora Sajor!</h1>
                                <p style="margin: 0;">Este mesaje ha sido enviado automáticamente desde la página web de <strong>Distribuidora Sajor</strong>, para confirmar tu pedido, a continuación te dejamos la información registrada:</p>
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
            $email->from('sistemas@sajor.mx', 'Distribuidora Sajor');
            $email->reply_to('sistemas@sajor.mx', 'Distribuidora Sajor');
            $email->to(array(
                $sale->customer->user->email => $sale->customer->name.' '.$sale->customer->last_name,
                //'omar@sectorweb.mx' => 'Sector Web',
                //'ventas@sajor.mx' => 'Distribuidora Sajor',
            ));
            $email->subject('Distribuidora Sajor - Pedido #'.$sale->id);
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
		->related('tax_data')
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
                                <p style="margin: 0;">Este mesaje ha sido enviado desde la página web de <strong>Distribuidora Sajor</strong>, con la siguiente información:</p>
                                <p>
                                    <strong style="display: block;">Nombre:</strong>
                                    <span style="display: block; margin-bottom: 15px; color: #ee3530"">'.$sale->customer->name.' '.$sale->customer->last_name.'</span>
                                    <strong style="display: block;">Correo electrónico:</strong>
                                    <span style="display: block; margin-bottom: 15px; color: #ee3530"">'.$sale->customer->user->email.'</span>
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
			$email->from('sistemas@sajor.mx', 'Distribuidora Sajor');
            $email->reply_to($sale->customer->user->email, $sale->customer->name.' '.$sale->customer->last_name);
            $email->to(array(
				'sistemas@sajor.mx' => 'Distribuidora Sajor',
				//'omar@sectorweb.mx' => 'Sector Web',
            ));
            $email->subject('Distribuidora Sajor - Venta en línea #'.$sale->id);
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
		->related('tax_data')
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
                                <p style="margin: 0;">Este mesaje ha sido enviado desde la página web de <strong>Distribuidora Sajor</strong>, con la siguiente información:</p>
                                <p>
                                    <strong style="display: block;">Nombre:</strong>
                                    <span style="display: block; margin-bottom: 15px; color: #ee3530"">'.$sale->customer->name.' '.$sale->customer->last_name.'</span>
                                    <strong style="display: block;">Correo electrónico:</strong>
                                    <span style="display: block; margin-bottom: 15px; color: #ee3530"">'.$sale->customer->user->email.'</span>
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
			$email->from('sistemas@sajor.mx', 'Distribuidora Sajor');
            $email->reply_to($sale->customer->user->email, $sale->customer->name.' '.$sale->customer->last_name);
            $email->to(array(
                'omar@sectorweb.mx' => 'Sector Web'
            ));
            $email->subject('Distribuidora Sajor - Dev #'.$sale->id);
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
