<?php

/**
* CONTROLADOR CATALOGO
*
* @package  app
* @extends  Controller_Base
*/
class Controller_Catalogo extends Controller_Base
{
	/**
	* INDEX
	*
	* MUESTRA LOS PRODUCTOS DEL CATALOGO
	*
	* @access  public
	* @return  Void
	*/
	public function action_index()
	{
		# SE INICIALIZAN LAS VARIABLES
		$data            	= array();
		$baners_info		= array();
		$categories_info 	= array();
		$subcategories_info = array();
		$brands_info     	= array();
		$products_info   	= array();
		$per_page        	= 30;
		$available          = 0;

		# SE OBTIENE LOS BANERS
		$baners = Model_Baner::get_carousel();

		# SI SE OBTUVIERON 	BANERS
		if(!empty($baners))
		{
			# SE RECORRE ELEMENTO POR ELEMENTO
			foreach($baners as $baner)
			{
				# SE ALMACENA LA INFORMACION
				$baners_info[] = array(
					'image' => $baner->image,
					'url'   => $baner->url
				);
			}
		}

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
				# SE ALMACENA LA INFORMACION
				$categories_info[] = array(
					'slug' => $category->slug,
					'name' => Str::truncate($category->name, 30)
				);
			}
		}
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
				# SE ALMACENA LA INFORMACION
				$subcategories_info[] = array(
					'slug' => $subcategory->slug,
					'name' => Str::truncate($subcategory->name, 30)
				);
			}
		}
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
				# SE ALMACENA LA INFORMACION
				$brands_info[] = array(
					'slug' => $brand->slug,
					'name' => Str::truncate($brand->name, 30)
				);
			}
		}

		# SE BUSCA LA INFORMACION A TRAVES DEL MODELO
		$products = Model_Product::query()
		->where('status', 1)
		->where('deleted', 0);

		# SI EXISTE LA SESSION DE PRODUCTS_AVAILABLE
		if(Session::get('products_available'))
		{
			# SE AGREGA LA CLAUSULA
			$products = $products->where('available', '>', 0);
		}

		# SE ESTABLECE LA CONFIGURACION DE LA PAGINACION
		$config = array(
			'pagination_url' => Uri::current(),
			'total_items'    => $products->count(),
			'per_page'       => $per_page,
			'num_links'		 => 5,
			'link_offset'	 => 0.50,
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
					'id'             => $product->id,
					'product_id'     => $product->id,
					'slug'           => $product->slug,
					'name'           => $product->name,
					'code'      	 => $product->code,
					'image'          => $product->image,
					'available'      => ($product->available == 0) ? true : false,
					'original_price' => ($product->original_price > 0) ? number_format($product->original_price, 2, '.', ',') : '',
					'price'          => number_format(Model_Products_Price::get_price($product->id, Request::forge('sectorweb/user/get_type_customer', false)->execute()->response->body), 2, '.', ',')
				);
			}
		}

		# SE ALMACENA LA INFORMACION PARA LA VISTA
		$data['baners'] 		= $baners_info;
		$data['categories'] 	= $categories_info;
		$data['subcategories'] 	= $subcategories_info;
		$data['brands']     	= $brands_info;
		$data['products']   	= $products_info;
		$data['pagination'] 	= $pagination->render();

		# SE CARGA LA VISTA
		$this->template->title       = 'Tienda | Natura y Mas';
		$this->template->description = 'Tienda de productos de la Natura y Mas.';
		$this->template->content     = View::forge('catalogo/index', $data, false);
	}


	/**
	 * BUSQUEDA
	 *
	 * REDIRECCIONA A LA URL DE BUSCAR CON LOS PARAMETROS
	 *
	 * @access  public
	 * @return  Void
	 */
	public function action_busqueda()
	{
		# SI SE UTILIZO EL METODO POST
		if(Input::method() == 'POST')
		{
			# SE CREA LA VALIDACION DE LOS CAMPOS
			$val = Validation::forge('search');
			$val->add_callable('Rules');
			$val->add_field('search', 'search', 'required|max_length[100]');

			# SI NO HAY NINGUN PROBLEMA CON LA VALIDACION
			if($val->run())
			{
				# SE REMPLAZAN ALGUNOS CARACTERES
				$search = str_replace(' ', '+', $val->validated('search'));
				$search = str_replace('*', '', $search);

				# SE REDIRECCIONA A BUSCAR ARTICULOS
				Response::redirect('buscar/'.$search);
			}
			else
			{
				# SE REDIRECCIONA AL USUARIO
				Response::redirect('tienda');
			}
		}
		else
		{
			# SE REDIRECCIONA AL USUARIO
			Response::redirect('/');
		}
	}


	/**
	 * BUSCAR
	 *
	 * MUESTRA LOS RESULTADOS DE LA BUSQUEDA
	 *
	 * @access  public
	 * @return  Void
	 */
	public function action_buscar($search = '')
	{
		# SE INICIALIZAN LAS VARIABLES
		$data            	= array();
		$baners_info		= array();
		$categories_info 	= array();
		$subcategories_info = array();
		$brands_info     	= array();
		$products_info   	= array();
		$per_page        	= 30;
		$original_search 	= $search;
		$search          	= str_replace('+', ' ', rawurldecode($search));

		# SE OBTIENE LOS BANERS
		$baners = Model_Baner::get_carousel();

		# SI SE OBTUVIERON 	BANERS
		if(!empty($baners))
		{
			# SE RECORRE ELEMENTO POR ELEMENTO
			foreach($baners as $baner)
			{
				# SE ALMACENA LA INFORMACION
				$baners_info[] = array(
					'image' => $baner->image,
					'url'   => $baner->url
				);
			}
		}

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
				# SE ALMACENA LA INFORMACION
				$categories_info[] = array(
					'slug' => $category->slug,
					'name' => Str::truncate($category->name, 30)
				);
			}
		}
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
				# SE ALMACENA LA INFORMACION
				$subcategories_info[] = array(
					'slug' => $subcategory->slug,
					'name' => Str::truncate($subcategory->name, 30)
				);
			}
		}
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
				# SE ALMACENA LA INFORMACION
				$brands_info[] = array(
					'slug' => $brand->slug,
					'name' => Str::truncate($brand->name, 30)
				);
			}
		}

		# SE BUSCA LA INFORMACION A TRAVES DEL MODELO
		$products = Model_Product::query()
		->and_where_open()
		->where('name', 'like', '%'.$search.'%')
		->or_where('code', 'like', '%'.$search.'%')
		->and_where_close()
		->where('status', 1)
		->where('deleted', 0);

		# SI EXISTE LA SESSION DE PRODUCTS_AVAILABLE
		if(Session::get('products_available'))
		{
			# SE AGREGA LA CLAUSULA
			$products = $products->where('available', '>', 0);
		}

		# SE ESTABLECE LA CONFIGURACION DE LA PAGINACION
		$config = array(
			'pagination_url' => Uri::base(false).substr($_SERVER['REQUEST_URI'], 1),
			'total_items'    => $products->count(),
			'per_page'       => $per_page,
			'num_links'		 => 5,
			'link_offset'	 => 0.50,
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
					'id'             => $product->id,
					'product_id'     => $product->id,
					'slug'           => $product->slug,
					'name'           => $product->name,
					'code'           => $product->code,
					'image'          => $product->image,
					'available'      => ($product->available == 0) ? true : false,
					'original_price' => ($product->original_price > 0) ? number_format($product->original_price, 2, '.', ',') : '',
					'price'          => number_format(Model_Products_Price::get_price($product->id, Request::forge('sectorweb/user/get_type_customer', false)->execute()->response->body), 2, '.', ',')
				);
			}
		}

		# SE ALMACENA LA INFORMACION PARA LAS VISTAS
		$data['baners'] 		= $baners_info;
		$data['categories'] 	= $categories_info;
		$data['subcategories'] 	= $subcategories_info;
		$data['brands']     	= $brands_info;
		$data['title']      	= $search;
		$data['products']   	= $products_info;
		$data['pagination'] 	= $pagination->render();

		# SE CARGA LA VISTA DE INICIO
		$this->template->title       = $search.' | Natura y Mas';
		$this->template->description = 'Tienda de productos de la Natura y Mas.';
		$this->template->content     = View::forge('catalogo/buscar', $data, false);
	}


	/**
	* CATEGORIA
	*
	* MUESTRA LOS PRODUCTOS DE LA CATEGORIA
	*
	* @access  public
	* @return  Void
	*/
	public function action_categoria($slug = '')
	{
		# SE INICIALIZAN LAS VARIABLES
		$data            	= array();
		$baners_info		= array();
		$categories_info 	= array();
		$subcategories_info = array();
		$brands_info     	= array();
		$products_info   	= array();
		$per_page        	= 30;

		# SE OBTIENE LOS BANERS
		$baners = Model_Baner::get_carousel();

		# SI SE OBTUVIERON 	BANERS
		if(!empty($baners))
		{
			# SE RECORRE ELEMENTO POR ELEMENTO
			foreach($baners as $baner)
			{
				# SE ALMACENA LA INFORMACION
				$baners_info[] = array(
					'image' => $baner->image,
					'url'   => $baner->url
				);
			}
		}

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
				# SE ALMACENA LA INFORMACION
				$categories_info[] = array(
					'slug' => $category->slug,
					'name' => Str::truncate($category->name, 30)
				);
			}
		}
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
				# SE ALMACENA LA INFORMACION
				$subcategories_info[] = array(
					'slug' => $subcategory->slug,
					'name' => Str::truncate($subcategory->name, 30)
				);
			}
		}
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
				# SE ALMACENA LA INFORMACION
				$brands_info[] = array(
					'slug' => $brand->slug,
					'name' => Str::truncate($brand->name, 30)
				);
			}
		}

		# SE BUSCA LA INFORMACION A TRAVES DEL MODELO
		$category = Model_Category::query()
		->where('slug', $slug)
		->where('deleted', 0)
		->get_one();

		# SI NO SE OBTIENE INFORMACION
		if(empty($category))
		{
			# SE REDIRECCIONA AL USUARIO
			Response::redirect('/');
		}

		# SE BUSCA LA INFORMACION A TRAVES DEL MODELO
		$products = Model_Product::query()
		->where('category_id', $category->id)
		->where('status', 1)
		->where('deleted', 0);

		# SI EXISTE LA SESSION DE PRODUCTS_AVAILABLE
		if(Session::get('products_available'))
		{
			# SE AGREGA LA CLAUSULA
			$products = $products->where('available', '>', 0);
		}

		# SE ESTABLECE LA CONFIGURACION DE LA PAGINACION
		$config = array(
			'pagination_url' => Uri::current(),
			'total_items'    => $products->count(),
			'per_page'       => $per_page,
			'num_links'		 => 5,
			'link_offset'	 => 0.50,
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
					'id'             => $product->id,
					'product_id'     => $product->id,
					'slug'           => $product->slug,
					'name'           => $product->name,
					'code'      	 => $product->code,
					'image'          => $product->image,
					'available'      => ($product->available == 0) ? true : false,
					'original_price' => ($product->original_price > 0) ? number_format($product->original_price, 2, '.', ',') : '',
					'price'          => number_format(Model_Products_Price::get_price($product->id, Request::forge('sectorweb/user/get_type_customer', false)->execute()->response->body), 2, '.', ',')
				);
			}
		}

		# SE ALMACENA LA INFORMACION PARA LA VISTA
		$data['baners'] 		= $baners_info;
		$data['categories'] 	= $categories_info;
		$data['subcategories'] 	= $subcategories_info;
		$data['brands']     	= $brands_info;
		$data['title']      	= $category->name;
		$data['products']   	= $products_info;
		$data['pagination'] 	= $pagination->render();

		# SE CARGA LA VISTA
		$this->template->title       = $category->name.' | Natura y Mas';
		$this->template->description = 'Tienda de productos de la Natura y Mas.';
		$this->template->content     = View::forge('catalogo/categoria', $data, false);
	}

	/**
	* SUBCATEGORIA
	*
	* MUESTRA LOS PRODUCTOS DE LA CATEGORIA
	*
	* @access  public
	* @return  Void
	*/
	public function action_subcategoria($slug = '')
	{
		# SE INICIALIZAN LAS VARIABLES
		$baners_info		= array();
		$data            	= array();
		$categories_info 	= array();
		$subcategories_info = array();
		$brands_info     	= array();
		$products_info   	= array();
		$per_page        	= 30;

		# SE OBTIENE LOS BANERS
		$baners = Model_Baner::get_carousel();

		# SI SE OBTUVIERON 	BANERS
		if(!empty($baners))
		{
			# SE RECORRE ELEMENTO POR ELEMENTO
			foreach($baners as $baner)
			{
				# SE ALMACENA LA INFORMACION
				$baners_info[] = array(
					'image' => $baner->image,
					'url'   => $baner->url
				);
			}
		}

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
				# SE ALMACENA LA INFORMACION
				$categories_info[] = array(
					'slug' => $category->slug,
					'name' => Str::truncate($category->name, 30)
				);
			}
		}
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
			# SE ALMACENA LA INFORMACION
			$subcategories_info[] = array(
				'slug' => $subcategory->slug,
				'name' => Str::truncate($subcategory->name, 30)
			);
		}
	}
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
				# SE ALMACENA LA INFORMACION
				$brands_info[] = array(
					'slug' => $brand->slug,
					'name' => Str::truncate($brand->name, 30)
				);
			}
		}

		# SE BUSCA LA INFORMACION A TRAVES DEL MODELO
		$subcategory = Model_Subcategory::query()
		->where('slug', $slug)
		->where('deleted', 0)
		->get_one();

		# SI NO SE OBTIENE INFORMACION
		if(empty($subcategory))
		{
			# SE REDIRECCIONA AL USUARIO
			Response::redirect('/');
		}

		# SE BUSCA LA INFORMACION A TRAVES DEL MODELO
		$products = Model_Product::query()
		->where('subcategory_id', $subcategory->id)
		->where('status', 1)
		->where('deleted', 0);

		# SI EXISTE LA SESSION DE PRODUCTS_AVAILABLE
		if(Session::get('products_available'))
		{
			# SE AGREGA LA CLAUSULA
			$products = $products->where('available', '>', 0);
		}

		# SE ESTABLECE LA CONFIGURACION DE LA PAGINACION
		$config = array(
			'pagination_url' => Uri::current(),
			'total_items'    => $products->count(),
			'per_page'       => $per_page,
			'num_links'		 => 5,
			'link_offset'	 => 0.50,
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
					'id'        		=> $product->id,
					'product_id'		=> $product->id,
					'slug'      		=> $product->slug,
					'name'      		=> $product->name,
					'code'      	 	=> $product->code,
					'image'     		=> $product->image,
					'available' 		=> ($product->available == 0) ? true : false,
					'original_price' 	=> ($product->original_price > 0) ? number_format($product->original_price, 2, '.', ',') : '',
					'price'     		=> number_format(Model_Products_Price::get_price($product->id, Request::forge('sectorweb/user/get_type_customer', false)->execute()->response->body), 2, '.', ',')
				);
			}
		}

		# SE ALMACENA LA INFORMACION PARA LA VISTA
		$data['baners'] 		= $baners_info;
		$data['categories'] 	= $categories_info;
		$data['subcategories'] 	= $subcategories_info;
		$data['brands']     	= $brands_info;
		$data['title']      	= $subcategory->name;
		$data['products']   	= $products_info;
		$data['pagination'] 	= $pagination->render();

		# SE CARGA LA VISTA
		$this->template->title       = $subcategory->name.' | Natura y Mas';
		$this->template->description = 'Tienda de productos de la Natura y Mas.';
		$this->template->content     = View::forge('catalogo/categoria', $data, false);
	}


	/**
	* MARCA
	*
	* MUESTRA LOS PRODUCTOS DE LA MARCA
	*
	* @access  public
	* @return  Void
	*/
	public function action_marca($slug = '')
	{
		# SE INICIALIZAN LAS VARIABLES
		$data            	= array();
		$baners_info		= array();
		$categories_info 	= array();
		$subcategories_info = array();
		$brands_info     	= array();
		$products_info   	= array();
		$per_page        	= 30;

		# SE OBTIENE LOS BANERS
		$baners = Model_Baner::get_carousel();

		# SI SE OBTUVIERON 	BANERS
		if(!empty($baners))
		{
			# SE RECORRE ELEMENTO POR ELEMENTO
			foreach($baners as $baner)
			{
				# SE ALMACENA LA INFORMACION
				$baners_info[] = array(
					'image' => $baner->image,
					'url'   => $baner->url
				);
			}
		}

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
				# SE ALMACENA LA INFORMACION
				$categories_info[] = array(
					'slug' => $category->slug,
					'name' => Str::truncate($category->name, 30)
				);
			}
		}
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
				# SE ALMACENA LA INFORMACION
				$subcategories_info[] = array(
					'slug' => $subcategory->slug,
					'name' => Str::truncate($subcategory->name, 30)
				);
			}
		}
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
				# SE ALMACENA LA INFORMACION
				$brands_info[] = array(
					'slug' => $brand->slug,
					'name' => Str::truncate($brand->name, 30)
				);
			}
		}

		# SE BUSCA LA INFORMACION A TRAVES DEL MODELO
		$brand = Model_Brand::query()
		->where('slug', $slug)
		->where('deleted', 0)
		->get_one();

		# SI NO SE OBTIENE INFORMACION
		if(empty($brand))
		{
			# SE REDIRECCIONA AL USUARIO
			Response::redirect('/');
		}

		# SE BUSCA LA INFORMACION A TRAVES DEL MODELO
		$products = Model_Product::query()
		->where('brand_id', $brand->id)
		->where('status', 1)
		->where('deleted', 0);

		# SI EXISTE LA SESSION DE PRODUCTS_AVAILABLE
		if(Session::get('products_available'))
		{
			# SE AGREGA LA CLAUSULA
			$products = $products->where('available', '>', 0);
		}

		# SE ESTABLECE LA CONFIGURACION DE LA PAGINACION
		$config = array(
			'pagination_url' => Uri::current(),
			'total_items'    => $products->count(),
			'per_page'       => $per_page,
			'num_links'		 => 5,
			'link_offset'	 => 0.50,
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
					'id'             => $product->id,
					'product_id'     => $product->id,
					'slug'           => $product->slug,
					'name'           => $product->name,
					'code'      	 => $product->code,
					'image'          => $product->image,
					'available'      => ($product->available == 0) ? true : false,
					'original_price' => ($product->original_price > 0) ? number_format($product->original_price, 2, '.', ',') : '',
					'price'          => number_format(Model_Products_Price::get_price($product->id, Request::forge('sectorweb/user/get_type_customer', false)->execute()->response->body), 2, '.', ',')
				);
			}
		}

		# SE ALMACENA LA INFORMACION PARA LA VISTA
		$data['baners'] 		= $baners_info;
		$data['categories'] 	= $categories_info;
		$data['subcategories'] 	= $subcategories_info;
		$data['brands']     	= $brands_info;
		$data['title']      	= $brand->name;
		$data['products']   	= $products_info;
		$data['pagination'] 	= $pagination->render();

		# SE CARGA LA VISTA
		$this->template->title       = $brand->name.' | Natura y Mas';
		$this->template->description = 'Tienda de productos de la Natura y Mas.';
		$this->template->content     = View::forge('catalogo/categoria', $data, false);
	}

	/**
	* PROMOCIONES
	*
	* MUESTRA LOS PRODUCTOS DEL CATALOGO
	*
	* @access  public
	* @return  Void
	*/
	public function action_promociones()
	{
		# SE INICIALIZAN LAS VARIABLES
		$data            	= array();
		$baners_info		= array();
		$categories_info 	= array();
		$subcategories_info = array();
		$brands_info     	= array();
		$products_info   	= array();
		$per_page        	= 30;
		$available          = 0;

		# SE OBTIENE LOS BANERS
		$baners = Model_Baner::get_carousel();

		# SI SE OBTUVIERON 	BANERS
		if(!empty($baners))
		{
			# SE RECORRE ELEMENTO POR ELEMENTO
			foreach($baners as $baner)
			{
				# SE ALMACENA LA INFORMACION
				$baners_info[] = array(
					'image' => $baner->image,
					'url'   => $baner->url
				);
			}
		}

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
				# SE ALMACENA LA INFORMACION
				$categories_info[] = array(
					'slug' => $category->slug,
					'name' => Str::truncate($category->name, 30)
				);
			}
		}
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
				# SE ALMACENA LA INFORMACION
				$subcategories_info[] = array(
					'slug' => $subcategory->slug,
					'name' => Str::truncate($subcategory->name, 30)
				);
			}
		}
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
				# SE ALMACENA LA INFORMACION
				$brands_info[] = array(
					'slug' => $brand->slug,
					'name' => Str::truncate($brand->name, 30)
				);
			}
		}

		# SE BUSCA LA INFORMACION A TRAVES DEL MODELO
		$products = Model_Product::query()
		->where('status', 1)
		->where('original_price', '>', 0)
		->where('deleted', 0);

		# SI EXISTE LA SESSION DE PRODUCTS_AVAILABLE
		if(Session::get('products_available'))
		{
			# SE AGREGA LA CLAUSULA
			$products = $products->where('available', '>', 0);
		}

		# SE ESTABLECE LA CONFIGURACION DE LA PAGINACION
		$config = array(
			'pagination_url' => Uri::current(),
			'total_items'    => $products->count(),
			'per_page'       => $per_page,
			'num_links'		 => 5,
			'link_offset'	 => 0.50,
			'uri_segment'    => 'pagina',
		);

		# SE CREA LA INSTANCIA DE LA PAGINACION
		$pagination = Pagination::forge('products', $config);

		# SE EJECUTA EL QUERY
		$products = $products->order_by('id', 'asc')
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
					'id'             => $product->id,
					'product_id'     => $product->id,
					'slug'           => $product->slug,
					'name'           => $product->name,
					'code'      	 => $product->code,
					'image'          => $product->image,
					'available'      => ($product->available == 0) ? true : false,
					'original_price' => ($product->original_price > 0) ? number_format($product->original_price, 2, '.', ',') : '',
					'price'          => number_format(Model_Products_Price::get_price($product->id, Request::forge('sectorweb/user/get_type_customer', false)->execute()->response->body), 2, '.', ',')
				);
			}
		}

		# SE ALMACENA LA INFORMACION PARA LA VISTA
		$data['baners'] 		= $baners_info;
		$data['categories'] 	= $categories_info;
		$data['subcategories'] 	= $subcategories_info;
		$data['brands']     	= $brands_info;
		$data['products']   	= $products_info;
		$data['pagination'] 	= $pagination->render();

		# SE CARGA LA VISTA
		$this->template->title       = 'Tienda | Natura y Mas';
		$this->template->description = 'Tienda de productos de la Natura y Mas.';
		$this->template->content     = View::forge('catalogo/promocion', $data, false);
	}
}
