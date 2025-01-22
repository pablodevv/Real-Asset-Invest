<? include("connect.php"); include("includes/Funcoes/sessao.php"); include("includes/Funcoes/Funcoes.php");
$file = explode("/", $_SERVER['PHP_SELF']); $file = end($file);

$nomeU = $_SESSION[NOME_SESSAO]['nome'];
$idU = $_SESSION[NOME_SESSAO]['id'];
$area = 'Usuários';

if ($_SESSION[NOME_SESSAO]['tipoUsuario']=='3') {
    print "<script>window.alert('Acesso somente para Administradores.');</script>";
    print "<script>window.location='index.php';</script>";
}

/* Seleciona dados do usuário selecionado */
$id = isset($_GET['id'])?intval($_GET['id']):0;
$a = CRUD::SELECT_ID('', 'boss_login', $id);

/* Selciona todos os usuários */
$sqlt = CRUD::SELECT('', 'boss_login', 'tipousuario<>1', '', 'order by nome'); 
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
    <link href="<?=BOSS_TATICA?>/js/SimpleNotifications/style.css" rel="stylesheet"/>
    <link href="<?=BOSS_TATICA?>css/estilo.css" rel="stylesheet">

    <script src="<?=BOSS_TATICA?>js/jquery-1.10.2.js"></script>
    <script src="//netdna.bootstrapcdn.com/bootstrap/3.0.0/js/bootstrap.min.js"></script>
    <script src="<?=BOSS_TATICA?>js/plugins/metisMenu/jquery.metisMenu.js"></script>
    <script src="<?=BOSS_TATICA?>js/sb-admin.js"></script>
    <script src="<?=BOSS_TATICA?>js/plugins/select2-3.4.5/select2.js"></script>
    <script src="<?=BOSS_TATICA?>js/plugins/dataTables/jquery.dataTables.js"></script>
    <script src="<?=BOSS_TATICA?>js/plugins/dataTables/dataTables.bootstrap.js"></script>
    <script src="<?=BOSS_TATICA?>/js/SimpleNotifications/ttw-simple-notifications.js"></script> 

</head>

<body>

    <?
    $id = isset($_GET['id']) ? $_GET['id'] : "";
    $acao = isset($_GET['acao']) ? $_GET['acao'] : "";
    $nomee = isset($_GET['nomee']) ? $_GET['nomee'] : "";

    $inserir = isset($_POST['inserir']) ? $_POST['inserir'] : "";
    $nome = isset($_POST['nome']) ? $_POST['nome'] : "";
    $login = isset($_POST['login']) ? $_POST['login'] : "";
    $email = isset($_POST['email']) ? $_POST['email'] : "";
    $tipoUsuario = isset($_POST['tipoUsuario']) ? $_POST['tipoUsuario'] : "";
    $areasAcesso = isset($_POST['areasAcesso']) ? $_POST['areasAcesso'] : "";
    $senha = isset($_POST['senha']) ? $_POST['senha'] : ""; $senha = $senha!=''?md5($senha):'';

    if ($inserir=='true') { 
        if ($acao=='editar') {  

            $params = $senha == ""
                ? array('login'=>$login, 'nome'=>$nome, 'email'=>$email, 'tipoUsuario'=>$tipoUsuario, 'areasAcesso'=>$areasAcesso) 
                : array('login'=>$login, 'nome'=>$nome, 'email'=>$email, 'tipoUsuario'=>$tipoUsuario, 'areasAcesso'=>$areasAcesso, 'redefinirSenha'=>1, 'senha'=>$senha);
            if(CRUD::UPDATE('boss_login', $params,$id)) print "<script>window.location='$file?alert=Usuário alterado&tp-alert=success';</script>";

            //TIMELINE
            TIMELINE::add($idU, $nomeU, $area, "Editou o usuário: $nome");

        } else { 
            if ($id = CRUD::INSERT_ID('boss_login', array('login'=>$login, 'email'=>$email, 'senha'=>$senha, 'nome'=>$nome, 'tipoUsuario'=>$tipoUsuario, 'areasAcesso'=>$areasAcesso, 'redefinirSenha'=>1))) print "<script>window.location='$file?alert=Usuário cadastrado&tp-alert=success';</script>";
        
            //TIMELINE
            TIMELINE::add($idU, $nomeU, $area, "Inseriu um usuário: $nome");

        }
    }

    if($acao=='excluir'){
    if(CRUD::DELETE('boss_login', $id)) print "<script>window.location='$file?alert=Usuário deletado&tp-alert=success';</script>";

    //TIMELINE
    TIMELINE::add($idU, $nomeU, $area, "Excluiu um Usuário: $nomee");
    } else if($acao=='bloquear'){
        if(CRUD::UPDATE('boss_login', array('bloqueado'=>1),$id)) print "<script>window.location='$file?alert=Usuário bloqueado&tp-alert=success';</script>";

        //TIMELINE
        TIMELINE::add($idU, $nomeU, $area, "Bloqueou um usuário: $nomee");

    } else if($acao=='desbloquear'){
        if(CRUD::UPDATE('boss_login', array('bloqueado'=>0),$id)) print "<script>window.location='$file?alert=Usuário desbloqueado&tp-alert=success';</script>";

        //TIMELINE
        TIMELINE::add($idU, $nomeU, $area, "Desbloqueou um usuário: $nomee");

    }
            
    ?>

    <script type="text/javascript" src="<?=BOSS_TATICA?>js/jquery.complexify.js"></script>
    <script type="text/javascript">

        $(document).ready(function () {

            var notifications = $('body').ttwSimpleNotifications();
            <?=isset($_GET['alert'])?'notifications.show("'.$_GET['alert'].'");':'';?>
            <?=isset($_GET['tp-alert'])?'$(".ttw-simple-notification").addClass("'.$_GET['tp-alert'].'");':'';?>

            $('#dataTables-example').dataTable();

            <?=($acao!='editar' && $acao!='novo')?"$('#edit').hide();":""?>


            $("#tipoUsuario").select2({'containerCssClass':'form-control semPadding'});
            $("#areasAcesso").select2({
                data:[
                    <? $areas = CRUD::SELECT('', 'boss_areasacesso', '', '', ''); $i=1;
                    foreach($areas as $aa){
                        echo ($i==1?"":",").'{id:'.$aa['id'].',text:"'.$aa['nomeArea'].'"}'; $i++;
                    }?>
                ],
                multiple: true,
                'containerCssClass':'form-control semPadding'
            });

            <?=($acao!='')?"":"$('#escondeAreas').hide();"?>
            $("#tipoUsuario").on("change", function() {
                if ($(this).val()!='1') {
                    $("#escondeAreas").slideDown();
                } else {
                    $("#escondeAreas").slideUp();
                }
            });

          var validateUsername = $('#login').next('span');
          $('#login').keyup(function () {
            var t = this; 
            if (this.value != this.lastValue) {
              if (this.timer) clearTimeout(this.timer);
              validateUsername.removeClass('error').html('<img src="img/loading.gif" height="16" width="16" style="float:right; margin:-25px 5px 0 0;" />');
              
              this.timer = setTimeout(function () {
                $.ajax({
                  url: 'includes/Funcoes/ajax-username-validation.php',
                  data: 'action=check_exist_username&table=login&field=login&username=' + t.value+'&userAntigo=<?=isset($a['login'])?$a['login']:''?>',
                  dataType: 'json',
                  type: 'post',
                  success: function (j) {
                    if(j.msg==1){
                        $('#login').addClass("error");
                        validateUsername.html('<h6 class="msgerro">Insira o nome de usuário</h6>');
                    } else if(j.msg==2){
                        $('#login').addClass("error");
                        validateUsername.html('<h6 class="msgerro">Apenas letras e números</h6>');
                    } else if(j.msg==3){
                        $('#login').addClass("error");
                        validateUsername.html('<h6 class="msgerro">Usuário já existe</h6>');
                        document.form.loginExiste.value = 'sim';
                    } else {
                        $('#login').removeClass("error");
                        validateUsername.html('');
                        document.form.loginExiste.value = 'nao';
                    }
                  }
                });
              }, 200);
              
              this.lastValue = this.value;
            }
          });

            $("#senha").complexify({minimumChars:6,strengthScaleFactor:0.8}, function (valid, complexity) { 
                $("#mtSenha .progress-bar").attr('aria-valuenow',complexity).css('width',complexity+'%'); 
                if(complexity>20) $("#mtSenha .progress-bar").removeClass('progress-bar-danger').addClass('progress-bar-info');
                if(complexity<20) $("#mtSenha .progress-bar").removeClass('progress-bar-info').removeClass('progress-bar-sucess').addClass('progress-bar-danger');
                if(valid) { $("#mtSenha .progress-bar").removeClass('progress-bar-info').addClass('progress-bar-success'); $("#validou").val("sim"); } else {$("#validou").val("");}
            });

            $('.forca-senha').hide();
            $('#senha').focusin(function() {
                $('.forca-senha').slideDown();
            });
            $('#senha').focusout(function() {
                if (document.form.validou.value == ""){ $('#senha').addClass("error"); $('#senha').next('span').html('<h6 class="msgerro">Senha fraca</h6>'); erros=erros+1;$('#senha').focus(); }
                $('.forca-senha').slideUp();
            });

            var validateSenha = $('#senha').next('span')
            $('#senha').keyup(function () {
                if(this.value ==''){
                    $('#senha').addClass("error");
                    validateSenha.html('<h6 class="msgerro">Insira a nova senha</h6>');
                } else {
                    $('#senha').removeClass("error");
                    validateSenha.html('');
                }
            });
        });

        function valida_email(email) {
            if(email.match('^([0-9,a-z,A-Z]+)([.,_,-]([0-9,a-z,A-Z]+))*[@]([0-9,a-z,A-Z]+)([.,_\,-]([0-9,a-z,A-Z]+))*[.]([0-9,a-z,A-Z]){2}([0-9,a-z,A-Z])?$')){
                return true;    
            } else {
                return false;
            }
        }

        function verificacao(){
            var erros=0;
            if (document.form.nome.value == ""){ $('#nome').addClass("error"); $('#nome').next('span').html('<h6 class="msgerro">Informe o Nome</h6>'); erros=1; }
            if (document.form.login.value == ""){ $('#login').addClass("error"); $('#login').next('span').html('<h6 class="msgerro">Informe o Usuário</h6>'); erros=1; }
            if (document.form.loginExiste.value == "sim"){ $('#login').addClass("error"); $('#login').next('span').html('<h6 class="msgerro">usuário já existe</h6>'); erros=1; }
            if (document.form.email.value == ""){ $('#email').addClass("error"); $('#email').next('span').html('<h6 class="msgerro">Informe o Email</h6>'); erros=1; 
            } else if (!valida_email(document.form.email.value)){ $('#email').addClass("error"); $('#email').next('span').html('<h6 class="msgerro">Email inválido</h6>'); erros=1; }
            if (document.form.tipoUsuario.value == ""){ $('#s2id_tipoUsuario a').addClass("error"); $('#tipoUsuario').next('span').html('<h6 class="msgerro">Informe o Tipo de Usuário</h6>'); erros=1; }
            if ((document.form.areasAcesso.value == "") && (document.form.tipoUsuario.value != "1") && (document.form.tipoUsuario.value != "")){ $('#areasAcesso').addClass("error"); $('#areasAcesso').next('span').html('<h6 class="msgerro">Informe os Áreas de acesso</h6>'); erros=1; }
            <? if($acao!='editar'){ ?>
            if (document.form.senha.value == ""){ $('#senha').addClass("error"); $('#senha').next('span').html('<h6 class="msgerro">Informe uma senha</h6>'); erros=1; }
            <? } ?>
            if(erros==0){ document.form.inserir.value = 'true'; document.form.submit(); }
        }
    </script>

    <? include('topo.php');include('menu.php');?>
    
    <div id="page-wrapper">
        <div class="row">
            <div class="col-lg-12">
                <h1 class="page-header">Usuários <?=$u?></h1>
            </div>
            <!-- /.col-lg-12 -->
        </div>

        <div class="row" id="edit">
            <div class="col-lg-6 col-lg-offset-3">
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <i class="<?=$acao==''?'glyphicon glyphicon-plus-sign':'glyphicon glyphicon-pencil'?>"></i> <?=$acao=='editar'?'Editar':'Novo'?> Usuário
                    </div>
                    <div class="panel-body">
                        <form role="form" name="form" action="" method="post" class="form-horizontal">
                            <fieldset>
                                <div class="form-group">
                                    <label for="nome" class="col-lg-3 control-label">Nome</label>
                                    <div class="col-lg-9">
                                        <input class="form-control" name="nome" id="nome" type="text" value="<?=isset($a['nome'])?$a['nome']:''?>"><span></span>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="login" class="col-lg-3 control-label">Usuário</label>
                                    <div class="col-lg-9">
                                        <input class="form-control" name="login" id="login" type="text" value="<?=isset($a['login'])?$a['login']:''?>"><span></span>
                                        <input type="hidden" name="loginExiste">
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label for="email" class="col-lg-3 control-label">Email</label>
                                    <div class="col-lg-9">
                                        <input class="form-control" name="email" id="email" type="text" value="<?=isset($a['email'])?$a['email']:''?>"><span></span>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label for="tipoUsuario" class="col-lg-3 control-label">Tipo de Usuário</label>
                                    <div class="col-lg-9">
                                        <select name="tipoUsuario" id="tipoUsuario">
                                            <?$tipoU = isset($a['tipoUsuario'])?$a['tipoUsuario']:''?>
                                            <option value="">Selecione</option>
                                            <option value="2" <?=$tipoU==2?'selected':''?>>Moderador</option>
                                            <option value="3" <?=$tipoU==3?'selected':''?>>Usuário</option>
                                            <option value="4" <?=$tipoU==4?'selected':''?>>Desenvolvedor</option>
                                        </select><span></span>
                                    </div>
                                </div>
                                <div class="form-group" id="escondeAreas" <?=$tipoU=='1'?'style="display:none;"':''?>>
                                    <label for="areasAcesso" class="col-lg-3 control-label">Áreas de Acesso</label>
                                    <div class="col-lg-9">
                                        <input type="hidden" name="areasAcesso" id="areasAcesso" value="<?=isset($a['areasAcesso'])?$a['areasAcesso']:''?>"><span></span>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="nome" class="col-lg-3 control-label">Nova Senha</label>
                                    <div class="col-lg-9">
                                        <input class="form-control" name="senha" id="senha" type="password" value="" autocomplete="off"><span></span>
                                        <span class="forca-senha"><p>Força da Senha:</p>
                                            <div class="progress progress-striped active" id="mtSenha">
                                              <div class="progress-bar progress-bar-danger"  role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100">
                                                <span class="sr-only"></span>
                                              </div>
                                            </div>
                                        </span>
                                        <input type="hidden" name="validou" id="validou">
                                    </div>
                                </div>
                                <br>
                                <div class="col-sm-offset-8 col-sm-4">
                                    <input type="hidden" name="inserir">
                                    <a class="btn btn-lg btn-success btn-block" onClick="verificacao()">OK</a>
                                </div>         
                            </fieldset>
                        </form>
                    </div>
                </div>
            </div>

        </div>

        <div class="row" id="list">
                <div class="col-lg-12">
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            Usuários cadastrados
                            <a <?=$acao!=''?'href="?acao=novo"':'class="add"'?> style="float:right;" onClick="$('#edit').slideDown();">
                                <div class="btn btn-success btn-xs"><i class="glyphicon glyphicon-plus-sign"></i> Novo</div>
                            </a>
                        </div>
                        <div class="panel-body">
                            <? if ($total_reg==0){ echo "Nenhum registro encontrado.";} else { ?>
                            <div class="table-responsive">
                                <table class="table table-striped table-bordered table-hover" id="dataTables-example">
                                    <thead>
                                        <tr>
                                            <th>Nome</th>
                                            <th>Usuário</th>
                                            <th>Tipo de Usuário</th>
                                            <th>Áreas de Acesso</th>
                                            <th>Status</th>
                                            <th style="width:30px;"></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <? $cont = 1; $tipo = array('','Administrador', 'Moderador', 'Usuário'); 
                                        foreach ($sqlt as $key => $t) {
                                            $aa =  explode(',', $t['areasAcesso']); $nomeA = '';
                                            foreach ($aa as $idA) { 
                                                $areas = $DB->prepare('SELECT nomeArea FROM boss_areasacesso where id=:id');
                                                $areas->bindValue(':id', $idA, PDO::PARAM_INT);
                                                if(!($areas->execute())) print_r($areas->errorInfo());
                                                
                                                $nomeA .= ($nomeA!=''?', ':'').$areas->fetchColumn();
                                            }
                                            ?>
                                            <tr class="<?=$cont%2==0?'odd':'even'?>">
                                                <td><?=$t['nome']?></td>
                                                <td><?=$t['login']?></td>
                                                <td><?=$tipo[($t['tipoUsuario'])]?></td>
                                                <td><?=$t['tipoUsuario']=='1'?'Todas':$nomeA?></td>
                                                <td><?=$t['bloqueado']==0?'<i class="glyphicon glyphicon-ok-circle" style="color:green;"></i> Ativo':'<i class="glyphicon glyphicon-ban-circle" style="color:red;"></i> Bloqueado'?></td>
                                                <th style="width:50px; text-align:center;">
                                                    <a href="?acao=editar&id=<?=$t['id']?>" title="Editar" name="Editar" style="margin-top:10px;">
                                                        <div class="btn btn-info btn-xs"><i class="glyphicon glyphicon-pencil"></i></div>
                                                    </a>
                                                    <a href="?acao=excluir&id=<?=$t['id']?>&nomee=<?=$t['nome']?>" title="Excluir" name="Excluir">
                                                        <div class="btn btn-danger btn-xs"><i class="glyphicon glyphicon-trash"></i></div>
                                                    </a>
                                                    <a href="?acao=<?=$t['bloqueado']==1?'des':''?>bloquear&id=<?=$t['id']?>&nomee=<?=$t['nome']?>" title="Bloquear / Desbloquear" name="Bloquear / Desbloquear">
                                                        <div class="btn btn-warning btn-xs"><i class="glyphicon glyphicon-ban-circle"></i></div>
                                                    </a>
                                                </th>
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