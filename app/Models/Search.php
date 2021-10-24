<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Item
 * @package App\Models
 *
 * @property string $keyword
 * @property string $country
 * @property string $device
 * @property json $search_results
 */
class Search extends Model
{
    use HasFactory;



    public $table = 'searches';

    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';

    const DESKTOP = 'desktop';
    const MOBILE = 'mobile';


    public $fillable = [
        'keyword',
        'country',
        'device' ,
        'user_id',
        'status'
        ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'keyword' => 'string',
        'country' => 'string',
        'device' => 'string',
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     **/
    public function iterations(){
        return $this->hasMany(\App\Models\SearchIteration::class, 'search_id');
    }
}
