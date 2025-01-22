<?
// error_reporting(E_ALL);
// ini_set('display_errors', 1);

if (!class_exists('Conexao')) {

    DEFINE('DB_HOST','mysql.site2.taticaweb.com.br');
    DEFINE('DB_USER','boss_site2');
    DEFINE('DB_PASS','mXpMqNzpj7GYC8H');
    DEFINE('DB_TABLE','boss_site2');
    DEFINE('SITE_URL','https://site2.taticaweb.com.br/');
  
    //DEFINIR NOME DA SESS�O 


    
    DEFINE('NOME_SESSAO', 'login_bossv3_teste_1');
    DEFINE('NOME_SITE', 'Teste');
    DEFINE('NOME_SITE_EMAIL', 'Teste');
    DEFINE('BOSS_TATICA', 'https://www.taticaweb.com.br/bossv3/');
    
    DEFINE('EMAIL_TATICA', 'contato@taticaweb.com.br');

    DEFINE('USUARIO_EMAIL_AUTENTICADO', 'marketing@taticaweb.com.br');
    DEFINE('SENHA_EMAIL_AUTENTICADO', 'INTEGRADA*2060');
  
    DEFINE('EMAIL_CONTATO', 'contato@taticaweb.com.br');

    class Conexao extends PDO {  
          private static $instancia;
     
        public function Conexao($dsn, $username = "", $password = "") {
             //O construtro abaixo � o do PDO
            parent::__construct($dsn, $username, $password);
        }
     
        public static function getInstance() {
            if(!isset( self::$instancia )){
                try {
                    self::$instancia = new Conexao("mysql:host=".DB_HOST.";dbname=".DB_TABLE, DB_USER , DB_PASS, array( PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8", PDO::MYSQL_ATTR_USE_BUFFERED_QUERY => true ));
                } catch ( Exception $e ) {
                    echo 'Erro ao conectar';
                    exit ();
                }
            }
            return self::$instancia;
        }
    }

    $DB = Conexao::getInstance();
    $a = $DB->query("SET NAMES utf8;SET character_set_connection=utf8;SET character_set_client=utf8;SET character_set_results=utf8;SET time_zone='-3:00';");
    $a->closeCursor();

    
    date_default_timezone_set('America/Sao_paulo');
    setlocale(LC_TIME, 'pt_BR', 'pt_BR.utf-8', 'pt_BR.utf-8', 'portuguese');

    /* ============= CRUD ==================*/
    include('includes/Funcoes/CRUD.php');
    include('includes/Funcoes/Timeline.php');

    if (session_status() == PHP_SESSION_NONE) {
        session_start();
    }
}
?>
