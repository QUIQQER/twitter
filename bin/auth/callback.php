<?php

/**
 * Twitter redirect
 */

define('QUIQQER_SYSTEM', true);
require dirname(dirname(dirname(dirname(dirname(__FILE__))))) . "/header.php";

use Abraham\TwitterOAuth\TwitterOAuth;

$Session = QUI::getSession();
$Twitter = QUI::getPackage('quiqqer/twitter');

$request_token = array(
    'oauth_token' => $Session->get('oauth_token'),
    'oauth_token_secret' => $Session->get('oauth_token_secret')
);

if (isset($_REQUEST['denied'])) {
    exit('Permission was denied. Please start over.');
}

if (isset($_REQUEST['oauth_token'])
    && $request_token['oauth_token'] !== $_REQUEST['oauth_token']
) {
    $Session->set('oauth_status', 'oldtoken');
    header('Location: ' . URL_DIR);
    exit;
}

if (!isset($_REQUEST['oauth_verifier'])) {
    $_REQUEST['oauth_verifier'] = '';
}


try {
    $Connection = new TwitterOAuth(
        $Twitter->getConfig()->get('auth', 'CONSUMER_KEY'),
        $Twitter->getConfig()->get('auth', 'CONSUMER_SECRET'),
        $request_token['oauth_token'],
        $request_token['oauth_token_secret']
    );

    $access_token = $Connection->oauth(
        "oauth/access_token",
        array("oauth_verifier" => $_REQUEST['oauth_verifier'])
    );

    if (200 == $Connection->getLastHttpCode()) {
        $Session->set('access_token', $access_token);
        $Session->set('status', 'verified');

        $Session->del('oauth_token');
        $Session->del('oauth_token_secret');

        $userData = $Connection->get('account/verify_credentials');

        $result = QUI::getDataBase()->fetch(array(
            'from' => QUI\Twitter\Handler::getUserDatabaseTableName(),
            'where' => array(
                'oauth_uid' => $userData->id
            )
        ));

        if (!isset($result[0])) {
            QUI::getDataBase()->insert(
                QUI\Twitter\Handler::getUserDatabaseTableName(),
                array(
                    'uid' => $User->getId(),
                    'oauth_uid' => $userData->id,
                    'oauth_provider' => 'twitter',
                    'oauth_token' => $access_token['oauth_token'],
                    'oauth_secret' => $access_token['oauth_token_secret'],
                    'username' => $userData->screen_name
                )
            );
        } else {
            QUI::getDataBase()->update(
                QUI\Twitter\Handler::getUserDatabaseTableName(),
                array(
                    'uid' => $User->getId(),
                    'oauth_provider' => 'twitter',
                    'oauth_token' => $access_token['oauth_token'],
                    'oauth_secret' => $access_token['oauth_token_secret'],
                    'username' => $userData->screen_name
                ),
                array(
                    'oauth_uid' => $userData->id
                )
            );
        }

    } else {
        $Session->destroy();
        header('Location: ' . URL_DIR);
        exit;
    }

} catch (\Exception $Exception) {
    QUI\System\Log::writeException($Exception);

    $Session->destroy();
    header('Location: ' . URL_DIR);
    exit;
}