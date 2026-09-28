<?php
/** Top bar shown inside app-content. Expects $pageTitle to be set. */
?>
<div class="app-topbar">
  <div class="d-flex align-items-center gap-3">
    <button class="btn btn-outline-secondary sidebar-toggle-btn"><i class="bi bi-list"></i></button>
    <h5 class="mb-0"><?= htmlspecialchars($pageTitle ?? '') ?></h5>
  </div>
  <div class="d-flex align-items-center gap-2">
    <span class="text-muted small d-none d-sm-inline">
      <i class="bi bi-person-fill"></i> <?= htmlspecialchars(Session::get('name') ?? '') ?>
      <span class="badge bg-secondary text-uppercase ms-1"><?= htmlspecialchars(Session::role() ?? '') ?></span>
    </span>
  </div>
</div>
