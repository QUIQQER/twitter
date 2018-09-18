<?php

namespace QUI\Twitter;

use Abraham\TwitterOAuth\TwitterOAuth;
use QUI;

class Connection
{
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
    public static function getByQuiqqerUser(QUI\Interfaces\Users\User $User)
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

        $Connection = self::getByOAuthToken($data['oauth_token'], $data['oauth_secret']);

        return $Connection;
    }


    /**
     * Returns the Twitter-Connection for the current QUIQQER user-session
     *
     * @return TwitterOAuth
     *
     * @throws QUI\Exception
     */
    public static function getBySession()
    {
        return self::getByQuiqqerUser(QUI::getUserBySession());
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
    public static function getByOAuthToken($oAuthToken, $oAuthTokenSecret)
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
    public static function getByTwitterUsername($twitterUsername)
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

        $Connection = self::getByOAuthToken($data['oauth_token'], $data['oauth_secret']);

        return $Connection;
    }
}
