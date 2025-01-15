<?php

namespace App\Controller;

class NewsController
{
    public function __invoke(int $id)
    {
        echo "Page des news" . $id;
    }
}
