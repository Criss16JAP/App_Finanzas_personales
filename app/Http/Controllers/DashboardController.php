<?php

namespace App\Http\Controllers;

use App\Services\DashboardService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function __construct(private DashboardService $dashboardService)
    {
    }

    public function index()
    {
        $data = $this->dashboardService->getDashboardData(Auth::user());
        return view('dashboard', $data);
    }
}
