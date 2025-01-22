    <?
    $email_mod = EMAIL_TATICA;
    $sql_mod = CRUD::SELECT('', 'boss_login', 'tipoUsuario=2', '', ''); 
    foreach ($sql_mod as $key => $mod) $email_mod = $email_mod==''?$mod['email']:$email_mod.', '.$mod['email'];

    $loga = isset($_POST['loga']) ? $_POST['loga'] : "";
    $login = isset($_POST['login']) ? $_POST['login'] : "";
    $senha = isset($_POST['senha']) ? md5($_POST['senha']) : "";

    if (($loga=='true') and ($senha!=='') and ($login!=='')) {   
        
        $sql = CRUD::SELECT('', 'boss_login', 'login=:login', array('login'=>$login), ''); 
        $total_reg = sizeof($sql);

        if($total_reg>0){
            foreach ($sql as $key => $value) $dados = $value;
            if($senha == $dados['senha']){
                if($dados['bloqueado']==0){
                    $_SESSION[NOME_SESSAO] = array("id"=>$dados['id'], "login"=>$dados['login'], "nome"=>$dados['nome'], "tipoUsuario"=>$dados['tipoUsuario'], "areasAcesso"=> explode(',', $dados['areasAcesso']), "redefinirSenha"=>$dados['redefinirSenha'], "id"=>$dados['id'],);
                    $_SESSION['time']=time(); 
                    $_SESSION['senhaErrada'.$login] = 0;
                    print "<script>window.location = window.location.href;</script>";

                } else {

                    print "<script>alert('Sua senha foi bloqueada. Entre em contato com um administrador.');</script>";    

                    //print "<script>window.location='".SITE_URL."boss/senhaBloqueada.php';</script>";  
                    //sua conta esta bloqueada ...  clique aqui para saber como desbloqueá-la : contate-a-tatica.php?duvida=conta-bloqueada
                }
            } else {
                if(!isset($_SESSION['senhaErrada'.$login])){
                    $_SESSION['senhaErrada'.$login] = 1;
                } else {
                    $_SESSION['senhaErrada'.$login] ++;
                }
                $i = $_SESSION['senhaErrada'.$login];
                if($_SESSION['senhaErrada'.$login]>=5){ 

                    //reseta a contagem
                    $_SESSION['senhaErrada'.$login] = 0;

                    CRUD::UPDATE('boss_login', array('bloqueado'=>1),$dados['id']);
                    CRUD::INSERT('alerta', array('tipo'=>'Senha Errada - 5 tentativas', 'login'=>$login, 'ip'=>$_SERVER['REMOTE_ADDR'], 'data'=>date("Y-m-d H:i:s")));

                    //ENVIA EMAIL DE ALERTA PARA TÁTICA                       

                        // montando o email
                        $site = NOME_SITE;
                        $destinatarios = EMAIL_TATICA;
                        $nomeRemetente = NOME_SITE;
                        $usuario = USUARIO_EMAIL_AUTENTICADO;
                        $senha = SENHA_EMAIL_AUTENTICADO;
                        $subject = "Possibilidade de Ataque: $site";
                        $mensagem = "
                        <h3><b>O Site $site pode ter sido alvo de ataque</b></h3>
                        <p>Identificamos várias tentativas de login no BOSS do site $site com o usuário $login, por segurança a conta foi bloqueada e uma nova senha deve ser gerada.<br>Detalhes sobre o ataque:
                        <br><br>
                        Tipo: Senha Errada - 5 tentativas<br>
                        Usuário: $login<br>
                        Data: ".date("d/m/Y H:i:s")."<br>
                        Endereço IP: ".$_SERVER['REMOTE_ADDR']."</p>";




                        /*********************************** A PARTIR DAQUI NAO ALTERAR ************************************/

                        include_once("../envio/class.phpmailer.php");

                        $To = $destinatarios;
                        $Subject = sprintf('=?%s?%s?%s?=', 'UTF-8', 'B', base64_encode($subject));

                        $Message = utf8_decode($mensagem);
                        $Host = 'smtp.'.substr(strstr($usuario, '@'), 1);
                        $Username = $usuario;
                        $Password = $senha;
                        $Port = "587";

                        $mail = new PHPMailer();
                        $body = $Message;
                        $mail->IsSMTP(); // telling the class to use SMTP
                        $mail->Host = $Host; // SMTP server
                        $mail->SMTPDebug = 0; // enables SMTP debug information (for testing)
                        // 1 = errors and messages
                        // 2 = messages only
                        $mail->SMTPAuth = true; // enable SMTP authentication
                        $mail->Port = $Port; // set the SMTP port for the service server
                        $mail->Username = $Username; // account username
                        $mail->Password = $Password; // account password

                        $mail->SetFrom($usuario, $nomeRemetente);
                        $mail->Subject = $Subject;
                        $mail->MsgHTML($body);
                        $mail->AddAddress($To, "");
                        $sql_mod = CRUD::SELECT('', 'boss_login', 'tipoUsuario=2', '', ''); 
                        foreach ($sql_mod as $key => $mod) $mail->AddAddress($mod['email'], "");

                        if($mail->Send()){
                            //echo "ok";
                        }  else {
                            //echo "erro";
                        }

                    print "<script>window.location='".SITE_URL."boss/senhaBloqueada.php';</script>";  //sua conta esta bloqueada ...  clique aqui para saber como desbloqueá-la : contate-a-tatica.php?duvida=conta-bloqueada
                } else{
                    $msgSenha =  "<h6 class='msgerro'>Senha incorreta! Tentativa: $i de 5</h6>"; 
                }
            }
        } else {
            if(!isset($_SESSION['loginInexistente'])){
                $_SESSION['loginInexistente'] = 1;
            } else {
                $_SESSION['loginInexistente'] ++;
            }
            $msg =  "<h6 class='msgerro'>Usuário e/ou Senha incorretos</h6>"; 
        }
    }
            
    ?>

    <script type="text/javascript">
        $(document).ready(function () {
          var validateUsername = $('#validateUsername');
          $('#login').keyup(function () {
            var t = this; 
            if (this.value != this.lastValue) {
              if (this.timer) clearTimeout(this.timer);
              validateUsername.removeClass('error').html('<img src="img/loading.gif" height="16" width="16" style="float:right; margin:-25px 5px 0 0;" />');
              
              this.timer = setTimeout(function () {
                $.ajax({
                  url: '<?=SITE_URL?>boss/includes/Funcoes/ajax-username-validation.php',
                  data: 'action=check_username&table=login&field=login&username=' + t.value,
                  dataType: 'json',
                  type: 'post',
                  success: function (j) {
                    if(j.msg==1){
                        $('#login').addClass("error");
                        validateUsername.html('<h6 class="msgerro">Insira o nome de usuário</h6>');
                    } else if(j.msg==2){
                        $('#login').addClass("error");
                        validateUsername.html('<h6 class="msgerro">Apenas letras e/ou números</h6>');
                    } else {
                        $('#login').removeClass("error");
                        validateUsername.html('');
                    }
                  }
                });
              }, 200);
              
              this.lastValue = this.value;
            }
          });

            var validatePassword = $('#validatePassword');
            $('#senha').keyup(function () {
                if(this.value ==''){
                    $('#senha').addClass("error");
                    validatePassword.html('<h6 class="msgerro">Insira a senha</h6>');
                } else {
                    $('#senha').removeClass("error");
                    validatePassword.html('');
                }
            });
        });

        function verificacao(){
            var erros=0;
            if (document.form.login.value == ""){ $('#login').addClass("error"); $('#validateUsername').html('<h6 class="msgerro">Insira o nome de usuário</h6>'); erros=erros+1; }
            if (document.form.senha.value ==""){ $('#senha').addClass("error"); $('#validatePassword').html('<h6 class="msgerro">Insira a senha</h6>'); erros=erros+1; }
            if (document.form.captcha_code.value ==""){ $('#captcha_code').addClass("error"); $('#validateCaptcha').html('<h6 class="msgerro">Insira o Código da imagem</h6>'); erros=erros+1; }

            var code = $('#captcha_code').val();
            $('#validaCaptcha').load('<?=SITE_URL?>boss/includes/securimage/sendCaptcha.php?code='+code, function(){
                if ($('#validaCaptcha').html() =="invalido"){ $('#captcha_code').addClass("error"); $('#validateCaptcha').html('<h6 class="msgerro">Código incorreto</h6>'); erros=erros+1; document.getElementById('captcha').src = '<?=SITE_URL?>boss/includes/securimage/securimage_show.php?' + Math.random(); }    
                if(erros==0){ document.form.loga.value = 'true'; document.form.submit(); }
            });            
        }

        document.onkeyup=function(e){
            if(e.which  == 13){ 
                //document.form.submit();
                verificacao();
            }
        }
    </script>
    
    <div class="container">
        <br><br>
        <div class="row">
            <div class="col-lg-6 col-lg-offset-3 col-sm-8 col-sm-offset-2">
                    <img src="<?=SITE_URL?>boss/img/logo.png" class="img-responsive logo login">
                    <div class="col-md-10 col-md-offset-1">    
                        <div class="login-panel panel panel-default">
                            <div class="panel-heading">
                                <h3 class="panel-title text-center">Insira o seu usuário e senha:</h3>
                            </div>
                            <div class="panel-body">
                                <?=isset($msg)?$msg:''?>
                                <form role="form" name="form" action="" method="post">
                                    <fieldset>
                                        <div class="input-group">
                                            <span class="input-group-addon"><i class="fa fa-user fa-fw"></i></span>
                                            <input class="form-control <?=isset($msgUser)?'error':''?>" placeholder="Usuário" name="login" id="login" type="login">
                                        </div><span id="validateUsername"></span>

                                        <div class="input-group">
                                            <span class="input-group-addon"><i class="fa fa-asterisk fa-fw"></i></span>
                                            <input class="form-control <?=isset($msgSenha)?'error':''?>" placeholder="Senha" name="senha" id="senha" type="password" value="">    
                                        </div><span id="validatePassword"><?=isset($msgSenha)?$msgSenha:''?></span>

                                        <div class="input-group">
                                            <span class="input-group-addon"><i class="fa fa-question fa-fw"></i></span>
                                            <input class="form-control" placeholder="Insira o código da imagem:" name="captcha_code" id="captcha_code" type="text" value="">
                                        </div> <span id="validateCaptcha"><?=isset($msgCaptcha)?$msgCaptcha:''?></span> 
                                        <div id="validaCaptcha" style="display:none;"></div>

                                        <div style="text-align:center;">
                                            <div class="btn btn-default attCaptcha" type="button" onclick="document.getElementById('captcha').src = '<?=SITE_URL?>boss/includes/securimage/securimage_show.php?' + Math.random(); return false" class="btn btn-info btn-sm" title="Trocar imagem" alt="Trocar imagem"><i class="fa fa-refresh fa-fw"></i><i style="font-size:12px;">Trocar</i></div>
                                            <img id="captcha" src="<?=SITE_URL?>boss/includes/securimage/securimage_show.php" alt="CAPTCHA Image" class="img-responsive img-thumbnail" style="margin-top:10px;padding-bottom:10px;width:225px; height:86px;"/>
                                        </div>

                                        <div class="checkbox">
                                            <label>
                                                <a data-toggle="modal" data-target="#esqueciSenha" class="abreEsqueciSenha"><h6 class="text-right">Esqueci minha senha</h6></a>
                                            </label>
                                        </div>
                                        <!-- Change this to a button or input when using this as a form -->
                                        <input type="hidden" name="loga">
                                        <a class="btn btn-lg btn-success btn-block" onClick="verificacao()">ENTRAR</a>
                                    </fieldset>
                                </form>
                            </div>
                        </div>
                    </div>
                    <div style="clear:both;"></div>
            </div>
        </div>
    </div>
    <? include('rodape.php'); ?>

<div class="modal fade" id="esqueciSenha">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
        <h4 class="modal-title">Esqueci minha senha</h4>
      </div>
      <div class="modal-body" id="loadMensagem">
        <form role="form" name="esqueciSenha" method="post" action="" id="esqueciSenha">
            <div class="col-xs-10">
                <input type="text" class="form-control" name="loginEsqueciSenha" id="loginEsqueciSenha" placeholder="Login"><span></span>
            </div>
            <div class="col-xs-2" style="padding: 0;">
                <input type="button" class="form-control btn btn-success" value="Enviar" id="loadBotao" onClick="verificaEsqueciSenha()">
                <input type="hidden" name="enviaEmail">
            </div>
            <div class="clearfix"></div>
        </form>
      </div>
    </div><!-- /.modal-content -->
  </div><!-- /.modal-dialog -->
</div><!-- /.modal -->

<script type="text/javascript">

    function verificaEsqueciSenha(){
        var erros=0;
        $('.error').next('span').html('');
        $('.error').removeClass('error');

        if (document.esqueciSenha.loginEsqueciSenha.value == ""){ $('#loginEsqueciSenha').addClass("error"); $('#loginEsqueciSenha').next('span').html('<h6 class="msgerro">Informe o Login</h6>'); erros=1; }
        if (erros==0){ 
            document.esqueciSenha.enviaEmail.value = 'true';
            $('#loadBotao').attr('value','Enviando...');
            $('#esqueciSenha').submit();
        } 
    }
      

    $('a.abreEsqueciSenha').click(function(){
        $('#loadMensagem').html('<p>Informe seu <b>usuário</b> abaixo:</p>'+  
        '<form role="form" name="esqueciSenha" method="post" action="" id="esqueciSenha">'+
            '<div class="col-xs-10">'+
                '<input type="text" class="form-control" name="loginEsqueciSenha" id="loginEsqueciSenha" placeholder="Usuário"><span></span>'+
            '</div>'+
            '<div class="col-xs-2" style="padding: 0;">'+
                '<input type="button" class="form-control btn btn-success" value="Enviar" id="loadBotao" onClick="verificaEsqueciSenha()">'+
                '<input type="hidden" name="enviaEmail">'+
            '</div>'+
            '<div class="clearfix"></div>'+
        '</form>');
    });

    $('#esqueciSenha').submit(function(){
        var login = $('#loginEsqueciSenha').val();
        $('#loadMensagem').load('enviaEsqueciSenha.php?login='+login);
        $('#loginEsqueciSenha').val('');
        return false;
    });

</script>