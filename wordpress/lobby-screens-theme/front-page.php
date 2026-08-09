<?php
/**
 * The whole screen. One page, static presentation — no user interaction,
 * meant to run unattended on a 35-45" lobby TV. All data below is fetched
 * server-side on each load (see functions.php) so there's no CORS/CSP wall
 * like a client-side fetch would hit.
 *
 * Section order below follows the client's stated priority:
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
      <stop offset="0%" stop-color="#ffdd7a"/><stop offset="100%" stop-color="#f2a93c"/>
    </linearGradient>
    <linearGradient id="cloudGrad" x1="0" y1="0" x2="0" y2="1">
      <stop offset="0%" stop-color="#ffffff"/><stop offset="100%" stop-color="#e7ecf7"/>
    </linearGradient>
    <linearGradient id="rainGrad" x1="0" y1="0" x2="0" y2="1">
      <stop offset="0%" stop-color="#c7d0e0"/><stop offset="100%" stop-color="#9aa6bd"/>
    </linearGradient>
    <symbol id="wxSun" viewBox="0 0 40 40">
      <circle cx="20" cy="20" r="10" fill="url(#sunGrad)"/>
      <g stroke="url(#sunGrad)" stroke-width="2.2" stroke-linecap="round">
        <path d="M20 3v4M20 33v4M37 20h-4M7 20H3M32 8l-2.8 2.8M10.8 29.2 8 32M32 32l-2.8-2.8M10.8 10.8 8 8"/>
      </g>
    </symbol>
    <symbol id="wxCloud" viewBox="0 0 40 40">
      <circle cx="24" cy="17" r="8.5" fill="url(#cloudGrad)" stroke="#d8ddec" stroke-width="0.6"/>
      <circle cx="14" cy="20" r="7" fill="url(#cloudGrad)" stroke="#d8ddec" stroke-width="0.6"/>
      <rect x="7" y="19" width="26" height="10" rx="5" fill="url(#cloudGrad)" stroke="#d8ddec" stroke-width="0.6"/>
    </symbol>
    <symbol id="wxRain" viewBox="0 0 40 40">
      <circle cx="24" cy="13" r="7" fill="url(#cloudGrad)" stroke="#d8ddec" stroke-width="0.6"/>
      <circle cx="14" cy="16" r="6" fill="url(#cloudGrad)" stroke="#d8ddec" stroke-width="0.6"/>
      <rect x="7" y="15" width="26" height="9" rx="4.5" fill="url(#cloudGrad)" stroke="#d8ddec" stroke-width="0.6"/>
      <g stroke="url(#rainGrad)" stroke-width="2" stroke-linecap="round">
        <path d="M14 28v5M22 28v5M30 28v5"/>
      </g>
    </symbol>
  </defs>
</svg>

<div class="screen" dir="rtl" lang="he">
  <div class="main">

    <div class="sidebar">
      <!-- priority 6: building + clock -->
      <div class="sb-card sb-photo">
        <div class="bg"></div>
        <div class="scrim"></div>
        <div class="content">
          <div class="sb-head">
            <div class="sb-mark">מ</div>
            <div>
              <div class="sb-name">נחל חבר 16-18</div>
              <div class="sb-addr">באר שבע</div>
            </div>
          </div>
          <div class="sb-clock">
            <div class="sb-time" id="clockTime">--:--</div>
            <div class="sb-date" id="clockDate"><?php echo esc_html( date_i18n( 'l · j בF Y' ) ); ?></div>
          </div>
        </div>
      </div>

      <!-- priority 3: weekly weather -->
      <div class="sb-card sb-weather">
        <div class="wx-head">
          <span class="wx-title">מזג אוויר — באר שבע</span>
          <span class="wx-badge">שבוע קדימה</span>
        </div>
        <div class="wx-list">
          <?php if ( empty( $weather ) ) : ?>
            <div class="wx-row"><span class="wx-row-day">—</span><span>אין נתונים כרגע</span></div>
          <?php else : foreach ( $weather as $day ) :
            $kind = lobby_screens_weather_icon_kind( $day['code'] );
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

      <!-- priority 4: shabbat + multi-city (client-editable later — see README) -->
      <div class="sb-card shabbat-card">
        <div class="bg"></div>
        <div class="scrim"></div>
        <div class="content">
          <?php if ( $shabbat && $shabbat['primary'] ) : ?>
            <span class="shabbat-tag"><span class="flame"></span>כניסת שבת — באר שבע</span>
            <div class="shabbat-time"><?php echo esc_html( $shabbat['primary']['candle'] ); ?></div>
            <div class="shabbat-parasha-row">
              פרשת השבוע <b class="shabbat-parasha"><?php echo esc_html( $shabbat['parasha'] ); ?></b>
              · יציאת שבת <?php echo esc_html( $shabbat['primary']['havdalah'] ); ?>
            </div>
            <div class="city-row">
              <?php foreach ( $shabbat['cities'] as $city ) : ?>
                <div class="city-chip">
                  <span class="city-chip-k"><?php echo esc_html( $city['label'] ); ?></span>
                  <span class="city-chip-v"><?php echo esc_html( $city['candle'] ); ?></span>
                </div>
              <?php endforeach; ?>
            </div>
          <?php else : ?>
            <span class="shabbat-tag"><span class="flame"></span>כניסת שבת</span>
            <div class="shabbat-time">—</div>
          <?php endif; ?>
        </div>
      </div>
    </div>

    <!-- hero: priority 1 (sports) dominates the rotation, priority 5 (ad) gets one slot -->
    <div class="hero" id="hero">
      <?php
      $slide_index = 0;
      foreach ( $one_stories as $story ) :
      ?>
      <div class="slide <?php echo 0 === $slide_index ? 'active' : ''; ?>" data-slide="<?php echo esc_attr( $slide_index ); ?>">
        <?php if ( $story['image'] ) : ?>
          <div class="bg" style="background-image:url('<?php echo esc_url( $story['image'] ); ?>')"></div>
        <?php endif; ?>
        <div class="scrim"></div>
        <div class="content">
          <span class="slide-eyebrow">
            <img class="eyebrow-logo" src="<?php echo esc_url( get_stylesheet_directory_uri() . '/assets/images/one-logo.png' ); ?>" alt="ONE">
            עדכוני ספורט · ONE
          </span>
          <h2><?php echo esc_html( $story['title'] ); ?></h2>
        </div>
      </div>
      <?php $slide_index++; endforeach; ?>

      <!-- priority 5: business ad -->
      <div class="slide slide-ad" data-slide="<?php echo esc_attr( $slide_index ); ?>" style="background:linear-gradient(135deg,#1c2b4a,#0e1830);">
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
      <img class="ynet-logo" src="<?php echo esc_url( get_stylesheet_directory_uri() . '/assets/images/ynet-logo.png' ); ?>" alt="Ynet">
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
}
tick();setInterval(tick,15000);

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
