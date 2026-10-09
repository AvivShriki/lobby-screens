#!/usr/bin/env bash
# Refresh the static GitHub Pages snapshot in docs/ from the running theme.
#
# docs/ is a frozen rendering of the WordPress output so the design can be
# opened in a browser (and shown to the client) without booting Playground.
# It was previously produced by hand; this script makes it repeatable.
#
# Usage:  ./tools/snapshot.sh [base-url]        (default http://127.0.0.1:9400)
# The Playground server must already be running against wordpress/lobby-screens-theme.

set -euo pipefail

BASE="${1:-http://127.0.0.1:9400}"
ROOT="$(cd "$(dirname "$0")/.." && pwd)"
THEME="$ROOT/wordpress/lobby-screens-theme"
DOCS="$ROOT/docs"

echo "→ fetching $BASE"
# Playground boots with --login: the first request 302s to "/" while setting
# auth cookies. Without a cookie jar curl re-triggers that redirect forever
# and writes a 0-byte file, so keep the jar and follow the redirect.
JAR="$(mktemp)"
trap 'rm -f "$JAR"' EXIT
curl -fsSL -c "$JAR" -b "$JAR" "$BASE/" -o "$DOCS/index.html.tmp"
if [ ! -s "$DOCS/index.html.tmp" ]; then
  echo "empty response from $BASE — is the Playground server running?" >&2
  exit 1
fi

# rewrite theme URLs to the flat layout docs/ uses, and drop cache-busting
# query strings so the files resolve as plain paths on Pages
python3 - "$DOCS/index.html.tmp" "$BASE" "$THEME" <<'PY'
import re, sys
path, base, theme_dir = sys.argv[1], sys.argv[2].rstrip('/'), sys.argv[3]
s = open(path, encoding='utf-8').read()
theme = re.escape(base) + r'/wp-content/themes/lobby-screens-theme/'
# Cache busting. WordPress emits ?ver=<hand-written number>; replace it with a
# hash of the file's actual bytes. Stripping the query outright (what this did
# first) meant a republished snapshot kept serving whatever style.css a visitor
# already had cached — the page looked unchanged after a real deploy, which is
# exactly how the v7.2 font fix appeared not to have shipped.
import hashlib, os
# Hash the THEME's style.css, not docs/style.css. docs/style.css is still the
# PREVIOUS build's copy at this point — the cp happens after this script — so
# hashing it stamped every build with the hash of the stylesheet it was
# replacing. The query string therefore only changed one build late, and a
# returning visitor kept the cached old CSS against the new HTML until the next
# run. Found 9.10.2026 while shipping the v8.1 header rail, which is exactly the
# kind of pure-CSS change that bug hides.
css = os.path.join(theme_dir, 'style.css')
ver = hashlib.sha256(open(css, 'rb').read()).hexdigest()[:10] if os.path.exists(css) else '0'
s = re.sub(theme + r'style\.css(\?[^"\']*)?', 'style.css?v=' + ver, s)
s = re.sub(theme, '', s)
# dns-prefetch for the dev host is meaningless once this is on Pages
s = re.sub(r"\s*<link rel='dns-prefetch' href='//[^']*' />", '', s)
# any remaining absolute link to the dev server would 404 on Pages
leftover = re.findall(re.escape(base) + r'[^"\'\s>]*', s)
if leftover:
    print('WARNING: unrewritten dev-server URLs still present:', file=sys.stderr)
    for u in sorted(set(leftover))[:10]:
        print('  ' + u, file=sys.stderr)
open(path, 'w', encoding='utf-8').write(s)
PY

mv "$DOCS/index.html.tmp" "$DOCS/index.html"
cp "$THEME/style.css" "$DOCS/style.css"
rsync -a --delete "$THEME/assets/" "$DOCS/assets/"

echo "→ docs/ refreshed from $BASE"
