<?php

function test_ALL(Web $w) {
    $p = $w->pathMatch("id");
    if (empty($p["id"])) {
        $w->error("No connection ID specified", "/report-connections");
    }
    
    $connection = $w->Report->getConnection($p["id"]);
    if (empty($connection->id)) {
        $w->error("Connection could not be found", "/report-connections");
    }
    
    $connection->decrypt();
    try {
        $dbo = $connection->getDb();
        echo "Connected to DB<br/>Fetching databases to test connection...<br/>";
        
        $results;
        switch ($connection->db_driver) {
            case "pgsql":
                $results = $dbo->sql("SELECT datname FROM pg_database")->fetch_all();
                break;
            case "mysql":
                $results = $dbo->sql("show databases")->fetch_all();
                break;
        }
        
        if (!empty($results)) {
            foreach(array_values($results) as $r) {
                echo "\t{$r[0]}<br/>";
            }
        } else {
            echo "No results found";
        }
    } catch (Exception $e) {
        echo $e->getMessage();
    }
    
}