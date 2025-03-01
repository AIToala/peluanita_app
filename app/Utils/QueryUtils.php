<?php
namespace App\Utils;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;
use App\Models\Auth\PersonalAccessToken;

class QueryUtils {
    /**
     * @return User
     */
    public static function getCurrentUser(): User {
        $request = request();
        $user = $request->user();
        $exp = PersonalAccessToken::getToken($request->bearerToken())->expires_at;
        return Cache::remember("auth-user:$user->id", Carbon::parse($exp), function() use($user){
            return $user;
        });
    }

    /**
     * Return the given user based on the Id that is provided
     * @return \App\Models\User
     */
    public static function getUserById($id): User {
        return Cache::remember("auth-user:$id", now()->addHours(10), function() use ($id){
            return User::query()->find($id);
        });
    }

    /**
     * Return the given user based on the username that is provided
     * @return \App\Models\User
     */
    public static function getUserByUsername(string $username): User {
        $fusername = strtolower($username);
        return Cache::remember("auth-user:{$username}", now()->addHours(10), function() use ($fusername){
            return User::query()->where('username', $fusername)->first();
        });
    }

}
