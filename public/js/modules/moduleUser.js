$(() => {
    $("#show_hide_password a").on("click", function (event) {
        event.preventDefault();
        if ($("#show_hide_password input").attr("type") == "text")
            $("#show_hide_password input").attr("type", "password");
        else if ($("#show_hide_password input").attr("type") == "password")
            $("#show_hide_password input").attr("type", "text");
    });

    $("#show_hide_password_new a").on("click", function (event) {
        event.preventDefault();
        if ($("#show_hide_password_new input").attr("type") == "text")
            $("#show_hide_password_new input").attr("type", "password");
        else if ($("#show_hide_password_new input").attr("type") == "password")
            $("#show_hide_password_new input").attr("type", "text");
    });

    $("form#formUpdatePassword").submit(async function (event) {
        event.preventDefault();

        if (validateForm(event, this)) {
            try {
                /** Mostrar el spinner */
                $("#loadingSpinnerPassword").show();

                /** Deshabilitamos el botón de submit */
                $('#btnUpdate').prop("disabled", true);
                
                /** Llamamos a submitForm pasando el módulo dinámicamente */
                await submitForm(this, "updatePassword", "Usuario", () => {
                    $("#modalUpdatePassword").modal("toggle");
                });
            } catch (error) {
                console.log("Ocurrió un error al crear/actualizar la contraseña en moduleUser");
            } finally {
                /** Ocultar el spinner después de que se complete la solicitud */
                $("#loadingSpinnerPassword").hide();
                $('#btnUpdate').prop("disabled", false);
            }
        }
    });

    $("form#formUserPermissions").submit(async function (event) {
        event.preventDefault();

        if (validateForm(event, this)) {
            try {
                /** Mostrar el spinner */
                $("#loadingSpinnerPermissions").show();

                /** Deshabilitamos el botón de submit */
                $('#btnPermissions').prop("disabled", true);
                
                /** Llamamos a submitForm pasando el módulo dinámicamente */
                await submitForm(this, "updatePermissions", "Usuario", () => {
                    $("#modalUserPermissions").modal("toggle");
                });
            } catch (error) {
                console.log("Ocurrió un error al crear/actualizar los permisos del usuario en moduleUser");
            } finally {
                /** Ocultar el spinner después de que se complete la solicitud */
                $("#loadingSpinnerPermissions").hide();
                $('#btnPermissions').prop("disabled", false);
            }
        }
    });
});

/** Abre el modal para registrar un nuevo usuario.
 * Habilita el campo 'user' para que el usuario pueda ingresarlo manualmente.
 * Muestra el campo para ingresar la contraseña.
 */
const addUser = () => {
    openModal('Usuario');
    $('#user').removeAttr('readonly');
    $('#container-password').removeClass('d-none');
 };

/** Carga los datos de un usuario existente en el formulario para su edición.
 * Oculta el campo 'password' para evitar su modificación.
 *
 * @param {string} module - Nombre del módulo (por lo general 'Producto').
 * @param {number} idUser - ID del usuario que se desea actualizar.
 */
const updateUser = async (module, idUser) => {
    await updateRegister(module, idUser);
    $('#user').prop('readonly', true);
    $('#container-password').addClass('d-none');
}

/** Actualiza la contraseña existente de un usuario.
 * @param {number} idUser - ID del usuario que se desea actualizar.
 */
const updatePassword = async (idUser) => {
    /** Limpiamos el formulario */
    clearForm("#modalUpdatePassword");

    /** Asignamos el identificador del usuario */
    $('#idPassword').val(idUser);

    /** Mostramos el modal */
    $('#modalUpdatePassword').modal('toggle');
}

/** Actualiza la contraseña existente de un usuario.
 * @param {number} idUser - ID del usuario que se desea actualizar.
 * @param {number} nameUser - Nombre del usuario que se desea actualizar.
 */
const updatePermissions = async (idUser, nameUser) => {

    const formData = new FormData();
    formData.append("id", idUser);

    /** Llamamos a submitForm pasando el módulo dinámicamente */
    await submitForm(formData, "loadPermissionDetails", "Usuario", (data) => {
        
        /** Limpiamos el formulario */
        clearForm("#modalUserPermissions");

        /** Asignamos el identificador del usuario */
        $('#idPermissions').val(idUser);

        /** Cargamos los permisos en el modal del usuario */
        $("#permissions").html(data);

        /** Mostramos el modal */
        $('#modalUserPermissions').modal('toggle');
    }, false);
}