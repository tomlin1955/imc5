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
    $pdo = new PDO($dsn, $username, $password, [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION, // 啟用錯誤例外
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,       // 預設傳回陣列
        PDO::ATTR_EMULATE_PREPARES   => false,                  // 停用模擬模擬預處理
    ]);

    echo "🎉 資料庫連線成功！<br>";

    // 測試：建立一個簡單的資料表並寫入資料
    $pdo->exec("CREATE TABLE IF NOT EXISTS users (id INT AUTO_INCREMENT PRIMARY KEY, name VARCHAR(50))");
    
    // 測試：寫入一筆資料
    $stmt = $pdo->prepare("INSERT INTO users (name) VALUES (:name)");
    $stmt->execute(['name' => 'GitHub Codespaces User']);
    
    // 測試：撈取資料
    $stmt = $pdo->query("SELECT * FROM users");
    $users = $stmt->fetchAll();

    echo "<h3>資料庫測試撈取結果：</h3>";
    echo "<pre>";
    print_r($users);
    echo "</pre>";

} catch (PDOException $e) {
    // 連線失敗時顯示錯誤訊息
    echo "❌ 資料庫連線失敗: " . $e->getMessage();
}
?>
