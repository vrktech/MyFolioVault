# Investment Portfolio Tracker — Complete User & Operations Manual
**Indian Financial Market Edition &bull; 100% Local Offline Wealth Management**

---

## Table of Contents
1. [System Architecture & Core Principles](#1-system-architecture--core-principles)
2. [Initial Setup, Profile & Preferences](#2-initial-setup-profile--preferences)
3. [Module 1: Equities (Stocks & Shares)](#3-module-1-equities-stocks--shares)
   - [3.1 How to Add a New Stock](#31-how-to-add-a-new-stock)
   - [3.2 How to Record a BUY Transaction](#32-how-to-record-a-buy-transaction)
   - [3.3 How to Record a SELL Transaction & FIFO Capital Gains](#33-how-to-record-a-sell-transaction--fifo-capital-gains)
   - [3.4 How to Record Dividend Payouts & TDS](#34-how-to-record-dividend-payouts--tds)
   - [3.5 How to Record Corporate Actions](#35-how-to-record-corporate-actions)
     - [3.5.1 Stock Splits](#351-stock-splits)
     - [3.5.2 Bonus Issues](#352-bonus-issues)
     - [3.5.3 Rights Issues](#353-rights-issues)
     - [3.5.4 Mergers](#354-mergers)
     - [3.5.5 Demergers](#355-demergers)
4. [Module 2: InvITs & REITs](#4-module-2-invits--reits)
   - [4.1 How to Add a New Trust](#41-how-to-add-a-new-trust)
   - [4.2 How to Record Buy & Sell Transactions](#42-how-to-record-buy--sell-transactions)
   - [4.3 How to Record 4-Component Quarterly Distributions](#43-how-to-record-4-component-quarterly-distributions)
5. [Module 3: Exchange Traded Funds (ETFs)](#5-module-3-exchange-traded-funds-etfs)
   - [5.1 How to Add a Categorized ETF](#51-how-to-add-a-categorized-etf)
   - [5.2 How to Record Buy & Sell Orders](#52-how-to-record-buy--sell-orders)
   - [5.3 How to Record ETF Splits](#53-how-to-record-etf-splits)
   - [5.4 ETF Taxation Rules (Equity vs Sec 50AA)](#54-etf-taxation-rules-equity-vs-sec-50aa)
6. [Module 4: Bonds & Fixed Income](#6-module-4-bonds--fixed-income)
   - [6.1 How to Add a Bond or SGB](#61-how-to-add-a-bond-or-sgb)
   - [6.2 How to Record Buy Transactions (Clean Price vs Accrued Interest)](#62-how-to-record-buy-transactions-clean-price-vs-accrued-interest)
   - [6.3 How to Record Coupon / Interest Payouts & TDS](#63-how-to-record-coupon--interest-payouts--tds)
   - [6.4 Sovereign Gold Bond (SGB) Sec 47(viic) Tax Exemption](#64-sovereign-gold-bond-sgb-sec-47viic-tax-exemption)
7. [Module 5: Mutual Funds](#7-module-5-mutual-funds)
   - [7.1 How to Add a Fund & Folio](#71-how-to-add-a-fund--folio)
   - [7.2 How to Record SIP & Lumpsum Investments](#72-how-to-record-sip--lumpsum-investments)
   - [7.3 How to Record Redemptions (FIFO Units Matching)](#73-how-to-record-redemptions-fifo-units-matching)
8. [Module 6: National Pension System (NPS Tier 1)](#8-module-6-national-pension-system-nps-tier-1)
   - [8.1 How to Set Up PRAN & Pension Fund Manager](#81-how-to-set-up-pran--pension-fund-manager)
   - [8.2 How to Record Voluntary Contributions (Scheme E/C/G/A Split)](#82-how-to-record-voluntary-contributions-scheme-ecga-split)
   - [8.3 How to Record Quarterly Fee & Unit Deductions](#83-how-to-record-quarterly-fee--unit-deductions)
9. [Analytics, Reports & Capital Gains Tax Audit](#9-analytics-reports--capital-gains-tax-audit)
10. [Production Deployment, Data Cleanup & Backup](#10-production-deployment-data-cleanup--backup)

---

## 1. System Architecture & Core Principles

**Investment Portfolio Tracker** is engineered specifically for Indian retail investors, HNIs, and family offices managing multiple asset classes denominated in **Indian Rupees (₹ INR)**.

### Core Architecture Pillars:
1. **100% Offline by Design**:
   - The application does not contact Google Fonts, third-party CDNs, or external tracking analytics.
   - All vendor stylesheets (Bootstrap 5.3.3) and icon fonts (Bootstrap Icons 1.11.3) are bundled locally in `public/assets/`.
   - The user interface renders with the clean native system font stack (`-apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Arial, sans-serif`).
   - Your financial records, trade entries, and personal net worth remain strictly confidential on your host machine.
2. **Indian Tax & Statutory Conformity**:
   - Built around the Indian Financial Year cycle (April 1 to March 31).
   - Automated FIFO (First-In, First-Out) queue management complying with Section 45 and Section 48 of the Indian Income Tax Act.
   - Computes Short-Term Capital Gains (STCG) and Long-Term Capital Gains (LTCG) with accurate holding periods in days.
   - Incorporates updated capital gains tax rates under the Finance Act (e.g. STCG @ 20%, LTCG @ 12.5% with ₹1.25 Lakh annual exemption under Sec 112A).
   - Captures statutory friction: Securities Transaction Tax (STT), Stamp Duty, Exchange Turnover charges, SEBI turnover fees, and GST.

---

## 2. Initial Setup, Profile & Preferences

### 2.1 First-Time Login
1. Open your browser and navigate to the application URL (e.g., `http://localhost/Portfolio/public/` or `http://localhost/Portfolio/`).
2. Log in using your administrator credentials:
   - **Email**: `admin@portfolio.local`
   - **Password**: `password123`

### 2.2 Configuring Financial Year & System Preferences
Navigate to **Admin Settings** (gear icon in sidebar):
- **Financial Year Start Month**:
  - For India, select **April (Month 4)**.
  - *How It Works*: Every report, trade ledger, dividend filter, and tax audit evaluates the financial year as starting on April 1 and ending on March 31 of the following year (e.g., FY 2024-25 = `2024-04-01` to `2025-03-31`).
- **Default Records Per Page**:
  - Choose between `20`, `40`, `50`, `80`, or `100` records per page.
  - *How It Works*: Stored in your user session and database profile (`users.records_per_page`). All interactive tables in Equities, ETFs, Bonds, InvITs, Mutual Funds, and NPS default to this page size.
- **Sector Master Management**:
  - Accessible via **Admin Settings &rarr; Manage Sectors** (`/settings/sectors`).
  - Pre-seeded with 18 standard Indian equity sectors (Banking & Financial Services, Information Technology, Energy, Automobiles, FMCG, Pharmaceuticals, Metals & Mining, Power, Real Estate, etc.). You can add custom sectors anytime.

---

## 3. Module 1: Equities (Stocks & Shares)

The Equities module tracks direct shares listed on the National Stock Exchange (NSE) and Bombay Stock Exchange (BSE).

```
┌─────────────────────────────────────────────────────────────┐
│                    EQUITIES LIFECYCLE                       │
│                                                             │
│   [ Add Stock ] ──► [ BUY Order ] ──► Establishes FIFO Lot  │
│                           │                                 │
│                           ├──► [ Corporate Action ]         │
│                           │     (Split / Bonus / Rights)    │
│                           │                                 │
│                           ├──► [ Dividend Received ]        │
│                           │     (Gross, TDS, Net Payout)    │
│                           │                                 │
│                           └──► [ FIFO SELL Order ]          │
│                                      │                      │
│                                      ▼                      │
│                               [ FIFO Matching ]             │
│                                (Earliest Lot First)         │
│                                      │                      │
│                                      ▼                      │
│                          Holding Days Calculation           │
│                           ├── <= 365 days ──► STCG (20%)    │
│                           └── > 365 days  ──► LTCG (12.5%)  │
└─────────────────────────────────────────────────────────────┘
```

---

### 3.1 How to Add a New Stock

Use this action whenever you wish to track a new equity security in your portfolio before recording purchase orders.

#### Step-by-Step Instructions:
1. Navigate to **Equities** from the sidebar.
2. Click the **Add New Stock** button in the top right corner.
3. Complete the stock master form:
   - **Symbol**: Enter the official ticker symbol on NSE (e.g. `HDFCBANK`, `RELIANCE`, `TCS`, `INFY`). The symbol is automatically formatted in uppercase.
   - **Company Name**: Enter the full registered legal name (e.g. `HDFC Bank Limited`).
   - **Exchange**: Select `NSE` (default) or `BSE`.
   - **ISIN**: Enter the 12-character International Securities Identification Number (e.g. `INE040A01034`).
   - **Sector**: Select the industry sector from the dropdown (e.g. `Banking & Financial Services`).
   - **Current Market Price (CMP)**: Enter the latest market closing price in ₹ (e.g. `1640.50`).
4. Click **Save Stock**.

#### How It Works:
- The system checks for duplicate symbols under your user account.
- Creates a master row in `equities`.
- Initializes holding metrics (`total_quantity = 0`, `invested_amount = 0.00`).
- The CMP serves as the benchmark against which unrealized gain/loss is calculated once buy lots are added.

---

### 3.2 How to Record a BUY Transaction

Use this action whenever you purchase shares through your broker (Zerodha, Groww, ICICI Direct, HDFC Sky, AngelOne, etc.).

#### Step-by-Step Instructions:
1. On the **Equities** dashboard, locate the stock row in the **Active Holdings** table.
2. Click the green **+ Trans** button on that stock's row (or click the main **Add Transaction** button and pick the stock from the dropdown).
3. The Transaction modal opens. Ensure the **Buy More** tab is selected (green button).
4. Fill in the transaction parameters:
   - **Transaction Date**: Enter the trade execution date (e.g. `2024-05-15`).
   - **Quantity**: Enter the number of shares bought (e.g. `50`).
   - **Buy Price (per share)**: Enter the executed purchase price in ₹ (e.g. `1520.00`). Defaults to the current CMP.
   - **Brokerage & Statutory Taxes**: Enter the sum of Brokerage + STT + Stamp Duty + GST + Exchange Turn-over charges (e.g. `120.00`).
   - **Notes / Demat Account**: Optional reference note (e.g. `Zerodha Demat - Long term SIP`).
5. Observe the live preview:
   $$\text{Total Cash Outlay} = (\text{Quantity} \times \text{Price}) + \text{Charges} = (50 \times 1520.00) + 120.00 = ₹\text{ }76,120.00$$
6. Click **Confirm Purchase Lot**.

#### How It Works Behind the Scenes:
- Inserts a record into `equity_transactions` with `transaction_type = 'BUY'`.
- Initializes `remaining_quantity = 50.0000`. This creates an independent **FIFO lot** that preserves its acquisition date (`2024-05-15`) and unit cost (`₹ 1,520.00`).
- Updates the parent stock metrics:
  - Total Active Shares increases by `50`.
  - Invested capital increases by `₹ 76,000.00` (excluding charges which are logged under statutory friction).
  - Weighted Average Buy Price is recalculated across all active open lots:
    $$\text{Average Price} = \frac{\sum (\text{Remaining Quantity}_i \times \text{Buy Price}_i)}{\sum \text{Remaining Quantity}_i}$$
  - Unrealized Gain/Loss is updated in real time against CMP:
    $$\text{Unrealized P\&L} = (\text{CMP} - \text{Average Price}) \times \text{Active Shares}$$

---

### 3.3 How to Record a SELL Transaction & FIFO Capital Gains

Use this action whenever you exit all or part of your shareholding.

#### Step-by-Step Instructions:
1. Locate the stock on the **Equities** dashboard.
2. Click the **+ Trans** button on that row.
3. In the modal, select the **Sell Holding** tab (red button).
4. Fill in the sell parameters:
   - **Sell Date**: Enter the date shares were sold (e.g. `2025-06-20`).
   - **Sell Quantity**: Enter shares to sell (e.g. `20`). The system strictly restricts this input to $\le$ your currently held active quantity.
   - **Sell Price (per share)**: Enter executed selling price (e.g. `1750.00`).
   - **Brokerage & STT**: Enter total brokerage and selling STT (e.g. `85.00`).
   - **Notes**: e.g., `Partial profit booking`.
5. Observe the live preview:
   - Net Cash Proceeds: $(20 \times 1750.00) - 85.00 = ₹\text{ }34,915.00$.
   - Estimated Realized Gain: $₹\text{ }34,915.00 - (20 \times 1520.00) = +₹\text{ }4,515.00$.
6. Click **Execute FIFO Sell**.

#### How the FIFO Matching Engine Works:
1. The engine queries all open purchase lots for this stock ordered by `transaction_date ASC, id ASC`.
2. It takes the earliest available lot:
   - Lot 1 (`2024-05-15`): Held 50 shares @ ₹ 1,520.00.
3. Matches 20 shares from Lot 1:
   - Deducts 20 from Lot 1: Lot 1 `remaining_quantity` becomes `30.0000`.
4. Calculates the exact holding duration:
   $$\text{Holding Period} = \text{Date}(\text{2025-06-20}) - \text{Date}(\text{2024-05-15}) = 401\text{ days}$$
5. **Tax Classification**:
   - For Indian listed equities, holding period $> 365\text{ days}$ qualifies as **Long-Term Capital Gain (LTCG)** under Section 112A.
   - If holding was $\le 365\text{ days}$, it qualifies as **Short-Term Capital Gain (STCG)** under Section 111A.
6. Computes Capital Gain:
   $$\text{Cost Basis} = 20 \times 1520.00 = ₹\text{ }30,400.00$$
   $$\text{Net Sell Proceeds} = (20 \times 1750.00) - 85.00 = ₹\text{ }34,915.00$$
   $$\text{Realized LTCG} = ₹\text{ }34,915.00 - ₹\text{ }30,400.00 = +₹\text{ }4,515.00$$
7. Creates an immutable audit row in `equity_capital_gains` linking the exit transaction ID to the purchase lot ID.
8. If the quantity sold was greater than Lot 1, the engine drains Lot 1 to 0 and continues matching remaining shares against Lot 2, Lot 3, etc., generating distinct tax lots for each vintage.

---

### 3.4 How to Record Dividend Payouts & TDS

Use this action whenever a company credits dividends to your bank account.

#### Step-by-Step Instructions:
1. Click **+ Trans** on the stock row.
2. Select the **Record Dividend** tab (blue button).
3. Enter dividend details:
   - **Payout Date**: Enter the date dividend was credited to your bank account (e.g. `2024-08-10`).
   - **Dividend Type**: Select `Final`, `Interim`, or `Special`.
   - **Shares Held on Record Date**: Enter eligible shares (e.g. `50`).
   - **Dividend Per Share**: Enter dividend per share in ₹ (e.g. `19.50`).
   - **Total Gross Dividend**: Automatically calculates $50 \times 19.50 = ₹\text{ }975.00$ (or enter custom gross amount).
   - **TDS Deducted**: Under Section 194 of the IT Act, companies deduct 10% TDS if total dividend paid to an Indian resident in a financial year exceeds ₹5,000. Enter TDS if deducted (e.g. `97.50`), else enter `0.00`.
   - **Notes**: e.g., `Final Dividend FY24 @ ₹19.50/share`.
4. Click **Record Dividend**.

#### How It Works:
- Inserts a record into `equity_dividends`.
- Net Dividend credited = $\text{Gross} - \text{TDS} = 975.00 - 97.50 = ₹\text{ }877.50$.
- Does **not** modify your active share quantity or cost basis.
- Immediately appears in the **Dividend Ledger** tab with Financial Year filters.
- Summarizes into the **Income Report** for easy comparison with your Form 26AS / Annual Information Statement (AIS).

---

### 3.5 How to Record Corporate Actions

All corporate actions are managed through the dedicated **Record Corporate Action** button located at the top of the Equities dashboard.

```
┌─────────────────────────────────────────────────────────────┐
│                 CORPORATE ACTIONS MATRIX                    │
│                                                             │
│   Stock Split   ──► Multiplies shares, divides cost/share.  │
│                     Total invested capital invariant.       │
│                                                             │
│   Bonus Issue   ──► Allots zero-cost shares (Price = ₹0.00).│
│                     Enters FIFO queue as of Record Date.    │
│                                                             │
│   Rights Issue  ──► Allots shares at discounted rights price│
│                     Creates new purchase lot at rights cost.│
│                                                             │
│   Merger        ──► Swaps parent shares into target stock.  │
│                     Inherits historical cost & holding days.│
│                                                             │
│   Demerger      ──► Splits parent cost into spun-off entity │
│                     using IT Dept cost apportionment %.     │
└─────────────────────────────────────────────────────────────┘
```

#### Real-Time Eligibility Verification Engine
Before saving any corporate action, select the stock and enter the **Record Date**. The application automatically sends an asynchronous request to verify how many shares you held on that exact date:
$$\text{Eligible Shares} = \sum \text{BUY Quantity}_{(\text{date} \le \text{Record Date})} - \sum \text{SELL Quantity}_{(\text{date} \le \text{Record Date})}$$
A green verification badge confirms:
`Eligible shares on 2024-09-01: 50 shares`

---

#### 3.5.1 Stock Splits

Used when a company subdivides its face value (e.g. ₹10 face value split into ₹2 face value &rarr; 1:5 split; or ₹2 into ₹1 &rarr; 1:2 split).

- **Example**: HDFC Bank executes a **1:2 Split** with Record Date `2024-09-01`.
- **Inputs**:
  - **Action Type**: Select `Stock Split`.
  - **Stock**: `HDFCBANK`.
  - **Record Date**: `2024-09-01`.
  - **Ratio (Old)**: `1`.
  - **Ratio (New)**: `2`.
- **How It Works**:
  1. The split multiplier is:
     $$M = \frac{\text{Ratio New}}{\text{Ratio Old}} = \frac{2}{1} = 2.0$$
  2. For every open purchase lot acquired on or before `2024-09-01`:
     $$\text{New Quantity} = \text{Remaining Quantity} \times 2.0 = 50 \times 2 = 100\text{ shares}$$
     $$\text{New Buy Price} = \frac{\text{Original Buy Price}}{2.0} = \frac{1520.00}{2} = ₹\text{ }760.00$$
  3. Total invested capital remains identical:
     $$100 \times 760.00 = ₹\text{ }76,000.00$$
  4. The stock CMP is updated proportionally ($1640.50 / 2 = ₹\text{ }820.25$).
  5. An entry is recorded in `corporate_actions` documenting the ratio and action date.

---

#### 3.5.2 Bonus Issues

Used when a company issues free bonus shares to existing shareholders (e.g. 1:1, 1:2, or 2:1 bonus).

- **Example**: TCS announces a **1:1 Bonus Issue** with Record Date `2024-09-15`. You hold 35 shares.
- **Inputs**:
  - **Action Type**: Select `Bonus Issue`.
  - **Stock**: `TCS`.
  - **Record Date**: `2024-09-15`.
  - **Ratio (Existing)**: `1`.
  - **Ratio (Bonus)**: `1`.
- **How It Works**:
  1. The engine checks eligible shares as of `2024-09-15` (35 shares).
  2. Calculates bonus shares to allocate:
     $$\text{Allotted Bonus Shares} = \frac{35}{1} \times 1 = 35\text{ shares}$$
  3. Under **Section 55(2)(aa)** of the Indian Income Tax Act, the cost of acquisition of bonus shares is **NIL (₹ 0.00)**.
  4. The engine inserts a new BUY transaction lot into `equity_transactions`:
     - `transaction_type = 'BUY'`
     - `transaction_date = '2024-09-15'`
     - `quantity = 35`
     - `price = 0.00`
     - `total_amount = 0.00`
     - `brokerage = 0.00`, `stt_taxes = 0.00`
     - `notes = 'Bonus Issue (1:1) allotment on record date 2024-09-15'`
  5. Your total holding becomes $35 + 35 = 70\text{ shares}$.
  6. **Why this preserves FIFO tax accuracy**:
     - The original 35 shares retain their original purchase date (e.g. `2023-01-10`) and cost basis (e.g. ₹ 3,200).
     - The 35 bonus shares enter the FIFO queue as of `2024-09-15` with zero cost basis.
     - When you sell shares in the future, the older lot is taxed first using its original purchase date. Once exhausted, the bonus lot is sold with cost basis ₹0.00 and acquisition date `2024-09-15`, generating 100% accurate capital gains tax audits.

---

#### 3.5.3 Rights Issues

Used when an existing company offers additional shares to existing shareholders at a discounted price.

- **Inputs**:
  - **Action Type**: `Rights Issue`.
  - **Stock**: Select stock.
  - **Record Date**: Date of rights entitlement.
  - **Subscribed Quantity**: Number of rights shares accepted.
  - **Subscription Price**: Offer price per share (e.g. ₹ 1,250.00).
- **How It Works**:
  - Creates a new BUY lot with the subscribed quantity, subscription price, and allotment date.
  - Enters the FIFO queue at the discounted purchase cost basis.

---

#### 3.5.4 Mergers

Used when a company merges into another listed company (e.g. HDFC Limited merged into HDFC Bank Limited in July 2023).

- **Example**: 25 shares of HDFC Ltd swapped for 42 shares of HDFC Bank.
- **Inputs**:
  - **Action Type**: `Merger`.
  - **Source Stock**: `HDFC` (HDFC Limited).
  - **Destination Merged Stock**: `HDFCBANK`.
  - **Ratio**: `25 Old` : `42 New`.
- **How It Works**:
  1. The engine calculates the shares of the target company to be issued.
  2. The parent stock lots are closed out (deactivated).
  3. Corresponding lots are created under the destination merged stock.
  4. **Cost & Holding Period Grandfathering**: Under Section 47(vii) and Section 49(2) of the Indian Income Tax Act, the cost of acquisition of the parent shares is transferred to the merged shares, and the holding period includes the period for which the parent shares were held.

---

#### 3.5.5 Demergers

Used when a company spins off an operating division into a separately listed entity (e.g. Reliance Industries spinning off Jio Financial Services Limited in July 2023).

- **Example**: Holding 100 shares of Reliance Industries. 1 share of JFSL received per 1 share of RIL. Official cost of acquisition split: 91.1% retained in RIL, 8.9% allocated to JFSL.
- **Inputs**:
  - **Action Type**: `Demerger`.
  - **Parent Stock**: `RELIANCE`.
  - **Spun-off Resulting Stock**: `JIOFIN`.
  - **Record Date**: Demerger record date.
  - **Cost Apportionment %**: `8.9%`.
- **How It Works**:
  1. Eligible shares in the parent company on the record date are determined.
  2. For every active parent lot, the cost per share is reduced by the apportionment factor:
     $$\text{New Parent Cost} = \text{Original Cost} \times (1 - 0.089) = \text{Original Cost} \times 91.1\%$$
  3. Corresponding new lots are created under the spun-off stock (`JIOFIN`) with:
     $$\text{Demerged Share Cost} = \text{Original Parent Cost} \times 8.9\%$$
  4. Acquisition date of the demerged shares is grandfathered to the original acquisition date of the parent lot (Section 49(2C) of the IT Act).

---

## 4. Module 2: InvITs & REITs

Tracks Infrastructure Investment Trusts (InvITs) like PowerGrid InvIT, IRB InvIT, and Real Estate Investment Trusts (REITs) like Embassy Office Parks REIT, Mindspace Business Parks REIT, and Brookfield India Real Estate Trust.

### 4.1 How to Add a New Trust
1. Click **InvITs / REITs** in the sidebar &rarr; **Add New Trust**.
2. Enter:
   - **Symbol**: `EMBASSY`
   - **Name**: `Embassy Office Parks REIT`
   - **Type**: Select `REIT` or `InvIT`.
   - **ISIN**: `INE041025011`
   - **CMP**: `₹ 385.00`
3. Click **Save Trust**.

### 4.2 How to Record Buy & Sell Transactions
- Follows the identical strict FIFO queue architecture as equities.
- Holding period threshold for Long-Term Capital Gains on listed REIT/InvIT units is **36 months (3 years)** for acquisitions prior to Finance Act 2024, or **12 months** for listed units under recent amendments.

### 4.3 How to Record 4-Component Quarterly Distributions
REIT/InvIT managers (Embassy, Mindspace, etc.) send quarterly distribution notices detailing 4 distinct components:
1. Click **+ Dist** on the trust row.
2. Enter the distribution breakdown:
   - **Distribution Date**: Payout credit date.
   - **Units Held**: Total units held on record date.
   - **1. Dividend Component (per unit)**: Check whether exempt or taxable based on whether the SPV opted for Section 115BAA concessional tax.
   - **2. Interest Component (per unit)**: Taxable in the hands of the unit holder at their applicable slab rate.
   - **3. Rental Income Component (per unit)**: Taxable at slab rate.
   - **4. Return of Capital / Repayment of Debt (per unit)**:
     - *How It Works*: Return of Capital is **not** taxed as immediate income. Instead, the application systematically deducts this amount from the unit cost basis of your active purchase lots.
     - If the cumulative return of capital exceeds your original purchase price, the excess is taxed under Section 56(2)(xii).
3. Click **Save Distribution**. The transaction is logged in the **Distribution Ledger** and updates your annual income report.

---

## 5. Module 3: Exchange Traded Funds (ETFs)

### 5.1 How to Add a Categorized ETF
1. Click **ETFs** &rarr; **Add New ETF**.
2. Complete the form:
   - **Symbol**: e.g., `NIFTYBEES`
   - **ETF Name**: `Nippon India ETF Nifty 50 BeES`
   - **Category**: Select from:
     - `Index`: Nifty 50, Nifty Next 50, Bank Nifty
     - `Gold`: Sovereign gold tracking funds
     - `Silver`: Physical silver tracking funds
     - `Liquid`: Overnight and liquid debt funds
     - `International`: Nasdaq 100, S&P 500
   - **AMC**: `Nippon India Mutual Fund`
   - **CMP**: Current trading price in ₹.
3. Click **Save ETF**.

### 5.2 How to Record Buy & Sell Orders
- Standard lot-by-lot FIFO matching.
- Captures brokerage and STT.

### 5.3 How to Record ETF Splits
Certain ETFs execute unit splits (e.g. Nippon India ETF Nifty 50 BeES executed a **1:10 split** where ₹1,700 units were split into ₹170 units).
1. Click **Record ETF Split** at the top of the ETFs module.
2. Select the ETF, enter the Record Date, and specify the split ratio (`1 Old` : `10 New`).
3. *How It Works*: All open purchase lots as of the record date have their unit count multiplied by 10 and their cost basis per unit divided by 10. Overall invested capital remains unchanged.

### 5.4 ETF Taxation Rules (Equity vs Sec 50AA)
- **Equity ETFs** (e.g. `NIFTYBEES`, `JUNIORBEES`, `BANKBEES` with $\ge 65\%$ Indian equity exposure):
  - Holding $> 365\text{ days}$: LTCG @ 12.5% (with ₹1.25L annual exemption).
  - Holding $\le 365\text{ days}$: STCG @ 20%.
- **Debt / Gold / Silver ETFs** (`GOLDBEES`, `SILVERBEES`, `LIQUIDBEES` acquired on or after April 1, 2023):
  - Governed by **Section 50AA** of the Income Tax Act.
  - Deemed Short-Term Capital Gains regardless of holding period and taxed at your marginal slab rate.

---

## 6. Module 4: Bonds & Fixed Income

Tracks Sovereign Gold Bonds (SGB), Government of India Dated Securities (G-Secs), State Development Loans (SDLs), and Corporate Non-Convertible Debentures (NCDs).

### 6.1 How to Add a Bond or SGB
1. Click **Bonds** in the sidebar &rarr; **Add New Bond**.
2. Select **Bond Category**:
   - `Sovereign Gold Bond (SGB)`
   - `Government Security (G-Sec)`
   - `State Development Loan (SDL)`
   - `Corporate NCD`
3. Enter Bond Details:
   - **Symbol / Tranche**: e.g., `SGBMAY29` (SGB 2021-22 Series I).
   - **ISIN**: e.g., `IN0020210058`.
   - **Face Value**: ₹ 1,000.00 for G-Secs/NCDs or 1 Gram issue price for SGBs.
   - **Coupon Rate (%)**: e.g. `2.50%` for SGBs, `7.18%` for 10-year benchmark G-Sec.
   - **Coupon Frequency**: `Semi-Annual` (standard for GoI bonds & SGBs), `Annual`, `Quarterly`, or `Monthly`.
   - **Maturity Date**: Official redemption date.

### 6.2 How to Record Buy Transactions (Clean Price vs Accrued Interest)
When purchasing bonds in the secondary market (e.g. on the NSE/BSE debt segment or via RBI Retail Direct):
- Enter the **Clean Price** (price of the bond excluding interest).
- Enter **Accrued Interest Paid** to the seller for the days elapsed since the last coupon date.
- The engine separates accrued interest from capital cost basis so you do not pay double tax on coupon receipt.

### 6.3 How to Record Coupon / Interest Payouts & TDS
1. Click **+ Interest** on the bond row.
2. Enter:
   - **Payout Date**: Date coupon was credited to your bank account.
   - **Gross Interest**: Automatically computed as:
     $$\text{Gross Coupon} = \text{Held Quantity} \times \text{Face Value} \times \frac{\text{Coupon Rate}}{100 \times \text{Frequency}}$$
   - **TDS Deducted**: Enter TDS deducted under Section 193 (0% for G-Secs and SGBs; 10% for unlisted corporate bonds).
   - **Financial Year**: Assigned automatically.
3. Click **Save Payout**.

### 6.4 Sovereign Gold Bond (SGB) Sec 47(viic) Tax Exemption
- **Critical Tax Advantage**: Under **Section 47(viic)** of the Indian Income Tax Act, any capital gain arising to an individual on redemption of Sovereign Gold Bonds upon maturity (after 8 years) is **100% EXEMPT from tax**.
- When recording a maturity redemption:
  - The exit transaction is flagged as `EXEMPT_SGB_MATURITY`.
  - The realized gain is excluded from taxable capital gains in your tax report, while full cash proceeds are logged in your cash flow statement.

---

## 7. Module 5: Mutual Funds

Tracks Direct and Regular mutual fund folios across AMCs with AMFI scheme codes.

### 7.1 How to Add a Fund & Folio
1. Click **Mutual Funds** &rarr; **Add New Fund**.
2. Enter:
   - **Scheme Name**: e.g., `Parag Parikh Flexi Cap Fund - Direct Plan - Growth`.
   - **AMFI Scheme Code**: `122639`.
   - **Folio Number**: `10293847/91`.
   - **Category**: `Equity: Flexi Cap`.
   - **Current NAV**: Latest Net Asset Value (e.g. `₹ 82.45`).

### 7.2 How to Record SIP & Lumpsum Investments
1. Click **+ Trans** on the fund row &rarr; select **SIP Purchase** or **Lumpsum Purchase**.
2. Enter:
   - **Date**: Investment allotment date.
   - **Investment Amount**: Amount invested in ₹ (e.g. `₹ 10,000.00`).
   - **Allotted NAV**: Net Asset Value on the allotment date (e.g. `₹ 78.50`).
   - **Units Allotted**: Automatically calculated as $\text{Amount} / \text{NAV} = 127.3885\text{ units}$.
   - **Stamp Duty**: Captures the mandatory 0.005% stamp duty.
3. Click **Confirm Purchase Lot**.

### 7.3 How to Record Redemptions (FIFO Units Matching)
1. Click **+ Trans** &rarr; select **Redeem Units**.
2. Enter:
   - **Redemption Date**: Date units were processed by AMC.
   - **Units Redeemed**: Number of units to exit.
   - **Redemption NAV**: Exit NAV.
   - **Exit Load**: Any exit load deducted by the fund house.
3. **How It Works**:
   - Matches redeemed units against earliest SIP installments using FIFO.
   - For equity funds, units held $> 365\text{ days}$ generate **LTCG**; units held $\le 365\text{ days}$ generate **STCG**.
   - Generates precise audit records in `mutual_fund_capital_gains`.

---

## 8. Module 6: National Pension System (NPS Tier 1)

Tracks voluntary retirement contributions under Section 80CCD(1B) (additional ₹50,000 income tax deduction).

### 8.1 How to Set Up PRAN & Pension Fund Manager
1. Click **NPS (Tier 1)** &rarr; **Set Up Account**.
2. Enter:
   - **PRAN**: 12-digit Permanent Retirement Account Number (e.g. `110022334455`).
   - **Subscriber Name**: Registered account name.
   - **Pension Fund Manager (PFM)**: e.g. `HDFC Pension Management Company Limited` or `SBI Pension Funds`.
   - **Choice Type**: `Active Choice` or `Auto Choice`.

### 8.2 How to Record Voluntary Contributions (Scheme E/C/G/A Split)
When you contribute (e.g. ₹ 50,000 for annual tax deduction), your contribution is split across 4 asset classes:
1. Click **+ Contribution**.
2. Enter:
   - **Contribution Date**: Value date on your Protean / KFintech NPS receipt.
   - **Gross Contribution Amount**: Total contribution in ₹ (e.g. `₹ 50,000.00`).
   - **POP / Payment Charges**: Gateway fee or POP service charge (e.g. `₹ 25.00`).
   - **Net Amount Allocated**: $₹\text{ }49,975.00$.
3. Enter the 4-Scheme allocation breakdown:
   - **Scheme E** (Equities): e.g. 50% &rarr; ₹ 24,987.50 (Enter Scheme E NAV &rarr; units calculated).
   - **Scheme C** (Corporate Debt): e.g. 30% &rarr; ₹ 14,992.50 (Enter Scheme C NAV &rarr; units calculated).
   - **Scheme G** (Government Securities): e.g. 15% &rarr; ₹ 7,496.25 (Enter Scheme G NAV &rarr; units calculated).
   - **Scheme A** (Alternative Assets): e.g. 5% &rarr; ₹ 2,498.75 (Enter Scheme A NAV &rarr; units calculated).
4. Click **Save Contribution**.

### 8.3 How to Record Quarterly Fee & Unit Deductions
NRA / Central Recordkeeping Agency (CRA) and Custodian charges are deducted quarterly by cancelling units:
1. Click **Record Fee Deduction**.
2. Enter the date and the exact fraction of units cancelled in Scheme E, C, G, and A.
3. *How It Works*: Subtracts cancelled units directly from your scheme unit balance, ensuring your dashboard portfolio valuation matches your official quarterly eNPS CAS statement to the fourth decimal place.

---

## 9. Analytics, Reports & Capital Gains Tax Audit

Access the suite via **Reports** in the sidebar. Every report supports instant filtering by **Current FY**, **Last FY**, or **All Time**.

### 1. Cash Flow & Capital Activity Report (`/reports/cashflow`)
- Aggregates all gross capital inflows (purchases) and outflows (sales/redemptions) across all 6 asset modules.
- Displays statutory transaction friction (Brokerage, STT, Exchange charges, GST, Stamp Duty).
- Shows Net Capital Deployed in the financial year.

### 2. Capital Gains Tax Audit Report (`/reports/tax`)
- **ITR-2 / ITR-3 Filing Companion**:
  - Summarizes Short-Term Capital Gains (STCG @ 20% under Section 111A).
  - Summarizes Long-Term Capital Gains (LTCG @ 12.5% under Section 112A).
  - Flags tax-exempt transactions (SGB maturity under Section 47(viic)).
  - Provides lot-by-lot audit drill-down with buy dates, sell dates, holding days, buy costs, sale proceeds, and net gain.

### 3. Annual Income & Dividend Ledger (`/reports/income`)
- Complete schedule of:
  - Equity Dividends received.
  - Bond Coupon & Interest payments.
  - REIT & InvIT quarterly distributions (interest, dividend, rental).
- Displays gross income and TDS deducted under Section 194 / Section 193 for instant reconciliation with **Form 26AS** and **Annual Information Statement (AIS)**.

### 4. Expenses & Statutory Friction Report (`/reports/expenses`)
- Granular breakdown of total friction deducted during trading:
  - Brokerage
  - Securities Transaction Tax (STT)
  - Goods and Services Tax (GST)
  - Stamp Duty

### 5. Asset Allocation & Net Worth Matrix (`/reports/allocation`)
- Visual portfolio distribution percentages across:
  - Equities
  - InvITs & REITs
  - ETFs
  - Bonds & Fixed Income
  - Mutual Funds
  - NPS (Tier 1)
- Identifies portfolio concentration risks and sector weightings.

---

## 10. Production Deployment, Data Cleanup & Backup

### 10.1 Cleaning Sample Data for Fresh Production Use
If you explored the system with sample data and are ready to track your real investments:
1. Open a terminal in the project root:
   ```bash
   php cleanup_sample_data.php
   ```
2. What the script does:
   - Automatically saves a complete safety snapshot to `backup_sample_data.sql`.
   - Truncates all 22 transaction and asset holding tables.
   - Preserves your administrator login account and all 18 sector master records.
   - Resets all auto-increment IDs to 1.

### 10.2 Creating Manual Database Backups
To create an offline backup of your portfolio database at any time:
```bash
# Using mysqldump (Windows XAMPP path)
D:\xampp\mysql\bin\mysqldump.exe -u root -p investment_portfolio > my_portfolio_backup.sql
```

### 10.3 Restoring from Backup
```bash
D:\xampp\mysql\bin\mysql.exe -u root -p investment_portfolio < my_portfolio_backup.sql
```

### 10.4 First-Time Installation on a New Server (`install.php`)
1. Place the repository in your web server directory.
2. Navigate in your browser to:
   `http://your-server/Portfolio/install.php`
3. Complete the 3-step setup wizard (verifies system health, creates database, provisions all 25 tables, and sets your admin password).
4. **Important**: Click the prominent **"Delete install.php Now"** button on the final screen to permanently remove the setup wizard from your server for security.

---

## 11. Disclaimer & Limitation of Liability

> [!CAUTION]
> **LEGAL & FINANCIAL DISCLAIMER**:
> - **As-Is Provision**: This application is provided on an "AS IS" and "AS AVAILABLE" basis without any express or implied warranties.
> - **No Financial Advisory**: This software is built for personal record-keeping, tracking, and educational purposes. It does not constitute SEBI-registered investment advice or professional tax advice. Always verify capital gains figures, corporate actions, and income schedules with official broker contract notes, depository statements, and a qualified Chartered Accountant before filing tax returns.
> - **Limitation of Liability**: The authors and contributors shall not be liable for any data loss, computational inaccuracies, financial loss, or damages arising from the use of this software. Users are solely responsible for maintaining regular database backups.

---

## 12. Feedback & Issues

- 🐛 **Issues & Feature Requests**: If you discover calculation differences, encounter bugs, or have ideas for new features, please report them by opening an issue on **GitHub**.
- ⭐ **Support the Project**: If this tracker helps you manage your investments, consider giving the repository a star on GitHub!

---
*Investment Portfolio Tracker &bull; Indian Financial Market Edition &bull; Local Offline Wealth Management*
