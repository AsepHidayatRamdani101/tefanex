<?php

try {
    $pdo = new PDO('mysql:host=127.0.0.1;dbname=tefa_db', 'root', '');
    $stmt = $pdo->query('DESCRIBE payments');
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
    foreach ($rows as $row) {
        echo $row['Field'] . "\t" . $row['Type'] . PHP_EOL;
    }
} catch (Exception $e) {
    echo 'ERR: ' . $e->getMessage() . PHP_EOL;
}
