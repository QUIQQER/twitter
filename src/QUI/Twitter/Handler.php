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
     * @deprecated Use getConnectionByTwitterUsername() instead
     *
     * @param string $twitterUsername
     *
     * @return TwitterOAuth
     *
     * @throws QUI\Exception
     */
    public static function getConnectionByUser($twitterUsername)
    {
        return self::getConnectionByTwitterUsername($twitterUsername);
    }


    /**
     * Returns the Twitter-Connection for a QUIQQER user.
     * Throws an exception if the user hasn't registered a Twitter-account.
     *
     * @param QUI\Interfaces\Users\User $User
     *
     * @return TwitterOAuth
     *
     * @throws QUI\Exception
     */
    public static function getConnectionByQuiqqerUser(QUI\Interfaces\Users\User $User)
    {
        $result = QUI::getDataBase()->fetch(array(
            'from'  => QUI\Twitter\UserHandler::getUserDatabaseTableName(),
            'where' => array(
                'uid' => $User->getId()
            ),
            'limit' => '1'
        ));

        if (!isset($result[0])) {
            throw new QUI\Exception(array(
                'quiqqer/twitter',
                'exception.twitteruser.not.found'
            ));
        }

        $data = $result[0];

        $Connection = self::getConnectionByOAuthToken($data['oauth_token'], $data['oauth_secret']);

        return $Connection;
    }


    /**
     * Returns the Twitter-Connection for the current QUIQQER user-session
     *
     * @return TwitterOAuth
     *
     * @throws QUI\Exception
     */
    public static function getConnectionBySession()
    {
        return self::getConnectionByQuiqqerUser(QUI::getUserBySession());
    }


    /**
     * Returns the Twitter connection for an OAuth Token and it's secret.
     *
     * @param $oAuthToken
     * @param $oAuthTokenSecret
     *
     * @return TwitterOAuth
     *
     * @throws QUI\Exception
     */
    public static function getConnectionByOAuthToken($oAuthToken, $oAuthTokenSecret)
    {
        return new TwitterOAuth(
            Config::getConsumerKey(),
            Config::getConsumerSecret(),
            $oAuthToken,
            $oAuthTokenSecret
        );
    }


    /**
     * Return the Twitter connection for a Twitter username
     *
     * @param string $twitterUsername
     *
     * @return TwitterOAuth
     *
     * @throws QUI\Exception
     */
    public static function getConnectionByTwitterUsername($twitterUsername)
    {
        $result = QUI::getDataBase()->fetch(array(
            'from'  => QUI\Twitter\UserHandler::getUserDatabaseTableName(),
            'where' => array(
                'username' => $twitterUsername
            )
        ));

        if (!isset($result[0])) {
            throw new QUI\Exception(array(
                'quiqqer/twitter',
                'exception.twitteruser.not.found'
            ));
        }

        $data = $result[0];

        $Connection = self::getConnectionByOAuthToken($data['oauth_token'], $data['oauth_secret']);

        return $Connection;
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
