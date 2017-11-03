<?php

namespace LaravelWithLimits;

use Cache;

/**
 * A rate limit.
 *
 * A rate limit associated with an endpoint.
 *
 * @author Jonathan Staniforth
 * @copyright 2017 Jonathan Staniforth
 * @version 1.0.0
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
     * Sets the name of the system with the endpoint.
     *
     * @param string $api Name of the system.
     *
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
     * Checks if the rate limit for the endpoint has been reached.
     *
     * @param \GuzzleHttp\Psr7\Response $response GuzzleHttp response object.
     *
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
     * Sets the endpoint to the resource.
     *
     * @param string $endpoint Name of the endpoint.
     *
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
     * Sets the header that contains the rate limit of the endpoint in the response.
     *
     * @param string $endpoint Name of the header.
     *
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
     * Checks if the endpoint has exceeded its rate limit.
     *
     * @return boolean True if the rate limit has been exceeded.
     */
    public function exceeded()
    {
        return Cache::get($this->api.$this->endpoint, false);
    }
}
