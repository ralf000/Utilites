<?

 function g( $var ) {
    echo '<link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/highlight.js/9.0.0/styles/default.min.css">
                <script src="//cdnjs.cloudflare.com/ajax/libs/highlight.js/9.0.0/highlight.min.js"></script>
                <script>hljs.initHighlightingOnLoad();</script>';
    echo '<pre><code class="html" style="border: 1px solid black;">';
    if ( is_array( $var ) ) {
        print_r( $var );
    } elseif ( is_object( $var ) ) {
        $class = get_class( $var );
        Reflection::export( new ReflectionClass( $class ) );
    } else {
        echo htmlspecialchars( $var );
    }
    echo '</code></pre>';
}

 function DBListBuilder() {
     if ( $_SERVER['REQUEST_METHOD'] === 'POST' ) {
         $inner = $_POST['inner'];
         $dot = $_POST['dot'];
         $list = $_POST['list'];
         $comma = $_POST['comma'];
         $tab = $_POST['tab'];
         $table = $_POST['table'];
         if ( $inner ) {
             if ( $table ) {
                 $pattern = '/\s[^a href]\w*?=".*?"/';
                 $output = preg_replace( $pattern, '', $inner );
                 $pattern = '~<tr>\r?\n\s+(<td>.*</td>\r?\n\s+){1,2}<td>&nbsp;</td>\r?\n\s+</tr>~Uui';
                 $output = preg_replace( $pattern, '', $output );
                 $pattern = '~<a href~Uui';
                 $output = preg_replace( $pattern, '<a target="_blank" href', $output );
                 $pattern = '~<table>~Uui';
                 $output = preg_replace( $pattern, '<table class="wide-table">', $output );
                 return $output;
             } else {
                 $output = strip_tags( $inner );
                 $output = str_replace( '&#34;', '', $output );
                 $pattern = '/\n|\t|\v/';
                 $output = preg_replace( $pattern, '', $output );
                 $pattern = '/&bull;|\s-/';
                 $output = preg_replace( $pattern, ';<br>', $output );

                 if ( $comma )
                     $output = str_replace( ',', ';', $output );
                 if ( $dot ) {
                     $output = str_replace( '.', ';', $output );
                     $pos = strpos( $output, '.', strlen( $output ) - strlen( '.' ) );
                     $pos !== false ? $output = substr_replace( $output, '', $pos, strlen( '.' ) ) : $output;
                 }

                 $pos = strpos( $output, ';' );
                 $pos !== false ? $output = substr_replace( $output, '', $pos, strlen( ';' ) ) : $output;
                 return $output;
             }
         }
     }
 }
?>
<html>
    <head>
        <title>List Builder From DB</title>
        <link rel="stylesheet" href="http://getbootstrap.com/dist/css/bootstrap.min.css">
        <script src="http://ajax.googleapis.com/ajax/libs/jquery/1/jquery.min.js"></script>
        <script src="http://getbootstrap.com/dist/js/bootstrap.min.js"></script>
        <script src="http://cdn.ckeditor.com/4.5.4/standard-all/ckeditor.js"></script>
    </head>
    <body>
        <div class="container">
            <h3 class="page-header">List Builder From DB</h3>
            <form action="<?= $_SERVER['REQUEST_URI'] ?>" method="post">
                <!--                <div class="form-group">
                                <textarea name="inner" class="form-control" style="width: 100%" rows="10"><?= $inner ?></textarea>
                                </div>-->
                <div class="form-group">
                    <textarea id="inner" name="inner" class="form-control"><?= $inner ?></textarea>
                </div>
                <script type="text/javascript">
                    CKEDITOR.replace('inner');
                </script>
                <script type="text/javascript">
                    window.onload = function ()
                    {
                        CKEDITOR.replace('inner');
                    };
                </script>
                <div class="checkbox">
                    <label>
                        <input type="checkbox" name="dot">  Разбить по точке
                    </label>
                </div>
                <div class="checkbox">
                    <label>
                        <input type="checkbox" name="comma">  Разбить по запятой
                    </label>
                </div>
                <div class="checkbox">
                    <label>
                        <input type="checkbox" name="tab">  Разбить по табуляции / переводу строки
                    </label>
                </div>
                <div class="checkbox">
                    <label>
                        <input type="checkbox" name="list">  Сделать список ul>li
                    </label>
                </div>
                <div class="checkbox">
                    <label>
                        <input type="checkbox" name="table" checked>  Очистить таблицу
                    </label>
                </div>
                <input type="submit" value="Отправить" class="btn btn-primary">
            </form>
            <br><br><br>
            <?
             ($output = DBListBuilder()) ? g( $output ) : '';
            ?>
        </div>
        <div id="top" style="font-size: 52px; position: fixed; top: 90vh;left: 250px; cursor: pointer;"><span class="glyphicon glyphicon-chevron-up"></span></div>
        <script type="text/javascript">
            $(function () {
                var top = $('#top').hide();
                $(window).on('scroll', function () {
                    if ($(this).scrollTop() > 130)
                        top.fadeIn();
                    else
                        top.fadeOut();
                });
                top.on('click', function (e) {
                    e.preventDefault();
                    var t = $(this);
                    $('html,body').animate({scrollTop: 0}, 400);
                });
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
