$(() => {
    const formdata = new FormData();
    submitForm(formdata, 'droplist', 'TipoGasto', (data) => {
        $('#id_tipo_gasto').html(data);
    }, false);
});