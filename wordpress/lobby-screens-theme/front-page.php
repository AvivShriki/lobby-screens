<?php
/**
 * The whole screen. One page, static presentation — no user interaction,
 * meant to run unattended on a 35-45" lobby TV. All data below is fetched
 * server-side on each load (see functions.php) so there's no CORS/CSP wall
 * like a client-side fetch would hit.
 *
 * v2 — restructured around the client's own reference examples (see
 * examples/ and the README design-decision log): pill-header + white-card
 * for every sidebar module, dark navy top bar for identity/clock, full-bleed
 * photo only in the rotating hero.
 *
 * Section order follows the client's stated priority:
 * 1) Sports (ONE)  2) Ynet ticker  3) Weekly weather  4) Shabbat times
 * 5) Business ad   6) Clock/date
 */

$one_stories = lobby_screens_get_one_stories( 3 );
$ynet_lines  = lobby_screens_get_ynet_headlines( 6 );
$weather     = lobby_screens_get_weekly_weather();
$shabbat     = lobby_screens_get_shabbat_times();

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
      <path d="M12 2c1.5 2 1.5 3.2 0 5-1.5-1.8-1.5-3 0-5Z" fill="#fff"/>
      <rect x="9.5" y="8" width="5" height="10" rx="1"/>
      <path d="M6 21h12"/>
    </symbol>
    <symbol id="icoBall" viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
      <circle cx="12" cy="12" r="9"/>
      <path d="M12 3v18M3 12h18M5.6 5.6l12.8 12.8M18.4 5.6 5.6 18.4"/>
    </symbol>
    <symbol id="icoMega" viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
      <path d="M3 11v2a2 2 0 0 0 2 2h1l4 4v-6M3 11l7-4v10M3 11h1M16 8a4 4 0 0 1 0 8M19 5a8 8 0 0 1 0 14"/>
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
    <div class="sidebar">

      <!-- priority 3: weekly weather -->
      <div class="pcard weather-card">
        <div class="pill-head">
          <svg class="pill-icon"><use href="#icoThermo"/></svg>
          מזג אוויר
          <span class="pill-badge">שבוע קדימה</span>
        </div>
        <div class="pcard-body">
          <?php if ( empty( $weather ) ) : ?>
            <div class="wx-row"><span class="wx-row-day">—</span><span>אין נתונים כרגע</span></div>
          <?php else : foreach ( $weather as $day ) :
            $kind    = lobby_screens_weather_icon_kind( $day['code'] );
            $icon_id = 'sun' === $kind ? 'wxSun' : ( 'rain' === $kind ? 'wxRain' : 'wxCloud' );
          ?>
            <div class="wx-row">
              <span class="wx-row-day"><?php echo esc_html( $day['label'] ); ?></span>
              <svg class="wx-row-icon"><use href="#<?php echo esc_attr( $icon_id ); ?>"/></svg>
              <span class="wx-row-temps">
                <span class="wx-row-max"><?php echo esc_html( $day['max'] ); ?>°</span>
                <span class="wx-row-min"><?php echo esc_html( $day['min'] ); ?>°</span>
              </span>
            </div>
          <?php endforeach; endif; ?>
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

    <!-- hero: priority 1 (sports) dominates the rotation; welcome + ad get one slot each -->
    <div class="hero" id="hero">
      <div class="slide slide-welcome active" data-slide="0">
        <div class="bg" style="background-image:url('<?php echo esc_url( $theme_uri . '/assets/images/candles-pixabay.jpg' ); ?>')"></div>
        <div class="scrim"></div>
        <div class="content">
          <span class="slide-eyebrow"><svg class="eyebrow-icon"><use href="#icoCandle"/></svg>ברוכים הבאים</span>
          <h2>שבת שלום לדיירי נחל חבר 16-18</h2>
          <?php if ( $shabbat && $shabbat['primary'] ) : ?>
            <p>הדלקת נרות היום ב־<?php echo esc_html( $shabbat['primary']['candle'] ); ?> · שבת נעימה ומבורכת לכל המשפחות בבניין</p>
          <?php endif; ?>
        </div>
      </div>

      <?php
      $slide_index = 1;
      foreach ( $one_stories as $story ) :
      ?>
      <div class="slide" data-slide="<?php echo esc_attr( $slide_index ); ?>">
        <?php if ( $story['image'] ) : ?>
          <div class="bg" style="background-image:url('<?php echo esc_url( $story['image'] ); ?>')"></div>
        <?php endif; ?>
        <div class="scrim"></div>
        <div class="content">
          <span class="slide-eyebrow">
            <img class="eyebrow-logo" src="<?php echo esc_url( $theme_uri . '/assets/images/one-logo.png' ); ?>" alt="ONE">
            עדכוני ספורט · ONE
          </span>
          <h2><?php echo esc_html( $story['title'] ); ?></h2>
        </div>
      </div>
      <?php $slide_index++; endforeach; ?>

      <!-- priority 5: business ad -->
      <div class="slide slide-ad" data-slide="<?php echo esc_attr( $slide_index ); ?>" style="background:linear-gradient(135deg,#16a06b,#0d8058);">
        <div class="scrim"></div>
        <div class="content">
          <span class="ad-label">פרסומת</span>
          <h2>בית הפול, באר שבע</h2>
          <p>שולחנות פול, בר משקאות ואווירה — הבילוי הבא שלכם בשכונה</p>
        </div>
      </div>

      <div class="dots" id="dots">
        <?php for ( $i = 0; $i <= $slide_index; $i++ ) : ?>
          <span class="dot <?php echo 0 === $i ? 'on' : ''; ?>"></span>
        <?php endfor; ?>
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

(function(){
  var slides=document.querySelectorAll('.slide');
  var dots=document.querySelectorAll('.dot');
  var i=0;
  var reduce=window.matchMedia('(prefers-reduced-motion: reduce)').matches;
  if(reduce || slides.length<2) return;
  setInterval(function(){
    slides[i].classList.remove('active');
    dots[i].classList.remove('on');
    i=(i+1)%slides.length;
    slides[i].classList.add('active');
    dots[i].classList.add('on');
  },7000);
})();
</script>
<?php wp_footer(); ?>
</body>
</html>
