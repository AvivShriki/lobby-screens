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
	wp_enqueue_style( 'lobby-screens-style', get_stylesheet_uri(), array(), '1.0' );
}
add_action( 'wp_enqueue_scripts', 'lobby_screens_assets' );

/**
 * Ynet — main RSS feed. Curated: the raw feed on a heavy news day is mostly
 * crime/security/politics, not appropriate for a residential lobby ticker.
 * Filter out headlines matching hard-news keywords rather than showing
 * everything verbatim.
 */
function lobby_screens_get_ynet_headlines( $limit = 6 ) {
	$cache_key = 'lobby_ynet_headlines';
	$cached    = get_transient( $cache_key );
	if ( false !== $cached ) {
		return $cached;
	}

	require_once ABSPATH . WPINC . '/feed.php';
	$feed = fetch_feed( 'https://www.ynet.co.il/Integration/StoryRss2.xml' );

	$headlines = array();
	if ( ! is_wp_error( $feed ) ) {
		$blocklist = array(
			'נרצח', 'רצח', 'הצתה', 'אונס', 'תקיפה מינית', 'עבירות מין', 'עבירת מין', 'נחנק', 'דרס', 'טבח', 'הרג', 'נהרג',
			'מת ', 'מתה ', 'נספה', 'נספתה', 'אסון', 'תאונה קטלנית', 'קטלנית', 'פיגוע', 'חטיפה',
			'התאבדות', 'התאבד', 'גופה', 'גופת', 'נפטר', 'נפטרה', 'שכול', 'הלוויה', 'קבורה',
			'תקיפה', 'אלימות', 'התעללות', 'שוד', 'פריצה', 'מעצר', 'נעצר', 'חשד לרצח', 'קטין', 'קטינה', 'נערים', 'נערה', 'נער ', 'ילד ', 'ילדה ',
			'מלחמה', 'טילים', 'רקטות', 'פצועים', 'נפגעים', 'כטב״מ',
			'נפל אל מותו', 'נפלה אל מותה', 'ללא רוח חיים', 'איבד את חייו', 'איבדה את חייה', 'טבע', 'טבעה',
		);
		$items     = $feed->get_items( 0, 30 );
		foreach ( $items as $item ) {
			if ( count( $headlines ) >= $limit ) {
				break;
			}
			$title = $item->get_title();
			$skip  = false;
			foreach ( $blocklist as $word ) {
				if ( mb_strpos( $title, $word ) !== false ) {
					$skip = true;
					break;
				}
			}
			if ( ! $skip ) {
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
	$cache_key = 'lobby_one_headlines';
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
 * ONE.co.il — official RSS, includes a real image per story. No longer
 * used on the page itself (v3 dropped images from the ONE section per
 * client request) but kept since it may be useful again later.
 */
function lobby_screens_get_one_stories( $limit = 3 ) {
	$cache_key = 'lobby_one_stories';
	$cached    = get_transient( $cache_key );
	if ( false !== $cached ) {
		return $cached;
	}

	require_once ABSPATH . WPINC . '/feed.php';
	$feed = fetch_feed( 'https://www.one.co.il/rss' );

	$stories = array();
	if ( ! is_wp_error( $feed ) ) {
		$items = $feed->get_items( 0, $limit );
		foreach ( $items as $item ) {
			$image_url = '';
			// SimplePie doesn't parse media:content generically; pull it from the raw item XML.
			$enclosure = $item->get_enclosure();
			if ( $enclosure && $enclosure->get_link() ) {
				$image_url = $enclosure->get_link();
			}
			$stories[] = array(
				'title' => html_entity_decode( $item->get_title(), ENT_QUOTES, 'UTF-8' ),
				'image' => $image_url,
				'link'  => $item->get_permalink(),
			);
		}
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
 * Hebcal — real candle-lighting/havdalah/parasha data for five cities.
 * Marked in the brief as something the client will eventually edit directly —
 * this stays a live read for now, structured so a future admin-editable
 * override can slot in per city without changing the template.
 */
function lobby_screens_get_shabbat_times() {
	$cache_key = 'lobby_shabbat_times';
	$cached    = get_transient( $cache_key );
	if ( false !== $cached ) {
		return $cached;
	}

	$cities = array(
		'jerusalem' => array( 'label' => 'י-ם', 'geonameid' => 281184 ),
		'telaviv'   => array( 'label' => 'ת״א', 'geonameid' => 293397 ),
		'haifa'     => array( 'label' => 'חיפה', 'geonameid' => 294801 ),
		'beersheva' => array( 'label' => 'ב״ש', 'geonameid' => 295530 ),
		'eilat'     => array( 'label' => 'אילת', 'geonameid' => 295277 ),
	);

	$result = array( 'cities' => array(), 'parasha' => '', 'primary' => null );

	foreach ( $cities as $key => $city ) {
		$url      = "https://www.hebcal.com/shabbat?cfg=json&geonameid={$city['geonameid']}&M=on";
		$response = wp_remote_get( $url, array( 'timeout' => 8 ) );
		if ( is_wp_error( $response ) ) {
			continue;
		}
		$body = json_decode( wp_remote_retrieve_body( $response ), true );
		$candle = '';
		$havdalah = '';
		$parasha = '';
		foreach ( $body['items'] ?? array() as $item ) {
			if ( 'candles' === $item['category'] ) {
				$candle = substr( $item['title'], -5 );
			} elseif ( 'havdalah' === $item['category'] ) {
				$havdalah = substr( $item['title'], -5 );
			} elseif ( 'parashat' === $item['category'] ) {
				$parasha = $item['hebrew'] ?? '';
			}
		}
		$result['cities'][] = array(
			'label'    => $city['label'],
			'candle'   => $candle,
			'havdalah' => $havdalah,
		);
		if ( 'beersheva' === $key ) {
			$result['primary'] = array( 'candle' => $candle, 'havdalah' => $havdalah );
		}
		if ( empty( $result['parasha'] ) && $parasha ) {
			$result['parasha'] = $parasha;
		}
	}

	if ( empty( $result['cities'] ) ) {
		return null;
	}

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
