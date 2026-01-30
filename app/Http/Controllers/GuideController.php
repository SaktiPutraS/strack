<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View;

class GuideController extends Controller
{
    /**
     * Display the guide chat page
     */
    public function index(): View
    {
        return view('guide.index');
    }

    /**
     * Show Phase 1 - Initial Contact
     */
    public function phase1(): View
    {
        return view('guide.phase1');
    }

    /**
     * Show Phase 2 - Requirement Gathering
     */
    public function phase2(): View
    {
        return view('guide.phase2');
    }

    /**
     * Show Phase 3 - Quotation & Deal
     */
    public function phase3(): View
    {
        return view('guide.phase3');
    }

    /**
     * Show Phase 4 - Development
     */
    public function phase4(): View
    {
        return view('guide.phase4');
    }

    /**
     * Show Phase 5 - Delivery & Payment
     */
    public function phase5(): View
    {
        return view('guide.phase5');
    }

    /**
     * Show Pricing Guide
     */
    public function pricing(): View
    {
        return view('guide.pricing');
    }
}
