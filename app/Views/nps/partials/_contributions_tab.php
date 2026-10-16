            <div class="tab-pane fade p-3" id="contributions" role="tabpanel">
                <?php if (empty($contributions)): ?>
                    <p class="text-muted text-center py-4">No contributions recorded yet.</p>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0 small" id="npsContributionsTable">
                            <thead class="table-light text-muted text-uppercase" style="font-size: 0.72rem;">
                                <tr>
                                    <th>Date</th>
                                    <th>Type</th>
                                    <th>Ack / Receipt No.</th>
                                    <th class="text-end">Gross Amount (₹)</th>
                                    <th class="text-end">Cash Charges</th>
                                    <th class="text-end">Net Invested (₹)</th>
                                    <th>Scheme Allotment Breakdown</th>
                                    <th>Notes</th>
                                    <th class="text-center" style="width: 85px;">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($contributions as $c): ?>
                                    <?php
                                    $eUnits = 0; $eNav = 0; $eAmt = 0;
                                    $cUnits = 0; $cNav = 0; $cAmt = 0;
                                    $gUnits = 0; $gNav = 0; $gAmt = 0;
                                    $aUnits = 0; $aNav = 0; $aAmt = 0;
                                    if (!empty($c['allotments'])) {
                                        foreach ($c['allotments'] as $alt) {
                                            if ($alt['scheme_type'] === 'SCHEME_E') { $eUnits = $alt['units']; $eNav = $alt['nav']; $eAmt = $alt['allocated_amount']; }
                                            if ($alt['scheme_type'] === 'SCHEME_C') { $cUnits = $alt['units']; $cNav = $alt['nav']; $cAmt = $alt['allocated_amount']; }
                                            if ($alt['scheme_type'] === 'SCHEME_G') { $gUnits = $alt['units']; $gNav = $alt['nav']; $gAmt = $alt['allocated_amount']; }
                                            if ($alt['scheme_type'] === 'SCHEME_A') { $aUnits = $alt['units']; $aNav = $alt['nav']; $aAmt = $alt['allocated_amount']; }
                                        }
                                    }
                                    ?>
                                    <tr class="nps-contrib-row">
                                        <td><?= date('d-M-Y', strtotime($c['transaction_date'])) ?></td>
                                        <td>
                                            <?php if ($c['contribution_type'] === 'VOLUNTARY'): ?>
                                                <span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-1">Voluntary</span>
                                            <?php elseif ($c['contribution_type'] === 'EMPLOYER'): ?>
                                                <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-2 py-1">Employer</span>
                                            <?php else: ?>
                                                <span class="badge bg-secondary-subtle text-secondary border px-2 py-1"><?= esc($c['contribution_type']) ?></span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="font-monospace text-muted"><?= esc($c['acknowledgement_no'] ?: '—') ?></td>
                                        <td class="text-end fw-semibold"><?= format_inr($c['gross_amount']) ?></td>
                                        <td class="text-end text-muted"><?= format_inr($c['optional_cash_charges']) ?></td>
                                        <td class="text-end fw-bold text-dark"><?= format_inr($c['net_amount']) ?></td>
                                        <td>
                                            <?php if (!empty($c['allotments'])): ?>
                                                <div class="d-flex flex-column gap-1" style="font-size: 0.75rem;">
                                                    <?php foreach ($c['allotments'] as $alt): ?>
                                                        <div>
                                                            <span class="fw-semibold"><?= substr($alt['scheme_type'], -1) ?>:</span>
                                                            <?= number_format($alt['units'], 4) ?> units @ ₹<?= number_format($alt['nav'], 4) ?>
                                                            <span class="text-muted">(<?= format_inr($alt['allocated_amount']) ?>)</span>
                                                        </div>
                                                    <?php endforeach; ?>
                                                </div>
                                            <?php else: ?>
                                                <span class="text-muted">—</span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="text-secondary"><?= esc($c['notes'] ?: '—') ?></td>
                                        <td class="text-center">
                                            <button type="button" 
                                                    class="btn btn-sm btn-outline-secondary py-0 px-1.5 open-edit-nps-contrib"
                                                     data-bs-toggle="modal" 
                                                    data-bs-target="#editNpsContribModal"
                                                    data-id="<?= $c['id'] ?>"
                                                    data-date="<?= esc($c['transaction_date']) ?>"
                                                    data-type="<?= esc($c['contribution_type']) ?>"
                                                    data-gross="<?= (float)$c['gross_amount'] ?>"
                                                    data-charges="<?= (float)$c['optional_cash_charges'] ?>"
                                                    data-ack="<?= esc($c['acknowledgement_no'] ?? '') ?>"
                                                    data-notes="<?= esc($c['notes'] ?? '') ?>"
                                                    data-units-e="<?= (float)$eUnits ?>" data-nav-e="<?= (float)$eNav ?>" data-amt-e="<?= (float)$eAmt ?>"
                                                    data-units-c="<?= (float)$cUnits ?>" data-nav-c="<?= (float)$cNav ?>" data-amt-c="<?= (float)$cAmt ?>"
                                                    data-units-g="<?= (float)$gUnits ?>" data-nav-g="<?= (float)$gNav ?>" data-amt-g="<?= (float)$gAmt ?>"
                                                    data-units-a="<?= (float)$aUnits ?>" data-nav-a="<?= (float)$aNav ?>" data-amt-a="<?= (float)$aAmt ?>"
                                                    title="Edit Contribution">
                                                <i class="bi bi-pencil" style="font-size: 0.75rem;"></i>
                                            </button>
                                            <a href="<?= base_url('nps/delete-transaction/' . $c['id']) ?>" 
                                               class="btn btn-sm btn-outline-danger py-0 px-1.5 ms-1"
                                               onclick="return confirm('Delete this contribution transaction?');"
                                               title="Delete Contribution">
                                                <i class="bi bi-trash" style="font-size: 0.75rem;"></i>
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                    <?= view('partials/pagination_bar', ['idPrefix' => 'npsContrib']) ?>
                <?php endif; ?>
            </div>

