<?php namespace App\myLibs;

use Image; 
use Purifier;

class EXIFTagsReader {

    var $aExclude = [                     // Exclude these EXIF tags
        'DataDump',
        'Undefined*',
        'ModeArray',
        'ImageInfo'       
    ];                                             

    // UTF8 encode an array
    // $encoded_array = array_map('utf8_encode_array', $your_array);
    function utf8_encode_deep(&$input) {
        if (is_string($input)) {
            $input = utf8_encode($input);
        } else if (is_array($input)) {
            foreach ($input as &$value) {
                $this->utf8_encode_deep($value);
            }
            unset($value);
        } else if (is_object($input)) {
            $vars = array_keys(get_object_vars($input));
                foreach ($vars as $var) {
                $this->utf8_encode_deep($input->$var);
            }
        }
    }

    function match_wildcard($needle, $haystack) {
       $regex = str_replace(
            ["\*", "\?"],       // wildcard chars
            ['.*','.'],         // regexp chars
            preg_quote($needle)
        );
       return preg_match('/^' . $regex . '$/is', $haystack);
    }

    function getTags($path, $search=[]) {
        try { 
            $exif = Image::make($path)->exif()?:[];                 // Extract any EXIF tags from the file
            $this->utf8_encode_deep($exif);                         // UTF8 encode it 
            $exif = Purifier::clean($exif);                         // Secure it
            foreach ($exif as $key => $val) {
                foreach ($this->aExclude as $aExcludeKey) {         // Unset Excluded items
                    if ($this->match_wildcard($aExcludeKey, $key)) {
                        unset($exif[$key]);
                        continue 2;
                    }
                }
                if (is_array($val)) {                               // Flatten array items
                    foreach ($val as $k => $v) {
                        $exif[$key.'_'.$k] = $v;
                    }
                    unset($exif[$key]);
                } elseif ($key == 'DateTime') {
                    $exif['TakenAt'] = date('Y-m-d H:i:s', strtotime($val));
                }
            }
        } catch (\Exception $e) { return null; }    
        $this->exif = $exif;
        //dd($exif);
        
        try {
            $iptc = Image::make($path)->iptc()?:[];                 // Extract any IPTC tags from the file
            $this->utf8_encode_deep($iptc);                         // UTF8 encode it 
            $iptc = Purifier::clean($iptc);                         // Secure it
            foreach ($iptc as $key => $val) {
                foreach ($this->aExclude as $aExcludeKey) {         // Unset Excluded items
                    if ($this->match_wildcard($aExcludeKey, $key)) {
                        unset($iptc[$key]);
                        continue 2;
                    }
                }
                if (is_array($val)) {                               // Flatten array items
                    foreach ($val as $k => $v) {
                        $iptc[$key.'_'.$k] = $v;
                    }
                    unset($iptc[$key]);
                } elseif ($key == 'DateTime') {
                    $iptc['TakenAt'] = date('Y-m-d H:i:s', strtotime($val));
                }
            }
        } catch (\Exception $e) { return null; }    
        $this->iptc = $iptc;
        //dd($iptc);

        return json_encode(array_merge($exif, $iptc));
    }

}
