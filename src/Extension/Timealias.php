<?php

/**
 * @package     Webmarka.Plugin
 * @subpackage  Content.Timealias
 *
 * @copyright   (C) 2026 Beibit Sakhanov <https://webmarka.kz>
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

namespace Webmarka\Plugin\Content\Timealias\Extension;

\defined('_JEXEC') or die;

use Joomla\CMS\Date\Date;
use Joomla\CMS\Event\Model\BeforeSaveEvent;
use Joomla\CMS\Plugin\CMSPlugin;
use Joomla\Database\DatabaseAwareTrait;
use Joomla\Database\ParameterType;
use Joomla\Event\SubscriberInterface;

/**
 * Присваивает новому материалу алиас вида ГГГГММДД-ЧЧММСС.
 *
 * Время берётся по часовому поясу сайта (настройка «Часовой пояс сайта»),
 * а не по часам сервера. Если такой алиас в этой категории уже занят
 * (в том числе материалом в корзине), добавляется суффикс -2, -3 и т. д.:
 * иначе ядро откажется сохранять материал.
 *
 * Алиас, заданный вручную или пришедший при импорте, по умолчанию сохраняется.
 */
final class Timealias extends CMSPlugin implements SubscriberInterface
{
    use DatabaseAwareTrait;

    /**
     * com_content.article — админка и REST API; com_content.form — создание с сайта.
     */
    private const CONTEXTS = ['com_content.article', 'com_content.form'];

    public static function getSubscribedEvents(): array
    {
        return ['onContentBeforeSave' => 'onContentBeforeSave'];
    }

    public function onContentBeforeSave(BeforeSaveEvent $event): void
    {
        if (!$event->getIsNew() || !\in_array($event->getContext(), self::CONTEXTS, true)) {
            return;
        }

        if ((int) $this->params->get('keep_alias', 1) === 1 && $this->requestedAlias($event) !== '') {
            return;
        }

        $article = $event->getItem();
        $catid   = (int) $article->catid;
        $base    = $this->siteTimestamp();
        $alias   = $base;

        for ($n = 2; $this->isTaken($alias, $catid); $n++) {
            $alias = $base . '-' . $n;
        }

        $article->alias = $alias;
    }

    /**
     * Алиас в том виде, в каком его задал человек или импортёр.
     *
     * По самому материалу этого уже не понять: при сохранении из формы и через API
     * модель статьи подставляет в пустое поле транслит заголовка раньше, чем
     * срабатывает событие. Поэтому смотрим на исходный запрос.
     */
    private function requestedAlias(BeforeSaveEvent $event): string
    {
        $app   = $this->getApplication();
        $input = $app->getInput();

        if ($input->getCmd('option') === 'com_content') {
            // Формы админки и сайта.
            $form = $input->get('jform', null, 'array');

            if (\is_array($form)) {
                $alias = trim((string) ($form['alias'] ?? ''));

                // «Сохранить как копию» присылает алиас оригинала. Это не просьба
                // его сохранить — ядро рассуждает так же.
                if ($alias !== '' && $input->get('task') === 'save2copy') {
                    $originalId = (int) ($app->isClient('site') ? $input->getInt('a_id') : $input->getInt('id'));

                    if ($alias === $this->aliasOf($originalId)) {
                        return '';
                    }
                }

                return $alias;
            }

            // REST API: тело запроса читается так же, как в ApiController::save().
            if ($app->isClient('api')) {
                $data = $input->get('data', json_decode((string) $input->json->getRaw(), true), 'array');

                return \is_array($data) ? trim((string) ($data['alias'] ?? '')) : '';
            }
        }

        // Импортёры и скрипты, которые вызывают модель напрямую: что передали, то и задано.
        return trim((string) ($event->getData()['alias'] ?? ''));
    }

    private function siteTimestamp(): string
    {
        // Не Factory::getDate(): тот кеширует «сейчас» на весь запрос, и при импорте
        // или пакетной загрузке все материалы получили бы одно и то же время.
        $date = new Date('now', 'UTC');

        try {
            $date->setTimezone(new \DateTimeZone($this->getApplication()->get('offset', 'UTC')));
        } catch (\Exception) {
            // Битая настройка часового пояса — остаёмся в UTC, но материал сохраняем.
        }

        return $date->format('Ymd-His', true);
    }

    private function isTaken(string $alias, int $catid): bool
    {
        $db    = $this->getDatabase();
        $query = $db->createQuery()
            ->select('1')
            ->from($db->quoteName('#__content'))
            ->where($db->quoteName('alias') . ' = :alias')
            ->where($db->quoteName('catid') . ' = :catid')
            ->bind(':alias', $alias)
            ->bind(':catid', $catid, ParameterType::INTEGER);

        return (bool) $db->setQuery($query, 0, 1)->loadResult();
    }

    private function aliasOf(int $id): string
    {
        if ($id <= 0) {
            return '';
        }

        $db    = $this->getDatabase();
        $query = $db->createQuery()
            ->select($db->quoteName('alias'))
            ->from($db->quoteName('#__content'))
            ->where($db->quoteName('id') . ' = :id')
            ->bind(':id', $id, ParameterType::INTEGER);

        return (string) $db->setQuery($query)->loadResult();
    }
}
