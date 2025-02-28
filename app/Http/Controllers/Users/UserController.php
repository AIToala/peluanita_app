<?php

namespace App\Http\Controllers\Users;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Controllers\Auth\AuthDataController;
use App\Http\Requests\Users\IndexUserRequest;
use App\Utils\LogUtils;
use Inertia\Inertia;

class UserController extends AuthDataController
{
    public function index(IndexUserRequest $request)
    {

        try {
            $nconn = $this->getNconn();
            $user = $this->getUser();
            LogUtils:info("User: " . $user->name . " is trying to access the users list");
        } catch (\Throwable $th) {
            LogUtils::error($th);
            return response()->json(
                [
                    'message' => 'An error occurred while trying to access the users list',
                    'error' => $th->getMessage()
                ],
                500
            );
        }
    }
}
