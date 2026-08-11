<?php
/**
 * The whole screen. One page, static presentation — no user interaction,
 * meant to run unattended on a 35-45" lobby TV. All data below is fetched
 * server-side on each load (see functions.php) so there's no CORS/CSP wall
 * like a client-side fetch would hit.
 *
 * v4 — "Otzma Premium Dark", built against Aviv's full written spec
 * (2026-08-11) and the real client brand (עוצמה ניהול ואחזקת מבנים,
 * assets in LOGO AND DESIGN/). Anti-dashboard: one rotating notice on a
 * big central stage, identity column on the right, quiet news footer.
 *
 * $lobby below is the seed of the future multi-tenant config: everything
 * a building would customize lives here, not scattered through markup.
 * The real system will load this per building; widgets get show/hide
 * flags here when that lands.
 */

$lobby = array(
	'company_slogan' => '"דרכינו היא עוצמתנו"',
	'building'       => 'נחל חבר 16-18',
	'city'           => 'באר שבע',
	// PLACEHOLDER notices — no real content supplied yet by the client.
	'notices'        => array(
		array(
			'title'  => 'ישיבת ועד בית ביום רביעי הקרוב בשעה 20:00',
			'detail' => 'הישיבה תתקיים בלובי הבניין. נוכחות כלל הדיירים חשובה.',
		),
		array(
			'title'  => 'נא לשמור על ניקיון חדר האשפה והחניון המשותף',
			'detail' => 'סביבה נקיה היא הבית של כולנו. תודה על שיתוף הפעולה.',
		),
		array(
			'title'  => 'עבודות תחזוקה במעלית הצפונית ביום שני',
			'detail' => 'בין השעות 09:00–13:00. נא להשתמש במעלית הדרומית.',
		),
	),
);

$one_headlines = lobby_screens_get_one_headlines( 8 );
$ynet_lines    = lobby_screens_get_ynet_headlines( 6 );
$weather       = lobby_screens_get_weekly_weather();
$shabbat       = lobby_screens_get_shabbat_times();

$ynet_track = '';
foreach ( array_merge( $ynet_lines, $ynet_lines ) as $line ) {
	$ynet_track .= '<span><b>מבזק</b>' . esc_html( $line ) . '</span>';
}

$theme_uri = get_stylesheet_directory_uri();
?>
<!doctype html>
<html <?php language_attributes(); ?>>
<head>
<meta charset="<?php bloginfo( 'charset' ); ?>">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title><?php bloginfo( 'name' ); ?></title>
<?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>

<svg width="0" height="0" style="position:absolute">
  <defs>
    <linearGradient id="sunGrad" x1="0" y1="0" x2="1" y2="1">
      <stop offset="0%" stop-color="#d8c793"/><stop offset="100%" stop-color="#9D9379"/>
    </linearGradient>
    <linearGradient id="cloudGrad" x1="0" y1="0" x2="0" y2="1">
      <stop offset="0%" stop-color="#d6d4cd"/><stop offset="100%" stop-color="#a3a19a"/>
    </linearGradient>
    <linearGradient id="rainGrad" x1="0" y1="0" x2="0" y2="1">
      <stop offset="0%" stop-color="#b0aca1"/><stop offset="100%" stop-color="#8a867c"/>
    </linearGradient>
    <symbol id="wxSun" viewBox="0 0 40 40">
      <circle cx="20" cy="20" r="10" fill="url(#sunGrad)"/>
      <g stroke="url(#sunGrad)" stroke-width="2.2" stroke-linecap="round">
        <path d="M20 3v4M20 33v4M37 20h-4M7 20H3M32 8l-2.8 2.8M10.8 29.2 8 32M32 32l-2.8-2.8M10.8 10.8 8 8"/>
      </g>
    </symbol>
    <symbol id="wxCloud" viewBox="0 0 40 40">
      <circle cx="24" cy="17" r="8.5" fill="url(#cloudGrad)"/>
      <circle cx="14" cy="20" r="7" fill="url(#cloudGrad)"/>
      <rect x="7" y="19" width="26" height="10" rx="5" fill="url(#cloudGrad)"/>
    </symbol>
    <symbol id="wxRain" viewBox="0 0 40 40">
      <circle cx="24" cy="13" r="7" fill="url(#cloudGrad)"/>
      <circle cx="14" cy="16" r="6" fill="url(#cloudGrad)"/>
      <rect x="7" y="15" width="26" height="9" rx="4.5" fill="url(#cloudGrad)"/>
      <g stroke="url(#rainGrad)" stroke-width="2" stroke-linecap="round">
        <path d="M14 28v5M22 28v5M30 28v5"/>
      </g>
    </symbol>
  </defs>
</svg>

<div class="bg-photo"></div>
<img class="ornament ornament-top" src="<?php echo esc_url( $theme_uri . '/assets/images/otzma-ornament.png' ); ?>" alt="">
<img class="ornament ornament-bottom" src="<?php echo esc_url( $theme_uri . '/assets/images/otzma-ornament.png' ); ?>" alt="">

<div class="frame" dir="rtl" lang="he">

  <!-- identity column: fixed info per spec §3 — company brand, building,
       clock/date, plus shabbat (the client's own sign opens with בס"ד) -->
  <aside class="identity">
    <img class="identity-logo" src="<?php echo esc_url( $theme_uri . '/assets/images/otzma-logo-dark.png' ); ?>" alt="עוצמה — ניהול ואחזקת מבנים">
    <div class="identity-slogan"><?php echo esc_html( $lobby['company_slogan'] ); ?></div>

    <div class="divider"><span class="divider-gem"></span></div>

    <h1 class="identity-building"><?php echo esc_html( $lobby['building'] ); ?></h1>
    <div class="identity-city"><?php echo esc_html( $lobby['city'] ); ?></div>

    <div class="identity-clock">
      <svg class="clock-analog" viewBox="0 0 100 100">
        <circle class="clock-analog-face" cx="50" cy="50" r="47"/>
        <?php for ( $i = 0; $i < 12; $i++ ) :
          $angle = $i * 30;
          $x1    = 50 + 41 * sin( deg2rad( $angle ) );
          $y1    = 50 - 41 * cos( deg2rad( $angle ) );
          $x2    = 50 + 45.5 * sin( deg2rad( $angle ) );
          $y2    = 50 - 45.5 * cos( deg2rad( $angle ) );
        ?>
          <line class="clock-analog-tick" x1="<?php echo esc_attr( round( $x1, 2 ) ); ?>" y1="<?php echo esc_attr( round( $y1, 2 ) ); ?>" x2="<?php echo esc_attr( round( $x2, 2 ) ); ?>" y2="<?php echo esc_attr( round( $y2, 2 ) ); ?>"/>
        <?php endfor; ?>
        <line class="clock-analog-hand clock-analog-hour" id="handHour" x1="50" y1="50" x2="50" y2="29"/>
        <line class="clock-analog-hand clock-analog-min" id="handMin" x1="50" y1="50" x2="50" y2="19"/>
        <line class="clock-analog-hand clock-analog-sec" id="handSec" x1="50" y1="50" x2="50" y2="15"/>
        <circle class="clock-analog-pin" cx="50" cy="50" r="2.6"/>
      </svg>
      <div class="clock-digital" id="clockTime">--:--</div>
      <div class="clock-date"><?php echo esc_html( lobby_screens_hebrew_date() ); ?></div>
    </div>

    <?php if ( $shabbat ) : ?>
    <div class="shabbat">
      <div class="divider"><span class="divider-gem"></span></div>
      <div class="shabbat-parasha"><?php echo esc_html( $shabbat['parasha'] ); ?></div>
      <div class="shabbat-times">
        <div class="shabbat-t">
          <span class="shabbat-t-label">כניסת שבת</span>
          <span class="shabbat-t-val"><?php echo esc_html( $shabbat['primary']['candle'] ?? '--:--' ); ?></span>
        </div>
        <div class="shabbat-t">
          <span class="shabbat-t-label">יציאת שבת</span>
          <span class="shabbat-t-val"><?php echo esc_html( $shabbat['primary']['havdalah'] ?? '--:--' ); ?></span>
        </div>
      </div>
    </div>
    <?php endif; ?>
  </aside>

  <!-- stage: ONE notice at a time, huge type, slow crossfade — the
       anti-dashboard heart of the design -->
  <main class="stage">
    <div class="stage-label">הודעות לדיירים</div>
    <div class="stage-viewport" id="stageViewport">
      <?php foreach ( $lobby['notices'] as $i => $n ) : ?>
      <div class="stage-item<?php echo 0 === $i ? ' is-active' : ''; ?>">
        <div class="stage-title"><?php echo esc_html( $n['title'] ); ?></div>
        <?php if ( ! empty( $n['detail'] ) ) : ?>
          <div class="stage-detail"><?php echo esc_html( $n['detail'] ); ?></div>
        <?php endif; ?>
      </div>
      <?php endforeach; ?>
    </div>
    <div class="stage-dots" id="stageDots">
      <?php foreach ( $lobby['notices'] as $i => $n ) : ?>
        <span class="stage-dot<?php echo 0 === $i ? ' is-active' : ''; ?>"></span>
      <?php endforeach; ?>
    </div>

    <!-- ONE sports strip — swapped with weather per Aviv (v4.1) -->
    <div class="one-strip">
      <img class="one-logo" src="<?php echo esc_url( $theme_uri . '/assets/images/one-logo.png' ); ?>" alt="ONE.CO.IL">
      <div class="one-rotator">
        <?php foreach ( $one_headlines as $i => $headline ) : ?>
          <div class="one-headline<?php echo 0 === $i ? ' is-active' : ''; ?>"><?php echo esc_html( $headline ); ?></div>
        <?php endforeach; ?>
      </div>
    </div>
  </main>

  <!-- news footer: compact weather row + ynet ticker, kept quiet -->
  <footer class="news">
    <div class="news-weather">
      <?php foreach ( $weather as $day ) :
        $kind    = lobby_screens_weather_icon_kind( $day['code'] );
        $icon_id = 'sun' === $kind ? 'wxSun' : ( 'rain' === $kind ? 'wxRain' : 'wxCloud' );
      ?>
      <div class="wx-col">
        <span class="wx-day"><?php echo esc_html( $day['label'] ); ?></span>
        <svg class="wx-icon"><use href="#<?php echo esc_attr( $icon_id ); ?>"/></svg>
        <span class="wx-max"><?php echo esc_html( $day['max'] ); ?>°</span>
        <span class="wx-min"><?php echo esc_html( $day['min'] ); ?>°</span>
      </div>
      <?php endforeach; ?>
    </div>
    <div class="news-ynet">
      <span class="ynet-chip"><img src="<?php echo esc_url( $theme_uri . '/assets/images/ynet-logo.png' ); ?>" alt="Ynet"></span>
      <div class="ticker-flow">
        <div class="ticker-track"><?php echo $ynet_track; ?></div>
      </div>
      <div class="news-brand">עוצמה · ניהול ואחזקת מבנים</div>
    </div>
  </footer>
</div>

<script>
function tick(){
  var d=new Date();
  var p=function(n){return String(n).padStart(2,'0');};
  var el=document.getElementById('clockTime');
  if(el) el.textContent=p(d.getHours())+':'+p(d.getMinutes());

  var h=d.getHours()%12, m=d.getMinutes(), s=d.getSeconds();
  var hourEl=document.getElementById('handHour');
  var minEl=document.getElementById('handMin');
  var secEl=document.getElementById('handSec');
  if(hourEl) hourEl.style.transform='rotate('+((h+m/60)*30)+'deg)';
  if(minEl) minEl.style.transform='rotate('+((m+s/60)*6)+'deg)';
  if(secEl) secEl.style.transform='rotate('+(s*6)+'deg)';
}
tick();setInterval(tick,1000);

/* slow rotators: one visible item at a time, long holds, gentle fades */
function rotate(itemSel,dotSel,holdMs){
  var items=document.querySelectorAll(itemSel);
  var dots=dotSel?document.querySelectorAll(dotSel):[];
  if(items.length<2) return;
  var i=0;
  setInterval(function(){
    items[i].classList.remove('is-active');
    if(dots[i]) dots[i].classList.remove('is-active');
    i=(i+1)%items.length;
    items[i].classList.add('is-active');
    if(dots[i]) dots[i].classList.add('is-active');
  },holdMs);
}
rotate('.stage-item','.stage-dot',11000);
rotate('.one-headline',null,8000);
</script>
<?php wp_footer(); ?>
</body>
</html>
