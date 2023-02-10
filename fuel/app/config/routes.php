<?php
	return array(
	'_root_' => 'inicio/index', // The default route
	'_404_'  => '404/index', // The main 404 route

	# TIENDA
	'producto/(:any)'       	=> 'producto/index/$1',
	'tienda/busqueda'       	=> 'catalogo/busqueda',
	'buscar/(:any)'         	=> 'catalogo/buscar/$1',
	'tienda'                	=> 'catalogo/index',
	'promociones'              	=> 'catalogo/promociones',
	'tienda/familia/(:any)' 	=> 'catalogo/categoria/$1',
	'tienda/subfamilia/(:any)' 	=> 'catalogo/subcategoria/$1',
	'tienda/marca/(:any)'   	=> 'catalogo/marca/$1',

    # MI CUENTA
	'iniciar-sesion'                                      => 'iniciar_sesion',
	'recuperar-contrasena'                                => 'recuperar_contrasena',
	'recuperar-contrasena/nueva-contrasena/(:num)/(:any)' => 'recuperar_contrasena/nueva_contrasena/$1/$2',
	'cerrar-sesion'                                       => 'cerrar_sesion',
    'mi-cuenta/direcciones/agregar'                       => 'mi_cuenta/direcciones_agregar',
    'mi-cuenta/direcciones/editar/(:num)'                 => 'mi_cuenta/direcciones_editar/$1',
    'mi-cuenta/facturacion/agregar'                       => 'mi_cuenta/facturacion_agregar',
    'mi-cuenta/facturacion/editar/(:num)'                 => 'mi_cuenta/facturacion_editar/$1',
    'mi-cuenta/descarga_pdf/(:num)'                       => 'mi_cuenta/descarga_pdf/$1',
    'mi-cuenta/descarga_xml/(:num)'                       => 'mi_cuenta/descarga_xml/$1',
    'mi-cuenta'                                           => 'mi_cuenta',
    'mi-cuenta/(:any)'                                    => 'mi_cuenta/$1',

	# SECCIONES
	'preguntas-frecuentes' => 'preguntas_frecuentes',

	# DOCUMENTOS
	'aviso-de-privacidad'    => 'documentos/aviso_privacidad',
	'terminos-y-condiciones' => 'documentos/terminos_condiciones',
);
