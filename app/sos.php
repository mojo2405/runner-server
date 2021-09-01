<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\users;

class sos extends Model
{
    protected $table = 'sos';

    protected $fillable = ['GUID','latitude','longitude','viewed'];

    public function user()
    {
        return $this->hasOne('App\users');
    }
}
