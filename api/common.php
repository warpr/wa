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

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/store.php';

use Webauthn\Server;
use Webauthn\PublicKeyCredentialRpEntity;
use Webauthn\PublicKeyCredentialUserEntity;

$relaying_party = new PublicKeyCredentialRpEntity(
    'WaWaWa demo',
    'kuno.app',
);

$credentials = new CredentialRepository();
$all_users = new UserRepository();

$server = new Server($relaying_party, $credentials, null);

session_name('wawawa');
session_start();

function not_found($msg) {
    $body = [ 'success' => false, 'msg' => $msg ];
    echo json_encode($body, JSON_PRETTY_PRINT) . "\n";
    die();
}

function generic_error($msg) {
    $body = [ 'success' => false, 'msg' => $msg ];
    echo json_encode($body, JSON_PRETTY_PRINT) . "\n";
    die();
}

function avatar($email)
{
    $hash = empty($email) ? md5('lud@example.com') : md5($email);
    return 'https://www.gravatar.com/avatar/' . $hash . '?d=mp';
}
