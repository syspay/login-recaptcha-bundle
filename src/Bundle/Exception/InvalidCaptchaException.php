<?php

/*
 * This file is part of the Login Recaptcha Bundle.
 *
 * (c) Gabriel Caruana <gabb1995@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace LoginRecaptcha\Bundle\Exception;

use Symfony\Component\Security\Core\Exception\AuthenticationException;

/**
 * InvalidCaptchaException is thrown when the recaptcha response is invalid.
 */
class InvalidCaptchaException extends AuthenticationException
{
    /**
     * {@inheritdoc}
     */
    public function getMessageKey()
    {
        return 'Invalid captcha.';
    }
}
