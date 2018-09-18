<?php

namespace QUI\Twitter;

use QUI;

class Config
{
    /**
     * Returns this package's config or false if there is none
     *
     * @return bool|QUI\Config
     *
     * @throws QUI\Exception
     */
    public static function getPackageConfig()
    {
        return QUI::getPackage('quiqqer/twitter')->getConfig();
    }


    /**
     * Returns the Twitter Consumer Key set in the system's config
     *
     * @return array|string
     *
     * @throws QUI\Exception
     */
    public static function getConsumerKey()
    {
        return self::getPackageConfig()->get('auth', 'TWITTER_CONSUMER_KEY');
    }


    /**
     * Returns the Twitter Consumer Secret set in the system's config
     *
     * @return string
     *
     * @throws QUI\Exception
     */
    public static function getConsumerSecret()
    {
        return self::getPackageConfig()->get('auth', 'TWITTER_CONSUMER_SECRET');
    }
}