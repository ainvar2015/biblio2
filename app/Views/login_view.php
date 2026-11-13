<!DOCTYPE html>
<html lang="en">

<head>
  <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
  <meta charset="utf-8">
  <!-- Title and other stuffs -->
  <title>Login - Biblio</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="description" content="">
  <meta name="keywords" content="">
  <meta name="author" content="">

  <!-- Stylesheets -->
  <link href="<?php echo base_url('assets/css/bootstrap.min.css'); ?>" rel="stylesheet">
  <link rel="stylesheet" href="<?php echo base_url('assets/css/font-awesome.min.css'); ?>">
  <link href="<?php echo base_url('assets/css/style.css'); ?>" rel="stylesheet">

  <script src="<?php echo base_url('assets/js/respond.min.js'); ?>"></script>
  <!--[if lt IE 9]>
  <script src="js/html5shiv.js"></script>
  <![endif]-->

  <!-- Favicon -->
  <link rel="shortcut icon" href="<?php echo base_url(); ?>assets/img/favicon.png">
</head>

<body>

  <!-- Form area -->
  <div class="admin-form">
    <div class="container">

      <div class="row">
        <div class="col-md-12">
          <!-- Widget starts -->
          <div class="widget worange">
            <!-- Widget head -->
            <div class="widget-head">
              <i class="fa fa-lock"></i> Login
            </div>

            <div class="widget-content">
              <div class="padd">
                <!-- Login form -->
                <?= form_open('authenticate') ?>
                  <div class="mb-3">
                    <label for="login" class="form-label">Usuario</label>
                    <input type="text" name="login" id="login" class="form-control"
                      value="<?= old('login') ?>" required>
                  </div>

                  <div class="mb-3">
                    <label for="password" class="form-label">Contraseña</label>
                    <input type="password" name="password" id="password"
                      class="form-control" required autocomplete="current-password">
                  </div>
                  <?php if (isset($error)): ?>
                    <div class="text-danger small">
                      <?= $error ?>
                    </div>
                  <?php endif; ?>

                  <div class="form-check form-switch mb-3">
                    <input class="form-check-input" type="checkbox" name="recordar" id="recordar">
                    <label class="form-check-label" for="recordar">
                      Recordarme
                    </label>
                  </div>

                  <button type="submit" class="btn btn-primary w-100">
                    Entrar
                  </button>
                <?= form_close() ?>

              </div>
            </div>
            <div class="widget-foot">
              ¿No Registrado? <a href="#">Regístrese aquí</a>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>



</body>
</html>