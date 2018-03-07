Login reCAPTCHA Bundle
===================
[![License](https://img.shields.io/packagist/l/syspay/login-recaptcha-bundle.svg)](https://packagist.org/packages/syspay/login-recaptcha-bundle)

* Packagist Page:  https://packagist.org/packages/syspay/login-recaptcha-bundle
* Repository: https://github.com/Gabb1995/login-recaptcha-bundle
* Version: 2.1.0
* License: MIT, see [LICENSE](LICENSE)

## Description
Login reCAPTCHA Bundle makes it easy for you to integrate Google reCAPTCHA inside login forms in Symfony 3.

## Installation
This symfony bundle is available on Packagist as
[`syspay/login-recaptcha-bundle`](https://packagist.org/packages/syspay/login-recaptcha-bundle) and can be
installed either by running the `composer require` command or adding the library
to your `composer.json`.

To add this dependency using the command, run the following from within your
project directory:
```
composer require syspay/login-recaptcha-bundle
```

Alternatively, add the dependency directly to your `composer.json` file:
```json
"require": {
    "syspay/login-recaptcha-bundle": "^2.1"
}
```
After composer installation go to your `AppKernel.php` file and add the following line inside `registerBundles()`:
```
$bundles = [
    ...
    new LoginRecaptcha\Bundle\LoginRecaptchaBundle(),
];
```

## Configuration

To use this functionality you have to use `form_login_captcha` instead of `form_login` in your `security.yml` file. This new security listener factory has all the same options as `form_login` but it has a required new option called `google_recaptcha_secret` where you have to enter your Google reCAPTCHA secret key.

```
form_login_captcha:
    login_path: login
    check_path: login_check
    username_parameter: "login_form[username]"
    password_parameter: "login_form[password]"
    csrf_parameter: "login_form[_token]"
    default_target_path: homepage
    google_recaptcha_secret: XXXXXXXXXXXXXXXX_XXXX_XXXXXXXXXXXXXXXXXXX
```

From then on your login form expects a new post parameter called `g-recaptcha-response` which is created by any reCAPTCHA plugin. Then the bundle authenticates the response by using Google's own reCAPTCHA package.

## Validating reCAPTCHA only after several failed attempts

By default the bundle always checks for the post parameter `g-recaptcha-response` but this can be annoying on users as they'd have to input the reCAPTCHA everytime they login, therefore there is an option to only validate the reCAPTCHA after several failed logins from an ip range. This option needs a couple of extra configurations on your end. The way it works is that everytime there is a failed login a listener is called to create or increment a particular key in your cache client.

All you have to do is to set up your cache client example redis and declare a particular service that the bundle expects. This service needs to implement the class `LoginRecaptcha\Bundle\Client\CacheClientInterface`. A Predis extension is already provided in the bundle under the name `LoginRecaptcha\Bundle\Client\PredisClient`. After creating your class or using the provided one declare the following service in your `app/services.yml`. It is very important that the service name is `login_recaptcha.cache_client`.

```
    login_recaptcha.cache_client:
        class: LoginRecaptcha\Bundle\Client\PredisClient
        arguments:
            - '@snc_redis.default'
            - '%attempts%
            - '%expiry%
```

In my case `@snc_redis.default` is my cache service. The second argument is the number of failed attempts you want to verify the reCATPCHA after and the third argument is how long you want the key to stay in your cache.

After this all you have to do is add the option `always_captcha: false` under `form_login_captcha` in your `security.yml` file.
```
form_login_captcha:
    login_path: login
    check_path: login_check
    username_parameter: "login_form[username]"
    password_parameter: "login_form[password]"
    csrf_parameter: "login_form[_token]"
    default_target_path: homepage
    google_recaptcha_secret: XXXXXXXXXXXXXXXX_XXXX_XXXXXXXXXXXXXXXXXXX
    always_captcha: false
```

To check on the front end whether you should be showing the reCAPTCHA widget or not you need to inject the service `login_recaptcha.captcha_login_form.manager` in your controller and call the function `isCaptchaNeeded()` which takes the `$request->getClientIp()`. This returns `true` or `false` which you can then pass as a variable to twig.
