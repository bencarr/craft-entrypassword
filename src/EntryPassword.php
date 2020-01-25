<?php

namespace bencarr\entrypassword;

use bencarr\entrypassword\behaviors\EntryPasswordBehaviors;
use bencarr\entrypassword\elements\actions\SetPasswordAction;
use bencarr\entrypassword\fields\EntryPasswordField;
use bencarr\entrypassword\services\CookieService;
use Craft;
use craft\base\Element;
use craft\base\Plugin;
use craft\elements\Entry;
use craft\events\DefineBehaviorsEvent;
use craft\events\RegisterComponentTypesEvent;
use craft\events\RegisterElementActionsEvent;
use craft\events\RegisterUrlRulesEvent;
use craft\services\Fields;
use craft\web\UrlManager;
use yii\base\Event;

/**
 * @property CookieService $cookie
 *
 */
class EntryPassword extends Plugin
{
    /**
     * @var EntryPassword
     */
    public static $plugin;

    public $schemaVersion = '1.0.0';

    public function init()
    {
        parent::init();
        self::$plugin = $this;

        $this->setComponents([
            'cookie' => CookieService::class,
        ]);

        Event::on(
            UrlManager::class,
            UrlManager::EVENT_REGISTER_SITE_URL_RULES,
            function(RegisterUrlRulesEvent $event) {
                $event->rules['entryPasswordValidation'] = 'entry-password/validate';
            }
        );

        Event::on(
            Fields::class,
            Fields::EVENT_REGISTER_FIELD_TYPES,
            function(RegisterComponentTypesEvent $event) {
                $event->types[] = EntryPasswordField::class;
            }
        );

        Event::on(
            Entry::class,
            Entry::EVENT_DEFINE_BEHAVIORS,
            function(DefineBehaviorsEvent $event) {
                $event->behaviors[$this->id] = EntryPasswordBehaviors::class;
            }
        );

        Event::on(
            Entry::class,
            Element::EVENT_REGISTER_ACTIONS,
            function(RegisterElementActionsEvent $event) {
                $event->actions[] = SetPasswordAction::class;
            }
        );

        Craft::$app->view->hook('cp.entries.edit.settings', function(array &$context) {
            /** @var Entry $entry */
            $entry = $context['entry'];

            /** @var EntryPasswordField|null $field */
            $field = $entry->getEntryPasswordField();
            if ($field && $field->displaysInSidebar) {
                return $field->getSidebarInputHtml($entry->getFieldValue($field->handle));
            }

            return null;
        });

        Craft::info(Craft::t('entry-password', 'Entry Password plugin loaded',), __METHOD__);
    }
}
