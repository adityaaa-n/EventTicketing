<?php

$host = "db.seufwkortmywrvkuyglj.supabase.co";
$port = "5432";
$user = "postgres";
// PERHATIAN: Masukkan password database Supabase Anda di bawah ini
$pass = "tabahmuhamad2"; 
$db   = "postgres"; 

$dsn = "pgsql:host=$host;port=$port;dbname=$db;sslmode=require";

try {
    $conn = new PDO($dsn, $user, $pass);
    // Set error mode to exception
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Koneksi Database Gagal: " . $e->getMessage());
}
?>