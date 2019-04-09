<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Deposit;
use Auth;
use Alert;
use App\Activity;
use App\Deposit_method;

class DepositController extends Controller
{
    public function __construct(){
    	$this->middleware('auth');
    }

    public function index(){}
    public function deposit(){
        return view('deposit.new');
    }
    public function deposit_add(Request $r){
        $type = $r->type;
        $method = $r->method;
        $sender = $r->sender;
        $quantity = $r->quantity;



        $r->validate([
            'type' => 'required',
            'method' => 'required|exists:deposit_methods,id',
            'sender' => 'required|string',
            'quantity' => 'required|numeric|min:'.env('MIN_DEPOSIT')
        ]);
        


        $check = Deposit_method::find($method);
        $rate = $check->rate;
        $data = $check->data;
        
        if($check->name == 'BANK BCA' && $check->type == 'AUTO'){
            $unique = rand(0,999);
            $send_quantity = $quantity + $unique;
            $get_balance = $send_quantity * $rate;
        }else{
            $send_quantity = $quantity;
            $get_balance = $quantity * $rate;
        }
        

        $deposit = new Deposit;
        $deposit->user_id = Auth::user()->id;
        $deposit->quantity = $send_quantity;
        $deposit->sender = $sender;
        $deposit->get_balance = $get_balance;
        $deposit->method = $method;
        $deposit->status = "Pending";
        $deposit->save();

        $activity = new Activity;
        $activity->user_id = Auth::user()->id;
        $activity->type = "Deposit";
        $activity->description = "Membuat permintaan deposit sebesar Rp ".$send_quantity;
        $activity->user_agent = $r->header('User-Agent');
        $activity->ip = $r->ip();
        $activity->save();  

        if($type == 'Manual'){
            session()->flash('success',"<b>Permintaan deposit diterima</b><br> <b>Tujuan transfer:</b> $data <br><b>  Nominal transfer: Rp </b> ".number_format($send_quantity)." <br> <b>Dapat saldo:</b> ".number_format($get_balance)."<br> Jika sudah melakukan transfer silahkan hubungi admin di <a href='/contact'>Halaman Kontak</a>");
            Alert::success('Sukses membuat deposit','Sukses!');
            return redirect()->back();
        }else{
            session()->flash('success',"<b>Permintaan deposit diterima</b><br> <b>Tujuan transfer:</b> $data <br><b>  Nominal transfer: </b>Rp  ".number_format($send_quantity)." <br> <b>Dapat saldo: Rp </b> ".number_format($get_balance)."<br> Jika sudah melakukan transfer silahkan tunggu 5-10 menit agar saldo masuk. Jika belum masuk juga silahkan hubungi admin di <a href='/contact'>Halaman Kontak</a>");
            Alert::success('Sukses membuat deposit','Sukses!');
            return redirect()->back();
        }

    }
    public function history(){
    	$deposit = Deposit::where('user_id', auth()->user()->id)->orderBy('id','desc')->paginate(10);
        
    	return view('deposit.history', compact('deposit'));
    } 

    public function cancel_deposit(Request $r) {
        $id = $r->id;
        
        $deposit = Deposit::find($id);
        $deposit->status = 'Canceled';
        $deposit->save();
        Alert::success('Sukses membatalkan deposit!', 'Sukses!');
        return redirect()->back();
    }



    public function get_method(Request $r) {
        $type = $r->type;
        $r->validate([
            'type' => 'required'
        ]);

        $method = Deposit_method::where('type',$type)->get();
        $res = "<option value=''>Pilih salah satu..</option>";
        foreach($method as $metode) {
            $id_method = $metode->id;
            $name = $metode->name;
            $res .= "<option value='$id_method'>$name</option>";
        }
        return $res;
    }

    public function get_rate(Request $r) {
        $method = $r->method;
        $select = Deposit_method::find($method);
        return $select->rate;
    }
}
