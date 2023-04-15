<?php
/* unrealircd_spamfilter */

$spamfilter = $rpc->spamfilter()->getAll();

try {
    $result = $pdo->query("SELECT 1 FROM " . $config["mysql"]["table_prefix"] . "spamfilter LIMIT 1");
} catch (\PDOException $e) {
    // We got an exception (table not found)

    $line = "";
    foreach ($spamfilter as $spam_filter) {
        // creating the table with the correct columns
        foreach ($spam_filter as $key => $value) {
            $line .= ",";
            $line .= "`$key` TEXT NOT NULL";
        }
        break;

    }
    $statements = [
        'CREATE TABLE `' . $config["mysql"]["table_prefix"] . 'spamfilter` (
            `id` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY
            '.$line.'
          ) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;'
    ];

    foreach ($statements as $statement) {
        $pdo->exec($statement);
    }
}


/*
print_r($spamfilter);
exit;
*/

$stmt = $pdo->prepare("TRUNCATE TABLE " . $config["mysql"]["table_prefix"] . "spamfilter");
$stmt->execute();

try {
    foreach ($spamfilter as $spam_filter) {
        $array1 = array();
        $array2 = array();
        $array3 = array();
        $array3["id"] = "";
        // creating the table with the correct columns
        foreach ($spam_filter as $key => $value) {
            array_push($array1, $key);
            array_push($array2, ":".$key);
            $array3[$key] = "$value";
        }
        $prep = $pdo->prepare("INSERT INTO " . $config["mysql"]["table_prefix"] . "spamfilter (id, ".implode(", ",$array1).") 
        VALUES (:id, ".implode(", ",$array2).")");
        $prep->execute($array3);
    }
} catch (\PDOException $e) {
    $stmt = $pdo->prepare("DROP TABLE " . $config["mysql"]["table_prefix"] . "spamfilter");
    $stmt->execute();

    $line = "";
    foreach ($spamfilter as $spam_filter) {
        // creating the table with the correct columns
        foreach ($spam_filter as $key => $value) {
            $line .= ",";
            $line .= "`$key` TEXT NOT NULL";
        }
        break;

    }
    $statements = [
        'CREATE TABLE `' . $config["mysql"]["table_prefix"] . 'spamfilter` (
            `id` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY
            '.$line.'
          ) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;'
    ];

    foreach ($statements as $statement) {
        $pdo->exec($statement);
    }
}