<div class="mainbar">
    <div class="container-fluid mt-4">

        <div class="row g-3">

            <?= renderWidget([
                'title' => 'Libros',
                'icon'  => 'navicon',
                'value' => $totalLibros,
                'text'  => 'Libros registrados',
                'color' => 'blue',
                'actions' => false
            ]) ?>

            <?= renderWidget([
                'title' => 'Autores',
                'icon'  => 'image',
                'value' => $totalAutores,
                'text'  => 'Autores registrados',
                'color' => 'green',
                'actions' => false
            ]) ?>

            <?= renderWidget([
                'title' => 'Editoriales',
                'icon'  => 'globe',
                'value' => $totalEditoriales,
                'text'  => 'Editoriales registradas',
                'color' => 'orange',
                'actions' => false
            ]) ?>

            <?= renderWidget([
                'title' => 'Géneros',
                'icon'  => 'globe',
                'value' => $totalGeneros,
                'text'  => 'Géneros registrados',
                'color' => 'yellow',
                'actions' => false
            ]) ?>

            <?= renderWidget([
                'title' => 'Usuarios',
                'icon'  => 'users',
                'value' => $totalUsuarios,
                'text'  => 'Usuarios registrados',
                'color' => 'red',
                'actions' => false
            ]) ?>

            <?= renderWidget([
                'title' => 'Calendario',
                'icon'  => 'calendar',
                'value' => $totalEventos,
                'text'  => 'Eventos programados',
                'color' => 'dark',
                'actions' => false
            ]) ?>

        </div>

    </div>
</div>