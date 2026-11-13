<div class="container-fluid py-4">
        <div class="row">
            <div class="col-12">
                <div class="card shadow-sm">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0"><i class="bi bi-pencil-square"></i> Editando editorial: <strong><?= esc($editorial->nombreeditorial) ?></strong></h5>
                    </div>
                        <div class="card-body">
                            <div class="col-md-12">
								<form class="" method="post" action="<?php echo base_url(); ?>editar/update/editorial/<?php echo $editorial->id; ?>">
                                    <?= csrf_field() ?>
               						<div class="form-group col-lg-12">
                                       <label for="nombreeditorial">Nombre de la Editorial</label>
               							<input id="nombreeditorial" name="nombreeditorial" type="text" class="form-control" value="<?php echo $editorial->nombreeditorial; ?>">
               						</div>
               						<div class="form-group col-lg-12">
                                       <label for="descripcion">Descripción</label>
               							<textarea class="form-control" id="descripcion" name="descripcion" rows="" cols=""><?php echo $editorial->descripcion; ?></textarea>
               						</div>
               						<input type="hidden" name="fecha_alta" value="<?php echo date("Y-m-d"); ?>">
                                    <div class="row">
                                    	<div class="form-group col-lg-12 v2-mar-top-40 text-end pt-3"> 
                                            <button class="btn btn-effect-ripple btn-primary" type="submit">
                                                <i class="bi bi-pencil-square"></i> Editar Editorial
                                            </button> 
                                        </div>
                                    </div>
                				</form>
							</div>
                        </div>
                    </div>
                </div>
            </div>
</div>