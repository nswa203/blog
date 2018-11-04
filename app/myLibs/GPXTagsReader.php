<?php namespace App\myLibs;

use PHPCoord\OSRef;
use PHPCoord\LatLng;
use PHPCoord\RefEll;

// Recursively runs through an array structure to capture certain keys
function recursive($data, $keys=['trkpt', 'rtept', 'wpt'], $r=[]) {
    foreach ($data as $key => $value) {
        if (is_array($value)) {
            if (in_array($key, $keys)) {
                for ($i=0; $i<sizeof($value); ++$i) {
                    $value[$i]['type'] = $key;
                } 
                $r = array_merge($r, $value);
                continue;
            }
            $r = recursive($value, $keys, $r);
        }    
    }
    return $r;
}

// Rebuilds selected GPS points data into a 2-D array
// Populate each GPS location with Easting, Northing, OSRef, Elevation, delta Distance, delta Height
// Adds Start & Finish waypoints if no other waypoints present.  
function getPoints($data) {
    $tags = ['lat', 'lon', 'ele', 'time', 'type', 'name', 'image', 'link', 'east', 'north', 'osref', 'd', 'h'];
    $points = [];
    $distance = 0;
    $climb    = 0;
    $time     = false;
    $lowest   = false;
    $highest  = false;
    foreach ($data as $item) {
        $point = [];
        foreach ($tags as $tag) {
            $point[$tag] = false;
            if (isset($item['@attributes'])) {
                if (isset($item['@attributes'][$tag])) { 
                    $point[$tag] = $item['@attributes'][$tag];
                }
            }
            if (isset($item[$tag])) { $point[$tag] = $item[$tag]; }
        } 
        $type = $point['type'];
        $points[$type][] = $point;
    }
    foreach ($points as $type => $list) {
        for ($i=0; $i<sizeof($list); ++$i) {
            $point = $list[$i]; 
            $p1 = new LatLng($point['lat'], $point['lon'], 0, RefEll::wgs84());
            $p2 = $p1->toOSRef();
            $point['east' ] = $p2->getX();
            $point['north'] = $p2->getY();
            $point['osref'] = $p2->toSixFigureReference();
            $point['ele'  ] = getElevation([$p2->getX(), $p2->getY()], true, true);
            if ($i>0) {
                $point['d'] = $p2Prev->distance($p2);
                $point['h'] = $point['ele'] - $points[$type][$i-1]['ele'];
            }
            $p2Prev = $p2;
            $points[$type][$i] = $point;
            $lowest  = ($lowest==false  or $point['ele']<$lowest)   ? $point['ele'] : $lowest;
            $highest = ($highest==false or  $point['ele']>$highest) ? $point['ele'] : $highest;
            
            $time = ($time==false && $point['time']!=false) ? $point['time'] : $time;
            if ($type!='wpt') {
                $distance = $distance + $point['d'];
                $climb = $point['h']>0 ? $climb + $point['h'] : $climb;
            }
        }
        $pStart  = $points[$type][0];
        $pFinish = $points[$type][$i-1];
    }
    if (!isset($points['wpt'])) {           // Add Start & Finish waypoints
        $pFinish['type'] = 'wpt';
        $pFinish['name'] = 'Finish';
        $points['wpt'][] = $pFinish;
        $pStart['type' ] = 'wpt';
        $pStart['name' ] = 'Start';
        $points['wpt'][] = $pStart;        
    }
    $points['Time'    ] = $time;
    $points['Distance'] = round($distance / 1609.344, 2);    // Convert metres to miles
    $points['Highest' ] = $highest;
    $points['Lowest'  ] = $lowest;
    $points['Climb'   ] = $climb;
    //dd('GPXTagsReader',$points);
    return $points;
}

class GPXTagsReader {
    var $aQTFF = [
        'rtept' => 'Route Point',           
        'trkpt' => 'Track Point',
        'wpt'   => 'Way Point',
    ];

    function getTags($path, $search=[]) {
        if (count($search) == 0) { $search = $this->aQTFF; }
        $file = fopen($path, 'rb');     
        $data = fread($file, 256);                   // Read header & check if any gpx
        fclose($file);

        if (stripos($data, '<gpx ') == false) {      // LEAVE - Bad Header
            return false;                                   
        }

        $tags['FileName'] = $path;
        $tags['Search'  ] = $search;
        $tags['Distance'] = 0;
        $tags['Climb'   ] = 0;
        $tags['Lowest'  ] = 0;
        $tags['Highest' ] = 0;
        $tags['Time'    ] = 0;

        $xml = json_decode(json_encode((array)simplexml_load_file($path)), 1);   // Read GPX file into an array
        $gpx = getPoints(recursive($xml, ['trkpt', 'rtept', 'wpt']));            // Extract different point types
        return json_encode($gpx);
    }

}
