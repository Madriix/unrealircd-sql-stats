<?php
/* unrealircd_users and unrealircd_top_countries */
try {
    $result = $pdo->query("SELECT 1 FROM " . $config["mysql"]["table_prefix"] . "users LIMIT 1");
} catch (\PDOException $e) {
    // We got an exception (table not found)
    $statements = [
        'CREATE TABLE `' . $config["mysql"]["table_prefix"] . 'users` (
            `id` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
            `id_user` varchar(255) NOT NULL,
            `name` varchar(255) NOT NULL,
            `username` varchar(255) NOT NULL,
            `realname` varchar(255) NOT NULL,
            `vhost` varchar(255) NOT NULL,
            `account` varchar(255) NOT NULL,
            `reputation` varchar(255) NOT NULL,
            `hostname` varchar(255) NOT NULL,
            `ip` varchar(255) NOT NULL,
            `country_code` varchar(2) NOT NULL,
            `asn` varchar(10) NOT NULL,
            `asname` varchar(255) NOT NULL,
            `connected_since` varchar(255) NOT NULL,
            `idle_since` varchar(255) NOT NULL,
            `idle` varchar(255) NOT NULL,
            `modes` varchar(255) NOT NULL,
            `channels` TEXT NOT NULL,
            `security_groups` TEXT NOT NULL,
            `away_reason` TEXT NOT NULL,
            `away_since` TEXT NOT NULL
          ) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;'
    ];

    foreach ($statements as $statement) {
        $pdo->exec($statement);
    }
}

$users = $rpc->user()->getAll(4); 
/* https://bugs.unrealircd.org/view.php?id=6327 */

/*
print_r($users);
exit;*/


$stmt = $pdo->prepare("TRUNCATE TABLE " . $config["mysql"]["table_prefix"] . "users");
$stmt->execute();

try {
    foreach ($users as $user) {
        //echo "Name : " . $user->name . "<br>";
        $name               = $user->name ?? '';
        $id                 = $user->id ?? '';
        $username           = $user->user->username ?? '';
        $realname           = $user->user->realname ?? '';
        $vhost              = $user->user->vhost ?? '';
        $account            = $user->user->account ?? '';
        $reputation         = $user->user->reputation ?? '';
        $hostname           = $user->hostname ?? '';
        $ip                 = $user->ip ?? '';
        $country_code       = $user->geoip->country_code ?? '';
        $asn                = $user->geoip->asn ?? '';
        $asname             = $user->geoip->asname ?? '';
        $connected_since    = $user->connected_since ?? '';
        $idle_since         = $user->idle_since ?? '';
        $idle               = abs(strtotime($connected_since) - strtotime($idle_since));
        $modes              = $user->user->modes ?? '';

        $channels = $user->user->channels ?? '';
        $chans = '';
        if ($channels!="") {
            foreach ($channels as $chan) {
                $chans .= ",".$chan->name;
            }
        }

        $security_groups = $user->user->{'security-groups'} ?? '';
        $sg = '';
        if ($security_groups!="") {
            foreach ($security_groups as $key => $value ) {
                $sg .= ",".$value;
            }
        }

        $away_reason = $user->user->{'away_reason'} ?? '';
        $away_since = $user->user->{'away_since'} ?? '';

        
        $prep = $pdo->prepare("INSERT INTO " . $config["mysql"]["table_prefix"] . "users (id, id_user, name, username, realname, vhost, account, reputation, hostname, ip, country_code, asn, asname, connected_since, idle_since, idle, modes, channels, security_groups, away_reason, away_since) 
        VALUES (:id, :id_user, :name, :username, :realname, :vhost, :account, :reputation, :hostname, :ip, :country_code, :asn, :asname, :connected_since, :idle_since, :idle, :modes, :channels, :security_groups, :away_reason, :away_since)");
        $prep->execute([
            "id" => '',
            "id_user" => $id,
            "name" => $name, 
            "username" => $username, 
            "realname" => $realname, 
            "vhost" => $vhost, 
            "account" => $account, 
            "reputation" => $reputation, 
            "hostname" => $hostname, 
            "ip" => $ip, 
            "country_code" => $country_code, 
            "asn" => $asn, 
            "asname" => $asname, 
            "connected_since" => $connected_since, 
            "idle_since" => $idle_since,
            "idle" => $idle,
            "modes" => $modes,
            "channels" => ltrim($chans, ","),
            "security_groups" => ltrim($sg, ","),
            "away_reason" => $away_reason,
            "away_since" => $away_since
        ]);
    }
} catch (\PDOException $e) {
    $stmt = $pdo->prepare("DROP TABLE " . $config["mysql"]["table_prefix"] . "users");
    $stmt->execute();

    $statements = [
        'CREATE TABLE `' . $config["mysql"]["table_prefix"] . 'users` (
            `id` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
            `id_user` varchar(255) NOT NULL,
            `name` varchar(255) NOT NULL,
            `username` varchar(255) NOT NULL,
            `realname` varchar(255) NOT NULL,
            `vhost` varchar(255) NOT NULL,
            `account` varchar(255) NOT NULL,
            `reputation` varchar(255) NOT NULL,
            `hostname` varchar(255) NOT NULL,
            `ip` varchar(255) NOT NULL,
            `country_code` varchar(2) NOT NULL,
            `asn` varchar(10) NOT NULL,
            `asname` varchar(255) NOT NULL,
            `connected_since` varchar(255) NOT NULL,
            `idle_since` varchar(255) NOT NULL,
            `idle` varchar(255) NOT NULL,
            `modes` varchar(255) NOT NULL,
            `channels` TEXT NOT NULL,
            `security_groups` TEXT NOT NULL,
            `away_reason` TEXT NOT NULL,
            `away_since` TEXT NOT NULL
          ) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;'
    ];

    foreach ($statements as $statement) {
        $pdo->exec($statement);
    }
}

  /* unrealircd_top_countries */

  try {
    $result = $pdo->query("SELECT 1 FROM " . $config["mysql"]["table_prefix"] . "top_countries LIMIT 1");
} catch (\PDOException $e) {
    // We got an exception (table not found)
    $statements = [
        'CREATE TABLE `' . $config["mysql"]["table_prefix"] . 'top_countries` (
            `id` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
            `country_code` varchar(255) NOT NULL,
            `users` TEXT NOT NULL
          ) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;'
    ];

    foreach ($statements as $statement) {
        $pdo->exec($statement);
    }
}



$stmt = $pdo->prepare("TRUNCATE TABLE " . $config["mysql"]["table_prefix"] . "top_countries");
$stmt->execute();

$userFlags = array();
foreach($users as $user)
{
    if (isset($user->geoip->country_code))
    array_push($userFlags, $user->geoip->country_code);
}
$userFlags = array_count_values($userFlags);
arsort($userFlags);
foreach($userFlags as $country_code => $count){
    $prep = $pdo->prepare("INSERT INTO " . $config["mysql"]["table_prefix"] . "top_countries (id, country_code, users) 
    VALUES (:id, :country_code, :users)");
    $prep->execute([
        "id" => '',
        "country_code" => $country_code,
        "users" => $count
    ]);
}
