<?php
$cookieFile = __DIR__ . '/scratch_test_cookie.txt';
@unlink($cookieFile);

// 1. Get login page
$ch = curl_init('http://localhost/Portfolio/public/login');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_COOKIEJAR, $cookieFile);
$html = curl_exec($ch);
curl_close($ch);

preg_match('/name="csrf_test_name"\s+value="([^"]+)"/', $html, $m);
$csrf = $m[1] ?? '';
echo "CSRF: " . substr($csrf, 0, 10) . "...\n";

// 2. Post login
$ch = curl_init('http://localhost/Portfolio/public/login');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query([
    'csrf_test_name' => $csrf,
    'email' => 'admin@portfolio.local',
    'password' => 'password123',
]));
curl_setopt($ch, CURLOPT_COOKIEFILE, $cookieFile);
curl_setopt($ch, CURLOPT_COOKIEJAR, $cookieFile);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
$res = curl_exec($ch);
curl_close($ch);

// 3. Check welcome page
$ch = curl_init('http://localhost/Portfolio/public/');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_COOKIEFILE, $cookieFile);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
$welcome = curl_exec($ch);
curl_close($ch);

@unlink($cookieFile);

echo "Welcome page length: " . strlen($welcome) . "\n";
echo "Has Consolidated Portfolio: " . (strpos($welcome, 'Consolidated Portfolio Net Worth') !== false ? 'YES' : 'NO') . "\n";
echo "Has Equities: " . (strpos($welcome, 'Equities') !== false ? 'YES' : 'NO') . "\n";
echo "Has InvITs & REITs: " . (strpos($welcome, 'InvITs & REITs') !== false ? 'YES' : 'NO') . "\n";
echo "Has ETFs: " . (strpos($welcome, 'Exchange Traded Funds') !== false ? 'YES' : 'NO') . "\n";
echo "Has Bonds: " . (strpos($welcome, 'Bonds & Fixed Income') !== false ? 'YES' : 'NO') . "\n";
echo "Has Mutual Funds: " . (strpos($welcome, 'Mutual Funds') !== false ? 'YES' : 'NO') . "\n";
echo "Has NPS: " . (strpos($welcome, 'NPS (Tier 1)') !== false ? 'YES' : 'NO') . "\n";
echo "Has CodeIgniter 4.5.5: " . (strpos($welcome, 'CodeIgniter 4.5.5') !== false ? 'YES' : 'NO') . "\n";
echo "Has System Specs: " . (strpos($welcome, 'System Specifications') !== false ? 'YES' : 'NO') . "\n";
echo "Has Offline Ready: " . (strpos($welcome, 'Offline Ready') !== false ? 'YES' : 'NO') . "\n";
