<!doctype html>
<html lang="en">
    <head>
        <title>Laporan Analisis</title>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
        <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css" integrity="sha384-Vkoo8x4CGsO3+Hhxv8T/Q5PaXtkKtu6ug5TOeNV6gBiFeWPGFN9MuhOf23Q9Ifjh" crossorigin="anonymous">
    </head>
    <style>
        body {
            font-family: "Segoe UI", sans-serif;
        }
        header {
            position: fixed;
            top: -60px;
            left: 0px;
            right: 0px;
            height: 50px;

            /** Extra personal styles **/
            /*background-color: #03a9f4;*/
            /*color: white;*/
            text-align: center;
            line-height: 45px;
        }

        footer {
            position: fixed;
            bottom: -60px;
            left: 0px;
            right: 0px;
            height: 50px;

            /** Extra personal styles **/
            /*background-color: #03a9f4;*/
            /*color: black;*/
            /*text-align: center;*/
            line-height: 30px;
            font-size: 12px;
        }
    </style>
    <body>
        <header>
            Wish Enterprise
        </header>

        <footer>
            @Copyright {{date("Y")}}
        </footer>

        <h3>Laporan Analisis Data {{$who}} terhadap {{$terhadap}}</h3>
        <div>
            <p>Persentase kelengkapan berkas dari {{$tglawal}} sampai dengan {{$tglakhir}}, digambarkan pada tabel berikut: </p>
            <table class="table table-sm table-striped">
                <thead>
                    <tr>
                        <th rowspan="2" style="vertical-align: middle">{{$who}}</th>
                        <th colspan="3" style="text-align: center">Bulan</th>

                    </tr>
                    <tr>
                        @foreach($bulan as $bu) {
                        <th>{{$bu}}</th>
                        @endforeach
                    </tr>
                </thead>
                <tbody>
                @for ($i = 0; $i < count($whodata); $i++)
                    <tr>
                        <td>{{$whodata[$i]->nama}}</td>
                        @for($j = 0; $j < count($bulan); $j++)
                            <td>{{$persentase[$i][$j]}}%</td>
                        @endfor
                    </tr>
                @endfor
                </tbody>
            </table>
        </div>
    </body>
</html>
