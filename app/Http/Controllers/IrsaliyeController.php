<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use PhpOffice\PhpWord\PhpWord;
use PhpOffice\PhpWord\Shared\Converter;

class IrsaliyeController extends Controller{
  public function Index(){
    return view('irsaliye/irsaliye');
  }

  public function Liste(){
    return view('irsaliye/irsaliye');
  }

  public function GetListe(Request $request){
    $sirala = $request->query('sirala');
    $siralaYon = $request->query('siralaYon');
    $satirAdeti = $request->query('satirAdeti', 10);
    $sayfa = $request->query('sayfa', 1);
    $yillar = $request->query('yillar');

    $query = DB::table('irsaliye')
      ->select('musteri.unvan', 'irsaliye.*')
      ->leftJoin('musteri', 'musteri.id', '=', 'irsaliye.musteri_id');

    if (!is_null($yillar))
      $query->whereIn(DB::raw('YEAR(irs_tar)'), $yillar);
    
    if ($siralaYon == 'artan')
      $query->orderBy('irs_tar', 'ASC')->orderBy('irs_saat', 'ASC');
    else
      $query->orderBy('irs_tar', 'DESC')->orderBy('irs_saat', 'DESC');

    $irsaliye = $query->get()->toArray();

    $irsaliye = array_slice($irsaliye, ($sayfa - 1) * $satirAdeti, $satirAdeti);

    array_push($irsaliye,ceil(count($irsaliye) / $satirAdeti));

    return response()->json($irsaliye);
  }

  public function IrsaliyeSearch(Request $request){
    $search = $request->query('search');
    $siralaYon = $request->query('siralaYon');
    $yillar = $request->query('yillar');

    $query = DB::table('irsaliye')
      ->select('musteri.unvan', 'irsaliye.*')
      ->leftJoin('musteri', 'musteri.id', '=', 'irsaliye.musteri_id');

    if (!is_null($yillar))
      $query->whereIn(DB::raw('YEAR(irs_tar)'), $yillar);

    if (!empty($search)) {
      $query->where(function ($q) use ($search) {
        $q->orWhere('irs_no', 'like', "%{$search}%")
          ->orWhere('musteri.unvan', 'like', "%{$search}%");
      });
    }

    if ($siralaYon == 'artan')
      $query->orderBy('irs_tar', 'ASC')->orderBy('irs_saat', 'ASC');
    else
      $query->orderBy('irs_tar', 'DESC')->orderBy('irs_saat', 'DESC');
  
    $irsaliye = $query->get()->toArray();

    return response()->json($irsaliye);
  }

  public function Create(Request $request){
    if ($request->isMethod('post')) {
        $snArr = $request->input('sn', []);
        $mpsArr = $request->input('mps', []);
        $stokkodArr = $request->input('stokkod', []);
        $cinsiArr = $request->input('cinsi', []);
        $adediArr = $request->input('adedi', []);

        $kalemler = [
            'sn' => $snArr,
            'mps' => $mpsArr,
            'stokkod' => $stokkodArr,
            'cinsi' => $cinsiArr,
            'adedi' => $adediArr
        ];

        $irsaliyeArr = [
            'unvan' => $request->input('unvantext'),
            'adres' => $request->input('adres'),
            'il' => $request->input('il'),
            'ilce' => $request->input('ilce'),
            'vergidairesi' => $request->input('vd'),
            'vergino' => $request->input('vdno'),
            'irsno' => $request->input('irsno'),
            'irssaat' => $request->input('irssaat'),
            'irstar' => $request->input('irstar'),
            'kalemler' => $kalemler
        ];

        $cikisYapilanStoklar = DB::table('stok')
            ->leftJoin('stok_cikis', 'stok_cikis.stok_id', '=', 'stok.id')
            ->where('stok_cikis.cikis_tarih', date('Y-m-d'))
            ->where('stok_cikis.yazildi', 0)
            ->get();

        if ($cikisYapilanStoklar->isNotEmpty() || !empty($irsaliyeArr["kalemler"]["cinsi"])) {
            $irsaliyeDb = [
                'irs_no' => $request->input('irsno'),
                'irs_tar' => date('Y-m-d', strtotime($request->input('irstar'))),
                'irs_saat' => $request->input('irssaat'),
                'musteri_id' => $request->input('unvan')
            ];

            $irs_id = DB::table('irsaliye')->insertGetId($irsaliyeDb);

            foreach ($cikisYapilanStoklar as $stoklar) {
                DB::table('kalemler')->insert([
                    'stok_id' => $stoklar->stok_id,
                    'irs_id' => $irs_id
                ]);

                DB::table('stok_cikis')
                    ->where('stok_id', $stoklar->stok_id)
                    ->update(['yazildi' => 1]);
            }

            for ($i = 0; $i < count($kalemler["cinsi"]); $i++) {
                $stokArr = [
                    'sn' => $kalemler["sn"][$i],
                    'mps' => $kalemler["mps"][$i],
                    'stokkod' => $kalemler["stokkod"][$i],
                    'cinsi' => $kalemler["cinsi"][$i]
                ];

                $stokVar = DB::table('stok')->where($stokArr)->first();

                if (is_null($stokVar)) {
                    $stokDbArr = [
                        'sn' => $kalemler["sn"][$i],
                        'mps' => $kalemler["mps"][$i],
                        'stokkod' => $kalemler["stokkod"][$i],
                        'cinsi' => $kalemler["cinsi"][$i],
                        'adedi' => $kalemler["adedi"][$i],
                        'created_at' => now()
                    ];

                    $stokId = DB::table('stok')->insertGetId($stokDbArr);

                    DB::table('stok_cikis')->insert([
                        'cikis_tarih' => $stokDbArr["created_at"],
                        'stok_id' => $stokId,
                        'miktar' => $stokDbArr["adedi"]
                    ]);

                    DB::table('kalemler')->insert([
                        'stok_id' => $stokId,
                        'irs_id' => $irs_id
                    ]);
                } else {
                    $stokId = $stokVar->id;

                    $stokDbArr = [
                        'sn' => $kalemler["sn"][$i],
                        'mps' => $kalemler["mps"][$i],
                        'stokkod' => $kalemler["stokkod"][$i],
                        'cinsi' => $kalemler["cinsi"][$i],
                        'adedi' => $kalemler["adedi"][$i],
                        'created_at' => now()
                    ];

                    $cikisArr = [
                        'cikis_tarih' => $stokDbArr["created_at"],
                        'stok_id' => $stokId,
                        'miktar' => $stokDbArr["adedi"]
                    ];

                    $cikisVar = DB::table('stok_cikis')->where($cikisArr)->first();

                    if (is_null($cikisVar)) {
                        DB::table('stok_cikis')->insert($cikisArr);
                    }

                    $kalemVar = DB::table('kalemler')->where([
                        'stok_id' => $stokId,
                        'irs_id' => $irs_id
                    ])->first();

                    if (is_null($kalemVar)) {
                        DB::table('kalemler')->insert([
                            'stok_id' => $stokId,
                            'irs_id' => $irs_id
                        ]);
                    }
                }
            }

            $this->createDocument($irsaliyeArr);

        } else {
            Session::flash('msgerror', 'İrsaliye\'ye herhangi bir ürün girişi yapılmadı.');
            return Redirect::back();
        }

    } else {
        $musteriler = DB::table('musteri')->get();
        $cikisYapilanStoklar = DB::table('stok')
            ->leftJoin('stok_cikis', 'stok_cikis.stok_id', '=', 'stok.id')
            ->where('stok_cikis.cikis_tarih', date('Y-m-d'))
            ->where('stok_cikis.yazildi', 0)
            ->get();

        return view('layout', [
            'page' => 'irsaliye.irsaliyeolustur',
            'musteriler' => $musteriler,
            'stoklar' => $cikisYapilanStoklar
        ]);
    }
  }

  public function createDocument($data){
    $phpWord = new PhpWord();
    $section = $phpWord->addSection();

    // Sayfa kenar boşlukları
    $sectionStyle = $section->getStyle();
    $sectionStyle->setMarginLeft(Converter::cmToTwip(1.0));
    $sectionStyle->setMarginRight(Converter::cmToTwip(1.5));
    $sectionStyle->setMarginTop(Converter::cmToTwip(2));
    $sectionStyle->setMarginBottom(Converter::cmToTwip(2));

    // Belgeye tablo ekleme
    $tableStyle = ['cellMargin' => 5, 'cellSpacing' => Converter::cmToTwip(0.1)];
    $table = $section->addTable($tableStyle);

    // Satırlar ve hücreler ekleme
    $table->addRow(Converter::cmToTwip(0.9), ['cantSplit' => true, 'exactHeight' => true]);
    $table->addCell(Converter::cmToTwip(2));
    $table->addCell(Converter::cmToTwip(6))->addText($data["unvan"], ['name' => 'Arial', 'size' => 8]);
    $table->addCell(Converter::cmToTwip(11));

    // Diğer tablo hücreleri (örnek)
    $table->addRow(Converter::cmToTwip(1), ['cantSplit' => true, 'exactHeight' => true]);
    $table->addCell(Converter::cmToTwip(2));
    $table->addCell(Converter::cmToTwip(6))->addText($data["adres"], ['name' => 'Arial', 'size' => 8]);
    $table->addCell(Converter::cmToTwip(11));

    // Kalemler tabloyu dolduruyor
    $table = $section->addTable($tableStyle);
    foreach ($data["kalemler"]["cinsi"] as $i => $cinsi) {
        $table->addRow(Converter::cmToTwip(0.4), ['cantSplit' => true, 'exactHeight' => true]);
        $table->addCell(Converter::cmToTwip(1));
        $snMps = (!empty($data["kalemler"]["sn"][$i]) ? "SN." . $data["kalemler"]["sn"][$i] : "") . 
                 (!empty($data["kalemler"]["mps"][$i]) ? "/ MPS." . $data["kalemler"]["mps"][$i] : "");
        $table->addCell(Converter::cmToTwip(4))->addText($snMps, ['name' => 'Arial', 'size' => 8]);
        $table->addCell(Converter::cmToTwip(2))->addText($data["kalemler"]["stokkod"][$i] ?? '', ['name' => 'Arial', 'size' => 8]);
        $table->addCell(Converter::cmToTwip(9))->addText($cinsi, ['name' => 'Arial', 'size' => 8]);
        $table->addCell(Converter::cmToTwip(3))->addText($data["kalemler"]["adedi"][$i], ['name' => 'Arial', 'size' => 8]);
    }

    // Dosya oluşturma
    $file = $data["irsno"] . '.docx';
    try {
        $xmlWriter = \PhpOffice\PhpWord\IOFactory::createWriter($phpWord, 'Word2007');
        $xmlWriter->save($file);

        return response()->download($file)->deleteFileAfterSend(true);
    } catch (\Exception $e) {
        Session::flash('msgerror', 'İrsaliye Oluşturulamadı');
        return Redirect::back();
    }
  }

  public function Detay(Request $request){
    $irsaliyeId = $request->query('id');

    $irsaliyeInfo = DB::table('irsaliye')
        ->where('id', $irsaliyeId)
        ->first();

    $kalemInfo = DB::table('kalemler')
        ->where('irs_id', $irsaliyeId)
        ->get();

    $stokInfo = [];
    $stokCikisInfo = [];

    foreach ($kalemInfo as $kalem) {
        $stok = DB::table('stok')
            ->where('id', $kalem->stok_id)
            ->get();

        if (!in_array($stok, $stokInfo)) {
            $stokInfo[] = $stok;

            foreach ($stok as $stokInf) {
                $stokCikis = DB::table('stok_cikis')
                    ->select('stok.id', 'stok_cikis.miktar')
                    ->join('stok', 'stok.id', '=', 'stok_cikis.stok_id')
                    ->where('stok.id', $stokInf->id)
                    ->whereDate('stok_cikis.cikis_tarih', $irsaliyeInfo->irs_tar)
                    ->get();

                $stokMiktar = [];

                foreach ($stokCikis as $cikisDetay) {
                    if (isset($stokMiktar[$cikisDetay->id])) {
                        $stokMiktar[$cikisDetay->id] = (string) ($stokMiktar[$cikisDetay->id] + $cikisDetay->miktar);
                    } else {
                        $stokMiktar[$cikisDetay->id] = $cikisDetay->miktar;
                    }
                }

                $stokCikisInfo[] = $stokMiktar;
            }
        }
    }

    $data = [
        'irsaliyeTar' => date_format(date_create($irsaliyeInfo->irs_tar), "d/m/Y"),
        'irsaliyeSaat' => $irsaliyeInfo->irs_saat,
        'irsaliyeNo' => $irsaliyeInfo->irs_no,
        'stokDetay' => $stokInfo,
        'stokCikisDetay' => $stokCikisInfo
    ];

    return response()->json($data);
  }
}
