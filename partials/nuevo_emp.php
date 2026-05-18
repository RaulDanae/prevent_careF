<!-- Modal para registros nuevos y edicion -->
 <div class="modal fade" id="modalNuevo" data-bs-backdrop="static">
    <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">

            <!-- Header -->
            <div class="modal-header">
                <h5 class="modal-title">Compañia</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <!-- Form -->
            <form id="formEmpresa">

                <div class = "modal-body">
                    
                    <input type="hidden" name="id" id="id"> <!-- Caja oculta que obtiene el id de empresa -->

                    <!-- Datos Compañia -->
                    <div class="row mb-2">
                        <div class="col-md-8">
                            <label for="comp" class="etiquetaL">Compañia</label>
                            <input type="text" class="form-control" name="comp" id="comp" required>
                        </div>
                        <div class="col-md-4">
                            <label for="rescomp" class="etiquetaL">Nombre Corto</label>
                            <input type="text" class="form-control" name="rescomp" id="rescomp" required>
                        </div>
                    </div>
                    <div class="row mb-2">
                        <div class="col-md-6">
                            <label for="diremp" class="etiquetaL">Direccion Emp</label>
                            <input type="text" class="form-control" name="diremp" id="diremp">
                        </div>
                        <div class="col-md-2">
                            <label for="razemp" class="etiquetaL">Razon Social Emp</label>
                            <input type="text" class="form-control" name="razemp" id="razemp">
                        </div>
                        <div class="col-md-2">
                            <label for="rfcemp" class="etiquetaL">RFC Empresa</label>
                            <input type="text" class="form-control" name="rfcemp" id="rfcemp" maxlength="12">
                        </div>
                        <div class="col-md-2">
                            <label for="telemp" class="etiquetaL">Telefono Empresa</label>
                            <input type="text" class="form-control" name="telemp" id="telemp" maxlength="10" pattern="\d{10}" inputmode="numeric" required>
                        </div>                      
                    </div>
                    <div class="row mb-2">
                        <div class="col-md-6">
                            <label for="nomcon" class="etiquetaL">Nombre Contacto</label>
                            <input type="text" class="form-control" name="nomcon" id="nomcon" required>
                        </div>
                        <div class="col-md-2">
                            <label for="gencon" class="etiquetaL">Genero Contacto</label>
                            <select class="form-control" name="gencon" id="gencon" required>
                                <option selected class="form-control" value = ""></option>
                                <option value="F">F</option>
                                <option value="M">M</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label for="telcon" class="etiquetaL">Telefono Contacto</label>
                            <input type="text" class="form-control" name="telcon" id="telcon" maxlength="10" pattern="\d{10}" inputmode="numeric" required>
                        </div>
                        <div class="col-md-2">
                            <label for="mailcon" class="etiquetaL">Mail Contacto</label>
                            <input type="text" class="form-control" name="mailcon" id="mailcon" required>
                        </div>
                    </div>                        
                </div>

                <hr>

                <div class="mt-4 px-3">
                    <h6 class="fw-bold">Sucursales</h6>

                    <table class="table table-sm table-bordered tablaSucursales">
                        <thead class="table-light">
                            <tr>
                                <th>Nombre Sucursal</th>
                                <th style="width: 60px;"></th>
                            </tr>
                        </thead>
                        <tbody id="bodySucursales">
                            <tr>
                                <td>
                                    <input type="hidden" name="suc_id[]" value="">
                                    <input type="text" name="suc_nombre[]" class="form-control" required>
                                </td>
                                <td class="text-center">
                                    <button type="button" class="btn btn-warning btn-sm eliminarFila" data-bs-toggle="tooltip"
                                            data-bs-placement="top" title="Elimina sucursal">
                                        <i class="fa-solid fa-trash"></i>
                                    </button>
                                </td>
                            </tr>
                        </tbody>
                    </table>

                    <button type="button" class="btn btn-primary btn-sm btnAgregarSucursal mt-2" data-bs-toggle="tooltip"
                            data-bs-placement="top" title="Agrega rango">
                        <i class="fa fa-plus"></i> Agregar Sucursal
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
<!-- /Fin de Modal nuevo -->