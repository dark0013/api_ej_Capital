<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FondoFinancieroModel extends Model
{
    use HasFactory;
    protected $table = 'tbl_adm_fondo_financiero';
    public  $timestamps = false;
}
