<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\File;
use App\Observers\FileObserver;
use App\Folder;
use App\Observers\FolderObserver;  
use App\User;
use App\Observers\UserObserver;

class ObserverServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        User::observe(UserObserver::class);
        Folder::observe(FolderObserver::class);
        File::observe(FileObserver::class);        
    }

    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}
