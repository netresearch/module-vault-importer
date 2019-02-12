<?php
/**
 * See LICENSE.md for license details.
 */
declare(strict_types=1);

namespace Netresearch\VaultImport\Webservice;

use Netresearch\VaultImport\Webservice\Transport\GuzzleWrapper;
use Netresearch\VaultImport\Webservice\Transport\GuzzleWrapperFactory;
use Psr\Log\LoggerInterface;
use Vault\AuthenticationStrategies\TokenAuthenticationStrategy;
use Vault\Client;
use Vault\ClientFactory;
use Vault\Transports\Transport;

class VaultClientAdapter implements VaultClientAdapterInterface
{

    /** @var LoggerInterface */
    private $logger;

    /**
     * VaultClientAdapter constructor.
     *
     * @param LoggerInterface $logger
     */
    public function __construct(
        LoggerInterface $logger
    ) {
        $this->logger = $logger;
    }

    /**
     * Fetch secret from given path
     *
     * @param string $uri
     * @param string $token
     * @param string $secretPath
     * @return string[]
     * @throws \Psr\Cache\InvalidArgumentException
     */
    public function fetchSecret($uri, $token, $secretPath)
    {
        /** @var Transport $transportObject */
        $transportObject = new GuzzleWrapper(
            ['base_uri' => $uri]
        );
        /** @var Client $client */
        $client = new Client($transportObject, $this->logger);

        $authenticated = $client->setAuthenticationStrategy(
            new TokenAuthenticationStrategy(
                $token
            )
        )->authenticate();

        if (!$authenticated) {
            throw new AuthenticationException("Authentification failure");
        }
        $response = $client->read($secretPath);

        return $response->getData();
    }
}
