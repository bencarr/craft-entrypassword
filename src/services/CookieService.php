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
     * @param Entry $entry
     * @throws Exception
     * @throws InvalidConfigException
     */
    public function set(Entry $entry)
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
     * @param Entry $entry
     * @return string
     * @throws Exception
     * @throws InvalidConfigException
     */
    protected function getCookieName(Entry $entry)
    {
        $entryIdHash = Craft::$app->getSecurity()->hashData($entry->id);

        return self::PREFIX . $entryIdHash;
    }

    protected function getCookieValue(Entry $entry)
    {
        $password = $entry->getEntryPasswordFieldValue();

        return Craft::$app->getSecurity()->maskToken($password);
    }

    protected function getCookieExpiration(Entry $entry)
    {
        $field = $entry->getEntryPasswordField();
        if ($field) {
            return $field->cookieExpiration;
        }

        return null;
    }

    /**
     * @param Entry $entry
     * @throws Exception
     * @throws InvalidConfigException
     */
    public function remove(Entry $entry)
    {
        $name = $this->getCookieName($entry);

        Craft::$app->getResponse()->cookies->remove($name);
    }

    /**
     * @param Entry $entry
     * @return Cookie
     * @throws Exception
     * @throws InvalidConfigException
     */
    public function get(Entry $entry)
    {
        $name = $this->getCookieName($entry);

        return Craft::$app->getRequest()->cookies->get($name);
    }

    /**
     * @param Entry $entry
     * @return bool
     * @throws Exception
     * @throws InvalidConfigException
     */
    public function isValid(Entry $entry)
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
