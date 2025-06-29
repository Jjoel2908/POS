$(() => {
   initializeProductsDropdown();
   loadTemporaryDetails('DetalleCompra');
});

/** Ejecuta una acción adicional complementaria a un proceso principal.
 *
 * Esta función se utiliza para realizar tareas secundarias que deben llevarse a cabo
 * después (o como consecuencia) de una acción principal, como actualizar la interfaz,
 * mostrar mensajes, realizar validaciones extra o disparar eventos personalizados.
 *
 * Su propósito es mantener el código modular y evitar mezclar responsabilidades dentro
 * de la lógica principal.
 */
const runAdditionalStep = () => {}