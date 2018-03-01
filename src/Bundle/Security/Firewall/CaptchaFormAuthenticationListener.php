<?php

namespace Genius\Bundle\CoreBundle\Security\Firewall;

use Genius\Security\CaptchaManager;
use Genius\Security\Exception\InvalidCaptchaException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Http\Firewall\UsernamePasswordFormAuthenticationListener;
use Symfony\Component\Security\Http\ParameterBagUtils;

/**
 * CaptchaFormAuthenticationListener
 */
class CaptchaFormAuthenticationListener extends UsernamePasswordFormAuthenticationListener
{
    /** @var CaptchaManager $captchaManager */
    private $captchaManager;

    /**
     * setCaptchaManager
     *
     * @param CaptchaManager $captchaManager
     */
    public function setCaptchaManager(CaptchaManager $captchaManager)
    {
        $this->captchaManager = $captchaManager;
    }

    /**
     * {@inheritdoc}
     */
    protected function attemptAuthentication(Request $request)
    {
        if ($this->captchaManager->isCaptchaNeeded($request)) {
            $requestBag = $this->options['post_only'] ? $request->request : $request;
            $recaptchaResponse = ParameterBagUtils::getParameterBagValue($requestBag, 'g-recaptcha-response');

            if (!$this->captchaManager->isValidCaptchaResponse($recaptchaResponse, $request->getClientIp())) {
                throw new InvalidCaptchaException();
            }
        }

        return parent::attemptAuthentication($request);
    }
}
