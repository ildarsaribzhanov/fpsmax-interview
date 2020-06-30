<?php

namespace App\Models;

use DateTime;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Match
 *
 * @package App\Models
 * @property int            $id
 * @property int            $ext_id
 * @property int            $league_id
 * @property DateTime       $begin_at
 * @property string         $name
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 */
class Match extends Model
{
    protected $table = 'matches';

    protected $fillable = [
        'ext_id',
        'league_id',
        'begin_at',
        'name',
    ];
}
