<?php

namespace bencarr\entrypassword;

use bencarr\entrypassword\behaviors\EntryPasswordBehaviors;
use bencarr\entrypassword\fields\EntryPassword as EntryPasswordField;
use bencarr\entrypassword\services\CookieService;
use bencarr\entrypassword\twigextensions\EntryPasswordTwigExtension;
use Craft;
use craft\base\Plugin;
use craft\elements\Entry;
use craft\events\DefineBehaviorsEvent;
use craft\events\PluginEvent;
use craft\events\RegisterComponentTypesEvent;
use craft\events\RegisterUrlRulesEvent;
use craft\services\Fields;
use craft\services\Plugins;
use craft\web\UrlManager;
use yii\base\Event;

/**
 * @property CookieService $cookie
 *
 */
class EntryPassword extends Plugin
{
    // Static Properties
    // =========================================================================

// Static
    /**
     * @var EntryPassword
     */
    public static $plugin;

    // Public Properties
    // =========================================================================

    /**
     * @var string
     */
    public $schemaVersion = '1.0.0';

    // Public Methods
    // =========================================================================

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();
        self::$plugin = $this;

        Craft::$app->view->registerTwigExtension(new EntryPasswordTwigExtension());

        $this->setComponents([
            'cookie' => CookieService::class,
        ]);

        Event::on(
            UrlManager::class,
            UrlManager::EVENT_REGISTER_SITE_URL_RULES,
            function(RegisterUrlRulesEvent $event) {
                $event->rules['siteActionTrigger1'] = 'entry-password/validate';
            }
        );

        Event::on(
            UrlManager::class,
            UrlManager::EVENT_REGISTER_CP_URL_RULES,
            function(RegisterUrlRulesEvent $event) {
                $event->rules['cpActionTrigger1'] = 'entry-password/validate/do-something';
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
            Plugins::class,
            Plugins::EVENT_AFTER_INSTALL_PLUGIN,
            function(PluginEvent $event) {
                if ($event->plugin === $this) {
                }
            }
        );

        Event::on(
            Entry::class,
            Entry::EVENT_DEFINE_BEHAVIORS,
            function(DefineBehaviorsEvent $event) {
                $event->behaviors[$this->name] = EntryPasswordBehaviors::class;
            }
        );

        Craft::info(
            Craft::t(
                'entry-password',
                '{name} plugin loaded',
                ['name' => $this->name]
            ),
            __METHOD__
        );
    }

    // Protected Methods
    // =========================================================================

}
