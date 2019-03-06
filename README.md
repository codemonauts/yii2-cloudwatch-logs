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
                    'class' => 'Codemonauts\Cloudwatchlogs\Target',
                    'logGroup' => '/webserver/production/myapp',
                    'region' => 'eu-west-1',
                    'levels' => ['trace', 'info', 'error', 'warning'],
                    'logVars' => [],
                ],
                // ...
            ],
        ],
```

## Configuration Options

 * (*string*) `$region` (required) The name of the AWS region e.g. eu-west-1
 * (*string*) `$logGroup` (required) The name of the log group.
 * (*string*) `$logStream` (optional) The name of the log stream. If omitted, it will try to determine the ID of the EC2 instance running on.
 * (*string*) `$key` (optional) Your AWS access key.
 * (*string*) `$secret` (optional) Your AWS secret.
