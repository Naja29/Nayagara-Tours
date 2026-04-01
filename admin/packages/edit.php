<?php
session_start();
require_once __DIR__ . '/../config/db.php';
define('ADMIN_URL', '/nayagara-tours/admin');
require_once __DIR__ . '/../includes/auth.php';

$pdo = getPDO();
$id  = (int)($_GET['id'] ?? 0);

$pkg = $pdo->prepare('SELECT * FROM packages WHERE id = ?');
$pkg->execute([$id]);
$pkg = $pkg->fetch();

if (!$pkg) {
    header('Location: index.php'); exit;
}

$categories   = ['cultural','beach','wildlife','hill','honeymoon','adventure'];
$badgeLabels  = ['' => 'None', 'popular' => 'Popular', 'bestseller' => 'Best Seller',
                 'new' => 'New', 'limited' => 'Limited Spots', 'hotdeal' => 'Hot Deal'];
$difficulties = ['easy' => 'Easy', 'moderate' => 'Moderate', 'challenging' => 'Challenging'];
$errors       = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title        = trim($_POST['title']        ?? '');
    $slug         = trim($_POST['slug']         ?? '');
    $category     = $_POST['category']          ?? '';
    $duration     = trim($_POST['duration']     ?? '');
    $price        = $_POST['price']             ?? '';
    $old_price    = ($_POST['old_price'] ?? '') !== '' ? $_POST['old_price'] : null;
    $group_size   = trim($_POST['group_size']   ?? '');
    $description  = trim($_POST['description']  ?? '');
    $highlights   = trim($_POST['highlights']   ?? '');
    $itinerary    = trim($_POST['itinerary']    ?? '');
    $inclusions   = trim($_POST['inclusions']   ?? '');
    $exclusions   = trim($_POST['exclusions']   ?? '');
    $destinations = trim($_POST['destinations'] ?? '');
    $badge        = $_POST['badge']             ?? '';
    $best_season  = trim($_POST['best_season']  ?? '');
    $difficulty   = $_POST['difficulty']        ?? 'moderate';
    $rating       = ($_POST['rating'] ?? '') !== '' ? (float)$_POST['rating'] : null;
    $review_count = (int)($_POST['review_count'] ?? 0);
    $is_featured  = isset($_POST['is_featured']) ? 1 : 0;
    $is_active    = isset($_POST['is_active'])   ? 1 : 0;

    if ($title === '')    $errors[] = 'Title is required.';
    if ($slug === '')     $errors[] = 'Slug is required.';
    if ($category === '') $errors[] = 'Category is required.';
    if ($duration === '') $errors[] = 'Duration is required.';
    if ($price === '')    $errors[] = 'Price is required.';
    if ($description === '') $errors[] = 'Description is required.';

    // Slug unique (excluding self)
    if ($slug !== '') {
        $chk = $pdo->prepare('SELECT id FROM packages WHERE slug = ? AND id != ?');
        $chk->execute([$slug, $id]);
        if ($chk->fetch()) $errors[] = 'Slug already exists. Use a different one.';
    }

    // Handle new image upload
    $cover_image = $pkg['cover_image']; // keep existing by default
    if (!empty($_FILES['cover_image']['name'])) {
        $file    = $_FILES['cover_image'];
        $allowed = ['image/jpeg','image/png','image/webp'];
        if (!in_array($file['type'], $allowed)) {
            $errors[] = 'Image must be JPG, PNG or WEBP.';
        } elseif ($file['size'] > 25 * 1024 * 1024) {
            $errors[] = 'Image must be under 25MB.';
        } else {
            $ext  = pathinfo($file['name'], PATHINFO_EXTENSION);
            $name = 'pkg_' . time() . '_' . bin2hex(random_bytes(4)) . '.' . $ext;
            $dest = __DIR__ . '/../../uploads/packages/' . $name;
            if (move_uploaded_file($file['tmp_name'], $dest)) {
                // Delete old image
                if ($pkg['cover_image']) {
                    $old = __DIR__ . '/../../' . $pkg['cover_image'];
                    if (file_exists($old)) unlink($old);
                }
                $cover_image = 'uploads/packages/' . $name;
            } else {
                $errors[] = 'Failed to upload image.';
            }
        }
    }

    // Remove image if requested
    if (isset($_POST['remove_image'])) {
        if ($pkg['cover_image']) {
            $old = __DIR__ . '/../../' . $pkg['cover_image'];
            if (file_exists($old)) unlink($old);
        }
        $cover_image = null;
    }

    if (empty($errors)) {
        $stmt = $pdo->prepare('
            UPDATE packages SET
              title=?, slug=?, category=?, duration=?, price=?, old_price=?,
              group_size=?, description=?, highlights=?, itinerary=?,
              inclusions=?, exclusions=?, destinations=?, badge=?, best_season=?,
              difficulty=?, rating=?, review_count=?, cover_image=?, is_featured=?, is_active=?
            WHERE id=?
        ');
        $stmt->execute([
            $title, $slug, $category, $duration, $price, $old_price,
            $group_size, $description, $highlights, $itinerary,
            $inclusions, $exclusions, $destinations ?: null, $badge ?: null, $best_season ?: null,
            $difficulty, $rating, $review_count,
            $cover_image, $is_featured, $is_active, $id
        ]);
        header('Location: index.php?updated=1');
        exit;
    }

    // Repopulate with POST data on error
    $pkg = array_merge($pkg, $_POST);
}

$pageTitle = 'Edit Package';
include __DIR__ . '/../includes/header.php';
?>

<div class="page-header">
  <h1><i class="bi bi-pencil me-2 text-primary"></i>Edit Package</h1>
  <a href="index.php" class="btn btn-outline-secondary">
    <i class="bi bi-arrow-left me-1"></i> Back
  </a>
</div>

<?php if ($errors): ?>
  <div class="alert alert-danger">
    <i class="bi bi-exclamation-triangle me-1"></i>
    <ul class="mb-0 ps-3">
      <?php foreach ($errors as $e): ?>
        <li><?= htmlspecialchars($e) ?></li>
      <?php endforeach; ?>
    </ul>
  </div>
<?php endif; ?>

<form method="POST" enctype="multipart/form-data">
  <div class="row g-3">

    <!-- LEFT COLUMN -->
    <div class="col-lg-8">

      <div class="admin-card mb-3">
        <div class="card-header">Basic Information</div>
        <div class="p-3">
          <div class="mb-3">
            <label class="form-label">Package Title <span class="text-danger">*</span></label>
            <input type="text" name="title" id="titleInput" class="form-control"
                   value="<?= htmlspecialchars($pkg['title']) ?>" required>
          </div>
          <div class="mb-3">
            <label class="form-label">Slug <span class="text-danger">*</span></label>
            <input type="text" name="slug" id="slugInput" class="form-control"
                   value="<?= htmlspecialchars($pkg['slug']) ?>" required>
          </div>
          <div class="row g-3">
            <div class="col-sm-6">
              <label class="form-label">Category <span class="text-danger">*</span></label>
              <select name="category" class="form-select" required>
                <?php foreach ($categories as $cat): ?>
                  <option value="<?= $cat ?>" <?= $pkg['category'] === $cat ? 'selected' : '' ?>>
                    <?= ucfirst($cat) ?>
                  </option>
                <?php endforeach; ?>
              </select>
            </div>
            <div class="col-sm-6">
              <label class="form-label">Duration <span class="text-danger">*</span></label>
              <input type="text" name="duration" class="form-control"
                     value="<?= htmlspecialchars($pkg['duration']) ?>" required>
            </div>
            <div class="col-sm-6">
              <label class="form-label">Best Season</label>
              <input type="text" name="best_season" class="form-control"
                     value="<?= htmlspecialchars($pkg['best_season'] ?? '') ?>"
                     placeholder="e.g. December – April">
            </div>
            <div class="col-sm-6">
              <label class="form-label">Difficulty</label>
              <select name="difficulty" class="form-select">
                <?php foreach ($difficulties as $val => $label): ?>
                  <option value="<?= $val ?>" <?= ($pkg['difficulty'] ?? 'moderate') === $val ? 'selected' : '' ?>>
                    <?= $label ?>
                  </option>
                <?php endforeach; ?>
              </select>
            </div>
          </div>
        </div>
      </div>

      <div class="admin-card mb-3">
        <div class="card-header">Pricing</div>
        <div class="p-3">
          <div class="row g-3">
            <div class="col-sm-4">
              <label class="form-label">Price (USD) <span class="text-danger">*</span></label>
              <div class="input-group">
                <span class="input-group-text">$</span>
                <input type="number" name="price" class="form-control" step="0.01" min="0"
                       value="<?= htmlspecialchars($pkg['price']) ?>" required>
              </div>
            </div>
            <div class="col-sm-4">
              <label class="form-label">Old Price</label>
              <div class="input-group">
                <span class="input-group-text">$</span>
                <input type="number" name="old_price" class="form-control" step="0.01" min="0"
                       value="<?= htmlspecialchars($pkg['old_price'] ?? '') ?>">
              </div>
            </div>
            <div class="col-sm-4">
              <label class="form-label">Group Size</label>
              <input type="text" name="group_size" class="form-control"
                     value="<?= htmlspecialchars($pkg['group_size'] ?? '') ?>">
            </div>
          </div>
        </div>
      </div>

      <div class="admin-card mb-3">
        <div class="card-header">Description <span class="text-danger">*</span></div>
        <div class="p-3">
          <textarea name="description" class="form-control" rows="5" required><?= htmlspecialchars($pkg['description']) ?></textarea>
        </div>
      </div>

      <div class="admin-card mb-3">
        <div class="card-header">Destinations
          <small class="text-muted fw-normal ms-2">One per line, shown as location tags on the card</small>
        </div>
        <div class="p-3">
          <textarea name="destinations" class="form-control" rows="4"
                    placeholder="Sigiriya&#10;Dambulla&#10;Polonnaruwa&#10;Kandy"><?= htmlspecialchars($pkg['destinations'] ?? '') ?></textarea>
        </div>
      </div>

      <div class="admin-card mb-3">
        <div class="card-header">Highlights <small class="text-muted fw-normal ms-2">One per line</small></div>
        <div class="p-3">
          <textarea name="highlights" class="form-control" rows="5"><?= htmlspecialchars($pkg['highlights'] ?? '') ?></textarea>
        </div>
      </div>

      <div class="admin-card mb-3">
        <div class="card-header">Itinerary</div>
        <div class="p-3">
          <textarea name="itinerary" class="form-control" rows="8"><?= htmlspecialchars($pkg['itinerary'] ?? '') ?></textarea>
        </div>
      </div>

      <div class="row g-3">
        <div class="col-md-6">
          <div class="admin-card mb-3">
            <div class="card-header">Inclusions <small class="text-muted fw-normal ms-2">One per line</small></div>
            <div class="p-3">
              <textarea name="inclusions" class="form-control" rows="6"><?= htmlspecialchars($pkg['inclusions'] ?? '') ?></textarea>
            </div>
          </div>
        </div>
        <div class="col-md-6">
          <div class="admin-card mb-3">
            <div class="card-header">Exclusions <small class="text-muted fw-normal ms-2">One per line</small></div>
            <div class="p-3">
              <textarea name="exclusions" class="form-control" rows="6"><?= htmlspecialchars($pkg['exclusions'] ?? '') ?></textarea>
            </div>
          </div>
        </div>
      </div>

    </div>

    <!-- RIGHT COLUMN -->
    <div class="col-lg-4">

      <div class="admin-card mb-3">
        <div class="card-header">Cover Image</div>
        <div class="p-3">
          <?php if ($pkg['cover_image']): ?>
            <div id="currentImageWrap" class="mb-2">
              <img src="/nayagara-tours/<?= htmlspecialchars($pkg['cover_image']) ?>"
                   id="imagePreview"
                   style="width:100%;height:180px;object-fit:cover;border-radius:8px;">
            </div>
            <div class="form-check mb-2">
              <input class="form-check-input" type="checkbox" name="remove_image" id="removeImg">
              <label class="form-check-label text-danger small" for="removeImg">Remove current image</label>
            </div>
          <?php else: ?>
            <div id="imagePreviewWrap" class="mb-3 d-none">
              <img id="imagePreview" src="" style="width:100%;height:180px;object-fit:cover;border-radius:8px;">
            </div>
          <?php endif; ?>
          <input type="file" name="cover_image" id="imageInput"
                 class="form-control" accept="image/jpeg,image/png,image/webp">
          <div class="form-text">Upload new image to replace. JPG, PNG or WEBP. Max 3MB.</div>
        </div>
      </div>

      <!-- Badge & Rating -->
      <div class="admin-card mb-3">
        <div class="card-header">Badge & Rating</div>
        <div class="p-3">
          <div class="mb-3">
            <label class="form-label">Card Badge</label>
            <select name="badge" class="form-select">
              <?php foreach ($badgeLabels as $val => $label): ?>
                <option value="<?= $val ?>" <?= ($pkg['badge'] ?? '') === $val ? 'selected' : '' ?>>
                  <?= $label ?>
                </option>
              <?php endforeach; ?>
            </select>
            <div class="form-text">Shown as a label on the package image.</div>
          </div>
          <div class="row g-2">
            <div class="col-6">
              <label class="form-label">Rating</label>
              <input type="number" name="rating" class="form-control" step="0.1" min="0" max="5"
                     value="<?= htmlspecialchars($pkg['rating'] ?? '') ?>"
                     placeholder="4.9">
            </div>
            <div class="col-6">
              <label class="form-label">Review Count</label>
              <input type="number" name="review_count" class="form-control" min="0"
                     value="<?= htmlspecialchars($pkg['review_count'] ?? '0') ?>"
                     placeholder="128">
            </div>
          </div>
        </div>
      </div>

      <div class="admin-card mb-3">
        <div class="card-header">Settings</div>
        <div class="p-3">
          <div class="form-check form-switch mb-3">
            <input class="form-check-input" type="checkbox" name="is_active"
                   id="isActive" value="1" <?= $pkg['is_active'] ? 'checked' : '' ?>>
            <label class="form-check-label" for="isActive">
              <i class="bi bi-eye me-1"></i> Active
            </label>
          </div>
          <div class="form-check form-switch">
            <input class="form-check-input" type="checkbox" name="is_featured"
                   id="isFeatured" value="1" <?= $pkg['is_featured'] ? 'checked' : '' ?>>
            <label class="form-check-label" for="isFeatured">
              <i class="bi bi-star me-1"></i> Featured
            </label>
          </div>
        </div>
      </div>

      <div class="d-grid gap-2">
        <button type="submit" class="btn btn-primary">
          <i class="bi bi-check-lg me-1"></i> Update Package
        </button>
        <a href="index.php" class="btn btn-outline-secondary">Cancel</a>
      </div>

    </div>
  </div>
</form>

<script>
document.getElementById('titleInput').addEventListener('input', function () {
  const slugInput = document.getElementById('slugInput');
  if (slugInput.dataset.manual) return;
  slugInput.value = this.value
    .toLowerCase().trim()
    .replace(/[^a-z0-9\s-]/g, '')
    .replace(/\s+/g, '-')
    .replace(/-+/g, '-');
});
document.getElementById('slugInput').addEventListener('input', function () {
  this.dataset.manual = 'true';
});
document.getElementById('imageInput').addEventListener('change', function () {
  const preview = document.getElementById('imagePreview');
  const wrap    = document.getElementById('imagePreviewWrap');
  if (this.files && this.files[0]) {
    const reader = new FileReader();
    reader.onload = e => {
      preview.src = e.target.result;
      if (wrap) wrap.classList.remove('d-none');
    };
    reader.readAsDataURL(this.files[0]);
  }
});
</script>

<?php include __DIR__ . '/../includes/footer.php'; ?>
