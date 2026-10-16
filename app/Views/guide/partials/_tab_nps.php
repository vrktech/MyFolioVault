            <div class="tab-pane fade" id="tab-nps" role="tabpanel">
                <div class="d-flex align-items-center gap-2 mb-3 pb-2 border-bottom">
                    <span class="badge bg-secondary rounded-circle p-2"><i class="bi bi-shield-check fs-6 text-white"></i></span>
                    <h5 class="fw-bold text-dark mb-0">7. National Pension System (NPS Tier 1)</h5>
                </div>

                <p class="text-secondary">
                    Tracks voluntary retirement contributions and Employer contributions.
                </p>

                <!-- 7.1 FIRST-TIME SETUP -->
                <div class="card bg-light border p-3 rounded-3 mb-4">
                    <h6 class="fw-bold text-dark mb-2"><i class="bi bi-person-plus-fill me-1 text-primary"></i>7.1 First-Time Setup: PRAN, Pension Fund Manager &amp; Scheme Details</h6>
                    <div class="small text-secondary mb-2">
                        When you open the NPS module for the first time without an active PRAN account, you will be automatically directed to the <strong>Setup NPS Tier 1 Account</strong> page (<code>/nps/new</code>).
                    </div>
                    <ol class="small text-secondary mb-3 ps-3">
                        <li class="mb-2">
                            <strong>Step 1 (Subscriber &amp; PRAN Identification):</strong>
                            <ul class="mt-1 ps-3">
                                <li><strong>PRAN:</strong> Enter your 12-digit Permanent Retirement Account Number (e.g. <code>110012345678</code>).</li>
                                <li><strong>Subscriber Name:</strong> Full name matching your CRA registration.</li>
                                <li><strong>Pension Fund Manager (PFM):</strong> Select or enter your fund house (e.g. <em>HDFC Pension Management, ICICI Prudential Pension Fund, SBI Pension Funds, UTI Retirement Solutions</em>).</li>
                                <li><strong>Investment Choice:</strong> Choose <code>Active Choice</code> (customizable equity/debt split) or <code>Auto Choice</code> (lifecycle).</li>
                            </ul>
                        </li>
                        <li class="mb-2">
                            <strong>Step 2 (Target Asset Allocation Across 4 Schemes):</strong>
                            Enter your allocation percentages across the asset classes (<strong>must total exactly 100%</strong>):
                            <ul class="mt-1 ps-3">
                                <li><strong>Scheme E (Equity):</strong> Up to 75% for Active Choice (e.g. <code>50%</code>).</li>
                                <li><strong>Scheme C (Corporate Debt):</strong> Up to 100% (e.g. <code>30%</code>).</li>
                                <li><strong>Scheme G (Government Bonds):</strong> Up to 100% (e.g. <code>15%</code>).</li>
                                <li><strong>Scheme A (Alternative Assets):</strong> Up to 5% (e.g. <code>5%</code>).</li>
                            </ul>
                        </li>
                        <li class="mb-2">
                            <strong>Step 3 (Scheme Codes for Automated NAV Updates):</strong>
                            Enter the 8-character Scheme Codes from <a href="https://npsnav.in" target="_blank" rel="noopener" class="text-decoration-none fw-semibold">npsnav.in</a> (e.g. <code>SM007001</code> for ICICI Prudential Scheme E Tier 1). This enables automated one-click daily NAV sync.
                        </li>
                        <li class="mb-2">
                            <strong>Step 4 (Initial Contribution &amp; Allotment):</strong>
                            Enter your initial deposit date, gross amount (₹), contribution type (<code>Voluntary</code> / <code>Employee</code> / <code>Employer</code>), optional POP friction charges, and the units/NAV for each scheme from your CRA transaction receipt.
                        </li>
                        <li class="mb-0">
                            Click <strong>"Save &amp; Setup NPS Account"</strong> to launch your Tier 1 portfolio.
                        </li>
                    </ol>
                </div>

                <!-- 7.2 HOW TO EDIT SCHEME DETAILS LATER -->
                <div class="card bg-light border p-3 rounded-3 mb-4">
                    <h6 class="fw-bold text-dark mb-2"><i class="bi bi-pencil-square me-1 text-primary"></i>7.2 How to Edit Scheme Details, PFM &amp; Target Allocation Later</h6>
                    <div class="small text-secondary mb-2">
                        If your target allocation rebalances, your PFM changes, or you need to update/insert scheme codes:
                    </div>
                    <ol class="small text-secondary mb-3 ps-3">
                        <li class="mb-1">On the <a href="<?= base_url('nps') ?>" class="fw-semibold text-decoration-none">NPS Dashboard</a>, look at the top <strong>PRAN Account Card</strong>.</li>
                        <li class="mb-1">Click the options dropdown menu on the right and select <strong>"Edit Account &amp; Allocation"</strong> (or click the edit pencil button).</li>
                        <li class="mb-1">In the <strong>Edit Account &amp; Target Allocation</strong> modal, you can update:
                            <ul class="mt-1 ps-3">
                                <li><strong>Subscriber Name &amp; PFM Name</strong> (e.g. if switching to another fund house).</li>
                                <li><strong>Investment Choice</strong> (<code>Active</code> or <code>Auto</code>).</li>
                                <li><strong>Target Allocation %</strong> across Scheme E, Scheme C, Scheme G, and Scheme A (ensuring they sum to 100%).</li>
                                <li><strong>Scheme Codes</strong> for automated NAV updates via <code>npsnav.in</code>.</li>
                            </ul>
                        </li>
                        <li class="mb-1">Click <strong>"Update Account"</strong> to apply the changes immediately.</li>
                    </ol>
                    <div class="p-2.5 bg-white rounded-3 border small">
                        <strong>Updating Scheme NAVs:</strong>
                        Click <strong>"Sync NAVs"</strong> at the top of the dashboard for automatic real-time fetching from <code>npsnav.in</code>, or click <strong>"Update NAVs"</strong> to manually enter the latest NAV values and valuation date.
                    </div>
                </div>

                <!-- 7.3 CONTRIBUTIONS -->
                <div class="card bg-light border p-3 rounded-3 mb-4">
                    <h6 class="fw-bold text-dark mb-2"><i class="bi bi-wallet-fill me-1 text-success"></i>7.3 Recording Subsequent Contributions</h6>
                    <ol class="small text-secondary mb-2 ps-3">
                        <li class="mb-1">Click <strong>+ Add Transaction</strong> &rarr; select the <strong>Contribution</strong> tab.</li>
                        <li class="mb-1">Enter Contribution Date, Gross Amount (e.g. ₹ 50,000), and Contribution Type (<code>Voluntary</code>, <code>Employee</code>, <code>Employer</code>).</li>
                        <li class="mb-1">Enter any POP cash charges (e.g. ₹ 25).</li>
                        <li class="mb-1">Enter the NAV and allotted units for each scheme from your official CRA receipt.</li>
                        <li class="mb-0">Click <strong>Confirm Contribution</strong>.</li>
                    </ol>
                </div>

                <!-- 7.4 FEE DEDUCTIONS -->
                <div class="card bg-light border p-3 rounded-3 mb-0">
                    <h6 class="fw-bold text-dark mb-2"><i class="bi bi-pie-chart me-1 text-danger"></i>7.4 Recording Quarterly Fee Deductions</h6>
                    <p class="small text-secondary mb-2">
                        At the end of each quarter (June, September, December, March), CRA and Custodian charges are deducted by canceling fractional units.
                    </p>
                    <ol class="small text-secondary mb-0 ps-3">
                        <li class="mb-1">Click <strong>+ Add Transaction</strong> &rarr; select the <strong>Quarterly Unit Deduction</strong> tab.</li>
                        <li class="mb-1">Enter Deduction Date and the exact fractional units cancelled from each scheme (e.g. <code>0.1850</code> from Scheme E).</li>
                        <li class="mb-0">Click <strong>Confirm Fee Deduction</strong> to keep your dashboard balances in 100% agreement with your official statement.</li>
                    </ol>
                </div>
            </div>

