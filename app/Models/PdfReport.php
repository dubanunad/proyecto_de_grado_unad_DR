<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PdfReport extends Model
{
    use HasFactory;
    protected $fillable = ['branch_id', 'pdf_path'];
}
