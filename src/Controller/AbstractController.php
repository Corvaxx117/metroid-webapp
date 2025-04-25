<?php

namespace App\Controller;

use App\FlashMessage\FlashMessage;
use App\View\ViewRenderer;

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
