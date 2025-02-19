<?php

namespace App\Controller;

use App\Services\ViewRenderer;

class HomeController
{
    public function __construct(private ViewRenderer $viewRenderer) {}
    public function __invoke()
    {
        $data = [
            'title' => 'Page d\'accueil',
            'content' => 'Bienvenue sur la page d\'accueil !'
        ];
        $this->viewRenderer->render('home.phtml', $data);
    }
}
