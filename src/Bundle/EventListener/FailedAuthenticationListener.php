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

use LoginRecaptcha\Bundle\Security\Firewall\CaptchaFormAuthenticationListener;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Security\Core\Event\AuthenticationFailureEvent;

/**
 * FailedAuthenticationListener
 */
class FailedAuthenticationListener
{
    /** @var RequestStack $requestStack */
    private $requestStack;

    /** @var CaptchaFormAuthenticationListener $captchaFormAuthenticationListener */
    private $captchaFormAuthenticationListener;


    /**
     * __construct
     * @param RequestStack $requestStack
     */
    public function __construct(RequestStack $requestStack)
    {
        $this->requestStack = $requestStack;
    }

    /**
     * @param CaptchaFormAuthenticationListener $captchaFormAuthenticationListener
     */
    public function setCaptchaFormAuthenticationListener(CaptchaFormAuthenticationListener $captchaFormAuthenticationListener)
    {
        $this->captchaFormAuthenticationListener = $captchaFormAuthenticationListener;
    }

    /**
     * Called on authentication failure
     *
     * @throws \Exception
     *
     * @param AuthenticationFailureEvent $event
     */
    public function onAuthenticationFailure(AuthenticationFailureEvent $event)
    {
        if ($this->captchaFormAuthenticationListener) {
            $request = $this->requestStack->getCurrentRequest();
            $this->captchaFormAuthenticationListener->increaseFailedAttempts($request->getClientIp());
        }
    }
}
