<?php

/*
 * This file is part of the Login Recaptcha Bundle.
 *
 * (c) Gabriel Caruana <gabb1995@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace LoginRecaptcha\Bundle\Client;

use Predis\ClientInterface;

/**
 * PredisClient
 *
 * Client for Predis {@link https://packagist.org/packages/predis/predis}
 */
class PredisClient implements CacheClientInterface
{
    /** @var ClientInterface $cacheClient */
    private $cacheClient;

    /** @var int $attempts */
    private $attempts;

    /** @var int $expiry */
    private $expiry;

    /**
     * __construct
     *
     * @param ClientInterface $cacheClient
     * @param int    $attempts
     * @param int    $expiry
     */
    public function __construct(ClientInterface $cacheClient, $attempts, $expiry)
    {
        $this->cacheClient = $cacheClient;
        $this->attempts    = $attempts;
        $this->expiry      = $expiry;
    }

    /**
     * {@inheritdoc}
     */
    public function getAttempts()
    {
        return $this->attempts;
    }

    /**
     * {@inheritdoc}
     */
    public function getExpiry()
    {
        return $this->expiry;
    }

    /**
     * {@inheritdoc}
     */
    public function incr($key)
    {
        $this->cacheClient->incr($key);
    }

    /**
     * {@inheritdoc}
     */
    public function get($key)
    {
        return $this->cacheClient->get($key);
    }

    /**
     * {@inheritdoc}
     */
    public function set($key, $data)
    {
        $this->cacheClient->set($key, $data);
    }

    /**
     * {@inheritdoc}
     */
    public function expire($key, $seconds)
    {
        $this->cacheClient->expire($key, $seconds);
    }
}
