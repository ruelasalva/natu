<?php

/**
 * CONTROLADOR INICIAR SESION
 *
 * @package  app
 * @extends  Controller_Base
 */
class Controller_Iniciar_Sesion extends Controller_Base
{
	/**
	 * BEFORE
	 *
	 * REVISA SI EL USUARIO TIENE UNA SESION,
	 * SI EXISTE REDIRECCIONA A MI CUENTA
	 *
	 * @return Void
	 */
	public function before()
	{
		# REQUERIDA PARA EL TEMPLATING
		parent::before();

		# SI ESTA LOGUEADO SE REDIRECCIONA AL DASHBOARD
		if(Auth::check())
		{
			Response::redirect_back('/', 'refresh');
		}
	}


	/**
	 * INDEX
	 *
	 * MUESTRA LA PAGINA DE INDEX
	 *
	 * @access  public
	 * @return  Void
	 */
	public function action_index()
	{
		# SE INICIALIZA EL ARREGLO DATA
		$data = array();

		

		# SE INICIALIZA LA VARIABLE SECRET_KEY natura.COM.MX
		$secret_key = '6LdfrJojAAAAAPb2k61uc9ALmqIBc4HeVFSL_lTK';

		# SE DECLARA EL ARREGLO QUE CONTIENE LAS CLASES HTML DE LOS CAMPOS DEL FORM
		$classes = array(
			'username' => null,
			'password' => null
		);

		# SI SE UTILIZO EL METODO POST
		if(Input::method() == 'POST')
		{
			# SE OBTIENEN LOS DATOS ENVIADOS POR POST
			$username    = Input::post('username');
			$password    = Input::post('password');
			$g_recaptcha = Input::post('g-recaptcha-response');

			# SI EL RECAPCTA NO ESTA VACIO
			if($g_recaptcha != '')
			{
				# SE REALIZA LA PETICION A RECAPTCHA
				$response_recaptcha = file_get_contents('https://google.com/recaptcha/api/siteverify?secret='.$secret_key.'&response='.$g_recaptcha.'&remoteip='.Input::ip());

				# SE CONVIERTE EN ARREGLO LA RESPUESTA
				$recaptcha = json_decode($response_recaptcha, true);

				# SI EL RECAPTCHA ES VALIDO
				if($recaptcha['success'])
				{
		            # SE EJECUTA LOGIN DEL MODULO DE USER
		            $response = Request::forge('sectorweb/user/login', false)->execute(array($username, $password))->response->body;

		            switch ($response) {
		                # USUARIO VALIDO Y LOGEADO
		                case 'ok':
		                    # SE REDIRECCIONA AL DASHBOARD
		                    Response::redirect_back('/', 'refresh');
		                break;

		                # USUARIO INVALIDO
		                case 'ivalid_user':
		                    # SE ALMACENAN LAS CLASES HTML DE LOS CAMPOS VALIDADOS
		                    $classes = array(
		                        'username' => 'is-invalid',
		                        'password' => 'is-invalid'
		                    );

		                    # SE ESTABLECE EL ERROR EN UNA SESION FLASH
		                    Session::set_flash('error', '<p>Este usuario no es válido.</p>');
		                break;

		                # USERNAME Y PASSWORD INCORRECTOS
		                case 'invalid_credentials':
		                    # SE ALMACENAN LAS CLASES HTML DE LOS CAMPOS VALIDADOS
		                    $classes = array(
		                        'username' => 'is-invalid',
		                        'password' => 'is-invalid'
		                    );

		                    # SE ESTABLECE EL ERROR EN UNA SESION FLASH
		                    Session::set_flash('error', '<p>Nombre de usuario o contraseña incorrectos.</p>');
		                break;

		                default:
		                    # SE ESTABLECE EL ERROR EN UNA SESION FLASH
		                    Session::set_flash('error', '<p>Algo inesperado ha ocurrido, por favor recarga la página.</p>');
		                break;
		            }
				}
				else
				{
					# SE ESTABLECE EL ERROR EN UNA SESION FLASH
					Session::set_flash('error', '<p>No se pudo validar el captcha, por favor vuelve a intentarlo.</p>');
				}
			}
			else
			{
				# SE ESTABLECE EL ERROR EN UNA SESION FLASH
				Session::set_flash('error', '<p>Es necesario validar que no eres un robot.</p>');
			}
		}

		# SE ALMACENA LA INFORMACION DEL FORMULARIO
        $data['classes'] = $classes;

		# SE CARGA LA VISTA DE INICIAR SESION
		$this->template->title       = 'Iniciar sesión | Distribuidora Sajor';
		$this->template->description = 'Inicio de sesión en Distribuidora Sajor.';
		$this->template->content     = View::forge('iniciar_sesion/index', $data, false);
	}


	// /**
	//  * INDEX
	//  *
	//  * MUESTRA LA PAGINA DE INDEX
	//  *
	//  * @access  public
	//  * @return  Void
	//  */
	// public function action_index()
	// {
	// 	# SE INICIALIZA EL ARREGLO DATA
	// 	$data = array();
	//
	// 	# SE DECLARA EL ARREGLO QUE CONTIENE LAS CLASES HTML DE LOS CAMPOS DEL FORM
	// 	$classes = array(
	// 		'username' => null,
	// 		'password' => null
	// 	);
	//
	// 	# SI SE UTILIZO EL METODO POST
	// 	if(Input::method() == 'POST')
	// 	{
	// 		# SE OBTIENEN LOS DATOS ENVIADOS POR POST
	// 		$username    = Input::post('username');
	// 		$password    = Input::post('password');
	//
	// 		# SE EJECUTA LOGIN DEL MODULO DE USER
	// 		$response = Request::forge('sectorweb/user/login', false)->execute(array($username, $password))->response->body;
	//
	// 		switch ($response) {
	// 			# USUARIO VALIDO Y LOGEADO
	// 			case 'ok':
	// 				# SE REDIRECCIONA AL DASHBOARD
	// 				Response::redirect_back('/', 'refresh');
	// 			break;
	//
	// 			# USUARIO INVALIDO
	// 			case 'ivalid_user':
	// 				# SE ALMACENAN LAS CLASES HTML DE LOS CAMPOS VALIDADOS
	// 				$classes = array(
	// 					'username' => 'is-invalid',
	// 					'password' => 'is-invalid'
	// 				);
	//
	// 				# SE ESTABLECE EL ERROR EN UNA SESION FLASH
	// 				Session::set_flash('error', '<p>Este usuario no es válido.</p>');
	// 			break;
	//
	// 			# USERNAME Y PASSWORD INCORRECTOS
	// 			case 'invalid_credentials':
	// 				# SE ALMACENAN LAS CLASES HTML DE LOS CAMPOS VALIDADOS
	// 				$classes = array(
	// 					'username' => 'is-invalid',
	// 					'password' => 'is-invalid'
	// 				);
	//
	// 				# SE ESTABLECE EL ERROR EN UNA SESION FLASH
	// 				Session::set_flash('error', '<p>Nombre de usuario o contraseña incorrectos.</p>');
	// 			break;
	//
	// 			default:
	// 				# SE ESTABLECE EL ERROR EN UNA SESION FLASH
	// 				Session::set_flash('error', '<p>Algo inesperado ha ocurrido, por favor recarga la página.</p>');
	// 			break;
	// 		}
	// 	}
	//
	// 	# SE ALMACENA LA INFORMACION DEL FORMULARIO
    //     $data['classes'] = $classes;
	//
	// 	# SE CARGA LA VISTA DE INICIAR SESION
	// 	$this->template->title       = 'Iniciar sesión | Distribuidora Sajor';
	// 	$this->template->description = 'Inicio de sesión en Distribuidora Sajor.';
	// 	$this->template->content     = View::forge('iniciar_sesion/index', $data, false);
	// }
}
