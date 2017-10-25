<?php
header('Content-Type: application/javascript');
//xbox
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL,"https://xboxapi.com/v2/2533274908675729/profile");
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, ["X-Auth: noice_auth"]);
$x_arr = json_decode(curl_exec($ch));
curl_close ($ch);

//steam
$s_arr = [json_decode(file_get_contents('http://api.steampowered.com/ISteamUser/GetPlayerSummaries/v0002/?key=noice_key&steamids=76561198057334495')), json_decode(file_get_contents('http://api.steampowered.com/IPlayerService/GetOwnedGames/v1/?key=noice_key&steamid=76561198057334495&format=json&include_appinfo=1&include_played_free_games=1"'))->response];

//github
$g_opt = ['http' => ['method' => 'GET','header' => ['User-Agent: PHP']]];
$g_ctx = stream_context_create($g_opt);
$git = json_decode(file_get_contents("https://api.github.com/users/salies", false, $g_ctx));

//lastfm
$l_info =  json_decode(file_get_contents("http://ws.audioscrobbler.com/2.0/?method=user.getinfo&user=salies&api_key=noice_key&format=json"));
$l_top = json_decode(file_get_contents("http://ws.audioscrobbler.com/2.0/?method=user.gettoptracks&user=salies&api_key=noice_key&format=json"));

//MOCKUP - SUBSTITUIR PELA API DEPOIS (N FIZ PQ TEM LIMITE DE REQUEST)
$arr = "var info_bar = ".json_encode(array(
    'gamertag' => $x_arr->Gamertag, 
    'gamerscore' => $x_arr->Gamerscore, 
    'gamerpic' => $x_arr->GameDisplayPicRaw, 
    'gold' => $x_arr->TenureLevel, 
    'steam_name' => $s_arr[0]->response->players[0]->personaname,
    'steam_avatar' => $s_arr[0]->response->players[0]->avatarfull,
    'steam_games' => $s_arr[1]->game_count,
    'steam_years' => date("Y") - (gmdate("Y", $s_arr[0]->response->players[0]->timecreated)),
    'steam_playtime' => round(array_sum(array_column($s_arr[1]->games, 'playtime_forever')) / 60),
    'git_name' => $git->login,
    'git_avatar' => $git->avatar_url,
    'git_repos' => $git->public_repos,
    'git_followers' => $git->followers,
    'lastfm_name' => $l_info->user->name,
    'lastfm_scrobbles' => $l_info->user->playcount,
    'lastfm_toptrack' => $l_top->toptracks->track[0]->name,
    'lastfm_avatar' => $l_info->user->image[2]->{"#text"}
)).";";

echo($arr);
echo '
document.querySelector(".git_name").innerHTML = info_bar.git_name;
document.querySelector(".lastfm_name").innerHTML = info_bar.lastfm_name;
document.querySelector(".scrobbles").innerHTML = info_bar.lastfm_scrobbles;
if((info_bar.lastfm_toptrack).length > 13){
    var toptrack = (info_bar.lastfm_toptrack).substring(0,10) + "...";
}
else{
    var toptrack = info_bar.lastfm_toptrack;
}
document.querySelector(".toptrack").innerHTML = toptrack;
document.querySelector(".git_name").innerHTML = info_bar.git_name;
document.querySelector(".repos").innerHTML = info_bar.git_repos;
document.querySelector(".git_followers").innerHTML = info_bar.git_followers;
document.querySelector(".gamerscore").innerHTML = ((info_bar.gamerscore).toLocaleString()).replace(/,/g, '.');
document.querySelector(".xplayed").innerHTML = info_bar.gold;
document.querySelector(".gamertag").innerHTML = info_bar.gamertag;
document.querySelector(".steam_name").innerHTML = info_bar.steam_name;
document.querySelector(".sgames").innerHTML = info_bar.steam_games;
document.querySelector(".hoursplayed").innerHTML = info_bar.steam_playtime;
document.querySelector(".service").innerHTML = info_bar.steam_years;

document.querySelector(".lastfm_avatar").src=info_bar.lastfm_avatar;
document.querySelector(".git_avatar").src=info_bar.git_avatar;
document.querySelector(".xbox_avatar").src=info_bar.gamerpic;
document.querySelector(".steam_avatar").src=info_bar.steam_avatar;
$(".networks").fadeIn();
';
?>
