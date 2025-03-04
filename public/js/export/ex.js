/** Clase que gestiona la interacción de un módulo con su respectivo modal y controlador.
 * Permite abrir modales, limpiar formularios y establecer la URL del controlador dinámicamente.
 */
class classPOS {
    /** Constructor de la clase.
     * @param {string} module - Nombre del módulo (Ej: Productos, Ventas, etc.).
     * @param {string} controller - Nombre del controlador (Ej: category, sales).
     */
    constructor(module, controller) {
      /** Guarda el nombre del módulo. */
      this.module = module;
      /** Construye la URL base del controlador, usando el nombre en minúsculas. */
      this.URL = `../../../controllers/${controller.toLowerCase()}.php?`;
    }
  
    async viewModal() {
      let text = ["Categoría", "Caja"].includes(this.module)
        ? "Nueva "
        : "Nuevo ";
      $(`${this.modal} #modalTitle`).html(
        `${text}<span class="text-primary">${this.module}</span>`
      );
      $(`${this.modal} #btnSave`).html(
        '<i class="fa-regular fa-floppy-disk me-1"></i> Guardar'
      );
      this.clearForm();
      $(this.modal).modal("toggle");
    }
  
    async clearForm(modalId) {
      $(`${modalId} form`).get(0).reset();
      $(`${modalId} input`).val("");
      $(`${modalId} select`).val("").trigger("change");
      $(`${modalId} form`).removeClass("was-validated");
    }
  
    async validateForm(event, form) {
      if (!form.checkValidity()) {
        event.preventDefault();
        event.stopPropagation();
        this.showAlert(false, "Complete los campos requeridos");
        form.classList.add("was-validated");
        return false;
      }
      return true;
    }
  
    async submitForm(form, operation, callback) {
      let formData = new FormData(form);
      $.ajax({
        url: `${this.URL}op=${operation}`,
        type: "POST",
        data: formData,
        contentType: false,
        processData: false,
        success: (response) => {
          let res = JSON.parse(response);
          this.showAlert(res.success, res.message);
          if (res.success && callback) callback();
          $(this.modal).modal("toggle");
        },
      });
    }
  
    async loadTable(tableId) {
      $.ajax({
        url: `${this.URL}op=dataTable`,
        type: "GET",
        dataType: "JSON",
        success: (data) => {
          let content = initTable();
          content.columns = [
            { data: "id", title: "#" },
            { data: "categoria", title: "Nombre" },
            { data: "estado", title: "Estado" },
            { data: "btn", title: "Acciones" },
          ];
          content.data = data;
          content.createdRow = (row, data) => {
            $(`td:eq(0), td:eq(1)`, row).addClass("text-start");
          };
          showTable(tableId, content);
        },
      });
    }
  
    async updateRecord(id, operation) {
      $.ajax({
        url: `${this.URL}op=${operation}`,
        type: "POST",
        data: new FormData().append("id", id),
        contentType: false,
        processData: false,
        success: (response) => {
          let res = JSON.parse(response);
          if (res.success) {
            $(this.modal).modal("toggle");
            $.each(res.data, (key, value) => {
              $(`#${key}`).val(value);
            });
          } else {
            this.showAlert(res.success, res.message);
          }
        },
      });
    }
  
    async deleteRecord(id, nombre, callback) {
      Swal.fire({
        title: `Eliminar ${this.module}`,
        html: `<p>¿Estás seguro de eliminar este ${this.module.toLowerCase()}?</p><b>${nombre}</b>`,
        confirmButtonText: "Eliminar",
        cancelButtonText: "Cancelar",
        showCancelButton: true,
      }).then((result) => {
        if (result.isConfirmed) {
          $.ajax({
            url: `${this.URL}op=delete`,
            type: "POST",
            data: new FormData().append("id", id),
            contentType: false,
            processData: false,
            success: (response) => {
              let res = JSON.parse(response);
              this.showAlert(res.success, res.message);
              if (callback) callback();
            },
          });
        }
      });
    }
  
    async showAlert(success, message) {
      Lobibox.notify(success ? "success" : "error", {
        pauseDelayOnHover: true,
        size: "mini",
        icon: success ? "bx bx-check-circle" : "bx bx-x-circle",
        position: "bottom right",
        msg: `<p class="my-1">${message}</p>`,
      });
    }
  }
  