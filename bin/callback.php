<?php

/**
 * Twitter redirect
 */

define('QUIQQER_SYSTEM', true);
require dirname(dirname(dirname(dirname(__FILE__)))) . "/header.php";

use Abraham\TwitterOAuth\TwitterOAuth;

$Session = QUI::getSession();

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
    header('Location: ./clearsessions.php');
    exit;
}

$Connection = new TwitterOAuth(
    CONSUMER_KEY,
    CONSUMER_SECRET,
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

} else {
    $Session->destroy();
    header('Location: ./');
    exit;
}
