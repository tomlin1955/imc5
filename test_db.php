<?php
// 資料庫連線設定
$host     = '127.0.0.1'; // 請用 IP 取代 localhost，避免 Unix Socket 連線錯誤
$port     = '3306';
$dbname   = 'testdb';    // 依你在 docker-compose 或 docker run 設定的名稱
$username = 'root';
$password = 'rootpassword'; // 依你設定的密碼

// 建立資料來源名稱 (DSN)
$dsn = "mysql:host=$host;port=$port;dbname=$dbname;charset=utf8mb4";

try {
    // 建立 PDO 連線物件
