<?php
$currentPage = basename($_SERVER['PHP_SELF']);
$currentDir  = basename(dirname($_SERVER['PHP_SELF']));

function navItem(string $href, string $icon, string $label, string $dir): string {
    global $currentDir;
    $active = ($currentDir === $dir) ? 'active' : '';
    return '<li class="nav-item">
        <a class="nav-link ' . $active . '" href="' . $href . '">
            <i class="bi ' . $icon . ' me-2"></i>' . $label . '
        </a>
    </li>';
}
?>
<aside class="admin-sidebar" id="adminSidebar">
  <div class="sidebar-brand">
    <i class="bi bi-compass me-2"></i>Nayagara Tours
  </div>
  <ul class="nav flex-column px-2 mt-2">
    <li class="nav-item">
      <a class="nav-link <?= $currentDir === 'admin' ? 'active' : '' ?>"
         href="<?= ADMIN_URL ?>/index.php">
        <i class="bi bi-speedometer2 me-2"></i>Dashboard
      </a>
    </li>
    <?= navItem(ADMIN_URL . '/packages/index.php',  'bi-suitcase-lg',   'Packages',   'packages') ?>
    <?= navItem(ADMIN_URL . '/bookings/index.php',  'bi-calendar-check','Bookings',   'bookings') ?>
    <?= navItem(ADMIN_URL . '/inquiries/index.php', 'bi-envelope',      'Inquiries',  'inquiries') ?>
    <?= navItem(ADMIN_URL . '/gallery/index.php',   'bi-images',        'Gallery',    'gallery') ?>
    <?= navItem(ADMIN_URL . '/blog/index.php',      'bi-file-earmark-text', 'Blog',   'blog') ?>
    <?= navItem(ADMIN_URL . '/reviews/index.php',   'bi-star',          'Reviews',    'reviews') ?>
    <?= navItem(ADMIN_URL . '/banners/index.php',   'bi-image',         'Banners',    'banners') ?>
  </ul>

  <div class="sidebar-footer">
    <a href="<?= ADMIN_URL ?>/../index.html" target="_blank" class="btn btn-sm btn-outline-secondary w-100">
      <i class="bi bi-globe me-1"></i> View Website
    </a>
  </div>
</aside>
