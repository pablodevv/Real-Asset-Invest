<? if(!isset($_SESSION)) session_start();  include("connect.php");

$acao = isset($_GET["acao"]) ? $_GET["acao"] : "" ;
if ($acao=='sair') {
    unset($_SESSION[NOME_SESSAO]); print "<script>window.alert('Logoff efetuado com sucesso.');</script>";
    print "<script>window.location='".SITE_URL."boss/index.php';</script>";
}

?>
<!DOCTYPE html>
<html>

<head>

    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>BOSS v.3</title>

    <link href="//netdna.bootstrapcdn.com/bootstrap/3.0.0/css/bootstrap.min.css" rel="stylesheet">
    <link href="//netdna.bootstrapcdn.com/font-awesome/4.0.3/css/font-awesome.css" rel="stylesheet">
    <link href="<?=BOSS_TATICA?>css/sb-admin.css" rel="stylesheet">
    <link href="<?=BOSS_TATICA?>css/estilo.css" rel="stylesheet">

    <script src="<?=BOSS_TATICA?>js/jquery-1.10.2.js"></script>
    <script src="//netdna.bootstrapcdn.com/bootstrap/3.0.0/js/bootstrap.min.js"></script>
    <script src="<?=BOSS_TATICA?>js/plugins/metisMenu/jquery.metisMenu.js"></script>
    <script src="<?=BOSS_TATICA?>js/sb-admin.js"></script>

</head>

<body>

    <? 
        if(!isset($_SESSION[NOME_SESSAO])){
            include("login.php"); 
        }else if( (time() - $_SESSION['time']) > 60*60) {
            include("login.php");
        } else if($_SESSION[NOME_SESSAO]['redefinirSenha']){
            include("redefinirSenha.php");
        } else {
            include("home.php");
        } 
    ?>

</body>

</html>