<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;
use App\Models\Production;
use App\Models\User;
use App\Models\ProductionStep;
use App\Models\ProductionRecord;
use Yajra\DataTables\DataTables;
use Carbon\Carbon;
use App\Notifications\RecordsNotification;

class ProductionController extends Controller{
  public function Productions(){
    return view('Production/productions');
  }

  public function GetProductions(){
    $productions = Production::all();
    
    foreach ($productions as $production) {
      $production->job_count = ProductionRecord::join('production_steps', 'production_records.step_id', '=', 'production_steps.id')
        ->where('status', '!=', 'Onaylandı')
        ->where('production_steps.production_id', $production->id)
        ->distinct('production_records.mps')
        ->count('production_records.mps');
    }

    return DataTables::of($productions)->make(true);
  }

  public function AddProduction(Request $request){
    $validatedData = $request->validate([
      'title' => 'required|string|max:255',
      'stock_code' => 'nullable|string',
      'steps' => 'required|array',
      'steps.*.step_title' => 'required|string|max:255',
      'steps.*.step_time' => 'nullable|numeric',
    ]);

    $production = Production::create([
        'title' => $validatedData['title'],
        'stock_code' => $validatedData['stock_code']
    ]);

    foreach ($validatedData['steps'] as $step) {
      $this->AddProductionStep($production->id, $step);
    }

    return redirect()->route('uretim')->with('success', 'Üretim ve adımlar başarıyla kaydedildi.');
  }

  public function AddProductionStep($id, $step){
    ProductionStep::create([
      'production_id' => $id,
      'title' => $step['step_title'],
      'time' => $step['step_time'],
    ]);
  }

  public function GetProduction($productionId){
    $production = Production::with('steps')->find($productionId);

    if (!$production)
      return response()->json(['error' => 'Production not found.'], 404);
        
    return response()->json($production);
  }

  public function Update(Request $request){
    $validatedData = $request->validate([
      'id' => 'required|integer',
      'title' => 'required|string|max:255',
      'stock_code' => 'nullable|string',
      'steps' => 'required|array',
      'steps.*.step_title' => 'required|string|max:255',
      'steps.*.step_time' => 'nullable|numeric',
    ]);

    $production = Production::find($validatedData['id']);
    if (!$production) {
        return response()->json(['error' => 'Production not found.'], 404);
    }

    $production->update([
        'title' => $validatedData['title'],
        'stock_code' => $validatedData['stock_code']
    ]);

    ProductionStep::where('production_id', $validatedData['id'])->delete();

    foreach ($validatedData['steps'] as $step) {
        ProductionStep::create([
            'production_id' => $validatedData['id'],
            'title' => $step['step_title'],
            'time' => $step['step_time']
        ]);
    }

    return redirect()->route('uretim')->with('success', 'Üretim ve adımlar başarıyla güncellendi.');
  }

  public function Delete($productionId){
    $production = Production::find($productionId);
    if (!$production)
      return response()->json(['error' => 'Production not found.'], 404);

    //$production->steps()->delete();
    $production->delete();

    return response()->json(['success' => 'Üretim başarıyla silindi.']);
  }

  public function AddTask(Request $request){
    $data = [
      'mps' => $request->input('mps'),
      'quantity' => $request->input('quantity'),
      'controller_id' => $request->input('controller_id'),
      'production_id' => $request->input('production_id')
    ];

    DB::table('tasks')->insert($data);

    return redirect()->route('GetRecordsByUser', ['id' => Auth::user()->id]);
  }

  public function GetTaskById($id){
    $task = DB::table('tasks')->where('id', $id)->first();

    if (!$task) 
      return response()->json(['message' => 'Task not found'], 404);

    $production = Production::with('steps')->find($task->production_id);
    
    $task->production = $production;

    return response()->json($task);
  }

  public function DeleteTask($id){
    $task = DB::table('tasks')->where('id', $id)->delete();

    if ($task) 
      return response()->json(['message' => 'Kayıtlar başarıyla silindi.'], 200);
    else 
      return response()->json(['message' => 'Kayıt bulunamadı.'], 404);
  }

  public function AddProductionRecord(Request $request){
    $mps = $request->input('mps');
    $quantity = $request->input('quantity');
    $controller_id = $request->input('controller_id');
    $taskSteps = $request->input('taskStep');
    
    foreach ($taskSteps as $taskStep) {
      $step_id = $taskStep['step_id'];
      $employees = $taskStep['employees'];
      $description = $taskStep['description'];

      foreach ($employees as $employeeFullName) {
        $user = User::where(DB::raw('LOWER(fullname)'), strtolower($employeeFullName))->first();
  
        if (!$user) {
          $username = str_replace(' ', '', strtolower($employeeFullName));
          $password = $username;
  
          $user = new User();
          $user->fullname = $employeeFullName;
          $user->username = $username;
          $user->password = $password;
          $user->role = "Çalışan";
          $user->save();
        }
      }

      $productionRecord = new ProductionRecord();
      $productionRecord->mps = $mps;
      $productionRecord->step_id = $step_id;
      $productionRecord->quantity = $quantity;
      $productionRecord->controller_id = $controller_id;
      $productionRecord->employees = implode(', ', $employees);
      $productionRecord->status = "Devam Ediyor";
      $productionRecord->start = now();
      $productionRecord->description = $description;
      $productionRecord->save();
    }

    return redirect()->route('GetRecordsByUser', ['id' => $controller_id])->with('success', 'Görev başarıyla atandı.');
  }

  public function GetProductionRecords(){
    $records = ProductionRecord::selectRaw(
        'MAX(production_records.id) as id, 
         production_records.mps, 
         MAX(production_records.quantity) as quantity, 
         CASE 
            WHEN SUM(CASE WHEN production_records.status = "Başlamadı" THEN 1 ELSE 0 END) = COUNT(*) THEN "Başlamadı"
            WHEN SUM(CASE WHEN production_records.status = "Bitirildi" THEN 1 ELSE 0 END) = COUNT(*) THEN "Bitirildi"
            WHEN SUM(CASE WHEN production_records.status = "Onaylandı" THEN 1 ELSE 0 END) = COUNT(*) THEN "Onaylandı"
            ELSE "Devam Ediyor"
         END as status, 
         productions.title, 
         productions.stock_code, 
         users.fullname'
    )
    ->join('production_steps', 'production_records.step_id', '=', 'production_steps.id')
    ->join('productions', 'production_steps.production_id', '=', 'productions.id')
    ->join('users', 'production_records.controller_id', '=', 'users.id')
    ->groupBy('production_records.mps', 'productions.title', 'productions.stock_code', 'users.fullname')
    ->orderBy('production_records.created_at', 'desc')
    ->get();
    
    return view('production/tasks', compact('records'));
  }

  public function GetRecordsByMps($mps){
    $records = ProductionRecord::where('mps', $mps)
      ->with(['step.production', 'controller'])
      ->get();

    return response()->json($records);
  }

  public function GetRecordsByUser($id){
    $tasks = DB::table('tasks')
      ->where('controller_id', $id)
      ->orderBy('created_at', 'desc')
      ->get();

    $taskDetails = [];

    foreach ($tasks as $task) {
      $production = Production::find($task->production_id);
      
      $productionRecords = ProductionRecord::where('mps', $task->mps)
        ->with(['step.production'])
        ->get();

      $taskDetails[] = [
        'task' => $task,
        'production' => $production,
        'productionRecords' => $productionRecords
      ];
    }

    return view('production/tasksById', compact('taskDetails'));

    // if ($user->role != "Çalışan"){

    //   $records = ProductionRecord::where('controller_id', $id)
    //     ->with(['step.production'])
    //     ->orderBy('created_at', 'desc')
    //     ->get();

    //   $groupedRecords = $records->groupBy('mps');
    // }
    // else{
    //   $records = ProductionRecord::where('employees', 'like', '%'. $user->fullname .'%')
    //     ->with(['step.production', 'controller'])
    //     ->orderBy('created_at', 'desc')
    //     ->get();
    //   $groupedRecords = $records->groupBy('mps');
    // }
      
    // return view('production/tasksById', compact('groupedRecords'));
  }

  public function DeleteRecords($mps){
    $records = ProductionRecord::where('mps', $mps)->delete();
    $task = DB::table('tasks')->where('mps', $mps)->delete();

    if ($records && $task) 
      return response()->json(['message' => 'Kayıtlar başarıyla silindi.'], 200);
    else 
      return response()->json(['message' => 'Kayıtlar silinemedi.'], 404);
  }

  public function StartTask($id){
    $record = ProductionRecord::find($id);

    if ($record->start == null)
      $record->update(['status' => "Devam Ediyor", 'start' => now()]);
    else {
      $record->update(['status' => "Devam Ediyor"]);
      DB::table('pauses')->where('record_id', $id)->where('end', null)->update(['end' => now()]);
    }

    $controllers = User::where('role', 'Kontrolcü')->get();

    foreach ($controllers as $controller) {
      $stepTitle = $record->step->title;

      $controller->notify(new RecordsNotification($record->mps, $record->step->production->title, "Başlatıldı", "$stepTitle adımı başlatıldı."));
    }

    return redirect()->route('GetRecordsByUser', ['id' => Auth::user()->id]);
  }

  public function PauseTask($id){
    $record = ProductionRecord::find($id);

    $elapsedTime = $record->elapsed_time + abs(Carbon::now()->diffInSeconds(Carbon::parse($record->updated_at)));

    $record->update(['status' => "Duraklatıldı", 'elapsed_time' => $elapsedTime]);

    DB::table('pauses')->insert(['record_id' => $id, 'start' => now()]);

    $controllers = User::where('role', 'Kontrolcü')->get();

    foreach ($controllers as $controller) {
      $stepTitle = $record->step->title;

      $controller->notify(new RecordsNotification($record->mps, $record->step->production->title, "Duraklatıldı", "$stepTitle adımı duraklatıldı."));
    }

    return redirect()->route('GetRecordsByUser', ['id' => Auth::user()->id]);
  }

  public function EndTask($id){
    $record = ProductionRecord::find($id);

    $elapsedTime = $record->elapsed_time + abs(Carbon::now()->diffInSeconds(Carbon::parse($record->updated_at)));

    $record->update(['status' => "Bitirildi", 'start' => now(), 'elapsed_time' => $elapsedTime]);

    DB::table('pauses')->where('record_id', $id)->where('end', null)->update(['end' => now()]);

    $controllers = User::where('role', 'Kontrolcü')->get();

    foreach ($controllers as $controller) {
      $stepTitle = $record->step->title;

      $controller->notify(new RecordsNotification($record->mps, $record->step->production->title, "Bitirildi", "$stepTitle adımı bitirildi."));
    }

    return redirect()->route('GetRecordsByUser', ['id' => Auth::user()->id]);
  }

  public function AutoPause(){
    $records = ProductionRecord::where('status', 'Devam Ediyor')->get();
    
    if ($records->isEmpty())
      return response()->json(['message' => 'No records with status "Devam Ediyor" found.'], 404);
    
    foreach ($records as $record) {
      $record->status = 'Otomatik Duraklatıldı';
      $record->elapsed_time = $record->elapsed_time + abs(Carbon::now()->diffInSeconds(Carbon::parse($record->updated_at)));
      $record->save();
    }

    return response()->json(['message' => 'All records have been updated to "Otomatik Duraklatıldı".']);
  }

  public function AutoStart(){
    $records = ProductionRecord::where('status', 'Otomatik Duraklatıldı')->get();

    if ($records->isEmpty())
      return response()->json(['message' => 'No records with status "Otomatik Duraklatıldı" found.'], 404);
    
    foreach ($records as $record) {
      $record->status = 'Devam Ediyor';
      $record->save();
    }

    return response()->json(['message' => 'All records have been updated to "Devam Ediyor".']);
  }

  public function DeleteTaskStep($id){
    $record = ProductionRecord::find($id);

    $record->delete();

    return response()->json(['message' => 'Adım başarıyla silindi.'], 200);
  }

  public function AddEmployees(Request $request){
    $id = $request->input('id');
    $newEmployees = $request->input('employees');

    foreach ($newEmployees as $employeeFullName) {
      $user = User::where(DB::raw('LOWER(fullname)'), strtolower($employeeFullName))->first();

      if (!$user) {
        $username = str_replace(' ', '', strtolower($employeeFullName));
        $password = $username;

        $user = new User();
        $user->fullname = $employeeFullName;
        $user->username = $username;
        $user->password = $password;
        $user->role = "Çalışan";
        $user->save();
      }
    }

    $record = ProductionRecord::find($id);

    $currentEmployees = $record->employees;

    $employeeArray = explode(', ', $currentEmployees);

    foreach ($newEmployees as $newEmployee){
      if (!in_array($newEmployee, $employeeArray))
        $employeeArray[] = $newEmployee;
    }
    
    $updatedEmployees = implode(', ', $employeeArray);
    
    $record->employees = $updatedEmployees;
    $record->save();

    return redirect()->route('GetRecordsByUser', ['id' => Auth::user()->id]);
  }

  public function RemoveEmployee(Request $request){
    $id = $request->input('id');
    $employeeToRemove = $request->input('employee');

    $record = ProductionRecord::find($id);

    $currentEmployees = $record->employees;

    $employeeArray = explode(', ', $currentEmployees);

    if (in_array($employeeToRemove, $employeeArray)) {
      $employeeArray = array_filter($employeeArray, function($employee) use ($employeeToRemove) {
        return $employee !== $employeeToRemove;
      });
    }

    $updatedEmployees = implode(', ', $employeeArray);

    $record->employees = $updatedEmployees;
    $record->save();

    return response()->json(['message' => 'Çalışan başarıyla çıkartıldı', 'employees' => $updatedEmployees]);
  }

  public function ApproveCompletedRecords($mps){
    $records = ProductionRecord::where('mps', $mps)->get();

    $approved = 1;
    foreach ($records as $record) {
      if ($record->status = "Bitirildi")
        $record->update(['status' => 'Onaylandı']);

      if ($record->status != "Onaylandı")
        $approved = 0;
    }

    if ($approved == 1){
      $inspectors = User::where('role', 'Denetleyici')->get();

      foreach ($inspectors as $inspector) {
        $inspector->notify(new RecordsNotification($record->mps, $record->step->production->title, "Onaylandı", Auth::user()->fullname . " tarafından onaylandı ve verimlilik raporu oluşturuldu."));
      }
    }

    return response()->json(['message' => 'Adımlar başarıyla onaylandı.'], 200);
  }

  public function ApproveRecord($id){
    $record = ProductionRecord::find($id);
    $record->update(['status' => 'Onaylandı']);

    $records = ProductionRecord::where('mps', $record->mps)->get();

    $approved = 1;
    foreach ($records as $record) {
      if ($record->status != "Onaylandı")
        $approved = 0;
    }

    if ($approved == 1){
      $inspectors = User::where('role', 'Denetleyici')->get();

      foreach ($inspectors as $inspector) {
        $inspector->notify(new RecordsNotification($record->mps, $record->step->production->title, "Onaylandı", Auth::user()->fullname . " tarafından onaylandı ve verimlilik raporu oluşturuldu."));
      }
    }

    return response()->json(['message' => 'Adım başarıyla onaylandı.'], 200);
  }

  public function GetReports(){
    $records = ProductionRecord::with('step')
        ->orderBy('created_at', 'desc')
        ->get()
        ->groupBy('mps');

    $results = [];

    foreach ($records as $mps => $group) {
      $allApproved = true;

      foreach ($group as $record) {
        if ($record->status !== 'Onaylandı') {
          $allApproved = false;
          break;
        }
      }

      if ($allApproved) {
        foreach ($group as $record) {
          $stepTime = $record->step->time / count(explode(', ',$record->employees));
          $expectedTime = $record->quantity * $stepTime;
          $elapsedTime = $record->elapsed_time;

          if ($elapsedTime <= $expectedTime) 
            $efficiency = 100;
          elseif ($elapsedTime >= 1.5 * $expectedTime) 
              $efficiency = 0;
          else 
            $efficiency = 100 - ((($elapsedTime - $expectedTime) / ($expectedTime * 0.5)) * 100);
          
          if ($efficiency >= 80)
            $record->efficiencyColor = "#00FF00";
          elseif ($efficiency >= 60)
            $record->efficiencyColor = "#00FFFF";
          elseif ($efficiency >= 40)
            $record->efficiencyColor = "#FFFF00";
          elseif ($efficiency >= 20)
            $record->efficiencyColor = "#FF8400";
          else
            $record->efficiencyColor = "#FF0000";     
      
          $record->efficiencyPercentage = number_format($efficiency, 0);

          $pauses = DB::table('pauses')->where('record_id', $record->id)->get();

          $formattedPauses = [];
          $totalPause = 0;

          foreach ($pauses as $pause) {
            $minutesPaused = abs(Carbon::parse($pause->start)->diffInSeconds(Carbon::parse($pause->end)));
            $totalPause += $minutesPaused;

            $formattedStart = Carbon::parse($pause->start)->translatedFormat('d F H:i');;
            $formattedEnd = Carbon::parse($pause->end)->translatedFormat('d F H:i');;

            $formattedPauses[] = $formattedStart . ' - ' . $formattedEnd;
          }

          $record->pauses = $formattedPauses;
          $record->totalPause = $totalPause;
        }

        $results[] = $group;
      }
    }


    return view('production/reports', compact('results'));
  }

  public function Notifications(){
    $user = Auth::user();
    $notifications = $user->notifications;

    return view('notifications', compact('notifications'));
  }
}
