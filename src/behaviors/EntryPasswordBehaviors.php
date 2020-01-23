<?php

namespace bencarr\entrypassword\behaviors;

use bencarr\entrypassword\EntryPassword;
use bencarr\entrypassword\fields\EntryPassword as EntryPasswordField;
use Craft;
use craft\elements\Entry;
use craft\helpers\ArrayHelper;
use yii\base\Behavior;

/**
 * @property Entry $owner
 */
class EntryPasswordBehaviors extends Behavior
{
    public function requiresPassword()
    {
        if (!$this->isPasswordProtected()) {
            return false;
        }

        if (EntryPassword::getInstance()->cookie->isValid($this->owner)) {
            return false;
        }

        if (Craft::$app->getUser()->getIsAdmin()) {
            return $this->getEntryPasswordField()->requiredForAuthenticatedUsers;
        }

        return true;
    }

    public function isPasswordProtected()
    {
        return !empty($this->getEntryPasswordFieldValue());
    }

    public function getEntryPasswordFieldValue()
    {
        $field = $this->getEntryPasswordField();
        if ($field) {
            return $this->owner->getFieldValue($field->handle);
        }

        return null;
    }

    /**
     * @return EntryPasswordField|null
     */
    public function getEntryPasswordField()
    {
        $layout = $this->owner->getFieldLayout();
        if ($layout) {
            return ArrayHelper::firstWhere(
                $layout->getFields(),
                function($field) {
                    return get_class($field);
                },
                EntryPasswordField::class
            );
        }

        return null;
    }
}
