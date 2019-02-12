<?php
/**
 * See LICENSE.md for license details.
 */
declare(strict_types=1);

namespace Netresearch\VaultImport\Webservice;

interface VaultClientAdapterInterface
{
    /**
     * Fetch secret from given path
     *
     * @param string $uri
     * @param string $token
     * @param string $secretPath
     * @return string[]
     * @throws \Psr\Cache\InvalidArgumentException
     */
    public function fetchSecret($uri, $token, $secretPath);
}
