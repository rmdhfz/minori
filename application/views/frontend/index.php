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
    <center>
      <small><a href="https://linkedin.com/in/hfzrmd">Copyright @ 2022. By Hafiz Ramadhan</a></small>
    </center>
  </div>
</body>
<script type="text/javascript">
  $(document).ready(function() {
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
  });
</script>
</html>
