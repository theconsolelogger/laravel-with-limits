<?php

namespace LaravelWithLimits;

use Closure;
use GuzzleHttp\Client;
use LaravelWithLimits\RateLimit;

/**
 * A HTTP request.
 *
 * HTTP request containing all the information
 * to send a request to a resource.
 *
 * @author Jonathan Staniforth
 * @copyright 2017 Jonathan Staniforth
 * @version 1.0.2
 */
class Request
{
    /**
     * @var string $base_uri The base URI of the request.
     * @var \GuzzleHttp\Client $client The GuzzleHttp client to use for the request.
     * @var string $method The method to perform on the resource.
     * @var string $path The path to the resource.
     * @var string[] $headers An associative array containing headers for the request.
     * @var string[] $query An associative array containing the parameters for the request.
     * @var \LaravelWithLimits\RateLimit[] $rate_limit A list of rate limits.
     */
    private $base_uri, $client, $method, $path;
    private $headers, $query, $rate_limits = [];

    public function __construct()
    {
        $this->client = new Client;
    }

    /**
     * Sets the method of the request.
     *
     * Sets the method to be performed on the resource.
     * Includes: GET, POST, PUT, PATCH, DELETE.
     *
     * @param string $method The method, e.g. GET.
     *
     * @return self Returns this instance.
     */
    public function method($method)
    {
        $this->method = $method;

        return $this;
    }

    /**
     * Sets the path of the request.
     *
     * @param string $path The path.
     * @return self Returns this instance.
     */
    public function path($path)
    {
        $this->path = $path;

        return $this;
    }

    /**
     * Sends the request.
     *
     * @return \GuzzleHttp\Psr7\Response The response from the request.
     */
    public function send()
    {
        foreach ($this->rate_limits as $rate_limit)
        {
            if ($rate_limit->exceeded())
            {
                return false;
            }
        }

        $response = $this->client->request($this->method, $this->base_uri.$this->path, [
            'headers' => $this->headers,
            'query' => $this->query,
        ]);

        foreach ($this->rate_limits as $rate_limit)
        {
            $rate_limit->check_limit($response);
        }

        return $response;
    }

    /**
     * Sets the base URI.
     *
     * @param string $base_uri The base URI.
     * @return self Returns this instance.
     */
    public function withBaseUri($base_uri)
    {
        $this->base_uri = $base_uri;

        return $this;
    }

    /**
     * Sets the headers.
     *
     * @param string[] $headers An associative array, key is name of header and value is the value of the header.
     * @return self Returns this instance.
     */
    public function withHeaders(array $headers)
    {
        $this->headers = array_collapse([$this->headers, $headers]);

        return $this;
    }

    /**
     * Sets a rate limit.
     *
     * @param callback $callback A closure to call on the rate limit.
     * @return self Returns this instance.
     */
    public function withLimit(Closure $callback)
    {
        $this->rate_limit[] = $rate_limit = new RateLimit;

        $callback($rate_limit);

        return $this;
    }

    /**
     * Sets the parameters.
     *
     * @param string[] $parameters An associative array, key is name of parameter and value is the value of the parameter.
     * @return self Returns this instance.
     */
    public function withParameters(array $parameters)
    {
        $this->query = array_collapse([$this->query, $parameters]);

        return $this;
    }

}
