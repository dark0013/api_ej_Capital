<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Autorizacion extends Model
{
    use HasFactory;
    protected $table = 'tbl_adm_Autorizacion';
    public  $timestamps = false;
   //protected $primarykey = 'id_prueba';
   //protected $autoincrementing = false;
   //protected $connection = 'mysql, etc..';
   //protected $hidden = array('id_estado','folio','id');


}
