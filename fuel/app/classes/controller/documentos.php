<?php

/**
 * CONTROLADOR DOCUMENTOS
 *
 * @package  app
 * @extends  Controller_Base
 */
class Controller_Documentos extends Controller_Base
{
	/**
    * AVISO PRIVACIDAD
    *
    * MUESTRA LA PAGINA DE AVISO DE PRIVACIDAD
    *
    * @access  public
    * @return  Void
    */
    public function action_aviso_privacidad()
    {
        # SE INICIALIZA EL ARREGLO DATA
        $data = array();

        # SE CARGA LA VISTA DE INICIO
        $this->template->title       = 'Aviso de Privacidad | Natura y Mas';
        $this->template->description = 'Aviso de Privacidad de Natura y Mas.';
        $this->template->content     = View::forge('documentos/aviso_privacidad', $data, false);
    }


    /**
    * TERMINOS CONDICIONES
    *
    * MUESTRA LA PAGINA DE TERMINOS Y CONDICIONES
    *
    * @access  public
    * @return  Void
    */
    public function action_terminos_condiciones()
    {
        # SE INICIALIZA EL ARREGLO DATA
        $data = array();

        # SE CARGA LA VISTA DE INICIO
        $this->template->title       = 'Términos y condiciones | Natura y Mas';
        $this->template->description = 'Términos y condiciones de Natura y Mas.';
        $this->template->content     = View::forge('documentos/terminos_condiciones', $data, false);
    }
}
