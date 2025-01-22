<?

function temAcesso($nomeDaArea){

    if ($_SESSION[NOME_SESSAO]['tipoUsuario']!='1') {

        $nomeDaArea = CRUD::SELECT('id', 'boss_areasacesso', 'nomeArea=:area', array('area'=>$nomeDaArea), '');
        $nomeDaArea = $nomeDaArea[0]['id'];

        if (in_array($nomeDaArea, $_SESSION[NOME_SESSAO]['areasAcesso'])===false) {
            return false;
        } else {
            return true;
        }

    } else {
        return true;
    }

    return false;
}

    $menuAtivo = isset($area)?$area:'';
    $subMenuAtivo = isset($subArea)?$subArea:'';

?>

<style type="text/css">
.nav li .caret { border-top-color: #428bca; border-bottom-color: #428bca; float: right; margin: 9px 0px;}
.nav li.active .caret {border-style: solid; border-width: 0 4px 4px 4px; border-color: transparent transparent #ffffff transparent;}
</style>

<div class="navbar-default navbar-static-side menu-lateral" role="navigation">
    <div class="sidebar-collapse" id="sidebar-collapse">
        <ul class="nav nav-pills nav-stacked" id="side-menu">
            
            <li<?=$menuAtivo==''?' class="active"':''?>>
                <a href="<?=SITE_URL?>boss/index.php"><i class="glyphicon glyphicon-home"></i> Início </a>
            </li>

            <? if (temAcesso('Banner')) { ?>
                <li<?=$menuAtivo=='Banner'?' class="active"':''?>>
                    <a href="<?=SITE_URL?>boss/modulos/banner.php"><i class="glyphicon glyphicon-circle-arrow-right"></i> Banners </a>
                </li>   
            <?}?>
            
            <? if (temAcesso('Informações')) { ?>
                <li<?=$menuAtivo=='Informações'?' class="active"':''?>>
                    <a href="<?=SITE_URL?>boss/modulos/informacao.php"><i class="glyphicon glyphicon-circle-arrow-right"></i> Informações </a>
                </li>   
            <?}?>

            <? if (temAcesso('Quem sou')) { ?>
                <li<?=$menuAtivo=='Quem sou'?' class="active"':''?>>
                    <a href="<?=SITE_URL?>boss/modulos/sobre.php"><i class="glyphicon glyphicon-circle-arrow-right"></i> Quem sou </a>
                </li>   
            <?}?>
            
            <? if (temAcesso('Doenças Tratadas')) { ?>
                <li<?=$menuAtivo=='Doenças Tratadas'?' class="active"':''?>>
                    <a href="<?=SITE_URL?>boss/modulos/doencas_tratadas.php"><i class="glyphicon glyphicon-circle-arrow-right"></i> Doenças Tratadas </a>
                </li>   
            <?}?>

            <? if (temAcesso('Especialidades')) { ?>
                <li<?=$menuAtivo=='Especialidades'?' class="active"':''?>>
                    <a href="<?=SITE_URL?>boss/modulos/especialidades.php"><i class="glyphicon glyphicon-circle-arrow-right"></i> Especialidades </a>
                </li>   
            <?}?>

            <? if (temAcesso('FAQ')) { ?>
                <li<?=$menuAtivo=='FAQ'?' class="active"':''?>>
                    <a href="<?=SITE_URL?>boss/modulos/faq.php"><i class="glyphicon glyphicon-circle-arrow-right"></i> FAQ </a>
                </li>   
            <?}?>

            <? if (temAcesso('Depoimentos')) { ?>
                <li<?=$menuAtivo=='Depoimentos'?' class="active"':''?>>
                    <a href="<?=SITE_URL?>boss/modulos/depoimentos.php"><i class="glyphicon glyphicon-circle-arrow-right"></i> Depoimentos </a>
                </li>   
            <?}?>

            <? if (temAcesso('Contato')) { ?>
                <li<?=$menuAtivo=='Contato' ?' class="active"':''?>><a href="#"><i class="glyphicon glyphicon-circle-arrow-right"></i> Contato<span class="caret"></span></a>
                    <ul class="nav nav-second-level <?=$menuAtivo=='Contato'?'in':''?>">
                        <li <?=$subMenuAtivo=='sessão'?' class="active"':''?>><a href="<?=SITE_URL?>boss/modulos/contato.php">Sessão</a></li>
                        <li <?=$subMenuAtivo=='mensagens'?' class="active"':''?>><a href="<?=SITE_URL?>boss/modulos/mensagens.php">Mensagens</a></li>
                    </ul>
                </li>       
            <?}?>

            <? if (temAcesso('Blog')) { ?>
                <li<?=$menuAtivo=='Blog' ?' class="active"':''?>><a href="#"><i class="glyphicon glyphicon-circle-arrow-right"></i> Blog <span class="caret"></span></a>
                    <ul class="nav nav-second-level <?=$menuAtivo=='Blog'?'in':''?>">
                        <li <?=$subMenuAtivo=='Categorias'?' class="active"':''?>><a href="<?=SITE_URL?>boss/modulos/blog-categoria.php">Cadastro de Categorias</a></li>
                        <li <?=$subMenuAtivo=='Artigos'?' class="active"':''?>><a href="<?=SITE_URL?>boss/modulos/blog.php">Cadastro de Artigo</a></li>
                    </ul>
                </li>       
            <?}?>

            <? if (temAcesso('Dados da Empresa')) { ?>
                <li<?=$menuAtivo=='Dados da Empresa'?' class="active"':''?>>
                    <a href="<?=SITE_URL?>boss/modulos/dados_empresa.php"><i class="glyphicon glyphicon-circle-arrow-right"></i> Dados da Empresa </a>
                </li>   
            <?}?>
        </ul>
    </div>
</div>
