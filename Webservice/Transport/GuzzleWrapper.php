<?php
/**
 * See LICENSE.md for license details.
 */
declare(strict_types=1);

namespace Netresearch\VaultImport\Webservice\Transport;

use GuzzleHttp\Client;
use Vault\Transports\Transport;

/**
 * Class GuzzleWrapper
 *
 * Provides implementation for Vault\Transports\Transport using the GuzzleHttp\Client
 *
 * @author Paul Siedler <paul.siedler@netresearch.de>
 * @link http://www.netresearch.de/
 */
class GuzzleWrapper extends Client implements Transport
{}
