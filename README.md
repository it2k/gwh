GWH
===

Library for handle git web hooks from gitlab.com or github.com and run commands 

Features
--------

- Run commands by global get request
- Run commands by hook on git repository
- Run commands by hook on some branch
- Security check commit author (global, repository, branch)
- Security check param from $_GET request
- Send email author and mail recipients with the results of the execute command

Require
-------

- php >= 5.3
- symfony/options-resolver >= 2.3.*
- apache/log4php >= 2.3.*

Install
-------
``` bash
$ php composer require it2k/gwh "1.*"
```

Usage
-----
```php
<?php

include __DIR__.'/../vendor/autoload.php';

use It2k\GWH\Hook;

//Save logs to file
$loggerConfiguration = array(
    'rootLogger' => array(
        'level' => 'WARN',
        'appenders' => array('default'),
    ),
    'appenders' => array(
        'default' => array(
            'class' => 'LoggerAppenderFile',
            'layout' => array(
                'class' => 'LoggerLayoutPattern',
                'params' => array('conversionPattern' => '%date{M d H:i:s} %-5level %msg%n'),
            ),
            'params' => array(
                'file' => __DIR__ . '/hook.log',
                'append' => true
            )
        )
    )
);

$options = array(
    'sendEmails'          => true,
    'sendEmailAuthor'     => true,
    'mailRecipients'      => array(),
    'allowedAuthors'      => '*',
    'allowedHosts'        => '*',
    'loggerConfiguration' => LoggerConfiguratorDefault::getDefaultConfiguration(),
);

$hook = new Hook(__DIR__, $options);

$hook->addRepository('git@github.com:it2k/gwh.git', '/var/www/html/hook/', array('allowedAuthors' => array('egor@zyuskin.ru')))
     ->addBranch('master')
     ->addCommand(array('git status', 'git reset --hard HEAD', 'git pull origin master'))
     ->getParent()
     ->addBranch('production', '/var/www/production/hook/'))
     ->addCommand('git pull origin production')
     ->addCommand('ls -la', '/var/spool/mail/');

$hook->execute();
```

Configuration
-------------

Configuration can be unique for each branch, it is enough to pass the variable options of type array. See example below.

```php
$options = array(
    'sendEmails'            => false,                          // Enable or disable sending emails
    'sendEmailAuthor'       => false,                          // Enable or disable sending email commit author
    'sendEmailFrom'         => 'git-web-hook@'.gethostname(),  // Email address from which messages are sent
    'mailRecipients'        => array(),                        // Array subscriber 
    'allowedAuthors'        => array(),                        // Array authors email allowed on this branch
    'allowedHosts'          => array(),                        // Array hosts allowed on this branch
    'securityCode'          => '',                             // Security code on check $_GET request
    'securityCodeFieldName' => 'code',                         // $_GET field name of security code
    'repositoryFieldName'   => 'url',                          // Repository filed name on the JSON query
    'loggerInstanceName'    => 'gwh',                          // Logger instance name by default
    'loggerConfiguration'   => array(),                        // Configuration by logger 
);
```

Logger configuration
--------------------
For more information see http://logging.apache.org/log4php/

By default logger is off. Enable console log message set 
```php
$options = array(
    ...
    'loggerConfiguration' => LoggerConfiguratorDefault::getDefaultConfiguration()
    ...
);
```

If you want save messages to file then set 
```php
$options = array(
    ...
    'loggerConfiguration' => array(
        'rootLogger' => array(
                'level' => 'WARN',
                'appenders' => array('default'),
        ),
        'appenders' => array(
            'default' => array(
                'class' => 'LoggerAppenderFile',
                'layout' => array(
                    'class' => 'LoggerLayoutPattern',
                    'params' => array('conversionPattern' => '%date{M d H:i:s} %-5level %msg%n'),
                ),
                'params' => array(
                    'file' => __DIR__ . '/hook.log',
                    'append' => true
                )
            )
        )
    ),
    ...
);
```

Security code checking configuration
------------------------------------

Setup config:
```php
$options = array(
    ...
    'securityCode'          => 'GjnfkrjdsqKfvgjcjc',
    'securityCodeFieldName' => 'mySecurityCode',
    ...
);
```

and setup web hook on gitlab.com or github.com on 
```
http://yourhost/hook.php?mySecurityCode=GjnfkrjdsqKfvgjcjc
```

if security code not pass check the you see 
```
Jan 01 00:00:00 WARN Security code not match
```
in the log file

License
--------
This library is under the MIT license. See the complete license in [here](https://github.com/it2k/gwh/blob/master/LICENSE)
