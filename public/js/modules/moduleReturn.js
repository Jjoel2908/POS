const addReturn = () => {
   const formdata = new FormData();
   submitForm(formdata, "droplist", 'Ventas', (data) => {
      $("#formReturns #id_venta").html(data);
      openModal('Devolución');
   }, false);
};

$('#formReturns #id_venta').on('select2:select', e => {
   const id = e.params.data.id;

   const formdata = new FormData();
   formdata.append("id", id);

   submitForm(formdata, "droplist", 'DetallesVenta', (data) => {
      $("form#formReturns #id_detail").html(data);
      $("form#formReturns #id_detail").val('');
   }, false);
});

$('#formReturns #id_detail').on('select2:select', e => {
   const id = e.params.data.id;  

   const formdata = new FormData();
   formdata.append("id", id);

   submitForm(formdata, "update", 'DetallesVenta', (data) => {
      const max = parseInt(data.quantity); 
      $('#formReturns #cantidad').attr('max', max); 
      $('#formReturns #cantidad').val(1); 
      $('#formReturns #precio').val(data.price);
      $('#formReturns #id_producto').val(data.id_producto);
   }, false);
});

$('#formReturns #cantidad').on('input', () => {
   const max = parseInt($('#formReturns #cantidad').attr('max'));
   const currentValue = parseInt($('#formReturns #cantidad').val());

   if (currentValue > max)
      $('#formReturns #cantidad').val(max);
});