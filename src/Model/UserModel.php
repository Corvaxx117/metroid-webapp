<?php

namespace App\Model;

use App\Model\BaseModel;

class UserModel extends BaseModel
{
    protected function getTableName(): string
    {
        return 'users';
    }

    /**
     * Trouve un utilisateur par son ID.
     */
    public function find(int $id): ?array
    {
        return $this->findOneBy(['id' => $id]);
    }

    /**
     * Récupère tous les utilisateurs.
     */
    public function findAll(): array
    {
        return parent::findAll();
    }

    /**
     * Trouve un utilisateur par son email.
     */
    public function findByEmail(string $email): ?array
    {
        return $this->findOneBy(['email' => $email]);
    }
}
