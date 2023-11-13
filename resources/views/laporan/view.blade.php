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
                    <td class="lh-1">{!!$ikk->realisasi->realisasi ?? '-' !!}</td>
                    <td class="lh-sm">{!! nl2br(e($ikk->realisasi->analisa ?? '')) !!} </td>
                    <td class="lh-1">{!! $ikk->realisasi->kendala ?? ''!!}</td>
                </tr>
                @endforeach

            </tbody>
        </table>
        @endforeach

    </div>
</body>

</html>