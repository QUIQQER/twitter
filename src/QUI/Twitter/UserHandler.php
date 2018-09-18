<?php

namespace QUI\Twitter;

use QUI;

class UserHandler
{
    /**
     * Returns an array of all twitter usernames in the system
     *
     * @return string[]
     */
    public static function getTwitterUsernames()
    {
        $result = QUI::getDataBase()->fetch(array(
            'select' => 'username',
            'from'   => self::getUserDatabaseTableName()
        ));

        $usernames = array();

        foreach ($result as $entry) {
            $usernames[] = $entry['username'];
        }

        return $usernames;
    }


    /**
     * Returns the database table name for Twitter-userdata
     *
     * @return string
     */
    public static function getUserDatabaseTableName()
    {
        return QUI::getDBTableName('twitter_users');
    }
}