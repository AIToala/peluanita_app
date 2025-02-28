<?php

namespace App\Http\Controllers\Utils;

interface HasNconn {
    public function setNconn($nconn);
    public function getNconn();
}
