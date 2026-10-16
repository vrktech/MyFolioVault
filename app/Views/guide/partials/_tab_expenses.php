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

