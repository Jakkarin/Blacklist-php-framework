<?php namespace systems;

class View
{
    public static function make($_view, $_data = array(), $_template = null)
    {
        if (empty($_template)) {
            $_config = Config::get('app');
            $_template = $_config['template'];
        } $_viewPath = APP_PATH . 'views/' . $_template . '/' . $_view . '.blade.php';
        $_cahcePath = APP_PATH . 'caches/views/' . md5($_template . $_view);
        $_viewTime = filemtime($_viewPath);
        if ( ! file_exists($_cahcePath))
            self::makeCache($_view, $_viewPath, $_cahcePath, $_viewTime);
        else {
            $_cacheTime = filemtime($_cahcePath);
            if ($_viewTime !== $_cacheTime)
                self::makeCache($_view, $_viewPath, $_cahcePath, $_viewTime);
        } return self::render($_cahcePath, $_data);
    }

    private function makeCache($_viewName, $_viewPath ,$_cahcePath, $_viewTime)
    {
        $_viewContent = file_get_contents($_viewPath);
        if (preg_match('/@extends\(\'([A-z0-9.]+)\'\)/', $_viewContent, $_match)) {
            $_extendContent = file_get_contents(str_replace($_viewName, str_replace('.', '/', $_match[1]), $_viewPath));
            if (preg_match_all('/@layout\(\'([A-z0-9]+)\'\)/', $_extendContent, $_match, PREG_SET_ORDER)) {
                foreach ($_match as $_layout) {
                    $_pattern = '/@section\(\\\'' . $_layout[1] . '\\\'\)\n?((.\n?)*)@endsection/';
                    if (preg_match($_pattern, $_viewContent, $_match))
                        $_extendContent = str_replace($_layout[0], rtrim($_match[1], PHP_EOL), $_extendContent);
                }
            } if (preg_match('/@layout/', $_extendContent))
                $_extendContent = preg_replace('/@layout\((.+)?\)/', '', $_extendContent);
        } else $_extendContent = $_viewContent;
        if (preg_match_all('/@include\(\'([A-z0-9.]+)\'\)/', $_extendContent, $_match, PREG_SET_ORDER)) {
            foreach ($_match as $_view)
                $_extendContent = str_replace($_view[0], file_get_contents(str_replace($_viewName . '.blade', $_view[1] . '.blade', $_viewPath)), $_extendContent);
        } if (preg_match('/@widgets/', $_extendContent)) {
            $_extendContent = preg_replace('/@widgets\((.*)\)/', '<?php systems\Widget::load($1); ?>', $_extendContent);
        } if (preg_match('/\{\!\!/', $_extendContent)) {
            $_extendContent = str_replace('{!!', '<?php', $_extendContent);
            $_extendContent = str_replace('!!}', ';?>', $_extendContent);
        } if (preg_match('/\{\{\{/', $_extendContent)) {
            $_extendContent = str_replace('{{{', '<?php echo', $_extendContent);
            $_extendContent = str_replace('}}}', ';?>', $_extendContent);
        } if (preg_match('/@foreach/', $_extendContent)) {
            $_extendContent = preg_replace('/@foreach\((.*)\)/', '<?php foreach($1): ?>', $_extendContent);
            $_extendContent = str_replace('@endforeach', '<?php endforeach; ?>', $_extendContent);
        } if (preg_match('/@for/', $_extendContent)) {
            $_extendContent = preg_replace('/@for\((.*)\)/', '<?php for($1): ?>', $_extendContent);
            $_extendContent = str_replace('@endfor', '<?php endfor; ?>', $_extendContent);
        } if (preg_match('/@while/', $_extendContent)) {
            $_extendContent = preg_replace('/@while\((.*)\)/', '<?php while($1): ?>', $_extendContent);
            $_extendContent = str_replace('@endwhile', '<?php endwhile; ?>', $_extendContent);
        } if (preg_match('/@do/', $_extendContent)) {
            $_extendContent = str_replace('@do', '<?php do { ?>', $_extendContent);
            $_extendContent = preg_replace('/@enddo\((.*)\)/', '<?php } while($1); ?>', $_extendContent);
        } if (file_put_contents($_cahcePath, $_extendContent, LOCK_EX))
            touch($_cahcePath, $_viewTime);
    }

    private function render($_viewPath, $_dataArray)
    {
        foreach ($_dataArray as $_key => $_value)
            $$_key = $_value;
        include $_viewPath;
    }
}
