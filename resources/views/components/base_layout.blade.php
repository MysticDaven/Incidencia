<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    @vite(["resources/css/app.css", "resources/js/app.js" ])
    <link href="{{ asset('img/web.png') }}" rel="shortcut icon" type="image/png">
    <title>Incidencia</title>    
</head>
<body>
    <x-header></x-header>        
        {{ $slot }}            
    <footer></footer>
</body>
</html>