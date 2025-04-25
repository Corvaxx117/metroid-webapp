<?php

namespace Mini\Model;

use Mini\Database\Connection;

class ModelFactory
{
    static public function create(string $className): TableAbstractModel
    {
        return new $className(Connection::getInstance());
    }
}
