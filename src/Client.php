<?php

namespace Onetoweb\Onlineparcel;

use GuzzleHttp\Client as GuzzleClient;
use GuzzleHttp\RequestOptions;
use DateTimeZone;
use DateTime;

/**
 * Onlineparcel Api Client.
 *
 * @author Jonathan van 't Ende <jvantende@onetoweb.nl>
 * @copyright Onetoweb. B.V.
 *
 * @link https://ship.onlineparcel.nl/nl/domains/2/api/docs
 */
class Client
{
    const METHOD_POST = 'POST';
    
    /**
     * @var string
     */
    private $publicKey;
    
    /**
     * @var string
     */
    private $secretKey;
    
    /**
     * @param string $publicKey
     * @param string $secretKey
     */
    public function __construct(string $publicKey, string $secretKey)
    {
        $this->publicKey = $publicKey;
        $this->secretKey = $secretKey;
    }
    
    /**
     * @param string $endpoint
     * @param string $jsonData
     * @param string $timestamp
     */
    private function createHashString(string $endpoint, string $jsonData, string $timestamp): string
    {
        return hash_hmac('sha512', implode('|', [
            $this->publicKey,
            self::METHOD_POST,
            $endpoint,
            $jsonData,
            $timestamp
        ]), $this->secretKey);
    }
    
    /**
     * @param string $endpoint
     * @param array $data = []
     * 
     * @return array|null
     */
    public function request(string $endpoint, array $data = []): ?array
    {
        // get timestamp
        $date = new DateTime('now', new DateTimeZone('UTC'));
        $timestamp = $date->format('c');
        
        // encode data
        $jsonData = json_encode($data);
        
        // build options
        $options = [
            RequestOptions::HEADERS => [
                'Content-Type' => 'application/json',
                'charset' => 'utf-8',
                'x-date' => $timestamp,
                'x-public' => $this->publicKey,
                'x-hash' => $this->createHashString($endpoint, $jsonData, $timestamp),
            ],
            RequestOptions::BODY => $jsonData
        ];
        
        // build guzzle client
        $guzzleClient = new GuzzleClient([
            'base_uri' => 'https://ship.onlineparcel.nl'
        ]);
        
        // make request
        $result = $guzzleClient->request(self::METHOD_POST, $endpoint, $options);
        
        // get contents
        $contents = $result->getBody()->getContents();
        
        // return data
        return json_decode($contents, true);
    }
    
    /**
     * @param array $data = []
     *
     * @return array|null
     */
    public function getShippingMethods(array $data = []): ?array
    {
        return $this->request('/nl/api/shipment/methods', $data);
    }
    
    /**
     * @param array $data = []
     *
     * @return array|null
     */
    public function createShipment(array $data = []): ?array
    {
        return $this->request('/nl/api/shipment/create', $data);
    }
    
    /**
     * @param array $data = []
     *
     * @return array|null
     */
    public function createLabel(array $data = []): ?array
    {
        return $this->request('/nl/api/shipment/labels', $data);
    }
    
    /**
     * @param array $data = []
     *
     * @return array|null
     */
    public function createPackingList(array $data = []): ?array
    {
        return $this->request('/nl/api/shipment/packing-list', $data);
    }
}