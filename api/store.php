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

use Webauthn\PublicKeyCredentialSource;
use Webauthn\PublicKeyCredentialSourceRepository;
use Webauthn\PublicKeyCredentialUserEntity;

class CredentialRepository implements PublicKeyCredentialSourceRepository
{
    private $path = '/tmp/registrations.json';

    public function findOneByCredentialId(string $credential_id): ?PublicKeyCredentialSource
    {
        $encoded_id = base64_encode($credential_id);

        $data = $this->read();
        if (empty($data[$encoded_id])) {
            return null;
        }

        return PublicKeyCredentialSource::createFromArray($data[$encoded_id]);
    }

    public function findAllForUserEntity(PublicKeyCredentialUserEntity $user): array
    {
        $sources = [];
        foreach($this->read() as $data)
        {
            $source = PublicKeyCredentialSource::createFromArray($data);
            if ($source->getUserHandle() === $user->getId())
            {
                $sources[] = $source;
            }
        }
        return $sources;
    }

    public function saveCredentialSource(PublicKeyCredentialSource $source): void
    {
        $data = $this->read();
        $encoded_id = base64_encode($source->getPublicKeyCredentialId());
        $data[$encoded_id] = $source;
        $this->write($data);
    }

    private function read(): array
    {
        if (file_exists($this->path))
        {
            return json_decode(file_get_contents($this->path), true);
        }
        return [];
    }

    private function write(array $data): void
    {
        file_put_contents($this->path, json_encode($data), LOCK_EX);
    }
}

class UserRepository
{
    private $users = [
        [
            'display_name' => 'Kuno Woudt',
            'id' => '23',
            'username' => 'kuno@frob.nl',
        ],
        [
            'display_name' => 'Agent Kuno',
            'id' => '30',
            'username' => 'frob@outlook.com',
        ]
    ];

    public function findWebauthnUserByUsername(string $username): ?PublicKeyCredentialUserEntity
    {
        foreach ($this->users as $user) {
            if ($username === $user['username']) {
                return $this->createUserEntity($user);
            }
        }

        return null;
    }

    public function findWebauthnUserByUserHandle(string $userHandle): ?PublicKeyCredentialUserEntity
    {
        foreach ($this->users as $user) {
            if ($username === $user['id']) {
                return $this->createUserEntity($user);
            }
        }

        return null;
    }

    private function createUserEntity($user): PublicKeyCredentialUserEntity
    {
        return new PublicKeyCredentialUserEntity(
            $user['username'],
            $user['id'],
            $user['display_name'],
        );
    }
}
