    <?
    $inserir = isset($_POST['inserir']) ? $_POST['inserir'] : "";
    $senha = isset($_POST['senha']) ? md5($_POST['senha']) : "";
    $novasenha = isset($_POST['novasenha']) ? md5($_POST['novasenha']) : "";
    $login_id = $_SESSION[NOME_SESSAO]['id'];
    $login = $_SESSION[NOME_SESSAO]['login'];

    if (($inserir=='true') and ($senha!=='') and ($novasenha!=='') and ($login_id!=='')) {  
        
        $sql = $DB->prepare('SELECT * from boss_login where id=:login_id');
        $sql->bindValue(':login_id', $login_id, PDO::PARAM_INT);
        $sql->execute();

        if( ($dados = $sql->fetch()) and ($senha == $dados['senha']) ){
        
            $sql = $DB->prepare("UPDATE  boss_login SET senha=:novasenha, redefinirSenha='0' where id=:login_id");
            $sql->bindValue(':login_id', $login_id, PDO::PARAM_INT);
            $sql->bindValue(':novasenha', $novasenha, PDO::PARAM_STR);
            $sql->execute();
            $_SESSION[NOME_SESSAO]['redefinirSenha'] = 0;
            print "<script>window.alert('Senha alterada com sucesso!');window.location = window.location.href;</script>";

        } else {    
            if(!isset($_SESSION['senhaErrada'.$login])){
                $_SESSION['senhaErrada'.$login] = 1;
            } else {
                $_SESSION['senhaErrada'.$login] ++;
            }
            $i = $_SESSION['senhaErrada'.$login];
            if($_SESSION['senhaErrada'.$login]>=5){ 
                $sql = $DB->prepare("UPDATE  boss_login SET bloqueado='1' where id=:login_id");
                $sql->bindValue(':login_id', $dados['login']['id'], PDO::PARAM_INT);
                $sql->execute();
                print "<script>window.location='contaBloqueada.php';</script>";  //sua conta esta bloqueada ...  clique aqui para saber como desbloqueá-la : contate-a-tatica.php?duvida=conta-bloqueada
            } else{
                $msgSenha =  "<h6 class='msgerro'>Senha incorreta! Tentativa: $i de 5</h6>"; 
            }
        }
    }
            
    ?>

    <script type="text/javascript" src="<?=BOSS_TATICA?>js/jquery.complexify.js"></script>
    <script type="text/javascript">

        $(document).ready(function () {
            $("#novasenha").complexify({minimumChars:6,strengthScaleFactor:0.8}, function (valid, complexity) { 
                $("#mtSenha .progress-bar").attr('aria-valuenow',complexity).css('width',complexity+'%'); 
                if(complexity>20) $("#mtSenha .progress-bar").removeClass('progress-bar-danger').addClass('progress-bar-info');
                if(complexity<20) $("#mtSenha .progress-bar").removeClass('progress-bar-info').removeClass('progress-bar-sucess').addClass('progress-bar-danger');
                if(valid) { $("#mtSenha .progress-bar").removeClass('progress-bar-info').addClass('progress-bar-success'); $("#validou").val("sim"); } else {$("#validou").val("");}
            });
          var validateUsername = $('#validateUsername');
          $('#login').keyup(function () {
            var t = this; 
            if (this.value != this.lastValue) {
              if (this.timer) clearTimeout(this.timer);
              validateUsername.removeClass('error').html('<img src="img/loading.gif" height="16" width="16" style="float:right; margin:-25px 5px 0 0;" />');
              
              this.timer = setTimeout(function () {
                $.ajax({
                  url: 'includes/Funcoes/ajax-username-validation.php',
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

            var validateSenha = $('#validateSenha');
            $('#senha').keyup(function () {
                if(this.value ==''){
                    $('#senha').addClass("error");
                    validateSenha.html('<h6 class="msgerro">Insira a senha</h6>');
                } else {
                    $('#senha').removeClass("error");
                    validateSenha.html('');
                }
            });

            $('.forca-senha').hide();

            $('#novasenha').focusin(function() {
                $('.forca-senha').slideDown();
            });
            $('#novasenha').focusout(function() {
                if (document.form.validou.value == ""){ $('#novasenha').addClass("error"); $('#validateNovaSenha').html('<h6 class="msgerro">Senha fraca</h6>'); erros=erros+1;$('#novasenha').focus(); }
                $('.forca-senha').slideUp();
            });
            var validateNovaSenha = $('#validateNovaSenha');
            $('#novasenha').keyup(function () {
                if(this.value ==''){
                    $('#novasenha').addClass("error");
                    validateNovaSenha.html('<h6 class="msgerro">Insira a nova senha</h6>');
                } else {
                    $('#novasenha').removeClass("error");
                    validateNovaSenha.html('');
                }
            });

            var validateConfSenha = $('#validateConfSenha');
            $('#confsenha').keyup(function () {
                if(this.value ==''){
                    $('#confsenha').addClass("error");
                    validateConfSenha.html('<h6 class="msgerro">Confirme a senha</h6>');
                } else {
                    $('#confsenha').removeClass("error");
                    validateConfSenha.html('');
                }
            });
        });

        function verificacao(){
            var erros=0;
            if (document.form.senha.value == ""){ $('#senha').addClass("error"); $('#validateSenha').html('<h6 class="msgerro">Informa sua senha atual</h6>'); erros=erros+1; }
            if (document.form.validou.value == ""){ $('#novasenha').addClass("error"); $('#validateNovaSenha').html('<h6 class="msgerro">Senha fraca</h6>'); erros=erros+1; }
            if (document.form.novasenha.value == ""){ $('#novasenha').addClass("error"); $('#validateNovaSenha').html('<h6 class="msgerro">Informa a nova senha</h6>'); erros=erros+1; }
            if (document.form.confsenha.value ==""){ $('#confsenha').addClass("error"); $('#validateConfSenha').html('<h6 class="msgerro">Confirme a senha</h6>'); erros=erros+1; }
            if (document.form.novasenha.value != document.form.confsenha.value){ $('#confsenha').addClass("error"); $('#validateConfSenha').html('<h6 class="msgerro">As senhas não conferem</h6>'); erros=erros+1; }
            if(erros==0){ document.form.inserir.value = 'true'; document.form.submit(); }
        }
    </script>
    
    <div class="container">
        <br><br>
        <div class="row">
            <div class="col-md-12">
                <div class="jumbotron">
                    <h2 class="text-center"><span class="glyphicon glyphicon-lock"></span> Trocar Senha</h2><br>
                    <p class="text-center">A sua senha ainda é temporária, <br>você deve configurar uma nova senha para ter acesso ao BOSS:</p>
                    <div class="col-md-4 col-md-offset-4">
                        <div class="login-panel panel panel-default">
                            <div class="panel-heading">
                                <h3 class="panel-title text-center">Escolha uma nova senha:</h3>
                            </div>
                            <div class="panel-body">
                                <form role="form" name="form" action="" method="post">
                                    <fieldset>
                                        <div class="form-group">
                                            <input class="form-control <?=isset($msgSenha)?'error':''?>" placeholder="Senha Atual" name="senha" id="senha" type="password" value="">
                                            <span id="validateSenha"><?=isset($msgSenha)?$msgSenha:''?></span>
                                        </div>
                                        <div class="form-group">
                                            <input class="form-control" placeholder="Nova Senha (mínimo 6 caracteres)" name="novasenha" id="novasenha" type="password" value="">
                                            <span id="validateNovaSenha"></span>
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
                                            <input class="form-control" placeholder="Confirmar Senha" name="confsenha" id="confsenha" type="password" value="">
                                            <span id="validateConfSenha"></span>
                                        </div>
                                        <div class="checkbox">
                                            <label>
                                                <a href="contate-a-tatica.php?duvida=esqueci-minha-senha"><h6 class="text-right">Esqueci minha senha</h6></a>
                                            </label>
                                        </div>
                                        <!-- Change this to a button or input when using this as a form -->
                                        <input type="hidden" name="inserir">
                                        <a class="btn btn-lg btn-success btn-block" onClick="verificacao()">OK</a>
                                    </fieldset>
                                </form>
                            </div>
                        </div>
                    </div>
                    <div style="clear:both;"></div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <nav class="navbar navbar-fixed-bottom rodape" role="navigation">
                    <p class="text-center">BOSS v.02 - Painel Administrativo por Tática Web<a href="http://www.taticaweb.com.br"><img src="img/tatica_web.png" align="right"></a></p>
                </nav>
            </div>
        </div>
    </div>


