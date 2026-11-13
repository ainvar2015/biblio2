<!-- app/Views/buscar_usuario.php -->
<div class="container-fluid py-4">
    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="bi bi-search"></i> Buscar usuario</h5>
                </div>
                <div class="card-body">
                    <form id="formBuscarUsuario" method="get" action="<?= base_url('historial/buscar') ?>">
                        <div class="row g-3">
                            <div class="col-md-4">
                                <label class="form-label">Nombre o DNI/Email</label>
                                <input type="text" class="form-control" name="q" placeholder="Ej: Juan Pérez o 12345678A" required>
                            </div>
                            <div class="col-md-2 d-flex align-items-end">
                                <button type="submit" class="btn btn-primary w-100">
                                    <i class="bi bi-search"></i> Buscar
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>