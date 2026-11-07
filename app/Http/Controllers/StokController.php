<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

class StokController extends Controller{
  public function Create(Request $request){
    $data = [
      'sn' => $request->input('sn'),
      'mps' => $request->input('mps'),
      'stokkod' => $request->input('stokkod'),
      'cinsi' => $request->input('cinsi'),
      'adedi' => $request->input('adedi'),
      'created_at' => now()->format('Y-m-d')
    ];

    DB::table('stok')->insert($data);
    Session::flash('msgsuccess', 'Stok Oluşturuldu');

    return redirect()->route('stok.liste');
  }

  public function Update(Request $request){
    $stok_id = $request->input('stok_id');
    $data = $request->only(['sn', 'mps', 'stokkod', 'cinsi', 'adedi']);

    $updated = DB::table('stok')
      ->where('id', $stok_id)
      ->update($data);

    if ($updated)
      return response()->json(['message' => 'Stok başarıyla güncellendi'], 200);
    else 
      return response()->json(['error' => 'Güncelleme başarısız'], 500);
  }

  public function Edit(Request $request, $stok_id){
    if ($request->isMethod('post')) {
        $data = $request->only(['sn', 'mps', 'stokkod', 'cinsi', 'adedi']);

        DB::table('stok')
          ->where('id', $stok_id)
          ->update($data);

        Session::flash('msgsuccess', 'Stok Düzenlendi');

        return redirect()->route('stok.liste');
    } else {
        $stok = DB::table('stok')
          ->where('id', $stok_id)
          ->first();

        $stokCikis = DB::table('stok_cikis')
          ->where('stok_id', $stok_id)
          ->get();

        $stok->kalan = $stok->adedi;
        foreach ($stokCikis as $cikisMiktar) {
          $stok->kalan -= $cikisMiktar->miktar;
        }

        return view('stok/stokolustur', ['stok' => $stok, 'id' => $stok_id]);
    }
  }

  public function Cikis(Request $request){
    $data = [
      'cikis_tarih' => now()->format('Y-m-d'),
      'stok_id' => $request->input('stok_id_cikis'),
      'miktar' => $request->input('cikis_miktar'),
    ];

    $inserted = DB::table('stok_cikis')->insert($data);

    if ($inserted)
      return response()->json(['message' => 'Stok çıkışı başarıyla kaydedildi'], 201);
    else
      return response()->json(['error' => 'Kaydetme başarısız'], 500);
  }

  public function DeleteCikis(Request $request){
    $id = $request->input('id');

    $deleted = DB::table('stok_cikis')->where('id', $id)->delete();

    if ($deleted)
      return response()->json(['message' => 'Stok çıkışı başarıyla silindi'], 200);
    else
      return response()->json(['error' => 'Silme başarısız'], 500);
  }

  public function Liste(){
		return view('stok/liste');
	}

  public function GetListe(Request $request){
    $sirala = $request->query('sirala');
    $siralaYon = $request->query('siralaYon');
    $satirAdeti = $request->query('satirAdeti', 10);
    $sayfa = $request->query('sayfa', 1);
    $yillar = $request->query('yillar');

    $stokCikis = DB::table('stok_cikis')
      ->select('stok.id', 'stok_cikis.miktar', 'stok_cikis.cikis_tarih')
      ->join('stok', 'stok.id', '=', 'stok_cikis.stok_id')
      ->get()
      ->groupBy('id')
      ->map(function ($group) {
        return $group->sum('miktar');
      })
      ->toArray();

    $query = DB::table('stok')
      ->select('*')
      ->where('hidden', '!=', '1');

    if (is_null($yillar))
      $query->where('created_at', '>', '2022-12-31');
    else
      $query->whereIn(DB::raw('YEAR(created_at)'), $yillar);

    $stok = $query->get()->toArray();

    foreach ($stok as $key => $value) {
      $id = $value->id;
      $stok[$key]->kalan = isset($stokCikis[$id]) ? $value->adedi - $stokCikis[$id] : $value->adedi;
    }

    if ($sirala && $siralaYon) {
      usort($stok, function ($a, $b) use ($sirala, $siralaYon) {
        if ($siralaYon === 'artan')
          return $a->$sirala <=> $b->$sirala;
        else
          return $b->$sirala <=> $a->$sirala;    
      });
    }

    $stok = array_slice($stok, ($sayfa - 1) * $satirAdeti, $satirAdeti);

    array_push($stok,ceil(count($stok) / $satirAdeti));

    return response()->json($stok);
  }

  public function Arsiv(){
		return view('stok/arsiv');
	}

  public function GetArsiv(Request $request){
    $sirala = $request->query('sirala');
    $siralaYon = $request->query('siralaYon');
    $satirAdeti = $request->query('satirAdeti', 10);
    $sayfa = $request->query('sayfa', 1);
    $yillar = $request->query('yillar');

    $stokCikis = DB::table('stok_cikis')
      ->select('stok.id', 'stok_cikis.miktar', 'stok_cikis.cikis_tarih')
      ->join('stok', 'stok.id', '=', 'stok_cikis.stok_id')
      ->get()
      ->groupBy('id')
      ->map(function ($group) {
        return $group->sum('miktar');
      })
      ->toArray();

    $query = DB::table('stok')
      ->select('*');

    if (is_null($yillar))
      $query->where('created_at', '<', '2023-01-01');
    else
      $query->whereIn(DB::raw('YEAR(created_at)'), $yillar);
    
    $stok = $query->get()->toArray();

    foreach ($stok as $key => $value) {
      $id = $value->id;
      $stok[$key]->kalan = isset($stokCikis[$id]) ? $value->adedi - $stokCikis[$id] : $value->adedi;
    }

    if ($sirala && $siralaYon) {
      usort($stok, function ($a, $b) use ($sirala, $siralaYon) {
        if ($siralaYon === 'artan')
          return $a->$sirala <=> $b->$sirala;
        else
          return $b->$sirala <=> $a->$sirala;          
      });
    }

    $stok = array_slice($stok, ($sayfa - 1) * $satirAdeti, $satirAdeti);

    array_push($stok,ceil(count($stok) / $satirAdeti));

    return response()->json($stok);
  }

  public function Istatistik(){
		return view('stok/istatistik');
	}

  public function StokCikis(){
    $stokCikis = DB::table('stok_cikis')
      ->select('*')
      ->join('stok', 'stok.id', '=', 'stok_cikis.stok_id')
      ->get()
      ->toArray();

    return response('<pre>' . htmlspecialchars(print_r($stokCikis, true)) . '</pre>');
  }

  public function EnFazlaCikis(Request $request){
    $dateStart = $request->query('dateStart');
    $dateEnd = $request->query('dateEnd');

    $stokCikis = DB::table('stok_cikis')
      ->select('stok.stokkod', 'stok.cinsi', DB::raw('count(*) as kac_defa'), DB::raw('min(stok_cikis.cikis_tarih) as min_tarih'), DB::raw('max(stok_cikis.cikis_tarih) as max_tarih'))
      ->join('stok', 'stok.id', '=', 'stok_cikis.stok_id')
      ->where('stok.stokkod', '!=', '')
      ->when($dateStart, function ($query, $dateStart) {
        return $query->where('cikis_tarih', '>=', $dateStart);
      })
      ->when($dateEnd, function ($query, $dateEnd) {
        return $query->where('cikis_tarih', '<=', $dateEnd);
      })
      ->groupBy('stok.stokkod')
      ->orderByRaw('count(*) DESC')
      ->limit(1)
      ->get()
      ->toArray();

    return response()->json($stokCikis);
  }

  public function EnFazlaMiktarCikis(Request $request){
    $dateStart = $request->query('dateStart');
    $dateEnd = $request->query('dateEnd');

    $stokCikis = DB::table('stok_cikis')
      ->select('stok.stokkod', 'stok.cinsi', DB::raw('sum(miktar) as toplam_miktar'))
      ->join('stok', 'stok.id', '=', 'stok_cikis.stok_id')
      ->where('stok.stokkod', '!=', '')
      ->when($dateStart, function ($query, $dateStart) {
        return $query->where('cikis_tarih', '>=', $dateStart);
      })
      ->when($dateEnd, function ($query, $dateEnd) {
        return $query->where('cikis_tarih', '<=', $dateEnd);
      })
      ->groupBy('stok.stokkod')
      ->orderByRaw('sum(miktar) DESC')
      ->limit(1)
      ->get()
      ->toArray();

    return response()->json($stokCikis);
  }

  public function CikisYapilmayanStoklar(Request $request){
    $dateStart = $request->query('dateStart');
    $dateEnd = $request->query('dateEnd');
    $sirala = $request->query('sirala');
    $siralaYon = $request->query('siralaYon');
    $satirAdeti = $request->query('satirAdeti', 10);
    $sayfa = $request->query('sayfa', 1);

    $rowCount = DB::table('stok')
      ->where('hidden', '!=', 1)
      ->when($dateStart, function ($query, $dateStart) {
        return $query->where('created_at', '>=', $dateStart);
      })
      ->when($dateEnd, function ($query, $dateEnd) {
        return $query->where('created_at', '<=', $dateEnd);
      })
      ->whereNotExists(function ($query) {
        $query->select(DB::raw('1'))
          ->from('stok_cikis')
          ->whereRaw('stok_cikis.stok_id = stok.id');
      })
      ->count();

    $stoklar = DB::table('stok')
      ->select('*', DB::raw('DATEDIFF(CURDATE(), created_at) AS gecen_gun'))
      ->where('hidden', '!=', 1)
      ->when($dateStart, function ($query, $dateStart) {
        return $query->where('created_at', '>=', $dateStart);
      })
      ->when($dateEnd, function ($query, $dateEnd) {
        return $query->where('created_at', '<=', $dateEnd);
      })
      ->whereNotExists(function ($query) {
        $query->select(DB::raw('1'))
          ->from('stok_cikis')
          ->whereRaw('stok_cikis.stok_id = stok.id');
      })
      ->when($sirala, function ($query) use ($sirala, $siralaYon) {
        $query->orderBy($sirala, $siralaYon === 'artan' ? 'asc' : 'desc');
      })
      ->limit($satirAdeti)
      ->offset(($sayfa - 1) * $satirAdeti)
      ->get()
      ->toArray();

    array_push($stoklar, ceil($rowCount[0]['rowCount'] / $satirAdeti));

    return response()->json($stoklar);
  }

  public function SifirlanmayanStoklar(Request $request){
    $sirala = $request->query('sirala');
    $siralaYon = $request->query('siralaYon');

    $stoklar = DB::table('stok as s')
      ->select('s.*', DB::raw('IFNULL(SUM(sc.miktar), 0) AS toplam_cikis'), DB::raw('(s.adedi - IFNULL(SUM(sc.miktar), 0)) AS kalan_adet'))
      ->leftJoin('stok_cikis as sc', 's.id', '=', 'sc.stok_id')
      ->where('sc.cikis_tarih', '>=', '2024-05-20')
      ->where('s.hidden', '!=', 1)
      ->whereDate('s.created_at', '<=', now()->subDays(30)->format('Y-m-d'))
      ->whereDate('s.created_at', '>=', '2024-05-20')
      ->groupBy('s.id', 's.adedi')
      ->having('kalan_adet', '>', 0)
      ->when($sirala, function ($query) use ($sirala, $siralaYon) {
        $query->orderBy($sirala, $siralaYon === 'artan' ? 'asc' : 'desc');
      })
      ->get()
      ->toArray();

    return response()->json($stoklar);
  }

  public function StokSearch(Request $request){
    $listeMi = $request->query('listeMi');
    $search = $request->query('search');
    $sirala = $request->query('sirala');
    $siralaYon = $request->query('siralaYon');
    $yillar = $request->query('yillar');

    $stokCikis = DB::table('stok_cikis')
      ->select('stok.id', 'stok_cikis.miktar as miktar', 'stok_cikis.cikis_tarih')
      ->join('stok', 'stok.id', '=', 'stok_cikis.stok_id')
      ->get()
      ->groupBy('id')
      ->map(function ($item) {
        return $item->sum('miktar');
      })
      ->toArray();

    $stokQuery = DB::table('stok');

    if ($listeMi === "true") {
      if (is_null($yillar))
        $stokQuery->where('created_at', '>', '2022-12-31');
      else
        $stokQuery->whereIn(DB::raw('YEAR(created_at)'), $yillar);
      
      $stokQuery->where('hidden', '!=', '1');
    }

    if (!is_null($yillar))
      $stokQuery->whereIn(DB::raw('YEAR(created_at)'), $yillar);

    $stokQuery->where(function ($query) use ($search) {
      $query->where('sn', 'like', "%{$search}%")
            ->orWhere('mps', 'like', "%{$search}%")
            ->orWhere('stokkod', 'like', "%{$search}%")
            ->orWhere('cinsi', 'like', "%{$search}%");
    });

    $stok = $stokQuery->get()->toArray();

    foreach ($stok as $key => $value) {
        if (isset($stokCikis[$value->id]))
          $stok[$key]->kalan = $value->adedi - $stokCikis[$value->id];
        else
          $stok[$key]->kalan = $value->adedi;
    }

    if ($sirala) {
      usort($stok, function ($a, $b) use ($sirala, $siralaYon) {
        if ($siralaYon === 'artan')
          return $a->$sirala <=> $b->$sirala;
        else
          return $b->$sirala <=> $a->$sirala;
      });
    }

    return response()->json($stok);
  }

  public function GetCins(Request $request){
    $stokkod = $request->query('stokkod');

    $cins = DB::table('stok')
      ->select('cinsi')
      ->where('stokkod', $stokkod)
      ->orderBy('created_at', 'desc')
      ->first();

    return response()->json($cins);
  }

  public function GetKalan(Request $request){
    $stok_id = $request->query('stok_id');

    $stok = DB::table('stok')
      ->select('adedi')
      ->where('id', $stok_id)
      ->first();

    if (!$stok)
      return response()->json(['error' => 'Stok bulunamadı'], 404);
    
    $stokCikis = DB::table('stok_cikis')
      ->select('miktar')
      ->where('stok_id', $stok_id)
      ->get();

    $kalan = $stok->adedi;
    foreach ($stokCikis as $cikisMiktar) {
        $kalan -= $cikisMiktar->miktar;
    }

    return response()->json($kalan);
  }

  public function GetDetay(Request $request){
    $stok_id = $request->input('id');

    $stokDetay = DB::table('stok_cikis')
      ->where('stok_id', $stok_id)
      ->orderBy('cikis_tarih', 'desc')
      ->get();

    return response()->json($stokDetay);
  }

  public function Not(Request $request){
    $stok_id = $request->input('stok_id');
    $stok_not = $request->input('stok_notu');

    $updated = DB::table('stok')
      ->where('id', $stok_id)
      ->update(['stok_not' => $stok_not]);

    return response()->json(['message' => 'Stok notu güncellendi'], 200);  
  }

  public function GetNot(Request $request){
    $id = $request->input('id');

    $stokDetay = DB::table('stok')
      ->select('stok_not')
      ->where('id', $id)
      ->first();

    return response()->json($stokDetay);
  }

  public function GetInfo(Request $request){
    $id = $request->input('id');

    $stokInfo = DB::table('stok')
      ->where('id', $id)
      ->first();

    return response()->json($stokInfo);
  }

  public function Listele(){
    return view('stok/misafir');
  }

  public function Hide(Request $request){
    $stokId = $request->input('id');

    DB::table('stok')
      ->where('id', $stokId)
      ->update(['hidden' => 1]);

    return response()->json(['success' => true]);
  }

  public function ShowStok(Request $request){
    $stokId = $request->input('id');

    DB::table('stok')
      ->where('id', $stokId)
      ->update(['hidden' => 0]);

    return response()->json(['success' => true]);
  }
}
