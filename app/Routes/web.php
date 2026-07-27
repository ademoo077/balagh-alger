<?php
return [
    // Public routes
    'GET /' => ['LandingController', 'landing'],
    'GET /api/landing-stats' => ['LandingController', 'statsApi'],
    'GET /login' => ['AuthController', 'loginForm'],
    'POST /login' => ['AuthController', 'login'],
    'POST /logout' => ['AuthController', 'logout'],
    'GET /register' => ['AuthController', 'registerForm'],
    'POST /register' => ['AuthController', 'register'],
    'GET /forgot-password' => ['AuthController', 'forgotPasswordForm'],
    'POST /forgot-password' => ['AuthController', 'forgotPassword'],
    
    // Profile
    'GET /profile' => ['UserController', 'profile'],
    'POST /profile' => ['UserController', 'updateProfile'],
    
    // Public tracking
    'GET /suivi' => ['TrackingController', 'index'],
    'GET /suivi/{code}' => ['TrackingController', 'show'],
    
    // Dashboard
    'GET /dashboard' => ['DashboardController', 'index'],
    
    // Reports
    'GET /reports' => ['ReportController', 'index'],
    'GET /reports/create' => ['ReportController', 'create'],
    'POST /reports/store' => ['ReportController', 'store'],
    'GET /reports/{id}' => ['ReportController', 'show'],
    'GET /reports/{id}/edit' => ['ReportController', 'edit'],
    'POST /reports/{id}/update' => ['ReportController', 'update'],
    'POST /reports/{id}/assign' => ['ReportController', 'assign'],
    'POST /reports/{id}/status' => ['ReportController', 'changeStatus'],
    'POST /reports/{id}/redirect' => ['ReportController', 'redirectReport'],
    'POST /reports/{id}/comment' => ['ReportController', 'comment'],
    'GET /reports/{id}/print' => ['ReportController', 'print'],
    'GET /reports/{id}/pdf' => ['ReportController', 'downloadPdf'],
    'POST /reports/{id}/rate' => ['ReportController', 'rate'],
    'GET /reports/export' => ['ReportController', 'export'],
    'GET /reports/export-monthly' => ['ReportController', 'exportMonthly'],
    
    // Interventions (workflow)
    'GET /interventions' => ['InterventionController', 'index'],
    'GET /interventions/{id}' => ['InterventionController', 'show'],
    'POST /interventions/{id}/assign' => ['InterventionController', 'assign'],
    'POST /interventions/{id}/start' => ['InterventionController', 'startIntervention'],
    'POST /interventions/{id}/photo' => ['InterventionController', 'uploadPhoto'],
    'POST /interventions/{id}/complete' => ['InterventionController', 'completeIntervention'],
    'POST /interventions/{id}/validate' => ['InterventionController', 'validateWork'],
    'POST /interventions/{id}/close' => ['InterventionController', 'close'],
    
    // Users
    'GET /users' => ['UserController', 'index'],
    'GET /users/create' => ['UserController', 'create'],
    'POST /users/store' => ['UserController', 'store'],
    'GET /users/{id}' => ['UserController', 'show'],
    'GET /users/{id}/edit' => ['UserController', 'edit'],
    'POST /users/{id}/update' => ['UserController', 'update'],
    'POST /users/{id}/delete' => ['UserController', 'delete'],
    'POST /users/{id}/status' => ['UserController', 'changeStatus'],
    
    // Organizations
    'GET /organizations' => ['OrganizationController', 'index'],
    'GET /organizations/create' => ['OrganizationController', 'create'],
    'POST /organizations/store' => ['OrganizationController', 'store'],
    'GET /organizations/{id}' => ['OrganizationController', 'show'],
    'GET /organizations/{id}/edit' => ['OrganizationController', 'edit'],
    'POST /organizations/{id}/update' => ['OrganizationController', 'update'],
    
    // Dairas
    'GET /dairas' => ['DairaController', 'index'],
    'GET /dairas/{id}' => ['DairaController', 'show'],
    
    // Categories
    'GET /categories' => ['CategoryController', 'index'],
    'GET /categories/create' => ['CategoryController', 'create'],
    'POST /categories' => ['CategoryController', 'store'],
    'GET /categories/{id}/edit' => ['CategoryController', 'edit'],
    'POST /categories/{id}' => ['CategoryController', 'update'],
    'POST /categories/{id}/delete' => ['CategoryController', 'destroy'],
    'POST /categories/{id}/toggle' => ['CategoryController', 'toggle'],
    
    // Notifications
    'GET /notifications' => ['NotificationController', 'index'],
    'POST /notifications/{id}/read' => ['NotificationController', 'markRead'],
    'POST /notifications/read-all' => ['NotificationController', 'markAllRead'],
    'GET /api/notifications/count' => ['NotificationController', 'count'],
    
    // Settings
    'GET /settings' => ['SettingController', 'index'],
    'POST /settings/update' => ['SettingController', 'update'],
    
    // Section Communes management (chef_unite assigns communes to chef_section)
    'GET /section-communes' => ['SectionCommuneController', 'index'],
    'POST /section-communes/assign' => ['SectionCommuneController', 'assign'],
    'POST /section-communes/remove' => ['SectionCommuneController', 'remove'],
    'GET /api/section-communes/{userId}' => ['SectionCommuneController', 'getByUser'],

    // Landing Page Management (admin_central only)
    'GET /admin/landing' => ['LandingPageController', 'index'],
    'GET /admin/landing/partners' => ['LandingPageController', 'partners'],
    'POST /admin/landing/partners/store' => ['LandingPageController', 'partnerStore'],
    'GET /admin/landing/partners/{id}/edit' => ['LandingPageController', 'partnerEdit'],
    'POST /admin/landing/partners/{id}' => ['LandingPageController', 'partnerUpdate'],
    'POST /admin/landing/partners/{id}/delete' => ['LandingPageController', 'partnerDelete'],
    'POST /admin/landing/partners/{id}/toggle' => ['LandingPageController', 'partnerToggle'],
    'GET /admin/landing/gallery' => ['LandingPageController', 'gallery'],
    'POST /admin/landing/gallery/store' => ['LandingPageController', 'galleryStore'],
    'POST /admin/landing/gallery/{id}/delete' => ['LandingPageController', 'galleryDelete'],
    'POST /admin/landing/gallery/{id}/toggle' => ['LandingPageController', 'galleryToggle'],
    'GET /admin/landing/testimonials' => ['LandingPageController', 'testimonials'],
    'GET /admin/landing/testimonials/create' => ['LandingPageController', 'testimonialCreate'],
    'POST /admin/landing/testimonials/store' => ['LandingPageController', 'testimonialStore'],
    'GET /admin/landing/testimonials/{id}/edit' => ['LandingPageController', 'testimonialEdit'],
    'POST /admin/landing/testimonials/{id}' => ['LandingPageController', 'testimonialUpdate'],
    'POST /admin/landing/testimonials/{id}/delete' => ['LandingPageController', 'testimonialDelete'],
    'POST /admin/landing/testimonials/{id}/toggle' => ['LandingPageController', 'testimonialToggle'],
    'GET /admin/landing/before-after' => ['LandingPageController', 'beforeAfter'],
    'GET /admin/landing/before-after/create' => ['LandingPageController', 'beforeAfterCreate'],
    'POST /admin/landing/before-after/store' => ['LandingPageController', 'beforeAfterStore'],
    'GET /admin/landing/before-after/{id}/edit' => ['LandingPageController', 'beforeAfterEdit'],
    'POST /admin/landing/before-after/{id}' => ['LandingPageController', 'beforeAfterUpdate'],
    'POST /admin/landing/before-after/{id}/delete' => ['LandingPageController', 'beforeAfterDelete'],
    'POST /admin/landing/before-after/{id}/toggle' => ['LandingPageController', 'beforeAfterToggle'],
    'GET /admin/landing/faq' => ['LandingPageController', 'faq'],
    'GET /admin/landing/faq/create' => ['LandingPageController', 'faqCreate'],
    'POST /admin/landing/faq/store' => ['LandingPageController', 'faqStore'],
    'GET /admin/landing/faq/{id}/edit' => ['LandingPageController', 'faqEdit'],
    'POST /admin/landing/faq/{id}' => ['LandingPageController', 'faqUpdate'],
    'POST /admin/landing/faq/{id}/delete' => ['LandingPageController', 'faqDelete'],
    'POST /admin/landing/faq/{id}/toggle' => ['LandingPageController', 'faqToggle'],
    'GET /admin/landing/settings' => ['LandingPageController', 'settings'],
    'POST /admin/landing/settings' => ['LandingPageController', 'settingsUpdate'],

    // Audit
    'GET /audit' => ['AuditController', 'index'],
    
    // API endpoints
    'GET /api/stats' => ['App\Controllers\Api\StatsController', 'index'],
    'GET /api/communes/{dairaId}' => ['App\Controllers\Api\DairaController', 'communes'],
    'GET /api/reports/map' => ['App\Controllers\Api\ReportController', 'map'],
    'GET /api/reports/heatmap' => ['App\Controllers\Api\HeatmapController', 'index'],
    'GET /api/reports/check-duplicate' => ['App\Controllers\Api\ReportController', 'checkDuplicate'],
    'GET /api/reports/search' => ['App\Controllers\Api\ReportController', 'search'],
    'GET /api/reports/similar' => ['App\Controllers\Api\ReportController', 'similar'],
    'GET /api/subcategories/{categoryId}' => ['App\Controllers\Api\CategoryController', 'subcategories'],
    'POST /api/set-lang' => ['App\Controllers\Api\LangController', 'set'],
    'POST /api/push/subscribe' => ['App\Controllers\Api\PushController', 'subscribe'],
    'POST /api/push/unsubscribe' => ['App\Controllers\Api\PushController', 'unsubscribe'],
    'GET /api/badges/my' => ['App\Controllers\Api\BadgeController', 'myBadges'],
    'GET /api/commune-ranking' => ['App\Controllers\Api\StatsController', 'communeRanking'],

    // Citizen Impact page
    'GET /impact' => ['DashboardController', 'impact'],

    // Citizen Interface (bottom nav, mobile-first)
    'GET /home' => ['CitizenController', 'home'],
    'GET /quick-report' => ['CitizenController', 'quickReport'],
    'POST /quick-report' => ['CitizenController', 'quickReportStore'],
    'GET /feed' => ['FeedController', 'index'],
    'POST /feed' => ['FeedController', 'store'],
    'POST /feed/{id}/like' => ['FeedController', 'like'],
    'POST /feed/{id}/comment' => ['FeedController', 'comment'],
    'GET /leaderboard' => ['CitizenController', 'leaderboard'],
    'GET /citizen/map' => ['CitizenController', 'map'],
    'GET /before-after' => ['CitizenController', 'beforeAfter'],
    'GET /my-profile' => ['CitizenController', 'profile'],
    'GET /my-profile/edit' => ['CitizenController', 'editProfile'],
    'POST /my-profile/edit' => ['CitizenController', 'updateProfile'],
];
