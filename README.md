# Personal Investment Portfolio Tracker

[![PHP](https://img.shields.io/badge/PHP-8.1%2B-blue.svg)](https://www.php.net/)
[![CodeIgniter](https://img.shields.io/badge/Framework-CodeIgniter%204-EF4444.svg)](https://codeigniter.com/)
[![MySQL](https://img.shields.io/badge/Database-MySQL%20%7C%20MariaDB-00758F.svg)](https://www.mysql.com/)
[![Bootstrap](https://img.shields.io/badge/UI-Bootstrap%205.3.3%20(Offline)-7952B3.svg)](https://getbootstrap.com/)
[![License: MIT](https://img.shields.io/badge/License-MIT-green.svg)](LICENSE)

A high-performance, private, and 100% offline-compatible investment portfolio management system designed specifically for Indian capital market investors. Track equities, REITs, InvITs, ETFs, Sovereign Gold Bonds (SGBs), Mutual Funds, and NPS across multiple brokers and family accounts with comprehensive Indian corporate actions and tax engine support.

---

## Key Modules & Capabilities

### 1. Equities (Stocks)
- Multi-exchange support (NSE / BSE) with real-time portfolio valuation and weighted average price (WAP) calculations.
- Comprehensive corporate actions engine:
  - **Bonus Issues**: Ratio-based share credits with automatic WAP dilution without tax trigger.
  - **Stock Splits**: Face-value split multipliers with quantity scaling and proportional price adjustment.
  - **Rights Issues**: Pre-emptive subscription tracking with application cost basis integration.
  - **Mergers & Demergers**: Share swap ratios and cost-of-acquisition apportionment across resultant entities.
  - **Dividend Ledger**: Track per-share cash dividends and gross payouts.
- Real-time sector allocation visualization across 18 major industry verticals.

### 2. Real Estate & Infrastructure Investment Trusts (REITs & InvITs)
- Specialized multi-component distribution ledger supporting Indian tax structures:
  - **Dividend Income** (Exempt under Sec 10(23FD) if SPV has not opted for Sec 115BAA)
  - **Interest Income** (Taxable at slab rates)
  - **Repayment of Capital / Amortization of SPV Debt** (Subtracted from unit cost basis; excess taxed under Sec 56(2)(xii))
  - **Other Income** (Taxable)

### 3. Exchange-Traded Funds (ETFs)
- Real-time tracking of broad market indices (Nifty 50, Bank Nifty), sector ETFs, thematic ETFs, and commodity ETFs (Gold & Silver).
- Unit holding calculations, buy/sell trade book, and realized/unrealized profit & loss.

### 4. Bonds & Sovereign Gold Bonds (SGBs)
- **Sovereign Gold Bonds (SGBs)**: Gram-based gold holdings with maturity tracking and Indian Income Tax Act **Section 47(viic)** capital gains exemption flags upon RBI redemption.
- **Corporate & Government Bonds**: Face value, coupon rate, coupon frequency, and accrued semi-annual/annual interest tracking.

### 5. Mutual Funds
- Direct and Regular plans across Equity, Debt, Hybrid, and Solution-Oriented schemes.
- SIP (Systematic Investment Plan) and lump-sum investment ledger.
- ELSS (Equity Linked Savings Scheme) tracking with 3-year statutory lock-in periods.

### 6. National Pension System (NPS)
- PRAN-level account tracking for **Tier I** (Tax-saving pension account) and **Tier II** (Volatile withdrawal account).
- Multi-PFRDA pension fund manager allocation and asset class splits (Equity `E`, Corporate Debt `C`, Government Securities `G`, Alternative Assets `A`).

---

## 100% Offline Architecture

- **Zero Cloud or Third-Party CDN Dependencies**: No reliance on external CDNs or Google Fonts. Works seamlessly on air-gapped systems or offline local servers.
- **Bundled Assets**: Bootstrap 5.3.3 CSS/JS and Bootstrap Icons 1.11.3 are pre-packaged in `public/assets/`.
- **Native OS Font Stack**: High-legibility modern typography using `-apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif`.

---

## System Requirements

- **PHP**: 8.1 or higher (PHP 8.2+ recommended)
  - Required Extensions: `intl`, `mbstring`, `mysqli`, `json`
- **Database**: MySQL 5.7+ or MariaDB 10.3+
- **Web Server**: Apache 2.4+ with `mod_rewrite` enabled (e.g., XAMPP, WampServer, LAMP)

---

## Installation & First-Time Setup

### Step 1: Clone Repository
Clone this repository into your web server's root directory (e.g. `xampp/htdocs/`):
```bash
cd /path/to/webserver/htdocs
git clone https://github.com/<your-username>/<your-repo-name>.git Portfolio
cd Portfolio
```

### Step 2: Add CodeIgniter 4 Framework Engine
*This repository contains pure application source code.* Ensure the official CodeIgniter 4 framework `system/` directory is in the root:
- **Option A (Composer)**: Run `composer install` in the project root.
- **Option B (Manual)**: Download the [CodeIgniter 4 release zip](https://github.com/codeigniter4/framework/releases) and place the `system` folder directly into the project root directory.

### Step 3: Run the Web Setup Wizard
Open your browser and navigate to:
```
http://localhost/Portfolio/public/install.php
```
The wizard will:
1. Validate PHP extensions and directory write permissions.
2. Connect to MySQL and automatically create the `investment_portfolio` database.
3. Import the clean production schema (`schema.sql`) with all 25 tables and sector seeds.
4. Generate the `.env` file with secure session and encryption keys.
5. Create your initial Administrator account.

### Step 4: Secure the Installation
Once the wizard finishes, click the **Delete Setup Installer** button (or manually delete `public/install.php`) to secure your installation for production.

---

## Manual Database Setup (Alternative)

If you prefer configuring manually via MySQL CLI or phpMyAdmin:
1. Create a database:
   ```sql
   CREATE DATABASE investment_portfolio CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
   ```
2. Import the schema:
   ```bash
   mysql -u root -p investment_portfolio < schema.sql
   ```
3. Copy the environment configuration template:
   ```bash
   cp .env.example .env
   ```
4. Edit `.env` to supply your database credentials, base URL, and encryption key.

---

## Documentation & User Guide

- **In-App Interactive User Guide**: Navigate to `http://localhost/Portfolio/public/guide` after logging in.
- **Markdown Manual**: Read [`USER_GUIDE.md`](USER_GUIDE.md) for detailed workflows, corporate action calculations, and tax rule references.

---

## ⚠️ Disclaimer & Limitation of Liability

> [!CAUTION]
> **PLEASE READ CAREFULLY BEFORE USING THIS APPLICATION:**
> 
> 1. **"As Is" Software**: This application is open-source software provided on an **"AS IS"** and **"AS AVAILABLE"** basis without warranties of any kind, either express or implied, including but not limited to merchantability, fitness for a particular purpose, or freedom from defects.
> 2. **No Liability for Data Loss or Damages**: In no event shall the author(s), contributor(s), or copyright holder(s) be held liable for any direct, indirect, incidental, special, consequential, or punitive damages (including, without limitation, loss of data, database corruption, software errors, downtime, or business interruption) arising out of the installation, use, or inability to use this software.
> 3. **Not Financial or Tax Advice**: This tool is developed strictly for personal portfolio tracking, bookkeeping, and educational utility. It does **not** constitute financial, legal, investment, or tax advice under SEBI regulations or the Indian Income Tax Act. Tax calculations, corporate action formulas (bonus, splits, mergers), and capital gains treatments should always be independently validated against official broker contract notes, depository statements (CDSL/NSDL), and certified tax professionals before filing returns.
> 4. **Backup Responsibility**: You are solely responsible for securing your environment, maintaining regular database backups, protecting passwords/encryption keys, and verifying calculation results.

---

## 💡 Feedback, Issues & Feature Requests

Community feedback is what makes open-source tools better for everyone. We actively welcome you to report any issues or propose enhancements:

- 🐛 **Report Bugs & Issues**: If you encounter any calculation differences, database errors, UI glitches, or broken links, please **[Open an Issue on GitHub](https://github.com/)** describing what happened, along with reproduction steps.
- 💡 **Feature Requests & Ideas**: Have ideas for additional Indian investment instruments (e.g. T-Bills, FDs, P2P), new corporate action types, tax reports (ITR filing schedules), or visual charts? Please submit a feature request via GitHub Issues!
- 🤝 **Pull Requests**: Code contributions, bug fixes, and performance improvements are always welcome.

---

## ⭐ Support the Project

If this personal portfolio tracker saves you time, helps you manage your investments, or brings you value:

- ⭐ **Star this repository** on GitHub — it gives the project visibility and helps other Indian retail investors discover it!

---

## License

This project is licensed under the MIT License - see the [LICENSE](LICENSE) file for details.
