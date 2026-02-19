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