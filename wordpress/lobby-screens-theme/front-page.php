<?php
/**
 * The whole screen. One page, static presentation — no user interaction,
 * meant to run unattended on a 35-45" lobby TV. All data is fetched
 * server-side (see functions.php) so there's no CORS/CSP wall.
 *
 * v5 — "Otzma Modern" + component architecture (client spec §17-18).
 * Design: full-bleed building photo as the ambient material, floating
 * glass cards, thin/bold Heebo contrast, gold as a sparse accent —
 * grounded in modern smart-building / TV-interface references, NOT the
 * classic-hotel serif look of v4 (Aviv: "לא מתחבר לעיצוב").
 *
 * Architecture: every widget is an independent component under
 * template-parts/widgets/ (§18: ClockWidget, WeatherWidget, NewsWidget,
 * AnnouncementWidget, SportsWidget + stubs for Events/Image/Video).
 * This file only holds per-building config ($lobby) and zone composition.
 * Adding a widget = dropping a file in widgets/ and listing it in a zone.
 */

$lobby = array(
	'company'  => 'עוצמה · ניהול ואחזקת מבנים',
	'building' => 'נחל חבר 16-18',
	'city'     => 'באר שבע',
	// PLACEHOLDER notices — no real content supplied yet by the client.
	'notices'  => array(
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
	// zone composition — the seed of the future per-building widget
	// config (show/hide/order). Stub widgets (events/image/video) exist
	// under template-parts/widgets/ and activate by listing them here.
	'zones'    => array(
		'rail' => array( 'weather', 'shabbat', 'sports' ),
	),
);

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
      <stop offset="0%" stop-color="#e8d9a8"/><stop offset="100%" stop-color="#B9A87E"/>
    </linearGradient>
    <linearGradient id="cloudGrad" x1="0" y1="0" x2="0" y2="1">
      <stop offset="0%" stop-color="#e3e2dd"/><stop offset="100%" stop-color="#b0afa8"/>
    </linearGradient>
    <linearGradient id="rainGrad" x1="0" y1="0" x2="0" y2="1">
      <stop offset="0%" stop-color="#c0bdb2"/><stop offset="100%" stop-color="#94908a"/>
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

<div class="fit" id="fitCanvas">
  <!-- ambient background. muted+playsinline are required for autoplay to
       be allowed at all; the still frame stays as poster so the screen is
       never blank while the file buffers, and as the fallback if a player
       refuses to decode the video. -->
  <video class="bg-video" autoplay muted loop playsinline preload="auto"
    poster="<?php echo esc_url( $theme_uri . '/assets/images/lobby-bg-tower.jpg' ); ?>">
    <source src="<?php echo esc_url( $theme_uri . '/assets/images/lobby-bg.mp4' ); ?>" type="video/mp4">
  </video>
  <div class="bg-scrim"></div>

  <div class="frame" dir="rtl" lang="he">

    <header class="zone-top">
      <?php
      get_template_part( 'template-parts/widgets/identity', null, array(
        'building' => $lobby['building'],
        'city'     => $lobby['city'],
        'company'  => $lobby['company'],
      ) );
      get_template_part( 'template-parts/widgets/clock' );
      ?>
    </header>

    <main class="zone-hero">
      <?php
      get_template_part( 'template-parts/widgets/announcements', null, array(
        'notices' => $lobby['notices'],
      ) );
      ?>
    </main>

    <aside class="zone-rail">
      <?php foreach ( $lobby['zones']['rail'] as $widget ) : ?>
        <?php get_template_part( 'template-parts/widgets/' . $widget ); ?>
      <?php endforeach; ?>
    </aside>

    <footer class="zone-ticker">
      <?php get_template_part( 'template-parts/widgets/news-ticker' ); ?>
    </footer>

  </div>
</div><!-- /.fit -->

<script>
/* fit-to-screen: scale the fixed 1920x1080 canvas to the window, keeping
   the full frame visible on any monitor/TV (letterboxed when needed) */
function fitScreen(){
  var s=Math.min(window.innerWidth/1920, window.innerHeight/1080);
  document.getElementById('fitCanvas').style.transform=
    'translate(-50%,-50%) scale('+s+')';
}
window.addEventListener('resize',fitScreen);
fitScreen();

/* background video keep-alive. An unattended lobby screen has nobody to
   click "play": browsers can refuse the initial autoplay, and a long
   uptime can leave the element stalled after a tab throttle or a decode
   hiccup. Retry on the events that typically unblock it, plus a slow
   watchdog that restarts playback if the clock stops advancing. */
(function(){
  var v=document.querySelector('.bg-video');
  if(!v) return;
  var kick=function(){ var p=v.play(); if(p&&p.catch) p.catch(function(){}); };
  ['loadeddata','canplay','pause','stalled','suspend'].forEach(function(e){
    v.addEventListener(e,kick);
  });
  document.addEventListener('visibilitychange',function(){
    if(!document.hidden) kick();
  });
  kick();
  var last=-1;
  setInterval(function(){
    if(v.paused || v.currentTime===last) kick();
    last=v.currentTime;
  },5000);
})();

/* ClockWidget */
function tick(){
  var d=new Date();
  var p=function(n){return String(n).padStart(2,'0');};
  var el=document.getElementById('clockTime');
  if(el) el.textContent=p(d.getHours())+':'+p(d.getMinutes());
}
tick();setInterval(tick,1000);

/* shared rotator: one visible item at a time, long holds, gentle fades */
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
rotate('.w-ann-item','.w-ann-dot',11000);   /* AnnouncementWidget */
rotate('.w-sports-item',null,8000);          /* SportsWidget */
</script>
<?php wp_footer(); ?>
</body>
</html>
