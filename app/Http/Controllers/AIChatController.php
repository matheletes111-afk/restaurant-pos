<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class AIChatController extends Controller
{
    public function index()
    {
        return view('ask-ai');
    }

public function send(Request $request)
{
    $question = $request->question;

    try {
        $curl = curl_init();

        curl_setopt_array($curl, [
            CURLOPT_URL => "https://api.deepseek.com/v1/chat/completions",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST => true,
            CURLOPT_HTTPHEADER => [
                "Content-Type: application/json",
                "Authorization: Bearer " . env('DEEPSEEK_API_KEY')
            ],
            CURLOPT_POSTFIELDS => json_encode([
                "model" => "deepseek-chat",
                "messages" => [
                    ["role" => "system", "content" => "You are an expert assistant for restaurant business. Give answers in simple language with clear suggestions."],
                    ["role" => "user", "content" => $question]
                ],
                "max_tokens" => 500
            ], JSON_UNESCAPED_SLASHES)
        ]);

        $response = curl_exec($curl);
        $error = curl_error($curl);
        curl_close($curl);

        if ($error) {
            return response()->json(['answer' => "CURL ERROR: " . $error]);
        }

        $decoded = json_decode($response, true);

        if (!isset($decoded['choices'][0]['message']['content'])) {
            return response()->json(['answer' => "Invalid response: " . $response]);
        }

        return response()->json([
            'answer' => $decoded['choices'][0]['message']['content']
        ]);

    } catch (\Exception $e) {
        return response()->json(['answer' => "Exception: " . $e->getMessage()]);
    }
}



}
