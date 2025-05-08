<?php

namespace Metroid\FlashMessage;

use Metroid\FlashMessage\Handler\SessionHandler;
use Metroid\FlashMessage\Handler\NoSessionHandler;

class FlashMessage
{
    private object $handler;

    public function __construct(bool $useSession = true)
    {
        $this->handler = $useSession ? new SessionHandler() : new NoSessionHandler();
    }

    public function add(string $type, string $message): void
    {
        $this->handler->add($type, $message);
    }

    public function get(string $type): array
    {
        return $this->handler->get($type);
    }

    public function has(string $type): bool
    {
        return $this->handler->has($type);
    }

    public function renderFlash(): void
    {
        if (method_exists($this->handler, 'renderFlash')) {
            $this->handler->renderFlash();
        }
    }
}
