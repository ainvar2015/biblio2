<?php namespace App\Models;

use CodeIgniter\Model;

class LoginModel extends Model
{
    protected $table = 'usuarios';
    protected $primaryKey = 'id'; // Ajusta según tu tabla
    protected $allowedFields = ['login','password','validkey','empresa','fecha_alta','activo','promotora','nivel','anuncios'];

    public function __construct()
    {
        parent::__construct();
        helper('date');
    }

    public function mkPasswd()
    {
        $consts = 'bcdgklmnprstuvxyz';
        $vowels = 'aeiou';
        $nums = '123456789';
        $simbols = '@#$%&';

        $const = [];
        $vow = [];
        $num = [];
        $sym = [];

        for ($x = 0; $x < 6; $x++) {
            mt_srand((double) microtime() * 1000000);
            $const[$x] = substr($consts, mt_rand(0, strlen($consts) - 1), 1);
            $vow[$x]   = substr($vowels, mt_rand(0, strlen($vowels) - 1), 1);
            $num[$x]   = substr($nums, mt_rand(0, strlen($nums) - 1), 1);
            $sym[$x]   = substr($simbols, mt_rand(0, strlen($simbols) - 1), 1);
        }

        return $const[0] . $vow[0] . $num[2] . $const[1] . $vow[1] . $const[3] . $sym[5] . $num[3] . $const[4];
    }

    public function crearKey()
    {
        $fecha = round(time() / 1000);
        srand($fecha);
        $validkey = rand(1, 100000000);
        return md5($validkey);
    }

    public function login_user($login, $password)
    {
        $db = \Config\Database::connect();
        $builder = $db->table('usuarios');
        $builder->where('login', $login);
        $builder->where('password', $password);
        $query = $builder->get();

        if ($query->getNumRows() == 1) {
            return $query->getRow();
        } else {
            session()->setFlashdata('usuario_incorrecto', '<p class="error center"><i class="fa fa-warning"></i>Los datos introducidos son incorrectos</p>');
            return redirect()->to(base_url('login'));
        }
    }

    public function nuevo_registro()
    {
        $request = service('request');
        $data = $request->getPost();
        $data['validkey'] = $this->crearKey();
        $passw_sin = $this->mkPasswd();
        $data['password'] = sha1($passw_sin);
        $data['login'] = $request->getPost('email');
        $data['empresa'] = 'NO';
        $data['fecha_alta'] = now();
        $data['activo'] = 'NO';
        $data['promotora'] = 'NO';
        $data['nivel'] = 'usu';
        $data['anuncios'] = 0;

        $db = \Config\Database::connect();
        $db->table('usuarios')->insert($data);

        return ['password' => $passw_sin, 'validKey' => $data['validkey']];
    }
}
