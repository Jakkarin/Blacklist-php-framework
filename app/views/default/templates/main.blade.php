<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <title>@layout('title')</title>
    </head>
    <body>
        @layout('contents')
        <footer>
            {!! var_dump((microtime(true) - $_SERVER['REQUEST_TIME_FLOAT']), (memory_get_usage(true) / 1048576)) !!}
        </footer>
    </body>
</html>
