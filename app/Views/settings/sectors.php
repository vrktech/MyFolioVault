<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<div class="container-fluid px-0">
    <!-- Settings Sub-Navigation -->
    <?= $this->include('settings/_nav') ?>

    <!-- Equity Sector / Industry Master -->
    <div class="card border-0 shadow-sm rounded-4" id="sectors-pane">
        <div class="card-header bg-white border-bottom py-3 px-4 rounded-top-4 d-flex justify-content-between align-items-center flex-wrap gap-2">
            <div class="d-flex align-items-center">
                <div class="p-2 bg-success bg-opacity-10 text-success rounded-3 me-3">
                    <i class="bi bi-diagram-3 fs-5"></i>
                </div>
                <div>
                    <h5 class="fw-bold text-dark mb-0">Equities Sector / Industry Master</h5>
                    <small class="text-muted">Manage standard sectors and industries available in the Equities module dropdown</small>
                </div>
            </div>
            <div>
                <button type="button" class="btn btn-success btn-sm px-3 rounded-3 fw-semibold" data-bs-toggle="modal" data-bs-target="#addSectorModal">
                    <i class="bi bi-plus-circle me-1"></i>Add New Sector
                </button>
            </div>
        </div>
        <div class="card-body p-4">
            <?php if (empty($sectors)): ?>
                <div class="text-center py-5 text-muted">
                    <i class="bi bi-diagram-3 fs-1 d-block mb-3 text-secondary"></i>
                    <p class="mb-0">No sectors found. Click "Add New Sector" to create one.</p>
                </div>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light text-muted small text-uppercase">
                            <tr>
                                <th style="width: 50px;">#</th>
                                <th style="min-width: 220px;">Sector / Industry Name</th>
                                <th>Description</th>
                                <th class="text-center" style="width: 150px;">Linked Stocks</th>
                                <th class="text-center" style="width: 130px;">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($sectors as $idx => $sec): ?>
                                <tr>
                                    <td class="text-muted small"><?= $idx + 1 ?></td>
                                    <td class="fw-bold text-dark">
                                        <i class="bi bi-tag-fill text-primary me-2"></i><?= esc($sec['name']) ?>
                                    </td>
                                    <td class="text-muted small"><?= esc($sec['description'] ?? '—') ?></td>
                                    <td class="text-center">
                                        <?php if ($sec['stock_count'] > 0): ?>
                                            <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-2.5 py-1">
                                                <?= $sec['stock_count'] ?> active <?= $sec['stock_count'] === 1 ? 'stock' : 'stocks' ?>
                                            </span>
                                        <?php else: ?>
                                            <span class="badge bg-light text-muted border px-2.5 py-1">0 stocks</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-center">
                                        <button type="button" 
                                                class="btn btn-sm btn-outline-secondary rounded-2 px-2 py-1 me-1 edit-sector-btn"
                                                data-id="<?= $sec['id'] ?>"
                                                data-name="<?= esc($sec['name']) ?>"
                                                data-desc="<?= esc($sec['description'] ?? '') ?>"
                                                title="Edit Sector">
                                            <i class="bi bi-pencil"></i>
                                        </button>
                                        <?php if ($sec['stock_count'] > 0): ?>
                                            <button type="button" 
                                                    class="btn btn-sm btn-outline-secondary rounded-2 px-2 py-1 opacity-50"
                                                    disabled 
                                                    title="Cannot delete: <?= $sec['stock_count'] ?> active stock(s) assigned">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        <?php else: ?>
                                            <a href="<?= base_url('settings/delete-sector/' . $sec['id']) ?>" 
                                               class="btn btn-sm btn-outline-danger rounded-2 px-2 py-1"
                                               onclick="return confirm('Are you sure you want to delete sector \'<?= esc($sec['name']) ?>\'?')"
                                               title="Delete Sector">
                                                <i class="bi bi-trash"></i>
                                            </a>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Modal: Add New Sector -->
<div class="modal fade" id="addSectorModal" tabindex="-1" aria-labelledby="addSectorModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow rounded-4">
            <form action="<?= base_url('settings/add-sector') ?>" method="POST">
                <?= csrf_field() ?>
                <div class="modal-header border-bottom py-3 px-4">
                    <h5 class="modal-title fw-bold text-dark" id="addSectorModalLabel">
                        <i class="bi bi-plus-circle text-success me-2"></i>Add New Equity Sector
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label for="add_sector_name" class="form-label small fw-semibold text-secondary">Sector / Industry Name <span class="text-danger">*</span></label>
                        <input type="text" 
                               class="form-control" 
                               id="add_sector_name" 
                               name="name" 
                               placeholder="e.g. Information Technology, Banking, Auto" 
                               required>
                    </div>
                    <div class="mb-2">
                        <label for="add_sector_desc" class="form-label small fw-semibold text-secondary">Description (Optional)</label>
                        <textarea class="form-control" 
                                  id="add_sector_desc" 
                                  name="description" 
                                  rows="2" 
                                  placeholder="Brief description of industries covered in this sector"></textarea>
                    </div>
                </div>
                <div class="modal-footer border-top py-3 px-4">
                    <button type="button" class="btn btn-light rounded-3" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success rounded-3 fw-semibold">
                        <i class="bi bi-check2-circle me-1"></i>Save Sector
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal: Edit Sector -->
<div class="modal fade" id="editSectorModal" tabindex="-1" aria-labelledby="editSectorModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow rounded-4">
            <form action="<?= base_url('settings/update-sector') ?>" method="POST">
                <?= csrf_field() ?>
                <input type="hidden" name="id" id="edit_sector_id">
                <div class="modal-header border-bottom py-3 px-4">
                    <h5 class="modal-title fw-bold text-dark" id="editSectorModalLabel">
                        <i class="bi bi-pencil-square text-primary me-2"></i>Edit Equity Sector
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label for="edit_sector_name" class="form-label small fw-semibold text-secondary">Sector / Industry Name <span class="text-danger">*</span></label>
                        <input type="text" 
                               class="form-control" 
                               id="edit_sector_name" 
                               name="name" 
                               required>
                        <div class="form-text small text-muted">
                            <i class="bi bi-info-circle me-1"></i>Renaming this sector will automatically update all existing equities linked to it.
                        </div>
                    </div>
                    <div class="mb-2">
                        <label for="edit_sector_desc" class="form-label small fw-semibold text-secondary">Description (Optional)</label>
                        <textarea class="form-control" 
                                  id="edit_sector_desc" 
                                  name="description" 
                                  rows="2"></textarea>
                    </div>
                </div>
                <div class="modal-footer border-top py-3 px-4">
                    <button type="button" class="btn btn-light rounded-3" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary rounded-3 fw-semibold">
                        <i class="bi bi-check2-circle me-1"></i>Update Sector
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const editModal = new bootstrap.Modal(document.getElementById('editSectorModal'));
    document.querySelectorAll('.edit-sector-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            document.getElementById('edit_sector_id').value = this.dataset.id;
            document.getElementById('edit_sector_name').value = this.dataset.name;
            document.getElementById('edit_sector_desc').value = this.dataset.desc;
            editModal.show();
        });
    });
});
</script>

<?= $this->endSection() ?>

