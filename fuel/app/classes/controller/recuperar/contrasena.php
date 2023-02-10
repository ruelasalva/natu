<?php

/**
 * CONTROLADOR RECUPERAR CONTRASENA
 *
 * @package  app
 * @extends  Controller_Base
 */
class Controller_Recuperar_Contrasena extends Controller_Base
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
	}


	/**
	 * INDEX
	 *
	 * MUESTRA LA PAGINA DE RECUPERAR CONTRASENA
	 *
	 * @access  public
	 * @return  Void
	 */
	public function action_index()
	{
		# SE INICIALIZA EL ARREGLO DATA
		$data = array();

		# SE DECLARA EL ARREGLO QUE CONTIENE LAS CLASES HTML DE LOS CAMPOS DEL FORM
		$classes = array(
			'email' => null
		);

		# SI SE UTILIZO EL METODO POST
		if(Input::method() == 'POST')
		{
			# SE CREA LA VALIDACION DE LOS CAMPOS
			$val = Validation::forge('new_password');
			$val->add_callable('rules');

			# SE AGREGAN LAS REGLAS DE VALIDACION A LOS CAMPOS
			$val->add_field('email', 'Correo electrónico', 'required|valid_email');

			# SI NO HAY NINGUN PROBLEMA CON LA VALIDACION
			if($val->run())
			{
				# SE OBTIENE LA INFORMACION DEL USUARIO
				$user = Model_User::find_by_email($val->validated('email'));

				# SI SE OBTUVO INFORMACION DEL USUARIO
				if($user)
				{
                    # SI EL USUARIO ES UN CLIENTE
                    if($user->group == 1)
                    {
                        # SE CREA EL HASH
                        $hash = Str::random('alnum', 16);

                        # SE GUARDA LA INFORMACION
                        $data_to_update = array(
                            'token' => $hash
                        );

                        # SE ACTUALIZA LA INFORMACION DEL USUARIO EN LA BASE DE DATOS
                        $user_update = Auth::instance()->update_user($data_to_update, $user->username);

                        # SI SE REALIZO EL CAMBIO
                        if($user_update)
                        {
                            # SE ALMACENA LA INFORMACION DEL USUARIO
                            $customer_name = ($user->customer->name != '') ? $user->customer->name.' '.$user->customer->last_name : 'Usuario de Distribuidora Sajor';
                            $mail = $user->email;

                            # SE ALMACENA EL MENSAJE DEL CORREO
                            $data['body'] =
                            '<tr>
                                <td style="background-color: #ffffff;">
                                    <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%">
                                        <tr>
                                            <td style="padding: 30px; font-family: sans-serif; font-size: 15px; line-height: 20px; color: #555555;">
                                                <h1 style="margin: 0 0 10px 0; font-family: sans-serif; font-size: 25px; line-height: 30px; color: #333333; font-weight: normal;">Cambio de contraseña.</h1>
                                                <p style="margin: 0;">Se solicitó el cambio de contraseña para el correo: <strong>'.$user->email.'</strong>, para restablecerla haz click en el enlace debajo.</p>
                                                <p>
                                                    <strong style="display: block;">Enlace:</strong>
                                                    <span style="display: block; margin-bottom: 15px"><a href="'.Uri::base(false).'recuperar-contrasena/nueva-contrasena/'.$user->id.'/'.$hash.'">'.Uri::base(false).'recuperar-contrasena/nueva-contrasena/'.$user->id.'/'.$hash.'</a></span>
                                                </p>
                                            </td>
                                        </tr>
                                    </table>
                                </td>
                            </tr>';

                            # SE ENVIA UN EMAIL CON EL MENSAJE
                            $email = Email::forge();
                            $email->from('contacto@sectorweb.mx', 'Distribuidora Sajor');
                            $email->to($mail, $customer_name);
                            $email->subject('Distribuidora Sajor - Cambio de contraseña');
                            $email->html_body(View::forge('email_templates/default', $data, false), false);

                            try{
                                # SE ENVIA EL EMAIL
                                if($email->send())
                                {
                                    # SE ESTABLECE EL MENSAJE DE EXITO
                                    Session::set_flash('success', '<p><b>!Listo!</b> Verifica tu correo para restablecer tu contraseña.</p>');
                                }
                                else
                                {
                                    # SE ESTABLECE EL ERROR EN UNA SESION FLASH
                                    Session::set_flash('error', '<p>No es posible enviar el correo en este momento. Intente más tarde.</p>');
                                }
                            }
                            catch(\EmailSendingFailedException $e)
                            {
                                # SE ESTABLECE EL ERROR
                                Session::set_flash('error', '<p>Algo inesperado ha ocurrido.</p>');
                            }
                            catch(\EmailValidationFailedException $e)
                            {
                                # SE ESTABLECE EL ERROR
                                Session::set_flash('error', '<p>Email(s) inválido(s).</p>');
                            }
                        }
                        else
                        {
                            # SE ALMACENAN LAS CLASES HTML DE LOS CAMPOS VALIDADOS
                            $classes['email'] = 'is-invalid';

                            # SE ESTABLECE EL ERROR EN UNA SESION FLASH
                            Session::set_flash('error', '<p>Algo inesperado ha ocurrido.</p>');
                        }
                    }
                    else
                    {
                        # SE ESTABLECE EL ERROR EN UNA SESION FLASH
					    Session::set_flash('error', '<p>Este usuario no tiene permitido realizar esta acción.</p>');
                    }
				}
				else
				{
					# SE ALMACENAN LAS CLASES HTML DE LOS CAMPOS VALIDADOS
					$classes['email'] = 'is-invalid';

					# SE ESTABLECE EL ERROR EN UNA SESION FLASH
					Session::set_flash('error', '<p>El correo no existe en la base de datos.</p>');
				}
			}
			else
			{
                $data['errors'] = $val->error();

				# SE ALMACENAN LAS CLASES HTML DE LOS CAMPOS VALIDADOS
				$classes['email'] = 'is-invalid';

				# SE ESTABLECE EL ERROR EN UNA SESION FLASH
				Session::set_flash('error', '<p>Ingresa un correo válido.</p>');
			}
		}

		# SE ALMACENA LA INFORMACION DEL FORMULARIO
		$data['classes'] = $classes;

		# SE CARGA LA VISTA DE RECUPERAR CONTRASENA
		$this->template->title       = 'Recuperar contraseña | Distribuidora Sajor';
		$this->template->description = 'Recupera la contraseña de cuenta en Distribuidora Sajor.';
		$this->template->content     = View::forge('recuperar_contrasena/index', $data, false);
	}


	/**
	 * NUEVA CONTRASENA
	 *
	 * CAMBIA LA CONTRASENA DE UN USUARIO
	 *
	 * @access  public
	 * @return  Void
	 */
	public function action_nueva_contrasena($id, $token)
	{
		# SE INICIALIZA EL ARREGLO DATA
		$data = array();

		# SE DECLARA EL ARREGLO QUE CONTIENE LAS CLASES HTML DE LOS CAMPOS DEL FORM
		$classes = array(
			'new_password' => null,
			'confirm_password' => null,
		);

        # SE BUSCA EL USUARIO
		$user = Model_User::find_by_id($id);

        # SI NO SE ENCUENTRA
        if($user ===  null)
        {
            # SE REDIRECCIONA A INICIO
            Response::redirect('/');
        }

        # SI EL USUARIO NO ES UN CLIENTE
        if($user->group != 1)
        {
            # SE REDIRECCIONA A INICIO
            Response::redirect('/');
        }

        # SE DESERIALIZAN LOS CAMPOS EXTRAS
        $profile_fields = unserialize($user->profile_fields);

        # SI EL TOKEN RECIBIDO ES DIFERENTE AL DE LA BD
        if($profile_fields['token'] == '' or $profile_fields['token'] != $token)
        {
            # SE REDIRECCIONA A INICIO
            Response::redirect('/');
        }

		# SI SE UTILIZO EL METODO POST
		if(Input::method() == 'POST')
		{
			# SE CREA LA VALIDACION DE LOS CAMPOS
			$val = Validation::forge('reset_password');
			$val->add_callable('rules');

			# SE AGREGAN LAS REGLAS DE VALIDACION A LOS CAMPOS
			$val->add_field('new_password', 'Nueva contraseña', 'required|min_length[6]');
            $val->add_field('confirm_password', 'Confirmar contraseña', 'required|match_field[new_password]');

			# SI NO HAY NINGUN PROBLEMA CON LA VALIDACION
			if($val->run())
			{
                $reset_password = Auth::reset_password($user->username);

                # SE GUARDA LA INFORMACION
                $data_to_update = array(
                    'old_password' => $reset_password,
                    'password'     => $val->validated('new_password'),
                    'token'        => ''
                );

                # SE ACTUALIZA LA INFORMACION DEL USUARIO EN LA BASE DE DATOS
                $user_update = Auth::instance()->update_user($data_to_update, $user->username);

                # SI SE ALMACENA LA INFORMACION
                if($user_update)
                {
                    # SE ESTABLECE EL MENSAJE EXITO
                    Session::set_flash('success', '<b>!Listo!</b> Ahora puedes iniciar sesión con tu nueva contraseña.');

                    # SE REDIRECCIONA A INICIAR SESION
                    Response::redirect('iniciar-sesion');
                }
                else
                {
                    # SE ESTABLECE EL ERROR A LA HORA DE ENVIAR EL FORMULARIO
                    Session::set_flash('error', '<p>No se pudo actualizar la información del usuario.</p>');
                }

			}
			else
			{
				# SE ENVIAN LOS ERRORES
				$data['errors'] = $val->error();

				# SE ALMACENAN LAS CLASES HTML DE LOS CAMPOS VALIDADOS
				$classes = array(
					'new_password'     => (($val->error('new_password'))) ? 'is-invalid': 'is-valid',
					'confirm_password' => (($val->error('confirm_password'))) ? 'is-invalid': 'is-valid'
                );

				# SE ESTABLECE EL ERROR A LA HORA DE ENVIAR EL FORMULARIO
				Session::set_flash('error', '<p>Las contraseñas no coinciden.</p>');
			}
		}

		# SE ALMACENA LA INFORMACION DEL FORMULARIO
		$data['classes']  = $classes;
		$data['username'] = $user->username;
		$data['email']    = $user->email;
		$data['id']       = $user->id;
		$data['token']    = $token;

		# SE CARGA LA VISTA DE RECUPERAR CONTRASENA
		$this->template->title       = 'Nueva contraseña | Distribuidora Sajor';
		$this->template->description = 'Establece la nueva contraseña de tu cuenta en Distribuidora Sajor.';
		$this->template->content     = View::forge('recuperar_contrasena/nueva_contrasena', $data, false);
	}
}
