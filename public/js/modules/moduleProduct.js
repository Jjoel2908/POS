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
     
   },

   /** U P D A T E  P R O D U C T */
   updateProduct: (id) => {
      let url = URL_PRODUCT + "op=updateProduct";
      $('#modalAddProduct #codigo').prop('readonly', true);
      POS.update(id, url);
   },
}

const addProduct = () => {
   openModal('Producto');
   if ( $('#formAddProduct #codigo').attr('readonly') ) $('#formAddProduct #codigo').removeAttr('readonly');
}