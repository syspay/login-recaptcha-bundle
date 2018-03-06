<?php

/*
 * This file is part of the Login Recaptcha Bundle.
 *
 * (c) Gabriel Caruana <gabb1995@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace LoginRecaptcha\Bundle\EventListener;

use LoginRecaptcha\Bundle\Client\CacheClientInterface;
use LoginRecaptcha\Bundle\Manager\CaptchaLoginFormManager;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Security\Core\Event\AuthenticationFailureEvent;

/**
 * FailedAuthenticationListener
 */
class FailedAuthenticationListener
{
    /** @var RequestStack $requestStack */
    private $requestStack;

    /** @var CaptchaLoginFormManager $formManager */
    private $formManager;

    /**
     * __construct
     * @param RequestStack $requestStack
     */
    public function __construct(RequestStack $requestStack)
    {
        $this->requestStack = $requestStack;
    }

    /**
     * setFormManager
     *
     * @param CacheClientInterface $cacheClient
     */
    public function setFormManager(CacheClientInterface $cacheClient)
    {
        if (!($this->options['always_captcha'])) {
            $this->formManager = new CaptchaLoginFormManager();
            $this->formManager->setCacheClient($cacheClient);
        }
    }

    /**
     * Called on authentication failure
     *
     * @param AuthenticationEvent $event
     */
    public function onAuthenticationFailure(AuthenticationFailureEvent $event)
    {
        if (!is_null($this->formManager)) {
            $request = $this->requestStack->getCurrentRequest();
            $this->formManager->increaseFailedAttempts($request->getClientIp());
        }
    }
}
