<div class="container-fluid py-4">
    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0"><i class="bi bi-pencil-square"></i> Editando género: <strong><?= esc($genero->genero) ?></strong></h5>
                </div>
                <div class="card-body">
                    <div class="col-md-12">
                        <form class="" method="post" action="<?php echo base_url(); ?>editar/update/genero/<?php echo $genero->id; ?>">
                            <?= csrf_field() ?>
                            <div class="form-group col-lg-12">
                                <label for="genero">Género</label>
                                <input id="genero" name="genero" type="text" class="form-control" value="<?php echo $genero->genero; ?>">
                            </div>
                            <div class="form-group col-lg-12">
                                <label for="descripcion">Descripción</label>
                                <textarea class="form-control " id="descripcion" name="descripcion" rows="" cols=""><?php echo $genero->descripcion; ?></textarea>
                                <!--                 					<input id="descripcion" type="text" class="validate"> -->
                            </div>
                            <div class="form-group col-lg-12 v2-mar-top-40 text-end pt-3">
                                <button class="btn btn-effect-ripple btn-primary" type="submit">
                                    <i class="bi bi-pencil-square"></i> Editar Género
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>