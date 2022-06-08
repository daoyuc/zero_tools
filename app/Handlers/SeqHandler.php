<?php

namespace App\Handlers;

use Carbon\Carbon;
use GuzzleHttp\Client;
use Monolog\Handler\AbstractProcessingHandler;
use Monolog\Logger;

class SeqHandler extends AbstractProcessingHandler
{
    const SEQ_API_URI = '/api/events/raw';

    protected $logLevelMap = [
        '100' => 'Debug',
        '200' => 'Information',
        '250' => 'Information',
        '300' => 'Warning',
        '400' => 'Error',
        '500' => 'Error',
        '550' => 'Fatal',
        '600' => 'Fatal',
    ];

    protected $client;

    protected $serverUrl;

    public function __construct(string $serverUrl = '', $level = Logger::DEBUG, $bubble = true)
    {
        $this->serverUrl = $serverUrl;

        $this->client = new Client();

        parent::__construct($level, $bubble);
    }

    protected function write(array $record): void
    {
        $url = $this->serverUrl . SeqHandler::SEQ_API_URI;
        $eventBody = [
            'Events' => [[
                //An ISO 8601 timestamp
                'Timestamp' => Carbon::now()->toIso8601String(),
                //An implementation-specific level identifier; Seq requires a string value if present
                "Level" => $this->logLevelMap[$record['level']] ?: 'Information',
                //Alternative to Message;
                "MessageTemplate" => $record['formatted'],
            ]]
        ];

        try {
            $this->client->post($url, [
                "body" => json_encode($eventBody)
            ]);
        } catch (\Exception $exception) {
            return;
        }
    }
}
