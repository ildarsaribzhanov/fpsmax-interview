<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class Team
 *
 * @package App\Models
 *
 * @property int            $id
 * @property int            $ext_id
 * @property string         $name
 * @property string         $image
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 */
class Team extends Model
{
    protected $table = 'teams';

    protected $fillable = [
        'ext_id',
        'name',
        'image',
    ];
}
