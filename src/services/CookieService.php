<?php

namespace bencarr\entrypassword\services;

use Craft;
use craft\base\Component;
use craft\elements\Entry;
use yii\base\Exception;
use yii\base\InvalidConfigException;
use yii\web\Cookie;

class CookieService extends Component
{
    public const PREFIX = 'entrypassword_';
    
    /**
     * @throws Exception
     * @throws InvalidConfigException
     */
    public function set(Entry $entry): void
    {
        $cookie = new Cookie([
            'name' => $this->getCookieName($entry),
            'value' => $this->getCookieValue($entry),
            'expire' => $this->getCookieExpiration($entry),
            'secure' => true,
            'httpOnly' => true,
        ]);
        
        Craft::$app->getResponse()->cookies->add($cookie);
    }
    
    /**
     * @throws Exception
     * @throws InvalidConfigException
     */
    protected function getCookieName(Entry $entry): string
    {
        $entryIdHash = Craft::$app->getSecurity()->hashData($entry->id);
        
        return self::PREFIX.$entryIdHash;
    }
    
    protected function getCookieValue(Entry $entry): string
    {
        $password = $entry->getEntryPasswordFieldValue();
        
        return Craft::$app->getSecurity()->maskToken($password);
    }
    
    protected function getCookieExpiration(Entry $entry): ?int
    {
        $field = $entry->getEntryPasswordField();
        
        return $field->cookieExpiration ?? null;
    }
    
    /**
     * @throws Exception
     * @throws InvalidConfigException
     */
    public function remove(Entry $entry): void
    {
        $name = $this->getCookieName($entry);
        
        Craft::$app->getResponse()->cookies->remove($name);
    }
    
    /**
     * @throws Exception
     * @throws InvalidConfigException
     */
    public function get(Entry $entry): ?Cookie
    {
        $name = $this->getCookieName($entry);
        
        return Craft::$app->getRequest()->cookies->get($name);
    }
    
    /**
     * @throws Exception
     * @throws InvalidConfigException
     */
    public function isValid(Entry $entry): bool
    {
        $name = $this->getCookieName($entry);
        $cookie = Craft::$app->getRequest()->cookies->get($name);
        if ($cookie) {
            $value = Craft::$app->getSecurity()->unmaskToken($cookie->value);
            
            return $value === $entry->getEntryPasswordFieldValue();
        }
        
        return false;
    }
}
