<!-- Modal para detalles del libro -->
    <div class="modal fade" id="modalDetalleLibro" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title"><i class="bi bi-book"></i> <span id="modalLibroTitulo">Detalles del libro</span></h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-4 text-center mia">
                            <img
                                id="modalLibroPortada"
                                src="<?= base_url('assets/img/no-portada.gif') ?>"
                                alt="Portada del libro"
                                class="img-fluid mb-3"
                                style="max-height: 300px;">
                        </div>
                        <div class="col-md-8">
                            <div class="table-responsive">
                                <table class="table table-sm table-borderless">
                                    <tbody>
                                        <tr>
                                            <th>Título:</th>
                                            <td id="modalLibroTituloDetalle"></td>
                                        </tr>
                                        <tr>
                                            <th>Autor:</th>
                                            <td id="modalLibroAutor"></td>
                                        </tr>
                                        <tr>
                                            <th>Editorial:</th>
                                            <td id="modalLibroEditorial"></td>
                                        </tr>
                                        <tr>
                                            <th>Género:</th>
                                            <td id="modalLibroGenero"></td>
                                        </tr>
                                        <tr>
                                            <th>Año de edición:</th>
                                            <td id="modalLibroAno"></td>
                                        </tr>
                                        <tr>
                                            <th>ISBN:</th>
                                            <td id="modalLibroIsbn"></td>
                                        </tr>
                                        <tr>
                                            <th>Número de páginas:</th>
                                            <td id="modalLibroPaginas"></td>
                                        </tr>
                                        <tr>
                                            <th>Préstamos totales:</th>
                                            <td id="modalLibroPrestamos"></td>
                                        </tr>
                                        <tr>
                                            <th>Estado:</th>
                                            <td id="modalLibroEstado"></td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <div>
                        <!-- Botón dinámico para prestar/devolver -->
                        <button
                            id="btnAccionLibro"
                            class="btn"
                            data-id-libro=""
                            data-estado="">
                            <i class="bi"></i> <span id="txtAccionLibro"></span>
                        </button>
                    </div>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                </div>
            </div>
        </div>
    </div>