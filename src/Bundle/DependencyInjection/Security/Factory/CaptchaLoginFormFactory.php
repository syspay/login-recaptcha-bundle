<?php

/*
 * This file is part of the Login Recaptcha Bundle.
 *
 * (c) Gabriel Caruana <gabb1995@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Bundle\CoreBundle\DependencyInjection\Security\Factory;

use Symfony\Bundle\SecurityBundle\DependencyInjection\Security\Factory\FormLoginFactory;

/**
 * CaptchaLoginFormFactory
 */
class CaptchaLoginFormFactory extends FormLoginFactory
{
    /**
     * {@inheritdoc}
     */
    public function __construct()
    {
        $this->addOption('google_recaptcha_secret', null);

        parent::__construct();
    }

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
