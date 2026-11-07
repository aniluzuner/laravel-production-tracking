<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

class MusteriController extends Controller{
  public function Liste(){
    $musteriler = DB::table('musteri')->get()->toArray();

    $musteriSehirArray = [];

    foreach ($musteriler as $musteri) {
        $result = DB::table('sehir')
            ->join('ilce', 'ilce.ilce_sehirkey', '=', 'sehir.sehir_key')
            ->select('sehir.sehir_title', 'ilce.ilce_title')
            ->where('sehir.sehir_key', $musteri->il_id)
            ->where('ilce.ilce_id', $musteri->ilce_id)
            ->first();

        if ($result) {
            $musteriSehirArray[$musteri->id] = [
                'sehir' => $result->sehir_title,
                'ilce' => $result->ilce_title
            ];
        }
    }

    return view('musteri/musteri', [
      'musteriler' => $musteriler,
      'musterisehir' => $musteriSehirArray
    ]);
  }

  public function Create(Request $request){
    if ($request->isMethod('post')) {
      $data = [
        'unvan' => $request->input('unvan'),
        'vd' => $request->input('vd'),
        'vno' => $request->input('vdno'),
        'il_id' => $request->input('il'),
        'ilce_id' => $request->input('ilce'),
        'adres' => $request->input('adres')
      ];

      DB::table('musteri')->insert($data);

      Session::flash('msgsuccess', 'Müşteri Oluşturuldu');
      return Redirect::route('musteri.create');
    } else {
      $sehirler = DB::table('sehir')->get()->toArray();
      return view('musteri/musteriolustur', ['sehirler' => $sehirler]);
    }   
  }

  public function GetIlceler(Request $request){
    $ilceSehirKey = $request->input('id');

    $ilceDetay = DB::table('ilce')
      ->where('ilce_sehirkey', $ilceSehirKey)
      ->get();

    return response()->json($ilceDetay);
  }

  public function GetDetay(Request $request){
    $id = $request->input('id');

    $musteriDetay = DB::table('musteri')
      ->where('id', $id)
      ->first();

    return response()->json($musteriDetay);
  }

  public function GetIlIlce(Request $request){
    $ilId = $request->input('il_id');
    $ilceId = $request->input('ilce_id');

    $ilIlce = DB::table('sehir')
      ->join('ilce', 'ilce.ilce_sehirkey', '=', 'sehir.sehir_key')
      ->select('sehir.sehir_title', 'ilce.ilce_title')
      ->where('sehir.sehir_key', $ilId)
      ->where('ilce.ilce_id', $ilceId)
      ->get();

    return response()->json($ilIlce);
  }
}
