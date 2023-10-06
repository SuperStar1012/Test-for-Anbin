<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WelcomeModel extends Model
{
    use HasFactory;
    protected $table = "files";
    protected $fillable = [
        'name',
        'file_path',
        'status'
    ];
}
