<?php

/**
 * CONTROLADOR CERRAR SESION
 *
 * @package  app
 * @extends  Controller_Base
 */
class Controller_Cerrar_Sesion extends Controller_Base
{
	/**
	 * BEFORE
	 *
	 * REVISA SI EL USUARIO TIENE UNA SESION,
	 * SI NO EXISTE REDIRECCIONA AL LOGIN
	 *
	 * @return Void
	 */
	public function before()
	{
		# REQUERIDA PARA EL TEMPLATING
		parent::before();
	}


	/**
	 * CERRAR SESION
	 *
	 * ELIMINA LA SESION DEL ADMINISTRADOR Y REDIRECCIONA A LA VISTA DEL LOGIN
	 *
	 * @access  public
	 * @return  void
	 */
	public function action_index()
	{
        # SI HAY UN USUARIO LOGEADO
        if(Auth::check())
        {
            # SE EJECUTA EL MODULO DE DESLOGUEO
            Request::forge('sectorweb/user/logout', false)->execute();
        }

        # SE REDIRECCIONA AL CONTROLADOR BASE
        Response::redirect_back('/', 'refresh');
	}
}
