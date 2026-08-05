CREATE TABLE IF NOT EXISTS imc_members (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(50) NOT NULL COMMENT '中文姓名',
    gender ENUM('male', 'female') NOT NULL COMMENT '性別',
    phone VARCHAR(20) NOT NULL UNIQUE COMMENT '行動電話',
    email VARCHAR(100) NOT NULL UNIQUE COMMENT '電子郵件',
    chapter VARCHAR(50) NOT NULL COMMENT '加入分社',
    birthday DATE NULL COMMENT '出生年月日',
    company VARCHAR(150) NOT NULL COMMENT '服務企業名稱',
    job_title VARCHAR(100) NOT NULL COMMENT '職稱',
    attachment_path VARCHAR(255) NULL COMMENT '名片附件上傳路徑',
    status VARCHAR(20) DEFAULT 'pending' COMMENT '審核狀態(pending/approved/rejected)',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP COMMENT '申請時間',
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;