<?php

namespace bencarr\entrypassword\fields;

use bencarr\entrypassword\assetbundles\entrypassword\EntryPasswordAsset;
use Craft;
use craft\base\ElementInterface;
use craft\base\Field;
use craft\helpers\Json;
use yii\db\Schema;

class EntryPassword extends Field
{
    // Public Properties
    // =========================================================================

    /**
     * @inheritdoc
     */
    public static function displayName(): string
    {
        return Craft::t('entry-password', 'Entry Password');
    }

    /**
     * @var bool
     */
    public $requiredForAuthenticatedUsers = false;

    // Static Methods
    // =========================================================================
    /**
     * @var integer
     */
    public $cookieExpiration = 0;

    // Public Methods
    // =========================================================================

    /**
     * @inheritdoc
     */
    public function rules()
    {
        $rules = parent::rules();
        $rules = array_merge($rules, [
            ['requiredForAuthenticatedUsers', 'boolean'],
            ['requiredForAuthenticatedUsers', 'default', 'value' => false],
            ['cookieExpiration', 'integer'],
            ['cookieExpiration', 'default', 'value' => 0],
        ]);
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
        // Register our asset bundle
        Craft::$app->getView()->registerAssetBundle(EntryPasswordAsset::class);

        // Get our id and namespace
        $id = Craft::$app->getView()->formatInputId($this->handle);
        $namespacedId = Craft::$app->getView()->namespaceInputId($id);

        // Variables to pass down to our field JavaScript to let it namespace properly
        $jsonVars = [
            'id' => $id,
            'name' => $this->handle,
            'namespace' => $namespacedId,
            'prefix' => Craft::$app->getView()->namespaceInputId(''),
        ];
        $jsonVars = Json::encode($jsonVars);
        Craft::$app->getView()->registerJs("$('#{$namespacedId}-field').EntryPasswordEntryPassword(" . $jsonVars . ");");

        // Render the input template
        return Craft::$app->getView()->renderTemplate(
            'entry-password/_components/fields/EntryPassword_input',
            [
                'name' => $this->handle,
                'value' => $value,
                'field' => $this,
                'id' => $id,
                'namespacedId' => $namespacedId,
            ]
        );
    }
}
