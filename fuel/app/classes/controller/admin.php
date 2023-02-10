<?php

/**
 * CONTROLADOR ADMIN
 *
 * @package  app
 * @extends  Controller_Admin
 */
class Controller_Admin extends Controller_Baseadmin
{
	public $template = 'admin/template';

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

		# SI EL USUARIO NO ES ADMINISTRADOR
		if((!Auth::member(100) and !Auth::member(50)) and Request::active()->action != 'login')
		{
			# SE DESTRUYE SU SESION
			Session::destroy();

			# SE REDIRECCIONA AL CONTROLADOR BASE
			Response::redirect('admin/login');
		}
	}


	/**
	 * LOGIN
	 *
	 * COMPRUEBA EL NOMBRE DE USUARIO Y LA CONTRASEÑA COTEJANDO EN LA BASE DE DATOS,
	 * SI EXISTE CREA LA SESION DEL ADMINISTRADOR Y REDIRECCIONA AL DASHBOARD
	 *
	 * @access  public
	 * @return  Void
	 */
	public function action_login()
	{
		# SI ESTA LOGUEADO SE REDIRECCIONA AL DASHBOARD
		Auth::check() and Response::redirect('admin');

		# SE INICIALIZA EL ARREGLO DATA
		$data = array();

		

		# SE INICIALIZA LA VARIABLE SECRET_KEY 
		$secret_key = '6LdfrJojAAAAAPb2k61uc9ALmqIBc4HeVFSL_lTK';

		# SE INICIALIZA LA VARIABLE USERNAME
		$username = '';

		# SI SE UTILIZO EL METODO POST
		if(Input::method() == 'POST')
		{
            # SE OBTIENEN LOS DATOS ENVIADOS POR POST
            $username    = Input::post('username');
            $password    = Input::post('password');
			$rememberme  = Input::post('rememberme');
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
		            $response = Request::forge('sectorweb/admin/login', false)
					->execute(array($username, $password, $rememberme))
					->response
					->body;

					# DEPENDIENDO DE LA RESPUESTA
		            switch($response)
					{
		                # USUARIO VALIDO Y LOGEADO
		                case 'ok':
		                    # SE REDIRECCIONA AL DASHBOARD
		                    Response::redirect('admin');
		                break;

		                # USUARIO INVALIDO
		                case 'ivalid_user':
		                    # SE ESTABLECE EL MENSAJE DE ERROR
		                    Session::set_flash('error', '<p>Este usuario no es válido.</p');
		                break;

		                # USERNAME Y PASSWORD INCORRECTOS
		                case 'invalid_credentials':
		                    # SE ESTABLECE EL MENSAJE DE ERROR
		                    Session::set_flash('error', '<p>Nombre de usuario o contraseña incorrectos.</p>');
		                break;

		                default:
		                    # SE ESTABLECE EL MENSAJE DE ERROR
		                    Session::set_flash('error', '<p>Algo inesperado ha ocurrido, por favor recarga la página.</p>');
		                break;
		            }
				}
				else
				{
					# SE ESTABLECE EL MENSAJE DE ERROR
					Session::set_flash('error', '<p>No se pudo validar el captcha, por favor vuelve a intentarlo.</p>');
				}
			}
			else
			{
				# SE ESTABLECE EL MENSAJE DE ERROR
				Session::set_flash('error', '<p>Es necesario validar que no eres un robot.</p>');
			}
		}

		# SE ALMACENA LA INFORMACION DEL FORMULARIO
        $data['username'] = $username;

		# SE CARGA LA VISTA
		 return View::forge('admin/login', array('data' => $data));
	}


	// /**
	//  * LOGIN
	//  *
	//  * COMPRUEBA EL NOMBRE DE USUARIO Y LA CONTRASEÑA COTEJANDO EN LA BASE DE DATOS,
	//  * SI EXISTE CREA LA SESION DEL ADMINISTRADOR Y REDIRECCIONA AL DASHBOARD
	//  *
	//  * @access  public
	//  * @return  Void
	//  */
	// public function action_login()
	// {
	// 	# SI ESTA LOGUEADO SE REDIRECCIONA AL DASHBOARD
	// 	Auth::check() and Response::redirect('admin');
	//
	// 	# SE INICIALIZA EL ARREGLO DATA
	// 	$data = array();
	//
	// 	# SE INICIALIZA LA VARIABLE USERNAME
	// 	$username = '';
	//
	// 	# SI SE UTILIZO EL METODO POST
	// 	if(Input::method() == 'POST')
	// 	{
    //         # SE OBTIENEN LOS DATOS ENVIADOS POR POST
    //         $username   = Input::post('username');
    //         $password   = Input::post('password');
	// 		$rememberme = Input::post('rememberme');
	//
	// 		# SE EJECUTA LOGIN DEL MODULO DE USER
	// 		$response = Request::forge('sectorweb/admin/login', false)
	// 		->execute(array($username, $password, $rememberme))
	// 		->response
	// 		->body;
	//
	// 		# DEPENDIENDO DE LA RESPUESTA
	// 		switch($response)
	// 		{
	// 			# USUARIO VALIDO Y LOGEADO
	// 			case 'ok':
	// 				# SE REDIRECCIONA AL DASHBOARD
	// 				Response::redirect('admin');
	// 			break;
	//
	// 			# USUARIO INVALIDO
	// 			case 'ivalid_user':
	// 				# SE ESTABLECE EL MENSAJE DE ERROR
	// 				Session::set_flash('error', '<p>Este usuario no es válido.</p');
	// 			break;
	//
	// 			# USERNAME Y PASSWORD INCORRECTOS
	// 			case 'invalid_credentials':
	// 				# SE ESTABLECE EL MENSAJE DE ERROR
	// 				Session::set_flash('error', '<p>Nombre de usuario o contraseña incorrectos.</p>');
	// 			break;
	//
	// 			default:
	// 				# SE ESTABLECE EL MENSAJE DE ERROR
	// 				Session::set_flash('error', '<p>Algo inesperado ha ocurrido, por favor recarga la página.</p>');
	// 			break;
	// 		}
	// 	}
	//
	// 	# SE ALMACENA LA INFORMACION DEL FORMULARIO
    //     $data['username'] = $username;
	//
	// 	# SE CARGA LA VISTA
	// 	 return View::forge('admin/login', array('data' => $data));
	// }


	/**
	 * INDEX
	 *
	 * CARGA LA VISTA DEL DASHBOARD
	 *
	 * @access  public
	 * @return  void
	 */
	public function action_index()
	{
		# SE INICIALIZAN LAS VARIABLES
		$data        = array();
		$days        = array('Domingo','Lunes','Martes','Miércoles','Jueves','Viernes','Sábado');
		$months      = array('Enero','Febrero','Marzo','Abril','Mayo','Junio','Julio','Agosto','Septiembre','Octubre','Noviembre','Diciembre');
		$slides_info = array();
		$sales_info  = array();
		$admins_info = array();
		$users_info  = array();

		# SE ALMACENA LA FECHA DE HOY
		$date = $days[date('w', time())].' '.date('d', time()).' de '.$months[date('n', time())-1]. ' del '.date('Y', time());

		# SE OBTIENE LA INFORMACION DE LOS MODELOS
		$sales_count  = Model_Sale::query()->where('status', '>=', 1);
		$users_count  = Model_User::query()->where('group', '=', 1);
		$admins_count = Model_User::query()->where('group', 100)->or_where('group', 50);
		$slides_count = Model_Slide::query();

		# SE BUSCA LA INFORMACION A TRAVES DEL MODELO
		$sales = Model_Sale::query()
		->related('customer')
		->where('status', '>=', 1)
		->order_by('sale_date', 'desc')
		->limit(10)
		->get();

		# SI SE OBTIENE INFORMACION
		if(!empty($sales))
		{
			# SE RECORRE ELEMENTO POR ELEMENTO
			foreach($sales as $sale)
			{
				# SE ALMACENA LA INFORMACION
				$sales_info[] = array(
					'id'       => $sale->id,
					'customer' => $sale->customer->name.' '.$sale->customer->last_name,
					'email'    => $sale->customer->user->email,
                    'type'     => ($sale->status == 2) ? $sale->payment->type->name.' (Por revisar)' : $sale->payment->type->name,
					'total'    => '$'.number_format($sale->total, '2', '.', ','),
					'sale_date' => date('d/m/Y - H:i', $sale->sale_date)
				);
			}
		}

		# SE BUSCA LA INFORMACION A TRAVES DEL MODELO
		$admins = Model_User::query()
		->where_open()
		->where('group', 100)
		->or_where('group', 50)
		->where_close()
		->order_by('id', 'desc')
		->limit(10)
		->get();

		# SI SE OBTIENE INFORMACION
		if(!empty($admins))
		{
			# SE RECORRE ELEMENTO POR ELEMENTO
			foreach($admins as $admin)
			{
				# SE DESERIALIZAN LOS CAMPOS EXTRAS
				$status = unserialize($admin->profile_fields);

				# SE ALMACENA LA INFORMACION
				$admins_info[] = array(
					'full_name' => $status['full_name'],
					'email'     => $admin->email
				);
			}
		}

		# SE BUSCA LA INFORMACION A TRAVES DEL MODELO
		$slides = Model_Slide::query()
		->order_by('id', 'desc')
		->limit(5)
		->get();

		# SI SE OBTIENE INFORMACION
		if(!empty($slides))
		{
			# SE RECORRE ELEMENTO POR ELEMENTO
			foreach($slides as $slide)
			{
				# SE ALMACENA LA INFORMACION
				$slides_info[] = array(
					'title' => $slide->url,
					'type'  => 'Index'
				);
			}
		}

		# SE BUSCA LA INFORMACION A TRAVES DEL MODELO
		$users = Model_User::query()
		->where_open()
		->where('group', 1)
		->where_close()
		->order_by('id', 'desc')
		->limit(7)
		->get();

		# SI SE OBTIENE INFORMACION
		if(!empty($users))
		{
			# SE RECORRE ELEMENTO POR ELEMENTO
			foreach($users as $user)
			{
				# SE DESERIALIZAN LOS CAMPOS EXTRAS
				$status = unserialize($user->profile_fields);

				# SE ALMACENA LA INFORMACION
				$users_info[] = array(
					'username' => $user->username,
					'connected' => ($status['connected']) ? 'Conectado' : 'Desconectado',
					'email'     => $user->email,
					'updated_at' => date('d/m/Y - H:i', $user->updated_at)
				);
			}
		}


		# SE ALMACENA LA INFORMACION PARA LA VISTA
		$data['date']         = $date;
		$data['sales_count']  = $sales_count->count();
		$data['admins_count'] = $admins_count->count();
		$data['users_count']  = $users_count->count();
		$data['slides_count'] = $slides_count->count();
		$data['admins']       = $admins_info;
		$data['users']        = $users_info;
		$data['sales']        = $sales_info;
		$data['slides']       = $slides_info;

		# SE CARGA LA VISTA
		$this->template->title   = 'Dashboard';
		$this->template->content = View::forge('admin/dashboard', $data);
	}


	/**
	 * LOGOUT
	 *
	 * CIERRA LA SESION DEL ADMINISTRADOR
	 *
	 * @access  public
	 * @return  void
	 */
	public function action_logout()
	{
		# SE EJECUTA LOGIN DEL MODULO DE USER
		$response = Request::forge('sectorweb/admin/logout', false)
		->execute();

		# SE REDIRECCIONA AL USUARIO
		Response::redirect('/admin');
	}
}
