// perfil -> estudios
let estudiosPorPerfil = {};

// estudio -> perfiles
let perfilesPorEstudio = {};

// Hace llamado a la tabla y aplica paginacion -->
  let tabla_recep

  $(document).ready(function(){

    tabla_recep = $('#tabla-rcp').DataTable({
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
        url: BASE_URL + "/partials/rellrecep.php",
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
          title: 'Estudios Paciente',
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

        const tabla = this.api();

        this.api().columns.adjust();

        // 1. Quitar evento default de DataTables
        $('#tabla-rcp_filter input').off();

        // 2. Agregar tu propio control
        $('#tabla-rcp_filter input').on('input', function () {

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
      tabla_recep.draw();
    })

  });

    // Boton Descargar
    $(document).on('click', '.js-activar-excel', function (e) {
        e.preventDefault();
        tabla_recep.button('.buttons-excel').trigger();
    });

//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

///////////////////////////////////////////////////// MODAL RECEPCION ///////////////////////////////////////////////////

let userProfile =  (PERFIL_USUARIO || '').toLowerCase();

$('#tabla-rcp').on('click', '.btnEditar', function () {

    wizardMode = 'edit';
    editId = $(this).data('id');

    $('#myModalLabel').text('Editar Estudios');
    $('#btnSave').text('Actualizar');

    cargarDatosEstudios(editId);
});

let estudiosOriginales = [];

function cargarDatosEstudios(Id) {

    $.ajax({
        url: BASE_URL + '/config/get_recep.php',
        type: 'POST',
        data: { id: Id },
        dataType: 'json',

        success: function (data) {

            // Guardar originales para poder ver los cambios que se haran al guardar perfiles
            window.perfilesOriginales =
                data.perfiles.map(p => String(p.id_perfil));
            // Guardar originales para poder ver los cambios que se haran al guardar estudios
            window.estudiosOriginales =
                data.estudios.map(e => String(e.id_estudio));            

            wizardMode = 'edit';

            // Paciente
            $('[name="id_paciente_evento"]').val(data.paciente.id_paciente_evento);
            $('[name="empleado"]').val(data.paciente.colaborador);
            $('[name="empresa"]').val(data.paciente.nomcom);
            $('[name="sucursales"]').val(data.paciente.nombre_sucursal);            

            // Limpiar relaciones
            estudiosPorPerfil = {};
            perfilesPorEstudio = {};

            // redner perfiles
            renderPerfilesSeleccionados(data.perfiles);

            // Generar estudios
            renderEstudiosSeleccionados(data.estudios);
            

            $('#modalNuevo').modal('show');            

        }
    });
}

function renderEstudios(estudios){

    let html = '';

    $('#bodyEstudios').empty();

    estudios.forEach(e => {

        html += `
        <tr>
                

            <!--Id Paciente Evento, Id Estudio, Nombre Estudio -->
            <td>
                <select class="form-control selectEstudio" name="est_id[]" style="width:100%" required>
                    <option value="${e.id_estudio}" selected>${e.nombre}</option>  
                </select>
            </td>

            <!-- Boton -->
            <td class="text-center">
                <button type="button" class="btn btn-warning btn-sm eliminarFila">
                    <i class="fa fa-trash"></i>
                </button>
            </td>
        </tr>`;
    });

    $('#bodyEstudios').html(html);
}

////////////////////////////////////////// Cargar perfiles por edicion /////////////////////////////
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


/////////////////////////////////// Para buscar Perfiles ///////////////////////////////////////////

$('#buscadorPerfiles').select2({
    dropdownParent: $('#modalNuevo'),
    theme: "bootstrap-5",
    width: '100%',
    placeholder: 'Buscar perfil...',
    minimumInputLength: 1,
    ajax: {
        url: BASE_URL + '/config/obten_perfil.php', // ajusta a tu endpoint real
        dataType: 'json',
        delay: 250,
        data: function (params) {
            return {
                term: params.term
            };
        },
        processResults: function (data) {
            return data; // Ya viene en formato correcto
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

    agregarEstudiosDesdePerfil(data.id);

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

    quitarPerfil(id);
});

async function quitarPerfil(idPerfil){

    idPerfil = String(idPerfil);

    if(tieneDatosRelacionados(idPerfil)){
        alertify.error("No puedes eliminar este perfil");
        return;
    }

    let estudios = estudiosPorPerfil[idPerfil] || [];

    let id_paev =
        $('[name="id_paciente_evento"]').val();

    console.log("Quitando perfil:", idPerfil);
    console.log("Estudios del perfil:", estudios);
    console.log("perfilesPorEstudio:", perfilesPorEstudio);

    for(let idEstudio of estudios){

        // quitar relación Perfil -> Estudios
        if(perfilesPorEstudio[idEstudio]){

            perfilesPorEstudio[idEstudio] =
                perfilesPorEstudio[idEstudio]
                .filter(p => String(p) !== String(idPerfil)
            );
        }

        // otro perfil aún usa estudio
        if(
            perfilesPorEstudio[idEstudio] &&
            perfilesPorEstudio[idEstudio].length > 0
        ){
            continue;
        }

        let bloqueado = false;

        // validar resultados
        if(id_paev){

            try{

                let resp = await fetch(
                    `${BASE_URL}/config/validar_eliminar_estudio.php`,
                    {
                        method: "POST",
                        headers: {
                            "Content-Type":
                            "application/x-www-form-urlencoded"
                        },
                        body:
                        `id_estudio=${idEstudio}&id_paev=${id_paev}`
                    }
                );

                let data = await resp.json();

                bloqueado = data.bloqueado;

            }catch(e){
                console.error(e);
            }
        }

        // 🔥 tiene resultados -> conservar
        if(bloqueado){

            alertify.warning(
                `El estudio ${idEstudio} tiene resultados y se conservará`
            );

            continue;
        }

        console.log("Intentando eliminar estudio:", idEstudio);

        // eliminar visualmente
        $('#bodyEstudios tr').each(function(){
            
            let val = $(this)
                .find('select[name="est_id[]"]')
                .val();
                
            if(String(val) === String(idEstudio)){
                $(this).remove();
            }

        });

        // limpiar referencia
        delete perfilesPorEstudio[idEstudio];
    }

    // eliminar perfil
    delete estudiosPorPerfil[idPerfil];

    // eliminar chip
    $('#perfil_'+idPerfil).remove();

    console.log("estudiosPorPerfil", estudiosPorPerfil);
    console.log("perfilesPorEstudio", perfilesPorEstudio);
}

function tieneDatosRelacionados(id){
    return perfilesBloqueados.includes(String(id));
}

//////////////////////// Agregar Estudios para Edicion ////////////////////////////////
function renderEstudiosSeleccionados(estudios){

    $('#bodyEstudios').empty();

    estudios.forEach(est => {

        let idEstudio = String(est.id_estudio);

        let fila = `
            <tr data-estudio="${idEstudio}">
                <td>
                    <select class="form-control selectEstudio"
                            name="est_id[]"
                            style="width:100%"
                            required>

                        <option value="${est.id_estudio}" selected>
                            ${est.nombre}
                        </option>

                    </select>
                </td>

                <td class="text-center">
                    <button type="button"
                            class="btn btn-warning btn-sm eliminarFila">
                            <i class="fa fa-trash"></i>
                    </button>
                </td>
            </tr>
        `;

        $('#bodyEstudios').append(fila);

    });

    $('.selectEstudio').each(function(){

        if(!$(this).hasClass("select2-hidden-accessible")){
            inicializarSelectEstudio($(this));
        }
    });
}


//////////////////////// Agregar Estudios por Perfil ////////////////////////////////
function agregarEstudiosDesdePerfil(id_perfil){

    fetch(`${BASE_URL}/config/obten_estudios_perfil.php?id_perfil=${id_perfil}`)
    .then(res => res.json())
    .then(estudios => {

        // inicializar arreglo del perfil
        if(!estudiosPorPerfil[id_perfil]){
            estudiosPorPerfil[id_perfil] = [];
        }

        estudios.forEach(est => {

            let idEstudio = String(est.id);
            let idPerfil = String(id_perfil);

            // ========================================
            // RELACION PERFIL -> ESTUDIOS
            // ========================================

            if(!estudiosPorPerfil[idPerfil].includes(idEstudio)){
                estudiosPorPerfil[idPerfil].push(idEstudio);
            }

            // ========================================
            // RELACION ESTUDIO -> PERFILES
            // ========================================

            if(!perfilesPorEstudio[idEstudio]){
                perfilesPorEstudio[idEstudio] = [];
            }

            if(!perfilesPorEstudio[idEstudio].includes(idPerfil)){
                perfilesPorEstudio[idEstudio].push(idPerfil);
            }

            // ========================================
            // VALIDAR SI YA EXISTE EN TABLA
            // ========================================

            let filaExistente =
                $(`#bodyEstudios tr[data-estudio="${idEstudio}"]`);

            if(filaExistente.length > 0){
                return;
            }

            // ========================================
            // CREAR FILA
            // ========================================

            let fila = `
                <tr data-estudio="${idEstudio}">
                    <td>
                        <select class="form-control selectEstudio"
                                name="est_id[]"
                                style="width:100%"
                                required>
                                
                            <option value="${est.id}" selected>
                                ${est.nombre}
                            </option>
                        
                        </select>
                    </td>
                    
                    <td class="text-center">
                        <button type="button"
                                class="btn btn-warning btn-sm eliminarFila">
                                <i class="fa fa-trash"></i>
                        </button>
                    </td>
                </tr>
            `;

            $('#bodyEstudios').append(fila);

        });

        // Reinicializar select2
        $('.selectEstudio').each(function(){

            if(!$(this).hasClass("select2-hidden-accesible")){
                inicializarSelectEstudio($(this));
            }
        });

        console.log("estudiosPorPerfil", estudiosPorPerfil);
        console.log("perfilesPorEstudio", perfilesPorEstudio);

    });

}





//////////////////////// Para resetear el modal al dar click en nuevo /////////////////////

$('#btnNuevoM').on('click', function () {

    resetModalEvento(); // TODO pasa por aquí

    $('#modalNuevo').modal('show');

});

/////////////////////////// Se resetea el modal despues de cerrarlo /////////////////////////////

$('#modalNuevo').on('hidden.bs.modal', onModalHiddenEvento);

function onModalHiddenEvento() {

    // Limpiamos diccionarios
    estudiosPorPerfil = {};
    perfilesPorEstudio = {};

    // 🔹 Reset completo
    resetModalEvento();

    // 🔹 Manejo de foco (accesibilidad)
    if (document.querySelector('#btndescargar')) {
        safeFocus('#btndescargar');
    } else {
        safeFocus('#btnNuevoM');
    }
}

function resetModalEvento() {

    // Limpiar bloqueados
    perfilesBloqueados = [];

    // limpiar relaciones
    estudiosPorPerfil = {};
    perfilesPorEstudio = {};

    // Modo
    wizardMode = 'create';
    editId = null;

    // Reset form
    $('#formPaciente')[0].reset();

    // Limpiar Select2
    $('#buscadorPerfiles').val(null).trigger('change');

    // Limpiar CHIPS de Perfiles
    $('#perfilesSeleccionados').empty();

    // Limpiar tabla de estudios
    $('#bodyEstudios').empty();

    // (opcional) dejar una fila base
    $('#bodyEstudios').empty();

    // Limpiar bloqueados
    perfilesBloqueados = [];

    // Limpiar UI
    $('#summaryContent').empty().hide();
    $('#ajaxError').addClass('d-none').empty();

    // Reset inputs (por si vienes de edición)
    $('#formPaciente')
        .find('input, select')
        .prop('disabled', false)
        .prop('readonly', false)
        .removeAttr('data-disabled-by-profile');

    // Reset botón
    $('#btnSave').text('Guardar');

}


//// Agregar Estudios dinamicamente ////
$(document).on("click", ".btnAgregarEstudios", function() {
    let tabla = $("#bodyEstudios");

    tabla.append(`
        <tr>
            <td>
                <select class = "form-control selectEstudio" name="est_id[]" style = "width:100%" required></select>
            </td>
            <td class = "text-center">
                <button type="button" class="btn btn-warning btn-sm eliminarFila" data-bs-toggle="tooltip"
                                      data-bs-placement="top" title="Elimina Estudio">
                    <i class="fa-solid fa-trash"></i>
                </button>
            </td>
        </tr>
    `);
    // Inicializar select2 en la nueva fila
    inicializarSelectEstudio($('.selectEstudio').last());
});

function inicializarSelectEstudio(elemento){

    elemento.select2({
        dropdownParent: $('#modalNuevo'),
        theme: "bootstrap-5",
        width: '100%',
        placeholder: 'Buscar estudio...',
        minimumInputLength: 1,
        ajax: {
            url: BASE_URL + '/config/obten_estudio.php', // endpoint
            dataType: 'json',
            delay: 250,
            data: function (params) {
                return {
                    term: params.term
                };
            },
            processResults: function (data) {
                return data; // {results:[{id,text}]}
            }
        }
    });

    // cuando seleccionas estudio
    elemento.on('select2:select', function(e){

        const data = e.params.data;

        let existe = false;

        $('select[name="est_id[]"]').not(this).each(function(){
            if($(this).val() == data.id){
                existe = true;
            }
        });

        if(existe){
            alert("Este estudio ya está agregado");
            $(this).val(null).trigger('change');
            return;
        }

    });
}



///// Eliminar estudio /////
$(document).on("click", ".eliminarFila", async function () {

    let fila = $(this).closest("tr");

    let id_estudio = fila.find('select[name="est_id[]"]').val();

    let id_paev = $('[name="id_paciente_evento"]').val();

    // Si la fila está vacía o aún no se selecciona estudio
    if (!id_estudio) {
        fila.remove();
        return;
    }

    // Si es registro nuevo aún no guardado
    if (!id_paev) {
        fila.remove();
        return;
    }

    try {

        let resp = await fetch(
            `${BASE_URL}/config/validar_eliminar_estudio.php`,
            {
                method: "POST",
                headers: {
                    "Content-Type": "application/x-www-form-urlencoded"
                },
                body: `id_estudio=${id_estudio}&id_paev=${id_paev}`
            }
        );

        let data = await resp.json();

        // Tiene resultados
        if (data.bloqueado) {

            alertify.error(
                "Este estudio ya tiene resultados y no puede eliminarse"
            );

            return;
        }

        // No tiene resultados
        fila.remove();

    } catch (e) {

        console.error(e);

        alertify.error(
            "Error al validar si el estudio puede eliminarse"
        );
    }

});



// Funcion para obtener que perfiles se quedaron despues de una modificacion
function obtenerPerfilesActuales(){

    let perfiles = [];

    $('input[name="perfiles[]"]:checked').each(function(){
        perfiles.push(String($(this).val()));
    });

    return perfiles;
}

// Funcion para obtener los estudios que se quedaron despues de una modificacion
function obtenerEstudiosActuales(){

    let estudios = [];

    $('select[name="est_id[]"]').each(function(){

        let val = $(this).val();

        if(val && !estudios.includes(String(val))){
            estudios.push(String(val));
        }

    });

    return estudios;
}


// Funcion que ayuda a contrastar el registro anterior con el arctual y asi identificar que se agrego y que se elimino
function diferenciaArrays(originales, actuales){

    return {
        agregados: actuales.filter(x => !originales.includes(x)),
        eliminados: originales.filter(x => !actuales.includes(x))
    };
}


///////////////////////// GUARDAR ///////////////////////////////
$('#formPaciente').on('submit', async function(e){

    e.preventDefault();

    let perfilesActuales = obtenerPerfilesActuales();

    let estudiosActuales = obtenerEstudiosActuales();

    // Comparaciones
    let cambiosPerfiles =
        diferenciaArrays(
            window.perfilesOriginales || [],
            perfilesActuales
        );

    let cambiosEstudios =
        diferenciaArrays(
            window.estudiosOriginales || [],
            estudiosActuales
        );

    let payload = {

        id_paciente_evento:
            $('[name="id_paciente_evento"]').val(),

        perfiles: perfilesActuales,

        estudios: estudiosActuales,

        perfiles_agregados:
            cambiosPerfiles.agregados,

        perfiles_eliminados:
            cambiosPerfiles.eliminados,

        estudios_agregados:
            cambiosEstudios.agregados,

        estudios_eliminados:
            cambiosEstudios.eliminados
    };

    console.log(payload);

    try {

        let resp = await fetch(
            `${BASE_URL}/save/guardar_recepcion.php`,
            {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify(payload)
            }
        );

        let data = await resp.json();

        if(data.success){

            alertify.success("Cambios guardados");

            $('#modalNuevo').modal('hide');

            tabla_recep.ajax.reload(null, false);

        }else{

            alertify.error(data.message || 'Error al guardar');
        }

    } catch(e){

        console.error(e);

        alertify.error('Error de conexión');
    }

});







