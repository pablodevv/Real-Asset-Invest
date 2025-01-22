<? 
    include("connect.php"); 
    include("includes/Funcoes/Funcoes.php"); 

    $email_mod = EMAIL_TATICA;
    $sql_mod = CRUD::SELECT('', 'boss_login', 'tipoUsuario=2', '', ''); 
    foreach ($sql_mod as $key => $mod) $email_mod = $email_mod==''?$mod['email']:$email_mod.', '.$mod['email'];


    $login = isset($_GET['login'])?$_GET['login']:'';
    $sqlt = CRUD::SELECT('', 'boss_login', 'login=:login', array('login'=>$login), ''); 

    if(sizeof($sqlt)>0){

        $id = $sqlt[0]['id'];
        $email = $sqlt[0]['email'];
        $nome = $sqlt[0]['nome'];
        $login = $sqlt[0]['login'];
        // $site = NOME_SITE;

        $NovaSenha = rand(1,100000);

        $to = $email;    
        $de = USUARIO_EMAIL_AUTENTICADO;

        $destinatarios = $to;
        $nomeRemetente = NOME_SITE;
        $usuario = USUARIO_EMAIL_AUTENTICADO;
        $senha = SENHA_EMAIL_AUTENTICADO;
        $subject = "Solicitação de nova senha - ".NOME_SITE;
       


        $mensagem = '

        <html>
        <head>  
        <title>Nova mensagem de </title>
        </head>
        <body>         

            <font face="Arial, Helvetica, sans-serif" size="2" color="#5A4A42">

            <div style="background: #f8f8f8; display: block; padding: 0px; min-width: 500px;">
               
                <a href="'.SITE_URL.'"><img src="'.SITE_URL.'img/logo_topo.png" alt="'.NOME_SITE.'" style="display: block; margin: 0 auto; padding: 25px;"></a>
                <h1 style="color: #fff; background: #0769ad; font-size: 18px; text-align: center; margin: 0px; padding: 15px 0px; text-transform: uppercase;">Redefinição de Senha </h1>

                    <div style="padding: 30px; background: #fff; color: #000;">   
                        <div style="max-width: 500px; margin: 0 auto; color: #000; border: 1px solid #0769ad; border-radius: 5px; padding: 10px;">
                        <b style="color: #000;">Nova senha:</b> <span style="color: #000;"> Olá '.$nome.', a sua nova senha é <b>'.$NovaSenha.'</b></span><br>  
                        <b style="color: #000;">Obs:</b> <span style="color: #000;"> Entre normalmente com o seu usuário( <b>'.$login.'</b> ) e nova senha( <b>'.$NovaSenha.'</b> ) temporária. Depois que entrar vai aparecer a opção para inserir a sua nova senha definitiva.</span><br>    
                    </div>       
              
                </div>

            </div>       
            <div style="background: #fff; height: 40px; padding: 10px; width: 100%; display: block; margin: -20px 0 0 0;">
                <div style="display: block; max-width: 500px; margin: 0 auto; margin-top: 5px; font-size: 10px;">
                    <p style="display: block; float: left; color: #000;">Este email é enviado automaticamente, por favor não responda.</p>
                    <p style="display: block; float: right; color: #000;"><a style="color: #000;" href="'.SITE_URL.'">'.NOME_SITE.'</a></p>
                </div>
            </div>

        </body>
        </html>';   
      
        //echo $mensagem; die();  
        include_once("./envio/class.phpmailer.php");
   
        $To = $destinatarios;
        $Subject = sprintf('=?%s?%s?%s?=', 'UTF-8', 'B', base64_encode($subject));
        $Message = utf8_decode($mensagem);

        //$Message = utf8_decode($mensagem);
        //$Host = 'smtp.'.substr(strstr($usuario, '@'), 1);           //DREAMHOST USA mail. OUTROS USAM smtp.
        $Host = 'smtp.outlook.office365.com'; 
       
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
        $mail->SMTPSecure = "tls";
        $mail->Port = $Port; // set the SMTP port for the service server
        $mail->Username = $Username; // account username
        $mail->Password = $Password; // account password
       
        $mail->SetFrom($usuario, $nomeRemetente);
        $mail->Subject = $Subject;  
        $mail->MsgHTML($Message);

        //foreach ($To as $key) {
            $mail->AddAddress($To, $key);
        //}


        $ok = false;
        if($mail->Send()){
            $ok = true;
        }


        if( $ok == true ){


            $CryptNovaSenha = md5($NovaSenha);
            $params = array( 'redefinirSenha'=> 1 , 'senha'=> $CryptNovaSenha );
            if(CRUD::UPDATE( 'boss_login', $params, $id )) {   
                echo "Foi enviado uma mensagem para ($email) com todas as instruções para concluir a redefinição de senha. ";
            }

        }  else {   
   
            //echo "erro";
            echo "Desculpe, algum erro está impedindo a sua ação. Por favor tente novamente.";

        }



        // $id = $sqlt[0]['id'];
        // $email = $sqlt[0]['email'];
        // $nome = $sqlt[0]['nome'];
        // $login = $sqlt[0]['login'];
        // $site = NOME_SITE;

        // //ENVIA EMAIL P/ CONTATO   

        // // montando o email
        // $destinatarios = $email;
        // $nomeRemetente = 'teste';
        // $usuario = USUARIO_EMAIL_AUTENTICADO;
        // $senha = SENHA_EMAIL_AUTENTICADO;
        // $subject = "Solicitação de nova senha";
        // $mensagem = "
        // <h4><b>Solicitação de nova senha</b></h4>
        // <p>Um usuário solicitou a geração de uma nova senha para acesso ao BOSS: <br>
        // <br>
        // <b>Site:</b> $site (<a href='".SITE_URL."'>".SITE_URL."</a>)<br>
        // <b>Nome:</b> $nome<br>
        // <b>Login:</b> $login<br>
        // <b>Email:</b> $email<br>
        // <br>
        // Favor entrar em contato.
        // </p>";




        // /*********************************** A PARTIR DAQUI NAO ALTERAR ************************************/

        // include_once("./envio/class.phpmailer.php");

        // $To = $destinatarios;
        // $Subject = sprintf('=?%s?%s?%s?=', 'UTF-8', 'B', base64_encode($subject));

        // $Message = utf8_decode($mensagem);
        // //$Host = 'smtp.'.substr(strstr($usuario, '@'), 1);
        
        // $Host = 'smtp.outlook.office365.com'; 
    
        // $Username = $usuario;
        // $Password = $senha;
        // $Port = "587";

        // $mail = new PHPMailer();
        // $body = $Message;
        // $mail->IsSMTP(); // telling the class to use SMTP
        // $mail->Host = $Host; // SMTP server
        // $mail->SMTPDebug = 1; // enables SMTP debug information (for testing)
        // // 1 = errors and messages  
        // // 2 = messages only
        // $mail->SMTPAuth = true; // enable SMTP authentication
        // $mail->Port = $Port; // set the SMTP port for the service server
        // $mail->Username = $Username; // account username
        // $mail->Password = $Password; // account password

        // $mail->SetFrom($usuario, $nomeRemetente);
        // $mail->Subject = $Subject;
        // $mail->MsgHTML($body);
        // $mail->AddAddress($To, "");
        // $sql_mod = CRUD::SELECT('', 'boss_login', 'tipoUsuario=2', '', ''); 
        // foreach ($sql_mod as $key => $mod) $mail->AddAddress($mod['email'], "");

        // if($mail->Send()){

        //     //echo "ok";
        //     echo "Um email foi enviado para nossa equipe de atendimento que logo irá entrar em contato através do seu email $email, por favor aguarde.";

        // }  else {

        //     //echo "erro";
        //     echo "Desculpe, algum erro está impedindo a sua ação. Por favor tente novamente.";

        // }

    
    } else { ?>
        <p>Informe seu <b>usuário</b> abaixo:</p>  
        <form role="form" name="esqueciSenha" method="post" action="" id="esqueciSenha">
            <div class="col-xs-10">
                <input type="text" class="form-control" name="loginEsqueciSenha" id="loginEsqueciSenha" placeholder="Login"><span></span>
                <h6 class='msgerro'>Login não encontrado!!</h6>
            </div>
            <div class="col-xs-2" style="padding: 0;">
                <input type="button" class="form-control btn btn-success" value="Enviar" id="loadBotao" onClick="verificaEsqueciSenha()">
                <input type="hidden" name="enviaEmail">
            </div>
            <div class="clearfix"></div>
            
        </form>
    <? }
       
?>