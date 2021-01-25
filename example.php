<?php

require 'vendor/autoload.php';

use Onetoweb\Onlineparcel\Client;

// client parameters
$publicKey = 'public_key';
$secretKey = 'secret_key';

// setup client
$client = new Client($publicKey, $secretKey);

// get shipment methods
$shipmentMethods = $client->getShippingMethods([
    'country' => 'NL',
    'weight' => 2.5,
    'length' => 2,
]);

// create shipment
$shipment = $client->createShipment([
    'shipping_option' => 'AFHALEN',
    'sender_address' => false,
    'return_address' => [
        'country' => 'NL',
        'name' => 'Jane Doe',
        'company_name' => 'Some company, BV',
        'postal_code' => '9738 AM',
        'house_number' => '5',
        'house_number_addition' => '',
        'street' => 'Zeewinde',
        'city' => 'Groningen',
        'phone' => '088-1234567',
        'email' => 'jd@example.com',
    ],
    'country' => 'NL',
    'customer_id' => '',
    'name' => 'John Doe',
    'company_name' => '',
    'postal_code' => '9738 AM',
    'house_number' => '5',
    'house_number_addition' => '',
    'street' => 'Zeewinde',
    'city' => 'Groningen',
    'phone' => '088-1234567',
    'email' => 'jd@example.com',
    'weight' => 1,
    'labels_num' => 1,
    'webshop_order_id' => 'ORD123',
    'items' => [
        [
            'sku' => '1234567',
            'qty' => 1,
            'title' => 'Blue jeans',
            'location' => '',
        ]
    ]
]);

// create label
$label = $client->createLabel([
    'webshop_order_ids' => ['ORD123'],
    'printer' => 'laser_a4'
]);

// create packing list
$packingList = $client->createPackingList([
    'webshop_order_ids' => ['ORD123'],
]);
