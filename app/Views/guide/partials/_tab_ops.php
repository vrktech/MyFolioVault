            <div class="tab-pane fade" id="tab-ops" role="tabpanel">
                <div class="d-flex align-items-center gap-2 mb-3 pb-2 border-bottom">
                    <span class="badge bg-dark rounded-circle p-2"><i class="bi bi-hdd-network fs-6 text-white"></i></span>
                    <h5 class="fw-bold text-dark mb-0">10. Backup, Cleanup &amp; Production Setup</h5>
                </div>

                <!-- 10.1 IN-APP DATABASE BACKUP -->
                <div class="card border-0 shadow-sm rounded-4 mb-4">
                    <div class="card-header bg-white border-bottom py-3 px-4 rounded-top-4">
                        <div class="d-flex align-items-center">
                            <div class="p-2 bg-primary bg-opacity-10 text-primary rounded-3 me-3">
                                <i class="bi bi-cloud-arrow-down fs-5"></i>
                            </div>
                            <div>
                                <h6 class="fw-bold text-dark mb-0">10.1 One-Click In-App Database Backup</h6>
                                <small class="text-muted">Generate and download a compressed SQL dump directly from the browser</small>
                            </div>
                        </div>
                    </div>
                    <div class="card-body p-4">
                        <p class="text-secondary small mb-3">
                            You can export an offline backup of your entire investment portfolio at any time from the administration settings:
                        </p>
                        <ol class="small text-secondary ps-3 mb-3">
                            <li class="mb-1">Navigate to <strong>Settings</strong> in the sidebar and click the <strong>Database Backup &amp; Restore</strong> tab (<code>/settings/database</code>).</li>
                            <li class="mb-1">Review your portfolio statistics: Total Tables, Stored Records, and Storage Size.</li>
                            <li class="mb-1">Click <strong>"Download Database Backup (.zip)"</strong>.</li>
                            <li>Your browser will immediately download a timestamped ZIP archive: <code>myfoliovault_backup_YYYY-MM-DD_His.zip</code>.</li>
                        </ol>

                        <div class="p-3 bg-light rounded-3 border small">
                            <strong class="text-dark d-block mb-1"><i class="bi bi-file-earmark-zip text-primary me-1"></i> What is Inside the Backup Archive:</strong>
                            <ul class="text-muted mb-2 ps-3">
                                <li><code>backup_YYYY-MM-DD_His.sql</code>: Full SQL dump containing table structures, truncates, and chunked <code>INSERT</code> statements for all 24 portfolio tables (Equities, Mutual Funds, ETFs, Bonds, REITs/InvITs, NPS, Expenses, Corporate Actions, FIFO Tax Logs, and Sector Masters).</li>
                                <li><code>manifest.json</code>: Metadata summary with release version, export timestamp, and individual table record counts.</li>
                            </ul>
                            <div class="text-success fw-semibold">
                                <i class="bi bi-shield-check me-1"></i><strong>Credentials Protected:</strong> The <code>users</code> table (passwords, emails, sessions) and <code>migrations</code> table are strictly excluded. Restoring a backup on any installation will never overwrite active admin logins or break schema versioning.
                            </div>
                        </div>
                    </div>
                </div>

                <!-- 10.2 IN-APP DATABASE RESTORE -->
                <div class="card border-0 shadow-sm rounded-4 mb-4">
                    <div class="card-header bg-white border-bottom py-3 px-4 rounded-top-4">
                        <div class="d-flex align-items-center">
                            <div class="p-2 bg-danger bg-opacity-10 text-danger rounded-3 me-3">
                                <i class="bi bi-cloud-arrow-up fs-5"></i>
                            </div>
                            <div>
                                <h6 class="fw-bold text-dark mb-0">10.2 One-Click In-App Database Restore</h6>
                                <small class="text-muted">Revert or migrate your portfolio data by uploading a backup ZIP archive</small>
                            </div>
                        </div>
                    </div>
                    <div class="card-body p-4">
                        <p class="text-secondary small mb-3">
                            To restore your portfolio state from a previously generated backup archive:
                        </p>
                        <ol class="small text-secondary ps-3 mb-3">
                            <li class="mb-1">Navigate to <strong>Settings</strong> &rarr; <strong>Database Backup &amp; Restore</strong> (<code>/settings/database</code>).</li>
                            <li class="mb-1">Under <strong>Restore Database from Backup</strong>, click <strong>Choose File</strong> and select your <code>myfoliovault_backup_*.zip</code> file.</li>
                            <li class="mb-1">Click the <strong>"Upload &amp; Restore Database"</strong> button.</li>
                            <li class="mb-1">A safety confirmation modal will appear. Review the warning and check the mandatory acknowledgment checkbox: <em>"I understand that existing portfolio data will be overwritten"</em>.</li>
                            <li>Click <strong>"Yes, Restore Database"</strong>.</li>
                        </ol>

                        <div class="alert alert-warning border-warning border-opacity-25 bg-warning bg-opacity-10 rounded-3 p-3 small mb-3">
                            <div class="d-flex">
                                <i class="bi bi-shield-exclamation text-warning fs-5 me-2 flex-shrink-0"></i>
                                <div>
                                    <strong class="text-dark">Automated Safety Snapshot:</strong> Before wiping existing portfolio tables, the system automatically creates a pre-restore safety dump in <code>writable/backups/safety_pre_restore.sql</code>. If any unexpected error occurs, you can recover your prior data from this snapshot.
                                </div>
                            </div>
                        </div>

                        <div class="p-2.5 bg-light rounded-3 border small text-muted">
                            <i class="bi bi-info-circle me-1 text-primary"></i>
                            <strong>Demo Sandbox Notice:</strong> In the public demo website, both database backup download and database restore operations are permanently locked to preserve shared sandbox integrity.
                        </div>
                    </div>
                </div>

                <!-- 10.3 CLEANUP SAMPLE DATA -->
                <div class="card border-0 shadow-sm rounded-4 mb-4">
                    <div class="card-header bg-white border-bottom py-3 px-4 rounded-top-4">
                        <div class="d-flex align-items-center">
                            <div class="p-2 bg-warning bg-opacity-10 text-warning rounded-3 me-3">
                                <i class="bi bi-trash3 fs-5"></i>
                            </div>
                            <div>
                                <h6 class="fw-bold text-dark mb-0">10.3 How to Clean Sample Data for Production Use</h6>
                                <small class="text-muted">Reset all demo transactions and start tracking your real personal portfolio</small>
                            </div>
                        </div>
                    </div>
                    <div class="card-body p-4">
                        <p class="text-secondary small mb-2">
                            If you tested the platform with sample records and are now ready to track your actual investments from a fresh slate, run this CLI command from your application root directory:
                        </p>
                        <div class="bg-dark text-light p-3 rounded-3 font-monospace small mb-3">
                            php cleanup_sample_data.php
                        </div>
                        <div class="p-3 bg-light rounded-3 border small">
                            <strong class="text-dark d-block mb-1">What It Does:</strong>
                            <ul class="text-muted mb-0 ps-3">
                                <li>Automatically creates a safety backup dump in <code>backup_sample_data.sql</code>.</li>
                                <li>Truncates all 24 transaction and holding tables across all modules.</li>
                                <li>Preserves your active administrator login and all 18 standard equity sectors.</li>
                                <li>Resets all primary auto-increment counters back to 1.</li>
                            </ul>
                        </div>
                    </div>
                </div>

                <!-- 10.4 CLI DATABASE TOOLS -->
                <div class="card border-0 shadow-sm rounded-4 mb-2">
                    <div class="card-header bg-white border-bottom py-3 px-4 rounded-top-4">
                        <div class="d-flex align-items-center">
                            <div class="p-2 bg-secondary bg-opacity-10 text-secondary rounded-3 me-3">
                                <i class="bi bi-terminal fs-5"></i>
                            </div>
                            <div>
                                <h6 class="fw-bold text-dark mb-0">10.4 Command-Line Database Tools (CLI Alternatives)</h6>
                                <small class="text-muted">Standard terminal commands for advanced database administrators</small>
                            </div>
                        </div>
                    </div>
                    <div class="card-body p-4">
                        <h6 class="small fw-bold text-dark mb-1">Creating a Manual SQL Dump:</h6>
                        <div class="bg-dark text-light p-2.5 rounded-3 font-monospace small mb-3">
                            mysqldump -u root -p investment_portfolio > backup_my_portfolio.sql
                        </div>

                        <h6 class="small fw-bold text-dark mb-1">Restoring from an External SQL Dump:</h6>
                        <div class="bg-dark text-light p-2.5 rounded-3 font-monospace small mb-3">
                            mysql -u root -p investment_portfolio &lt; backup_my_portfolio.sql
                        </div>

                        <h6 class="small fw-bold text-dark mb-1">Packaging Standalone Release Archives:</h6>
                        <div class="bg-dark text-light p-2.5 rounded-3 font-monospace small">
                            pack -patch &nbsp;&nbsp;&nbsp;&nbsp;# Increments patch version (e.g. 1.0.5 -> 1.0.6) and builds standalone ZIP
                        </div>
                    </div>
                </div>
            </div>
