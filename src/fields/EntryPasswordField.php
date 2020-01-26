<?php

namespace bencarr\entrypassword\fields;

use bencarr\entrypassword\assetbundles\entrypassword\EntryPasswordAsset;
use Craft;
use craft\base\ElementInterface;
use craft\base\Field;
use craft\base\PreviewableFieldInterface;
use yii\db\Schema;

class EntryPasswordField extends Field implements PreviewableFieldInterface
{
    public $requiredForAuthenticatedUsers = false;

    public $cookieExpiration = 0;

    public $displaysInSidebar = true;

    public static function displayName(): string
    {
        return Craft::t('entry-password', 'Entry Password');
    }

    public function rules()
    {
        $rules = parent::rules();
        $rules[] = [['requiredForAuthenticatedUsers'], 'boolean'];
        $rules[] = [['cookieExpiration'], 'integer'];
        $rules[] = [['displaysInSidebar'], 'boolean'];

        return $rules;
    }

    public function getContentColumnType(): string
    {
        return Schema::TYPE_TEXT;
    }

    public function getSettingsHtml(): string
    {
        return Craft::$app->getView()->renderTemplate(
            'entry-password/_components/fields/EntryPassword_settings',
            [
                'field' => $this,
            ]
        );
    }

    public function getInputHtml($value, ElementInterface $element = null): string
    {
        return Craft::$app->getView()->renderTemplate(
            'entry-password/_components/fields/EntryPassword_input',
            $this->getInputContext([
                'value' => $value,
            ])
        );
    }

    public function getSidebarInputHtml($value): string
    {
        // Register our asset bundle
        Craft::$app->getView()->registerAssetBundle(EntryPasswordAsset::class);

        // Render the input template
        return Craft::$app->getView()->renderTemplate(
            'entry-password/_components/fields/EntryPassword_sidebarInput',
            $this->getInputContext([
                'value' => $value,
            ])
        );
    }

    public function getPersistenceOptions(): array
    {
        $options = [
            0, // Browser session
            60 * 60 * 24, // 1 Day
            60 * 60 * 24 * 7, // 1 Week
            60 * 60 * 24 * 30, // 1 Month
            60 * 60 * 24 * 365, // 1 Year
        ];

        return array_map(function($value) {
            return [
                'value' => $value,
                'label' => Craft::t('entry-password', 'persistenceOptions.' . $value),
            ];
        }, $options);
    }

    protected function getInputContext(array $context = []): array
    {
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

    public function getTableAttributeHtml($value, ElementInterface $element): string
    {
        return (string)$value;
    }
}
