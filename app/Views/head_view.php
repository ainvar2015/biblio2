<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="csrf-token" content="<?= csrf_token() ?>">
<meta name="csrf-hash" content="<?= csrf_hash() ?>">
<meta name="base-url" content="<?= base_url() ?>">
<title><?= esc($titulo ?? 'Biblioteca') ?></title>
<link rel="icon" href="<?= base_url('assets/img/favicon.png') ?>">
