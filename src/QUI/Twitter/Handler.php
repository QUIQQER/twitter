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
     * Return the twitter connection from an user
     *
     * @param string $twitterUsername
     * @return TwitterOAuth
     *
     * @throws QUI\Exception
     */
    public static function getConnectionByUser($twitterUsername)
    {
        $Twitter = QUI::getPackage('quiqqer/twitter');

        $result = QUI::getDataBase()->fetch(array(
            'from' => QUI\Twitter\Handler::getUserDatabaseTableName(),
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
            $Twitter->getConfig()->get('auth', 'CONSUMER_KEY'),
            $Twitter->getConfig()->get('auth', 'CONSUMER_SECRET'),
            $data['oauth_token'],
            $data['oauth_secret']
        );

        return $Connection;
    }

    /**
     * Return all twitter users in the system
     *
     * @return array
     */
    public static function getTwitterUser()
    {
        $result = QUI::getDataBase()->fetch(array(
            'select' => 'username',
            'from' => QUI\Twitter\Handler::getUserDatabaseTableName()
        ));

        $usernames = array();

        foreach ($result as $entry) {
            $usernames[] = $entry['usernames'];
        }

        return $usernames;
    }
}
