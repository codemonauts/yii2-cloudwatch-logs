Yii2 Cloudwatch Logs Target
===========================

A Yii2 log target for AWS Cloudwatch Logs.

## Installation and Configuration

Install the package through [composer](http://getcomposer.org):

    composer require codemonauts/yii2-cloudwatch-logs

And then add this to your application configuration:

```php
<?php
return [
    // ...
    'components' => [
        // ...
        'log' => [
            'targets' => [
                [
                    'class' => \codemonauts\cloudwatchlogs\Target::class,
                    'region' => 'eu-west-1',
                    'logGroup' => '/webserver/production/my-craft',
                    'logStream' => 'instance-1', // omit for automatic instance ID
                    'levels' => ['error', 'warning', 'info', 'trace', 'profile'],
                    'logVars' => ['_GET', '_POST', '_FILES', '_COOKIE', '_SESSION', '_SERVER'],
                    'key' => 'your-key', // omit for instance role
                    'secret' => 'your-secret', // omit for instance role
                ],
                // ...
            ],
        ],
```

## Configuration Options

 * (*string*) `$region` (required) The name of the AWS region e.g. eu-west-1
 * (*string*) `$logGroup` (required) The name of the log group.
 * (*string*) `$logStream` (optional) The name of the log stream. If omitted, it will try to determine the ID of the EC2 instance running on.
 * (*array*)  `$levels` (optional) Log level. Default by Yii2: ['error', 'warning', 'info', 'trace', 'profile']
 * (*array*)  `$logVars` (optional) Variables to log. Default by Yii2: ['_GET', '_POST', '_FILES', '_COOKIE', '_SESSION', '_SERVER']
 * (*string*) `$key` (optional) Your AWS access key.
 * (*string*) `$secret` (optional) Your AWS secret.

## Cloudwatch Logs Insights

If you want to parse the logs with Insights, then do something like this:

```
fields @timestamp, @message
| sort @timestamp desc
| limit 20
| parse '[*][*][*][*][*] *' as ip, userId, sessionId, logLevel, category, message
```
