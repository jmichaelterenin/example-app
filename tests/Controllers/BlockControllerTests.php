<?php
    
namespace Tests\Controllers;
    
use Illuminate\Http\Response;
use Tests\TestCase;
    
class BlockControllerTests extends TestCase {
   
  public function testIndexReturnsDataInValidFormat() {
    
        $this->json('get', 'api/blocks')
         ->assertStatus(Response::HTTP_OK)
         ->assertJsonStructure(
             [                
                '*' => [
                    'id',
                    'starts_at',
                    'length',
                    'block_type',
                    'clinic_id',
                    'patient_id'
                ]                 
             ]
         );
  }

  public function testAvailableCheckReturnsDataInValidFormat() {
    
    $this->json('get', 'api/blocks/available/5130/?query_date=20200810')
     ->assertStatus(Response::HTTP_OK)
     ->assertJsonStructure(
         [   
             
            'available' => [
                '*' => [
                    'start',
                    'end'
                ]               
            ]  
         ]
     );
  }

  public function testAvailableCheckReturnsErrorWithInvalidFormat() {
    
    $this->json('get', 'api/blocks/available/5130/?query_date=dffjl334721')
     ->assertStatus(Response::HTTP_OK)
     ->assertJson([
        'success' => false,
    ]);
}

  public function testStoreAction() {
        $response = $this->call('POST', 'api/blocks/store', array(
            'starts_at' => '2021-08-14 09:00:00',
            'length' => 60,
            'block_type' => 'appointment',
            'clinic_id' => 5130,
            'patient_id' => 123   
        ));

        $response
            ->assertStatus(200)
            ->assertJson([
                'success' => true,
            ]);
  }

  public function testStoreActionInputValidationStartsAt() {

    $response = $this->call('POST', 'api/blocks/store', array(
        'starts_at' => '2020-08-10 13:00:00',
        'length' => 60,
        'block_type' => 'appointment',
        'clinic_id' => 10425,
        'patient_id' => 123   
    ));

    $response
        ->assertStatus(200)
        ->assertJson([
            'success' => false,
        ]);

  }

  public function testStoreActionInputValidationLength() {

    $response = $this->call('POST', 'api/blocks/store', array(
        'starts_at' => '2021-08-10 13:00:00',
        'length' => 76,
        'block_type' => 'appointment',
        'clinic_id' => 10425,
        'patient_id' => 123   
    ));

    $response
        ->assertStatus(200)
        ->assertJson([
            'success' => false,
        ]);

  }

  public function testStoreActionInputValidationBlockType() {

    $response = $this->call('POST', 'api/blocks/store', array(
        'starts_at' => '2021-08-10 13:00:00',
        'length' => 60,
        'block_type' => 'lunch',
        'clinic_id' => 10425,
        'patient_id' => 123   
    ));

    $response
        ->assertStatus(200)
        ->assertJson([
            'success' => false,
        ]);

  }  

  public function testStoreActionInputValidationClinicId() {

    $response = $this->call('POST', 'api/blocks/store', array(
        'starts_at' => '2021-08-10 13:00:00',
        'length' => 60,
        'block_type' => 'appointment',
        'clinic_id' => 0,
        'patient_id' => 123   
    ));

    $response
        ->assertStatus(200)
        ->assertJson([
            'success' => false,
        ]);

  }  

  public function testStoreActionPreventsOverlappingAppointments() {

    $response = $this->call('POST', 'api/blocks/store', array(
        'starts_at' => '2021-08-10 15:30:00',
        'length' => 60,
        'block_type' => 'appointment',
        'clinic_id' => 10425,
        'patient_id' => 123   
    ));

    $response
        ->assertStatus(200)
        ->assertJson([
            'success' => false,
        ]);
  }

  public function testStoreActionPreventsAppointmentOutsideBusinessHours() {

    $response = $this->call('POST', 'api/blocks/store', array(
        'starts_at' => '2021-07-21 08:00:00',
        'length' => 60,
        'block_type' => 'appointment',
        'clinic_id' => 10425,
        'patient_id' => 123   
    ));

    $response
        ->assertStatus(200)
        ->assertJson([
            'success' => false,
        ]);
  }

}