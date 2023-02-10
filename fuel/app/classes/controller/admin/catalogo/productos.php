<?php

/**
 * CONTROLADOR ADMIN_CATALOGO_PRODUCTOS
 *
 * @package  app
 * @extends  Controller_Admin
 */
class Controller_Admin_Catalogo_Productos extends Controller_Admin
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
		$data          = array();
		$products_info = array();
		$per_page      = 100;

		# SE BUSCA LA INFORMACION A TRAVES DEL MODELO
		$products = Model_Product::query()
		->where('deleted', 0);

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
			$products = $products->where(DB::expr("CONCAT(`t0`.`name`,`code`)"), 'like', '%'.$search.'%');
		}

		# SE ESTABLECE LA CONFIGURACION DE LA PAGINACION
		$config = array(
			'name'           => 'admin',
			'pagination_url' => Uri::current(),
			'total_items'    => $products->count(),
			'per_page'       => $per_page,
			'uri_segment'    => 'pagina',
		);

		# SE CREA LA INSTANCIA DE LA PAGINACION
		$pagination = Pagination::forge('products', $config);

		# SE EJECUTA EL QUERY
		$products = $products->order_by('id', 'desc')
		->rows_limit($pagination->per_page)
		->rows_offset($pagination->offset)
		->get();

		# SI SE OBTIENE INFORMACION
		if(!empty($products))
		{
			# SE RECORRE ELEMENTO POR ELEMENTO
			foreach($products as $product)
			{
				# SE ALMACENA LA INFORMACION
				$products_info[] = array(
					'id'            => $product->id,
					'code'          => $product->code,
					'codebar'       => $product->codebar,
					'image'         => $product->image,
					'slug'         	=> $product->slug,
					'name'          => Str::truncate($product->name, '60', '...'),
					'name_complete' => $product->name,
					'brand'    	    => $product->brand->name,
					'category'      => $product->category->name,
					'available'     => $product->available,
					'weight'        => $product->weight,
					'price_1'       => '$'.number_format(Model_Products_Price::get_price($product->id, 1), '2', '.', ','),
					'price_2'       => '$'.number_format(Model_Products_Price::get_price($product->id, 2), '2', '.', ','),
					'price_3'       => '$'.number_format(Model_Products_Price::get_price($product->id, 3), '2', '.', ','),
					'status'        => $product->status,
					'status_index'  => $product->status_index
				);
			}
		}

		# SE ALMACENA LA INFORMACION PARA LA VISTA
		$data['products']   = $products_info;
		$data['search']     = str_replace('%', ' ', $search);
		$data['pagination'] = $pagination->render();

		# SE CARGA LA VISTA
		$this->template->title   = 'Productos';
		$this->template->content = View::forge('admin/catalogo/productos/index', $data, false);
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
				Response::redirect('admin/catalogo/productos/index/'.$search);
			}
			else
			{
				# SE REDIRECCIONA AL USUARIO
				Response::redirect('admin/catalogo/productos');
			}
		}
		else
		{
			# SE REDIRECCIONA AL USUARIO
			Response::redirect('admin/catalogo/productos');
		}
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
		$data             = array();
		$classes          = array();
		$fields           = array('name', 'category', 'subcategory','brand', 'image', 'description', 'code','codebar', 'original_price', 'price_1', 'price_2', 'price_3', 'available','weight');
		$category_opts    = array();
		$subcategory_opts = array();
		$brand_opts       = array();

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
			$val = Validation::forge('product');
			$val->add_callable('Rules');
			$val->add_field('category', 'categoría', 'required|valid_string[numeric]|numeric_min[1]');
			$val->add_field('subcategory', 'grupo', 'required|valid_string[numeric]|numeric_min[1]');
			$val->add_field('brand', 'marca', 'required|valid_string[numeric]|numeric_min[1]');
			$val->add_field('name', 'nombre', 'required|min_length[1]|max_length[255]');
			$val->add_field('code', 'código', 'required|min_length[1]|max_length[255]');
			$val->add_field('codebar', 'código de barras', 'required|min_length[1]|max_length[255]');
			$val->add_field('image', 'imagen', 'required|min_length[1]');
			$val->add_field('description', 'descripción', 'required|min_length[1]');
			$val->add_field('original_price', 'precio original', 'required|float');
			$val->add_field('price_1', 'precio (normal)', 'required|float');
			$val->add_field('price_2', 'precio (mayorista #1)', 'required|float');
			$val->add_field('price_3', 'precio (mayorista #3)', 'required|float');
			$val->add_field('available', 'cantidad disponible', 'required|valid_string[numeric]|numeric_min[0]');
			$val->add_field('weight', 'peso producto', 'required|float');

			# SI NO HAY NINGUN PROBLEMA CON LA VALIDACION
			if($val->run())
			{
				# SE INICIALIZA EL CONTADOR
				$count = 1;

				# SE GENERA EL SLUG A PARTIR DEL NOMBRE
				$original_slug = Inflector::friendly_title($val->validated('name'), '-', true);
				$slug_temp     = Inflector::friendly_title($val->validated('name'), '-', true);

				# HACER HASTA NO ENCONTRAR EL SLUG REPETIDO
				do{
					# SE VERIFICA SI EXISTE EL SLUG EN LA BASE DE DATOS
					$exist = Model_Product::query()
					->where('slug', $slug_temp)
					->get();

					# SI EL SLUG EXISTE
					if(!empty($exist))
					{
						# SE LE AGREGA EL VALOR DEL CONTADOR AL FINAL DE SLUG
						$slug_temp = $original_slug.'-'.$count;

						# SE INCREMENTA EL CONTADOR
						$count++;
					}
				}while(!empty($exist));
				# FIN DE LA VERIFICACION DEL SLUG

				# SE CREA EL MODELO CON LA INFORMACION
				$product = new Model_Product(array(
					'category_id'    => $val->validated('category'),
					'subcategory_id' => $val->validated('subcategory'),
					'brand_id'       => $val->validated('brand'),
					'slug'           => $slug_temp,
					'name'           => $val->validated('name'),
					'code'           => $val->validated('code'),
					'codebar'        => $val->validated('codebar'),
					'image'          => $val->validated('image'),
					'description'    => $val->validated('description'),
					'original_price' => $val->validated('original_price'),
					'available'      => $val->validated('available'),
					'weight'         => $val->validated('weight'),
					'status'         => 1,
					'status_index'   => 0,
					'deleted'        => 0
				));

				# SI SE ALMACENA EL REGISTRO EN LA BASE DE DATOS
				if($product->save())
				{
					# SE CREA EL MODELO CON LA INFORMACION
					$product_price_1 = new Model_Products_Price(array(
						'type_id'    => 1,
						'product_id' => $product->id,
						'price'      => $val->validated('price_1')
					));

					# SE ALMACENA EL REGISTRO EN LA BASE DE DATOS
					$product_price_1->save();

					# SE CREA EL MODELO CON LA INFORMACION
					$product_price_2 = new Model_Products_Price(array(
						'type_id'    => 2,
						'product_id' => $product->id,
						'price'      => $val->validated('price_2')
					));

					# SE ALMACENA EL REGISTRO EN LA BASE DE DATOS
					$product_price_2->save();

					# SE CREA EL MODELO CON LA INFORMACION
					$product_price_3 = new Model_Products_Price(array(
						'type_id'    => 3,
						'product_id' => $product->id,
						'price'      => $val->validated('price_3')
					));

					# SE ALMACENA EL REGISTRO EN LA BASE DE DATOS
					$product_price_3->save();

					# SE ESTABLECE EL MENSAJE DE EXITO
					Session::set_flash('success', 'Se agregó el producto <b>'.$val->validated('name').'</b> correctamente.');

					# SE REDIRECCIONA AL USUARIO
					Response::redirect('admin/catalogo/productos');
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

				# SI LA DIVISA ES PESOS
				if(Input::post('badge') == 0)
				{
					# SE ESTABLECE EL VALOR DE LAS CLASES
					$classes['exchange_rate']['form-group']   = '';
					$classes['exchange_rate']['form-control'] = '';
				}
			}
		}

		# SE ESTBLECE LA OPCION POR DEFAULT
		$category_opts += array('0' => 'Selecciona una opción');

		# SE BUSCA LA INFORMACION A TRAVES DEL MODELO
		$categories = Model_Category::query()
		->where('deleted', 0)
		->order_by('name', 'asc')
		->get();

		# SI SE OBTIENE INFORMACION
		if(!empty($categories))
		{
			# SE RECORRE ELEMENTO POR ELEMENTO
			foreach($categories as $category)
			{
				# SE ALMACENA LA OPCION
				$category_opts += array($category->id => $category->name);
			}
		}

		# SE ESTBLECE LA OPCION POR DEFAULT
		$subcategory_opts += array('0' => 'Selecciona una opción');

		# SE BUSCA LA INFORMACION A TRAVES DEL MODELO
		$subcategories = Model_Subcategory::query()
		->where('deleted', 0)
		->order_by('name', 'asc')
		->get();

		# SI SE OBTIENE INFORMACION
		if(!empty($subcategories))
		{
			# SE RECORRE ELEMENTO POR ELEMENTO
			foreach($subcategories as $subcategory)
			{
				# SE ALMACENA LA OPCION
				$subcategory_opts += array($subcategory->id => $subcategory->name);
			}
		}

		# SE ESTBLECE LA OPCION POR DEFAULT
		$brand_opts += array('0' => 'Selecciona una opción');

		# SE BUSCA LA INFORMACION A TRAVES DEL MODELO
		$brands = Model_Brand::query()
		->where('deleted', 0)
		->order_by('name', 'asc')
		->get();

		# SI SE OBTIENE INFORMACION
		if(!empty($brands))
		{
			# SE RECORRE ELEMENTO POR ELEMENTO
			foreach($brands as $brand)
			{
				# SE ALMACENA LA OPCION
				$brand_opts += array($brand->id => $brand->name);
			}
		}

		# SE ALMACENA LA INFORMACION PARA LA VISTA
		$data['classes']          = $classes;
		$data['category_opts']    = $category_opts;
		$data['subcategory_opts'] = $subcategory_opts;
		$data['brand_opts']       = $brand_opts;

		# SE CARGA LA VISTA
		$this->template->title   = 'Agregar producto';
		$this->template->content = View::forge('admin/catalogo/productos/agregar', $data);
	}


	/**
	 * INFO
	 *
	 * MUESTRA LA INFORMACION DE UN REGISTRO DE LA BASE DE DATOS
	 *
	 * @access  public
	 * @return  Void
	 */
	public function action_info($product_id = 0)
	{
		# SI NO SE RECIBE UN ID O NO ES UN NUMERO
		if($product_id == 0 || !is_numeric($product_id))
		{
			# SE REDIRECCIONA AL USUARIO
			Response::redirect('admin/catalogo/productos');
		}

		# SE INICIALIZAN LAS VARIABLES
		$data           = array();
		$filter         = '';
		$galleries_info = array();

		# SE BUSCA LA INFORMACION A TRAVES DEL MODELO
		$product = Model_Product::query()
		->where('id', $product_id)
		->where('deleted', 0)
		->get_one();

		# SI SE OBTIENE INFORMACION
		if(!empty($product))
		{
			# SI EXISTE LA RELACION
			if(!empty($product->galleries))
			{
				# SE RECORRE ELEMENTO POR ELEMENTO
				foreach($product->galleries as $image)
				{
					# SE ALMACENA LA INFORMACION
					$galleries_info[] = array(
						'id'    => $image->id,
						'image' => $image->image,
						'order' => $image->order
					);
				}
			}

			# SE ALMACENA LA INFORMACION PARA LA VISTA
			$data['name']           = $product->name;
			$data['category']       = $product->category->name;
			$data['subcategory']    = $product->subcategory->name;
			$data['brand']          = $product->brand->name;
			$data['code']           = $product->code;
			$data['codebar']        = $product->codebar;
			$data['image']          = $product->image;
			$data['description']    = nl2br($product->description);
			$data['original_price'] = '$'.number_format($product->original_price, '2', '.', ',');
			$data['price_1']        = '$'.number_format(Model_Products_Price::get_price($product->id, 1), '2', '.', ',');
			$data['price_2']        = '$'.number_format(Model_Products_Price::get_price($product->id, 2), '2', '.', ',');
			$data['price_3']        = '$'.number_format(Model_Products_Price::get_price($product->id, 3), '2', '.', ',');
			$data['available']      = $product->available;
			$data['weight']         = $product->weight;
			$data['galleries']      = $galleries_info;
		}
		else
		{
			# SE REDIRECCIONA AL USUARIO
			Response::redirect('admin/catalogo/productos');
		}

		# SE ALMACENA LA INFORMACION PARA LA VISTA
		$data['id'] = $product_id;

		# SE CARGA LA VISTA
		$this->template->title   = 'Información de la producto';
		$this->template->content = View::forge('admin/catalogo/productos/info', $data, false);
	}


	/**
	 * EDITAR
	 *
	 * PERMITE EDITAR UN REGISTRO DE LA BASE DE DATOS
	 *
	 * @access  public
	 * @return  Void
	 */
	public function action_editar($product_id = 0)
	{
		# SI NO SE RECIBE UN ID O NO ES UN NUMERO
		if($product_id == 0 || !is_numeric($product_id))
		{
			# SE REDIRECCIONA AL USUARIO
			Response::redirect('admin/catalogo/productos');
		}

		# SE INICIALIZAN LAS VARIABLES
		$data             = array();
		$classes          = array();
		$fields           = array('name', 'category', 'subcategory','brand', 'image', 'description', 'code', 'codebar','original_price', 'price_1', 'price_2', 'price_3', 'available','weight');
		$category_opts 	  = array();
		$subcategory_opts = array();
		$brand_opts       = array();

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
		$product = Model_Product::query()
		->where('id', $product_id)
		->where('deleted', 0)
		->get_one();

		# SI SE OBTIENE INFORMACION
		if(!empty($product))
		{
			# SE ALMACENA LA INFORMACION PARA LA VISTA
			$data['name']           = $product->name;
			$data['category']       = $product->category_id;
			$data['subcategory']    = $product->subcategory_id;
			$data['brand']          = $product->brand_id;
			$data['code']           = $product->code;
			$data['codebar']        = $product->codebar;
			$data['image']          = $product->image;
			$data['description']    = $product->description;
			$data['original_price'] = $product->original_price;
			$data['price_1']        = Model_Products_Price::get_price($product->id, 1);
			$data['price_2']        = Model_Products_Price::get_price($product->id, 2);
			$data['price_3']        = Model_Products_Price::get_price($product->id, 3);
			$data['available']      = $product->available;
			$data['weight']         = $product->weight;
		}
		else
		{
			# SE REDIRECCIONA AL USUARIO
			Response::redirect('admin/catalogo/productos');
		}

		# SI SE UTILIZO EL METODO POST
		if(Input::method() == 'POST')
		{
			# SE CREA LA VALIDACION DE LOS CAMPOS
			$val = Validation::forge('product');
			$val->add_callable('Rules');
			$val->add_field('category', 'categoría', 'required|valid_string[numeric]|numeric_min[1]');
			$val->add_field('subcategory', 'grupo', 'required|valid_string[numeric]|numeric_min[1]');
			$val->add_field('brand', 'marca', 'required|valid_string[numeric]|numeric_min[1]');
			$val->add_field('name', 'nombre', 'required|min_length[1]|max_length[255]');
			$val->add_field('code', 'código', 'required|min_length[1]|max_length[255]');
			$val->add_field('codebar', 'código de barras', 'required|min_length[1]|max_length[255]');
			$val->add_field('image', 'imagen', 'required|min_length[1]');
			$val->add_field('description', 'descripción', 'required|min_length[1]');
			$val->add_field('original_price', 'precio original', 'required|float');
			$val->add_field('price_1', 'precio (normal)', 'required|float');
			$val->add_field('price_2', 'precio (mayorista #1)', 'required|float');
			$val->add_field('price_3', 'precio (mayorista #2)', 'required|float');
			$val->add_field('available', 'cantidad disponible', 'required|valid_string[numeric]|numeric_min[0]');
			$val->add_field('weight', 'peso producto', 'required|float');

			# SI NO HAY NINGUN PROBLEMA CON LA VALIDACION
			if($val->run())
			{
				# SE INICIALIZA EL CONTADOR
				$count = 1;

				# SE GENERA EL SLUG A PARTIR DEL NOMBRE
				$original_slug = Inflector::friendly_title($val->validated('name'), '-', true);
				$slug_temp     = Inflector::friendly_title($val->validated('name'), '-', true);

				# HACER HASTA NO ENCONTRAR EL SLUG REPETIDO
				do{
					# SE VERIFICA SI EXISTE EL SLUG EN LA BASE DE DATOS
					$exist = Model_Product::query()
					->where('slug', $slug_temp)
					->get_one();

					# SI EL SLUG EXISTE
					if(!empty($exist))
					{
						# SI EL ID DEL SLUG ES DIFERENTE AL ID ORIGINAL
						if($exist->id != $product->id)
						{
							# SE LE AGREGA EL VALOR DEL CONTADOR AL FINAL DE SLUG
							$slug_temp = $original_slug.'-'.$count;

							# SE INCREMENTA EL CONTADOR
							$count++;
						}
						else
						{
							# SE ROMPE EL CICLO DEL DO-WHILE
							break;
						}
					}
				}while(!empty($exist));
				# FIN DE LA VERIFICACION DEL SLUG

				# SE ESTEBLECE LA NUEVA INFORMACION
				$product->category_id    = $val->validated('category');
				$product->subcategory_id = $val->validated('subcategory');
				$product->brand_id       = $val->validated('brand');
				$product->slug           = $slug_temp;
				$product->name           = $val->validated('name');
				$product->code           = $val->validated('code');
				$product->codebar        = $val->validated('codebar');
				$product->image          = $val->validated('image');
				$product->description    = $val->validated('description');
				$product->original_price = $val->validated('original_price');
				$product->available      = $val->validated('available');
				$product->weight         = $val->validated('weight');

				# SI SE ALMACENO EL REGISTRO EN LA BASE DE DATOS
				if($product->save())
				{
					# SE BUSCA LA INFORMACION A TRAVES DEL MODELO
					$product_price_1 = Model_Products_Price::query()
					->where('product_id', $product_id)
					->where('type_id', 1)
					->get_one();

					# SE ESTEBLECE LA NUEVA INFORMACION
					$product_price_1->price = $val->validated('price_1');

					# SE ACTUALIZA EL REGISTRO EN LA BASE DE DATOS
					$product_price_1->save();

					# SE BUSCA LA INFORMACION A TRAVES DEL MODELO
					$product_price_2 = Model_Products_Price::query()
					->where('product_id', $product_id)
					->where('type_id', 2)
					->get_one();

					# SE ESTEBLECE LA NUEVA INFORMACION
					$product_price_2->price = $val->validated('price_2');

					# SE ACTUALIZA EL REGISTRO EN LA BASE DE DATOS
					$product_price_2->save();

					# SE BUSCA LA INFORMACION A TRAVES DEL MODELO
					$product_price_3 = Model_Products_Price::query()
					->where('product_id', $product_id)
					->where('type_id', 3)
					->get_one();

					# SE ESTEBLECE LA NUEVA INFORMACION
					$product_price_3->price = $val->validated('price_3');

					# SE ACTUALIZA EL REGISTRO EN LA BASE DE DATOS
					$product_price_3->save();

					# SE ESTABLECE EL MENSAJE DE EXITO
					Session::set_flash('success', 'Se actualizó la información de <b>'.$product->name.'</b> correctamente.');

					# SE REDIRECCIONA AL USUARIO
					Response::redirect('admin/catalogo/productos/editar/'.$product_id);
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

				# SI LA DIVISA ES PESOS
				if(Input::post('badge') == 0)
				{
					# SE ESTABLECE EL VALOR DE LAS CLASES
					$classes['exchange_rate']['form-group']   = '';
					$classes['exchange_rate']['form-control'] = '';
				}
			}
		}

		# SE ESTBLECE LA OPCION POR DEFAULT
		$category_opts += array('0' => 'Selecciona una opción');

		# SE BUSCA LA INFORMACION A TRAVES DEL MODELO
		$categories = Model_Category::query()
		->where('deleted', 0)
		->order_by('name', 'asc')
		->get();

		# SI SE OBTIENE INFORMACION
		if(!empty($categories))
		{
			# SE RECORRE ELEMENTO POR ELEMENTO
			foreach($categories as $category)
			{
				# SE ALMACENA LA OPCION
				$category_opts += array($category->id => $category->name);
			}
		}

		# SE ESTBLECE LA OPCION POR DEFAULT
		$subcategory_opts += array('0' => 'Selecciona una opción');

		# SE BUSCA LA INFORMACION A TRAVES DEL MODELO
		$subcategories = Model_Subcategory::query()
		->where('deleted', 0)
		->order_by('name', 'asc')
		->get();

		# SI SE OBTIENE INFORMACION
		if(!empty($subcategories))
		{
			# SE RECORRE ELEMENTO POR ELEMENTO
			foreach($subcategories as $subcategory)
			{
				# SE ALMACENA LA OPCION
				$subcategory_opts += array($subcategory->id => $subcategory->name);
			}
		}

		# SE ESTBLECE LA OPCION POR DEFAULT
		$brand_opts += array('0' => 'Selecciona una opción');

		# SE BUSCA LA INFORMACION A TRAVES DEL MODELO
		$brands = Model_Brand::query()
		->where('deleted', 0)
		->order_by('name', 'asc')
		->get();

		# SI SE OBTIENE INFORMACION
		if(!empty($brands))
		{
			# SE RECORRE ELEMENTO POR ELEMENTO
			foreach($brands as $brand)
			{
				# SE ALMACENA LA OPCION
				$brand_opts += array($brand->id => $brand->name);
			}
		}

		# SE ALMACENA LA INFORMACION PARA LA VISTA
		$data['id']            		= $product_id;
		$data['classes']       		= $classes;
		$data['category_opts'] 		= $category_opts;
		$data['subcategory_opts'] 	= $subcategory_opts;
		$data['brand_opts']    		= $brand_opts;

		# SE CARGA LA VISTA
		$this->template->title   = 'Editar producto';
		$this->template->content = View::forge('admin/catalogo/productos/editar', $data);
	}


	/**
	 * ELIMINAR
	 *
	 * CAMBIA EL VALOR DEL CAMPO DELETED PARA UN BORRADO LOGICO
	 *
	 * @access  public
	 * @return  Void
	 */
	public function action_eliminar($product_id = 0)
	{
		# SI NO SE RECIBE UN ID O NO ES UN NUMERO
		if($product_id == 0 || !is_numeric($product_id))
		{
			# SE REDIRECCIONA AL USUARIO
			Response::redirect('admin/catalogo/productos');
		}

		# SE INICIALIZAN LAS VARIABLES
		$relations_info = '';

		# SE BUSCA LA INFORMACION A TRAVES DEL MODELO
		$product = Model_Product::query()
		->where('id', $product_id)
		->where('deleted', 0)
		->get_one();

		# SI SE OBTIENE INFORMACION
		if(!empty($product))
		{
			# SE ESTEBLECE LA NUEVA INFORMACION
			$product->deleted = 1;

			# SI SE ALMACENO EL REGISTRO EN LA BASE DE DATOS
			if($product->save())
			{
				# SE ESTABLECE EL MENSAJE DE EXITO
				Session::set_flash('success', 'Se eliminó el producto <b>'.$product->name.'</b> correctamente.');
			}
		}

		# SE REDIRECCIONA AL USUARIO
		Response::redirect('admin/catalogo/productos');
	}


	/**
	 * AGREGAR FOTO
	 *
	 * PERMITE AGREGAR UNA FOTO A UN REGISTRO DE LA BASE DE DATOS
	 *
	 * @access  public
	 * @return  Void
	 */
	public function action_agregar_foto($product_id = 0)
	{
		# SI NO SE RECIBE UN ID O NO ES UN NUMERO
		if($product_id == 0 || !is_numeric($product_id))
		{
			# SE REDIRECCIONA AL USUARIO
			Response::redirect('admin/catalogo/productos');
		}

		# SE INICIALIZAN LAS VARIABLES
		$data    = array();
		$classes = array();
		$fields  = array('image');

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
		$product = Model_Product::query()
		->where('id', $product_id)
		->where('deleted', 0)
		->get_one();

		# SI SE OBTIENE INFORMACION
		if(!empty($product))
		{
			# SE ALMACENA LA INFORMACION PARA LA VISTA
			$data['name'] = $product->name;
		}
		else
		{
			# SE REDIRECCIONA AL USUARIO
			Response::redirect('admin/catalogo/productos');
		}

		# SI SE UTILIZO EL METODO POST
		if(Input::method() == 'POST')
		{
			# SE CREA LA VALIDACION DE LOS CAMPOS
			$val = Validation::forge('product');
			$val->add_callable('Rules');
			$val->add_field('image', 'imagen', 'required|min_length[1]');

			# SI NO HAY NINGUN PROBLEMA CON LA VALIDACION
			if($val->run())
			{
				# SE OBTIENE EL ORDEN MAXIMO
				$order = Model_Products_Image::query()
				->where('product_id', $product_id)
				->max('order');

				# SE CREA EL MODELO CON LA INFORMACION
				$product_image = new Model_Products_Image(array(
					'product_id' => $product_id,
					'image'      => $val->validated('image'),
					'order'      => $order + 1
				));

				# SI SE ALMACENO EL REGISTRO EN LA BASE DE DATOS
				if($product_image->save())
				{
					# SE ESTABLECE EL MENSAJE DE EXITO
					Session::set_flash('success', 'Se agregó la imagen correctamente.');

					# SE REDIRECCIONA AL USUARIO
					Response::redirect('admin/catalogo/productos/info/'.$product_id);
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

				# SI LA DIVISA ES PESOS
				if(Input::post('badge') == 0)
				{
					# SE ESTABLECE EL VALOR DE LAS CLASES
					$classes['exchange_rate']['form-group']   = '';
					$classes['exchange_rate']['form-control'] = '';
				}
			}
		}

		# SE ALMACENA LA INFORMACION PARA LA VISTA
		$data['id']      = $product_id;
		$data['classes'] = $classes;

		# SE CARGA LA VISTA
		$this->template->title   = 'Agregar producto';
		$this->template->content = View::forge('admin/catalogo/productos/agregar_foto', $data);
	}


	/**
	 * INFO FOTO
	 *
	 * MUESTRA LA INFORMACION DE UN REGISTRO DE LA BASE DE DATOS
	 *
	 * @access  public
	 * @return  Void
	 */
	public function action_info_foto($photo_id = 0)
	{
		# SI NO SE RECIBE UN ID O NO ES UN NUMERO
		if($photo_id == 0 || !is_numeric($photo_id))
		{
			# SE REDIRECCIONA AL USUARIO
			Response::redirect('admin/catalogo/productos');
		}

		# SE INICIALIZAN LAS VARIABLES
		$data = array();

		# SE BUSCA LA INFORMACION A TRAVES DEL MODELO
		$photo = Model_Products_Image::query()
		->where('id', $photo_id)
		->get_one();

		# SI SE OBTIENE INFORMACION
		if(!empty($photo))
		{
			# SE ALMACENA LA INFORMACION PARA LA VISTA
			$data['product_id'] = $photo->product_id;
			$data['name']       = $photo->product->name;
			$data['image']      = $photo->image;
		}
		else
		{
			# SE REDIRECCIONA AL USUARIO
			Response::redirect('admin/catalogo/productos');
		}

		# SE ALMACENA LA INFORMACION PARA LA VISTA
		$data['id'] = $photo_id;

		# SE CARGA LA VISTA
		$this->template->title   = 'Información de la fotografía';
		$this->template->content = View::forge('admin/catalogo/productos/info_foto', $data, false);
	}


	/**
	 * EDITAR FOTO
	 *
	 * PERMITE EDITAR UN REGISTRO DE LA BASE DE DATOS
	 *
	 * @access  public
	 * @return  Void
	 */
	public function action_editar_foto($photo_id = 0)
	{
		# SI NO SE RECIBE UN ID O NO ES UN NUMERO
		if($photo_id == 0 || !is_numeric($photo_id))
		{
			# SE REDIRECCIONA AL USUARIO
			Response::redirect('admin/catalogo/productos');
		}

		# SE INICIALIZAN LAS VARIABLES
		$data    = array();
		$classes = array();
		$fields  = array('image');

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
		$photo = Model_Products_Image::query()
		->where('id', $photo_id)
		->get_one();

		# SI SE OBTIENE INFORMACION
		if(!empty($photo))
		{
			# SE ALMACENA LA INFORMACION PARA LA VISTA
			$data['product_id'] = $photo->product_id;
			$data['name']       = $photo->product->name;
			$data['image']      = $photo->image;
		}
		else
		{
			# SE REDIRECCIONA AL USUARIO
			Response::redirect('admin/catalogo/productos');
		}

		# SI SE UTILIZO EL METODO POST
		if(Input::method() == 'POST')
		{
			# SE CREA LA VALIDACION DE LOS CAMPOS
			$val = Validation::forge('product');
			$val->add_callable('Rules');
			$val->add_field('image', 'imagen', 'required|min_length[1]');

			# SI NO HAY NINGUN PROBLEMA CON LA VALIDACION
			if($val->run())
			{
				# SE ESTEBLECE LA NUEVA INFORMACION
				$photo->image = $val->validated('image');

				# SI SE ALMACENO EL REGISTRO EN LA BASE DE DATOS
				if($photo->save())
				{
					# SE ESTABLECE EL MENSAJE DE EXITO
					Session::set_flash('success', 'Se actualizó la información de la fotografía correctamente.');

					# SE REDIRECCIONA AL USUARIO
					Response::redirect('admin/catalogo/productos/editar_foto/'.$photo_id);
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

				# SI LA DIVISA ES PESOS
				if(Input::post('badge') == 0)
				{
					# SE ESTABLECE EL VALOR DE LAS CLASES
					$classes['exchange_rate']['form-group']   = '';
					$classes['exchange_rate']['form-control'] = '';
				}
			}
		}

		# SE ALMACENA LA INFORMACION PARA LA VISTA
		$data['id']      = $photo_id;
		$data['classes'] = $classes;

		# SE CARGA LA VISTA
		$this->template->title   = 'Editar fotografía';
		$this->template->content = View::forge('admin/catalogo/productos/editar_foto', $data);
	}


	/**
	 * ELIMINAR FOTO
	 *
	 * ELIMINA UN REGISTRO DE LA BASE DE DATOS
	 *
	 * @access  public
	 * @return  Void
	 */
	public function action_eliminar_foto($photo_id = 0)
	{
		# SI NO SE RECIBE UN ID O NO ES UN NUMERO
		if($photo_id == 0 || !is_numeric($photo_id))
		{
			# SE REDIRECCIONA AL USUARIO
			Response::redirect('admin/catalogo/productos');
		}

		# SE BUSCA LA INFORMACION A TRAVES DEL MODELO
		$photo = Model_Products_Image::query()
		->where('id', $photo_id)
		->get_one();

		# SI SE OBTIENE INFORMACION
		if(!empty($photo))
		{
			# SI SE ELIMINO EL REGISTRO EN LA BASE DE DATOS
			if($photo->delete())
			{
				# SI EL ARCHIVO EXISTE
				if(file_exists(DOCROOT.'assets/uploads/'.$photo->image))
				{
					# SE ELIMINAN EL ARCHIVO
					File::delete(DOCROOT.'assets/uploads/'.$photo->image);
				}

				# SI EL ARCHIVO EXISTE
				if(file_exists(DOCROOT.'assets/uploads/thumb_'.$photo->image))
				{
					# SE ELIMINAN EL ARCHIVO
					File::delete(DOCROOT.'assets/uploads/thumb_'.$photo->image);
				}

				# SE ESTABLECE EL MENSAJE DE EXITO
				Session::set_flash('success', 'Se eliminó la fotografía correctamente.');

				# SE REDIRECCIONA AL USUARIO
				Response::redirect('admin/catalogo/productos/info/'.$photo->product_id);
			}
		}

		# SE REDIRECCIONA AL USUARIO
		Response::redirect('admin/catalogo/productos');
	}
}
