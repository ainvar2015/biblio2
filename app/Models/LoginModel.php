<?php

namespace App\Models;

use CodeIgniter\Model;

class LoginModel extends Model
{
    protected $table      = 'usuarios';
    protected $primaryKey = 'id';

    protected $returnType = 'object';

    protected $allowedFields = [
        'login',
        'email',
        'password',
        'foto',
        'activo',
        'key',
        'nombre',
        'apellidos',
        'descripcion',
        'direccion',
        'poblacion',
        'provincia',
        'c_postal',
        'telefono',
        'email',
        'mostrar_email',
        'mostrar_nombre_real',
        'permitir_contacto',
        'nivel',
        'fecha_alta',
        'fecha_ultima_modificacion'
    ];

    /**
     * Login con compatibilidad SHA1 → password_hash
     */
    public function getUserByLogin(string $login, string $password): ?object
    {
        $user = $this->where('login', $login)
                     ->where('activo', 1)
                     ->first();

        if (!$user) {
            return null;
        }

        // ✅ Caso moderno (password_hash)
        if (password_verify($password, $user->password)) {
            return $user;
        }

        // ⚠️ Caso legacy SHA1 (esto es para compatibilidad, se re-hash automáticamente al loguear, se puede eliminar esta parte en el futuro)
        if ($user->password === sha1($password)) {

            // 🔄 Re-hash automático
            $this->update($user->id, [
                'password' => password_hash($password, PASSWORD_DEFAULT),
            ]);

            return $user;
        }

        return null;
    }

    /**
     * Crear usuario nuevo (ya con password_hash)
     */
    public function crearUsuario(array $data): bool
    {
        return $this->insert([
            'login'     => $data['login'],
            'email'     => $data['email'],
            'password'  => password_hash($data['password'], PASSWORD_DEFAULT),
            'foto'      => $data['foto'] ?? '',
            'nivel'     => $data['nivel'] ?? 'inv',
            'activo'    => $data['activo'] ?? 1,
            'nombre'    => $data['nombre'],
            'apellidos' => $data['apellidos'] ?? '',
            'descripcion' => $data['descripcion'] ?? '',
            'direccion' => $data['direccion'] ?? '',
            'poblacion' => $data['poblacion'] ?? '',
            'provincia' => $data['provincia'] ?? '',
            'c_postal' => $data['c_postal'] ?? '',
            'telefono'  => $data['telefono'] ?? '',
            'mostrar_email' => $data['mostrar_email'] ?? 0,
            'mostrar_nombre_real' => $data['mostrar_nombre_real'] ?? 0,
            'permitir_contacto' => $data['permitir_contacto'] ?? 0,
            'fecha_alta' => date('Y-m-d H:i:s'),
            'fecha_ultima_modificacion' => date('Y-m-d H:i:s'),
        ]);
    }
}
