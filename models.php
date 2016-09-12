<?php
include('db.php');
include('utils.php');

class Pokemon
{
    static function get_active($swLat, $swLng, $neLat, $neLng)
    {
        global $db;
        if ($swLat == null || $swLng == null || $neLat == null || $neLng == null) {
            $sql = "SELECT * FROM `pokemon` WHERE `disappear_time` > UTC_TIMESTAMP()";
        } else {
            $sql = "SELECT * FROM `pokemon` WHERE `disappear_time` > UTC_TIMESTAMP() AND `latitude` >= '" . $swLat . "' AND  `longitude` >= '" . $swLng . "' AND  `latitude` <= '" . $neLat . "' AND  `longitude` <= '" . $neLng . "'";
        }
        $result = $db->query($sql);
        $data = array();
        if (mysqli_num_rows($result) > 0) {
            $i = 0;
            while ($row = $db->fetch($result)) {
                $data[$i]['disappear_time'] = ($row['disappear_time'] == null) ? null : (integer) (strtotime($row['disappear_time'] . " UTC") * 1000);
                $data[$i]['encounter_id'] = $row['encounter_id'];
                $data[$i]['latitude'] = (float) $row['latitude'];
                $data[$i]['longitude'] = (float) $row['longitude'];
                $data[$i]['pokemon_id'] = (integer) $row['pokemon_id'];
                $data[$i]['pokemon_name'] = get_pokemon_name($row['pokemon_id']);
                $data[$i]['pokemon_rarity'] = get_pokemon_rarity($row['pokemon_id']);
                $data[$i]['pokemon_types'] = get_pokemon_types($row['pokemon_id']);
                $data[$i]['spawnpoint_id'] = $row['spawnpoint_id'];
                $i++;
            }
        }
        return $data;
    }
    
    static function get_seen($duration)
    {
        global $db;
        if (preg_match('/(\d+)([hdmy])/', $duration, $matches)) {
            $durationCount = $matches[1];
            $durationIdentifier = $matches[2];
        } else {
            $durationCount = "0";
            $durationIdentifier = "";
        }
        switch ($durationIdentifier) {
            case "h":
                $durationIdentifier = "HOUR";
                break;
            case "d":
                $durationIdentifier = "DAY";
                break;
            case "m":
                $durationIdentifier = "MONTH";
                break;
            case "y":
                $durationIdentifier = "YEAR";
                break;
            default:
                $durationIdentifier = "HOUR";
                break;
        }
        if ($durationCount > 0) {
            $sql = "SELECT DISTINCT `t1`.`pokemon_id`, `t1`.`disappear_time`, `t1`.`latitude`, `t1`.`longitude`, `counttable`.`count` FROM `pokemon` AS t1 INNER JOIN (SELECT `t3`.`pokemon_id`, COUNT(`t3`.`pokemon_id`) AS count, MAX(`t3`.`disappear_time`) AS lastappeared FROM `pokemon` AS t3 WHERE (`t3`.`disappear_time` > DATE_SUB(UTC_TIMESTAMP(), INTERVAL " . $durationCount . " " . $durationIdentifier . ")) GROUP BY `t3`.`pokemon_id`) AS counttable ON (`t1`.`pokemon_id` = `counttable`.`pokemon_id`) WHERE (`t1`.`disappear_time` = `counttable`.`lastappeared`)";
        } else {
            $sql = "SELECT DISTINCT `t1`.`pokemon_id`, `t1`.`disappear_time`, `t1`.`latitude`, `t1`.`longitude`, `counttable`.`count` FROM `pokemon` AS t1 INNER JOIN (SELECT `t3`.`pokemon_id`, COUNT(`t3`.`pokemon_id`) AS count, MAX(`t3`.`disappear_time`) AS lastappeared FROM `pokemon` AS t3 WHERE (`t3`.`disappear_time` > 0) GROUP BY `t3`.`pokemon_id`) AS counttable ON (`t1`.`pokemon_id` = `counttable`.`pokemon_id`) WHERE (`t1`.`disappear_time` = `counttable`.`lastappeared`)";
        }
        $result = $db->query($sql);
        $data = array();
        $data['pokemon'] = array();
        $data['total'] = 0;
        if (mysqli_num_rows($result) > 0) {
            $totalPokemon = 0;
            $i = 0;
            while ($row = $db->fetch($result)) {
                $data['pokemon'][$i]['count'] = (integer) $row['count'];
                $data['pokemon'][$i]['disappear_time'] = ($row['disappear_time'] == null) ? null : (integer) (strtotime($row['disappear_time'] . " UTC") * 1000);
                $data['pokemon'][$i]['latitude'] = (float) $row['latitude'];
                $data['pokemon'][$i]['longitude'] = (float) $row['longitude'];
                $data['pokemon'][$i]['pokemon_id'] = (integer) $row['pokemon_id'];
                $data['pokemon'][$i]['pokemon_name'] = get_pokemon_name($row['pokemon_id']);
                
                $totalPokemon += $data['pokemon'][$i]['count'];
                $i++;
            }
            $data['total'] = $totalPokemon;
        }
        return $data;
    }
    
    static function get_appearances($pokemon_id, $last_appearance, $duration)
    {
        global $db;
        $dt = new DateTime("@$last_appearance");
        $last_appearance_readable = $dt->format('Y-m-d H:i:s');
        if (preg_match('/(\d+)([hdmy])/', $duration, $matches)) {
            $durationCount = $matches[1];
            $durationIdentifier = $matches[2];
        } else {
            $durationCount = "0";
            $durationIdentifier = "";
        }
        switch ($durationIdentifier) {
            case "h":
                $durationIdentifier = "HOUR";
                break;
            case "d":
                $durationIdentifier = "DAY";
                break;
            case "m":
                $durationIdentifier = "MONTH";
                break;
            case "y":
                $durationIdentifier = "YEAR";
                break;
            default:
                $durationIdentifier = "HOUR";
                break;
        }
        if ($durationCount > 0) {
            $sql = "SELECT `t1`.`encounter_id`, `t1`.`spawnpoint_id`, `t1`.`pokemon_id`, `t1`.`latitude`, `t1`.`longitude`, `t1`.`disappear_time` FROM `pokemon` AS t1 WHERE (((`t1`.`pokemon_id` = " . $pokemon_id . ") AND (`t1`.`disappear_time` > '" . $last_appearance_readable . "')) AND (`t1`.`disappear_time` > DATE_SUB(UTC_TIMESTAMP(), INTERVAL " . $durationCount . " " . $durationIdentifier . "))) ORDER BY `t1`.`disappear_time` ASC";
        } else {
            $sql = "SELECT `t1`.`encounter_id`, `t1`.`spawnpoint_id`, `t1`.`pokemon_id`, `t1`.`latitude`, `t1`.`longitude`, `t1`.`disappear_time` FROM `pokemon` AS t1 WHERE (((`t1`.`pokemon_id` = " . $pokemon_id . ") AND (`t1`.`disappear_time` > '" . $last_appearance_readable . "')) AND (`t1`.`disappear_time` > 0)) ORDER BY `t1`.`disappear_time` ASC";
        }
        $result = $db->query($sql);
        $data = array();
        if (mysqli_num_rows($result) > 0) {
            $i = 0;
            while ($row = $db->fetch($result)) {
                $data[$i]['disappear_time'] = ($row['disappear_time'] == null) ? null : (integer) (strtotime($row['disappear_time'] . " UTC") * 1000);
                $data[$i]['encounter_id'] = $row['encounter_id'];
                $data[$i]['latitude'] = (float) $row['latitude'];
                $data[$i]['longitude'] = (float) $row['longitude'];
                $data[$i]['pokemon_id'] = (integer) $row['pokemon_id'];
                $data[$i]['spawnpoint_id'] = $row['spawnpoint_id'];
                $i++;
            }
        }
        return $data;
    }
    
    static function get_spawn_time($disappear_time)
    {
        return ($disappear_time + 2700) % 3600;
    }
    
    static function get_spawnpoints($southBoundary, $westBoundary, $northBoundary, $eastBoundary)
    {
        global $db;
        if (!in_array("", array(
            $southBoundary,
            $westBoundary,
            $northBoundary,
            $eastBoundary
        ))) {
            $sqlWhere = " WHERE ((((`t1`.`latitude` <= " . $northBoundary . ") AND (`t1`.`latitude` >= " . $southBoundary . ")) AND (`t1`.`longitude` >= " . $westBoundary . ")) AND (`t1`.`longitude` <= " . $eastBoundary . "))";
        } else {
            $sqlWhere = "";
        }
        $sql = "SELECT `t1`.`latitude`, `t1`.`longitude`, `t1`.`spawnpoint_id`, ((EXTRACT(minute FROM `t1`.`disappear_time`) * 60) + EXTRACT(second FROM `t1`.`disappear_time`)) AS time, Count(`t1`.`spawnpoint_id`) AS count FROM `pokemon` AS t1" . $sqlWhere . " GROUP BY `t1`.`latitude`, `t1`.`longitude`, `t1`.`spawnpoint_id`, time";
        $result = $db->query($sql);
        $data = array();
        if (mysqli_num_rows($result) > 0) {
            $i = 0;
            while ($row = $db->fetch($result)) {
                $size = count($data);
                for ($n = 0; $n < $size; $n++) {
                    if ($data[$n]['spawnpoint_id'] == $row['spawnpoint_id']) {
                        $data[$n]['special'] = (boolean) true;
                        continue 2;
                    }
                }
                $data[$i]['latitude'] = (float) $row['latitude'];
                $data[$i]['longitude'] = (float) $row['longitude'];
                $data[$i]['spawnpoint_id'] = $row['spawnpoint_id'];
                $data[$i]['time'] = (integer) Pokemon::get_spawn_time($row['time']);
                $i++;
            }
        }
        return $data;
    }
}

class Pokestop
{
    static function get_stops($swLat, $swLng, $neLat, $neLng)
    {
        global $db;
        if ($swLat == null || $swLng == null || $neLat == null || $neLng == null) {
            $sql = "SELECT * FROM `pokestop`";
        } else {
            $sql = "SELECT * FROM `pokestop` WHERE `latitude` >= '" . $swLat . "' AND  `longitude` >= '" . $swLng . "' AND  `latitude` <= '" . $neLat . "' AND  `longitude` <= '" . $neLng . "'";
        }
        $result = $db->query($sql);
        $data = array();
        if (mysqli_num_rows($result) > 0) {
            $i = 0;
            while ($row = $db->fetch($result)) {
                $data[$i]['active_fort_modifier'] = $row['active_fort_modifier'];
                $data[$i]['enabled'] = (boolean) $row['enabled'];
                $data[$i]['last_modified'] = ($row['last_modified'] == null) ? null : (integer) (strtotime($row['last_modified'] . " UTC") * 1000);
                $data[$i]['latitude'] = (float) $row['latitude'];
                $data[$i]['longitude'] = (float) $row['longitude'];
                $data[$i]['lure_expiration'] = ($row['lure_expiration'] == null) ? null : (integer) (strtotime($row['lure_expiration'] . " UTC") * 1000);
                $data[$i]['pokestop_id'] = $row['pokestop_id'];
                
                $i++;
            }
        }
        return $data;
    }
}

class Gym
{
    static function get_gyms($swLat, $swLng, $neLat, $neLng)
    {
        global $db;
        if ($swLat == null || $swLng == null || $neLat == null || $neLng == null) {
            $sql = "SELECT * FROM `gym`";
        } else {
            $sql = "SELECT * FROM `gym` WHERE `latitude` >= '" . $swLat . "' AND  `longitude` >= '" . $swLng . "' AND  `latitude` <= '" . $neLat . "' AND  `longitude` <= '" . $neLng . "'";
        }
        $result = $db->query($sql);
        $data = array();
        if (mysqli_num_rows($result) > 0) {
            while ($row = $db->fetch($result)) {
                $data[$row['gym_id']]['enabled'] = (boolean) $row['enabled'];
                $data[$row['gym_id']]['guard_pokemon_id'] = (integer) $row['guard_pokemon_id'];
                $data[$row['gym_id']]['gym_id'] = $row['gym_id'];
                $data[$row['gym_id']]['gym_points'] = (integer) $row['gym_points'];
                $data[$row['gym_id']]['last_modified'] = ($row['last_modified'] == null) ? null : (integer) (strtotime($row['last_modified'] . " UTC") * 1000);
                $data[$row['gym_id']]['last_scanned'] = ($row['last_scanned'] == null) ? null : (integer) (strtotime($row['last_scanned'] . " UTC") * 1000);
                $data[$row['gym_id']]['latitude'] = (float) $row['latitude'];
                $data[$row['gym_id']]['longitude'] = (float) $row['longitude'];
                // In my personal setup, I've got detailed gym info collection turned off, so the following two values
                // are hard-coded to be empty, as is my preference. Should you want to fill them, you'll need to add one
                // or two more SQL queries (or modify the existing one), then fill these two fields with the correct info.
                $data[$row['gym_id']]['name'] = null;
                $data[$row['gym_id']]['pokemon'] = array();
                $data[$row['gym_id']]['team_id'] = (integer) $row['team_id'];
            }
        }
        return $data;
    }
}

class ScannedLocation
{
    static function get_recent($swLat, $swLng, $neLat, $neLng)
    {
        global $db;
        $sql = "SELECT * FROM `scannedlocation` WHERE `last_modified` >= DATE_SUB(UTC_TIMESTAMP(), INTERVAL 15 MINUTE) AND `latitude` >= '" . $swLat . "' AND  `longitude` >= '" . $swLng . "' AND  `latitude` <= '" . $neLat . "' AND  `longitude` <= '" . $neLng . "' ORDER BY `last_modified` ASC";
        $result = $db->query($sql);
        $data = array();
        if (mysqli_num_rows($result) > 0) {
            $i = 0;
            while ($row = $db->fetch($result)) {
                $data[$i]['last_modified'] = ($row['last_modified'] == null) ? null : (integer) (strtotime($row['last_modified'] . " UTC") * 1000);
                $data[$i]['latitude'] = (float) $row['latitude'];
                $data[$i]['longitude'] = (float) $row['longitude'];
                $i++;
            }
        }
        return $data;
    }
}

?>