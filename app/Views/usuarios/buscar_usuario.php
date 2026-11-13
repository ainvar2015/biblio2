<div class="container-fluid py-4">
    <div class="row justify-content-center">
        <div class="col-md-8 col-lg-6">
            
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="bi bi-search"></i> Buscar usuario</h5>
                </div>
                <div class="card-body p-4">
                    
                    <p class="text-muted mb-4">
                        Busca socios o prestatarios externos por nombre, apellidos, DNI o email.
                    </p>
                    
                    <form action="<?= base_url('usuarios/buscar') ?>" method="get" class="needs-validation" novalidate>
                        <div class="input-group input-group-lg mb-3">
                            <span class="input-group-text bg-light">
                                <i class="bi bi-search"></i>
                            </span>
                            <input 
                                type="text" 
                                class="form-control" 
                                name="q" 
                                id="q"
                                placeholder="Nombre, apellidos, DNI o email..."
                                value="<?= esc($query ?? '') ?>"
                                required
                                autofocus>
                            <button class="btn btn-primary" type="submit">
                                <i class="bi bi-search me-1"></i> Buscar
                            </button>
                        </div>
                        <div class="invalid-feedback">
                            Introduce un término de búsqueda
                        </div>
                    </form>
                    
                    <?php if (!empty($query)): ?>
                        <div class="alert alert-info mt-3">
                            <i class="bi bi-info-circle me-2"></i>
                            Buscando: <strong><?= esc($query) ?></strong>
                        </div>
                    <?php endif; ?>
                    
                </div>
            </div>
            
        </div>
    </div>
</div>