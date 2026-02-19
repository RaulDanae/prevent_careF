// Hace llamado a la tabla y aplica paginacion -->
  let tabla_rcp

  $(document).ready(function(){

    // Datepicker
    $(".datepicker").datepicker({
      "dateFormat": "yy-mm-dd",
      changeYear: true,
      yearRange: "1950:+10",   // Rango de años a visualizar
      showButtonPanel: true
    });

    tabla_rcp = $('#tabla_rcp').DataTable({
      scrollX: true,
      autoWidth: false,
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
        url: BASE_URL + "/partials/tremcp.php",
        type: "POST",
        data: function (data){
          data.fecvarias = $('#fecvarias').val();
          data.buscarFechaInicio = $('#buscar_inicio').val();
          data.buscarFechaFin = $('#buscar_fin').val();
        }
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
      tabla_grales.draw();
    })

  });

  /* Boton Limpiar */
  $("#btn_limpiar").click(function(event) {
    $("#formGral")[0].reset();
  });
  /* Boton Limpiar */