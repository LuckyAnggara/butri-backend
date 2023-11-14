<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Laravel Pagination Demo</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
</head>

<body>
    <div class="container mt-5">

        <h5>Realisasi Per Jenis Kegiatan</h5>

        <table class="table table-bordered mb-5">
            <thead>
                <tr class="table-success">
                    <th>#</th>
                    <th>Kode</th>
                    <th>Kegiatan</th>
                    <th>Unit</th>
                    <th>Pagu</th>
                    <th>Realisasi</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($realisasiKegiatan as $kegiatan)
                <tr>
                    <td class="lh-1">{{$loop->iteration}}</td>
                    <td class="lh-1">{{$kegiatan->kode}}</td>
                    <td class="lh-1">{{$kegiatan->name}}</td>
                    <td class="lh-1">{{$kegiatan->group->name}}</td>
                    <td class="lh-1">{{$kegiatan->pagu}}</td>
                    <td class="lh-1">{{$kegiatan->realisasi_saat_ini}}</td>
                </tr>
                @endforeach

            </tbody>
        </table>

        <h5>Realisasi Per Jenis Belanja</h5>
        <table class="table table-bordered mb-5">
            <thead>
                <tr class="table-success">
                    <th>#</th>
                    <th>Kegiatan</th>
                    <th>Pagu</th>
                    <th>Realisasi</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($realisasiBelanja as $belanja)
                <tr>
                    <td class="lh-1">{{$loop->iteration}}</td>
                    <td class="lh-1">{{$belanja->name}}</td>
                    <td class="lh-1">{{$belanja->pagu}}</td>
                    <td class="lh-1">{{$belanja->realisasi_saat_ini}}</td>
                </tr>
                @endforeach

            </tbody>
        </table>

        <h5>Indikator Kinerja Utama</h5>
        <table class="table table-bordered mb-5">
            <thead>
                <tr class="table-success">
                    <th>#</th>
                    <th>Indikator</th>
                    <th>Target</th>
                    <th>Realisasi</th>
                    <th>Analisa</th>
                    <th>Kendala</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($realisasiIKU as $iku)
                <tr>
                    <td class="lh-1">{{$loop->iteration}}</td>
                    <td class="lh-1">{{$iku->name}}</td>
                    <td class="lh-1">{{$iku->target}}</td>
                    <td class="lh-1">{!!$iku->realisasi->realisasi ?? '-' !!}</td>
                    <td class="lh-sm">{!! nl2br(e($iku->realisasi->analisa ?? '')) !!} </td>
                    <td class="lh-1">{!! $iku->realisasi->kendala ?? ''!!}</td>
                </tr>
                @endforeach

            </tbody>
        </table>


        @foreach ($groups as $group)

        <h5>Indikator Kinerja Kegiatan {{ $group->name }}</h5>
        <table class="table table-bordered mb-5">
            <thead>
                <tr class="table-success">
                    <th>#</th>
                    <th>Indikator</th>
                    <th>Target</th>
                    <th>Realisasi</th>
                    <th>Analisa</th>
                    <th>Kendala</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($group->ikk as $ikk)
                <tr>
                    <td class="lh-1">{{$loop->iteration}}</td>
                    <td class="lh-1">{{$ikk->name}}</td>
                    <td class="lh-1">{{$ikk->target}}</td>
                    <td class="lh-1">{!! nl2br(e($ikk->realisasi->realisasi ?? '-')) !!}</td>
                    <td class="lh-sm">{!! nl2br(e($ikk->realisasi->analisa ?? '')) !!} </td>
                    <td class="lh-1">{!! nl2br(e($ikk->realisasi->kendala ?? ''))!!}</td>
                </tr>
                @endforeach

            </tbody>
        </table>
        @endforeach

    </div>
</body>

</html>