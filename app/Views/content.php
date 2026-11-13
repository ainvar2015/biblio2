    <aside class="right-aside">
        <!-- Content Header (Page header) -->
        <section class="content-header">
            <h1>Alta de Autores</h1>
        </section>
        <!-- Main content -->
        <section class="content">
            <div class="row">
                <div class="col-md-12 col-lg-12">
                    <div class="card ">
                        <div class="card-header">
                            <h3 class="card-title">
                                Basic Form Validation
                            </h3>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-12">
                                <form class="" method="post" action="<?php echo base_url(); ?>altas/add_libro">
                					<div class="row">
                						<div class="form-group col s5">
                							<div class="switch">
                								<label> Libro
                									<input name="tipo_item" type="checkbox" value="1"> <span class="lever"></span> CD </label>
                							</div>
                						</div>
                						<div class="form-group col s7">
                                            <label for="codigoISBN">ISBN</label>
                							<input id="codigoISBN" name="codigoISBN" type="text" class="form-control">
                						</div>
                					</div>
                					<div class="row">
                						<div class="form-group col s12">
                                            <label for="titulo">Título</label>
                							<input id="titulo" name="titulo" type="text" class="form-control">
                						</div>
                					</div>
                					<div class="row">
                						<div class="form-group col s10">
											<label for="autor">Autor</label>
                							<select class="form-control select2" id="autor" name="autor">
                								<option value='' disabled selected>Seleccione un Autor &raquo;</option>
                								<?php foreach($autores as $autor){ ?>
                								<option value="<?php echo $autor->id; ?>"><?php echo $autor->nombreautor; ?></option>
                								<?php } ?>
											</select>
                						</div>
                						<div class="form-group col s2">
                							<a class="waves-effect waves-light btn btn-outline-primary btn-sm lightbox" href="<?php echo base_url(); ?>altas/nuevoAutorLB?lightbox[iframe]=true&lightbox[width]=600&lightbox[height]=550"><i class="fa fa-plus"></i> autor</a>
                						</div>
                					</div>
                					<div class="row">
                						<div class="form-group col s10">
											<label for="editorial">Editorial</label>
                							<select class="form-control select2" id="editorial" name="editorial">
                								<option value='' disabled selected>Seleccione una Editorial &raquo;</option>
                								<?php foreach($editoriales as $editorial){ ?>
                								<option value="<?php echo $editorial->id; ?>"><?php echo $editorial->nombreeditorial; ?></option>
                								<?php } ?>
											</select>
                						</div>
                						<div class="form-group col s2">
                							<a class="waves-effect waves-light btn btn-outline-primary btn-sm lightbox" href="<?php echo base_url(); ?>altas/nuevaEditorialLB?lightbox[iframe]=true&lightbox[width]=600&lightbox[height]=500"><i class="fa fa-plus"></i> editorial</a>
                						</div>
                					</div>
                					<div class="row">
                						<div class="form-group col s10">
											<label for="genero">Género</label>
                							<select class="form-control select2" multiple="multiple" id="genero" name="genero">
                								<option value='' disabled>Seleccione un Género &raquo; </option>
                								<?php foreach($generos as $genero){ ?>
                								<option value="<?php echo $genero->id; ?>"><?php echo $genero->genero; ?></option>
                								<?php } ?>
											</select>
                						</div>
                					    <div class="form-group col s2">
                						    <a class="waves-effect waves-light btn btn-outline-primary btn-sm lightbox" href="<?php echo base_url(); ?>altas/nuevoGeneroLB?lightbox[iframe]=true&lightbox[width]=600&lightbox[height]=500"><i class="fa fa-plus"></i> género</a>
                					    </div>
                                    </div>
                					<div class="row">
                						<div class="form-group col s6">
                                            <label for="n_paginas">Nº de páginas</label>
                							<input id="n_paginas" name="n_paginas" type="number" class="form-control">
                						</div>
                						<div class="form-group col s6">
                                            <label for="ano_edicio">Año de edición (Ej. 2017)</label>
                							<input id="ano_edicio" name="ano_edicio" type="text" class="form-control">
                						</div>
                					</div>
                                    <div class="row">
                                        <input type="hidden" name="fecha_alta" value="<?php echo date('Y-m-d'); ?>" />
                                        <div class="form-group col s12 v2-mar-top-40"> <button class="btn btn-effect-ripple btn-primary" type="submit">Guardar Libro</button> </div>
                                    </div>
                				</form>
                                </div>
                            </div>
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