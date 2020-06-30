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
require_once __DIR__ . '/store.php';

use Webauthn\PublicKeyCredentialSource;
use Webauthn\PublicKeyCredentialCreationOptions;

$request_body = file_get_contents('php://input');
$post = trim($request_body);
if ($post) {
    $post = json_decode($post, true);
}

$user = $all_users->findWebauthnUserByUsername($post['username'] ?? '');

if (empty($user)) {
    not_found("User not found");
}


// This avoids multiple registration of the same authenticator with the user account
// Get the list of authenticators associated to the user
$credential_sources = $user ? $credentials->findAllForUserEntity($user) : [];

// Convert the Credential Sources into Public Key Credential Descriptors
$exclude_credentials = array_map(function (PublicKeyCredentialSource $credential) {
    return $credential->getPublicKeyCredentialDescriptor();
}, $credential_sources);

$register_options = $server->generatePublicKeyCredentialCreationOptions(
    $user,
    PublicKeyCredentialCreationOptions::ATTESTATION_CONVEYANCE_PREFERENCE_NONE,
    $exclude_credentials
);

$_SESSION['register_options'] = $register_options;
$_SESSION['user'] = $user;

echo json_encode($register_options, JSON_PRETTY_PRINT) . "\n";
