// Hace llamado a la tabla y aplica paginacion -->
  let tabla_evento

  $(document).ready(function(){

    tabla_evento = $('#tabla-eve').DataTable({
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
        url: BASE_URL + "/partials/relleve.php",
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
      tabla_evento.draw();
    })

    // Boton Descargar
    $(document).on('click', '.js-activar-excel', function (e) {
        e.preventDefault();
        tabla_evento.button('.buttons-excel').trigger();
    });

  });

/////////////////////////////////// Modulo Nuevo /////////////////////////////////////////////////

function onModalHiddenEvento() {

    // 🔹 Reset completo
    resetModalEvento();

    // 🔹 Manejo de foco (accesibilidad)
    if (document.querySelector('#btndescargar')) {
        safeFocus('#btndescargar');
    } else {
        safeFocus('#btnNuevoM');
    }
}


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

    resetModalEvento(); // TODO pasa por aquí

    $('#modalNuevo').modal('show');
});

/////////////////////////// Se resetea el modal despues de cerrarlo /////////////////////////////

$('#modalNuevo').on('hidden.bs.modal', onModalHiddenEvento);

/////////////////////////////////////////////////////////////////////

let wizardMode = 'create'; // create | edit
let editId = null;

let currentStep = 1;
let totalSteps = 0;

function showStep(step) {
    $('.step').removeClass('active');
    $('.step[data-step="' + step + '"]').addClass('active');

    if (step === 1) {
        sincronizarGlobal();
    }

    $('#btnPrev').toggle(step > 1);
    $('#btnNext').toggle(step < totalSteps);
    $('#btnSave').toggle(step === totalSteps);

    // CONTROL BOTÓN SINCRONIZAR
    actualizarBotonSync();

//    if (step === 3 && !perfilesCargados) {

//        let seleccionados = wizardMode === 'edit'
//            ? perfilesOriginales
//            : [];

//        cargarPerfiles(seleccionados);

//        perfilesCargados = true;
//    }

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

    // Validacion del step
    if (!validateStep(currentStep)) return;

    // Solo aplicar en paso donde estan sucursales
    if (currentStep ===1) {

        const global = $('#global').val();

        const checkboxes = $('#contenedorSucursales input[type="checkbox"]');
        const total = checkboxes.length;
        const seleccionadas = checkboxes.filter(':checked').length;

        // Caso: No global y ninguna seleccionada
        if (global === 'NO' && seleccionadas === 0){
            alertify.error('Debes seleccional al menos una sucursal');
            return;
        }

        // Caso: No global pero todas seleccionadas
        if (global === 'NO' && total > 0 && seleccionadas === total) {
            $('#global').val('SI').trigger('change');
            alertify.message('Se seleccionaron todas las sucursales. Evento marcado como GLOBAL');
        }
    }

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

// Reiniciar wizard correctamente al abrir el modal Cliente
$('#modalNuevo').on('shown.bs.modal', function () {

    totalSteps = $('#modalNuevo .step').length;
    showStep(currentStep);

    if (wizardMode === 'edit') return; // Importante no limpiar en edicion

    // Global por default
    $('#global').val('SI');

    // Limpiar sucursales
    //$('#contenedorSucursales').html(''); No deberia ser necesario ya que otras partes ya lo limpian

});


function buildSummary() {
    const summary = [
        { label: 'Nombre Evento', value: $('#descrip').val() },
        { label: 'Compañia', value: $('#compa option:selected').text() },
        { label: 'Tipo Evento', value: $('#tevento').val() },
        { label: 'Global', value: $('#global').val() },
        { label: 'Fecha Evento', value: $('#fevento').val() },
        { label: 'Nombre Corto', value: $('#nomcorto').val() }
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




//// Ocultar boton de Syncronizacion /////////////////
function actualizarBotonSync() {

    if (wizardMode !== 'edit') {
        $('#btnSyncPacientes').addClass('d-none');
        return;
    }

    if (currentStep !== totalSteps) {
        $('#btnSyncPacientes').addClass('d-none');
        return;
    }

    if (huboCambiosSucursales()) {
        $('#btnSyncPacientes')
            .removeClass('d-none')
            .addClass('btn-warning');
    } else {
        $('#btnSyncPacientes')
            .addClass('d-none')
            .removeClass('btn-warning');
    }
}

///////////////////////////// GUARDAR CLIENTE /////////////////////////////

$('#formWizard').on('submit', function (e) {
    e.preventDefault();   

    // Seguridad: solo permitir envío en último paso
    if (currentStep !== totalSteps) return;

    if (wizardMode === 'edit' && huboCambiosSucursales()) {
        alertify.warning('Las sucursales cambiaron. Recuerda sincronizar pacientes.');
    }

    // SOLUCIÓN CLAVE
    const checks = $('#contenedorSucursales input[type="checkbox"]');

    if ($('#global').val() === 'SI') {
        checks.prop('checked', true);
    }

    checks.prop('disabled', false);

    $('#ajaxError').addClass('hidden').empty();

    if (wizardMode === 'edit') {
        alertify.message('Si modificas sucursales, sincroniza pacientes manualmente');
    }

    // UI bloqueada
    $('#btnSave').prop('disabled', true);
    $('#btnSave .btn-text').addClass('hidden');
    $('#saveSpinner').removeClass('hidden');


    $.ajax({
        url: wizardMode === 'edit'
          ? '../save/update_eve.php' 
          : '../save/guardar_eve.php',
        type: 'POST',
        data: $(this).serialize(),
        dataType: 'json',

        success: function (resp) {

            if (resp.success) {

                alertify.success(
                  wizardMode === 'edit'
                    ? 'Evento actualizado'
                    : 'Evento guardado'
                );

                // MENSAJE DE PERFILES BLOQUEADOS
                if (resp.perfiles_bloqueados && resp.perfiles_bloqueados.length > 0) {
                    alertify.warning(
                        `Algunos perfiles no se eliminaron porque ya tienen datos (${resp.perfiles_bloqueados.length})`
                    );
                }

                if (wizardMode === 'edit' && resp.cambio_sucursales) {
                    alertify.warning('Las sucursales cambiaron. Debes sincronizar pacientes.');
                }                

                // Cerrar modal
                $('#modalNuevo').modal('hide');

                // Refrescar DataTable si existe
                if ($.fn.DataTable.isDataTable('#tabla-eve')) {
                    $('#tabla-eve').DataTable().ajax.reload(null, false);
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

$('#tabla-eve').on('click', '.btnEditar', function () {

    wizardMode = 'edit';
    editId = $(this).data('id');

    $('#myModalLabel').text('Editar Evento');
    $('#btnSave').text('Actualizar');

    cargarDatosEvento(editId);
});

let sucursalesOriginales = [];

function cargarDatosEvento(Id) {

    $.ajax({
        url: BASE_URL + '/config/get_eve.php',
        type: 'POST',
        data: { id: Id },
        dataType: 'json',

        success: function (data) {

            wizardMode = 'edit';

            //  GUARDAR ESTADO ORIGINAL
            sucursalesOriginales = data.sucursal.map(s => String(s.id_sucursal));

            // INFO PACIENTES
            if (data.total_pacientes > 0) {
                alertify.message(`Pacientes en evento: ${data.total_pacientes}`);
            }

            // OPCIONAL: GUARDAR ID EVENTO
            $('#btnSyncPacientes').data('id', data.id);

            // Bloqueo si ya hay captura
            if (data.tiene_captura) {

                // Deshabilitar checkboxes
                $('#contenedorSucursales input[type="checkbox"]').prop('disabled', true);

                // Deshabilitar global también
                $('#global').prop('readonly', true);

                // Mensaje al usuario
                alertify.warning('Este evento ya tiene datos capturados. No puedes modificar sucursales.');

            } 

            // Paso 1
            $('#id').val(data.id);
            $('#descrip').val(data.nombreevento);
            $('#compa').val(data.compania);
            $('#tevento').val(data.tevento);
            $('#global').val(data.global).trigger('change');
            $('#fevento').val(data.fevento);
            $('#nomcorto').val(data.ncorto);
            
            // Obtener IDs seleccionados
            const seleccionadas = data.sucursal.map(s => String(s.id_sucursal));

            // Cargar sucursales y marcar
            cargarSucursales(data.compania, seleccionadas, 'edit');

            setTimeout(() => {
                actualizarBotonSync();
            }, 300);

            // cargar perfiles en chips
            renderPerfilesSeleccionados(data.perfiles);
            
            // Abrir modal
            $('#modalNuevo').modal('show');

            // Estado inicial del wizard
            currentStep = 1;
            totalSteps = $('#modalNuevo .step').length;
            showStep(currentStep); // Mostrar paso sin resetear datos

            

        }
    });
}

function huboCambiosSucursales() {
    const actuales = $('#contenedorSucursales input:checked')
        .map(function(){ return $(this).val(); })
        .get();

    return JSON.stringify(actuales.sort()) !== JSON.stringify(sucursalesOriginales.sort());
}


///////////////////////////////// Para cargar sucursales //////////////////////////////////////
function cargarSucursales(idComp, seleccionadas = [], modo = 'nuevo') {

    if (!idComp) return;

    $.ajax({
        url: BASE_URL + '/config/get_sucursales.php',
        type: 'POST',
        data: { id_comp: idComp },
        dataType: 'json',

        success: function (data) {

            let html = '';

            data.forEach(s => {

                let checked = '';
                let disabled = '';

                if (modo === 'nuevo') {
                    // Nuevo: todo seleccionado
                    checked = 'checked';
                    disabled = 'disabled';
                } else {
                    // Edición: solo seleccionadas
                    checked = seleccionadas.includes(String(s.id_sucursal)) ? 'checked' : '';
                }

                html += `
                    <label class="sucursal-item">
                        <input type="checkbox"
                               name="sucursales[]"
                               class="chkSuc"
                               value="${s.id_sucursal}"
                               ${checked}
                               ${disabled}>
                        <span>${s.nombre_sucursal}</span>
                    </label>
                `;
            });

            $('#contenedorSucursales').html(html);

            // aplicar lógica de GLOBAL siempre
            sincronizarGlobal();
        }
    });
}


// Al hacer modificaciones en la compañia
$('#compa').on('change', function () {

    let idComp = $(this).val();

    // Reset global
    if (wizardMode === 'create') {
        $('#global').val('SI');
    }
    

    cargarSucursales(idComp);

});

//////////////////////// Habilitar cambio Global ////////////////////////////

$('#global').on('change', function () {

    sincronizarGlobal();

});


//////////////////////// Cargar Perfiles por edicion ///////////////////////////////
let perfilesBloqueados = [];
function renderPerfilesSeleccionados(perfiles){

    $('#perfilesSeleccionados').empty();
    perfilesBloqueados = [];

    perfiles.forEach(p => {

        if(p.bloqueado == 1){
            perfilesBloqueados.push(String(p.id_perfil));
        }

        let html = `
            <div class="perfil-chip"
                 id="perfil_${p.id_perfil}">

                <input type="checkbox"
                       name="perfiles[]"
                       value="${p.id_perfil}"
                       checked>

                <span>${p.nombre}</span>

                <button type="button"
                        class="btnEliminarPerfil"
                        data-id="${p.id_perfil}">
                    ×
                </button>
            </div>
        `;

        $('#perfilesSeleccionados').append(html);
    });

}

/////////////////////////// Actualizar Nombre Corto ////////////////////////////////
function actualizarNombreCorto() {

    let compaTexto = $('#compa option:selected').text();
    let fecha = $('#fevento').val();

    // Validar que ambos tengan valor
    if (!compaTexto || !fecha) {
        $('#nomcorto').val('');
        return;
    }

    // Formatear fecha (YYYY-MM-DD → DDMMYYYY o como quieras)
    let partes = fecha.split('-'); // [YYYY, MM, DD]
    let fechaFormateada = partes[2] + partes[1] + partes[0];

    // Concatenar
    let resultado = compaTexto.trim() + '_' + fechaFormateada;

    $('#nomcorto').val(resultado.toUpperCase());
}

/////////// Escucha cuando se modifica algo en compañia o fecha evento ///////////////////////////
$('#compa, #fevento').on('change', actualizarNombreCorto);


///////// Se actualiza nombre Corto ////////////////
$('#formWizard').on('submit', function (e) {
    actualizarNombreCorto();

        // habilitar TODOS antes de enviar
    $('#contenedorSucursales input[type="checkbox"]').prop('disabled', false);

});

////////////////// Necesario para la sincronizacion de los checkbox //////////////////////////////////
function sincronizarGlobal() {

    const valor = $('#global').val();
    const checkboxes = $('#contenedorSucursales input[type="checkbox"]');

    if (valor === 'SI') {
        checkboxes.prop('checked', true).prop('disabled', true);
    } else {
        checkboxes.prop('disabled', false);
    }
}

function resetModalEvento() {

    // Modo
    wizardMode = 'create';
    editId = null;

    // Reset form
    $('#formWizard')[0].reset();

    // Limpiar UI
    $('#summaryContent').empty().hide();
    $('#ajaxError').addClass('d-none').empty();

    // Reset wizard
    currentStep = 1;
    totalSteps = $('#modalNuevo .step').length;

    // OCULTAR BOTÓN
    $('#btnSyncPacientes').addClass('d-none');

    // Reset inputs (por si vienes de edición)
    $('#formWizard')
        .find('input, select')
        .prop('disabled', false)
        .prop('readonly', false)
        .removeAttr('data-disabled-by-profile');

    // Reset sucursales
    $('#contenedorSucursales').html('');

    // Reset global
    $('#global').val('SI');

    // Reset botón
    $('#btnSave').text('Guardar');

    // perfiles
    perfilesOriginales = [];
    perfilesCargos = false;

    // Ir a paso 1
    showStep(1);
}

function safeFocus(selector) {
    const el = document.querySelector(selector);
    if (el) {
        setTimeout(() => el.focus(), 100); // importante para Bootstrap
    }
}

//////////// Para que boton de sincronizar aparezca al editar ////////////////////////////
if (wizardMode === 'edit') {
    $('#btnSyncPacientes').removeClass('d-none');
} else {
    $('#btnSyncPacientes').addClass('d-none');
}

//////////// Para que mensaje de que se agregaran todos los registros solo se vean en editar ////////////////////////////
if (wizardMode === 'edit') {
    $('#msgSync').removeClass('d-none');
} else {
    $('#msgSync').addClass('d-none');
}

////////////////////////////////// Llamar a Sync Evento pacientew //////////////////////////////
$(document).on('click', '#btnSyncPacientes', function () {

    let id_evento = $('#id').val();

    if (!id_evento) {
        alertify.error('No hay evento seleccionado');
        return;
    }

    if (!confirm('¿Deseas sincronizar pacientes?')) return;

    $.ajax({
        url: '../save/sync_evento_pacientes.php',
        type: 'POST',
        data: { id_evento: id_evento },
        dataType: 'json',
        success: function(resp) {
            if (resp.success) {
                alertify.success('Pacientes sincronizados');
            } else {
                alertify.error(resp.message);
            }
        },
        error: function() {
            alertify.error('Error al sincronizar');
        }
    });

});

///////////////////////// Para seleccionar perfiles de estudio ///////////////////////////
$('#buscadorPerfiles').select2({
    dropdownParent: $('#modalNuevo'),
    theme: "bootstrap-5",
    width: '100%',
    placeholder: 'Buscar perfil...',
    minimumInputLength: 1,
    ajax: {
        url: BASE_URL + '/config/obten_perfil.php',
        dataType: 'json',
        delay: 250,
        data: function(params){
            return {
                term: params.term // clave
            };
        },
        processResults: function(data){
            return data; // YA viene en formato correcto
        }
    }
});    

/////////////// Agregar perfil como tarjeta ////////////////

$(document).on('select2:select', '#buscadorPerfiles', function(e){

    const data = e.params.data;

    // evitar duplicados
    if($('#perfil_'+data.id).length) return;

    let html = `
        <div class="perfil-chip border rounded px-2 py-1 d-flex align-items-center"
             id="perfil_${data.id}">

            <input type="checkbox"
                   class="me-2 perfil-check"
                   name="perfiles[]"
                   value="${data.id}"
                   checked>

            <span class="me-2">${data.text}</span>

            <button type="button"
                    class="btnEliminarPerfil"
                    data-id="${data.id}">
                ×
            </button>
        </div>
    `;

    $('#perfilesSeleccionados').append(html);

    // limpiar buscador
    $(this).val(null).trigger('change');
});

////// Eliminar perfil de tarjeta ///////////////////
$(document).on('click', '.btnEliminarPerfil', function(){

    let id = $(this).data('id');

    // 🔒 Validación opcional
    if(tieneDatosRelacionados(id)){
        alertify.error("No puedes eliminar este perfil");
        return;
    }

    $('#perfil_'+id).remove();
});


function tieneDatosRelacionados(id){
    return perfilesBloqueados.includes(String(id));
}



$(document).on('change', '#contenedorSucursales input[type="checkbox"]', function () {
    validarGlobalAutomatico();
    actualizarBotonSync();
});

let avisoGlobalMostrado = false;

function validarGlobalAutomatico() {

    const global = $('#global').val();
    const checks = $('#contenedorSucursales input[type="checkbox"]');

    const total = checks.length;
    const seleccionadas = checks.filter(':checked').length;

    if (global === 'NO' && total > 0 && seleccionadas === total) {

        $('#global').val('SI').trigger('change');

        if (!avisoGlobalMostrado) {
            alertify.message('Se seleccionaron todas las sucursales. Evento marcado como GLOBAL');
            avisoGlobalMostrado = true;
        }
    } else {
        avisoGlobalMostrado = false;
    }
}