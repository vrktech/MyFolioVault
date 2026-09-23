# MyFolioVault — Complete User & Operations Manual
**Indian Financial Market Edition &bull; 100% Local Offline Wealth Management**

---

## About MyFolioVault

**MyFolioVault** is a self-hosted, 100% offline personal wealth management and portfolio tracking application tailored specifically for the Indian financial market. Developed from real-world personal investment practices, it is designed to fulfill the end-to-end portfolio tracking, bookkeeping, and tax compliance needs of an average retail investor, salaried professional, and family office in India.

Many modern investors trade across multiple brokerages (Zerodha, Groww, AngelOne, ICICI Direct, Upstox, etc.) and hold diverse assets—**Direct Equities (NSE/BSE)**, **InvITs & REITs**, **Exchange Traded Funds (ETFs)**, **Bonds & Sovereign Gold Bonds (SGBs)**, **Mutual Funds (SIP & Lumpsum)**, and **NPS (Tier 1)**. Most commercial platforms either require sharing sensitive broker credentials or fail to accurately handle Indian statutory nuances like FIFO capital gains matching, 4-component REIT distributions, SGB tax exemptions, clean bond pricing, and daily consolidated broker contract notes.

**MyFolioVault** solves this by running completely privately on your local machine with zero external dependencies, no third-party CDNs, and zero tracking. It gives you full transparency and control over your cost basis, passive income dividends, statutory tax friction, and asset allocation across financial years.

---

## Table of Contents
1. [Installation & First-Time Setup (Step-by-Step)](#1-installation--first-time-setup-step-by-step)
   - [1.1 System Prerequisites](#11-system-prerequisites)
   - [1.2 Download & Extract Archive](#12-download--extract-archive)
   - [1.3 Web Setup Wizard (install.php) Walkthrough](#13-web-setup-wizard-installphp-walkthrough)
   - [1.4 The Demo Data Checkbox Explained](#14-the-demo-data-checkbox-explained)
   - [1.5 Manual Installation Alternative (CLI / phpMyAdmin)](#15-manual-installation-alternative-cli--phpmyadmin)
2. [Welcome Dashboard, Navigation & Live CMP Updates](#2-welcome-dashboard-navigation--live-cmp-updates)
   - [2.1 Welcome Dashboard KPI Metrics & Portfolio Net Worth](#21-welcome-dashboard-kpi-metrics--portfolio-net-worth)
   - [2.2 Financial Year (FY) Date Filtering](#22-financial-year-fy-date-filtering)
   - [2.3 Live CMP & NAV Updating Mechanics](#23-live-cmp--nav-updating-mechanics)
     - [2.3.1 Equities, ETFs & REITs Live Quotes](#231-equities-etfs--reits-live-quotes)
     - [2.3.2 Mutual Funds AMFI NAV Refresh](#232-mutual-funds-amfi-nav-refresh)
     - [2.3.3 Bonds & Fixed Income (Manual CMP Editing)](#233-bonds--fixed-income-manual-cmp-editing)
     - [2.3.4 NPS NAV Updating & Scheme Code Lookup (npsnav.in)](#234-nps-nav-updating--scheme-code-lookup-npsnavin)
3. [Module 1: Equities (Stocks & Shares)](#3-module-1-equities-stocks--shares)
   - [3.1 How to Add a New Stock](#31-how-to-add-a-new-stock)
   - [3.2 How to Record a BUY Transaction](#32-how-to-record-a-buy-transaction)
   - [3.3 How to Record a SELL Transaction & FIFO Capital Gains](#33-how-to-record-a-sell-transaction--fifo-capital-gains)
   - [3.4 How to Record Dividend Payouts & TDS](#34-how-to-record-dividend-payouts--tds)
   - [3.5 How to Record Corporate Actions (Splits, Bonus, Rights, Mergers, Demergers)](#35-how-to-record-corporate-actions)
   - [3.6 Active Holdings vs. Past Holdings](#36-active-holdings-vs-past-holdings)
   - [3.7 How to Edit a Stock](#37-how-to-edit-a-stock)
   - [3.8 How to Delete a Stock (and What Happens to Related Transactions)](#38-how-to-delete-a-stock-and-what-happens-to-related-transactions)
4. [Module 2: InvITs & REITs](#4-module-2-invits--reits)
   - [4.1 How to Add a New Trust](#41-how-to-add-a-new-trust)
   - [4.2 How to Record Buy & Sell Transactions (Whole Units)](#42-how-to-record-buy--sell-transactions-whole-units)
   - [4.3 How to Record 4-Component Quarterly Distributions](#43-how-to-record-4-component-quarterly-distributions)
5. [Module 3: Exchange Traded Funds (ETFs)](#5-module-3-exchange-traded-funds-etfs)
   - [5.1 How to Add a Categorized ETF](#51-how-to-add-a-categorized-etf)
   - [5.2 How to Record Buy & Sell Orders](#52-how-to-record-buy--sell-orders)
   - [5.3 How to Record ETF Splits](#53-how-to-record-etf-splits)
   - [5.4 ETF Taxation Rules (Equity vs Sec 50AA)](#54-etf-taxation-rules-equity-vs-sec-50aa)
6. [Module 4: Bonds & Fixed Income](#6-module-4-bonds--fixed-income)
   - [6.1 How to Add a Bond or SGB](#61-how-to-add-a-bond-or-sgb)
   - [6.2 Clean Price vs. Accrued Interest in Buy Orders](#62-clean-price-vs-accrued-interest-in-buy-orders)
   - [6.3 How to Record Coupon / Interest Payouts & TDS](#63-how-to-record-coupon--interest-payouts--tds)
   - [6.4 Sovereign Gold Bond (SGB) Sec 47(viic) Tax Exemption](#64-sovereign-gold-bond-sgb-sec-47viic-tax-exemption)
   - [6.5 How to Record Redemption After Expiry / Maturity Date](#65-how-to-record-redemption-after-expiry--maturity-date)
7. [Module 5: Mutual Funds](#7-module-5-mutual-funds)
   - [7.1 How to Add a Fund & Folio](#71-how-to-add-a-fund--folio)
   - [7.2 How to Record SIP & Lumpsum Investments](#72-how-to-record-sip--lumpsum-investments)
   - [7.3 How to Record Redemptions (FIFO Units Matching)](#73-how-to-record-redemptions-fifo-units-matching)
8. [Module 6: National Pension System (NPS Tier 1)](#8-module-6-national-pension-system-nps-tier-1)
   - [8.1 First-Time Setup: PRAN, Pension Fund Manager & Scheme Details (Step-by-Step)](#81-first-time-setup-pran-pension-fund-manager--scheme-details-step-by-step)
   - [8.2 How to Edit Scheme Details, PFM & Target Allocation Later](#82-how-to-edit-scheme-details-pfm--target-allocation-later)
   - [8.3 How to Record Contributions (Scheme E/C/G/A Split)](#83-how-to-record-contributions-scheme-ecga-split)
   - [8.4 How to Record Quarterly Fee & Unit Deductions](#84-how-to-record-quarterly-fee--unit-deductions)
9. [Module 7: Consolidated Brokerage & Expenses Ledger (NEW)](#9-module-7-consolidated-brokerage--expenses-ledger)
   - [9.1 Why This Module Exists (Consolidated Contract Notes)](#91-why-this-module-exists-consolidated-contract-notes)
   - [9.2 How to Record an Expense Transaction](#92-how-to-record-an-expense-transaction)
   - [9.3 What to Enter in the Fields (Brokerage vs STT vs Platform Charges)](#93-what-to-enter-in-the-fields)
   - [9.4 How Expenses Add Up in Cash Flow & Friction Reports](#94-how-expenses-add-up-in-cash-flow--friction-reports)
   - [9.5 How to Edit & Delete Expense Records](#95-how-to-edit--delete-expense-records)
10. [Analytics, Reports & Capital Gains Tax Audit](#10-analytics-reports--capital-gains-tax-audit)
    - [10.1 Cash Flow & Capital Activity Report](#101-cash-flow--capital-activity-report)
    - [10.2 Capital Gains & Tax Audit Report (ITR Filing)](#102-capital-gains--tax-audit-report-itr-filing)
    - [10.3 Passive Income & Distribution Report](#103-passive-income--distribution-report)
    - [10.4 Expenses & Statutory Friction Report](#104-expenses--statutory-friction-report)
    - [10.5 Asset Allocation & Valuation Snapshot](#105-asset-allocation--valuation-snapshot)
    - [10.6 CSV Exporting & Spreadsheet Compatibility](#106-csv-exporting--spreadsheet-compatibility)
11. [Production Deployment, Data Cleanup & Backup](#11-production-deployment-data-cleanup--backup)
12. [Credits & Technology Stack Acknowledgements](#12-credits--technology-stack-acknowledgements)
13. [Disclaimer & Limitation of Liability](#13-disclaimer--limitation-of-liability)

---

## 1. Installation & First-Time Setup (Step-by-Step)

### 1.1 System Prerequisites
Before running MyFolioVault, ensure your local web environment satisfies the following requirements:
- **PHP Version**: PHP 8.1 or higher (PHP 8.2 or 8.3 recommended).
- **Database Server**: MySQL 8.0+ or MariaDB 10.4+.
- **Web Server**: Apache (via XAMPP, WAMP, Laragon, or standalone) with `mod_rewrite` enabled.
- **Required PHP Extensions**: `mysqli`, `pdo_mysql`, `intl`, `mbstring`, `curl`, `openssl`, `json`.
- **Local URL**: For example, `http://localhost/[your_installation_folder]/public/` or virtual host `http://myfoliovault.local/`.

---

### 1.2 Download & Extract Archive
1. **Download Standalone Package**: Download the latest standalone `.zip` version from the **[GitHub Releases section](https://github.com/vrktech/MyFolioVault/releases)**. It is completely ready to install and includes all necessary pre-bundled components, including the CodeIgniter 4 framework engine and Bootstrap offline assets.
2. Extract the archive into your web server's document root directory:
   - **XAMPP (Windows)**: `C:\xampp\htdocs\[your_installation_folder]`
   - **WAMP (Windows)**: `C:\wamp64\www\[your_installation_folder]`
   - **Laragon (Windows)**: `C:\laragon\www\[your_installation_folder]`
   - **Linux / Apache**: `/var/www/html/[your_installation_folder]`
3. *(Note: Replace `[your_installation_folder]` with your chosen folder name, for example `Portfolio` or `MyFolioVault`).*

---

### 1.3 Web Setup Wizard (`install.php`) Walkthrough
MyFolioVault includes an automated browser-based setup wizard at `public/install.php`.

```
┌─────────────────────────────────────────────────────────────────┐
│                   INSTALLER SETUP FLOW                          │
│                                                                 │
│   [ Step 1: Requirements ] ──► [ Step 2: Database Config ]      │
│                                           │                     │
│   [ Step 4: Admin Account ] ◄── [ Step 3: Schema & Demo Check ] │
│             │                                                   │
│   [ Step 5: .env Config ]  ──► [ Step 6: Self-Deletion ]        │
└─────────────────────────────────────────────────────────────────┘
```

1. **Launch Wizard**: Open your browser and navigate to:
   `http://localhost/[your_installation_folder]/public/install.php`
   *(Change `[your_installation_folder]` to the actual folder name where you extracted MyFolioVault, for example `Portfolio`).*
2. **Step 1: System Checks**:
   - The installer verifies PHP version, required PHP extensions, and write permissions for `.env` and `writable/`.
   - Click **Next: Database Configuration**.
3. **Step 2: Database Setup**:
   - Enter your MySQL server details:
     - **Database Host**: `localhost` (or `127.0.0.1`)
     - **Port**: `3306`
     - **Database Name**: `investment_portfolio` (the wizard can automatically create it if it doesn't exist)
     - **Username**: `root` (or your database user)
     - **Password**: *(leave blank if default XAMPP, or enter your MySQL root password)*
   - Click **Test & Initialize Database**.
4. **Step 3: Database Initialization & Demo Data**:
   - The wizard executes `schema.sql` to create all 20+ tables.
   - **Install Demo / Sample Data Checkbox**:
     - *Default*: **Unchecked (No)**.
     - *When to Check*: If you are evaluating the software and want realistic sample stocks (Reliance, TCS, HDFC Bank), REITs, ETFs, Sovereign Gold Bonds, Mutual Funds, NPS, and contract note charges pre-loaded.
     - *When to Leave Unchecked*: If you are setting up your own clean personal portfolio for live production use.
5. **Step 4: Create Administrator Account**:
   - Enter your **Full Name** (e.g. `Karthik Investor`).
   - Enter your **Email Address** (used as your login username, e.g. `karthik@myfamily.in`).
   - Enter a **Strong Password** (minimum 6 characters).
   - Select your **Financial Year Start Month** (Default: **April (Month 4)** for India).
6. **Step 5: Write Configuration (`.env`)**:
   - The installer automatically sets up encryption keys, database credentials, session handler, and app base URL.
7. **Step 6: Security Auto-Deletion**:
   - Click **Delete Installer & Proceed to Login**.
   - The installer permanently removes `public/install.php` to prevent unauthorized re-installation.

---

### 1.4 The Demo Data Checkbox Explained
During Step 3 of the installer, you are presented with a checkbox:
- **`[ ] Install Realistic Sample / Demo Portfolio Data`**
- If you check this box, MyFolioVault imports `sample_data.sql`. You will have an active multi-asset portfolio with historical purchases, sales, dividends, bonus shares, corporate actions, and contract note charges.
- If you leave this box unchecked, the database will be created completely empty, pre-seeded only with standard Indian equity sectors (IT, Banking, Pharma, FMCG, Auto, Energy, etc.).

---

### 1.5 Manual Installation Alternative (CLI / phpMyAdmin)
If you prefer manual setup or are deploying on a headless Linux server:
1. Create a MySQL database:
   ```sql
   CREATE DATABASE `investment_portfolio` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
   ```
2. Import the database schema:
   ```bash
   mysql -u root -p investment_portfolio < schema.sql
   ```
   *(Optional: If you want demo data, also run: `mysql -u root -p investment_portfolio < sample_data.sql`)*
3. Copy the environment configuration:
   ```bash
   cp env .env
   ```
4. Open `.env` and configure:
   - `app.baseURL = 'http://localhost/[your_installation_folder]/public/'`
   - `database.default.hostname = 'localhost'`
   - `database.default.database = 'investment_portfolio'`
   - `database.default.username = 'root'`
   - `database.default.password = ''`
5. Generate an encryption key or keep the pre-generated key in `.env`.
6. Log in at `/login` with credentials `admin@portfolio.local` / `password123` (if demo data was imported) or create a user in the `users` table with password hash generated via `password_hash('your_password', PASSWORD_DEFAULT)`.

---

## 2. Welcome Dashboard, Navigation & Live CMP Updates

### 2.1 Welcome Dashboard KPI Metrics & Portfolio Net Worth
When you sign in, the Welcome Dashboard provides an instantaneous snapshot of your total wealth:
- **Consolidated Portfolio Net Worth**: Total current valuation of all active assets in INR (₹).
- **Total Invested Capital**: Net cost basis of your active holdings.
- **Total Unrealized P&L**: Profit or loss on paper across active holdings in absolute rupees and return percentage.
- **Active Asset Positions**: Count of open holdings currently held.
- **Consolidated Brokerage & Statutory Charges Card**: Summarizes total recorded broker commissions, STT, and platform fees from your daily contract notes, with quick links to view or record an expense.
- **Asset Class Breakdown Cards**: Quick cards for Equities, InvITs/REITs, ETFs, Bonds, Mutual Funds, and NPS with invested capital, valuation, and unrealized return.

---

### 2.2 Financial Year (FY) Date Filtering
MyFolioVault is tailored for the Indian financial cycle (April 1 to March 31):
- **Current FY**: For example, FY 2026-27 (`2026-04-01` to `2027-03-31`). Default for all activity ledgers and reports.
- **Last FY**: Previous financial year (e.g. FY 2025-26). Essential when filing annual Income Tax Returns (ITR-2 or ITR-3).
- **All Time**: Unfiltered full historical view from your very first trade.

---

### 2.3 Live CMP & NAV Updating Mechanics

#### 2.3.1 Equities, ETFs & REITs Live Quotes
- In **Equities** (`/equities`), **ETFs** (`/etfs`), and **InvITs/REITs** (`/reits-invits`), click the **"Refresh Live Prices"** button at the top right of the dashboard.
- The system fetches current market quotes using Yahoo Finance / NSE feeds and saves the latest timestamp.
- You can also manually update any stock price by clicking the pencil/edit icon on the stock card.

#### 2.3.2 Mutual Funds AMFI NAV Refresh
- In **Mutual Funds** (`/mutual-funds`), click **"Refresh Latest NAVs"**.
- MyFolioVault queries the daily official NAV feed from the **Association of Mutual Funds in India (AMFI)** using the fund's 6-digit AMFI Scheme Code.

#### 2.3.3 Bonds & Fixed Income (Manual CMP Editing)
> [!IMPORTANT]
> **Secondary Market Bonds & SGBs Do Not Have Live Streaming Feeds**:
> Unlike equities, Indian Government Securities (G-Secs), Corporate NCDs, and Sovereign Gold Bonds (SGBs) traded on NSE/BSE debt segments do not offer public automated real-time price feeds.
> - **How to Update Bond Prices**: On the **Bonds Dashboard** (`/bonds`), locate the bond in the Active Holdings table and click the **Edit Price / CMP** icon (or use the bond action menu).
> - Enter the current clean market price per unit (e.g. ₹ 985.50 for a ₹ 1,000 face value NCD, or ₹ 7,450 for 1 gram of SGB). The dashboard recalculates your current valuation and unrealized returns immediately.

#### 2.3.4 NPS NAV Updating & Scheme Code Lookup (`npsnav.in`)
The NPS module fetches live Net Asset Values for all 4 PFRDA asset classes (Scheme E, C, G, A) directly via [npsnav.in](https://npsnav.in).

**How to Find Your NPS Scheme Code on `npsnav.in`**:
1. Open your browser and go to [npsnav.in](https://npsnav.in).
2. In the search box, enter your **Pension Fund Manager** (e.g. `ICICI Prudential`, `HDFC Pension`, `SBI Pension Funds`, `UTI Retirement Solutions`, `Kotak Pension Fund`) and the scheme name (e.g. `Scheme E Tier I`).
3. Click on the scheme from the search results to open its dedicated page.
4. Note the **8-character Scheme Code** shown at the end of the URL or on the page title:
   - For example: **[ICICI PRUDENTIAL SCHEME E - TIER I](https://npsnav.in/funds/SM007001)** &rarr; Scheme code is **`SM007001`**.
   - HDFC Pension Management Scheme E (Tier I) &rarr; **`SM008001`**.
   - SBI Pension Funds Scheme E (Tier I) &rarr; **`SM001001`**.
5. In MyFolioVault, navigate to **NPS (Tier 1)** &rarr; **Account Settings / Edit Account** and enter this scheme code for Scheme E, Scheme C, Scheme G, and Scheme A.
6. Whenever you click **"Update Scheme NAVs"**, MyFolioVault pulls the latest closing NAVs automatically from `npsnav.in`!

---

## 3. Module 1: Equities (Stocks & Shares)

### 3.1 How to Add a New Stock
1. Navigate to **Equities** &rarr; Click **"+ Add New Stock"**.
2. **Field Explanations**:
   - **Company Name**: Official registered company name (e.g. `Reliance Industries Ltd`).
   - **Stock Symbol**: Trading ticker symbol (e.g. `RELIANCE`). Renders as a colorful badge throughout the application.
   - **Exchange**: Select `NSE` (National Stock Exchange) or `BSE` (Bombay Stock Exchange).
   - **ISIN**: 12-character International Securities Identification Number (e.g. `INE002A01018`). Optional, but useful for demat reconciliation.
   - **Sector**: Select the industry sector (e.g. `Energy, Oil & Gas`, `Information Technology`). Drives the equity sector breakdown in the allocation report.
   - **Current Market Price (CMP)**: Initial price per share in INR.

---

### 3.2 How to Record a BUY Transaction
1. In the Equities Dashboard, find the stock &rarr; Click **"Buy"** (or use the transaction modal).
2. **Field Explanations**:
   - **Transaction Date**: Trade execution date from your broker contract note.
   - **Quantity**: Whole number of shares purchased (e.g. `50`).
   - **Price Per Share (₹)**: Execution price per share (e.g. `2450.00`).
   - **Brokerage (₹)**: Direct broker commission if itemized on the trade note. *(Leave 0 if you record consolidated daily contract note expenses in the Expenses module!)*
   - **STT & Other Taxes (₹)**: Securities Transaction Tax and turnover fees if itemized.
   - **Total Amount (₹)**: Auto-calculated as `(Quantity * Price) + Brokerage + STT`.
   - **Notes**: Trade rationale or broker order ID.
3. Submitting the form creates a new FIFO lot with `remaining_quantity = quantity`.

---

### 3.3 How to Record a SELL Transaction & FIFO Capital Gains
1. Locate the stock in the Active Holdings table &rarr; Click **"Sell"**.
2. **Field Explanations**:
   - **Transaction Date**: Sale date.
   - **Quantity**: Number of shares sold (cannot exceed total available active units).
   - **Selling Price Per Share (₹)**: Execution sale price.
   - **Brokerage & STT (₹)**: Any itemized sale charges.
3. **Automated FIFO Lot Matching & Tax Classification**:
   - The engine automatically exhausts buy lots from oldest to newest in strict FIFO order (Indian Income Tax Act Sec 45 & 48).
   - Computes **Holding Days**: `Sell Date - Buy Date`.
   - **STCG vs LTCG**:
     - Holding period &le; 365 days &rarr; **Short-Term Capital Gain (STCG)** (Taxed @ 20% under Section 111A).
     - Holding period > 365 days &rarr; **Long-Term Capital Gain (LTCG)** (Taxed @ 12.5% above ₹1.25 Lakh exemption under Section 112A).

---

### 3.4 How to Record Dividend Payouts & TDS
1. In Equities Dashboard &rarr; Click **"Record Dividend"**.
2. Enter the **Dividend Date** and select the stock.
3. Enter **Dividend Per Share (DPS)** or **Total Gross Amount** (the calculator auto-computes the other based on shares held on the record date).
4. Enter **TDS Deducted (₹)** (Tax Deducted at Source under Section 194, typically 10% if dividend exceeds ₹5,000).
5. Net Received in bank = `Gross Amount - TDS Deducted`.

---

### 3.5 How to Record Corporate Actions
1. **Stock Splits**: Specify split ratio (e.g. 1:2 or 1:10). Increases share quantity proportionally and scales down average price, keeping cost basis constant.
2. **Bonus Issues**: Specify bonus ratio (e.g. 1:1). Adds new bonus shares at ₹0 cost basis with their own acquisition date for future FIFO capital gains.
3. **Rights Issues**: Add subscribed shares with application price.
4. **Mergers & Demergers**: Apportion cost basis across resultant entities according to the court-approved ratio.

---

### 3.6 Active Holdings vs. Past Holdings
- **Active Holdings Tab**: Stocks where you currently own at least 1 share. Displays CMP, WAP, invested value, current value, and stacked unrealized P&L.
- **Past Holdings Tab**: Stocks where you have sold 100% of your position. Shows historical cost, realized exit value, and total realized capital gain.

---

### 3.7 How to Edit a Stock
1. On the **Equities Dashboard** (`/equities`), locate the stock in the Active Holdings table.
2. In the **Action** column on that stock's row, click the dropdown toggle and select **"Edit Stock Details"** (or click the edit pencil icon).
3. In the **Edit Stock Details** modal, you can modify:
   - **Company Name**: Full legal or recognizable name (e.g. `HDFC Bank Limited`).
   - **Symbol**: Stock ticker in uppercase (e.g. `HDFCBANK`).
   - **ISIN**: 12-character alphanumeric international security code (e.g. `INE040A01034`).
   - **Sector**: Industry classification (e.g. `Banking & Financial Services`, `Information Technology`).
   - **Exchange**: Primary market `NSE` or `BSE`.
4. Click **"Save Changes"** to update the metadata immediately.
5. *(Note: To update the Current Market Price (CMP), use the inline CMP edit link on the dashboard or click **"Refresh Live Prices"** to automatically pull real-time market prices from Yahoo Finance).*

---

### 3.8 How to Delete a Stock (and What Happens to Related Transactions)
1. On the **Equities Dashboard**, click the **Action** dropdown next to the stock you wish to delete and select **"Delete Stock"** (red trash icon).
2. A confirmation prompt will appear: *"Are you sure you want to remove this stock from your portfolio?"*. Confirm to proceed.

> [!CAUTION]
> **What Happens to Related Transactions When a Stock is Deleted:**
> MyFolioVault enforces strict relational integrity with cascading deletions (`ON DELETE CASCADE`):
> 1. **All Transactions Erased**: Every single BUY and SELL trade executed for this stock in `equity_transactions` is permanently deleted.
> 2. **Capital Gains History Purged**: All historical realized capital gains (STCG/LTCG) and matched FIFO tax records in `equity_capital_gains` are permanently erased.
> 3. **Dividend Records Removed**: All cash dividends, TDS withheld records, and dividend history in `equity_dividends` are deleted.
> 4. **Corporate Actions Erased**: All splits, bonuses, rights, and mergers tied to this stock are removed.
>
> **Best Practice Recommendation:**
> - If you no longer own shares because you sold your entire holding, **DO NOT delete the stock**! Instead, record a **SELL** transaction. This reduces your active units to 0 and automatically moves the stock to the **Past Holdings** tab. This preserves your historical realized profit/loss, historical cash flows, and tax audit trail for ITR filing.
> - Only use **Delete Stock** if you entered a stock by mistake or want to purge all historical records of that security entirely.

---

## 4. Module 2: InvITs & REITs

### 4.1 How to Add a New Trust
1. Navigate to **InvITs & REITs** &rarr; Click **"+ Add New Trust"**.
2. Select **Trust Type**:
   - `REIT`: Real Estate Investment Trust (e.g. Embassy Office Parks REIT, Mindspace Business Parks REIT, Brookfield India Real Estate Trust, Nexus Select Trust).
   - `INVIT`: Infrastructure Investment Trust (e.g. PowerGrid InvIT, IRB InvIT Fund).
3. Enter Trust Name, Trading Symbol, Exchange, and Sponsor.

---

### 4.2 How to Record Buy & Sell Transactions (Whole Units)
- InvITs and REITs trade in minimum lot sizes of 1 unit. Units must be whole numbers without decimals.
- Buy orders establish cost basis; Sell orders trigger FIFO lot matching and capital gains calculation (Holding period > 365 days for LTCG).

---

### 4.3 How to Record 4-Component Quarterly Distributions
Indian REITs and InvITs distribute cash quarterly with 4 distinct tax components:
1. In the InvITs/REITs Dashboard &rarr; Click **"Record Distribution"**.
2. Enter the components from your broker's distribution statement or AMC notice:
   - **Interest Component (₹)**: Taxable in the hands of unitholders at your applicable income tax slab rate.
   - **Dividend Component (₹)**: Exempt under Sec 10(23FD) if the underlying SPV did not opt for the concessional 22% tax regime under Sec 115BAA; otherwise taxable at slab.
   - **Return of Capital / Amortization of SPV Debt (ROC) (₹)**: Reduces your unit acquisition cost basis. Any cumulative ROC exceeding original cost is taxed under Section 56(2)(xii).
   - **Other Income (₹)**: Taxable at slab.
   - **TDS Deducted (₹)**: Tax withheld by the trust.
3. Net Received = `(Interest + Dividend + ROC + Other) - TDS`.

---

## 5. Module 3: Exchange Traded Funds (ETFs)

### 5.1 How to Add a Categorized ETF
1. Navigate to **ETFs** &rarr; Click **"+ Add New ETF"**.
2. Enter Name (e.g. `Nippon India Nifty 50 BeES ETF`), Symbol (`NIFTYBEES`), and Exchange.
3. Select **Category**:
   - `Equity Index / Large Cap`: Tracks Nifty 50, Sensex, Nifty Next 50.
   - `Equity Sectoral / Thematic`: Bank BeES, IT ETF, Pharma ETF.
   - `Commodity Gold`: Gold BeES, HDFC Gold ETF.
   - `Commodity Silver`: Silver BeES, ICICI Silver ETF.
   - `Debt / Liquid`: Liquid BeES, G-Sec ETFs.
   - `International`: Nasdaq 100 ETF, S&P 500 ETF.

---

### 5.2 How to Record Buy & Sell Orders
- Enter Transaction Date, Quantity, Price, Brokerage, and STT.
- The dashboard calculates your weighted average cost and unrealized returns.

---

### 5.3 How to Record ETF Splits
- When high-priced ETFs (e.g. Gold BeES or Junior BeES) execute face value splits, use **"Record Split"** to scale up units without altering total cost.

---

### 5.4 ETF Taxation Rules (Equity vs Sec 50AA)
- **Equity ETFs** (holding &ge; 65% domestic equity): STCG @ 20% (&le; 365 days), LTCG @ 12.5% (> 365 days with ₹1.25 Lakh exemption).
- **Gold, Silver & Debt ETFs** (holding < 65% equity acquired on or after April 1, 2023): Governed by **Section 50AA**, taxed as Short-Term Capital Gains at investor's slab rate regardless of holding period.

---

## 6. Module 4: Bonds & Fixed Income

### 6.1 How to Add a Bond or SGB
1. Navigate to **Bonds** &rarr; Click **"+ Add New Bond"**.
2. Select **Category**:
   - `CORPORATE_NCD`: Corporate Non-Convertible Debentures (e.g. Tata Capital, L&T Finance). Default selection.
   - `GOVT_SECURITY`: Central Government Bonds (G-Secs) or State Development Loans (SDLs).
   - `SGB`: Sovereign Gold Bonds issued by the Reserve Bank of India.
   - `TAX_FREE`: Tax-Free PSU Bonds (e.g. NHAI, REC, PFC, IREDA).
3. Enter Bond Name, Symbol, ISIN, Face Value (e.g. ₹ 1,000), Coupon Rate %, and Coupon Frequency (Annual, Semi-Annual, Monthly, Cumulative).

---

### 6.2 Clean Price vs. Accrued Interest in Buy Orders
When purchasing bonds in the secondary market between coupon payout dates, understand the two price components:
- **Clean Price (₹)**: The market quote of the bond itself, excluding accumulated interest.
- **Accrued Interest (₹)**: Interest that has accrued on the bond from the last coupon payment date up to the trade settlement date. You pay this upfront to the seller, and you will recover it when the issuer pays the next full coupon.
- **Total Purchase Outlay**:
  $$\text{Total Amount} = (\text{Quantity} \times \text{Clean Price}) + \text{Accrued Interest} + \text{Brokerage}$$
- *MyFolioVault separates clean price from accrued interest so your capital gains cost basis is never artificially inflated by interest income!*

---

### 6.3 How to Record Coupon / Interest Payouts & TDS
1. In Bonds Dashboard &rarr; Click **"Record Coupon / Interest"**.
2. Select the bond and enter the payout date.
3. Enter **Gross Interest Received** and **TDS Deducted** (Section 193, 10% on listed corporate NCDs if interest exceeds ₹5,000). Tax-Free bonds and G-Secs have ₹0 TDS.

---

### 6.4 Sovereign Gold Bond (SGB) Sec 47(viic) Tax Exemption
- Under Section 47(viic) of the Indian Income Tax Act, any capital gains arising on redemption of Sovereign Gold Bonds by an individual investor at RBI maturity are **100% EXEMPT FROM CAPITAL GAINS TAX**.
- When recording a maturity redemption in MyFolioVault, select `MATURITY_REDEMPTION`. The tax engine flags the gain as `EXEMPT_SGB_MATURITY`, completely separating it from taxable LTCG!

---

### 6.5 How to Record Redemption After Expiry / Maturity Date
When a Sovereign Gold Bond (SGB), Government Security (G-Sec), or Corporate NCD reaches its maturity date:
1. **Automated Maturity Recognition**:
   - As soon as the maturity date arrives or passes (`Maturity Date <= today` or `Days to Maturity <= 0`), the dashboard flags the holding with a prominent red **"Matured"** status in the Maturity Date column.
   - An interactive dark **"Redeem"** button (with a gold award badge `bi-award`) automatically appears in the Action column next to *"Add Trans"*, as well as inside the split dropdown as *"Redeem at Maturity"*.
2. **Step-by-Step Maturity Redemption Process**:
   - Click the **Redeem** button on the matured bond row to open the **Maturity Redemption Modal**.
   - **Redemption Date**: Enter the date the principal redemption proceeds were credited to your bank account (defaults to today).
   - **Redeemed Quantity**: Enter the units or grams being redeemed (auto-filled with your current active units/grams).
   - **Redemption Price per Unit (₹)**:
     - For **SGBs**: Enter the final redemption rate fixed by RBI (calculated as the simple average of closing gold prices of 999 purity published by IBJA for the week preceding maturity).
     - For **G-Secs & Corporate NCDs**: Enter the face value returned by the issuer (e.g. ₹ 1,000.00).
   - **Notes**: Optional audit remark (e.g. `RBI SGB 2018-19 Series IV Final Redemption`).
   - The preview box instantly calculates the **Total Principal Amount Received** (`Redeemed Quantity * Redemption Price`).
   - Click **"Confirm Maturity Redemption"**.
3. **Accounting & Tax Effects**:
   - For **SGBs**, the redemption is processed under **Section 47(viic)** — the entire capital gain is logged as **100% Tax-Exempt** in the Capital Gains Report.
   - For **G-Secs & Corporate Bonds**, capital gains or losses (redemption proceeds minus net cost basis) are computed and audited under the Tax & Redemption Log.
   - The active units drop to 0, and the bond automatically moves to the **Past Holdings / Fully Redeemed Bonds** archive table on the Bonds dashboard.

---

## 7. Module 5: Mutual Funds

### 7.1 How to Add a Fund & Folio
1. Navigate to **Mutual Funds** &rarr; Click **"+ Add New Fund"**.
2. Enter Scheme Name (e.g. `Parag Parikh Flexi Cap Fund - Direct Plan - Growth`).
3. Enter the 6-digit **AMFI Code** (e.g. `122639`) for automated daily NAV refreshes.
4. Select AMC / Fund House, Category (Equity, Debt, Hybrid, ELSS), and enter your **Folio Number**.

---

### 7.2 How to Record SIP & Lumpsum Investments
1. In Mutual Funds Dashboard &rarr; Click **"Record Investment"**.
2. **Purchase Type**: Defaults to **Lumpsum** (switch to **SIP** for recurring auto-debits).
3. Enter **Investment Date**, **Purchase NAV**, and **Amount (₹)**.
4. The calculator automatically computes **Units Allotted**:
   $$\text{Units} = \frac{\text{Amount} - \text{Stamp Duty}}{\text{NAV}}$$
   *(Stamp duty on mutual fund purchases is 0.005%)*.

---

### 7.3 How to Record Redemptions (FIFO Units Matching)
- When selling units, enter the Redemption Date, Units to Redeem, and Exit NAV.
- The engine matches redeemed units against earliest purchased lots in strict FIFO order, calculating holding periods and capital gains tax.

---

## 8. National Pension System (NPS Tier 1)

### 8.1 First-Time Setup: PRAN, Pension Fund Manager & Scheme Details (Step-by-Step)
When visiting the NPS module (`/nps`) for the first time without an active account, you are automatically directed to the **Setup NPS Tier 1 Account** wizard (`/nps/new`).

Follow these step-by-step instructions to initialize your PRAN account:

1. **Step 1: Subscriber & Account Identification**:
   - **PRAN**: Enter your 12-digit Permanent Retirement Account Number (e.g. `110012345678`).
   - **Subscriber Name**: Enter your name as registered with the Central Recordkeeping Agency (CRA - Protean/NSDL or KFintech).
   - **Pension Fund Manager (PFM)**: Enter your chosen fund house (e.g. *HDFC Pension Management*, *ICICI Prudential Pension Fund*, *SBI Pension Funds*, *UTI Retirement Solutions*, *Kotak Mahindra Pension Fund*, etc.).
   - **Investment Choice**: Select **Active Choice** (allows customized allocation up to 75% in equity) or **Auto Choice** (lifecycle matrix based on age).

2. **Step 2: Target Asset Allocation Across 4 Schemes**:
   - Specify your target percentage across the 4 asset classes. **The total sum must equal exactly 100%**:
     - **Scheme E (Equity)**: Up to 75% under Active Choice (e.g. `50.00%`).
     - **Scheme C (Corporate Debt)**: Up to 100% (e.g. `30.00%`).
     - **Scheme G (Government Securities)**: Up to 100% (e.g. `15.00%`).
     - **Scheme A (Alternative Assets)**: Up to 5% (e.g. `5.00%`).

3. **Step 3: Configuring Scheme Codes for Automated Live NAV Sync**:
   - To enable automated one-click NAV updates via [npsnav.in](https://npsnav.in), enter the official 8-character Scheme Code for each selected asset class:
     - **Scheme E Code**: e.g. `SM007001` for *ICICI PRUDENTIAL SCHEME E - TIER I* or `SM008001` for *HDFC PENSION MANAGEMENT SCHEME E - TIER I*.
     - **Scheme C Code**: e.g. `SM007002` for *ICICI Scheme C*.
     - **Scheme G Code**: e.g. `SM007003` for *ICICI Scheme G*.
     - **Scheme A Code**: e.g. `SM007004` for *ICICI Scheme A*.
   - *(Tip: To look up your Scheme Code, visit [npsnav.in](https://npsnav.in), search for your PFM name and asset class, and copy the 8-character alphanumeric code from the fund URL or details card).*

4. **Step 4: Initial Contribution & Allotment Details**:
   - Enter your initial or baseline contribution to establish the portfolio:
     - **Transaction Date**: Date of deposit or allotment from your CRA transaction receipt.
     - **Gross Amount (₹)**: Total amount transferred (e.g. `₹ 50,000.00`).
     - **Contribution Type**: Select `Voluntary` (for Section 80CCD(1B)), `Employee`, or `Employer`.
     - **Optional POP Charges (₹)**: Payment gateway / POP commissions (e.g. `₹ 25.00`). Net amount allocated becomes `₹ 49,975.00`.
     - **Scheme Allotments**: Enter the purchase NAV and units allotted for Scheme E, C, G, and A from your official CRA allotment statement.
   - Click **"Save & Setup NPS Account"**.

---

### 8.2 How to Edit Scheme Details, PFM & Target Allocation Later
As your investment journey evolves, you may change your Pension Fund Manager, switch asset allocation percentages, or update scheme codes.

Follow these step-by-step instructions to modify your NPS account settings:

1. **Navigate to Dashboard**: Go to **NPS (Tier 1)** in the sidebar (`/nps`).
2. **Open the Edit Modal**:
   - On the top **PRAN Account Information** card, click the three-dots action dropdown (or split button) on the right side and select **"Edit Account & Allocation"** (or click the edit pencil button).
   - This opens the **Edit Account & Target Allocation** modal.
3. **Fields You Can Update**:
   - **Subscriber Name**: Update if there was any clerical spelling error.
   - **Pension Fund Manager (PFM)**: Update if you switched to a different pension fund manager (e.g. switched from SBI to HDFC).
   - **Investment Choice**: Switch between `Active` and `Auto`.
   - **Target Asset Allocation %**: Adjust the percentage split across Scheme E, Scheme C, Scheme G, and Scheme A. *Remember: the sum of the four allocations must equal exactly 100%*.
   - **Scheme Codes for Auto NAV Updates**: Update or insert the 8-character Scheme Codes from [npsnav.in](https://npsnav.in) for Scheme E, Scheme C, Scheme G, and Scheme A.
4. **Save Changes**: Click **"Update Account"**. Your revised asset distribution targets and scheme codes are applied immediately.
5. **Updating Scheme NAVs**:
   - **One-Click Automated Sync**: Click the **"Sync NAVs"** button at the top right of the NPS dashboard. MyFolioVault calls the `npsnav.in` endpoint and refreshes NAVs for all configured scheme codes simultaneously.
   - **Manual NAV Edit**: If offline or if your scheme code isn't entered, click the **"Update NAVs"** button to manually enter the latest NAV values and valuation date for Scheme E, C, G, and A.
6. **Editing Historical Transactions**:
   - If an error was made on a past contribution or unit deduction, scroll down to the **Contribution History** or **Quarterly Unit Deductions** table and click the **Edit** icon on that specific row to update dates, amounts, or individual scheme units and NAVs.

---

### 8.3 How to Record Contributions (Scheme E/C/G/A Split)
1. In the NPS Dashboard &rarr; Click **"+ Add Transaction"** &rarr; select the **Contribution** tab.
2. Enter the **Transaction Date**, **Gross Amount (₹)**, and **Contribution Type** (`Voluntary`, `Employee`, or `Employer`).
3. Enter any **POP Charges / Platform Deductions** (e.g. `₹ 25.00`).
4. Enter the units allotted and NAV for Scheme E, Scheme C, Scheme G, and Scheme A as detailed on your CRA contribution acknowledgement slip.
5. Click **"Confirm Contribution"**. The new units are added to your scheme balances and audited under the Contribution History ledger.

---

### 8.4 How to Record Quarterly Fee & Unit Deductions
1. Every quarter, the CRA (Protean / KFintech) and Custodian deduct administrative maintenance charges by canceling fractional units from your scheme holdings.
2. In the NPS Dashboard &rarr; Click **"+ Add Transaction"** &rarr; select the **Quarterly Unit Deduction** tab.
3. Enter the **Deduction Date** (e.g. end of quarter: June 30, September 30, December 31, March 31).
4. Enter the fractional units deducted from each scheme (e.g. `0.2345` units cancelled from Scheme E, `0.1120` from Scheme C, etc.).
5. Click **"Confirm Fee Deduction"**. The units are deducted from scheme balances, keeping your recorded unit balances in exact 100% agreement with your official CRA statement.

---

## 9. Module 7: Consolidated Brokerage & Expenses Ledger (NEW)

### 9.1 Why This Module Exists (Consolidated Contract Notes)
In India, discount and full-service brokers (Zerodha, Groww, AngelOne, Upstox, ICICI Direct, Kotak Securities, HDFC Sky) generate a **Consolidated Daily Contract Note** at the end of every trading day.

In this statement:
- Brokerage, exchange turnover fees, SEBI charges, clearing fees, stamp duty, and 18% GST are totaled across the day's trades rather than broken down per security.
- Furthermore, non-trade charges like **Demat Annual Maintenance Charges (AMC)**, **CDSL/NSDL DP Transaction Charges** (e.g. ₹15.93 per scrip debit on delivery sales), **Call & Trade charges**, and payment gateway fees are debited directly to your trading ledger.

Trying to divide these small charges across individual stocks is cumbersome and prone to rounding errors. **MyFolioVault solves this with the Consolidated Brokerage & Expenses Ledger!**

```
┌─────────────────────────────────────────────────────────────┐
│                 EXPENSES FLOW IN REPORTS                    │
│                                                             │
│   Daily Contract Note / AMC / DP Debit                      │
│                           │                                 │
│                           ▼                                 │
│          [ Record in Expenses Ledger ]                      │
│             (Date, Amount, Type, Module)                    │
│                           │                                 │
│         ┌─────────────────┴─────────────────┐               │
│         ▼                                   ▼               │
│  [ Cash Flow Report ]              [ Expenses Report ]      │
│  Sums into module total            Categorizes by type:     │
│  expense & reduces Net Flow        Brokerage / STT / Other  │
└─────────────────────────────────────────────────────────────┘
```

---

### 9.2 How to Record an Expense Transaction
1. In the sidebar under **Asset Modules**, click **Expenses** (`/expenses`).
2. Click the **"+ Record Expense"** button.
3. Fill in the modal form and click **Save Expense**.

---

### 9.3 What to Enter in the Fields
- **Expense Date**: Date on your contract note or bank/demat statement debit.
- **Amount (₹)**: Total rupee amount of the charge (e.g. `47.20`).
- **Expense Type**:
  - `Brokerage Charges`: Direct broker commission, delivery turnover brokerage, call-and-trade fees.
  - `STT & Statutory Taxes`: Securities Transaction Tax (STT), Exchange Turnover Charges, SEBI Regulatory Turnover Fee, State Stamp Duty, and GST (18%).
  - `Platform & Misc Fees`: Demat Annual Maintenance (AMC), CDSL/NSDL DP charges upon stock sales, depository charges, API subscription fees.
- **Related Module**:
  - Select the asset class the charge belongs to: `Equities`, `Exchange Traded Funds (ETFs)`, `Bonds & Fixed Income`, `InvITs & REITs`, or `Mutual Funds`. *(NPS is excluded because NPS charges are deducted via fractional unit cancellations)*.
- **Notes / Reference**:
  - Enter the contract note number, broker name, or reason (e.g. `Zerodha Contract Note #20260923`, `CDSL DP charges on Tata Motors sale`, `Groww AMC Q1`).

---

### 9.4 How Expenses Add Up in Cash Flow & Friction Reports
> [!IMPORTANT]
> **Automatic Reporting Integration**:
> Every expense recorded in this module **adds up on top of any trade-level friction** across all financial reports:
> 1. **Cash Flow & Capital Activity Report (`/reports/cashflow`)**:
>    - The recorded expense amount is added to that module's `Total Expense (₹)`.
>    - Decreases the module's and portfolio's `Net Flow (₹)`.
>    - Increments the module's total `Activities` count.
> 2. **Expenses & Statutory Friction Report (`/reports/expenses`)**:
>    - `BROKERAGE` adds directly to the module's Brokerage column.
>    - `STT_TAXES` adds directly to the module's STT & Taxes column.
>    - `PLATFORM_MISC` adds directly to the module's Platform & Misc column.
>    - Recomputes total friction, percentage drag, and exports cleanly into CSV!
> 3. **Welcome Page Dashboard (`/`)**:
>    - Included in the **Consolidated Brokerage & Statutory Charges** overview card.

---

### 9.5 How to Edit & Delete Expense Records
- **Edit**: In the Expenses table, click the pencil icon next to any entry. Update the date, amount, type, module, or notes, and click **Update Expense**.
- **Delete**: Click the red trash icon. A confirmation modal appears preventing accidental deletion. Confirming removes the expense and instantly updates all reports.

---

## 10. Analytics, Reports & Capital Gains Tax Audit

Navigate to **Analytics & Reports** in the sidebar (`/reports`) to access the 5 dedicated audit views:

### 10.1 Cash Flow & Capital Activity Report (`/reports/cashflow`)
- Full audit of money moving in and out of your portfolio for the selected financial year.
- Computes:
  $$\text{Net Flow} = (\text{Total Sold} + \text{Passive Income}) - (\text{Total Purchases} + \text{Expenses \& TDS})$$
- Displays breakdown across all 6 asset modules and equity sectors.

### 10.2 Capital Gains & Tax Audit Report (`/reports/tax`)
- Designed specifically for preparing Indian Income Tax Returns (**ITR-2 / ITR-3**).
- Itemizes every matched FIFO sale with Buy Date, Sell Date, Holding Days, Buy Price, Sell Price, and Realized Gain.
- Segregated into:
  - **Equity STCG @ 20%** (Section 111A)
  - **Equity LTCG @ 12.5%** (Section 112A with ₹1.25 Lakh exemption)
  - **Debt / SGB LTCG @ 12.5%** (Section 112)
  - **Sec 50AA Debt / Commodity Gains** (Taxed at slab rates)
  - **Exempt SGB Maturity Redemptions** (Section 47(viic))

### 10.3 Passive Income & Distribution Report (`/reports/income`)
- Consolidates all cash payouts received:
  - Equity Cash Dividends
  - Bond Semi-Annual & Annual Coupon Interest
  - REIT & InvIT Distributions (Interest, Dividend, ROC, Other)
- Tracks **TDS Deducted** for claiming tax credits in **Schedule TDS / Form 26AS / AIS**.

### 10.4 Expenses & Statutory Friction Report (`/reports/expenses`)
- Audits trading friction across your portfolio.
- Combines trade-level fees with standalone daily contract note expenses.
- Displays Brokerage, STT & Taxes, TDS Withheld, Platform & Misc, and Total Friction.

### 10.5 Asset Allocation & Valuation Snapshot (`/reports/allocation`)
- Visual portfolio allocation by asset class with percentage share.
- Deep-dive breakdown of direct equities by sector (Banking, IT, FMCG, Energy, etc.).

### 10.6 CSV Exporting & Spreadsheet Compatibility
- Click **"Export to CSV"** on any report to download official audit files.
- All CSVs are encoded with **UTF-8 Byte Order Mark (BOM)** (`\xEF\xBB\xBF`), ensuring that Indian Rupee symbols (₹) and accented characters open cleanly in **Microsoft Excel**, **Apple Numbers**, and **LibreOffice Calc** without character corruption.

---

## 11. Production Deployment, Data Cleanup & Backup

### 11.1 Creating Backup Dumps
To create an offline backup of your portfolio database:
```bash
mysqldump -u root -p investment_portfolio > backup_portfolio_$(date +%Y%m%d).sql
```

### 11.2 Restoring from Backup
```bash
mysql -u root -p investment_portfolio < backup_portfolio_20260923.sql
```

### 11.3 Packaging Standalone Releases
Run the automated packaging utility to generate clean distribution zips without sensitive `.env` files or git artifacts:
```bash
php package.php v1.0.0
```
Outputs: `dist/myfoliovault-v{VERSION}-standalone.zip`.

---

## 12. Credits & Technology Stack Acknowledgements

MyFolioVault is proudly built on open-source foundations:
- **CodeIgniter 4**: High-performance, lightweight, and elegant PHP framework ([codeigniter.com](https://codeigniter.com)).
- **Bootstrap 5.3.3 & Bootstrap Icons**: Clean, responsive, offline-bundled user interface ([getbootstrap.com](https://getbootstrap.com)).
- **Chart.js**: Client-side interactive canvas charts for asset allocation and portfolio trends ([chartjs.org](https://chartjs.org)).
- **npsnav.in**: Open National Pension System NAV repository ([npsnav.in](https://npsnav.in)).
- **AMFI India**: Daily official mutual fund Net Asset Value data ([amfiindia.com](https://www.amfiindia.com)).
- **Google Gemini AI**: Architecture engineering, FIFO tax matching algorithms, and documentation design.
- **PHP & MariaDB/MySQL**: Modern, dependable, and self-hosted database and runtime engines.

---

## 13. Disclaimer & Limitation of Liability

> [!CAUTION]
> **PLEASE READ CAREFULLY BEFORE USING THIS APPLICATION:**
> 
> 1. **"As Is" Software**: This application is open-source software provided on an **"AS IS"** and **"AS AVAILABLE"** basis without warranties of any kind, either express or implied, including but not limited to merchantability, fitness for a particular purpose, or freedom from defects.
> 2. **No Liability for Data Loss or Damages**: In no event shall the author(s), contributor(s), or copyright holder(s) be held liable for any direct, indirect, incidental, special, consequential, or punitive damages (including, without limitation, loss of data, database corruption, software errors, downtime, or business interruption) arising out of the installation, use, or inability to use this software.
> 3. **Not Financial or Tax Advice**: This tool is developed strictly for personal portfolio tracking, bookkeeping, and educational utility. It does **not** constitute financial, legal, investment, or tax advice under SEBI regulations or the Indian Income Tax Act. Tax calculations, corporate action formulas (bonus, splits, mergers), and capital gains treatments should always be independently validated against official broker contract notes, depository statements (CDSL/NSDL), and certified tax professionals before filing returns.
> 4. **Backup Responsibility**: You are solely responsible for securing your environment, maintaining regular database backups, protecting passwords/encryption keys, and verifying calculation results.

---
*MyFolioVault &bull; 100% Offline Personal Wealth Management for Indian Investors.*
