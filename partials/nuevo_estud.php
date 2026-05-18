<!-- Modal para registros nuevos y edición -->
<div class="modal fade" id="modalNuevo" data-bs-backdrop="static">
    <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">

            <!-- Header -->
             <div class="modal-header">
                <h5 class="modal-title">Nuevo Estudio</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
             </div>

            <!-- Form -->
            <form id="formEstudio">

                <div class="modal-body">

                    <input type="hidden" name="id_estudio"> <!-- Caja oculta que obtiene el id del estudio -->

                    <!-- CLAVE / ESTUDIO -->
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <label class="form-label">Clave</label>
                            <input type="text" class="form-control" name="cestudio" required>
                        </div>

                        <div class="col-md-8">
                            <label class="form-label">Estudio</label>
                            <input type="text" class="form-control" name="nomestudio" required>
                        </div>
                    </div>

                    <!-- PERFILES -->
                    <div class="row mb-3">
                        <div class="col-md-12">
                            <label class="form-label">Perfiles</label>

                            <select id="perfilest" name="perfilest[]" class="catalogo-select" data-tabla="perfilestudios"  multiple required>
                                <option value=""></option>
                            </select>

                        </div>
                    </div>

                    <hr>

                    <h6>Configuraciones del estudio</h6>

                    <div class="accordion" id="configuracionesEstudio"></div>

                    <button type="button" class="btn btn-primary w-100 mt-3" id="btnAgregarConfig" data-bs-toggle="tooltip"
                                          data-bs-placement="top" title="Agrega configuracion">
                        <i class="fa fa-plus"></i> Agregar Configuración
                    </button>

                </div>

                <!-- Footer -->
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-success">Guardar</button>
                </div>

            </form>
        </div>
    </div>
</div>


<div id="templateConfig" style="display:none">
    <div class="accordion-item configuracion-estudio">
        <h2 class="accordion-header d-flex align-items-center">

        <button class="accordion-button flex-grow-1" type="button" data-bs-toggle="collapse">
            <i class="fa fa-plus"></i> Configuración
        </button>

        <button type="button" class="btn btn-info btn-sm duplicarConfig ms-2 d-flex align-items-center justify-content-center" style="width:32px;height:32px" data-bs-toggle="tooltip"
                              data-bs-placement="top" title="Copiar valores">
            <i class="fa-solid fa-copy"></i>
        </button>        

        <button type="button" class="btn btn-danger btn-sm eliminarConfig ms-2 d-flex align-items-center justify-content-center" style="width:32px;height:32px" data-bs-toggle="tooltip"
                              data-bs-placement="top" title="Elimina configuracion">
            <i class="fa-solid fa-trash"></i>
        </button>

        </h2>

        <div class="accordion-collapse collapse">
            <div class="accordion-body">
                <div class="row mb-3">
                    <div class="col-md-3">
                        <label>Metodología</label>
                        <select name="metodologia[]" class="catalogo-select" data-tabla="tmetodologias">
                            <option value=""></option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label>Unidad</label>
                        <select name="unidad[]" class="catalogo-select" data-tabla="tunidades">
                            <option value=""></option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label>Muestra</label>
                        <select name="muestra[]" class="catalogo-select" data-tabla="tipomuestras">
                            <option value=""></option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label>Recipiente</label>
                        <select name="recipiente[]" class="catalogo-select" data-tabla="trecipientes">
                            <option value=""></option>
                        </select>
                    </div>
                </div>

                <hr>

                <h6>Rangos de referencia</h6>

                <table class="table table-sm table-bordered tablaRangos">
                    <thead class="table-light">
                        <tr>
                            <th>Genero</th>
                            <th>Edad Min</th>
                            <th>Edad Max</th>
                            <th>Val Bajo</th>
                            <th>Lim Inf</th>
                            <th>Lim Sup</th>
                            <th>Val Alto</th>
                            <th>Val Crit</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
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
                            <td><input type="number" name="valor_bajo[]" class="form-control" step="0.01"></td>
                            <td><input type="number" name="lim_inf[]" class="form-control" step="0.01"></td>
                            <td><input type="number" name="lim_sup[]" class="form-control" step="0.01"></td>
                            <td><input type="number" name="valor_alto[]" class="form-control" step="0.01"></td>
                            <td><input type="number" name="valor_critico[]" class="form-control" step="0.01"></td>
                            <td>
                                <button type="button" class="btn btn-warning btn-sm eliminarFila" data-bs-toggle="tooltip"
                                                      data-bs-placement="top" title="Elimina rango">
                                    <i class="fa-solid fa-trash"></i>
                                </button>
                            </td>
                        </tr>
                    </tbody>
                </table>

                <button type="button" class="btn btn-primary btn-sm btnAgregarFila mt-2" data-bs-toggle="tooltip"
                                      data-bs-placement="top" title="Agrega rango">
                    <i class="fa fa-plus"></i> Rango
                </button>

            </div>
        </div>
    </div>
</div>