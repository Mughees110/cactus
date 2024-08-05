<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Carbon\Carbon;
use App\Models\User;
use App\Models\Payment;
use App\Models\Count;
use Stripe\Stripe;
use Stripe\Customer;
use Stripe\Charge;
class Noti extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:noti';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        Stripe::setApiKey(config('services.stripe.secret'));
        $day=Carbon::now()->day;
        if($day==1){
            $users=User::all();
            $date=Carbon::now()->toDateString();
            foreach ($users as $key => $user) {
                $exists=Payment::where('userId',$user->id)->where('date',$date)->exists();
                if($exists==false){
                    $payment=new Payment;
                    $payment->userId=$user->id;
                    $payment->date=$date;
                    $sum=0;
                    $counts=Count::where('userId',$user->id)->get();
                    foreach ($counts as $key => $value) {
                        $sum=$sum+$value->points;
                    }
                    if($sum<4000){
                        $payment->status="not-eligible";
                        $payment->amount=0;
                        $payment->points=$sum;
                    }
                    if($sum>=4000&&$sum<12000){
                        $payment->status="pending";
                        $payment->amount=19;
                        $payment->points=$sum;
                    }
                    if($sum>=12000&&$sum<36000){
                        $payment->status="pending";
                        $payment->amount=39;
                        $payment->points=$sum;
                    }
                    if($sum>=36000){
                        $payment->status="pending";
                        $payment->amount=79;
                        $payment->points=$sum;
                    }
                    
                    $payment->save();
                }
            }
            $payments=Payment::where('status','pending')->orWhere('status','failed')->get();
            foreach ($payments as $keyp => $payment) {
                $user=User::find($payment->userId);
                if($user&&$user->stripeId){
                    $charge = Charge::create([
                        'amount' => $payment->amount,
                        'currency' => 'eur',
                        'description' => 'Recurring payment',
                        'customer' => $user->stripeId,
                    ]);
                    if($charge&&$charge->status=="succeeded"){
                        $payment->status="success";
                        $payment->save();
                    }
                    if(!$charge||$charge->status!="succeeded"){
                        $payment->status="failed";
                        $payment->save();
                    }
                }
            }

        }
        dd('done');
    }
}
