<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Verifikasi Pendaftaran Akun Ecommerce Ismail</title>
</head>
<body>
    <h2>Halo, {{ $customer->name }}</h2>
    <p>Terima kasih telah melakukan transaksi pada aplikasi kami, berikut password anda:<br>
        <span style="font-size: 20px; font-weight: bold;">{{ $password }}</span>
    </p>
    <p>Dan jangan lupa untuk melakukan verifikasi pendaftaran
        <a href="{{ route('customer.verify', $customer->activate_token) }}">DISINI</a>
    </p>
</body>
</html>