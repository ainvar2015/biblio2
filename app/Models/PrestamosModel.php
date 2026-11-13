<?php

namespace App\Models;

use CodeIgniter\Model;

class PrestamosModel extends Model
{
    protected $table = 'biblio2_prestamos';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'id_biblioteca',
        'tipo_prestatario',
        'id_libro',
        'id_usuario',
        'nombre_externo',
        'email_externo',
        'telefono_externo',
        'dni_externo',
        'usuario',
        'isbn',
        'titulo_libro',
        'fecha_prestamo',
        'fecha_devolucion',
        'fecha_entrada',
        'fecha_devolucion_real',
        'notas',
        'devuelto',
        'fecha_creacion'
    ];

    protected $useTimestamps = false;
    protected $dateFormat = 'datetime';

    // ============================================
    // DETALLE DE UN PRÉSTAMO (sin cambios)
    // ============================================
    function getPrestamosConDetalle($idPrestamo)
    {
        $builder = $this->db->table('biblio2_prestamos p')
            ->select('p.*,l.titulo, l.codigoISBN, l.portada, a.nombreautor as autor')
            ->join('biblio2_libros l', 'l.id = p.id_libro')
            ->join('biblio2_autores a', 'a.id = l.autor', 'left')
            ->where('p.id', $idPrestamo);
        return $builder->orderBy('p.fecha_devolucion', 'ASC')->get()->getResult();
    }

    // ============================================
    // PRÉSTAMOS ACTIVOS (con filtro de biblioteca)
    // ============================================
    public function getPrestamosActivos($filtros = [], ?int $id_biblioteca = null)
    {
        $builder = $this->db->table('biblio2_prestamos p')
            ->select('p.*, l.titulo as titulo_libro, l.codigoISBN, l.portada,
            GROUP_CONCAT(DISTINCT a.nombreautor ORDER BY a.nombreautor SEPARATOR ", ") as autores,
            CASE 
                WHEN p.tipo_prestatario = "socio" THEN u.nombre
                ELSE p.nombre_externo
            END as nombre_prestatario,
            CASE 
                WHEN p.tipo_prestatario = "socio" THEN u.email
                ELSE p.email_externo
            END as email_prestatario,
            CASE 
                WHEN p.tipo_prestatario = "socio" THEN u.telefono
                ELSE p.telefono_externo
            END as telefono_prestatario,
            DATEDIFF(CURDATE(), p.fecha_devolucion) as dias_retraso', false)
            ->join('biblio2_libros l', 'l.id = p.id_libro')
            ->join('biblio2_libros_autores la', 'la.id_libro = l.id', 'left')
            ->join('biblio2_autores a', 'a.id = la.id_autor', 'left')
            ->join('biblio2_usuarios u', 'u.id = p.id_usuario', 'left')
            ->groupStart()
            ->where('p.fecha_entrada IS NULL')
            ->orWhere('p.fecha_entrada', '0000-00-00')
            ->groupEnd();

        // Filtro por biblioteca
        if ($id_biblioteca !== null) {
            $builder->where('p.id_biblioteca', $id_biblioteca);
        }

        // Aplicar filtros adicionales
        if (!empty($filtros['buscar'])) {
            $builder->groupStart()
                ->like('l.titulo', $filtros['buscar'])
                ->orLike('l.codigoISBN', $filtros['buscar'])
                ->orLike('p.nombre_externo', $filtros['buscar'])
                ->orLike('u.nombre', $filtros['buscar'])
                ->groupEnd();
        }

        if (!empty($filtros['tipo'])) {
            $builder->where('p.tipo_prestatario', $filtros['tipo']);
        }

        if (!empty($filtros['retraso'])) {
            $builder->where('p.fecha_devolucion <', date('Y-m-d'));
        }

        // ✅ IMPORTANTE: GROUP BY para que GROUP_CONCAT funcione
        $builder->groupBy('p.id');

        return $builder->orderBy('p.fecha_devolucion', 'ASC')->get()->getResult();
    }

    // ============================================
    // HISTORIAL (con filtro de biblioteca)
    // ============================================
    public function getHistorialPrestamos(?int $id_biblioteca = null, $limit = 100)
    {
        $builder = $this->db->table('biblio2_prestamos p')
            ->select('p.*, l.titulo as titulo_libro, l.codigoISBN,
                                 CASE 
                                     WHEN p.tipo_prestatario = "socio" THEN u.nombre
                                     ELSE p.nombre_externo
                                 END as nombre_prestatario,
                                 DATEDIFF(p.fecha_devolucion, p.fecha_prestamo) as dias_totales', false)
            ->join('biblio2_libros l', 'l.id = p.id_libro')
            ->join('biblio2_usuarios u', 'u.id = p.id_usuario', 'left')
            ->where('p.fecha_entrada IS NOT NULL')
            ->where('p.fecha_entrada !=', '0000-00-00');

        if ($id_biblioteca !== null) {
            $builder->where('p.id_biblioteca', $id_biblioteca);
        }

        return $builder->orderBy('p.fecha_devolucion', 'DESC')
            ->limit($limit)
            ->get()
            ->getResult();
    }

    // ============================================
    // ESTADÍSTICAS (con filtro de biblioteca)
    // ============================================
    public function getEstadisticas(?int $id_biblioteca = null)
    {
        $stats = [];

        // Total activos
        $builder = $this->db->table('biblio2_prestamos')
            ->where('(fecha_entrada IS NULL OR fecha_entrada = "0000-00-00")');
        if ($id_biblioteca !== null) {
            $builder->where('id_biblioteca', $id_biblioteca);
        }
        $stats['activos'] = $builder->countAllResults();

        // En retraso
        $builder = $this->db->table('biblio2_prestamos')
            ->where('(fecha_entrada IS NULL OR fecha_entrada = "0000-00-00")')
            ->where('fecha_devolucion <', date('Y-m-d'));
        if ($id_biblioteca !== null) {
            $builder->where('id_biblioteca', $id_biblioteca);
        }
        $stats['retraso'] = $builder->countAllResults();

        // Devueltos este mes
        $builder = $this->db->table('biblio2_prestamos')
            ->where('fecha_devolucion >=', date('Y-m-01'));
        if ($id_biblioteca !== null) {
            $builder->where('id_biblioteca', $id_biblioteca);
        }
        $stats['devueltos_mes'] = $builder->countAllResults();

        // Préstamos por tipo
        $builder = $this->db->table('biblio2_prestamos')
            ->select('tipo_prestatario, COUNT(*) as total')
            ->where('(fecha_entrada IS NULL OR fecha_entrada = "0000-00-00")');
        if ($id_biblioteca !== null) {
            $builder->where('id_biblioteca', $id_biblioteca);
        }
        $stats['por_tipo'] = $builder->groupBy('tipo_prestatario')
            ->get()
            ->getResult();

        // Top libros más prestados
        $builder = $this->db->table('biblio2_prestamos p')
            ->select('l.titulo, l.id, COUNT(*) as total')
            ->join('biblio2_libros l', 'l.id = p.id_libro');
        if ($id_biblioteca !== null) {
            $builder->where('p.id_biblioteca', $id_biblioteca);
        }
        $stats['top_libros'] = $builder->groupBy('p.id_libro')
            ->orderBy('total', 'DESC')
            ->limit(5)
            ->get()
            ->getResult();

        return $stats;
    }

    // ============================================
    // REGISTRAR DEVOLUCIÓN (sin cambios)
    // ============================================
    public function registrarDevolucion($idPrestamo, $fechaEntrada = null)
    {
        $fechaEntrada = $fechaEntrada ?? date('Y-m-d');

        $this->db->transStart();

        $this->update($idPrestamo, [
            'fecha_devolucion' => $fechaEntrada
        ]);

        $prestamo = $this->find($idPrestamo);
        if ($prestamo) {
            $this->db->table('biblio2_libros')
                ->where('id', $prestamo['id_libro'])
                ->update(['prestado' => 0]);
        }

        $this->db->transComplete();

        return $this->db->transStatus();
    }

    // ============================================
    // PRÉSTAMOS VENCIDOS (para recordatorios)
    // ============================================
    public function getPrestamosVencidos(?int $id_biblioteca = null, int $dias = 0)
    {
        $fechaLimite = date('Y-m-d', strtotime("-{$dias} days"));

        $builder = $this->where('devuelto', 0)
            ->where('fecha_devolucion <', $fechaLimite);

        if ($id_biblioteca !== null) {
            $builder->where('id_biblioteca', $id_biblioteca);
        }

        return $builder->findAll();
    }
}
