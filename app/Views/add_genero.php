<div class="container-fluid py-4">
    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0"><i class="bi bi-plus-circle"></i> Alta de géneros</h5>
                </div>
                <div class="card-body">
                    <div class="col-md-12">
                        <form id="formAltaGenero" method="post" action="<?= base_url(); ?>altas/guardar/genero">
                            <?= csrf_field() ?>
                            <div class="form-group col-lg-12">
                                <label for="genero">Género</label>
                                <input id="genero" name="genero" type="text" class="form-control">
                            </div>
                            <div class="form-group col-lg-12">
                                <label for="descripcion">Descripción</label>
                                <textarea class="form-control " id="descripcion" name="descripcion" rows="" cols=""></textarea>
                                <!--                 					<input id="descripcion" type="text" class="validate"> -->
                            </div>
                    </div>
                    <div class="form-group col-lg-12 v2-mar-top-40 pt-3 text-end"> <button class="btn btn-effect-ripple btn-primary" type="submit"><i class="bi bi-save"></i> Género</button> </div>
                </div>
                </form>
            </div>
        </div>
    </div>
</div>
</div>
</section>
<!-- /.content -->
</aside>
<!-- /.right-side -->
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
<!-- ./wrapper -->