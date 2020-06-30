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

use Nyholm\Psr7\Factory\Psr17Factory;
use Nyholm\Psr7Server\ServerRequestCreator;

$psr17Factory = new Psr17Factory();
$creator = new ServerRequestCreator(
    $psr17Factory, // ServerRequestFactory
    $psr17Factory, // UriFactory
    $psr17Factory, // UploadedFileFactory
    $psr17Factory  // StreamFactory
);

$server_request = $creator->fromGlobals();

$request_body = trim(file_get_contents('php://input'));

$register_options = $_SESSION['register_options'] ?? null;
$user = $_SESSION['user'] ?? null;
if (empty($register_options) || empty($user)) {
    generic_error('Session lost, please try again');
}

try {
    $credential_source = $server->loadAndCheckAttestationResponse(
        $request_body,
        $register_options,
        $server_request
    );

    // The user entity and the public key credential source can now be stored using their repository
    // The Public Key Credential Source repository must implement Webauthn\PublicKeyCredentialSourceRepository
    $credentials->saveCredentialSource($credential_source);
} catch(\Throwable $e) {
    /* echo $e->getTraceAsString() . "\n";
     * die(); */
    generic_error($e->getMessage());
}

$response = [
    'success' => true,
    'msg' => 'Registration saved',
];

echo json_encode($response, JSON_PRETTY_PRINT) . "\n";
