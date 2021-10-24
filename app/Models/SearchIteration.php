<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SearchIteration extends Model
{
    use HasFactory;

    public $fillable = [
        'search_id',
        'task_id',
        'search_results' ,
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     **/
    public function parent(){
        return $this->belongsTo(\App\Models\Search::class, 'search_id');
    }

}
