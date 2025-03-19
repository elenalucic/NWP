<?php

$pdo = new PDO("mysql:host=localhost;dbname=radovi", "root", "");

$backupFile = 'backup.txt';
$handle = fopen($backupFile, 'w');

$tables = $pdo->query("SHOW TABLES")->fetchAll(PDO::FETCH_COLUMN);

foreach ($tables as $table) {
    $rows = $pdo->query("SELECT * FROM $table")->fetchAll(PDO::FETCH_ASSOC);
    
    foreach ($rows as $row) {
        $columns = array_keys($row);
        $values = array_map(function($value) use ($pdo) {
            return $value === null ? 'NULL' : $pdo->quote($value);
        }, array_values($row));
        
        $sql = "INSERT INTO $table (" . implode(', ', $columns) . ") VALUES (" . implode(', ', $values) . ");\n";
        fwrite($handle, $sql);
    }
}

fclose($handle);

$zip = new ZipArchive();
$zipFile = 'backup.zip';

$zip->open($zipFile, ZipArchive::CREATE);
$zip->addFile($backupFile, basename($backupFile));
$zip->close();
?>