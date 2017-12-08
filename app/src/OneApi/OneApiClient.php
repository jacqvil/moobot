<?php namespace Moo\OneApi;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\BadResponseException;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use Moo\OneApi\OneApiClientInterface;

/**
 * Class OneApiClient
 * @package OneApi
 * @author  Jacques Viljoen <jacques.viljoen@mukuru.com>
 */
class OneApiClient implements OneApiClientInterface
{
    CONST GET = 'GET';
    CONST POST = 'POST';
    CONST PUT = 'PUT';
    CONST SUCCESS = 'success';

    CONST HTTP_SUCCESS = 200;
    /**
     * @var Client;
     */
    protected $client;

    /**
     * @var array
     */
    protected $config;

    /**
     * @var null
     */
    protected $token = null;

    /**
     * OneApiClient constructor.
     *
     * @param Client $client
     * @param array $config
     */
    public function __construct(Client $client, array $config)
    {
        $this->client = new $client;
        $this->config = $config;
    }

    public function authenticate()
    {
        $result = $this->request(self::POST, 'token', [
            'grant_type'    => 'password',
            'client_id'     => $this->config['ONEAPI_CLIENT'],
            'client_secret' => $this->config['ONEAPI_SECRET'],
            'username'      => $this->config['ONEAPI_USERNAME'],
            'password'      => $this->config['ONEAPI_PASSWORD']
        ], TRUE);

        $this->token = $result->access_token;
    }

    /**
     * @param $mobileNumber
     */
    public function customers($mobileNumber)
    {
        \Log::info('calling customers for mobile: ' . $mobileNumber);
       $result  = $this->request(self::GET, 'customers', [
            'mobile_number'     => $mobileNumber,
            'payment_gateway'   => $this->config['ONEAPI_PAYMENT_GATEWAY']
        ], false);

       \Log::info('result status' . $result->status);
        if ($result->status == self::SUCCESS) {
            return $result->data->customers;
        }

    }

    /**
     * @param $customerId
     * @return array;
     */
    public function recipients($customerId)
    {
        $result  = $this->request(self::GET, 'customers/'.$customerId.'/recipients', [
        ], false);

        \Log::info('result status' . $result->status);
        if ($result->status == self::SUCCESS) {
            return $result->data->recipients;
        }
    }

    /**
     * @param $customerId
     */
    public function corridors($customerId)
    {
        // TODO: Implement corridors() method.
    }

    /**
     * @param $customerId
     * @param $corridorId
     */
    public function remittanceCities($customerId, $corridorId)
    {
        // TODO: Implement remittanceCities() method.
    }

    /**
     * @param $customerId
     */
    public function remittanceCountries($customerId)
    {
        // TODO: Implement remittanceCountries() method.
    }

    /**
     * @param $customerId
     * @param $recipientId
     * @param $corridorId
     * @param $payoutAmount
     * @return mixed
     */
    public function calculate($customerId, $recipientId, $corridorId, $payoutAmount)
    {
        \Log::info('calling calculate...');
        $result = $this->request(self::POST, 'orders/calculator', [
            'payment_gateway'   => $this->config['ONEAPI_PAYMENT_GATEWAY'],
            'customer_id'       => $customerId,
            'recipient_id'      => $recipientId,
            'corridor_id'       => $corridorId,
            'pay_out_amount'    => $payoutAmount,
            'is_rest'           => true
        ], true);

        //\Log::info('result status' . $result->status);
        if ($result !== null && $result->status == self::SUCCESS) {
            return $result->data->quote_info;
        }

        return null;
    }

    /**
     * @param $customerId
     */
    public function createOrder($customerId)
    {
        // TODO: Implement createOrder() method.
    }

    /**
     * @param $type
     * @param $endpoint
     * @param $body
     * @param bool $asJson
     * @param array $headers
     * @return mixed
     */
    protected function request($type, $endpoint, array $body, $asJson = TRUE, $headers = [])
    {
        $options = [
            'headers'   => $headers
        ];

        if ($this->token !== null) {
            if ($asJson && $type == self::POST) {
                $options['query'] = ['access_token' => $this->token];
            } else {
                $body['access_token'] = $this->token;
            }
        }

        if ($asJson) {
            $options['headers']['content-type'] = 'application/json';
            $options['json'] = $body;
        }

        if ($type == self::GET) {
            $options['query'] = $body;
        }

        try {
            \Log::info('about to call OneApi');
            \Log::info(json_encode($options['json']));
            $response = $this->client->request($type, $this->config['ONEAPI_URL'] . $endpoint, $options);

            if ($response->getStatusCode() == self::HTTP_SUCCESS) {
                $result = json_decode($response->getBody());
                return $result;
            }
            else {
                throw new BadResponseException('OneApi request failed with status code ' . $response->getStatusCode(), $response);
            }

        } catch (RequestException $e) {
            \Log::error($e->getMessage());
        }

        return null;

    }

}