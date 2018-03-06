<?php

/*
 * This file is part of the Login Recaptcha Bundle.
 *
 * (c) Gabriel Caruana <gabb1995@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace LoginRecaptcha\Bundle\Manager;

use LoginRecaptcha\Bundle\Client\CacheClientInterface;
use LoginRecaptcha\Bundle\Util\IpRangeUtil;

/**
 * All functions related to captcha login form
 */
class CatpchaLoginFormManager
{
    const PREFIX_FAIL_IP_RANGE = 'fail_ip_range:';

    /** @var CacheClientInterface $cacheClient */
    private $cacheClient;

    /**
     * setCacheClient
     *
     * @param CacheClientInterface $cacheClient
     */
    public function setCacheClient(CacheClientInterface $cacheClient)
    {
        $this->cacheClient = $cacheClient;
    }

    /**
     * isCaptchaNeeded
     *
     * @param String $clientIp
     *
     * @return boolean
     */
    public function isCaptchaNeeded($clientIp)
    {
        return $this->checkIpRange(IpRangeUtil::getIpRange($clientIp));
    }

    /**
     * increaseFailedAttempts
     *
     * @param String $clientIp
     */
    public function increaseFailedAttempts($clientIp)
    {
        $ipRange = IpRangeUtil::getIpRange($clientIp);

        $cachedIpRange = $this->cacheClient->get(self::PREFIX_FAIL_IP_RANGE.$ipRange);

        if ($cachedIpRange) {
            $this->cacheClient->incr(self::PREFIX_FAIL_IP_RANGE.$ipRange);
        } else {
            $this->cacheClient->set(self::PREFIX_FAIL_IP_RANGE.$ipRange, 1);
        }

        $this->cacheClient->expire(self::PREFIX_FAIL_IP_RANGE.$ipRange, $this->cacheClient->getExpiry());
    }

    /**
     * checkIpRange
     *
     * @param string $ipRange Format must be xxx-xxx
     *
     * @return boolean
     */
    private function checkIpRange($ipRange)
    {
        $cachedIpRange = $this->cacheClient->get(self::PREFIX_FAIL_IP_RANGE.$ipRange);
        if ($cachedIpRange && $cachedIpRange >= $this->$this->cacheClient->getAttempts()) {
            return true;
        }

        return false;
    }
}
