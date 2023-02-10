<?php

class Controller_Base extends Controller_Template
{
	/**
	 * BEFORE
	 *
	 * OBTIENE Y ESTABLECE EN UNA VARIABLE GLOBAL EL NOMBRE DE USUARIO
	 *
	 * @return Void
	 */
	public function before()
	{
		# REQUERIDA PARA EL TEMPLATING
        parent::before();

		/*========================================
			CARRITO
		========================================*/

        # SE EJECUTA EL MODULO QUE DEPURA EL CARRITO
        $response = Request::forge('sectorweb/cart/debug', false)->execute()->response->body;

        # SE OBTIENE LA INFORMACION DE LA RESPUESTA
        $cart_data               = $response['cart_data'];
        $cart_session            = $response['cart_session'];
        $cart_unavailable        = $response['cart_unavailable'];
        $total_products_price    = $response['total_products_price'];
        $total_products_quantity = $response['total_products_quantity'];
        $total                   = $response['total'];

        # SI EXISTEN PRODUCTOS NO DISPONIBLES
        if(!empty($cart_unavailable))
        {
            # SE INICIALIZAN LAS VARIABLES
            $unavailable_products = '';

            # SE RECORREN LOS PRODUCTOS
            foreach($cart_unavailable as $product)
            {
                # SE CONSTRUYE LA LEYENDA CON LOS PRODUCTOS ELIMINADOS
                $unavailable_products .= '<p>- '.$product['name'].'</p>';
            }

            # SE ESTABLECE EL MENSAJE DE ALERTA DE LOS PRODUCTOS QUE SE ELIMINARON
            Session::set_flash('general_warning', '<h4 class="alert-heading">¡Atención!</h4><p>Los siguientes productos han sido removidos de tu carrito porque ya no están disponibles en la tienda:</p><hr>'.$unavailable_products);
        }

		/*========================================
			VARIABLES GLOBALES
		========================================*/

        # SE ALMACENAN LAS VARIABLES GLOBALES
        View::set_global('logged_in', Request::forge('sectorweb/user/is_valid', false)->execute()->response->body);
		View::set_global('cart_data', $cart_data);
        View::set_global('total_products_price', $total_products_price);
        View::set_global('total_products_quantity', $total_products_quantity);
        View::set_global('total', $total);
	}
}
