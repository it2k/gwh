<?php
/**
 * Created by PhpStorm.
 * User: zyuskin_en
 * Date: 31.12.14
 * Time: 1:06
 */

include __DIR__.'/../vendor/autoload.php';

use It2k\GWH\Hook;

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

$hook->addRepository('git@github.com:it2k/gwh.git')
     ->addBranch('master')
     ->addCommand(array('git status', 'git reset --hard HEAD', 'git pull origin master'))
     ->getParent()
     ->addBranch('production')
     ->addCommand('git pull origin production');

$hook->execute();