<?php
$pageTitle = 'Manage Students';
require ROOT_PATH . '/views/layouts/header.php';

$studentModel = new Student();
$search  = Validator::sanitize($_GET['search'] ?? '');
$class   = Validator::sanitize($_GET['class'] ?? '');
$section = Validator::sanitize($_GET['section'] ?? '');
$students = $studentModel->getAll($search, $class, $section);
$classes = $studentModel->getDistinctClasses();
?>
<div class="d-flex">
  <?php require ROOT_PATH . '/views/layouts/sidebar_admin.php'; ?>

  <div class="app-content flex-grow-1">
    <?php require ROOT_PATH . '/views/layouts/topbar.php'; ?>
    <?php require ROOT_PATH . '/views/layouts/alerts.php'; ?>

    <div class="d-flex justify-content-between align-items-center mb-3">
      <h6 class="mb-0"><i class="bi bi-people text-primary"></i> All Students (<?= count($students) ?>)</h6>
      <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#addStudentModal">
        <i class="bi bi-person-plus"></i> Add Student
      </button>
    </div>

    <!-- Search & filter -->
    <form class="row g-2 mb-3" method="GET" action="<?= BASE_URL ?>index.php">
      <input type="hidden" name="page" value="admin_manage_students">
      <div class="col-md-5">
        <input type="text" class="form-control" name="search" placeholder="Search by name, roll no, or email" value="<?= htmlspecialchars($search) ?>">
      </div>
      <div class="col-md-3">
        <input type="text" class="form-control" name="class" placeholder="Filter by class" value="<?= htmlspecialchars($class) ?>">
      </div>
      <div class="col-md-2">
        <input type="text" class="form-control" name="section" placeholder="Section" value="<?= htmlspecialchars($section) ?>">
      </div>
      <div class="col-md-2">
        <button class="btn btn-outline-primary w-100"><i class="bi bi-search"></i> Filter</button>
      </div>
    </form>

    <div class="card p-3">
      <div class="table-responsive">
        <table class="table table-hover align-middle">
          <thead><tr><th>Roll No</th><th>Name</th><th>Email</th><th>Class</th><th>Section</th><th>Phone</th><th>Actions</th></tr></thead>
          <tbody>
          <?php if (empty($students)): ?>
            <tr><td colspan="7" class="text-center text-muted">No students found.</td></tr>
          <?php else: foreach ($students as $s): ?>
            <tr>
              <td><?= htmlspecialchars($s['roll_no']) ?></td>
              <td><?= htmlspecialchars($s['name']) ?></td>
              <td><?= htmlspecialchars($s['email']) ?></td>
              <td><?= htmlspecialchars($s['class']) ?></td>
              <td><?= htmlspecialchars($s['section'] ?? '-') ?></td>
              <td><?= htmlspecialchars($s['phone'] ?? '-') ?></td>
              <td>
                <button class="btn btn-sm btn-outline-secondary" data-bs-toggle="modal" data-bs-target="#editModal<?= $s['id'] ?>"><i class="bi bi-pencil"></i></button>
                <a href="<?= BASE_URL ?>action.php?controller=Admin&action=deleteStudent&id=<?= $s['id'] ?>" class="btn btn-sm btn-outline-danger confirm-delete"><i class="bi bi-trash"></i></a>
              </td>
            </tr>

            <!-- Edit modal -->
            <div class="modal fade" id="editModal<?= $s['id'] ?>" tabindex="-1">
              <div class="modal-dialog">
                <form class="modal-content" action="<?= BASE_URL ?>action.php?controller=Admin&action=editStudent" method="POST">
                  <input type="hidden" name="csrf_token" value="<?= Session::csrfToken() ?>">
                  <input type="hidden" name="student_id" value="<?= $s['id'] ?>">
                  <div class="modal-header">
                    <h6 class="modal-title">Edit <?= htmlspecialchars($s['name']) ?></h6>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                  </div>
                  <div class="modal-body">
                    <div class="row">
                      <div class="col-6 mb-2"><label class="form-label small">Class</label><input class="form-control" name="class" value="<?= htmlspecialchars($s['class']) ?>" required></div>
                      <div class="col-6 mb-2"><label class="form-label small">Section</label><input class="form-control" name="section" value="<?= htmlspecialchars($s['section'] ?? '') ?>"></div>
                    </div>
                    <div class="row">
                      <div class="col-6 mb-2"><label class="form-label small">Phone</label><input class="form-control" name="phone" value="<?= htmlspecialchars($s['phone'] ?? '') ?>"></div>
                      <div class="col-6 mb-2"><label class="form-label small">DOB</label><input type="date" class="form-control" name="dob" value="<?= htmlspecialchars($s['dob'] ?? '') ?>"></div>
                    </div>
                    <div class="mb-2"><label class="form-label small">Gender</label>
                      <select class="form-select" name="gender">
                        <option value="">Select</option>
                        <?php foreach (['Male','Female','Other'] as $g): ?>
                          <option value="<?= $g ?>" <?= ($s['gender'] ?? '') === $g ? 'selected' : '' ?>><?= $g ?></option>
                        <?php endforeach; ?>
                      </select>
                    </div>
                    <div class="mb-2"><label class="form-label small">Address</label><textarea class="form-control" name="address" rows="2"><?= htmlspecialchars($s['address'] ?? '') ?></textarea></div>
                  </div>
                  <div class="modal-footer">
                    <button type="submit" class="btn btn-primary btn-sm">Save</button>
                  </div>
                </form>
              </div>
            </div>
          <?php endforeach; endif; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>

<!-- Add student modal -->
<div class="modal fade" id="addStudentModal" tabindex="-1">
  <div class="modal-dialog">
    <form class="modal-content" action="<?= BASE_URL ?>action.php?controller=Admin&action=addStudent" method="POST">
      <input type="hidden" name="csrf_token" value="<?= Session::csrfToken() ?>">
      <div class="modal-header">
        <h6 class="modal-title">Add New Student</h6>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <div class="mb-2"><label class="form-label small">Full Name</label><input class="form-control" name="name" required></div>
        <div class="mb-2"><label class="form-label small">Email</label><input type="email" class="form-control" name="email" required></div>
        <div class="row">
          <div class="col-6 mb-2"><label class="form-label small">Roll No</label><input class="form-control" name="roll_no" required></div>
          <div class="col-6 mb-2"><label class="form-label small">Phone</label><input class="form-control" name="phone"></div>
        </div>
        <div class="row">
          <div class="col-6 mb-2"><label class="form-label small">Class</label><input class="form-control" name="class" required></div>
          <div class="col-6 mb-2"><label class="form-label small">Section</label><input class="form-control" name="section"></div>
        </div>
        <div class="mb-2"><label class="form-label small">Temporary Password</label><input class="form-control" name="password" placeholder="Default: Student@123"></div>
      </div>
      <div class="modal-footer">
        <button type="submit" class="btn btn-primary btn-sm">Add Student</button>
      </div>
    </form>
  </div>
</div>

<?php require ROOT_PATH . '/views/layouts/footer.php'; ?>
