// Hace llamado a la tabla y aplica paginacion -->
  let tabla_registros

  $(document).ready(function(){

    tabla_registros = $('#tabla-reg').DataTable({
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
        url: BASE_URL + "/partials/rellreg.php",
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
      tabla_registros.draw();
    })

    // Boton Descargar
    $('#btndescargar').on('click', function () {
      tabla_registros.button('.buttons-excel').trigger();
    });

  });


/////////////////////////////////// Modulo Nuevo /////////////////////////////////////////////////

//////////////////////////////// Calcular Edad //////////////////////////////////////////////////

function calcularEdadEntreFechas(fechaNacimiento, fechaRegistro) {
    if (!fechaNacimiento || !fechaRegistro) return '';

    const nacimiento = new Date(fechaNacimiento);
    const registro = new Date(fechaRegistro);

    if (isNaN(nacimiento) || isNaN(registro)) return '';

    let edad = registro.getFullYear() - nacimiento.getFullYear();
    const mes = registro.getMonth() - nacimiento.getMonth();

    if (mes < 0 || (mes === 0 && registro.getDate() < nacimiento.getDate())) {
        edad--;
    }

    return edad >= 0 ? edad : '';
}

function actualizarEdad() {
    const fnacimiento = $('#fnacimiento').val();
    const fregistro = $('#fregistro').val();

    if (!fnacimiento || !fregistro) return;

    const edad = calcularEdadEntreFechas(fnacimiento, fregistro);
    $('#edad').val(edad);
}

$('#fnacimiento').on('change', actualizarEdad);
$('#fregistro').on('change', actualizarEdad);


///////////////////////////Modal Salir //////////////////////////////////////////////////////////
$(document).on('click', '#modalNuevo .btn-close', function () {
    this.blur();              // ← quitar foco del botón cerrar
    document.getElementById('btnNuevoM').focus(); // ← devolver foco
});

const btnOpen = document.getElementById('btnNuevoM');

$('#modalNuevo').on('hide.bs.modal', function () {
    btnOpen.focus();
});
/////////////////////////////////////////////////////////////////////////////////////////////

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

////////////////////////////////////////////////////////////////////////////////////////////////

/////////////////////////// Se resetea el modal despues de cerrarlo /////////////////////////////
$('#modalNuevo').on('hidden.bs.modal', function () {

    // Limpieza final por seguridad
    $('#formWizard')[0].reset();
    $('#summaryContent').empty().hide();

    wizardMode = 'create';
    editMemberId = null;
});
////////////////////////////////////////////////////////////////////////////////////////////////

//////////////////////////Grupo Id/////////////////////////////////////
$('#compania').on('change', function () {

    //if (wizardMode === 'edit') return;

    const cod_comp = $(this).find(':selected').data('grupo') || '';
    $('#cod_comp').val(cod_comp);
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

    //////////////////////////////////////////////////////////////////////////////////
    ///////////////// Calcular fecha y hora de registro en nuevo./////////////////////////////
    const $fregistro = $('#fregistro');
    const $hregistro = $('#hregistro');

    const now = new Date();

    // Fecha actual en formato YYYY-MM-DD (requerido por input[type=date])
     const fechaHoy = now.toISOString().slice(0, 10);

    // Hora actual HH:MM
     const horaActual = now.toTimeString().slice(0, 5);

    // Solo asignar si están vacíos
     if (!$fregistro.val()) {
         $fregistro.val(fechaHoy);
     }

     if (!$hregistro.val()) {
        $hregistro.val(horaActual);
     }
    /////////////////////////////////////////////////////////////////////////////////

    // 2. Bloqueo de cajas de texto
    const PERFIL = (PERFIL_USUARIO || '').toLowerCase();

    $('#cod_comp')
        .prop('readonly', true);
    $('#edad')
        .prop('readonly', true);
    
});

function buildSummary() {
    const summary = [
        { label: 'Codigo Compañia', value: $('#cod_comp').val() },
        { label: 'Compañia', value: $('#compania option:selected').text() },
        { label: 'Clave', value: $('#clave').val() },
        { label: 'Colaborador', value: $('#colaborador').val() },
        { label: 'Fec Nacimiento', value: $('#fnacimiento').val() },
        { label: 'Genero', value: $('#genero option:selected').text() },
        { label: 'CURP', value: $('#curp').val() },
        { label: 'Email', value: $('#email').val() },
        { label: 'RFC', value: $('#rfc').val() },
        { label: 'Edad', value: $('#edad').val() },
        { label: 'Privacidad', value: $('#privacidad option:selected').text() },
        { label: 'Consentimiento', value: $('#consentimiento option:selected').text() },
        { label: 'Observaciones', value: $('#observaciones').val() },
        { label: 'Fec Registro', value: $('#fregistro').val() },
        { label: 'Hor Registro', value: $('#hregistro').val() }
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
    

/// GUARDAR CLIENTE //////

$('#formWizard').on('submit', function (e) {
    e.preventDefault();   

    // Seguridad: solo permitir envío en último paso
    if (currentStep !== totalSteps) return;

    // Removemos el nombre de la empresa ya que no lo necesitamos
    $('#compania').removeAttr('name');

    $('#ajaxError').addClass('hidden').empty();

    // UI bloqueada
    $('#btnSave').prop('disabled', true);
    $('#btnSave .btn-text').addClass('hidden');
    $('#saveSpinner').removeClass('hidden');

    // 🔓 Desbloquear selects bloqueados por perfil
    $('#formWizard select[data-disabled-by-profile]')
        .prop('disabled', false);

    $.ajax({
        url: wizardMode === 'edit'
          ? '../save/update_colaborador.php' 
          : '../save/guardar_colaborador.php',
        type: 'POST',
        data: $(this).serialize(),
        dataType: 'json',

        success: function (resp) {

            if (resp.success) {

                alertify.success(
                  wizardMode === 'edit'
                    ? 'Registro actualizado'
                    : 'Registro guardado'
                );

                // Cerrar modal
                $('#modalNuevo').modal('hide');

                // Refrescar DataTable si existe
                if ($.fn.DataTable.isDataTable('#tabla-reg')) {
                    $('#tabla-reg').DataTable().ajax.reload(null, false);
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



let userProfile =  (PERFIL_USUARIO || '').toLowerCase();

$('#tabla-reg').on('click', '.btnEditar', function () {

    wizardMode = 'edit';
    editId = $(this).data('id');

    $('#myModalLabel').text('Editar Cliente');
    $('#btnSave').text('Actualizar');

    cargarDatosColaborador(editId);
});

function cargarDatosColaborador(Id) {

    ///////////////// Calcular fecha y hora de registro en nuevo./////////////////////////////
    const $fregistro = $('#fregistro');
    const $hregistro = $('#hregistro');

    const now = new Date();
    const fechaHoy = now.toLocaleDateString('en-CA'); // YYYY-MM-DD local
    const horaActual = now.toTimeString().slice(0, 5);

    /////////////////////////////////////////////////////////////////////////////////

    $.ajax({
        url: BASE_URL + '/config/get_registro.php',
        type: 'POST',
        dataType: 'json',
        data: { id: Id },
        success: function (data) {

            wizardMode = 'edit';

            // Paso 1
            $('#id').val(data.id);
            $('#cod_comp').val(data.cod_comp);
            $('#compania').val(data.compania);

            // Paso 2
            $('#clave').val(data.clave);
            $('#colaborador').val(data.colaborador);
            $('#fnacimiento').val(data.fec_nac);
            $('#genero').val(data.genero);
            $('#curp').val(data.curp);
            $('#email').val(data.email);
            $('#rfc').val(data.rfc || data.curp);
            $('#edad').val(data.edad);
            $('#privacidad').val(data.aprivacidad);
            $('#consentimiento').val(data.cinformado);
            $('#observaciones').val(data.obs_reg);
            $fregistro.val(data.fregistro || fechaHoy);
            $hregistro.val(data.hregistro || horaActual);

            // CALCULAR EDAD AL ABRIR
            actualizarEdad();

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

/////////////////////////// Para importar registros //////////////////////////////////

///// PAra que abra la ventana de carga de archivos ////////
$(document).on('click', '#btnExcel', function () {
    $('#excelFile').click();
});
/////////////////////////////////////////////////////////////

//////// AJAX para importar el registro ////////////////////
$('#excelFile').on('change', function () {

    let archivo = this.files[0];

    if (!archivo) return;

    let formData = new FormData();
    formData.append('excel', archivo);

    $.ajax({
        url: BASE_URL + '/save/importar_excel.php',
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
    const id = $(this).data('id');

    const w = window.open(
        '../partials/imprimir_brazalete.php?id=' + id,
        '_blank',
        'width=500,height=200'
    );

    w.focus();
});




