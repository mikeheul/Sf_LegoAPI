<?php

namespace App\HttpClient;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Contracts\HttpClient\HttpClientInterface;

/**
 * Class LegoHttpClient
 * @package App\Client
 */
class LegoHttpClient extends AbstractController
{
    /**
     * @var HttpClientInterface
     */
    private $httpClient;
    private $key;

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
        $key = $this->getParameter('api_key');
        $response = $this->httpClient->request('GET', "/api/v3/lego/sets/?key=$key&page_size=1000&theme_id=171&ordering=year", [
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
        $key = $this->getParameter('api_key');
        $response = $this->httpClient->request('GET', "/api/v3/lego/sets/$id/?key=$key", [
            'verify_peer' => false, 
        ]);
        $data = json_decode($response->getContent(), true);

        return $data;
    }

    public function getThemeInfo($id): array 
    {
        $key = $this->getParameter('api_key');
        $response = $this->httpClient->request('GET', "/api/v3/lego/themes/$id/?key=$key", [
            'verify_peer' => false, 
        ]);
        $data = json_decode($response->getContent(), true);

        return $data;
    }

    public function getMinifigs($id): array 
    {
        $key = $this->getParameter('api_key');
        $response = $this->httpClient->request('GET', "/api/v3/lego/sets/$id/minifigs/?key=$key", [
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