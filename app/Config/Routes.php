<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */

// Authentication Routes (Public)
$routes->get('login', 'Auth::login');
$routes->post('login', 'Auth::attemptLogin');
$routes->get('logout', 'Auth::logout');

// Protected Routes (Require Login)
$routes->group('', ['filter' => 'auth'], static function ($routes) {
    // Welcome / Dashboard
    $routes->get('/', 'Home::index');
    $routes->get('welcome', 'Home::index');

    // Admin & Account Settings
    $routes->get('settings', 'Settings::index');
    $routes->post('settings/update-profile', 'Settings::updateProfile');
    $routes->get('settings/password', 'Settings::password');
    $routes->post('settings/change-password', 'Settings::changePassword');
    $routes->get('settings/sectors', 'Settings::sectors');
    $routes->post('settings/add-sector', 'Settings::addSector');
    $routes->post('settings/update-sector', 'Settings::updateSector');
    $routes->get('settings/delete-sector/(:num)', 'Settings::deleteSector/$1');

    // Step 2.1: Equities Module
    $routes->get('equities', 'Equities::index');
    $routes->get('equities/new', 'Equities::newStock');
    $routes->post('equities/new', 'Equities::createStock');
    $routes->post('equities/transaction', 'Equities::addTransaction');
    $routes->post('equities/update-stock', 'Equities::updateStock');
    $routes->post('equities/update-transaction', 'Equities::updateTransaction');
    $routes->get('equities/delete-transaction/(:num)', 'Equities::deleteTransaction/$1');
    $routes->post('equities/update-dividend', 'Equities::updateDividend');
    $routes->get('equities/delete-dividend/(:num)', 'Equities::deleteDividend/$1');
    $routes->get('equities/refresh-prices', 'Equities::refreshPrices');
    $routes->get('equities/delete/(:num)', 'Equities::deleteStock/$1');
    $routes->get('equities/check-corporate-action-eligibility', 'Equities::checkCorporateActionEligibility');
    $routes->post('equities/corporate-action', 'Equities::recordCorporateAction');

    // Step 2.2: ETFs Module
    $routes->get('etfs', 'Etfs::index');
    $routes->get('etfs/new', 'Etfs::newEtf');
    $routes->post('etfs/new', 'Etfs::createEtf');
    $routes->post('etfs/transaction', 'Etfs::addTransaction');
    $routes->post('etfs/update-etf', 'Etfs::updateEtf');
    $routes->post('etfs/update-transaction', 'Etfs::updateTransaction');
    $routes->get('etfs/delete-transaction/(:num)', 'Etfs::deleteTransaction/$1');
    $routes->get('etfs/refresh-prices', 'Etfs::refreshPrices');
    $routes->get('etfs/delete/(:num)', 'Etfs::deleteEtf/$1');
    $routes->get('etfs/check-split-eligibility', 'Etfs::checkSplitEligibility');
    $routes->post('etfs/split', 'Etfs::recordSplit');

    // Step 2.3: Mutual Funds Module
    $routes->get('mutual-funds', 'MutualFunds::index');
    $routes->get('mutual-funds/new', 'MutualFunds::newFund');
    $routes->post('mutual-funds/new', 'MutualFunds::createFund');
    $routes->get('mutual-funds/lookup-amfi/(:segment)', 'MutualFunds::lookupAmfi/$1');
    $routes->post('mutual-funds/transaction', 'MutualFunds::addTransaction');
    $routes->post('mutual-funds/update-fund', 'MutualFunds::updateFund');
    $routes->post('mutual-funds/update-transaction', 'MutualFunds::updateTransaction');
    $routes->get('mutual-funds/delete-transaction/(:num)', 'MutualFunds::deleteTransaction/$1');
    $routes->get('mutual-funds/refresh-navs', 'MutualFunds::refreshNavs');
    $routes->get('mutual-funds/delete/(:num)', 'MutualFunds::deleteFund/$1');

    // Step 2.4: NPS Module (National Pension System - Tier 1)
    $routes->get('nps', 'Nps::index');
    $routes->get('nps/new', 'Nps::newAccount');
    $routes->post('nps/new', 'Nps::createAccount');
    $routes->post('nps/transaction', 'Nps::addTransaction');
    $routes->post('nps/update-account', 'Nps::updateAccount');
    $routes->post('nps/update-transaction', 'Nps::updateTransaction');
    $routes->get('nps/delete-transaction/(:num)', 'Nps::deleteTransaction/$1');
    $routes->match(['get', 'post'], 'nps/update-navs', 'Nps::updateNavs');
    $routes->get('nps/delete/(:num)', 'Nps::deleteAccount/$1');

    // Step 2.5: Bonds Module (SGBs, G-Secs, Corporate NCDs, Tax-Free)
    $routes->get('bonds', 'Bonds::index');
    $routes->get('bonds/new', 'Bonds::newBond');
    $routes->post('bonds/new', 'Bonds::createBond');
    $routes->post('bonds/transaction', 'Bonds::addTransaction');
    $routes->post('bonds/update-bond', 'Bonds::updateBond');
    $routes->post('bonds/update-transaction', 'Bonds::updateTransaction');
    $routes->get('bonds/delete-transaction/(:num)', 'Bonds::deleteTransaction/$1');
    $routes->post('bonds/update-interest', 'Bonds::updateInterest');
    $routes->get('bonds/delete-interest/(:num)', 'Bonds::deleteInterest/$1');
    $routes->post('bonds/update-price', 'Bonds::updatePrice');
    $routes->get('bonds/delete/(:num)', 'Bonds::deleteBond/$1');

    // Step 2.6: InvITs & REITs Module
    $routes->get('reits-invits', 'ReitsInvits::index');
    $routes->get('reits-invits/new', 'ReitsInvits::newTrust');
    $routes->post('reits-invits/new', 'ReitsInvits::createTrust');
    $routes->post('reits-invits/update-trust', 'ReitsInvits::updateTrust');
    $routes->post('reits-invits/transaction', 'ReitsInvits::addTransaction');
    $routes->post('reits-invits/update-transaction', 'ReitsInvits::updateTransaction');
    $routes->get('reits-invits/delete-transaction/(:num)', 'ReitsInvits::deleteTransaction/$1');
    $routes->post('reits-invits/update-distribution', 'ReitsInvits::updateDistribution');
    $routes->get('reits-invits/delete-distribution/(:num)', 'ReitsInvits::deleteDistribution/$1');
    $routes->get('reits-invits/refresh-prices', 'ReitsInvits::refreshPrices');
    $routes->get('reits-invits/delete/(:num)', 'ReitsInvits::deleteTrust/$1');

    // Reports Module (Multipage: Cashflow, Capital Gains / Tax, Income, Expenses, Allocation)
    $routes->group('reports', static function ($routes) {
        $routes->get('', 'Reports::cashflow');
        $routes->get('cashflow', 'Reports::cashflow');
        $routes->get('export-cashflow', 'Reports::exportCashflow');

        $routes->get('tax', 'Reports::tax');
        $routes->get('export-tax', 'Reports::exportTax');

        $routes->get('income', 'Reports::income');
        $routes->get('export-income', 'Reports::exportIncome');

        $routes->get('expenses', 'Reports::expenses');
        $routes->get('export-expenses', 'Reports::exportExpenses');

        $routes->get('allocation', 'Reports::allocation');
    });

    // User Guide Route
    $routes->get('guide', 'Guide::index');
});



