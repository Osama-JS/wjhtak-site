<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\Trip;
use Illuminate\Http\Request;

class CompanyProfileController extends Controller
{
    public function show(Company $company)
    {
        $trips = Trip::with(['images', 'fromCountry', 'toCountry'])
            ->where('company_id', $company->id)
            ->where('active', true)
            ->where('expiry_date', '>=', now())
            ->latest()
            ->paginate(12);

        return view('frontend.companies.profile', compact('company', 'trips'));
    }
}
