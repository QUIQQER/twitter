<?php

/**
 * Twitter redirect
 */

define('QUIQQER_SYSTEM', true);
require dirname(dirname(dirname(dirname(dirname(__FILE__)))))."/header.php";

use Abraham\TwitterOAuth\TwitterOAuth;

$Session = QUI::getSession();
$Twitter = QUI::getPackage('quiqqer/twitter');

$request_token = [
    'oauth_token'        => $Session->get('oauth_token'),
    'oauth_token_secret' => $Session->get('oauth_token_secret')
];

if (isset($_REQUEST['denied'])) {
    exit('Permission was denied. Please start over.');
}

if (isset($_REQUEST['oauth_token'])
    && $request_token['oauth_token'] !== $_REQUEST['oauth_token']
) {
    $Session->set('oauth_status', 'oldtoken');
    echo "Tokens are invalid";
    echo '<pre>';
    print_r($request_token, true);
    echo '</pre>';

    exit;
}

$User = QUI::getUserBySession();

if (!isset($_REQUEST['oauth_verifier'])) {
    $_REQUEST['oauth_verifier'] = '';
}


try {
    $Connection = new TwitterOAuth(
        $Twitter->getConfig()->get('auth', 'TWITTER_CONSUMER_KEY'),
        $Twitter->getConfig()->get('auth', 'TWITTER_CONSUMER_SECRET'),
        $request_token['oauth_token'],
        $request_token['oauth_token_secret']
    );

    $access_token = $Connection->oauth(
        "oauth/access_token",
        ["oauth_verifier" => $_REQUEST['oauth_verifier']]
    );

    $Connection = new TwitterOAuth(
        $Twitter->getConfig()->get('auth', 'TWITTER_CONSUMER_KEY'),
        $Twitter->getConfig()->get('auth', 'TWITTER_CONSUMER_SECRET'),
        $access_token['oauth_token'],
        $access_token['oauth_token_secret']
    );

    $userData = $Connection->get('account/verify_credentials');


    if (200 == $Connection->getLastHttpCode()) {
        $Session->set('access_token', $access_token);
        $Session->set('status', 'verified');

        if (isset($userData->errors) && count($userData->errors)) {
            throw new QUI\Exception(
                $userData->errors[0]->message,
                $userData->errors[0]->code
            );
        }


        $result = QUI::getDataBase()->fetch([
            'from'  => QUI\Twitter\UserHandler::getUserDatabaseTableName(),
            'where' => [
                'oauth_uid' => $userData->id
            ]
        ]);

        if (!isset($result[0])) {
            QUI::getDataBase()->insert(
                QUI\Twitter\UserHandler::getUserDatabaseTableName(),
                [
                    'uid'            => $User->getId(),
                    'oauth_uid'      => $userData->id,
                    'oauth_provider' => 'twitter',
                    'oauth_token'    => $access_token['oauth_token'],
                    'oauth_secret'   => $access_token['oauth_token_secret'],
                    'username'       => $userData->screen_name
                ]
            );
        } else {
            QUI::getDataBase()->update(
                QUI\Twitter\UserHandler::getUserDatabaseTableName(),
                [
                    'uid'            => $User->getId(),
                    'oauth_provider' => 'twitter',
                    'oauth_token'    => $access_token['oauth_token'],
                    'oauth_secret'   => $access_token['oauth_token_secret'],
                    'username'       => $userData->screen_name
                ],
                [
                    'oauth_uid' => $userData->id
                ]
            );
        }

        header('Location: '.URL_DIR);
    } else {
//        header('Location: ' . URL_DIR);
        exit;
    }

} catch (\Exception $Exception) {
    QUI\System\Log::writeException($Exception);

    echo $Exception->getMessage();
    exit;
}
