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



        @if ($pointers['p1']==1)
            Średnie PM1: {{ number_format($delta_array['delta_pm1'],2,'.','') }}<br>
            Ilość pomiarów: {{  $delta_array['pm1_count'] }}<br>
            Maksymalne PM1: {{ number_format($min_max_array['max_pm1'],2,'.','') }}<br>
            Minimalne PM1: {{ number_format($min_max_array['min_pm1'],2,'.','') }}<br>

        @endif

        @if ($pointers['p2']==1)
            <hr class="my-2">
            Średnie PM2_5: {{ number_format($delta_array['delta_pm2_5'],2,'.','') }}<br>
            Ilość pomiarów: {{  $delta_array['pm2_5_count'] }}<br>
            Maksymalne PM2_5: {{ number_format($min_max_array['max_pm2_5'],2,'.','') }}<br>
            Minimalne PM2_5: {{ number_format($min_max_array['min_pm2_5'],2,'.','') }}<br>

        @endif

        @if ($pointers['p3']==1)
            <hr class="my-2">
            Średnie PM10: {{ number_format($delta_array['delta_pm10'],2,'.','') }}<br>
            Ilość pomiarów: {{  $delta_array['pm10_count'] }}<br>
            Maksymalne PM10: {{ number_format($min_max_array['max_pm10'],2,'.','') }}<br>
            Minimalne PM10: {{ number_format($min_max_array['min_pm10'],2,'.','') }}<br>

        @endif

        @if ($pointers['p4']==1)
            <hr class="my-2">
            Średnia wilgotność: {{ number_format($delta_array['delta_humidity'],2,'.','') }}<br>
            Ilość pomiarów: {{  $delta_array['humidity_count'] }}<br>
            Maksymalna wilgotność: {{ number_format($min_max_array['max_humidity'],2,'.','') }}<br>
            Minimalna wilgotność: {{ number_format($min_max_array['min_humidity'],2,'.','') }}<br>

        @endif

        @if ($pointers['p5']==1)
            <hr class="my-2">
            Średnie ciśnienie: {{ number_format($delta_array['delta_pressure'],2,'.','') }}<br>
            Ilość pomiarów: {{  $delta_array['pressure_count'] }}<br>
            Maksymalne ciśnienie: {{ number_format($min_max_array['max_pressure'],2,'.','') }}<br>
            Minimalne ciśnienie: {{ number_format($min_max_array['min_pressure'],2,'.','') }}<br>

        @endif

        @if ($pointers['p6']==1)
            <hr class="my-2">
            Średnia temperatura: {{ number_format($delta_array['delta_temperature'],2,'.','') }}<br>
            Ilość pomiarów: {{  $delta_array['temperature_count'] }}<br>
            Maksymalna temperatura: {{ number_format($min_max_array['max_temperature'],2,'.','') }}<br>
            Minimalna temperatura: {{ number_format($min_max_array['min_temperature'],2,'.','') }}<br>

        @endif   

      
    </div>
 

 
    <div class="footer margin-top">
     
    </div>
</body>
</html>