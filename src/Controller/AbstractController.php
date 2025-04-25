<?php

namespace Mini\Controller;

use Mini\FlashMessage\FlashMessage;
use Mini\View\ViewRenderer;

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
