    <div class="container-fluid py-4">
        <div class="row">
            <div class="col-12">
                <div class="card shadow-sm">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0"><i class="bi bi-plus-circle"></i> Alta de autores</h5>
                    </div>
                    <div class="card-body">
                        <div class="col-md-12">
                            <form class="" method="post" action="<?php echo base_url(); ?>altas/guardar/autor" id="formAltaAutor">
                                <?= csrf_field() ?>
                                <div class="form-group col-lg-12">
                                    <label for="nombreautor" class="control-label">Nombre del autor</label>
                                    <input class="form-control" id="nombreautor" name="nombreautor" type="text" class="validate">
                                </div>
                                <div class="form-group col-lg-12">
                                    <label for="lugar_nacimiento" class="control-label">Lugar de nacimiento</label>
                                    <input class="form-control" id="lugar_nacimiento" name="lugar_nacimiento" type="text" class="validate">
                                </div>
                                <div class="row">
                                    <div class="form-group col-lg-4">
                                        <label for="fecha_nacimiento" class="control-label m-t-10">
                                            Fecha de nacimiento
                                        </label>
                                        <div class="input-group">
                                            <input type="date" class="form-control pull-right" data-language='es' id="fecha_nacimiento" name="fecha_nacimiento" />
                                        </div>
                                    </div>
                                    <div class="form-group col-lg-4">
                                        <label for="fecha_fallecimiento" class="control-label m-t-10">
                                            Fecha fallecimiento
                                        </label>
                                        <div class="input-group">
                                            <input type="date" class="form-control pull-right" data-language='es' id="fecha_fallecimiento" name="fecha_muerte" />
                                        </div>
                                    </div>
                                    <div class="form-group col-lg-4"></div>
                                </div>
                                <!-- Datos biográficos -->
                                <div class="row mb-3 <?= empty($autor->biografia) ? 'd-none' : '' ?>" id="datosBiograficosRow">
                                    <div class="col-lg-12">
                                        <label for="biografia" class="form-label">Datos biográficos</label>
                                        <textarea id="biografia" name="biografia" rows="6" class="form-control"></textarea>
                                    </div>
                                </div>

                                <div class="row mb-3" id="btnMostrarDatosBiograficos">
                                    <div class="col-lg-12 pt-3">
                                        <button type="button" class="btn btn-outline-secondary" id="muestraDatosBiograficos">Añadir datos biográficos</button>
                                    </div>
                                </div>
                                <div class="form-group col s12 v2-mar-top-40 pt-3 text-end">
                                    <button class="btn btn-effect-ripple btn-primary" type="submit">
                                        <i class="bi bi-save"></i> Guardar Autor
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php
        $toast = session()->getFlashdata('toast');
        if ($toast):
        ?>
            <script>
                document.addEventListener('DOMContentLoaded', function() {
                    if (typeof Toastify !== 'undefined') {
                        Toastify({
                            text: '<?= json_encode($toast['message']) ?>',
                            duration: 4000,
                            gravity: 'top',
                            position: 'right',
                            style: {
                                background: '<?= $toast['type'] === 'success' ? '#198754' : '#dc3545' ?>',
                                color: '#fff'
                            }
                        }).showToast();
                    } else {
                        alert('<?= json_encode($toast['message']) ?>');
                    }
                });
            </script>
        <?php endif; ?>
    </div>