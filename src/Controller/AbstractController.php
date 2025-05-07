<?php

namespace Metroid\Controller;

use Metroid\FlashMessage\FlashMessage;
use Metroid\View\ViewRenderer;

abstract class AbstractController
{
    public function __construct(
        readonly protected ViewRenderer $viewRenderer,
        readonly protected FlashMessage $flashMessage,
    ) {
        $this->init();
    }

    protected function init(): void {}
}
