    <aside class="right-aside">
        <!-- Content Header (Page header) -->
        <section class="content-header">
            <h1>Perfil de Usuario</h1>
        </section>
        <!-- Main content -->
        <section class="content">
            <div class="row">
                <div class="col-md-12 col-lg-12">
                    <div class="card ">
                        <div class="card-header">
                            <div class="row">
                                <h3 class="card-title col-md-3">
                                    Perfil del Usuario "<?= $usuario->login; ?>"
                                </h3>
                                <h5 class="text-end col-md-9 text-info-emphasis fw-semibold"><i class="fa fa-info-circle"></i> Para cambiar la contraseña deberá escribir una nueva. Si no la quiere cambiar, no escriba nada en ese campo.</h5>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="col-md-12">
                                <?= form_open('usuarios/update'.$usuario->id) ?>
                                    <div class="row">
                                        <div class="form-group col-md-3">
                                            <label for="login" class="control-label">Login<span class="fst-italic"> (el login no se puede modificar)</span></label>
                                            <input class="form-control" id="login" name="login" type="text" disabled value="<?= $usuario->login; ?>">
                                        </div>
                                        <div class="form-group col-md-3">
                                            <label for="password" class="control-label">Nueva Password</label>
                                            <input class="form-control" id="password" name="password" type="password" value="">
                                            <div class="error"><?= validation_show_error('password'); ?></div>
                                        </div>
                                        <div class="form-group col-md-3">
                                            <label for="password1" class="control-label">Repita el Password</label>
                                            <input class="form-control" id="password1" name="password1" type="password" value="">
                                            <div class="error"><?= validation_show_error('password1'); ?></div>
                                        </div>
                                        <div class="form-group col-md-3">
                                            <label for="fecha_nacimiento" class="control-label">
                                                Fecha de nacimiento
                                            </label>
                                            <input type="date" class="form-control pull-right" data-language='es' id="fecha_nacimiento" placeholder="Formato: dd/mm/aaaa" name="fecha_nacimiento" value="<?= $usuario->fecha_alta; ?>" />
                                        </div>
                                    </div>
                                    <hr />
                                    <div class="row">
                                        <div class="form-group col-lg-3">
                                            <label for="foto" class="control-label m-t-10">Fotografía (opcional)</label>
                                            <div class="input-group">
                                                <input type="file" class="form-control pull-right" data-language='es' id="foto" placeholder="Añada fotografía" name="foto" />
                                            </div>
                                        </div>
                                        <div class="form-group col-lg-3">
                                            <label for="nivel" class="control-label m-t-10">
                                                Nivel (el nivel solo lo puede modificar el administrador)
                                            </label>
                                            <?php if($usuario->nivel == 'adm'){ ?>
                                            <select class="form-select" id="nivel" name="nivel">
                                                <option value="adm"<?php if($usuario->nivel == 'adm') echo ' selected'; 
                                                 ?>>Administrador</option>
                                                <option value="usu"<?php if($usuario->nivel == 'usu') echo ' selected'; 
                                                 ?>>Usuario</option>
                                                <option value="inv"<?php if($usuario->nivel == 'inv') echo ' selected'; 
                                                 ?>>Invitado</option>
                                            </select>
                                            <?php }else{ 
                                                switch ($usuario->nivel){
                                                case 'usu':
                                                $elnivel = 'Usuario';
                                                break;
                                                case 'inv':
                                                $elnivel = 'Invitado';
                                                break;
                                                }?>
                                                <input class="form-control" type="text" id="nivel" name="nivel" disabled value="<?= $elnivel; ?>" />
                                            <?php } ?>
                                        </div>
                                        <div class="form-group col-md-6">
                                            <div class="row">
                                                <label class="control-label">Permisos</label>
                                                <div class="form-check form-switch col-md-6">
                                                    <input class="form-check-input" type="checkbox" role="switch" id="activo" name="activo" value="1" <?php if($usuario->activar == 1) echo 'checked'; if($usuario->nivel != 'adm') echo ' disabled'; ?> />
                                                    <label for="activo" class="form-check-label">Activo (modificable por el administrador)</label>
                                                </div>
                                                <div class="form-check form-switch col-md-6">
                                                    <input class="form-check-input" type="checkbox" role="switch" name="mostrar_email" id="mostrar_email" value="1" <?php if($usuario->mostrar_email == 1) echo 'checked'; ?> />
                                                    <label for="mostrar_email" class="form-check-label">Mostrar email</label>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="form-check form-switch col-md-6">
                                                    <input class="form-check-input" type="checkbox" role="switch" name="mostrar_nombre_real" id="mostrar_nombre_real" value="1" <?php if($usuario->mostrar_nombre_real == 1) echo 'checked'; ?> />
                                                    <label for="mostrar_nombre_real" class="form-check-label">Mostrar nombre real</label>
                                                </div>
                                                <div class="form-check form-switch col-md-6">
                                                    <input type="checkbox" class="form-check-input" role="switch" name="permitir_contacto" id="permitir_contacto" value="1" <?php if($usuario->permitir_contacto == 1) echo 'checked'; ?> />
                                                    <label for="permitir_contacto" class="form-check-label">Permitir  contacto</label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <hr />
                                    <div class="row">
                                        <div class="form-group col-lg-3">
                                            <label for="nombre" class="control-label m-t-10">
                                                Nombre
                                            </label>
                                            <div class="input-group">
                                                <input type="text" class="form-control pull-right" data-language='es' id="nombre" placeholder="Nombre" name="nombre" value="<?= $usuario->nombre; ?>" />
                                                <div class="error"><?= validation_show_error('nombre'); ?></div>
                                            </div>
                                        </div>
                                        <div class="form-group col-lg-4">
                                            <label for="apellidos" class="control-label m-t-10">
                                                Apellidos
                                            </label>
                                            <div class="input-group">
                                                <input type="text" class="form-control pull-right" data-language='es' id="apellidos" placeholder="Apellidos" name="apellidos" value="<?= $usuario->apellidos; ?>" />
                                            </div>
                                        </div>
                                        <div class="form-group col-lg-3">
                                            <label for="email" class="control-label m-t-10">
                                                Email <span class="fst-italic"> (el email no se puede modificar)</span>
                                            </label>
                                            <div class="input-group">
                                                <input type="email" class="form-control pull-right" data-language='es' id="email" placeholder="Email" disabled name="email" value="<?= $usuario->email; ?>" />
                                            </div>
                                        </div>
                                        <div class="form-group col-lg-2">
                                            <label for="telefono" class="control-label m-t-10">
                                                Teléfono
                                            </label>
                                            <div class="input-group">
                                                <input type="tel" class="form-control pull-right" data-language='es' id="telefono" placeholder="Teléfono" name="telefono" value="<?= $usuario->telefono; ?>" />
                                            </div>
                                        </div>
                                    </div>
                                    <hr />
                                    <div class="row">
                                        <div class="form-group col-lg-3">
                                            <label for="direccion" class="control-label m-t-10">
                                                Dirección
                                            </label>
                                            <div class="input-group">
                                                <input type="text" class="form-control pull-right" data-language='es' id="direccion" placeholder="Dirección" name="direccion" value="<?= $usuario->direccion; ?>" />
                                            </div>
                                        </div>
                                        <div class="form-group col-lg-4">
                                            <label for="apellidos" class="control-label m-t-10">
                                                Población
                                            </label>
                                            <div class="input-group">
                                                <input type="text" class="form-control pull-right" data-language='es' id="poblacion" placeholder="Población" name="poblacion" value="<?= $usuario->poblacion; ?>" />
                                            </div>
                                        </div>
                                        <div class="form-group col-lg-3">
                                            <label for="provincia" class="control-label m-t-10">
                                                Provincia
                                            </label>
                                            <div class="input-group">
                                                <input type="text" class="form-control pull-right" data-language='es' id="provincia" placeholder="Provincia" name="provincia" value="<?= $usuario->provincia; ?>" />
                                            </div>
                                        </div>
                                        <div class="form-group col-lg-2">
                                            <label for="c_postal" class="control-label m-t-10">
                                                Código postal
                                            </label>
                                            <div class="input-group">
                                                <input type="numeric" class="form-control pull-right" data-language='es' id="c_postal" placeholder="Código postal" name="c_postal" value="<?= $usuario->c_postal; ?>" />
                                            </div>
                                        </div>
                                    </div>
                                    <hr />
                                    <div class="form-group col s12 v2-mar-top-40">
                                        <button class="btn btn-effect-ripple btn-primary" type="submit">
                                            Editar perfil
                                        </button>
                                    </div>
                            </div>
                            <input type="hidden" name="fecha_ultima_modificacion" value="<?= date('Y-m-d'); ?>">
                            <?= form_close() ?>
                        </div>
                    </div>
                </div>
            </div>
            </div>
        </section>
        <!-- /.content -->
    </aside>
    <!-- /.right-side -->
    </div>
    <!-- ./wrapper -->