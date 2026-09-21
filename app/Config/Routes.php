<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */

// ==========================================
// 1. PUBLIC FRONTEND ROUTES
// ==========================================
$routes->get('/', 'Home::index');
$routes->get('properti', 'Property::index');
$routes->get('properti/(:segment)', 'Property::detail/$1');
$routes->get('siteplan', 'Siteplan::index');
$routes->get('siteplan/(:num)', 'Siteplan::index/$1');
$routes->get('kpr-calculator', 'Kpr::index');
$routes->get('blog', 'Blog::index');
$routes->get('blog/(:segment)', 'Blog::detail/$1');
$routes->get('artikel', 'Blog::index');
$routes->get('artikel/(:segment)', 'Blog::detail/$1');
$routes->get('sitemap.xml', 'Sitemap::index');
$routes->get('sitemap', 'Sitemap::index');

// Lead Magnet & Brochure Download
$routes->post('leads/store', 'Lead::store');
$routes->get('brochure/download/(:num)', 'Brochure::download/$1');
$routes->get('brochure/download-global', 'Brochure::downloadGlobal');

// Gemini AI Virtual Assistant API Endpoint
$routes->post('api/chat/send', 'Chat::send');

// ==========================================
// 2. ADMIN AUTHENTICATION
// ==========================================
$routes->group('admin', function ($routes) {
    $routes->get('login', 'Auth::login');
    $routes->post('login', 'Auth::processLogin');
    $routes->get('logout', 'Auth::logout');
});

// ==========================================
// 3. ADMIN PANEL (PROTECTED WITH ADMIN GUARD)
// ==========================================
$routes->group('admin', ['filter' => 'admin_auth'], function ($routes) {
    $routes->get('/', 'Admin\Dashboard::index');
    $routes->get('dashboard', 'Admin\Dashboard::index');

    // Katalog Properti (Accessible by Superadmin & Admin/Sales)
    $routes->group('properties', function ($routes) {
        $routes->get('/', 'Admin\Properties::index');
        $routes->get('create', 'Admin\Properties::create');
        $routes->post('store', 'Admin\Properties::store');
        $routes->get('edit/(:num)', 'Admin\Properties::edit/$1');
        $routes->post('update/(:num)', 'Admin\Properties::update/$1');
        $routes->post('delete/(:num)', 'Admin\Properties::delete/$1');
        $routes->post('delete-image/(:num)', 'Admin\Properties::deleteImage/$1');
    });

    // Master Siteplan & Interactive Plotter
    $routes->group('siteplan', function ($routes) {
        $routes->get('/', 'Admin\Siteplan::index');
        $routes->post('store', 'Admin\Siteplan::store');
        $routes->get('builder/(:num)', 'Admin\Siteplan::builder/$1');
        $routes->post('save-pins/(:num)', 'Admin\Siteplan::savePins/$1');
        $routes->post('toggle-status/(:num)', 'Admin\Siteplan::toggleStatus/$1');
        $routes->post('activate/(:num)', 'Admin\Siteplan::activate/$1');
        $routes->post('deactivate/(:num)', 'Admin\Siteplan::deactivate/$1');
        $routes->post('delete/(:num)', 'Admin\Siteplan::delete/$1');
    });

    // Leads / Prospek (Viewable by Superadmin & Admin/Sales)
    $routes->group('leads', function ($routes) {
        $routes->get('/', 'Admin\Leads::index');
        $routes->post('delete/(:num)', 'Admin\Leads::delete/$1', ['filter' => 'role_guard:Superadmin']);
    });

    // Media Manager (elFinder 2.1.70 & Asset Library)
    $routes->group('media', function ($routes) {
        $routes->get('/', 'Admin\Media::manager');
        $routes->get('manager', 'Admin\Media::manager');
        $routes->get('popup', 'Admin\Media::popup');
        $routes->match(['get', 'post'], 'connector', 'Admin\Media::connector');
        $routes->get('classic', 'Admin\Media::index');
        $routes->post('upload', 'Admin\Media::upload');
        $routes->post('delete/(:num)', 'Admin\Media::delete/$1');
        $routes->get('list-json', 'Admin\Media::listJson');
    });

    // Manajemen Artikel & Berita (CMS Blog)
    $routes->group('articles', function ($routes) {
        $routes->get('/', 'Admin\Articles::index');
        $routes->get('create', 'Admin\Articles::create');
        $routes->post('store', 'Admin\Articles::store');
        $routes->get('edit/(:num)', 'Admin\Articles::edit/$1');
        $routes->post('update/(:num)', 'Admin\Articles::update/$1');
        $routes->post('delete/(:num)', 'Admin\Articles::delete/$1');
    });

    // Homepage Section Manager CMS
    $routes->group('sections', function ($routes) {
        $routes->get('/', 'Admin\Sections::index');
        $routes->post('reorder', 'Admin\Sections::reorder');
        $routes->post('toggle/(:num)', 'Admin\Sections::toggle/$1');
        $routes->get('edit/(:segment)', 'Admin\Sections::edit/$1');
        $routes->post('update/(:segment)', 'Admin\Sections::update/$1');
    });

    // Superadmin Exclusive Management: User Admins & Settings CMS
    $routes->group('', ['filter' => 'role_guard:Superadmin'], function ($routes) {
        // User Admin Management
        $routes->group('admins', function ($routes) {
            $routes->get('/', 'Admin\Admins::index');
            $routes->get('create', 'Admin\Admins::create');
            $routes->post('store', 'Admin\Admins::store');
            $routes->get('edit/(:num)', 'Admin\Admins::edit/$1');
            $routes->post('update/(:num)', 'Admin\Admins::update/$1');
            $routes->post('delete/(:num)', 'Admin\Admins::delete/$1');
        });

        // Web Settings & CMS
        $routes->group('settings', function ($routes) {
            $routes->get('/', 'Admin\Settings::index');
            $routes->post('update', 'Admin\Settings::update');
        });
    });

    // Developer Exclusive Management: Google & SEO Suite
    $routes->group('google-seo', ['filter' => 'role_guard:Developer'], function ($routes) {
        $routes->get('/', 'Admin\GoogleSeo::index');
        $routes->post('update', 'Admin\GoogleSeo::update');
    });
});

