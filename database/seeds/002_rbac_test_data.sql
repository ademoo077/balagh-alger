-- =====================================================
-- BALAGH ALGER - Seed 2: RBAC Test Data
-- Uses actual daira/commune IDs
-- =====================================================
SET NAMES utf8mb4;

SET @pwd = '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi';

-- =====================================================
-- UPDATE ROLE HIERARCHY
-- =====================================================
UPDATE `roles` SET `level` = 6 WHERE `name` = 'resp_central';

-- =====================================================
-- ADDITIONAL PERMISSIONS
-- =====================================================
INSERT IGNORE INTO `permissions` (`name`, `label`, `module`, `action`) VALUES
('reports.reassign', 'Réaffecter un signalement', 'reports', 'reassign'),
('reports.redirect', 'Rediriger vers un autre organisme', 'reports', 'redirect'),
('users.manage_org', 'Gérer les utilisateurs de l''organisme', 'users', 'manage_org'),
('reports.view_org', 'Voir les signalements de l''organisme', 'reports', 'view_org');

INSERT INTO `role_permissions` (`role_id`, `permission_id`)
SELECT r.id, p.id FROM roles r, permissions p
WHERE r.name = 'admin_central' AND p.name IN ('reports.reassign', 'reports.redirect', 'users.manage_org', 'reports.view_org')
AND NOT EXISTS (SELECT 1 FROM role_permissions rp WHERE rp.role_id = r.id AND rp.permission_id = p.id);

INSERT INTO `role_permissions` (`role_id`, `permission_id`)
SELECT r.id, p.id FROM roles r, permissions p
WHERE r.name = 'resp_central' AND p.name IN ('reports.reassign', 'reports.redirect', 'users.manage_org', 'reports.view_org')
AND NOT EXISTS (SELECT 1 FROM role_permissions rp WHERE rp.role_id = r.id AND rp.permission_id = p.id);

INSERT INTO `role_permissions` (`role_id`, `permission_id`)
SELECT r.id, p.id FROM roles r, permissions p
WHERE r.name = 'chef_unite' AND p.name IN ('users.manage_org', 'reports.view_org')
AND NOT EXISTS (SELECT 1 FROM role_permissions rp WHERE rp.role_id = r.id AND rp.permission_id = p.id);

-- =====================================================
-- DTP USERS (Org: DTP, Daira: Dar El Beïda id=7)
-- DTP resp_central is at Dar El Beïda
-- =====================================================
-- Update existing resp.dtp user to correct daira
UPDATE users SET daira_id = 7, organization_id = (SELECT id FROM organizations WHERE slug='dtp')
WHERE email = 'resp.dtp@balagh-alger.dz';

UPDATE users SET daira_id = 7, organization_id = (SELECT id FROM organizations WHERE slug='dtp')
WHERE email = 'chef.dtp.baraki@balagh-alger.dz';

UPDATE users SET daira_id = 7, organization_id = (SELECT id FROM organizations WHERE slug='dtp'), section = 'Voirie'
WHERE email = 'chefsec.dtp.baraki@balagh-alger.dz';

UPDATE users SET daira_id = 7, organization_id = (SELECT id FROM organizations WHERE slug='dtp'), section = 'Voirie'
WHERE email = 'agent.dtp.baraki@balagh-alger.dz';

-- =====================================================
-- SEAAL USERS (Org: SEAAL, Daira: Draria id=8)
-- =====================================================
UPDATE users SET daira_id = 8, organization_id = (SELECT id FROM organizations WHERE slug='seaal')
WHERE email = 'resp.seaal@balagh-alger.dz';

UPDATE users SET daira_id = 8, organization_id = (SELECT id FROM organizations WHERE slug='seaal'), section = 'Eau'
WHERE email = 'agent.seaal.bir@balagh-alger.dz';

-- =====================================================
-- ASROUT USERS (Org: ASROUT, Daira: Bab El Oued id=1)
-- =====================================================
UPDATE users SET daira_id = 1, organization_id = (SELECT id FROM organizations WHERE slug='asROUT')
WHERE email = 'resp.asrouter@balagh-alger.dz';

UPDATE users SET daira_id = 1, organization_id = (SELECT id FROM organizations WHERE slug='asROUT')
WHERE email = 'chef.asr.bo@balagh-alger.dz';

UPDATE users SET daira_id = 1, organization_id = (SELECT id FROM organizations WHERE slug='asROUT'), section = 'Technique'
WHERE email = 'chefsec.asr.bo@balagh-alger.dz';

UPDATE users SET daira_id = 1, organization_id = (SELECT id FROM organizations WHERE slug='asROUT'), section = 'Technique'
WHERE email = 'agent.asr.bo@balagh-alger.dz';

UPDATE users SET daira_id = 1, organization_id = (SELECT id FROM organizations WHERE slug='asROUT'), section = 'Technique'
WHERE email = 'karim@balagh-alger.dz';

-- =====================================================
-- SONELGAZ USERS (Org: SONELGAZ, Daira: Hussein Dey id=10)
-- =====================================================
UPDATE users SET daira_id = 10, organization_id = (SELECT id FROM organizations WHERE slug='sonelgaz')
WHERE email = 'resp.sonelgaz@balagh-alger.dz';

UPDATE users SET daira_id = 10, organization_id = (SELECT id FROM organizations WHERE slug='sonelgaz')
WHERE email = 'chef.son.bmr@balagh-alger.dz';

UPDATE users SET daira_id = 10, organization_id = (SELECT id FROM organizations WHERE slug='sonelgaz'), section = 'Eclairage'
WHERE email = 'agent.son.bmr@balagh-alger.dz';

-- =====================================================
-- ENSURE ROLE ASSIGNMENTS EXIST
-- =====================================================
-- ASROUT
INSERT IGNORE INTO `user_roles` SELECT u.id, r.id, 1, NOW()
FROM users u, roles r WHERE u.email = 'resp.asrouter@balagh-alger.dz' AND r.name = 'resp_central';
INSERT IGNORE INTO `user_roles` SELECT u.id, r.id, 1, NOW()
FROM users u, roles r WHERE u.email = 'chef.asr.bo@balagh-alger.dz' AND r.name = 'chef_unite';
INSERT IGNORE INTO `user_roles` SELECT u.id, r.id, 1, NOW()
FROM users u, roles r WHERE u.email = 'chefsec.asr.bo@balagh-alger.dz' AND r.name = 'chef_section';
INSERT IGNORE INTO `user_roles` SELECT u.id, r.id, 1, NOW()
FROM users u, roles r WHERE u.email = 'agent.asr.bo@balagh-alger.dz' AND r.name = 'intervenant';
INSERT IGNORE INTO `user_roles` SELECT u.id, r.id, 1, NOW()
FROM users u, roles r WHERE u.email = 'karim@balagh-alger.dz' AND r.name = 'intervenant';

-- SONELGAZ
INSERT IGNORE INTO `user_roles` SELECT u.id, r.id, 1, NOW()
FROM users u, roles r WHERE u.email = 'resp.sonelgaz@balagh-alger.dz' AND r.name = 'resp_central';
INSERT IGNORE INTO `user_roles` SELECT u.id, r.id, 1, NOW()
FROM users u, roles r WHERE u.email = 'chef.son.bmr@balagh-alger.dz' AND r.name = 'chef_unite';
INSERT IGNORE INTO `user_roles` SELECT u.id, r.id, 1, NOW()
FROM users u, roles r WHERE u.email = 'agent.son.bmr@balagh-alger.dz' AND r.name = 'intervenant';

-- SEAAL
INSERT IGNORE INTO `user_roles` SELECT u.id, r.id, 1, NOW()
FROM users u, roles r WHERE u.email = 'resp.seaal@balagh-alger.dz' AND r.name = 'resp_central';
INSERT IGNORE INTO `user_roles` SELECT u.id, r.id, 1, NOW()
FROM users u, roles r WHERE u.email = 'agent.seaal.bir@balagh-alger.dz' AND r.name = 'intervenant';

-- DTP
INSERT IGNORE INTO `user_roles` SELECT u.id, r.id, 1, NOW()
FROM users u, roles r WHERE u.email = 'resp.dtp@balagh-alger.dz' AND r.name = 'resp_central';
INSERT IGNORE INTO `user_roles` SELECT u.id, r.id, 1, NOW()
FROM users u, roles r WHERE u.email = 'chef.dtp.baraki@balagh-alger.dz' AND r.name = 'chef_unite';
INSERT IGNORE INTO `user_roles` SELECT u.id, r.id, 1, NOW()
FROM users u, roles r WHERE u.email = 'chefsec.dtp.baraki@balagh-alger.dz' AND r.name = 'chef_section';
INSERT IGNORE INTO `user_roles` SELECT u.id, r.id, 1, NOW()
FROM users u, roles r WHERE u.email = 'agent.dtp.baraki@balagh-alger.dz' AND r.name = 'intervenant';

-- =====================================================
-- TEST REPORTS (DTP: Bab El Oued daira=1, SONELGAZ: Hussein Dey daira=10, SEAAL: Draria daira=8)
-- =====================================================

-- DTP report in Bab El Oued (daira_id=1, commune: Bab El Oued id=1)
INSERT IGNORE INTO `reports` (tracking_code, title, description, category_id, subcategory_id, priority, status, daira_id, commune_id, address, latitude, longitude, citizen_name, citizen_phone, citizen_id, organization_id, workflow_step, assigned_to, assigned_at_central, created_at)
SELECT 'BA-2026-000010', 'Nid-de-poule rue Didouche Mourad', 'Grand nid-de-poule dangereux dans la chaussée',
    (SELECT id FROM categories WHERE slug='dtp-travaux'), (SELECT id FROM subcategories WHERE slug='nid-de-poule' AND category_id=(SELECT id FROM categories WHERE slug='dtp-travaux')),
    'high', 'assigned', 1, 1,
    'Rue Didouche Mourad, Bab El Oued', 36.7900, 3.0500, 'Ahmed Ben Salah', '0555123456',
    (SELECT id FROM users WHERE email='technique.motos@gmail.com'),
    (SELECT id FROM organizations WHERE slug='dtp'), 1,
    (SELECT id FROM users WHERE email='resp.dtp@balagh-alger.dz'),
    NOW(), DATE_SUB(NOW(), INTERVAL 3 DAY);

-- DTP report: Trottoir endommagé (daira_id=1)
INSERT IGNORE INTO `reports` (tracking_code, title, description, category_id, subcategory_id, priority, status, daira_id, commune_id, address, latitude, longitude, citizen_name, citizen_phone, citizen_id, organization_id, workflow_step, created_at)
SELECT 'BA-2026-000011', 'Trottoir endommagé Bab El Oued', 'Trottoir effondré devant l''école',
    (SELECT id FROM categories WHERE slug='dtp-travaux'), (SELECT id FROM subcategories WHERE slug='trottoir-endommage' AND category_id=(SELECT id FROM categories WHERE slug='dtp-travaux')),
    'medium', 'submitted', 1, 1,
    'Avenue principale Bab El Oued', 36.7900, 3.0510, 'Sara Khelifi', '0555234567',
    (SELECT id FROM users WHERE email='technique.motos@gmail.com'),
    (SELECT id FROM organizations WHERE slug='dtp'), 0,
    DATE_SUB(NOW(), INTERVAL 2 DAY);

-- SONELGAZ report in Hussein Dey (daira_id=10, commune: Hussein Dey id=43)
INSERT IGNORE INTO `reports` (tracking_code, title, description, category_id, subcategory_id, priority, status, daira_id, commune_id, address, latitude, longitude, citizen_name, citizen_phone, citizen_id, organization_id, workflow_step, created_at)
SELECT 'BA-2026-000012', 'Lampe éteinte Hussein Dey', 'Plusieurs lampes éteintes sur le boulevard',
    (SELECT id FROM categories WHERE slug='sonelgaz-electricite'), (SELECT id FROM subcategories WHERE slug='lampe-eteinte' AND category_id=(SELECT id FROM categories WHERE slug='sonelgaz-electricite')),
    'medium', 'submitted', 10, 43,
    'Boulevard Krim Belkacem, Hussein Dey', 36.7400, 3.1000, 'Sara Khelifi', '0555234567',
    (SELECT id FROM users WHERE email='technique.motos@gmail.com'),
    (SELECT id FROM organizations WHERE slug='sonelgaz'), 0,
    DATE_SUB(NOW(), INTERVAL 2 DAY);

-- SEAAL report in Draria (daira_id=8, commune: Draria id=35)
INSERT IGNORE INTO `reports` (tracking_code, title, description, category_id, subcategory_id, priority, status, daira_id, commune_id, address, latitude, longitude, citizen_name, citizen_phone, citizen_id, organization_id, workflow_step, created_at)
SELECT 'BA-2026-000013', 'Fuite d''eau Draria', 'Fuite importante devant le centre commercial',
    (SELECT id FROM categories WHERE slug='seaal-eau'), (SELECT id FROM subcategories WHERE slug='fuite-eau' AND category_id=(SELECT id FROM categories WHERE slug='seaal-eau')),
    'high', 'in_progress', 8, 35,
    'Avenue de la République, Draria', 36.7330, 2.9840, 'Youcef Amrani', '0555345678',
    (SELECT id FROM users WHERE email='technique.motos@gmail.com'),
    (SELECT id FROM organizations WHERE slug='seaal'), 4,
    DATE_SUB(NOW(), INTERVAL 12 HOUR);
