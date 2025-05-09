<?php

namespace Metroid\Model;

use Metroid\Database\Connection;
use Metroid\Database\Model\TableAbstractModel;

class ModelFactory
{
    /**
     * Create an instance of the specified model class.
     *
     * This function instantiates a new object of the given class name,
     * with a database connection instance injected into it.
     *
     * @param string $className The fully qualified name of the model class to instantiate.
     * @return TableAbstractModel An instance of the specified model class.
     */

    static public function create(string $className): TableAbstractModel
    {
        return new $className(Connection::getInstance());
    }
}
