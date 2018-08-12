<?php namespace App\myLibs;

// When using in, say a Controller ...
// use App\myLibs\ID3TagsReader as ID3Tags;
// $id3 = new ID3Tags;
// $id3->getTags(filePath($file));
class ID3TagsReader {

    var $aTV23 = [                     // Possible sys tags (ID3 v2.3)
        'AENC' => 'Audio encryption',
        'APIC' => 'Picture',
        'COMM' => 'Comments',
        'COMR' => 'Commercial frame',
        'ENCR' => 'Encryption method registration',
        'EQUA' => 'Equalization',
        'ETCO' => 'Event timing codes',
        'GEOB' => 'General encapsulated object',
        'GRID' => 'Group identification registration',
        'IPLS' => 'Involved people list',
        'LINK' => 'Linked information',
        'MCDI' => 'Music CD identifier',
        'MLLT' => 'MPEG location lookup table',
        'OWNE' => 'Ownership frame',
        'PRIV' => 'Private frame',
        'PCNT' => 'Play counter',
        'POPM' => 'Popularimeter',
        'POSS' => 'Position synchronisation frame',
        'RBUF' => 'Recommended buffer size',
        'RVAD' => 'Relative volume adjustment',
        'RVRB' => 'Reverb',
        'SYLT' => 'Synchronized lyric',
        'SYTC' => 'Synchronized tempo codes',
        'TALB' => 'Album',
        'TBPM' => 'BPM',
        'TCOM' => 'Composer',
        'TCON' => 'Content',
        'TCOP' => 'Copyright message',
        'TDAT' => 'Date',
        'TDLY' => 'Playlist delay',
        'TENC' => 'Encoded by',
        'TEXT' => 'Lyricist',
        'TFLT' => 'File type',
        'TIME' => 'Time',
        'TIT1' => 'Content group description',
        'TIT2' => 'Title',
        'TIT3' => 'Subtitle',
        'TKEY' => 'Initial key',
        'TLAN' => 'Language',
        'TLEN' => 'Length',
        'TMED' => 'Media type',
        'TOAL' => 'Original album',
        'TOFN' => 'Original filename',
        'TOLY' => 'Original lyricist',
        'TOPE' => 'Original artist',
        'TORY' => 'Original release year',
        'TOWN' => 'File owner',
        'TPE1' => 'Performer',
        'TPE2' => 'Band',
        'TPE3' => 'Conductor',
        'TPE4' => 'Interpreted by',
        'TPOS' => 'Part of a set',
        'TPUB' => 'Publisher',
        'TRCK' => 'Track',
        'TRDA' => 'Recording dates',
        'TRSN' => 'Internet radio station name',
        'TRSO' => 'Internet radio station owner',
        'TSIZ' => 'Size',
        'TSRC' => 'ISRC',
        'TSSE' => 'Software/Hardware settings used for encoding',
        'TYER' => 'Year',
        'TXXX' => 'User defined text information frame',
        'UFID' => 'Unique file identifier',
        'USER' => 'Terms of use',
        'USLT' => 'Unsychronized lyric',
        'WCOM' => 'Commercial information',
        'WCOP' => 'Copyright',
        'WOAF' => 'Official audio file webpage',
        'WOAR' => 'Official artist/performer webpage',
        'WOAS' => 'Official audio source webpage',
        'WORS' => 'Official internet radio station homepage',
        'WPAY' => 'Payment',
        'WPUB' => 'Publishers official webpage',
        'WXXX' => 'User defined URL link frame'
    ];                                             

    var $aItype = [                                 // Array of APIC Picture types
        'Other',
        '32x32 pixels file icon (PNG only)',
        'Other file icon',
        'Cover (front)',
        'Cover (back)',
        'Leaflet page',
        'Media (e.g. lable side of CD)',
        'Lead artist/lead performer/soloist',
        'Artist/performer',
        'Conductor',
        'Band/Orchestra',
        'Composer',
        'Lyricist/text writer',
        'Recording Location',
        'During recording',
        'During performance',
        'Movie/video screen capture',
        'A bright coloured fish',
        'Illustration',
        'Band/artist logotype',
        'Publisher/Studio logotype'
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
        if (count($search) == 0) { $search = $this->aTV23; }
        $file = fopen($path, 'rb');     
        $data = fread($file, 10);                   // Read header & check if any ID3
        $tags['FileName'] = $path;
        $tags['Search'  ] = $search;
        $tags['Count'   ] = 0; 
        $tags['Header'  ] = bin2hex($data);            
        if (substr($data, 0, 3) != 'ID3') {          // LEAVE - Bad Header
            fclose($file);
            return false;                                   
        }
        
        $tSize = hexdec(bin2hex(substr($data, 6, 4)));
        rewind($file);
        $data  = fread($file, $tSize+10);            // Read header & all tags
        fclose($file);
        $tags['Version' ] = hexdec(bin2hex(substr($data, 3, 1))) . '.' . hexdec(bin2hex(substr($data, 4, 1)));
        $tags['TagsSize'] = $tSize;

        foreach ($search as $tag => $taglabel) {     // Check each of our "search" tags
            $tag = strtoupper($tag);
            $taglabel = strtr($taglabel, ['(s)' => '']);
            $taglabel = strtr($taglabel, '/ ()', '____');

            $pos = strpos($data, $tag . chr(0));
            if ($pos != false) {
                $tSize                = hexdec(bin2hex(substr($data, $pos+5, 3)));
                $tags[$tag]['ID'    ] = substr($data, $pos, 4);
                $tags[$tag]['Header'] = bin2hex(substr($data, $pos, 10));
                if (substr($data, $pos+10, 1) == chr(0)) {
                    $tags[$tag]['Size'] = $tSize;
                    $tags[$tag]['Data'] = substr($data, $pos+11, $tSize-1);
                    $id3[$taglabel]     = $tags[$tag]['Data'];
                    $tags[$tag]['More'] = substr($data, $pos+11-1+$tSize, 4) == $tag ? 'Y' : 'N';
                    $tags['Count']++;
                    if ($tag == 'APIC') {           // Special processing for embeded pictures
                        $pos+10;
                        $tags[$tag]['Flag'     ] = bin2hex(substr($data, $pos+10, 1));
                        $r = $this->text_decode($data, $pos+11);
                        $tags[$tag]['MimeType' ] = $r[0];
                        $pos = $r[1];
                        $tags[$tag]['ImageType'] = $this->aItype[hexdec(bin2hex(substr($data, $pos, 1)))];
                        $pStart = strpos($data, hex2bin('FFD8FF'), $pos);
                        $pEnd   = strpos($data, hex2bin('FFD9'),   $pos)+2;                       
                        $tags[$tag]['PicStart' ] = $pStart;
                        $tags[$tag]['PicEnd'   ] = $pEnd; 
                        //dd(bin2hex(substr($data, $pStart, $pEnd-$pStart)));
                        $id3[$taglabel] = base64_encode(substr($data, $pStart, $pEnd-$pStart));
                        // $file->picture = $tags['Picture'];
                        // Then in HTML: src="data:image/jpeg;base64, {{ $file->picture }}"
                    } elseif ($tag == 'TLEN') {
                        $tags[$taglabel] = $this->format_period($tags[$tag]['Data']/1000);
                    }
                    $this->$taglabel = $id3[$taglabel];
                } else {
                    $tags[$tag]['Error'] = 'Misaligned data!';
                }
            }        
        }
        return json_encode($id3);
    }

}
