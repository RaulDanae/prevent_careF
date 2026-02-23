<!-- Modal para registros nuevos y edicion -->
 <div class="modal fade" id="modalNuevo" data-bs-backdrop="static" data-bs-keyboard="false">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="myModalLabel">Capacidad Auditiva</h4>
                <button type="button" class="btn-close" id="btnCloseModal" aria-label="Close">
                </button>
            </div>

            <!-- Stepper -->
             <div class="stepper-container mb-3">
                <div class="progress mb-2" style="height:6px;">
                    <div id="progressBar" class="progress-bar" style="width:25%"></div>
                </div>

                <div class="stepper d-flex justify-content-between">
                    <div class="step-item active" data-step="1">Datos</div>
                    <div class="step-item" data-step="2">Audicion</div>
                    <div class="step-item" data-step="3">Confirmar</div>
                </div>
             </div>

            <form id="formWizard">
                <div class="modal-body">

                    <!-- Paso 1 -->
                     <div class="step" data-step="1">
                        <div class="row mb-2">
                            <div class="col-md-4">
                                <label for="curp" class="etiquetaL">CURP</label>
                                <input type="text" class="form-control" name="curp" id="curp" readonly>
                            </div>
                            <div class="col-md-8">
                                <label for="nombre" class="etiquetaL">Nombre</label>
                                <input type="text" class="form-control" name="nombre" id="nombre" readonly>
                            </div>
                        </div>
                        <div class="row mb-2">
                            <div class="col-md-4">
                                <label for="genero" class="etiquetaL">Genero</label>
                                <input type="text" class="form-control" name="genero" id="genero" readonly>
                            </div>
                            <div class="col-md-4">
                                <label for="fnacimiento" class="etiquetaL">Fecha Nacimiento</label>
                                <input type="date" class="form-control" name="fnacimiento" id="fnacimiento" readonly>
                            </div>                           
                        </div>
                     </div>
                     
                    <!-- /Paso 1 -->

                    <!-- Paso 2 -->
                     <div class="step" data-step="2">
                        <div class="table-responsive">
                            <table class="table table-bordered tabla-audimetria text-center align-middle">
                                <thead class="table-light">
                                    <tr>
                                        <th>Decibeles</th>
                                        <th>Oído Derecho</th>
                                        <th>Oído Izquierdo</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>0.5 kHz</td>
                                        <td>
                                            <input type="number" class="form-control"
                                                name="od_500" id="od_500"
                                                min="0" max="50" step="1">
                                        </td>
                                        <td>
                                            <input type="number" class="form-control"
                                                name="oi_500" id="oi_500"
                                                min="0" max="50" step="1">
                                        </td>
                                    </tr>

                                    <tr>
                                        <td>1 kHz</td>
                                        <td>
                                            <input type="number" class="form-control"
                                                name="od_1000" id="od_1000"
                                                min="0" max="50" step="1">
                                        </td>
                                        <td>
                                            <input type="number" class="form-control"
                                                name="oi_1000" id="oi_1000"
                                                min="0" max="50" step="1">
                                        </td>
                                    </tr>

                                    <tr>
                                        <td>2 kHz</td>
                                        <td>
                                            <input type="number" class="form-control"
                                                name="od_2000" id="od_2000"
                                                min="0" max="50" step="1">
                                        </td>
                                        <td>
                                            <input type="number" class="form-control"
                                                name="oi_2000" id="oi_2000"
                                                min="0" max="50" step="1">
                                        </td>
                                    </tr>

                                    <tr>
                                        <td>4 kHz</td>
                                        <td>
                                            <input type="number" class="form-control"
                                                name="od_4000" id="od_4000"
                                                min="0" max="50" step="1">
                                        </td>
                                        <td>
                                            <input type="number" class="form-control"
                                                name="oi_4000" id="oi_4000"
                                                min="0" max="50" step="1">
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <div class="row mb-2">
                            <div class="col-md-6">
                                <label for="consulta" class="etiquetaL">Requiere Consulta Audiologica</label>
                                <select class="form-control" name="consulta" id="consulta" required>
                                    <option selected class="form-control" value = ""></option>
                                    <option value="SI">SI</option>
                                    <option value="NO">NO</option>
                                </select>
                            </div>
                        </div>
                        <div class="row mb-2">
                            <div class="col-md-12">
                                <label for="observaciones" class="etiquetaL">Observaciones</label>
                                <textarea class="form-control" name = "observaciones" id = "observaciones" rows="2" ></textarea>
                            </div>
                        </div>
                     </div>    
                    <!-- /Paso 2 -->

                    <!-- Paso 3 -->
                     <div class="step" data-step="3">
                        <p>Revisa la información antes de guardar.</p>
                     </div>

                     <div id="summaryContent" class="row gy-2"></div>

                     <!-- Errores AJAX -->
                      <div id="ajaxError" class="alert alert-danger d-none mt-3"></div>
                    <!-- /Paso 3 -->
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary d-none" id="btnPrev">Anterior</button>
                    <button type="button" class="btn btn-primary" id="btnNext">Siguiente</button>
                    <button type="submit" class="btn btn-success" id="btnSave">
                        <span class="btn-text">Guardar</span>
                        <span class="spinner-border spinner-border-sm d-none" id="saveSpinner" role="status"></span>
                    </button>
                </div>
            </form>
        </div>
    </div>
 </div>
<!-- /FIn de Modal nuevo -->