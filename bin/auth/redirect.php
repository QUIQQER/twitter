<?php

/**
 * Twitter redirect
 */

define('QUIQQER_SYSTEM', true);
require dirname(dirname(dirname(dirname(dirname(__FILE__))))) . "/header.php";

use Abraham\TwitterOAuth\TwitterOAuth;

$Twitter = QUI::getPackage('quiqqer/twitter');

// The TwitterOAuth instance
$Connection = new TwitterOAuth(
    $Twitter->getConfig()->get('auth', 'CONSUMER_KEY'),
    $Twitter->getConfig()->get('auth', 'CONSUMER_SECRET')
);

$request_token = $Connection->oauth(
    'oauth/request_token',
    array('oauth_callback' => OAUTH_CALLBACK)
);

switch ($Connection->getLastHttpCode()) {
    case 200:
        $Session->set('oauth_token', $request_token['oauth_token']);
        $Session->set('oauth_token_secret', $request_token['oauth_token_secret']);

        $url = $Connection->url(
            'oauth/authorize',
            array('oauth_token' => $Session->get('oauth_token'))
        );

        header('Location: ' . $url);
        break;

    default:
        dir('Could not connect to Twitter. Refresh the page or try again later.');
}
