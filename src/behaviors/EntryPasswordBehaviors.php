<?php

namespace bencarr\entrypassword\behaviors;

use bencarr\entrypassword\EntryPassword;
use bencarr\entrypassword\fields\EntryPasswordField;
use Craft;
use craft\elements\Entry;
use craft\errors\InvalidFieldException;
use craft\helpers\ArrayHelper;
use yii\base\Behavior;
use yii\base\Exception;
use yii\base\InvalidConfigException;

/**
 * @property-read Entry $owner
 * @property-read ?EntryPasswordField $entryPasswordField
 * @property-read ?string $entryPasswordFieldValue
 */
class EntryPasswordBehaviors extends Behavior
{
    /**
     * @throws Exception
     * @throws InvalidConfigException
     */
    public function requiresPassword(): bool
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
    
    /**
     * @throws InvalidFieldException
     */
    public function isPasswordProtected(): bool
    {
        return !empty($this->getEntryPasswordFieldValue());
    }
    
    /**
     * @throws InvalidFieldException
     */
    public function getEntryPasswordFieldValue(): ?string
    {
        $field = $this->getEntryPasswordField();
        if ($field) {
            $value = $this->owner->getFieldValue($field->handle);
            if ($value) {
                return $value;
            }
        }
        
        return null;
    }
    
    public function getEntryPasswordField(): ?EntryPasswordField
    {
        $layout = $this->owner->getFieldLayout();
        if ($layout) {
            return ArrayHelper::firstWhere(
                $layout->getFields(),
                fn($field) => get_class($field),
                EntryPasswordField::class
            );
        }
        
        return null;
    }
}
