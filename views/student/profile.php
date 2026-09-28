<?php
$pageTitle = 'My Profile';
require ROOT_PATH . '/views/layouts/header.php';

$studentModel = new Student();
$studentId = (int) Session::get('student_id');
$student = $studentModel->findById($studentId);
$picPath = BASE_URL . 'assets/uploads/profile/' . ($student['profile_pic'] ?: 'default.png');
?>
<div class="d-flex">
  <?php require ROOT_PATH . '/views/layouts/sidebar_student.php'; ?>

  <div class="app-content flex-grow-1">
    <?php require ROOT_PATH . '/views/layouts/topbar.php'; ?>
    <?php require ROOT_PATH . '/views/layouts/alerts.php'; ?>

    <div class="row g-3">
      <div class="col-lg-4">
        <div class="card p-4 text-center">
          <img src="<?= $picPath ?>" onerror="this.src='https://ui-avatars.com/api/?name=<?= urlencode($student['name']) ?>&background=4f46e5&color=fff'"
               class="rounded-circle mx-auto mb-3" width="120" height="120" style="object-fit:cover;">
          <h6 class="mb-0"><?= htmlspecialchars($student['name']) ?></h6>
          <p class="text-muted small mb-3">Roll No: <?= htmlspecialchars($student['roll_no']) ?></p>

          <form action="<?= BASE_URL ?>action.php?controller=Student&action=uploadProfilePic" method="POST" enctype="multipart/form-data">
            <input type="hidden" name="csrf_token" value="<?= Session::csrfToken() ?>">
            <input type="file" name="profile_pic" class="form-control form-control-sm mb-2" accept=".jpg,.jpeg,.png" required>
            <button class="btn btn-sm btn-outline-primary w-100"><i class="bi bi-upload"></i> Update Photo</button>
          </form>
        </div>
      </div>

      <div class="col-lg-8">
        <div class="card p-4">
          <h6 class="mb-3"><i class="bi bi-person-lines-fill text-primary"></i> Edit Profile</h6>
          <form action="<?= BASE_URL ?>action.php?controller=Student&action=updateProfile" method="POST" class="needs-validation" novalidate>
            <input type="hidden" name="csrf_token" value="<?= Session::csrfToken() ?>">

            <div class="row">
              <div class="col-md-6 mb-3">
                <label class="form-label">Name</label>
                <input type="text" class="form-control" value="<?= htmlspecialchars($student['name']) ?>" disabled>
              </div>
              <div class="col-md-6 mb-3">
                <label class="form-label">Email</label>
                <input type="text" class="form-control" value="<?= htmlspecialchars($student['email']) ?>" disabled>
              </div>
            </div>

            <div class="row">
              <div class="col-md-4 mb-3">
                <label class="form-label">Class</label>
                <input type="text" class="form-control" name="class" value="<?= htmlspecialchars($student['class']) ?>" required>
              </div>
              <div class="col-md-4 mb-3">
                <label class="form-label">Section</label>
                <input type="text" class="form-control" name="section" value="<?= htmlspecialchars($student['section'] ?? '') ?>">
              </div>
              <div class="col-md-4 mb-3">
                <label class="form-label">Phone</label>
                <input type="text" class="form-control" name="phone" value="<?= htmlspecialchars($student['phone'] ?? '') ?>">
              </div>
            </div>

            <div class="row">
              <div class="col-md-6 mb-3">
                <label class="form-label">Date of Birth</label>
                <input type="date" class="form-control" name="dob" value="<?= htmlspecialchars($student['dob'] ?? '') ?>">
              </div>
              <div class="col-md-6 mb-3">
                <label class="form-label">Gender</label>
                <select class="form-select" name="gender">
                  <option value="">Select</option>
                  <?php foreach (['Male','Female','Other'] as $g): ?>
                    <option value="<?= $g ?>" <?= ($student['gender'] ?? '') === $g ? 'selected' : '' ?>><?= $g ?></option>
                  <?php endforeach; ?>
                </select>
              </div>
            </div>

            <div class="mb-3">
              <label class="form-label">Address</label>
              <textarea class="form-control" name="address" rows="2"><?= htmlspecialchars($student['address'] ?? '') ?></textarea>
            </div>

            <button type="submit" class="btn btn-primary"><i class="bi bi-check2"></i> Save Changes</button>
          </form>
        </div>
      </div>
    </div>
  </div>
</div>

<?php require ROOT_PATH . '/views/layouts/footer.php'; ?>
