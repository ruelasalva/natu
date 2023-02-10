<?php
/**
* CONTROLADOR INICIO
*
* @package  app
* @extends  Controller_Base
*/
class Controller_Inicio extends Controller_Base
{
	/**
	* INDEX
	*
	* CARGA LA VISTA DE INICIO
	*
	* @access  public
	* @return  void
	*/
	public function action_index()
	{
		# SE INICIALIZAN LAS VARIABLES
		$data                   = array();
		$slides_info            = array();
		$brands_info            = array();
		$featured_products_info = array();
		$new_products_info      = array();

		# SE OBTIENE LOS SLIDES
		$slides = Model_Slide::get_carousel();

		# SI SE OBTUVIERON SLIDES
		if(!empty($slides))
		{
			# SE RECORRE ELEMENTO POR ELEMENTO
			foreach($slides as $slide)
			{
				# SE ALMACENA LA INFORMACION
				$slides_info[] = array(
					'image' => $slide->image,
					'url'   => $slide->url
				);
			}
		}

		# SE OBTIENE LAS MARCAS
		$brands = Model_Brand::get_all();

		# SI SE OBTUVO RESULTADO
		if(!empty($brands))
		{
			# SE RECORRE ELEMENTO POR ELEMENTO
			foreach($brands as $brand)
			{
				# SE ALMACENA LA INFORMACION
				$brands_info[] = array(
					'slug'  => $brand->slug,
					'name'  => $brand->name,
					'image' => $brand->image
				);
			}
		}

		# SE OBTIENE LOS PRODUCTOS DESTACADOS
		$featured_products = Model_Product::get_highlighted();

		# SI SE OBTUVO RESULTADO
		if(!empty($featured_products))
		{
			# SE RECORRE ELEMENTO POR ELEMENTO
			foreach($featured_products as $product)
			{
				# SE ALMACENA LA INFORMACION
				$featured_products_info[] = array(
					'id'        => $product->id,
					'slug'      => $product->slug,
					'name'      => $product->name,
					'code'      => $product->code,
					'image'     => $product->image,
					'available' => ($product->available == 0) ? true : false,
					'price'     => array(
						'original' => ($product->original_price > 0) ? number_format($product->original_price, 2, '.', ',') : '',
						'current'  => number_format(Model_Products_Price::get_price($product->id, Request::forge('sectorweb/user/get_type_customer', false)->execute()->response->body), 2, '.', ',')
					)
				);
			}
		}

		# SE OBTIENEN LOS ULTIMOS PRODUCTOS
		$new_products = Model_Product::get_news();

		# SI SE OBTUVO RESULTADO
		if(!empty($new_products))
		{
			# SE RECORRE ELEMENTO POR ELEMENTO
			foreach($new_products as $product)
			{
				# SE ALMACENA LA INFORMACION
				$new_products_info[] = array(
					'id'        => $product->id,
					'slug'      => $product->slug,
					'name'      => $product->name,
					'code'      => $product->code,
					'image'     => $product->image,
					'available' => ($product->available == 0) ? true : false,
					'price'     => array(
						'original' => ($product->original_price > 0) ? number_format($product->original_price, 2, '.', ',') : '',
						'current'  => number_format(Model_Products_Price::get_price($product->id, Request::forge('sectorweb/user/get_type_customer', false)->execute()->response->body), 2, '.', ',')
					)
				);
			}
		}

		# SE ALMACENAN LA INFORMACION EN LA VISTA
		$data['slides']            = $slides_info;
		$data['brands']            = $brands_info;
		$data['featured_products'] = $featured_products_info;
		$data['new_products']      = $new_products_info;

		# SE CARGA LA VISTA
		$this->template->title       = 'Natura y Mas';
		$this->template->description = 'Natura y Mas - Productos naturales de calidad para un estilo de vida saludable';
		$this->template->content     = View::forge('inicio/index', $data);
	}
}
