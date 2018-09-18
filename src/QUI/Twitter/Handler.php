<?php

namespace QUI\Twitter;

use QUI;
use Abraham\TwitterOAuth\TwitterOAuth;

/**
 * Class Handler
 * @package QUI\Twitter
 */
class Handler
{
    /**
     * Return the database table name from the twitter users.
     *
     * @deprecated Use UserHandler::getUserDatabaseTableName() instead.
     *
     * @return string
     */
    public static function getUserDatabaseTableName()
    {
        return UserHandler::getUserDatabaseTableName();
    }


    /**
     * Return the Twitter connection for a Twitter username
     *
     * @deprecated Use Connection::getByTwitterUsername() instead
     *
     * @param string $twitterUsername
     *
     * @return TwitterOAuth
     *
     * @throws QUI\Exception
     */
    public static function getConnectionByUser($twitterUsername)
    {
        return Connection::getByTwitterUsername($twitterUsername);
    }


    /**
     * Returns an array of all twitter usernames in the system
     *
     * @deprecated Use getTwitterUsernames() instead
     * @return string[]
     */
    public static function getTwitterUser()
    {
        return UserHandler::getTwitterUsernames();
    }
}
