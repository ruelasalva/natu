<?php

/**
 * CONTROLADOR EMPRESA
 *
 * @package  app
 * @extends  Controller_Base
 */
class Controller_Empresa extends Controller_Base
{
	/**
    * INDEX
    *
    * MUESTRA LA PAGINA DE EMPRESA
    *
    * @access  public
    * @return  Void
    */
    public function action_index()
    {
        # SE INICIALIZA EL ARREGLO DATA
        $data = array();

        # SE CARGA LA VISTA DE INICIO
        $this->template->title       = 'Empresa | Natura y Mas';
        $this->template->description = 'Información de la empresa Natura y Mas. Conoce nuestra historia y valores.';
        $this->template->content     = View::forge('empresa/index', $data, false);
    }
}
