    <div class="container-fluid py-4">
        <div class="row">
            <div class="col-12">
                <div class="card shadow-sm">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <div class="col-lg-5">
                            <h5 class="mb-0"><i class="bi bi-pencil-square"></i> Editando ajustes de la aplicación</h5>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-12">
                                <?= form_open('admin/update/settings') ?>
                                    <div class="row">
                                        <div class="form-group col-lg-4">
                                            <label for="base_url">URL de la página</label>
                                            <input id="base_url" name="base_url" type="url" class="form-control" value="<?= $settings->base_url; ?>" />
                                            <div class="error"><?= validation_show_error('base_url'); ?></div>
                                        </div>
                                        <div class="form-group col-lg-4">
                                            <label for="titulo_pagina">Título página</label>
                                            <input id="titulo_pagina" name="titulo_pagina" type="text" class="form-control" value="<?= $settings->titulo_pagina; ?>" />
                                            <div class="error"><?= validation_show_error('titulo_pagina'); ?></div>
                                        </div>
                                        <div class="form-group col-lg-4">
                                            <label for="nombre_bd">Nombre de la BD</label>
                                            <input id="nombre_bd" name="nombre_bd" type="text" class="form-control" value="<?= $settings->nombre_bd; ?>" />
                                            <div class="error"><?= validation_show_error('nombre_bd'); ?></div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="form-group col-lg-4">
                                            <label for="nom_empresa">Nombre de la empresa</label>
                                            <input id="nom_empresa" name="nom_empresa" type="text" class="form-control" value="<?= $settings->nom_empresa; ?>">
                                        </div>
                                        <div class="form-group col-lg-2">
                                            <label for="cif">Cif empresa</label>
                                            <input id="cif" name="cif" type="text" class="form-control" value="<?= $settings->cif; ?>">
                                        </div>
                                        <div class="form-group col-lg-2">
                                            <label for="telefono">Teléfono empresa</label>
                                            <input id="telefono" name="telefono" type="text" class="form-control" value="<?= $settings->telefono; ?>">
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="form-group col-lg-5">
                                            <label for="direccion">Dirección</label>
                                            <input id="direccion" name="direccion" type="text" class="form-control" value="<?= $settings->direccion; ?>">
                                        </div>
                                        <div class="form-group col-lg-2">
                                            <label for="poblacion">Población</label>
                                            <input id="poblacion" name="poblacion" type="text" value="<?= $settings->poblacion; ?>" class="form-control">
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="form-group col-lg-2">
                                            <label for="provincia">Provincia</label>
                                            <div class="input-group">
                                                <input id="provincia" name="provincia" type="text" value="<?= $settings->provincia; ?>" class="form-control" />
                                            </div>
                                        </div>
                                        <div class="form-group col-lg-2">
                                            <label for="cod_postal">Código postal</label>
                                            <input id="cod_postal" name="cod_postal" type="text" value="<?= $settings->cod_postal; ?>" class="form-control" />
                                        </div>
                                        <div class="form-group col-lg-5">
                                            <label for="pais">País</label>
                                            <select id="pais" name="pais" class="form-control select2">
                                                <?php foreach ($paises as $pais) { ?>
                                                    <option value="<?= $pais->country_name; ?>"
                                                        <?php if ($pais->country_name == $settings->pais) echo ' selected'; ?>>
                                                        <?= $pais->country_name; ?></option>';
                                                <?php } ?>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="form-group col-lg-12 v2-mar-top-40 text-end pt-3"> <button class="btn btn-effect-ripple btn-primary" type="submit"><i class="bi bi-save"></i> Guardarpreferencias</button> </div>
                                    </div>
                                <?= form_close() ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>