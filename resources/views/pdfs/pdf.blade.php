<!doctype html>
<html lang="pl">
<head>

    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">


    <title>{{ __('pdf.labels.title') }}</title>
<style>

body { font-family: DejaVu Sans, sans-serif; }
h4 {
    margin: 0;
}
.w-full {
    width: 100%;
}
.w-half {
    width: 50%;
}
.margin-top {
    margin-top: 1.25rem;
}
.footer {
    font-size: 0.875rem;
    padding: 1rem;
    background-color: rgb(241 245 249);
}
table {
    width: 100%;
    border-spacing: 0;
}
table.products {
    font-size: 0.875rem;
}
table.products tr {
    background-color: rgb(96 165 250);
}
table.products th {
    color: #ffffff;
    padding: 0.5rem;
}
table tr.items {
    background-color: rgb(241 245 249);
}
table tr.items td {
    padding: 0.5rem;
}
.total {
    text-align: right;
    margin-top: 1rem;
    font-size: 0.875rem;
}

</style>


</head>
<body>
    @php $logo = public_path('logo.png'); @endphp
    @inlinedImage($logo)
    <table class="w-full">
        <tr>
            <td class="w-half">
                <img src="{{ asset('public/logo.png') }}" alt="Chmurexpol" width="200" />
            </td>
            <td class="w-half">                                     

            </td>
        </tr>
    </table>

 
    <div class="margin-top">
        <table class="products">
            <tr>
                <th>ID Pomiarów</th>
                <th>Nazwa urządzenia</th>
                <th>Data pomiaru</th>
                <th>Utworzono</th>
            </tr>
           
                @foreach($measurements as $measurement)
                <tr class="items">
                {{ $n +=1 }}
                    <td>
                        {{ $measurement['id'] }}
                    </td>
                    <td>
                        {{ $measurement['device_name'] }}
                    </td>
                    <td>
                        {{ $measurement['measurements_date'] }}
                    </td>
                    <td>
                        {{ $measurement['created_at'] }}
                    </td>
                </tr>
                @endforeach
           
        </table>
    </div>
 
    <div class="total">
{{ __('pdf.labels.all_measurements') }}: {{ $n }}
    </div>
 
    <div class="footer margin-top">
        <div>Thank you</div>
    </div>
</body>
</html>