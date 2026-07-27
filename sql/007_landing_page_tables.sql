-- ============================================
-- Balagh Alger: Landing Page Management Tables
-- ============================================

-- 1. Partners
CREATE TABLE IF NOT EXISTS landing_partners (
    id         INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name       VARCHAR(100) NOT NULL,
    icon       VARCHAR(50) NOT NULL DEFAULT 'fas fa-building',
    color      VARCHAR(30) DEFAULT 'var(--primary-light)',
    sort_order INT DEFAULT 0,
    is_active  TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 2. Gallery
CREATE TABLE IF NOT EXISTS landing_gallery (
    id         INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    image_url  VARCHAR(500) NOT NULL,
    alt_text   VARCHAR(255) DEFAULT '',
    sort_order INT DEFAULT 0,
    is_active  TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 3. Testimonials
CREATE TABLE IF NOT EXISTS landing_testimonials (
    id              INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    text_fr         TEXT NOT NULL,
    text_ar         TEXT NOT NULL,
    author_name     VARCHAR(100) NOT NULL,
    author_role     VARCHAR(100) NOT NULL,
    avatar_letter   CHAR(1) NOT NULL,
    avatar_gradient VARCHAR(30) DEFAULT 'var(--gradient-accent)',
    rating          TINYINT DEFAULT 5,
    is_active       TINYINT(1) DEFAULT 1,
    sort_order      INT DEFAULT 0,
    created_at      TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 4. Before/After
CREATE TABLE IF NOT EXISTS landing_before_after (
    id           INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    before_image VARCHAR(500) NOT NULL,
    after_image  VARCHAR(500) NOT NULL,
    title_fr     VARCHAR(200) NOT NULL,
    title_ar     VARCHAR(200) NOT NULL,
    desc_fr      TEXT NOT NULL,
    desc_ar      TEXT NOT NULL,
    sort_order   INT DEFAULT 0,
    is_active    TINYINT(1) DEFAULT 1,
    created_at   TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 5. FAQ
CREATE TABLE IF NOT EXISTS landing_faq (
    id          INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    question_fr VARCHAR(300) NOT NULL,
    question_ar VARCHAR(300) NOT NULL,
    answer_fr   TEXT NOT NULL,
    answer_ar   TEXT NOT NULL,
    sort_order  INT DEFAULT 0,
    is_active   TINYINT(1) DEFAULT 1,
    created_at  TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ============================================
-- SEED DATA
-- ============================================

-- Partners (12)
INSERT INTO landing_partners (name, icon, color, sort_order) VALUES
('SEAAL', 'fas fa-water', 'var(--accent)', 1),
('Sonelgaz', 'fas fa-bolt', 'var(--amber)', 2),
('DTP', 'fas fa-road', 'var(--primary-light)', 3),
('ASRout', 'fas fa-road', '#f97316', 4),
('ERMA', 'fas fa-industry', 'var(--red)', 5),
('EDéval', 'fas fa-lightbulb', 'var(--amber)', 6),
('EGCTU', 'fas fa-dumbbell', 'var(--green)', 7),
('EMCU', 'fas fa-tree', 'var(--green)', 8),
('HUPE', 'fas fa-umbrella-beach', 'var(--accent)', 9),
('NETCOM', 'fas fa-broom', 'var(--pink)', 10),
('Wilaya d''Alger', 'fas fa-building', 'var(--primary-light)', 11),
('APC', 'fas fa-city', 'var(--accent)', 12);

-- Gallery (6)
INSERT INTO landing_gallery (image_url, alt_text, sort_order) VALUES
('https://images.unsplash.com/photo-1548018560-c7196e7a1eda?w=600&q=80', 'Alger vue', 1),
('https://images.unsplash.com/photo-1580502304784-8985b7eb7260?w=600&q=80', 'Rue Alger', 2),
('https://images.unsplash.com/photo-1590073242678-70ee3fc28e8e?w=600&q=80', 'Construction', 3),
('https://images.unsplash.com/photo-1568515387631-8b650bbcdb90?w=600&q=80', 'Ville', 4),
('https://images.unsplash.com/photo-1497436072909-60f360e1d4b1?w=600&q=80', 'Nature', 5),
('https://images.unsplash.com/photo-1414609245224-afa02bfb3fda?w=600&q=80', 'Route', 6);

-- Testimonials (3)
INSERT INTO landing_testimonials (text_fr, text_ar, author_name, author_role, avatar_letter, avatar_gradient, rating, sort_order) VALUES
('J''ai signalé un trou de rue, et il a été réparé en une semaine. Plateforme formidable !', 'أبلغت عن حفرة في شارعنا، وتم إصلاحها خلال أسبوع. منصة رائعة!', 'Karim B.', 'El Harrach', 'K', 'var(--gradient-accent)', 5, 1),
('La facilité d''utilisation et la transparence du suivi me font recommander cette plateforme à tous.', 'سهولة الاستخدام والشفافية في التتبع تجعلانيني أوصي بهذه المنصة للجميع.', 'Sarah L.', 'Bir Mourad Raïs', 'S', 'var(--gradient-cool)', 5, 2),
('Enfin une plateforme qui permet aux citoyens d''améliorer leur ville. Merci Balagh Alger !', 'أخيراً منصة تسمح للمواطنين بالمساهمة في تحسين مدينتهم. شكراً بلاغ الجزائر!', 'Yacine H.', 'Hussein Dey', 'Y', 'var(--gradient-purple)', 4, 3);

-- Before/After (3)
INSERT INTO landing_before_after (before_image, after_image, title_fr, title_ar, desc_fr, desc_ar, sort_order) VALUES
('https://images.unsplash.com/photo-1515162305055-fca6a7efaea5?w=600&q=80', 'https://images.unsplash.com/photo-1449824913935-59a10b8d2000?w=600&q=80', 'Réparation rue du quartier', 'إصلاح طريق الحي', 'Nid-de-poule comblé et route rénovée en 5 jours.', 'تم إصلاح الحفر وترقيع الطريق خلال 5 أيام.', 1),
('https://images.unsplash.com/photo-1532996122724-e3c354a0b15b?w=600&q=80', 'https://images.unsplash.com/photo-1497436072909-60f360e1d4b1?w=600&q=80', 'Nettoyage de la zone', 'تنظيف المنطقة', 'Déchets retirés, espace vert créé.', 'إزالة النفايات وإنشاء مساحة خضراء.', 2),
('https://images.unsplash.com/photo-1558618666-fcd25c85f82e?w=600&q=80', 'https://images.unsplash.com/photo-1580502304784-8985b7eb7260?w=600&q=80', 'Réparation éclairage', 'إصلاح الإضاءة', 'Nouveaux lampadaires installés, éclairage amélioré.', 'تم تركيب مصابيح جديدة وتحسين الإضاءة.', 3);

-- FAQ (6)
INSERT INTO landing_faq (question_fr, question_ar, answer_fr, answer_ar, sort_order) VALUES
('Comment signaler un problème ?', 'كيفية الإبلاغ عن مشكلة؟', 'Créez un compte, cliquez sur "Signaler un problème", remplissez le formulaire avec photo et localisation, puis soumettez.', 'أنشئ حساباً، انقر على "الإبلاغ عن مشكلة"، املأ النموذج مع الصورة والموقع، ثم أرسل.', 1),
('Combien de temps prend le traitement ?', 'كم يستغرق المعالجة؟', 'Le délai varie selon la catégorie : 7-15 jours ouvrables. Vous pouvez suivre l''avancement en temps réel via votre code de suivi.', 'يختلف المدة حسب الفئة: 7-15 يوم عمل. يمكنك متابعة التقدم في الوقت الفعلي عبر كود التتبع.', 2),
('Puis-je suivre mon signalement ?', 'هل يمكنني متابعة بلاغي؟', 'Oui ! Chaque signalement reçoit un code unique. Utilisez la page "Suivi" pour voir l''état en temps réel.', 'نعم! كل بلاغ يحصل على كود فريد. استخدم صفحة "المتابعة" لرؤية الحالة في الوقت الفعلي.', 3),
('Comment modifier mon signalement ?', 'كيف أعدّل بلاغي؟', 'Vous pouvez modifier votre signalement tant qu''il n''a pas été pris en charge par un intervenant. Allez dans "Mes signalements".', 'يمكنك تعديل بلاغك ما لم يتم اتخاذه من قبل المتدخل. اذهب إلى "بلاغاتي".', 4),
('Les photos sont-elles obligatoires ?', 'هل الصور إلزامية؟', 'Les photos sont fortement recommandées car elles accélèrent le traitement. Une photo par défaut sera ajoutée si vous n''en avez pas.', 'الصور موصى بها بشدة لأنها تسرّع المعالجة. ستُضاف صورة افتراضية إذا لم يكن لديك صورة.', 5),
('Comment contacter le support ?', 'التواصل مع الدعم؟', 'Utilisez la page de contact ou envoyez un email à support@balagh-alger.dz. Notre équipe répond sous 24h.', 'استخدم صفحة الاتصال أو أرسل بريداً إلكترونياً إلى support@balagh-alger.dz. يرد فريقنا خلال 24 ساعة.', 6);

-- ============================================
-- SETTINGS (hero image + social links)
-- ============================================
INSERT INTO settings (group_name, key_name, value, type, label, is_public) VALUES
('landing', 'hero_image', 'https://images.unsplash.com/photo-1449824913935-59a10b8d2000?w=900&q=80', 'image', 'Image hero showcase', 1),
('landing', 'facebook_url', '#', 'text', 'Facebook URL', 1),
('landing', 'twitter_url', '#', 'text', 'Twitter URL', 1),
('landing', 'instagram_url', '#', 'text', 'Instagram URL', 1),
('landing', 'youtube_url', '#', 'text', 'YouTube URL', 1)
ON DUPLICATE KEY UPDATE value = VALUES(value);

-- ============================================
-- PERMISSION
-- ============================================
INSERT INTO permissions (slug, name, description) VALUES
('landing.manage', 'Gérer la page d''accueil', 'Contrôle du contenu de la landing page')
ON DUPLICATE KEY UPDATE name = VALUES(name);

-- Map to admin_central (role_id = 7)
INSERT IGNORE INTO role_permissions (role_id, permission_id)
SELECT 7, id FROM permissions WHERE slug = 'landing.manage';
