// Hace llamado a la tabla y aplica paginacion -->
  let tabla_filtro

  $(document).ready(function(){

    tabla_filtro = $('#tabla-fil').DataTable({
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
        url: BASE_URL + "/partials/rellfil.php",
        type: "POST",
        },

      columnDefs: [
        { targets: 0, orderable: false },
        { targets: "_all", defaultContent: "" }
      ],

      dom:      
         '<"d-flex justify-content-between align-items-center"l f>rtip',
      buttons: [
        {
          extend: "excelHtml5",
          className: 'buttons-excel d-none', //Oculto
          title: 'Filtro Evento',
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

        if(data[4] === 0) {
            $(row).addClass('fila-baja');
        }

      },

      initComplete: function() {
        this.api().columns.adjust();
      }

    });

    // Boton Buscar
    $('#btn_search').click(function() {
      tabla_filtro.draw();
    })

    // Boton Descargar
    $(document).on('click', '.js-activar-excel', function (e) {
        e.preventDefault();
        tabla_filtro.button('.buttons-excel').trigger();
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

//////////////////////// Para resetear el modal al dar click en nuevo /////////////////////

$('#btnNuevoM').on('click', function () {

    //  RESET TOTAL DE ESTADO
    wizardMode = 'create';
    $('#btnSave').text('Guardar');

    // Limpiar formulario y estados
    $('#formWizard')[0].reset();
    $('#summaryContent').empty().hide();
    $('#ajaxError').addClass('d-none').empty();

    currentStep = 1;
    totalSteps = $('#modalNuevo .step').length;

    // Quitar disabled / readonly previos
    $('#formWizard')
        .find('input, select')
        .prop('disabled', false)
        .prop('readonly', false)
        .removeAttr('data-disabled-by-profile');

    // Reset wizard
    currentStep = 1;
    totalSteps = $('#modalNuevo .step').length;

    showStep(currentStep);

    // Abrir modal
    $('#modalNuevo').modal('show');
});

/////////////////////////// Se resetea el modal despues de cerrarlo /////////////////////////////

$('#modalNuevo').on('hidden.bs.modal', function () {

    // Limpieza final por seguridad
    $('#formWizard')[0].reset();

    $('#comp, #rescomp').prop('disabled', false);

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


$('#btnNuevoM').on('click', function () {

    wizardMode = 'create';

    $('#formWizard')[0].reset();
    $('#summaryContent').empty().hide();
    $('#ajaxError').addClass('d-none').empty();

    currentStep = 1;
    totalSteps = $('#modalNuevo .step').length;

    showStep(currentStep);
    $('#modalNuevo').modal('show');
});


// Reiniciar wizard correctamente al abrir el modal Cliente
$('#modalNuevo').on('shown.bs.modal', function () {

    totalSteps = $('#modalNuevo .step').length;
    showStep(currentStep);

    if (wizardMode === 'edit') return; // PAra que no borre en editar

});


function buildSummary() {
    const summary = [
        { label: 'Nombre', value: $('#nom').val() },
        { label: 'Usuario', value: $('#uss').val() },
        { label: 'Evento', value: $('#evento option:selected').text() }
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

///////////////////////////////// EDITAR ///////////////////////////////////////
let userProfile =  (PERFIL_USUARIO || '').toLowerCase();

$('#tabla-fil').on('click', '.btnEditar', function () {

    wizardMode = 'edit';
    editId = $(this).data('id');

    $('#myModalLabel').text('Editar Evento');
    $('#btnSave').text('Actualizar');

    cargarDatosColaborador(editId);
});

function cargarDatosColaborador(Id) {
    $.ajax({
        url: BASE_URL + '/config/get_fil.php',
        type: 'POST',
        dataType: 'json',
        data: { id: Id},
        success: function (data) {

            wizardMode = 'edit';

            // Paso 1
            $('#id').val(data.id);
            $('#nom').val(data.nombre);
            $('#uss').val(data.usuario);
            $('#evento').val(data.id_evento);

            // Bloquear todos los campos menos evento
            $('#nom').prop('disabled', true);
            $('#uss').prop('disabled', true);
            $('#evento').prop('disabled', false);

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

///////////////////////////////////////////////////////////////////////

//////////////////////// GUARDAR /////////////////////////////////////////

$('#formWizard').on('submit', function (e) {
    e.preventDefault();   

    // Seguridad: solo permitir envío en último paso
    if (currentStep !== totalSteps) return;

    $('#ajaxError').addClass('hidden').empty();

    // UI bloqueada
    $('#btnSave').prop('disabled', true);
    $('#btnSave .btn-text').addClass('hidden');
    $('#saveSpinner').removeClass('hidden');

    // 🔓 Desbloquear selects bloqueados por perfil
    $('#formWizard select[data-disabled-by-profile]')
        .prop('disabled', false);

    $.ajax({
        url: BASE_URL + '/save/update_filtro.php',
        type: 'POST',
        data: $(this).serialize(),
        dataType: 'json',

        success: function (resp) {

            if (resp.success) {

                alertify.success('Registro actualizado');

                // Cerrar modal
                $('#modalNuevo').modal('hide');

                // Refrescar DataTable si existe
                if ($.fn.DataTable.isDataTable('#tabla-fil')) {
                    $('#tabla-fil').DataTable().ajax.reload(null, false);
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

