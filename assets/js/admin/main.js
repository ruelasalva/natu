$(document).ready(function() {
    class UploadAdapter {
        constructor(loader) {
            this.loader = loader;
        }

        upload() {
            return this.loader.file
            .then(file => new Promise((resolve, reject) => {
                this._initRequest();
                this._initListeners(resolve, reject, file);
                this._sendRequest(file);
            } ) );
        }

        abort() {
            if(this.xhr) {
                this.xhr.abort();
            }
        }

        _initRequest() {
            const xhr = this.xhr = new XMLHttpRequest();

            xhr.open('POST', url + 'ckeditor_image.json', true);
            xhr.responseType = 'json';
        }

        _initListeners(resolve, reject, file) {
            const xhr = this.xhr;
            const loader = this.loader;
            const genericErrorText = `No se puede subir el archivo: ${ file.name }`;

            xhr.addEventListener('error', () => reject(genericErrorText));
            xhr.addEventListener('abort', () => reject());
            xhr.addEventListener('load', () => {
                const response = xhr.response;

                if (!response || response.error) {
                    return reject( response && response.error ? response.error.message : genericErrorText );
                }

                resolve({
                    default: response.url
                });
            });

            if(xhr.upload) {
                xhr.upload.addEventListener('progress', evt => {
                    if(evt.lengthComputable) {
                        loader.uploadTotal = evt.total;
                        loader.uploaded = evt.loaded;
                    }
                });
            }
        }

        _sendRequest(file) {
            const data = new FormData();
            data.append('access_id', access_id);
            data.append('access_token', access_token);
            data.append('file', file);
            this.xhr.send(data);
        }
    }


    function CustomUploadAdapterPlugin( editor ) {
        editor.plugins.get('FileRepository').createUploadAdapter = (loader) => {
            return new UploadAdapter(loader);
        };
    }

    function notify(placement, align, icon, type, animIn, animOut, messageText) {
        $.notify({
            icon: icon,
            title: ' Aviso importante',
            message: messageText,
            url: ''
        }, {
            element: 'body',
            type: type,
            allow_dismiss: true,
            placement: {
                from: placement,
                align: align
            },
            offset: {
                x: 15,
                y: 15
            },
            spacing: 10,
            z_index: 1080,
            delay: 2500,
            timer: 25000,
            url_target: '_blank',
            mouse_over: false,
            animate: {
                enter: animIn,
                exit: animOut
            },
            template: '<div data-notify="container" class="alert alert-dismissible alert-{0} alert-notify" role="alert">' +
            '<span class="alert-icon" data-notify="icon"></span> ' +
            '<div class="alert-text"</div> ' +
            '<span class="alert-title" data-notify="title">{1}</span> ' +
            '<span data-notify="message">{2}</span>' +
            '</div>' +
            '<button type="button" class="close" data-notify="dismiss" aria-label="Close"><span aria-hidden="true">&times;</span></button>' +
            '</div>'
        });
    }

    if($('.delete-item').length > 0) {
        $('.delete-item').click(function(){
            var link = $(this).attr('href');

            setTimeout(function() {
                swal({
                    title: '¿Estás seguro de eliminar el registro?',
                    text: "No habrá forma de revertir esto.",
                    type: 'warning',
                    showCancelButton: true,
                    buttonsStyling: false,
                    confirmButtonClass: 'btn btn-danger',
                    confirmButtonText: 'Sí, eliminar',
                    cancelButtonClass: 'btn btn-secondary',
                    cancelButtonText: 'Cancelar'
                }).then((result) => {
                    if(result.value) {
                        $(location).attr('href', link);
                    }
                })
            }, 200);

            return false;
        });
    }

    if($('.sorted-table').length > 0) {
        var oldIndex;
        var newIndex;

        $('.sorted-table').sortable({
            containerSelector: 'table',
            itemPath: '> tbody',
            itemSelector: 'tr',
            placeholder: '<tr class="placeholder"/>',
            onDragStart: function($item, container, _super) {
                oldIndex = $item.index();
                $item.appendTo($item.parent());
                _super($item, container);
            },
            onDrop: function($item, container, _super) {
                newIndex = $item.index();

                if(newIndex != oldIndex) {
                    $item.closest('table').find('tbody tr').each(function (i, row) {
                        var SendData = {
                            'id': $(this).data('item-id'),
                            'order': i+1
                        };

                        row = $(row);
                        row.find('.order-num').html(i+1);

                        $.ajax({
                            type: 'post',
                            dataType: 'json',
                            url: url + 'order_table',
                            data: SendData,
                            success: function(response)
                            {
                                if(response.msg != 'ok')
                                {
                                    notify('top', 'center', 'ni ni-bell-55', 'danger', 'animated bounceIn', 'animated bounceOut', response.msg);
                                }
                            }
                        });
                    });
                }

                _super($item, container);
            }
        });
    }

    if($('.sorted-table-banners').length > 0) {
        var oldIndex;
        var newIndex;

        $('.sorted-table-banners').sortable({
            containerSelector: 'table',
            itemPath: '> tbody',
            itemSelector: 'tr',
            placeholder: '<tr class="placeholder"/>',
            onDragStart: function($item, container, _super) {
                oldIndex = $item.index();
                $item.appendTo($item.parent());
                _super($item, container);
            },
            onDrop: function($item, container, _super) {
                newIndex = $item.index();

                if(newIndex != oldIndex) {
                    $item.closest('table').find('tbody tr').each(function (i, row) {
                        var SendData = {
                            'id': $(this).data('item-id'),
                            'order': i+1
                        };

                        row = $(row);
                        row.find('.order-num').html(i+1);

                        $.ajax({
                            type: 'post',
                            dataType: 'json',
                            url: url + 'order_table_banners',
                            data: SendData,
                            success: function(response)
                            {
                                if(response.msg != 'ok')
                                {
                                    notify('top', 'center', 'ni ni-bell-55', 'danger', 'animated bounceIn', 'animated bounceOut', response.msg);
                                }
                            }
                        });
                    });
                }

                _super($item, container);
            }
        });
    }

    if($('.sorted-table-product-images').length > 0) {
        var oldIndex;
        var newIndex;

        $('.sorted-table-product-images').sortable({
            containerSelector: 'table',
            itemPath: '> tbody',
            itemSelector: 'tr',
            placeholder: '<tr class="placeholder"/>',
            onDragStart: function($item, container, _super) {
                oldIndex = $item.index();
                $item.appendTo($item.parent());
                _super($item, container);
            },
            onDrop: function($item, container, _super) {
                newIndex = $item.index();

                if(newIndex != oldIndex) {
                    $item.closest('table').find('tbody tr').each(function (i, row) {
                        var SendData = {
                            'id': $(this).data('item-id'),
                            'order': i+1
                        };

                        row = $(row);
                        row.find('.order-num').html(i+1);

                        $.ajax({
                            type: 'post',
                            dataType: 'json',
                            url: url + 'order_table_product_images',
                            data: SendData,
                            success: function(response)
                            {
                                if(response.msg != 'ok')
                                {
                                    notify('top', 'center', 'ni ni-bell-55', 'danger', 'animated bounceIn', 'animated bounceOut', response.msg);
                                }
                            }
                        });
                    });
                }

                _super($item, container);
            }
        });
    }

    if($('.toggle-ps').length > 0) {
        $(':checkbox.toggle-ps').bind('change', function() {
            // SE OBTIENEN LOS DATOS
            var SendData = {
                'product': $(this).data('product'),
                'value': (this.checked) ? 1 : 0
            };

            // SE REALIZA EL AJAX
            $.ajax({
                type: 'post',
                dataType: 'json',
                url: url + 'product_status',
                data: SendData,
                success: function(response)
                {
                    // SI LA RESPUESTA ES ERROR
                    if(response.msg != 'ok')
                    {
                        notify('top', 'center', 'ni ni-bell-55', 'danger', 'animated bounceIn', 'animated bounceOut', response.msg);
                    }
                }
            });
        });
    }

    if($('.toggle-psi').length > 0) {
        $(':checkbox.toggle-psi').bind('change', function() {
            // SE OBTIENEN LOS DATOS
            var SendData = {
                'product': $(this).data('product'),
                'value': (this.checked) ? 1 : 0
            };

            // SE REALIZA EL AJAX
            $.ajax({
                type: 'post',
                dataType: 'json',
                url: url + 'product_status_index',
                data: SendData,
                success: function(response)
                {
                    // SI LA RESPUESTA ES ERROR
                    if(response.msg != 'ok')
                    {
                        notify('top', 'center', 'ni ni-bell-55', 'danger', 'animated bounceIn', 'animated bounceOut', response.msg);
                    }
                }
            });
        });
    }

    if($('#post-content').length > 0) {
		ClassicEditor
		.create(document.querySelector('#post-content'), {
	        language: 'es',
            extraPlugins: [ CustomUploadAdapterPlugin ],
            mediaEmbed: {
                previewsInData: true
            }
	    })
		.then( newEditor => {
		    postContent = newEditor;
		})
		.catch(error => {
			console.error(error);
		});
	}

	$('#add-content').click(function(){
		const contentData = postContent.getData();

		$('#content').val(contentData);
	});

    if($('.confirm-transfer').length > 0) {
        $('.confirm-transfer').click(function(){
            var link = $(this).attr('href');

            setTimeout(function() {
                swal({
                    title: '¿Estás seguro de confirmar la transferencia de esta venta?',
                    text: "No habrá forma de revertir esto.",
                    type: 'info',
                    showCancelButton: true,
                    buttonsStyling: false,
                    confirmButtonClass: 'btn btn-primary',
                    confirmButtonText: 'Sí, confirmar',
                    cancelButtonClass: 'btn btn-secondary',
                    cancelButtonText: 'Cancelar'
                }).then((result) => {
                    if(result.value) {
                        $(location).attr('href', link);
                    }
                })
            }, 200);

            return false;
        });
    }
});
