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
                            <li><strong>Tax Classification:</strong> Because holding period is &gt; 365 days (12 months), it is classified as <strong>Long-Term Capital Gain (LTCG)</strong>. If holding was &le; 365 days, it would be classified as <strong>Short-Term Capital Gain (STCG)</strong>.</li>
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
                        <li><strong>TDS Deducted:</strong> 10% TDS if applicable (enter TDS amount e.g. <code>97.50</code> or <code>0.00</code>).</li>
                        <li>Click <strong>Record Dividend</strong>.</li>
                    </ol>
                    <div class="p-2.5 bg-white rounded-3 border small">
                        <strong>How It Works:</strong> Logs the entry in <code>equity_dividends</code>. Net dividend (₹ 877.50) is credited to income. Does <em>not</em> alter your active share count or cost basis. Populates the <strong>Dividend Ledger</strong> and feeds directly into your Annual Passive Income report for Form 26AS/AIS reconciliation.
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
                            Under statutory rules, the cost of acquisition of bonus shares is <strong>NIL (₹ 0.00)</strong>.
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
                            <em>How It Works:</em> Closes parent lots and creates target stock lots inheriting historical cost and holding days.
                        </div>
                        <div class="small text-secondary">
                            <strong>Demerger (e.g. Reliance &rarr; Jio Financial Services):</strong> Enter parent stock, resulting spun-off stock, and official Cost Apportionment % (e.g. 8.9% to JFSL, 91.1% retained in Reliance).
                            <em>How It Works:</em> Apportions the cost basis of each active parent lot by 91.1% and allocates matching shares in the demerged entity at 8.9% cost basis, grandfathering original acquisition dates.
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

