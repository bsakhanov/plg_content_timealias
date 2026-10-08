# Changelog · История изменений

The format follows [Keep a Changelog](https://keepachangelog.com/en/1.1.0/); versions follow [Semantic Versioning](https://semver.org/).
Формат — [Keep a Changelog](https://keepachangelog.com/ru/1.1.0/), нумерация версий — [семантическая](https://semver.org/lang/ru/).

## [1.0.0] — 2026-10-06

First release · Первый выпуск.

### English

- New articles get an alias in the `YYYYMMDD-HHMMSS` format.
- The timestamp follows the site time zone, not the server clock.
- If the alias is already taken in the category (trashed articles included), `-2`, `-3` and so on are appended.
- An alias typed in by hand or supplied on import (through the Joomla model or the REST API) is kept by default; the *Keep supplied alias* switch is in the plugin settings.
- *Save as Copy* gets a fresh timestamp, not the original's alias.
- Articles created in the administrator, through the REST API and through the frontend form are handled.
- Existing articles, categories and menu items are left untouched.
- Two release archives: an installer package and an archive to unpack into the site root.

### Русский

- Новому материалу присваивается алиас вида `ГГГГММДД-ЧЧММСС`.
- Время берётся по часовому поясу сайта, а не по часам сервера.
- Если алиас в категории уже занят (в том числе материалом в корзине), добавляется суффикс `-2`, `-3` и т. д.
- Алиас, вписанный вручную или пришедший при импорте (через модель Joomla или REST API), по умолчанию сохраняется; переключатель «Сохранять заданный алиас» — в настройках плагина.
- «Сохранить как копию» получает новую метку времени, а не алиас оригинала.
- Обрабатываются материалы из админки, через REST API и через форму на сайте.
- Существующие материалы, категории и пункты меню не затрагиваются.
- Два архива в релизе: пакет для установщика и архив для распаковки в корень сайта.

[1.0.0]: https://github.com/bsakhanov/plg_content_timealias/releases/tag/v1.0.0
