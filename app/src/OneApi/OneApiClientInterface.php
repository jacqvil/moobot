<?php namespace Moo\OneApi;

interface OneApiClientInterface
{
    public function authenticate();
    public function customers($mobileNumber);
    public function recipients($customerId);
    public function corridors($customerId);
    public function remittanceCities($customerId, $corridorId);
    public function remittanceCountries($customerId);
    public function calculate($customerId, $recipientId, $corridorId, $countryId, $payoutAmount);
    public function createOrder($customerId, $recipientId, $corridorId, $payInAmount,
        $payoutAmount, $calculationToken, $operatorId, $publicBuyRate, $transactionReference, $affiliateBalanceAmount);
}