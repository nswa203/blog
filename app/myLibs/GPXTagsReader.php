<?php namespace App\myLibs;

use PHPCoord\OSRef;
use PHPCoord\LatLng;
use PHPCoord\RefEll;

// Recursively runs through 7 deep array structure to capture certain keys (crude but effective)
function recursivePoints(array $data1, array $keys) {
    $results = [];
    foreach ($data1 as $key1 => $data2) {
        if (in_array($key1, $keys, true)) { $results = array_merge($results, addType($data2, $key1)); }
        else if (!is_array($data2)) { continue; }
        foreach ($data2 as $key2 => $data3) {
            if (in_array($key2, $keys, true)) { $results = array_merge($results, addType($data3, $key2)); }
            else if (!is_array($data3)) { continue; }
            foreach ($data3 as $key3 => $data4) {
                if (in_array($key3, $keys, true)) { $results = array_merge($results, addType($data4, $key3)); }
                else if (!is_array($data4)) { continue; }
                foreach ($data4 as $key4 => $data5) {
                    if (in_array($key4, $keys, true)) { $results = array_merge($results, addType($data5, $key4)); }
                    else if (!is_array($data5)) { continue; }
                    foreach ($data5 as $key5 => $data6) {
                        if (in_array($key5, $keys, true)) { $results = array_merge($results, addType($data6, $key5)); }
                        else if (!is_array($data6)) { continue; }
                        foreach ($data6 as $key6 => $data7) {
                            if (in_array($key6, $keys, true)) { $results = array_merge($results, addType($data7, $key6)); }
                        }    
                    }    
                }
            }
        }
    }
//dd($data1, $results);
    return $results;
}
// NS01 Fix for single point arrays
function addType(array $data, $type='none') {
    foreach ($data as $key => $value) {
        if (is_array($data[$key])) {                // NS01
            $data[$key]['type'] = $type;
        } else {                                    // NS01
            $data['type'] = $type;
            $data2[0] = $data;
            return $data2; 
        }
    }
    return $data;
}

// Rebuilds selected GPS points data into a 2-D array
// Populate each GPS location with Easting, Northing, OSRef, Elevation, delta Distance, delta Height
// Adds Start & Finish waypoints if no other waypoints present.
// NS01   
function getPoints($data) {
    $tags = ['lat', 'lon', 'ele', 'time', 'type', 'name', 'image', 'link', 'east', 'north', 'osref', 'd', 'h', 'icon', 'iconsize'];
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
            if($point['east']<0 || $point['north']<0) {     // NS01 Out-of-bounds
                $point['type'] = $point['type'].'Err';
                continue;
            }            
            $point['osref'] = $p2->toSixFigureReference();
            $point['ele'  ] = getElevation([$p2->getX(), $p2->getY()], true, true);
            if ($i>0) {
                $point['d'] = $p2Prev->distance($p2);
                $point['h'] = $point['ele'] - $points[$type][$i-1]['ele'];
            }
            $p2Prev = $p2;
            $points[$type][$i] = $point;
            $lowest  = ($lowest ==false or $point['ele']<$lowest)  ? $point['ele'] : $lowest;
            $highest = ($highest==false or $point['ele']>$highest) ? $point['ele'] : $highest;
            
            $time = ($time==false && $point['time']!=false) ? $point['time'] : $time;
            if ($type!='wpt') {
                $distance = $distance + $point['d'];
                $climb = $point['h']>0 ? $climb + $point['h'] : $climb;
            }
        }
        $pStart  = $points[$type][0];
        $pFinish = $points[$type][$i-1];
    }
    if (! isset($points['wpt'])) {                          // Add Start & Finish waypoints
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
//dump('getTags', $path, $search);
        if (count($search) == 0) { $search = $this->aQTFF; }
        $file = fopen($path, 'rb');     
        $data = fread($file, 512);                   // Read header & check if any gpx NS01
        fclose($file);
//dump($data, stripos($data, '<gpx '));
        if (stripos($data, '<gpx ') == false) {      // LEAVE - Bad Header
            return false;                                   
        }
//dd($path, $search, $file, $data);

        $tags['FileName'] = $path;
        $tags['Search'  ] = $search;
        $tags['Distance'] = 0;
        $tags['Climb'   ] = 0;
        $tags['Lowest'  ] = 0;
        $tags['Highest' ] = 0;
        $tags['Time'    ] = 0;

        $xml = json_decode(json_encode((array)simplexml_load_file($path)), 1);      // Read GPX file into an array
        $gpx = getPoints(recursivePoints($xml, ['trkpt', 'rtept', 'wpt']));         // Extract different point types
//dd($gpx);
        return json_encode($gpx);
    }

}
