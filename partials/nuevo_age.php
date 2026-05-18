<!-- Modal para registros nuevos y edicion -->
 <div class="modal fade" id="modalNuevo" data-bs-backdrop="static" data-bs-keyboard="false">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="myModalLabel">Agenda</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
                </button>
            </div>

            <!-- Stepper -->
             <div class="stepper-container mb-3">
                <div class="progress mb-2" style="height:6px;">
                    <div id="progressBar" class="progress-bar" style="width:25%"></div>
                </div>

                <div class="stepper d-flex justify-content-between">
                    <div class="step-item active" data-step="1">Generales</div>
                    <div class="step-item" data-step="2">Complementar</div>
                    <div class="step-item" data-step="3">Confirmar</div>
                </div>
             </div>

            <form id="formWizard">
                <input type="hidden" class="form-control" name="id" id="id">
                <div class="modal-body">

                    <!-- Paso 1 -->
                     <div class="step" data-step="1">
                        <div class="row mb-2">
                            <div class="col-md-8">
                                <label for="pacien" class="etiquetaL">Paciente</label>
                                <input type="text" class="form-control" name="pacien" id="pacien" disabled>
                            </div>
                            <div class="col-md-4">
                                <label for="even" class="etiquetaL">Edad</label>
                                <input type="text" class="form-control" name="edad" id="edad" disabled>
                            </div>
                        </div>
                        <div class="row mb-2">
                            <div class="col-md-6">
                                <label for="compania" class="etiquetaL">Compañia</label>
                                <input type="text" class="form-control" name="compania" id="compania" disabled>
                            </div>
                            <div class="col-md-6">
                                <label for="sucursal" class="etiquetaL">Sucursal</label>
                                <input type="text" class="form-control" name="sucursal" id="sucursal" disabled>
                            </div>
                        </div> 
                        <div class="row mb-2">
                            <div class="col-md-12">
                                <label for="evento" class="etiquetaL">Evento</label>
                                <input type="text" class="form-control" name="evento" id="evento" disabled>
                            </div>
                        </div>   
                     </div>   
                    <!-- /Paso 1 -->

                    <!-- Paso 2 -->
                     <div class="step" data-step="2">
                        <div class="row mb-2">
                            <div class="col-md-6">
                                <label for="curp" class="etiquetaL">CURP</label>
                                <input type="text" class="form-control" name="curp" id="curp" disabled>
                            </div>
                            <div class="col-md-6">
                                <label for="rfc" class="etiquetaL">RFC</label>
                                <input type="text" class="form-control" name="rfc" id="rfc" required>
                            </div>
                        </div>
                        <div class="row mb-2">
                            <div class="col-md-2">
                                <label for="privacidad" class="etiquetaL">Aviso Privacidad</label>
                                <select class="form-control" name="privacidad" id="privacidad">
                                    <option selected class="form-control" value = ""></option>
                                    <option value="SI">SI</option>
                                    <option value="NO">NO</option>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label for="concentimiento" class="etiquetaL">C Informado</label>
                                <select class="form-control" name="consentimiento" id="consentimiento">
                                    <option selected class="form-control" value = ""></option>
                                    <option value="SI">SI</option>
                                    <option value="NO">NO</option>
                                </select>
                            </div>
                        </div>
                        <div class="row mb-2">
                            <div class="col-md-4">
                                <label for="agehmuestra" class="etiquetaL">Hr Agenda Toma Muestra</label>
                                <input type="datetime-local" class="form-control" name="agehmuestra" id="agehmuestra">
                            </div>
                            <div class="col-md-4">
                                <label for="hmuestra" class="etiquetaL">Hr Asiste Toma Muestra</label>
                                <input type="datetime-local" class="form-control" name="hmuestra" id="hmuestra">
                            </div>
                            <div class="col-md-4">
                                <label for="obst" class="etiquetaL">Comentario</label>
                                <input type="text" class="form-control" name="obst" id="obst" readonly> 
                            </div> 
                        </div>
                        <div class="row mb-2">
                            <div class="col-md-4">
                                <label for="agehevento" class="etiquetaL">Hr Agenda Evento</label>
                                <input type="datetime-local" class="form-control" name="agehevento" id="agehevento">
                            </div>                        
                            <div class="col-md-4">
                                <label for="hevento" class="etiquetaL">Hr Asiste Evento</label>
                                <input type="datetime-local" class="form-control" name="hevento" id="hevento">
                            </div>
                            <div class="col-md-4">
                                <label for="obse" class="etiquetaL">Comentario</label>
                                <input type="text" class="form-control" name="obse" id="obse" readonly> 
                            </div>                            
                        </div>
                        <div class="row mb-2">
                            <div class="col-md-12">
                                <label for="obs" class="etiquetaL">Observaciones</label>
                                <input type="text" class="form-control" name = "obs" id = "obs">
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