<?php

namespace App\Http\Controllers;

use App\Models\Block;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;
use Exception;

class BlockController extends Controller
{
    public function index(Request $request)
    {
        return Block::all();
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

            $validator = Validator::make($request->all(), [
                'query_date' => 'nullable|date_format:Ymd'
            ]);
    
            if ($validator->fails()) {
                throw new Exception($validator->errors()->first());            
            }

            $query_date = $request->input('query_date');    
            $query_date = ( $query_date != '' ? Carbon::createFromFormat('Ymd', $query_date)->format('Y-m-d') : Carbon::today()->format('Y-m-d') );
            
            $result['success'] = 1;
            $result['available'] = $this->getAvailableSlots($clinic_id, $query_date);

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
        /**
         *  Check first that the appointment time is available.          
         */
        
        $result = [];
        try {

            $validator = Validator::make($request->all(), [
                'starts_at' => 'required|date_format:Y-m-d H:i:s|after:now',
                'length' => 'required|integer|multiple_of:30',
                'block_type' => 'required|in:appointment,clinic', 
                'clinic_id' => 'required|integer|gt:0',
                'patient_id' => 'required|integer|gt:0',
            ]);
    
            if ($validator->fails()) {
                throw new Exception($validator->errors()->first());            
            }

            $date_str = Carbon::createFromFormat('Y-m-d H:i:s', $request->input('starts_at'))->format('Y-m-d'); 
            $availableSlots = $this->getAvailableSlots($request->input('clinic_id'), $date_str);            
            if (!$this->slotIsAvailable($request->input('starts_at'), $availableSlots))
                throw new Exception('The desired time slot is not available.');            

            $result['success'] = 1;    
            $result['block'] = Block::create([
                'starts_at' => $request->input('starts_at'),
                'length' => $request->input('length'),
                'block_type' => $request->input('block_type'),
                'clinic_id' => $request->input('clinic_id'),
                'patient_id' => $request->input('patient_id'),
            ]);        
    
        } catch (Exception $e) {
            $result['success'] = 0;
            $result['error'] = $e->getMessage();
        }
        return response($result);
    }

    /**
     * Check if the requested appointment time is available based on the time slots
     * 
     * @param string $date_str
     * @param array $availableSlots ['start' => 'H:i:s', 'end' => 'H:i:s']
     * 
     * @return boolean isAvailable
     */
    private function slotIsAvailable($date_str, $availableSlots)
    {        

        $isAvailable = false;
        $dateObj = Carbon::createFromFormat('Y-m-d H:i:s', $date_str);
        $date_only = $dateObj->format('Y-m-d');
        $i = 0;
        while(!$isAvailable 
                && $i < count($availableSlots) 
                && $dateObj->gte( Carbon::createFromFormat('Y-m-d H:i:s', $date_only.' '.$availableSlots[$i]['start']) )) {

            $isAvailable = (
                    $dateObj->gte(Carbon::createFromFormat('Y-m-d H:i:s', $date_only.' '.$availableSlots[$i]['start']))
                    && $dateObj->lt(Carbon::createFromFormat('Y-m-d H:i:s', $date_only.' '.$availableSlots[$i]['end']))
                );
            
            $i++;
        }
        return $isAvailable;
    }

    /**
     * Get the available time slots for a given clinician and date
     * 
     * @param unsigned int $clinic_id
     * @param string $query_date of format: Y-m-d
     * 
     * @return array $result
     */
    private function getAvailableSlots($clinic_id, $query_date)
    {        

        $query = Block::where('clinic_id', $clinic_id);        
        $query->whereDate('starts_at', $query_date);
        $blocks = $query->orderBy('starts_at')->get()->makeHidden(['id', 'clinic_id']);
        
        $start = Carbon::createFromFormat('Y-m-d H:i:s', $query_date.' 09:00:00');             
        $end = Carbon::createFromFormat('Y-m-d H:i:s', $query_date.' 17:00:00'); 
        
        $blocks->each(function($b) use(&$result, &$start) {                
            $starts_at = Carbon::createFromFormat('Y-m-d H:i:s', $b->starts_at);
            if ($starts_at->gt($start)) // gt to handle overlapping appointments
                $result[] = ['start' => $start->format('H:i:s'), 'end' => $starts_at->format('H:i:s')];
            $start = $starts_at->addMinutes($b->length);                  
        });            
        if ($start != $end) 
            $result[] = ['start' => $start->format('H:i:s'), 'end' => $end->format('H:i:s') ];
        
        return $result;    
    }
}