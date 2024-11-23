<?php

namespace App\Http\Controllers;

use App\Models\Points;
use App\Models\Users;
use App\Models\Trips;
use App\Models\Verifications;
use App\Models\Transactions;
use App\Models\Withdrawals;
use Illuminate\Http\Request;
use Carbon\Carbon;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        $startOfDay = Carbon::today(); // Start of the day (00:00:00)
        $endOfDay = Carbon::today()->setTime(23, 59, 59); // End of the day (23:59:59)
        $today = Carbon::today()->format('Y-m-d');
        $yesterday = Carbon::yesterday()->format('Y-m-d'); // Getting yesterday's date

        $trips_count = Trips::count();
        $today_registration_count = Users::whereBetween('created_at', [$startOfDay, $endOfDay])->count();
        $today_reward_count = Transactions::where('type', 'reward_points')
        ->whereDate('datetime', $today)
        ->count();
        $pending_withdrawals_count = Withdrawals::where('status', 0)
        ->sum('amount');
        $today_recharge_amount = Transactions::where('type', 'add_points')
        ->whereDate('datetime', $today)
        ->sum('amount');
        $pending_trips_count = Trips::where('trip_status', 0)->count();
        $pending_verification_count = Verifications::where('payment_status', 0)
        ->whereNotNull('payment_image')
        ->where('payment_image', '<>', '')
        ->count();
        $today_active_users = Users::whereDate('active_datetime', $today)->count();
        
        // Optional: Count of pending profiles and cover images
        // $pending_profile_count = Users::where('profile_verified', 0)->whereNotNull('profile')->count();
        // $pending_cover_image_count = Users::where('profile_verified', 0)->whereNotNull('cover_img')->count();
        
        return view('home', [
            'trips_count' => $trips_count,
            'today_registration_count' => $today_registration_count,
            'today_reward_count' => $today_reward_count,
            'pending_withdrawals_count' => $pending_withdrawals_count,
            'pending_trips_count' => $pending_trips_count,
            'pending_verification_count' => $pending_verification_count,
            'today_recharge_amount' => $today_recharge_amount,
            'today_active_users' => $today_active_users,
        ]);
    }
}
