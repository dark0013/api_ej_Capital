<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Contracts\Auth\Authenticatable;
use Laravel\Sanctum\HasApiTokens;

class Usuarios extends Model implements Authenticatable
{
    use HasFactory,HasApiTokens;
   // use HasApiTokens;

    protected $table = 'tbl_adm_usuarios';
    public $timestamps = false;

    // Métodos requeridos para la interfaz Authenticatable
    public function getAuthIdentifierName()
    {
        return 'cedula'; // Cambia esto según el campo que actúe como identificador en tu tabla
    }

    public function getAuthIdentifier()
    {
        return $this->getAttribute($this->getAuthIdentifierName());
    }

    public function getAuthPassword()
    {
        return $this->contrasenia; // Cambia esto según el campo de contraseña en tu tabla
    }
    
    // Métodos requeridos para la funcionalidad "Remember Me"
    public function getRememberToken()
    {
        return $this->remember_token;
    }

    public function setRememberToken($value)
    {
        $this->remember_token = $value;
    }

    public function getRememberTokenName()
    {
        return 'remember_token';
    }
}
