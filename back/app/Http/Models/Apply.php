<?php

namespace App\Http\Models;


use App\Http\Models\Web\User;

class Apply extends Base
{
    public $timestamps = false;
    protected $table = 'apply';

    public function user()
    {
        return $this->belongsTo('App\Http\Models\Web\User','user_id')->select('id','username','mobile','avatar','pid');
    }

    public static function checkTen($user)
    {
        $result = false;
        $i = 0;

        if($user->attached)
        {
            return true;
        }elseif($user->pid)
        {
            $parent = User::find($user->pid);
        }else{
            return $result;
        }

        while ($i<10 && $parent)
        {
            if($parent->attached)
            {
                return true;
            }elseif($parent->level>=1)
            {
                return true;
            }else{
                $parent = User::find($parent->pid);
            }
            $i++;
        }
        return false;
    }
}
