<?php

namespace LoginRecaptcha\Bundle\Util;

/*
 * This file is part of the Login Recaptcha Bundle.
 *
 * (c) Gabriel Caruana <gabb1995@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */


/**
 * IpRangeUtil
 */
class IpRangeUtil
{
    /**
     * Get the ip range of an ip in the form xxx-xxx
     *
     * @param string $ip
     *
     * @return string
     */
    public static function getIpRange($ip)
    {
        $ip = ip2long($ip);
        $ipRangeStart = $ip & 0b11111111111111111111111100000000;
        $ipRangeEnd = $ip | 0b00000000000000000000000011111111;

        $ipRange = $ipRangeStart.'-'.$ipRangeEnd;

        return $ipRange;
    }
}
