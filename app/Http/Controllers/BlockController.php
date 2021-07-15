<?php

namespace App\Http\Controllers;

use App\Models\Block;
use Illuminate\Http\Request;
use App\Service\SchedulingService;
use Carbon\Carbon;
use Exception;

class BlockController extends Controller
{
    private $schedulingService;

    public function __construct(SchedulingService $schedulingService)
    {
        $this->schedulingService = $schedulingService;
    }    

    public function index(Request $request)
    {
        return $this->schedulingService->getAll($request);
    }

    /**
     * Return all the available time slots for a given clinician and date
     * Assume that the clinics are open from 9am to 5pm.
     *
     * GET api/blocks/available
     *
     * @param  unsigned int clinic_id    
     * @param  \Illuminate\Http\Request  $request
     * 
     * @return \Illuminate\Http\Response
     */
    public function available($clinic_id, Request $request)
    {
        $result = [];
        try  {

            $result['available'] = $this->schedulingService->getAvailableTimeSlots($clinic_id, $request);            
            $result['success'] = 1;             

        } catch (Exception $e) {
            $result = ['success' => 0, 'error' => $e->getMessage()];
        }
        return $result;
    }


    /**
     * Book an appointment of X minutes (30 minute slots) from one of the available time slot for a patient
     *
     * POST api/blocks/book
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $result = [];
        try {

            $result['block'] = $this->schedulingService->schedule($request);
            $result['success'] = 1;                
    
        } catch (Exception $e) {
            $result['success'] = 0;
            $result['error'] = $e->getMessage();
        }
        return response($result);
    }

}