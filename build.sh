#!/usr/bin/env bash
# Builds the release archives · Собирает архивы релиза:
#   dist/plg_content_timealias_<version>.zip            — installer package · пакет для установщика
#   dist/plg_content_timealias_<version>_site-root.zip  — unpack into the Joomla root · распаковать в корень Joomla
# The version is read from timealias.xml · Версия берётся из timealias.xml.
set -euo pipefail
cd "$(dirname "$0")"

version=$(sed -n 's:.*<version>\(.*\)</version>.*:\1:p' timealias.xml)
files=(timealias.xml services src language)
pkg="dist/plg_content_timealias_${version}.zip"
root="dist/plg_content_timealias_${version}_site-root.zip"

mkdir -p dist
rm -f "$pkg" "$root"
zip -qrX "$pkg" "${files[@]}"

tmp=$(mktemp -d)
trap 'rm -rf "$tmp"' EXIT
mkdir -p "$tmp/plugins/content/timealias"
cp -r "${files[@]}" "$tmp/plugins/content/timealias/"
(cd "$tmp" && zip -qrX "$OLDPWD/$root" plugins)

echo "$pkg"
echo "$root"
