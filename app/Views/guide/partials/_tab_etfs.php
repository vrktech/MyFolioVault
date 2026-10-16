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

                <h6 class="fw-bold text-dark mb-2">4.3 ETF Capital Gains Classification</h6>
                <div class="table-responsive">
                    <table class="table table-sm table-bordered small">
                        <thead class="table-light">
                            <tr>
                                <th>Category</th>
                                <th>Capital Gains Treatment</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td><strong>Equity ETFs (&ge; 65% domestic equities)</strong></td>
                                <td>Holding &gt; 12 months &rarr; LTCG. Holding &le; 12 months &rarr; STCG.</td>
                            </tr>
                            <tr>
                                <td><strong>Gold, Silver &amp; Debt ETFs</strong></td>
                                <td>Deemed Short-Term Capital Gains regardless of holding period.</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

