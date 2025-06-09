<?php

include_once('_geocode.inc.php');
include_once('_getStateList.inc.php');

/**
 * Determine lat/lng bounds of each region and add them to the array
 */
function getRegions ($items) {
  $regions = [];

  foreach ($items as $item) {
    $coordPairs = json_decode($item['coords']);
    $region = [ // hang on to these elements
      'name' => $item['name'],
      'code' => $item['code'],
      'contacts' => $item['contacts'],
      'message' => $item['message']
    ];

    if ($coordPairs) {
      $lats = [];
      $lons = [];

      foreach ($coordPairs as $coordPair) {
        array_push($lats, $coordPair[0]);
        array_push($lons, $coordPair[1]);
      }
      $region['n'] = max($lats);
      $region['e'] = max($lons);
      $region['s'] = min($lats);
      $region['w'] = min($lons);
    }
    array_push($regions, $region);
  }

  return $regions;
}

/**
 * Check if string matches an event id
 *
 * @param $str {String}
 * @return {Boolean}
 */
function isEvent ($str) {
  if (preg_match('/[a-zA-Z]{2}\w{8}/', $str)) {
    return true;
  }
}

/**
 * Check if string matches an instrument
 *
 * @param $str {String}
 * @return {Boolean}
 */
function isInstrument ($str) {
  if (preg_match('/[^_]+_[^_]+_[^_]+/', $str)) {
    return true;
  }
}

/**
 * Get a request parameter from $_GET or $_POST
 *
 * @param $name {String}
 *     The parameter name
 * @param $default {?} default is NULL
 *     Optional default value if the parameter was not provided.
 *
 * @return $value {String}
 */
function safeParam ($name, $default=NULL,) {
  $value = null;

  if (isset($_POST[$name]) && $_POST[$name] !== '') {
      $value = filter_input(INPUT_POST, $name);
  } elseif (isset($_GET[$name]) && $_GET[$name] !== '') {
      $value = filter_input(INPUT_GET, $name);
  } else {
      $value = $default;
  }

  if ($value !== null && is_string($value)) {
      $value = htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
  }

  return $value;
}

/**
 * Convert an array to a json feed and print it
 *
 * @param $array {Array}
 *     Data from db
 * @param $callback {String} default is NULL
 *     optional callback for jsonp requests
 */
function showJson ($array, $callback=NULL) {
  header('Content-Type: application/json');
  header('Access-Control-Allow-Origin: *');
  header('Access-Control-Allow-Methods: *');
  header('Access-Control-Allow-Headers: accept,origin,authorization,content-type');

  $json = json_encode($array);
  if ($callback) {
    $json = "$callback($json)";
  }
  print $json;
}
