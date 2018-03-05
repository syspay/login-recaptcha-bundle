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

use LoginRecaptcha\Bundle\Exception\InvalidCaptchaException;
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
    /**
     * {@inheritdoc}
     */
    public function __construct(TokenStorageInterface $tokenStorage, AuthenticationManagerInterface $authenticationManager, SessionAuthenticationStrategyInterface $sessionStrategy, HttpUtils $httpUtils, $providerKey, AuthenticationSuccessHandlerInterface $successHandler, AuthenticationFailureHandlerInterface $failureHandler, array $options = array(), LoggerInterface $logger = null, EventDispatcherInterface $dispatcher = null, CsrfTokenManagerInterface $csrfTokenManager = null)
    {
        parent::__construct($tokenStorage, $authenticationManager, $sessionStrategy, $httpUtils, $providerKey, $successHandler, $failureHandler, array_merge(array(
            'google_recaptcha_secret' => null,
        ), $options), $logger, $dispatcher);
    }

    /**
     * {@inheritdoc}
     */
    protected function attemptAuthentication(Request $request)
    {
        $requestBag = $this->options['post_only'] ? $request->request : $request;
        $recaptchaResponse = ParameterBagUtils::getParameterBagValue($requestBag, 'g-recaptcha-response');


        if (!$this->isValidCaptchaResponse($recaptchaResponse, $request->getClientIp())) {
            throw new InvalidCaptchaException();
        }

        return parent::attemptAuthentication($request);
    }

    /**
     * isValidCaptchaResponse
     *
     * @param String $captchaResponse
     * @param String $clientIp
     *
     * @return boolean
     */
    private function isValidCaptchaResponse($captchaResponse, $clientIp)
    {
        if (is_null($this->options['google_recaptcha_secret'])) {
            $this->logger->error('Google recaptcha secret key is null, this should be inputted in the form_login_captcha option google_recaptcha_secret.');

            throw new \Exception('Google recaptcha secret key is null');
        }

        $recaptcha = new ReCaptcha($this->options['google_recaptcha_secret']);
        $response  = $recaptcha->verify($captchaResponse, $clientIp);

        if ($response->isSuccess()) {
            return true;
        }
        $this->logger->info('Recaptcha failed: '.$response->getErrorCodes());

        return false;
    }
}
