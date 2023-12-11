<?php

namespace App\Traits;

use App\Models\File;
use Illuminate\Http\Request;

trait FileCheckTrait
{

    public function check($ids){
        $lockedFiles = File::whereIn('id', $ids)->where('status', 1)->get();
        if ($lockedFiles->isNotEmpty()){
            return false;
        }else{
            return true;
        }
    }

}
