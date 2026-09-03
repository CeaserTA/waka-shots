<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreEnquiryRequest;
use App\Models\Enquiry;
use Illuminate\Http\RedirectResponse;

class EnquiryController extends Controller
{
    public function store(StoreEnquiryRequest $request): RedirectResponse
    {
        // Status is set here rather than accepted from the request — the
        // public form has no business deciding whether an enquiry arrives
        // already "booked".
        Enquiry::create([...$request->validated(), 'status' => 'new']);

        // to_route rather than back(): the success toast only exists on the
        // contact page, and back() depends on a Referer header that isn't
        // guaranteed to be there.
        return to_route('contact')->with('success', 'Thank you. Your enquiry has been sent successfully.');
    }
}
