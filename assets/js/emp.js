// Hace llamado a la tabla y aplica paginacion -->
  let tabla_compania

  $(document).ready(function(){

    tabla_compania = $('#tabla-emp').DataTable({
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
        url: BASE_URL + "/partials/rellemp.php",
        type: "POST",
        },

      columnDefs: [
        { targets: [1, 2, 3, 5, 8, 9], className: 'nowrap', orderable: false },
        { targets: "_all", defaultContent: "" }
      ],

      dom:      
         '<"d-flex justify-content-between align-items-center"l f>rtip',
      buttons: [
        {
          extend: "excelHtml5",
          className: 'buttons-excel d-none', //Oculto
          title: 'Compañias',
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
      tabla_compania.draw();
    })

    // Boton Descargar
    $(document).on('click', '.js-activar-excel', function (e) {
        e.preventDefault();
        tabla_compania.button('.buttons-excel').trigger();
    });

  });

/////////////////////////////////// Modulo Nuevo /////////////////////////////////////////////////

///////////////////////////Modal Salir //////////////////////////////////////////////////////////
function safeFocus(selector) {
    const el = document.querySelector(selector);
    if (el) el.focus();
}


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
    EmpresaMode = 'create';
    $('#btnSave').text('Guardar');

    // Limpiar formulario y estados
    $('#formEmpresa')[0].reset();
    $('#summaryContent').empty().hide();
    $('#ajaxError').addClass('d-none').empty();

    currentStep = 1;
    totalSteps = $('#modalNuevo .step').length;

    // Quitar disabled / readonly previos
    $('#formEmpresa')
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



/////////////////////////////////////////////////////////////////////

let EmpresaMode = 'create'; // create | edit
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

    EmpresaMode = 'create';

    $('#formEmpresa')[0].reset();
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

    if (EmpresaMode === 'edit') return; // PAra que no borre en editar

});


function buildSummary() {
    const summary = [
        { label: 'Nombre', value: $('#comp').val() },
        { label: 'Usuario', value: $('#rescomp').val() }
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

$('#formEmpresa').on('submit', function (e) {
    e.preventDefault();   

    $.ajax({
        url: EmpresaMode === 'edit'
          ? '../save/update_emp.php' 
          : '../save/guardar_emp.php',
        type: 'POST',
        data: $(this).serialize(),
        dataType: 'json',

        success: function (resp) {

            if (resp.success) {

                alertify.success(
                  EmpresaMode === 'edit'
                    ? 'Compañia actualizado'
                    : 'Compañia guardado'
                );

                // Quitar foco, para que cuando se cierre el modal no marque error
                document.activeElement.blur();

                // Cerrar modal
                $('#modalNuevo').modal('hide');

                // Refrescar DataTable si existe
                if ($.fn.DataTable.isDataTable('#tabla-emp')) {
                    $('#tabla-emp').DataTable().ajax.reload(null, false);
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
        }
    });
});

///////////////////////// EDICION //////////////////////////////

let editId = null;

$('#tabla-emp').on('click', '.btnEditar', function () {

    EmpresaMode = 'edit';
    editId = $(this).data('id');

    $('#myModalLabel').text('Editar Compañia');
    
    //limpiarModalEmpresa();

    $('#modalNuevo').modal('show');

    // Espera a qque el modal este listo
    setTimeout(() => {
        cargarDatosCompania(editId);
    }, 150);

});

function cargarDatosCompania(Id) {

    $.ajax({
        url: BASE_URL + '/config/get_emp.php',
        type: 'POST',
        data: { id: Id },
        dataType: 'json',
        success: function (data) {

            EmpresaMode = 'edit';

            // Campo oculto
            $('#id').val(data.id);

            // Datos compañia
            $('#comp').val(data.compania);
            $('#rescomp').val(data.nombre);
            $('#diremp').val(data.direccion);
            $('#razemp').val(data.razon);
            $('#rfcemp').val(data.rfc);
            $('#telemp').val(data.telefono);
            $('#nomcon').val(data.contacto);
            $('#gencon').val(data.genero);
            $('#telcon').val(data.telefonoc);
            $('#mailcon').val(data.mailc);

            // Sucursal
            let tbody = $('#bodySucursales');
            tbody.html('');

            if (data.sucursal && data.sucursal.length > 0) {
                data.sucursal.forEach(s => {
                    crearSucursalData(s.id_sucursal || '', s.nombre_sucursal || '');
                });
            } else {
                crearSucursalData();
            }

        },

        error: function (data) {
            if(data.error) {
                alertify.error(data.error);
                return;
            }
        }
    });

}

function crearSucursalData(id = '', nombre = '') {
    $('#bodySucursales').append(`
        <tr>
            <td>
                <input type="hidden" name="suc_id[]" value="${id}">
                <input type="text" name="suc_nombre[]" class="form-control" value="${nombre}">
            </td>
            <td class="text-center">
                <button type="button" class="btn btn-warning btn-sm eliminarFila">
                    <i class="fa-solid fa-trash"></i>
                </button>
            </td>
        </tr>
    `)
}

//// Agregar Rangos dinamicamente ////
$(document).on("click", ".btnAgregarSucursal", function() {
    let tabla = $("#bodySucursales");

    tabla.append(`
        <tr>
            <td>
                <input type="hidden" name="suc_id[]" value="">
                <input type="text" name="suc_nombre[]" class="form-control">
            </td>
            <td class="text-center">
                <button type="button" class="btn btn-warning btn-sm eliminarFila">
                    <i class="fa-solid fa-trash"></i>
                </button>
            </td>
        </tr>
    `);
});

///// Eliminar rango /////
$(document).on("click", ".eliminarFila", function() {
    let fila = $("#bodySucursales tr");
    if(fila.length>1){
        $(this).closest("tr").remove();
    }
});

//////////////// Activar la funcion de limpieza del modal al cerrar el modal //////////////////////
$('#modalNuevo').on('hidden.bs.modal', function () {

    EmpresaMode = 'create';
    limpiarModalEmpresa();

    // solo mover foco aqui
    $('[data-bs-target="#modalNuevo"]').trigger('focus');

});

$(document).on('hide.bs.modal', '#modalNuevo', function () {
    document.activeElement.blur();
});

///////////////// Funcion para limpiar el modal /////////////////////////////////////////////
function limpiarModalEmpresa(){

    // reset form
    $('#formEmpresa')[0].reset();

    // limpiar tabla de sucursales
    let tabla = $("#bodySucursales");

    // agregar fila inicial
    tabla.html(`
        <tr>
            <td>
                <input type="hidden" name="suc_id[]" value="">
                <input type="text" name="suc_nombre[]" class="form-control">
            </td>
            <td class="text-center">
                <button type="button" class="btn btn-sm btn-warning eliminarFila">
                    <i class="fa-solid fa-trash"></i>
                </button>
            </td>
        </tr>
    `);

}