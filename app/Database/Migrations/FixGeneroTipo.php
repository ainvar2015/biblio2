<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class FixGeneroTipo extends Migration
{
    public function up()
    {
        // Paso 1: Crear tabla temporal con los géneros textuales únicos
        $generosTexto = $this->db->query("
            SELECT DISTINCT TRIM(genero) as genero_texto 
            FROM biblio2_libros 
            WHERE genero IS NOT NULL 
              AND genero != '' 
              AND genero REGEXP '^[0-9]+$' = 0
        ")->getResultArray();

        // Paso 2: Para cada género textual, buscar o crear su equivalente numérico en biblio2_generos
        foreach ($generosTexto as $gt) {
            $texto = trim($gt['genero_texto']);
            
            // Buscar si ya existe un género con ese nombre
            $existe = $this->db->table('biblio2_generos')
                ->where('genero', $texto)
                ->get()
                ->getRow();

            if ($existe) {
                // Actualizar libros con ese texto al ID numérico
                $this->db->query("
                    UPDATE biblio2_libros 
                    SET genero = ? 
                    WHERE TRIM(genero) = ? 
                      AND genero REGEXP '^[0-9]+$' = 0
                ", [$existe->id, $texto]);
            } else {
                // Crear nuevo género y obtener su ID
                $this->db->table('biblio2_generos')->insert(['genero' => $texto]);
                $nuevoId = $this->db->insertID();
                
                $this->db->query("
                    UPDATE biblio2_libros 
                    SET genero = ? 
                    WHERE TRIM(genero) = ? 
                      AND genero REGEXP '^[0-9]+$' = 0
                ", [$nuevoId, $texto]);
            }
        }

        // Paso 3: Limpiar registros que no se pudieron convertir (poner NULL)
        $this->db->query("
            UPDATE biblio2_libros 
            SET genero = NULL 
            WHERE genero IS NOT NULL 
              AND genero != '' 
              AND genero REGEXP '^[0-9]+$' = 0
        ");

        // Paso 4: Convertir los que ya eran numéricos pero en formato string
        $this->db->query("
            UPDATE biblio2_libros 
            SET genero = CAST(genero AS UNSIGNED) 
            WHERE genero IS NOT NULL AND genero != ''
        ");

        // Paso 5: Cambiar tipo de columna a INT
        $this->forge->modifyColumn('biblio2_libros', [
            'genero' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => true,
            ]
        ]);
    }

    public function down()
    {
        // Volver a VARCHAR (pierdes la relación numérica, los datos quedan como strings de números)
        $this->forge->modifyColumn('biblio2_libros', [
            'genero' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => true,
            ]
        ]);
    }
}