{{> header}}
{{#usuarioAdmin}}
    <main>
        <section class="row justify-content-center m-3 ancho">
            <article class=" justify-content-center mt-4 ancho2">
                <h3 class="text-dark mb-3">Registrar empleado</h3>
                <form class="form-horizontal" role="form" action="/registroEmpleado/registroEmpleado" method="post" enctype="multipart/form-data">
                    <div class="intDatos">
                        <h2>Introduzca los datos del empleado.</h2>
                    </div>
                    <div class="form-group">
                        {{#nombreUsuarioError}}
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                <strong>El nombre de usuario no existe.</strong>
                                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                        {{/nombreUsuarioError}}
                        {{#registroExitoso}}
                            <div class="alert alert-success alert-dismissible fade show" role="alert">
                                <strong>Se registro el empleado con exito.</strong>
                                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                        {{/registroExitoso}}
                        {{#camposVacios}}
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                <strong>Se debe completar todos los campos.</strong>
                                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                        {{/camposVacios}}
                        <label for="nombreUsuario" class="col-12 control-label"><span class="text-info">*</span>Nombre de usuario</label>
                        <div class="col-12">
                            <input type="text" id="nombreUsuario" name="nombreUsuario" placeholder="Nombre de Usuario" class="form-control" value="{{nombreUsuario}}" required>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="tipoLicencia" class="col-12 control-label"><span class="text-info">*</span>Tipo de licencia</label>
                        <div class="col-12">
                            <select name="tipoLicencia" id="tipoLicencia" class="custom-select form-control">
                                <option selected disabled>-</option>
                                <option value="auto">Auto</option>
                                <option value="camion">Camion</option>
                                <option value="tractor">Tractor</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="rolAsignar" class="col-12 control-label"><span class="text-info">*</span>Rol a asignar</label>
                        <div class="col-12">
                            <select name="rolAsignar" id="rolAsignar" class="custom-select form-control">
                                <option selected disabled>-</option>
                                <option value="1">Administrador</option>
                                <option value="2">Supervisor</option>
                                <option value="3">Encargado</option>
                                <option value="4">Chofer</option>
                                <option value="5">Mecanico</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="col-sm-9 col-sm-offset-3">
                            <span class="help-block alert alert-info">*Campos requeridos</span>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-outline-primary btn-block">Registrar</button>
                </form>
            </article>

        </section>
    </main>
{{/usuarioAdmin}}
{{^usuarioAdmin}}
    {{> error404}}
{{/usuarioAdmin}}
{{> footer}}