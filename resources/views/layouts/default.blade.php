<!DOCTYPE html>
<html>
    <head>
        <title>@yield('title', 'Sample App') - 魔剑客</title>
        <link rel="stylesheet" href="/css/app.css">
    </head>
    <body>
        @include('layouts._header')

        <div class="container">
            @include('layouts._messages')
            @yield('content')
            @include('layouts._footer')
        </div>    
    </body>
    <script src="/js/app.js"></script>
</html>