<?php namespace systems;

class View
{
    public static function make($view, $data = array(), $template = null)
    {
        if (empty($template))
            $template = Config::get('app')['DefaultTemplate'];
        $viewPath = APP_PATH . 'views/' . $template . '/' . $view . '.blade.php';
        $cahcePath = APP_PATH . 'caches/views/' . md5($template . $view) . '.cache';
        $viewTime = filemtime($viewPath);
        if ( ! file_exists($cahcePath))
            self::makeCache($view, $viewPath, $cahcePath, $viewTime);
        else {
            $cacheTime = filemtime($cahcePath);
            if ($viewTime !== $cacheTime)
                self::makeCache($view, $viewPath, $cahcePath, $viewTime);
        } return self::render($cahcePath, $data);
    }

    private static function makeCache($viewName, $viewPath ,$cahcePath, $viewTime)
    {
        $viewContent = file_get_contents($viewPath);
        if (preg_match('/@extends\(\'([A-z0-9.]+)\'\)/', $viewContent, $match)) {
            $extendContent = file_get_contents(str_replace($viewName, str_replace('.', '/', $match[1]), $viewPath));
            if (preg_match_all('/@layout\(\'([A-z0-9]+)\'\)/', $extendContent, $match, PREG_SET_ORDER)) {
                foreach ($match as $layout) {
                    $pattern = '/@section\(\\\'' . $layout[1] . '\\\'\)\n?((.\n?)*)@endsection/';
                    if (preg_match($pattern, $viewContent, $match))
                        $extendContent = str_replace($layout[0], rtrim($match[1], PHP_EOL), $extendContent);
                }
            } if (preg_match('/@layout/', $extendContent))
                $extendContent = preg_replace('/@layout\((.+)?\)/', '', $extendContent);
        } else $extendContent = $viewContent;
        if (preg_match_all('/@include\(\'([A-z0-9.]+)\'\)/', $extendContent, $match, PREG_SET_ORDER)) {
            foreach ($match as $view)
                $extendContent = str_replace($view[0], file_get_contents(str_replace($viewName . '.blade', $view[1] . '.blade', $viewPath)), $extendContent);
        } if (preg_match('/@widgets/', $extendContent)) {
            $extendContent = preg_replace('/@widgets\((.*)\)/', '<?php systems\Widget::load($1); ?>', $extendContent);
        } if (preg_match('/\{\!\!/', $extendContent)) {
            $extendContent = str_replace('{!!', '<?php', $extendContent);
            $extendContent = str_replace('!!}', ';?>', $extendContent);
        } if (preg_match('/\{\{\{/', $extendContent)) {
            $extendContent = str_replace('{{{', '<?php echo', $extendContent);
            $extendContent = str_replace('}}}', ';?>', $extendContent);
        } if (preg_match('/@foreach/', $extendContent)) {
            $extendContent = preg_replace('/@foreach\((.*)\)/', '<?php foreach($1): ?>', $extendContent);
            $extendContent = str_replace('@endforeach', '<?php endforeach; ?>', $extendContent);
        } if (preg_match('/@for/', $extendContent)) {
            $extendContent = preg_replace('/@for\((.*)\)/', '<?php for($1): ?>', $extendContent);
            $extendContent = str_replace('@endfor', '<?php endfor; ?>', $extendContent);
        } if (preg_match('/@while/', $extendContent)) {
            $extendContent = preg_replace('/@while\((.*)\)/', '<?php while($1): ?>', $extendContent);
            $extendContent = str_replace('@endwhile', '<?php endwhile; ?>', $extendContent);
        } if (preg_match('/@do/', $extendContent)) {
            $extendContent = str_replace('@do', '<?php do { ?>', $extendContent);
            $extendContent = preg_replace('/@enddo\((.*)\)/', '<?php } while($1); ?>', $extendContent);
        } if (file_put_contents($cahcePath, $extendContent, LOCK_EX))
            touch($cahcePath, $viewTime);
    }

    private static function render($viewPath, $dataArray)
    {
        foreach ($dataArray as $key => $value)
            $$key = $value;
        include $viewPath;
    }
}
