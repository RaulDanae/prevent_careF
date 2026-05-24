// Hace llamado a la tabla y aplica paginacion -->
  let tabla_agenda

  $(document).ready(function(){

    tabla_agenda = $('#tabla-age').DataTable({
      scrollX: true,
      autoWidth: true,
      responsive: false,
      scrollY: 450,
      processing: true,
      serverSide: true,
      order: [],
      destroy: true,
      language: {
        url: BASE_URL + "/assets/js/Spanish.json"
      },

      "ajax": {
        url: BASE_URL + "/partials/rellage.php",
        type: "POST",
        },

      columnDefs: [
        { targets: [3, 4, 5, 6, 7, 8, 9, 10, 11, 12], className: 'nowrap', orderable: false },
        { targets: "_all", defaultContent: "" }
      ],

      dom:      
         '<"d-flex justify-content-between align-items-center"lf>rtip',
      buttons: [
        {
          extend: "excelHtml5",
          className: 'buttons-excel d-none', //Oculto
          title: 'Eventos',
          exportOptions: {
            columns: ':not(:first-child)', // No se exporta la columna Editar
            modifier: {
              page: 'current' // Solo lo visible en pantalla
            }
          }
        }
      ],

      drawCallback: function () {
        this.api().columns.adjust();
      },

      rowCallback: function(row, data){

        pintarTiempo(row, data, 7, 8);
        pintarTiempo(row, data, 9, 10);

      },

      initComplete: function() {

        const tabla = this.api();

        this.api().columns.adjust();

        // 1. Quitar evento default de DataTables
        $('#tabla-age_filter input').off();

        // 2. Agregar tu propio control
        $('#tabla-age_filter input').on('input', function () {

        let val = $(this).val().trim();

        // Detectar solo códigos tipo EV000123
        let match = val.match(/^EV0*\d+$/i);

        if (match) {
            let limpio = val
                .replace(/^EV/i, '')
                .replace(/^0+/, '');
                
                // Reemplazar en el input
                $(this).val(limpio);

                // Buscar ya limpio
                tabla.search(limpio).draw();

            } else {
                
                // Búsqueda normal
                tabla.search(val).draw();
            }

        });                

      }


    });

    // Boton Buscar
    $('#btn_search').click(function() {
      tabla_agenda.draw();
    })

    // Boton Descargar
    $(document).on('click', '.js-activar-excel', function (e) {
        e.preventDefault();
        window.location.href = BASE_URL + '/partials/exportar_agenda.php';
    });

  });

/////////////////////////////////// Modulo Nuevo /////////////////////////////////////////////////

///////////////////////////Modal Salir //////////////////////////////////////////////////////////
function safeFocus(selector) {
    const el = document.querySelector(selector);
    if (el) el.focus();
}

$('#modalNuevo').on('hidden.bs.modal', function () {
    if (document.querySelector('#btndescargar')) {
        safeFocus('#btndescargar');
    } else {
        safeFocus('#btnNuevoM'); // fallback
    }
});

const btnOpen = document.getElementById('btndescargar');

$('#modalNuevo').on('hide.bs.modal', function () {
    const $btn = $('#btnNuevoM');
    if ($btn.length) {
        $btn.trigger('focus');
    }
});

/////////////////////////// Se resetea el modal despues de cerrarlo /////////////////////////////

$('#modalNuevo').on('hidden.bs.modal', function () {

    // Limpieza final por seguridad
    $('#formWizard')[0].reset();

    $('#summaryContent').empty().hide();

    wizardMode = 'create';
    editMemberId = null;
});

/////////////////////////////////////////////////////////////////////

let wizardMode = 'create'; // create | edit
let editMemberId = null;

let currentStep = 1;
let totalSteps = 0;

function showStep(step) {
    $('.step').removeClass('active');
    $('.step[data-step="' + step + '"]').addClass('active');

    $('#btnPrev').toggle(step > 1);
    $('#btnNext').toggle(step < totalSteps);
    $('#btnSave').toggle(step === totalSteps);

    if(step === totalSteps && !$('#summaryContent').children().length) {
      buildSummary();
    }else{
      $('#summaryContent').empty().hide();
    }

    updateStepper(step);
    updateProgress(step);

}

function validateStep(step) {
    let valid = true;

    $('.step[data-step="' + step + '"]')
        .find(':input[required]')
        .each(function () {
            if (!$(this).val()) {
                $(this).addClass('is-invalid');
                valid = false;
            } else {
                $(this).removeClass('is-invalid');
            }
        });

    return valid;
}

//// Indicador visual de pasos //////
function updateStepper(step) {
    $('.step-item').each(function () {
        const stepNumber = parseInt($(this).data('step'));

        $(this).removeClass('active completed');

        if (stepNumber < step) {
            $(this).addClass('completed');
        } else if (stepNumber === step) {
            $(this).addClass('active');
        }
    });
}

function updateProgress(step) {
    if (totalSteps === 0) return; // seguridad extra
    const percent = (step / totalSteps) * 100;
    $('#progressBar').css('width', percent + '%');
}

$('#btnNext').on('click', function () {
    if (!validateStep(currentStep)) return;
    currentStep++;
    showStep(currentStep);
});

$('#btnPrev').on('click', function () {
    currentStep--;
    showStep(currentStep);
});

$('.step-item').on('click', function () {
    const targetStep = parseInt($(this).data('step'));

    if (targetStep > currentStep) {
        if (!validateStep(currentStep)) return;
    }

    currentStep = targetStep;
    showStep(currentStep);
});

function buildSummary() {
    const summary = [
        { label: 'RFC', value: $('#rfc').val() },
        { label: 'Aviso Privacidad', value: $('#privacidad option:selected').text() },
        { label: 'Consentimiento Informado', value: $('#consentimiento option:selected').text() },
        { label: 'Hr Agenda Toma Muestras', value: $('#agehmuestra').val() },
        { label: 'Hr Toma Muestra', value: $('#hmuestra').val() },
        { label: 'Hr Agenda Evento', value: $('#agehevento').val() },
        { label: 'Hora Evento', value: $('#hevento').val() },
        { label: 'Observaciones', value: $('#obs').val() }
    ];

    let html = '';

    summary.forEach(item => {
        if (item.value) {
            html += `
                <div class="col-md-6">
                    <div class="border rounded p-2 bg-light">
                        <small class="text-muted">${item.label}</small>
                        <div class="fw-semibold">${item.value}</div>
                    </div>
                </div>`;
        }
    });

    $('#summaryContent').html(html).show();
}

///////////////////////////// GUARDAR CLIENTE /////////////////////////////

$('#formWizard').on('submit', function (e) {
    e.preventDefault();   

    // Seguridad: solo permitir envío en último paso
    if (currentStep !== totalSteps) return;

    $('#ajaxError').addClass('hidden').empty();

    // UI bloqueada
    $('#btnSave').prop('disabled', true);
    $('#btnSave .btn-text').addClass('hidden');
    $('#saveSpinner').removeClass('hidden');

    $.ajax({
        url: BASE_URL + '/save/update_age.php',
        type: 'POST',
        data: $(this).serialize(),
        dataType: 'json',

        success: function (resp) {

            if (resp.success) {

                alertify.success('Registro actualizado');

                // Cerrar modal
                $('#modalNuevo').modal('hide');

                // Refrescar DataTable si existe
                if ($.fn.DataTable.isDataTable('#tabla-age')) {
                    $('#tabla-age').DataTable().ajax.reload(null, false);
                }

            } else {
                alertify.error(resp.message);
            }
        },
        error: function (xhr) {
          // Aqu entran los errores 409, 400, etc
          if (xhr.responseJSON && xhr.responseJSON.message) {
            alertify.error(xhr.responseJSON.message);
          } else {
            alertify.error('Error de comunicacion con el servidor');
          }
        },

        complete: function () {
            // Restaurar botón
            $('#btnSave').prop('disabled', false);
            $('#btnSave .btn-text').removeClass('hidden');
            $('#saveSpinner').addClass('hidden');
        }
    });
});

///////////////////////// EDICION //////////////////////////////

let userProfile =  (PERFIL_USUARIO || '').toLowerCase();

$('#tabla-age').on('click', '.btnEditar', function () {

    wizardMode = 'edit';
    editidpaceven = $(this).data('idpacienteevento');

    $('#myModalLabel').text('Editar Agenda');
    $('#btnSave').text('Actualizar');

    cargarDatosAgenda(editidpaceven);
});

function cargarDatosAgenda(editidpaceven) {

        $.ajax({
        url: BASE_URL + '/config/get_age.php',
        type: 'POST',
        dataType: 'json',
        data: { editidpaceven: editidpaceven },
        success: function (data) {

            wizardMode = 'edit';

            // Paso 1
            $('#id').val(data.id_paciente_evento);
            $('#pacien').val(data.colaborador);
            $('#edad').val(data.edad);
            $('#compania').val(data.nomcom);
            $('#sucursal').val(data.nombre_sucursal);
            $('#evento').val(data.nomevento);
            $('#curp').val(data.curp);
            $('#rfc').val((data.rfc && data.rfc.trim() !== '' ? data.rfc : data.curp).toUpperCase()); // lo llena con curp si rfc vacio si no deja rfc
            $('#privacidad').val(data.aprivacidad);
            $('#consentimiento').val(data.cinformado);
            $('#agehmuestra').val(data.programa_htm);
            $('#hmuestra').val(data.hora_toma_muestra);
            $('#agehevento').val(data.programa_he);
            $('#hevento').val(data.hora_evento);
            $('#obs').val(data.observaciones);

            // Estado inicial del wizard
            currentStep = 1;
            totalSteps = $('#modalNuevo .step').length;

            // Abrir modal
            $('#modalNuevo').modal('show');

            // MOstrar paso sin resetear datos
            showStep(currentStep);
        }
    });

}

/// Detectar Cambios Automaticos
$('#hmuestra, #agehmuestra').on('change', function () {
    evaluarTiempoMuestra();
});

$('#agehevento, #hevento').on('change', function () {
    evaluarTiempoEvento();
});

// Al colocar el focus en la caja de texto y esta se encuentre vacia se llena automaticamente
$('#hmuestra, #hevento').on('focus', function () {

    // Solo colocar fecha si está vacío
    if (!$(this).val()) {

        $(this).val(fechaHoraActual()).trigger('change');

    }

});

$('#modalNuevo').on('shown.bs.modal', function () {

    evaluarTiempoMuestra();
    evaluarTiempoEvento();

});

function fechaHoraActual() {

    let ahora = new Date();

    let year = ahora.getFullYear();
    let month = String(ahora.getMonth() + 1).padStart(2, '0');
    let day = String(ahora.getDate()).padStart(2, '0');

    let hours = String(ahora.getHours()).padStart(2, '0');
    let minutes = String(ahora.getMinutes()).padStart(2, '0');

    return `${year}-${month}-${day}T${hours}:${minutes}`;
}


// ========================
// TOMA MUESTRA
// ========================

function evaluarTiempoMuestra() {

    let agenda = $('#agehmuestra').val();
    let asiste = $('#hmuestra').val();

    if (!agenda || !asiste) return;

    let fechaAgenda = new Date(agenda);
    let fechaAsiste = new Date(asiste);

    let diferenciaMin = (fechaAsiste - fechaAgenda) / 60000;

    let comentario = $('#obst');

    comentario.removeClass(
        'bg-success bg-warning bg-danger bg-info text-white'
    );

    if (diferenciaMin < 0) {

        comentario.val('ADELANTADO');
        comentario.addClass('bg-info');

    } else if (diferenciaMin <= 29) {

        comentario.val('A TIEMPO');
        comentario.addClass('bg-success text-white');

    } else if (diferenciaMin <= 59) {

        comentario.val('CON RETRASO');
        comentario.addClass('bg-warning');

    } else {

        comentario.val('TARDE');
        comentario.addClass('bg-danger text-white');
    }
}


// ========================
// EVENTO
// ========================

function evaluarTiempoEvento() {

    let agenda = $('#agehevento').val();
    let asiste = $('#hevento').val();

    if (!agenda || !asiste) return;

    let fechaAgenda = new Date(agenda);
    let fechaAsiste = new Date(asiste);

    let diferenciaMin = (fechaAsiste - fechaAgenda) / 60000;

    let comentario = $('#obse');

    comentario.removeClass(
        'bg-success bg-warning bg-danger bg-info text-white'
    );

    if (diferenciaMin < 0) {

        comentario.val('Adelantado');
        comentario.addClass('bg-info');

    } else if (diferenciaMin <= 29) {

        comentario.val('A tiempo');
        comentario.addClass('bg-success text-white');

    } else if (diferenciaMin <= 59) {

        comentario.val('Con retraso');
        comentario.addClass('bg-warning');

    } else {

        comentario.val('Tarde');
        comentario.addClass('bg-danger text-white');
    }
}

// Esta funciuon hace lo mismo que la de arroba pero esta la uso para pintar la tabla 
function diferenciaMinutos(fecha1, fecha2) {

    if (!fecha1 || !fecha2) return null;

    let f1 = new Date(fecha1.replace(' ', 'T'));
    let f2 = new Date(fecha2.replace(' ', 'T'));

    return (f2 - f1) / 60000;
}

function pintarTiempo(row, data, idxAgenda, idxLlegada) {

    let agenda = data[idxAgenda];
    let llegada = data[idxLlegada];

    let minutos = diferenciaMinutos(agenda, llegada);

    let celdas = $('td:eq('+idxAgenda+'), td:eq('+idxLlegada+')', row);

    celdas.removeClass(
        'bg-verde-suave bg-amarillo-suave bg-rojo-suave bg-azul-suave'
    );

    if (minutos === null) return;

    if (minutos < 0) {

        celdas.addClass('bg-azul-suave');

    } else if (minutos < 30) {

        celdas.addClass('bg-verde-suave');

    } else if (minutos < 60) {

        celdas.addClass('bg-amarillo-suave');

    } else {

        celdas.addClass('bg-rojo-suave');
    }
}


/////////////////////////// Para importar registros //////////////////////////////////

///// PAra que abra la ventana de carga de archivos ////////

$(document).on('click', '.js-subir-excel', function (e) {
    e.preventDefault();
    $('#excelFile').click();
});
/////////////////////////////////////////////////////////////

//////// AJAX para importar el registro ////////////////////
$(document).on('change', '#excelFile', function () {

    let archivo = this.files[0];

    if (!archivo) return;

    let formData = new FormData();
    formData.append('excel', archivo);

    $.ajax({
        url: BASE_URL + '/save/importar_agenda.php',
        type: 'POST',
        data: formData,
        contentType: false,
        processData: false,
        dataType: 'json',
        beforeSend() {
            console.log('Subiendo...');
        },
        success(resp) {

            if (!resp.success) {
                alertify.error(resp.message);
                return;
            }

            // Mostrar resumen
            mostrarResumenAlertify(resp.resumen);

            // Se puede ver el log en consola
            if (resp.log && resp.log.length > 0) {

                console.table(resp.log);
            }
        },

        error(err) {
            alertify.error('Error de comunicacion con el servidor');
            console.error(err);
        }
    });
});

function mostrarResumenAlertify(resumen) {

    let html = `
        <div style="text-align:left">
            <p><strong>Resultado de la carga:</strong></p>
            <ul>
                <li>🟢 Altas: <strong>${resumen.altas}</strong></li>
                <li>🔵 Actualizaciones: <strong>${resumen.actualizados}</strong></li>
                <li>🔴 Errores: <strong>${resumen.errores}</strong></li>
            </ul>
        </div>
    `;

    alertify.alert('Carga de Excel', html);
}



/////////////////////// IMPRIMIR BRAZALETES //////////////////////////////////////////

$(document).on('click', '.btnPrint', function () {
    const id = $(this).data('$id_paciente_evento');

    const w = window.open(
        '../partials/imprimir_brazalete.php?id=' + id,
        '_blank',
        'width=500,height=200'
    );

    w.focus();
});


/////////////////////// IMPRIMIR Etiquetas //////////////////////////////////////////

$(document).on('click', '.btnLabel', function(){

    let id = $(this).data('idpacienteevento');

    fetch(`${BASE_URL}/partials/imprimir_estudio.php?id=${id}`)
        .then(res => res.json())
        .then(data => {

            if (!Array.isArray(data)) {
                alert(data.error || 'Error al obtener datos');
                return;
            }

            imprimirEtiquetas(data);

        })
        .catch(err => {
            console.error(err);
            alert('Error de conexión');
        });

});

// IMprimimr
function generarEtiquetas(data) {

    let html = '';

    data.forEach((e, i) => {

        html += `

            <div class="etiqueta-container">

                <div class="etiqueta">

                    <svg class = "barcode" id="barcode_${i}"></svg>

                    <div class="nombre">${e.nombre}</div>

                    <div class="codigo"> ${e.id}</div>

                    <div class="vertical izquierda">${e.fechaE}</div>

                    <div class="vertical derecha">
                        <div>${e.recipiente}</div>
                        <div class="hora">${e.horaE}</div>
                    </div>

                    <div class="info-derecha">
                        ${e.edad} A ${e.genero}<br>
                        ${e.fechaN}
                    </div>

                </div>

                <input type="checkbox" class="chkEtiqueta" data-id="${e.id}"
                                                           data-nombre="${e.nombre}"
                                                           data-fechae="${e.fechaE}"
                                                           data-horae="${e.horaE}"
                                                           data-recipiente="${e.recipiente}"
                                                           data-edad="${e.edad}"
                                                           data-genero="${e.genero}"
                                                           data-fechan="${e.fechaN}" checked>

            </div>
        
        `;
    });

    // TOOLBAR (FUERA DEL LOOP)
    let controles = `
        <div class="toolbar">
            <button onclick="seleccionarTodo()">Seleccionar todo</button>
            <button onclick="deseleccionarTodo()">Quitar todo</button>
            <button onclick="imprimirSeleccionadas()">Imprimir seleccionadas</button>
        </div>
    `;

    return controles + `<div class="contenedor-etiquetas">${html}</div>`;
}

// Para ver vista previa
function imprimirEtiquetas(data) {

    let ventana = window.open('', '_blank');

    let contenido = `
        <html>
            <head>
                <title>Etiquetas</title>

                <style>
                    body{
                        margin:0;
                        background:#f0f0f0;
                        font-family: Arial, sans-serif;
                    }

                    .toolbar {
                        position: sticky;
                        top: 0;
                        background: white;
                        padding: 10px;
                        z-index: 100;
                        border-bottom: 1px solid #ccc;
                    }

                    .contenedor-etiquetas {
                        display: flex;
                        flex-wrap: wrap;
                        gap: 15px;
                        padding: 15px;
                    }

                    .etiqueta-container {
                        display: flex;
                        flex-direction: column;
                        align-items: center;
                    }

                    .chkEtiqueta {
                        margin-bottom: 10px;
                        transform:scale(1.3);
                    }

                    /* Etiqueta NIIMBOT REAL */

                    .etiqueta {
                        position: relative;

                        width: 384px;
                        height: 240px;

                        background: white;

                        overflow: hidden;

                        box-sizing: border-box;

                        font-family: Arial, sans-serif;
                    }

                    /* Barcode centrado */
                    .barcode {
                        position:absolute;

                        top:20px;
                        left:55px;

                        width:240px;
                        height:50px;

                    }

                    /* Nombre */
                    .nombre {
                        position:absolute;

                        top:80px;

                        width:100%;

                        text-align:center;

                        font-size:16px;

                        font-weight:bold;
                    }

                    /* Fecha izquierda */
                    .izquierda {
                        position:absolute;

                        left:18px;
                        top:100px;

                        transform:rotate(-90deg);

                        transform-origin:left top;

                        font-size:15px;

                        font-weight:bold;
                    }

                    /* Recipiente derecha */
                    .derecha {
                        position:absolute;

                        right:10px;
                        top:125px;

                        transform:rotate(90deg);

                        transform-origin:top right;

                        font-size:13px;

                        font-weight:bold;

                        width:120px;

                        line-height:18px;
                    }

                    /* Info derecha inferior */
                    .info-derecha{

                        position:absolute;

                        right:55px;

                        top:90px;

                        text-align:right;

                        font-size:14px;

                        line-height:18px;

                        font-weight:bold;
                    }

                    .derecha div{
                        white-space:nowrap;
                    }

                    .derecha .hora{
                        margin-top:12px;

                        text-align:center;
                    }

                </style>
            </head>
            <body>
                ${generarEtiquetas(data)}

                <script>

                    async function imprimirSeleccionadas() {

                        const checks = document.querySelectorAll('.chkEtiqueta');

                        let zpl = '';

                        for (const chk of checks) {
                            
                            if (!chk.checked) continue;

                            const id = chk.dataset.id;
                            const nombre = chk.dataset.nombre;
                            const fechae = chk.dataset.fechae;
                            const horae = chk.dataset.horae;
                            const recipiente = chk.dataset.recipiente;
                            const edad = chk.dataset.edad;
                            const genero = chk.dataset.genero;
                            const fechan = chk.dataset.fechan;

                            zpl += \`

                                    ^XA

                                    ^PW400
                                    ^LL240
                                    ^CI28

                                    ; ===== FECHA IZQUIERDA =====
                                    ^FO10,35
                                    ^A0B,18,18
                                    ^FD\${fechae}^FS

                                    ; ===== CODIGO DE BARRAS =====
                                    ^BY1,2,45
                                    ^FO110,15
                                    ^BCN,45,N,N,N
                                    ^FD\${id}^FS

                                    ; ===== NOMBRE =====
                                    ^FO125,95
                                    ^A0N,20,20
                                    ^FD\${nombre}^FS

                                    ; ===== RECIPIENTE =====
                                    ^FO370,25
                                    ^A0B,16,16
                                    ^FD\${recipiente}^FS

                                    ; ===== HORA =====
                                    ^FO335,70
                                    ^A0B,16,16
                                    ^FD\${horae}^FS

                                    ; ===== EDAD / SEXO =====
                                    ^FO250,170
                                    ^A0N,16,16
                                    ^FD\${edad} A \${genero}^FS

                                    ^FO250,195
                                    ^A0N,16,16
                                    ^FD\${fechan}^FS

                                    ^PQ1
                                    ^XZ
                                    \`;

                        };

                        window.opener.BrowserPrint.getDefaultDevice(
                            "printer",
                            function(printer){
                                printer.send(
                                    zpl,
                                    function(){
                                        alert("Etiquetas impresas");
                                    },
                                    function(error) {
                                        console.error(error);
                                        alert("Error al imprimir");
                                    }
                                );
                            },
                            function(error){
                                console.error(error);
                                alert("No se encontro impresora Zebra");
                            }
                        );

                    }

    
                    function seleccionarTodo() {
                        document.querySelectorAll('.chkEtiqueta').forEach(c => c.checked = true);
                    }

                    function deseleccionarTodo() {
                        document.querySelectorAll('.chkEtiqueta').forEach(c => c.checked = false);
                    }
                </script>

            </body>

        </html>
    `;

    ventana.document.write(contenido);
    ventana.document.close();

    ventana.onload = function () {

        data.forEach((e, index) => {

            this.window.opener.JsBarcode(
                ventana.document.querySelector(`#barcode_${index}`),
                e.id,
                {
                    format: "CODE128",
                    width: 2,
                    height: 65,
                    displayValue: false,
                    margin: 0
                }
            );

        });

    };

};