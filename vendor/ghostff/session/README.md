# Session PHP(7.4+)
PHP Session Manager (non-blocking, flash, segment, session encryption). Uses PHP [open_ssl](http://php.net/manual/en/book.openssl.php) for **optional** encrypt/decryption of session data.

### Driver support  Scope
![file](https://img.shields.io/badge/FILE-completed-brightgreen.svg?style=flat-square)&nbsp;&nbsp;&nbsp;
![cookie](https://img.shields.io/badge/COOKIE-completed-brightgreen.svg?style=flat-square)&nbsp;&nbsp;&nbsp;
![pdo](https://img.shields.io/badge/PDO-completed-brightgreen.svg?style=flat-square)&nbsp;&nbsp;&nbsp;
![memcached](https://img.shields.io/badge/MEMCACHED-completed-brightgreen.svg?style=flat-square)&nbsp;&nbsp;&nbsp;
![redis](https://img.shields.io/badge/REDIS-active-brightgreen.svg?style=flat-square)&nbsp;&nbsp;&nbsp;
[![license](https://img.shields.io/pypi/l/Django.svg?style=flat-square)]()&nbsp;&nbsp;&nbsp;
[![Minimum PHP Version](https://img.shields.io/badge/php-%3E%3D%207.4-8892BF.svg?style=flat-square)](http://php.net/releases/7_4_0.php)

# Installation   
You can download the Latest [release version ](https://github.com/Ghostff/Session/releases/) as a standalone, alternatively you can use [Composer](https://getcomposer.org/) 
```json
$ composer require ghostff/session
```
```json
"require": {
    "ghostff/session": "^2.0"
}
```    

## Basic usage
```php
# Start session with default default or specified configurations.
$session = new Session(); 

$session->set('email', 'foo@bar.com');

echo $session->get('email');
```

## Configuration Options
```php
# use custom configuration file.
Session::setConfigurationFile('path/to/my/config.php');
 
# overriding specific configuration settings
Session::updateConfiguration([
    Session::CONFIG_DRIVER        => Redis::class,
    Session::CONFIG_START_OPTIONS => [
        Session::CONFIG_START_OPTIONS_SAVE_PATH => __DIR__ . '/tmp'
    ]
]);

# override a configuration for current session instance
$session = new Session([Session::CONFIG_ENCRYPT_DATA => true]);
```

## Initializing Session
```php
# Start session with an auto generated id.
$session = new Session(); 

# Start session with custom ID
$session = new Session(null, bin2hex(random_bytes(32)));
```

## Using Segment *:Session*
```php
 $segment = $session->segment('my_segment');
```

## Retrieving Session ID  *:string*
```php
echo $session->id();
```

## Committing changes *:void*
```php
# Opens, writes and closes session.
$session->commit();
```

## Setting Session Data *:Session*
```php
$session->set('fname', 'foo');
# Setting Segment
$segment->set('name', 'bar');

# Setting Flash
$session->setFlash('name', 'foobar');
# Setting Segment Flash
$segment->setFlash('name', 'barfoo');

$session->commit();
```

## Retrieving Session Data *:mixed*
```php
echo $session->get('name'); # outputs foo
echo $session->getOrDefault('unset_value', 'not found'); # outputs not found
# Retrieving Segment
echo $segment->get('name'); # outputs bar
echo $segment->getOrDefault('unset_value', 'not found'); # outputs not found

# Retrieving Flash
echo $session->getFlash('name'); # outputs foobar
echo $session->getFlashOrDefault('name', 'not found'); # outputs not found
# Retrieving Segment Flash
echo $segment->getFlash('name'); # outputs barfoo
echo $segment->getFlashOrDefault('name', 'not found'); # outputs not found
```

## Removing Session Data *:Session*
```php
$session->del('name');
# Removing Segment
$segment->del('name');

# Removing Flash
$session->delFlash('name');
# Removing Segment Flash
$segment->delFlash('name');
```

## Retrieve all session or segment data *:array*
```php
$session->getAll();
# Retrieve only in specified segment.
$session->getAll('my_segment_name');
```

## Check if variable exist in current session namespace *:bool*
```php
$session->exist('name');
# Search flashes
$session->exist('name', true);
```

## Removing all data in current segment *:Session*
```php
$session->clear();
```

## Destroying session *:void*
```php
$session->destroy();
```

## Regenerate session ID *:void*
```php
$session->rotate();
# Delete the old associated session file or not
$session->rotate(true);
```

## Setting Queued Session Data *:Session*
```php
$session->push('age', 10)
        ->push('age', 20)
        ->push('age', 30)
        ->push('age', 40);
```

## Retrieving (pop/shift) Queued Session Data *:mixed*
```php
echo $session->pop('age', true);  # outputs 10
echo $session->pop('age', true);  # outputs 20
echo $session->pop('age');        # outputs 40
echo $session->pop('age');        # outputs 30
```


