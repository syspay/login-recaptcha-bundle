Login reCAPTCHA Bundle
===================
[![License](https://img.shields.io/packagist/l/gabb1995/login-recaptcha-bundle.svg)](https://packagist.org/packages/gabb1995/login-recaptcha-bundle)

* Packagist Page:  https://packagist.org/packages/gabb1995/login-recaptcha-bundle
* Repository: https://github.com/Gabb1995/login-recaptcha-bundle
* Version: 1.0.0
* License: MIT, see [LICENSE](LICENSE)

## Description
Login reCAPTCHA Bundle makes it easy for you to integrate Google reCAPTCHA inside login forms in Symfony.

## Installation
This symfony bundle is available on Packagist as
[`gabb1995/login-recaptcha-bundle`](https://packagist.org/packages/gabb1995/login-recaptcha-bundle) and can be
installed either by running the `composer require` command or adding the library
to your `composer.json`.

To add this dependency using the command, run the following from within your
project directory:
```
composer require gabb1995/login-recaptcha-bundle
```

Alternatively, add the dependency directly to your `composer.json` file:
```json
"require": {
    "gabb1995/login-recaptcha-bundle": "~1.0"
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
