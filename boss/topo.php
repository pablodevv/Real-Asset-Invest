<script type="text/javascript">
    $(document).ready(function () {
        $('a').hover(function(){$(this).tooltip('show')});
    });
</script>
<div id="wrapper">

        <nav class="navbar navbar-default navbar-static-top" role="navigation" style="margin-bottom: 0">
            <div class="row">
                <div class="col-xs-4">
                    <a class="navbar-brand" href="<?=SITE_URL?>boss/index.php"><img src="<?=SITE_URL?>boss/img/logo.png" class="img-responsive logo internas" align="left" title="Painel Administrativo"></a>    
                </div>
                <div class="col-xs-8">
                    <a class="navbar-toggle" data-toggle="collapse" data-target=".sidebar-collapse" href="#">
                        <span class="sr-only">Toggle navigation</span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                    </a>    
                    <ul class="nav navbar-top-links navbar-right detallhe-usuario-topo">
                        <li class="dropdown">
                            <a class="dropdown-toggle" data-toggle="dropdown" href="#" data-step="1" data-intro="O seu perfil pode ser acessado por aqui.<?=$_SESSION[NOME_SESSAO]['tipoUsuario']==1?' Assim como a lista de usuários.':''?> " data-position="left">
                                <? $n = explode(' ',$_SESSION[NOME_SESSAO]['nome']); echo $n[0];?> <i class="fa fa-user fa-fw"></i>  <i class="fa fa-caret-down"></i>
                            </a>
                            <ul class="dropdown-menu dropdown-user submenuuser">
                                <li>
                                    <a href="<?=SITE_URL?>boss/conta.php" data-toggle="tooltip" data-placement="left" data-animation="true" title="Alterar senha e nome"><i class="fa fa-user fa-fw"></i> Meu Perfil</a></li>
                                    <? if($_SESSION[NOME_SESSAO]['tipoUsuario']!=3) { ?> 
                                        <li><a href="<?=SITE_URL?>boss/usuarios.php" data-toggle="tooltip" data-placement="left" data-animation="true" title="Alterar ou Cadastrar usuários"><i class="glyphicon glyphicon-list"></i> Lista Usuários</a></li>
                                    <? } ?>
                                    <li class="divider"></li>
                                    <li><a href="?acao=sair"><i class="fa fa-sign-out fa-fw"></i> Sair</a>
                                </li>
                            </ul>
                        </li>
                        <li>
                            <a data-step="2" data-intro="As perguntas frequentes podem responder suas dúvidas" data-position="left" href="<?=BOSS_TATICA?>faq.php" target="_blank">FAQ <i class="fa fa-comments-o fa-fw"></i></a>
                        </li>
                    </ul>
                </div>
            </div>
        </nav>