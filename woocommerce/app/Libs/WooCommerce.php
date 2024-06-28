<?php

namespace App\Libs;

use Automattic\WooCommerce\Client;

class Woocommerce
{
    protected $client;

    public function __construct()
    {
        $this->client = new Client(
            env('WOOCOMMERCE_STORE_URL'),
            env('WOOCOMMERCE_CONSUMER_KEY'),
            env('WOOCOMMERCE_CONSUMER_SECRET'),
            [
                'wp_api' => true,
                'version' => 'wc/v3',
            ]
        );
    }

    public function get($endpoint, $parameters = [])
    {
        return $this->client->get($endpoint, $parameters);
    }

    public function post($endpoint, $data)
    {
        return $this->client->post($endpoint, $data);
    }

    public function put($endpoint, $data)
    {
        return $this->client->put($endpoint, $data);
    }
}