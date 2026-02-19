<!-- Modal para registros nuevos y edicion -->
 <div class="modal fade" id="modalNuevo" data-bs-backdrop="static" data-bs-keyboard="false">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="myModalLabel">Usuario</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
                </button>
            </div>

            <!-- Stepper -->
             <div class="stepper-container mb-3">
                <div class="progress mb-2" style="height:6px;">
                    <div id="progressBar" class="progress-bar" style="width:25%"></div>
                </div>

                <div class="stepper d-flex justify-content-between">
                    <div class="step-item active" data-step="1">Usuario</div>
                    <div class="step-item" data-step="2">Confirmar</div>
                </div>
             </div>

            <form id="formWizard">
                <input type="hidden" class="form-control" name="id" id="id">
                <div class="modal-body">

                    <!-- Paso 1 -->
                     <div class="step" data-step="1">
                        <div class="row mb-2">
                            <div class="col-md-6">
                                <label for="nom" class="etiquetaL">Nombre</label>
                                <input type="text" class="form-control" name="nom" id="nom" required>
                            </div>
                            <div class="col-md-6">
                                <label for="uss" class="etiquetaL">Usuario</label>
                                <input type="text" class="form-control" name="uss" id="uss" required>
                            </div>
                        </div>
                        <div class="row mb-2">                        
                            <div class="col-md-5">
                                <label for="pas1" class="etiquetaL">Password</label>
                                <input type="password" class="form-control" name="pas1" id="pas1" placeholder = "Contraseña">
                            </div>
                            <div class="col-md-5">
                                <label for="pas2" class="etiquetaL"> Confirmar Password</label>
                                <input type="password" class="form-control" name="pas2" id="pas2" placeholder = "Confirmar Contraseña">
                            </div>
                            <div class="col-md-2 d-flex align-items-end">
                                <i id="iconValidacion" class="fa-solid mb-2" 
                                    style="display:none; font-size:24px;"></i>
                            </div>
                        </div>
                        <div class="row mb-2">
                            <div class="col-md-5">
                                <label for="perfil" class="etiquetaL">Perfil</label>
                                <select class="form-control" name="perfil" id="perfil" required>
                                    <option selected class="form-control" value=""></option>
                                    <?php WHILE($row = $perfile -> fetch_assoc()) { ?>
                                        <option 
                                            value= "<?php echo htmlspecialchars($row['Id']); ?>">
                                            <?php echo htmlspecialchars($row['perfil']); ?>
                                        </option>
                                    <?php  } ?>
                                </select>
                            </div>
                            <div class="col-md-5">
                                <label for="estatus" class="etiquetaL">Estatus</label>
                                <select class="form-control" name="estatus" id="estatus" required>
                                    <option selected class="form-control" value = ""></option>
                                    <option value="1">Activo</option>
                                    <option value="0">Baja</option>
                                </select>
                            </div>                    
                        </div>
                     </div>
                     
                    <!-- /Paso 1 -->

                    <!-- Paso 2 -->
                     <div class="step" data-step="2">
                        <p>Revisa la información antes de guardar.</p>
                     </div>

                     <div id="summaryContent" class="row gy-2"></div>

                     <!-- Errores AJAX -->
                      <div id="ajaxError" class="alert alert-danger d-none mt-3"></div>
                    <!-- /Paso 2 -->
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