<?

 function g($var) {
     echo '<link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/highlight.js/9.0.0/styles/default.min.css">
                <script src="//cdnjs.cloudflare.com/ajax/libs/highlight.js/9.0.0/highlight.min.js"></script>
                <script>hljs.initHighlightingOnLoad();</script>';
     echo '<pre><code class="html" style="border: 1px solid black;">';
     if (is_array($var)) {
         print_r($var);
     } elseif (is_object($var)) {
         $class = get_class($var);
         Reflection::export(new ReflectionClass($class));
     } else {
         echo htmlspecialchars($var);
     }
     echo '</code></pre>';
 }

 function handLink($link) {
     $pieces = pathinfo($link);
     $id = (int) end(explode('/', $pieces['dirname']));
     $name = current(explode('.', $pieces['basename']));
     if (is_numeric($name) && preg_match('/^(0+)(\d+)$/', $name, $match)) {
         $cnt = strlen($match[0]);
         $newName = $match[1] . (int) ++$match[2];
         if (strlen($newName) > $cnt)
             $newName = substr($newName, 1);
         $pieces['basename'] = str_replace($name, $newName, $pieces['basename']);
     } else {
         $pieces['basename'] = str_replace($name, ++$name, $pieces['basename']);
     }
     $pieces['dirname'] = str_replace($id, ++$id, $pieces['dirname']);
     return (string) $pieces['dirname'] . '/' . $pieces['basename'];
 }

 if ($_SERVER['REQUEST_METHOD'] === 'POST') {
     if (filter_has_var(INPUT_POST, 'string'))
         $str = filter_input(INPUT_POST, 'string');
     if (filter_has_var(INPUT_POST, 'cnt'))
         $cnt = filter_input(INPUT_POST, 'cnt', FILTER_SANITIZE_NUMBER_INT);
     if (filter_has_var(INPUT_POST, 'href'))
         $href = filter_input(INPUT_POST, 'href', FILTER_SANITIZE_STRING);
     if (filter_has_var(INPUT_POST, 'src'))
         $src = filter_input(INPUT_POST, 'src', FILTER_SANITIZE_STRING);

     if (!empty($str) && is_string($str)) {
         $output = '';
         if (isset($href) && $href == 'on') {
             preg_match('/href=[\"\'](.*?)[\"\']/', $str, $matches);
             $h = $matches[1];
         }
         if (isset($src) && $src == 'on') {
             preg_match('/src=[\"\'](.*?)[\"\']/', $str, $matches);
             $s = $matches[1];
         }
         for ($i = 0; $i < $cnt; $i++) {
             $output .= $str . "\n";
             if (isset($h)) {
                 $str = str_replace($h, $h = handLink($h), $str);
             }
             if (isset($s)) {
                 $str = str_replace($s, $s = handLink($s), $str);
             }
         }
     }
 }
?>
<html>
    <head>
        <title>Генератор всего</title>
        <link rel="stylesheet" href="http://getbootstrap.com/dist/css/bootstrap.min.css">
        <script src="http://ajax.googleapis.com/ajax/libs/jquery/1/jquery.min.js"></script>
        <script src="http://getbootstrap.com/dist/js/bootstrap.min.js"></script>
    </head>
    <body>
        <div class="container-fluid" style="margin-top: 50px;">
            <div class="row">
                <div class="col-md-12">
                    <form action="<?= $_SERVER['REQUEST_URI'] ?>" method="post">
                        <div class="form-group">
                            <label for="string">Строка для генерации / ссылка на первый слайд</label>
                            <input type="text" name="string" id="string" class="form-control"/>
                        </div>
                        <div class="form-group">
                            <label for="cnt">Количество итераций</label>
                            <input type="text" name="cnt" id="cnt" class="form-control"/>
                        </div>
                        <div class="checkbox">
                            <label>
                                <input type="checkbox" name="href">
                                Инкрементировать атрибут href?
                            </label>
                        </div>
                        <div class="checkbox">
                            <label>
                                <input type="checkbox" name="src">
                                Инкрементировать атрибут src?
                            </label>
                        </div>
                        <hr />
                        <div class="checkbox">
                            <label>
                                <input type="checkbox" name="citylife">
                                Презентация «Жизнь города»
                            </label>
                        </div>
                        <input type="hidden" name="cityLifePresentation" />
                        <button type="submit" class="btn btn-default">Генерировать</button>
                    </form>
                    <?
                     if (isset($output) && !empty($output)) {
                         echo '<h3>Результат</h3>';
                         g($output);
                     }
                    ?>
                </div>
            </div>
        </div>
    </body>
</html>