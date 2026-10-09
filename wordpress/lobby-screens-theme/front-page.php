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
	// brief; the dates are kept in the coming week so the demo screen doesn't
	// advertise ones that have already passed — they had drifted a month behind
	// and were refreshed on 9.10.2026, and each now falls on the weekday its own
	// text names. Still waiting on real content.
	'notices'  => array(
		array(
			'title'    => 'תחזוקת מעליות',
			'detail'   => 'ביום שני הקרוב תתקיים בדיקה תקופתית במעליות הבניין.',
			'date'     => '12.10.2026',
			'icon'     => 'wrench',
			'priority' => true,
		),
		array(
			'title'  => 'הסדרי חניה',
			'detail' => 'לתשומת לב הדיירים — החל מה-1 בנובמבר יחול שינוי בהסדרי החניה.',
			'date'   => '20.10.2026',
			'icon'   => 'car',
		),
		array(
			'title'  => 'עבודות אינסטלציה',
			'detail' => 'ביום חמישי תבוצע עבודת תחזוקה במערכת המים בבניין.',
			'date'   => '15.10.2026',
			'icon'   => 'water',
		),
		array(
			'title'  => 'ערב דיירים',
			'detail' => 'ביום שלישי בשעה 19:30 יתקיים ערב דיירים בלובי הבניין.',
			'date'   => '13.10.2026',
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
<?php /* Not bloginfo('name'): a fresh Playground install is called "My WordPress
         Website", and that string was reaching the published snapshot as the
         browser-tab title. The building's own identity is the right title and
         it already lives in $lobby. */ ?>
<title><?php echo esc_html( $lobby['company'] . ' · ' . $lobby['building'] ); ?></title>
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
  <!-- ambient background, built from the client's own brand material
       instead of stock footage: their charcoal, their champagne gold, and
       the topographic contour field lifted from their printed signage.
       Nothing here is licensed from a third party, nothing gets upscaled
       badly, and the luminance is fully under our control — which is what
       finally lets the cards be properly transparent (see style.css). -->
  <div class="bg-base"></div>
  <img class="bg-contours bg-contours-a" src="<?php echo esc_url( $theme_uri . '/assets/images/otzma-contours.png' ); ?>" alt="">
  <img class="bg-contours bg-contours-b" src="<?php echo esc_url( $theme_uri . '/assets/images/otzma-contours.png' ); ?>" alt="">
  <img class="bg-watermark" src="<?php echo esc_url( $theme_uri . '/assets/images/otzma-mark.png' ); ?>" alt="">
  <div class="bg-scrim"></div>

  <div class="frame" dir="rtl" lang="he">

    <!-- v8.1 — the header rail.
         Until now this was two groups pinned to the two edges: logo + greeting
         on the right, clock on the left, and roughly 990px of nothing between
         them. That emptiness was deliberate in v7.2 — a video played behind the
         header and the gap was the window you saw it through. v8 removed the
         clip, and what had been a window became a hole.

         So the header's own content is redistributed across the full width as
         cells sharing two horizontal bands (see .hdr-cell in style.css).

         v8.1.1 — Aviv cut the building-name cell and asked for everything left
         to be bigger. Both halves of that are one decision: four cells instead
         of five frees ~260px of width, which is what pays for the type going up
         roughly a third. The screen is read standing in a lobby, from metres
         away, with nobody to lean in — the 19px second lines were decoration at
         that distance. The city moved onto the weather line, since it left the
         screen with the building cell and the temperature needs it.

         RTL: the first child lands on the right, so the logo still holds the
         reading side and the clock still holds the far side, exactly as the
         client mockup stages them. -->
    <header class="zone-top">

      <div class="hdr-cell hdr-brand reveal"><?php get_template_part( 'template-parts/widgets/brand' ); ?></div>

      <div class="hdr-rule" aria-hidden="true"></div>

      <div class="hdr-cell hdr-greet reveal"><?php get_template_part( 'template-parts/widgets/welcome', null, array(
        'title' => $lobby['welcome'],
        'sub'   => $lobby['motto'],
      ) ); ?></div>

      <div class="hdr-rule" aria-hidden="true"></div>

      <?php $geo = lobby_screens_weather_coords(); ?>
      <div class="hdr-cell hdr-wx reveal"
           data-lat="<?php echo esc_attr( $geo['lat'] ); ?>"
           data-lon="<?php echo esc_attr( $geo['lon'] ); ?>"
           data-tz="<?php echo esc_attr( $geo['tz'] ); ?>"
           data-city="<?php echo esc_attr( $lobby['city'] ); ?>"><?php get_template_part( 'template-parts/widgets/weather-now', null, array(
        'city' => $lobby['city'],
      ) ); ?></div>

      <div class="hdr-rule" aria-hidden="true"></div>

      <div class="hdr-cell hdr-time reveal"><?php get_template_part( 'template-parts/widgets/clock' ); ?></div>

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
/* ---- weather fallback ----
   The temperature is server-rendered (lobby_screens_get_current_weather), and
   that is the path a real WordPress install takes — this block then does
   nothing, because the cell is already full.

   The public demo is a different animal: it is rebuilt by a GitHub Actions
   runner, and Open-Meteo is the one source that does not answer from there.
   Ynet, ONE and Hebcal all render from CI over the same transport and the same
   8s timeout, so it is specific to that host; a rate limit on GitHub's shared
   egress IPs is the likeliest explanation, but it is NOT proven. Rather than
   guess at the cause, the page tops the cell up itself — Open-Meteo is one of
   the three sources that sends CORS (measured against the Pages origin), so
   this is the one feed that can be fetched client-side at all. It also keeps
   the temperature current between the 15-minute rebuilds.

   If this fails too, the cell stays empty and removes itself along with its
   separator (.hdr-cell:empty) — no error text on a lobby wall, brief §18. */
(function(){
  var cell = document.querySelector('.hdr-wx');
  if (!cell || cell.children.length) return;   /* the server already filled it */

  var url = 'https://api.open-meteo.com/v1/forecast'
          + '?latitude='  + encodeURIComponent(cell.dataset.lat)
          + '&longitude=' + encodeURIComponent(cell.dataset.lon)
          + '&current_weather=true'
          + '&timezone='  + encodeURIComponent(cell.dataset.tz);

  fetch(url, {cache:'no-store'})
    .then(function(r){ return r.ok ? r.json() : null; })
    .then(function(d){
      var now = d && d.current_weather;
      if (!now) return;
      /* mirrors lobby_screens_weather_icon_kind() — keep the two in step */
      var code = Number(now.weathercode), kind = 'cloud';
      if (code === 0 || code === 1) { kind = 'sun'; }
      else if ([61,63,65,80,81,82,95].indexOf(code) !== -1) { kind = 'rain'; }
      var words = {sun:'בהיר', cloud:'מעונן', rain:'גשום'};
      var temp  = Math.round(Number(now.temperature));
      if (!isFinite(temp)) return;
      /* the span goes in empty and its text is set below, so the only values
         interpolated into markup here are ones this file wrote itself */
      var city = cell.dataset.city ? '<span class="w-wx-now-city"></span>' : '';
      cell.innerHTML =
        '<div class="w-wx-now">'
      +   '<div class="w-wx-now-main">'
      +     '<svg class="w-wx-now-icon"><use href="#wx'
      +       kind.charAt(0).toUpperCase() + kind.slice(1) + '"></use></svg>'
      +     '<span class="w-wx-now-temp">' + temp + '\u00B0</span>'
      +   '</div>'
      +   '<div class="w-wx-now-cond">' + city + '<span>' + words[kind] + '</span></div>'
      + '</div>';
      if (cell.dataset.city) {
        cell.querySelector('.w-wx-now-city').textContent = cell.dataset.city;
      }
    })
    .catch(function(){ /* leave the cell collapsed */ });
})();

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

/* ---- Shabbat countdown ----
   Hebcal hands us offset-aware timestamps, so Date parses them against the
   screen's own clock and the arithmetic stays correct wherever the screen sits.
   Three states, because a countdown that runs past zero is worse than none:
     before candle lighting  -> time remaining
     between candles+havdalah -> the greeting ("שבת שלום ומבורך")
     after havdalah           -> hold, the 15-minute reload brings next week
   Updates on the minute; seconds would draw the eye for no reason at this
   distance, and the brief asks for calm movement only. */
(function(){
  var el=document.querySelector('.w-shabbat-count');
  if(!el) return;
  var candle=new Date(el.dataset.candle);
  var havdalah=el.dataset.havdalah ? new Date(el.dataset.havdalah) : null;
  if(isNaN(candle)) return;
  var labelEl=el.querySelector('.w-shabbat-count-label');
  var valEl=el.querySelector('.w-shabbat-count-val');

  function plural(n,one,many){ return n===1 ? one : n+' '+many; }
  /* Hebrew joins the last pair with vav. It takes a hyphen before a numeral
     ("יומיים ו-8 שעות") but not before a word ("שעה ודקה") — joining with a
     fixed " ו-" produced "שעה ו-דקה". */
  function joinHe(parts){
    if(parts.length<2) return parts[0]||'';
    var tail=parts[parts.length-1];
    var vav=/^\d/.test(tail) ? ' ו-' : ' ו';
    return parts.slice(0,-1).join(', ')+vav+tail;
  }

  function render(){
    var now=new Date();
    if(havdalah && now>=candle && now<havdalah){
      el.classList.add('is-in');
      labelEl.textContent='';
      valEl.textContent=el.dataset.greeting;
      return;
    }
    if(now>=candle){ el.classList.add('is-done'); return; }

    el.classList.remove('is-in','is-done');
    var mins=Math.floor((candle-now)/60000);
    var d=Math.floor(mins/1440), h=Math.floor((mins%1440)/60), m=mins%60;
    var parts=[];
    if(d) parts.push(plural(d,'יום','ימים'));
    if(h) parts.push(plural(h,'שעה','שעות'));
    if(!d && m) parts.push(plural(m,'דקה','דקות'));
    labelEl.textContent='הדלקת הנרות בעוד';
    valEl.textContent=joinHe(parts) || 'רגעים ספורים';
  }
  render();
  setInterval(render,30000);
})();

/* ---- ClockWidget ---- the only value rendered client-side.
   The digits go into fixed-width cells rather than one text node, because the
   clock face (Fraunces) has no tabular figures and a bare string would shuffle
   sideways every minute. The cells are written once and only their text is
   touched afterwards, so the common case — 59 ticks out of 60, when nothing
   changed — does no DOM work at all. */
var clockCells=null, clockLast='';
function tick(){
  var el=document.getElementById('clockTime');
  if(!el) return;
  var d=new Date();
  var p=function(n){return String(n).padStart(2,'0');};
  var now=p(d.getHours())+':'+p(d.getMinutes());
  if(now===clockLast) return;
  clockLast=now;
  if(!clockCells){
    el.textContent='';
    clockCells=[];
    for(var i=0;i<now.length;i++){
      var span=document.createElement('span');
      span.className = now[i]===':' ? 'w-clock-colon' : 'w-clock-digit';
      el.appendChild(span);
      clockCells.push(span);
    }
  }
  for(var j=0;j<now.length;j++){
    if(clockCells[j].textContent!==now[j]) clockCells[j].textContent=now[j];
  }
}
tick();setInterval(tick,1000);

/* ---- rotators ----
   One generic driver for every crossfading block on the screen. A container
   marked data-rotator="<hold ms>" cycles .is-active across its element
   children; a container with a single child never animates, which is why the
   notices card sits still in a building with only one notice to show.

   data-rotator-dots="<element id>" optionally mirrors the position onto a row
   of dots. The alternative was a second timer next to this one, which is how
   the dots and the panel end up disagreeing after a few hours. */
(function(){
  document.querySelectorAll('[data-rotator]').forEach(function(box){
    var items=box.children;
    if(items.length<2) return;
    var hold=parseInt(box.getAttribute('data-rotator'),10)||12000;
    var dotBox=document.getElementById(box.getAttribute('data-rotator-dots')||'');
    var dots=dotBox?dotBox.children:null;
    var i=0;
    setInterval(function(){
      items[i].classList.remove('is-active');
      if(dots&&dots[i]) dots[i].classList.remove('is-active');
      i=(i+1)%items.length;
      items[i].classList.add('is-active');
      if(dots&&dots[i]) dots[i].classList.add('is-active');
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
