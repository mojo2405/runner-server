<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\location;

class users extends Model
{
    protected $table = 'users';

    protected $fillable = ['ID','phone','GUID','runner_number'];

    public function locations()
    {
        return $this->hasMany("App\location" , "GUID","GUID");
    }
}
