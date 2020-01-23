<?php

namespace bencarr\entrypassword\fields;

use bencarr\entrypassword\assetbundles\entrypassword\EntryPasswordAsset;
use Craft;
use craft\base\ElementInterface;
use craft\base\Field;
use yii\db\Schema;

class EntryPassword extends Field
{
    // Public Properties
    // =========================================================================

    /**
     * @var bool
     */
    public $requiredForAuthenticatedUsers = false;

    /**
     * @var integer
     */
    public $cookieExpiration = 0;

    /**
     * @var bool
     */
    public $displaysInSidebar = true;

    // Static Methods
    // =========================================================================

    /**
     * @inheritdoc
     */
    public static function displayName(): string
    {
        return Craft::t('entry-password', 'Entry Password');
    }

    // Public Methods
    // =========================================================================

    /**
     * @inheritdoc
     */
    public function rules()
    {
        $rules = parent::rules();
        $rules[] = [['requiredForAuthenticatedUsers'], 'boolean'];
        $rules[] = [['cookieExpiration'], 'integer'];
        $rules[] = [['displaysInSidebar'], 'boolean'];

        return $rules;
    }

    /**
     * @inheritdoc
     */
    public function getContentColumnType(): string
    {
        return Schema::TYPE_TEXT;
    }

    /**
     * @inheritdoc
     */
    public function normalizeValue($value, ElementInterface $element = null)
    {
        return $value;
    }

    /**
     * @inheritdoc
     */
    public function serializeValue($value, ElementInterface $element = null)
    {
        return parent::serializeValue($value, $element);
    }

    /**
     * @inheritdoc
     */
    public function getSettingsHtml()
    {
        // Render the settings template
        return Craft::$app->getView()->renderTemplate(
            'entry-password/_components/fields/EntryPassword_settings',
            [
                'field' => $this,
            ]
        );
    }

    /**
     * @inheritdoc
     */
    public function getInputHtml($value, ElementInterface $element = null): string
    {
        return Craft::$app->getView()->renderTemplate(
            'entry-password/_components/fields/EntryPassword_input',
            $this->getInputContext([
                'value' => $value,
            ]),
            );
    }

    public function getSidebarInputHtml($value)
    {
        // Register our asset bundle
        Craft::$app->getView()->registerAssetBundle(EntryPasswordAsset::class);

        // Render the input template
        return Craft::$app->getView()->renderTemplate(
            'entry-password/_components/fields/EntryPassword_sidebarInput',
            $this->getInputContext([
                'value' => $value,
            ]),
            );
    }

    protected function getInputContext(array $context = [])
    {
        // Get our id and namespace
        $id = Craft::$app->getView()->formatInputId($this->handle);
        $namespacedId = Craft::$app->getView()->namespaceInputId($id);

        return array_merge([
            'static' => true,
            'name' => $this->handle,
            'field' => $this,
            'id' => $id,
            'namespacedId' => $namespacedId,
        ], $context);
    }
}
