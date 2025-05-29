<?php

namespace App\Http\Controllers\Api\Customer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Restaurant;
use App\Http\Resources\RestaurantResource;

class RestaurantController extends Controller
{
      /**
     * Display a listing of restaurants.
     */
    public function index(Request $request)
    {
        // Optional: add pagination or filtering logic
        $restaurants = Restaurant::query()->get();

        return RestaurantResource::collection($restaurants);
    }
}
