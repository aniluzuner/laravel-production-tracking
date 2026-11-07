@extends('layouts.main')

@section('title', 'Yazaroğlu Elektronik')

@section('content')
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <div class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1 class="m-0">Bildirimler</h1>
          </div><!-- /.col -->
          <div class="col-sm-6">
          </div><!-- /.col -->
        </div><!-- /.row -->
      </div><!-- /.container-fluid -->
    </div>
    <!-- /.content-header -->

    <!-- Main content -->
    <section class="content">
      <div class="container-fluid">
        <div class="row">
          @php
            $unreadNotifications = $notifications->where('read_at', null);
            $readNotifications = $notifications->where('read_at', '!=', null);
          @endphp
          
          @foreach($unreadNotifications as $notification)
            <div class="col-12 position-relative">
              <div class="info-box">
                @if($notification->data['status'] == "Başlatıldı")
                  <span class="info-box-icon bg-info"><i class="fa fa-play"></i></span>
                @elseif($notification->data['status'] == "Duraklatıldı")
                  <span class="info-box-icon bg-warning"><i class="fa fa-pause"></i></span>
                @elseif($notification->data['status'] == "Onaylandı")
                  <span class="info-box-icon bg-success"><i class="fa fa-check"></i></span>
                @else
                  <span class="info-box-icon bg-success"><i class="fa fa-stop"></i></span>
                @endif

                <div class="info-box-content">
                  <span class="info-box-number d-flex justify-content-between">MPS: {{ $notification->data['mps'] }} {{ $notification->data['title']}} <span class="text-muted font-weight-light">{{ $notification->created_at->diffForHumans() }}</span></span>
                  <span class="info-box-text">{{ $notification->data['message'] }}</span>
                </div>
              </div>
              @if($notification->data['status'] == "Onaylandı")
                <a href="{{ route('raporlar') }}" class="stretched-link"></a>
              @else
                <a href="{{ route('gorevler') }}" class="stretched-link"></a>
              @endif
            </div>
          @endforeach

          <table class="table table-hover bg-white rounded">
            <tbody>
              <tr data-widget="expandable-table" aria-expanded="false">
                <td class="border-0 rounded text-lg">
                  <i class="expandable-table-caret fas fa-caret-right fa-fw"></i>
                  Okunmuş Bildirimler
                </td>
              </tr>
              <tr class="expandable-body d-none">
                <td>
                  <div>
                    @foreach($readNotifications as $notification)
                      <div class="col-12 position-relative">
                        <div class="info-box">
                          @if($notification->data['status'] == "Başlatıldı")
                            <span class="info-box-icon bg-info"><i class="fa fa-play"></i></span>
                          @elseif($notification->data['status'] == "Duraklatıldı")
                            <span class="info-box-icon bg-warning"><i class="fa fa-pause"></i></span>
                          @elseif($notification->data['status'] == "Onaylandı")
                            <span class="info-box-icon bg-success"><i class="fa fa-check"></i></span>
                          @else
                            <span class="info-box-icon bg-success"><i class="fa fa-stop"></i></span>
                          @endif

                          <div class="info-box-content">
                            <span class="info-box-number d-flex justify-content-between">MPS: {{ $notification->data['mps'] }} {{ $notification->data['title']}} <span class="text-muted font-weight-light">{{ $notification->created_at->diffForHumans() }}</span></span>
                            <span class="info-box-text">{{ $notification->data['message'] }}</span>
                          </div>
                        </div>
                        @if($notification->data['status'] == "Onaylandı")
                          <a href="{{ route('raporlar') }}" class="stretched-link"></a>
                        @else
                          <a href="{{ route('gorevler') }}" class="stretched-link"></a>
                        @endif
                      </div>
                    @endforeach
                  </div>  
                </td>
              </tr>
            </tbody>
          </table>
          {{auth()->user()->unreadNotifications->markAsRead();}}
        </div>
      </div>
    </section>
  </div>
@endsection