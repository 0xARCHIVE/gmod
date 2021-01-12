<?php

include_once('./libs/steamID.php');

function get_player_list() {
  $api_endpoint = "https://api.trackyserver.com/widget/index.php?id=663633";
  $response = file_get_contents($api_endpoint);
  $data = json_decode($response,true);

  $players = array();
  if (!array_key_exists("playerslist", $data)) { return $players; }
  foreach($data["playerslist"] as $value) {
    if (!array_key_exists("name", $value)) { continue; }
    array_push($players, $value["name"]);
  }

  return $players;
}

$factions = array(
  "freelancer" => "Freelancers",
  "starfleet" => "Star Fleet",
  "legion" => "The Legion",
  "miners" => "Major Miners",
  "corporation" => "The Corporation",
  "alliance" => "The Alliance",
  "unassigned" => "Unassigned"
);

// debugging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// include templates
$scoreboard_template = file_get_contents('./scoreboard.tpl');
$row_template = file_get_contents('./row.tpl');

///////////////////////////////////////////////

// get player info
$api_endpoint = "https://api.spaceage.mp/v2/players/";
$response = file_get_contents($api_endpoint);
$data = json_decode($response,true);

// generate rows
$html_rows = array();
$player_list = get_player_list();

foreach($data as $key => $row) {
  $rank = $key + 1;
  $name = $row["name"];
  $score = $row["score"]; if ($score == 0) { break; }
  $faction = $row["faction_name"];
  $faction_leader = $row["is_faction_leader"];
  $steamid = $row["steamid"];

  if (!array_key_exists($faction, $factions)) { $faction = "unassigned"; }
  $faction_name = $factions[$faction];

  $row_replacements = array(
    "{rank}" => $rank,
    "{name}" => $name,
    "{score}" => number_format($score, 0),
    "{faction}" => $faction,
    "{faction_name}" => $faction_name,
    "{faction_leader}" => $faction_leader ? "faction_leader" : "not_faction_leader",
    "{community_id}" => SteamIDConverter::convert($steamid),
    "{online}" => in_array($name, $player_list) ? "online" : "not_online"
  );

  $html_row = strtr($row_template, $row_replacements);
  array_push($html_rows, $html_row);
}

// insert rows into scoreboard template
$html_rows = join("\n", $html_rows);
$scoreboard_replacements = array("{rows}" => $html_rows);
$html_scoreboard = strtr($scoreboard_template, $scoreboard_replacements);

echo($html_scoreboard);

?>
