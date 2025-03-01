<?php

namespace App\Http\Controllers\Utils;

use App\Models\User;
use Illuminate\Auth\Authenticatable;

abstract class AuthDataController implements HasNconn, HasUser
{
    protected User | Authenticatable | null $user = null;

    private ?string $nconn = 'mysql';


    public function getNconn()
    {
        if (! $this->nconn && ! app()->runningInConsole()) {
            $this->setNconn(nconn());
        }

        return $this->nconn;
    }

    public function setNconn($nconn): void
    {
        $this->nconn = $nconn;
    }

    final public function getUserId()
    {
        return $this->getUser()->getKey();
    }

    public function getUser(): Authenticatable | User
    {
        if (! $this->user && ! app()->runningInConsole()) {
            $user = user();
            $this->setUser($user);
        }

        return $this->user;
    }

    public function setUser($user): void
    {
        $this->user = $user;
    }
}
