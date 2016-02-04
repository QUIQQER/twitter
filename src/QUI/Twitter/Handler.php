<?php

namespace QUI\Twitter;

use QUI;

/**
 * Class Handler
 * @package QUI\Twitter
 */
class Handler
{
    /**
     * Return the database table name from the twitter users
     *
     * @return string
     */
    public static function getUserDatabaseTableName()
    {
        return QUI::getDBTableName('twitter_users');
    }
}
