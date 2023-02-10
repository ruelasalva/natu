<?php

/**
* CONTROLADOR DESEADOS
*
* @package  app
* @extends  Controller_Base
*/
class Controller_Deseados extends Controller_Base
{
	/**
	* INDEX
	*
	* CARGA LA VISTA DE PRODUCTOS DESEADOS
	*
	* @access  public
	* @return  void
	*/
	public function action_index()
	{
		# SE INICIALIZAN LAS VARIABLES
		$data          = array();
        $products_info = array();

        # SE HACE UNA INSTANCIA DE AUTH
        $this->auth = Auth::instance();

        # SI EXISTE UNA SESION DE USUARIO
        if($this->auth->check())
        {
            # SE OBTIENE LA INFORMACION DEL CLIENTE
            $customer = Model_Customer::get_one(array('id_user' => $this->auth->get('id')));

            # SI SE OBTIENE INFORMACION
            if(!empty($customer))
            {
                # SE BUSCA LA INFORMACION A TRAVES DEL MODELO
                $wishlist = Model_Wishlist::query()
                ->where('customer_id', $customer->id)
                ->get_one();

                # SI SE OBTIENE INFORMACION
                if(!empty($wishlist))
                {
                    # SE BUSCA LA INFORMACION A TRAVES DEL MODELO
                    $wishlist_products = Model_Wishlists_Product::query()
                    ->where('wishlist_id', $wishlist->id)
                    ->get();

                    # SI SE OBTIENE INFORMACION
                    if(!empty($wishlist_products))
                    {
                        # SE RECORRE ELEMENTO POR ELEMENTO
                        foreach($wishlist_products as $product)
                        {
							# SI EL PRODUCTO ESTA ACTIVO
							if($product->product->status == 1 && $product->product->deleted == 0)
							{
								# SE OBTIENE EL PRECIO DEL PRODUCTO
	                            $price = Model_Products_Price::get_price($product->product->id, Request::forge('sectorweb/user/get_type_customer', false)->execute()->response->body);

								# SE ALMANCENA LA INFORMACION DEL PRODUCTO
	                            $products_info[] = array(
	                                'id'          => $product->product->id,
	                                'image'       => $product->product->image,
	                                'slug'        => $product->product->slug,
	                                'name'        => $product->product->name,
	                                'description' => $product->product->description,
									'available'   => ($product->product->available == 0) ? true : false,
	                                'price'       => number_format($price, 2, '.', ',')
	                            );
							}
                        }
                    }
                }
            }
        }
        else
        {
            # SE REDIRECCIONA AL USUARIO
			Response::redirect('tienda');
        }

        # SE ALMACENA LA INFORMACION PARA LA VISTA
        $data['products'] = $products_info;

		# SE CARGA LA VISTA
		$this->template->title       = 'Deseados | Natura y Mas';
		$this->template->description = 'Checkout de Natura y Mas.';
		$this->template->content     = View::forge('deseados/index', $data);
	}
}
