<?php

/**
 * CONTROLADOR CONTACTO
 *
 * @package  app
 * @extends  Controller_Base
 */
class Controller_Contacto extends Controller_Base {
	/**
	 * INDEX
	 *
	 * CARGA LA VISTA DE CONTACTO
	 *
	 * @access  public
	 * @return  void
	 */
	public function action_index()
	{
		# SE INICIALIZAN LAS VARIABLES
        $data = array();

		# SE CARGA LA VISTA
        $this->template->title       = 'Contacto | Natura y Mas';
        $this->template->description = 'Métodos de contacto de Natura y Mas.';
		$this->template->content     = View::forge('contacto/index', $data);
	}
}
