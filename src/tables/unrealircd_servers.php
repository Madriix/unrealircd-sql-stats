<?php
/* unrealircd_servers */

$servers = $rpc->server()->getAll();

try {
    $result = $pdo->query("SELECT 1 FROM " . $config["mysql"]["table_prefix"] . "servers LIMIT 1");
} catch (\PDOException $e) {
    // We got an exception (table not found)
    $line = "";
    foreach ($servers as $server) {
        // creating the table with the correct columns
        foreach ($server as $key => $value) {
            //$line .= ",";
            if ($key!="server")
            $line .= "`$key` TEXT NOT NULL,";
        }
        break;
    }
    
    $statements = [
        'CREATE TABLE `' . $config["mysql"]["table_prefix"] . 'servers` (
            '.rtrim($line, ",").'
          ) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;'
    ];

    foreach ($statements as $statement) {
        $pdo->exec($statement);
    }
}


/*
print_r($servers);
exit;
*/

$stmt = $pdo->prepare("TRUNCATE TABLE " . $config["mysql"]["table_prefix"] . "servers");
$stmt->execute();


foreach ($servers as $server) {
    $array1 = array();
    $array2 = array();
    $array3 = array();
    // creating the table with the correct columns
    foreach ($server as $key => $value) {
        if ($key!="server") {
            array_push($array1, $key);
            array_push($array2, ":".$key);
            $array3[$key] = "$value";
        }
    }
    $prep = $pdo->prepare("INSERT INTO " . $config["mysql"]["table_prefix"] . "servers (".ltrim(implode(", ",$array1), ",").") 
    VALUES (".ltrim(implode(",",$array2), ",").")");
    $prep->execute($array3);
}
