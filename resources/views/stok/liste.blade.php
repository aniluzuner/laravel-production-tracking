@extends('layouts.main')

@section('title', 'Yazaroğlu Elektronik')

@section('content')

<div class="content-wrapper">
  <!-- /.content-header -->

  <!-- Main content -->
  <section class="content">
    <div class="row">
      @if (session('msgsuccess'))
        <div class="col-lg-12">
          <div class="alert alert-success"> {{session('msgsuccess')}} </div>
        </div>
      @endif
      
      @if (session('msgerror'))
        <div class="col-lg-12">
          <div class="alert alert-danger"> {{session('msgerror')}} </div>
        </div>
      @endif

      <div class="col-lg-12">
        <div class="box box-danger">
          <div class="box-header with-border">
            <h3 class="box-title">Stok Oluştur</h3>
          </div>
          <div class="box-body">
            <form action="{{ route('stok.create') }}" method="POST" autocomplete="off">
              @csrf
              <div class="col-xs-1">
                <div class="form-group">
                  <label for="sn">SN</label>
                  <input type="text" name="sn" class="form-control" id="sn" placeholder="SN">
                </div>
              </div>
              <div class="col-xs-2">
                <div class="form-group">
                  <label for="mps">MPS</label>
                  <input type="text" name="mps" class="form-control" id="mps" placeholder="MPS">
                </div>
              </div>
              <div class="col-xs-3">
                <div class="form-group">
                  <label for="stokkod">Stok Kodu</label>
                  <input type="text" name="stokkod" class="form-control" id="stokkod" placeholder="Stok Kodu">
                </div>
              </div>
              <div class="col-xs-4">
                <div class="form-group">
                  <label for="cinsi">Cinsi</label>
                  <input type="text" name="cinsi" class="form-control" data-validation="required" data-validation-error-msg="Cinsi Alanı Boş Bırakılamaz" id="cinsi" placeholder="Cinsi">
                </div>
              </div>
              <div class="col-xs-2">
                <div class="form-group">
                  <label for="adedi">Adedi</label>
                  <input type="text" name="adedi" class="form-control" data-validation="required number" data-validation-allowing="range[1;9999999]" data-validation-error-msg="Hatalı Adet Girişi" id="adedi" placeholder="Adedi">
                </div>
              </div>
            </form>
          </div>
          <div class="box-footer">
            <button type="submit" class="btn btn-primary">Kaydet</button>
          </div>
          </form>
        </div>
      </div>
    </div>
      <div class="row">
          <div class="col-lg-12">
              <div class="box box-danger">
                  <div class="box-header with-border">
                      <h3 style="font-size:24px; margin:10px 0;">Stok Listesi</h3>
                  </div>
                  <div class="box-body" style="font-size: 18px;">
                      <div class="container-flex" style="margin-bottom: 15px;">
                          <div class="row">
                              <div class="col-lg-4">
                                  <select name="satirAdeti" id="satirAdeti" onChange="listeGuncelle()" style="padding:5px; border-radius: 5px; margin-right: 5px;">
                                      <option value="10" selected>10</option>
                                      <option value="25">25</option>
                                      <option value="50">50</option>
                                      <option value="100">100</option>
                                      <option value="250">250</option>
                                  </select>
                                  <span>&nbsp;Adet satır görüntüleniyor</span>
                              </div>
                              <div class="col-lg-4">
                                  <input type="text" name="search" id="search" class="form-control input-lg" placeholder="Aramak için en az 3 karakter giriniz" style="padding: 5px 10px; border-radius: 5px; margin-left: auto;">
                              </div>
                              <div class="col-lg-4" style="display: flex; justify-content: end; align-items: center;">
                                  <span>Sırala &nbsp;&nbsp;</span>
                                  <select name="sirala" id="sirala" onChange="listeGuncelle()" style="padding: 5px 10px; border-radius: 5px;">
                                      <option value="kalan-azalan" selected>Kalan azalan</option>
                                      <option value="kalan-artan">Kalan artan</option>
                                      <option value="created_at-azalan">En yeni tarih</option>
                                      <option value="created_at-artan">En eski tarih</option>
                                      <option value="adedi-azalan">Adet azalan</option>
                                      <option value="adedi-artan">Adet artan</option>
                                  </select>
                              </div>
                              <div class="col-lg-12" style="margin: 20px 0px;">
                                  <label style="margin-right: 15px; font-size: 24px; font-weight: 500;">
                                      <input type="checkbox" onClick="yilEkle(2024)"> 2024
                                  </label>
                                  <label style="margin-right: 15px; font-size: 24px; font-weight: 500;">
                                      <input type="checkbox" onClick="yilEkle(2023)"> 2023
                                  </label>
                                  <label style="margin-right: 15px; font-size: 24px; font-weight: 500;">
                                      <input type="checkbox" onClick="yilEkle(2022)"> 2022
                                  </label>
                                  <label style="margin-right: 15px; font-size: 24px; font-weight: 500;">
                                      <input type="checkbox" onClick="yilEkle(2021)"> 2021
                                  </label>
                                  <label style="margin-right: 15px; font-size: 24px; font-weight: 500;">
                                      <input type="checkbox" onClick="yilEkle(2020)"> 2020
                                  </label>
                                  <label style="margin-right: 15px; font-size: 24px; font-weight: 500;">
                                      <input type="checkbox" onClick="yilEkle(2019)"> 2019
                                  </label>
                                  <label style="margin-right: 15px; font-size: 24px; font-weight: 500;">
                                      <input type="checkbox" onClick="yilEkle(2018)"> 2018
                                  </label>
                              </div>
                          </div>
                      </div>
                      
                      
                      <table id="stok-tablo" class="table">
                          <thead>
                          <tr>
                              <th>Giriş Tarihi</th>
                              <th>SN</th>
                              <th>MPS</th>
                              <th>Stok Kodu</th>
                              <th>Cinsi</th>
                              <th>Adedi</th>
                              <th>Kalan</th>
                              <th>İşlemler</th>
                          </tr>
                          </thead>
                          <tbody>
                              
                          </tbody>
                      </table>
                      <nav style="display:flex; justify-content: center;">
                          <ul id="pagination" class="pagination"></ul>
                      </nav>
          </div>
        </div>
      </div>
    </div>
  </section>
  <!-- /.content -->
</div>
<!-- /.content-wrapper -->

<script>
  function formatDate(dateStr) {
        const parts = dateStr.split('-');
        const formattedDate = `${parts[2]}/${parts[1]}/${parts[0]}`;
        return formattedDate;
    }       

    var sayfa = 1;
    var sayfaMax;

    function setSayfa(yeniSayfa){
        sayfa = yeniSayfa;
        listeGuncelle();
    }

    function setPageButtons(){
        document.getElementById('pagination').innerHTML = "";

        if (sayfa > 3)
            $('#pagination').append(`
                <li class="page-item"><a class="page-link" id ="pageButton0" href="#" onClick="setSayfa(1)">1</a></li>
            `);

        if (sayfa != 1)
            $('#pagination').append(`
                <li class="page-item">
                    <a class="page-link" href="#" onClick="setSayfa(${sayfa-1})">&larr;</a>
                </li>`);

        var pageButtons = [];

        if (sayfa <= 3)
            pageButtons = [1,2,3,4,5];
        else if (sayfa >= sayfaMax - 2)
            pageButtons = [sayfaMax - 4, sayfaMax - 3, sayfaMax - 2, sayfaMax - 1, sayfaMax];
        else
            pageButtons = [sayfa - 2, sayfa - 1, sayfa, sayfa + 1, sayfa + 2];

        $('#pagination').append(`
            <li class="page-item"><a class="page-link" id ="pageButton0" href="#" onClick="setSayfa(${pageButtons[0]})">${pageButtons[0]}</a></li>
            <li class="page-item"><a class="page-link" id ="pageButton1" href="#" onClick="setSayfa(${pageButtons[1]})">${pageButtons[1]}</a></li>
            <li class="page-item"><a class="page-link" id ="pageButton2" href="#" onClick="setSayfa(${pageButtons[2]})">${pageButtons[2]}</a></li>
            <li class="page-item"><a class="page-link" id ="pageButton3" href="#" onClick="setSayfa(${pageButtons[3]})">${pageButtons[3]}</a></li>
            <li class="page-item"><a class="page-link" id ="pageButton4" href="#" onClick="setSayfa(${pageButtons[4]})">${pageButtons[4]}</a></li>
        `);

        if (sayfa != sayfaMax)
            $('#pagination').append(`
                <li class="page-item">
                    <a class="page-link" href="#" onClick="setSayfa(${sayfa+1})">&rarr;</a>
                </li>`);

        if (sayfa < sayfaMax - 2)
            $('#pagination').append(`
                <li class="page-item"><a class="page-link" href="#" onClick="setSayfa(${sayfaMax})">${sayfaMax}</a></li>
            `);

        pageButtons.forEach(function (value, i) {
            if (value == sayfa)
                $('#pageButton' + i).css("background-color", "#e8e8e8");
        });
    }
    
    var yillar = [];
    function yilEkle(yil){
        if (yillar.includes(yil))
            yillar = yillar.filter(item => item != yil);
        else
            yillar.push(yil);

        listeGuncelle();
    }

    function listeGuncelle() {
        document.querySelector('#stok-tablo tbody').innerHTML = "";
        document.getElementById('search').value = "";

        const satirAdeti = parseInt(document.getElementById('satirAdeti').value);
        const [sirala, siralaYon] = document.getElementById('sirala').value.split('-');

        $.ajax({
            url: 'getListe',
            type: 'GET',
            data: {
                sirala: sirala,
                siralaYon: siralaYon,
                sayfa: sayfa,
                satirAdeti: satirAdeti,
                yillar: yillar
            },
            success: function (response) {
                try {
                    const jsonData = JSON.parse(response);
                    const tableBody = $('#stok-tablo tbody');

                    sayfaMax = jsonData[jsonData.length - 1];
                    jsonData.pop();

                    jsonData.forEach(item => {
                        const row = $('<tr></tr>');
                        row.append(`<td>${formatDate(item.created_at)}</td>`);
                        row.append(`<td>${item.sn}</td>`);
                        row.append(`<td>${item.mps}</td>`);
                        row.append(`<td>${item.stokkod}</td>`);
                        row.append(`<td>${item.cinsi}</td>`);
                        row.append(`<td>${item.adedi}</td>`);
                        row.append(`<td>${item.kalan}</td>`);
                        row.append(`
                                    <td>
                                        <button type="button" class="btn btn-success" onClick="showInspectModal(${item.id})" id="inspect">İncele</button>
                                        <button type="button" class="btn btn-primary" onClick="showCikisModal(${item.id})">Çıkış Yap</button>
                                        <button type="button" class="btn btn-info" onClick="showNotModal(${item.id})">Not</button>
                                        <button type="button" class="btn btn-danger" onClick="hideStok(${item.id})">Gizle</button>
                                        <a href="edit/${item.id}"><button type="button" class="btn btn-warning">Düzenle</button></a>
                                    </td>
                                `);
                        tableBody.append(row);
                    });
                    console.log(jsonData);
                    setPageButtons();
                } catch (error) {
                    console.error("JSON parse error:", error);
                }
            },
            error: function (error) {
                console.error(error);
            }
        });
    }

    var searchReq;

    $("#search").keyup(function() {
        if (searchReq) 
            searchReq.abort();

        document.querySelector('#stok-tablo tbody').innerHTML = "";
        document.getElementById('pagination').innerHTML = "";

        const search = document.getElementById('search').value;

        if (search == ""){
            listeGuncelle();
            return;
        }

        if (search.length <= 2)
            return;
            
        const [sirala, siralaYon] = document.getElementById('sirala').value.split('-');

        searchReq = $.ajax({
            url: 'stokSearch',
            type: 'GET',
            data: {
                sirala: sirala,
                siralaYon: siralaYon,
                listeMi: "true",
                search: search,
                yillar: yillar
            },
            success: function (response) {
                try {
                    const jsonData = JSON.parse(response);
                    const tableBody = $('#stok-tablo tbody');

                    jsonData.forEach(item => {
                        const row = $('<tr></tr>');
                        row.append(`<td>${formatDate(item.created_at)}</td>`);
                        row.append(`<td>${item.sn}</td>`);
                        row.append(`<td>${item.mps}</td>`);
                        row.append(`<td>${item.stokkod}</td>`);
                        row.append(`<td>${item.cinsi}</td>`);
                        row.append(`<td>${item.adedi}</td>`);
                        row.append(`<td>${item.kalan}</td>`);
                        row.append(`
                                    <td>
                                        <button type="button" class="btn btn-success" onClick="showInspectModal(${item.id})" id="inspect">İncele</button>
                                        <button type="button" class="btn btn-primary" onClick="showCikisModal(${item.id})">Çıkış Yap</button>
                                        <button type="button" class="btn btn-info" onClick="showNotModal(${item.id})">Not</button>
                                        <button type="button" class="btn btn-danger" onClick="hideStok(${item.id})">Gizle</button>
                                        <a href="edit/${item.id}"><button type="button" class="btn btn-warning">Düzenle</button></a>
                                    </td>
                                `);
                        tableBody.append(row);
                    });
                    console.log(jsonData);
                } catch (error) {
                    console.error("JSON parse error:", error);
                }
            }
        });
    });

    var getCins;

    $("#stokkod").keyup(function() {
        if (getCins) 
            getCins.abort();

        if ($("#stokkod").val().length <= 2)
            return;

        getCins = $.ajax({
            url: 'getCins',
            type: 'GET',
            data: {
                stokkod: $("#stokkod").val()
            },
            success: function (response) {
                try {
                    const jsonData = JSON.parse(response);
                   
                    if (jsonData != null)
                        $('#cinsi').val(jsonData['cinsi']);
                    else
                        $('#cinsi').val('');
                   
                } catch (error) {
                    console.error("JSON parse error:", error);
                }
            }
        });
    });

    $(document).ready(function () {
        listeGuncelle();
    });
  
</script>
<div class="modal fade" id="stokdetay" style="display: none;">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">×</span></button>
				<h4 class="modal-title">Stok Detayı</h4>
			</div>
			<div class="modal-body">
				<table class="table" id="stok_detail">
					<thead>
					<th>Çıkış Tarihi</th>
					<th>Miktar</th>
					<th>İşlemler</th>
					</thead>
					<tbody>

					</tbody>
				</table>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-default" data-dismiss="modal">Kapat</button>
			</div>
		</div>
		<!-- /.modal-content -->
	</div>
	<!-- /.modal-dialog -->
</div>


<div class="modal fade" id="stokcikis" style="display: none;">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">×</span></button>
				<h4 class="modal-title">Stok Detayı</h4>
			</div>
			<div class="modal-body">
				<div class="box box-danger">
					<form id="stok_cikis_form">
						<div class="box-body">
							<div class="col-xs-12">
								<div class="form-group">
									<label for="cikis">Çıkış Yapılacak Miktar</label>
                                    <div style="display: flex;">
                                        <input type="text" id="cikis" name="cikis_miktar" class="form-control" data-validation="required number" data-validation-allowing="range[1;9999999]" data-validation-error-msg="Hatalı Adet Girişi" placeholder="Çıkış Yapılacak Miktar">
                                        <button type="button" class="btn btn-danger" id="tumu">Tümü</button>
                                    </div>
									<input type="hidden" name="stok_id_cikis" id="stok_id_cikis">
                                    <script>
                                        $('#tumu').on("click", function(){
                                            $.ajax({
                                                url: 'getKalan',
                                                type: 'GET',
                                                data: {
                                                    stok_id: $("#stok_id_cikis").val()
                                                },
                                                success: function (response) {
                                                    try {
                                                        const jsonData = JSON.parse(response);
                                                        $('#cikis').val(jsonData);
                                                    
                                                    } catch (error) {
                                                        console.error("JSON parse error:", error);
                                                    }
                                                }
                                            });
                                        });
                                    </script>
								</div>
							</div>
						</div>
						<div class="box-footer">
							<button type="submit" class="btn btn-primary">Kaydet</button>
						</div>
					</form>
				</div>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-default" data-dismiss="modal">Kapat</button>
			</div>
		</div>
		<!-- /.modal-content -->
	</div>
	<!-- /.modal-dialog -->
</div>

<div class="modal fade" id="stoknot" style="display: none;">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">×</span></button>
				<h4 class="modal-title">Stok Notu</h4>
			</div>
			<div class="modal-body">
				<div class="box box-danger">
					<form id="stoknotform">
						<div class="box-body">
							<div class="col-xs-12">
								<div class="form-group">
									<label>Stok Notu</label>
									<textarea class="form-control" rows="3" name="stok_notu" id="stoknotarea" style="overflow:auto;resize:none" placeholder="Stok Notu"></textarea>
									<input type="hidden" name="stok_id" id="stok_id">
								</div>
							</div>
						</div>
						<div class="box-footer">
							<button type="submit" class="btn btn-primary">Kaydet</button>
						</div>
					</form>
				</div>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-default" data-dismiss="modal">Kapat</button>
			</div>
		</div>
		<!-- /.modal-content -->
	</div>
	<!-- /.modal-dialog -->
</div>
@endsection