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
     * Return the database table name from the twitter users
     *
     * @return string
     */
    public static function getUserDatabaseTableName()
    {
        return QUI::getDBTableName('twitter_users');
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
            'from'  => QUI\Twitter\Handler::getUserDatabaseTableName(),
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
            self::getConsumerKey(),
            self::getConsumerSecret(),
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
            'from'  => QUI\Twitter\Handler::getUserDatabaseTableName(),
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
        return self::getTwitterUsernames();
    }

    /**
     * Returns an array of all twitter usernames in the system
     *
     * @return string[]
     */
    public static function getTwitterUsernames()
    {
        $result = QUI::getDataBase()->fetch(array(
            'select' => 'username',
            'from'   => QUI\Twitter\Handler::getUserDatabaseTableName()
        ));

        $usernames = array();

        foreach ($result as $entry) {
            $usernames[] = $entry['username'];
        }

        return $usernames;
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
}
