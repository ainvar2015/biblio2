<?php

/** @var string $tipo */
switch ($tipo) {
  case 'autores':
    $singular = 'autor';
    $tablaId = 'tableAutores';
    break;
  case 'editoriales':
    $singular = 'editorial';
    $tablaId = 'tableEditoriales';
    break;
  case 'generos':
    $singular = 'genero';
    $tablaId = 'tableGeneros';
    break;
  case 'libros':
    $singular = 'libro';
    $tablaId = 'tableLibros';
    break;
  case 'prestamos':
    $singular = 'prestamo';
    $tablaId = 'tablePrestamos';
    break;
  default:
    $singular = '';
    break;
}
?>
<div class="container-fluid py-4">
  <div class="row">
    <div class="col-12">
      <div class="card shadow-sm">
        <div class="card-header d-flex justify-content-between align-items-center">
          <h5 class="mb-0"><i class="bi bi-table"></i> Tabla de <?= $tipo ?></h5>
          <?php if ($tipo !== 'prestamos'): ?>
          <a href="<?= base_url("altas/nuevo/") . $singular ?>" class="btn btn-success btn-sm">
            <i class="bi bi-plus"></i> Nuevo <?= ucfirst($singular) ?>
          </a>
          <?php endif; ?>
        </div>

        <div class="card-body">
          <?php if (!empty($items)): ?>
            <div class="table-responsive">
              <table id="datatable" class="table table-striped table-bordered" style="width:100%">
                <thead>
                  <tr>
                    <?php if ($tipo === 'autores'): ?>
                      <th>Nombre Autor</th>
                      <th>Lugar Nacimiento</th>
                      <th>Fecha Nacimiento</th>
                      <th>Acciones</th>
                    <?php elseif ($tipo === 'editoriales'): ?>
                      <th>Nombre Editorial</th>
                      <th>Descripción</th>
                      <th>Acciones</th>
                    <?php elseif ($tipo === 'generos'): ?>
                      <th>Nombre Género</th>
                      <th>Descripción</th>
                      <th>Acciones</th>
                    <?php elseif ($tipo === 'libros'): ?>
                      <th>Título</th>
                      <th>Autor</th>
                      <th>Género</th>
                      <th>Editorial</th>
                      <th>Estado</th>
                      <th>Acciones</th>
                    <?php elseif ($tipo === 'prestamos'): ?>
                      <th>ID Préstamo</th>
                      <th>ID Libro</th>
                      <th>Usuario</th>
                      <th>ISBN</th>
                      <th>Título Libro</th>
                      <th>Fecha Salida</th>
                      <th>Estado</th>
                      <th>Acciones</th>
                    <?php endif; ?>
                  </tr>
                </thead>
                <tbody>
                  <?php foreach ($items as $item): ?>
                    <tr>
                      <?php if ($tipo === 'autores'): ?>
                        <td><?= esc($item->nombreautor) ?></td>
                        <td><?= esc($item->lugar_nacimiento) ?></td>
                        <td><?= date('d-m-Y', strtotime($item->fecha_nacimiento)) ?></td>
                        <td width="90" align="center">
                          <a href="<?= base_url("editar/autor/{$item->id}") ?>" class="btn btn-sm btn-outline-primary"><i class="bi bi-pencil-square" title="Editar <?= $item->nombreautor ?>"></i></a>
                          <button class="btn btn-outline-danger btn-sm btn-borrar"
                            data-id="<?= $item->id ?>"
                            data-nombre="<?= esc($item->nombreautor) ?>"
                            data-tipo="autores">
                            <i class="bi bi-trash" title="Eliminar <?= $item->nombreautor ?>"></i>
                          </button>

                        </td>

                      <?php elseif ($tipo === 'editoriales'): ?>
                        <td><?= esc($item->nombreeditorial) ?></td>
                        <td><?= esc($item->descripcion) ?></td>
                        <td width="90" align="center">
                          <a href="<?= base_url("editar/editorial/{$item->id}") ?>" class="btn btn-sm btn-outline-primary"><i class="bi bi-pencil-square" title="Editar <?= $item->nombreeditorial ?>"></i></a>
                          <button class="btn btn-sm btn-outline-danger btn-borrar" data-id="<?= $item->id ?>" data-nombre="<?= $item->nombreeditorial ?>" data-tipo="editoriales"><i class="bi bi-trash" title="Borrar <?= $item->nombreeditorial ?>"></i></button>
                        </td>

                      <?php elseif ($tipo === 'generos'): ?>
                        <td><?= esc($item->genero) ?></td>
                        <td><?= esc($item->descripcion) ?></td>
                        <td width="90" align="center">
                          <a href="<?= base_url("editar/genero/{$item->id}") ?>" class="btn btn-sm btn-outline-primary"><i class="bi bi-pencil-square" title="Editar <?= $item->genero ?>"></i></a>
                          <button class="btn btn-sm btn-outline-danger btn-borrar" data-id="<?= $item->id ?>" data-nombre="<?= $item->genero ?>" data-tipo="generos"><i class="bi bi-trash" title="Borrar <?= $item->genero ?>"></i></button>
                        </td>

                      <?php elseif ($tipo === 'libros'): ?>
                        <td><?= esc($item->titulo) ?></td>
                        <td><?= esc($autoresMap[$item->autor] ?? 'Desconocido') ?></td>
                        <td><?= esc($generosMap[$item->genero] ?? '') ?></td>
                        <td><?= esc($editorialesMap[$item->editorial] ?? 'Desconocido') ?></td>
                        <td align="center"><span class="badge <?= $item->prestamo ? 'bg-warning text-dark' : 'bg-success' ?>">
                            <?= $item->prestamo ? 'En préstamo' : 'En casa' ?>
                          </span></td>
                        <td width="130" align="center">
                          <a href="<?= base_url("editar/libro/{$item->id}") ?>" class="btn btn-sm btn-outline-primary"><i class="bi bi-pencil" title="Editar <?= $item->titulo ?>"></i></a>
                          <?php if (!$item->prestamo): ?>
                            <button class="btn btn-outline-success btn-sm"
                              data-bs-toggle="modal" data-bs-target="#modalPrestamo"
                              data-id="<?= $item->id ?>"
                              data-titulo="<?= esc($item->titulo) ?>">
                              <i class="bi bi-arrow-right-circle" title="Gestionar préstamo libro <?= $item->titulo ?>"></i>
                            </button>
                          <?php elseif ($item->prestamo): ?>
                            <button class="btn btn-outline-warning btn-sm"
                              data-bs-toggle="modal" data-bs-target="#modalPrestamo"
                              data-id="<?= $item->id ?>"
                              data-titulo="<?= esc($item->titulo) ?>">
                              <i class="bi bi-arrow-left-circle" title="Gestionar devolución libro <?= $item->titulo ?>"></i>
                            </button>
                          <?php endif; ?>
                          <button class="btn btn-sm btn-outline-danger btn-borrar" data-id="<?= $item->id ?>" data-nombre="<?= esc($item->titulo) ?>" data-tipo="libros"><i class="bi bi-trash"  title="Eliminar libro <?= $item->titulo ?>"></i></button>
                        </td>

                      <?php elseif ($tipo === 'prestamos'): ?>
                        <td><?= $item->id ?></td>
                        <td><?= $item->id_libro ?></td>
                        <td><?= esc($item->usuario) ?></td>
                        <td><?= esc($item->isbn) ?></td>
                        <td><?= esc($item->titulo_libro) ?></td>
                        <td><?= date('d-m-Y', strtotime($item->fecha_salida)) ?></td>
                        <td align="center"><span class="badge <?= $item->devuelto ? 'bg-success' : 'bg-warning text-dark' ?>">
                            <?= $item->devuelto ? 'Devuelto' : 'En préstamo' ?>
                          </span></td>
                        <td align="center">
                          <?php if ($item->devuelto): ?>
                            <button class="btn btn-outline-success btn-sm"
                              data-bs-toggle="modal" data-bs-target="#modalPrestamo"
                              data-id="<?= $item->id_libro ?>"
                              data-titulo="<?= esc($item->titulo_libro) ?>">
                              <i class="bi bi-arrow-right-circle"></i>
                            </button>
                          <?php elseif (!$item->devuelto): ?>
                            <button class="btn btn-outline-warning btn-sm"
                              data-bs-toggle="modal" data-bs-target="#modalPrestamo"
                              data-id="<?= $item->id_libro ?>"
                              data-titulo="<?= esc($item->titulo_libro) ?>">
                              <i class="bi bi-arrow-left-circle" title="Gestionar devolución libro"></i>
                            </button>
                          <?php endif; ?>
                        </td>
                      <?php endif; ?>
                    </tr>
                  <?php endforeach; ?>
                </tbody>
              </table>
            </div>
          <?php else: ?>
            <div class="alert alert-warning">No hay elementos para mostrar.</div>
          <?php endif; ?>
        </div>
      </div>
    </div>
  </div>
</div>