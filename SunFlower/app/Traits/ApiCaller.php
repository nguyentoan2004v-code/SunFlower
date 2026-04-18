<?php

namespace App\Traits;

use Illuminate\Support\Facades\Http;

trait ApiCaller
{
    public function callApi($endpoint, $method = 'GET', $data = [])
    {
        // Nhớ check lại port nếu bro xài XAMPP thì bỏ cái :8000 đi nha
        $baseUrl = 'http://127.0.0.1:8000'; 
        
        $request = Http::withHeaders(['Accept' => 'application/json']);

        // Nếu có token trong session thì nhét vào Header
        if (session()->has('api_token')) {
            $request->withToken(session('api_token'));
        }

        try {
            if (strtoupper($method) === 'POST') {
                $response = $request->post($baseUrl . $endpoint, $data);
            } elseif (strtoupper($method) === 'PUT') {
                $response = $request->put($baseUrl . $endpoint, $data);
            } elseif (strtoupper($method) === 'DELETE') {
                $response = $request->delete($baseUrl . $endpoint, $data);
            } else {
                $response = $request->get($baseUrl . $endpoint, $data);
            }
            return $response->json();
        } catch (\Exception $e) {
            return ['error' => 'Lỗi kết nối API.', 'message' => $e->getMessage()];
        }
    }
}