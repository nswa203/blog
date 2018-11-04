<?php namespace App\myLibs;

class M4ATagsReader {
    var $aQTFF = [                                  // Common tags
        //'ilst' => 'Metadata Item List',
        'alb'  => 'Album',                          // Keep as Album           
        'ART'  => 'Performer',                      // Keep as Performer
        'cmt'  => 'Comments',
        'com'  => 'Composer',
        'cpy'  => 'Copyright',
        'day'  => 'Year',
        'des'  => 'Track Alt1',
        'enc'  => 'Encoded By',
        'gen'  => 'Genre',
        'grp'  => 'Grouping',
        'lyr'  => 'Lyrics',
        'nam'  => 'Title',                          // Keep as Title
        'nrt'  => 'Narrator',
        'pub'  => 'Publisher',
        'src'  => 'Source',
        'swf'  => 'Song Writer',
        'too'  => 'Encoder',
        'trk'  => 'Track Alt2',
        'wrt'  => 'Writer',
        'covr' => 'Picture',                        // Keep as Picture
        'trkn' => 'Track',                          // Keep as Track
    ];                                             

    function text_decode($string, $start=0) {
        $end = strpos($string, chr(0), $start);
        return[substr($string, $start, $end-$start), $end+1];
    }

    function format_period($seconds_input) {
        $hours = (int)($minutes = (int)($seconds = (int)($milliseconds = (int)($seconds_input * 1000)) / 1000) / 60) / 60;
        return intval($hours).':'.($minutes%60).':'.($seconds%60).(($milliseconds===0)?'':'.'.rtrim($milliseconds%1000, '0'));
    }

    function getTags($path, $search=[]) {
        if (count($search) == 0) { $search = $this->aQTFF; }
        $file = fopen($path, 'rb');     
        $data = fread($file, 16);                   // Read header & check if any M4A
        
        $tags['FileName'] = $path;
        $tags['Search'  ] = $search;
        $tags['Count'   ] = 0; 
        $tags['Header'  ] = bin2hex($data);            
        if (substr($data,8, 3) != 'M4A') {          // LEAVE - Bad Header
            fclose($file);
            return false;                                   
        }

        $tSize = hexdec(bin2hex(substr($data, 6, 4)));
        $tSize = 900000;        
        rewind($file);
        $data  = fread($file, $tSize);              // Read header & all tags
        fclose($file);

        foreach ($search as $tag => $taglabel) {     // Check each of our "search" tags
            if (strlen($tag) == 3) { $tag = chr(169).$tag; }
            $taglabel = strtr($taglabel, ['(s)' => '']);
            $taglabel = strtr($taglabel, '/ ()', '____');
            $pos = strpos($data, $tag);
            if ($pos != false) {
//dd(substr($data,$pos, 20000));
                $tSize                = hexdec(bin2hex(substr($data, $pos+4, 4)))-16;
                $tags[$tag]['ID'    ] = $tag;
                $tags[$tag]['Header'] = bin2hex(substr($data, $pos-4, 24));
                if (substr($data, $pos+8, 4) == 'data') {
                    $tags[$tag]['Size'] = $tSize;
                    if ($taglabel == 'Track') {               // Special processing for numeric data
                        $tags[$tag]['Data'] = (string) hexdec(bin2hex(substr($data, $pos+20, 4)));
                    } else {
                        $tags[$tag]['Data'] = substr($data, $pos+20, $tSize);
                    }    
                    $id3[$taglabel]     = $tags[$tag]['Data'];
                    $tags['Count']++;
                    if ($tag == 'covr') {           // Special processing for embeded pictures
                        $pStart = strpos($data, hex2bin('FFD8FF'), $pos);
                        $pEnd   = strpos($data, hex2bin('FFD9'),   $pos)+2;                       
                        $tags[$tag]['PicStart' ] = $pStart;
                        $tags[$tag]['PicEnd'   ] = $pEnd; 
                        //dd(bin2hex(substr($data, $pStart, $pEnd-$pStart)));
                        $id3[$taglabel] = base64_encode(substr($data, $pStart, $pEnd-$pStart));
                        // $file->picture = $tags['Picture'];
                        // Then in HTML: src="data:image/jpeg;base64, {{ $file->picture }}"
                    }
                    $this->$taglabel = $id3[$taglabel];
                } else {
                    $tags[$tag]['Error'] = 'Misaligned data!';
                }
            }        
        }
        $id3 = isset($id3) ? $id3 : [];
//dd($id3);        
        return json_encode($id3);
    }

}
