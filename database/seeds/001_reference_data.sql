-- =====================================================
-- BALAGH ALGER - Seeds: Rôles, Permissions, Organismes, Daïras, Communes, Catégories
-- =====================================================

SET NAMES utf8mb4;

-- =====================================================
-- RÔLES
-- =====================================================
INSERT INTO `roles` (`name`, `label`, `description`, `level`) VALUES
('citizen', 'Citoyen', 'Citoyen utilisant la plateforme pour signaler des anomalies', 1),
('intervenant', 'Intervenant', 'Agent technique chargé du traitement des signalements', 2),
('chef_section', 'Chef de Section', 'Chef d''une section au sein d''un organisme', 3),
('chef_unite', 'Chef d''Unité', 'Chef d''une unité gérant plusieurs sections', 4),
('admin_local', 'Administrateur Local', 'Administrateur d''un organisme', 5),
('admin_central', 'Administrateur Central', 'Administrateur central de la Wilaya d''Alger', 6);

-- =====================================================
-- PERMISSIONS
-- =====================================================
INSERT INTO `permissions` (`name`, `label`, `module`, `action`) VALUES
-- Reports
('reports.view', 'Voir les signalements', 'reports', 'view'),
('reports.create', 'Créer un signalement', 'reports', 'create'),
('reports.update', 'Modifier un signalement', 'reports', 'update'),
('reports.delete', 'Supprimer un signalement', 'reports', 'delete'),
('reports.assign', 'Assigner un signalement', 'reports', 'assign'),
('reports.resolve', 'Résoudre un signalement', 'reports', 'resolve'),
('reports.comment', 'Commenter un signalement', 'reports', 'comment'),
('reports.export', 'Exporter les signalements', 'reports', 'export'),
('reports.view_all', 'Voir tous les signalements', 'reports', 'view_all'),
('reports.view_assigned', 'Voir les signalements assignés', 'reports', 'view_assigned'),
-- Users
('users.view', 'Voir les utilisateurs', 'users', 'view'),
('users.create', 'Créer un utilisateur', 'users', 'create'),
('users.update', 'Modifier un utilisateur', 'users', 'update'),
('users.delete', 'Supprimer un utilisateur', 'users', 'delete'),
('users.suspend', 'Suspendre un utilisateur', 'users', 'suspend'),
-- Organizations
('organizations.view', 'Voir les organismes', 'organizations', 'view'),
('organizations.create', 'Créer un organisme', 'organizations', 'create'),
('organizations.update', 'Modifier un organisme', 'organizations', 'update'),
('organizations.delete', 'Supprimer un organisme', 'organizations', 'delete'),
-- Dairas & Communes
('dairas.view', 'Voir les daïras', 'dairas', 'view'),
('dairas.manage', 'Gérer les daïras', 'dairas', 'manage'),
-- Categories
('categories.view', 'Voir les catégories', 'categories', 'view'),
('categories.manage', 'Gérer les catégories', 'categories', 'manage'),
-- Dashboard
('dashboard.view', 'Voir le tableau de bord', 'dashboard', 'view'),
('dashboard.stats', 'Voir les statistiques', 'dashboard', 'stats'),
-- Settings
('settings.view', 'Voir les paramètres', 'settings', 'view'),
('settings.update', 'Modifier les paramètres', 'settings', 'update'),
-- Audit
('audit.view', 'Voir le journal d''audit', 'audit', 'view'),
-- Notifications
('notifications.view', 'Voir les notifications', 'notifications', 'view'),
('notifications.manage', 'Gérer les notifications', 'notifications', 'manage');

-- =====================================================
-- RÔLE-PERMISSIONS
-- =====================================================

-- Citoyen
INSERT INTO `role_permissions` (`role_id`, `permission_id`)
SELECT r.id, p.id FROM roles r, permissions p
WHERE r.name = 'citizen' AND p.name IN ('reports.create', 'reports.view_assigned', 'reports.comment', 'dashboard.view', 'notifications.view');

-- Intervenant
INSERT INTO `role_permissions` (`role_id`, `permission_id`)
SELECT r.id, p.id FROM roles r, permissions p
WHERE r.name = 'intervenant' AND p.name IN ('reports.view', 'reports.view_assigned', 'reports.update', 'reports.resolve', 'reports.comment', 'dashboard.view', 'dashboard.stats', 'notifications.view');

-- Chef de Section
INSERT INTO `role_permissions` (`role_id`, `permission_id`)
SELECT r.id, p.id FROM roles r, permissions p
WHERE r.name = 'chef_section' AND p.name IN ('reports.view', 'reports.view_all', 'reports.update', 'reports.assign', 'reports.resolve', 'reports.comment', 'reports.export', 'users.view', 'dashboard.view', 'dashboard.stats', 'notifications.view', 'notifications.manage');

-- Chef d'Unité
INSERT INTO `role_permissions` (`role_id`, `permission_id`)
SELECT r.id, p.id FROM roles r, permissions p
WHERE r.name = 'chef_unite' AND p.name IN ('reports.view', 'reports.view_all', 'reports.update', 'reports.assign', 'reports.resolve', 'reports.comment', 'reports.export', 'users.view', 'users.create', 'users.update', 'dashboard.view', 'dashboard.stats', 'notifications.view', 'notifications.manage', 'organizations.view');

-- Admin Local
INSERT INTO `role_permissions` (`role_id`, `permission_id`)
SELECT r.id, p.id FROM roles r, permissions p
WHERE r.name = 'admin_local' AND p.name IN ('reports.view', 'reports.view_all', 'reports.update', 'reports.assign', 'reports.resolve', 'reports.comment', 'reports.export', 'reports.delete', 'users.view', 'users.create', 'users.update', 'users.suspend', 'organizations.view', 'organizations.update', 'dashboard.view', 'dashboard.stats', 'categories.view', 'categories.manage', 'notifications.view', 'notifications.manage', 'audit.view', 'settings.view', 'settings.update');

-- Admin Central: toutes les permissions
INSERT IGNORE INTO `role_permissions` (`role_id`, `permission_id`)
SELECT r.id, p.id FROM roles r, permissions p
WHERE r.name = 'admin_central';

-- =====================================================
-- ORGANISMES
-- =====================================================
INSERT INTO `organizations` (`name`, `name_ar`, `slug`, `code`, `address`, `phone`, `email`, `description`) VALUES
('NETCOM', 'نتكوم', 'netcom', 'NETCOM', 'Alger', NULL, NULL, 'Entreprise Nationale de Télécommunications'),
('ASROUT', 'اسرتوت', 'asROUT', 'ASROUT', 'Alger', NULL, NULL, 'Algérie Telecom Routage'),
('HUPE', 'هيب', 'hupe', 'HUPE', 'Alger', NULL, NULL, 'Hygiène et Propreté de l''Environnement'),
('EXTRANET', 'اكسترانيت', 'extranet', 'EXTRANET', 'Alger', NULL, NULL, 'Réseau Extranet'),
('ERMA', 'إرم', 'erma', 'ERMA', 'Alger', NULL, NULL, 'Entreprise de Recyclage et Management Ambiental'),
('EMCU', 'إم سي يو', 'emcu', 'EMCU', 'Alger', NULL, NULL, 'Entreprise de Maintenance et de Construction Urbaine'),
('EGCTU', 'إج سي تي يو', 'egctu', 'EGCTU', 'Alger', NULL, NULL, 'Entreprise de Gestion de la Construction et des Travaux Urbains'),
('EGPFC', 'إج بي إف سي', 'egpfc', 'EGPFC', 'Alger', NULL, NULL, 'Entreprise de Gestion des Parcs et Forêts et de la Commune'),
('SEAAL', 'سيال', 'seaal', 'SEAAL', 'Alger', NULL, NULL, 'Société de l''Eau et de l''Assainissement d''Alger'),
('SONELGAZ', 'سونلغاز', 'sonelgaz', 'SONELGAZ', 'Alger', NULL, NULL, 'Société Nationale de l''Electricité et du Gaz'),
('Commune', 'البلدية', 'commune', 'COMMUNE', 'Alger', NULL, NULL, 'Service communal'),
('DTP', 'دي تي بي', 'dtp', 'DTP', 'Alger', NULL, NULL, 'Direction des Travaux Publics'),
('DRE', 'دي آر إي', 'dre', 'DRE', 'Alger', NULL, NULL, 'Direction Régionale de l''Environnement'),
('DEPTIC', 'دي بي تي سي', 'deptic', 'DEPTIC', 'Alger', NULL, NULL, 'Direction de l''Équipement Public, des Travaux Immobiliers et de la Construction'),
('GESTIMMO', 'جيستيم', 'gestimmo', 'GESTIMMO', 'Alger', NULL, NULL, 'Gestion Immobilière'),
('EDEVAL', 'إي دي فل', 'edeval', 'EDEVAL', 'Alger', NULL, NULL, 'Entreprise de Distribution de l''Eau Valve'),
('OPGI', 'أوجي', 'opgi', 'OPGI', 'Alger', NULL, NULL, 'Office Public de Gestion Immobilière'),
('OPLA', 'أوبيلا', 'opla', 'OPLA', 'Alger', NULL, NULL, 'Office Public Local d''Habitation'),
('VNSA', 'في أن إس أ', 'vnsa', 'VNSA', 'Alger', NULL, NULL, 'Voirie Non Classée et Services Annexes'),
('Direction de la Pêche', 'مديرية الصيد البحري', 'peche', 'PECHE', 'Alger', NULL, NULL, 'Direction de la Pêche et des Ressources Halieutiques'),
('Circonscription Administrative', 'التقطيعة الإدارية', 'circonscription', 'CIRCO', 'Alger', NULL, NULL, 'Circonscription Administrative');

-- =====================================================
-- CATÉGORIES
-- =====================================================
INSERT INTO `categories` (`name`, `name_ar`, `slug`, `icon`, `color`, `organization_id`, `sort_order`) VALUES
('Voirie et Routes', 'الطرقات والطرق', 'voirie', 'fas fa-road', '#e74c3c', (SELECT id FROM organizations WHERE slug='dtp'), 1),
('Éclairage Public', 'الإنارة العمومية', 'eclairage', 'fas fa-lightbulb', '#f39c12', (SELECT id FROM organizations WHERE slug='sonelgaz'), 2),
('Eau et Assainissement', 'المياه والصرف الصحي', 'eau', 'fas fa-tint', '#3498db', (SELECT id FROM organizations WHERE slug='seaal'), 3),
('Propreté et Hygiène', 'النظافة والصحة', 'proprete', 'fas fa-broom', '#27ae60', (SELECT id FROM organizations WHERE slug='hupe'), 4),
('Éducation', 'التربية والتعليم', 'education', 'fas fa-graduation-cap', '#9b59b6', NULL, 5),
('Santé', 'الصحة', 'sante', 'fas fa-heartbeat', '#e91e63', NULL, 6),
('Sécurité', 'الأمن', 'securite', 'fas fa-shield-alt', '#34495e', NULL, 7),
('Environnement', 'البيئة', 'environnement', 'fas fa-leaf', '#2ecc71', (SELECT id FROM organizations WHERE slug='dre'), 8),
('Transport', 'النقل', 'transport', 'fas fa-bus', '#1abc9c', NULL, 9),
('Bâtiments Publics', 'المباني العامة', 'batiments', 'fas fa-building', '#95a5a6', (SELECT id FROM organizations WHERE slug='egctu'), 10),
('Équipements Communaux', 'المعدات البلدية', 'equipements', 'fas fa-tools', '#e67e22', (SELECT id FROM organizations WHERE slug='commune'), 11),
('Télécommunications', 'الاتصالات', 'telecom', 'fas fa-wifi', '#2980b9', (SELECT id FROM organizations WHERE slug='netcom'), 12),
('Gestion Immobilière', 'التسيير العقاري', 'immobilier', 'fas fa-home', '#8e44ad', (SELECT id FROM organizations WHERE slug='gestimmo'), 13),
('Espace Vert', 'المساحات الخضراء', 'espace-vert', 'fas fa-tree', '#27ae60', NULL, 14),
('Autres', 'أخرى', 'autres', 'fas fa-ellipsis-h', '#95a5a6', NULL, 99);

-- =====================================================
-- SOUS-CATÉGORIES
-- =====================================================
INSERT INTO `subcategories` (`category_id`, `name`, `name_ar`, `slug`, `sort_order`) VALUES
-- Voirie
((SELECT id FROM categories WHERE slug='voirie'), 'Nid-de-poule', 'حفر', 'nid-de-poule', 1),
((SELECT id FROM categories WHERE slug='voirie'), 'Dégât de chaussée', 'تلف الطريق', 'degat-chaussee', 2),
((SELECT id FROM categories WHERE slug='voirie'), 'Trottoir endommagé', 'تضرر الرصيف', 'trottoir', 3),
((SELECT id FROM categories WHERE slug='voirie'), 'Signalisation défaillante', '_failure de signalisation', 'signalisation', 4),
((SELECT id FROM categories WHERE slug='voirie'), 'Panne de signalétique', 'لافتة معطلة', 'panneau', 5),
-- Éclairage
((SELECT id FROM categories WHERE slug='eclairage'), 'Lampe éteinte', 'مصبة مطفأة', 'lampe-eteinte', 1),
((SELECT id FROM categories WHERE slug='eclairage'), 'Lampe clignotante', 'مصبة وامضة', 'lampe-clignotante', 2),
((SELECT id FROM categories WHERE slug='eclairage'), 'Poteau endommagé', 'عمود متضرر', 'poteau', 3),
((SELECT id FROM categories WHERE slug='eclairage'), 'Câble électrique', 'الكيبل الكهربائي', 'cable', 4),
-- Eau
((SELECT id FROM categories WHERE slug='eau'), 'Fuite d''eau', 'تسرب المياه', 'fuite', 1),
((SELECT id FROM categories WHERE slug='eau'), 'Canalisation rompue', 'أنبوب مقطوع', 'canalisation', 2),
((SELECT id FROM categories WHERE slug='eau'), 'Regard de visite', 'manhole', 'regard', 3),
((SELECT id FROM categories WHERE slug='eau'), 'Égout bouché', 'مجاري مسدودة', 'egout', 4),
((SELECT id FROM categories WHERE slug='eau'), 'Inondation', 'فيضان', 'inondation', 5),
-- Propreté
((SELECT id FROM categories WHERE slug='proprete'), 'Dépôt sauvage', 'مخلفات عشوائية', 'depot-sauvage', 1),
((SELECT id FROM categories WHERE slug='proprete'), 'Conteneur plein', 'حاوية ممتلئة', 'conteneur', 2),
((SELECT id FROM categories WHERE slug='proprete'), 'Conteneur endommagé', 'حاوية متضررة', 'conteneur-endommage', 3),
((SELECT id FROM categories WHERE slug='proprete'), 'Encombrants', 'أغراض كبيرة الحجم', 'encombrants', 4),
-- Environnement
((SELECT id FROM categories WHERE slug='environnement'), 'Pollution', 'تلوث', 'pollution', 1),
((SELECT id FROM categories WHERE slug='environnement'), 'Déforestation', 'إزالة الغابات', 'deforestation', 2),
((SELECT id FROM categories WHERE slug='environnement'), 'Bruit', 'ضوضاء', 'bruit', 3),
-- Bâtiments
((SELECT id FROM categories WHERE slug='batiments'), 'Façade dégradée', 'واجهة متدهورة', 'facade', 1),
((SELECT id FROM categories WHERE slug='batiments'), 'Toiture endommagée', 'سطح متضرر', 'toiture', 2),
((SELECT id FROM categories WHERE slug='batiments'), 'Vandalisme', 'تخريب', 'vandalisme', 3),
-- Télécom
((SELECT id FROM categories WHERE slug='telecom'), 'Câble coupé', 'كيبل مقطوع', 'cable-coupe', 1),
((SELECT id FROM categories WHERE slug='telecom'), 'Boîtier endommagé', ' صندوق متضرر', 'boitier', 2),
((SELECT id FROM categories WHERE slug='telecom'), 'Antenne défectueuse', 'هوائي معطل', 'antenne', 3);

-- =====================================================
-- DAIRAS DE LA WILAYA D'ALGER (13 Circonscriptions)
-- =====================================================
INSERT INTO `dairas` (`name`, `name_ar`, `code`, `latitude`, `longitude`) VALUES
('Sidi M''Hamed', 'سيدي امحمد', 'DAI-001', 36.7712, 3.0583),
('El Biar', 'الأبيار', 'DAI-002', 36.7636, 3.0106),
('Bouzareah', 'بوزريعة', 'DAI-003', 36.7833, 3.0167),
('Draria', 'دراية', 'DAI-004', 36.7333, 2.9833),
('Dely Ibrahim', 'دالي إبراهيم', 'DAI-005', 36.7500, 2.9833),
('Chéraga', ' الشراقة', 'DAI-006', 36.7667, 2.9500),
('Hussein Dey', 'حسين داي', 'DAI-007', 36.7400, 3.1000),
('Kouba', 'القبة', 'DAI-008', 36.7600, 3.0833),
('Bir Mourad Raïs', 'بئر مراد رايس', 'DAI-009', 36.7333, 3.0500),
('Bab Ezzouar', 'باب الزوار', 'DAI-010', 36.7200, 3.1833),
('Dar El Beïda', 'الدريوش', 'DAI-011', 36.7167, 3.2500),
('Rouiba', 'الرويبة', 'DAI-012', 36.7333, 3.2833),
('Reghaia', 'الرغاية', 'DAI-013', 36.7667, 3.3500);

-- =====================================================
-- COMMUNES PAR DAIRA
-- =====================================================

-- Sidi M'Hamed
INSERT INTO `communes` (`daira_id`, `name`, `name_ar`, `code`, `postal_code`, `latitude`, `longitude`) VALUES
((SELECT id FROM dairas WHERE code='DAI-001'), 'Sidi M''Hamed', 'سيدي احمد', 'COM-001', '16000', 36.7712, 3.0583),
((SELECT id FROM dairas WHERE code='DAI-001'), 'Alger Centre', 'الجزائر الوسطى', 'COM-002', '16001', 36.7700, 3.0600),
((SELECT id FROM dairas WHERE code='DAI-001'), 'El Madania', 'المدينة', 'COM-003', '16002', 36.7750, 3.0550),
((SELECT id FROM dairas WHERE code='DAI-001'), 'Belouizdad', 'بوزريعة', 'COM-004', '16003', 36.7800, 3.0500),
((SELECT id FROM dairas WHERE code='DAI-001'), 'Hammam El Aghalbi', 'حمام العقالبي', 'COM-005', '16004', 36.7680, 3.0650);

-- El Biar
INSERT INTO `communes` (`daira_id`, `name`, `name_ar`, `code`, `postal_code`, `latitude`, `longitude`) VALUES
((SELECT id FROM dairas WHERE code='DAI-002'), 'El Biar', 'الأبيار', 'COM-006', '16030', 36.7636, 3.0106),
((SELECT id FROM dairas WHERE code='DAI-002'), 'Bouzareah', 'بوزريعة', 'COM-007', '16031', 36.7833, 3.0167),
((SELECT id FROM dairas WHERE code='DAI-002'), 'Birkhadem', 'بئر خادم', 'COM-008', '16032', 36.7500, 3.0200),
((SELECT id FROM dairas WHERE code='DAI-002'), 'El Harrach', 'الحراش', 'COM-009', '16200', 36.7200, 3.1300),
((SELECT id FROM dairas WHERE code='DAI-002'), 'Ouled Fayet', 'أولاد فايت', 'COM-010', '16033', 36.7450, 3.0050);

-- Draria
INSERT INTO `communes` (`daira_id`, `name`, `name_ar`, `code`, `postal_code`, `latitude`, `longitude`) VALUES
((SELECT id FROM dairas WHERE code='DAI-004'), 'Draria', 'دراية', 'COM-011', '16012', 36.7333, 2.9833),
((SELECT id FROM dairas WHERE code='DAI-004'), 'Dely Ibrahim', 'دالي إبراهيم', 'COM-012', '16013', 36.7500, 2.9833),
((SELECT id FROM dairas WHERE code='DAI-004'), 'Ain Benian', 'عين البنيان', 'COM-013', '16014', 36.8000, 2.9167),
((SELECT id FROM dairas WHERE code='DAI-004'), 'Zeralda', 'زرالدة', 'COM-014', '16015', 36.7167, 2.9500),
((SELECT id FROM dairas WHERE code='DAI-004'), 'Chéraga', 'الشراقة', 'COM-015', '16016', 36.7667, 2.9500);

-- Hussein Dey
INSERT INTO `communes` (`daira_id`, `name`, `name_ar`, `code`, `postal_code`, `latitude`, `longitude`) VALUES
((SELECT id FROM dairas WHERE code='DAI-007'), 'Hussein Dey', 'حسين داي', 'COM-016', '16006', 36.7400, 3.1000),
((SELECT id FROM dairas WHERE code='DAI-007'), 'Kouba', 'القبة', 'COM-017', '16050', 36.7600, 3.0833),
((SELECT id FROM dairas WHERE code='DAI-007'), 'Bir Mourad Raïs', 'بئر مراد رايس', 'COM-018', '16011', 36.7333, 3.0500),
((SELECT id FROM dairas WHERE code='DAI-007'), 'El Magharia', 'المغارية', 'COM-019', '16007', 36.7450, 3.1050),
((SELECT id FROM dairas WHERE code='DAI-007'), 'Ben Aknoun', 'بن عكنون', 'COM-020', '16008', 36.7550, 3.0250);

-- Bab Ezzouar
INSERT INTO `communes` (`daira_id`, `name`, `name_ar`, `code`, `postal_code`, `latitude`, `longitude`) VALUES
((SELECT id FROM dairas WHERE code='DAI-010'), 'Bab Ezzouar', 'باب الزوار', 'COM-021', '16028', 36.7200, 3.1833),
((SELECT id FROM dairas WHERE code='DAI-010'), 'Ain Naadja', 'عينanja', 'COM-022', '16029', 36.7300, 3.1700),
((SELECT id FROM dairas WHERE code='DAI-010'), 'Bouzareah', 'بوزريعة', 'COM-023', '16030', 36.7350, 3.1750),
((SELECT id FROM dairas WHERE code='DAI-010'), 'El Assrama', 'المعسكر', 'COM-024', '16031', 36.7150, 3.1900);

-- Dar El Beïda
INSERT INTO `communes` (`daira_id`, `name`, `name_ar`, `code`, `postal_code`, `latitude`, `longitude`) VALUES
((SELECT id FROM dairas WHERE code='DAI-011'), 'Dar El Beïda', 'الدريوش', 'COM-025', '16022', 36.7167, 3.2500),
((SELECT id FROM dairas WHERE code='DAI-011'), 'Bab Ezzouar', 'باب الزوار', 'COM-026', '16023', 36.7100, 3.2400),
((SELECT id FROM dairas WHERE code='DAI-011'), 'Bir Touta', 'بئر الطوطة', 'COM-027', '16024', 36.7000, 3.2600),
((SELECT id FROM dairas WHERE code='DAI-011'), 'Sidi Moussa', 'سيدي موسى', 'COM-028', '16025', 36.6950, 3.2450);

-- Rouiba
INSERT INTO `communes` (`daira_id`, `name`, `name_ar`, `code`, `postal_code`, `latitude`, `longitude`) VALUES
((SELECT id FROM dairas WHERE code='DAI-012'), 'Rouiba', 'الرويبة', 'COM-029', '16035', 36.7333, 3.2833),
((SELECT id FROM dairas WHERE code='DAI-012'), 'Reghaia', 'الرغاية', 'COM-030', '16036', 36.7667, 3.3500),
((SELECT id FROM dairas WHERE code='DAI-012'), 'El Harrouch', 'الحروش', 'COM-031', '16037', 36.7200, 3.3000),
((SELECT id FROM dairas WHERE code='DAI-012'), 'Ain Taya', 'عين طاية', 'COM-032', '16038', 36.7900, 3.3200);

-- =====================================================
-- UTILISATEURS (Admin Central + Intervenants de test)
-- =====================================================
-- Mot de passe: admin123 (hash bcrypt)
INSERT INTO `users` (`uuid`, `first_name`, `last_name`, `email`, `phone`, `password`, `status`, `email_verified_at`) VALUES
('a0eebc99-9c0b-4ef8-bb6d-6bb9bd380a11', 'Admin', 'Central', 'admin@balagh-alger.dz', '0555000001', '$2y$12$LJ3m4ys3Lhdo5z10K6qJYuhN6GJNqG8J5uJ4vJ5qJ6kJ7lJ8mJ9n', 'active', NOW()),
('b0eebc99-9c0b-4ef8-bb6d-6bb9bd380a12', 'Mohamed', 'Ben Ali', 'mohamed.benali@balagh-alger.dz', '0555000002', '$2y$12$LJ3m4ys3Lhdo5z10K6qJYuhN6GJNqG8J5uJ4vJ5qJ6kJ7lJ8mJ9n', 'active', NOW()),
('c0eebc99-9c0b-4ef8-bb6d-6bb9bd380a13', 'Fatima', 'Boudiaf', 'fatima.boudiaf@balagh-alger.dz', '0555000003', '$2y$12$LJ3m4ys3Lhdo5z10K6qJYuhN6GJNqG8J5uJ4vJ5qJ6kJ7lJ8mJ9n', 'active', NOW()),
('d0eebc99-9c0b-4ef8-bb6d-6bb9bd380a14', 'Karim', 'Zeroual', 'karim.zeroual@balagh-alger.dz', '0555000004', '$2y$12$LJ3m4ys3Lhdo5z10K6qJYuhN6GJNqG8J5uJ4vJ5qJ6kJ7lJ8mJ9n', 'active', NOW()),
('e0eebc99-9c0b-4ef8-bb6d-6bb9bd380a15', 'Amina', 'Hadj', 'amina.hadj@balagh-alger.dz', '0555000005', '$2y$12$LJ3m4ys3Lhdo5z10K6qJYuhN6GJNqG8J5uJ4vJ5qJ6kJ7lJ8mJ9n', 'active', NOW());

-- Assigner les rôles
INSERT INTO `user_roles` (`user_id`, `role_id`, `assigned_by`) VALUES
(1, (SELECT id FROM roles WHERE name='admin_central'), NULL),
(2, (SELECT id FROM roles WHERE name='chef_unite'), 1),
(3, (SELECT id FROM roles WHERE name='chef_section'), 1),
(4, (SELECT id FROM roles WHERE name='intervenant'), 1),
(5, (SELECT id FROM roles WHERE name='intervenant'), 1);

-- =====================================================
-- SIGNALEMENTS DE TEST
-- =====================================================
INSERT INTO `reports` (`tracking_code`, `title`, `description`, `category_id`, `subcategory_id`, `priority`, `status`, `daira_id`, `commune_id`, `address`, `latitude`, `longitude`, `citizen_name`, `citizen_phone`, `organization_id`, `created_at`) VALUES
('BA-2026-000001', 'Nid-de-poule rue Didouche Mourad', 'Grand nid-de-poule dans la chaussée, risque d''accident', (SELECT id FROM categories WHERE slug='voirie'), (SELECT id FROM subcategories WHERE slug='nid-de-poule'), 'high', 'in_progress', (SELECT id FROM dairas WHERE code='DAI-001'), (SELECT id FROM communes WHERE code='COM-001'), 'Rue Didouche Mourad, Alger Centre', 36.7710, 3.0580, 'Ahmed Ben Salah', '0555123456', (SELECT id FROM organizations WHERE slug='dtp'), DATE_SUB(NOW(), INTERVAL 3 DAY)),
('BA-2026-000002', 'Lampe éteinte Boulevard Krim Belkacem', 'Plusieurs lampes éteintes sur le boulevard', (SELECT id FROM categories WHERE slug='eclairage'), (SELECT id FROM subcategories WHERE slug='lampe-eteinte'), 'medium', 'submitted', (SELECT id FROM dairas WHERE code='DAI-007'), (SELECT id FROM communes WHERE code='COM-016'), 'Boulevard Krim Belkacem', 36.7420, 3.0980, 'Sara Khelifi', '0555234567', (SELECT id FROM organizations WHERE slug='sonelgaz'), DATE_SUB(NOW(), INTERVAL 2 DAY)),
('BA-2026-000003', 'Dépôt sauvage route de Reghaia', 'Accumulation de déchets le long de la route', (SELECT id FROM categories WHERE slug='proprete'), (SELECT id FROM subcategories WHERE slug='depot-sauvage'), 'urgent', 'assigned', (SELECT id FROM dairas WHERE code='DAI-012'), (SELECT id FROM communes WHERE code='COM-030'), 'Route Nationale N°5', 36.7680, 3.3510, NULL, NULL, (SELECT id FROM organizations WHERE slug='hupe'), DATE_SUB(NOW(), INTERVAL 1 DAY)),
('BA-2026-000004', 'Fuite d''eau avenue de la République', 'Fuite importante devant le centre commercial', (SELECT id FROM categories WHERE slug='eau'), (SELECT id FROM subcategories WHERE slug='fuite'), 'high', 'in_progress', (SELECT id FROM dairas WHERE code='DAI-002'), (SELECT id FROM communes WHERE code='COM-006'), 'Avenue de la République', 36.7650, 3.0120, 'Youcef Amrani', '0555345678', (SELECT id FROM organizations WHERE slug='seaal'), DATE_SUB(NOW(), INTERVAL 12 HOUR)),
('BA-2026-000005', 'Vandalisme salle de sport communale', 'Miroirs cassés et équipements détériorés', (SELECT id FROM categories WHERE slug='batiments'), (SELECT id FROM subcategories WHERE slug='vandalisme'), 'low', 'resolved', (SELECT id FROM dairas WHERE code='DAI-010'), (SELECT id FROM communes WHERE code='COM-021'), 'Centre sportif Bab Ezzouar', 36.7210, 3.1850, 'Nadia Bensalem', '0555456789', (SELECT id FROM organizations WHERE slug='commune'), DATE_SUB(NOW(), INTERVAL 5 DAY));
