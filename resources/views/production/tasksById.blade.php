@extends('layouts.main')

@section('title', 'Yazaroğlu Elektronik')

@section('content')

<div class="content-wrapper">
  <div class="content-header">
    <div class="container-fluid">
      <div class="row mb-2">
        <div class="col-12">
          <h1 class="m-0 p-0" style="line-height: 1.75;">Görevlerim</h1>
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

            @foreach ($taskDetails as $taskDetail)
                <table class="table table-hover bg-white rounded">
                  <tbody>
                    <tr data-widget="expandable-table" aria-expanded="false">
                      <td class="border-0 rounded text-lg">
                        <i class="expandable-table-caret fas fa-caret-right fa-fw"></i>
                        {{ $taskDetail['production']->title }}
                      </td>
                    </tr>
                    <tr class="expandable-body d-none">
                      <td>
                        <div>
                          <p><strong>MPS: </strong>{{ $taskDetail['task']->mps }}</p>
                          <p><strong>Üretim Başlığı: </strong>{{ $taskDetail['production']->title }}</p>
                          <p><strong>Stok Kodu: </strong>{{ $taskDetail['production']->stock_code }}</p>
                          <p><strong>Adet: </strong>{{ $taskDetail['task']->quantity }}</p>
                          <p><strong>Tarih: </strong>{{ \Carbon\Carbon::parse($taskDetail['task']->created_at)->isoFormat('D MMMM YYYY HH:mm') }}</p>
                          <p><strong>Adımlar: </strong></p>
                          @foreach ($taskDetail['productionRecords'] as $record)
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
                                      <p><strong>Çalışanlar: </strong>
                                        @php
                                          $employees = explode(', ', $record->employees);

                                          foreach($employees as $employee){
                                            echo "<button onClick='removeEmployee($record->id, \"$employee\")' class='btn btn-sm btn-primary mx-1'> $employee <i class='fa fa-xmark ml-2'></i> </button>";
                                          }
                                        @endphp

                                        <button type="button" class="btn btn-sm btn-outline-dark" onClick="addEmployees({{ $record->id }})" data-toggle="modal" data-target="#modal-calisanEkle">Çalışan Ekle</button>
                                      </p>
                                      @if ($record->description != null)
                                        <p><strong>Açıklama: </strong>{{ $record->description }}</p>
                                      @endif
                                      <p><strong>Durum: </strong>{{ $record->status }}</p>

                                      <div class="d-flex flex-column flex-md-row">
                                        @if ($record->status == "Başlamadı")
                                          <a href="/StartTask/{{ $record->id }}" class="btn btn-success mx-1 mb-1 mb-md-0">
                                            <i class="fa fa-play"></i> Başla</a>
                                        @elseif ($record->status == "Devam Ediyor")
                                          <a href="/PauseTask/{{ $record->id }}" class="btn btn-warning mx-1 mb-1 mb-md-0">
                                            <i class="fa fa-pause"></i> Duraklat</a>
                                          <a href="/EndTask/{{ $record->id }}" class="btn btn-danger mx-1 mb-1 mb-md-0" onclick="return confirm('{{ $record->step->title }} adımını bitirmek istediğinizden emin misiniz?');">
                                            <i class="fa fa-stop"></i> Bitir</a>
                                        @elseif ($record->status == "Duraklatıldı" || $record->status == "Otomatik Duraklatıldı")
                                          <a href="/StartTask/{{ $record->id }}" class="btn btn-success mx-1 mb-1 mb-md-0">
                                            <i class="fa fa-play"></i> Devam Et</a>
                                          <a href="/EndTask/{{ $record->id }}" class="btn btn-danger mx-1 mb-1 mb-md-0" onclick="return confirm('{{ $record->step->title }} adımını bitirmek istediğinizden emin misiniz?');">
                                            <i class="fa fa-stop"></i> Bitir</a>
                                        @endif
                                      </div>
                                    </div>  
                                  </td>
                                </tr>
                              </tbody>
                            </table>
                            @endforeach
                            <div class="p-3">
                              <button type="button" class="btn btn-outline-dark" onClick="addTaskStep({{ $taskDetail['task']->id }})" data-toggle="modal" data-target="#modal-adimEkle">Adım Ekle</button>
                              @if(count($taskDetail['productionRecords']) == 0)
                                  <button type="button" class="btn btn-danger ml-2" onClick="deleteTask({{ $taskDetail['task']->id }})"> <i class="fa fa-trash"></i> Görevi İptal Et</button>
                              @endif
                            </div>
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

<div class="modal fade" id="modal-adimEkle">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="modalGuncelleLabel">Adım Ekle</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <form action="{{ route('AddProductionRecord') }}" method="post" autocomplete="off">
        @csrf
        <div class="modal-body">
          <div class="form-group" id="addStepDiv">
            
          </div>
        </div>
        <div class="modal-footer justify-content-between">
          <button type="button" class="btn btn-default" data-dismiss="modal">Kapat</button>
          <button type="submit" class="btn btn-success">Başlat</button>
        </div>
      </form>
    </div>
  </div>
</div>

<div class="modal fade" id="modal-calisanEkle">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="modalGuncelleLabel">Çalışan Ekle</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <form action="{{ route('AddEmployees') }}" method="post" autocomplete="off">
        @csrf
        <div class="modal-body">
          <div class="form-group" id="calisanEkle">
            
          </div>
        </div>
        <div class="modal-footer justify-content-between">
          <button type="button" class="btn btn-default" data-dismiss="modal">Kapat</button>
          <button type="submit" class="btn btn-success">Kaydet</button>
        </div>
      </form>
    </div>
  </div>
</div>

<script>
  var employees;

  document.addEventListener("DOMContentLoaded", function() {
    $.ajax({
      url: '/GetEmployees',
      type: 'GET',
      success: function (response) {
        employees = response;
      }
    });        
  });

  function addTaskStep(id){
    $('#addStepDiv').empty();

    $.ajax({
      url: '/task/' + id,
      type: 'GET',
      success: function (response) {
        $('#addStepDiv').append(`<input type="hidden" name="mps" value="${response.mps}"></input>`);
        $('#addStepDiv').append(`<input type="hidden" name="quantity" value="${response.quantity}"></input>`);
        $('#addStepDiv').append(`<input type="hidden" name="controller_id" value="${response.controller_id}"></input>`);

        let select1 = document.createElement('select');
        select1.name = "taskStep[0][step_id]";
        select1.className = "form-control mt-2";
        select1.required = "true";

        let placeholderOption1 = document.createElement('option');
        placeholderOption1.value = "";
        placeholderOption1.textContent = 'Adım seçin';
        placeholderOption1.selected = true;
        select1.appendChild(placeholderOption1);

        response.production.steps.forEach(function(step){
          select1.innerHTML += `<option value="${step.id}"> ${step.title} </option>`;
        });

        $('#addStepDiv').append(select1);

        let employeeDiv = document.createElement('div');
        employeeDiv.id = "calisanlar";
        employeeDiv.className = "d-block mt-2 ";

        let select2 = document.createElement('select');
        select2.id = "employeeSelect";
        select2.name = "taskStep[0][employees][]";
        select2.className = "form-control";
        select2.multiple = "true";
        select2.required = "true";
        
        employees.forEach(function(employee){
            select2.innerHTML += `<option value="${employee.fullname}"> ${employee.fullname} </option>`;
        });

        employeeDiv.appendChild(select2);
        $('#addStepDiv').append(employeeDiv);

        let textarea = document.createElement('textarea');
        textarea.name = "taskStep[0][description]";
        textarea.className = "form-control my-2";
        textarea.placeholder = "Açıklama yazın";
        textarea.rows = 1;
        $('#addStepDiv').append(textarea);

        $("#employeeSelect").select2({
          tags: true,
          placeholder: "Çalışan seçin ya da ekleyin",
          allowClear: true,
          tokenSeparators: [',']
        });

      },
      error: function (xhr, status, error) {
        console.log('Error: ' + error);
      }
    });
  }

  function deleteTask(id){
    if(confirm("Görevi iptal etmek istediğinize emin misiniz?")){
      $.ajax({
        url: '/DeleteTask/' + id,
        method: 'GET',
        data: {
            _token: '{{ csrf_token() }}'
        },
        success: function(response) {
          location.reload();
        },
        error: function(xhr) {
          alert('An error occurred: ' + xhr.responseJSON.message);
        }
      });
    }
  }

  function addEmployees(id){
    $('#calisanEkle').empty();

    $('#calisanEkle').append(`<input type="hidden" name="id" value="${id}}"></input>`);

    let select = document.createElement('select');
    select.id = "employeeSelect";
    select.name = "employees[]";
    select.className = "form-control";
    select.multiple = "true";
    select.required = "true";
        
    employees.forEach(function(employee){
      select.innerHTML += `<option value="${employee.fullname}"> ${employee.fullname} </option>`;
    });

    $('#calisanEkle').append(select);

    $("#employeeSelect").select2({
          tags: true,
          placeholder: "Çalışan seçin ya da ekleyin",
          allowClear: true,
          tokenSeparators: [',']
        });
  }

  function removeEmployee(id, employee){
    if(confirm(`${employee} isimli çalışanı bu adımdan çıkartmak istediğinize emin misiniz?`)){
      $.ajax({
        url: '/RemoveEmployee',
        method: 'POST',
        data: {
            _token: '{{ csrf_token() }}',
            id: id,
            employee: employee
        },
        success: function(response) {
          location.reload();
        },
        error: function(xhr) {
          alert('An error occurred: ' + xhr.responseJSON.message);
        }
      });
    }
  }
</script>
@endsection