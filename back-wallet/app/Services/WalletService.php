<?php

namespace App\Services;

use App\Models\Client;

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

        // Persist the client to the database
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

}
