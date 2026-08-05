<?php
header('Content-Type: application/json; charset=utf-8');

// 💡 配合你原本 index.html 的 api 路由判斷
$action = $_GET['action'] ?? '';
if ($action !== 'join') {
    echo json_encode(['success' => false, 'message' => '未知的 API 請求動作']);
    exit;
}

// 1. 手動解析 .env 檔案
if (file_exists(__DIR__ . '/.env')) {
    $lines = file(__DIR__ . '/.env', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        if (strpos(trim($line), '#') === 0) continue;
        list($name, $value) = explode('=', $line, 2);
        $_ENV[trim($name)] = trim($value);
    }
}

// 2. 連接資料庫
$host = $_ENV['DB_HOST'] ?? '127.0.0.1';
$port = $_ENV['DB_PORT'] ?? '3306';
$dbname = $_ENV['DB_DATABASE'] ?? 'testdb';
$username = $_ENV['DB_USERNAME'] ?? 'root';
$password = $_ENV['DB_PASSWORD'] ?? 'rootpassword';

try {
    $dsn = "mysql:host=$host;port=$port;dbname=$dbname;charset=utf8mb4";
    $pdo = new PDO($dsn, $username, $password, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
    ]);
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => '資料庫連線失敗']);
    exit;
}

// 3. 接收表單傳過來的文字欄位
$name      = $_POST['name'] ?? '';
$gender    = $_POST['gender'] ?? '';
$phone     = $_POST['phone'] ?? '';
$email     = $_POST['email'] ?? '';
$chapter   = $_POST['chapter'] ?? '';
$birthday  = !empty($_POST['birthday']) ? $_POST['birthday'] : null;
$company   = $_POST['company'] ?? '';
$job_title = $_POST['job_title'] ?? '';

// 基本防呆驗證
if (empty($name) || empty($gender) || empty($phone) || empty($email) || empty($chapter) || empty($company) || empty($job_title)) {
    echo json_encode(['success' => false, 'message' => '請填寫所有必填欄位 (*)']);
    exit;
}

// 4. 處理附件檔案上傳 (名片/簡介)
$attachment_path = null;
if (isset($_FILES['attachment']) && $_FILES['attachment']['error'] === UPLOAD_ERR_OK) {
    $upload_dir = __DIR__ . '/uploads/';
    if (!is_dir($upload_dir)) {
        mkdir($upload_dir, 0755, true);
    }
    
    $file_extension = pathinfo($_FILES['attachment']['name'], PATHINFO_EXTENSION);
    $new_file_name = date('Ymd_His') . '_' . uniqid() . '.' . $file_extension;
    $target_file = $upload_dir . $new_file_name;

    if (move_uploaded_file($_FILES['attachment']['tmp_name'], $target_file)) {
        $attachment_path = 'uploads/' . $new_file_name;
    }
}

// 5. 檢查手機或 Email 是否重複註冊
$stmt = $pdo->prepare("SELECT id FROM imc_members WHERE phone = ? OR email = ?");
$stmt->execute([$phone, $email]);
if ($stmt->fetch()) {
    echo json_encode(['success' => false, 'message' => '此手機號碼或電子郵件已申請過入會']);
    exit;
}

// 6. 寫入資料庫
try {
    $sql = "INSERT INTO imc_members (name, gender, phone, email, chapter, birthday, company, job_title, attachment_path) 
            VALUES (:name, :gender, :phone, :email, :chapter, :birthday, :company, :job_title, :attachment_path)";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        ':name'            => $name,
        ':gender'          => $gender,
        ':phone'           => $phone,
        ':email'           => $email,
        ':chapter'         => $chapter,
        ':birthday'        => $birthday,
        ':company'         => $company,
        ':job_title'       => $job_title,
        ':attachment_path' => $attachment_path
    ]);

    echo json_encode(['success' => true, 'message' => '入會申請已成功送出，請靜候分社審核通知！']);
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => '系統寫入失敗，請聯絡系統管理員']);
}
?>