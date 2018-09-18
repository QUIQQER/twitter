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
        $Twitter = QUI::getPackage('quiqqer/twitter');

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

        $Connection = new TwitterOAuth(
            $Twitter->getConfig()->get('auth', 'TWITTER_CONSUMER_KEY'),
            $Twitter->getConfig()->get('auth', 'TWITTER_CONSUMER_SECRET'),
            $data['oauth_token'],
            $data['oauth_secret']
        );

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
}
