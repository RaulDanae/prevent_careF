<!-- Modal para registros nuevos y edicion -->
 <div class="modal fade" id="modalNuevo" data-bs-backdrop="static" data-bs-keyboard="false">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="myModalLabel">Evento</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
                </button>
            </div>

            <!-- Stepper -->
             <div class="stepper-container mb-3">
                <div class="progress mb-2" style="height:6px;">
                    <div id="progressBar" class="progress-bar" style="width:25%"></div>
                </div>

                <div class="stepper d-flex justify-content-between">
                    <div class="step-item active" data-step="1">Evento</div>
                    <div class="step-item" data-step="2">Sucursales</div>
                    <div class="step-item" data-step="3">Perfil Estudios</div>
                    <div class="step-item" data-step="4">Confirmar</div>
                </div>
             </div>

            <form id="formWizard">
                <input type="hidden" class="form-control" name="id" id="id">
                <div class="modal-body">

                    <!-- Paso 1 -->
                     <div class="step" data-step="1">
                        <div class="row mb-2">
                            <div class="col-md-12">
                                <label for="descrip" class="etiquetaL">Nombre Evento</label>
                                <input type="text" class="form-control" name = "descrip" id = "descrip" required>
                            </div>
                        </div>
                        <div class="row mb-2">
                            <div class="col-md-4">
                                <label for="tevento" class="etiquetaL">Tipo Evento</label>
                                <select class="form-control" name="tevento" id="tevento" required>
                                    <option selected class="form-control" value = ""></option>
                                    <option value="FERIA">FERIA</option>
                                    <option value="EVENTO">EVENTO</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label for="compa" class="etiquetaL">Compañia</label>
                                <select class="form-control" name="compa" id="compa" required>
                                    <option selected class="form-control" value=""></option>
                                    <?php WHILE($row = $compania -> fetch_assoc()) { ?>
                                        <option 
                                            value= "<?php echo htmlspecialchars($row['id_comp']); ?>">
                                            <?php echo htmlspecialchars($row['nomcom']); ?>
                                        </option>
                                    <?php  } ?>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label for="fevento" class="etiquetaL">Fecha Evento</label>
                                <input type="date" class="form-control" name="fevento" id="fevento" required>
                            </div>
                        </div>
                        <div class="row mb-2">
                            <div class="col-md-4">
                                <label for="nomcorto" class="etiquetaL">Nombre Corto</label>
                                <input type="text" class="form-control" name = "nomcorto" id = "nomcorto" readonly>                                
                            </div>
                        </div>
                     </div>
                    <!-- /Paso 1 -->

                    <!-- Paso 2 -->
                     <div class="step" data-step="2">
                        <div class="row mb-2">
                            <div class="col-md-4">
                                <label for="global" class="etiquetaL">Global</label>
                                <select class="form-control" name="global" id="global" required>
                                    <option selected class="form-control" value="SI">SI</option>
                                    <option value="NO">NO</option>
                                </select>
                            </div>
                        </div>
                        <div class="row mb-2">
                            <div class="col-md-12">
                                <label class="etiquetaL">Sucursales</label>
                                <div id="contenedorSucursales" class="sucursales-grid"></div>
                            </div>
                        </div>
                        <div class="alert alert-info mt-2 py-2 small">
                            Al guardar el evento, se cargarán automáticamente todos los pacientes de las sucursales seleccionadas.
                        </div>
                        <div id="msgSync" class="alert alert-warning mt-2 py-2 small d-none">
                            Si modificas las sucursales, debes sincronizar manualmente los pacientes.
                        </div>
                     </div>
                    <!-- /Paso 2 --> 

                    <!-- Paso 3 -->
                     <div class="step" data-step="3">
                        <div class="row mb-2">
                            <div class="col-md-12">
                                <label class="etiquetaL">Perfiles para el Evento</label>

                                <!-- Buscador -->
                                <select id = "buscadorPerfiles" class = "form-control"></select>
                                
                                <!-- Seleccionados -->
                                <div id="perfilesSeleccionados" class="d-flex flex-wrap gap-2 mt-3"></div>
                                
                            </div>
                        </div>
                     </div>
                    <!-- /Paso 3 -->

                    <!-- Paso 4 -->
                     <div class="step" data-step="4">
                        <p>Revisa la información antes de guardar.</p>
                     </div>

                     <div id="summaryContent" class="row gy-2"></div>

                     <!-- Errores AJAX -->
                     <div id="ajaxError" class="alert alert-danger d-none mt-3"></div>

                     <div id="contadorPacientes" class="small text-muted mt-1"></div>

                    <!-- /Paso 4 -->
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary d-none" id="btnPrev">Anterior</button>
                    <button type="button" class="btn btn-primary" id="btnNext">Siguiente</button>
                    <button type="submit" class="btn btn-success" id="btnSave">
                        <span class="btn-text">Guardar</span>
                        <span class="spinner-border spinner-border-sm d-none" id="saveSpinner" role="status"></span>
                    </button>
                    <button type="button" class="btn btn-info d-none" id="btnSyncPacientes">
                        Sincronizar Pacientes
                    </button>
                </div>
            </form>
        </div>
    </div>
 </div>
<!-- /FIn de Modal nuevo -->