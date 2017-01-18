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

 if ( $_SERVER['REQUEST_METHOD'] === 'POST' ) {
     if ( filter_has_var( INPUT_POST, 'inner' ) ) {
         $output = $table = '';
         $inner = filter_input( INPUT_POST, 'inner' );
         g($inner); exit;
         if ( preg_match( '/Специалист по ценообразованию|Руководителям о ценообразовании/i', $inner, $matches ) )
             $output .= '<p style="text-align: center;"><strong>"' . trim( $matches[0] ) . '"</strong></p>' . "\r\n";
         if ( preg_match( '/<td.*>(Аудитория .*)<\/td>/iU', $inner, $matches ) )
             $output .= '<p style="text-align: center;">' . trim( $matches[1] ) . '</p>' . "\r\n";
         if ( preg_match( '/<td.*>(Группа .*)<\/td>/iU', $inner, $matches ) )
             $output .= '<p style="text-align: center;">' . trim( $matches[1] ) . '</p>' . "\r\n";
         if ( preg_match( '/(Дата занятий) (.*)<\/td>/iU', $inner, $matches ) )
             $output .= '<p style="text-align: center;">' . trim( $matches[1] ) . ' <strong>' . trim( $matches[2] ) . '</strong></p>' . "\r\n";
         if ( preg_match( '/(Время занятий) (.*)<\/td>/iU', $inner, $matches ) )
             $output .= '<p style="text-align: center;">' . trim( $matches[1] ) . ' <strong>' . trim( $matches[2] ) . '</strong></p>' . "\r\n";
         $output .= "<table>\r\n\t\t<tr>\r\n\t\t\t<td>";
         $pos = strpos( $inner, 'ГРБС' );
         $table = substr( $inner, $pos );
         $output .= $table;
         $pattern = [
             '/<td ?(col|row)span=.*?>/i',
             '/(\t*<td>\&nbsp\;<\/td>\r*\n*){2,}/',
             '/\n\t{2,}\r/',
             '/ГРБС/',
             '/Организация/',
             '/Ф.И.О.?/',
             '/Должность/',
             '/Курс/',
             '/(<tr>\r*\n*\t*<td>)( *[^&nbsp;]+)(<\/td>)/iU',
             '~(<td>.+</td>).*\n.*?<td>&nbsp;</td>~'
         ];
         $replacement = [
             '<td>',
             '',
             '',
             '<strong>ГРБС</strong>',
             '<strong>Организация</strong>',
             '<strong>Ф.И.О.</strong>',
             '<strong>Должность</strong>',
             '<strong>Курс</strong>',
             '$1<strong>$2</strong>$3',
             '$1'
         ];
         $output = preg_replace( $pattern, $replacement, $output );
     }
 }
?>
<html>
    <head>
        <title>Agency Tables Creator</title>
        <link rel="stylesheet" href="http://getbootstrap.com/dist/css/bootstrap.min.css">
        <script src="http://ajax.googleapis.com/ajax/libs/jquery/1/jquery.min.js"></script>
        <script src="http://getbootstrap.com/dist/js/bootstrap.min.js"></script>
        <script src="http://cdn.ckeditor.com/4.5.4/standard-all/ckeditor.js"></script>
    </head>
    <body>
        <div class="container">
            <h3 class="page-header">Agency Tables Creator</h3>
            <form action="<?= $_SERVER['REQUEST_URI'] ?>" method="post">
                <div class="form-group">
                    <textarea id="inner" name="inner" class="form-control"><?= $inner ?></textarea>
                </div>
                <script type="text/javascript">
                    CKEDITOR.replace('inner');
                </script>
                <input type="submit" id="submit" style="display: none" value="Отправить" class="btn btn-primary">
            </form>
            <br><br><br>
            <?
             $output ? g( $output ) : 'Парсинг окончился неудачно';
            ?>
        </div>
        <div id="top" style="font-size: 52px; position: fixed; top: 90vh;left: 250px; cursor: pointer;"><span class="glyphicon glyphicon-chevron-up"></span></div>
        <script type="text/javascript">
            $(function () {
                for (var i in CKEDITOR.instances) {
                    CKEDITOR.instances[i].setData('');
                    CKEDITOR.instances[i].on('paste', function () {
                        setTimeout(function () {
                            $('#submit').click();
                        }, 300);
                    });
                }

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
            });
        </script>
    </body>
</html>