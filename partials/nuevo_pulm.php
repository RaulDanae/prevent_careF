<!-- Modal para registros nuevos y edicion -->
 <div class="modal fade" id="modalNuevo" data-bs-backdrop="static" data-bs-keyboard="false">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="myModalLabel">Capacidad Pulmonar</h4>
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
                    <div class="step-item" data-step="2">Respiracion</div>
                    <div class="step-item" data-step="3">Confirmar</div>
                </div>
             </div>

            <form id="formWizard" novalidate>
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
                            <div class="col-md-4">
                                <label for="peso" class="etiquetaL">Peso</label>
                                <input type="number" class="form-control" name="peso" id="peso" readonly>
                            </div>                            
                        </div>
                        <div class="row mb-2">
                            <div class="col-md-4">
                                <label for="talla" class="etiquetaL">Talla</label>
                                <input type="number" class="form-control" name="talla" id="talla" readonly>
                            </div> 
                            <div class="col-md-4">
                                <label for="edad" class="etiquetaL">Edad</label>
                                <input type="number" class="form-control" name="edad" id="edad" readonly>
                            </div>                            
                        </div>
                     </div>
                     
                    <!-- /Paso 1 -->

                    <!-- Paso 2 -->
                     <div class="step" data-step="2">
                        <div class="row mb-2">
                            <div class="col-md-4">
                                <label for="fvc" class="etiquetaL">FVC</label>
                                <input type="number" class="form-control" name="fvc" id="fvc" required>
                            </div>
                            <div class="col-md-4">
                                <label for="fev1" class="etiquetaL">FEV1</label>
                                <input type="number" class="form-control" name="fev1" id="fev1" required>
                            </div>
                            <div class="col-md-4">
                                <label for="fevfvc" class="etiquetaL">FEV1_FVC</label>
                                <input type="number" class="form-control" name="fevfvc" id="fevfvc" required>
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