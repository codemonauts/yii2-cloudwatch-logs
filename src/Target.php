<?php
namespace Codemonauts\Cloudwatchlogs;

use yii\log\Target as BaseTarget;
use yii\base\InvalidConfigException;
use Aws\CloudWatchLogs\CloudWatchLogsClient;

class Target extends BaseTarget
{
    /**
     * @var string The name of the log group.
     */
    public $logGroup;

    /**
     * @var string The AWS region to use e.g. eu-west-1.
     */
    public $region;

    /**
     * @var string Your AWS access key.
     */
    public $key;

    /**
     * @var string The name of the log stream. When not set, we try to get the ID of your EC2 instance.
     */
    public $logStream;

    /**
     * @var string Your AWS secret.
     */
    public $secret;

    /**
     * @var CloudWatchLogsClient
     */
    private $client;

    /**
     * @var string
     */
    private $sequenceToken;

    /**
     * @inheritdoc
     */
    public function init() {
        if (empty($this->logGroup)) {
            throw new InvalidConfigException("A log group must be set.");
        }

        if (empty($this->region)) {
            throw new InvalidConfigException("The AWS region must be set.");
        }

        if (empty($this->logStream)) {
            $instanceId = @file_get_contents("http://instance-data/latest/meta-data/instance-id");
            if ($instanceId !== false) {
                $this->logStream = $instanceId;
            } else {
                throw new InvalidConfigException("Cannot identify instance ID and no log stream name is set.");
            }
        }

        $params = [
            'region' => $this->region,
            'version' => 'latest',
        ];

        if (!empty($this->key) && !empty($this->secret)) {
            $params['credentials'] = [
                'key' => $this->key,
                'secret' => $this->secret,
            ];
        }

        $this->client = new CloudWatchLogsClient($params);

        $this->refreshSequenceToken();
    }

    /**
     * @inheritdoc
     */
    public function export()
    {
        $logEvents = [];

        foreach ($this->messages as $message) {
            $logEvents[] = [
                'message' => $message,
                'timestamp' => time()
            ];
        }

        $data = [
            'logEvents' => $logEvents,
            'logGroupName' => $this->logGroup,
            'logStreamName' => $this->logStream,
        ];

        if (!empty($this->sequenzeToken)) {
            $data['sequenceToken'] = $this->sequenceToken;
        }

        $response = $this->client->putLogEvents($data);
        $this->sequenceToken = $response->get('nextSequenceToken');
    }

    /**
     * Get the sequence token for the selected log stream.
     *
     * returns void
     */
    private function refreshSequenceToken()
    {
        $existingStreams = $this->client->describeLogStreams([
            'logGroupName' => $this->logGroup,
            'logStreamNamePrefix' => $this->logStream,
        ])->get('logStreams');

        foreach($existingStreams as $stream) {
            if ($stream['logStreamName'] === $this->logStream && isset($stream['uploadSequenceToken'])) {
                $this->sequenceToken = $stream['uploadSequenceToken'];
            }
        }
    }
}
