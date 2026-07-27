-- ============================================================
-- 008: Complete category/subcategory restructure
-- Based on user-provided anomaly mapping (70 fields, 150+ subcategories)
-- Organization mapping via IFS logic
-- ============================================================

SET FOREIGN_KEY_CHECKS = 0;

-- ============================================================
-- 1. REMAP EXISTING REPORTS to new categories before deleting old ones
-- ============================================================

-- Category 1 (Propreté et Hygiène / NETCOM) → نفايات منزلية (id will be 1)
UPDATE reports SET category_id = 1 WHERE category_id = 1;

-- Category 2 (Voirie et Routes / ASROUT) → الطريق (id will be 16)
UPDATE reports SET category_id = 16 WHERE category_id = 2;

-- Category 3 (Eau et Assainissement / SEAAL) → تسرب المياه الصالحة للشرب (id will be 10)
UPDATE reports SET category_id = 10 WHERE category_id = 3;

-- Category 4 (Électricité et Gaz / SONELGAZ) → حالة الأعمدة الكهربائية (id will be 22)
UPDATE reports SET category_id = 22 WHERE category_id = 4;

-- Category 10 (Plages et Corniche / HUPE) → لافتات (id will be 58)
UPDATE reports SET category_id = 58 WHERE category_id = 10;

-- Category 11 (Travaux Publics / DTP) → الطريق (id will be 16)
UPDATE reports SET category_id = 16 WHERE category_id = 11;

-- ============================================================
-- 2. CLEAR OLD STRUCTURE
-- ============================================================

DELETE FROM organization_rules;
DELETE FROM subcategories;
DELETE FROM categories;

-- Reset auto increment
ALTER TABLE categories AUTO_INCREMENT = 1;
ALTER TABLE subcategories AUTO_INCREMENT = 1;

-- ============================================================
-- 3. INSERT NEW CATEGORIES (70 fields)
-- ============================================================

INSERT INTO categories (id, name, name_ar, slug, icon, color, organization_id, deadline_days, is_active, sort_order) VALUES
(1,  'Nourriture ménagère', 'نفايات منزلية', 'nefayat-managiya', 'fas fa-trash', '#6c757d', 1, 7, 1, 1),
(2,  'Opération de balayage', 'عملية الكنس', 'amaliyat-alkans', 'fas fa-broom', '#6c757d', 1, 7, 1, 2),
(3,  'Conteneurs à ordures', 'حاويات القمامة', 'hawiyat-alqamama', 'fas fa-dumpster', '#6c757d', 1, 7, 1, 3),
(4,  'Poubelles', 'سلات المهملات', 'salat-almahmalat', 'fas fa-trash-alt', '#6c757d', 1, 7, 1, 4),
(5,  'Déchets marins', 'مخلفات بحرية', 'makhalfat-bahariya', 'fas fa-water', '#6c757d', 1, 7, 1, 5),
(6,  'Déchets verts', 'نفايات خضراء', 'nefayat-khadraa', 'fas fa-leaf', '#6c757d', 1, 7, 1, 6),
(7,  'Déchets solides', 'النفايات الصلبة', 'alnefayat-alsaliba', 'fas fa-recycle', '#28a745', 2, 10, 1, 7),
(8,  'Déchets mixtes', 'نفايات مختلطة', 'nefayat-mukhtalita', 'fas fa-trash-restore', '#28a745', 2, 10, 1, 8),
(9,  'Caniveaux', 'البالوعات', 'albalawat', 'fas fa-grip-lines', '#28a745', 2, 10, 1, 9),
(10, 'Réseau eau potable', 'تسرب المياه الصالحة للشرب', 'tasarrub-miyah-sharba', 'fas fa-tint', '#28a745', 2, 10, 1, 10),
(11, 'Égouts', 'تسرب مياه الصرف الصحي', 'tasarrub-miyah-sayf', 'fas fa-water', '#28a745', 2, 10, 1, 11),
(12, 'Bouches d''égout', 'أغطية قنوات الصرف الصحي', 'aghtiyat-qanawat', 'fas fa-circle', '#28a745', 2, 10, 1, 12),
(13, 'Bassin d''égout', 'خزان الصرف الصحي', 'khazan-alkanati', 'fas fa-fill-drip', '#28a745', 2, 10, 1, 13),
(14, 'Évier stagnants', 'ركود المياه', 'rukud-almiyah', 'fas fa-tint-slash', '#28a745', 2, 10, 1, 14),
(15, 'Perrons', 'أرصفة', 'arasefa', 'fas fa-walking', '#28a745', 2, 10, 1, 15),
(16, 'Voirie', 'الطريق', 'altarik', 'fas fa-road', '#28a745', 2, 10, 1, 16),
(17, 'Barrières ciment', 'حواجز إسمنتية', 'hawajiz-isamentiya', 'fas fa-university', '#28a745', 2, 10, 1, 17),
(18, 'Câbles téléphone cave', 'أقبية الأسلاك الهاتفية', 'aqabiya-aslak', 'fas fa-phone-slash', '#28a745', 2, 10, 1, 18),
(19, 'Éclairage public', 'الإنارة العمومية', 'alenera-aloumoumiya', 'fas fa-lightbulb', '#ffc107', 4, 15, 1, 19),
(20, 'Horloges', 'الساعات', 'alsaat', 'fas fa-clock', '#ffc107', 4, 15, 1, 20),
(21, 'Horlogerie', 'الساعات العامة', 'alsaat-alamma', 'fas fa-business-time', '#ffc107', 4, 15, 1, 21),
(22, 'Poteaux électriques', 'حالة الأعمدة الكهربائية', 'halat-alamdah', 'fas fa-bolt', '#ffc107', 4, 15, 1, 22),
(23, 'Entretien espaces verts', 'نقص في العناية بالمساحة الخضراء', 'naqs-enaya-fadaa', 'fas fa-seedling', '#20c997', 15, 7, 1, 23),
(24, 'Taille arbres', 'العناية بالأشجار', 'enaya-bilashjar', 'fas fa-tree', '#20c997', 15, 7, 1, 24),
(25, 'Herbes nuisibles', 'حشائش ضارة', 'hashaish-darra', 'fas fa-leaf', '#20c997', 15, 7, 1, 25),
(26, 'Éboulis rocheux', 'إنهيار صخري', 'inihar-sakhri', 'fas fa-mountain', '#20c997', 15, 7, 1, 26),
(27, 'Avancée sable', 'زحف الرمال', 'zahaf-arlamal', 'fas fa-wind', '#20c997', 15, 7, 1, 27),
(28, 'Érosion sol', 'إنجراف التربة', 'injiraf-alturba', 'fas fa-landmark', '#20c997', 15, 7, 1, 28),
(29, 'Escarpements', 'المنحدرات', 'almanahid', 'fas fa-angle-double-down', '#20c997', 15, 7, 1, 29),
(30, 'Signalisation verticale', 'الإشارات العمومية', 'alisharat-aloumoumiya', 'fas fa-sign', '#17a2b8', 6, 7, 1, 30),
(31, 'Signalisation horizontale', 'الإشارات الأفقية', 'alisharat-alufuqiya', 'fas fa-arrows-alt-h', '#17a2b8', 6, 7, 1, 31),
(32, 'Abribus', 'واقيات مواقف الحافلات', 'waqiyat-mawaqif', 'fas fa-bus', '#17a2b8', 6, 7, 1, 32),
(33, 'Barrières métalliques', 'حواجز حديدية', 'hawajiz-hadidiya', 'fas fa-border-all', '#17a2b8', 6, 7, 1, 33),
(34, 'Passage piéton', 'جسر المشاة', 'jasr-almoshaa', 'fas fa-walking', '#17a2b8', 6, 7, 1, 34),
(35, 'Entrée station', 'مدخل الموقف', 'madkhal-almauqif', 'fas fa-door-open', '#17a2b8', 6, 7, 1, 35),
(36, 'Parking', 'موقف سيارات', 'mauqif-sayarat', 'fas fa-parking', '#17a2b8', 6, 7, 1, 36),
(37, 'Station urbaine', 'محطة حضرية', 'mahatta-hadariya', 'fas fa-city', '#17a2b8', 6, 7, 1, 37),
(38, 'Arrêts bus', 'مواقف الحافلات', 'mawaqif-alhafilat', 'fas fa-bus-alt', '#17a2b8', 6, 7, 1, 38),
(39, 'Gros œuvre', 'أشغال طور الإنجاز', 'ashghal-tour-injaz', 'fas fa-hard-hat', '#17a2b8', 6, 7, 1, 39),
(40, 'Drapeaux', 'الرايات الوطنية', 'alriyat-alwataniya', 'fas fa-flag', '#e83e8c', 5, 7, 1, 40),
(41, 'Fontaines', 'نافورات المياه', 'nafurat-almiyah', 'fas fa-faucet', '#e83e8c', 5, 7, 1, 41),
(42, 'Kiosques', 'مقصورات', 'maqaswarat', 'fas fa-store', '#e83e8c', 5, 7, 1, 42),
(43, 'Fontaines publiques', 'النافورات العمومية', 'alnafurat-aloumoumiya', 'fas fa-tint', '#e83e8c', 5, 7, 1, 43),
(44, 'Voitures défraîchies', 'سيارات مهترئة', 'sayarat-muhtari-a', 'fas fa-car-crash', '#fd7e14', 9, 7, 1, 44),
(45, 'Usage illégal voie publique', 'الإستغلال الغير قانوني للطريق العام', 'istighlal-hair-qanuni', 'fas fa-ban', '#fd7e14', 9, 7, 1, 45),
(46, 'Usage illégal plages', 'الإستغلال الغير قانوني للشواطئ', 'istighlal-shawati', 'fas fa-umbrella-beach', '#fd7e14', 9, 7, 1, 46),
(47, 'Façades immeubles', 'واجهات المباني', 'wajihat-almaabani', 'fas fa-building', '#fd7e14', 9, 7, 1, 47),
(48, 'Immeuble effondré', 'بناية مهدمة', 'binaya-muhadama', 'fas fa-home', '#fd7e14', 9, 7, 1, 48),
(49, 'Ascenseurs', 'حالة المصاعد', 'halat-almasaad', 'fas fa-arrow-up', '#fd7e14', 9, 7, 1, 49),
(50, 'Terrains jeux', 'الملاعب الجوارية', 'almalab-aljawariya', 'fas fa-futbol', '#fd7e14', 9, 7, 1, 50),
(51, 'Bidonvilles', 'بيوت قصديرية', 'buyut-qasdiriya', 'fas fa-home', '#fd7e14', 9, 7, 1, 51),
(52, 'Aires jeux', 'فضاء التسلية', 'fadaa-altasliya', 'fas fa-child', '#fd7e14', 9, 7, 1, 52),
(53, 'Caves', 'أقبية', 'aqabiya', 'fas fa-warehouse', '#fd7e14', 9, 7, 1, 53),
(54, 'Plaques commémoratives', 'لوحات إشهارية', 'lawhat-ishhariya', 'fas fa-sign', '#fd7e14', 9, 7, 1, 54),
(55, 'Escaliers', 'حالة السلالم والأدراج', 'halat-alsalalim', 'fas fa-stairs', '#fd7e14', 9, 7, 1, 55),
(56, 'Échafaudages', 'الدعامات', 'alddaam', 'fas fa-drafting-compass', '#fd7e14', 9, 7, 1, 56),
(57, 'Panneaux advertisement', 'لافتات إشهارية', 'lafihat-ishhariya', 'fas fa-ad', '#fd7e14', 9, 7, 1, 57),
(58, 'Plages', 'لافتات', 'lafihat', 'fas fa-umbrella-beach', '#007bff', 3, 7, 1, 58),
(59, 'Animaux errants', 'حيوانات ضالة', 'hayawanat-dala', 'fas fa-paw', '#007bff', 3, 7, 1, 59),
(60, 'Barques pêche', 'قوارب الصيد', 'qawarib-alsayd', 'fas fa-ship', '#007bff', 3, 7, 1, 60),
(61, 'Sépultures', 'وضعية القبور', 'wadiyyat-alqubur', 'fas fa-monument', '#007bff', 3, 7, 1, 61),
(62, 'Oued', 'أودية', 'awdiya', 'fas fa-water', '#6610f2', 11, 10, 1, 62),
(63, 'Sources', 'الينابيع', 'alyanabi', 'fas fa-spa', '#6610f2', 11, 7, 1, 63),
(64, 'Arrosage', 'مرشات', 'mrashshat', 'fas fa-shower', '#6610f2', 11, 7, 1, 64),
(65, 'Poteaux téléphone', 'عمود أسلاك الهاتف', 'amud-aslak-hatif', 'fas fa-phone', '#6610f2', 11, 15, 1, 65),
(66, 'Rochers', 'صخور متدلية', 'sukhur-mutadalliya', 'fas fa-mountain', '#6610f2', 11, 7, 1, 66),
(67, 'Voie rapide', 'طريق سريع', 'tarik-sari', 'fas fa-highway', '#343a40', 10, 10, 1, 67),
(68, 'Cimetière', 'مقبرة', 'maqbara', 'fas fa-headstone', '#6f42c1', 18, 7, 1, 68),
(69, 'Réservoir eau', 'خزان المياه', 'khazan-almiyah', 'fas fa-database', '#20c997', 13, 7, 1, 69),
(70, 'Autre anomalie', 'أخرى', 'ukhra', 'fas fa-question-circle', '#adb5bd', NULL, 7, 1, 70);
INSERT INTO subcategories (category_id, name, name_ar, slug, sort_order) VALUES
(1, 'Dépôt sauvage', 'مخلفات عشوائية', 'makhalfat-eishwiya', 1),
(1, 'Retard collecte', 'تأخر في رفع النفايات', 'takhar-rif-nifayat', 2);
INSERT INTO subcategories (category_id, name, name_ar, slug, sort_order) VALUES
(2, 'Insuffisance balayage', 'نقص في عملية الكنس', 'naqs-amaliyat-kans', 1);
INSERT INTO subcategories (category_id, name, name_ar, slug, sort_order) VALUES
(3, 'Conteneur manquant', 'غياب الحاويات', 'ghiyab-alhawiyat', 1),
(3, 'Conteneur insuffisant', 'نقص في الحاويات', 'naqs-alhawiyat', 2),
(3, 'Conteneur dégradé', 'إهتراء الحاويات', 'ihtira-alhawiyat', 3);
INSERT INTO subcategories (category_id, name, name_ar, slug, sort_order) VALUES
(4, 'Poubelle dégradée', 'إهتراء السلة', 'ihi-tira-alsilla', 1),
(4, 'Poubelle manquante', 'غياب السلة', 'ghiyab-alsilla', 2),
(4, 'Poubelle insuffisante', 'نقص في السلات', 'naqs-alsalat', 3);
INSERT INTO subcategories (category_id, name, name_ar, slug, sort_order) VALUES
(5, 'Algues', 'أعشاب بحرية', 'ashab-bahariya', 1);
INSERT INTO subcategories (category_id, name, name_ar, slug, sort_order) VALUES
(6, 'Dépôt sauvage', 'مخلفات عشوائية', 'makhalfat-khadraa', 1),
(6, 'Retard collecte', 'تأخر في رفع النفايات', 'takhar-khadraa', 2);
INSERT INTO subcategories (category_id, name, name_ar, slug, sort_order) VALUES
(7, 'Dépôt sauvage', 'مخلفات عشوائية', 'makhalfat-saliba', 1),
(7, 'Retard collecte', 'تأخر في رفع النفايات', 'takhar-saliba', 2);
INSERT INTO subcategories (category_id, name, name_ar, slug, sort_order) VALUES
(8, 'Dépôt sauvage', 'مخلفات عشوائية', 'makhalfat-mukhtalita', 1),
(8, 'Retard collecte', 'تأخر في رفع النفايات', 'takhar-mukhtalita', 2);
INSERT INTO subcategories (category_id, name, name_ar, slug, sort_order) VALUES
(9, 'Bouchée', 'مسدودة', 'masduda', 1),
(9, 'Sans couvercle', 'بدون غطاء', 'bidun-ghataya', 2),
(9, 'Écroulée', 'محطمة', 'muhatama', 3);
INSERT INTO subcategories (category_id, name, name_ar, slug, sort_order) VALUES
(10, 'Fuite réseau', 'تسرب من الشبكة', 'tasarrub-min-shabaka', 1);
INSERT INTO subcategories (category_id, name, name_ar, slug, sort_order) VALUES
(11, 'Collecte eaux usées', 'مجمع مياه الصرف الصحي', 'majmi-miyah-sayf', 1),
(11, 'Collecte usées+pluviales', 'مجمع مياه الصرف ومياه الأمطار', 'majmi-miyah-amtar', 2),
(11, 'Collecte piscine', 'مجمع مياه الصرف للمسبح', 'majmi-miyah-masbah', 3),
(11, 'Fuite individuelle', 'تسرب الصرف الصحي الفردي', 'tasarrub-sayf-fardi', 4),
(11, 'Station pompage', 'مجمع محطة الرفع', 'majmi-mahatta-rafa', 5);
INSERT INTO subcategories (category_id, name, name_ar, slug, sort_order) VALUES
(12, 'Couvercle manquant', 'غياب الغطاء', 'ghiyab-ghataya', 1),
(12, 'Couvercle dégradé', 'مهترئة', 'muhtari-a', 2),
(12, 'Canal bouchée', 'مسدودة', 'masduda-qanat', 3);
INSERT INTO subcategories (category_id, name, name_ar, slug, sort_order) VALUES
(13, 'Débordant', 'المتدفق', 'almutadafiq', 1),
(13, 'Non débordant', 'غير المتدفق', 'ghayr-almutadafiq', 2);
INSERT INTO subcategories (category_id, name, name_ar, slug, sort_order) VALUES
(14, 'Eau stagnante', 'مياه راكدة', 'miyah-rakida', 1);
INSERT INTO subcategories (category_id, name, name_ar, slug, sort_order) VALUES
(15, 'Nid-de-poule', 'حفرة في الرصيف', 'hafra-rasif', 1),
(15, 'Usure', 'إهتراء', 'ihtira-rasif', 2),
(15, 'Pas entretien après travaux', 'عدم صيانة بعد الأشغال', 'adam-siyana-bad-ashghal', 3),
(15, 'Manque peinture bordure', 'نقص في طلاء حواف الرصيف', 'naqs-talaa-hawaf', 4),
(15, 'Manque lavage', 'نقص في غسل الرصيف', 'naqs-ghasl-rasif', 5);
INSERT INTO subcategories (category_id, name, name_ar, slug, sort_order) VALUES
(16, 'Nid-de-poule', 'حفرة في الطريق', 'hafra-tarik', 1),
(16, 'Usure chaussée', 'إهتراء', 'ihtira-tarik', 2),
(16, 'Pas entretien après travaux', 'عدم صيانة بعد إنتهاء الأشغال', 'adam-siyana-tarik', 3);
INSERT INTO subcategories (category_id, name, name_ar, slug, sort_order) VALUES
(17, 'Manque peinture', 'نقص في الطلاء', 'naqs-talaa-isam', 1),
(17, 'Barrière dégradée', 'حاجز مهترئ', 'hajiz-muhtari', 2),
(17, 'Barrière manquante', 'غياب الحواجز', 'ghiyab-hawajiz-isam', 3);
INSERT INTO subcategories (category_id, name, name_ar, slug, sort_order) VALUES
(18, 'Couvercle cassé', 'غطاء القبو محطم', 'ghataya-alqabu-muhatam', 1),
(18, 'Couvercle manquant', 'غياب الغطاء', 'ghiyab-ghataya-aslak', 2),
(18, 'Câbles exposés', 'بروز الأسلاك', 'buruz-aslak-hatif', 3);
INSERT INTO subcategories (category_id, name, name_ar, slug, sort_order) VALUES
(19, 'Lampes cassées', 'مصابيح محطمة', 'masabih-muhatama', 1),
(19, 'Lampes éteintes', 'مصابيح معطلة', 'masabih-muattala', 2),
(19, 'Poteau usé', 'عمود إنارة مهترئ', 'amud-inara-muhtari', 3),
(19, 'Poteau penché', 'عمود إنارة آيل للسقوط', 'amud-inara-ail', 4),
(19, 'Éclairage manquant', 'غياب الإنارة', 'ghiyab-alinara', 5),
(19, 'Manque peinture poteau', 'نقص في طلاء عمود الإنارة', 'naqs-talaa-amud', 6),
(19, 'Câbles exposés', 'بروز أسلاك الكهرباء من عمود الإنارة', 'buruz-aslak-amud', 7);
INSERT INTO subcategories (category_id, name, name_ar, slug, sort_order) VALUES
(20, 'Horloge défectueuse', 'ساعة معطلة', 'saa-muattala', 1),
(20, 'Horloge usée', 'ساعة مهترئة', 'saa-muhtari-a', 2);
INSERT INTO subcategories (category_id, name, name_ar, slug, sort_order) VALUES
(21, 'Horloge défectueuse', 'ساعة معطلة', 'saa-amma-muattala', 1),
(21, 'Horloge usée', 'ساعة مهترئة', 'saa-amma-muhtari', 2);
INSERT INTO subcategories (category_id, name, name_ar, slug, sort_order) VALUES
(22, 'Poteau penché', 'عمود آيل للسقوط', 'amud-ail-kahraba', 1),
(22, 'Câbles exposés', 'بروز الأسلاك', 'buruz-aslak-kahraba', 2),
(22, 'Poteau usé', 'عمود مهترئ', 'amud-muhtari-kahraba', 3);
INSERT INTO subcategories (category_id, name, name_ar, slug, sort_order) VALUES
(23, 'Entretien insuffisant', 'نقص في العناية', 'naqs-enaya-fadaa', 1),
(23, 'Arrosage insuffisant', 'نقص في الري', 'naqs-ri-fadaa', 2),
(23, 'Taille insuffisante', 'نقص في تقليم الأشجار', 'naqs-taqleem-ashjar', 3);
INSERT INTO subcategories (category_id, name, name_ar, slug, sort_order) VALUES
(24, 'Taille insuffisante', 'نقص في تقليم الأشجار', 'naqs-taqleem-ashjar2', 1),
(24, 'Tuteurs manquants', 'غياب دواعم الأشجار', 'ghiyab-dawaam', 2),
(24, 'Arbre penché', 'شجرة آيلة للسقوط', 'shajara-aila', 3),
(24, 'Gestion troncs non conforme', 'نقص في تجيير جذوع الأشجار', 'naqs-tajir-judhu', 4),
(24, 'Gestion non conforme', 'تجيير غير مطابق للمعايير', 'tajir-ghayr-mutabiq', 5);
INSERT INTO subcategories (category_id, name, name_ar, slug, sort_order) VALUES
(25, 'Présence herbes nuisibles', 'وجود حشائش ضارة', 'wujud-hashash', 1);
INSERT INTO subcategories (category_id, name, name_ar, slug, sort_order) VALUES
(26, 'Éboulis', 'إنهيار صخري', 'inihar-sakhri2', 1);
INSERT INTO subcategories (category_id, name, name_ar, slug, sort_order) VALUES
(27, 'Avancée sable', 'زحف الرمال', 'zahaf-arlamal2', 1);
INSERT INTO subcategories (category_id, name, name_ar, slug, sort_order) VALUES
(28, 'Érosion', 'إنجراف التربة', 'injiraf-alturba2', 1);
INSERT INTO subcategories (category_id, name, name_ar, slug, sort_order) VALUES
(29, 'Entretien insuffisant', 'نقص في العناية بالمنحدرات', 'naqs-enaya-manahid', 1);
INSERT INTO subcategories (category_id, name, name_ar, slug, sort_order) VALUES
(30, 'Panneau cassé', 'لوحة محطمة', 'lawha-muhatama', 1),
(30, 'Panneau penché', 'لوحة آيلة للسقوط', 'lawha-aila', 2),
(30, 'Panneau caché', 'محجوب عن الرؤية', 'mahjub-ruya', 3);
INSERT INTO subcategories (category_id, name, name_ar, slug, sort_order) VALUES
(31, 'Manque peinture', 'نقص في الطلاء', 'naqs-talaa-isharat', 1);
INSERT INTO subcategories (category_id, name, name_ar, slug, sort_order) VALUES
(32, 'Abribus cassé', 'تحطم الواقية', 'tahattum-alwakiya', 1),
(32, 'Abribus manquant', 'غياب الواقية', 'ghiyab-alwakiya', 2),
(32, 'Panneau station manquant', 'غياب لافتة إسم الموقف', 'ghiyab-lafit', 3),
(32, 'Graffiti', 'وجود كتابات على الواقية', 'wujud-kitabat', 4);
INSERT INTO subcategories (category_id, name, name_ar, slug, sort_order) VALUES
(33, 'Manque peinture', 'نقص في الطلاء', 'naqs-talaa-hadid', 1),
(33, 'Barrière manquante', 'غياب الحواجز', 'ghiyab-hawajiz-hadid', 2),
(33, 'Barrière dégradée', 'حاجز مهترئ', 'hajiz-muhtari-hadid', 3);
INSERT INTO subcategories (category_id, name, name_ar, slug, sort_order) VALUES
(34, 'Effondrement partielle', 'تحطم جزء من الجسر', 'tahattum-juz-jasr', 1),
(34, 'Sol usé', 'إهتراء أرضية الجسر', 'ihtira-ardh-jasr', 2),
(34, 'Escalier usé', 'إهتراء الدرج', 'ihtira-udruj', 3),
(34, 'Garde-corps usé', 'إهتراء حاجز الحماية', 'ihtira-hajiz-himaya', 4);
INSERT INTO subcategories (category_id, name, name_ar, slug, sort_order) VALUES
(35, 'Panneau manquant', 'غياب اللافتة', 'ghiyab-lafita-madkhal', 1),
(35, 'Non aménagé', 'غير مهيأ', 'ghayr-muhaa', 2),
(35, 'Panneau usé', 'لافتة مهترئة', 'lafita-muhtari', 3);
INSERT INTO subcategories (category_id, name, name_ar, slug, sort_order) VALUES
(36, 'Stationnement gênant', 'إعاقة المرور', 'iaqat-almarur', 1);
INSERT INTO subcategories (category_id, name, name_ar, slug, sort_order) VALUES
(37, 'Station dégradée', 'محطة مهترئة', 'mahatta-muhtari', 1);
INSERT INTO subcategories (category_id, name, name_ar, slug, sort_order) VALUES
(38, 'Arrêt dégradé', 'موقف مهترئ', 'mauqif-muhtari', 1);
INSERT INTO subcategories (category_id, name, name_ar, slug, sort_order) VALUES
(39, 'Chantier abandonné', 'أشغال مهملة', 'ashghal-muhmala', 1);
INSERT INTO subcategories (category_id, name, name_ar, slug, sort_order) VALUES
(40, 'Drapeau manquant', 'غياب الراية', 'ghiyab-alriya', 1),
(40, 'Drapeau déchiré', 'راية ممزقة', 'riya-mumazzaqa', 2),
(40, 'Mât usé', 'عمود الراية مهترئ', 'amud-riya-muhtari', 3),
(40, 'Mât penché', 'عمود الراية آيل للسقوط', 'amud-riya-ail', 4);
INSERT INTO subcategories (category_id, name, name_ar, slug, sort_order) VALUES
(41, 'Fontaine défectueuse', 'نافورة معطلة', 'nafura-muattala', 1),
(41, 'Fontaine sale', 'نافورة متسخة', 'nafura-mutakhkha', 2),
(41, 'Fuite fontaine', 'تسرب مياه النافورة', 'tasarrub-nafura', 3);
INSERT INTO subcategories (category_id, name, name_ar, slug, sort_order) VALUES
(42, 'Protection civile', 'الحماية المدنية', 'himaya-madaniya', 1),
(42, 'Police', 'الشرطة', 'shurta', 2),
(42, 'Administrateur', 'المتصرف', 'mutasarrif', 3),
(42, 'Gendarmerie', 'الدرك الوطني', 'dark-watani', 4),
(42, 'Toilettes', 'مرحاض', 'mirhad', 5);
INSERT INTO subcategories (category_id, name, name_ar, slug, sort_order) VALUES
(43, 'Fontaine défectueuse', 'نافورة عمومية معطلة', 'nafura-amma-muattala', 1);
INSERT INTO subcategories (category_id, name, name_ar, slug, sort_order) VALUES
(44, 'Sans plaque', 'بدون لوحة ترقيم', 'bidun-lawha', 1),
(44, 'Avec plaque', 'بلوحة ترقيم', 'bilawha', 2);
INSERT INTO subcategories (category_id, name, name_ar, slug, sort_order) VALUES
(45, 'Barrières', 'حواجز', 'hawajiz-tarik', 1),
(45, 'Vente informelle', 'بيع العشوائي للخضر والفواكه', 'bay-eishwi-khudar', 2),
(45, 'Extension illégale', 'إستحواذ غير قانوني (تمديد البناية)', 'istihdid-ghayr-qanuni', 3);
INSERT INTO subcategories (category_id, name, name_ar, slug, sort_order) VALUES
(46, 'Parasols et chaises', 'المظلات والكراسي', 'mazallat-kerasi', 1),
(46, 'Parking', 'مواقف السيارات', 'mawaqif-sayarat-shati', 2);
INSERT INTO subcategories (category_id, name, name_ar, slug, sort_order) VALUES
(47, 'Mur penché', 'جدار آيل للسقوط', 'jidar-ail', 1),
(47, 'Balcon penché', 'شرفة منزل آيلة للسقوط', 'sharfa-manzil-aila', 2),
(47, 'Mur dégradé', 'جدار مهترئ', 'jidar-muhtari', 3),
(47, 'Graffiti', 'كتابات على الجدران', 'kitabat-jidran', 4),
(47, 'Manque peinture', 'نقص في الطلاء', 'naqs-talaa-wajihat', 5);
INSERT INTO subcategories (category_id, name, name_ar, slug, sort_order) VALUES
(48, 'Effondrement', 'بناية مهدمة', 'binaya-muhadama2', 1);
INSERT INTO subcategories (category_id, name, name_ar, slug, sort_order) VALUES
(49, 'Ascenseur usé', 'مصعد مهترئ', 'masad-muhtari', 1),
(49, 'Ascenseur en panne', 'مصعد معطل', 'masad-muattal', 2);
INSERT INTO subcategories (category_id, name, name_ar, slug, sort_order) VALUES
(50, 'Terrain usé', 'ملعب مهترئ', 'malab-muhtari', 1);
INSERT INTO subcategories (category_id, name, name_ar, slug, sort_order) VALUES
(51, 'Présence bidonvilles', 'وجود بيوت قصديرية', 'wujud-buyut-qasdir', 1);
INSERT INTO subcategories (category_id, name, name_ar, slug, sort_order) VALUES
(52, 'Jeux usés', 'اهتراء العاب الأطفال', 'ihtira-alab-atfal', 1);
INSERT INTO subcategories (category_id, name, name_ar, slug, sort_order) VALUES
(53, 'Cave dégradée', 'قبو مهترئ', 'qabu-muhtari', 1),
(53, 'Couvercle manquant', 'غياب الغطاء', 'ghiyab-ghataya-qabu', 2);
INSERT INTO subcategories (category_id, name, name_ar, slug, sort_order) VALUES
(54, 'Panneau installé', 'منصبة', 'mansuba', 1),
(54, 'Panneau non installé', 'غير منصبة', 'ghayr-mansuba', 2);
INSERT INTO subcategories (category_id, name, name_ar, slug, sort_order) VALUES
(55, 'Escalier usé', 'مهترئة', 'muhtari-salalim', 1),
(55, 'Manque lavage', 'نقص في الغسل', 'naqs-ghasl-salalim', 2);
INSERT INTO subcategories (category_id, name, name_ar, slug, sort_order) VALUES
(56, 'Échafaudage usé', 'دعامة مهترئة', 'ddaama-muhtari', 1);
INSERT INTO subcategories (category_id, name, name_ar, slug, sort_order) VALUES
(57, 'Panneau manquant', 'غياب', 'ghiyab-lafita-ishh', 1),
(57, 'Texte illisible', 'كتابة غير واضحة', 'kitaba-ghayr-wadha', 2);
INSERT INTO subcategories (category_id, name, name_ar, slug, sort_order) VALUES
(58, 'Plage sale', 'شاطئ قذر', 'shati-azir', 1),
(58, 'Corniche dégradée', 'كورنيش متضرر', 'kurnish-mudawar', 2),
(58, 'Dépôt sauvage', 'مخلفات عشوائية على الشاطئ', 'makhalfat-shati', 3),
(58, 'Animal errant', 'حيوان ضال على الشاطئ', 'hayawan-dal-shati', 4);
INSERT INTO subcategories (category_id, name, name_ar, slug, sort_order) VALUES
(59, 'Chiens', 'كلاب', 'kilab', 1),
(59, 'Chats', 'قطط', 'qitat', 2),
(59, 'Cochons', 'خنازير', 'khnazir', 3);
INSERT INTO subcategories (category_id, name, name_ar, slug, sort_order) VALUES
(60, 'Présence barques', 'وجود قوارب صيد', 'wujud-qawarib', 1);
INSERT INTO subcategories (category_id, name, name_ar, slug, sort_order) VALUES
(61, 'Tombe dégradée', 'قبر محطم', 'qabr-muhatam', 1);
INSERT INTO subcategories (category_id, name, name_ar, slug, sort_order) VALUES
(62, 'Béton obstrué', 'إنسداد الأنابيب الإسمنتية', 'insidad-anabib-isam', 1),
(62, 'Déversement direct', 'الصب المباشر لمياه الوادي', 'sab-mubashar-awdiya', 2);
INSERT INTO subcategories (category_id, name, name_ar, slug, sort_order) VALUES
(63, 'Déversement source', 'صب مياه الينابيع', 'sab-miyah-yanabi', 1);
INSERT INTO subcategories (category_id, name, name_ar, slug, sort_order) VALUES
(64, 'Arroseur usé', 'مهترئ', 'muhtari-mrashshat', 1);
INSERT INTO subcategories (category_id, name, name_ar, slug, sort_order) VALUES
(65, 'Poteau penché', 'عمود آيل للسقوط', 'amud-ail-hatif', 1),
(65, 'Câbles exposés', 'بروز الأسلاك', 'buruz-aslak-hatif2', 2),
(65, 'Poteau usé', 'عمود مهترئ', 'amud-muhtari-hatif', 3);
INSERT INTO subcategories (category_id, name, name_ar, slug, sort_order) VALUES
(66, 'Rocher mobile', 'صخرة متدلية', 'sakhra-mutadalla', 1);
INSERT INTO subcategories (category_id, name, name_ar, slug, sort_order) VALUES
(67, 'Nid-de-poule rapide', 'حفرة في الطريق السريع', 'hafra-tarik-sari', 1);
INSERT INTO subcategories (category_id, name, name_ar, slug, sort_order) VALUES
(68, 'Cimetière sale', 'مقبرة قذرة', 'maqbara-azra', 1),
(68, 'Clôture dégradée', 'سياج مقبرة متضرر', 'siyaj-maqbara', 2),
(68, 'Entretien nécessaire', 'صيانة ضرورية', 'siyana-daruriya', 3);
INSERT INTO subcategories (category_id, name, name_ar, slug, sort_order) VALUES
(69, 'Réservoir manquant', 'غياب', 'ghiyab-khazan', 1),
(69, 'Réservoir usé', 'مهترئ', 'muhtari-khazan', 2);
INSERT INTO subcategories (category_id, name, name_ar, slug, sort_order) VALUES
(70, 'Autre', 'أخرى', 'ukhra-gen', 1);
INSERT INTO organization_rules (organization_id, category_id, subcategory_id, daira_id, priority_order) VALUES
(1, 1, NULL, NULL, 1),   -- نفايات منزلية → NETCOM
(1, 2, NULL, NULL, 1),   -- عملية الكنس → NETCOM
(1, 3, NULL, NULL, 1),   -- حاويات القمامة → NETCOM
(1, 4, NULL, NULL, 1),   -- سلات المهملات → NETCOM
(1, 5, NULL, NULL, 1),   -- مخلفات بحرية → NETCOM
(1, 6, NULL, NULL, 1);   -- نفايات خضراء → NETCOM
INSERT INTO organization_rules (organization_id, category_id, subcategory_id, daira_id, priority_order) VALUES
(2, 7, NULL, NULL, 1),   -- النفايات الصلبة → ASROUT
(2, 8, NULL, NULL, 1),   -- نفايات مختلطة → ASROUT
(2, 9, NULL, NULL, 1),   -- بالوعات → ASROUT
(2, 10, NULL, NULL, 1),  -- تسرب المياه الصالحة → ASROUT
(2, 11, NULL, NULL, 1),  -- تسرب مياه الصرف الصحي → ASROUT
(2, 12, NULL, NULL, 1),  -- أغطية قنوات الصرف الصحي → ASROUT
(2, 13, NULL, NULL, 1),  -- خزان الصرف الصحي → ASROUT
(2, 14, NULL, NULL, 1),  -- ركود المياه → ASROUT
(2, 15, NULL, NULL, 1),  -- أرصفة → ASROUT
(2, 16, NULL, NULL, 1),  -- الطريق → ASROUT
(2, 17, NULL, NULL, 1),  -- حواجز إسمنتية → ASROUT
(2, 18, NULL, NULL, 1);  -- أقبية الأسلاك الهاتفية → ASROUT
INSERT INTO organization_rules (organization_id, category_id, subcategory_id, daira_id, priority_order) VALUES
(4, 19, NULL, NULL, 1),  -- الإنارة العمومية → ERMA
(4, 20, NULL, NULL, 1),  -- الساعات → ERMA
(4, 21, NULL, NULL, 1),  -- الساعات العامة → ERMA
(4, 22, NULL, NULL, 1);  -- حالة الأعمدة الكهربائية → ERMA
INSERT INTO organization_rules (organization_id, category_id, subcategory_id, daira_id, priority_order) VALUES
(15, 23, NULL, NULL, 1),  -- نقص العناية بالمساحة الخضراء → EDEVAL
(15, 24, NULL, NULL, 1),  -- العناية بالأشجار → EDEVAL
(15, 25, NULL, NULL, 1),  -- حشائش ضارة → EDEVAL
(15, 26, NULL, NULL, 1),  -- إنهيار صخري → EDEVAL
(15, 27, NULL, NULL, 1),  -- زحف الرمال → EDEVAL
(15, 28, NULL, NULL, 1),  -- إنجراف التربة → EDEVAL
(15, 29, NULL, NULL, 1);  -- المنحدرات → EDEVAL
INSERT INTO organization_rules (organization_id, category_id, subcategory_id, daira_id, priority_order) VALUES
(6, 30, NULL, NULL, 1),  -- الإشارات العمومية → EGCTU
(6, 31, NULL, NULL, 1),  -- الإشارات الأفقية → EGCTU
(6, 32, NULL, NULL, 1),  -- واقيات مواقف الحافلات → EGCTU
(6, 33, NULL, NULL, 1),  -- حواجز حديدية → EGCTU
(6, 34, NULL, NULL, 1),  -- جسر المشاة → EGCTU
(6, 35, NULL, NULL, 1),  -- مدخل الموقف → EGCTU
(6, 36, NULL, NULL, 1),  -- موقف سيارات → EGCTU
(6, 37, NULL, NULL, 1),  -- محطة حضرية → EGCTU
(6, 38, NULL, NULL, 1),  -- مواقف الحافلات → EGCTU
(6, 39, NULL, NULL, 1);  -- أشغال طور الإنجاز → EGCTU
INSERT INTO organization_rules (organization_id, category_id, subcategory_id, daira_id, priority_order) VALUES
(5, 40, NULL, NULL, 1),  -- الرايات الوطنية → EMCU
(5, 41, NULL, NULL, 1),  -- نافورات المياه → EMCU
(5, 42, NULL, NULL, 1),  -- مقصورات → EMCU
(5, 43, NULL, NULL, 1);  -- النافورات العمومية → EMCU
INSERT INTO organization_rules (organization_id, category_id, subcategory_id, daira_id, priority_order) VALUES
(9, 44, NULL, NULL, 1),  -- سيارات مهترئة → البلدية
(9, 45, NULL, NULL, 1),  -- الإستغلال الغير قانوني للطريق العام → البلدية
(9, 46, NULL, NULL, 1),  -- الإستغلال الغير قانوني للشواطئ → البلدية
(9, 47, NULL, NULL, 1),  -- واجهات المباني → البلدية
(9, 48, NULL, NULL, 1),  -- بناية مهدمة → البلدية
(9, 49, NULL, NULL, 1),  -- حالة المصاعد → البلدية
(9, 50, NULL, NULL, 1),  -- الملاعب الجوارية → البلدية
(9, 51, NULL, NULL, 1),  -- بيوت قصديرية → البلدية
(9, 52, NULL, NULL, 1),  -- فضاء التسلية → البلدية
(9, 53, NULL, NULL, 1),  -- أقبية → البلدية
(9, 54, NULL, NULL, 1),  -- لوحات إشهارية → البلدية
(9, 55, NULL, NULL, 1),  -- حالة السلالم والأدراج → البلدية
(9, 56, NULL, NULL, 1),  -- الدعامات → البلدية
(9, 57, NULL, NULL, 1);  -- لافتات إشهارية → البلدية
INSERT INTO organization_rules (organization_id, category_id, subcategory_id, daira_id, priority_order) VALUES
(3, 58, NULL, NULL, 1),  -- لافتات/شاطئ → HUPE
(3, 59, NULL, NULL, 1),  -- حيونات ضالة → HUPE
(3, 60, NULL, NULL, 1),  -- قوارب الصيد → HUPE
(3, 61, NULL, NULL, 1);  -- وضعية القبور → HUPE
INSERT INTO organization_rules (organization_id, category_id, subcategory_id, daira_id, priority_order) VALUES
(11, 62, NULL, NULL, 1),  -- أودية → DRE
(11, 63, NULL, NULL, 1),  -- الينابيع → DRE
(11, 64, NULL, NULL, 1),  -- مرشات → DRE
(11, 65, NULL, NULL, 1),  -- عمود أسلاك الهاتف → DRE
(11, 66, NULL, NULL, 1);  -- صخور متدلية → DRE
INSERT INTO organization_rules (organization_id, category_id, subcategory_id, daira_id, priority_order) VALUES
(10, 67, NULL, NULL, 1);  -- طريق سريع → DTP
INSERT INTO organization_rules (organization_id, category_id, subcategory_id, daira_id, priority_order) VALUES
(18, 68, NULL, NULL, 1);  -- مقبرة → EGPFC
INSERT INTO organization_rules (organization_id, category_id, subcategory_id, daira_id, priority_order) VALUES
(13, 69, NULL, NULL, 1);  -- خزان المياه → GESTIMMO
DELETE FROM categories WHERE id IN (4, 5, 6, 10, 11, 12, 13, 14, 15, 20, 21, 22, 23, 24, 25);
SET FOREIGN_KEY_CHECKS = 1;
SELECT c.id, c.name_ar, c.slug, o.code as org, COUNT(sc.id) as subcats
FROM categories c
LEFT JOIN organizations o ON c.organization_id = o.id
LEFT JOIN subcategories sc ON sc.category_id = c.id
GROUP BY c.id
ORDER BY c.id;