            <div class="tab-pane fade" id="tab-ops" role="tabpanel">
                <div class="d-flex align-items-center gap-2 mb-3 pb-2 border-bottom">
                    <span class="badge bg-dark rounded-circle p-2"><i class="bi bi-hdd-network fs-6 text-white"></i></span>
                    <h5 class="fw-bold text-dark mb-0">10. Backup, Cleanup &amp; Production Setup</h5>
                </div>

                <h6 class="fw-bold text-dark mb-2">10.1 How to Clean Sample Data for Production Use</h6>
                <p class="text-secondary small">
                    If you tested the platform with sample records and are now ready to track your personal investments:
                </p>
                <div class="bg-dark text-light p-3 rounded-3 font-monospace small mb-2">
                    php cleanup_sample_data.php
                </div>
                <div class="p-2.5 bg-light rounded-3 border small mb-4">
                    <strong>What It Does:</strong>
                    Automatically creates a complete safety snapshot in <code>backup_sample_data.sql</code>, truncates all 23 transaction and holding tables, preserves your administrator user account and all 18 standard equity sectors, and resets all auto-increment IDs to 1.
                </div>

                <h6 class="fw-bold text-dark mb-2">10.2 Creating Manual Database Backups</h6>
                <div class="bg-dark text-light p-3 rounded-3 font-monospace small mb-2">
                    mysqldump -u root investment_portfolio > backup_my_portfolio.sql
                </div>
                <p class="small text-secondary mb-0">
                    To restore from backup: <code>mysql -u root investment_portfolio &lt; backup_my_portfolio.sql</code>.
                </p>
            </div>

