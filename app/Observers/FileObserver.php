<?php

namespace App\Observers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\File as FileSys;
use App\File;
use App\Folder;

// This Observer is stored in App\Observers and loaded from 
// App\Providers\ObserverServiceProvider with "File::observe(FileObserver::class);"
// Possible Events: retrieved, creating, created, updating, updated, saving, saved, deleting, deleted, restoring, restored
class FileObserver
{
    protected $request;
    public function __construct(Request $request) {
        $this->request = $request;
    }

    // Forces Folder observer to update folder size     
    function updateFolder(File $file, $folder=false) {
        if (gettype($folder) == 'integer') { echo '!1!'; $folder = Folder::find($folder);          }
        if (! $folder)                     { echo '!2!'; $folder = Folder::find($file->folder_id); }
        
        $myrc = $folder->save(); 
        return $myrc;
    }

    // Checks if enough space on folder for this file   
    function fitOnFolder(File $file, Folder $folder) {
        if ($folder->size + $file->size > $folder->max_size * 1048576) {
            $avail = $folder->max_size * 1048576 - $folder->size;
            msgx(['warning' => ['Insufficient space in "' . $folder->slug . '" ' . mySize($avail) .' available.']]);
            return false;
        }
        return true;
    }

    // Returns a filepath if not already used   
    function getUnique(File $file, Folder $folder) {
        $path = filePath($file, $folder);
        $myrc = FileSys::exists($path);

        if ($myrc) {
            msgx(['warning' => ['File already exists "' . $path . '".']]);
        } 
        return $myrc ? false : $path;
    }

    // This ensures that before the File row is saved in the database, we check for a collision
    // of the target path and that there is enough space. We then extract meta data from
    // the file, buid a security hash and move the physical File to its correct destination.
    // We synchronize any associations, such as "Tags", in the "saved" & "deleted" functions.
    // File->file must have the fn.ft 
    // This requires certain additional Request parameters...
    //  Request->action       be set to the request type (store, update, copy, move)
    //  Request->folderTarget be set to the target Folder:: 
    //  Request->fileSource   be set to the index in the uploaded files list OR path to the existing file
    // NS01
    public function saving(File $file) {
        $op = $this->request->action;
        
        if (! in_array($op, ['alter', 'update'])) {
            $folderTarget = $this->request->folderTarget;
            $pathTarget   = $this->getUnique($file, $folderTarget);

            if (! $pathTarget)                              { return false; }   // Already a file with that name
            if (! $this->fitOnFolder($file, $folderTarget)) { return false; }   // No space
        }    

        if ($op == 'alter') {
            return true;                                                        // No Physical file changes so just return OK 
        } else if ($op == 'copy') {
            $fileSource = $this->request->fileSource;
            $myrc = FileSys::copy($fileSource, $pathTarget);                    // C:\...\fn.ft -> C:\...\fn.ft
        } else if ($op == 'move') {                                                                     
            $fileSource = $this->request->fileSource;
            $myrc = FileSys::move($fileSource, $pathTarget);                    // C:\...\fn.ft -> C:\...\fn.ft
        } else if ($op == 'store') {
            $fileSource   = $this->request->file('files')[$this->request->fileSource];
            // Retrieve information about the uploaded file  
            $file->mime_type = $fileSource->getMimeType();
            $file->meta      = getMeta($fileSource);                            // Look in Helpers.php
            $file->sha256    = hash_file('sha256', $fileSource);
            // Move from temporary storage to target
            $myrc = FileSys::move($fileSource, $pathTarget);
        } else if ($op == 'replace') {
            $fileSource   = $this->request->file('files')[$this->request->fileSource];
            $folderTarget = $this->request->folderTarget;
            $pathTarget   = $this->getUnique($file, $folderTarget); 
            $folderTarget->size = $folderTarget->size - $file->size;
            if (! $pathTarget)                              { return false; }    // Already a file with that name
            if (! $this->fitOnFolder($file, $folderTarget)) { return false; }    // No space
            // Retrieve information about the uploaded file  
            $file->mime_type = $fileSource->getMimeType();
            $file->meta      = getMeta($fileSource);
            $file->sha256    = hash_file('sha256', $fileSource);
            // Erase old file
            $myrc = FileSys::delete($this->request->originalFilePath);
            // Move from temporary storage to target
            $myrc = FileSys::move($fileSource, $pathTarget);
        }  

        if ($myrc) {                            // NS01 folderObserver is slow to update so we do it here
            $folderTarget->size = $folderTarget->size + $file->size;        
            $folderTarget->save();
        }

    return $myrc;
    }    

    //  This ensures that once the File row has been saved in the database, we synchronize any
    //  Tags and force an update of the Folder->size.
    //  Request->folderSource is required for Move to ensure we update the source folder.
    public function saved(File $file) {
        $op = $this->request->action;
       
        if        ($op == 'store') {
            $myrc = $file->tags()->sync($this->request->tags, false);
        } else if ($op == 'alter') {
            if ($this->request->tags) {
                $myrc = $file->tags()->sync($this->request->tags, true );
            } else { $myrc = true; }                
        } else if ($op == 'copy') {
            $myrc = $file->tags()->sync($this->request->tags, false);
        } else if ($op == 'move') {
            $myrc = $this->updateFolder($file, $this->request->folderSource);
        } else if ($op == 'replace') {
            $myrc = $file->tags()->sync($this->request->tags, true);
        }

        //$myrc = $this->updateFolder($file, $this->request->folder);
        return $myrc;
    }

    // This ensures that once the File row has been deleted from the database, we detach any
    // Tags, erase the file from storage and force an update of the Folder->size.  
    public function deleted(File $file) {
        $myrc = false;
        $file->tags()->detach();
        $folder = Folder::find($file->folder_id);

        if ($folder) {
            $filePath = folder_path($folder)->path . '\\' . $file->file;    // C:\folder\fn.ft
            $myrc = FileSys::delete($filePath);
        }
        
        $myrc = $myrc ? $this->updateFolder($file, $folder) : false;
        return $myrc;
    }            

}
