<?php

namespace App\HttpClient;

use Symfony\Contracts\HttpClient\HttpClientInterface;

/**
 * Class LegoHttpClient
 * @package App\Client
 */
class LegoHttpClient
{
    /**
     * @var HttpClientInterface
     */
    private $httpClient;

    /**
     * LegottpClient constructor.
     *
     * @param HttpClientInterface $lego
     */
    public function __construct(HttpClientInterface $lego)
    {
        $this->httpClient = $lego;
    }

    /**
     * @return array
     */
    public function getSets(): array
    {
        // 5fd20b58ad29e058d268dfd5d5720edb
        $response = $this->httpClient->request('GET', "/api/v3/lego/sets/?key=5fd20b58ad29e058d268dfd5d5720edb&page_size=1000&theme_id=171&ordering=year", [
            'verify_peer' => false, 
        ]);
        $data = json_decode($response->getContent(), true);

        $sets = [];

        foreach ($data as $set) {
            $sets[] = $set;
        }

        return $sets;
    }

    public function getSetInfo($id): array
    {
        $response = $this->httpClient->request('GET', "/api/v3/lego/sets/$id/?key=5fd20b58ad29e058d268dfd5d5720edb", [
            'verify_peer' => false, 
        ]);
        $data = json_decode($response->getContent(), true);

        return $data;
    }

    public function getThemeInfo($id): array 
    {
        $response = $this->httpClient->request('GET', "/api/v3/lego/themes/$id/?key=5fd20b58ad29e058d268dfd5d5720edb", [
            'verify_peer' => false, 
        ]);
        $data = json_decode($response->getContent(), true);

        return $data;
    }

    public function getMinifigs($id): array 
    {
        $response = $this->httpClient->request('GET', "/api/v3/lego/sets/$id/minifigs/?key=5fd20b58ad29e058d268dfd5d5720edb", [
            'verify_peer' => false, 
        ]);
        $data = json_decode($response->getContent(), true);

        $minifigs = [];

        foreach ($data as $minifig) {
            $minifigs[] = $minifig;
        }

        return $minifigs[3];
    }
}