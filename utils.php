<?php

function get_pokemon_data($pokemon_id)
{
    // My implementation doesn't account for different locales. If you need
    // other languages, feel free to re-write this function to switch to
    // other languages as needed.
    $fileContents = file_get_contents(dirname(__FILE__) . "/static/dist/data/pokemon.min.json");
    $pokemonData = json_decode($fileContents, true);
    return $pokemonData[$pokemon_id];
}

function get_pokemon_name($pokemon_id)
{
    return get_pokemon_data($pokemon_id)['name'];
}

function get_pokemon_rarity($pokemon_id)
{
    return get_pokemon_data($pokemon_id)['rarity'];
}

function get_pokemon_types($pokemon_id)
{
    return get_pokemon_data($pokemon_id)['types'];
}

function purge_data($hours)
{
    global $db;
    $sql = "DELETE FROM  `pokemon` WHERE  `disappear_time` < DATE_SUB( UTC_TIMESTAMP( ) , INTERVAL " . $hours . " HOUR )";
    $result = $db->query($sql);
}

?>