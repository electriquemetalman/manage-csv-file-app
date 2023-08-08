<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class competition extends Model
{
    use HasFactory;
    protected $fillable = [
        'title',
        'litel_description',
        'long_description',
        'evaluation_text',
        'ref_file'
    ];
}
