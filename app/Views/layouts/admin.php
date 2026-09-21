<?php
$adminName = session()->get('admin_name') ?? 'Administrator';
$adminRole = session()->get('admin_role') ?? 'Superadmin';
$companyName = $settings['company_name'] ?? 'Grand Harmoni Residence';
$primary = $settings['primary_color'] ?? '#0d6efd';
?>
<!doctype html>
<html lang="id">
  <!--begin::Head-->
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title><?= esc($title ?? 'Dashboard') ?> | <?= esc($companyName) ?> Admin</title>

    <!--begin::Theme Init-->
    <script>
      (() => {
        'use strict';
        const root = document.documentElement;
        if (root.getAttribute('data-lte-color-mode') === 'off') {
          return;
        }
        const STORAGE_KEY = 'lte-theme';
        let stored = null;
        try {
          stored = localStorage.getItem(STORAGE_KEY);
        } catch {}
        const authored = root.getAttribute('data-bs-theme');
        let resolved = 'light';
        if (stored === 'dark' || stored === 'light') {
          resolved = stored;
        } else if (authored === 'dark' || authored === 'light') {
          resolved = authored;
        } else if (globalThis.matchMedia('(prefers-color-scheme: dark)').matches) {
          resolved = 'dark';
        }
        root.setAttribute('data-bs-theme', resolved);
        root.style.colorScheme = resolved;
        if (resolved !== authored) {
          root.setAttribute('data-lte-theme-resolved', '');
        }
      })();
    </script>
    <!--end::Theme Init-->

    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta name="csrf-token" content="<?= csrf_hash() ?>">
    <meta name="csrf-header" content="<?= csrf_header() ?>">

    <!--begin::Fonts-->
    <link
      rel="stylesheet"
      href="https://cdn.jsdelivr.net/npm/@fontsource/source-sans-3@5.0.12/index.css"
      crossorigin="anonymous"
    />
    <!--end::Fonts-->

    <!--begin::Third Party Plugin(OverlayScrollbars)-->
    <link
      rel="stylesheet"
      href="https://cdn.jsdelivr.net/npm/overlayscrollbars@2.11.0/styles/overlayscrollbars.min.css"
      crossorigin="anonymous"
    />
    <!--end::Third Party Plugin(OverlayScrollbars)-->

    <!--begin::Third Party Plugin(Bootstrap Icons)-->
    <link
      rel="stylesheet"
      href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css"
      crossorigin="anonymous"
    />
    <!--end::Third Party Plugin(Bootstrap Icons)-->

    <!--begin::Required Plugin(AdminLTE)-->
    <link rel="stylesheet" href="<?= base_url('adminlte/css/adminlte.min.css') ?>" />
    <!--end::Required Plugin(AdminLTE)-->

    <?= $this->renderSection('styles') ?>
  </head>
  <!--end::Head-->

  <!--begin::Body-->
  <body class="layout-fixed sidebar-expand-lg bg-body-tertiary">
    <!--begin::App Wrapper-->
    <div class="app-wrapper">
      <!--begin::Header-->
      <nav class="app-header navbar navbar-expand bg-body">
        <!--begin::Container-->
        <div class="container-fluid">
          <!--begin::Start Navbar Links-->
          <ul class="navbar-nav">
            <li class="nav-item">
              <a class="nav-link" data-lte-toggle="sidebar" href="#" role="button" aria-label="Toggle sidebar">
                <i class="bi bi-list"></i>
              </a>
            </li>
            <li class="nav-item d-none d-md-block">
              <a href="<?= base_url() ?>" target="_blank" class="nav-link text-warning fw-semibold">
                <i class="bi bi-pencil-square me-1"></i> Editor Halaman Publik
              </a>
            </li>
            <li class="nav-item d-none d-md-block">
              <a href="<?= base_url() ?>" target="_blank" class="nav-link text-muted">
                <i class="bi bi-box-arrow-up-right me-1"></i> Live Website
              </a>
            </li>
          </ul>
          <!--end::Start Navbar Links-->

          <!--begin::End Navbar Links-->
          <ul class="navbar-nav ms-auto align-items-center">
            <!--begin::Fullscreen Toggle-->
            <li class="nav-item">
              <a class="nav-link" href="#" data-lte-toggle="fullscreen" aria-label="Toggle fullscreen">
                <i data-lte-icon="maximize" class="bi bi-arrows-fullscreen"></i>
                <i data-lte-icon="minimize" class="bi bi-fullscreen-exit d-none"></i>
              </a>
            </li>
            <!--end::Fullscreen Toggle-->

            <!--begin::Color Mode Toggle-->
            <li class="nav-item dropdown">
              <a
                class="nav-link"
                href="#"
                id="bd-theme"
                aria-label="Toggle color scheme"
                data-bs-toggle="dropdown"
                aria-expanded="false"
              >
                <i class="bi bi-sun-fill" data-lte-theme-icon="light"></i>
                <i class="bi bi-moon-fill d-none" data-lte-theme-icon="dark"></i>
                <i class="bi bi-circle-half d-none" data-lte-theme-icon="auto"></i>
              </a>
              <ul
                class="dropdown-menu dropdown-menu-end"
                aria-labelledby="bd-theme"
                style="--bs-dropdown-min-width: 8rem"
              >
                <li>
                  <button
                    type="button"
                    class="dropdown-item d-flex align-items-center"
                    data-bs-theme-value="light"
                    aria-pressed="false"
                  >
                    <i class="bi bi-sun-fill me-2"></i> Light
                    <i class="bi bi-check-lg ms-auto d-none"></i>
                  </button>
                </li>
                <li>
                  <button
                    type="button"
                    class="dropdown-item d-flex align-items-center"
                    data-bs-theme-value="dark"
                    aria-pressed="false"
                  >
                    <i class="bi bi-moon-fill me-2"></i> Dark
                    <i class="bi bi-check-lg ms-auto d-none"></i>
                  </button>
                </li>
                <li>
                  <button
                    type="button"
                    class="dropdown-item d-flex align-items-center active"
                    data-bs-theme-value="auto"
                    aria-pressed="true"
                  >
                    <i class="bi bi-circle-half me-2"></i> Auto
                    <i class="bi bi-check-lg ms-auto d-none"></i>
                  </button>
                </li>
              </ul>
            </li>
            <!--end::Color Mode Toggle-->

            <!--begin::User Menu Dropdown-->
            <li class="nav-item dropdown user-menu">
              <a href="#" class="nav-link dropdown-toggle d-flex align-items-center gap-2" data-bs-toggle="dropdown">
                <span class="user-image rounded-circle bg-primary text-white d-inline-flex align-items-center justify-content-center shadow-sm fw-bold" style="width: 32px; height: 32px; font-size: 0.85rem;">
                  <?= strtoupper(substr($adminName, 0, 1)) ?>
                </span>
                <span class="d-none d-md-inline"><?= esc($adminName) ?></span>
              </a>
              <ul class="dropdown-menu dropdown-menu-lg dropdown-menu-end">
                <!--begin::User Image Header-->
                <li class="user-header text-bg-primary">
                  <div class="rounded-circle bg-white text-primary d-inline-flex align-items-center justify-content-center fw-bold mx-auto mb-2 shadow" style="width: 60px; height: 60px; font-size: 1.5rem;">
                    <?= strtoupper(substr($adminName, 0, 1)) ?>
                  </div>
                  <p>
                    <?= esc($adminName) ?> - <?= esc($adminRole) ?>
                    <small><?= esc($companyName) ?></small>
                  </p>
                </li>
                <!--end::User Image Header-->

                <!--begin::Menu Footer-->
                <li class="user-footer">
                  <a href="<?= site_url('admin/settings') ?>" class="btn btn-default btn-outline-secondary">Pengaturan</a>
                  <a href="<?= site_url('admin/logout') ?>" class="btn btn-default btn-outline-danger float-end">Keluar</a>
                </li>
                <!--end::Menu Footer-->
              </ul>
            </li>
            <!--end::User Menu Dropdown-->
          </ul>
          <!--end::End Navbar Links-->
        </div>
        <!--end::Container-->
      </nav>
      <!--end::Header-->

      <!--begin::Sidebar-->
      <aside class="app-sidebar bg-body-secondary shadow" data-bs-theme="dark">
        <!--begin::Sidebar Brand-->
        <div class="sidebar-brand">
          <a href="<?= site_url('admin/dashboard') ?>" class="brand-link">
            <?php 
            $adminLogoSrc = '';
            if (!empty($settings['company_logo'])) {
                $cLogo = $settings['company_logo'];
                if (str_starts_with($cLogo, 'http://') || str_starts_with($cLogo, 'https://')) {
                    $adminLogoSrc = $cLogo;
                } elseif (file_exists(FCPATH . 'uploads/settings/' . $cLogo)) {
                    $adminLogoSrc = base_url('uploads/settings/' . $cLogo);
                } elseif (file_exists(FCPATH . ltrim($cLogo, '/'))) {
                    $adminLogoSrc = base_url(ltrim($cLogo, '/'));
                }
            }
            ?>
            <?php if (!empty($adminLogoSrc)): ?>
              <img src="<?= esc($adminLogoSrc) ?>" alt="Logo" class="brand-image opacity-75 shadow">
            <?php else: ?>
              <span class="brand-image bg-primary text-white rounded d-inline-flex align-items-center justify-content-center shadow-xs" style="width: 33px; height: 33px;">
                <i class="bi bi-buildings"></i>
              </span>
            <?php endif; ?>
            <span class="brand-text fw-light"><?= esc($companyName) ?></span>
          </a>
        </div>
        <!--end::Sidebar Brand-->

        <!--begin::Sidebar Wrapper-->
        <div class="sidebar-wrapper">
          <nav class="mt-2" aria-label="Main navigation">
            <ul class="nav sidebar-menu flex-column" data-lte-toggle="treeview" role="menu" data-accordion="false" id="navigation">
              <li class="nav-header">MANAJEMEN</li>

              <li class="nav-item">
                <a href="<?= site_url('admin/dashboard') ?>" class="nav-link <?= url_is('admin') || url_is('admin/dashboard') ? 'active' : '' ?>">
                  <i class="nav-icon bi bi-speedometer"></i>
                  <p>Dashboard</p>
                </a>
              </li>

              <li class="nav-item">
                <a href="<?= site_url('admin/leads') ?>" class="nav-link <?= url_is('admin/leads*') ? 'active' : '' ?>">
                  <i class="nav-icon bi bi-person-lines-fill"></i>
                  <p>Leads & Prospek</p>
                </a>
              </li>

              <li class="nav-item">
                <a href="<?= site_url('admin/properties') ?>" class="nav-link <?= url_is('admin/properties*') ? 'active' : '' ?>">
                  <i class="nav-icon bi bi-house-door-fill"></i>
                  <p>Katalog Properti</p>
                </a>
              </li>

              <li class="nav-item">
                <a href="<?= site_url('admin/siteplan') ?>" class="nav-link <?= url_is('admin/siteplan*') ? 'active' : '' ?>">
                  <i class="nav-icon bi bi-map-fill"></i>
                  <p>Master Siteplan</p>
                </a>
              </li>

              <li class="nav-item <?= url_is('admin/media*') ? 'menu-open' : '' ?>">
                <a href="#" class="nav-link <?= url_is('admin/media*') ? 'active' : '' ?>">
                  <i class="nav-icon bi bi-folder2-open"></i>
                  <p>
                    Media Manager
                    <i class="nav-arrow bi bi-chevron-right"></i>
                  </p>
                </a>
                <ul class="nav nav-treeview">
                  <li class="nav-item">
                    <a href="<?= site_url('admin/media') ?>" class="nav-link <?= (url_is('admin/media') || url_is('admin/media/manager')) ? 'active' : '' ?>">
                      <i class="nav-icon bi bi-hdd-network"></i>
                      <p>File Manager</p>
                    </a>
                  </li>
                  <li class="nav-item">
                    <a href="<?= site_url('admin/media/classic') ?>" class="nav-link <?= url_is('admin/media/classic*') ? 'active' : '' ?>">
                      <i class="nav-icon bi bi-images"></i>
                      <p>Galeri Klasik</p>
                    </a>
                  </li>
                </ul>
              </li>

              <li class="nav-item">
                <a href="<?= site_url('admin/articles') ?>" class="nav-link <?= url_is('admin/articles*') ? 'active' : '' ?>">
                  <i class="nav-icon bi bi-newspaper"></i>
                  <p>Artikel &amp; Berita</p>
                </a>
              </li>

              <li class="nav-header">CMS &amp; PENGATURAN</li>

              <li class="nav-item">
                <a href="<?= site_url('admin/sections') ?>" class="nav-link <?= url_is('admin/sections*') ? 'active' : '' ?>">
                  <i class="nav-icon bi bi-pencil-square text-warning"></i>
                  <p>
                    Editor Halaman Publik
                    <span class="badge bg-warning text-dark ms-1" style="font-size: 0.65rem;">CMS</span>
                  </p>
                </a>
              </li>

              <li class="nav-item">
                <a href="<?= site_url('admin/settings') ?>" class="nav-link <?= url_is('admin/settings*') ? 'active' : '' ?>">
                  <i class="nav-icon bi bi-palette-fill"></i>
                  <p>Tema &amp; Pengaturan</p>
                </a>
              </li>

              <?php if (in_array($adminRole, ['Developer', 'Superadmin'], true)): ?>
              <li class="nav-item">
                <a href="<?= site_url('admin/admins') ?>" class="nav-link <?= url_is('admin/admins*') ? 'active' : '' ?>">
                  <i class="nav-icon bi bi-shield-lock-fill"></i>
                  <p>Pengguna Admin</p>
                </a>
              </li>
              <?php endif; ?>

              <?php if ($adminRole === 'Developer'): ?>
              <li class="nav-header">DEVELOPER ONLY</li>
              <li class="nav-item">
                <a href="<?= site_url('admin/google-seo') ?>" class="nav-link <?= url_is('admin/google-seo*') ? 'active' : '' ?>">
                  <i class="nav-icon bi bi-google text-danger"></i>
                  <p>
                    Google &amp; SEO Suite
                    <span class="badge bg-danger ms-1" style="font-size: 0.65rem;">Dev</span>
                  </p>
                </a>
              </li>
              <?php endif; ?>

              <li class="nav-item mt-3 pt-2 border-top border-secondary border-opacity-25">
                <a href="<?= site_url('admin/logout') ?>" class="nav-link text-danger">
                  <i class="nav-icon bi bi-box-arrow-right text-danger"></i>
                  <p>Keluar Sistem</p>
                </a>
              </li>
            </ul>
          </nav>
        </div>
        <!--end::Sidebar Wrapper-->
      </aside>
      <!--end::Sidebar-->

      <!--begin::App Main-->
      <main class="app-main">
        <!--begin::App Content Header-->
        <div class="app-content-header">
          <div class="container-fluid">
            <div class="row">
              <div class="col-sm-6">
                <h3 class="mb-0"><?= esc($title ?? 'Dashboard') ?></h3>
              </div>
              <div class="col-sm-6">
                <ol class="breadcrumb float-sm-end">
                  <li class="breadcrumb-item"><a href="<?= site_url('admin/dashboard') ?>">Home</a></li>
                  <li class="breadcrumb-item active" aria-current="page"><?= esc($title ?? 'Dashboard') ?></li>
                </ol>
              </div>
            </div>
          </div>
        </div>
        <!--end::App Content Header-->

        <!--begin::App Content-->
        <div class="app-content">
          <div class="container-fluid">
            <!-- Flash Messages -->
            <?php if (session()->getFlashdata('success')): ?>
              <div class="alert alert-success alert-dismissible fade show d-flex align-items-center gap-2 mb-3" role="alert">
                <i class="bi bi-check-circle-fill fs-5"></i>
                <div><?= session()->getFlashdata('success') ?></div>
                <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert" aria-label="Close"></button>
              </div>
            <?php endif; ?>

            <?php if (session()->getFlashdata('error')): ?>
              <div class="alert alert-danger alert-dismissible fade show d-flex align-items-center gap-2 mb-3" role="alert">
                <i class="bi bi-exclamation-triangle-fill fs-5"></i>
                <div><?= session()->getFlashdata('error') ?></div>
                <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert" aria-label="Close"></button>
              </div>
            <?php endif; ?>

            <?= $this->renderSection('content') ?>
          </div>
        </div>
        <!--end::App Content-->
      </main>
      <!--end::App Main-->

      <!--begin::Footer-->
      <footer class="app-footer">
        <div class="float-end d-none d-sm-inline">
          Admin Control Panel &bull; CodeIgniter 4
        </div>
        <strong>
          Copyright &copy; <?= date('Y') ?> <a href="<?= base_url() ?>" class="text-decoration-none"><?= esc($companyName) ?></a>.
        </strong>
        All rights reserved.
      </footer>
      <!--end::Footer-->
    </div>
    <!--end::App Wrapper-->

    <!--begin::Script-->
    <!--begin::Third Party Plugin(OverlayScrollbars)-->
    <script
      src="https://cdn.jsdelivr.net/npm/overlayscrollbars@2.11.0/browser/overlayscrollbars.browser.es6.min.js"
      crossorigin="anonymous"
    ></script>
    <!--end::Third Party Plugin(OverlayScrollbars)-->

    <!--begin::Required Plugin(popperjs for Bootstrap 5)-->
    <script
      src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js"
      crossorigin="anonymous"
    ></script>
    <!--end::Required Plugin(popperjs for Bootstrap 5)-->

    <!--begin::Required Plugin(Bootstrap 5)-->
    <script
      src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
      crossorigin="anonymous"
    ></script>
    <!--end::Required Plugin(Bootstrap 5)-->

    <!--begin::Third Party Plugin(jQuery)-->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <!--end::Third Party Plugin(jQuery)-->

    <!--begin::Required Plugin(AdminLTE)-->
    <script src="<?= base_url('adminlte/js/adminlte.min.js') ?>"></script>
    <!--end::Required Plugin(AdminLTE)-->

    <!--begin::OverlayScrollbars Configure-->
    <script>
      const SELECTOR_SIDEBAR_WRAPPER = '.sidebar-wrapper';
      const Default = {
        scrollbarTheme: 'os-theme-light',
        scrollbarAutoHide: 'leave',
        scrollbarClickScroll: true,
      };
      document.addEventListener('DOMContentLoaded', function () {
        const sidebarWrapper = document.querySelector(SELECTOR_SIDEBAR_WRAPPER);
        const isMobile = window.innerWidth <= 992;
        if (
          sidebarWrapper &&
          typeof OverlayScrollbarsGlobal?.OverlayScrollbars !== 'undefined' &&
          !isMobile
        ) {
          OverlayScrollbarsGlobal.OverlayScrollbars(sidebarWrapper, {
            scrollbars: {
              theme: Default.scrollbarTheme,
              autoHide: Default.scrollbarAutoHide,
              clickScroll: Default.scrollbarClickScroll,
            },
          });
        }
      });
    </script>
    <!--end::OverlayScrollbars Configure-->

    <?= $this->renderSection('scripts') ?>
  </body>
  <!--end::Body-->
</html>
