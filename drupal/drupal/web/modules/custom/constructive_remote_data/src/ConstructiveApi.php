<?php

declare(strict_types=1);

namespace Drupal\constructive_remote_data;

use Drupal\Component\Serialization\Json;
use Drupal\Component\Datetime\Time;
use Drupal\Core\Cache\CacheBackendInterface;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;

/**
 * The Constructive API client.
 */
final class ConstructiveApi {

  /**
   * The HTTP client.
   *
   * @var \GuzzleHttp\Client
   */
  private Client $client;

  /**
   * The cache backend.
   *
   * @var \Drupal\Core\Cache\CacheBackendInterface
   */
  private CacheBackendInterface $cache;

  /**
   * The time component.
   *
   * @var \Drupal\Component\Datetime\Time;
   */
  protected Time $time;

  /**
   * The API endpoint.
   *
   * @const string
   */
  protected const ENDPOINT = 'https://bedrock.lndo.site/wp-json/constructive-api/v1/posts';

  /**
   * The cache interval.
   *
   * @const int
   */
  protected const INTERVAL = 900;

  /**
   * Constructs a new ConstructiveApi object.
   *
   * @param \GuzzleHttp\Client $client
   *   The HTTP client.
   * @param \Drupal\Core\Cache\CacheBackendInterface $cache
   *   The cache backend.
   * @param \Drupal\Component\Datetime\Time $time
   *   The time component.
   */
  public function __construct(Client $client, CacheBackendInterface $cache, Time $time) {
    $this->client = $client;
    $this->cache = $cache;
    $this->time = $time;
  }

  /**
   * Gets results from the API.
   *
   * @return array
   *   The result.
   *
   * @throws \GuzzleHttp\Exception\GuzzleException
   */
  public function getPosts(): array {
    $cache_key = 'constructive:api';
    if ($cache = $this->cache->get($cache_key)) {
      return $cache->data;
    }
    try {
      $time = (int) $this->time->getRequestTime();

      $response = $this->client->get(self::ENDPOINT);
      $data = Json::decode((string) $response->getBody());
      $this->cache->set($cache_key, $data, self::INTERVAL + $time);
      return $data;
    }
    catch (RequestException $e) {
      return [];
    }
  }

}
