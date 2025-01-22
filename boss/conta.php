<? include("connect.php"); include("includes/Funcoes/sessao.php"); include("includes/Funcoes/Funcoes.php");
$file = explode("/", $_SERVER['PHP_SELF']); $file = end($file);

$nomeU = $_SESSION[NOME_SESSAO]['nome'];
$area = 'Usuários';

/* Seleciona dados do usuário */
$id = $_SESSION[NOME_SESSAO]['id'];
$a = CRUD::SELECT_ID('', 'boss_login', $id);

/* Selciona timeline do usuário */
$sqlTL = CRUD::SELECT('', 'boss_timeline', 'idUsuario=:id' , array('id'=>$id), 'order by data desc'); 
$total_reg = sizeof($sqlTL);

?>
<!DOCTYPE html>
<html>

<head>

    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Conta - BOSS v.3</title>

    <link href="//netdna.bootstrapcdn.com/bootstrap/3.0.0/css/bootstrap.min.css" rel="stylesheet">
    <link href="//netdna.bootstrapcdn.com/font-awesome/4.0.3/css/font-awesome.css" rel="stylesheet">
    <link href="<?=BOSS_TATICA?>css/sb-admin.css" rel="stylesheet">
    <link href="<?=BOSS_TATICA?>js/plugins/select2-3.4.5/select2.css" rel="stylesheet"/>
    <link href="<?=BOSS_TATICA?>css/plugins/dataTables/dataTables.bootstrap.css" rel="stylesheet">
    <link href="<?=BOSS_TATICA?>js/SimpleNotifications/style.css" rel="stylesheet"/>
    <link href="<?=BOSS_TATICA?>css/estilo.css" rel="stylesheet">

    <script src="<?=BOSS_TATICA?>js/jquery-1.10.2.js"></script>
    <script src="//netdna.bootstrapcdn.com/bootstrap/3.0.0/js/bootstrap.min.js"></script>
    <script src="<?=BOSS_TATICA?>js/plugins/metisMenu/jquery.metisMenu.js"></script>
    <script src="<?=BOSS_TATICA?>js/sb-admin.js"></script>
    <script src="<?=BOSS_TATICA?>js/plugins/select2-3.4.5/select2.js"></script>
    <script src="<?=BOSS_TATICA?>js/plugins/dataTables/jquery.dataTables.js"></script>
    <script src="<?=BOSS_TATICA?>js/plugins/dataTables/dataTables.bootstrap.js"></script>
    <script src="<?=BOSS_TATICA?>js/plugins/dataTables/datetime-br.js"></script>
    <script src="<?=BOSS_TATICA?>js/SimpleNotifications/ttw-simple-notifications.js"></script> 

</head>

<body>

    <?
    $inserir = isset($_POST['inserir']) ? $_POST['inserir'] : "";
    $nome = isset($_POST['nome']) ? $_POST['nome'] : "";
    $email = isset($_POST['email']) ? $_POST['email'] : "";
    $login = isset($_POST['login']) ? $_POST['login'] : "";
    $senha = isset($_POST['senha']) ? md5($_POST['senha']) : "";
    $novasenha = isset($_POST['novasenha'])? $_POST['novasenha'] : "";
    

    if (($inserir=='true') and ($senha!=='') and ($id!=='')) {  

        if($senha == $a['senha']){
            $params = $novasenha == ''? array('nome'=>$nome, 'email'=>$email, 'login'=>$login) : array('nome'=>$nome, 'email'=>$email, 'login'=>$login, 'senha'=>md5($novasenha));
            if(CRUD::UPDATE('boss_login', $params,$id)) print "<script>window.location='$file?alert=Dados alterados&tp-alert=success';</script>";

            //TIMELINE
            TIMELINE::add($idU, $nomeU, $area, "Editou o próprio perfil");

        } else {    
            if(!isset($_SESSION['senhaErrada'.$login])){
                $_SESSION['senhaErrada'.$login] = 1;
            } else {
                $_SESSION['senhaErrada'.$login] ++;
            }
            $i = $_SESSION['senhaErrada'.$login];
            if($_SESSION['senhaErrada'.$login]>=5){ 
                print "
                <script>
                    if (confirm('Você já errou sua senha 5 vezes. Se esqueceu sua senha entre em contato com a Tática Web. Ir p/ página de contato?')) {
                        window.location='".SITE_URL."boss/faq.php#esqueci-minha-senha';
                    }
                </script>";

                //TIMELINE
                TIMELINE::add($idU, $nomeU, $area, "Bloqueou sua senha");
            } else{
                $msgSenha =  "<h6 class='msgerro'>Senha incorreta!</h6>"; 
            }
        }
    }
            
    ?>

    <script type="text/javascript" src="<?=BOSS_TATICA?>js/jquery.complexify.js"></script>
    <script type="text/javascript">

        $(document).ready(function () {

            var notifications = $('body').ttwSimpleNotifications();
            <?=isset($_GET['alert'])?'notifications.show("'.$_GET['alert'].'");':'';?>
            <?=isset($_GET['tp-alert'])?'$(".ttw-simple-notification").addClass("'.$_GET['tp-alert'].'");':'';?>

            $('#dataTables').dataTable({
                "aaSorting": [[ 0, "desc" ]], //ordenação padrão da tabela por ordem decrescente(desc) da primeira coluna(0)
                "aoColumns": [
                    { "sType": "datetime-br" }, //opção de ordenar por data
                    null,
                    null
                ]
            });

            $("#tipoUsuario").select2();

            $("#areasAcesso").select2({
                data:[
                    <? $areas = CRUD::SELECT('', 'boss_areasacesso', '', '', ''); $i=1;
                    foreach($areas as $aa){
                        echo ($i==1?"":",").'{id:'.$aa['id'].',text:"'.$aa['nomeArea'].'"}'; $i++;
                    }?>
                ],
                multiple: true
            });


            $('.escondeNovaSenha').hide();

            $('.forca-senha').hide();
            $('#novasenha').focusin(function() {
                $('.forca-senha').slideDown();
            }).focusout(function() {
                $('.forca-senha').slideUp();
            });

            $("#novasenha").complexify({minimumChars:6,strengthScaleFactor:0.8}, function (valid, complexity) { 
                $("#mtSenha .progress-bar").attr('aria-valuenow',complexity).css('width',complexity+'%'); 
                if(complexity>20) $("#mtSenha .progress-bar").removeClass('progress-bar-danger').addClass('progress-bar-info');
                if(complexity<20) $("#mtSenha .progress-bar").removeClass('progress-bar-info').removeClass('progress-bar-sucess').addClass('progress-bar-danger');
                if(valid) { $("#mtSenha .progress-bar").removeClass('progress-bar-info').addClass('progress-bar-success'); $("#validou").val("sim"); } else {$("#validou").val("");}
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
                  data: 'action=check_exist_username&table=login&field=login&username=' + t.value+'&userAntigo=<?=$a['login']?>',
                  dataType: 'json',
                  type: 'post',
                  success: function (j) {
                    if(j.msg==1){
                        $('#login').addClass("error");
                        validateUsername.html('<h6 class="msgerro">Insira o nome de usuário</h6>');
                    } else if(j.msg==2){
                        $('#login').addClass("error");
                        validateUsername.html('<h6 class="msgerro">Apenas letras(minísculas) e números</h6>');
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
            if ($('#nome').val() == ""){ $('#nome').addClass("error"); $('#nome').next('span').html('<h6 class="msgerro">Informe o Nome</h6>'); erros=1; }
            if ($('#login').val() == ""){ $('#login').addClass("error"); $('#login').next('span').html('<h6 class="msgerro">Informe o Usuário</h6>'); erros=1; }
            if ($('#email').val() == ""){ $('#email').addClass("error"); $('#email').next('span').html('<h6 class="msgerro">Informe o Email</h6>'); erros=1; }
                else if (!valida_email($('#email').val())){ $('#email').addClass("error"); $('#email').next('span').html('<h6 class="msgerro">Email inválido</h6>'); erros=1; }
            if ($('#senha').val() == ""){ $('#senha').addClass("error"); $('#senha').next('span').html('<h6 class="msgerro">Informe sua senha atual</h6>'); erros=1; }
            if ($('#novasenha').val() != ""){
                if ($('#confsenha').val() ==""){ $('#confsenha').addClass("error"); $('#confsenha').next('span').html('<h6 class="msgerro">Confirme a senha</h6>'); erros=1; }
                if ($('#novasenha').val() != $('#confsenha').val()){ $('#confsenha').addClass("error"); $('#novasenha').next('span').html('<h6 class="msgerro">As senhas não conferem</h6>'); erros=1; }
            }
            if(erros==0){ $('#inserir').val('true'); document.form.submit(); }
        }
    </script>

    <? include('topo.php');include('menu.php');?>
    
    <div id="page-wrapper">
        <div class="row">
            <div class="col-lg-12">
                <h1 class="page-header">Configurações da conta</h1>
            </div>
            <!-- /.col-lg-12 -->
        </div>

        <div class="row">
            <div class="col-lg-4 col-md-6">
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <i class="fa fa-user fa-fw"></i> Dados Pessoais
                    </div>
                    <div class="panel-body">
                        <form role="form" name="form" action="" method="post">
                            <fieldset>
                                <div class="form-group">
                                    <label for="nome" class="control-label">Nome</label>
                                    <input class="form-control <?=isset($msgNome)?'error':''?>" name="nome" id="nome" type="text" value="<?=$a['nome']?>"><span></span>
                                </div>
                                <div class="form-group">
                                    <label for="login" class="control-label">Usuário</label>
                                    <input class="form-control" name="login" id="login" type="text" value="<?=$a['login']?>"><span></span>
                                    <input type="hidden" name="loginExiste">
                                </div>

                                <div class="form-group">
                                    <label for="email" class="control-label">Email</label>
                                    <input class="form-control" name="email" id="email" type="text" value="<?=$a['email']?>"><span></span>
                                </div>

                                <div class="form-group">
                                    <label for="nome" class="control-label">Tipo de Usuário</label><br>
                                    <select name="tipoUsuario" id="tipoUsuario" disabled>
                                        <option value="1" <?=$a['tipoUsuario']==1?'selected':''?>>Administrador</option>
                                        <option value="2" <?=$a['tipoUsuario']==2?'selected':''?>>Moderador</option>
                                        <option value="3" <?=$a['tipoUsuario']==3?'selected':''?>>Usuário</option>
                                    </select><span></span>
                                </div>
                                <div class="form-group">
                                    <label for="email" class="control-label">Áreas de Acesso</label><br>
                                        <? $tipoU = isset($a['tipoUsuario'])?$a['tipoUsuario']:'';
                                        if($tipoU=='1'){
                                            echo '<input class="form-control" readonly type="text" value="Um Administrador tem acesso à todas as áreas"><span></span>';
                                        }else{
                                            echo '<input type="hidden" name="areasAcesso" disabled id="areasAcesso" value="'.(isset($a['areasAcesso'])?$a['areasAcesso']:'').'"><span></span>';
                                        }?>  
                                    <span></span>
                                </div>

                                <div class="clearfix"></div>
                                <div class="form-group col-xs-8" style="padding-left: 0;">
                                    <label for="senha" class="control-label">Senha Atual</label>
                                    <input class="form-control <?=isset($msgSenha)?'error':''?>" name="senha" id="senha" type="password" value="">
                                    <span><?=isset($msgSenha)?$msgSenha:''?></span>
                                </div>

                                <div class="btn btn-info col-xs-4" onClick="$('.escondeNovaSenha').slideToggle();" style="margin-top:25px;">Nova Senha</div>
                                <div class="clearfix"></div>
                                <div class="escondeNovaSenha">
                                    <div class="form-group">
                                        <label for="senha" class="control-label">Nova Senha</label>
                                        <input class="form-control" placeholder="(mínimo 6 caracteres)" name="novasenha" id="novasenha" type="password" value="">
                                        <span></span>
                                        <span class="forca-senha"><p>Força da Senha:</p>
                                            <div class="progress progress-striped active" id="mtSenha">
                                              <div class="progress-bar progress-bar-danger"  role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100">
                                                <span class="sr-only"></span>
                                              </div>
                                            </div>
                                        </span>
                                        <input type="hidden" name="validou" id="validou">
                                    </div>
                                    <div class="form-group">
                                        <label for="senha" class="control-label">Confirmar Senha</label>
                                        <input class="form-control" name="confsenha" id="confsenha" type="password" value="">
                                        <span></span>
                                    </div>
                                </div>
                                
                                <input type="hidden" name="inserir" id="inserir">
                                <a class="btn btn-lg btn-success btn-block" onClick="verificacao()">Alterar</a>
                            </fieldset>
                        </form>
                    </div>
                </div>
            </div>

            <div class="col-lg-8  col-md-6">
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <i class="fa fa-clock-o fa-fw"></i> Minha Timeline
                    </div>
                    <div class="panel-body">

                        <? if ($total_reg==0){ echo "Nenhum registro encontrado.";} else { ?>
                            <div class="table-responsive">
                                <table class="table table-striped table-bordered table-hover" id="dataTables">
                                    <thead>
                                        <tr>
                                            <th>Data - Hora</th>
                                            <th>Ação</th>
                                            <th>Área</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <? $cont = 1; $tipo = array('','Administrador', 'Moderador', 'Usuário'); 
                                        foreach ($sqlTL as $key => $t) {
                                            ?>
                                            <tr class="<?=$cont%2==0?'odd':'even'?>">
                                                <td><?=Funcoes::fdata(substr($t['data'], 0, 10), "-")?> - <?=substr($t['data'], 11, 8)?></td>
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