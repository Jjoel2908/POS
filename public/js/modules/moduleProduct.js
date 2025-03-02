let POS = new classPOS('Producto', '#modalAddProduct');
let URL_PRODUCT = "../../../controllers/product.php?";
let URL_VALIDATE = URL_PRODUCT + 'op=validateInput';

$(() => {

   moduleProduct.tableProduct();
   
   /**  A D D   P R O D U C T  */
   $("form#formAddProduct").submit(function (event) {
      event.preventDefault();

      POS.validateForm(event, this).then((isValid) => {
         if (isValid) {
            let formData = new FormData(this);
            $.ajax({
               url: URL_PRODUCT + "op=saveProduct",
               type: "POST",
               data: formData,
               contentType: false,
               processData: false,
               success: function (e) {
                  response = JSON.parse(e);
                  if (response.success) {
                     POS.showAlert(response.success, response.message);
                     moduleProduct.tableProduct();
                     $("#modalAddProduct").modal("toggle");
                  } else POS.showAlert(response.success, response.message); 
               },
            });
         }
      });
   });

   $.post(URL_PRODUCT + "op=selectCategory", (categories) => {
      $("form#formAddProduct #id_categoria").html(categories);
   })
});

let moduleProduct = {

   /** M O D A L  A D D  P R O D U C T */
   modalAddProduct: () => {
      if ( $('#formAddProduct #codigo').attr('readonly') ) $('#formAddProduct #codigo').removeAttr('readonly');
      POS.viewModal();
   },

   /** T A B L E  P R O D U C T S */
   tableProduct: () => {
      $.ajax({
         url: URL_PRODUCT + "op=dataTable",
         type: "GET",
         dataType: "JSON",
         success: (data) => {
   
            let content = initTable();
            content.columns =    [  { data: 'nombre',        title: 'Nombre' },
                                    { data: 'codigo',        title: 'Código' },
                                    { data: 'id_categoria',  title: 'Categoría' },
                                    { data: 'precio_compra', title: 'Precio Compra' },
                                    { data: 'precio_venta',  title: 'Precio Venta' },
                                    { data: 'stock',         title: 'Stock' },
                                    { data: 'imagen',        title: 'Imagen' },
                                    { data: 'btn',           title: 'Acciones' } ];
            content.data = data;
            content.createdRow = (row, data) => {
   
               $(`td:eq(0), td:eq(1), td:eq(2)`, row).addClass("text-start");
               (data.stock == 0) ? $('td:eq(5)', row).addClass("bg-light-danger") : "";
            
            };
   
            showTable("#table-products", content);
         }
      });
	},

   /** U P D A T E  P R O D U C T */
   updateProduct: (id) => {
      let url = URL_PRODUCT + "op=updateProduct";
      $('#modalAddProduct #codigo').prop('readonly', true);
      POS.update(id, url);
   },

   /** D E L E T E  P R O D U C T */
   deleteProduct: (id, nombre) => {
      let url = URL_PRODUCT + "op=deleteProduct";
      POS.delete(id, nombre, url, moduleProduct.tableProduct);
   }
}