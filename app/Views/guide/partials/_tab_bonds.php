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

                <div class="alert alert-info border-2 border-info rounded-3 small my-3">
                    <div class="fw-bold text-dark mb-1"><i class="bi bi-info-circle-fill text-info me-1"></i> SGB Maturity &amp; Holding Duration Mechanics</div>
                    Capital gains on Sovereign Gold Bonds are classified strictly based on holding duration: holdings exceeding 365 days are classified as <strong>LTCG</strong>, while holdings up to 365 days are classified as <strong>STCG</strong>. Since SGB maturity occurs after 8 years (&gt; 365 days), maturity redemptions are processed as long-term capital gains (LTCG).
                </div>

                <h6 class="fw-bold text-dark mb-2">5.2 Recording Coupon / Interest Payouts</h6>
                <p class="text-secondary small">
                    Click <strong>+ Interest</strong> on the bond row. Gross interest is auto-computed based on held quantity and coupon rate. Enter TDS deducted if applicable (0% for G-Secs and SGBs; 10% for unlisted corporate bonds). The net credit updates your annual income ledger.
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
                    <strong>Capital Gains &amp; Portfolio Impact:</strong>
                    Redemptions with holding periods over 365 days are recorded as <strong>LTCG</strong>. Once redeemed, the bond active units become 0 and it moves automatically to the <strong>Past Holdings / Fully Redeemed</strong> table.
                </div>
            </div>

