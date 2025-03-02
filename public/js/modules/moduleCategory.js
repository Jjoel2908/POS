let POS = new classPOS('Categoría', '#modalAddCategory');
let URL = "../../../controllers/category.php?";

$(() => {

   moduleCategory.tableCategory();
   
   /**  A D D   C A T E G O R Y  */
   $("form#formAddCategory").submit(function (event) {
      event.preventDefault();

      POS.validateForm(event, this).then((isValid) => {
         if (isValid) {
            let formData = new FormData(this);
            $.ajax({
            url: URL + "op=saveCategory",
            type: "POST",
            data: formData,
            contentType: false,
            processData: false,
            success: function (e) {
               response = JSON.parse(e);
               if (response.success) {
                  POS.showAlert(response.success, response.message);
                  moduleCategory.tableCategory();
                  $("#modalAddCategory").modal("toggle");
               } else POS.showAlert(response.success, response.message);               
            },
            });
         }
      });
   });
});

let moduleCategory = {

   /** M O D A L  A D D  U S E R */
   modalAddCategory: () => {
      POS.viewModal();
   },

   /** T A B L E  U S E R S */
   tableCategory: () => {
      $.ajax({
         url: URL + "op=dataTable",
         type: "GET",
         dataType: "JSON",
         success: (data) => {
   
            let content = initTable();
            content.columns =    [  { data: 'id',   title: '#' },
                                    { data: 'categoria',     title: 'Categoría' },
                                    { data: 'estado',   title: 'Estado' },
                                    { data: 'btn',      title: 'Acciones' } ];
            content.data = data;
            content.createdRow = (row, data) => {
               $(`td:eq(0), td:eq(1)`, row).addClass("text-start");
            };
   
            showTable("#table-categories", content);
         }
      });
	},

   /** U P D A T E  U S E R */
   updateCategory: (id) => {
      let url = URL + "op=updateCategory";
      POS.update(id, url);
   },

   /** D E L E T E  U S E R */
   deleteCategory: (id, nombre) => {
      let url = URL + "op=deleteCategory";
      POS.delete(id, nombre, url, moduleCategory.tableCategory);
   }
}