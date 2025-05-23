<?php

namespace Metroid\Http;

class RedirectResponse extends Response
{
    /**
     * Initialise une nouvelle instance de RedirectResponse.
     *
     * @param string $url L'URL vers laquelle rediriger.
     * @param int $status Le code de statut HTTP, 302 par défaut.
     * @param array $headers Les en-têtes HTTP supplémentaires à inclure.
     */

    public function __construct(string $url, int $status = 302, array $headers = [])
    {
        $headers['Location'] = $url;
        parent::__construct(
            statusCode: $status,
            headers: $headers
        );
    }
}
