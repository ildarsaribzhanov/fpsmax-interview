<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class League
 *
 * @package App\Models
 *
 * @property int            $id
 * @property int            $ext_id
 * @property string         $name
 * @property string         $url
 * @property string         $image
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 */
class League extends Model
{
    protected $table = 'leagues';

    protected $fillable = [
        'ext_id',
        'name',
        'url',
        'image',
    ];
}
