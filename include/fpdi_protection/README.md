FpdiProtection
=================================

[![Latest Stable Version](https://poser.pugx.org/setasign/fpdi-protection/v/stable.svg)](https://packagist.org/packages/setasign/fpdi-protection) [![Total Downloads](https://poser.pugx.org/setasign/fpdi-protection/downloads.svg)](https://packagist.org/packages/setasign/fpdi-protection) [![Latest Unstable Version](https://poser.pugx.org/setasign/fpdi-protection/v/unstable.svg)](https://packagist.org/packages/setasign/fpdi-protection) [![License](https://poser.pugx.org/setasign/fpdi-protection/license.svg)](https://packagist.org/packages/setasign/fpdi-protection)

A [FPDI](https://www.setasign.com/fpdi) 2 compatible and enhanced version of the [FPDF_Protection](http://www.fpdf.org/en/script/script37.php) script.

This version requires and uses OpenSSL functions instead of Mcrypt or a user land implementation of RC4.

RC4-40bits and RC4-128bits encryption are supported.

## Installation with [Composer](https://packagist.org/packages/setasign/fpdi-protection)

```json
{
    "require": {
        "setasign/fpdi-protection": "^2.0"
    }
}
```

## Manual Installation

If you do not use composer, just require the autoload.php in the /src folder:

```php
require_once('src/autoload.php');
```

If you have a PSR-4 autoloader implemented, just register the src path as follows:
```php
$loader = new \Example\Psr4AutoloaderClass;
$loader->register();
$loader->addNamespace('setasign\FpdiProtection', 'path/to/src/');
```

Don't forget to install [FPDI](https://www.setasign.com/fpdi) and [FPDF](http://www.fpdf.org/), too!

## Example

This class offers one public method, which allows you to set the protection of the resulting PDF document.
All other code is identically to [FPDI](https://www.setasign.com/fpdi) or [FPDF](http://www.fpdf.org/).

If you pass null or an empty string as the owner password the method will create a random owner password.
The return value of this method is the owner password.

```php
<?php
use setasign\FpdiProtection\FpdiProtection;

// setup the autoload function
require_once('vendor/autoload.php');

$pdf = new FpdiProtection();
$ownerPassword = $pdf->setProtection(
    FpdiProtection::PERM_PRINT | FpdiProtection::PERM_COPY,
    'the user password',
    'the owner password'
);

// ...
```