<?php

namespace Metroid\Model;

use Metroid\Database\Connection;

class ModelFactory
{
    static public function create(string $className): TableAbstractModel
    {
        return new $className(Connection::getInstance());
    }
}
