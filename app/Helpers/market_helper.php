<?php
/**
 * MyFolioVault - Market & NAV Fetching Helpers
 *
 * Provides centralized quote and NAV fetching for Indian market instruments
 * across NSE, BSE, AMFI (Mutual Funds), and PFRDA (NPS).
 */

if (!function_exists('fetch_yahoo_price')) {
    /**
     * Fetch the latest regular market price and previous close from Yahoo Finance.
     *
     * @param string $symbol   Trading ticker symbol (e.g. RELIANCE, NIFTYBEES, EMBASSY)
     * @param string $exchange Exchange identifier ('NSE' or 'BSE')
     * @return array{current_price: float, previous_close: float, updated_at: string}|null
     */
    function fetch_yahoo_price(string $symbol, string $exchange = 'NSE'): ?array
    {
        $suffix = (strtoupper($exchange) === 'BSE') ? '.BO' : '.NS';
        $ticker = urlencode(trim($symbol) . $suffix);
        $url    = "https://query1.finance.yahoo.com/v8/finance/chart/{$ticker}?interval=1d&range=1d";

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 4);
        curl_setopt($ch, CURLOPT_TIMEOUT, 6);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64)');

        $response = curl_exec($ch);
        curl_close($ch);

        if (!$response) {
            return null;
        }

        $json = json_decode($response, true);
        $meta = $json['chart']['result'][0]['meta'] ?? null;

        if ($meta && isset($meta['regularMarketPrice'])) {
            $cmp = (float) $meta['regularMarketPrice'];
            $prevClose = (float) ($meta['chartPreviousClose'] ?? $meta['previousClose'] ?? $cmp);

            return [
                'current_price'    => $cmp,
                'previous_close'   => $prevClose,
                'price_updated_at' => date('Y-m-d H:i:s'),
            ];
        }

        return null;
    }
}

if (!function_exists('fetch_amfi_nav')) {
    /**
     * Fetch latest scheme NAV and metadata from the open AMFI API (api.mfapi.in).
     *
     * @param string $amfiCode 6-digit AMFI scheme code (e.g. 120503)
     * @return array{nav: float, date: string, scheme_name: string, fund_house: string, category: string}|null
     */
    function fetch_amfi_nav(string $amfiCode): ?array
    {
        $amfiCode = trim($amfiCode);
        if (empty($amfiCode)) {
            return null;
        }

        $url = "https://api.mfapi.in/mf/{$amfiCode}";

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 4);
        curl_setopt($ch, CURLOPT_TIMEOUT, 6);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64)');

        $response = curl_exec($ch);
        curl_close($ch);

        if (!$response) {
            return null;
        }

        $data = json_decode($response, true);
        if (($data['status'] ?? '') !== 'SUCCESS' || empty($data['data'])) {
            return null;
        }

        $latest = $data['data'][0] ?? null;
        if (!$latest || !isset($latest['nav'])) {
            return null;
        }

        $rawDate = $latest['date'] ?? null;
        $formattedDate = null;
        if ($rawDate) {
            $dt = \DateTime::createFromFormat('d-m-Y', $rawDate);
            $formattedDate = $dt ? $dt->format('Y-m-d') : null;
        }

        return [
            'nav'         => (float) $latest['nav'],
            'date'        => $formattedDate ?: date('Y-m-d'),
            'scheme_name' => $data['meta']['scheme_name'] ?? '',
            'fund_house'  => $data['meta']['fund_house'] ?? '',
            'category'    => $data['meta']['scheme_category'] ?? '',
        ];
    }
}

if (!function_exists('fetch_nps_navs')) {
    /**
     * Fetch all closing NPS NAVs from npsnav.in.
     *
     * @return array{nav_map: array<string, float>, last_updated: string}|null
     */
    function fetch_nps_navs(): ?array
    {
        $url = "https://npsnav.in/api/all";

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 8);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 4);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64)');

        $response = curl_exec($ch);
        curl_close($ch);

        if (!$response) {
            return null;
        }

        $data = json_decode($response, true);
        if (!$data || !is_array($data) || empty($data['data'])) {
            return null;
        }

        $navMap = [];
        foreach ($data['data'] as $item) {
            $code = $item['scheme_code'] ?? null;
            $nav  = $item['nav'] ?? null;
            if ($code && $nav !== null && is_numeric($nav)) {
                $navMap[$code] = (float) $nav;
            }
        }

        return [
            'nav_map'      => $navMap,
            'last_updated' => $data['last_updated'] ?? date('Y-m-d'),
        ];
    }
}

