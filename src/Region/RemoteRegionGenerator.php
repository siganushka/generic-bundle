<?php

namespace Siganushka\GenericBundle\Region;

use Siganushka\GenericBundle\Model\RegionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class RemoteRegionGenerator implements RegionGeneratorInterface
{
    const URL = 'https://raw.githubusercontent.com/modood/Administrative-divisions-of-China/master/dist/pca-code.json';

    protected $httpClient;

    public function __construct(HttpClientInterface $httpClient)
    {
        $this->httpClient = $httpClient;
    }

    public function generate(): ?RegionInterface
    {
        $response = $this->httpClient->request('GET', self::URL);
        $contents = $response->getContent();

        $regions = json_decode($contents, true);
        if (JSON_ERROR_NONE !== json_last_error()) {
            throw new \UnexpectedValueException(json_last_error_msg());
        }

        dd($regions);

        return null;
    }
}
