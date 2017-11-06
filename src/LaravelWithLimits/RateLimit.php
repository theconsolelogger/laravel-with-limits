<?php

namespace LaravelWithLimits;

use Cache;

/**
 * A rate limit.
 *
 * @author Jonathan Staniforth
 * @copyright 2017 Jonathan Staniforth
 * @version 1.0.2
 */
class RateLimit
{
    /**
     * @var string $api Name of the system with the endpoint.
     * @var string $endpoint Name of the endpoint.
     * @var string $header Name of the header that contains the rate limit value for the endpoint.
     */
    private $api, $endpoint, $header;

    /**
     * Sets the api.
     *
     * @param string $api Name of the system.
     * @return self Returns this instance.
     */
    public function api($api)
    {
        $this->api = $api;

        return $this;
    }

    /**
     * Checks the rate limit.
     *
     * @param \GuzzleHttp\Psr7\Response $response GuzzleHttp response object.
     * @return boolean True if rate limit has been reached.
     */
    public function check_limit($response)
    {
        $rate_limit = $response->getHeader($header);
        $limit = $rate_limit[0];
        $time = $rate_limit[1];

        $number_of_requests = $response->getHeader($header.'-Count');
        $number_of_requests = $number_of_requests[0];

        if ($number_of_requests >= $limit)
        {
            Cache::put($this->api.$this->endpoint, true, $time);

            return true;
        }

        return false;
    }

    /**
     * Sets the endpoint.
     *
     * @param string $endpoint Name of the endpoint.
     * @return self Returns this instance.
     */
    public function endpoint($endpoint)
    {
        $this->endpoint = $endpoint;

        return $this;
    }

    /**
     * Sets the header.
     *
     * @param string $endpoint Name of the header.
     * @return self Returns this instance.
     */
    public function header($header)
    {
        $this->header = $header;

        return $this;
    }

    /**
     * Checks endpoint limit status.
     *
     * @return boolean True if the rate limit has been exceeded.
     */
    public function exceeded()
    {
        return Cache::get($this->api.$this->endpoint, false);
    }
}
