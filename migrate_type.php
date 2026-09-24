<?php
$host = "dev-data.tisi.go.th";
$db = "admin_dbtest";
$user = "co_dev2025";
$pass = "R$!3oH9b";

try {
    $dsn = "mysql:host=$host;dbname=$db;charset=utf8mb4";
    $pdo = new PDO($dsn, $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->exec("ALTER TABLE section5_application_labs_scope ADD COLUMN type INT DEFAULT NULL COMMENT '1=ขอบข่ายเดิม, 2=เพิ่ม, 3=ลด'");
    echo "Column type added successfully.\n";
} catch (PDOException $e) {
    if ($e->getCode() == '42S21') {
        echo "Column type already exists.\n";
    } else {
        echo "Database Error: " . $e->getMessage() . "\n";
    }
}
