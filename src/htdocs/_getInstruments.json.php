<?php

include_once '../conf/config.inc.php';
include_once '../lib/_functions.inc.php';
include_once '../lib/classes/Db.class.php';

$callback = safeParam('callback');
$now = date(DATE_RFC2822);

$db = new Db;

// Query instruments
$rsInstruments = $db->queryInstruments();

// Query plots
$rsPlots = $db->queryPlots();

// Initialize JSON output array
$output = [
    'generated' => $now,
    'count' => $rsInstruments->rowCount(),
    'type' => 'FeatureCollection',
    'features' => []
];

// Group plot metadata by site
$plots = $rsPlots->fetchAll(PDO::FETCH_GROUP | PDO::FETCH_ASSOC);

// Process instrument data and add to features array
while ($row = $rsInstruments->fetch(PDO::FETCH_ASSOC)) {
    $datetime = '';
    $file = '';
    $site = $row['site'];

    // Check if plots exist for the current site
    if (isset($plots[$site]) && is_array($plots[$site]) && !empty($plots[$site])) {
        $plot = $plots[$site][0]; // Get the first plot for the site
        if(is_array($plot)){
            $datetime = $plot['datetime'];
            $file = $plot['file'];
        }

    }

    $feature = [
        'geometry' => [
            'coordinates' => [
                floatval($row['lon']),
                floatval($row['lat'])
            ],
            'type' => 'Point'
        ],
        'id' => 'point' . intval($row['id']),
        'properties' => [
            'site' => $site,
            'net' => $row['net'],
            'loc' => $row['loc'],
            'description' => $row['description'],
            'status' => $row['status'],
            'file' => $file,
            'datetime' => $datetime
        ],
        'type' => 'Feature'
    ];

    array_push($output['features'], $feature);
}

// Send JSON response
showJson($output, $callback);

?>