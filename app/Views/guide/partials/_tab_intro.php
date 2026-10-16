            <div class="tab-pane fade show active" id="tab-intro" role="tabpanel">
                <div class="d-flex align-items-center gap-2 mb-3 pb-2 border-bottom">
                    <span class="badge bg-primary rounded-circle p-2"><i class="bi bi-info-circle fs-6 text-white"></i></span>
                    <h5 class="fw-bold text-dark mb-0">1. Getting Started &amp; System Configuration</h5>
                </div>
                
                <!-- About MyFolioVault & Author Purpose -->
                <div class="card border-0 shadow-sm rounded-4 bg-light p-4 mb-4 border-start border-primary border-4">
                    <h6 class="fw-bold text-dark mb-2"><i class="bi bi-stars text-primary me-2"></i>About MyFolioVault</h6>
                    <p class="text-secondary small mb-2">
                        <strong>MyFolioVault</strong> is a self-hosted, 100% offline personal wealth management and portfolio tracking application tailored specifically for the Indian financial market. Developed from real-world personal investment practices, it is designed to fulfill the end-to-end portfolio tracking, bookkeeping, and tax compliance needs of an average retail investor, salaried professional, and family office in India.
                    </p>
                    <p class="text-secondary small mb-0">
                        Many modern investors trade across multiple brokerages (Zerodha, Groww, AngelOne, ICICI Direct, Upstox) and hold diverse assets—<strong>Direct Equities</strong>, <strong>InvITs &amp; REITs</strong>, <strong>ETFs</strong>, <strong>Bonds &amp; SGBs</strong>, <strong>Mutual Funds</strong>, and <strong>NPS (Tier 1)</strong>. Most commercial platforms either require sharing sensitive broker credentials or fail to accurately handle Indian statutory nuances like FIFO capital gains matching, 4-component REIT distributions, clean bond pricing, and daily consolidated broker contract notes. MyFolioVault solves this completely locally on your private machine with zero external dependencies, no third-party CDNs, and zero tracking.
                    </p>
                </div>

                <div class="alert alert-primary bg-primary bg-opacity-10 border-primary rounded-3 small mb-4">
                    <i class="bi bi-shield-lock-fill me-1 text-primary"></i>
                    <strong>Privacy &amp; Offline Architecture:</strong> All assets (Bootstrap 5.3.3, Bootstrap Icons 1.11.3, custom styling) run locally from your server. The app uses the native system font stack (<code>-apple-system, BlinkMacSystemFont, "Segoe UI", Roboto</code>). No internet connection is required, and your financial data never leaves your computer.
                </div>

                <!-- Live Interactive Demo Sandbox -->
                <div class="card border-0 shadow-sm rounded-4 p-4 mb-4 text-white" style="background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%);">
                    <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-2">
                        <div class="d-flex align-items-center gap-2">
                            <span class="badge bg-white text-primary fw-bold px-2.5 py-1.5 rounded-pill">
                                <i class="bi bi-broadcast me-1"></i> Live Sandbox
                            </span>
                            <h6 class="fw-bold mb-0 text-white fs-6">Try MyFolioVault Live in Your Browser</h6>
                        </div>
                        <a href="https://myfoliovault.in/" target="_blank" class="btn btn-warning btn-sm rounded-pill px-3 fw-bold shadow-sm">
                            <i class="bi bi-box-arrow-up-right me-1"></i> Launch Demo Sandbox
                        </a>
                    </div>
                    <p class="text-white text-opacity-90 small mb-2">
                        Experience all portfolio features, analytics, and tax audit modules without setting up a local server. Pre-loaded with realistic Indian stock portfolios, mutual fund SIPs, ETFs, SGBs, REITs, and expenses.
                    </p>
                    <div class="d-flex align-items-center gap-3 flex-wrap small">
                        <span class="badge bg-white bg-opacity-25 text-white fw-normal px-2.5 py-1.5 rounded-2">
                            <strong>URL:</strong> <a href="https://myfoliovault.in/" target="_blank" class="text-white text-decoration-underline">https://myfoliovault.in/</a>
                        </span>
                        <span class="badge bg-white bg-opacity-25 text-white fw-normal px-2.5 py-1.5 rounded-2">
                            <strong>Email:</strong> admin@portfolio.local
                        </span>
                        <span class="badge bg-white bg-opacity-25 text-white fw-normal px-2.5 py-1.5 rounded-2">
                            <strong>Password:</strong> password123
                        </span>
                        <span class="text-white text-opacity-75 fst-italic">
                            <i class="bi bi-clock-history me-1"></i> Data resets periodically
                        </span>
                    </div>
                </div>

                <!-- Step-by-Step Installation Guide -->
                <div class="card border p-3 rounded-3 mb-4 bg-white">
                    <h6 class="fw-bold text-dark mb-2"><i class="bi bi-download me-1 text-success"></i>1.1 Step-by-Step: How to Install MyFolioVault</h6>
                    <div class="small text-secondary mb-3">
                        MyFolioVault features an automated browser-based setup wizard at <code>public/install.php</code>.
                    </div>
                    <ol class="small text-secondary mb-3 ps-3">
                        <li class="mb-2">
                            <strong>System Prerequisites:</strong> Ensure PHP 8.1+ and MySQL 8.0+ or MariaDB 10.4+ are running (e.g. via XAMPP, WAMP, or standalone Apache).
                        </li>
                        <li class="mb-2">
                            <strong>Download &amp; Extract:</strong> Download the latest standalone <code>.zip</code> version from the <a href="https://github.com/vrktech/MyFolioVault/releases" target="_blank" class="fw-semibold text-decoration-none">GitHub Releases section</a>. It is completely ready to install and includes all necessary pre-bundled components (including the CodeIgniter 4 framework core and Bootstrap offline assets). Extract its contents into your web server's document root folder, e.g. <code>C:\xampp\htdocs\[your_installation_folder]</code> (Windows) or <code>/var/www/html/[your_installation_folder]</code> (Linux).
                        </li>
                        <li class="mb-2">
                            <strong>Launch Wizard:</strong> Open your browser and navigate to <code>http://localhost/[your_installation_folder]/public/install.php</code>. <em>(Change <code>[your_installation_folder]</code> to the actual folder name where you extracted MyFolioVault, for example <code>Portfolio</code>).</em>
                        </li>
                        <li class="mb-2">
                            <strong>Step 1 (Requirements Check):</strong> The wizard checks PHP version, extensions (<code>mysqli</code>, <code>intl</code>, <code>curl</code>, <code>mbstring</code>), and file permissions.
                        </li>
                        <li class="mb-2">
                            <strong>Step 2 (Database Configuration):</strong> Enter host (<code>localhost</code>), database name (<code>investment_portfolio</code>), username (<code>root</code>), and password. The wizard can create the database for you if it does not exist.
                        </li>
                        <li class="mb-2">
                            <strong>Step 3 (Schema &amp; Demo Data Checkbox):</strong>
                            <div class="bg-light p-2.5 rounded-3 border mt-1">
                                <strong>The Demo Data Checkbox:</strong> You will see a checkbox: <code>[ ] Install Realistic Sample / Demo Portfolio Data</code> (default: unchecked).
                                <ul class="mb-0 mt-1 ps-3">
                                    <li><strong>Unchecked (Recommended for Production):</strong> Creates a completely clean, empty portfolio ready for your personal investments.</li>
                                    <li><strong>Checked:</strong> Seeds realistic stocks (Reliance, TCS, HDFC Bank), ETFs, SGBs, REITs, Mutual Funds, and contract note charges for testing and evaluation.</li>
                                </ul>
                            </div>
                        </li>
                        <li class="mb-2">
                            <strong>Step 4 (Admin Account):</strong> Enter your Name, Email, Password, and Financial Year Start Month (April).
                        </li>
                        <li class="mb-2">
                            <strong>Step 5 &amp; 6 (Auto-Deletion &amp; Login):</strong> The wizard creates your <code>.env</code> file, automatically self-deletes <code>install.php</code> for security, and redirects you to the login screen.
                        </li>
                    </ol>
                </div>

                <!-- Live CMP & NAV Updating Guide -->
                <div class="card border p-3 rounded-3 mb-4 bg-white">
                    <h6 class="fw-bold text-dark mb-2"><i class="bi bi-arrow-repeat me-1 text-info"></i>1.2 Live Current Market Price (CMP) &amp; NAV Updating Guide</h6>
                    <div class="small text-secondary mb-3">
                        How live prices and net asset values are maintained across asset classes:
                    </div>
                    <ul class="small text-secondary mb-0 ps-3">
                        <li class="mb-2">
                            <strong>Equities, ETFs, InvITs &amp; REITs:</strong> Click <strong>"Refresh Live Prices"</strong> at the top right of their respective dashboards. Fetches current market quotes via live feeds with local caching. You can also manually adjust any individual price via the edit modal.
                        </li>
                        <li class="mb-2">
                            <strong>Mutual Funds:</strong> Click <strong>"Refresh Latest NAVs"</strong> on the Mutual Funds dashboard to fetch official daily closing NAVs from the Association of Mutual Funds in India (AMFI) using the 6-digit AMFI code.
                        </li>
                        <li class="mb-2">
                            <strong>Bonds &amp; Fixed Income (Manual CMP):</strong>
                            <div class="alert alert-warning py-2 px-3 my-1 rounded-2 border">
                                <i class="bi bi-info-circle me-1"></i> <strong>Important Note on Bonds:</strong> Secondary market G-Secs, Corporate NCDs, and SGBs do not have automated streaming feeds. To update bond prices, use the <strong>Edit Price / CMP</strong> button on the Bond Dashboard to enter the current clean market price per unit.
                            </div>
                        </li>
                        <li class="mb-2">
                            <strong>National Pension System (NPS Scheme NAVs via npsnav.in):</strong>
                            <div>MyFolioVault pulls live closing NAVs for Scheme E, C, G, and A directly from <a href="https://npsnav.in" target="_blank" class="fw-semibold text-decoration-none">npsnav.in</a>.</div>
                            <div class="bg-light p-2.5 rounded-3 border mt-1">
                                <strong>How to find your NPS Scheme Code on npsnav.in:</strong>
                                <ol class="mb-0 mt-1 ps-3">
                                    <li>Visit <a href="https://npsnav.in" target="_blank" class="fw-semibold">npsnav.in</a> and search for your Pension Fund Manager (e.g. <em>ICICI Prudential</em>, <em>HDFC</em>, <em>SBI</em>) and scheme tier.</li>
                                    <li>Click on the fund to view its page. The 8-character Scheme Code is at the end of the URL.</li>
                                    <li><strong>Example:</strong> The <a href="https://npsnav.in/funds/SM007001" target="_blank" class="fw-semibold">ICICI PRUDENTIAL SCHEME E - TIER I</a> scheme code is <code>SM007001</code>.</li>
                                    <li>Enter this code in your NPS Account scheme configuration in MyFolioVault so clicking <strong>"Update Scheme NAVs"</strong> fetches live prices automatically.</li>
                                </ol>
                            </div>
                        </li>
                    </ul>
                </div>

                <h6 class="fw-bold text-dark mb-2">1.3 Financial Year &amp; System Preferences</h6>
                <ol class="text-secondary small">
                    <li class="mb-2">
                        <strong>Financial Year Configuration:</strong> Navigate to <a href="<?= base_url('settings') ?>" class="text-decoration-none fw-semibold">Admin Settings</a>. Confirm <strong>Financial Year Start Month</strong> is set to <code>April (Month 4)</code>. All trade ledgers and reports will automatically align to April 1 &ndash; March 31.
                    </li>
                    <li class="mb-2">
                        <strong>Default Records Per Page:</strong> In Admin Settings, select your default table pagination size (<code>20</code>, <code>40</code>, <code>50</code>, <code>80</code>, or <code>100</code>).
                    </li>
                    <li class="mb-2">
                        <strong>Sector Master:</strong> In <a href="<?= base_url('settings/sectors') ?>" class="text-decoration-none fw-semibold">Manage Sectors</a>, customize or add new industry sectors to track equity concentration.
                    </li>
                </ol>
            </div>

