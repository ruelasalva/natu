<?php

/**
 * CONTROLADOR DISTRIBUCIÓN
 *
 * @package  app
 * @extends  Controller_Base
 */
class Controller_Distribucion extends Controller_Base
{
	/**
    * INDEX
    *
    * MUESTRA LA PAGINA DE DISTRIBUCIÓN
    *
    * @access  public
    * @return  Void
    */
    public function action_index()
    {
        # SE INICIALIZA EL ARREGLO DATA
        $data        = array();
        $brands_info = array();

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

        # SE ALMACENAN LA INFORMACION EN LA VISTA
        $data['brands'] = $brands_info;

        # SE CARGA LA VISTA DE INICIO
        $this->template->title       = 'Distribución | Natura y Mas';
        $this->template->description = 'Conoce nuestro compromiso en la distribución de productos de la Natura y Mas.';
        $this->template->content     = View::forge('distribucion/index', $data, false);
    }
}
