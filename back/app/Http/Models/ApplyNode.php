<?php

namespace App\Http\Models;


class ApplyNode extends Base
{
    public $timestamps = false;
    protected $table = 'apply_node';

    public function user()
    {
        return $this->belongsTo('App\Http\Models\Web\User','user_id')->select('id','username','mobile','avatar','pid');
    }
}
