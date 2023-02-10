<?php

namespace sectorweb;

class Controller_Cart extends \Controller
{
	/**
	* BEFORE
	*
	*
	* @return Void
	*/
	public function before()
	{
        # SE HACE UNA INSTANCIA DE AUTH
        $this->auth = \Auth::instance();
	}


	/**
	 * DEBUG
	 *
	 * DEPURA EL CARRITO
	 *
	 * RECIBE EL CARRITO Y UN ID DE PRODUCTO EN CASO DE QUE TENGA QUE SER ELIMINADO
	 * RETORNA UN ARREGLO INDICANDO QUE PRODUCTOS ESTAN DISPONIBLES Y CUALES NO
	 * REGRESA EL TOTAL EN PRECIO Y CANTIDAD DE LOS PRODUCTOS DISPONIBLES
	 *
	 *
	 * @access  public
	 * @return  array
	 */
	public function action_debug($delete_product_id = null)
	{
		# SI LA PETICION ES LLAMADA DESDE UN CONTROLADOR
		if(\Request::is_hmvc())
		{
            # SE INICIALIZAN LOS ARREGLOS
            $cart_unavailable = array();
            $cart_data        = array();
            $cart             = array();

            # SE INICIALIZAN LAS VARIABLES
            $total_products_quantity  = 0;
            $total_products_price     = 0;
			$total_shipping           = 0;

            # SI EXISTE SESION DE CARRITO O HAY UN PRODUCTO A ELIMINAR
            if(\Session::get('cart') or $delete_product_id != null)
            {
                # SE OBTIENE LA SESION DEL CARRITO
                $cart = \Session::get('cart');

                # SI HAY PRODUCTOS EN EL CARRITO O HAY UN PRODUCTO A ELIMINAR
                if(!empty($cart) or $delete_product_id != null)
                {
                    # SI EXISTE UNA SESION DE USUARIO
                    if($this->auth->check())
                    {
                        # SE OBTIENE LA INFORMACION DE SU PERFIL
                        $customer = \Model_Customer::get_one(array('id_user' => $this->auth->get('id')));

                        # SI SE OBTUVO EL CUSTOMER
                        if(!empty($customer))
                        {
                            # SE OBTIENE EL ULTIMO PEDIDO NO ENVIADO
                            $sale = \Model_Sale::get_last_order_not_sent($customer->id);

                            # SI NO SE OBTUVO EL ULTIMO PEDIDO NO ENVIADO
                            if(empty($sale))
                            {
                                # SE CREA UNA ORDEN NO ENVIADA RELACIONADA AL CUSTOMER
                                $sale = \Model_Sale::set_new_order_not_sent($customer->id);
                            }
                        }

						///////////////////////////////////////////////////////////////

						# SE OBTIENEN LOS PRODUCTOS DE LA VENTA
						$sales_products = \Model_Sales_Product::get_all_products($sale->id);

						# SI SE OBTIENE INFORMACION
						if(!empty($sales_products))
						{
							# SE RECORRE ELEMENTO POR ELEMENTO
							foreach($sales_products as $sales_product)
							{
								# SE BUSCA EL PRODUCTO DE LA VENTA
								$sales_product_counts = \Model_Sales_Product::query()
								->where('sale_id', $sales_product->sale_id)
								->where('product_id', $sales_product->product_id)
								->get();

								# SI SE OBTIENE INFORMACION
								if(!empty($sales_product_counts))
								{
									# SI EXISTEN MAS REGISTROS
									if(count($sales_product_counts) >= 2)
									{
										# SE INICIALIZA EL CONTADOR
										$count = 1;

										# SE RECORRE ELEMENTO POR ELEMENTO
										foreach($sales_product_counts as $sales_product_count)
										{
											# SI EL REGISTRO ES DIFERENTE AL PRIMERO
											if($count > 1)
											{
												# SE ELIMINA EL REGISTRO DUPLICADO
												$sales_product_count->delete();
											}

											# SE INCREMENTA EL CONTADOR
											$count++;
										}
									}
								}
							}
						}

						///////////////////////////////////////////////////////////////
                    }

					# SE RECORRE PRODUCTO POR PRODUCTO
                    foreach($cart as $id => $array)
                    {
                        # SE BUSCA EL PRODUCTO
                        $product_cart = \Model_Product::find($id);

                        # SI SE OBTUVO EL RESULTADO
                        if(!empty($product_cart))
                        {
                            # SI EL PRODUCTO ESTA DISPONIBLE
                            if(
                                $product_cart->status == 1 and
                                $product_cart->available > 0 and
                                $product_cart->deleted == 0
                            )
                            {
                                # SE OBTIENE LA CANTIDAD SOLICITADA
                                $product_quantity = $array['quantity'];

                                # SI LA CANTIDAD DE LA PROPIEDAD ES MENOR A LA SOLICITADA
                                if($product_cart->available < $product_quantity)
                                {
                                    # SE ESTABLECE LA CANTIDAD MAXIMA DISPONIBLE DE LA PROPIEDAD EN CART Y SE SOBREESCRIBE LA VAR PRODUCT_QUANTITY
                                    $cart[$product_cart->id]['quantity'] = $product_cart->available;
                                    $product_quantity                    = $product_cart->available;
                                }

                                # SE OBTIENE EL PRECIO DEL PRODUCTO
                                $price_cart = \Model_Products_Price::get_price($product_cart->id, \Request::forge('sectorweb/user/get_type_customer', false)->execute()->response->body);

                                # SE OBTIENE EL TOTAL DEL PRODUCTO
                                $total_product_price = $price_cart * $product_quantity;

                                # SE CALCULA EL TOTAL EN CANTIDAD DE PRODUCTOS
                                $total_products_quantity += $product_quantity;

                                # SE CALCULA EL TOTAL DEL PRECIO DE LOS PRODUCTOS
                                $total_products_price += $total_product_price;

                                # SE LLENA EL ARREGLO CART_DATA CON LA INFORMACION DEL PRODUCTO
                                $cart_data[] = array(
                                    'id'                => $product_cart->id,
                                    'slug'              => $product_cart->slug,
                                    'name'              => $product_cart->name,
                                    'code'              => $product_cart->code,
                                    'image'             => $product_cart->image,
                                    'description'       => $product_cart->description,
                                    'price'             => array(
                                        'original'      => array(
                                            'regular'   => $product_cart->original_price,
                                            'formatted' => number_format($product_cart->original_price, 2, '.', ',')
                                        ),
                                        'current'       => array(
                                            'regular'   => $price_cart,
                                            'formatted' => number_format($price_cart, 2, '.', ',')
                                        ),
                                        'total'         => array(
                                            'regular'   => $total_product_price,
                                            'formatted' => number_format($total_product_price, 2, '.', ',')
                                        ),
                                    ),
                                    'available'         => (int)$product_cart->available,
                                    'quantity'          => array(
                                        'valid'         => (int)$product_quantity,
                                        'current'       => (int)$array['quantity'],
                                    ),
                                );

                                # SE HACEN LOS CAMBIOS EN LA BD
                                # SI LA SESION DE USUARIO ES VALIDA
                                if($this->auth->check())
                                {
                                    # SE VERIFICA SI YA EXTISTE ESE PRODUCTO EN EL PEDIDO
                                    $request = array(
                                        'id_sale'    => $sale->id,
                                        'id_product' => $product_cart->id
                                    );

                                    $sale_product = \Model_Sales_Product::get_one($request);

                                    # SI YA EXISTE UN REGISTRO DE ESE PRODUCTO EN LA ORDEN
                                    if(!empty($sale_product))
                                    {
                                        # SE ACTUALIZA LA INFORMACION DEL PRODUCTO SOBRE EL PEDIDO
                                        $request = array(
                                            'quantity' => $product_quantity,
                                            'price'    => $price_cart,
                                            'total'    => $total_product_price
                                        );

                                        # SE ACTUALIZA LA INFORMACION EN LA BASE DE DATOS
                                        $sale_product = \Model_Sales_Product::do_update($request, $sale_product->id);
                                    }
                                    else
                                    {
                                        # SE CREA LA RELACION DEL PRODUCTO Y PEDIDO
                                        $request = array(
                                            'sale_id'    => $sale->id,
                                            'product_id' => $product_cart->id,
                                            'quantity'   => $product_quantity,
                                            'price'      => $price_cart,
                                            'total'      => $total_product_price,
                                        );

                                        # SE GUARDA EL NUEVO PRODUCTO EN LA BASE DE DATOS
                                        $sale_product = \Model_Sales_Product::set_new_product($request);
                                    }
                                }
                            }
                            # SI NO
                            else
                            {
                                # SE ELIMINA DEL CARRITO
                                unset($cart[$product_cart->id]);

                                # SE LLENA EL ARREGLO CART_UNAVAILABLE CON LA INFORMACION DEL PRODUCTO
                                $cart_unavailable[] = array(
                                    'id'   => $product_cart->id,
                                    'name' => $product_cart->name
                                );

                                # SE HACEN LOS CAMBIOS EN LA BD
                                # SI LA SESION DE USUARIO ES VALIDA
                                if($this->auth->check())
                                {
                                    # SE BUSCA EL PRODUCTO EN LA ORDEN
                                    $request = array(
                                        'id_sale'    => $sale->id,
                                        'id_product' => $product_cart->id
                                    );

                                    $sale_product = \Model_Sales_Product::get_one($request);

                                    # SI SE ENCONTRO EL REGISTRO DEL PRODUCTO EN LA ORDEN
                                    if(!empty($sale_product))
                                    {
                                        # SE ELIMINA EL PRODUCTO DE LA VENTA
                                        $sale_product = \Model_Sales_Product::do_delete($sale_product->id);
                                    }
                                }
                            }
                        }
                        # SI NO SE ENCONTRO EL PRODUCTO EN LA BD
                        else
                        {
                            # SE ELIMINA EL PRODUCTO DEL CARRITO
                            unset($cart[$id]);
                        }
                    }

                    # SI EXISTE UNA SESION DE USUARIO
                    if($this->auth->check())
                    {
						//////////////////////////////////////////////////////////////////////////////

						# SI SE OBTUVO EL ULTIMO PEDIDO NO ENVIADO
						if(!empty($sale))
						{
							# SE OBTIENEN LOS PRODUCTOS DE LA VENTA
							$sales_products = \Model_Sales_Product::get_all_products($sale->id);

							# SI SE OBTIENE INFORMACION
							if(!empty($sales_products))
							{
								# SE RECORRE ELEMENTO POR ELEMENTO
								foreach($sales_products as $sales_product)
								{
									# SI NO EXISTE EL PRODUCTO EN LA SESION DEL CARRITO
									if(!array_key_exists($sales_product->product_id, $cart))
									{
										# SE ELIMINA EL PRODUCTO DE LA VENTA
										$sales_product = \Model_Sales_Product::do_delete($sales_product->id);
									}
									else
									{
										# SI LA CANTIDAD DEL PRODUCTO REGISTRADO ES DIFERENTE A LA CANTIDAD EL PRODUCTO EN SESION
										if($sales_product->quantity != $cart[$sales_product->product_id]['quantity'])
										{
											# SE ELIMINA EL PRODUCTO DE LA VENTA
											$sales_product = \Model_Sales_Product::do_delete($sales_product->id);
										}
									}
								}
							}
						}

						//////////////////////////////////////////////////////////////////////////////

                        # SE ACUTALIZAN LOS TOTALES DE SALE EN LA BD
                        $request = array(
                            'total'    => $total_products_price,
                            'shipping' => $total_shipping
                        );

                        # SE ACTUALIZA LA INFORMACION EN LA BASE DE DATOS
                        $sale = \Model_Sale::do_update($request, $sale->id);

                        # SI SE RECIBIO UN PRODUCTO A ELIMINAR DEL CARRITO Y BD
                        if(isset($delete_product_id))
                        {
                            # SE VERIFICA SI YA EXTISTE ESE PRODUCTO EN EL PEDIDO
                            $request = array(
                                'id_sale' => $sale->id,
                                'id_product' => $delete_product_id
                            );

                            $sale_product = \Model_Sales_Product::get_one($request);

                            # SI SE ENCONTRO EL REGISTRO DEL PRODUCTO EN LA ORDEN
                            if(!empty($sale_product))
                            {
                                # SE ELIMINA EL PRODUCTO DE LA VENTA
                                $sale_product = \Model_Sales_Product::do_delete($sale_product->id);
                            }
                        }
                    }

                    # SE ACTUALIZA O ELIMINA LA SESION DEL CARRITO
                    (!empty($cart)) ? \Session::set('cart', $cart) : \Session::delete('cart');
                }
                else
                {
                    # SE ELIMINA LA SESION DEL CARRITO
                    \Session::delete('cart');
                }
            }

            return array(
                'cart_data'               => $cart_data,
                'cart_session'            => $cart,
                'cart_unavailable'        => $cart_unavailable,
                'total_products_quantity' => $total_products_quantity,
                'total_products_price'    => array(
                    'regular'   => $total_products_price,
                    'formatted' => number_format($total_products_price, 2, '.', ',')
                ),
				'shipping'                => array(
					'regular'   => $total_shipping,
                    'formatted' => number_format($total_shipping, 2, '.', ',')
				),
				'total'                   => array(
					'regular' => $total_products_price + $total_shipping,
                    'formatted' => number_format($total_products_price + $total_shipping, 2, '.', ',')
				)
            );
		}
		else
		{
			// SE MUESTRA 404
			\Response::redirect('404');
		}
	}
}
