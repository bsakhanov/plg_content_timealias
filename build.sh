#!/usr/bin/env bash
# Собирает установочный пакет: dist/plg_content_timealias_<версия>.zip
# Версия берётся из timealias.xml — править её нужно только там.
set -euo pipefail
cd "$(dirname "$0")"
version=$(sed -n 's:.*<version>\(.*\)</version>.*:\1:p' timealias.xml)
out="dist/plg_content_timealias_${version}.zip"
mkdir -p dist
rm -f "$out"
zip -qrX "$out" timealias.xml services src language
echo "$out"
