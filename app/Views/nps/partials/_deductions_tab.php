            <div class="tab-pane fade p-3" id="deductions" role="tabpanel">
                <div class="alert alert-info py-2 px-3 mb-3 d-flex align-items-center rounded-3 small">
                    <i class="bi bi-info-circle-fill me-2 fs-5"></i>
                    <div>
                        <strong>Quarterly CRA & POP Service Charge Mechanism:</strong> Under PFRDA rules, account maintenance and turnover charges are debited directly from your scheme unit holdings at the prevailing quarter-end NAV rather than upfront cash.
                    </div>
                </div>

                <?php if (empty($deductions)): ?>
                    <p class="text-muted text-center py-4">No quarterly unit deductions recorded yet. When CRA deducts maintenance units quarterly, record them here.</p>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0 small" id="npsDeductionsTable">
                            <thead class="table-light text-muted text-uppercase" style="font-size: 0.72rem;">
                                <tr>
                                    <th>Deduction Date</th>
                                    <th>Quarter / Description</th>
                                    <th>Scheme Units Debited</th>
                                    <th>Ack / Ref No.</th>
                                    <th class="text-center" style="width: 85px;">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($deductions as $d): ?>
                                    <?php
                                    $deUnits = 0; $deNav = 0;
                                    $dcUnits = 0; $dcNav = 0;
                                    $dgUnits = 0; $dgNav = 0;
                                    $daUnits = 0; $daNav = 0;
                                    if (!empty($d['deductions'])) {
                                        foreach ($d['deductions'] as $du) {
                                            if ($du['scheme_type'] === 'SCHEME_E') { $deUnits = abs($du['units']); $deNav = $du['nav']; }
                                            if ($du['scheme_type'] === 'SCHEME_C') { $dcUnits = abs($du['units']); $dcNav = $du['nav']; }
                                            if ($du['scheme_type'] === 'SCHEME_G') { $dgUnits = abs($du['units']); $dgNav = $du['nav']; }
                                            if ($du['scheme_type'] === 'SCHEME_A') { $daUnits = abs($du['units']); $daNav = $du['nav']; }
                                        }
                                    }
                                    ?>
                                    <tr class="nps-deduct-row">
                                        <td><?= date('d-M-Y', strtotime($d['transaction_date'])) ?></td>
                                        <td><strong><?= esc($d['notes'] ?: 'Quarterly service charges') ?></strong></td>
                                        <td>
                                            <?php if (!empty($d['deductions'])): ?>
                                                <div class="d-flex flex-wrap gap-2">
                                                    <?php foreach ($d['deductions'] as $du): ?>
                                                        <span class="badge bg-danger-subtle text-danger border border-danger-subtle px-2 py-1">
                                                            <?= substr($du['scheme_type'], -1) ?>: -<?= number_format(abs($du['units']), 4) ?> units @ ₹<?= number_format($du['nav'], 4) ?>
                                                        </span>
                                                    <?php endforeach; ?>
                                                </div>
                                            <?php else: ?>
                                                <span class="text-muted">—</span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="font-monospace text-muted"><?= esc($d['acknowledgement_no'] ?: '—') ?></td>
                                        <td class="text-center">
                                            <button type="button" 
                                                    class="btn btn-sm btn-outline-secondary py-0 px-1.5 open-edit-nps-deduct"
                                                    data-bs-toggle="modal" 
                                                    data-bs-target="#editNpsDeductModal"
                                                    data-id="<?= $d['id'] ?>"
                                                    data-date="<?= esc($d['transaction_date']) ?>"
                                                    data-notes="<?= esc($d['notes'] ?? '') ?>"
                                                    data-ack="<?= esc($d['acknowledgement_no'] ?? '') ?>"
                                                    data-deduct-units-e="<?= (float)$deUnits ?>" data-deduct-nav-e="<?= (float)$deNav ?>"
                                                    data-deduct-units-c="<?= (float)$dcUnits ?>" data-deduct-nav-c="<?= (float)$dcNav ?>"
                                                    data-deduct-units-g="<?= (float)$dgUnits ?>" data-deduct-nav-g="<?= (float)$dgNav ?>"
                                                    data-deduct-units-a="<?= (float)$daUnits ?>" data-deduct-nav-a="<?= (float)$daNav ?>"
                                                    title="Edit Fee Deduction">
                                                <i class="bi bi-pencil" style="font-size: 0.75rem;"></i>
                                            </button>
                                            <a href="<?= base_url('nps/delete-transaction/' . $d['id']) ?>" 
                                               class="btn btn-sm btn-outline-danger py-0 px-1.5 ms-1"
                                               onclick="return confirm('Delete this fee deduction record?');"
                                               title="Delete Deduction">
                                                <i class="bi bi-trash" style="font-size: 0.75rem;"></i>
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                    <?= view('partials/pagination_bar', ['idPrefix' => 'npsDeduct']) ?>
                <?php endif; ?>
            </div>
