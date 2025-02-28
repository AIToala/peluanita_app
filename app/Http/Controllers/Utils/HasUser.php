<?php

namespace App\Http\Controllers\Utils;
use App\Models\User;
use Illuminate\Auth\Authenticatable;

interface HasUser {

    public function setUser($user);
    public function getUser(): Authenticatable|User;
}
