<?php

namespace App\Listeners;

use App\User;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class AuthListener
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    public function login(Authenticatable $user, $remember)
    {
        if(!$user instanceof User) {
            $user = User::find($user->getAuthIdentifier());
        }
        $user->setAttribute('wstoken', uniqid($user->id, true));
        $user->save();
    }

    public function logout(Authenticatable $user)
    {
        if(!$user instanceof User) {
            $user = User::find($user->getAuthIdentifier());
        }
        $user->setAttribute('wstoken', null);
        $user->save();
    }
}
