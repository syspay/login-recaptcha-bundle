<?php

/*
 * This file is part of the Login Recaptcha Bundle.
 *
 * (c) Gabriel Caruana <gabb1995@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace LoginRecaptcha\Bundle\Security\Firewall;

use LoginRecaptcha\Bundle\Client\CacheClientInterface;
use LoginRecaptcha\Bundle\Exception\InvalidCaptchaException;
use LoginRecaptcha\Bundle\Util\IpRangeUtil;
use Psr\Log\LoggerInterface;
use ReCaptcha\ReCaptcha;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Authentication\AuthenticationManagerInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationFailureHandlerInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationSuccessHandlerInterface;
use Symfony\Component\Security\Http\Firewall\UsernamePasswordFormAuthenticationListener;
use Symfony\Component\Security\Http\HttpUtils;
use Symfony\Component\Security\Http\ParameterBagUtils;
use Symfony\Component\Security\Http\Session\SessionAuthenticationStrategyInterface;

/**
 * CaptchaFormAuthenticationListener
 */
class CaptchaFormAuthenticationListener extends UsernamePasswordFormAuthenticationListener
{
    const PREFIX_FAIL_IP_RANGE = 'fail_ip_range:';

    /** @var CacheClientInterface $cacheClient */
    private $cacheClient;

    /**
     * {@inheritdoc}
     */
    public function __construct(TokenStorageInterface $tokenStorage, AuthenticationManagerInterface $authenticationManager, SessionAuthenticationStrategyInterface $sessionStrategy, HttpUtils $httpUtils, $providerKey, AuthenticationSuccessHandlerInterface $successHandler, AuthenticationFailureHandlerInterface $failureHandler, array $options = array(), LoggerInterface $logger = null, EventDispatcherInterface $dispatcher = null, CsrfTokenManagerInterface $csrfTokenManager = null)
    {
        parent::__construct($tokenStorage, $authenticationManager, $sessionStrategy, $httpUtils, $providerKey, $successHandler, $failureHandler, array_merge(array(
            'google_recaptcha_secret' => null,
            'always_captcha' => true,
            'attempts' => 0,
            'cache_expiry' => 300,
        ), $options), $logger, $dispatcher);
    }

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
     * @param String|null $clientIp
     *
     * @throws \Exception
     *
     * @return bool
     */
    public function isCaptchaNeeded($clientIp = null)
    {
        if ($this->options['always_captcha'] === true) {
            return true;
        }

        if (!$this->cacheClient) {
            throw new \Exception('Cache client cannot be null if always_captcha is not true.');
        }

        if (!isset($this->options['attempts'])) {
            throw new \Exception('attempts must be set to use isCaptchaNeeded when always_captcha is not true.');
        }

        return $this->checkIpRange(IpRangeUtil::getIpRange($clientIp));
    }

    /**
     * increaseFailedAttempts
     *
     * @throws \Exception
     *
     * @param String $clientIp
     */
    public function increaseFailedAttempts($clientIp)
    {
        if (!$this->cacheClient) {
            throw new \Exception('Cache client cannot be null if always_captcha is not true.');
        }

        if (!isset($this->options['cache_expiry'])) {
            throw new \Exception('cache_expiry must be set to use increaseFailedAttempts when always_captcha is not true.');
        }

        $ipRange = IpRangeUtil::getIpRange($clientIp);

        $cachedIpRange = $this->cacheClient->get(self::PREFIX_FAIL_IP_RANGE.$ipRange);

        if ($cachedIpRange) {
            $this->cacheClient->incr(self::PREFIX_FAIL_IP_RANGE.$ipRange);
        } else {
            $this->cacheClient->set(self::PREFIX_FAIL_IP_RANGE.$ipRange, 1);
        }

        $this->cacheClient->expire(self::PREFIX_FAIL_IP_RANGE.$ipRange, $this->options['cache_expiry']);
    }
    /**
     * {@inheritdoc}
     */
    protected function attemptAuthentication(Request $request)
    {
        if ($this->isCaptchaNeeded($request->getClientIp())) {
            $requestBag = $this->options['post_only'] ? $request->request : $request;
            $recaptchaResponse = ParameterBagUtils::getParameterBagValue($requestBag, 'g-recaptcha-response');

            if (!$this->isValidCaptchaResponse($recaptchaResponse, $request->getClientIp())) {
                throw new InvalidCaptchaException();
            }
        }

        /* Do the normal form_login attemptAuthentication */
        return parent::attemptAuthentication($request);
    }

    /**
     * Checks if the captcha is valid or not by sending a request to the google reCAPTCHA api
     *
     * @param String $captchaResponse
     * @param String $clientIp
     *
     * @throws \Exception
     *
     * @return boolean
     */
    private function isValidCaptchaResponse($captchaResponse, $clientIp)
    {
        if (is_null($this->options['google_recaptcha_secret'])) {
            throw new \Exception('Google recaptcha secret key is null, did you forget to input it in the form_login_captcha option google_recaptcha_secret?');
        }

        $recaptcha = new ReCaptcha($this->options['google_recaptcha_secret']);
        $response  = $recaptcha->verify($captchaResponse, $clientIp);

        if ($response->isSuccess()) {
            return true;
        }

        return false;
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
        if ($cachedIpRange && $cachedIpRange >= $this->options['attempts']) {
            return true;
        }

        return false;
    }
}
