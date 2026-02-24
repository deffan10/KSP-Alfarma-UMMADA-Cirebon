<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Nasabah;
use Session;
use Exception;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;


class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('check');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        // Chart: total simpanan (wajib + sukarela) per bulan — 12 bulan berurutan, bulan tanpa data = 0
        $chartRows = \DB::table('chart')->get()->keyBy('Month');
        $monthLabels = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];
        $data['credit'] = [];
        $data['month'] = [];
        for ($m = 1; $m <= 12; $m++) {
            $data['month'][] = $monthLabels[$m - 1];
            $data['credit'][] = isset($chartRows[$m]) ? (float) $chartRows[$m]->total : 0;
        }
        $data['juser'] = User::all()->count();
        $data['jnasabah'] = Nasabah::all()->count();
        $data['kas'] = \DB::table('sisa_kas')->first();
        $data['tot_pinjam'] = \DB::table('tot_pinjam')->first();
        $data['profile'] = \DB::table('profiles')->where('status','active')->first();
        return view('Dashboard',$data);
    }

    public function operator()
    {
        $data['user'] = User::all();
        return view('Operator.Show',$data);
    }

    public function destroy($id)
    {
        //
        $user = User::find($id);
        $user->delete($id);
        return redirect('operator');
    }

    public function create(Request $request)
    {
        $this->validate($request,[
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],]);

            try
            {
                $data = array(
                    'name'=>$request->name,
                    'email'=>$request->email,
                    'password'=>Hash::make($request->password)
                );
                User::create($data);
                $pesan = "Success";
            }
            catch(Exception $exception)
            {
                $pesan = 'Database error!, ada duplikat data' . $exception->getCode();
            }
            Session::flash('pesan',$pesan);
            return redirect ('operator');           
        
    }

}
