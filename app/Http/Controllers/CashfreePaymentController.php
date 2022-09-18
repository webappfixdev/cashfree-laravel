<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CashfreePaymentController extends Controller
{
    public function create(Request $request)
     {
          return view('payment-create');
     }


     public function store(Request $request)
     {
          $validated = $request->validate([
               'name' => 'required|min:3',
               'email' => 'required',
               'mobile' => 'required',
               'amount' => 'required'
          ]);
               
          $url = "https://sandbox.cashfree.com/pg/orders";

          $headers = array(
               "Content-Type: application/json",
               "x-api-version: 2022-01-01",
               "x-client-id: ".env('CASHFREE_API_KEY'),
               "x-client-secret: ".env('CASHFREE_API_SECRET')
          );

          $data = json_encode([
               'order_id' =>  'order_'.rand(1111111111,9999999999),
               'order_amount' => $validated['amount'],
               "order_currency" => "INR",
               "customer_details" => [
                    "customer_id" => 'customer_'.rand(111111111,999999999),
                    "customer_name" => $validated['name'],
                    "customer_email" => $validated['email'],
                    "customer_phone" => $validated['mobile'],
               ],
               "order_meta" => [
                    "return_url" => 'http://127.0.0.1:8000/cashfree/payments/success/?order_id={order_id}&order_token={order_token}'
               ]
          ]);

          $curl = curl_init($url);

          curl_setopt($curl, CURLOPT_URL, $url);
          curl_setopt($curl, CURLOPT_POST, true);
          curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
          curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
          curl_setopt($curl, CURLOPT_POSTFIELDS, $data);

          $resp = curl_exec($curl);

          curl_close($curl);

          return redirect()->to(json_decode($resp)->payment_link);
     }

     public function success(Request $request)
     {
          // dd($request->all()); // PAYMENT STATUS RESPONSE

        //     $client = new \GuzzleHttp\Client();
        //     $response = $client->request('GET', 'https://sandbox.cashfree.com/pg/orders/'. $request->get('order_id') .'/settlements', [
        //     'headers' => [
        //         'accept' => 'application/json',
        //         'x-api-version' => '2022-01-01',
        //         "x-client-id" => env('CASHFREE_API_KEY'),
        //         "x-client-secret" => env('CASHFREE_API_SECRET')
        //     ],

        // ]);
        // dd(json_decode($response->getBody()));

        $client = new \GuzzleHttp\Client();
        $response = $client->request('GET', 'https://sandbox.cashfree.com/pg/orders/order_6697568301/payments/885599190', [
          'headers' => [
            'accept' => 'application/json',
            'x-api-version' => '2022-01-01',
            'x-client-id' => '2348819b1223ac38d38c64ee19188432',
            'x-client-secret' => '17a62f830df771696e620ebf3ceba5e6492f8303',
          ],
        ]);

        dd(json_decode($response->getBody()));
     }

}
