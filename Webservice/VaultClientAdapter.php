<?php
/**
 * See LICENSE.md for license details.
 */
declare(strict_types=1);

namespace Netresearch\VaultImport\Webservice;

use Magento\Framework\Exception\AuthenticationException;
use Netresearch\VaultImport\Webservice\Transport\GuzzleWrapperFactory;
use Psr\Log\LoggerInterface;
use Vault\AuthenticationStrategies\TokenAuthenticationStrategyFactory;
use Vault\ClientFactory;

/**
 * Class VaultClientAdapter
 *
 * @package Netresearch\VaultImport\Webservice
 */
class VaultClientAdapter implements VaultClientAdapterInterface
{
    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var GuzzleWrapperFactory
     */
    private $guzzleWrapperFactory;

    /**
     * @var ClientFactory
     */
    private $clientFactory;

    /**
     * @var TokenAuthenticationStrategyFactory
     */
    private $tokenStrategyFactory;

    /**
     * VaultClientAdapter constructor.
     *
     * @param LoggerInterface $logger
     * @param GuzzleWrapperFactory $guzzleWrapperFactory
     * @param ClientFactory $clientFactory
     * @param TokenAuthenticationStrategyFactory $tokenStrategyFactory
     */
    public function __construct(
        LoggerInterface $logger,
        GuzzleWrapperFactory $guzzleWrapperFactory,
        ClientFactory $clientFactory,
        TokenAuthenticationStrategyFactory $tokenStrategyFactory
    ) {
        $this->logger = $logger;
        $this->guzzleWrapperFactory = $guzzleWrapperFactory;
        $this->clientFactory = $clientFactory;
        $this->tokenStrategyFactory = $tokenStrategyFactory;
    }

    /**
     * Fetch secret from given path
     *
     * @param string $uri
     * @param string $token
     * @param string $secretPath
     * @return string[]
     * @throws \Psr\Cache\InvalidArgumentException
     * @throws AuthenticationException
     */
    public function fetchSecret($uri, $token, $secretPath): array
    {
        $client = $this->prepareClient($uri, $token);

        if (!$client->authenticate()) {
            throw new AuthenticationException(__('Authentication failed'));
        }
        $response = $client->read($secretPath);

        return $response->getData();
    }

    /**
     * Create Client object and set authentication data.
     *
     * @param string $uri
     * @param string $token
     * @return \Vault\Client
     */
    private function prepareClient($uri, $token): \Vault\Client
    {
        $transportObject = $this->guzzleWrapperFactory->create(
            ['config' => ['base_uri' => $uri]]
        );
        $client = $this->clientFactory->create(
            ['transport' => $transportObject, 'logger' => $this->logger]
        );
        $strategy = $this->tokenStrategyFactory->create(
            ['token' => $token]
        );
        $client->setAuthenticationStrategy($strategy);

        return $client;
    }
}
