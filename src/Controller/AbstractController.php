<?php

namespace Metroid\Controller;

use Metroid\FlashMessage\FlashMessage;
use Metroid\View\ViewRenderer;
use Metroid\Http\Response;


abstract class AbstractController
{
    public function __construct(
        readonly protected ViewRenderer $viewRenderer,
        readonly protected FlashMessage $flashMessage,
    ) {

        $this->init();
    }

    protected function init(): void {}

    /**
     * Raccourci pour rendre une vue avec Response directement.
     */
    protected function render(string $view, array $data = [], int $statusCode = 200, array $headers = []): Response
    {
        return $this->viewRenderer->render($view, $data, $statusCode, $headers);
    }
}
