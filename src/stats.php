<?php
/*
    The idea is to run a cron job every 1-5 minutes:
    php /home/folder/unrealircd-sql-stats/src/stats.php

    This way, it would be possible to display the desired statistics on the websites.
*/

require_once "config.php";

require dirname(__DIR__) . '/vendor/autoload.php';

use UnrealIRCd\Connection;

$api_login = $config["unrealircd"]["rpc_user"].":".$config["unrealircd"]["rpc_password"]; // same as in the rpc-user block in UnrealIRCd

$rpc = new UnrealIRCd\Connection("wss://".$config["unrealircd"]["host"].":".$config["unrealircd"]["port"]."/",
                    $api_login,
                    Array("tls_verify"=>$config["unrealircd"]["tls_verify_cert"]));

require_once "sql/connection.php";
require_once "tables/unrealircd_users.php";
require_once "tables/unrealircd_channels.php";
require_once "tables/unrealircd_spamfilter.php";
require_once "tables/unrealircd_servers.php";
require_once "tables/unrealircd_name_bans.php";

