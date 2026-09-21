<?php
$defaultSize = (int) (session('recordsPerPage') ?? 20);
$prefix = $idPrefix ?? 'table';
$allowedSizes = [20, 40, 50, 80, 100];
?>
<div class="d-flex justify-content-between align-items-center flex-wrap gap-2 pt-3 border-top mt-2" id="<?= $prefix ?>PaginationBar">
    <div class="d-flex align-items-center gap-2 small text-muted">
        <span id="<?= $prefix ?>PageInfo">Showing 1 to <?= $defaultSize ?> entries</span>
        <span class="text-secondary">&bull;</span>
        <label for="<?= $prefix ?>PageSize" class="text-nowrap mb-0">Show:</label>
        <select id="<?= $prefix ?>PageSize" class="form-select form-select-sm shadow-none border-secondary-subtle" style="width: auto;">
            <?php foreach ($allowedSizes as $sz): ?>
                <option value="<?= $sz ?>" <?= $defaultSize === $sz ? 'selected' : '' ?>><?= $sz ?></option>
            <?php endforeach; ?>
        </select>
        <span>entries</span>
    </div>
    <nav aria-label="Table pagination">
        <ul class="pagination pagination-sm mb-0" id="<?= $prefix ?>PageControls"></ul>
    </nav>
</div>

