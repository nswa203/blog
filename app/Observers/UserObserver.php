<?php

namespace App\Observers;

use App\User;

// This Observer is stored in App\Observers and loaded from 
// App\Providers\ObserverServiceProvider with "User::observe(UserObserver::class);"
class UserObserver
{
    /**
     * Listen to the User creating event.
     *
     * @param  \App\User  $user
     * @return void
     */
    public function creating(User $user)
    {
        $user->api_token = bin2hex(openssl_random_pseudo_bytes(30));
    }
}
