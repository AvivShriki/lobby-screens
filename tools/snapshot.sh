#!/usr/bin/env bash
# Refresh the static GitHub Pages snapshot in docs/ from the running theme.
#
# docs/ is a frozen rendering of the WordPress output so the design can be
# opened in a browser (and shown to the client) without booting Playground.
#
# v8.9 renders TWO designs rather than one — the classic screen and the
# "contrast" variant — plus a compare page that puts them side by side.
# index.html stays the classic one: a variant existing must not move the
# default.
#
# Usage:  ./tools/snapshot.sh [base-url]        (default http://127.0.0.1:9400)
# The Playground server must already be running against wordpress/lobby-screens-theme.

set -euo pipefail

BASE="${1:-http://127.0.0.1:9400}"
ROOT="$(cd "$(dirname "$0")/.." && pwd)"
THEME="$ROOT/wordpress/lobby-screens-theme"
DOCS="$ROOT/docs"

# Playground boots with --login: the first request 302s to "/" while setting
# auth cookies. Without a cookie jar curl re-triggers that redirect forever
# and writes a 0-byte file, so keep the jar and follow the redirect.
JAR="$(mktemp)"
trap 'rm -f "$JAR"' EXIT

render () {                       # render <query-string> <output-file>
  local query="$1" out="$2"
  echo "→ fetching $BASE/$query → $out"
  curl -fsSL -c "$JAR" -b "$JAR" "$BASE/$query" -o "$DOCS/$out.tmp"
  if [ ! -s "$DOCS/$out.tmp" ]; then
    echo "empty response from $BASE — is the Playground server running?" >&2
    exit 1
  fi

  # rewrite theme URLs to the flat layout docs/ uses
  python3 - "$DOCS/$out.tmp" "$BASE" "$THEME" <<'PY'
import re, sys, hashlib, os
path, base, theme_dir = sys.argv[1], sys.argv[2].rstrip('/'), sys.argv[3]
s = open(path, encoding='utf-8').read()
theme = re.escape(base) + r'/wp-content/themes/lobby-screens-theme/'
# Cache busting. WordPress emits ?ver=<hand-written number>; replace it with a
# hash of the file's actual bytes. Hash the THEME's style.css, not docs/ — that
# copy is still the PREVIOUS build at this point, so hashing it stamped every
# build with the hash of the stylesheet it was replacing and the query string
# changed one build late.
css = os.path.join(theme_dir, 'style.css')
ver = hashlib.sha256(open(css, 'rb').read()).hexdigest()[:10] if os.path.exists(css) else '0'
s = re.sub(theme + r'style\.css(\?[^"\']*)?', 'style.css?v=' + ver, s)
s = re.sub(theme, '', s)
# dns-prefetch for the dev host is meaningless once this is on Pages
s = re.sub(r"\s*<link rel='dns-prefetch' href='//[^']*' />", '', s)
leftover = re.findall(re.escape(base) + r'[^"\'\s>]*', s)
if leftover:
    print('WARNING: unrewritten dev-server URLs still present:', file=sys.stderr)
    for u in sorted(set(leftover))[:10]:
        print('  ' + u, file=sys.stderr)
open(path, 'w', encoding='utf-8').write(s)
PY

  mv "$DOCS/$out.tmp" "$DOCS/$out"
}

render ""                index.html      # the design that ships
render "?theme=contrast" contrast.html   # the variant, for comparison

cp "$THEME/style.css" "$DOCS/style.css"
rsync -a --delete "$THEME/assets/" "$DOCS/assets/"

# The compare page is written here rather than kept as a file, so it can never
# drift from the two renders it points at.
cat > "$DOCS/compare.html" <<'HTML'
<!doctype html>
<html lang="he" dir="rtl">
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>עוצמה · השוואת עיצובים</title>
<style>
  *{box-sizing:border-box;margin:0}
  body{background:#0A0B0E;color:#F4F3EF;font:16px/1.5 'Heebo','Arial Hebrew',sans-serif;padding:26px 30px 34px}
  h1{font-size:26px;font-weight:600;letter-spacing:.02em}
  p.note{margin-top:8px;color:rgba(244,243,239,.72);font-weight:300;max-width:96ch}
  .grid{display:grid;grid-template-columns:1fr 1fr;gap:26px;margin-top:22px}
  .pane h2{font-size:19px;font-weight:600;color:#D9C79C;letter-spacing:.04em}
  .pane .sub{font-size:14px;font-weight:300;color:rgba(244,243,239,.62);margin-top:3px}
  .shot{position:relative;margin-top:10px;border:1px solid rgba(201,188,156,.28);border-radius:12px;overflow:hidden;aspect-ratio:16/9;background:#000}
  .shot iframe{position:absolute;top:0;left:0;width:1920px;height:1080px;border:0;transform-origin:top left}
  a.open{display:inline-block;margin-top:9px;font-size:14px;color:#D9C79C;text-decoration:none;border-bottom:1px solid rgba(217,199,156,.4)}
  @media (max-width:1100px){.grid{grid-template-columns:1fr}}
</style>
<h1>עוצמה · שתי גרסאות עיצוב</h1>
<p class="note">
  שתיהן אותו מבנה, אותו תוכן ואותם נתונים חיים — ההבדל הוא עיצובי בלבד.
  <b>קלאסי</b> היא הגרסה שרצה כברירת מחדל ואליה חוזרים תמיד.
  <b>ניגודיות</b> הופכת כל כרטיס למשטח אטום על רקע שקט יותר, מגדילה את הטקסטים הקטנים ופותחת את גובה השורה.
</p>
<div class="grid">
  <div class="pane">
    <h2>קלאסי — ברירת המחדל</h2>
    <div class="sub">הגרסה שאושרה עד v8.8. זו שנשארת אם לא עושים כלום.</div>
    <div class="shot"><iframe src="index.html" scrolling="no" loading="lazy"></iframe></div>
    <a class="open" href="index.html" target="_blank">פתח במסך מלא ↗</a>
  </div>
  <div class="pane">
    <h2>ניגודיות — הווריאציה</h2>
    <div class="sub">משטחים אטומים, רקע שקט, טקסטים קטנים מוגדלים, שורות פתוחות יותר.</div>
    <div class="shot"><iframe src="contrast.html" scrolling="no" loading="lazy"></iframe></div>
    <a class="open" href="contrast.html" target="_blank">פתח במסך מלא ↗</a>
  </div>
</div>
<script>
/* each iframe holds the real 1920x1080 screen; scale it to whatever width its
   pane ended up with, so both panes show the same thing at the same size */
function fit(){
  document.querySelectorAll('.shot').forEach(function(box){
    var f=box.querySelector('iframe');
    f.style.transform='scale('+(box.clientWidth/1920)+')';
  });
}
addEventListener('resize',fit); addEventListener('load',fit); fit();
</script>
</html>
HTML

echo "→ docs/ refreshed from $BASE (index.html, contrast.html, compare.html)"
