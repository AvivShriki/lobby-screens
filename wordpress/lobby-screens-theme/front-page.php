<?php
/**
 * The whole screen. One page, static presentation — no user interaction,
 * meant to run unattended on a 35-45" lobby TV. All data below is fetched
 * server-side on each load (see functions.php) so there's no CORS/CSP wall
 * like a client-side fetch would hit.
 *
 * v3 — client asked to move away from the green/navy palette entirely
 * (now bronze/glass over the building photo), moved ONE to the left
 * column as a text-only auto-scrolling headline list (logo + no images),
 * and put "הודעות לדיירים" (building notices — placeholder content, see
 * README) in the center where the ONE carousel used to be.
 *
 * Section order follows the client's stated priority:
 * 1) Sports (ONE)  2) Ynet ticker  3) Weekly weather  4) Shabbat times
 * 5) Business ad   6) Clock/date
 */

$one_headlines = lobby_screens_get_one_headlines( 10 );
$ynet_lines    = lobby_screens_get_ynet_headlines( 6 );
$weather       = lobby_screens_get_weekly_weather();
$shabbat       = lobby_screens_get_shabbat_times();

$ynet_track = '';
foreach ( array_merge( $ynet_lines, $ynet_lines ) as $i => $line ) {
	$tag         = ( 0 === $i % 2 ) ? 'מבזק' : 'Ynet';
	$ynet_track .= '<span><b>' . esc_html( $tag ) . ':</b>' . esc_html( $line ) . '</span>';
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
    <symbol id="icoThermo" viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
      <path d="M12 14.76V3.5a2.5 2.5 0 0 0-5 0v11.26a4.5 4.5 0 1 0 5 0Z"/>
    </symbol>
    <symbol id="icoCandle" viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
      <path d="M12 2c1.5 2 1.5 3.2 0 5-1.5-1.8-1.5-3 0-5Z" fill="var(--warm)" stroke="none"/>
      <rect x="9.5" y="8" width="5" height="10" rx="1"/>
      <path d="M6 21h12"/>
    </symbol>
    <symbol id="icoMega" viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
      <path d="M3 11v2a2 2 0 0 0 2 2h1l4 4v-6M3 11l7-4v10M3 11h1M16 8a4 4 0 0 1 0 8M19 5a8 8 0 0 1 0 14"/>
    </symbol>
    <symbol id="icoNote" viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
      <path d="M9 18V5l11-2v13"/>
      <circle cx="6" cy="18" r="3"/>
      <circle cx="17" cy="16" r="3"/>
    </symbol>
    <linearGradient id="sunGrad" x1="0" y1="0" x2="1" y2="1">
      <stop offset="0%" stop-color="#ffdd7a"/><stop offset="100%" stop-color="#f2a93c"/>
    </linearGradient>
    <linearGradient id="cloudGrad" x1="0" y1="0" x2="0" y2="1">
      <stop offset="0%" stop-color="#e4eaf5"/><stop offset="100%" stop-color="#b3c0d9"/>
    </linearGradient>
    <linearGradient id="rainGrad" x1="0" y1="0" x2="0" y2="1">
      <stop offset="0%" stop-color="#8f9db8"/><stop offset="100%" stop-color="#6b7a99"/>
    </linearGradient>
    <symbol id="wxSun" viewBox="0 0 40 40">
      <circle cx="20" cy="20" r="10" fill="url(#sunGrad)"/>
      <g stroke="url(#sunGrad)" stroke-width="2.2" stroke-linecap="round">
        <path d="M20 3v4M20 33v4M37 20h-4M7 20H3M32 8l-2.8 2.8M10.8 29.2 8 32M32 32l-2.8-2.8M10.8 10.8 8 8"/>
      </g>
    </symbol>
    <symbol id="wxCloud" viewBox="0 0 40 40">
      <circle cx="24" cy="17" r="8.5" fill="url(#cloudGrad)" stroke="#9aa8c4" stroke-width="0.6"/>
      <circle cx="14" cy="20" r="7" fill="url(#cloudGrad)" stroke="#9aa8c4" stroke-width="0.6"/>
      <rect x="7" y="19" width="26" height="10" rx="5" fill="url(#cloudGrad)" stroke="#9aa8c4" stroke-width="0.6"/>
    </symbol>
    <symbol id="wxRain" viewBox="0 0 40 40">
      <circle cx="24" cy="13" r="7" fill="url(#cloudGrad)" stroke="#9aa8c4" stroke-width="0.6"/>
      <circle cx="14" cy="16" r="6" fill="url(#cloudGrad)" stroke="#9aa8c4" stroke-width="0.6"/>
      <rect x="7" y="15" width="26" height="9" rx="4.5" fill="url(#cloudGrad)" stroke="#9aa8c4" stroke-width="0.6"/>
      <g stroke="url(#rainGrad)" stroke-width="2" stroke-linecap="round">
        <path d="M14 28v5M22 28v5M30 28v5"/>
      </g>
    </symbol>
  </defs>
</svg>

<div class="screen" dir="rtl" lang="he">

  <!-- priority 6: identity + clock, always visible -->
  <div class="topbar">
    <div class="tb-brand">
      <div class="tb-mark">מ</div>
      <div>
        <div class="tb-name">נחל חבר 16-18</div>
        <div class="tb-addr">באר שבע</div>
      </div>
    </div>
    <div class="tb-clock">
      <svg class="tb-analog" viewBox="0 0 100 100">
        <circle class="tb-analog-face" cx="50" cy="50" r="46"/>
        <?php for ( $i = 0; $i < 12; $i++ ) :
          $angle = $i * 30;
          $x1    = 50 + 40 * sin( deg2rad( $angle ) );
          $y1    = 50 - 40 * cos( deg2rad( $angle ) );
          $x2    = 50 + 45 * sin( deg2rad( $angle ) );
          $y2    = 50 - 45 * cos( deg2rad( $angle ) );
        ?>
          <line class="tb-analog-tick" x1="<?php echo esc_attr( $x1 ); ?>" y1="<?php echo esc_attr( $y1 ); ?>" x2="<?php echo esc_attr( $x2 ); ?>" y2="<?php echo esc_attr( $y2 ); ?>"/>
        <?php endfor; ?>
        <line class="tb-analog-hand tb-analog-hour" id="handHour" x1="50" y1="50" x2="50" y2="28"/>
        <line class="tb-analog-hand tb-analog-min" id="handMin" x1="50" y1="50" x2="50" y2="18"/>
        <line class="tb-analog-hand tb-analog-sec" id="handSec" x1="50" y1="50" x2="50" y2="14"/>
        <circle class="tb-analog-pin" cx="50" cy="50" r="3"/>
      </svg>
      <div class="tb-digital">
        <div class="tb-time" id="clockTime">--:--</div>
        <div class="tb-date"><?php echo esc_html( lobby_screens_hebrew_date() ); ?></div>
      </div>
    </div>
  </div>

  <div class="main">
    <div class="sidebar-right">

      <!-- priority 3: weekly weather -->
      <div class="pcard weather-card">
        <div class="pill-head">
          <svg class="pill-icon"><use href="#icoThermo"/></svg>
          מזג אוויר
          <span class="pill-badge">שבוע קדימה</span>
        </div>
        <div class="pcard-body">
          <div class="wx-strip">
          <?php if ( empty( $weather ) ) : ?>
            <span>אין נתונים כרגע</span>
          <?php else : foreach ( $weather as $day ) :
            $kind    = lobby_screens_weather_icon_kind( $day['code'] );
            $icon_id = 'sun' === $kind ? 'wxSun' : ( 'rain' === $kind ? 'wxRain' : 'wxCloud' );
          ?>
            <div class="wx-col">
              <span class="wx-col-day"><?php echo esc_html( $day['label'] ); ?></span>
              <svg class="wx-col-icon"><use href="#<?php echo esc_attr( $icon_id ); ?>"/></svg>
              <span class="wx-col-max"><?php echo esc_html( $day['max'] ); ?>°</span>
              <span class="wx-col-min"><?php echo esc_html( $day['min'] ); ?>°</span>
            </div>
          <?php endforeach; endif; ?>
          </div>
        </div>
      </div>

      <!-- priority 4: shabbat — client-supplied weekly graphic (see README:
           this is swapped by hand for now; an upload UI is a planned future
           step, not built yet) -->
      <div class="pcard shabbat-card">
        <div class="pill-head">
          <svg class="pill-icon"><use href="#icoCandle"/></svg>
          כניסת שבת
        </div>
        <div class="pcard-body">
          <img class="shabbat-graphic" src="<?php echo esc_url( $theme_uri . '/assets/images/shabbat-times-graphic.jpg' ); ?>" alt="זמני כניסת ויציאת שבת, פרשת <?php echo esc_attr( $shabbat['parasha'] ?? '' ); ?>">
        </div>
      </div>
    </div>

    <!-- center: building notices — replaces the ONE carousel that used to
         live here. PLACEHOLDER TEXT: no real notices supplied yet, do not
         treat these as real content. -->
    <div class="pcard notices-card">
      <div class="pill-head">
        <svg class="pill-icon"><use href="#icoMega"/></svg>
        הודעות לדיירים
      </div>
      <div class="pcard-body">
        <div class="notice-row">
          <span class="notice-dot"></span>
          <span class="notice-text"><b>ישיבת ועד בית</b> תתקיים ביום רביעי הקרוב בשעה 20:00, בלובי הבניין</span>
        </div>
        <div class="notice-row">
          <span class="notice-dot"></span>
          <span class="notice-text">נא לשמור על ניקיון וסדר בחדר האשפה ובחניון המשותף</span>
        </div>
        <div class="notice-row">
          <span class="notice-dot"></span>
          <span class="notice-text">עבודות תחזוקה במעלית הצפונית ביום שני, 09:00–13:00 — נא להשתמש במעלית הדרומית</span>
        </div>
      </div>
    </div>

    <div class="sidebar-left">
      <!-- priority 1: ONE headlines — text-only per client request, large
           scrolling headline text, ONE logo as a small side mark (not the
           hero element), vertical auto-scroll. -->
      <div class="pcard one-card">
        <div class="pill-head">
          <span>חדשות ספורט ONE</span>
          <img class="one-logo-side" src="<?php echo esc_url( $theme_uri . '/assets/images/one-logo.png' ); ?>" alt="ONE.CO.IL">
        </div>
        <div class="pcard-body">
          <div class="one-ticker">
            <div class="one-ticker-track">
              <?php foreach ( array_merge( $one_headlines, $one_headlines ) as $headline ) : ?>
                <div class="one-item"><?php echo esc_html( $headline ); ?></div>
              <?php endforeach; ?>
            </div>
          </div>
        </div>
      </div>

      <!-- background music player -->
      <div class="pcard player-card">
        <div class="pill-head">
          <svg class="pill-icon"><use href="#icoNote"/></svg>
          מוזיקת רקע
        </div>
        <div class="pcard-body">
          <iframe class="player-frame"
            src="https://www.youtube-nocookie.com/embed/eaqW5eQXTdM?autoplay=1&mute=1&loop=1&playlist=eaqW5eQXTdM&controls=1&modestbranding=1&rel=0"
            title="נגן מוזיקת רקע"
            allow="autoplay; encrypted-media"
            allowfullscreen></iframe>
        </div>
      </div>
    </div>
  </div>

  <!-- priority 2: ynet ticker -->
  <div class="ticker-bar">
    <div class="ynet-block">
      <img class="ynet-logo" src="<?php echo esc_url( $theme_uri . '/assets/images/ynet-logo.png' ); ?>" alt="Ynet">
      <span class="live-wrap"><span class="live-dot"></span>מתעדכן אוטומטית</span>
    </div>
    <div class="ticker-sep"></div>
    <div class="ticker-flow">
      <div class="ticker-flow-track"><?php echo $ynet_track; ?></div>
    </div>
    <div class="ticker-sep"></div>
    <div class="brand-block">
      <div class="brand-dot">DL</div>
      <div class="brand-text">
        <b>Digital Lobby</b>
        <span>התקנה ותמיכה · 05X-XXX-XXXX</span>
      </div>
    </div>
  </div>
</div>

<script>
function tick(){
  var d=new Date();
  var p=function(n){return String(n).padStart(2,'0');};
  var el=document.getElementById('clockTime');
  if(el) el.textContent=p(d.getHours())+':'+p(d.getMinutes());

  var h=d.getHours()%12, m=d.getMinutes(), s=d.getSeconds();
  var hourDeg=(h+m/60)*30;
  var minDeg=(m+s/60)*6;
  var secDeg=s*6;
  var hourEl=document.getElementById('handHour');
  var minEl=document.getElementById('handMin');
  var secEl=document.getElementById('handSec');
  if(hourEl) hourEl.style.transform='rotate('+hourDeg+'deg)';
  if(minEl) minEl.style.transform='rotate('+minDeg+'deg)';
  if(secEl) secEl.style.transform='rotate('+secDeg+'deg)';
}
tick();setInterval(tick,1000);
</script>
<?php wp_footer(); ?>
</body>
</html>
