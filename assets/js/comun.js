$(document).on('click', '#btnAcercaDe', function (e) {

    e.preventDefault();

    $('#modalInfoTitle').text('Acerca del Sistema');

    $('#modalInfoBody').html(`
        <p><strong>Prevent Care</strong></p>
        <p>Sistema de control médico laboral.</p>
        <p>Versión 1.0</p>
        <p>Desarrollado por Dan.</p>
    `);

    $('#modalInfoTitle').text('Acerca del módulo');

    if (typeof INFO_MODULO !== 'undefined') {
        $('#modalInfoBody').html(INFO_MODULO);
    } else {
        $('#modalInfoBody').html('<p>Información no disponible.</p>');
    }    

    $('#modalInfo').modal('show');
});

//// Inicializa el select2

$(document).ready(function(){

    initCatalogos();

});


// Tamaños del select2 //
//$('.catalogo-select').select2({
//    dropdownParent: $('#modalNuevo'),
//    width: '100%',
//    placeholder: 'Buscar o agregar'
//});


function initCatalogos(context = document){

    $(context).find('.catalogo-select').each(function(){

        const $select = $(this);

        if ($select.hasClass("select2-hidden-accessible")) {
            return;
        }

        const tabla = $select.data("tabla");

        $select.select2({

            dropdownParent: $select.closest('.modal'),
            theme: "bootstrap-5",
            width: '100%',
            tags: puedeCrearCatalogos,
            minimumInputLength: 0, // Para que no haga busquedas hasta colocar 2 digitos
            closeOnSelect: !$select.prop('multiple'),  // PAra que no se cierre la ventana mientras seleccionas perfiles
            allowClear: true,

            ajax: {
                url: BASE_URL + '/save/catalogo_buscar.php',
                dataType: 'json',
                delay: 250,

                data: function(params){
                    return {
                        q: params.term || '',
                        tabla: tabla
                    };
                },

                processResults: function(data){
                    return { results: data };
                }
            },

            createTag: function(params){

                let term = $.trim(params.term);

                if(term === '') {
                    return null;
                }

                return {
                    id: term,
                    text: term,
                    nuevo: true
                };
            }

        });

    });

}

$(document).on('select2:close', '.catalogo-select', function () {

    const select = $(this);
    const data = select.select2('data');

    // SI el registro creado es un "tag nuevo"
    if (data.length && data[0].nuevo) {

        select.val(null).trigger('change');

    }

});



$(document).on('select2:select', '.catalogo-select', function(e){

    const data = e.params.data;
    const tabla = $(this).data("tabla");
    const select = $(this);

    if(data.nuevo){

        $.post(BASE_URL + '/save/catalogo_insert.php', {

            tabla: tabla,
            nombre: data.text

        }, function(res){

            const option = new Option(data.text, res.id, true, true);

            select.append(option).trigger('change');

        }, "json");

    }

});
