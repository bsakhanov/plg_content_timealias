<p align="center">
  <picture>
    <source media="(prefers-color-scheme: dark)" srcset="docs/banner-dark.svg">
    <img src="docs/banner-light.svg" alt="Timealias — timestamp aliases for new Joomla articles" width="100%">
  </picture>
</p>

<p align="center"><strong>English</strong> · <a href="README.ru.md">Русский</a></p>

<p align="center">
  <a href="https://github.com/bsakhanov/plg_content_timealias/releases/latest"><img src="https://img.shields.io/github/v/release/bsakhanov/plg_content_timealias?style=flat-square&color=1e5b54" alt="Release"></a>
  <img src="https://img.shields.io/badge/Joomla-6.x-1e5b54?style=flat-square&logo=joomla&logoColor=white" alt="Joomla 6.x">
  <img src="https://img.shields.io/badge/PHP-8.3%2B-1e5b54?style=flat-square&logo=php&logoColor=white" alt="PHP 8.3+">
  <a href="LICENSE"><img src="https://img.shields.io/github/license/bsakhanov/plg_content_timealias?style=flat-square&color=1e5b54" alt="License: GPL-2.0"></a>
  <a href="https://github.com/bsakhanov/plg_content_timealias/releases"><img src="https://img.shields.io/github/downloads/bsakhanov/plg_content_timealias/total?style=flat-square&color=1e5b54" alt="Downloads"></a>
</p>

# Timealias

A Joomla 6 content plugin that gives every new article an alias in the `YYYYMMDD-HHMMSS` format — for example, `20261006-053650` — using the site time zone.

Article URLs come out short and uniform and no longer depend on the title: fix a headline after publication as often as you like, the link stays the same. Titles in any script, Cyrillic included, end up with the same clean slug.

## How it works

- **Only on creation.** Editing an existing article never touches its alias. *Save as Copy* creates a new article, so the copy gets a fresh timestamp rather than the original's alias.
- **Site time, not server time.** The timestamp follows *System → Global Configuration → Server → Website Time Zone*, not the server clock, which usually runs on UTC.
- **No clashes.** Joomla requires an alias to be unique within a category and refuses to save a duplicate. If two articles are created in the same second in the same category, the second gets `-2`, the third `-3`, and so on. Trashed articles count too.
- **Every entry point.** Articles created in the administrator, through the REST API and through the frontend submission form are all handled. Categories, menu items and other components are left alone.
- **Your alias wins.** An alias typed in by hand or supplied on import is kept by default; the timestamp is used only when the field is left empty. This can be switched off in the plugin settings.

## Importing existing articles

Articles already on the site are never touched. During an import, it depends on how the importer saves articles:

- **Through the Joomla model or the REST API** — the plugin sees them as new. If the importer passes the old alias, it is kept and existing URLs keep working; if there is no alias, a timestamp is used.
- **By writing to the database directly** (an SQL dump, or a table write that bypasses the model) — the plugin is not called at all, and aliases stay as they were.

For a large migration the most cautious route is to disable the plugin for the duration of the import.

Why the plugin looks at the original request: when an article is saved from a form or through the API, Joomla itself fills an empty alias field with a transliterated title *before* the plugin runs. To tell an empty field from a filled one, the plugin reads what actually arrived — the form's `jform[alias]`, the API request body, or the data handed to the model directly.

## Installation

Download the archive you need from [Releases](https://github.com/bsakhanov/plg_content_timealias/releases/latest).

**Through the installer** — `plg_content_timealias_<version>.zip`

1. *System → Install → Extensions*, upload the archive.
2. Enable *Content - Timealias* in *System → Manage → Plugins*.

**By unpacking into the site root** — `plg_content_timealias_<version>_site-root.zip`. Handy over FTP or a hosting file manager, without uploading through the admin panel.

1. Unpack the archive into the Joomla root; the files land in `plugins/content/timealias/`.
2. *System → Install → Discover*, select *Content - Timealias*, click *Install*.
3. Enable the plugin in *System → Manage → Plugins*.

The *Source code (zip)* attached to every release also installs through the installer: Joomla copies only the files listed in the manifest.

## Settings

| Option | Default | Effect |
|---|---|---|
| Keep supplied alias | Yes | *Yes*: the timestamp is used only when the alias field is empty. *No*: every new article gets a timestamp, whatever the field contains. |

## Requirements

Joomla 6.x, PHP 8.3 or later. Tested on Joomla 6.1.4 with PHP 8.3 and MariaDB 10.11.

## Building from source

```bash
./build.sh
# dist/plg_content_timealias_<version>.zip            — installer package
# dist/plg_content_timealias_<version>_site-root.zip  — archive for the site root
```

The version is read from `timealias.xml`; it is the only place to change it.

## License

[GNU General Public License v2.0 or later](LICENSE).

© 2026 Beibit Sakhanov · [webmarka.kz](https://webmarka.kz)
