<?php namespace OneApi;

use GuzzleHttp\Client;

class OneApiClient implements OneApiClientInterface
{

    /**
     * @var Client;
     */
    protected $client;

    /**
     * OneApiClient constructor.
     *
     * @param Client $client
     */
    public function __construct(Client $client)
    {
        $this->client = new $client;
    }

    public function authenticate($mobileNumber)
    {
        // TODO: Implement authenticate() method.
    }

    public function customers($mobileNumber)
    {
        // TODO: Implement customers() method.
    }

    public function recipients($customerId)
    {
        // TODO: Implement recipients() method.
    }

    public function corridors($customerId)
    {
        // TODO: Implement corridors() method.
    }

    public function remittanceCities($customerId, $corridorId)
    {
        // TODO: Implement remittanceCities() method.
    }

    public function remittanceCountries($customerId)
    {
        // TODO: Implement remittanceCountries() method.
    }

    public function calculate($calculator)
    {
        // TODO: Implement calculate() method.
    }

    public function createOrder($customerId)
    {
        // TODO: Implement createOrder() method.
    }


}