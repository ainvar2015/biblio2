<?php

if (!function_exists('renderWidget')) {

    /**
     * Renderiza un widget tipo dashboard (Bootstrap 5)
     *
     * @param array $config
     *  [
     *    'title'   => 'Usuarios',
     *    'icon'    => 'users',
     *    'value'   => '128',
     *    'text'    => 'Usuarios activos',
     *    'color'   => 'green', // green|blue|orange|red|dark
     *    'col'     => 'col-xl-4 col-md-6',
     *    'actions' => true
     *  ]
     *
     * @return string
     */
    function renderWidget(array $config = []): string
    {
        $title   = $config['title']   ?? '';
        $icon    = $config['icon']    ?? '';
        $value   = $config['value']   ?? '';
        $text    = $config['text']    ?? '';
        $color   = $config['color']   ?? 'blue';
        $col     = $config['col']     ?? 'col-xl-4 col-md-6';
        $actions = $config['actions'] ?? true;

        ob_start();
?>

        <div class="<?= esc($col) ?>">
            <div class="card widget widget-<?= esc($color) ?> h-100">

                <div class="card-header d-flex justify-content-between align-items-center">
                    <span>
                        <?php if ($icon): ?>
                            <i class="fa fa-<?= esc($icon) ?>"></i>
                        <?php endif; ?>
                        <?= esc($title) ?>
                    </span>

                    <?php if ($actions): ?>
                        <div class="widget-actions">
                            <a href="#" class="widget-minimize">
                                <i class="fa fa-chevron-up"></i>
                            </a>
                        </div>
                    <?php endif; ?>
                </div>

                <div class="card-body text-center">
                    <?php if ($value !== ''): ?>
                        <h2><?= esc($value) ?></h2>
                    <?php endif; ?>

                    <?php if ($text): ?>
                        <small><?= esc($text) ?></small>
                    <?php endif; ?>
                </div>

            </div>
        </div>

<?php
        return ob_get_clean();
    }
}
