let POS = new classPOS('Usuario', '#modalAddUser');
let URL_USER = "../../../controllers/user.php?";

$(() => {

   moduleUser.tableUser();
   
   /**  A D D   U S E R  */
   $("form#formAddUser").submit(function (event) {
      event.preventDefault();

      POS.validateForm(event, this).then((isValid) => {
         if (isValid) {
            let formData = new FormData(this);
            $.ajax({
            url: URL_USER + "op=saveUser",
            type: "POST",
            data: formData,
            contentType: false,
            processData: false,
            success: function (e) {
               response = JSON.parse(e);
               if (response.success) {
                  POS.showAlert(response.success, response.message);
                  moduleUser.tableUser();
                  $("#modalAddUser").modal("toggle");
               } else POS.showAlert(response.success, response.message);
            },
            });
         }
      });
   });

   /**  U P D A T E   P A S S W O R D  */
   $("form#formUpdatePassword").submit(function (event) {
      event.preventDefault();

      POS.validateForm(event, this).then((isValid) => {
         if (isValid) {
            let formData = new FormData(this);
            $.ajax({
            url: URL_USER + "op=updatePassword",
            type: "POST",
            data: formData,
            contentType: false,
            processData: false,
            dataType: 'json',
            success: function (response) {
               if (response.success) {
                  POS.showAlert(response.success, response.message);
                  moduleUser.tableUser();
                  $("#modalUpdatePassword").modal("toggle");
               } else POS.showAlert(response.success, response.message);
            },
            });
         }
      });
   });

   /**  U P D A T E   P E R M I S S I O N S  */
   $("form#formUserPermissions").submit(function (event) {
      event.preventDefault();

      POS.validateForm(event, this).then((isValid) => {
         if (isValid) {
            let formData = new FormData(this);
            $.ajax({
            url: URL_USER + "op=updatePermissions",
            type: "POST",
            data: formData,
            contentType: false,
            processData: false,
            dataType: 'json',
            success: function (response) {
               if (response.success) {
                  POS.showAlert(response.success, response.message);
                  $("#modalUserPermissions").modal("toggle");
               } else POS.showAlert(response.success, response.message);
            },
            });
         }
      });
   });
});

let moduleUser = {

   /** M O D A L  A D D  U S E R */
   modalAddUser: () => {
      if ( $('#formAddUser #user').attr('readonly') ) $('#formAddUser #user').removeAttr('readonly');
      if ($('#modalAddUser #container-password').hasClass('d-none')) $('#modalAddUser #container-password').removeClass('d-none');
      $('form#formAddUser #password').prop("disabled", false);
      POS.viewModal();
   },

   /** T A B L E  U S E R S */
   tableUser: () => {
      $.ajax({
         url: URL_USER + "op=dataTable",
         type: "GET",
         dataType: "JSON",
         success: (data) => {
   
            let content         = initTable();
            content.columns = [  { data: 'id',   title: '#' },
                                    { data: 'nombre',   title: 'Nombre' },
                                    { data: 'user',     title: 'Usuario' },
                                    { data: 'correo',   title: 'Correo' },
                                    { data: 'telefono', title: 'Teléfono' },
                                    { data: 'estado',   title: 'Estado' },
                                    { data: 'btn',      title: 'Acciones' } ];
            content.order      = [[0, "asc"]];
            content.columnDefs = [ { targets: [0], visible: false } ];
            content.data       = data;
            content.createdRow = (row, data) => {
               $(`td:eq(0), td:eq(1), td:eq(2)`, row).addClass("text-start");
            };
   
            showTable("#table-users", content);
         }
      });
	},

   /** U P D A T E  P A S S W O R D */
   updatePassword: id => {
      POS.clearForm();
      $('form#formUpdatePassword #id').val(id);
      $('#modalUpdatePassword').modal('toggle');
   },

   /** U P D A T E  P E R M I S S I O N S */
   userPermissions: (id, nameUser) => {
      POS.clearForm();
      $('#nameUser').html(nameUser);
      $('form#formUserPermissions #id').val(id);

      let data = {
         id: id
      }

      $.post(URL_USER + "op=showPermissions", data, permission => { 
         $("form#formUserPermissions #permissions").html(permission);
      });

      $('#modalUserPermissions').modal('toggle');
   },

   /** U P D A T E  U S E R */
   updateUser: id => {
      let url = URL_USER + "op=updateUser";
      $('#formAddUser #user').prop('readonly', true);
      $('#modalAddUser #container-password').addClass('d-none');
      $('form#formAddUser #password').prop("disabled", true);
      POS.update(id, url);
   },

   /** D E L E T E  U S E R */
   deleteUser: (id, nombre) => {
      let url = URL_USER + "op=deleteUser";
      POS.delete(id, nombre, url, moduleUser.tableUser);
   }
}