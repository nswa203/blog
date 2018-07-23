<?php

namespace App\Observers;

use Illuminate\Http\Request;
use App\Folder;

// This Observer is stored in App\Observers and loaded from 
// App\Providers\ObserverServiceProvider with "Folder::observe(FolderObserver::class);"
class FolderObserver
{
    protected $request;
    public function __construct(Request $request) {
        $this->request = $request;
    }
    
    /**
     * Listen to the Folder creating event.
     *
     * @param  \App\Folder  $folder
     * @return void
     */
    public function creating(Folder $folder)
    {
        //dd('Folder: Creating', $folder, $this->request);
    }
    public function deleting(Folder $folder)
    {
        //dd('Folder: Deleting', $folder, $this->request);
    }
    public function deleted(Folder $folder)
    {
        //dd('Folder: Deleted', $folder, $this->request);
    }
}
