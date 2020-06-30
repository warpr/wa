<?php declare(strict_types=1);
/**
 *   This file is part of wawawa, Web Auth Web Auth Web Auth.
 *   Copyright (C) 2020  Kuno Woudt <kuno@frob.nl>
 *
 *   This program is free software: you can redistribute it and/or modify
 *   it under the terms of copyleft-next 0.3.1.  See copyleft-next-0.3.1.txt.
 *
 *   SPDX-License-Identifier: copyleft-next-0.3.1
 */

namespace wawawa;

require_once __DIR__ . '/common.php';

use Webauthn\PublicKeyCredentialSource;
use Webauthn\PublicKeyCredentialRequestOptions;

$request_body = file_get_contents('php://input');
$post = trim($request_body);
if ($post) {
    $post = json_decode($post, true);
}

$user = $all_users->findWebauthnUserByUsername($post['username'] ?? '');

// Get the list of authenticators associated to the user
$credential_sources = $user ? $credentials->findAllForUserEntity($user) : [];

// Convert the Credential Sources into Public Key Credential Descriptors
$allowed_credentials = array_map(function (PublicKeyCredentialSource $credential) {
    return $credential->getPublicKeyCredentialDescriptor();
}, $credential_sources);

// We generate the set of options.
$login_options = $server->generatePublicKeyCredentialRequestOptions(
    $post['userVerification'] ?? 'discouraged',
    $allowed_credentials
);

$_SESSION['login_options'] = $login_options;
$_SESSION['user'] = $user;

echo json_encode($login_options, JSON_PRETTY_PRINT) . "\n";
