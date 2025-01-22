        
        <? 
            include('topo.php'); include('menu.php'); include('includes/Funcoes/Funcoes.php');
        ?>

        <script src="<?=BOSS_TATICA?>js/plugins/dataTables/jquery.dataTables.js"></script>
        <script src="<?=BOSS_TATICA?>js/plugins/dataTables/dataTables.bootstrap.js"></script>
        <script src="<?=BOSS_TATICA?>js/intro.js"></script>
        <link href="<?=BOSS_TATICA?>js/introjs.css" rel="stylesheet">
        <script type="text/javascript">

        $(document).ready(function () {

            $('#dataTables-example').dataTable();

        });

        function startIntro(){
            if($('.navbar-toggle').css('display') == 'none'){
                $('#side-menu').attr("data-step", "3");$('#side-menu').attr("data-intro", "Aqui está o menu, com os módulos editáveis de seu site");
            } else {
                $('#side-menu').attr("data-step", "");$('#side-menu').attr("data-intro", "");
                $('.navbar-toggle').attr("data-step", "3");$('.navbar-toggle').attr("data-intro", "Clique aqui para abrir o menu, com os módulos editáveis de seu site");$('.navbar-toggle').attr("data-position", "left");
            }
            introJs().start();
        }
        </script>
        
        <div id="page-wrapper">

            <div class="row">
                <div class="col-lg-12">
                    <h1 class="page-header">Página Inicial</h1>
                </div>
                <!-- /.col-lg-12 -->
            </div>
            <!-- /.row -->
            <div class="row">
                <div class="<?=$_SESSION[NOME_SESSAO]['tipoUsuario']=='3'?'col-lg-12':'col-lg-8'?>">
                    <div class="jumbotron">
                        <h2>Olá <?=$_SESSION[NOME_SESSAO]['nome']?>!</h2>
                        <p>Seja bem vindo ao BOSS, o Painel de Administração do seu site.<br><br>
                            Nesse painel é possível alterar o conteúdo do site, sem a necessidade de contactar a TáticaWeb a cada atualização.
                            <br><br>
                            Clique no botão abaixo para conhecer o BOSS e obter algumas dicas:</p><br>
                        <p><a class="btn btn-success btn-lg start-intro" role="button" onclick="startIntro()">Mostrar dicas</a></p>
                    </div>
                </div>
                <!-- /.col-lg-8/12 -->
                <? if ($_SESSION[NOME_SESSAO]['tipoUsuario']!='3') { 

                    /* Selciona todos os eventos */
                    $sqlTL = CRUD::SELECT('', 'boss_timeline', '' , '', 'order by data desc LIMIT 8'); 
                    $total_reg = sizeof($sqlTL); ?>

                <div class="col-lg-4">
                    <div class="panel panel-default"  data-step="4" data-intro="Esse painel mostra as últimas alterações feitas pelo BOSS" data-position="top">
                        <div class="panel-heading">
                            <i class="fa fa-clock-o fa-fw"></i> Timeline
                        </div>
                        <!-- /.panel-heading -->
                        <div class="panel-body">
                            <div class="list-group">
                                <? if ($total_reg==0){ echo "Nenhum registro encontrado.";} else { 
                                $cont = 1; $icones = array("Inseriu"=>"plus", "Editou"=>"edit", "Excluiu"=>"trash-o", "Bloqueou"=>"ban", "Desbloqueou"=>"ban", "Respondeu"=>"reply", "Marcou"=>"check-square", "Leu"=>"check-square", "Verificou"=>"check-square", "Reprovou"=>"ban", "Aprovou"=>"check-square", "Cancelou"=>"lock", "Ativou"=>"repeat");
                                    foreach ($sqlTL as $key => $t) {
                                        $idIcones = explode(" ", $t['acao']);
                                        $idIcones = $idIcones[0];
                                        ?>
                                        <a class="list-group-item">
                                            <i class="fa fa-<?=$icones[$idIcones]?> fa-fw"></i> <?=$t['acao']?>
                                            <span class="pull-right text-muted small"><em><?=Funcoes::time_ago(strtotime($t['data']))?></em>
                                            </span>
                                            <div class="clearfix"></div>
                                        </a>
                                    <? }
                                }?> 
                            </div>
                            <!-- /.list-group -->
                            <a href="timeline.php" class="btn btn-default btn-block">Ver todas as ações</a>
                        </div>
                        <!-- /.panel-body -->
                    </div>
                    <!-- /.panel -->
                </div>
                <!-- /.col-lg-4 -->
                <? } ?>
            </div>
            <!-- /.row -->
        </div>
        <!-- /#page-wrapper -->

    </div>
    <!-- /#wrapper -->
    <? include("rodape.php"); ?>