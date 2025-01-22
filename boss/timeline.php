<? include("connect.php"); include("includes/Funcoes/sessao.php"); include("includes/Funcoes/Funcoes.php");

$nomeU = $_SESSION[NOME_SESSAO]['nome'];
$area = 'Timeline';

if ($_SESSION[NOME_SESSAO]['tipoUsuario']=='3') {
    print "<script>window.alert('Acesso somente para Administradores.');</script>";
    print "<script>window.location='index.php';</script>";
}


/* Selciona todos os usuários */
$sqlt = CRUD::SELECT('', 'boss_timeline', '', '', 'order by data desc'); 
$total_reg = sizeof($sqlt);

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
    <link href="<?=BOSS_TATICA?>js/plugins/select2-3.4.5/select2.css" rel="stylesheet"/>
    <link href="<?=BOSS_TATICA?>css/plugins/dataTables/dataTables.bootstrap.css" rel="stylesheet">
    <link href="<?=BOSS_TATICA?>css/estilo.css" rel="stylesheet">

    <script src="<?=BOSS_TATICA?>js/jquery-1.10.2.js"></script>
    <script src="//netdna.bootstrapcdn.com/bootstrap/3.0.0/js/bootstrap.min.js"></script>
    <script src="<?=BOSS_TATICA?>js/plugins/metisMenu/jquery.metisMenu.js"></script>
    <script src="<?=BOSS_TATICA?>js/sb-admin.js"></script>
    <script src="<?=BOSS_TATICA?>js/plugins/select2-3.4.5/select2.js"></script>
    <script src="<?=BOSS_TATICA?>js/plugins/dataTables/jquery.dataTables.js"></script>
    <script src="<?=BOSS_TATICA?>js/plugins/dataTables/dataTables.bootstrap.js"></script>
    <script src="<?=BOSS_TATICA?>js/plugins/dataTables/datetime-br.js"></script>

</head>

<body>

    <script type="text/javascript" src="<?=BOSS_TATICA?>js/jquery.complexify.js"></script>
    <script type="text/javascript">

        $(document).ready(function () {

            $('#dataTables-example').dataTable({
                "aaSorting": [[ 0, "desc" ]], //ordenação padrão da tabela por ordem decrescente(desc) da primeira coluna(0)
                "aoColumns": [
                    { "sType": "datetime-br" }, //opção de ordenar por data
                    null,
                    null,
                    null
                ]
            });
        });
    </script>

    <? include('topo.php');include('menu.php');?>
    
    <div id="page-wrapper">
        <div class="row">
            <div class="col-lg-12">
                <h1 class="page-header">Timeline</h1>
            </div>
            <!-- /.col-lg-12 -->
        </div>

        <div class="row" id="list">
                <div class="col-lg-12">
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            Veja as ações de todos os usuários dentro do BOSS:
                        </div>
                        <div class="panel-body">
                            <? if ($total_reg==0){ echo "Nenhum registro encontrado.";} else { ?>
                            <div class="table-responsive">
                                <table class="table table-striped table-bordered table-hover" id="dataTables-example">
                                    <thead>
                                        <tr>
                                            <th>Data - Hora</th>
                                            <th>Usuário</th>
                                            <th>Ação</th>
                                            <th>Área</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <? $cont = 1;
                                        foreach ($sqlt as $key => $t) {
                                            ?>
                                            <tr class="<?=$cont%2==0?'odd':'even'?>">
                                                <td><?=Funcoes::fdata(substr($t['data'], 0, 10), "-")?> - <?=substr($t['data'], 11, 8)?></td>
                                                <td><?=$t['nomeUsuario']?></td>
                                                <td><?=$t['acao']?></td>
                                                <td><?=$t['area']?></td>
                                            </tr>
                                        <? }?> 
                                    </tbody>
                                </table>
                            </div>
                            <? } ?>
                        </div>
                    </div>
                </div>
            </div>
        
    </div>

    <? include("rodape.php"); ?>

</body>

</html>