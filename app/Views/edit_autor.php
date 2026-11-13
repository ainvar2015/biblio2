    <div class="container-fluid py-4">
        <div class="row">
            <div class="col-12">
                <div class="card shadow-sm">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0"><i class="bi bi-pencil-square"></i> Editando autor: <strong><?= esc($autor->nombreautor) ?></strong></h5>
                    </div>
                    <div class="card-body">
                        <div class="col-md-12">
                            <?= form_open('editar/update/autores/'.$autor->id) ?>
                                <div class="form-group col-lg-12">
                                    <label for="nombreautor" class="control-label">Nombre del autor</label>
                                    <input class="form-control" id="nombreautor" name="nombreautor" type="text" value="<?= $autor->nombreautor; ?>">
                                </div>
                                <div class="form-group col-lg-12">
                                    <label for="lugar_nacimiento" class="control-label">Lugar de nacimiento</label>
                                    <input class="form-control" id="lugar_nacimiento" name="lugar_nacimiento" type="text" value="<?= $autor->lugar_nacimiento; ?>">
                                </div>
                                <div class="row">
                                    <div class="form-group col-lg-4">
                                        <label for="fecha_nacimiento" class="control-label m-t-10">
                                            Fecha de nacimiento
                                        </label>
                                        <div class="input-group">
                                            <input type="date" class="form-control pull-right" data-language='es' id="fecha_nacimiento" placeholder="Formato: dd/mm/aaaa" name="fecha_nacimiento" value="<?= $autor->fecha_nacimiento; ?>" />
                                        </div>
                                    </div>
                                    <div class="form-group col-lg-4">
                                        <label for="fecha_fallecimiento" class="control-label m-t-10">
                                            Fecha fallecimiento
                                        </label>
                                        <div class="input-group">
                                            <input type="date" class="form-control pull-right" data-language='es' id="fecha_muerte" placeholder="Formato: dd/mm/aaaa" name="fecha_muerte" value="<?= $autor->fecha_muerte; ?>" />
                                        </div>
                                    </div>
                                    <div class="form-group col-lg-4"></div>
                                </div>
                                <!-- Reseña -->
                            <div class="row mb-3 <?= empty($autor->biografia) ? 'd-none' : '' ?>" id="datosBiograficosRow">
                                <div class="col-lg-12">
                                    <label for="biografia" class="form-label">Reseña del autor</label>
                                    <textarea id="biografia" name="biografia" rows="12" class="form-control"><?= esc($autor->biografia) ?></textarea>
                                </div>
                            </div>
                            <?php if (empty($autor->biografia)): ?>
                                <div class="row mb-3" id="btnMostrarDatosBiograficos">
                                    <div class="col-lg-12 pt-3">
                                        <button type="button" class="btn btn-outline-secondary" id="muestraDatosBiograficos">Añadir datos biográficos</button>
                                    </div>
                                </div>
                            <?php endif; ?>
                            <!-- Botón editar -->
                                <div class="form-group col s12 v2-mar-top-40 text-end pt-3">
                                    <button class="btn btn-effect-ripple btn-primary" type="submit">
                                        <i class="bi bi-pencil-square"></i> Editar Autor
                                    </button>
                                </div>
                            <?= form_close() ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
