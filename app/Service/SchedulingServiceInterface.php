<?php

namespace App\Service;

use Illuminate\Http\Request;

interface SchedulingServiceInterface
{
    public function getAll(Request $request);

    public function getAvailableTimeSlots($clinic_id, Request $request);

    public function schedule(Request $request);
    
}