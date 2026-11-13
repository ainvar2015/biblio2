<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pantalla Bloqueada</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .lock-screen {
            background: rgba(255, 255, 255, 0.95);
            padding: 3rem;
            border-radius: 20px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
            text-align: center;
            max-width: 400px;
            width: 90%;
        }
        .user-avatar {
            width: 100px;
            height: 100px;
            background: #e9ecef;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1.5rem;
            font-size: 3rem;
            color: #6c757d;
        }
        .lock-icon {
            font-size: 4rem;
            color: #ffc107;
            margin-bottom: 1rem;
        }
    </style>
</head>
<body>
    <div class="lock-screen">
        <div class="lock-icon">
            <i class="bi bi-lock-fill"></i>
        </div>
        
        <div class="user-avatar">
            <i class="bi bi-person-fill"></i>
        </div>
        
        <h3 class="mb-1"><?= esc(session()->get('username') ?? 'Usuario') ?></h3>
        <p class="text-muted mb-4">Sesión bloqueada</p>
        
        <?php if (session()->getFlashdata('error')): ?>
            <div class="alert alert-danger alert-dismissible fade show">
                <?= session()->getFlashdata('error') ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>
        
        <form action="<?= base_url('auth/verificarBloqueo') ?>" method="post">
            <?= csrf_field() ?>
            
            <div class="mb-3">
                <input type="password" 
                       name="password" 
                       class="form-control form-control-lg" 
                       placeholder="Ingresa tu contraseña" 
                       required 
                       autofocus>
            </div>
            
            <button type="submit" class="btn btn-primary w-100 btn-lg">
                <i class="bi bi-unlock"></i> Desbloquear
            </button>
        </form>
        
        <div class="mt-3">
            <a href="<?= base_url('logout') ?>" class="text-decoration-none text-danger">
                <i class="bi bi-box-arrow-right"></i> Cerrar sesión
            </a>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>