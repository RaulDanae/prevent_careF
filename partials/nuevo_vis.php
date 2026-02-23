<!-- Modal para registros nuevos y edicion -->
 <div class="modal fade" id="modalNuevo" data-bs-backdrop="static" data-bs-keyboard="false">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="myModalLabel">Agudeza Visual</h4>
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
                    <div class="step-item" data-step="2">Oftalmo</div>
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
                        <div class="row mb-2">
                            <div class="col-md-6">
                                <label for="dojod" class="etiquetaL">Distancia Ojo Derecho</label>
                                <input type="number" class="form-control" name="dojod" id="dojod" required>
                            </div>
                            <div class="col-md-6">
                                <label for="dojoi" class="etiquetaL">Distancia Ojo Izquierdo</label>
                                <input type="number" class="form-control" name="dojoi" id="dojoi" required>
                            </div>
                        </div>
                        <div class="row mb-2">
                            <div class="col-md-6">
                                <label for="agudezavd" class="etiquetaL">Agudeza Visual Ojo Derecho</label>
                                <input type="number" class="form-control" name="agudezavd" id="agudezavd" required>
                            </div>
                            <div class="col mb-6">
                                <label for="agudezavi" class="etiquetaL">Agudeza Visual Ojo Izquierdo</label>
                                <input type="number" class="form-control" name="agudezavi" id="agudezavi" required>
                            </div>
                        </div>
                        <div class="row mb-2">
                            <div class="col-md-6">
                                <label for="lentesl" class="etiquetaL">Requiere Lentes para ver de Lejos</label>
                                <select class="form-control" name="lentesl" id="lentesl" required>
                                    <option selected class="form-control" value = ""></option>
                                    <option value="SI">SI</option>
                                    <option value="NO">NO</option>
                                    <option value="NO EVALUADO">NO EVALUADO</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label for="lentesc" class="etiquetaL">Requiere Lentes para ver de Cerca</label>
                                <select class="form-control" name="lentesc" id="lentesc" required>
                                    <option selected class="form-control" value = ""></option>
                                    <option value="SI">SI</option>
                                    <option value="NO">NO</option>
                                    <option value="NO EVALUADO">NO EVALUADO</option>
                                </select>
                            </div>
                        </div>
                        <div class="row mb-2">
                            <div class="col-md-6">
                                <label for="consultaof" class="etiquetaL">Requiere Consulta Oftalmologica</label>
                                <select class="form-control" name="consultaof" id="consultaof" required>
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