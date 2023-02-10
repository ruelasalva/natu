<?php

/**
 * CONTROLADOR ADMIN_BANERS
 *
 * @package  app
 * @extends  Controller_Admin
 */
class Controller_Admin_Baners extends Controller_Admin
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
	public function action_index()
	{
		# SE INICIALIZAN LAS VARIABLES
		$data        = array();
		$baners_info = array();
		$per_page    = 100;

		# SE BUSCA LA INFORMACION A TRAVES DEL MODELO
		$baners = Model_Baner::query();

		# SE ESTABLECE LA CONFIGURACION DE LA PAGINACION
		$config = array(
			'name'           => 'admin',
			'pagination_url' => Uri::current(),
			'total_items'    => $baners->count(),
			'per_page'       => $per_page,
			'uri_segment'    => 'pagina',
		);

		# SE CREA LA INSTANCIA DE LA PAGINACION
		$pagination = Pagination::forge('baners', $config);

		# SE EJECUTA EL QUERY
		$baners = $baners->order_by('order', 'asc')
		->rows_limit($pagination->per_page)
		->rows_offset($pagination->offset)
		->get();

		# SI SE OBTIENE INFORMACION
		if(!empty($baners))
		{
			# SE RECORRE ELEMENTO POR ELEMENTO
			foreach($baners as $baner)
			{
				# SE ALMACENA LA INFORMACION
				$baners_info[] = array(
					'id'    => $baner->id,
					'image' => $baner->image,
					'url'   => $baner->url,
					'order' => $baner->order
				);
			}
		}

		# SE ALMACENA LA INFORMACION PARA LA VISTA
		$data['baners']     = $baners_info;
		$data['pagination'] = $pagination->render();

		# SE CARGA LA VISTA
		$this->template->title   = 'baners';
		$this->template->content = View::forge('admin/baners/index', $data, false);
	}


	/**
	 * AGREGAR
	 *
	 * PERMITE AGREGAR UN REGISTRO A LA BASE DE DATOS
	 *
	 * @access  public
	 * @return  Void
	 */
	public function action_agregar()
	{
		# SE INICIALIZAN LAS VARIABLES
		$data    = array();
		$classes = array();
		$fields  = array('image', 'url');

		# SE RECORRE CAMPO POR CAMPO
		foreach($fields as $field)
		{
			# SE CREAN LAS CLASES DEL CAMPO
			$classes[$field] = array (
				'form-group'   => null,
				'form-control' => null,
			);
		}

		# SI SE UTILIZA EL METODO POST
		if(Input::method() == 'POST')
		{
			# SE CREA LA VALIDACION DE LOS CAMPOS
			$val = Validation::forge('baner');
			$val->add_callable('Rules');
			$val->add_field('image', 'imagen', 'required|min_length[1]');
			$val->add_field('url', 'url', 'required|valid_url');

			# SI NO HAY NINGUN PROBLEMA CON LA VALIDACION
			if($val->run())
			{
				# SE OBTIENE EL ORDEN MAXIMO
				$order = Model_Baner::query()
				->max('order');

				# SE CREA EL MODELO CON LA INFORMACION
				$baner = new Model_Baner(array(
					'image' => $val->validated('image'),
					'url'   => $val->validated('url'),
					'order' => $order + 1
				));

				# SI SE ALMACENA EL REGISTRO EN LA BASE DE DATOS
				if($baner->save())
				{
					# SE ESTABLECE EL MENSAJE DE EXITO
					Session::set_flash('success', 'Se agregó el baner correctamente.');

					# SE REDIRECCIONA AL USUARIO
					Response::redirect('admin/baners');
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
		$data['classes'] = $classes;

		# SE CARGA LA VISTA
		$this->template->title   = 'Agregar baner';
		$this->template->content = View::forge('admin/baners/agregar', $data);
	}


	/**
	 * INFO
	 *
	 * MUESTRA LA INFORMACION DE UN REGISTRO DE LA BASE DE DATOS
	 *
	 * @access  public
	 * @return  Void
	 */
	public function action_info($baner_id = 0)
	{
		# SI NO SE RECIBE UN ID O NO ES UN NUMERO
		if($baner_id == 0 || !is_numeric($baner_id))
		{
			# SE REDIRECCIONA AL USUARIO
			Response::redirect('admin/baners');
		}

		# SE INICIALIZAN LAS VARIABLES
		$data       = array();
		$users_info = array();

		# SE BUSCA LA INFORMACION A TRAVES DEL MODELO
		$baner = Model_Baner::query()
		->where('id', $baner_id)
		->get_one();

		# SI SE OBTIENE INFORMACION
		if(!empty($baner))
		{
			# SE ALMACENA LA INFORMACION PARA LA VISTA
			$data['image'] = $baner->image;
			$data['url']   = $baner->url;
		}
		else
		{
			# SE REDIRECCIONA AL USUARIO
			Response::redirect('admin/baners');
		}

		# SE ALMACENA LA INFORMACION PARA LA VISTA
		$data['id'] = $baner_id;

		# SE CARGA LA VISTA
		$this->template->title   = 'Información del baner';
		$this->template->content = View::forge('admin/baners/info', $data);
	}


	/**
	 * EDITAR
	 *
	 * PERMITE EDITAR UN REGISTRO DE LA BASE DE DATOS
	 *
	 * @access  public
	 * @return  Void
	 */
	public function action_editar($baner_id = 0)
	{
		# SI NO SE RECIBE UN ID O NO ES UN NUMERO
		if($baner_id == 0 || !is_numeric($baner_id))
		{
			# SE REDIRECCIONA AL USUARIO
			Response::redirect('admin/baners');
		}

		# SE INICIALIZAN LAS VARIABLES
		$data    = array();
		$classes = array();
		$fields  = array('image', 'url');

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
		$baner = Model_Baner::query()
		->where('id', $baner_id)
		->get_one();

		# SI SE OBTIENE INFORMACION
		if(!empty($baner))
		{
			# SE ALMACENA LA INFORMACION PARA LA VISTA
			$data['image'] = $baner->image;
			$data['url']   = $baner->url;
		}
		else
		{
			# SE REDIRECCIONA AL USUARIO
			Response::redirect('admin/baners');
		}

		# SI SE UTILIZO EL METODO POST
		if(Input::method() == 'POST')
		{
			# SE CREA LA VALIDACION DE LOS CAMPOS
			$val = Validation::forge('baner');
			$val->add_callable('Rules');
			$val->add_field('image', 'imagen', 'required|min_length[1]');
			$val->add_field('url', 'url', 'required|valid_url');

			# SI NO HAY NINGUN PROBLEMA CON LA VALIDACION
			if($val->run())
			{
				# SE ESTEBLECE LA NUEVA INFORMACION
				$baner->image = $val->validated('image');
				$baner->url   = $val->validated('url');

				# SI SE ALMACENO EL REGISTRO EN LA BASE DE DATOS
				if($baner->save())
				{
					# SE ESTABLECE EL MENSAJE DE EXITO
					Session::set_flash('success', 'Se actualizó la información del baner correctamente.');

					# SE REDIRECCIONA AL USUARIO
					Response::redirect('admin/baners/editar/'.$baner_id);
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
		$data['id']      = $baner_id;
		$data['classes'] = $classes;

		# SE CARGA LA VISTA
		$this->template->title   = 'Editar baner';
		$this->template->content = View::forge('admin/baners/editar', $data);
	}


	/**
	 * ELIMINAR
	 *
	 * ELIMINA UN REGISTRO DE LA BASE DE DATOS
	 *
	 * @access  public
	 * @return  Void
	 */
	public function action_eliminar($baner_id = 0)
	{
		# SI NO SE RECIBE UN ID O NO ES UN NUMERO
		if($baner_id == 0 || !is_numeric($baner_id))
		{
			# SE REDIRECCIONA AL USUARIO
			Response::redirect('admin/baners');
		}

		# SE BUSCA LA INFORMACION A TRAVES DEL MODELO
		$baner = Model_Baner::query()
		->where('id', $baner_id)
		->get_one();

		# SI SE OBTIENE INFORMACION
		if(!empty($baner))
		{
			# SI EL ARCHIVO EXISTE
			if(file_exists(DOCROOT.'assets/uploads/'.$baner->image))
			{
				# SE ELIMINA EL ARCHIVO
				File::delete(DOCROOT.'assets/uploads/'.$baner->image);
			}

			# SI SE ELIMINO EL REGISTRO EN LA BASE DE DATOS
			if($baner->delete())
			{
				# SE ESTABLECE EL MENSAJE DE EXITO
				Session::set_flash('success', 'Se eliminó el baner correctamente.');
			}
		}

		# SE REDIRECCIONA AL USUARIO
		Response::redirect('admin/baners');
	}
}
