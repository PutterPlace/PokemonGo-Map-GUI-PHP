<?php
include('config.inc.php');
include('models.php');

switch (get_request_var("func")) {
    case "raw_data":
        raw_data();
        break;
    case "loc":
        loc();
        break;
    case "next_loc":
        next_loc();
        break;
    case "mobile":
        mobile();
        break;
    case "search_control":
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            post_search_control();
        } else {
            get_search_control();
        }
        break;
    default:
        echo "Nothing to see here.....";
}

function loc()
{
    global $startingLat, $startingLng;
    // Uses the starting latitude and longitude provided in config.inc.php
    // I'm not sure if this will ever even get used in this implementation,
    // but I included in for good measure.
    echo json_encode(array(
        "lat" => (float) $startingLat,
        "lng" => (float) $startingLng
    ));
}

function next_loc()
{
    // I didn't care for this, so re-write it if you'd like.
    http_response_code(403);
    echo "Location changes are turned off.";
}

function mobile()
{
    // I don't care for this, so re-write it if you'd like.
    http_response_code(403);
    echo "Nearby pokemon are disabled.";
}

function raw_data()
{
    global $purgeData;
    header('Content-Type: application/json');
    $swLat = get_request_var("swLat");
    $swLng = get_request_var("swLng");
    $neLat = get_request_var("neLat");
    $neLng = get_request_var("neLng");
    $duration = get_request_var("duration");
    $pokemon_id = get_request_var("pokemonid");
    $last_appearance = get_request_var("last");
    
    $data = array();
    if (get_request_var("pokemon") == "true") {
        $data['pokemons'] = Pokemon::get_active($swLat, $swLng, $neLat, $neLng);
    }
    if (get_request_var("pokestops") == "true") {
        $data['pokestops'] = Pokestop::get_stops($swLat, $swLng, $neLat, $neLng);
    }
    if (get_request_var("gyms") == "true") {
        $data['gyms'] = Gym::get_gyms($swLat, $swLng, $neLat, $neLng);
    }
    if (get_request_var("scanned") == "true") {
        $data['scanned'] = ScannedLocation::get_recent($swLat, $swLng, $neLat, $neLng);
    }
    if (get_request_var("seen") == "true") {
        $data['seen'] = Pokemon::get_seen($duration);
    }
    if (get_request_var("appearances") == "true") {
        $data['appearances'] = Pokemon::get_appearances($pokemon_id, $last_appearance / 1000, $duration);
    }
    if (get_request_var("spawnpoints") == "true") {
        $data['spawnpoints'] = Pokemon::get_spawnpoints($swLat, $swLng, $neLat, $neLng);
    }
    if ($purgeData > 0) {
        // I included the purge data function in my implementation for
        // those who may want to get rid of old spawns. This will ONLY remove
        // pokemon spawns. Scanned locations are purged automatically by the
        // running workers in PokemonGo-Map. Specify hours in config.inc.php
        purge_data($purgeData);
    }
    echo json_encode($data);
}

function get_search_control()
{
    echo json_encode(array(
        "status" => true
    ));
}

function post_search_control()
{
    // I don't care for this, so re-write it if you'd like.
    http_response_code(403);
    echo "Search control is disabled";
}

function get_request_var($varName)
{
    return (isset($_REQUEST[$varName])) ? $_REQUEST[$varName] : "";
}

?>