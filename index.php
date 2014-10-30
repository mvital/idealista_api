<?php
/**
 * Created by PhpStorm.
 * User: berro
 * Date: 29/10/14
 * Time: 19:28
 */

ini_set('display_errors',1);
error_reporting(E_ERROR);

//API localhost
$api_key ='fd32494b7c9eadee92ac495ecb273582';
//API produccion
//$api_key = '82b0e7ff63538cf481190e5dc40d3e6f';

$msg_out = '';
$url_api = 'http://www.idealista.com/labs/propertyMap.htm?action=json';
$a_params = array(
    'k' => $api_key,
    'operation' => 'rent',
    'center' => 'long,lat',
    'distance' => 'meters',
    'numPage' => '0',
    'minPrice' => '0',
    'maxPrice' => '0',
    'minSize' => '0',
    'maxSize' => '0',
    'flat' => 'true',
    'penthouse' => 'true',
    'studio' => 'true',
    'chalet' => 'true',
    'duplex' => 'true',
    'garage' => 'true',
    'premises' => 'true',
    'office' => 'true',
    'room' => 'true',
    'minRooms' => '',
    'since' => 'a',
    'pics' => '1',
);

if ($_REQUEST['k']) {
    foreach ($_REQUEST as $key => $value) {
        if ($value != '' && !in_array($key,$a_params)) {
            $api_filter .= '&' . $key . '=' . $value;
        }
    }

    $res = json_decode(file_get_contents($url_api . $api_filter));

    if ($res) {
        $msg_out .= '<h3>' . $res[0] . '</h3>';
        foreach ($res[1]->elementList as $piso) {
            ob_start();
            var_dump($piso);
            $msg_vardump = ob_get_clean();
            $msg_out .= '
                    <div class="row alert alert-warning">
                        <div class="col-md-2"><img src="'.$piso->thumbnail.'"><br><br></div>
                        <div class="col-md-8">
                            <b>'.$piso->district.'</b> '.$piso->address.'
                            <a href="https://www.google.es/maps/place/'.urlencode($piso->address.' '.$piso->province).'/@'.$piso->latitude.','.$piso->longitude.',15z/" target="_blank"><span class="glyphicon glyphicon-map-marker"></span></a><br>
                            '.$piso->price.' €
                            <br>
                            '.$msg_vardump.'
                        </div>
                        <div class="col-md-2"><img src="'.$piso->agentLogo.'"></div>

                    </div>';
        }
    }
}


?>
<!DOCTYPE html>
<html>
<head lang="en">
    <title></title>

    <meta charset="UTF-8">
    <!-- Latest compiled and minified CSS -->
    <link rel="stylesheet" href="css/bootstrap.css">

    <!-- Optional theme -->
    <link rel="stylesheet" href="css/bootstrap-theme.css">

    <!-- Latest compiled and minified JavaScript -->
    <script src="js/jquery.js"></script>
    <script src="js/bootstrap.js"></script>
</head>
<body>
<style>
</style>
<script>
    $('document').ready(function () {
        //Inicializa form
        $('#k').attr('value', '<?= $a_params['k']; ?>');
        <?php
        if(count($_REQUEST) == 0)
        {
            echo "
            $('#operation').attr('value','rent');
            $('#center').attr('value','41.6518453,-0.8673399');
            $('#distance').attr('value','1000');
            $('#pics').attr('value','1');
            $('#numPage').attr('value','1');
            ";
        }
        ?>

        //Muestra oculta form
        $('#filtro_boton').click(function () {
            $('#filtro').toggle(500, function () {
                if ($('#filtro').is(':visible')) {
                    $('#filtro_boton').text('Ocultar');
                }
                else {
                    $('#filtro_boton').text('Mostrar');
                }
            });

        });

        //Paginación
        $('.btn_anterior').click(function(){
            var pagina = $('#numPage').val();
            if(pagina > 0  ){
                $('#numPage').val(pagina - 1);
                $('#form_filtro').submit();
            }
        });
        $('.btn_siguiente').click(function(){
            var pagina = $('#numPage').val();
            if(pagina >= 0  ){
                $('#numPage').val(parseInt(pagina) + 1);
                $('#form_filtro').submit();
            }
        });
    });
</script>
<div class='container-fluid'>
    <br>

    <div class="row">
        <div class="col-md-12">
            <a href="index.php"><img src="http://www.idealista.com/static/common/home/resources/img/logo-small-es.png"></a>
            <br><br>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <a href="#" id="filtro_boton">Ocultar</a>

            <div id="filtro" class="alert alert-info">
                <form action="index.php" method="get" id="form_filtro">
                    <?php
                    foreach ($a_params as $key => $value) {
                        echo ' <input type="text" name="' . $key . '" id="' . $key . '" value="' . $_REQUEST[$key] . '" placeholder="' . $key . ': ' . $value . '" title="' . $key . ': ' . $value . '"> ';
                    }
                    ?>
                    <br><br>
                    <input type="submit" value="Enviar" class="btn btn-info">
                </form>
            </div>
            <div class="text-center">
                <a class="btn btn-info btn_anterior">< Anterior</a> - <a class="btn btn-info btn_siguiente">Siguiente ></a>
            </div>
        </div>
    </div>

    <?php
        echo $msg_out;
    ?>
    <div class="text-center">
        <a class="btn btn-info btn_anterior">< Anterior</a> - <a class="btn btn-info btn_siguiente">Siguiente ></a>
    </div>
</div>
</body>
</html>