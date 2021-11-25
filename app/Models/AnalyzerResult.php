<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AnalyzerResult extends Model
{
    use HasFactory;

    public static $analyzers = [
      'TEXTRAZOR'
    ];

    public static $TEXT_RAZOR = 'TEXTRAZOR';
    public static $WATSON = 'WATSON';

    public $table = 'analyzer_results';

    public $fillable = [
        'url',
        'analyzer',
        'results' ,
        'html',
        'default'
    ];

    public $casts = [
        'url' => 'string',
        'analyzer' => 'string',
        'results' => 'json',
        'html' => 'string'
    ];

}
