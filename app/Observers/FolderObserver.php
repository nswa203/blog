<?php

namespace App\Observers;

use Illuminate\Http\Request;
use App\Folder;

// This Observer is stored in App\Observers and loaded from 
// App\Providers\ObserverServiceProvider with "Folder::observe(FolderObserver::class);"
// Possible Events: retrieved, creating, created, updating, updated, saving, saved, deleting, deleted, restoring, restored
class FolderObserver
{

    protected $request;
    public function __construct(Request $request) {
        $this->request = $request;
    }
    
       public function saving(Folder $folder)
    {
        unset($folder->path); 
        $folder->size = folderSize(folderPath($folder));
    }

}
