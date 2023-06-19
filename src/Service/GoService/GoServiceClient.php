<?php

namespace App\Service\GoService;

use App\Service\GoService\Request\GoServiceRequestInterface;
use App\Service\GoService\Request\PostEventRequest;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;

class GoServiceClient
{
    private Client $httpClient;

    public function __construct(private readonly string $goServiceHost)
    {
        $this->httpClient = new Client();
    }

    public function sendPostEvent(PostEventRequest $postEventRequest): array
    {
        $response = $this->sendRequest($postEventRequest);

        return $response;
    }

    private function sendRequest(GoServiceRequestInterface $goServiceRequest): array
    {
        $options = [];
        if (!empty($goServiceRequest->getPostParam())) {
            $options['json'] = $goServiceRequest->getPostParam();
        }

        try {

            $response = $this->httpClient->request($goServiceRequest->getMethod()->value, $this->goServiceHost . $goServiceRequest->getUrl()->value, $options);

            return ['content' => $response->getBody()->getContents()];
        } catch (GuzzleException $e) {
            throw new \Exception($e->getMessage());
        }
    }
}