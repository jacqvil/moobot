<?php
/**
 * Created by PhpStorm.
 * User: jacquesviljoen
 * Date: 2017/12/07
 * Time: 1:50 PM
 */

namespace OneApi;


interface OneApiClientInterface
{
    public function authenticate($mobileNumber);
    public function customers($mobileNumber);
    public function recipients($customerId);
    public function corridors($customerId);
    public function remittanceCities($customerId, $corridorId);
    public function remittanceCountries($customerId);
    public function calculate($calculator);
    public function createOrder($customerId);
}