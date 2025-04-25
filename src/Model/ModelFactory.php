<?php

namespace App\Model;

use App\Database\Connection;

class ModelFactory
{
    static public function create(string $className): TableAbstractModel
    {
        return new $className(Connection::getInstance());
    }
}
