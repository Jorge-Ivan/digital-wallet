<?php

namespace App\Services;

use App\Models\Client;
use App\Models\Transaction;

class WalletService
{
    public function registerClient($document, $names, $email, $cellphone)
    {
        // Validate input
        if (empty($document) || empty($names) || empty($email) || empty($cellphone)) {
            return [
                'status' => 'false',
                'cod_error' => '01',
                'message_error' => 'All fields are required. One or more parameters are null.',
                'data' => null,
            ];
        }

        // Check if the document or email already exists
        $existingClient = Client::where(['document' => $document])->exists();

        if ($existingClient) {
            return [
                'status' => 'false',
                'cod_error' => '02',
                'message_error' => 'Client with this document already exists.',
                'data' => null,
            ];
        }

        $existingEmail = Client::where(['email' => $email])->exists();

        if ($existingEmail) {
            return [
                'status' => 'false',
                'cod_error' => '03',
                'message_error' => 'Client with this email already exists.',
                'data' => null,
            ];
        }

        // Create a new client instance
        $client = new Client();
        $client->document = $document;
        $client->names = $names;
        $client->email = $email;
        $client->cellphone = $cellphone;

        try {
            $client->save();

            return [
                'status' => 'true',
                'cod_error' => '00',
                'message_error' => 'Client registered successfully.',
                'data' => $client,
            ];
        } catch (\Exception $e) {
            return [
                'status' => 'false',
                'cod_error' => '99',
                'message_error' => 'An error occurred while registering the client.',
                'data' => null,
            ];
        }
    }

    public function chargeBalance($document, $cellphone, $amount)
    {
        // Validate input
        if (empty($document) || empty($cellphone) || empty($amount) || !is_numeric($amount) || $amount<=0) {
            return [
                'status' => 'false',
                'cod_error' => '01',
                'message_error' => 'All fields are required and amount must be a positive number.',
                'data' => null,
            ];
        }

        // Check if client with the document and cellphone already exists
        $client = Client::where(['document' => $document])->where(['cellphone' => $cellphone])->first();

        if (!$client) {
            return [
                'status' => 'false',
                'cod_error' => '04',
                'message_error' => 'Client with this document and cellphone not found.',
                'data' => null,
            ];
        }

        $client->wallet_balance += $amount;

        $transaction = new Transaction();
        $transaction->amount = $amount;
        $transaction->client_id = $client->id;
        $transaction->transaction_type = 'recharge';
        $transaction->sesion_id = bin2hex(random_bytes(16));

        try {
            $transaction->save();
            $client->save();

            return [
                'status' => 'true',
                'cod_error' => '00',
                'message_error' => 'Charge registered successfully.',
                'data' => $transaction,
            ];
        } catch (\Exception $e) {
            return [
                'status' => 'false',
                'cod_error' => '99',
                'message_error' => 'An error occurred while registering the data.',
                'data' => null,
            ];
        }

    }
}
