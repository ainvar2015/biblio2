<!-- Modal 1 AUTOR -->
<div id="addautor" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="addpoblacionLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">X</button>
        <h4 class="modal-title">AÑADIR AUTOR A LA BASE DE DATOS <strong></strong></h4>
      </div>
      <div class="modal-body" id="html_data">
      <form class="" method="post" action="<?php echo base_url(); ?>editar/update/autores/<?php echo $autor->id; ?>">
               						<div class="form-group col-lg-12">
                                        <label for="nombreautor" class="control-label">Nombre del autor</label>
                                        <input class="form-control" id="nombreautor" name="nombreautor" type="text" value="<?php echo $autor->nombreautor; ?>">
               						</div>
               						<div class="form-group col-lg-12">
                                        <label for="lugar_nacimiento" class="control-label">Lugar de nacimiento</label>
              							<input class="form-control" id="lugar_nacimiento" name="lugar_nacimiento" type="text" value="<?php echo $autor->lugar_nacimiento; ?>">
                                       </div>
                                    <div class="row">
                                        <div class="form-group col-lg-4">
                                            <label for="fecha_nacimiento" class="control-label m-t-10">
                                                Fecha de nacimiento
                                            </label>
                                            <div class="input-group">
                                                <div class="input-group-addon">
                                                    <i class="fa fa-fw fa-calendar"></i>
                                                </div>
                                                <input type="text" class="form-control pull-right" data-language='es' id="fecha_nacimiento" name="fecha_nacimiento"  value="<?php echo $autor->fecha_nacimiento; ?>" />
                                            </div>
                                        </div>
                                        <div class="form-group col-lg-4">
                                            <label for="fecha_fallecimiento" class="control-label m-t-10">
                                                Fecha fallecimiento
                                            </label>
                                            <div class="input-group">
                                                <div class="input-group-addon">
                                                    <i class="fa fa-fw fa-calendar"></i>
                                                </div>
                                                <input type="text" class="form-control pull-right" data-language='es' id="fecha_fallecimiento" name="fecha_muerte" value="<?php echo $autor->fecha_muerte; ?>" />
                                            </div>
                                        </div>
                                        <div class="form-group col-lg-4"></div>
                                    </div>
                                    <div class="row">
                						<div class="form-group col-lg-12">
                                            <label for="biografia">Datos biográficos</label>
                							<textarea id="biografia" name="biografia" rows="8" class="form-control"><?php echo $autor->biografia; ?></textarea>
                						</div>
                					</div>
                                   	<div class="form-group col s12 v2-mar-top-40"> 
                                        <button class="btn btn-effect-ripple btn-primary" type="submit">
                                            Editar Autor
                                        </button> 
                                    </div>
                				</form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal" aria-hidden="true">Cerrar Ventana</button>
      </div>
    </div>
  </div>
</div>
<!-- FIN MODAL 2 -->