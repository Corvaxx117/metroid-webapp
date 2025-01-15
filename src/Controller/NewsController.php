<?php

namespace App\Controller;

class NewsController extends BaseController
{
    public function __invoke(int $id)
    {
        echo "Page des news" . $id;
    }
}
