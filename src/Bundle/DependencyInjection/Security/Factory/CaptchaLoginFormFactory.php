<?php

namespace Genius\Bundle\CoreBundle\DependencyInjection\Security\Factory;

use Symfony\Bundle\SecurityBundle\DependencyInjection\Security\Factory\FormLoginFactory;

/**
 * CaptchaLoginFormFactory
 */
class CaptchaLoginFormFactory extends FormLoginFactory
{
    /**
     * {@inheritdoc}
     */
    public function getKey()
    {
        return 'form_login_captcha';
    }

    /**
     * {@inheritdoc}
     */
    protected function getListenerId()
    {
        return 'security.authentication.listener.form_login_captcha';
    }
}
