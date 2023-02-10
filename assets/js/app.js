/* ======================================
EXPRESIONES REGULARES
========================================= */
$.validator.methods.email = function(value, element)
{
	return (this.optional(element) || /^([a-z0-9\+\-\_]+)(\.[a-z0-9\+\-\_]+)*@([a-z0-9\-]+\.)+[a-z]{2,6}$/i.test(value));
};

function valid_email(string)
{
	return (/^([a-z0-9\+\-\_]+)(\.[a-z0-9\+\-\_]+)*@([a-z0-9\-]+\.)+[a-z]{2,6}$/i).test(string) ? true : false;
}

function alphabetic_spaces(string)
{
	return (/^[a-zA-Z\á\é\í\ó\ú\Á\É\Í\Ó\Ú\ñ\Ñ\ ]*$/).test(string) ? true : false;
}

function alphanumeric_spaces(string)
{
	return (/^[a-zA-Z\á\é\í\ó\ú\Á\É\Í\Ó\Ú\ñ\Ñ0-9\ ]*$/).test(string) ? true : false;
}

function alphanumeric(string)
{
	return (/^[a-zA-Z\á\é\í\ó\ú\Á\É\Í\Ó\Ú\ñ\Ñ0-9]*$/).test(string) ? true : false;
}

function min_length(string, val)
{
	if((/[^0-9]/).test(val))
	{
		return false;
	}

	return (string.length < parseInt(val)) ? false : true;
}

function max_length(string, val)
{
	if((/[^0-9]/).test(val))
	{
		return false;
	}

	return (string.length > parseInt(val)) ? false : true;
}

function exact_length(string, val)
{
	if ((/[^0-9]/).test(val))
	{
		return false;
	}

	return (string.length != parseInt(val)) ? false : true;
}

function numeric(string)
{
	return (/^[\-+]?[0-9]*\.?[0-9]+$/).test(string) ? true : false;
}

function integer(string)
{
	return (/^[\-+]?[0-9]+$/).test(string) ? true : false;
}

function is_natural(string)
{
	return (/^[0-9]+$/).test(string) ? true : false;
}

function is_natural_no_zero(string)
{
	if(!(/^[0-9]+$/).test(string))
	{
		return false;
	}

	if (parseInt(string) == 0)
	{
		return false;
	}

	return true;
}

function is_date(string)
{
	return (/^([\d]{2})([\/][\d]{2})([\/][\d]{4})$/).test(string) ? true : false;
}


/* ======================================
ALERTIFY
========================================= */
alertify.defaults = {
	notifier: {
		delay: 5,
		position: 'top-right',
		closeButton: true
	}
};


/* ======================================
ISEMPTY
========================================= */
function isEmpty(obj)
{
	for(var key in obj)
	{
		if(obj.hasOwnProperty(key))
		return false;
	}

	return true;
}


/* ======================================
DOCUMENT READY
========================================= */
$(document).ready(function(){

	/* ======================================
	CONTACTO
	========================================= */
	if($('#map').length)
	{
		var url = $('#url').data('url');

		function initMap()
		{
			var mapOptions = {
				zoom: 15,
				scrollwheel: false,
				draggable: false,
				center: new google.maps.LatLng(20.638093766854258, -103.32310503205356),
				styles: [{"featureType":"administrative.country","elementType":"geometry","stylers":[{"visibility":"simplified"},{"hue":"#ff0000"}]}]
			};

			var mapElement    = document.getElementById('map');
			var map           = new google.maps.Map(mapElement, mapOptions);
			var markerCustom  = url + 'assets/img/map-marker.png';
			var contentString = '<div id="content">'+
			'<div id="siteNotice">'+
			'</div>'+
			'<h1 id="firstHeading" class="firstHeading">Sajor</h1>'+
			'<div id="bodyContent">'+
			'<p>Calle Rio Juarez 1447,<br>Col. El Rosario,<br>Guadalajara, Jalisco.<br><br>Tel: 33 3942 7070</p>'+
			'</div>'+
			'</div>';

			var infowindow = new google.maps.InfoWindow({
				content: contentString
			});

			var marker = new google.maps.Marker({
				position: new google.maps.LatLng(20.638093766854258, -103.32310503205356),
				map: map,
				icon: markerCustom,
				title: 'Sajor'
			});

			infowindow.open(map, marker);
		}

		initMap();
	}
});

/* ======================================
VARIABLES GLOBALES
========================================= */
// SE OBTIENE LA URL BASE
const url = document.getElementById('url-location').dataset.url;

/*
*	CHECKSTATUS
*
*	VERIFICA EL STATUS DE UNA RESPUESTA
*
*/
const checkStatus = res => {
	if(res.status === 200 && res.status < 300)
	{
		return res;
	}
	else
	{
		let error = new Error(res.statusText);
		error.res = res;
		throw error;
	}
};


/*
*	TOJSON
*
*	RETORNA UNA PROMESA RESUELTA CON EL RESUTADO DEL PARSEO DE RESPONSE
*
*/
const toJSON = res => res.json();

/*
*   Class CartITem
*   Metodos de acciones del carrito
*/
class CartItem
{
	constructor(idProduct = null, quantity = null)
	{
		this.idProduct = idProduct;
		this.quantity = quantity;
	}

	set setIdProduct(idProduct)
	{
		this.idProduct = idProduct;
	}

	set setQuantity(quantity)
	{
		this.quantity = quantity;
	}

	add()
	{
		fetch(url + 'ajax/add_product_cart.json', {
			method: 'post',
			headers: {
				Accept: 'application/json',
				'Content-type': 'application/json'
			},
			body: JSON.stringify(this)
		})
		.then(checkStatus)
		.then(toJSON)
		.then(res => {
			switch (res.msg) {
				case 'ok':
					// SE OBTIENE EL PRODUCTO AGREGADO DEL ARREGLO CART_DATA
					const product = res.cart_data.filter(
						product => product.id == res.product_id
					);

					if(isEmpty(product)) {
						// SE MANDA UNA ALERTA AL USUARIO
						alertify.message(
							`No se puede agregar el producto.`
						);
					}
					else {
						// SI HAY MENOS PRODUCTOS DISPONIBLES DE LOS SOLICITADOS
						if (product[0].available < product[0].quantity.current) {
							// SE MANDA UNA ALERTA AL USUARIO
							alertify.message(
								`Este producto sólo tiene ${
									product[0].available
								} unidades disponibles.`
							);
						} else {
							// SE NOTIFICA QUE EL PRODUCTO HA SIDO AGREGADO
							const newItem = UI.createNotifierElement(product[0]);
							alertify.notify(newItem, 'cart-item', 3);
						}
						// return succesfull(true, res);
					}
				break;

				// PRODUCTO NO DISPONIBLE
				case 'product_not_found':
					// SE IMPRIME EL MENSAJE DE ERROR
					alertify.error('No hay piezas disponibles de este producto.');
				break;

				// PETICION INCOMPLETA
				case 'invalid_request':
					// SE IMPRIME EL MENSAJE DE ERROR
					return alertify.error(
						'Algo inesperado ha ocurrido, por favor refresca la página.'
					);
				break;

				default:
					// SE IMPRIME EL MENSAJE DE ERROR
					return alertify.error(
						'Algo inesperado ha ocurrido, por favor refresca la página.'
					);
				break;
			}

			// SE ACTUALIZA EL VALOR EN EL CONTADOR DEL CARRITO
			const numberItemsInCart = document.querySelector('.cart-qty');
			numberItemsInCart.innerHTML = res.total_products_quantity;
		})
		.catch(error => {
			console.log('Solicitud fallida:', error);
		});
	}

	edit()
	{
		fetch(url + 'ajax/edit_product_cart.json', {
			method: 'post',
			headers: {
				Accept: 'application/json',
				'Content-type': 'application/json'
			},
			body: JSON.stringify(this)
		})
		.then(checkStatus)
		.then(toJSON)
		.then(res => {
			switch (res.msg) {
				// OK
				case 'ok':
					// SE LLAMA A LA FUNCION QUE CONSTRUYE EL CHECKOUT
					UI.updateCheckout(res);
				break;

				default:
					// SE IMPRIME EL MENSAJE DE ERROR
					alertify.error(
						'Algo inesperado ha ocurrido, por favor refresca la página.'
					);
				break;
			}
		})
		.catch(error => {
			console.log('Solicitud fallida:', error);
		});
	}

	delete()
	{
		fetch(url + 'ajax/delete_product_cart.json', {
			method: 'post',
			headers: {
				Accept: 'application/json',
				'Content-type': 'application/json'
			},
			body: JSON.stringify(this)
		})
		.then(checkStatus)
		.then(toJSON)
		.then(res => {
			switch (res.msg) {
				// OK
				case 'ok':
					// SE LLAMA A LA FUNCION QUE CONSTRUYE EL CHECKOUT
					UI.updateCheckout(res);
				break;

				// USUARIO NO VALIDO
				case 'invalid_user':
					// SE IMPRIME EL MENSAJE DE ERROR
					alertify.error('Este usuario no es válido');

					// SE REDIRECCIONA A INICIO DESPUES DE 3SEG
					setTimeout(function() {
						window.location.replace(url);
					}, 3000);
				break;

				default:
					// SE IMPRIME EL MENSAJE DE ERROR
					alertify.error(
						'Algo inesperado ha ocurrido, por favor refresca la página.'
					);
				break;
			}
		})
		.catch(error => {
			console.log('Solicitud fallida:', error);
		});
	}
}


/*
*   CLASS UI
*   METODOS QUE ACTUALIZAN LA INTERFAZ DE USUARIO
*/
class UI
{
	/*
	*	BOOSTRAPDISPLAYALERT
	*
	*	MUESTRA UN ALERT DE BOOTSTRAP
	*
	*/
	static boostrapDisplayAlert(classNames, alertWrapper, message)
	{
		// SE OBTIENE EL ELEMENTO
		alertWrapper = document.querySelector(alertWrapper);

		// SE LIMPIA EL CAMPO
		alertWrapper.innerHTML = '';

		// SE ESCRIBE EL MENSAJE
		alertWrapper.innerHTML = `<div class="alert alert-${classNames} alert-dismissible fade show" role="alert">${message}<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>`;
	}


	/*
	*	ENABLEBUTTON
	*
	*	HABILITA UN BOTÓN
	*
	*/
	static enableButton(elButton, textButton = null)
	{
		elButton.classList.remove('disabled');
		elButton.removeAttribute('disabled');
		elButton.setAttribute('tabindex', 0);
		elButton.setAttribute('aria-disabled', false);

		if(textButton != null)
		{
			elButton.innerHTML = textButton;
		}
	}


	/*
	*	DISABLEBUTTON
	*
	*	PONE UN BOTÓN EN MODO ESPERA
	*
	*/
	static disableButton(elButton, textButton = null)
	{
		elButton.classList.add('disabled');
		elButton.setAttribute('disabled', true);
		elButton.setAttribute('tabindex', -1);
		elButton.setAttribute('aria-disabled', true);

		if(textButton != null)
		{
			elButton.innerHTML = textButton;
		}
	}


	/*
	*	WAITINGBUTTON
	*
	*	PONE UN BOTÓN EN MODO ESPERA
	*
	*/
	static waitingButton(elButton, textButton)
	{
		elButton.classList.add('disabled');
		elButton.setAttribute('disabled', true);
		elButton.setAttribute('tabindex', -1);
		elButton.setAttribute('aria-disabled', true);
		elButton.innerHTML = textButton;
	}


	/*
	*	ENABLEWAITINGSCREEN
	*
	*	HABILITA UN BOTÓN
	*
	*/
	static enableWaitingScreen()
	{
		const waitingResponse = document.getElementById('waiting-response');
		waitingResponse.classList.replace('d-none', 'd-flex');
		document.getElementsByTagName('html')[0].style.overflow = 'hidden';
	}


	/*
	*	DISABLEWAITINGSCREEN
	*
	*	DESHABILITA UN BOTÓN
	*
	*/
	static disableWaitingScreen()
	{
		const waitingResponse = document.getElementById('waiting-response');
		waitingResponse.classList.replace('d-flex', 'd-none');
		document.getElementsByTagName('html')[0].style.overflow = 'auto';
	}


	/*
	*	TOGGLEMEGAMENU
	*
	*	ABRE O CIERRA EL MEGAMENU
	*
	*/
	static toggleMegamenu(e)
	{
		const megamenuEl = document.getElementById('megamenu');

		if(e.keyCode != 27)
		{
			if(megamenuEl.classList.contains('show'))
			{
				megamenuEl.classList.remove('show');
				document.getElementsByTagName('html')[0].style.overflow = 'auto';
			}
			else
			{
				megamenuEl.classList.add('show');
				document.getElementsByTagName('html')[0].style.overflow = 'hidden';
			}
		}
		else
		{
			megamenuEl.classList.remove('show');
			document.getElementsByTagName('html')[0].style.overflow = 'auto';
		}
	}


	/*
	*	CREATENOTIFIERELEMENT
	*
	*	CREA UN DOMELEMENT DE UN ITEM QUE SE AGREGA AL CARRITO
	*  Y QUE SERÁ UTILIZADO PARA LA NOTIFICACIÓN.
	*
	*/
	static createNotifierElement(cartItem)
	{
		const {
			id,
			slug,
			name,
			image,
			quantity: { current },
			price: {
				current: { formatted }
			}
		} = cartItem;

		// SE CREA UN ELEMENTO NUEVO
		const newItem = document.createElement('div');
		newItem.classList.add('row', 'horizontal-card', `product-${id}`);
		newItem.innerHTML = `
		<div class="col-12">
		<p class="mb-2 text-left h5">Se agregó el producto:</p>
		<div class="pt-3">
		<div class="row">
		<div class="col-4 pr-0 pr-sm-3">
		<a title="${name}" class="" href="${url}producto/${slug}">
		<img alt="${name}" class="img-fluid d-block mx-auto" src="${url}assets/uploads/thumb_${image}">
		</a>
		</div>
		<div class="col-8">
		<div class="row">
		<div class="col-12 mb-2">
		<h5 class="text-left text-primary mb-2">${name}</h5>
		<ul class="list-inline list text-left mb-0">
		<li class="list-inline-item">
		$${formatted}
		</li>
		</ul>
		</div>
		</div>
		</div>
		</div>
		</div>
		</div>`;
		return newItem;
	}


	/*
	*	UPDATECHECKOUT
	*
	*   @PARAM {OBJECT[]} RESPONSE
	*	ACTUALIZA LAS PROPIEDADES DE LOS PRODUCTOS EN EL CARRITO
	*
	*/
	static updateCheckout(response)
	{
		// SE INICIALIZAN LOS ARREGLOS
		let data_to_update = [];

		// SI NO HAY PRODUCTOS EN EL CARRITO
		if(response.total_products_quantity == 0)
		{
			// SE LIMPIA LA TABLA
			UI.deleteCheckout();

			// SI EL ARREGLO CART UNAVAILABLE TIENE INFORMACION
			if(response.cart_unavailable.length > 0)
			{
				// SE INICIALIZA LA VARIABLE QUE CONTENDRA LA INFORMACION DE LOS PRODUCTOS NO DISPONIBLES
				let unavailable_products = '';

				// SE RECORRE PRODUCTO POR PRODUCTO
				response.cart_unavailable.forEach(cartItem => {
					// SE CONSTRUYE LA LEYENDA CON LOS PRODUCTOS ELIMINADOS
					unavailable_products += '<p>- ' + cartItem.name + '</p>';
				});

				// SE ESCRIBE EL MENSAJE CON LOS PRODUCTOS NO DISPONIBLES
				UI.boostrapDisplayAlert(
					'warning',
					'#general_alert',
					'<h4 class="alert-heading">¡Atención!</h4><p>Los siguientes productos han sido removidos de tu carrito porque ya no están disponibles en la tienda:</p><hr>' +
					unavailable_products
				);

				// SE MUEVE EL VIEWPORT AL TOP
				window.scrollTo(0, 0);
			}
			else
			{
				// SE NOTIFICA QUE SE ELIMINÓ EL PRODUCTO
				alertify.message('Se eliminó el producto del carrito.');
			}
		}
		else
		{
			// SE AGREGA AL ARREGLO DATA TO UPDATE UN OBJETO CON LOS TOTALES A ACTUALIZAR
			data_to_update.push({
				field: '.total-price',
				value: response.total.formatted
			});

			// SI LA CANTIDAD DEL PRODUCTO ES 0
			if (response.quantity == 0) {
				// SE ELIMINA EL PRODUCTO DEL CARRITO
				$(`.product-${response.product_id}`).hide('normal', function() {
					$(this).remove();
				});

				// SE NOTIFICA QUE SE ELIMINÓ EL PRODUCTO
				alertify.message('Se eliminó el producto del carrito.');
			}

			// SI EL ARREGLO CART DATA EXISTE Y TIENE INFORMACION
			if(response.cart_data.length > 0)
			{
				// SE RECORRE PRODUCTO POR PRODUCTO
				response.cart_data.forEach(cartItem => {
					// SE AGREGA AL ARREGLO DATA TO UPDATE UN OBJETO CON LAS CARACTERISTICAS DEL PRODUCTO A ACTUALIZAR
					data_to_update.push({
						field: `.product-${cartItem.id} .total-product-price`,
						value: cartItem.price.total.formatted
					});

					// SE ACTUALIZA LA CANTIDAD DEL PRODUCTO EN LOS TOUCHSPIN
					document.querySelector(`.product-${cartItem.id} .touchspin`).value =
					cartItem.quantity.valid;

					// SI LA CANTIDAD SOLICITADA ES MAYOR A LAS DISPONIBLES EN STOCK DE LA PROPIEDAD
					if(cartItem.available < cartItem.quantity.current)
					{
						// SE ACTUALIZA LA CANTIDAD MAXIMA DEL SPINNER
						$(`.product-${cartItem.id} .touchspin`).trigger(
							'touchspin.updatesettings',
							{
								max: cartItem.available
							}
						);

						// SE MANDA UNA ALERTA AL USUARIO
						alertify.message(
							`El producto <b>${cartItem.name}</b> sólo tiene <b>${
								cartItem.available
							}</b> unidades disponibles.`
						);
					}
				});
			}

			// SI EL ARREGLO CART DATA EXISTE Y TIENE INFORMACION
			if(response.cart_unavailable.length > 0)
			{
				// SE INICIALIZA LA VARIABLE QUE CONTENDRA LA INFORMACION DE LOS PRODUCTOS NO DISPONIBLES
				let unavailable_products = '';

				// SE RECORRE PRODUCTO POR PRODUCTO
				response.cart_unavailable.forEach(cartItem => {
					// SE CONSTRUYE LA LEYENDA CON LOS PRODUCTOS ELIMINADOS
					unavailable_products += '<p>- ' + cartItem.name + '</p>';

					// SE ELIMINA EL PRODUCTO DEL CARRITO
					$(`.product-${cartItem.id}`).hide('normal', function() {
						$(this).remove();
					});
				});

				// SE ESCRIBE EL MENSAJE CON LOS PRODUCTOS NO DISPONIBLES
				UI.boostrapDisplayAlert(
					'warning',
					'#general_alert',
					'<h4 class="alert-heading">¡Atención!</h4><p>Los siguientes productos han sido removidos de tu carrito porque ya no están disponibles en la tienda:</p><hr>' +
					unavailable_products
				);

				// SE MUEVE EL VIEWPORT AL TOP
				window.scrollTo(0, 0);
			}
		}

		// SE ALMACENA EN EL ARREGLO DATATOUPDATE EL VALOR EN EL CARRITO
		data_to_update.push({
			field: '.cart-qty',
			value: response.total_products_quantity
		});

		// SE ACTUALIZA LA INFORMACION
		data_to_update.forEach(
			el => (document.querySelector(el.field).innerHTML = el.value)
		);
	}


	/*
	*	DELETECHECKOUT
	*
	*   ELIMINA LOS ELEMENTOS DEL CHECKOUT CUANDO NO HAY PRODUCTOS EN EL CARRITO
	*
	*/
	static deleteCheckout()
	{
		// CHECKOUT TABLE / CART
		if(document.querySelectorAll('.checkout-products').length)
		{
			document.querySelector('.checkout-products').innerHTML =
			'<div class="p-3 border bg-white rounded mb-3"><p class="mb-0">No hay productos en tu carrito.</p></div>';
			$('#checkout_sidebar').hide('normal', function() {
				$(this).remove();
			});
		}
	}
}

/*==========================================
DOCUMENT READY
==========================================*/
$(() => {
	/*==========================================
	VARIABLES GLOBALES
	==========================================*/
	const url = document.getElementById('url-location').dataset.url;


	/*====================================
	GLASSCASE
	====================================*/
	if($('#glasscase').length)
	{
		// SE INICIALIZA LA VARIABLE POSITION
		let position = 'bottom';
		let zoomEnabled = false;
		const detailsColor = getComputedStyle(document.documentElement).getPropertyValue(
			'--primary'
		);

		$('#glasscase').glassCase({
			thumbsPosition: position,
			widthDisplay: 800,
			heightDisplay: 800,
			zoomPosition: 'inner',
			isZoomEnabled: zoomEnabled,
			colorActiveThumb: detailsColor,
			colorLoading: detailsColor,
			isThumbsOneRow: false
		});
	}


	/*====================================
	TOUCHSPIN
	====================================*/
	if(document.querySelectorAll('.touchspin').length)
	{
		const classes = 'btn border';

		$('.touchspin.touchspin-add').TouchSpin({
			initval: 1,
			min: 1,
			buttondown_class: classes,
			buttonup_class: classes
		});

		$('.touchspin.touchspin-edit').TouchSpin({
			min: 0,
			mousewheel: false,
			buttondown_class: classes,
			buttonup_class: classes
		});
	}


	/*=======================================
	FACTURA
	=========================================*/
	if($('#form_bill').length > 0) {

		// SI CAMBIA LA OPCION DE LA FACTURA
		$('#form_bill').change(function(){

			// SE OBTIENEN LOS VALORES
			var url  = $('#url-location').data('url');
			var bill = ($('#form_bill').is(':checked')) ? 1 : 0

			// SE MANDA EL AJAX
			$.ajax({
				type:     'post',
				dataType: 'json',
				url:      url + 'ajax/bill.json',
				data:     'bill=' + bill,
				success:  function(response)
				{
					// SI EXISTE UN ERROR EN EL AJAX
					if(response.msg != 'ok')
					{
						// SE MANDA UNA ALERTA AL USUARIO
						alertify.error('No se guardó la opción de la factura, por favor vuelve a intentarlo.');
					}
				}
			});//AJAX

			return false;
		});
	}


	/*====================================
	CHECKOUT - ACEPTAR TERMINOS
	====================================*/
	if(document.body.contains(document.getElementById('form_shipping_terms')))
	{
		const checkbox = document.getElementById('form_shipping_terms');
		const button = document.getElementById('button_continue_checkout');

		checkbox.checked ? UI.enableButton(button) : UI.disableButton(button);

		checkbox.addEventListener('click', () => {
			checkbox.checked ? UI.enableButton(button) : UI.disableButton(button);
		});

		button.addEventListener('click', e => {
			if(!checkbox.checked) {
				// SE EVITA LA ACCION DEFAULT DEL BOTON
				e.preventDefault();
				// SE IMPRIME EL MENSAJE DE ERROR
				alertify.error('Acepta los términos y condiciones.');
			}
		});
	}


	/* ======================================
	NAVBAR FUNCTIONALITY
	========================================= */
	if(document.querySelectorAll('.megamenu-open').length)
	{
		const openMegamenuBtns = document.querySelectorAll('.megamenu-open');
		const closeMegamenuBtns = document.querySelectorAll('.megamenu-close');

		openMegamenuBtns.forEach(btn =>
			btn.addEventListener('click', e => {
				e.preventDefault();
				UI.toggleMegamenu(e);
			})
		);

		closeMegamenuBtns.forEach(btn =>
			btn.addEventListener('click', e => {
				e.preventDefault();
				UI.toggleMegamenu(e);
			})
		);

		document.addEventListener('keyup', e => {
			if (e.keyCode === 27) {
				UI.toggleMegamenu(e);
			}
		});
	}


	/* ======================================
	ENVIAR EL CORREO ELECTRONICO
	========================================= */
	if(document.body.contains(document.getElementById('form_contact')))
	{
		// SE GUARDA LA REFERENCIA AL FORMULARIO
		const formSelector = '#form_contact';

		// INICIALIZACION DE PLUGIN DE VALIDACION
		$(formSelector).validate({
			// CONFIGURACION DE MANEJO DE ERRORES
			validClass: 'is-valid',
			errorClass: 'is-invalid',
			errorElement: 'div',
			errorPlacement: function(error, element) {
				error.appendTo(element.parent());
				error.addClass('invalid-feedback');
			},
			// REGLAS DE CAMPOS
			rules: {
				name: 'required',
				last_name: 'required',
				phone: 'required',
				email: {
					required: true,
					email: true
				},
				message: 'required'
			},
			// MANEJADOR DE EXITO
			submitHandler: form => {
				// SE OBTIENE EL SUBMIT DEL FORM
				const submitButton = document.querySelector(
					formSelector + ' button[type=submit]'
				);

				// SE DESABILITA EL SUBMIT
				UI.disableButton(
					submitButton,
					'<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Enviando...'
				);

				// SE HACE LA PETICION AL SERVIDOR USANDO FETCH API
				fetch(url + 'ajax/send_mail.json', {
					method: 'post',
					headers: {
						Accept: 'application/json',
						'Content-type': 'application/x-www-form-urlencoded'
					},
					body: $(form).serialize()
				})
				// SE VERIFICA EL HTTP STATUS
				.then(checkStatus)
				// SE PARCEA RESPONSE A JSON
				.then(toJSON)
				// SE MANEJA LA RESPUESTA
				.then(res => {
					// SI LA RESPUESTA FUE OK
					if(res.msg == 'ok')
					{
						// SE IMPRIME EL MENSAJE DE EXITO
						UI.boostrapDisplayAlert(
							'success',
							formSelector + ' .status',
							'Solicitud enviada correctamente.'
						);

						// SE LIMPIAN LOS CAMPOS
						form.reset();
					}
					else
					{
						// SE IMPRIME EL MENSAJE DE ERROR
						UI.boostrapDisplayAlert(
							'danger',
							formSelector + ' .status',
							'Ocurrió un error, intentalo más tarde.'
						);
					}

					// SE HABILITA EL BOTON
					UI.enableButton(
						submitButton,
						'Enviar <span class="lnr lnr-arrow-right"></span>'
					);
				})
				// SI HUBO ERROR EN LA PETICION SE CAPTURA EL ERROR
				.catch(error => {
					console.log('Solicitud fallida:', error);
				});
			},
			// MANEJADOR DE ERRORES
			invalidHandler: (event, validator) => {
				let errors = validator.numberOfInvalids();
				if (errors) {
					let message =
					errors == 1
					? 'Existe <b>1 campo vacío</b>. Por favor, regresa a llenarlo.'
					: 'Existen <b>' +
					errors +
					' campos vacíos.</b> Por favor, regresa a llenarlos.';

					UI.boostrapDisplayAlert('danger', formSelector + ' .status', message);
				} else {
					UI.boostrapDisplayAlert(
						'danger',
						formSelector + ' .status',
						'Ocurrió un error, intentalo más tarde.'
					);
				}
			}
		});
	}


	/* ======================================
	ENVIAR EL CORREO ELECTRONICO
	========================================= */
	if(document.body.contains(document.getElementById('form_modal')))
	{
		// SE GUARDA LA REFERENCIA AL FORMULARIO
		const formSelector = '#form_modal';

		// INICIALIZACION DE PLUGIN DE VALIDACION
		$(formSelector).validate({
			// CONFIGURACION DE MANEJO DE ERRORES
			validClass: 'is-valid',
			errorClass: 'is-invalid',
			errorElement: 'div',
			errorPlacement: function(error, element) {
				error.appendTo(element.parent());
				error.addClass('invalid-feedback');
			},
			// REGLAS DE CAMPOS
			rules: {
				name: 'required',
				last_name: 'required',
				phone: 'required',
				email: {
					required: true,
					email: true
				},
				message: 'required',
				product: 'required'
			},
			// MANEJADOR DE EXITO
			submitHandler: form => {
				// SE OBTIENE EL SUBMIT DEL FORM
				const submitButton = document.querySelector(
					formSelector + ' button[type=submit]'
				);

				// SE DESABILITA EL SUBMIT
				UI.disableButton(
					submitButton,
					'<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Enviando...'
				);

				// SE HACE LA PETICION AL SERVIDOR USANDO FETCH API
				fetch(url + 'ajax/send_mail_product.json', {
					method: 'post',
					headers: {
						Accept: 'application/json',
						'Content-type': 'application/x-www-form-urlencoded'
					},
					body: $(form).serialize()
				})
				// SE VERIFICA EL HTTP STATUS
				.then(checkStatus)
				// SE PARCEA RESPONSE A JSON
				.then(toJSON)
				// SE MANEJA LA RESPUESTA
				.then(res => {
					// SI LA RESPUESTA FUE OK
					if(res.msg == 'ok')
					{
						// SE IMPRIME EL MENSAJE DE EXITO
						UI.boostrapDisplayAlert(
							'success',
							formSelector + ' .status',
							'Solicitud enviada correctamente.'
						);

						// SE LIMPIAN LOS CAMPOS
						form.reset();
					}
					else
					{
						// SE IMPRIME EL MENSAJE DE ERROR
						UI.boostrapDisplayAlert(
							'danger',
							formSelector + ' .status',
							'Ocurrió un error, intentalo más tarde.'
						);
					}

					// SE HABILITA EL BOTON
					UI.enableButton(
						submitButton,
						'Enviar <span class="lnr lnr-arrow-right"></span>'
					);
				})
				// SI HUBO ERROR EN LA PETICION SE CAPTURA EL ERROR
				.catch(error => {
					console.log('Solicitud fallida:', error);
				});
			},
			// MANEJADOR DE ERRORES
			invalidHandler: (event, validator) => {
				let errors = validator.numberOfInvalids();
				if (errors) {
					let message =
					errors == 1
					? 'Existe <b>1 campo vacío</b>. Por favor, regresa a llenarlo.'
					: 'Existen <b>' +
					errors +
					' campos vacíos.</b> Por favor, regresa a llenarlos.';

					UI.boostrapDisplayAlert('danger', formSelector + ' .status', message);
				} else {
					UI.boostrapDisplayAlert(
						'danger',
						formSelector + ' .status',
						'Ocurrió un error, intentalo más tarde.'
					);
				}
			}
		});
	}


	/* ======================================
	MI CUENTA - DELETE ADDRESS
	========================================= */
	if(document.querySelectorAll('.address-delete').length)
	{
		const deleteAddressBtns = document.querySelectorAll('.address-delete');

		deleteAddressBtns.forEach(deleteAddressBtn => {
			deleteAddressBtn.addEventListener('click', e => {
				// SE OBTIENEN LA URL
				var url = $('#url-location').data('url');

				const addressId = e.target.dataset.address;

				fetch(url + 'ajax/delete_address.json', {
					method: 'post',
					headers: {
						Accept: 'application/json',
						'Content-type': 'application/x-www-form-urlencoded; charset=UTF-8'
					},
					body: 'addresss=' + encodeURIComponent(addressId)
				})
				.then(checkStatus)
				.then(toJSON)
				.then(data => {
					switch(data.msg)
					{
						case 'ok':
							const deleteAddress = document.querySelector(
								'#list_address_' + addressId
							);
							deleteAddress.remove();
						break;

						default:
							alertify.error(
								'Algo inesperado ha ocurrido, por favor refresca la página.'
							);
						break;
					}
				})
				.catch(error => {
					console.log('Solicitud fallida:', error);
				});
			});
		});
	}


	/* ======================================
	MI CUENTA - SET DEFAULT ADDRESS
	========================================= */
	if(document.querySelectorAll('.address-set-default').length)
	{
		document.addEventListener('click', e => {
			// SI SE HIZO CLICK EN UN ELEMENTO CON CLASE .address-set-default
			if(e.target.classList.contains('address-set-default'))
			{
				// SE OBTIENEN LA URL
				var url = $('#url-location').data('url');

				const addressId = e.target.dataset.address;

				fetch(url + 'ajax/set_default_address.json', {
					method: 'post',
					headers: {
						Accept: 'application/json',
						'Content-type': 'application/x-www-form-urlencoded; charset=UTF-8'
					},
					body: 'address=' + encodeURIComponent(addressId)
				})
				.then(checkStatus)
				.then(toJSON)
				.then(data => {
					switch(data.msg)
					{
						case 'ok':
							const addressesWrapper = document.querySelector(
								'.list-addresses .list-group'
							);

							const newDefaultAddress = document.querySelector(
								'#list_address_' + addressId
							);

							const currentDefaultAddress = addressesWrapper.querySelector(
								'.list-group-item.active'
							);

							let newDefaultBtn = document.createElement('button');
							newDefaultBtn.classList.add(
								'btn',
								'btn-primary',
								'btn-sm',
								'link-action',
								'address-set-default',
								'mr-2'
							);

							newDefaultBtn
							.appendChild(document.createElement('i'))
							.classList.add(
								'fas',
								'fa-check-circle',
								'fa-fw',
								'mr-1'
							);

							newDefaultBtn.appendChild(
								document.createTextNode('Hacer predeterminada')
							);

							// SI EXISTE UNA DIRECCION MARCADA POR DEFECTO
							if(currentDefaultAddress != null)
							{
								// SE QUITAN LOS ESTILOS DE LA DIRECCION PREDETERMINADA
								currentDefaultAddress
								.querySelector('.default-title')
								.remove();

								currentDefaultAddress.classList.remove('active');
								newDefaultBtn.dataset.address = currentDefaultAddress.querySelector(
									'.address-delete'
								).dataset.address;

								currentDefaultAddress
								.querySelector('.link-actions')
								.prepend(newDefaultBtn);
							}

							let newTitle = document.createElement('h3');

							newTitle.appendChild(
								document.createTextNode('Dirección predeterminada')
							);

							newTitle.classList.add(
								'default-title',
								'text-uppercase',
								'text-left',
								'mb-2',
								'text-white'
							);

							newDefaultAddress.prepend(newTitle);

							newDefaultAddress
							.querySelector('.address-set-default')
							.remove();

							newDefaultAddress.classList.add('active');
							addressesWrapper.insertBefore(
								newDefaultAddress,
								addressesWrapper.firstChild
							);
						break;

						default:
							alertify.error(
								'Algo inesperado ha ocurrido, por favor refresca la página.'
							);
						break;
					}
				})
				.catch(error => {
					console.log('Solicitud fallida:', error);
				});
			}
		});
	}


	/* ======================================
	MI CUENTA - DELETE TAX DATUM
	========================================= */
	if(document.querySelectorAll('.tax_datum-delete').length)
	{
		const deleteTaxdatumBtns = document.querySelectorAll('.tax_datum-delete');

		deleteTaxdatumBtns.forEach(deleteTaxdatumBtn => {
			deleteTaxdatumBtn.addEventListener('click', e => {
				// SE OBTIENEN LA URL
				var url = $('#url-location').data('url');

				const tax_datumId = e.target.dataset.tax_datum;

				fetch(url + 'ajax/delete_tax_datum.json', {
					method: 'post',
					headers: {
						Accept: 'application/json',
						'Content-type': 'application/x-www-form-urlencoded; charset=UTF-8'
					},
					body: 'tax_datum=' + encodeURIComponent(tax_datumId)
				})
				.then(checkStatus)
				.then(toJSON)
				.then(data => {
					switch(data.msg)
					{
						case 'ok':
							const deleteTaxdatum = document.querySelector(
								'#list_tax_datum_' + tax_datumId
							);
							deleteTaxdatum.remove();
						break;

						default:
							alertify.error(
								'Algo inesperado ha ocurrido, por favor refresca la página.'
							);
						break;
					}
				})
				.catch(error => {
					console.log('Solicitud fallida:', error);
				});
			});
		});
	}


	/* ======================================
	MI CUENTA - SET DEFAULT TAX DATUM
	========================================= */
	if(document.querySelectorAll('.tax_datum-set-default').length)
	{
		document.addEventListener('click', e => {
			// SI SE HIZO CLICK EN UN ELEMENTO CON CLASE .tax_datum-set-default
			if(e.target.classList.contains('tax_datum-set-default'))
			{
				// SE OBTIENEN LA URL
				var url = $('#url-location').data('url');

				const tax_datumId = e.target.dataset.tax_datum;

				fetch(url + 'ajax/set_default_tax_datum.json', {
					method: 'post',
					headers: {
						Accept: 'application/json',
						'Content-type': 'application/x-www-form-urlencoded; charset=UTF-8'
					},
					body: 'tax_datum=' + encodeURIComponent(tax_datumId)
				})
				.then(checkStatus)
				.then(toJSON)
				.then(data => {
					switch(data.msg)
					{
						case 'ok':
							const tax_dataWrapper = document.querySelector(
								'.list-tax_data .list-group'
							);

							const newDefaultTaxdatum = document.querySelector(
								'#list_tax_datum_' + tax_datumId
							);

							const currentDefaultTaxdatum = tax_dataWrapper.querySelector(
								'.list-group-item.active'
							);

							let newDefaultBtn = document.createElement('button');
							newDefaultBtn.classList.add(
								'btn',
								'btn-primary',
								'btn-sm',
								'link-action',
								'tax_datum-set-default',
								'mr-2'
							);

							newDefaultBtn
							.appendChild(document.createElement('i'))
							.classList.add(
								'fas',
								'fa-check-circle',
								'fa-fw',
								'mr-1'
							);

							newDefaultBtn.appendChild(
								document.createTextNode('Hacer predeterminado')
							);

							// SI EXISTE UNA DIRECCION MARCADA POR DEFECTO
							if(currentDefaultTaxdatum != null)
							{
								// SE QUITAN LOS ESTILOS DE LA DIRECCION PREDETERMINADA
								currentDefaultTaxdatum
								.querySelector('.default-title')
								.remove();

								currentDefaultTaxdatum.classList.remove('active');
								newDefaultBtn.dataset.tax_datum = currentDefaultTaxdatum.querySelector(
									'.tax_datum-delete'
								).dataset.tax_datum;

								currentDefaultTaxdatum
								.querySelector('.link-actions')
								.prepend(newDefaultBtn);
							}

							let newTitle = document.createElement('h3');

							newTitle.appendChild(
								document.createTextNode('RFC predeterminado')
							);

							newTitle.classList.add(
								'default-title',
								'text-uppercase',
								'text-left',
								'mb-2',
								'text-white'
							);

							newDefaultTaxdatum.prepend(newTitle);

							newDefaultTaxdatum
							.querySelector('.tax_datum-set-default')
							.remove();

							newDefaultTaxdatum.classList.add('active');
							tax_dataWrapper.insertBefore(
								newDefaultTaxdatum,
								tax_dataWrapper.firstChild
							);
						break;

						default:
							alertify.error(
								'Algo inesperado ha ocurrido, por favor refresca la página.'
							);
						break;
					}
				})
				.catch(error => {
					console.log('Solicitud fallida:', error);
				});
			}
		});
	}


	/*==========================================
	AGREGAR AL CARRITO
	==========================================*/
	if(document.querySelectorAll('.add-product-cart').length)
	{
		const addProductBtns = document.querySelectorAll('.add-product-cart');

		addProductBtns.forEach(btn =>
			btn.addEventListener('click', e => {
				const btnEl = e.target;
				let quantity = 0;

				switch(btnEl.dataset.type)
				{
					case 'single':
						quantity = 1;
					break;

					case 'multiple':
						var targetElement = event.target || event.srcElement;
						var parentElement = targetElement.parentElement.parentElement;
						var touchspin = parentElement.querySelector('.touchspin-add');

						quantity = touchspin.value;
					break;

					default:
						return alertify.error(
							'Algo inesperado ha ocurrido, por favor refresca la página.'
						);
					break;
				}

				const cart = new CartItem(btnEl.dataset.product, quantity);
				cart.add();
			})
		);
	}


	/*==========================================
	EDITAR CARRITO
	==========================================*/
	if(document.querySelectorAll('.edit-product-cart').length)
	{
		const cart = new CartItem();

		$('.edit-product-cart').change(e => {
			const inputEl = e.target;
			cart.setIdProduct = inputEl.dataset.product;
			cart.setQuantity = inputEl.value;
			cart.edit();
		});
	}


	/*==========================================
	ELIMINAR PRODUCTO CARRITO
	==========================================*/
	if(document.querySelectorAll('.delete-product-cart').length)
	{
		const deleteBtns = document.querySelectorAll('.delete-product-cart');
		deleteBtns.forEach(btn =>
			btn.addEventListener('click', e => {
				const cart = new CartItem(e.target.dataset.product);
				cart.delete();
			})
		);
	}


	/* ======================================
	CHECKOUT - AGREGAR DIRECCION
	========================================= */
	if($('#form_add_address').length)
	{
		// SE CREA LA VALIDACION
		$('#form_add_address').validate({
			// CONFIGURACION DEL VALIDATOR
			validClass: 'is-valid',
			errorClass: 'is-invalid',
			errorElement: 'div',
			errorPlacement: function(error, element) {
				error.appendTo(element.parent());
				error.addClass('invalid-feedback');
			},
			// REGLAS DE LOS CAMPOS
			rules: {
				name: 'required',
				last_name: 'required',
				phone: 'required',
				street: 'required',
				number: 'required',
				internal_number: {
					minlength: 1,
					maxlength: 255
				},
				colony: 'required',
				zipcode: 'required',
				state: {
					required: true,
					min: 1,
					max: 32,
					digits: true
				},
				city: 'required',
				details: {
					minlength: 1,
					maxlength: 255
				}
			},
			messages: {
				state: 'Selecciona una opción válida.'
			}
		});
	}


	/* ======================================
	MARQUEE
	========================================= */
	if($('#marquee').length)
	{
		$('#marquee').marquee({
			duration: 20000
		});
	}


	/* ======================================
	CAROUSEL
	========================================= */
    if($('.owl-types').length)
	{
		$('.owl-types').owlCarousel({
			loop: true,
			margin: 20,
			nav: true,
			navText: [
				'<i class="fa fa-chevron-left" aria-hidden="true"></i>',
				'<i class="fa fa-chevron-right" aria-hidden="true"></i>'
			],
			navContainer: '.owl-controls-types',
			navElement: 'a',
			autoplay: true,
			autoplayTimeout: 3000,
			autoplayHoverPause: true,
			responsive: {
				0: {
					items: 2,

				},
				577: {
					items: 2,
				},
				992: {
					items: 4
				},
				1200: {
					items: 5
				}
			}
		});
	}

	/* ======================================
	CAROUSEL
	========================================= */
    if($('#products-available').length)
	{
		$('#products-available').change(function(){

			// SE ALMACENA LA URL ACTUAL
			var current_url = $('#url-current').data('url');

			// SI EL SWITCH ESTA ACTIVO
			if($('#products-available').is(':checked'))
			{
				// SE INICIALIZA LA VARIABLE
				var availables = 1;
			}
			else
			{
				// SE INICIALIZA LA VARIABLE
				var availables = 0;
			}

			// SE HACE LA PETICION AL SERVIDOR USANDO FETCH API
			fetch(url + 'ajax/products_availables.json', {
				method: 'post',
				headers: {
					Accept: 'application/json',
					'Content-type': 'application/x-www-form-urlencoded'
				},
				body: 'value=' + availables
			})
			// SE VERIFICA EL HTTP STATUS
			.then(checkStatus)
			// SE PARCEA RESPONSE A JSON
			.then(toJSON)
			// SE MANEJA LA RESPUESTA
			.then(res => {
				// SI LA RESPUESTA FUE OK
				if(res.msg == 'ok')
				{
					// SE REDIRECCIONA AL USUARIO
					window.location.href = current_url;
				}
			})
			// SI HUBO ERROR EN LA PETICION SE CAPTURA EL ERROR
			.catch(error => {
				console.log('Solicitud fallida:', error);
			});
		});
	}


	/*==========================================
	AGREGAR UN PRODUCTO A DESEADOS
	==========================================*/
	if($('.add-product-wishlist').length)
	{
		$('.add-product-wishlist').click(function(){

			// SE ALMACENA LA URL ACTUAL
			var product = $(this).data('product');

			// SE HACE LA PETICION AL SERVIDOR USANDO FETCH API
			fetch(url + 'ajax/add_product_wishlist.json', {
				method: 'post',
				headers: {
					Accept: 'application/json',
					'Content-type': 'application/x-www-form-urlencoded'
				},
				body: 'product=' + product
			})
			// SE VERIFICA EL HTTP STATUS
			.then(checkStatus)
			// SE PARCEA RESPONSE A JSON
			.then(toJSON)
			// SE MANEJA LA RESPUESTA
			.then(res => {
				// SI LA RESPUESTA ES OK
				if(res.msg == 'ok')
				{
					// SE MUESTRA EL MENSAJE DE ERROR
					alertify.notify(
						'Se agregó el producto a deseados con éxito.'
					);
				}
				else
				{
					// SE MUESTRA EL MENSAJE DE ERROR
					alertify.error(
						res.msg
					);
				}
			})
			// SI HUBO ERROR EN LA PETICION SE CAPTURA EL ERROR
			.catch(error => {
				console.log('Solicitud fallida:', error);
			});
		});
	}


	/*==========================================
	ELIMINA UN PRODUCTOS DE DESEADOS
	==========================================*/
	if($('.delete-product-wishlist').length)
	{
		$('.delete-product-wishlist').click(function(){

			// SE ALMACENA LA URL ACTUAL
			var product = $(this).data('product');

			// SE HACE LA PETICION AL SERVIDOR USANDO FETCH API
			fetch(url + 'ajax/delete_product_wishlist.json', {
				method: 'post',
				headers: {
					Accept: 'application/json',
					'Content-type': 'application/x-www-form-urlencoded'
				},
				body: 'product=' + product
			})
			// SE VERIFICA EL HTTP STATUS
			.then(checkStatus)
			// SE PARCEA RESPONSE A JSON
			.then(toJSON)
			// SE MANEJA LA RESPUESTA
			.then(res => {
				// SI LA RESPUESTA ES OK
				if(res.msg == 'ok')
				{
					// SE ELIMINA EL DIV
					$('.product-' + product).hide('normal', function() {
						$(this).remove();
					});

					// SE MUESTRA EL MENSAJE DE ERROR
					alertify.notify(
						'Se eliminó el producto a deseados con éxito.'
					);

					// SI LA CANTIDAD DEL PRODUCTO ES 0
					if(res.quantity == 0)
					{
						// SE AGREGA UN DIV
						$('.wishlist-products').html('<div class="p-3 border bg-white rounded mb-3"><p class="mb-0">No hay productos en tu lista de deseados.</p></div>');
					}
				}
				else
				{
					// SE MUESTRA EL MENSAJE DE ERROR
					alertify.error(
						res.msg
					);
				}
			})
			// SI HUBO ERROR EN LA PETICION SE CAPTURA EL ERROR
			.catch(error => {
				console.log('Solicitud fallida:', error);
			});
		});
	}
});
