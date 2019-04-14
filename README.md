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

## Rate limit
The Cloudwatch Logs API requires a sequence token to be send with the messages to a log stream. This token is returned after a transfer of messages to the log stream to be used at the next transfer. For multi-process systems this is hard to handle.

So AWS Cloudwatch Logs does not support multiple processes writing to the same log stream at the same time. Their official approach is to create a separate log stream for each process on each instance. But think of PHP-FPM with dynamic childs on several instances. This would end up in thousands of abandoned log streams in a log group after a while. Empty log streams are not deleted automatically and remain in the log groups. They do not cost anything, but make log groups very messy.

Our approach is to create only one log stream per instance. To be able to send log streams from multiple processes at the same time, the current sequence token of the log stream must be queried before each transmission. But the AWS Cloudwatch Log API has a rate limit of 60 requests per second for this type of request.

In order to avoid running into this rate limit, it is **highly advisable** to pass only those logs directly to Cloudwatch Logs that happen rarely and are important. For example Warnings and Errors.

In large infrastructures with many simultaneous processes with a high log load, it is still better to use file logging and the Cloudwatch Log Agent, as this is only a single process.

Maybe this will change in the future.

## Cloudwatch Logs Insights

If you want to parse the logs with Insights, then do something like this:

```
fields @timestamp, @message
| sort @timestamp desc
| limit 20
| parse '[*][*][*][*][*] *' as ip, userId, sessionId, logLevel, category, message
```
