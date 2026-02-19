// Hace llamado a la tabla y aplica paginacion -->
  let tabla_alba

  $(document).ready(function(){

    tabla_alba = $('#tabla-alb').DataTable({
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
        url: "../partials/rellalb.php",
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
          title: 'Usuarios',
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
      tabla_alba.draw();
    })

    // Boton Descargar
    $('#btndescargar').on('click', function () {
      tabla_alba.button('.buttons-excel').trigger();
    });

  });

/////////////////////////////////// Modulo Nuevo /////////////////////////////////////////////////

///////////////////////////Modal Salir //////////////////////////////////////////////////////////
$(document).on('click', '#modalNuevo .btn-close', function () {
    this.blur();              // ← quitar foco del botón cerrar
    document.getElementById('btndescargar').focus(); // ← devolver foco
});

const btnOpen = document.getElementById('btndescargar');

$('#modalNuevo').on('hide.bs.modal', function () {
    btnOpen.focus();
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

    $('#nom, #uss, #perfil, #estatus').prop('disabled', false);

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
        { label: 'Perfil', value: $('#perfil option:selected').text() },
        { label: 'Estatus', value: $('#estatus option:selected').text() }
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


///////////////////////// Validacion de password ///////////////////////////////////

const idInput = document.getElementById("id");
const p1 = document.getElementById("pas1");
const p2 = document.getElementById("pas2");
const icon = document.getElementById("iconValidacion");
const btnNext = document.getElementById("btnNext");

function validarPasswords() {

  const esEdicion = idInput.value !== "";
  const v1 = p1.value.trim();
  const v2 = p2.value.trim();

  // Si es creacion el password es obligatorio
  if (!esEdicion) {

    if (v1 === "" && v2 === "") {
        icon.style.display = "none";
        btnNext.disabled = false;
        return;
    }

    if (v1.value !== v2.value) {
      btnNext.disabled = true;
      return;
    }


  } else {

    // Si es edicion -> solo validar si escriben algo
    if(v1 !== "" & v2 !== "") {
      if (v1.value !== v2.value){
        btnNext.disabled = true
        return;
      }
    }
  }

    icon.style.display = "inline";

    if (v1 === v2) {
        icon.className = "fa-solid fa-check text-success";
        btnNext.disabled = false;
    } else {
        icon.className = "fa-solid fa-xmark text-danger";
        btnNext.disabled = true;
    }


}

p1.addEventListener("keyup", validarPasswords);
p2.addEventListener("keyup", validarPasswords);


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

    // 🔓 Desbloquear selects bloqueados por perfil
    $('#formWizard select[data-disabled-by-profile]')
        .prop('disabled', false);

    $.ajax({
        url: wizardMode === 'edit'
          ? '../save/update_alba.php' 
          : '../save/guardar_alba.php',
        type: 'POST',
        data: $(this).serialize(),
        dataType: 'json',

        success: function (resp) {

            if (resp.success) {

                alertify.success(
                  wizardMode === 'edit'
                    ? 'Usuario actualizado'
                    : 'Usuario guardado'
                );

                // Cerrar modal
                $('#modalNuevo').modal('hide');

                // Refrescar DataTable si existe
                if ($.fn.DataTable.isDataTable('#tabla-alb')) {
                    $('#tabla-alb').DataTable().ajax.reload(null, false);
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

$('#tabla-alb').on('click', '.btnEditar', function () {

    wizardMode = 'edit';
    editId = $(this).data('id');

    $('#myModalLabel').text('Editar Usuario');
    $('#btnSave').text('Actualizar');

    cargarDatosUsuario(editId);
});

function cargarDatosUsuario(Id) {

        $.ajax({
        url: '../config/get_usuario.php',
        type: 'POST',
        dataType: 'json',
        data: { id: Id },
        success: function (data) {

            wizardMode = 'edit';

            // Paso 1
            $('#id').val(data.id);
            $('#nom').val(data.nombre);
            $('#uss').val(data.usuario);
            $('#perfil').val(data.perfil);
            $('#estatus').val(data.estatus);

            const esAdmin = (userProfile === 'adminis' || userProfile === 'supervi');

            if (!esAdmin) {
                // Bloquear todos los campos menos contraseña
                $('#nom').prop('disabled', true);
                $('#uss').prop('disabled', true);
                $('#perfil').prop('disabled', true);
                $('#estatus').prop('disabled', true);
            } else {
                // Desbloquear todos los campos
                $('#nom').prop('disabled', false);
                $('#uss').prop('disabled', false);
                $('#perfil').prop('disabled', false);
                $('#estatus').prop('disabled', false);

            }

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