<?php

namespace App\Actions;

use App\Models\Restaurant;
use App\Models\Review;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class FetchGoogleReviewsAction
{
    public function execute(Restaurant $restaurant)
    {
        if (!$restaurant->google_data_id && !$restaurant->google_place_id) {
            throw new \Exception('Restaurant must have a Google Data ID (SerpApi) or Place ID.');
        }

        $apiKey = config('services.serpapi.key') ?? env('SERPAPI_KEY');

        if (!$apiKey) {
            throw new \Exception('SerpApi Key is missing (SERPAPI_KEY).');
        }

        // Use data_id if available, otherwise fallback to place_id (though SerpApi prefers data_id for reviews engine)
        $params = [
            'engine' => 'google_maps_reviews',
            'api_key' => $apiKey,
            'hl' => 'en', // Language
        ];

        if ($restaurant->google_data_id) {
            $params['data_id'] = $restaurant->google_data_id;
        } else {
             // Fallback or error? SerpApi google_maps_reviews engine specifically asks for data_id or place_id.
             // place_id might work but data_id is safer if extracted from search.
             $params['place_id'] = $restaurant->google_place_id;
        }

        $response = Http::get('https://serpapi.com/search', $params);

        if ($response->failed()) {
            throw new \Exception('Failed to fetch data from SerpApi: ' . $response->body());
        }

        $data = $response->json();

        if (isset($data['error'])) {
            throw new \Exception('SerpApi Error: ' . $data['error']);
        }

        if (!isset($data['reviews'])) {
            return 0; 
        }

        $reviews = $data['reviews'];
        $count = 0;

        foreach ($reviews as $reviewData) {
            // SerpApi Review structure (usually):
            // {
            //   "user": { "name": "...", "link": "...", "thumbnail": "..." },
            //   "rating": 5,
            //   "date": "...",
            //   "snippet": "...",
            //   "iso_date": "...", (sometimes present, or we parse 'date')
            // }

            // Extract review details
            $authorName = $reviewData['user']['name'] ?? 'Anonymous';
            $authorUrl = $reviewData['user']['link'] ?? null;
            $profilePhotoUrl = $reviewData['user']['thumbnail'] ?? null;
            $rating = $reviewData['rating'] ?? 0;
            $text = $reviewData['snippet'] ?? '';
            $relativeTime = $reviewData['date'] ?? null;
            
            // Try to parse time. SerpApi often gives '2 months ago' or 'iso_date' if available.
            // If iso_date is available (e.g. from some engines), use it. google_maps_reviews might just give relative string.
            // We'll trust the relative string for display if timestamp isn't precise.
            // Let's create a unique ID. SerpApi doesn't always provide a stable ID for reviews. 
            // We use MD5 of author + text to avoid dupes.
            
            $googleReviewId = md5($authorName . ($reviewData['iso_date'] ?? $relativeTime) . $text);

            $existing = Review::where('restaurant_id', $restaurant->id)
                ->where('google_review_id', $googleReviewId)
                ->first();

            if (!$existing) {
                Review::create([
                    'restaurant_id' => $restaurant->id,
                    'author_name' => $authorName,
                    'author_url' => $authorUrl,
                    'profile_photo_url' => $profilePhotoUrl,
                    'rating' => $rating,
                    'text' => $text,
                    'relative_time_description' => $relativeTime,
                    'time' => isset($reviewData['iso_date']) ? \Carbon\Carbon::parse($reviewData['iso_date']) : null, // Best effort
                    'google_review_id' => $googleReviewId,
                    'is_visible' => true,
                ]);
                $count++;
            }
        }

        return $count;
    }
}
