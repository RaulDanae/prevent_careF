// Hace llamado a la tabla y aplica paginacion -->
  let tabla_pulmonar

  $(document).ready(function(){

    tabla_pulmonar = $('#tabla-cpu').DataTable({
      scrollX: true,
      autoWidth: true,
      responsive: false,
      scrollY: 450,
      processing: true,
      serverSide: true,
      order: [],
      destroy: true,
      language: {
        url: "../assets/js/Spanish.json"
      },

      "ajax": {
        url: "../partials/rellcpu.php",
        type: "POST",
        },

      columnDefs: [
        { targets: 0, orderable: false },
        { targets: "_all", defaultContent: "" }
      ],

      dom:      
         "<'row mb-2'<'col-md-6'l><'col-md-6 text-end'f>>" +
         "rt" +
         "<'row'<'col-md-6'i><'col-md-6'p>>",
      buttons: [
        {
          extend: "excelHtml5",
          className: 'buttons-excel d-none', //Oculto
          title: 'Actualizacion',
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

      initComplete: function() {
        this.api().columns.adjust();
      }

    });

    // Boton Buscar
    $('#btn_search').click(function() {
      tabla_pulmonar.draw();
    })

    // Boton Descargar
    $('#btndescargar').on('click', function () {
      tabla_pulmonar.button('.buttons-excel').trigger();
    });

  });

/////////////////////////////////// Modulo Nuevo /////////////////////////////////////////////////

///////////////////////////Modal Salir //////////////////////////////////////////////////////////
$('#modalNuevo').on('hide.bs.modal', function () {
    const btnOpen = document.getElementById('btndescargar');
    if (btnOpen) {
        btnOpen.focus();
    }
});

$(document).on('click', '#btnCloseModal', function () {
    const modalEl = document.getElementById('modalNuevo');
    const modal = bootstrap.Modal.getInstance(modalEl);
    modal.hide();
});
/////////////////////////////////////////////////////////////////////////////////////////////

/////////////////////////// Se resetea el modal despues de cerrarlo /////////////////////////////
$('#modalNuevo').on('hidden.bs.modal', function () {

    // Limpieza final por seguridad
    $('#formWizard')[0].reset();
    $('#summaryContent').empty().hide();

    wizardMode = 'create';
    editMemberId = null;
});
////////////////////////////////////////////////////////////////////////////////////////////////

///////////////////////// STEPS ///////////////////////////////////////////////////////////////
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
/////////////////////////////////////////////////////////////////////////////

//////////////////////////// SUMMARY ////////////////////////////////////////
function buildSummary() {
    const summary = [
        { label: 'FVC', value: $('#fvc').val() },
        { label: 'FEV1', value: $('#fev1').val() },
        { label: 'FEV1_FVC', value: $('#fevfvc').val() },
        { label: 'Observaciones', value: $('#observaciones').val() }
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

/////////////////////////////////////////////////////////////////////////////

///////////////////////////////// EDITAR ///////////////////////////////////////
let userProfile =  (PERFIL_USUARIO || '').toLowerCase();

$('#tabla-cpu').on('click', '.btnEditar', function () {

    wizardMode = 'edit';
    editcurp = $(this).data('curp');

    $('#myModalLabel').text('Editar Vitales');
    $('#btnSave').text('Actualizar');

    cargarDatosColaborador(editcurp);
});

function cargarDatosColaborador(CURP) {
    $.ajax({
        url: '../config/get_cpu.php',
        type: 'POST',
        dataType: 'json',
        data: { curp: CURP },
        success: function (data) {

            wizardMode = 'edit';

            // Paso 1
            $('#curp').val(data.curp);
            $('#nombre').val(data.colaborador);
            $('#genero').val(data.genero);
            $('#fnacimiento').val(data.fec_nac);
            $('#peso').val(data.peso);
            $('#talla').val(data.talla);
            $('#edad').val(data.edad);

            // Paso 2
            $('#fvc').val(data.fvc);
            $('#fev1').val(data.fev1);
            $('#fevfvc').val(data.fev1_fvc);
            $('#observaciones').val(data.obs_pul);

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
        url: '../save/update_pulmonar.php',
        type: 'POST',
        data: $(this).serialize(),
        dataType: 'json',

        success: function (resp) {

            if (resp.success) {

                alertify.success('Registro actualizado');

                // Cerrar modal
                $('#modalNuevo').modal('hide');

                // Refrescar DataTable si existe
                if ($.fn.DataTable.isDataTable('#tabla-cpu')) {
                    $('#tabla-cpu').DataTable().ajax.reload(null, false);
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

