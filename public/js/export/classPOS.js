/**   P O S   */
class classPOS {

	constructor(module, modal){
		this.module = module;
		this.modal  = modal;
	}

	async viewModal() {
		let text = (this.module == 'Categoría' || this.module == 'Caja') ? 'Nueva ' : 'Nuevo ';
		$(this.modal + ' #modalTitle').html(text + '<span class="text-primary"> ' + this.module + '</span>');
		$(this.modal + ' #btnSave').html('<i class="fa-regular fa-floppy-disk me-1"></i> Guardar');
		this.clearForm();
		$(this.modal).modal('toggle');
	}

	async clearForm() {
		$('form').get(0).reset();
		$('input').val('');
		$('select').val('').trigger('change');
		$('form').removeClass('was-validated');
	}

	async validateForm(event, form) {
		if( !form.checkValidity() ) {
			event.preventDefault();
			event.stopPropagation();
			this.showAlert(false, "Complete los campos requeridos");
			form.classList.add('was-validated');
			return false;
		} else {
			return true;
		}
	}

	async delete(id, nombre, url_delete, refresh_table) {

		let self   = this;
		let module = this.module;
		let text   = (this.module == 'Categoría' || this.module == 'Caja') ? 'la' : 'el';
		let response;

		Swal.fire({
			title: '<h3 class="mt-3">Eliminar ' + module + '</h3>',
			html: '<p class="font-size-20 mb-2">¿Estás seguro de eliminar ' + text + ' siguiente ' + module.toLowerCase() + '?</p> <b>' + nombre + '</b>',
			confirmButtonText: TextDelete,
			cancelButtonText: TextCancel,
			showCancelButton: true,
		}).then((result) => {
			if (result.isConfirmed) {
	
				let formdata = new FormData();
				formdata.append("id", id);
				
				$.ajax({
					url: url_delete,
					type: "POST",
					data: formdata,
					contentType: false,
					processData: false,
					success: (e) => {
						response = JSON.parse(e);
						self.showAlert(response.success, response.message);

						if (typeof refresh_table === 'function') {
                     refresh_table();
                  }
					},
				});
			}
		});
	}

	async update(id, url_update) {

		let self     = this;
		let modal    = this.modal;
		let formdata = new FormData();
		let response;

		formdata.append("id", id);
		
		$.ajax({
			url: url_update,
			type: "POST",
			data: formdata,
			processData: false,
			contentType: false, 
			beforeSend: function() { self.clearForm(); },
			success: e => {
				response = JSON.parse(e);

				if ( response.success ) {

					$(this.modal + ' #modalTitle').html('Actualizar <span class="text-primary"> ' + this.module + '</span>');
					$(this.modal + ' #btnSave').html('<i class="fa-regular fa-floppy-disk me-1"></i> Actualizar');

					if (this.module == 'Producto') {
						$.each(response.data, function(key, value) {
							if (key === "imagen") {
								if (value != "") $("#showImage").html(`<img class="img-fluid" src="../../../media/products/${value}" alt="Imagen de Producto">`)
									else $("#showImage").html("");
							} else {
								$(`select#${key}`).val(value).trigger('change');
								$("#" + key).val(value);
							}
						});

						$("#imagenactual").val(response.data['imagen']);
					} else {
						$.each(response.data, function(key, value) {
							$("#" + key).val(value);
						});
					}

					$(modal).modal("toggle");

				} else self.showAlert(response.success, response.message);
			}
		})
	}

	async showAlert(success, message) {
		let a = success ? "success" : "error";
		let i = success ? "bx bx-check-circle" : "bx bx-x-circle";

		Lobibox.notify(a, {
		pauseDelayOnHover: true,
		size: "mini",
		icon: i,
		continueDelayOnInactiveTab: false,
		position: "bottom right",
		msg: '<p class="my-1">'+  message + '</p>',
		sound: ""
		});
	}
	
	async validateInput(input, value, url_validate) {

		let formdata = new FormData();
		formdata.append(input, value);
		let response;

		$.ajax({
			url: url_validate,
			type: 'POST',
			data: formdata,
			contentType: false,
			processData: false,
			success: (e) =>{
	
				response = JSON.parse(e);
				if (response.success) {
					Swal.fire({ html: `<h5 class="fs-22 fw-normal"> ${response.message} </h5>`, showCloseButton: true, showConfirmButton: false })
				}
			}
		});
	}
}