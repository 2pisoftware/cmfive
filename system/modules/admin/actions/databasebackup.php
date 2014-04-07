<?php

// Using this as a guide: http://www.bendemeyer.com/2013/03/12/automated-site-backups-using-php-and-cron/
function databasebackup_ALL(Web $w) {
    global $MYSQL_USERNAME;
    global $MYSQL_PASSWORD;
    global $MYSQL_DB_NAME;
    
    AdminLib::navigation($w, "Database Backup");
    
    $datestamp = date("Y-m-d-H-i");
    $filedir = SYSTEM_PATH . "/install/backups/";
    
    $dir = new DirectoryIterator($filedir);
    foreach ($dir as $fileinfo) {
        if (!$fileinfo->isDot()) {
            $filename = $fileinfo->getFilename();
            try {
                $backuptime = DateTime::createFromFormat("Y-m-d-H-i\.\s\q\l", $filename);
                if ($backuptime) {
                    if (time() - $backuptime->getTimestamp() < (60*60*4)) {
                        $w->out("You cannot backup more than once every 4 hours");
                        return;
                    }
                }
            } catch (Exception $e) {
                // Invalid timestamp
            }
        }
    }
    
    $filename = "$datestamp.sql";
    if (!strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
        $w->out(shell_exec("mysqldump -u $MYSQL_USERNAME -p'$MYSQL_PASSWORD' $MYSQL_DB_NAME | gzip > {$filedir}{$filename}"));
    } else {
        // What to do with windows?
        // Either enter the path to mysqldump or create a shortcut thing
        // echo 'J:\xampp\mysql\bin\mysqldump.exe -u '.$MYSQL_USERNAME.' -p\''.$MYSQL_PASSWORD.'\' '.$MYSQL_DB_NAME.' > ' . "{$filedir}{$filename}";
        $w->out(exec('J:\xampp\mysql\bin\mysqldump.exe -u '.$MYSQL_USERNAME.' -p'.$MYSQL_PASSWORD.' ' . $MYSQL_DB_NAME . ' > ' . "{$filedir}{$filename}"));
    }
    $w->out("Backup completed to: {$filedir}{$filename}");
//    $w->out($response);
}
