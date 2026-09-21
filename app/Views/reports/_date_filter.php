<div class="card border-0 shadow-sm rounded-4 mb-4 bg-white">
    <div class="card-body p-3">
        <div class="d-flex flex-column flex-lg-row justify-content-between align-items-lg-center gap-3">
            <!-- Left: FY Selector Tabs -->
            <div class="d-flex flex-wrap align-items-center gap-2">
                <span class="text-secondary small fw-semibold me-1 d-none d-sm-inline">
                    <i class="bi bi-calendar3 me-1"></i>Period:
                </span>
                <div class="btn-group btn-group-sm shadow-sm rounded-pill p-1 bg-light border" role="group">
                    <a href="<?= base_url('reports/' . $activeTab . '?fy=CURRENT_FY') ?>" 
                       class="btn rounded-pill px-3 py-1.5 <?= ($range['key'] ?? '') === 'CURRENT_FY' ? 'btn-primary text-white fw-semibold shadow-sm' : 'btn-light text-secondary' ?>">
                        Current FY (<?= esc($range['fyRanges']['current']['label'] ?? '') ?>)
                    </a>
                    <a href="<?= base_url('reports/' . $activeTab . '?fy=LAST_FY') ?>" 
                       class="btn rounded-pill px-3 py-1.5 <?= ($range['key'] ?? '') === 'LAST_FY' ? 'btn-primary text-white fw-semibold shadow-sm' : 'btn-light text-secondary' ?>">
                        Last FY (<?= esc($range['fyRanges']['last']['label'] ?? '') ?>)
                    </a>
                    <a href="<?= base_url('reports/' . $activeTab . '?fy=ALL') ?>" 
                       class="btn rounded-pill px-3 py-1.5 <?= ($range['key'] ?? '') === 'ALL' ? 'btn-primary text-white fw-semibold shadow-sm' : 'btn-light text-secondary' ?>">
                        All Time
                    </a>
                </div>

                <!-- Active Date Range Badge -->
                <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-3 py-2 rounded-pill small fw-normal ms-lg-2">
                    <i class="bi bi-clock-history me-1"></i>
                    <?php if (!empty($range['startDate']) && !empty($range['endDate'])): ?>
                        <?= date('d-M-Y', strtotime($range['startDate'])) ?> &mdash; <?= date('d-M-Y', strtotime($range['endDate'])) ?>
                    <?php else: ?>
                        All Historical Records
                    <?php endif; ?>
                </span>
            </div>

            <!-- Right: Action Buttons (Export CSV & Print) -->
            <div class="d-flex align-items-center gap-2 ms-auto ms-lg-0">
                <?php if (in_array($activeTab, ['cashflow', 'tax', 'income', 'expenses'])): ?>
                    <a href="<?= base_url('reports/export-' . $activeTab . '?fy=' . ($range['key'] ?? 'CURRENT_FY')) ?>" 
                       class="btn btn-sm btn-outline-success rounded-pill px-3 py-1.5 shadow-sm d-flex align-items-center gap-1.5"
                       title="Download complete report data as CSV spreadsheet">
                        <i class="bi bi-file-earmark-spreadsheet"></i>
                        <span>Export CSV</span>
                    </a>
                <?php endif; ?>
                <button type="button" class="btn btn-sm btn-outline-secondary rounded-pill px-3 py-1.5 shadow-sm d-flex align-items-center gap-1.5"
                        onclick="window.print()" title="Print report or save as PDF">
                    <i class="bi bi-printer"></i>
                    <span>Print Statement</span>
                </button>
            </div>
        </div>
    </div>
</div>

