<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Stripe\Stripe;
use Stripe\Customer;
use Stripe\Charge;
use App\Models\User;
class PaymentController extends Controller
{
    public function createCharge(Request $request)
    {
        Stripe::setApiKey(config('services.stripe.secret'));
        if(!$request->token){
            $message='Unable to create stripe token';
            return response()->json(['message'=>$message]);
        }
        if(!$request->userId){
            $message='Unable to get userId';
            return response()->json(['message'=>$message]);
        }
        
        /*$request->validate([
            'token' => 'required|string',
            'amount' => 'required|integer',
            'userId'=>'required' // Amount in cents
        ]);*/
        $user=User::find($request->userId);
        if(!$user){
            $message="User not found";
            return response()->json(['message'=>$message]);
        }
        $user->stripeToken=$request->token;

        try {
            // Create a customer
            $customer = Customer::create([
                'description' => 'Customer for example',
                'source' => $request->token,
            ]);
            if(!$customer||!$customer->id){
                return response()->json(['message'=>'Unable to create customer']);
            }
            $user->stripeId=$customer->id;
            $user->save();


            // Save customer ID in your database
            // Example: $user->stripe_customer_id = $customer->id;
            // $user->save();

            // Charge the customer
            /*$charge = Charge::create([
                'amount' => $request->amount,
                'currency' => 'usd',
                'description' => 'Example charge',
                'customer' => $customer->id,
            ]);*/
            

            return response()->json(['message'=>'success']);
        } catch (\Exception $e) {
            $message=$e->getMessage();
            return response()->json(['message'=>$message]);
        }
    }

    public function chargeCustomer(Request $request)
    {
        Stripe::setApiKey(config('services.stripe.secret'));


        try {
            $charge = Charge::create([
                'amount' => 100,
                'currency' => 'usd',
                'description' => 'Example charge',
                'customer' => "cus_QY0iKeYenOlmxM",
            ]);

            return response()->json(['status' => 'success', 'charge' => $charge]);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()]);
        }
    }
    public function getChargeDetails()
    {
        // Set your secret key. Remember to switch to your live secret key in production!
        Stripe::setApiKey(config('services.stripe.secret'));

        try {
            // Retrieve the charge details using the charge ID
            $charge = Charge::retrieve("ch_3PgujTKiAN37OnJd0OwXE6Gb");

            // Return the charge details as a JSON response
            return response()->json(['status' => 'success', 'charge' => $charge]);
        } catch (\Exception $e) {
            // Handle any errors that occur
            return response()->json(['status' => 'error', 'message' => $e->getMessage()]);
        }
    }
}
