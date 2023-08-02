<?php

namespace App\Http\Controllers;

use App\Models\Restaurant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class TelegramController extends Controller
{
    public function get(Request $request)
    {
        $apiUrl = 'https://restaurant-api.dicoding.dev/list'; // Ganti dengan URL yang benar
        $bearerToken = '5ffb698e3d9a8ea8d51fb8847c216058';
        // Panggil API Zomato menggunakan HTTP Client (misalnya Guzzle)
        $response = Http::get($apiUrl);

        // Cek apakah panggilan berhasil
        if ($response->successful()) {
            // Ambil data restoran dari respon API Zomato
            $restaurants = $response->json();

            // Lakukan pemrosesan atau penyimpanan data restoran jika diperlukan
            // ...
            foreach ($restaurants['restaurants'] as $data){
                Restaurant::create(
                    [
                        'name' => $data['name'],
                        'menu' => 'https://restaurant-api.dicoding.dev/images/medium/'.$data['pictureId'],
                        'location' => $data['city'],
                        'review' => $data['rating'],
                    ]
                );
            }
            return $restaurants['restaurants']; // Kembalikan data restoran
        } else {
            // Panggilan gagal, tangani kesalahan jika diperlukan
            $errorCode = $response->status();
            $errorMessage = $response->json()['message'] ?? 'Unknown error occurred';
            // ...

            return null; // Kembalikan null atau berikan tanggapan yang sesuai untuk kegagalan
        }
    }

    public function index(Request $request){
        $telegramBotToken = '6224982493:AAErxLpbbBW3gNGgWGCam_JGwrfZlw6RHqw';
        $apiBaseUrl = "https://api.telegram.org/bot{$telegramBotToken}/";

        $data = $request->all();
        $chatId = $data['message']['chat']['id'];
        $text = $data['message']['text'];

        if (strpos($text, '/semua') === 0) {
            $keyword = trim(str_replace('/semua', '', $text));
            $restaurants = Restaurant::all();
            Http::post($apiBaseUrl . 'sendMessage', [
                'chat_id' => $chatId,
                'text' => $text
            ]);
        }

    }
}
