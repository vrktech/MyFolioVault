<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<!-- Guide Header -->
<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
    <div>
        <h4 class="fw-bold text-dark mb-1">
            <i class="bi bi-book-half me-2 text-warning"></i>Application User &amp; Operations Guide
        </h4>
        <div class="text-muted small">
            Exhaustive step-by-step documentation, formulas, and "How It Works" architectural mechanics for every module and action
        </div>
    </div>
    <div class="d-flex gap-2">
        <span class="badge bg-success-subtle text-success border border-success-subtle px-3 py-2 rounded-pill fw-semibold">
            <i class="bi bi-wifi-off me-1"></i> 100% Offline Compatible
        </span>
        <a href="<?= base_url('/') ?>" class="btn btn-outline-secondary btn-sm rounded-3">
            <i class="bi bi-house-door me-1"></i> Welcome Dashboard
        </a>
    </div>
</div>

<!-- Guide Content Layout: Left Nav Tabs, Right Tab Content -->
<div class="row g-4">
    <!-- Left Navigation Menu -->
    <div class="col-lg-3">
        <div class="card border-0 shadow-sm rounded-4 p-3 bg-white sticky-top" style="top: 80px;">
            <div class="nav flex-column nav-pills gap-1" id="guide-tabs" role="tablist" aria-orientation="vertical">
                <button class="nav-link active text-start py-2 px-3 rounded-3 small fw-semibold" id="tab-intro-btn" data-bs-toggle="pill" data-bs-target="#tab-intro" type="button" role="tab">
                    <i class="bi bi-info-circle me-2 text-primary"></i> 1. Getting Started &amp; Config
                </button>
                <button class="nav-link text-start py-2 px-3 rounded-3 small fw-semibold" id="tab-equities-btn" data-bs-toggle="pill" data-bs-target="#tab-equities" type="button" role="tab">
                    <i class="bi bi-graph-up-arrow me-2 text-primary"></i> 2. Equities &amp; Corporate Actions
                </button>
                <button class="nav-link text-start py-2 px-3 rounded-3 small fw-semibold" id="tab-reits-btn" data-bs-toggle="pill" data-bs-target="#tab-reits" type="button" role="tab">
                    <i class="bi bi-buildings me-2 text-info"></i> 3. InvITs &amp; REITs (Distributions)
                </button>
                <button class="nav-link text-start py-2 px-3 rounded-3 small fw-semibold" id="tab-etfs-btn" data-bs-toggle="pill" data-bs-target="#tab-etfs" type="button" role="tab">
                    <i class="bi bi-pie-chart me-2 text-success"></i> 4. Exchange Traded Funds (ETFs)
                </button>
                <button class="nav-link text-start py-2 px-3 rounded-3 small fw-semibold" id="tab-bonds-btn" data-bs-toggle="pill" data-bs-target="#tab-bonds" type="button" role="tab">
                    <i class="bi bi-receipt me-2 text-danger"></i> 5. Bonds &amp; SGB Tax Exemption
                </button>
                <button class="nav-link text-start py-2 px-3 rounded-3 small fw-semibold" id="tab-mfs-btn" data-bs-toggle="pill" data-bs-target="#tab-mfs" type="button" role="tab">
                    <i class="bi bi-briefcase me-2 text-warning"></i> 6. Mutual Funds (SIP &amp; FIFO)
                </button>
                <button class="nav-link text-start py-2 px-3 rounded-3 small fw-semibold" id="tab-nps-btn" data-bs-toggle="pill" data-bs-target="#tab-nps" type="button" role="tab">
                    <i class="bi bi-shield-check me-2 text-secondary"></i> 7. NPS Tier 1 (4-Scheme E/C/G/A)
                </button>
                <button class="nav-link text-start py-2 px-3 rounded-3 small fw-semibold" id="tab-expenses-btn" data-bs-toggle="pill" data-bs-target="#tab-expenses" type="button" role="tab">
                    <i class="bi bi-wallet2 me-2 text-danger"></i> 8. Brokerage &amp; Expenses Ledger
                </button>
                <button class="nav-link text-start py-2 px-3 rounded-3 small fw-semibold" id="tab-reports-btn" data-bs-toggle="pill" data-bs-target="#tab-reports" type="button" role="tab">
                    <i class="bi bi-file-earmark-bar-graph me-2 text-primary"></i> 9. Tax Reports &amp; Capital Gains
                </button>
                <button class="nav-link text-start py-2 px-3 rounded-3 small fw-semibold" id="tab-ops-btn" data-bs-toggle="pill" data-bs-target="#tab-ops" type="button" role="tab">
                    <i class="bi bi-hdd-network me-2 text-dark"></i> 10. Backup, Cleanup &amp; Production
                </button>
                <button class="nav-link text-start py-2 px-3 rounded-3 small fw-semibold text-danger" id="tab-support-btn" data-bs-toggle="pill" data-bs-target="#tab-support" type="button" role="tab">
                    <i class="bi bi-heart-fill me-2 text-danger"></i> 11. Support the Project
                </button>
            </div>
        </div>
    </div>

    <!-- Right Content Tabs -->
    <div class="col-lg-9">
        <div class="tab-content bg-white p-4 p-md-5 rounded-4 shadow-sm border" id="guide-tabContent">

            <!-- 1. GETTING STARTED & CONFIG -->
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
                        Many modern investors trade across multiple brokerages (Zerodha, Groww, AngelOne, ICICI Direct, Upstox) and hold diverse assets—<strong>Direct Equities</strong>, <strong>InvITs &amp; REITs</strong>, <strong>ETFs</strong>, <strong>Bonds &amp; SGBs</strong>, <strong>Mutual Funds</strong>, and <strong>NPS (Tier 1)</strong>. Most commercial platforms either require sharing sensitive broker credentials or fail to accurately handle Indian statutory nuances like FIFO capital gains matching, 4-component REIT distributions, SGB tax exemptions, clean bond pricing, and daily consolidated broker contract notes. MyFolioVault solves this completely locally on your private machine with zero external dependencies, no third-party CDNs, and zero tracking.
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

            <!-- 2. EQUITIES -->
            <div class="tab-pane fade" id="tab-equities" role="tabpanel">
                <div class="d-flex align-items-center gap-2 mb-3 pb-2 border-bottom">
                    <span class="badge bg-primary rounded-circle p-2"><i class="bi bi-graph-up-arrow fs-6 text-white"></i></span>
                    <h5 class="fw-bold text-dark mb-0">2. Equities (Stocks &amp; Corporate Actions)</h5>
                </div>

                <p class="text-secondary">
                    Manage direct equities listed on the National Stock Exchange (NSE) and Bombay Stock Exchange (BSE) with lot-by-lot FIFO matching, dividend ledgers, and an automated corporate actions engine.
                </p>

                <!-- 2.1 ADD STOCK -->
                <div class="card bg-light border p-3 rounded-3 mb-4">
                    <h6 class="fw-bold text-dark mb-2"><i class="bi bi-plus-circle me-1 text-primary"></i>2.1 How to Add a New Stock</h6>
                    <div class="small text-secondary mb-2">
                        <strong>When to use:</strong> When you wish to start tracking a new stock in your portfolio before recording purchase orders.
                    </div>
                    <ol class="small text-secondary mb-3">
                        <li>Navigate to <a href="<?= base_url('equities') ?>" class="fw-semibold text-decoration-none">Equities</a> and click <strong>Add New Stock</strong>.</li>
                        <li><strong>Symbol:</strong> Enter ticker in uppercase (e.g. <code>HDFCBANK</code>, <code>RELIANCE</code>, <code>TCS</code>, <code>INFY</code>).</li>
                        <li><strong>Company Name:</strong> Full registered legal name (e.g. <code>HDFC Bank Limited</code>).</li>
                        <li><strong>Exchange:</strong> Select <code>NSE</code> (default) or <code>BSE</code>.</li>
                        <li><strong>ISIN:</strong> 12-character alphanumeric code (e.g. <code>INE040A01034</code>).</li>
                        <li><strong>Sector:</strong> Pick from master sector list (e.g. <code>Banking &amp; Financial Services</code>).</li>
                        <li><strong>Current Market Price (CMP):</strong> Current share price in ₹ (e.g. <code>1640.50</code>).</li>
                        <li>Click <strong>Save Stock</strong>.</li>
                    </ol>
                    <div class="p-2.5 bg-white rounded-3 border small">
                        <strong>How It Works:</strong> Creates a record in the <code>equities</code> table. Total shares held initializes to 0. The CMP becomes the valuation benchmark used to calculate unrealized gains against purchase lots added later.
                    </div>
                </div>

                <!-- 2.2 BUY TRANSACTION -->
                <div class="card bg-light border p-3 rounded-3 mb-4">
                    <h6 class="fw-bold text-dark mb-2"><i class="bi bi-cart-plus me-1 text-success"></i>2.2 How to Record a BUY Transaction</h6>
                    <div class="small text-secondary mb-2">
                        <strong>When to use:</strong> Whenever you purchase shares through your broker (Zerodha, Groww, ICICI Direct, etc.).
                    </div>
                    <ol class="small text-secondary mb-3">
                        <li>On the Equities page, click the green <strong>+ Trans</strong> button on the stock row.</li>
                        <li>Ensure the <strong>Buy More</strong> tab is selected.</li>
                        <li><strong>Transaction Date:</strong> Trade execution date (e.g. <code>2024-05-15</code>).</li>
                        <li><strong>Quantity:</strong> Number of shares purchased (e.g. <code>50</code>).</li>
                        <li><strong>Buy Price:</strong> Purchase price per share in ₹ (e.g. <code>1520.00</code>).</li>
                        <li><strong>Brokerage &amp; Statutory Taxes:</strong> Sum of Brokerage + STT + Stamp Duty + GST + Exchange fees (e.g. <code>120.00</code>).</li>
                        <li><strong>Notes:</strong> Optional reference (e.g. <code>Zerodha Demat - Long term SIP</code>).</li>
                        <li>Verify the preview: <code>Total Outlay = (50 * 1520) + 120 = ₹ 76,120.00</code>.</li>
                        <li>Click <strong>Confirm Purchase Lot</strong>.</li>
                    </ol>
                    <div class="p-2.5 bg-white rounded-3 border small">
                        <strong>How It Works Behind the Scenes:</strong>
                        <ul class="mb-0 ps-3">
                            <li>Inserts a row into <code>equity_transactions</code> with <code>transaction_type = 'BUY'</code>.</li>
                            <li>Initializes <code>remaining_quantity = 50.0000</code>. This establishes an independent <strong>FIFO lot</strong> with its original acquisition date and cost basis.</li>
                            <li>Recalculates stock active shares (+50) and weighted average buy price:
                                <code>Avg Price = Total Cost Basis of Remaining Lots / Total Active Quantity</code>.
                            </li>
                            <li>Updates unrealized P&amp;L in real time: <code>(CMP - Avg Price) * Active Shares</code>.</li>
                        </ul>
                    </div>
                </div>

                <!-- 2.3 FIFO SELL TRANSACTION -->
                <div class="card bg-light border p-3 rounded-3 mb-4">
                    <h6 class="fw-bold text-dark mb-2"><i class="bi bi-cart-dash me-1 text-danger"></i>2.3 How to Record a SELL Transaction &amp; FIFO Capital Gains</h6>
                    <div class="small text-secondary mb-2">
                        <strong>When to use:</strong> Whenever you sell shares from your holdings.
                    </div>
                    <ol class="small text-secondary mb-3">
                        <li>Click <strong>+ Trans</strong> on the stock row &rarr; select the <strong>Sell Holding</strong> tab.</li>
                        <li><strong>Sell Date:</strong> Execution date (e.g. <code>2025-06-20</code>).</li>
                        <li><strong>Sell Quantity:</strong> Shares to sell (e.g. <code>20</code>). Cannot exceed currently held active shares.</li>
                        <li><strong>Sell Price:</strong> Selling price per share (e.g. <code>1750.00</code>).</li>
                        <li><strong>Brokerage &amp; STT:</strong> Selling friction charges (e.g. <code>85.00</code>).</li>
                        <li>Click <strong>Execute FIFO Sell</strong>.</li>
                    </ol>
                    <div class="p-2.5 bg-white rounded-3 border small">
                        <strong>How the FIFO Matching Algorithm Works:</strong>
                        <ol class="mb-0 ps-3">
                            <li>The engine queries all open purchase lots for this stock sorted chronologically by <code>transaction_date ASC, id ASC</code>.</li>
                            <li>It matches sold shares against the earliest lot:
                                Deducts 20 shares from the <code>2024-05-15</code> lot (remaining shares in that lot becomes 30).
                            </li>
                            <li>Calculates holding duration: <code>2025-06-20 - 2024-05-15 = 401 days</code>.</li>
                            <li><strong>Tax Classification:</strong> Because holding period is &gt; 365 days (12 months), it is classified as <strong>Long-Term Capital Gain (LTCG)</strong> under Section 112A. If holding was &le; 365 days, it would be classified as <strong>Short-Term Capital Gain (STCG)</strong> under Section 111A.</li>
                            <li>Calculates gain: <code>Cost Basis = 20 * 1520 = ₹ 30,400.00</code>; <code>Net Proceeds = (20 * 1750) - 85 = ₹ 34,915.00</code>; <code>Realized LTCG = ₹ 4,515.00</code>.</li>
                            <li>Generates an immutable record in <code>equity_capital_gains</code> linking the sell order to the purchase lot for audit trails.</li>
                        </ol>
                    </div>
                </div>

                <!-- 2.4 DIVIDENDS -->
                <div class="card bg-light border p-3 rounded-3 mb-4">
                    <h6 class="fw-bold text-dark mb-2"><i class="bi bi-cash-coin me-1 text-primary"></i>2.4 How to Record Dividend Payouts &amp; TDS</h6>
                    <div class="small text-secondary mb-2">
                        <strong>When to use:</strong> Whenever a company credits dividend to your linked bank account.
                    </div>
                    <ol class="small text-secondary mb-3">
                        <li>Click <strong>+ Trans</strong> &rarr; select the <strong>Record Dividend</strong> tab.</li>
                        <li><strong>Payout Date:</strong> Bank credit date (e.g. <code>2024-08-10</code>).</li>
                        <li><strong>Dividend Type:</strong> <code>Final</code>, <code>Interim</code>, or <code>Special</code>.</li>
                        <li><strong>Shares Held:</strong> Eligible shares on record date (e.g. <code>50</code>).</li>
                        <li><strong>Dividend Per Share:</strong> In ₹ (e.g. <code>19.50</code>). Auto-calculates gross: <code>50 * 19.50 = ₹ 975.00</code>.</li>
                        <li><strong>TDS Deducted:</strong> Under Sec 194 of IT Act, 10% TDS is deducted if gross dividend exceeds ₹5,000 in a financial year (enter TDS amount e.g. <code>97.50</code> or <code>0.00</code>).</li>
                        <li>Click <strong>Record Dividend</strong>.</li>
                    </ol>
                    <div class="p-2.5 bg-white rounded-3 border small">
                        <strong>How It Works:</strong> Logs the entry in <code>equity_dividends</code>. Net dividend (₹ 877.50) is credited to income. Does <em>not</em> alter your active share count or cost basis. Populates the <strong>Dividend Ledger</strong> and feeds directly into your Annual Income Tax report for Form 26AS/AIS reconciliation.
                    </div>
                </div>

                <!-- 2.5 CORPORATE ACTIONS -->
                <div class="card bg-light border p-3 rounded-3">
                    <h6 class="fw-bold text-dark mb-2"><i class="bi bi-diagram-2 me-1 text-purple" style="color: #6f42c1;"></i>2.5 How to Record Corporate Actions (Splits, Bonus, Rights, Mergers, Demergers)</h6>
                    <p class="small text-secondary mb-3">
                        Click the <strong>Record Corporate Action</strong> button at the top of the Equities module to open the modal.
                    </p>

                    <!-- Real-Time Eligibility Engine -->
                    <div class="p-2.5 bg-white rounded-3 border small mb-3">
                        <strong>Real-Time Eligibility Engine:</strong> When you select a stock and choose the Record Date, the application automatically computes eligible shares held on that exact date:
                        <code>Eligible Shares = SUM(BUY Qty where date &le; Record Date) - SUM(SELL Qty where date &le; Record Date)</code>.
                        A green verification badge displays your eligible share count before you save.
                    </div>

                    <!-- Sub-action: Stock Split -->
                    <div class="border rounded-3 p-3 bg-white mb-3">
                        <div class="fw-bold text-dark mb-1"><i class="bi bi-scissors me-1 text-primary"></i>A. Stock Split (e.g. 1:2 Split)</div>
                        <div class="small text-secondary mb-2">
                            <strong>Inputs:</strong> Stock: <code>HDFCBANK</code>, Record Date: <code>2024-09-01</code>, Ratio (Old): <code>1</code>, Ratio (New): <code>2</code>.
                        </div>
                        <div class="small bg-light p-2 rounded-2 border">
                            <strong>How It Works:</strong>
                            Multiplier = <code>2 / 1 = 2.0</code>. For all active purchase lots acquired on or before the record date:
                            <code>New Quantity = Remaining Quantity * 2.0</code> (e.g. 50 &rarr; 100 shares), and
                            <code>New Buy Price = Original Buy Price / 2.0</code> (e.g. ₹ 1520 &rarr; ₹ 760).
                            Total invested cost basis <code>100 * 760 = ₹ 76,000.00</code> remains completely unchanged! Stock CMP is divided by 2.
                        </div>
                    </div>

                    <!-- Sub-action: Bonus Issue -->
                    <div class="border rounded-3 p-3 bg-white mb-3">
                        <div class="fw-bold text-dark mb-1"><i class="bi bi-gift me-1 text-success"></i>B. Bonus Issue (e.g. 1:1 Bonus)</div>
                        <div class="small text-secondary mb-2">
                            <strong>Inputs:</strong> Stock: <code>TCS</code>, Record Date: <code>2024-09-15</code>, Ratio (Existing): <code>1</code>, Ratio (Bonus): <code>1</code>.
                        </div>
                        <div class="small bg-light p-2 rounded-2 border">
                            <strong>How It Works Behind the Scenes:</strong>
                            Under <strong>Section 55(2)(aa)</strong> of the Indian Income Tax Act, the cost of acquisition of bonus shares is <strong>NIL (₹ 0.00)</strong>.
                            The system creates a new BUY lot with:
                            <code>quantity = eligible_shares (e.g. 35)</code>, <code>price = 0.00</code>, <code>total_amount = 0.00</code>, and <code>transaction_date = Record Date</code>.
                            <strong>Why this is critical:</strong> Older lots retain their original acquisition dates (e.g. 2 years ago) and original purchase prices. The bonus lot enters the FIFO queue as a distinct zero-cost lot. When you sell later, older lots are exhausted first at their fair cost basis, and bonus shares are sold with ₹0 cost, preventing tax audit errors!
                        </div>
                    </div>

                    <!-- Sub-action: Rights Issue -->
                    <div class="border rounded-3 p-3 bg-white mb-3">
                        <div class="fw-bold text-dark mb-1"><i class="bi bi-file-earmark-check me-1 text-info"></i>C. Rights Issue</div>
                        <div class="small text-secondary mb-2">
                            <strong>Inputs:</strong> Stock, Record Date, Subscribed Quantity, and Discounted Subscription Price (e.g. ₹ 1,250.00).
                        </div>
                        <div class="small bg-light p-2 rounded-2 border">
                            <strong>How It Works:</strong> Creates a new purchase lot at the subscribed rights price with the allotment date. Enters the FIFO queue at the discounted cost basis.
                        </div>
                    </div>

                    <!-- Sub-action: Mergers & Demergers -->
                    <div class="border rounded-3 p-3 bg-white">
                        <div class="fw-bold text-dark mb-1"><i class="bi bi-shuffle me-1 text-warning"></i>D. Mergers &amp; Demergers</div>
                        <div class="small text-secondary mb-2">
                            <strong>Merger (e.g. HDFC Ltd into HDFC Bank):</strong> Enter source stock, target stock, and swap ratio (e.g. 42 target shares for 25 parent shares).
                            <em>How It Works:</em> Closes parent lots and creates target stock lots inheriting historical cost and holding days under Section 49(2) of the IT Act.
                        </div>
                        <div class="small text-secondary">
                            <strong>Demerger (e.g. Reliance &rarr; Jio Financial Services):</strong> Enter parent stock, resulting spun-off stock, and official IT Department Cost Apportionment % (e.g. 8.9% to JFSL, 91.1% retained in Reliance).
                            <em>How It Works:</em> Apportions the cost basis of each active parent lot by 91.1% and allocates matching shares in the demerged entity at 8.9% cost basis, grandfathering original acquisition dates under Section 49(2C).
                        </div>
                    </div>
                </div>

                <!-- 2.6 ACTIVE HOLDINGS VS PAST HOLDINGS -->
                <div class="card bg-light border p-3 rounded-3 mt-4">
                    <h6 class="fw-bold text-dark mb-2"><i class="bi bi-archive me-1 text-secondary"></i>2.6 Active Holdings vs. Past Holdings (Closed Positions)</h6>
                    <p class="small text-secondary mb-2">
                        To maintain a clean and uncluttered workspace, all asset modules (Equities, ETFs, Mutual Funds, InvITs/REITs, and Bonds) automatically segregate positions across two dedicated tabs:
                    </p>
                    <ul class="small text-secondary mb-0">
                        <li class="mb-2"><strong>Active Holdings:</strong> Strictly displays securities currently held with positive quantity (<code>quantity &gt; 0</code>). Fully sold or liquidated assets are automatically filtered out of this view.</li>
                        <li><strong>Past Holdings:</strong> Any security with <code>0</code> active quantity is automatically organized into this tab. Shows lifetime Realized Capital Gains (STCG &amp; LTCG), cumulative income earned (dividends, interest, or distributions), net returns, and a convenient <strong>+ Buy Again</strong> button to seamlessly re-enter a position.</li>
                    </ul>
                </div>

                <!-- 2.7 HOW TO EDIT A STOCK -->
                <div class="card bg-light border p-3 rounded-3 mt-4">
                    <h6 class="fw-bold text-dark mb-2"><i class="bi bi-pencil-square me-1 text-primary"></i>2.7 How to Edit a Stock</h6>
                    <div class="small text-secondary mb-2">
                        <strong>When to use:</strong> To correct or update company name, ticker symbol, ISIN, sector, or exchange.
                    </div>
                    <ol class="small text-secondary mb-3 ps-3">
                        <li class="mb-1">On the <a href="<?= base_url('equities') ?>" class="fw-semibold text-decoration-none">Equities Dashboard</a>, locate the stock in the Active Holdings table.</li>
                        <li class="mb-1">In the <strong>Action</strong> column, click the dropdown toggle and select <strong>"Edit Stock Details"</strong> (or click the edit pencil icon).</li>
                        <li class="mb-1">Update Company Name, Symbol, ISIN, Sector, or Exchange (NSE/BSE).</li>
                        <li class="mb-1">Click <strong>"Save Changes"</strong>.</li>
                    </ol>
                    <div class="p-2.5 bg-white rounded-3 border small">
                        <strong>Tip:</strong> To update the Current Market Price (CMP), use the inline CMP edit link on the dashboard or click <strong>"Refresh Live Prices"</strong> to fetch live quotes automatically from Yahoo Finance.
                    </div>
                </div>

                <!-- 2.8 HOW TO DELETE A STOCK -->
                <div class="card bg-light border p-3 rounded-3 mt-4 border-danger border-opacity-25">
                    <h6 class="fw-bold text-danger mb-2"><i class="bi bi-trash3-fill me-1 text-danger"></i>2.8 How to Delete a Stock (and What Happens to Related Transactions)</h6>
                    <div class="small text-secondary mb-2">
                        In the <strong>Action</strong> dropdown on the stock row, click <strong>"Delete Stock"</strong> and confirm the browser prompt.
                    </div>
                    <div class="alert alert-danger bg-danger bg-opacity-10 border-danger rounded-3 small mb-0">
                        <div class="fw-bold text-danger mb-1"><i class="bi bi-exclamation-triangle-fill me-1"></i> What Happens to Related Transactions When a Stock is Deleted:</div>
                        MyFolioVault enforces strict database cascading integrity (<code>ON DELETE CASCADE</code>):
                        <ul class="mb-2 ps-3">
                            <li><strong>All Trades Erased:</strong> Every buy and sell transaction in the trade ledger is permanently deleted.</li>
                            <li><strong>Capital Gains Purged:</strong> All historical realized capital gains (STCG/LTCG) and matched FIFO audit records are permanently erased.</li>
                            <li><strong>Dividends Deleted:</strong> All dividend entries and TDS withholding records for this stock are removed.</li>
                            <li><strong>Corporate Actions Cleared:</strong> Any past split, bonus, or rights records are deleted.</li>
                        </ul>
                        <strong>Important Best Practice:</strong>
                        If you simply sold all shares of a stock, <strong>DO NOT delete the stock</strong>! Instead, record a <strong>SELL</strong> transaction. The system will reduce your active shares to 0 and automatically move the security to the <strong>Past Holdings</strong> tab, preserving your full historical tax records, realized P&amp;L, and cash flow reports. Only use <strong>Delete Stock</strong> if you added an entry by mistake and want to purge it completely.
                    </div>
                </div>
            </div>

            <!-- 3. INVITs & REITs -->
            <div class="tab-pane fade" id="tab-reits" role="tabpanel">
                <div class="d-flex align-items-center gap-2 mb-3 pb-2 border-bottom">
                    <span class="badge bg-info rounded-circle p-2"><i class="bi bi-buildings fs-6 text-white"></i></span>
                    <h5 class="fw-bold text-dark mb-0">3. InvITs &amp; REITs (4-Component Distributions)</h5>
                </div>

                <p class="text-secondary">
                    Designed for Infrastructure Investment Trusts (InvITs) and Real Estate Investment Trusts (REITs) like Embassy Office Parks, Mindspace REIT, Brookfield India, and PowerGrid InvIT.
                </p>

                <h6 class="fw-bold text-dark mb-2">3.1 Adding a Trust &amp; Trading Units</h6>
                <p class="text-secondary small">
                    Click <strong>Add New Trust</strong>. Enter Symbol (e.g. <code>EMBASSY</code>), Legal Name, Type (<code>REIT</code> or <code>InvIT</code>), ISIN, and CMP (e.g. <code>₹ 385.00</code>). Buy and Sell transactions follow the same strict FIFO lot matching as equities.
                </p>

                <h6 class="fw-bold text-dark mb-2 mt-4">3.2 How to Record 4-Component Quarterly Distributions</h6>
                <ol class="small text-secondary mb-3">
                    <li>Click <strong>+ Dist</strong> on the trust row.</li>
                    <li>Enter <strong>Distribution Date</strong> and <strong>Units Held</strong> on record date.</li>
                    <li>Enter the 4 components from your quarterly manager notice:
                        <ul class="mt-2">
                            <li class="mb-1"><strong>1. Dividend Component (per unit):</strong> Exempt under Sec 10(23FD) if SPV did not opt for concessional tax under Sec 115BAA; taxable if opted.</li>
                            <li class="mb-1"><strong>2. Interest Component (per unit):</strong> Fully taxable at the investor's marginal income tax slab rate.</li>
                            <li class="mb-1"><strong>3. Rental Income Component (per unit):</strong> Taxable at the investor's slab rate.</li>
                            <li class="mb-1"><strong>4. Return of Capital (ROC) / Debt Amortization (per unit):</strong> Capital return reducing investment cost.</li>
                        </ul>
                    </li>
                    <li>Click <strong>Save Distribution</strong>.</li>
                </ol>
                <div class="p-3 bg-light rounded-3 border small">
                    <strong>How Return of Capital (ROC) Works Behind the Scenes:</strong>
                    Return of capital is <em>not</em> treated as taxable income upon receipt. Instead, the application systematically deducts the ROC amount from the unit cost basis of your active purchase lots. If cumulative ROC received over years exceeds your original purchase price, the excess is automatically taxed as income under Section 56(2)(xii).
                </div>
            </div>

            <!-- 4. ETFS -->
            <div class="tab-pane fade" id="tab-etfs" role="tabpanel">
                <div class="d-flex align-items-center gap-2 mb-3 pb-2 border-bottom">
                    <span class="badge bg-success rounded-circle p-2"><i class="bi bi-pie-chart fs-6 text-white"></i></span>
                    <h5 class="fw-bold text-dark mb-0">4. Exchange Traded Funds (ETFs)</h5>
                </div>

                <p class="text-secondary">
                    Categorized tracking for Index, Gold, Silver, Liquid, and International ETFs with split handling and tax rules.
                </p>

                <h6 class="fw-bold text-dark mb-2">4.1 Adding a Categorized ETF</h6>
                <p class="text-secondary small">
                    Click <strong>Add New ETF</strong>. Select category:
                    <code>Index</code> (Nifty 50, Bank Nifty), <code>Gold</code> (Nippon Gold BeES), <code>Silver</code> (Silver BeES), <code>Liquid</code> (Liquid BeES), or <code>International</code> (Nasdaq 100).
                </p>

                <h6 class="fw-bold text-dark mb-2 mt-4">4.2 How to Record ETF Splits (e.g. Nifty BeES 1:10 Split)</h6>
                <ol class="small text-secondary mb-3">
                    <li>Click <strong>Record ETF Split</strong> at the top of the ETFs module.</li>
                    <li>Select the ETF (e.g. <code>NIFTYBEES</code>).</li>
                    <li>Enter the Record Date. The system checks and verifies eligible units held on that date.</li>
                    <li>Enter Split Ratio: <code>1 Old</code> : <code>10 New</code>.</li>
                    <li>Click <strong>Execute ETF Split</strong>.</li>
                </ol>
                <div class="p-2.5 bg-light rounded-3 border small mb-4">
                    <strong>How It Works:</strong> Multiplies units by 10 and divides unit cost basis by 10 across all active purchase lots as of the record date. Total invested capital remains unchanged.
                </div>

                <h6 class="fw-bold text-dark mb-2">4.3 Indian ETF Taxation Architecture</h6>
                <div class="table-responsive">
                    <table class="table table-sm table-bordered small">
                        <thead class="table-light">
                            <tr>
                                <th>Category</th>
                                <th>Applicable Tax Section</th>
                                <th>Tax Treatment</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td><strong>Equity ETFs (&ge; 65% Indian equities)</strong></td>
                                <td>Sec 111A / 112A</td>
                                <td>Holding &gt; 12 months &rarr; LTCG @ 12.5% (with ₹1.25L annual exemption). Holding &le; 12 months &rarr; STCG @ 20%.</td>
                            </tr>
                            <tr>
                                <td><strong>Gold, Silver &amp; Debt ETFs (acquired on/after 1-Apr-2023)</strong></td>
                                <td>Sec 50AA</td>
                                <td>Deemed Short-Term Capital Gains regardless of holding period and taxed at your marginal slab rate.</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- 5. BONDS & SGBS -->
            <div class="tab-pane fade" id="tab-bonds" role="tabpanel">
                <div class="d-flex align-items-center gap-2 mb-3 pb-2 border-bottom">
                    <span class="badge bg-danger rounded-circle p-2"><i class="bi bi-receipt fs-6 text-white"></i></span>
                    <h5 class="fw-bold text-dark mb-0">5. Bonds &amp; Sovereign Gold Bonds (SGBs)</h5>
                </div>

                <p class="text-secondary">
                    Fixed income tracking for Sovereign Gold Bonds (SGB), Government of India Dated Securities (G-Secs), State Development Loans (SDLs), and Corporate NCDs.
                </p>

                <h6 class="fw-bold text-dark mb-2">5.1 Adding a Bond &amp; Secondary Market Clean Price</h6>
                <p class="text-secondary small">
                    Click <strong>Add New Bond</strong>. Enter Face Value (₹1,000 for G-Secs or 1 Gram issue price for SGBs), Coupon Rate (e.g. 2.50% for SGB, 7.18% for G-Sec), Coupon Frequency (Semi-Annual), and Maturity Date.
                    When recording secondary market purchases, enter Clean Price and separate Accrued Interest paid to the seller to ensure accurate cost basis accounting.
                </p>

                <div class="alert alert-warning border-2 border-warning rounded-3 small my-3">
                    <div class="fw-bold text-dark mb-1"><i class="bi bi-star-fill text-warning me-1"></i> SGB Section 47(viic) Tax Exemption Mechanics</div>
                    Under <strong>Section 47(viic)</strong> of the Indian Income Tax Act, any capital gain arising to an individual on redemption of Sovereign Gold Bonds upon maturity (8 years) is <strong>100% EXEMPT from tax</strong>. When recording a maturity exit, the engine flags the transaction as <code>EXEMPT_SGB_MATURITY</code>, zeroing taxable capital gains while crediting full proceeds to cash flow.
                </div>

                <h6 class="fw-bold text-dark mb-2">5.2 Recording Coupon / Interest Payouts</h6>
                <p class="text-secondary small">
                    Click <strong>+ Interest</strong> on the bond row. Gross interest is auto-computed based on held quantity and coupon rate. Enter TDS deducted under Section 193 (0% for G-Secs and SGBs; 10% for unlisted corporate bonds). The net credit updates your annual income ledger.
                </p>

                <h6 class="fw-bold text-dark mb-2 mt-4">5.3 How to Record Redemption After Expiry / Maturity Date</h6>
                <div class="small text-secondary mb-2">
                    When a bond or SGB reaches or passes its maturity date (<code>Maturity Date &le; Today</code>):
                </div>
                <div class="p-3 bg-light rounded-3 border small mb-3">
                    <div class="fw-bold text-dark mb-1"><i class="bi bi-award-fill text-warning me-1"></i> Automated Maturity Detection &amp; The Redeem Button:</div>
                    <ul class="mb-0 ps-3">
                        <li class="mb-1">The dashboard automatically flags the bond with a red <strong>"Matured"</strong> label under the Maturity Date column.</li>
                        <li class="mb-1">A dedicated dark <strong>"Redeem"</strong> button (with a gold award badge) automatically appears on the bond row in the Action column next to <em>"Add Trans"</em> (also available in the split dropdown).</li>
                    </ul>
                </div>
                <ol class="small text-secondary mb-3 ps-3">
                    <li class="mb-1">Click the <strong>"Redeem"</strong> button to open the <strong>Maturity Redemption Modal</strong>.</li>
                    <li class="mb-1"><strong>Redemption Date:</strong> Date when principal was credited to your bank account (defaults to today).</li>
                    <li class="mb-1"><strong>Redeemed Quantity:</strong> Number of units or grams to redeem (pre-filled with all active units).</li>
                    <li class="mb-1"><strong>Redemption Price per Unit (₹):</strong> Enter final settlement price (for SGB: RBI published gold redemption price; for G-Secs/NCDs: Face Value e.g. ₹1,000).</li>
                    <li class="mb-1"><strong>Notes:</strong> Optional remark (e.g. <code>RBI SGB Final Redemption</code>).</li>
                    <li class="mb-1">Click <strong>"Confirm Maturity Redemption"</strong>.</li>
                </ol>
                <div class="p-2.5 bg-white rounded-3 border small">
                    <strong>Tax &amp; Portfolio Impact:</strong>
                    For Sovereign Gold Bonds (SGBs), capital gains are <strong>100% Tax-Exempt</strong> under Section 47(viic) and logged under the exempt section in reports. Once redeemed, the bond active units become 0 and it moves automatically to the <strong>Past Holdings / Fully Redeemed</strong> table.
                </div>
            </div>

            <!-- 6. MUTUAL FUNDS -->
            <div class="tab-pane fade" id="tab-mfs" role="tabpanel">
                <div class="d-flex align-items-center gap-2 mb-3 pb-2 border-bottom">
                    <span class="badge bg-warning rounded-circle p-2"><i class="bi bi-briefcase fs-6 text-dark"></i></span>
                    <h5 class="fw-bold text-dark mb-0">6. Mutual Funds (SIP &amp; Lumpsum)</h5>
                </div>

                <p class="text-secondary">
                    Manage direct and regular mutual fund schemes across AMCs with AMFI scheme codes and FIFO redemptions.
                </p>

                <h6 class="fw-bold text-dark mb-2">6.1 Recording SIP &amp; Lumpsum Purchases</h6>
                <ol class="small text-secondary mb-3">
                    <li>Click <strong>+ Trans</strong> on the fund row &rarr; select <strong>SIP Purchase</strong> or <strong>Lumpsum</strong>.</li>
                    <li>Enter Investment Date, Amount in ₹ (e.g. ₹ 10,000), and Allotted NAV (e.g. ₹ 78.50).</li>
                    <li>The system computes units: <code>Units = Amount / NAV = 127.3885 units</code>.</li>
                    <li>Captures statutory 0.005% mutual fund stamp duty.</li>
                    <li>Click <strong>Confirm Purchase Lot</strong>.</li>
                </ol>

                <h6 class="fw-bold text-dark mb-2">6.2 Redemptions &amp; FIFO Units Matching</h6>
                <p class="text-secondary small">
                    When you redeem units, the engine matches them against your earliest SIP installments. For equity funds, units held &gt; 365 days generate Long-Term Capital Gains (LTCG); units held &le; 365 days generate Short-Term Capital Gains (STCG).
                </p>
            </div>

            <!-- 7. NPS TIER 1 -->
            <div class="tab-pane fade" id="tab-nps" role="tabpanel">
                <div class="d-flex align-items-center gap-2 mb-3 pb-2 border-bottom">
                    <span class="badge bg-secondary rounded-circle p-2"><i class="bi bi-shield-check fs-6 text-white"></i></span>
                    <h5 class="fw-bold text-dark mb-0">7. National Pension System (NPS Tier 1)</h5>
                </div>

                <p class="text-secondary">
                    Tracks voluntary retirement contributions under Section 80CCD(1B) (additional ₹50,000 tax deduction) and Employer contributions under Section 80CCD(2).
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

            <!-- 8. CONSOLIDATED BROKERAGE & EXPENSES -->
            <div class="tab-pane fade" id="tab-expenses" role="tabpanel">
                <div class="d-flex align-items-center gap-2 mb-3 pb-2 border-bottom">
                    <span class="badge bg-danger rounded-circle p-2"><i class="bi bi-wallet2 fs-6 text-white"></i></span>
                    <h5 class="fw-bold text-dark mb-0">8. Consolidated Brokerage &amp; Expenses Ledger</h5>
                </div>

                <p class="text-secondary">
                    Indian brokers (Zerodha, Groww, AngelOne, Upstox, ICICI Direct, Kotak Securities, HDFC Sky) provide consolidated daily contract notes at the end of each trading day where turnover fees, STT, and stamp duty are totaled across all trades. In addition, demat Annual Maintenance Charges (AMC) and CDSL/NSDL DP transaction fees (e.g. ₹15.93 per scrip debit on stock sales) are debited to your trading ledger.
                </p>

                <div class="card bg-light border p-3 rounded-3 mb-4">
                    <h6 class="fw-bold text-dark mb-2"><i class="bi bi-plus-circle me-1 text-danger"></i>8.1 How to Record an Expense</h6>
                    <ol class="small text-secondary mb-3 ps-3">
                        <li class="mb-2">
                            Navigate to <a href="<?= base_url('expenses') ?>" class="fw-semibold text-decoration-none text-danger">Expenses</a> in the sidebar (under Asset Modules).
                        </li>
                        <li class="mb-2">
                            Click <strong>"+ Record Expense"</strong>.
                        </li>
                        <li class="mb-2">
                            <strong>Expense Date:</strong> Enter the date from your broker contract note or bank/demat statement debit.
                        </li>
                        <li class="mb-2">
                            <strong>Amount (₹):</strong> Enter the total charge in rupees (e.g. <code>47.20</code>).
                        </li>
                        <li class="mb-2">
                            <strong>Expense Type:</strong>
                            <ul class="mb-0 mt-1 ps-3">
                                <li><strong>Brokerage Charges:</strong> Direct broker execution commissions and delivery turnover brokerage.</li>
                                <li><strong>STT &amp; Statutory Taxes:</strong> Securities Transaction Tax (STT), Exchange Turnover charges, SEBI Regulatory fees, Stamp Duty, and GST (18%).</li>
                                <li><strong>Platform &amp; Misc Fees:</strong> Demat AMC, CDSL/NSDL DP charges on stock sales, call-and-trade charges, and payment gateway fees.</li>
                            </ul>
                        </li>
                        <li class="mb-2">
                            <strong>Related Module:</strong> Allocate the expense to <code>Equities</code>, <code>Exchange Traded Funds (ETFs)</code>, <code>Bonds &amp; Fixed Income</code>, <code>InvITs &amp; REITs</code>, or <code>Mutual Funds</code>.
                        </li>
                        <li class="mb-2">
                            <strong>Notes / Reference:</strong> Enter contract note number, broker name, or remarks (e.g. <code>Zerodha Contract Note #20260923</code>, <code>CDSL DP charges on Tata Motors</code>).
                        </li>
                        <li>Click <strong>Save Expense</strong>.</li>
                    </ol>
                </div>

                <div class="card bg-light border p-3 rounded-3 mb-4">
                    <h6 class="fw-bold text-dark mb-2"><i class="bi bi-diagram-3 me-1 text-primary"></i>8.2 Automatic Flow into Reports</h6>
                    <p class="small text-secondary mb-2">
                        Expenses recorded here <strong>automatically add up on top of any trade-level friction</strong> across your reports:
                    </p>
                    <ul class="small text-secondary mb-0 ps-3">
                        <li class="mb-1">
                            <strong>Cash Flow &amp; Capital Activity Report:</strong> Summed into the module's total expenses, reducing net cash flow and incrementing activity counts.
                        </li>
                        <li class="mb-1">
                            <strong>Expenses &amp; Statutory Friction Report:</strong> Categorized into Brokerage, STT, and Platform friction, updating grand totals and CSV exports.
                        </li>
                        <li class="mb-1">
                            <strong>Welcome Dashboard:</strong> Reflected in the Consolidated Brokerage &amp; Charges summary card.
                        </li>
                    </ul>
                </div>
            </div>

            <!-- 9. TAX REPORTS -->
            <div class="tab-pane fade" id="tab-reports" role="tabpanel">
                <div class="d-flex align-items-center gap-2 mb-3 pb-2 border-bottom">
                    <span class="badge bg-primary rounded-circle p-2"><i class="bi bi-file-earmark-bar-graph fs-6 text-white"></i></span>
                    <h5 class="fw-bold text-dark mb-0">9. Analytics &amp; Capital Gains Tax Audit</h5>
                </div>

                <p class="text-secondary">
                    The <a href="<?= base_url('reports') ?>" class="text-decoration-none fw-semibold">Reports Suite</a> provides tax-ready filing data organized by Indian Financial Year.
                </p>

                <div class="row g-3">
                    <div class="col-md-6">
                        <div class="p-3 border rounded-3 bg-light h-100">
                            <div class="fw-bold text-dark mb-1">1. Cash Flow &amp; Capital Activity</div>
                            <div class="small text-secondary">Summary of all gross purchases, sales, statutory expenses, and net cash deployed for any Financial Year or All Time.</div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="p-3 border rounded-3 bg-light h-100">
                            <div class="fw-bold text-dark mb-1">2. Capital Gains Audit (ITR-2 / ITR-3)</div>
                            <div class="small text-secondary">Lot-by-lot realized gains audit drill-down with buy dates, sell dates, holding days, buy costs, sale proceeds, and net gain with STCG (@ 20%) and LTCG (@ 12.5%).</div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="p-3 border rounded-3 bg-light h-100">
                            <div class="fw-bold text-dark mb-1">3. Annual Income &amp; Dividend Ledger</div>
                            <div class="small text-secondary">Complete schedule of dividends, bond coupons, and trust distributions with TDS deductions under Sec 194/193 for instant reconciliation with Form 26AS and AIS.</div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="p-3 border rounded-3 bg-light h-100">
                            <div class="fw-bold text-dark mb-1">4. Statutory Friction &amp; Expenses</div>
                            <div class="small text-secondary">Granular breakdown of total friction deducted during trading: Brokerage, STT, GST, and Stamp Duty.</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- 10. BACKUP & MAINTENANCE -->
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

            <!-- 11. SUPPORT THE PROJECT -->
            <div class="tab-pane fade" id="tab-support" role="tabpanel">
                <div class="d-flex align-items-center gap-2 mb-3 pb-2 border-bottom">
                    <span class="badge bg-danger rounded-circle p-2"><i class="bi bi-heart-fill fs-6 text-white"></i></span>
                    <h5 class="fw-bold text-dark mb-0">11. Support the Project</h5>
                </div>

                <div class="card border-0 shadow-sm rounded-4 p-4 mb-4 bg-light border-start border-danger border-4">
                    <h6 class="fw-bold text-dark mb-2">
                        <i class="bi bi-shield-check text-success me-2"></i>100% Free, Private &amp; Open Source Wealth Management
                    </h6>
                    <p class="text-secondary small mb-2">
                        <strong>MyFolioVault</strong> is built on the core belief that managing personal and family wealth should be private, transparent, and completely free of charge. Your portfolio data stays strictly on your machine—there are no tracking scripts, no ads, no paywalls, and no subscription lock-in.
                    </p>
                    <p class="text-secondary small mb-0">
                        If MyFolioVault saves you time, helps you manage your family wealth, or simplifies your financial bookkeeping, please consider supporting its continuous development and hosting. Even a small contribution—like buying a cup of coffee or chai—directly keeps the project alive:
                    </p>
                </div>

                <div class="row g-4 mb-4">
                    <!-- GitHub Sponsors Card -->
                    <div class="col-md-6">
                        <div class="card h-100 border rounded-4 shadow-sm p-4 text-center d-flex flex-column justify-content-between">
                            <div>
                                <div class="mb-3">
                                    <span class="badge bg-danger-subtle text-danger p-3 rounded-circle">
                                        <i class="bi bi-github fs-2"></i>
                                    </span>
                                </div>
                                <h4 class="fw-bold text-dark mb-2 fs-5">GitHub Sponsors</h4>
                                <p class="text-secondary small mb-3">
                                    Ideal for international contributors, developers, and backers who prefer recurring or one-time card sponsorships.
                                </p>
                            </div>
                            <div>
                                <a href="https://github.com/sponsors/vrktech" target="_blank" class="btn btn-outline-danger rounded-pill px-4 fw-bold w-100">
                                    <i class="bi bi-heart-fill me-1"></i> Sponsor on GitHub
                                </a>
                                <div class="text-muted small mt-2">github.com/sponsors/vrktech</div>
                            </div>
                        </div>
                    </div>

                    <!-- UPI Payment Card (India) -->
                    <div class="col-md-6">
                        <div class="card h-100 border rounded-4 shadow-sm p-4 text-center d-flex flex-column justify-content-between bg-white">
                            <div>
                                <div class="mb-2">
                                    <span class="badge bg-success-subtle text-success p-2 px-3 rounded-pill fw-bold small">
                                        <i class="bi bi-lightning-charge-fill me-1"></i> UPI Direct (India)
                                    </span>
                                </div>
                                <h4 class="fw-bold text-dark mb-1 fs-5">Scan &amp; Pay via Any UPI App</h4>
                                <div class="text-muted small mb-3">Google Pay &bull; PhonePe &bull; Paytm &bull; BHIM &bull; CRED</div>
                                
                                <!-- QR Code -->
                                <div class="d-inline-block p-2 bg-white rounded-3 shadow-sm border mb-3">
                                    <img src="https://api.qrserver.com/v1/create-qr-code/?size=160x160&amp;data=upi%3A%2F%2Fpay%3Fpa%3Dkarthickvr%40oksbi%26pn%3DMyFolioVault%26cu%3DINR" 
                                         alt="UPI QR Code" 
                                         width="160" 
                                         height="160" 
                                         class="img-fluid rounded">
                                </div>

                                <div class="mb-2">
                                    <span class="badge bg-light text-dark border px-3 py-2 rounded-pill font-monospace small">
                                        karthickvr@oksbi
                                    </span>
                                </div>
                            </div>
                            <div>
                                <a href="upi://pay?pa=karthickvr@oksbi&amp;pn=MyFolioVault&amp;cu=INR" class="btn btn-success rounded-pill px-4 fw-bold w-100 d-md-none">
                                    <i class="bi bi-phone me-1"></i> Open in UPI App
                                </a>
                                <div class="text-muted small mt-2">Zero platform fees &bull; Instant ₹ INR transfer</div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Star on GitHub & Social Media Sharing Card -->
                <div class="card border rounded-4 p-4 bg-light shadow-sm">
                    <div class="d-flex align-items-center justify-content-between flex-wrap gap-3 pb-3 mb-3 border-bottom">
                        <div>
                            <div class="fw-bold text-dark mb-1">
                                <i class="bi bi-star-fill text-warning me-1"></i> Star the Repository
                            </div>
                            <div class="text-secondary small">
                                Helps give MyFolioVault visibility and lets other retail investors discover it.
                            </div>
                        </div>
                        <a href="https://github.com/vrktech/MyFolioVault" target="_blank" class="btn btn-outline-dark btn-sm rounded-pill px-4 fw-bold">
                            <i class="bi bi-star me-1"></i> Star on GitHub
                        </a>
                    </div>

                    <div>
                        <div class="fw-bold text-dark mb-1">
                            <i class="bi bi-share-fill text-primary me-1"></i> Share on Social Media &amp; Communities
                        </div>
                        <div class="text-secondary small mb-3">
                            Word of mouth is what keeps open-source projects thriving. Spread the word with your investment network:
                        </div>
                        <div class="d-flex gap-2 flex-wrap">
                            <a href="https://twitter.com/intent/tweet?text=Check%20out%20MyFolioVault%20-%20Free%2C%20100%25%20offline%2C%20and%20private%20portfolio%20tracker%20tailored%20for%20Indian%20investors%20(NSE%2FBSE%2C%20Mutual%20Funds%2C%20SGBs%2C%20REITs)&amp;url=https%3A%2F%2Fgithub.com%2Fvrktech%2FMyFolioVault" 
                               target="_blank" 
                               class="btn btn-dark btn-sm rounded-pill px-3">
                                <i class="bi bi-twitter-x me-1"></i> Post on X
                            </a>
                            <a href="https://www.linkedin.com/sharing/share-offsite/?url=https%3A%2F%2Fgithub.com%2Fvrktech%2FMyFolioVault" 
                               target="_blank" 
                               class="btn btn-primary btn-sm rounded-pill px-3" style="background-color: #0077b5; border-color: #0077b5;">
                                <i class="bi bi-linkedin me-1"></i> Share on LinkedIn
                            </a>
                            <a href="https://api.whatsapp.com/send?text=Check%20out%20MyFolioVault%20-%20A%20free%2C%20100%25%20offline%20portfolio%20tracker%20for%20Indian%20investors%3A%20https%3A%2F%2Fgithub.com%2Fvrktech%2FMyFolioVault" 
                               target="_blank" 
                               class="btn btn-success btn-sm rounded-pill px-3" style="background-color: #25d366; border-color: #25d366;">
                                <i class="bi bi-whatsapp me-1"></i> Share on WhatsApp
                            </a>
                            <a href="https://t.me/share/url?url=https%3A%2F%2Fgithub.com%2Fvrktech%2FMyFolioVault&amp;text=MyFolioVault%20-%20Free%20and%20offline%20portfolio%20tracker%20for%20Indian%20investors" 
                               target="_blank" 
                               class="btn btn-info btn-sm rounded-pill px-3 text-white" style="background-color: #0088cc; border-color: #0088cc;">
                                <i class="bi bi-telegram me-1"></i> Share on Telegram
                            </a>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>

    <!-- DISCLAIMER & LIMITATION OF LIABILITY -->
    <div class="card border-0 shadow-sm rounded-4 bg-light p-4 mt-4 border-start border-warning border-4">
        <div class="d-flex align-items-center gap-2 mb-2">
            <span class="badge bg-warning-subtle text-warning-emphasis p-2 rounded-circle">
                <i class="bi bi-shield-exclamation fs-5"></i>
            </span>
            <h5 class="fw-bold text-dark mb-0">Disclaimer &amp; Limitation of Liability</h5>
        </div>
        <div class="small text-secondary mb-3">
            <strong>Please read carefully before using this application:</strong>
        </div>
        <ol class="small text-secondary mb-0 ps-3">
            <li class="mb-2">
                <strong>"As Is" Software:</strong> This application is open-source software provided on an <strong>"AS IS"</strong> and <strong>"AS AVAILABLE"</strong> basis without warranties of any kind, either express or implied, including but not limited to merchantability, fitness for a particular purpose, or freedom from defects.
            </li>
            <li class="mb-2">
                <strong>No Liability for Data Loss or Damages:</strong> In no event shall the author(s), contributor(s), or copyright holder(s) be held liable for any direct, indirect, incidental, special, consequential, or punitive damages (including, without limitation, loss of data, database corruption, software errors, downtime, or business interruption) arising out of the installation, use, or inability to use this software.
            </li>
            <li class="mb-2">
                <strong>Not Financial or Tax Advice:</strong> This tool is developed strictly for personal portfolio tracking, bookkeeping, and educational utility. It does <strong>not</strong> constitute financial, legal, investment, or tax advice under SEBI regulations or the Indian Income Tax Act. Tax calculations, corporate action formulas (bonus, splits, mergers), and capital gains treatments should always be independently validated against official broker contract notes, depository statements (CDSL/NSDL), and certified tax professionals before filing returns.
            </li>
            <li class="mb-0">
                <strong>Backup Responsibility:</strong> You are solely responsible for securing your environment, maintaining regular database backups, protecting passwords/encryption keys, and verifying calculation results.
            </li>
        </ol>
    </div>
</div>

<?= $this->endSection() ?>
