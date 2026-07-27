CREATE TABLE IF NOT EXISTS user_badges (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id BIGINT UNSIGNED NOT NULL,
    badge_key VARCHAR(50) NOT NULL,
    badge_name VARCHAR(100) NOT NULL,
    badge_icon VARCHAR(50) DEFAULT 'fa-trophy',
    badge_color VARCHAR(20) DEFAULT '#f59e0b',
    earned_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY uk_user_badge (user_id, badge_key),
    KEY idx_user_id (user_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS sla_alerts (
    id INT AUTO_INCREMENT PRIMARY KEY,
    report_id INT NOT NULL,
    alert_type ENUM('j2','j1','overdue') NOT NULL,
    sent_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    KEY idx_report_id (report_id),
    UNIQUE KEY uk_sla_once (report_id, alert_type)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS push_subscriptions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id BIGINT UNSIGNED NOT NULL,
    endpoint TEXT NOT NULL,
    p256dh_key VARCHAR(255),
    auth_key VARCHAR(255),
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    KEY idx_user_id (user_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
