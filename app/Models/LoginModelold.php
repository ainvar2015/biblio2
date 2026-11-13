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
        'nombre',
        'direccion',
        'poblacion',
        'provincia',
        'codpostal',
        'nivel',
        'validKey',
        'activo',
    ];

    /**
     * Obtiene un usuario activo por login
     */
    public function getUserByLogin(string $login)
    {
        return $this->where('login', $login)
                    ->where('activo', 1)
                    ->first();
    }

    /**
     * Comprueba credenciales (legacy SHA1)
     * 👉 luego lo cambiamos a password_verify
     */
    public function checkCredentials(string $login, string $password): ?object
    {
        return $this->where([
                        'login'    => $login,
                        'password' => sha1($password),
                        'activo'   => 1,
                    ])
                    ->first();
    }

    /**
     * Registro nuevo (legacy)
     */
    public function nuevoRegistro(array $data)
    {
        $passwordPlano = substr(md5(uniqid()), 0, 8);

        $insert = [
            'email'     => $data['email'],
            'password'  => sha1($passwordPlano), // legacy
            'nombre'    => $data['nombre'],
            'direccion' => $data['direccion'],
            'poblacion' => $data['poblacion'],
            'provincia' => $data['provincia'],
            'codpostal' => $data['codpostal'],
            'nivel'     => 'usu',
            'validKey'  => md5(uniqid(true)),
            'activo'    => 0,
        ];

        if ($this->insert($insert)) {
            return [
                'password' => $passwordPlano,
                'validKey' => $insert['validKey'],
            ];
        }

        return false;
    }
}
