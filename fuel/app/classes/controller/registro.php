<?php

/**
 * CONTROLADOR REGISTRO
 *
 * @package  app
 * @extends  Controller_Base
 */
class Controller_Registro extends Controller_Base
{
	/**
	 * BEFORE
	 *
	 * REVISA SI EL USUARIO TIENE UNA SESION,
	 * SI EXISTE REDIRECCIONA A REGISTRO
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
	 * MUESTRA LA PAGINA DE REGISTRO
	 *
	 * @access  public
	 * @return  Void
	 */
	public function action_index()
	{
		# SE INICIALIZA EL ARREGLO DATA
        $data       = array();
		
		

		# SE INICIALIZA LA VARIABLE SECRET_KEY 
		$secret_key = '6LdfrJojAAAAAPb2k61uc9ALmqIBc4HeVFSL_lTK';

        # SE DECLARA EL ARREGLO QUE CONTIENE LAS CLASES HTML DE LOS CAMPOS DEL FORM
        $classes = array(
            'name'             => null,
            'last_name'        => null,
            'username'         => null,
            'email'            => null,
            'password'         => null,
            'confirm_password' => null,
        );

		# SI SE UTILIZO EL METODO POST
		if(Input::method() == 'POST')
		{
			# SE CREA LA VALIDACION DE LOS CAMPOS
			$val = Validation::forge('sign_up');
			$val->add_callable('rules');

            $val->add_field('name', 'Nombres', 'required|min_length[1]|max_length[255]');
            $val->add_field('last_name', 'Apellidos', 'required|min_length[1]|max_length[255]');
			$val->add_field('email', 'Correo electrónico', 'required|valid_email');
			$val->add_field('username', 'Usuario', 'required|min_length[1]|max_length[255]|valid_string[alpha,numeric]');
			$val->add_field('password', 'Contraseña', 'required|min_length[6]');
			$val->add_field('confirm_password', 'Confirmar contraseña', 'required|match_field[password]');
			$val->add_field('g-recaptcha-response', 'reCaptcha', 'required|min_length[1]');

			# SI NO HAY NINGUN PROBLEMA CON LA VALIDACION
			if($val->run())
			{
				# SE REALIZA LA PETICION A RECAPTCHA
				$response = file_get_contents('https://google.com/recaptcha/api/siteverify?secret='.$secret_key.'&response='.$val->validated('g-recaptcha-response').'&remoteip='.Input::ip());

				# SE CONVIERTE EN ARREGLO LA RESPUESTA
				$recaptcha = json_decode($response, true);

				# SI EL RECAPTCHA ES VALIDO
				if($recaptcha['success'])
				{
					try
					{
						# SE REGISTRA EL USUARIO EN LA BASE DE DATOS
						$user = Auth::instance()->create_user(
							$val->validated('username'),
							$val->validated('password'),
							$val->validated('email'),
							1, // GRUPO
							array(
								'connected' => false,
								'banned'    => false
							)
						);

						# SI SE ALMACENO EL USUARIO CORRECTAMENTE
						if($user)
						{
	                        # SE CREA UN ARREGLO CON LOS DATOS DEL USUARIO
	                        $request = array(
	                            'user_id'   => $user,
								'type_id'   => 1,
	                            'name'      => $val->validated('name'),
	                            'last_name' => $val->validated('last_name'),
								'codigosap' => '',
	                            'phone'     => ''
	                        );

							# SE CREA EL MODELO DE CUSTOMER
	                        $customer = Model_Customer::set_new_record($request);

	                        # SE EJECUTA LOGIN DEL MODULO DE USER
	                        $response = Request::forge('sectorweb/user/login', false)->execute(array($val->validated('username'), $val->validated('password')))->response->body;

	                        switch ($response) {
	                            # USUARIO VALIDO Y LOGEADO
	                            case 'ok':

	                                # SE ESTABLECE EL ERROR EN UNA SESION FLASH
	                                Session::set_flash('success', '<h4 class="alert-heading">¡Bien hecho!</h4><p>Tu cuenta ha sido creada correctamente.</p><hr><p>Te presentamos el panel de usuario donde podrás '.Html::anchor('mi_cuenta/direcciones', 'gestionar tus direcciones de envío', array('class' => 'alert-link', 'title' => 'Gestionar direcciones')).', '.Html::anchor('mi_cuenta/pedidos', 'ver tu historial de pedidos', array('class' => 'alert-link', 'title' => 'Historial de pedidos')).' y '.Html::anchor('mi_cuenta/editar', 'cambiar la información de tu cuenta.</p>', array('class' => 'alert-link', 'title' => 'Editar perfil')));

	                                # SE REDIRECCIONA AL DASHBOARD
	                                Response::redirect('mi-cuenta');
	                            break;

	                            default:
	                                # SE ESTABLECE EL ERROR EN UNA SESION FLASH
	                                Session::set_flash('error', '<p>Algo inesperado ha ocurrido, por favor recarga la página.</p>');

	                                # SE REDIRECCIONA AL DASHBOARD
	                                Response::redirect('registro');
	                            break;
	                        }

	                        # SE ALMACENA EL MENSAJE DE ÉXITO EN LA SESIÓN
	                        Session::set_flash('success', '<p>Tu cuenta ha sido creada, ahora puedes iniciar sesión</p>');

	                        # SE REDIRECCIONA AL LOGIN
	                        Response::redirect('iniciar-sesion');
	                    }
	                    else
	                    {
	                        # SE ESTABLECE EL MENSAJE DE ERROR
	                        Session::set_flash('error', '<p>¡Oops! Algo inesperado ha ocurrido, inténtalo de nuevo.</p>');
	                    }
					}
					catch(\SimpleUserUpdateException $e)
					{
						if($e->getMessage() == 'Username already exists')
						{
							# SE ESTABLECE EL MENSAJE DE ERROR
	                        Session::set_flash('error', '<p>El nombre de usuario <b>'.$val->validated('username').'</b> no está disponible, por favor intenta con uno diferente.</p>');

	                        # SE INDICA LA CLASSE DE ERROR EN EL CAMPO
	                        $classes['username'] = 'is-invalid';
						}
						else
						{
	                        Session::set_flash('error', '<p>Ya existe una cuenta asociada al correo <b>'.$val->validated('email').'.</b> Por favor intenta con uno diferente.</p>');

	                        # SE INDICA LA CLASSE DE ERROR EN EL CAMPO
	                        $classes['email'] = 'is-invalid';
						}

						# SE ALMACENA LA INFORMACION DEL FORMULARIO
						$data['name']      = Input::post('name');
						$data['last_name'] = Input::post('last_name');
						$data['username']  = Input::post('username');
						$data['email']     = Input::post('email');
					}
				}
				else
				{
					# SE ESTABLECE EL MENSAJE DE ERROR
					Session::set_flash('error', '<p>No se pudo validar el captcha, por favor vuelve a intentarlo.</p>');

					# SE ALMACENA LA INFORMACION DEL FORMULARIO
	                $data['name']      = Input::post('name');
	                $data['last_name'] = Input::post('last_name');
					$data['username']  = Input::post('username');
	                $data['email']     = Input::post('email');
				}
			}
			else
			{
                # SE ENVIAN LOS ERRORES
                $data['errors'] = $val->error();

                # SE ALMACENAN LAS CLASES HTML DE LOS CAMPOS VALIDADOS
                $classes = array(
                    'name'             => (($val->error('name'))) ? 'is-invalid': 'is-valid',
                    'last_name'        => (($val->error('last_name'))) ? 'is-invalid': 'is-valid',
                    'username'         => (($val->error('username'))) ? 'is-invalid': 'is-valid',
                    'email'            => (($val->error('email'))) ? 'is-invalid':    'is-valid',
                    'password'         => (($val->error('password'))) ? 'is-invalid': 'is-valid',
                    'confirm_password' => (($val->error('confirm_password'))) ? 'is-invalid': 'is-valid'
                );

				# SI EL RECAPTACHA ESTA VACIO
				if($val->validated('g-recaptcha-response') == '')
				{
					# SE ESTABLECE EL MENSAJE DE ERROR
					Session::set_flash('error', '<p>Hemos encontrado errores en el formulario, intenta nuevamente.<br>Es necesario validar que no eres un robot.</p>');
				}
				else
				{
					# SE ESTABLECE EL MENSAJE DE ERROR
					Session::set_flash('error', '<p>Hemos encontrado errores en el formulario, intenta nuevamente.</p>');
				}

                # SE ALMACENA LA INFORMACION DEL FORMULARIO
                $data['name']      = Input::post('name');
                $data['last_name'] = Input::post('last_name');
				$data['username']  = Input::post('username');
                $data['email']     = Input::post('email');
			}
        }

        # SE ALMACENA LA INFORMACION DEL FORMULARIO
        $data['classes'] = $classes;

		# SE CARGA LA VISTA DE REGISTRO
		$this->template->title       = 'Registro | Natura y Mas';
		$this->template->description = 'Registro de usuarios en Natura y Mas.';
		$this->template->content     = View::forge('registro/index', $data, false);
	}
}
