@extends('layouts.main')

@section('title', 'Yazaroğlu Elektronik')

@section('content')

<div class="content-wrapper">
  <div class="content-header">
    <div class="container-fluid">
      <div class="row mb-2">
        <div class="col-12">
          <h1 class="m-0 p-0" style="line-height: 1.75;">Raporlar</h1>
        </div><!-- /.col -->
      </div><!-- /.row -->
    </div><!-- /.container-fluid -->
  </div>
  <!-- /.content-header -->

  <!-- Main content -->
  <section class="content">
    <div class="container-fluid">
      <div class="row">
        <div class="col-md-12">
              @php
                function formatElapsedTime($seconds) {
                  $hours = floor($seconds / 3600);
                  $minutes = floor(($seconds % 3600) / 60);
                  $remainingSeconds = $seconds % 60;

                  $result = '';

                  if ($hours > 0)
                    $result .= "{$hours} saat ";
                    
                  if ($minutes > 0 || $hours > 0)
                    $result .= "{$minutes} dakika "; 

                  if ($remainingSeconds > 0)
                    $result .= "{$remainingSeconds} saniye";

                  return trim($result);
                }                
              @endphp

              @foreach ($results as $mps => $records)
                <table class="table table-hover bg-white rounded">
                  <tbody>
                    <tr data-widget="expandable-table" aria-expanded="false">
                      <td class="border-0 rounded text-lg">
                        <i class="expandable-table-caret fas fa-caret-right fa-fw"></i>
                        {{ $records[0]->step->production->title }}
                      </td>
                    </tr>
                    <tr class="expandable-body d-none">
                      <td>
                        <div>
                          <p><strong>MPS: </strong>{{ $records[0]->mps }}</p>
                          <p><strong>Üretim Başlığı: </strong>{{ $records[0]->step->production->title }}</p>
                          <p><strong>Stok Kodu: </strong>{{ $records[0]->step->production->stock_code }}</p>
                          <p><strong>Tarih: </strong>{{ $records[0]->created_at->isoFormat('D MMMM YYYY HH:mm') }}</p>
                          <p><strong>Adımlar: </strong></p>
                          @foreach ($records as $record)
                            <table class="table table-hover bg-white rounded">
                              <tbody>
                                <tr data-widget="expandable-table" aria-expanded="false">
                                  <td class="border-0 rounded text-lg">
                                    <i class="expandable-table-caret fas fa-caret-right fa-fw"></i>
                                    {{ $record->step->title }}
                                  </td>
                                </tr>
                                <tr class="expandable-body d-none">
                                  <td>
                                    <div>
                                      <p><strong>Adım: </strong>{{ $record->step->title }}</p>
                                      <p><strong>Adet: </strong>{{ $record->quantity }}</p>
                                      <p><strong>Çalışanlar: </strong>{{ $record->employees }}</p>
                                      @if ($record->description != null)
                                        <p><strong>Açıklama: </strong>{{ $record->description }}</p>
                                      @endif
                                      <p><strong>Başlangıç: </strong>{{ $record->created_at->isoFormat('D MMMM YYYY HH:mm') }}</p>
                                      <p><strong>Bitiş: </strong>{{ \Carbon\Carbon::parse($record->start)->isoFormat('D MMMM YYYY HH:mm') }}</p>
                                      
                                      @if ($record->pauses != null)
                                        <p><strong>Duraklatmalar:</strong></p>

                                        @foreach ($record->pauses as $pause)
                                          <p>&nbsp;&nbsp;&nbsp;&nbsp;{{ $pause }}</p>
                                        @endforeach

                                        <p>&nbsp;&nbsp;&nbsp;&nbsp;Toplam: {{ formatElapsedTime($record->totalPause) }}</p>
                                      @endif
                                      
                                      <p><strong>Geçen Süre: </strong>{{ formatElapsedTime($record->elapsed_time) }}</p>

                                      @if (Auth::user()->role == "Admin")
                                        @php
                                          $stepTime = $record->step->time / count(explode(', ',$record->employees));
                                          $expectedTime = $record->quantity * $stepTime;
                                        @endphp
                                        <p><strong>İdeal Süre: </strong>{{ formatElapsedTime($expectedTime) }}</p>
                                      @endif

                                      <p><strong>Verimlilik: </strong><span class="p-1 rounded" style="background-color: {{ $record->efficiencyColor }};"> %{{ $record->efficiencyPercentage }}</span></p>
                                    </div>  
                                  </td>
                                </tr>
                              </tbody>
                            </table>
                            @endforeach
                          
                          </div>
                        </div>  
                      </td>
                    </tr>
                  </tbody>
                </table>
              @endforeach

        </div>
      </div>
    </div>
    <!-- /.container-fluid -->
  </section>
  <!-- /.content -->
</div>

@endsection