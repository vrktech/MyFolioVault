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
                <button class="nav-link text-start py-2 px-3 rounded-3 small fw-semibold" id="tab-reports-btn" data-bs-toggle="pill" data-bs-target="#tab-reports" type="button" role="tab">
                    <i class="bi bi-file-earmark-bar-graph me-2 text-primary"></i> 8. Tax Reports &amp; Capital Gains
                </button>
                <button class="nav-link text-start py-2 px-3 rounded-3 small fw-semibold" id="tab-ops-btn" data-bs-toggle="pill" data-bs-target="#tab-ops" type="button" role="tab">
                    <i class="bi bi-hdd-network me-2 text-dark"></i> 9. Backup, Cleanup &amp; Production
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
                
                <p class="text-secondary">
                    Welcome to the <strong>Investment Portfolio Tracker</strong>. This platform provides complete self-hosted wealth tracking across Indian asset classes in <strong>INR (₹)</strong> with 100% offline capability and automated FIFO tax computation.
                </p>

                <div class="alert alert-primary bg-primary bg-opacity-10 border-primary rounded-3 small mb-4">
                    <i class="bi bi-shield-lock-fill me-1 text-primary"></i>
                    <strong>Privacy &amp; Offline Architecture:</strong> All assets (Bootstrap 5.3.3, Bootstrap Icons 1.11.3, custom styling) run locally from your server. The app uses the native system font stack (<code>-apple-system, BlinkMacSystemFont, "Segoe UI", Roboto</code>). No internet connection is required, and your financial data never leaves your computer.
                </div>

                <h6 class="fw-bold text-dark mb-2">Step-by-Step: Initial Setup &amp; Configuration</h6>
                <ol class="text-secondary small">
                    <li class="mb-2">
                        <strong>Log In:</strong> Navigate to <code>/login</code> and sign in using your administrator credentials (default: <code>admin@portfolio.local</code> / <code>password123</code>).
                    </li>
                    <li class="mb-2">
                        <strong>Financial Year Configuration:</strong> Navigate to <a href="<?= base_url('settings') ?>" class="text-decoration-none fw-semibold">Admin Settings</a>. For India, confirm that <strong>Financial Year Start Month</strong> is set to <code>April (Month 4)</code>.
                        <div class="bg-light p-2.5 rounded-3 border mt-1">
                            <strong>How It Works:</strong> When set to April, all trade ledgers, dividend audits, capital gains summaries, and cash flow reports automatically calculate ranges from April 1 to March 31 of the following year (e.g., Current FY 2024-25 = <code>2024-04-01</code> to <code>2025-03-31</code>; Last FY 2023-24 = <code>2023-04-01</code> to <code>2024-03-31</code>).
                        </div>
                    </li>
                    <li class="mb-2">
                        <strong>Default Records Per Page:</strong> In Admin Settings, select your default table pagination size (<code>20</code>, <code>40</code>, <code>50</code>, <code>80</code>, or <code>100</code>).
                        <div class="bg-light p-2.5 rounded-3 border mt-1">
                            <strong>How It Works:</strong> Stored in your user database profile (<code>users.records_per_page</code>) and loaded into your active session. All interactive tables across all modules automatically initialize with this page size.
                        </div>
                    </li>
                    <li class="mb-2">
                        <strong>Sector Master:</strong> Go to <a href="<?= base_url('settings/sectors') ?>" class="text-decoration-none fw-semibold">Manage Sectors</a>. Pre-seeded with 18 standard Indian equity sectors (IT, Banking, FMCG, Energy, Automobile, Pharmaceuticals, Metals &amp; Mining, etc.). You can add custom industry sectors anytime.
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
                    Tracks voluntary retirement contributions under Section 80CCD(1B) (additional ₹50,000 tax deduction).
                </p>

                <h6 class="fw-bold text-dark mb-2">7.1 Recording Contributions with 4-Scheme Split</h6>
                <ol class="small text-secondary mb-3">
                    <li>Click <strong>+ Contribution</strong>.</li>
                    <li>Enter Contribution Date and Gross Amount (e.g. ₹ 50,000).</li>
                    <li>Enter POP/gateway friction fee (e.g. ₹ 25). Net allocated = ₹ 49,975.</li>
                    <li>Allocate across the 4 asset classes:
                        <div class="row g-2 mt-1">
                            <div class="col-sm-6"><strong>Scheme E (Equity):</strong> e.g. 50% &rarr; ₹ 24,987.50</div>
                            <div class="col-sm-6"><strong>Scheme C (Corporate Debt):</strong> e.g. 30% &rarr; ₹ 14,992.50</div>
                            <div class="col-sm-6"><strong>Scheme G (Govt Bonds):</strong> e.g. 15% &rarr; ₹ 7,496.25</div>
                            <div class="col-sm-6"><strong>Scheme A (Alternative):</strong> e.g. 5% &rarr; ₹ 2,498.75</div>
                        </div>
                    </li>
                    <li>Enter the NAV for each scheme; allotted units are calculated automatically.</li>
                </ol>

                <h6 class="fw-bold text-dark mb-2">7.2 Quarterly Fee Deductions</h6>
                <p class="text-secondary small">
                    Click <strong>Record Fee Deduction</strong> to enter CRA and Custodian units cancelled at the end of each quarter. This ensures your dashboard balance precisely matches your official quarterly Protean / KFintech NPS statement.
                </p>
            </div>

            <!-- 8. TAX REPORTS -->
            <div class="tab-pane fade" id="tab-reports" role="tabpanel">
                <div class="d-flex align-items-center gap-2 mb-3 pb-2 border-bottom">
                    <span class="badge bg-primary rounded-circle p-2"><i class="bi bi-file-earmark-bar-graph fs-6 text-white"></i></span>
                    <h5 class="fw-bold text-dark mb-0">8. Analytics &amp; Capital Gains Tax Audit</h5>
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

            <!-- 9. BACKUP & MAINTENANCE -->
            <div class="tab-pane fade" id="tab-ops" role="tabpanel">
                <div class="d-flex align-items-center gap-2 mb-3 pb-2 border-bottom">
                    <span class="badge bg-dark rounded-circle p-2"><i class="bi bi-hdd-network fs-6 text-white"></i></span>
                    <h5 class="fw-bold text-dark mb-0">9. Backup, Cleanup &amp; Production Setup</h5>
                </div>

                <h6 class="fw-bold text-dark mb-2">9.1 How to Clean Sample Data for Production Use</h6>
                <p class="text-secondary small">
                    If you tested the platform with sample records and are now ready to track your personal investments:
                </p>
                <div class="bg-dark text-light p-3 rounded-3 font-monospace small mb-2">
                    php cleanup_sample_data.php
                </div>
                <div class="p-2.5 bg-light rounded-3 border small mb-4">
                    <strong>What It Does:</strong>
                    Automatically creates a complete safety snapshot in <code>backup_sample_data.sql</code>, truncates all 22 transaction and holding tables, preserves your administrator user account and all 18 standard equity sectors, and resets all auto-increment IDs to 1.
                </div>

                <h6 class="fw-bold text-dark mb-2">9.2 Creating Manual Database Backups</h6>
                <div class="bg-dark text-light p-3 rounded-3 font-monospace small mb-2">
                    mysqldump -u root investment_portfolio > backup_my_portfolio.sql
                </div>
                <p class="small text-secondary mb-4">
                    To restore from backup: <code>mysql -u root investment_portfolio &lt; backup_my_portfolio.sql</code>.
                </p>

                <h6 class="fw-bold text-dark mb-2">9.3 First-Time Setup Wizard (install.php)</h6>
                <p class="text-secondary small">
                    If deploying on a new computer or server, navigate in your browser to:
                    <code>http://your-server/Portfolio/install.php</code>.
                    The 3-step wizard performs system health checks, provisions all 25 tables, and creates your administrator account.
                    After installation, click the one-click <strong>"Delete install.php Now"</strong> button to permanently delete the setup wizard from disk.
                </p>
            </div>

        </div>
    </div>
</div>

<?= $this->endSection() ?>
