<?php
/* unrealircd_name_bans */

$name_bans = $rpc->nameban()->getAll();

try {
    $result = $pdo->query("SELECT 1 FROM " . $config["mysql"]["table_prefix"] . "name_bans LIMIT 1");
} catch (\PDOException $e) {
    // We got an exception (table not found)
    $line = "";
    foreach ($name_bans as $namebans) {
        // creating the table with the correct columns
        foreach ($namebans as $key => $value) {
            if ($key != "set_in_config") {
                $line .= ",";
                $line .= "`$key` TEXT NOT NULL";
            }

        }
        break;

    }
    $statements = [
        'CREATE TABLE `' . $config["mysql"]["table_prefix"] . 'name_bans` (
            `id` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY
            '.rtrim($line, ",").'
          ) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;'
    ];

    foreach ($statements as $statement) {
        $pdo->exec($statement);
    }
}


/*
print_r($name_bans);
exit;
*/

$stmt = $pdo->prepare("TRUNCATE TABLE " . $config["mysql"]["table_prefix"] . "name_bans");
$stmt->execute();

try {
    foreach ($name_bans as $namebans) {
        $array1 = array();
        $array2 = array();
        $array3 = array();
        $array3["id"] = "";
        // creating the table with the correct columns
        foreach ($namebans as $key => $value) {
            if ($key != "set_in_config") {
                array_push($array1, $key);
                array_push($array2, ":".$key);
                $array3[$key] = "$value";
            }
        }
        $prep = $pdo->prepare("INSERT INTO " . $config["mysql"]["table_prefix"] . "name_bans (id, ".implode(", ",$array1).") 
        VALUES (:id, ".implode(", ",$array2).")");
        $prep->execute($array3);
    }
} catch (\PDOException $e) {
    $stmt = $pdo->prepare("DROP TABLE " . $config["mysql"]["table_prefix"] . "name_bans");
    $stmt->execute();

    $line = "";
    foreach ($name_bans as $namebans) {
        // creating the table with the correct columns
        foreach ($namebans as $key => $value) {
            if ($key != "set_in_config") {
                $line .= ",";
                $line .= "`$key` TEXT NOT NULL";
            }

        }
        break;

    }
    $statements = [
        'CREATE TABLE `' . $config["mysql"]["table_prefix"] . 'name_bans` (
            `id` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY
            '.rtrim($line, ",").'
          ) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;'
    ];

    foreach ($statements as $statement) {
        $pdo->exec($statement);
    }
}