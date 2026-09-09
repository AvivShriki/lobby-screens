<?php
/**
 * Lobby Screens theme setup + live data helpers.
 * All external fetches happen server-side here — this is the whole point of
 * moving this from a static mockup into WordPress: no CORS/CSP wall.
 */

function lobby_screens_setup() {
	add_theme_support( 'title-tag' );
	add_theme_support( 'post-thumbnails' );
}
add_action( 'after_setup_theme', 'lobby_screens_setup' );

function lobby_screens_assets() {
	wp_enqueue_style( 'lobby-screens-style', get_stylesheet_uri(), array(), '7.0' );
}
add_action( 'wp_enqueue_scripts', 'lobby_screens_assets' );

// Unattended TV display — the admin bar's 32px html margin pushes the
// 100vh frame down and clips the news footer off-screen.
add_filter( 'show_admin_bar', '__return_false' );

/**
 * Strip WordPress head/foot cruft this screen can never use: emoji detection,
 * RSD/wlwmanifest/oEmbed discovery, the REST and shortlink tags. Nothing here
 * renders — it is bytes and one blocking script on a display that reloads
 * itself all day (brief §17), and it is also what left dead absolute URLs in
 * the static docs/ snapshot.
 */
function lobby_screens_trim_head() {
	remove_action( 'wp_head', 'rsd_link' );
	remove_action( 'wp_head', 'wlwmanifest_link' );
	remove_action( 'wp_head', 'wp_shortlink_wp_head' );
	remove_action( 'wp_head', 'rest_output_link_wp_head' );
	remove_action( 'wp_head', 'wp_oembed_add_discovery_links' );
	remove_action( 'wp_head', 'wp_oembed_add_host_js' );
	remove_action( 'wp_head', 'print_emoji_detection_script', 7 );
	remove_action( 'wp_print_styles', 'print_emoji_styles' );
	remove_action( 'template_redirect', 'rest_output_link_header', 11 );
}
add_action( 'init', 'lobby_screens_trim_head' );

/**
 * Shared lobby curation blocklist. The raw Ynet feed on a heavy news day is
 * mostly crime/security/casualty stories — not what a residential lobby
 * should be showing. Both the ticker and the news panel filter against this.
 *
 * KNOWN LIMIT (see README): a keyword list has already missed three real
 * headlines in testing. It is a stopgap, not the final answer — before this
 * screen goes live in a real lobby it needs either a curated feed, a model-
 * based classifier, or manual approval.
 */
function lobby_screens_ynet_blocklist() {
	return array(
		'נרצח', 'רצח', 'הצתה', 'אונס', 'תקיפה מינית', 'עבירות מין', 'עבירת מין', 'נחנק', 'דרס', 'טבח', 'הרג', 'נהרג',
		'מת ', 'מתה ', 'נספה', 'נספתה', 'אסון', 'תאונה קטלנית', 'קטלנית', 'פיגוע', 'חטיפה',
		'התאבדות', 'התאבד', 'גופה', 'גופת', 'נפטר', 'נפטרה', 'שכול', 'הלוויה', 'קבורה',
		'תקיפה', 'אלימות', 'התעללות', 'שוד', 'פריצה', 'מעצר', 'נעצר', 'חשד לרצח', 'קטין', 'קטינה', 'נערים', 'נערה', 'נער ', 'ילד ', 'ילדה ',
		'מלחמה', 'טילים', 'רקטות', 'פצועים', 'נפגעים', 'כטב״מ',
		'נפל אל מותו', 'נפלה אל מותה', 'ללא רוח חיים', 'איבד את חייו', 'איבדה את חייה', 'טבע', 'טבעה',
	);
}

/**
 * True when a piece of feed text trips the lobby blocklist.
 */
function lobby_screens_is_blocked( $text ) {
	foreach ( lobby_screens_ynet_blocklist() as $word ) {
		if ( mb_strpos( $text, $word ) !== false ) {
			return true;
		}
	}
	return false;
}

/**
 * Ynet — main RSS feed. Curated: the raw feed on a heavy news day is mostly
 * crime/security/politics, not appropriate for a residential lobby ticker.
 * Filter out headlines matching hard-news keywords rather than showing
 * everything verbatim.
 */
function lobby_screens_get_ynet_headlines( $limit = 6 ) {
	// the limit is part of the key: without it the first caller's count is
	// served to every later caller, which is how the ONE card ended up
	// rendering six stories after being asked for four.
	$cache_key = 'lobby_ynet_headlines_' . (int) $limit;
	$cached    = get_transient( $cache_key );
	if ( false !== $cached ) {
		return $cached;
	}

	require_once ABSPATH . WPINC . '/feed.php';
	$feed = fetch_feed( 'https://www.ynet.co.il/Integration/StoryRss2.xml' );

	$headlines = array();
	if ( ! is_wp_error( $feed ) ) {
		$items     = $feed->get_items( 0, 30 );
		foreach ( $items as $item ) {
			if ( count( $headlines ) >= $limit ) {
				break;
			}
			$title = $item->get_title();
			if ( ! lobby_screens_is_blocked( $title ) ) {
				$headlines[] = $title;
			}
		}
	}

	if ( empty( $headlines ) ) {
		$headlines = array( 'התחזית: מתעדכן בקרוב' );
	}

	set_transient( $cache_key, $headlines, 10 * MINUTE_IN_SECONDS );
	return $headlines;
}

/**
 * ONE.co.il — text-only headlines (client asked for logo + text, no
 * images, in a vertically auto-scrolling list). Same feed as
 * lobby_screens_get_one_stories(), just without the per-item image fetch.
 */
function lobby_screens_get_one_headlines( $limit = 10 ) {
	$cache_key = 'lobby_one_headlines_' . (int) $limit;
	$cached    = get_transient( $cache_key );
	if ( false !== $cached ) {
		return $cached;
	}

	require_once ABSPATH . WPINC . '/feed.php';
	$feed = fetch_feed( 'https://www.one.co.il/rss' );

	$headlines = array();
	if ( ! is_wp_error( $feed ) ) {
		$items = $feed->get_items( 0, $limit );
		foreach ( $items as $item ) {
			$headlines[] = html_entity_decode( $item->get_title(), ENT_QUOTES, 'UTF-8' );
		}
	}

	if ( empty( $headlines ) ) {
		$headlines = array( 'עדכוני ספורט ONE — מתעדכן בקרוב' );
	}

	set_transient( $cache_key, $headlines, 10 * MINUTE_IN_SECONDS );
	return $headlines;
}

/**
 * ONE.co.il — official RSS. The best-behaved of our three feeds: a real
 * image per item (both <enclosure> and <media:content>), plus the summary
 * inline in the description after the thumbnail markup.
 *
 * The image URL the feed hands back is already ONE's largest variant
 * (photo.one.co.il/Image/GG/7,1/<id>.webp, measured 636x326) — no rewriting
 * needed, unlike the Ynet thumbnails.
 */
function lobby_screens_get_one_stories( $limit = 4 ) {
	$cache_key = 'lobby_one_stories_v7_' . (int) $limit;
	$cached    = get_transient( $cache_key );
	if ( false !== $cached ) {
		return $cached;
	}

	require_once ABSPATH . WPINC . '/feed.php';
	$feed = fetch_feed( 'https://www.one.co.il/rss' );

	$stories = array();
	if ( ! is_wp_error( $feed ) ) {
		foreach ( $feed->get_items( 0, $limit ) as $item ) {
			$image     = '';
			$enclosure = $item->get_enclosure();
			if ( $enclosure && $enclosure->get_link() ) {
				$image = $enclosure->get_link();
			}

			$raw     = $item->get_description();
			$summary = trim( wp_strip_all_tags( $raw ) );
			$summary = html_entity_decode( $summary, ENT_QUOTES, 'UTF-8' );

			$ts = $item->get_date( 'U' );
			$stories[] = array(
				'title'   => html_entity_decode( $item->get_title(), ENT_QUOTES, 'UTF-8' ),
				'summary' => $summary,
				'image'   => $image,
				'time'    => $ts ? wp_date( 'H:i', $ts ) : '',
				'link'    => $item->get_permalink(),
			);
		}
	}

	if ( empty( $stories ) ) {
		return array();
	}

	set_transient( $cache_key, $stories, 10 * MINUTE_IN_SECONDS );
	return $stories;
}

/**
 * Open-Meteo — free, no API key required. 7-day forecast for Beer Sheva.
 */
function lobby_screens_get_weekly_weather() {
	$cache_key = 'lobby_weekly_weather';
	$cached    = get_transient( $cache_key );
	if ( false !== $cached ) {
		return $cached;
	}

	$url = add_query_arg(
		array(
			'latitude'       => 31.25,
			'longitude'      => 34.79,
			'daily'          => 'weathercode,temperature_2m_max,temperature_2m_min',
			'timezone'       => 'Asia/Jerusalem',
			'forecast_days'  => 7,
		),
		'https://api.open-meteo.com/v1/forecast'
	);

	$response = wp_remote_get( $url, array( 'timeout' => 8 ) );
	$days     = array();

	if ( ! is_wp_error( $response ) ) {
		$body = json_decode( wp_remote_retrieve_body( $response ), true );
		if ( ! empty( $body['daily']['time'] ) ) {
			$day_names = array( 'ראשון', 'שני', 'שלישי', 'רביעי', 'חמישי', 'שישי', 'שבת' );
			foreach ( $body['daily']['time'] as $i => $date ) {
				$dow    = (int) date( 'w', strtotime( $date ) );
				$days[] = array(
					'label' => $day_names[ $dow ],
					'code'  => $body['daily']['weathercode'][ $i ],
					'max'   => round( $body['daily']['temperature_2m_max'][ $i ] ),
					'min'   => round( $body['daily']['temperature_2m_min'][ $i ] ),
				);
			}
		}
	}

	if ( empty( $days ) ) {
		return array();
	}

	set_transient( $cache_key, $days, 30 * MINUTE_IN_SECONDS );
	return $days;
}

/** Map Open-Meteo WMO weather codes to a simple icon kind for our SVG symbol set. */
function lobby_screens_weather_icon_kind( $code ) {
	if ( in_array( (int) $code, array( 0, 1 ), true ) ) {
		return 'sun';
	}
	if ( in_array( (int) $code, array( 61, 63, 65, 80, 81, 82, 95 ), true ) ) {
		return 'rain';
	}
	return 'cloud';
}

/**
 * Hebcal — candle lighting, havdalah and the week's occasion for one city.
 *
 * BUG THIS REPLACES (found 9.9.2026 against live data): the previous version
 * looped every item and kept the LAST "candles" entry, assuming a week only
 * ever has one. On a yom tov week it has several. Rosh Hashana 5787 came back
 * as candles 18:31 (Fri) → candles 19:27 (Sat night, second night) → havdalah
 * 19:26 (Sun night), so the card printed "הדלקת נרות 19:27 / צאת השבת 19:26" —
 * an exit time a minute BEFORE the entry time. Now the first candle lighting
 * is taken, paired with the first havdalah at or after it, which is the
 * three-day block a resident actually needs.
 *
 * A parasha-less week is also normal (Rosh Hashana has no parashat item at
 * all). Rather than leave the card blank or, worse, keep showing last week's
 * parasha, the occasion falls back to the yom tov itself and the labels
 * change with it — so the card never claims a Shabbat that isn't there.
 *
 * Only the building's own city is fetched. The map is kept so a future
 * multi-city widget can ask for another one; fetching all five cost five
 * sequential HTTP calls on every cold cache for data nothing displayed.
 */
function lobby_screens_shabbat_cities() {
	return array(
		'jerusalem' => array( 'label' => 'י-ם', 'geonameid' => 281184 ),
		'telaviv'   => array( 'label' => 'ת״א', 'geonameid' => 293397 ),
		'haifa'     => array( 'label' => 'חיפה', 'geonameid' => 294801 ),
		'beersheva' => array( 'label' => 'ב״ש', 'geonameid' => 295530 ),
		'eilat'     => array( 'label' => 'אילת', 'geonameid' => 295277 ),
	);
}

function lobby_screens_get_shabbat_times( $city_key = 'beersheva' ) {
	$cities = lobby_screens_shabbat_cities();
	if ( ! isset( $cities[ $city_key ] ) ) {
		return null;
	}
	$city = $cities[ $city_key ];

	$cache_key = 'lobby_shabbat_v7_' . $city_key;
	$cached    = get_transient( $cache_key );
	if ( false !== $cached ) {
		return $cached;
	}

	$url      = "https://www.hebcal.com/shabbat?cfg=json&geonameid={$city['geonameid']}&M=on";
	$response = wp_remote_get( $url, array( 'timeout' => 8 ) );
	if ( is_wp_error( $response ) ) {
		return null;
	}

	$body  = json_decode( wp_remote_retrieve_body( $response ), true );
	$items = $body['items'] ?? array();
	if ( empty( $items ) ) {
		return null;
	}

	$candle    = '';
	$candle_ts = 0;
	$havdalah  = '';
	$parasha   = '';
	$holiday   = '';   // any holiday in the window, e.g. "ערב ראש השנה"
	$yomtov    = '';   // the actual chag — preferred, since Erev comes first
	$is_yomtov = false;

	foreach ( $items as $item ) {
		$ts = isset( $item['date'] ) ? strtotime( $item['date'] ) : 0;

		if ( 'candles' === $item['category'] && '' === $candle ) {
			$candle    = substr( $item['title'], -5 );
			$candle_ts = $ts;
		} elseif ( 'havdalah' === $item['category'] && '' === $havdalah && $ts >= $candle_ts ) {
			$havdalah = substr( $item['title'], -5 );
		} elseif ( 'parashat' === $item['category'] && '' === $parasha ) {
			$parasha = $item['hebrew'] ?? '';
		} elseif ( 'holiday' === $item['category'] ) {
			// the feed lists "ערב ראש השנה" before "ראש השנה", and the eve is
			// not the occasion a resident is looking for — keep both and let
			// the real yom tov win.
			if ( ! empty( $item['yomtov'] ) ) {
				$is_yomtov = true;
				if ( '' === $yomtov ) {
					$yomtov = $item['hebrew'] ?? '';
				}
			} elseif ( '' === $holiday ) {
				$holiday = $item['hebrew'] ?? '';
			}
		}
	}

	if ( '' === $candle ) {
		return null;
	}

	$occasion = $parasha ? $parasha : ( $yomtov ? $yomtov : $holiday );

	$result = array(
		'label'          => $city['label'],
		'primary'        => array( 'candle' => $candle, 'havdalah' => $havdalah ),
		'occasion'       => $occasion,
		'occasion_label' => $parasha ? 'פרשת השבוע' : 'המועד הקרוב',
		'end_label'      => $is_yomtov ? 'צאת החג' : 'צאת השבת',
		'greeting'       => $is_yomtov ? 'חג שמח ומבורך' : 'שבת שלום ומבורך',
		// kept for callers that still expect the old shape
		'cities'         => array( array( 'label' => $city['label'], 'candle' => $candle, 'havdalah' => $havdalah ) ),
		'parasha'        => $parasha,
	);

	set_transient( $cache_key, $result, HOUR_IN_SECONDS );
	return $result;
}

/**
 * Hebrew Gregorian date string. WP's date_i18n() needs the site locale
 * switched to he_IL to localize day/month names — simpler to just hardcode
 * the two small lookup arrays for this single display line.
 */
function lobby_screens_hebrew_date() {
	$days   = array( 'ראשון', 'שני', 'שלישי', 'רביעי', 'חמישי', 'שישי', 'שבת' );
	$months = array( 'ינואר', 'פברואר', 'מרץ', 'אפריל', 'מאי', 'יוני', 'יולי', 'אוגוסט', 'ספטמבר', 'אוקטובר', 'נובמבר', 'דצמבר' );
	$day    = $days[ (int) current_time( 'w' ) ];
	$month  = $months[ (int) current_time( 'n' ) - 1 ];
	return sprintf( 'יום %s · %s ב%s %s', $day, current_time( 'j' ), $month, current_time( 'Y' ) );
}

/**
 * Ynet — full stories for the news panel: image, summary, publish time and a
 * Hebrew section label. Same curation as the ticker, but applied to the
 * summary text too, since the panel shows a paragraph and not just a headline.
 *
 * The feed carries no <enclosure>; the thumbnail lives inside the description
 * HTML, so the image is parsed out of it and the rest becomes the summary.
 */
function lobby_screens_get_ynet_stories( $limit = 4 ) {
	$cache_key = 'lobby_ynet_stories_' . (int) $limit;
	$cached    = get_transient( $cache_key );
	if ( false !== $cached ) {
		return $cached;
	}

	require_once ABSPATH . WPINC . '/feed.php';
	$feed = fetch_feed( 'https://www.ynet.co.il/Integration/StoryRss2.xml' );

	$stories = array();
	if ( ! is_wp_error( $feed ) ) {
		foreach ( $feed->get_items( 0, 30 ) as $item ) {
			if ( count( $stories ) >= $limit ) {
				break;
			}
			$title = html_entity_decode( $item->get_title(), ENT_QUOTES, 'UTF-8' );
			$raw   = $item->get_description();

			$image = '';
			if ( preg_match( '/<img[^>]+src=[\'"]([^\'"]+)[\'"]/i', $raw, $m ) ) {
				$image = $m[1];
			}
			// drop the thumbnail anchor block, keep the editorial text
			$summary = trim( wp_strip_all_tags( preg_replace( '#<div>.*?</div>#is', '', $raw ) ) );
			$summary = html_entity_decode( $summary, ENT_QUOTES, 'UTF-8' );

			if ( lobby_screens_is_blocked( $title ) || lobby_screens_is_blocked( $summary ) ) {
				continue;
			}

			$ts = $item->get_date( 'U' );
			$stories[] = array(
				'title'    => $title,
				'summary'  => $summary,
				'image'    => $image,
				'time'     => $ts ? wp_date( 'H:i', $ts ) : '',
				'category' => lobby_screens_ynet_section( $item->get_permalink() ),
			);
		}
	}

	if ( empty( $stories ) ) {
		return array();
	}

	set_transient( $cache_key, $stories, 10 * MINUTE_IN_SECONDS );
	return $stories;
}

/**
 * Ynet article URLs carry their section as the first path segment
 * (ynet.co.il/<section>/...). Map the ones that actually show up to a Hebrew
 * label for the category tag; anything unmapped falls back to "חדשות".
 */
function lobby_screens_ynet_section( $link ) {
	$map = array(
		'news'          => 'חדשות',
		'economy'       => 'כלכלה',
		'sport'         => 'ספורט',
		'health'        => 'בריאות',
		'entertainment' => 'תרבות',
		'digital'       => 'טכנולוגיה',
		'food'          => 'אוכל',
		'travel'        => 'תיירות',
		'cars'          => 'רכב',
		'weather'       => 'מזג אוויר',
		'science'       => 'מדע',
		'judaism'       => 'יהדות',
	);
	$path = wp_parse_url( (string) $link, PHP_URL_PATH );
	$seg  = strtok( ltrim( (string) $path, '/' ), '/' );
	return $map[ $seg ] ?? 'חדשות';
}

/**
 * Open-Meteo — the single current temperature shown next to the clock.
 * Separate call from the weekly forecast so the header can render even if
 * the daily endpoint is unavailable.
 */
function lobby_screens_get_current_weather() {
	$cache_key = 'lobby_current_weather';
	$cached    = get_transient( $cache_key );
	if ( false !== $cached ) {
		return $cached;
	}

	$url = add_query_arg(
		array(
			'latitude'       => 31.25,
			'longitude'      => 34.79,
			'current_weather' => 'true',
			'timezone'       => 'Asia/Jerusalem',
		),
		'https://api.open-meteo.com/v1/forecast'
	);

	$response = wp_remote_get( $url, array( 'timeout' => 8 ) );
	if ( is_wp_error( $response ) ) {
		return null;
	}

	$body = json_decode( wp_remote_retrieve_body( $response ), true );
	if ( empty( $body['current_weather'] ) ) {
		return null;
	}

	$now = array(
		'temp' => round( $body['current_weather']['temperature'] ),
		'kind' => lobby_screens_weather_icon_kind( $body['current_weather']['weathercode'] ),
	);

	set_transient( $cache_key, $now, 20 * MINUTE_IN_SECONDS );
	return $now;
}
