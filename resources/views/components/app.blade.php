{{-- <html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>Laravel</title>

    </head>
    <div class="container">
        @yeild('content')


    </div>

    @yeild('footer')
</html> --}}

<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>Laravel</title>

    </head>
    <body>
        <nav>
            <a href="">Home</a>
            <a href="">About</a>
            <a href="">Contact</a>
        </nav> 

        {{$slot}}
    </body>
</html>