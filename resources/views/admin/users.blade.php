@extends('layouts.main')

@section('title', 'Yazaroğlu Elektronik')

@section('content')
<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
  <!-- Content Header (Page header) -->
  <div class="content-header">
    <div class="container-fluid">
      <div class="row mb-2">
        <div class="col-12 d-flex justify-content-between">
          <h1 class="m-0 p-0" style="line-height: 1.75;">Kullanıcı Listesi</h1>
          <button type="button" class="btn btn-lg btn-success" data-toggle="modal" data-target="#modal-calisanEkle"> <i class="fas fa-plus"></i> &nbsp;Kullanıcı Ekle </button>

        </div><!-- /.col -->
      </div>
    </div><!-- /.container-fluid -->
  </div>
  <!-- /.content-header -->

  <!-- Main content -->
  <section class="content">
    <div class="container-fluid">
      <div class="row">
        <div class="col-12">
          <div class="card">
            <div class="card-body">
              <table id="users-table" class="table table-hover display nowrap w-100" style="font-size: 0.9rem">
                <thead>
                  <tr>
                    <th>Adı Soyadı</th>
                    <th>Kullanıcı Adı</th>
                    <th>Şifre</th>
                    <th>E-posta</th>
                    <th>Telefon</th>
                    <th>Rol</th>
                    <th class="text-center">İşlemler</th>
                  </tr>
                </thead>
                <tbody>
                  @foreach ($users as $user)
                    <tr>
                        <td>{{ $user->fullname }}</td>
                        <td>{{ $user->username }}</td>
                        <td>{{ $user->password }}</td>
                        <td>{{ $user->email }}</td>
                        <td>{{ $user->phone }}</td>
                        <td>{{ $user->role }}</td>
                        <td>
                          <div class="d-flex justify-content-center flex-column flex-md-row">
                            <a href="/gorevler/{{ $user->id }}" type="button" class="btn btn-sm btn-success mx-1 mb-1 mb-md-0">
                              <i class="fa fa-list-check"></i> Görevler</a>
                            <button type="button" class="btn btn-sm btn-warning mx-1 mb-1 mb-md-0" onClick="edit({{$user->id}})" data-toggle="modal" data-target="#modal-calisanGuncelle">
                              <i class="fa fa-pencil-alt"></i> Düzenle</button>
                            <button type="button" class="btn btn-sm btn-danger mx-1 mb-1 mb-md-0" onClick="openDeleteModal({{ $user->id}})" data-toggle="modal" data-target="#modal-calisanKaldir">
                                <i class="fa fa-trash"></i> Sil</button>
                          </div>      
                        </td>
                    </tr>
                  @endforeach
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Çalışan Modal -->
    <div class="modal fade" id="modal-calisanEkle">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h4 class="modal-title">Kullanıcı Ekle</h4>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <form method="post" action="{{ route('AddUser') }}">
            @csrf
            <div class="modal-body">
              <div class="card-body">
                  <div class="row">
                    <div class="col-sm-12">
                      <!-- text input -->
                      <div class="form-group">
                        <label>Adı ve Soyadı</label>
                        <input type="text" class="form-control" name="fullname" required>
                        <label style="margin-top: 10px;">Kullanıcı Adı</label>
                        <input type="text" class="form-control" name="username"required>
                        <label style="margin-top: 10px;">Şifre</label>
                        <input type="text" class="form-control" name="password" minlength="8" required>
                        <label style="margin-top: 10px;">Telefon No</label>
                        <input type="text" class="form-control" name="phone">
                        <label style="margin-top: 10px;">Email</label>
                        <input type="text" class="form-control" name="email">
                        <label style="margin-top: 10px;">Rolü</label>
                        <select class="form-control" name="role" required>
                          <option value>Rol seçin</option>
                          <option value="Admin">Admin</option>
                          <option value="Denetleyici">Denetleyici</option>
                          <option value="Kontrolcü">Kontrolcü</option>
                          <option value="Bant Şefi">Bant Şefi</option>
                          <option value="Çalışan">Çalışan</option>
                        </select>
                      </div>
                    </div>
                  </div>
              </div>
            </div>
            <div class="modal-footer justify-content-between">
              <button type="button" class="btn btn-default" data-dismiss="modal">Kapat</button>
              <button type="submit" class="btn btn-primary">Ekle</button>
            </div>
          </form>
        </div>
        <!-- /.modal-content -->
      </div>
      <!-- /.modal-dialog -->
    </div>
    <div class="modal fade" id="modal-calisanGuncelle">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h4 class="modal-title">Kullanıcı Düzenle</h4>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <form method="post" action="{{ route('EditUser') }}" autocomplete="off">
            @csrf
            <div class="modal-body">
              <div class="card-body">
                  <div class="row">
                    <div class="col-sm-12">
                      <!-- text input -->
                      <div class="form-group">
                        <input type="hidden" name="id" id="edit-id">
                        <label>Adı ve Soyadı</label>
                        <input type="text" class="form-control" id="edit-fullname" name="fullname" required>
                        <label style="margin-top: 10px;">Kullanıcı Adı</label>
                        <input type="text" class="form-control" id="edit-username" name="username" required>
                        <label style="margin-top: 10px;">Şifre</label>
                        <input type="text" class="form-control" id="edit-password" name="password" minlength="8" required>
                        <label style="margin-top: 10px;">Telefon No</label>
                        <input type="text" class="form-control" id="edit-phone" name="phone">
                        <label style="margin-top: 10px;">Email</label>
                        <input type="text" class="form-control" id="edit-email" name="email">
                        <label style="margin-top: 10px;">Rolü</label>
                        <select class="form-control" name="role" required>
                          <option value>Rol seçin</option>
                          <option value="Admin">Admin</option>
                          <option value="Denetleyici">Denetleyici</option>
                          <option value="Kontrolcü">Kontrolcü</option>
                          <option value="Bant Şefi">Bant Şefi</option>
                          <option value="Çalışan">Çalışan</option>
                        </select>
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
    <div class="modal fade" id="modal-calisanKaldir">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h4 class="modal-title">Kullanıcı Sil</h4>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">×</span>
            </button>
          </div>
          <div class="modal-body">
            <p>Bu kullanıcıyı silmek istediğinize emin misiniz?</p>
          </div>
          <div id="delete-div" class="modal-footer justify-content-between">
          </div>
        </div>

      </div>
    </div>
  </section>
  <!-- /.content -->
</div>
<!-- /.content-wrapper -->
<script>
  function openDeleteModal(id){
    $('#delete-div').empty();
    $('#delete-div').append(`<button type="button" class="btn btn-outline-light" data-dismiss="modal">Kapat</button>`);
    $('#delete-div').append(`<button type='button' onClick='deleteUser(${id})' class='btn btn-danger toastrDefaultSuccess'>Sil</button>`);
  }

  function edit(id){
    $.ajax({
      url: 'GetUser/' + id,
      type: 'GET',
      success: function (response) {
        $('#edit-id').val(id);
        $('#edit-fullname').val(response.fullname);
        $('#edit-username').val(response.username);
        $('#edit-password').val(response.password);
        $('#edit-phone').val(response.phone);
        $('#edit-email').val(response.email);
      },
      error: function (xhr, status, error) {
        console.log('Error: ' + error);
      }
    });
  }

  function deleteUser(id){
    $.ajax({
      url: 'DeleteUser/' + id,
      type: 'DELETE',
      data: {
          _token: '{{ csrf_token() }}'
        },
      success: function (response) {
        location.reload();
      },
      error: function (xhr, status, error) {
        console.log('Error: ' + error);
      }
    });
  }
  
</script>
@endsection