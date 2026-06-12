<?php
require_once 'config/database.php';

try {
    $sql = file_get_contents('database.sql');

    // Remote DBs normally don't let you run CREATE DATABASE
    // We'll strip the first two lines out
    $sql = preg_replace('/CREATE DATABASE IF NOT EXISTS blood_donation_system;/', '', $sql);
    $sql = preg_replace('/USE blood_donation_system;/', '', $sql);

    $pdo->exec($sql);
    echo "Database tables created successfully!";
} catch (PDOException $e) {
    echo "Error creating tables: " . $e->getMessage();
}
?>