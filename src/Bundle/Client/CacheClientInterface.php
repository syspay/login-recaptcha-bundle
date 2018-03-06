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

/**
 * CacheClientInterface
 *
 * This is the interface that an injected cache client needs to implement.
 */
interface CacheClientInterface
{
    /**
     * Increment key data by 1
     *
     * @param String $key
     */
    public function incr($key);

    /**
     * Get Key Data
     *
     * @param String $key
     *
     * @return mixed
     */
    public function get($key);

    /**
     * Set Key Date
     *
     * @param String $key
     * @param mixed  $data
     */
    public function set($key, $data);

    /**
     * Expire key after number of seconds
     *
     * @param String $key
     * @param int    $seconds
     */
    public function expire($key, $seconds);

    /**
     * Get the number of failed login attempts before showing the captcha
     *
     * @return int
     */
    public function getAttempts();

    /**
     * Get the expiry in seconds of the cache key where the number of failed attempts
     * is stored
     *
     * @return int
     */
    public function getExpiry();
}
