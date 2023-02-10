<?php

/**
* The Ajax Controller.
*
* A basic controller example.  Has examples of how to set the
* response body and status.
*
* @package  app
* @extends  Controller
*/
class Controller_Admin_Ajax extends Controller_Rest
{
	/**
	* CKEDITOR_IMAGE
	*
	* @access  public
	* @return  Object
	*/
	public function post_ckeditor_image()
	{
		# SE INICIALIZAN LAS VARIABLES
		$response = array();

		# SE CREA LA VALIDACION DE LOS CAMPOS
		$val = Validation::forge('image');
		$val->add_callable('Rules');
		$val->add_field('access_id', 'access_id', 'required|valid_string[numeric]|numeric_min[1]');
		$val->add_field('access_token', 'access_token', 'min_length[1]');
		$val->add_field('file', 'file', 'min_length[1]');

		# SI NO HAY NINGUN PROBLEMA CON LA VALIDACION
		if($val->run())
		{
			# SE BUSCA LA INFORMACION A TRAVES DEL MODELO
			$check_access = Model_User::query()
			->where('id', $val->validated('access_id'))
			->get_one();

			# SI SE OBTIENE INFORMACION
			if(!empty($check_access))
			{
				# SI EL TOKEN ESTA REGISTRADO EN LA BASE DE DATOS
				if(md5($check_access->login_hash) == $val->validated('access_token'))
				{
					# SE OBTIENE LA REFERENCIA DE LA IMAGEN
					$image = $_FILES['file']['name'];

					# SI EL USUARIO SUBE LA IMAGEN
					if(!empty($image))
					{
						# SE ESTABLECE LA CONFIGURACION
						$config = array(
							'auto_process'        => false,
							'path'                => DOCROOT.DS.'assets/uploads',
							'randomize'           => false,
							'auto_rename'         => true,
							'normalize'           => true,
							'normalize_separator' => '-',
							'ext_whitelist'       => array('jpg', 'jpeg', 'png', 'gif'),
							'max_size'            => 20971520,
						);

						# SE INICIALIZA EL PROCESO UPLOAD CON LA CONFIGURACION ESTABLECIDA
						Upload::process($config);

						# SI EL ARCHIVO ES VALIDO
						if(Upload::is_valid())
						{
							# SE SUBE EL ARCHIVO
							Upload::save();

							# SE OBTIENE LA INFORMACION DEL ARCHIVO
							$value = Upload::get_files();

							# SE ALMACENA EL NOMBRE DEL ARCHIVO
							$file = $value[0]['saved_as'];

							# SE AGREGA EL PATH UPLOADS
							Asset::add_path('assets/uploads/', 'uploads');

							# SE BUSCA LAS FOTOS EN EL SERVIDOR
							$file_uploaded = DOCROOT.Asset::find_file($file, 'uploads');

							# SE OBTIENE LA INFORMACION DE LAS MEDIDAS
							$size = Image::sizes($file_uploaded);

							# SE ESTABLECE EL MENSAJE DE EXITO
							$response = array(
								'url' => Uri::base(false).'assets/uploads/'.$file
							);

							# SI HAY UN ARCHIVO ANTERIOR
							if($val->validated('last_file') != '')
							{
								# SI EL ARCHIVO EXISTE
								if(file_exists(DOCROOT.'assets/uploads/'.$val->validated('last_file')))
								{
									# SE ELIMINAN EL ARCHIVO
									File::delete(DOCROOT.'assets/uploads/'.$val->validated('last_file'));
								}
							}
						}
						else
						{
							# SE ESTABLECE EL MENSAJE DE ERROR
							$response = array(
								'error' => array(
									'message' => 'Solo están permitidos las imágenes con extensión .jpg, .jpeg, .png y .gif.'
								)
							);
						}
					}
					else
					{
						# SE ESTABLECE EL MENSAJE DE ERROR
						$response = array(
							'error' => array(
								'message' => 'No se envió ninguna imagen.'
							)
						);
					}
				}
				else
				{
					# SE ESTABLECE EL MENSAJE DE ERROR
					$response = array(
						'error' => array(
							'message' => 'Las credenciales no permiten el acceso al servidor.'
						)
					);
				}
			}
			else
			{
				# SE ESTABLECE EL MENSAJE DE ERROR
				$response = array(
					'error' => array(
						'message' => 'Las credenciales no permiten el acceso al servidor.'
					)
				);
			}
		}
		else
		{
			# SE ESTABLECE EL MENSAJE DE ERROR
			$response = array(
				'error' => array(
					'message' => 'No es posible subir la imagen con la información enviada.'
				)
			);
		}

		# SE ENVIA EL ARREGLO CON LA RESPUESTA
		$this->response($response);
	}

	
	/**
	* IMAGE
	*
	* @access  public
	* @return  Object
	*/
	public function post_image()
	{
		# SE INICIALIZAN LAS VARIABLES
		$msg  = '';
		$file = '';

		# SI ES UNA LLAMADA AJAX
		if(Input::is_ajax())
		{
			# SE CREA LA VALIDACION DE LOS CAMPOS
			$val = Validation::forge('image');
			$val->add_callable('Rules');
			$val->add_field('width', 'width', 'required|valid_string[numeric]|numeric_min[1]');
			$val->add_field('height', 'height', 'required|valid_string[numeric]|numeric_min[1]');
			$val->add_field('last_file', 'last_file', 'min_length[1]');

			# SI NO HAY NINGUN PROBLEMA CON LA VALIDACION
			if($val->run())
			{
				# SE OBTIENE LA REFERENCIA DE LA IMAGEN
				$image = $_FILES['file']['name'];

				# SI EL USUARIO SUBE LA IMAGEN
				if(!empty($image))
				{
					# SE ESTABLECE LA CONFIGURACION
					$config = array(
						'auto_process'  => false,
						'path'          => DOCROOT.DS.'assets/uploads',
						'randomize'     => false,
						'auto_rename'   => true,
						'normalize'     => true,
						'ext_whitelist' => array('jpg', 'jpeg', 'png', 'gif'),
						'max_size'      => 20971520,
						'prefix'        => 'sw_',
					);

					# SE INICIALIZA EL PROCESO UPLOAD CON LA CONFIGURACION ESTABLECIDA
					Upload::process($config);

					# SI EL ARCHIVO ES VALIDO
					if(Upload::is_valid())
					{
						# SE SUBE EL ARCHIVO
						Upload::save();

						# SE OBTIENE LA INFORMACION DEL ARCHIVO
						$value = Upload::get_files();

						# SE ALMACENA EL NOMBRE DEL ARCHIVO
						$file = $value[0]['saved_as'];

						# SE AGREGA EL PATH UPLOADS
						Asset::add_path('assets/uploads/', 'uploads');

						# SE BUSCA LAS FOTOS EN EL SERVIDOR
						$file_uploaded = DOCROOT.Asset::find_file($file, 'uploads');

						# SE OBTIENE LA INFORMACION DE LAS MEDIDAS
						$size = Image::sizes($file_uploaded);

						# SI LA IMAGEN CUMPLE CON LAS MEDIDAS
						if($size->width == $val->validated('width') && $size->height == $val->validated('height'))
						{
							# SE ESTABLECE EL MENSAJE DE EXITO
							$msg = 'ok';

							# SI HAY UN ARCHIVO ANTERIOR
							if($val->validated('last_file') != '')
							{
								# SI EL ARCHIVO EXISTE
								if(file_exists(DOCROOT.'assets/uploads/'.$val->validated('last_file')))
								{
									# SE ELIMINAN EL ARCHIVO
									File::delete(DOCROOT.'assets/uploads/'.$val->validated('last_file'));
								}
							}
						}
						else
						{
							# SI EL ARCHIVO RECIEN SUBIDO EXISTE
							if(file_exists(DOCROOT.'assets/uploads/'.$file))
							{
								# SE ELIMINAN EL ARCHIVO
								File::delete(DOCROOT.'assets/uploads/'.$file);
							}

							# SE LIMPIA LA VARIABLE
							$file = '';

							# SE ESTABLECE EL MENSAJE DE ERROR
							$msg = 'La imagen no tiene las medidas exactas ('.$val->validated('width').' X '.$val->validated('height').' px), por favor vuelve a subirla.';
						}
					}
					else
					{
						# SE ESTABLECE EL MENSAJE DE ERROR
						$msg = 'Solo están permitidos las imágenes con extensión .jpg, .jpeg, .png y .gif.';
					}
				}
				else
				{
					# SE ESTABLECE EL MENSAJE DE ERROR
					$msg = 'No se envió ninguna imagen.';
				}
			}
			else
			{
				# SE ESTABLECE EL MENSAJE DE ERROR
				$msg = 'No es posible subir la imagen con la información enviada.';
			}
		}
		else
		{
			# SE ESTABLECE EL MENSAJE DE ERROR
			$msg = 'La petición no es del tipo AJAX.';
		}

		# SE ENVIA EL ARREGLO CON LA RESPUESTA
		$this->response(array(
			'msg'  => $msg,
			'file' => $file
		));
	}


	/**
	* PRODUCT_IMAGE
	*
	* @access  public
	* @return  Object
	*/
	public function post_product_image()
	{
		# SE INICIALIZAN LAS VARIABLES
		$msg  = '';
		$file = '';

		# SI ES UNA LLAMADA AJAX
		if(Input::is_ajax())
		{
			# SE CREA LA VALIDACION DE LOS CAMPOS
			$val = Validation::forge('image');
			$val->add_callable('Rules');
			$val->add_field('width', 'width', 'required|valid_string[numeric]|numeric_min[1]');
			$val->add_field('height', 'height', 'required|valid_string[numeric]|numeric_min[1]');
			$val->add_field('last_file', 'last_file', 'min_length[1]');

			# SI NO HAY NINGUN PROBLEMA CON LA VALIDACION
			if($val->run())
			{
				# SE OBTIENE LA REFERENCIA DE LA IMAGEN
				$image = $_FILES['file']['name'];

				# SI EL USUARIO SUBE LA IMAGEN
				if(!empty($image))
				{
					# SE ESTABLECE LA CONFIGURACION
					$config = array(
						'auto_process'  => false,
						'path'          => DOCROOT.DS.'assets/uploads',
						'randomize'     => false,
						'auto_rename'   => true,
						'normalize'     => true,
						'ext_whitelist' => array('jpg', 'jpeg', 'png', 'gif'),
						'max_size'      => 20971520,
						'prefix'        => 'sw_',
					);

					# SE INICIALIZA EL PROCESO UPLOAD CON LA CONFIGURACION ESTABLECIDA
					Upload::process($config);

					# SI EL ARCHIVO ES VALIDO
					if(Upload::is_valid())
					{
						# SE SUBE EL ARCHIVO
						Upload::save();

						# SE OBTIENE LA INFORMACION DEL ARCHIVO
						$value = Upload::get_files();

						# SE ALMACENA EL NOMBRE DEL ARCHIVO
						$file = $value[0]['saved_as'];

						# SE AGREGA EL PATH UPLOADS
						Asset::add_path('assets/uploads/', 'uploads');

						# SE BUSCA LAS FOTOS EN EL SERVIDOR
						$file_uploaded = DOCROOT.Asset::find_file($file, 'uploads');

						# SE CREA CREA UNA INSTANCIA A LA CLASE IMAGE
						$thumb = Image::forge();

						# SE CARGA LA CONFIGURACION PREDETERMINADA Y SE CREA EL THUMBNAIL
						$thumb->load($file_uploaded)->preset('thumb_products')->save_pa('thumb_');

						# SE OBTIENE LA INFORMACION DE LAS MEDIDAS
						$size = Image::sizes($file_uploaded);

						# SI LA IMAGEN CUMPLE CON LAS MEDIDAS
						if($size->width == $val->validated('width') && $size->height == $val->validated('height'))
						{
							# SE ESTABLECE EL MENSAJE DE EXITO
							$msg = 'ok';

							# SI HAY UN ARCHIVO ANTERIOR
							if($val->validated('last_file') != '')
							{
								# SI EL ARCHIVO EXISTE
								if(file_exists(DOCROOT.'assets/uploads/'.$val->validated('last_file')))
								{
									# SE ELIMINAN EL ARCHIVO
									File::delete(DOCROOT.'assets/uploads/'.$val->validated('last_file'));
								}

								# SI EL ARCHIVO EXISTE
								if(file_exists(DOCROOT.'assets/uploads/thumb_'.$val->validated('last_file')))
								{
									# SE ELIMINAN EL ARCHIVO
									File::delete(DOCROOT.'assets/uploads/thumb_'.$val->validated('last_file'));
								}
							}
						}
						else
						{
							# SI EL ARCHIVO RECIEN SUBIDO EXISTE
							if(file_exists(DOCROOT.'assets/uploads/'.$file))
							{
								# SE ELIMINAN EL ARCHIVO
								File::delete(DOCROOT.'assets/uploads/'.$file);
							}

							# SI EL ARCHIVO RECIEN SUBIDO EXISTE
							if(file_exists(DOCROOT.'assets/uploads/thumb_'.$file))
							{
								# SE ELIMINAN EL ARCHIVO
								File::delete(DOCROOT.'assets/uploads/thumb_'.$file);
							}

							# SE LIMPIA LA VARIABLE
							$file = '';

							# SE ESTABLECE EL MENSAJE DE ERROR
							$msg = 'La imagen no tiene las medidas exactas ('.$val->validated('width').' X '.$val->validated('height').' px), por favor vuelve a subirla.';
						}
					}
					else
					{
						# SE ESTABLECE EL MENSAJE DE ERROR
						$msg = 'Solo están permitidos las imágenes con extensión .jpg, .jpeg, .png y .gif.';
					}
				}
				else
				{
					# SE ESTABLECE EL MENSAJE DE ERROR
					$msg = 'No se envió ninguna imagen.';
				}
			}
			else
			{
				# SE ESTABLECE EL MENSAJE DE ERROR
				$msg = 'No es posible subir la imagen con la información enviada.';
			}
		}
		else
		{
			# SE ESTABLECE EL MENSAJE DE ERROR
			$msg = 'La petición no es del tipo AJAX.';
		}

		# SE ENVIA EL ARREGLO CON LA RESPUESTA
		$this->response(array(
			'msg'  => $msg,
			'file' => $file
		));
	}


	/**
	* ORDER_TABLE
	*
	* @access  public
	* @return  Object
	*/
	public function post_order_table()
	{
		# SE INICIALIZAN LAS VARIABLES
		$msg = '';

		# SI ES UNA LLAMADA AJAX
		if(Input::is_ajax())
		{
			# SE CREA LA VALIDACION DE LOS CAMPOS
			$val = Validation::forge('order');
			$val->add_callable('Rules');
			$val->add_field('id', 'id', 'required|valid_string[numeric]|numeric_min[1]');
			$val->add_field('order', 'order', 'required|valid_string[numeric]|numeric_min[1]');

			# SI NO HAY NINGUN PROBLEMA CON LA VALIDACION
			if($val->run())
			{
				# SE BUSCA LA INFORMACION A TRAVES DEL MODELO
				$slide = Model_Slide::query()
				->where('id', $val->validated('id'))
				->get_one();

				# SI SE OBTIENE INFORMACION
				if(!empty($slide))
				{
					# SE ESTEBLECE LA NUEVA INFORMACION
					$slide->order = $val->validated('order');

					# SI SE ALMACENO EL REGISTRO EN LA BASE DE DATOS
					if($slide->save())
					{
						# SE ESTABLECE EL MENSAJE DE EXITO
						$msg = 'ok';
					}
					else
					{
						# SE ESTABLECE EL MENSAJE DE ERROR
						$msg = 'No se puede actualizar el orden, por favor intentalo más tarde.';
					}
				}
				else
				{
					# SE ESTABLECE EL MENSAJE DE ERROR
					$msg = 'El slide enviado no existe.';
				}
			}
			else
			{
				# SE ESTABLECE EL MENSAJE DE ERROR
				$msg = 'No es posible ordenar los slides con la información enviada.';
			}
		}
		else
		{
			# SE ESTABLECE EL MENSAJE DE ERROR
			$msg = 'La petición no es del tipo AJAX.';
		}

		# SE ENVIA EL ARREGLO CON LA RESPUESTA
		$this->response(array(
			'msg' => $msg
		));
	}


	/**
	* ORDER_TABLE_PRODUCT_IMAGES
	*
	* @access  public
	* @return  Object
	*/
	public function post_order_table_product_images()
	{
		# SE INICIALIZAN LAS VARIABLES
		$msg = '';

		# SI ES UNA LLAMADA AJAX
		if(Input::is_ajax())
		{
			# SE CREA LA VALIDACION DE LOS CAMPOS
			$val = Validation::forge('order');
			$val->add_callable('Rules');
			$val->add_field('id', 'id', 'required|valid_string[numeric]|numeric_min[1]');
			$val->add_field('order', 'order', 'required|valid_string[numeric]|numeric_min[1]');

			# SI NO HAY NINGUN PROBLEMA CON LA VALIDACION
			if($val->run())
			{
				# SE BUSCA LA INFORMACION A TRAVES DEL MODELO
				$image = Model_Products_Image::query()
				->where('id', $val->validated('id'))
				->get_one();

				# SI SE OBTIENE INFORMACION
				if(!empty($image))
				{
					# SE ESTEBLECE LA NUEVA INFORMACION
					$image->order = $val->validated('order');

					# SI SE ALMACENO EL REGISTRO EN LA BASE DE DATOS
					if($image->save())
					{
						# SE ESTABLECE EL MENSAJE DE EXITO
						$msg = 'ok';
					}
					else
					{
						# SE ESTABLECE EL MENSAJE DE ERROR
						$msg = 'No se puede actualizar el orden, por favor intentalo más tarde.';
					}
				}
				else
				{
					# SE ESTABLECE EL MENSAJE DE ERROR
					$msg = 'La imagen enviada no existe.';
				}
			}
			else
			{
				# SE ESTABLECE EL MENSAJE DE ERROR
				$msg = 'No es posible ordenar las imágenes con la información enviada.';
			}
		}
		else
		{
			# SE ESTABLECE EL MENSAJE DE ERROR
			$msg = 'La petición no es del tipo AJAX.';
		}

		# SE ENVIA EL ARREGLO CON LA RESPUESTA
		$this->response(array(
			'msg' => $msg
		));
	}


	/**
	 * PRODUCT_STATUS
	 *
	 * @access  public
	 * @return  Object
	 */
	public function post_product_status()
	{
		# SE INICIALIZAN LAS VARIABLES
		$msg = '';

		# SI ES UNA LLAMADA AJAX
		if(Input::is_ajax())
		{
			# SE CREA LA VALIDACION DE LOS CAMPOS
			$val = Validation::forge('product_status');
			$val->add_callable('Rules');
			$val->add_field('product', 'producto', 'required|valid_string[numeric]|numeric_min[1]');
			$val->add_field('value', 'valor', 'required|valid_string[numeric]|numeric_min[0]|numeric_max[1]');

			# SI NO HAY NINGUN PROBLEMA CON LA VALIDACION
			if($val->run())
			{
				# SE BUSCA LA INFORMACION A TRAVES DEL MODELO
				$product = Model_Product::query()
				->where('id', $val->validated('product'))
				->get_one();

				# SI SE OBTIENE INFORMACION
				if(!empty($product))
				{
					# SE ESTEBLECE LA NUEVA INFORMACION
					$product->status = ($val->validated('value') == 0) ? 0 : 1;

					# SI SE ALMACENO EL REGISTRO EN LA BASE DE DATOS
					if($product->save())
					{
						# SE ESTABLECE EL MENSAJE DE EXITO
						$msg = 'ok';
					}
					else
					{
						# SE ESTABLECE EL MENSAJE DE ERROR
						$msg = 'No se pudo cambiar el estatus del producto, por favor intenta más tarde.';
					}
				}
				else
				{
					# SE ESTABLECE EL MENSAJE DE ERROR
					$msg = 'El producto enviado no existe.';
				}
			}
			else
			{
				# SE ESTABLECE EL MENSAJE DE ERROR
				$msg = 'No es posible modificar el valor en base de datos con la información enviada.';
			}
		}
		else
		{
			# SE ESTABLECE EL MENSAJE DE ERROR
			$msg = 'La petición no es del tipo AJAX.';
		}

		# SE ENVIA EL ARREGLO CON LA RESPUESTA
		$this->response(array(
			'msg' => $msg
		));
	}


	/**
	 * PRODUCT_STATUS_INDEX
	 *
	 * @access  public
	 * @return  Object
	 */
	public function post_product_status_index()
	{
		# SE INICIALIZAN LAS VARIABLES
		$msg = '';

		# SI ES UNA LLAMADA AJAX
		if(Input::is_ajax())
		{
			# SE CREA LA VALIDACION DE LOS CAMPOS
			$val = Validation::forge('product_status_index');
			$val->add_callable('Rules');
			$val->add_field('product', 'producto', 'required|valid_string[numeric]|numeric_min[1]');
			$val->add_field('value', 'valor', 'required|valid_string[numeric]|numeric_min[0]|numeric_max[1]');

			# SI NO HAY NINGUN PROBLEMA CON LA VALIDACION
			if($val->run())
			{
				# SE BUSCA LA INFORMACION A TRAVES DEL MODELO
				$product = Model_Product::query()
				->where('id', $val->validated('product'))
				->get_one();

				# SI SE OBTIENE INFORMACION
				if(!empty($product))
				{
					# SE ESTEBLECE LA NUEVA INFORMACION
					$product->status_index = ($val->validated('value') == 0) ? 0 : 1;

					# SI SE ALMACENO EL REGISTRO EN LA BASE DE DATOS
					if($product->save())
					{
						# SE ESTABLECE EL MENSAJE DE EXITO
						$msg = 'ok';
					}
					else
					{
						# SE ESTABLECE EL MENSAJE DE ERROR
						$msg = 'No se pudo cambiar el estatus del producto, por favor intenta más tarde.';
					}
				}
				else
				{
					# SE ESTABLECE EL MENSAJE DE ERROR
					$msg = 'El producto enviado no existe.';
				}
			}
			else
			{
				# SE ESTABLECE EL MENSAJE DE ERROR
				$msg = 'No es posible modificar el valor en base de datos con la información enviada.';
			}
		}
		else
		{
			# SE ESTABLECE EL MENSAJE DE ERROR
			$msg = 'La petición no es del tipo AJAX.';
		}

		# SE ENVIA EL ARREGLO CON LA RESPUESTA
		$this->response(array(
			'msg' => $msg
		));
	}
}
