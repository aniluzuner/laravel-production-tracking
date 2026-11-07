@extends('layouts.main')

@section('title', 'Yazaroğlu Elektronik')

@section('content')

<div class="content-wrapper">
  <div class="content-header">
    <div class="container-fluid">
      <div class="row mb-2">
        <div class="col-12 d-flex justify-content-between">
          <h1 class="m-0 p-0" style="line-height: 1.75;">Üretim Listesi</h1>
          @if (Auth::user()->role == "Admin")
            <button type="button" class="btn btn-lg btn-success" data-toggle="modal" data-target="#modal-gorevEkle"> <i class="fas fa-plus"></i> &nbsp;Üretim Ekle </button>
          @endif
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
          <div class="card">
            <div class="card-body">
              <table class="table table-hover display nowrap w-100" style="font-size: 0.9rem;" id="uretim-table">
                <thead>
                  <tr>
                    <th>Üretim Başlığı</th>
                    <th>Stok Kodu</th>
                    <th>Aktif Görev</th>
                    <th>İşlemler</th>
                  </tr>
                </thead>
                <tbody>
                  {{-- @foreach ($productions as $production)
                    <tr>
                        <td>{{ $production->id }}</td>
                        <td>{{ $production->title }}</td>
                        <td class="text-center">{{ $production->stock_code }}</td>
                        <td class="text-center">{{ $production->job_count }}</td>
                        <td class="text-center">{{ $production->last_production_date }}</td>
                        <td>
                          <div class="d-flex justify-content-center flex-column flex-md-row">
                            <button type="button" class="btn btn-sm btn-info mx-1 mb-1 mb-md-0" onClick="getSteps({{$production->id}})" data-toggle="modal" data-target="#modal-onizleme">
                              <i class="fa fa-bars"></i> Adımlar</button>
                            <button type="button" class="btn btn-sm btn-success mx-1 mb-1 mb-md-0" onClick="assignTask({{$production->id}})" data-toggle="modal" data-target="#modal-gorevAta">
                                <i class="fa fa-users"></i> Görev Oluştur</button>
                            @if (Auth::user()->role == "Admin")
                              <button type="button" class="btn btn-sm btn-warning mx-1 mb-1 mb-md-0" onClick="edit({{$production->id}})" data-toggle="modal" data-target="#modal-guncelle">
                                <i class="fa fa-pencil-alt"></i> Düzenle</button>
                              <button type="button" class="btn btn-sm btn-danger mx-1 mb-1 mb-md-0" onClick="deleteProduction({{$production->id}})">
                                <i class="fa fa-trash"></i> Sil</button>
                            @endif
                          </div>      
                        </td>
                    </tr>
                  @endforeach --}}
                </tbody>
              </table>
            </div>
          </div>
          <!-- /.card -->
        </div>
      </div>
    </div>
    <!-- /.container-fluid -->



    <!-- Görev Ekle Modal -->
    <div class="modal fade" id="modal-gorevEkle">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h4 class="modal-title">Üretim Ekle</h4>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <form action="{{ route('productions.post') }}" method="POST" autocomplete="off">
            @csrf
            <div class="modal-body">
              <div class="card-body">
                
                  <div class="row">
                    <div class="col-sm-12">
                      <!-- text input -->
                      <div class="form-group">
                        <label>Üretim Başlığı</label>
                        <input type="text" class="form-control" name="title">
                        <label style="margin-top: 10px;">Stok Kodu</label>
                        <input type="text" class="form-control" name="stock_code">
                      </div>
                      <div>
                        <table class="table table-bordered">
                          <thead>
                            <tr>
                              <th>Adım</th>
                              <th>Sn / Adet</th>
                              <th>Sil</th>
                            </tr>
                          </thead>
                          <tbody>
        
                          </tbody>
                        </table>
                        <div class="d-flex justify-content-end">
                          <button type="button" class="btn btn-outline-dark" onClick="addStep(this)">Adım Ekle</button>
                        </div>
                      </div>
                    </div>
                  </div>
              </div>
            </div>
            <div class="modal-footer justify-content-between">
              <button type="button" class="btn btn-default" data-dismiss="modal">Kapat</button>
              <button type="submit" class="btn btn-primary">Kaydet</button>
            </div>
          </form>
        </div>
        <!-- /.modal-content -->
      </div>
      <!-- /.modal-dialog -->
    </div>
    <!-- /.modal -->

    <!-- Güncelle Modal -->
    <div class="modal fade" id="modal-guncelle">
      <div class="modal-dialog" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="modalGuncelleLabel">Üretim Düzenle</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <form action="{{ route('productions.update') }}" method="POST" autocomplete="off">
            @csrf
            <div class="modal-body">
                <div class="form-group">
                  <label>Üretim Başlığı</label>
                  <input type="text" class="form-control" name="title" id="edit-title">
                  <label style="margin-top: 10px;">Stok Kodu</label>
                  <input type="text" class="form-control" name="stock_code" id="edit-stock_code">
                  <input type="hidden" name="id" id="edit-id">
                </div>
                <div>
                  <table class="table table-bordered">
                    <thead>
                      <tr>
                        <th>Adım</th>
                        <th>Sn / Adet</th>
                        <th>Sil</th>
                      </tr>
                    </thead>
                    <tbody id="edit-steps">

                    </tbody>
                  </table>
                  <div class="d-flex justify-content-end">
                    <button type="button" class="btn btn-outline-dark" onClick="addStep(this)">Adım Ekle</button>
                  </div>
                </div>
              
            </div>
            <div class="modal-footer justify-content-between">
              <button type="button" class="btn btn-default" data-dismiss="modal">Kapat</button>
              <button type="submit" class="btn btn-primary">Kaydet</button>
            </div>
          </form>
        </div>
      </div>
    </div>
    <!-- /.modal -->

    <!-- Önizleme Modal -->
    <div class="modal fade" id="modal-onizleme">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">Üretim Adımları</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <div class="modal-body">

            <div class="card-body">
              <form>
                <div class="row">
                  <div class="col-sm-12">
                    <!-- text input -->
                    <div class="form-group">
                      <label>Üretim Başlığı</label>
                      <p id="steps-production-title"></p>
                      
                      <label style="margin-top: 10px;">Stok Kodu</label>
                      <p id="steps-production-stock-code"></p>

                      <div id="stepsTableContainer">
                        <table class="table table-responsive p-0">
                          <thead>
                            <tr>
                              <th>Adım</th>
                              @if (Auth::user()->role == "Admin")
                                <th>Süre (sn)</th>
                              @endif
                            </tr>
                          </thead>
                          <tbody id="steps-table">
                            
                          </tbody>
                        </table>
                      </div>
                    </div>
                  </div>
                </div>
              </form>
            </div>
          </div>
          <div class="modal-footer justify-content-between">
            <button type="button" class="btn btn-default" data-dismiss="modal">Kapat</button>
          </div>
        </div>
        <!-- /.modal-content -->
      </div>
      <!-- /.modal-dialog -->
    </div>
    <!-- /.modal -->


    <!-- Görev Ata Modal -->
    <div class="modal fade" id="modal-gorevAta">
      <div class="modal-dialog" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="modalGuncelleLabel">Görev Ata</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <form action="{{ route('AddTask') }}" method="post" autocomplete="off">
            @csrf
            <div class="modal-body">
              <div class="form-group" id="assignTask">
                
              </div>
            </div>
            <div class="modal-footer justify-content-between">
              <button type="button" class="btn btn-default" data-dismiss="modal">Kapat</button>
              <button type="submit" class="btn btn-primary">Görev Oluştur</button>
            </div>
          </form>
        </div>
      </div>
    </div>
    <!-- /.modal -->


  </section>
  <!-- /.content -->
</div>
<!-- /.content-wrapper -->

<script>
  function addStep(button){
    var tbody = button.closest('form').querySelector('table').querySelector('tbody');

    let tr = document.createElement('tr');
    tr.innerHTML = `<tr><td class="col-9"> <input type="text" class="form-control" name="steps[${tbody.rows.length}][step_title]"> </td><td class="col-3"> <input type="number" class="form-control" step="0.01" name="steps[${tbody.rows.length}][step_time]"</td><td><button class="btn btn-danger" onClick="deleteStep(this)">X</button></td></tr>`;
    tbody.appendChild(tr);
  }

  function deleteStep(button){
    var row = button.closest('tr');
    row.remove();
  }

  function getSteps(id){
    $('#steps-table').empty();
    $.ajax({
      url: 'productions/' + id,
      type: 'GET',
      success: function (response) {
        $('#steps-production-title').text(response.title);
        $('#steps-production-stock-code').text(response.stock_code);
      
        response.steps.forEach(function (step) {
          let tr = document.createElement('tr');
          tr.innerHTML += `<tr><td>${step.title} </td>`;

          if (@json(Auth::user()->role) == "Admin")
            tr.innerHTML += `<td class="text-center">${step.time} </td>`;

          tr.innerHTML += `</tr>`;

          $('#steps-table').append(tr);
        });
      },
      error: function (xhr, status, error) {
        console.log('Error: ' + error);
      }
    });
  }

  function edit(id){
    $.ajax({
      url: '/productions/' + id,
      type: 'GET',
      success: function (response) {
        $('#edit-id').val(id);
        $('#edit-title').val(response.title);
        $('#edit-stock_code').val(response.stock_code);

        $('#edit-steps').empty();
        response.steps.forEach(function(step, index){
          let tr = document.createElement('tr');
          tr.innerHTML = `<tr><td class="col-9"> <input type="text" class="form-control" name="steps[${index}][step_title]" value="${step.title}"> </td><td class="col-3"> <input type="number" class="form-control" name="steps[${index}][step_time]" value="${step.time}"></td><td><button class="btn btn-danger" onClick="deleteStep(this)">X</button></td></tr>`;
          $('#edit-steps').append(tr);
        });
      },
      error: function (xhr, status, error) {
        console.log('Error: ' + error);
      }
    });
  }

  var steps;
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

  function assignTask(id){
    $('#assignTask').empty();
    let controller_id = @json(Auth::id());
    $.ajax({
      url: '/productions/' + id,
      type: 'GET',
      success: function (response) {
        // steps = response.steps;
        
        $('#assignTask').append(`<label>Üretim Başlığı</label><p>${response.title}</p>`);
        if (response.stock_code !== null){
          $('#assignTask').append(`<label style="margin-top: 10px;">Stok Kodu</label><p> ${response.stock_code}</p>`);
        }
        $('#assignTask').append(`<label style="margin-top: 10px;">MPS No</label><input type="text" name="mps" class="form-control" required></input>`);
        $('#assignTask').append(`<label style="margin-top: 10px;">Adet</label><input type="number" name="quantity" class="form-control mb-2" required></input>`);
        $('#assignTask').append(`<input type="hidden" name="controller_id" value="${controller_id}"></input>`);
        $('#assignTask').append(`<input type="hidden" name="production_id" value="${id}"></input>`);

        // $('#assignTask').append(`<label style="margin-top: 10px">Üretim Adımları</label>`);
        // $('#assignTask').append(`<div class="d-flex justify-content-end">
        //                            <button type="button" class="btn btn-outline-dark" id="addTaskStepButton" onClick="addTaskStep(${response.id})">Adım Ekle</button>
        //                          </div>`);
      },
      error: function (xhr, status, error) {
        console.log('Error: ' + error);
      }
    });
  }

  function addTaskStep(){
    $('#addTaskStepButton').remove();

    let div = document.createElement('div');
    div.id = "adim" + ($('#assignTask div[id^="adim"]').length + 1);

    let select1 = document.createElement('select');
    select1.name = "taskStep[" + $('#assignTask div[id^="adim"]').length + "][step_id]";
    select1.className = "form-control mt-2";
    select1.required = "true";

    let placeholderOption1 = document.createElement('option');
    placeholderOption1.value = "";
    placeholderOption1.textContent = 'Adım seçin';
    placeholderOption1.selected = true;
    select1.appendChild(placeholderOption1);

    steps.forEach(function(step){
      select1.innerHTML += `<option value="${step.id}"> ${step.title} </option>`;
    });

    div.append(select1);

    let employeeDiv = document.createElement('div');
    employeeDiv.id = "calisanlar[" + $('#assignTask div[id^="adim"]').length + "]";
    employeeDiv.className = "d-block mt-2 ";

    let select2 = document.createElement('select');
    select2.id = "employeeSelect-" + $('#assignTask div[id^="adim"]').length;
    select2.name = "taskStep[" + $('#assignTask div[id^="adim"]').length + "][employees][]";
    select2.className = "form-control";
    select2.multiple = "true";
    select2.required = "true";
    
    employees.forEach(function(employee){
        select2.innerHTML += `<option value="${employee.fullname}"> ${employee.fullname} </option>`;
    });

    employeeDiv.appendChild(select2);
    div.appendChild(employeeDiv);

    let textarea = document.createElement('textarea');
    textarea.name = "taskStep[" + $('#assignTask div[id^="adim"]').length + "][description]";
    textarea.className = "form-control my-2";
    textarea.placeholder = "Açıklama yazın";
    textarea.rows = 1;
    div.append(textarea);

    div.innerHTML += '<button type="button" class="btn btn-outline-danger mb-4 w-100" onClick="deleteTaskStep(this)"><i class="fa fa-trash"></i> Sil</button>';

    $('#assignTask').append(div);

    $("#employeeSelect-" + ($('#assignTask div[id^="adim"]').length - 1)).select2({
      tags: true,
      placeholder: "Çalışan seçin ya da ekleyin",
      allowClear: true,
      tokenSeparators: [',']
    });

    $('#assignTask').append(`<div class="d-flex justify-content-end mt-2" id="addTaskStepButton">
      <button type="button" class="btn btn-outline-dark" onClick="addTaskStep()">Adım Ekle</button>
    </div>`);
  }

  function deleteTaskStep(button){
    $(button).closest('div').remove();
  }

  function deleteProduction(id){
    if(confirm(id + " ID'li üretimi silmek istediğinize emin misiniz?")){
      $.ajax({
        url: '/productions/' + id,
        type: 'DELETE',
        data: {
          _token: '{{ csrf_token() }}'
        },
        success: function (response) {
          location.reload();
        },
        error: function (xhr, status, error) {
          console.log('Error: ' + error);
          alert('An error occurred while deleting the production.');
        }
      });
    }
  }

  
</script>

@endsection