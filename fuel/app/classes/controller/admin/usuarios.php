<?php

/**
 * CONTROLADOR ADMIN_USUARIOS
 *
 * @package  app
 * @extends  Controller_Admin
 */
class Controller_Admin_Usuarios extends Controller_Admin
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
		$users_info = array();
		$per_page   = 100;

		# SE BUSCA LA INFORMACION A TRAVES DEL MODELO
		$users = Model_User::query()
		->related('customer')
		->where('group', 1);

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
			$users = $users->where(DB::expr("CONCAT(`t0`.`username`, ' ', `t0`.`email`,' ', `t1`.`id`)"), 'like', '%'.$search.'%');
		}

		# SE ESTABLECE LA CONFIGURACION DE LA PAGINACION
		$config = array(
			'name'           => 'admin',
			'pagination_url' => Uri::current(),
			'total_items'    => $users->count(),
			'per_page'       => $per_page,
			'uri_segment'    => 'pagina',
		);

		# SE CREA LA INSTANCIA DE LA PAGINACION
		$pagination = Pagination::forge('users', $config);

		# SE EJECUTA EL QUERY
		$users = $users->order_by('id', 'desc')
		->rows_limit($pagination->per_page)
		->rows_offset($pagination->offset)
		->get();

		# SI SE OBTIENE INFORMACION
		if(!empty($users))
		{
			# SE RECORRE ELEMENTO POR ELEMENTO
			foreach($users as $user)
			{
				# SE DESERIALIZAN LOS CAMPOS EXTRAS
				$status = unserialize($user->profile_fields);

				# SE ALMACENA LA INFORMACION
				$users_info[] = array(
					'id'       	=> $user->id,
					'customer_id'  => $user->customer->id,
					'username' 	=> $user->username,
					'name'     	=> $user->customer->name.' '.$user->customer->last_name,
					'email'    	=> $user->email,
					'type'     	=> $user->customer->type->name,
					'codigosap' => $user->customer->codigosap,
					'connected' => ($status['connected']) ? 'Conectado' : 'Desconectado',
					'banned'    => ($status['banned']) ? 'Sí' : 'No',
					'updated_at' => date('d/m/Y - H:i', $user->updated_at),
				);
			}
		}

		# SE ALMACENA LA INFORMACION PARA LA VISTA
		$data['users']      = $users_info;
		$data['search']     = str_replace('%', ' ', $search);
		$data['pagination'] = $pagination->render();

		# SE CARGA LA VISTA
		$this->template->title   = 'Usuarios';
		$this->template->content = View::forge('admin/usuarios/index', $data, false);
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
				Response::redirect('admin/usuarios/index/'.$search);
			}
			else
			{
				# SE REDIRECCIONA AL USUARIO
				Response::redirect('admin/usuarios');
			}
		}
		else
		{
			# SE REDIRECCIONA AL USUARIO
			Response::redirect('admin/usuarios');
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
	public function action_info($user_id = 0)
	{
		# SI NO SE RECIBE UN ID O NO ES UN NUMERO
		if($user_id == 0 || !is_numeric($user_id))
		{
			# SE REDIRECCIONA AL USUARIO
			Response::redirect('admin/usuarios');
		}

		# SE INICIALIZAN LAS VARIABLES
		$data           = array();
		$addresses_info = array();

		# SE BUSCA LA INFORMACION A TRAVES DEL MODELO
		$user = Model_User::query()
		->where('id', $user_id)
		->get_one();

		# SI SE OBTIENE INFORMACION
		if(!empty($user))
		{
			# SI EXISTEN DIRECCIONES
			if(!empty($user->customer->addresses))
			{
				# SE RECORRE ELEMENTO POR ELEMENTO
				foreach($user->customer->addresses as $address)
				{
					# SE ALMACENA LA INFORMACION
					$addresses_info[] = array(
						'id'      => $address->id,
						'street'  => $address->street.' '.$address->number,
						'colony'  => $address->colony,
						'zipcode' => $address->zipcode,
						'city'    => $address->city,
						'state'   => $address->state->name
					);
				}
			}

			# SE DESERIALIZAN LOS CAMPOS EXTRAS
			$status = unserialize($user->profile_fields);

			# SE ALMACENA LA INFORMACION PARA LA VISTA
			$data['username']  = $user->username;
			$data['email']     = $user->email;
			$data['name']      = $user->customer->name;
			$data['last_name'] = $user->customer->last_name;
			$data['phone']     = ($user->customer->phone != '') ? $user->customer->phone : 'N/A';
			$data['type']      = $user->customer->type->name;
			$data['codigosap'] = $user->customer->codigosap;
			$data['addresses'] = $addresses_info;
			$data['banned']    = ($status['banned']) ? 'Sí' : 'No';
		}
		else
		{
			# SE REDIRECCIONA AL USUARIO
			Response::redirect('admin/usuarios');
		}

		# SE ALMACENA LA INFORMACION PARA LA VISTA
		$data['id'] = $user_id;

		# SE CARGA LA VISTA
		$this->template->title   = 'Información del usuario';
		$this->template->content = View::forge('admin/usuarios/info', $data);
	}


	/**
	 * EDITAR
	 *
	 * PERMITE EDITAR UN REGISTRO DE LA BASE DE DATOS
	 *
	 * @access  public
	 * @return  Void
	 */
	public function action_editar($user_id = 0)
	{
		# SI NO SE RECIBE UN ID O NO ES UN NUMERO
		if($user_id == 0 || !is_numeric($user_id))
		{
			# SE REDIRECCIONA AL USUARIO
			Response::redirect('admin/usuarios');
		}

		# SE INICIALIZAN LAS VARIABLES
		$data    = array();
		$classes = array();
		$fields  = array('email', 'name', 'banned','last_name', 'phone', 'codigosap','type');

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
		$user = Model_User::query()
		->related('customer')
		->where('id', $user_id)
		->get_one();

		# SI SE OBTIENE INFORMACION
		if(!empty($user))
		{
			# SE DESERIALIZAN LOS CAMPOS EXTRAS
			$status = unserialize($user->profile_fields);

			# SE ALMACENA LA INFORMACION PARA LA VISTA
			$data['username']  = $user->username;
			$data['email']     = $user->email;
			$data['name']      = $user->customer->name;
			$data['last_name'] = $user->customer->last_name;
			$data['phone']     = ($user->customer->phone != '') ? $user->customer->phone : 'N/A';
			$data['type']      = $user->customer->type_id;
			$data['codigosap'] = $user->customer->codigosap;
			$data['banned']    = $status['banned'];
		}
		else
		{
			# SE REDIRECCIONA AL USUARIO
			Response::redirect('admin/usuarios');
		}

		# SI SE UTILIZO EL METODO POST
		if(Input::method() == 'POST')
		{
			# SE CREA LA VALIDACION DE LOS CAMPOS
			$val = Validation::forge('user');
			$val->add_callable('Rules');
			$val->add_field('email', 'email', 'required|min_length[1]|valid_email');
			$val->add_field('name', 'nombre', 'required|min_length[1]|max_length[255]');
            $val->add_field('last_name', 'apellidos', 'required|min_length[1]|max_length[255]');
            $val->add_field('phone', 'teléfono', 'required|min_length[1]|max_length[255]');
            $val->add_field('type', 'tipo de cliente', 'required|valid_string[numeric]|numeric_min[1]');
			$val->add_field('codigosap', 'codigo sap', 'min_length[1]|max_length[7]');
			$val->add_field('banned', 'baneado', 'required|valid_string[numeric]|numeric_min[0]|numeric_max[1]');

			# SI NO HAY NINGUN PROBLEMA CON LA VALIDACION
			if($val->run())
			{
				try
				{
					# SE ESTEBLECE LA NUEVA INFORMACION
					$data_to_update = array(
						'email' => $val->validated('email'),
						'banned'=> ($val->validated('banned')) ? true : false
					);

					# SE ACTUALIZA LA INFORMACION DEL USUARIO EN LA BASE DE DATOS
					$user_auth = Auth::instance()->update_user($data_to_update, $user->username);

					# SI SE ALMACENO EL REGISTRO EN LA BASE DE DATOS
					if($user_auth)
					{
						# SE BUSCA LA INFORMACION A TRAVES DEL MODELO
						$customer = Model_Customer::query()
						->where('user_id', $user_id)
						->get_one();

						# SI SE OBTIENE INFORMACION
						if(!empty($customer))
						{
							# SE ESTEBLECE LA NUEVA INFORMACION
							$customer->name      = $val->validated('name');
							$customer->last_name = $val->validated('last_name');
							$customer->phone     = $val->validated('phone');
							$customer->type_id   = $val->validated('type');
							$customer->codigosap = $val->validated('codigosap');
							$customer->banned    = $val->validated('banned');

							# SI SE ALMACENO EL REGISTRO EN LA BASE DE DATOS
							if($customer->save())
							{
								# SE ESTABLECE EL MENSAJE DE EXITO
								Session::set_flash('success', 'Se actualizó la información del usuario correctamente.');

								# SE REDIRECCIONA AL USUARIO
								Response::redirect('admin/usuarios/editar/'.$user_id);
							}
						}
						else
						{
							# SE ESTABLECE EL MENSAJE DE ERROR
							Session::set_flash('error', 'No se encontró la información del cliente asociada al usuario.');

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
				}
				catch(\SimpleUserUpdateException $e)
				{
					# SE ESTABLECE EL MENSAJE DE ERROR
					Session::set_flash('error', 'El correo electrónico <b>'.$val->validated('email').'</b> ya está asociado a otra cuenta, por favor escribe una dirección de correo electrónico diferente.');

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

		# SE ALMACENA LA INFORMACION PARA LA VISTA
		$data['id']        = $user_id;
		$data['classes']   = $classes;
		$data['type_opts'] = Model_Customers_Type::get_for_input();

		# SE CARGA LA VISTA
		$this->template->title   = 'Editar usuario';
		$this->template->content = View::forge('admin/usuarios/editar', $data);
	}


	/**
	 * EDITAR_DIRECCION
	 *
	 * PERMITE EDITAR UN REGISTRO DE LA BASE DE DATOS
	 *
	 * @access  public
	 * @return  Void
	 */
	public function action_editar_direccion($user_id = 0, $address_id = 0)
	{
		# SI NO SE RECIBE UN ID O NO ES UN NUMERO
		if($user_id == 0 || !is_numeric($user_id) || $address_id == 0 || !is_numeric($address_id))
		{
			# SE REDIRECCIONA AL USUARIO
			Response::redirect('admin/usuarios');
		}

		# SE INICIALIZAN LAS VARIABLES
		$data    = array();
		$classes = array();
		$fields  = array('name', 'last_name', 'phone', 'street', 'number', 'banned','codigosap','internal_number', 'colony', 'zipcode', 'city', 'state', 'details');

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
		$user = Model_User::query()
		->where('id', $user_id)
		->get_one();

		# SI SE OBTIENE INFORMACION
		if(!empty($user))
		{
			# SE BUSCA LA INFORMACION A TRAVES DEL MODELO
			$address = Model_Customers_Address::query()
			->where('customer_id', $user->customer->id)
			->where('id', $address_id)
			->get_one();

			# SI SE OBTIENE INFORMACION
			if(!empty($address))
			{
				# SE DESERIALIZAN LOS CAMPOS EXTRAS
				$status = unserialize($user->profile_fields);

				# SE ALMACENA LA INFORMACION PARA LA VISTA
				$data['username']        = $user->username;
				$data['state']           = $address->state_id;
				$data['name']            = $address->name;
				$data['last_name']       = $address->last_name;
				$data['phone']           = $address->phone;
				$data['street']          = $address->street;
				$data['number']          = $address->number;
				$data['internal_number'] = $address->internal_number;
				$data['colony']          = $address->colony;
				$data['zipcode']         = $address->zipcode;
				$data['city']            = $address->city;
				$data['details']         = $address->details;
			}
			else
			{
				# SE REDIRECCIONA AL USUARIO
				Response::redirect('admin/usuarios');
			}
		}
		else
		{
			# SE REDIRECCIONA AL USUARIO
			Response::redirect('admin/usuarios');
		}

		# SI SE UTILIZO EL METODO POST
		if(Input::method() == 'POST')
		{
			# SE CREA LA VALIDACION DE LOS CAMPOS
			$val = Validation::forge('address');
			$val->add_callable('Rules');
			$val->add_field('name', 'nombre', 'required|min_length[1]|max_length[255]');
			$val->add_field('last_name', 'apellidos', 'required|min_length[1]|max_length[255]');
			$val->add_field('phone', 'teléfono', 'required|min_length[1]|max_length[255]');
			$val->add_field('street', 'calle', 'required|min_length[1]|max_length[255]');
			$val->add_field('number', '# exterior', 'required|min_length[1]|max_length[255]');
			$val->add_field('internal_number', '# interior', 'min_length[1]|max_length[255]');
			$val->add_field('colony', 'colonia', 'required|min_length[1]|max_length[255]');
			$val->add_field('zipcode', 'código postal', 'required|min_length[1]|max_length[255]');
			$val->add_field('city', 'ciudad', 'required|min_length[1]|max_length[255]');
			$val->add_field('state', 'estado', 'required|valid_string[numeric]|numeric_between[1,32]');
			$val->add_field('details', 'información adicional', 'min_length[1]|max_length[255]');

			# SI NO HAY NINGUN PROBLEMA CON LA VALIDACION
			if($val->run())
			{
				# SE ESTEBLECE LA NUEVA INFORMACION
				$address->state_id        = $val->validated('state');
				$address->name            = $val->validated('name');
				$address->last_name       = $val->validated('last_name');
				$address->phone           = $val->validated('phone');
				$address->street          = $val->validated('street');
				$address->number          = $val->validated('number');
				$address->internal_number = $val->validated('internal_number');
				$address->colony          = $val->validated('colony');
				$address->zipcode         = $val->validated('zipcode');
				$address->city            = $val->validated('city');
				$address->details         = $val->validated('details');

				# SI SE ALMACENO EL REGISTRO EN LA BASE DE DATOS
				if($address->save())
				{
					# SE ESTABLECE EL MENSAJE DE EXITO
					Session::set_flash('success', 'Se actualizó la información de la dirección correctamente.');

					# SE REDIRECCIONA AL USUARIO
					Response::redirect('admin/usuarios/editar_direccion/'.$user_id.'/'.$address_id);
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

		# SE ALMACENA LA INFORMACION PARA LA VISTA
		$data['id']         = $address_id;
		$data['user_id']    = $user_id;
		$data['state_opts'] = Model_State::get_for_input();
		$data['classes']    = $classes;

		# SE CARGA LA VISTA
		$this->template->title   = 'Editar dirección';
		$this->template->content = View::forge('admin/usuarios/editar_direccion', $data);
	}
}
