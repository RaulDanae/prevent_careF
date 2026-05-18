<!-- Modal para registros nuevos y edición -->
<div class="modal fade" id="modalNuevo" data-bs-backdrop="static">
    <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">

            <!-- Header -->
             <div class="modal-header">
                <h5 class="modal-title">Estudios Paciente</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
             </div>

            <!-- Form -->
            <form id="formPaciente">

                <!-- Body --> 
                 <div class="modal-body">

                    <input type="hidden" name="id_paciente_evento"> <!-- Caja oculta que obtiene el id del estudio -->

                    <!-- Empleado -->
                     <div class="row mb-3">
                        <div class="col-md-12">
                            <label class="form-label">Empleado</label>
                            <input type="text" class="form-control" name="empleado" disabled>
                        </div>
                     </div>
                    <!-- /Empleado -->

                    <!-- Empresa y Sucursales -->
                     <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label">Empresa</label>
                            <input type="text" class="form-control" name="empresa" disabled>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Sucursales</label>
                            <input type="text" class="form-control" name="sucursales" disabled>
                        </div>                        
                     </div>
                    <!-- / Empresa y Sucursales --> 

                    <!-- Perfiles -->
                     <div class="row mb-3">
                        <div class="col-md-12">
                            <label class="etiquetaL">Perfiles para el Paciente</label>

                                <!-- Buscador -->
                                <select id="buscadorPerfiles" class="form-control" style="width:100%"></select>
                                
                                <!-- Seleccionados -->
                                <div id="perfilesSeleccionados" class="d-flex flex-wrap gap-2 mt-3"></div>
                                
                        </div>
                    </div>
                    <!-- /Perfiles -->    

                    <hr>

                    <!-- Estudios -->
                     <div class="mt-4 px-3">
                        <h6 class="fw-bold">Estudios</h6>

                        <!-- SCROLL SOLO TABLA -->
                        <div class="table-responsive mt-2">
                            <table class="table table-sm table-bordered tablaEstudios">
                                <thead class="table-light">
                                    <tr>
                                        <th>Nombre Estudio</th>
                                        <th style="width: 60px;"></th>
                                    </tr>
                                </thead>
                                <tbody id="bodyEstudios"></tbody>
                            </table>
                        </div>

                        <!-- Boton Visible -->
                         <div class="mt-2">
                            <button type="button" class="btn btn-primary btn-sm btnAgregarEstudios mt-2" data-bs-toggle="tooltip"
                                    data-bs-placement="top" title="Agrega Estudio">
                                <i class="fa fa-plus"></i> Agregar Estudio
                            </button>
                         </div>

                     </div>
                    <!-- /Estudios -->
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


