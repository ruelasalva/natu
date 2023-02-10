<?php

/**
 * CONTROLADOR PRODUCTO
 *
 * @package  app
 * @extends  Controller_Base
 */
class Controller_Producto extends Controller_Base
{
	/**
	 * INDEX
	 *
	 * CARGA LA VISTA DE PRODUCTO
	 *
	 * @access  public
	 * @return  void
	 */
	public function action_index($slug)
	{
		# SE INICIALIZAN LAS VARIABLES
        $data = array();

        # SE INICIALIZAN LOS ARREGLOS
        $related_products_info = array();
		$components_repeated   = array();
		$components_info       = array();
        $description           = '';
		$available             = 0;

        # SE BUSCA EL PRODUCTO
        $product = Model_Product::get_product(array('slug' => $slug));

        # SI SE ENCONTRO EL PRODUCTO
        if(empty($product))
        {
            # SE REDIRECCIONA A INICIO
            Response::redirect_back('/', 'refresh');
        }

        # SE INICIALIZAN LOS ARREGLOS
        $pictures_info = array();

        # SI SE ENCUENTRAN LAS IMAGENES
        if(!empty($product->galleries))
        {
            # SE BUSCAN LAS IMAGENES DEL PRODUCTO
            $galleries = Model_Products_Image::get_product_gallery($product->id);

            # SI SE OBTUVIERO LA GALERIA
            if(!empty($galleries))
            {
                # SE RECORRE FOTO POR FOTO
                foreach($galleries as $picture)
                {
                    # SE ALMACENA EN EL ARREGLO PICTURES_INFO
                    $pictures_info[] = array(
                        'image' => $picture->image,
                    );
                }
            }
        }

        # SE OBTIENEN LOS PRODUCTOS RELACIONADOS
        $related_products = Model_Product::get_catalog_related_products($product->id, $product->category_id);

        # SE REOCRREN LOS PRODUCTOS RELACIONADOS
        foreach($related_products as $related_product)
        {
            # SE ALMACENA LA INFORMACION EN EL ARREGLO RELATED_PRODUCTS_INFO
            $related_products_info[] = array(
                'id'        => $related_product->id,
                'product_id'=> $related_product->id,
                'slug'      => $related_product->slug,
                'name'      => $related_product->name,
                'image'     => $related_product->image,
                'code'      => $related_product->code,
				'available' => ($related_product->available == 0) ? true : false,
                'price'     => array(
                    'original' => ($related_product->original_price > 0) ? number_format($related_product->original_price, 2, '.', ',') : '',
                    'current'  => number_format(Model_Products_Price::get_price($related_product->id, Request::forge('sectorweb/user/get_type_customer', false)->execute()->response->body), 2, '.', ',')
                ),
            );
        }

        # SE ALMACENAN LOS DATOS PARA LA VISTA
        $data['product_id']       = $product->id;
        $data['image']            = $product->image;
        $data['category']         = array(
            'name' => $product->category->name,
            'slug' => $product->category->slug
        );
        $data['subcategory']            = array(
            'name' => $product->subcategory->name,
            'slug' => $product->subcategory->slug
        );
		$data['brand']            = array(
            'name' => $product->brand->name,
            'slug' => $product->brand->slug
        );
        $data['name']             = $product->name;
        $data['code']             = $product->code;
        $data['price']            = array(
            'original' => ($product->original_price > 0) ? number_format($product->original_price, 2, '.', ',') : '',
            'current'  => number_format(Model_Products_Price::get_price($product->id, Request::forge('sectorweb/user/get_type_customer', false)->execute()->response->body), 2, '.', ',')
        );
		$data['price_facebook']   = number_format(Model_Products_Price::get_price($product->id, 1), 2, '.', ',');
        $data['available']        = ($product->available == 0) ? true : false;
        $data['description']      = nl2br($product->description);
		$data['galleries']        = $pictures_info;
        $data['related_products'] = $related_products_info;

		# SE LIMPIA LA DESCRIPCION
        $description = preg_replace("/<([a-z][a-z0-9]*)[^>]*?(\/?)>/i",'<$1$2>', $product->description);
        $description = preg_replace("[\n|\r|\n\r]", ' ', $description);
        $description = str_replace(PHP_EOL, '', $description);
        $description = str_replace('', '"', $description);

		# SE CARGA LA VISTA
        $this->template->title       = $product->name.' | Natura y Mas';
		$this->template->description = $description;
		$this->template->content     = View::forge('producto/index', $data, false);
	}
}
