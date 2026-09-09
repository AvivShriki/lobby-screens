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
python3 - "$DOCS/index.html.tmp" "$BASE" <<'PY'
import re, sys
path, base = sys.argv[1], sys.argv[2].rstrip('/')
s = open(path, encoding='utf-8').read()
theme = re.escape(base) + r'/wp-content/themes/lobby-screens-theme/'
# Cache busting. WordPress emits ?ver=<hand-written number>; replace it with a
# hash of the file's actual bytes. Stripping the query outright (what this did
# first) meant a republished snapshot kept serving whatever style.css a visitor
# already had cached — the page looked unchanged after a real deploy, which is
# exactly how the v7.2 font fix appeared not to have shipped.
import hashlib, os
css = os.path.join(os.path.dirname(path), 'style.css')
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
