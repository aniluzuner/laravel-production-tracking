<!DOCTYPE html>
<html lang="tr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  {{-- <meta http-equiv="refresh" content="60"> --}}
  <title>@yield('title')</title>
  
  <link rel="icon" type="image/png" href="{{ asset('favicon.png') }}">
  <link rel="stylesheet" href="{{ asset('adminlte/plugins/select2/css/select2.min.css') }}">
  <link rel="stylesheet" href="{{ asset('adminlte/plugins/select2-bootstrap4-theme/select2-bootstrap4.min.css') }}"> 
  <link rel="stylesheet" href="{{ asset('adminlte/plugins/fontawesome-free/css/all.min.css') }}">
  <link rel="stylesheet" href="{{ asset('adminlte/plugins/tempusdominus-bootstrap-4/css/tempusdominus-bootstrap-4.min.css') }}">
  <link rel="stylesheet" href="{{ asset('adminlte/plugins/icheck-bootstrap/icheck-bootstrap.min.css') }}">
  <link rel="stylesheet" href="{{ asset('adminlte/plugins/jqvmap/jqvmap.min.css') }}">
  <link rel="stylesheet" href="{{ asset('adminlte/dist/css/adminlte.min.css') }}">
  <link rel="stylesheet" href="{{ asset('adminlte/plugins/overlayScrollbars/css/OverlayScrollbars.min.css') }}">
  <link rel="stylesheet" href="{{ asset('adminlte/plugins/daterangepicker/daterangepicker.css') }}">
  <link rel="stylesheet" href="{{ asset('adminlte/plugins/summernote/summernote-bs4.min.css') }}">
  <link rel="stylesheet" href="{{ asset('adminlte/plugins/datatables-bs4/css/dataTables.bootstrap4.min.css') }}">
  <link rel="stylesheet" href="{{ asset('adminlte/plugins/datatables-responsive/css/responsive.bootstrap4.min.css') }}">
  <link rel="stylesheet" href="{{ asset('adminlte/plugins/datatables-buttons/css/buttons.bootstrap4.min.css') }}">
</head>
<body class="hold-transition sidebar-mini layout-footer-fixed">
  <div class="wrapper">
    <!-- Navbar -->
    <nav class="main-header navbar navbar-expand navbar-white navbar-light">
      <!-- Left navbar links -->
      <ul class="navbar-nav text-lg">
        <li class="nav-item">
          <a class="nav-link" data-widget="pushmenu" href="#" role="button"><i class="fas fa-bars"></i></a>
        </li>
        <li class="nav-item d-none d-sm-inline-block">
          <a href="{{ route('/') }}" class="nav-link">Ana Sayfa</a>
        </li>
      </ul>
  
      <!-- Right navbar links -->
      <ul class="navbar-nav ml-auto text-lg">
        <li class="nav-item">
          <a class="nav-link" href="{{ route('bildirimler') }}">
            <i class="fas fa-bell"></i>
            @if (Auth::user()->unreadNotifications->count() > 0)
              <span class="badge badge-danger navbar-badge">{{ Auth::user()->unreadNotifications->count() }}</span>
            @endif
          </a>
        </li>
        <li class="nav-item">
          <a class="nav-link" data-widget="fullscreen" href="#" role="button">
            <i class="fas fa-expand-arrows-alt"></i>
          </a>
        </li>
      </ul>
    </nav>
    <!-- Main Sidebar Container -->
    <aside class="main-sidebar sidebar-light-primary elevation-4">
      <!-- Brand Logo -->
      <a href="{{ route('/') }}" class="brand-link">
        <img src="{{ asset('favicon.png') }}" alt="Logo" class="brand-image img-circle elevation-3" style="opacity: .8">
        <span class="brand-text text-lg">Yazaroğlu</span>
      </a>

      <!-- Sidebar -->
      <div class="sidebar">
        <!-- Sidebar user panel (optional) -->
        <div class="user-panel mt-3 pb-3 mb-3 d-flex">
          @auth 
            <div class="info">
                <a class="d-block"> Kullanıcı: {{ Auth::user()->username }} </a>
            </div> 
          @endauth
        </div>

        <!-- SidebarSearch Form -->
        <div class="form-inline">
          <div class="input-group" data-widget="sidebar-search">
            <input class="form-control form-control-sidebar" type="search" placeholder="Search" aria-label="Search">
            <div class="input-group-append">
              <button class="btn btn-sidebar">
                <i class="fas fa-search fa-fw"></i>
              </button>
            </div>
          </div>
        </div>

        <!-- Sidebar Menu -->
        <nav class="mt-2">
          <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
            @if (Auth::user()->role == "Admin")
              <li class="nav-item">
                <a href="{{ route('calisanlar') }}" class="nav-link">
                  <i class="fas fa-users nav-icon"></i>
                  <p>&nbsp;Kullanıcılar</p>
                </a>
              </li>
            @endif
            
            @if (Auth::user()->role != "Çalışan")
              <li class="nav-item">
                <a href="{{ route('uretim') }}" class="nav-link">
                  <i class="fas fa-list nav-icon"></i>
                  <p>&nbsp;Üretim Listesi</p>
                </a>
              </li>
            @endif

            @if (Auth::user()->role == "Admin" || Auth::user()->role == "Denetleyici")
              <li class="nav-item">
                <a href="{{ route('raporlar') }}" class="nav-link">
                  <i class="nav-icon fas fa-list"></i>
                  <p>&nbsp;Raporlar</p>
                </a>
              </li>
            @endif

            @if (Auth::user()->role == "Admin" || Auth::user()->role == "Kontrolcü")
              <li class="nav-item">
                <a href="{{ route('gorevler') }}" class="nav-link">
                  <i class="fas fa-list-check nav-icon"></i>
                  <p>&nbsp;Tüm Görevler</p>
                </a>
              </li>
            @endif

            <li class="nav-item">
              <a href="{{ route('GetRecordsByUser', ['id' => Auth::user()->id]) }}" class="nav-link">
                <i class="nav-icon fas fa-list-check"></i>
                <p>&nbsp;Görevlerim</p>
              </a>
            </li>

            <li class="nav-item">
              <a href="{{ route('logout') }}" class="nav-link">
                <i class="nav-icon fas fa-right-from-bracket"></i>
                <p>&nbsp;Çıkış Yap</p>
              </a>
            </li>

          </ul>
        </nav>
        <!-- /.sidebar-menu -->
      </div>
      <!-- /.sidebar -->
    </aside>

    <script src="{{ asset('adminlte/plugins/jquery/jquery.min.js') }}"></script>
    <main>
      @yield('content')
    </main>

  </div>

<script>
  function formatElapsedTime(seconds) {
    const hours = Math.floor(seconds / 3600);
    const minutes = Math.floor((seconds % 3600) / 60);
    const remainingSeconds = seconds % 60;

    let result = '';

    if (hours > 0)
        result += `${hours} saat `;

    if (minutes > 0 || hours > 0)
      result += `${minutes} dakika `;

    result += `${remainingSeconds} saniye`;

    return result.trim();
  }

  function formatDate(isoDateString) {
    const date = new Date(isoDateString);

    const options = {
        day: 'numeric',
        month: 'long',
        year: 'numeric',
        hour: '2-digit',
        minute: '2-digit',
        hour12: false
    };

    const formattedDate = date.toLocaleString('tr-TR', options).replace(',', '');

    return formattedDate;
  }
</script>

<script src="{{ asset('adminlte/plugins/select2/js/select2.full.min.js') }}"></script>
<script src="{{ asset('adminlte/plugins/jquery-ui/jquery-ui.min.js') }}"></script>
<!-- Resolve conflict in jQuery UI tooltip with Bootstrap tooltip -->
<script>
  $.widget.bridge('uibutton', $.ui.button);
</script>
<!-- Bootstrap 4 -->
<script src="{{ asset('adminlte/plugins/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
<script src="{{ asset('adminlte/plugins/chart.js/Chart.min.js') }}"></script>
<script src="{{ asset('adminlte/plugins/jquery-knob/jquery.knob.min.js') }}"></script>
<script src="{{ asset('adminlte/plugins/moment/moment.min.js') }}"></script>
<script src="{{ asset('adminlte/plugins/daterangepicker/daterangepicker.js') }}"></script>
<script src="{{ asset('adminlte/plugins/tempusdominus-bootstrap-4/js/tempusdominus-bootstrap-4.min.js') }}"></script>
<script src="{{ asset('adminlte/plugins/summernote/summernote-bs4.min.js') }}"></script>
<script src="{{ asset('adminlte/plugins/overlayScrollbars/js/jquery.overlayScrollbars.min.js') }}"></script>
<script src="{{ asset('adminlte/dist/js/adminlte.js') }}"></script>
<script src="{{ asset('adminlte/plugins/datatables/jquery.dataTables.min.js') }}"></script>
<script src="{{ asset('adminlte/plugins/datatables-bs4/js/dataTables.bootstrap4.min.js') }}"></script>
<script src="{{ asset('adminlte/plugins/datatables-responsive/js/dataTables.responsive.min.js') }}"></script>
<script src="{{ asset('adminlte/plugins/datatables-responsive/js/responsive.bootstrap4.min.js') }}"></script>
<script src="{{ asset('adminlte/plugins/datatables-buttons/js/dataTables.buttons.min.js') }}"></script>
<script src="{{ asset('adminlte/plugins/datatables-buttons/js/buttons.bootstrap4.min.js') }}"></script>
<script src="{{ asset('adminlte/plugins/datatables-buttons/js/buttons.html5.min.js') }}"></script>
<script src="{{ asset('adminlte/plugins/datatables-buttons/js/buttons.print.min.js') }}"></script>
<script src="{{ asset('adminlte/plugins/datatables-buttons/js/buttons.colVis.min.js') }}"></script>
<script src="{{ asset('adminlte/plugins/datatables-buttons/js/buttons.colVis.min.js') }}"></script>
<script>
  $('#uretim-table').DataTable({
    responsive: true,
    processing: true,
    serverSide: true,
    ajax: {
        url: '{{ route('GetProductions') }}',
        type: 'GET',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        }
    },
    columns: [
      { data: 'title', name: 'title' },
      { data: 'stock_code', name: 'stock_code', className: 'text-left text-md-center' },
      { data: 'job_count', name: 'job_count', className: 'text-left text-md-center', searchable: false },
      @if (Auth::user()->role == "Admin")
        { 
            data: 'id',
            name: 'id',
            render: function (data, type, row) {
                return `
                  <div class="d-flex justify-content-center flex-column flex-md-row">
                    <button type="button" class="btn btn-sm btn-info mx-1 mb-1 mb-md-0" onClick="getSteps(${data})" data-toggle="modal" data-target="#modal-onizleme">
                      <i class="fa fa-bars"></i> Adımlar</button>
                    <button type="button" class="btn btn-sm btn-success mx-1 mb-1 mb-md-0" onClick="assignTask(${data})" data-toggle="modal" data-target="#modal-gorevAta">
                      <i class="fa fa-users"></i> Görev Oluştur</button>
                    <button type="button" class="btn btn-sm btn-warning mx-1 mb-1 mb-md-0" onClick="edit(${data})" data-toggle="modal" data-target="#modal-guncelle">
                      <i class="fa fa-pencil-alt"></i> Düzenle</button>
                    <button type="button" class="btn btn-sm btn-danger mx-1 mb-1 mb-md-0" onClick="deleteProduction(${data})">
                      <i class="fa fa-trash"></i> Sil</button>
                  </div>
                `;
            },
            orderable: false,
            searchable: false,
            className: 'text-left text-md-center'
        }
      @else
        { 
            data: 'id',
            name: 'id',
            render: function (data, type, row) {
                return `
                  <div class="d-flex justify-content-center flex-column flex-md-row">
                    <button type="button" class="btn btn-sm btn-info mx-1 mb-1 mb-md-0" onClick="getSteps(${data})" data-toggle="modal" data-target="#modal-onizleme">
                      <i class="fa fa-bars"></i> Adımlar</button>
                    <button type="button" class="btn btn-sm btn-success mx-1 mb-1 mb-md-0" onClick="assignTask(${data})" data-toggle="modal" data-target="#modal-gorevAta">
                      <i class="fa fa-users"></i> Görev Oluştur</button>
                  </div>
                `;
            },
            orderable: false,
            searchable: false,
            className: 'text-center'
        }
      @endif

    ],
    order: [],
    language: {
      url: "{{ asset('adminlte/plugins/datatables/tr.json') }}"
    }
  });

  $('#gorevler-table').DataTable({
    responsive: true,
    order: [],
    language: {
      url: "{{ asset('adminlte/plugins/datatables/tr.json') }}"
    }
  });

  $('#users-table').DataTable({
    responsive: true,
    language: {
      url: "{{ asset('adminlte/plugins/datatables/tr.json') }}"
    }
  });
</script>
</body>
</html>
