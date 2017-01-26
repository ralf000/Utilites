<?php
function g($var)
{
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

/**
 * @param string $data
 * @return string
 */
function dataHandler($data)
{
    $patterns = [
        '~(<p>&nbsp;</p>)|(<br clear="all" />)|(<div class="WordSection\d+">)|(</div>)~Uui',
        '~\s?(</a>)~Uui',
        '~<p><strong>(.*)</strong></p>~Uui',
        '~<h1>(.*)</h1>~uUi',
        '~(</h\d>)\r?\n\s*<p>([a-zа-я])(.*)</p>~uU',
        '~-</p>(\r?\n\s*)*<p>([a-zа-я])~uU',
        '~\r?\n\s*([a-zа-я])~Uu',
        '~([a-zа-я])</p>\r?\n\s*<p>([a-zа-я]|\d)~Uu',
        '~(target="_blank")|(<span style="text-decoration: underline;">)|(</span>)|(- )~Uui',
        '~<a~Uui',
        '~ {2,}~uUi',
        '~(\r?\n){2,}~uUi',
        '~(&nbsp;)~uUi',
        '~<a(.*)href="(.*)"(.*)>(.*)</a>.*<a.*href="\2".*>(.*)</a>~uUi',
        '~- ~'
    ];

    $replacements = [
        '',
        '$1 ',
        '<h3>$1</h3>',
        '<h3>$1</h3>',
        ' $2$3$1',
        '$1$2',
        '$1',
        '$1 $2',
        '',
        '<a target="_blank"',
        ' ',
        "\n",
        '',
        '<a $1 href="$2" $3>$4 $5</a>',
        ''
    ];

    return preg_replace($patterns, $replacements, $data);
}

/**
 * @param string $imgLink
 * @param string $data
 * @return string
 */
function imageHandler($imgLink, $data)
{
    $img = '<img src="' . $imgLink . '" class="img-responsive img right">';
    $num = substr_count($data, '<h3>');

    $ch = curl_init();
    // Устанавливаем URL на который посылать запрос
    curl_setopt($ch, CURLOPT_URL, 'http://utilites.2hut.ru/presentGenerator/generator.php');
    //curl_setopt($ch, CURLOPT_HEADER, 1); //  Результат будет содержать заголовки
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); // Результат будет возвращём в переменную, а не выведен.
    curl_setopt($ch, CURLOPT_POST, 1); // Устанавливаем метод POST
    curl_setopt(
        $ch,
        CURLOPT_POSTFIELDS,
        "string=$img&cnt=$num"
    ); // посылаемые значения
    $result = curl_exec($ch);
    $result = preg_split('~\r?\n~', json_decode($result));
    array_unshift($result, $img);
    //удаляем пустые элементы
    $result = array_diff($result, [0, null]);
    $data = explode('</h3>', $data);
    $images = $output = [];
    foreach ($result as $key => $img) {
        if ($key % 2 === 1)
            $images[] = str_replace('right', 'left', $img);
        else
            $images[] = $img;
    }
    $i = 0;
    foreach ($data as $value) {
        if (strpos($value, '<h3>') !== false) {
            $output[] = $value . "</h3>\n" . $images[$i++] . "\n";
        } else {
            $output[] = $value;
        }
    }
    return implode("\n", $output);
}

function parse()
{
    if (filter_has_var(INPUT_POST, 'inner')) {
        $inner = filter_input(INPUT_POST, 'inner');
//        g($inner); exit;
        $result = dataHandler($inner);
        if (!filter_has_var(INPUT_POST, 'img'))
            return $result;

        $img = filter_input(INPUT_POST, 'img');
        return imageHandler($img, $result);
    }
    return null;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST')
    $result = parse();
?>
<html>
<head>
    <title>Investment Digest Handler</title>
    <link rel="stylesheet" href="http://getbootstrap.com/dist/css/bootstrap.min.css">
    <script src="http://ajax.googleapis.com/ajax/libs/jquery/1/jquery.min.js"></script>
    <script src="http://getbootstrap.com/dist/js/bootstrap.min.js"></script>
    <script src="http://cdn.ckeditor.com/4.5.4/standard-all/ckeditor.js"></script>
</head>
<body>
<div class="container">
    <h3 class="page-header">Investment Digest Handler</h3>
    <form action="<?= $_SERVER['REQUEST_URI'] ?>" method="post">
        <div class="form-group">
            <textarea id="inner" name="inner" class="form-control"><?= $inner ?></textarea>
        </div>
        <script type="text/javascript">
            CKEDITOR.replace('inner');
        </script>
        <div class="form-group">
            <label for="img">Image link</label>
            <input type="text" class="form-control" name="img" id="img" placeholder="src">
        </div>
        <input type="submit" id="submit" value="Отправить" class="btn btn-primary">
    </form>
    <br><br><br>
    <?
    isset($result) ? g($result) : null;
    ?>
</div>

<script type="text/javascript">


        $(function () {

            //выделение текста по клику
            $('code').click(function () {
                var e = this;
                if (window.getSelection) {
                    var s = window.getSelection();
                    if (s.setBaseAndExtent) {
                        s.setBaseAndExtent(e, 0, e, e.innerText.length - 1);
                    } else {
                        var r = document.createRange();
                        r.selectNodeContents(e);
                        s.removeAllRanges();
                        s.addRange(r);
                    }
                } else if (document.getSelection) {
                    var s = document.getSelection();
                    var r = document.createRange();
                    r.selectNodeContents(e);
                    s.removeAllRanges();
                    s.addRange(r);
                } else if (document.selection) {
                    var r = document.body.createTextRange();
                    r.moveToElementText(e);
                    r.select();
                }
            });
        });
</script>

</body>
</html>