<?php
/**
 * The whole screen. One page, passive presentation — no navigation, no
 * buttons, no interaction of any kind (brief §11). Meant to run unattended
 * for days on a lobby TV, so every external fetch happens server-side
 * (functions.php) and there is no CORS/CSP wall to work around.
 *
 * v7 — "Otzma Signage" (brief of 9.9.2026): a four-card dashboard over a
 * full-width Ynet ticker. This deliberately reverses the v5 brief's "not a
 * dashboard" rule; see the note at the top of style.css.
 *
 * v7.1 follows the client mockup (mockups/v7-client-mockup.png), which arrived
 * after the first pass and corrected two things the written brief got backwards:
 * the logo belongs on the RIGHT with the clock on the LEFT (§3 says the
 * opposite in words, but the mockup is RTL-correct), and the cards read right
 * to left.
 *
 * v7.2 (Aviv's sketch, mockups/v7-2-layout-sketch.png): the Ynet card is gone —
 * the ticker along the bottom already carries Ynet, so a whole card of it was
 * the same feed twice. What is left moves to two side columns with the middle
 * deliberately empty, so the background footage is a real part of the screen
 * rather than something buried under four panels.
 *
 * Architecture (brief §19): this file holds only the per-building config
 * ($lobby) and the zone composition. Every panel is an independent widget
 * under template-parts/widgets/, and the two news cards share one body
 * partial (template-parts/parts/feed-card.php) rather than duplicating it.
 * Swapping a panel for a given building = editing one line of $lobby.
 */

$lobby = array(
	'company'  => 'עוצמה · ניהול ואחזקת מבנים',
	'building' => 'נחל חבר 16-18',
	'city'     => 'באר שבע',
	// Hebcal city key — see lobby_screens_shabbat_cities()
	'shabbat_city' => 'beersheva',
	// the mockup sets the greeting on two lines rather than using the brief's
	// optional one-line motto
	'welcome'  => 'ברוכים הבאים',
	'motto'    => 'לבניין שלנו',

	// PLACEHOLDER notices. Wording is the client's own example text from the
	// brief; the dates were moved to the current week so the demo screen
	// doesn't advertise stale ones. Still waiting on real content.
	'notices'  => array(
		array(
			'title'    => 'תחזוקת מעליות',
			'detail'   => 'ביום שני הקרוב תתקיים בדיקה תקופתית במעליות הבניין.',
			'date'     => '14.09.2026',
			'icon'     => 'wrench',
			'priority' => true,
		),
		array(
			'title'  => 'הסדרי חניה',
			'detail' => 'לתשומת לב הדיירים — החל מה-1 באוקטובר יחול שינוי בהסדרי החניה.',
			'date'   => '11.09.2026',
			'icon'   => 'car',
		),
		array(
			'title'  => 'עבודות אינסטלציה',
			'detail' => 'ביום חמישי תבוצע עבודת תחזוקה במערכת המים בבניין.',
			'date'   => '10.09.2026',
			'icon'   => 'water',
		),
		array(
			'title'  => 'ערב דיירים',
			'detail' => 'ביום שלישי בשעה 19:30 יתקיים ערב דיירים בלובי הבניין.',
			'date'   => '08.09.2026',
			'icon'   => 'people',
		),
	),

	// The seed of the future per-building widget config (show/hide/order).
	// 'lead' is the full-height column on the reading side; 'stack' is the
	// shorter column on the far side, top to bottom. The empty middle between
	// them is the point of the layout, not a gap left over from it.
	'zones'    => array(
		'lead'  => array( 'residents-panel' ),
		'stack' => array( 'sports-panel', 'shabbat-panel' ),
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

<svg width="0" height="0" style="position:absolute" aria-hidden="true">
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
  <!-- ambient background. muted+playsinline are required for autoplay to be
       allowed at all; the still frame stays as poster so the screen is never
       blank while the file buffers, and as the fallback if a player refuses
       to decode the video. -->
  <video class="bg-video" autoplay muted loop playsinline preload="auto"
    poster="<?php echo esc_url( $theme_uri . '/assets/images/lobby-bg-tower.jpg' ); ?>">
    <source src="<?php echo esc_url( $theme_uri . '/assets/images/lobby-bg.mp4' ); ?>" type="video/mp4">
  </video>
  <div class="bg-scrim"></div>

  <div class="frame" dir="rtl" lang="he">

    <header class="zone-top">
      <!-- RTL: the first child lands on the right, where the mockup puts the
           logo and the greeting; the clock takes the far left. -->
      <div class="hdr-lead reveal">
        <?php
        get_template_part( 'template-parts/widgets/brand' );
        ?>
        <div class="hdr-divider"></div>
        <?php
        get_template_part( 'template-parts/widgets/welcome', null, array(
          'title' => $lobby['welcome'],
          'sub'   => $lobby['motto'],
        ) );
        ?>
      </div>
      <?php
      get_template_part( 'template-parts/widgets/clock-weather', null, array(
        'city' => $lobby['city'],
      ) );
      ?>
    </header>

    <main class="zone-grid">
      <?php foreach ( $lobby['zones']['lead'] as $panel ) : ?>
        <?php get_template_part( 'template-parts/widgets/' . $panel, null, $lobby ); ?>
      <?php endforeach; ?>

      <!-- the open middle: nothing here on purpose, so the footage shows -->
      <div class="zone-open" aria-hidden="true"></div>

      <div class="zone-stack">
        <?php foreach ( $lobby['zones']['stack'] as $panel ) : ?>
          <?php get_template_part( 'template-parts/widgets/' . $panel, null, $lobby ); ?>
        <?php endforeach; ?>
      </div>
    </main>

    <footer class="zone-ticker">
      <?php get_template_part( 'template-parts/widgets/ticker' ); ?>
    </footer>

  </div>

  <!-- brief §18: when the feeds can't be reached the screen keeps showing the
       last good render; this is the only hint that anything is stale. -->
  <div class="stale-badge" id="staleBadge">הנתונים מתעדכנים…</div>
</div><!-- /.fit -->

<script>
/* ---- fit-to-screen ----
   Scale the fixed 1920x1080 canvas to the window so the whole frame is
   visible on any monitor or TV (letterboxed when the aspect differs).
   This is what makes the design responsive without a second layout. */
function fitScreen(){
  var s=Math.min(window.innerWidth/1920, window.innerHeight/1080);
  document.getElementById('fitCanvas').style.transform=
    'translate(-50%,-50%) scale('+s+')';
}
window.addEventListener('resize',fitScreen);
fitScreen();

/* ---- background video keep-alive ----
   An unattended lobby screen has nobody to click "play": browsers can refuse
   the initial autoplay, and a long uptime can leave the element stalled after
   a tab throttle or a decode hiccup. Retry on the events that typically
   unblock it, plus a slow watchdog that restarts playback if the clock stops
   advancing. */
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

/* ---- ClockWidget ---- the only value rendered client-side */
function tick(){
  var d=new Date();
  var p=function(n){return String(n).padStart(2,'0');};
  var el=document.getElementById('clockTime');
  if(el) el.textContent=p(d.getHours())+':'+p(d.getMinutes());
}
tick();setInterval(tick,1000);

/* ---- rotators ----
   One generic driver for every crossfading block on the screen. A container
   marked data-rotator="<hold ms>" cycles .is-active across its element
   children; a container with a single child never animates, which is why the
   notices card sits still when a building has four notices or fewer. */
(function(){
  document.querySelectorAll('[data-rotator]').forEach(function(box){
    var items=box.children;
    if(items.length<2) return;
    var hold=parseInt(box.getAttribute('data-rotator'),10)||12000;
    var i=0;
    setInterval(function(){
      items[i].classList.remove('is-active');
      i=(i+1)%items.length;
      items[i].classList.add('is-active');
    },hold);
  });
})();

/* ---- data refresh (brief §17-18) ----
   The feeds are server-rendered, so the only way to get fresh news onto a
   screen that never gets touched is to reload the page. Two rules make that
   safe for an unattended display:
     1. never reload blind. If the probe fails the network is down and a
        reload would replace a good screen with a browser error page — the
        exact "broken screen" the brief rules out. Instead the last good
        render stays up and a small badge admits the data is stale.
     2. fade out first, so the refresh reads as a transition and not a flash.
   The interval is longer than the feed transients (10 min), so a reload
   always lands on data that has actually changed. */
(function(){
  var REFRESH=15*60*1000;
  var badge=document.getElementById('staleBadge');
  function stale(on){ if(badge) badge.classList.toggle('is-on',on); }

  function cycle(){
    if(!navigator.onLine){ stale(true); return; }
    fetch(window.location.href,{method:'HEAD',cache:'no-store'})
      .then(function(r){
        if(!r.ok) throw new Error('bad status');
        stale(false);
        document.body.classList.add('is-refreshing');
        setTimeout(function(){ window.location.reload(); },700);
      })
      .catch(function(){ stale(true); });
  }
  setInterval(cycle,REFRESH);
  window.addEventListener('online',function(){ stale(false); });
  window.addEventListener('offline',function(){ stale(true); });
})();
</script>
<?php wp_footer(); ?>
</body>
</html>
