public function addPrestamo()
    {
        $id_libro  = $this->request->getPost('id_libro');
        $fecha_salida = $this->request->getPost('fecha_salida'); // ejemplo
        $id_usuario = $this->request->getPost('id_usuario');

        // Iniciamos transacción
        $this->db->transStart();

        // Insertamos préstamo
        $this->db->table('prestamos')->insert([
            'id_libro'       => $id_libro,
            'id_usuario'     => $id_usuario,
            'fecha_salida' => $fecha_salida,
            'devuelto'       => 0
        ]);

        // Actualizamos el libro como prestado
        $this->db->table('libros')
            ->where('id', $id_libro)
            ->update(['prestamo' => '1']);

        // Finalizamos transacción
        $this->db->transComplete();

        return $this->db->transStatus(); // true si todo salió bien
    }
