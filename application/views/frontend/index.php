<!DOCTYPE html>
<html lang="en" dir="ltr">
<head>
  <meta charset="utf-8">
  <title>IT Kompentensi - PT. Minori</title>
  <link href="https://minori.co.id/img/favicon.png" rel="icon">
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap-theme.min.css" integrity="sha384-rHyoN1iRsVXV4nD0JutlnGaslCJuC7uwjduW9SVrLvRYooPp2bWYgmgJQIXwl/Sp" crossorigin="anonymous">
  <script src="https://code.jquery.com/jquery-3.6.0.js" integrity="sha256-H+K7U5CnXl1h5ywQfKtSj8PCmoN9aaq30gDh27Xc0jk=" crossorigin="anonymous"></script>
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js" integrity="sha384-Tc5IQib027qvyjSMfHjOMaLkfuWVxZxUPnCJA7l2mCWNIpG9mGCD8wGNIcPD7Txa" crossorigin="anonymous"></script>
  <!-- datatable -->
  <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.11.4/css/jquery.dataTables.min.css" media="screen, projection">
  <script src="https://cdn.datatables.net/1.11.4/js/jquery.dataTables.min.js" importance="low"></script>
  <!-- datatable -->
  <style type="text/css">
    a {
      cursor: pointer;
    }
  </style>
</head>
<body>
  <div class="container"><br>
    <h2>Test Kompetensi IT PT.Minori - 15 Februari 2022</h2> <hr>
    <ul class="nav nav-tabs" role="tablist">
      <li role="presentation" class="active"><a href="#karyawan" aria-controls="karyawan" role="tab" data-toggle="tab">Data Karyawan</a></li>
      <li role="presentation"><a href="#training" aria-controls="training" role="tab" data-toggle="tab">Data Training</a></li>
      <li role="presentation"><a href="#karyawantraining" aria-controls="karyawantraining" role="tab" data-toggle="tab">Data Karyawan Training</a></li>
    </ul>
    <div class="tab-content">
      <!-- tab karyawan -->
      <div role="tabpanel" class="tab-pane active" id="karyawan"><br>
        <button class="btn btn-sm btn-flat btn-primary" data-toggle="modal" data-target="#modal-karyawan" data-backdrop="static" data-keyboard="false">Tambah Karyawan</button> <br><br>
        <div class="dt-responsive table-responsive">
          <table id="table-karyawan" class="table nowrap" style="width: 100%;">
            <thead>
              <tr>
                <th>No.</th>
                <th>NIP</th>
                <th>Nama</th>
                <th>Jabatan</th>
                <th>Tanggal</th>
                <th>Opsi</th>
              </tr>
            </thead>
            <tfoot>
              <tr>
                <th>No.</th>
                <th>NIP</th>
                <th>Nama</th>
                <th>Jabatan</th>
                <th>Tanggal</th>
                <th>Opsi</th>
              </tr>
            </tfoot>
          </table>
        </div>
      </div>
      <!-- tab karyawan -->
      <div role="tabpanel" class="tab-pane" id="training">...</div>
      <div role="tabpanel" class="tab-pane" id="karyawantraining">...</div>
    </div>
    <!-- modals -->
    <div class="modal fade modal-flex" id="modal-karyawan" tabindex="-1" role="dialog">
      <div class="modal-dialog" role="document">
        <div class="modal-content">
          <div class="modal-header" style="background-color: #337ab7 !important;">
            <h4 class="modal-title" style="color: white;">Form Karyawan</h4>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <div class="modal-body">
            <form id="form-karyawan" method="post" autocomplete="false" accept-charset="utf-8">
              <input type="hidden" name="id" id="id">
              <div class="form-group row">
                <label class="col-sm-2 col-form-label"> NIP </label>
                <div class="col-sm-5">
                  <input type="text"name="nip"id="nip"class="form-control"required="1"placeholder="nip karyawan"pattern="[0-9]{10,10}"minlength="10"maxlength="10"data-toggle="tooltip"data-placement="top"title="nip karyawan" autocomplete="off" autofocus/>
                </div>
                <div class="col-sm-5">
                  <input type="text"name="nama"id="nama"class="form-control"required="1"placeholder="nama karyawan"pattern="[a-zA-Z\s]{3,35}"minlength="2"maxlength="35"data-toggle="tooltip"data-placement="top"title="nama karyawan" autocomplete="off"/>
                </div>
              </div>
              <div class="form-group row">
                <label class="col-sm-2 col-form-label"> Jabatan </label>
                <div class="col-sm-10">
                  <input type="text"name="jabatan"id="jabatan"class="form-control"required="1"placeholder="jabatan karyawan"pattern="[a-zA-Z\s]{3,35}"minlength="2"maxlength="35"data-toggle="tooltip"data-placement="top"title="jabatan karyawan" autocomplete="off"/>
                </div>
              </div>
              <div class="form-group row">
                <div class="col-sm-10">
                  <button type="submit" hidden="1" id="sbtkaryawan" value="1"></button>
                </div>
              </div>
            </form>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            <button type="button" class="btn btn-primary" onclick="$('#sbtkaryawan').click()">Save changes</button>
          </div>
        </div>
      </div>
    </div>
    <!-- modals -->
    <center>
      <small><a href="https://linkedin.com/in/hfzrmd" target="_blank">Copyright @ 2022. By Hafiz Ramadhan</a></small>
    </center>
  </div>
</body>
<script type="text/javascript">
  $(document).ready(function() {
    function ReloadTable(id){
      id.ajax.reload();
    }
    let table;
    table = $("#table-karyawan").DataTable({
      serverside: true,
      ajax: {
        type: "post",
        url: "karyawan/data",
      },
      language: {
        zeroRecords: "<center> Data tidak ditemukan </center>",
      },
      responsive: "true",
    });
    $("#form-karyawan").submit(function(event) {
      event.preventDefault();
      let url, act;
      const data = $(this).serialize(), id = $("#id").val(); 
      id ? url = "karyawan/edit" : url = "karyawan/simpan";
      id ? act = 'edit' : act = 'simpan';
      if (confirm("Apa data yang anda masukan sudah benar ?")) {
        $.post(url, data).done((res,xhr,status) => {
          alert(`Berhasil ${act} data karyawan`);
          ReloadTable(table);
        }).fail((xhr,status,err) => {
          alert(`Gagal ${act} data karyawan`);
          ReloadTable(table);
        })
      }
    });
    $("#table-karyawan").on('click', '#edit', function(event) {
      event.preventDefault();
      const id = $(this).data('id');
      $.post('karyawan/id', {id: id}).done((res,xhr,status) => {
        $("#id").val(res.data.id);
        $("#nip").val(res.data.nip);
        $("#nama").val(res.data.nama);
        $("#jabatan").val(res.data.jabatan);
        $("#modal-karyawan").modal('show');
      })
    });
  });
</script>
</html>
