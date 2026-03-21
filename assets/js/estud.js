// Hace llamado a la tabla y aplica paginacion -->
  let tabla_estudio

  $(document).ready(function(){

    tabla_estudio = $('#tabla-estud').DataTable({
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
        url: BASE_URL + "/partials/rellestudios.php",
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
      tabla_estudio.draw();
    })

    // Boton Descargar
    $('#btndescargar').on('click', function () {
      tabla_estudio.button('.buttons-excel').trigger();
    });

  });

/////////////////////////////////// Modulo Nuevo /////////////////////////////////////////////////

// Agregar configuraciones ///
let contadorConfig = 0;

function crearConfiguracion(){
    contadorConfig++;
    let config = $("#templateConfig .configuracion-estudio").clone();
    let collapseID = "config"+contadorConfig;
    config.find(".accordion-button").text("Configuracion "+contadorConfig).attr("data-bs-target","#"+collapseID);
    config.find(".accordion-collapse").attr("id",collapseID);

    // Limpiar posibles select2 clonados
    config.find(".select2-container").remove();
    config.find("select.catalogo-select").removeClass("select2-hidden-accessible").show();    

    $("#configuracionesEstudio").append(config);

    // Inicializar select2 solo en este bloque
    initCatalogos(config);

    config.find(".accordion-collapse").addClass("show");
}

// Boton de agregar configuracion
$("#btnAgregarConfig").click(function(){
    crearConfiguracion();
});

// Crear primera configuracion al abrir modal
$('#modalNuevo').on('shown.bs.modal', function () {
    if($("#configuracionesEstudio").children().length === 0){
        crearConfiguracion();
    }
});

$(document).on("click", ".duplicarConfig", function(){

    let original = $(this).closest(".configuracion-estudio");
    let copia = original.clone();

    contadorConfig++;

    let collapseID = "config"+contadorConfig;
    copia.find(".accordion-button").text("Configuración "+contadorConfig).attr("data-bs-target","#"+collapseID);
    copia.find(".accordion-collapse").attr("id",collapseID).removeClass("show");

    // limpiar select2 clonados
    copia.find(".select2-container").remove();
    copia.find("select.catalogo-select").removeClass("select2-hidden-accessible").show();
    $("#configuracionesEstudio").append(copia);

    initCatalogos(copia);

    renumerarConfiguraciones();

});

/////////////// Reiniciar el numerador configuracion cuando tienes configuracion 1, configuracion 2 y configuracion 3 y eliminas configuracion 2, se renumeran a configuracion 1 y el configuracion 3 cambia a configuracion 2
function renumerarConfiguraciones(){

    let i = 1;

    $("#configuracionesEstudio .configuracion-estudio").each(function(){
        let collapseID = "config"+i;
        $(this).find(".accordion-button").text("Configuración "+i).attr("data-bs-target","#"+collapseID);
        $(this).find(".accordion-collapse").attr("id",collapseID);

        i++;

    });

    contadorConfig = i-1;
}

// Eliminar configuracion
$(document).on("click", ".eliminarConfig", function(){
    if($(".configuracion-estudio").length <= 1){
        alertify.error("Debe existir al menos una configuracion");
        return;
    }
    $(this).closest(".configuracion-estudio").remove();

    renumerarConfiguraciones();
});

//// Agregar Rangos dinamicamente ////
$(document).on("click", ".btnAgregarFila", function() {
    let tabla = $(this).closest(".accordion-body").find(".tablaRangos tbody");

    tabla.append(`
        <tr>
            <td>
                <select name="genero[]" class="form-control">
                    <option></option>
                    <option value="M">M</option>
                    <option value="F">F</option>
                    <option value="A">A</option>
                </select>
            </td>
            <td><input type="number" name="edad_min[]" class="form-control"></td>
            <td><input type="number" name="edad_max[]" class="form-control"></td>
            <td><input type="number" name="valor_bajo[]" class="form-control"></td>
            <td><input type="number" name="lim_inf[]" class="form-control"></td>
            <td><input type="number" name="lim_sup[]" class="form-control"></td>
            <td><input type="number" name="valor_alto[]" class="form-control"></td>
            <td><input type="number" name="valor_critico[]" class="form-control"></td>
            <td>
                <button type="button" class="btn btn-warning btn-sm eliminarFila" data-bs-toggle="tooltip"
                                      data-bs-placement="top" title="Elimina rango">
                    <i class="fa-solid fa-trash"></i>
                </button>
            </td>
        </tr>
    `);
});

///// Eliminar rango /////
$(document).on("click", ".eliminarFila", function() {
    let fila = $(this).closest("tbody").find("tr");
    if(fila.length>1){
        $(this).closest("tr").remove();
    }
});

///////////////////////////Modal Salir //////////////////////////////////////////////////////////
$('#modalNuevo').on('hide.bs.modal', function () {
    const btnOpen = document.getElementById('btnNuevoM');
    if (btnOpen) {
        btnOpen.focus();
    }
});

$(document).on('click', '#btnCloseModal', function () {
    const modalEl = document.getElementById('modalNuevo');
    const modal = bootstrap.Modal.getInstance(modalEl);
    modal.hide();
});

/////////////////// Reiniciar el numero de Configuracion /////////////////////////////////////
$('#modalNuevo').on('shown.bs.modal', function () {

    if(EstudioMode === 'create'){
        contadorConfig = 0;
        $("#configuracionesEstudio").html("");
        crearConfiguracion();
    }
});

///////////////// Funcion para limpiar el modal /////////////////////////////////////////////
function limpiarModalEstudio(){

    // reset form
    $('#formEstudio')[0].reset();

    // limpiar select2
    $('#modalNuevo').find('.catalogo-select').val(null).trigger('change');

    // eliminar configuraciones extras
    $("#configuracionesEstudio .configuracion-estudio:not(:first)").remove();

    // resetear configuración 1
    let config = $("#configuracionesEstudio .configuracion-estudio:first");

    // cerrar accordion
    config.find(".accordion-collapse")
          .removeClass("show");

    // limpiar inputs
    config.find("input").val("");

    config.find("select").val("").trigger("change");

    // limpiar tabla de rangos
    let tabla = config.find(".tablaRangos tbody");

    tabla.empty();

    // agregar fila inicial
    tabla.append(`
        <tr>
            <td>
                <select class="form-control" name="genero[]">
                    <option value=""></option>
                    <option value="M">M</option>
                    <option value="F">F</option>
                    <option value="A">A</option>
                </select>
            </td>

            <td><input type="number" class="form-control" name="edad_min[]"></td>
            <td><input type="number" class="form-control" name="edad_max[]"></td>
            <td><input type="number" step="0.01" class="form-control" name="valor_bajo[]"></td>
            <td><input type="number" step="0.01" class="form-control" name="lim_inf[]"></td>
            <td><input type="number" step="0.01" class="form-control" name="lim_sup[]"></td>
            <td><input type="number" step="0.01" class="form-control" name="valor_alto[]"></td>
            <td><input type="number" step="0.01" class="form-control" name="valor_critico[]"></td>

            <td>
                <button type="button" class="btn btn-sm btn-danger eliminarFila">
                    <i class="fa-solid fa-trash"></i>
                </button>
            </td>
        </tr>
    `);

    contadorConfig = 1;

}

//////////////// Activar la funcion de limpieza del modal al cerrar el modal //////////////////////
$('#modalNuevo').on('hidden.bs.modal', function () {

    EstudioMode = 'create';
    limpiarModalEstudio();

});

////////////////////// Activar la funcion de limpieza del modal al abrirlo ////////////////////////
$('#btnNuevoM').click(function(){

    EstudioMode = 'create';
    limpiarModalEstudio();

});

///// Asi forzamos que el selec2 de perfiles contenga informacion.
$(document).on('select2:select', '#perfilest', function (e) {

    let data = e.params.data;
    let select = $(this);

    // Verifica si ya existe
    if (select.find("option[value='" + data.id + "']").length === 0) {
        let option = new Option(data.text, data.id, true, true);
        select.append(option);
    }

});


///////////////// Armar estructura del estudio para Guardar /////////////////////////

//const GRUPO_USUARIO = "<?= $_SESSION['usuario'] ?? '' ?>";

function armarEstudio(){

    let perfiles = [];

    $('select[name="perfilest[]"] option:selected').each(function(){
        perfiles.push($(this).val());
    });

    let estudio = {
        id_estudio: $('input[name="id_estudio"]').val(),
        clave: $('input[name="cestudio"]').val(),
        nombre: $('input[name="nomestudio"]').val(),
        perfiles: perfiles,
        usuario: GRUPO_USUARIO,
        configuraciones: []
    };

    $(".configuracion-estudio").each(function(){

        let config = {
            metodologia: $(this).find('select[name="metodologia[]"]').val(),
            unidad: $(this).find('select[name="unidad[]"]').val(),
            muestra: $(this).find('select[name="muestra[]"]').val(),
            recipiente: $(this).find('select[name="recipiente[]"]').val(),
            rangos: []
        };

        $(this).find(".tablaRangos tbody tr").each(function(){

            config.rangos.push({
                genero: $(this).find('select[name="genero[]"]').val(),
                edad_min: $(this).find('input[name="edad_min[]"]').val(),
                edad_max: $(this).find('input[name="edad_max[]"]').val(),
                valor_bajo: $(this).find('input[name="valor_bajo[]"]').val(),
                lim_inf: $(this).find('input[name="lim_inf[]"]').val(),
                lim_sup: $(this).find('input[name="lim_sup[]"]').val(),
                valor_alto: $(this).find('input[name="valor_alto[]"]').val(),
                valor_critico: $(this).find('input[name="valor_critico[]"]').val()
            });

        });

        estudio.configuraciones.push(config);

    });

    return estudio;
} 


///////////////////////////////// Guardar ///////////////////////////////////////

let EstudioMode = 'create'; // create | edit

$('#formEstudio').on('submit', function (e) {

    e.preventDefault(); 

    let data = armarEstudio();


    if(!data.perfiles || data.perfiles.length === 0){
        alertify.error("Debes seleccionar al menos un perfil");
        return;
    }

    // Validacion de traslapes
    if(!validarTraslapesEdad()){
        return;
    }

    $.ajax({
        url: EstudioMode === 'edit'
          ? '../save/update_estudios.php' 
          : '../save/guardar_estudios.php',
        type: 'POST',
        data: JSON.stringify(armarEstudio()),
        contentType: "application/json",
        dataType: 'json',

        success: function (resp) {

            if (resp.success) {

                alertify.success(
                  EstudioMode === 'edit'
                    ? 'Estudio actualizado'
                    : 'Estudio guardado'
                );

                // Cerrar modal
                $('#modalNuevo').modal('hide');

                // Refrescar DataTable si existe
                if ($.fn.DataTable.isDataTable('#tabla-estud')) {
                    $('#tabla-estud').DataTable().ajax.reload(null, false);
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
    });
});


/////// Funcion que valide que no se traslape las edades de hombres o de mujeres o de ambos ////////////
function validarTraslapesEdad(){

    let valido = true;

    // recorrer cada configuración
    $(".configuracion-estudio").each(function(){

        let rangos = [];

        let tabla = $(this).find(".tablaRangos");

        tabla.find("tbody tr").removeClass("table-danger");

        tabla.find("tbody tr").each(function(){

            let fila = $(this);

            let genero = fila.find('select[name="genero[]"]').val();
            let min = parseInt(fila.find('input[name="edad_min[]"]').val());
            let max = parseInt(fila.find('input[name="edad_max[]"]').val());

            if(!genero || isNaN(min) || isNaN(max)){
                return;
            }

            if(min >= max){
                fila.addClass("table-danger");
                alertify.error("Edad mínima debe ser menor que edad máxima");
                valido = false;
                return false;
            }

            rangos.push({
                fila:fila,
                genero:genero,
                min:min,
                max:max
            });

        });

        for(let i=0;i<rangos.length;i++){

            for(let j=i+1;j<rangos.length;j++){

                let g1 = rangos[i].genero;
                let g2 = rangos[j].genero;

                let validarGenero =
                    g1 === g2 ||
                    g1 === "A" ||
                    g2 === "A";

                if(validarGenero){

                    if(
                        rangos[i].min <= rangos[j].max &&
                        rangos[j].min <= rangos[i].max
                    ){

                        rangos[i].fila.addClass("table-danger");
                        rangos[j].fila.addClass("table-danger");

                        alertify.error("Existe traslape de edad en una configuración");

                        valido = false;
                        return false;
                    }

                }

            }

        }

    });

    return valido;

}



///////////////////////////////// EDITAR ///////////////////////////////////////
let editId = null;
$('#tabla-estud').on('click', '.btnEditar', function () {

    EstudioMode = 'edit';
    editId = $(this).attr('data-id');

    $('#myModalLabel').text('Editar Estudio');

    limpiarModalEstudio();

    $('#modalNuevo').modal('show');

    // Espera a que el modal este listo
    setTimeout(() => {
        cargarDatosEstudios(editId);
    }, 150);   

});


function cargarDatosEstudios(id) {

    $.ajax({
        url: BASE_URL + '/config/get_estud.php',
        type: 'POST',
        data: JSON.stringify({ id: id }),
        contentType: "application/json",
        dataType: 'json',


        success: function (data) {

            // Campo oculto
            $('input[name="id_estudio"]').val(data.id);

            // Datos Generales
            $('input[name="cestudio"]').val(data.clave);
            $('input[name="nomestudio"]').val(data.nombre);

            // Perfiles (select2)
            setTimeout(() => {
                cargarPerfilesSelect2(data.perfiles);
            }, 200);

            // Configuraciones
            $("#configuracionesEstudio").html('');
            contadorConfig = 0;

            data.configuraciones.forEach(config => {
                crearConfiguracionDesdeData(config);
            });
        },

        error: function () {
            alertify.error("Error al cargar el estudio");
        }
    });
}

function cargarPerfilesSelect2(perfiles){

    let select = $('#perfilest');

    // IDs
    let ids = perfiles.map(p => p.id);

    // Limpiar
    select.empty();

    // Agregar opciones manualmente
    perfiles.forEach(p => {
        let option = new Option(p.text, p.id, true, true);
        select.append(option);
    });

    // Setear valores
    select.val(ids).trigger('change');

}

function crearConfiguracionDesdeData(config){

    crearConfiguracion(); // usa tu función existente

    let bloque = $("#configuracionesEstudio .configuracion-estudio").last();

    // Selects principales
    setSelect2Value(bloque, 'metodologia[]', config.metodologia.id, config.metodologia.text);
    setSelect2Value(bloque, 'unidad[]', config.unidad.id, config.unidad.text);
    setSelect2Value(bloque, 'muestra[]', config.muestra.id, config.muestra.text);
    setSelect2Value(bloque, 'recipiente[]', config.recipiente.id, config.recipiente.text);

    // Limpiar tabla
    let tbody = bloque.find(".tablaRangos tbody");
    tbody.empty();

    // Cargar rangos
    config.rangos.forEach(r => {

        tbody.append(`
            <tr>
                <td>
                    <select name="genero[]" class="form-control">
                        <option value=""></option>
                        <option value="M" ${r.genero=='M'?'selected':''}>M</option>
                        <option value="F" ${r.genero=='F'?'selected':''}>F</option>
                        <option value="A" ${r.genero=='A'?'selected':''}>A</option>
                    </select>
                </td>
                <td><input type="number" name="edad_min[]" class="form-control" value="${r.edad_min ?? ''}"></td>
                <td><input type="number" name="edad_max[]" class="form-control" value="${r.edad_max ?? ''}"></td>
                <td><input type="number" name="valor_bajo[]" class="form-control" value="${r.valor_bajo ?? ''}"></td>
                <td><input type="number" name="lim_inf[]" class="form-control" value="${r.limite_inf ?? ''}"></td>
                <td><input type="number" name="lim_sup[]" class="form-control" value="${r.limite_sup ?? ''}"></td>
                <td><input type="number" name="valor_alto[]" class="form-control" value="${r.valor_alto ?? ''}"></td>
                <td><input type="number" name="valor_critico[]" class="form-control" value="${r.valor_critico ?? ''}"></td>
                <td>
                    <button type="button" class="btn btn-danger btn-sm eliminarFila">
                        <i class="fa-solid fa-trash"></i>
                    </button>
                </td>
            </tr>
        `);

    });

}


function setSelect2Value(container, name, value, text = null){

    let select = container.find(`select[name="${name}"]`);

    if(!select.length){
        console.warn("Select no encontrado:", name);
        return;
    }

    if(!value){
        return;
    }

    // Crear opción si no existe
    if(select.find("option[value='"+value+"']").length === 0){
        let option = new Option(text || value, value, true, true);
        select.append(option);
    }

    // Asignar valor
    select.val(value).trigger('change');
}