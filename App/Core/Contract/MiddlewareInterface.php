<?php
namespace App\Core\Contract;

use App\Core\Http\Request;
use App\Core\Mvc;

interface MiddlewareInterface{

    public function exec(Request $request);

}