let customerLoaded = false; 
$(() => {
    initializeProductsDropdown();
    loadTemporaryDetails("DetalleVenta");
    isCashboxOpen();
});

/** Verifica si existe una caja de registro activa */
const isCashboxOpen = async () => await submitForm(new FormData(), "isCashboxOpen", "Caja", () => {}, false);

/** Ejecuta una acción adicional complementaria a un proceso principal.
 *
 * Esta función se utiliza para realizar tareas secundarias que deben llevarse a cabo
 * después (o como consecuencia) de una acción principal, como actualizar la interfaz,
 * mostrar mensajes, realizar validaciones extra o disparar eventos personalizados.
 *
 * Su propósito es mantener el código modular y evitar mezclar responsabilidades dentro
 * de la lógica principal.
 */
const runAdditionalStep = () => {
    const formdata = new FormData();
    
    submitForm(formdata, "droplist", 'Cliente', (data) => {
        $("#formAdd #id_cliente").html(data);
        $(`#formAdd #id_cliente`).val('').trigger('change');
        customerLoaded = true; // Marcar como ya cargados
    }, false);
}

/** Detecta la opción seleccionada por el usuario en el campo de tipo de venta.
 * y realiza acciones dinámicas en el formulario según el valor seleccionado.
 */
$("form#formAdd #tipo_venta").on("select2:select", (e) => {
    const id = e.params.data.id;
    const customerField = $("#customerField");
    const customerId = $("#id_cliente");

    if (id == creditSale.toString()) {
        if (!customerLoaded)
        runAdditionalStep();

        customerField.show(); // Mostrar el campo
        customerId.prop("required", true); // Agregar el atributo requerido
    } else {
        customerField.hide(); // Ocultar el campo
        customerId.prop("required", false); // Quitar el atributo requerido
    }
});