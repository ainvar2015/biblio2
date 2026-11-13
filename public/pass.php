<?php
$password = '110657mrf';
echo $password.': '.sha1($password).'<br>';
$password = '110657';
echo $password.': '.sha1($password).'<br>';
$password = '110657Rf';
echo $password.': '.sha1($password).'<br>';
$password = 'admin';
echo $password.': '.sha1($password).'<br>';
$password = '1234567';
echo $password.': '.sha1($password).'<br>';
$password = '123456';
echo $password.': '.sha1($password).'<br>';
$password = 'diametro256';
echo $password.': '.sha1($password).'<br>';
$password = 'admin';
echo $password.': '.sha1($password).'<br>';

echo '<p></p>';
$password = 'ATereaSTio'; //admin en Ingeluxgalicia
echo $password.': '.sha1($password).'<br>';
$password = 'admin';
echo $password.': '.password_hash($password, PASSWORD_DEFAULT).'<br>';

$password = 'lugo1969';
echo $password.': '.password_hash($password, PASSWORD_DEFAULT).'<br><br>';

$password = 'admin123';
echo $password.': '.password_hash($password, PASSWORD_DEFAULT).'<br>';

?>