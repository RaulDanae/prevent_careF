<!-- Modal para registros nuevos y edicion -->
 <div class="modal fade" id="modalNuevo" data-bs-backdrop="static" data-bs-keyboard="false">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="myModalLabel">Colaborador</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
                </button>
            </div>

            <!-- Stepper -->
             <div class="stepper-container mb-3">
                <div class="progress mb-2" style="height:6px;">
                    <div id="progressBar" class="progress-bar" style="width:25%"></div>
                </div>

                <div class="stepper d-flex justify-content-between">
                    <div class="step-item active" data-step="1">Empresa</div>
                    <div class="step-item" data-step="2">Generales</div>
                    <div class="step-item" data-step="3">Confirmar</div>
                </div>
             </div>

            <form id="formWizard">
                <input type="hidden" class="form-control" name="id" id="id">
                <div class="modal-body">

                    <!-- Paso 1 -->
                     <div class="step" data-step="1">
                        <div class="row mb-2">
                            <div class="col-md-2">
                                <label for="cod_comp" class="etiquetaL">Cod Compañia</label>
                                <input type="text" class="form-control" name="cod_comp" id="cod_comp" maxlength="5" readonly>
                            </div>
                            <div class="col-md-10">
                                <label for="compania" class="etiquetaL">Compañia</label>
                                <select class="form-control" name="compania" id="compania" required>
                                    <option selected class="form-control" value=""></option>
                                    <?php WHILE($row = $compania -> fetch_assoc()) { ?>
                                        <option 
                                            value= "<?php echo htmlspecialchars($row['compania']); ?>"
                                            data-grupo="<?php echo $row['id_comp']; ?>">
                                            <?php echo htmlspecialchars($row['compania']); ?>
                                        </option>
                                    <?php  } ?>
                                </select>
                            </div>
                        </div>
                     </div>
                     
                    <!-- /Paso 1 -->

                    <!-- Paso 2 -->
                     <div class="step" data-step="2">
                        <div class="row mb-2">
                            <div class="col-md-4">
                                <label for="clave" class="etiquetaL">Clave</label>
                                <input type="text" class="form-control" name="clave" id="clave" maxlength = 6 required>
                            </div>
                            <div class="col-md-4">
                                <label for="colaborador" class="etiquetaL">Colaborador</label>
                                <input type="text" class="form-control" name="colaborador" id="colaborador" required>
                            </div>
                            <div class="col-md-4">
                                <label for="fnacimiento" class="etiquetaL">Fecha Nacimiento</label>
                                <input type="date" class="form-control" name="fnacimiento" id="fnacimiento" required>
                            </div>
                        </div>
                        <div class="row mb-2">    
                            <div class="col-md-4">
                                <label for="genero" class="etiquetaL">Genero</label>
                                <select class="form-control" name="genero" id="genero" required>
                                    <option selected class="form-control" value = ""></option>
                                    <option value="F">F</option>
                                    <option value="M">M</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label for="curp" class="etiquetaL">CURP</label>
                                <input type="text" class="form-control" name="curp" id="curp">
                            </div>
                            <div class="col-md-4">
                                <label for="email" class="etiquetaL">Email</label>
                                <input type="email" class="form-control" name="email" id="email" required>
                            </div>
                        </div>
                        <div class="row mb-2">
                            <div class="col-md-4">
                                <label for="edad" class="etiquetaL">Edad</label>
                                <input type="number" class="form-control" name="edad" id="edad" readonly>
                            </div>
                            <div class="col-md-4">
                                <label for="celular" class="etiquetaL">Celular</label>
                                <input type="number" class="form-control" name="celular" id="celular">
                            </div>
                            <div class="col-md-4">
                                <label for="rfc" class="etiquetaL">RFC</label>
                                <input type="text" class="form-control" name="rfc" id="rfc" required>
                            </div>
                        </div>
                        <div class="row mb-2">
                            <div class="col-md-2">
                                <label for="privacidad" class="etiquetaL">Aviso Privacidad</label>
                                <select class="form-control" name="privacidad" id="privacidad" required>
                                    <option selected class="form-control" value = ""></option>
                                    <option value="SI">SI</option>
                                    <option value="NO">NO</option>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label for="concentimiento" class="etiquetaL">C Informado</label>
                                <select class="form-control" name="consentimiento" id="consentimiento" required>
                                    <option selected class="form-control" value = ""></option>
                                    <option value="SI">SI</option>
                                    <option value="NO">NO</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label for="hrtomamuestra" class="etiquetaL">Hora de toma de muestras</label>
                                <input type="time" class="form-control" name="hrtomamuestra" id="hrtomamuestra">
                            </div>
                            <div class="col-md-4">
                                <label for="hrferia" class="etiquetaL">Hora Feria</label>
                                <input type="time" class="form-control" name="hrferia" id="hrferia">
                            </div> 
                        </div>
                        <div class="row mb-2">
                            <div class="col-md-12">
                                <label for="observaciones" class="etiquetaL">Observaciones</label>
                                <textarea class="form-control" name = "observaciones" id = "observaciones" rows="2" ></textarea>
                            </div>
                        </div>
                        <div class="row mb-2">
                            <div class="col-md-4">
                                <label for="fregistro" class="etiquetaL">Fecha Registro</label>
                                <input type="date" class="form-control" name="fregistro" id="fregistro" required>
                            </div>
                            <div class="col-md-4">
                                <label for="hregistro" class="etiquetaL">Hora Registro</label>
                                <input type="time" class="form-control" name="hregistro" id="hregistro" required>
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