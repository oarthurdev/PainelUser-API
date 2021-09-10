<?php
use Silex\Application;
use Silex\Provider\TwigServiceProvider;
use Silex\Provider\DoctrineServiceProvider;
use Silex\Provider\FormServiceProvider;
use Silex\Provider\UrlGeneratorServiceProvider;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Doctrine\DBAL\Configuration;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Silex\Provider\SwiftmailerServiceProvider;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

error_reporting(E_ALL);
ini_set('display_errors', true);

define('PATHUPLOADUSERIMAGE', 'C:/xampp/htdocs/PainelUser-API/Upload/User/ImagemPerfil/');

date_default_timezone_set('America/Sao_Paulo');

require __DIR__ . '/vendor/autoload.php';
require __DIR__ . '/vendor/PHPMailer/phpmailer/src/Exception.php';
require __DIR__ . '/vendor/PHPMailer/phpmailer/src/PHPMailer.php';
require __DIR__ . '/vendor/PHPMailer/phpmailer/src/SMTP.php';
require __DIR__ . '/classes/charInfo.php';
require __DIR__ . '/classes/manageChar.php';

$app = new Application();

$app['debug'] = true;
$app->register(new Silex\Provider\SessionServiceProvider());
$app->register(new Silex\Provider\SwiftmailerServiceProvider());

$app['session.storage.handler'] = null;

include "include/banco.inc.php";
include "include/mail.inc.php";
include "include/config.inc.php";
include "class.func.php";


$app['charInfo'] = new CharInfo();
$app['manageChar'] = new ManageChar();

function anti_sqli($sql)
{
	// remove palavras que contenham sintaxe sql
	$sql = preg_replace(sql_regcase("/(from|select|insert|delete|where|drop table|show tables|#|\*|--|\\\\)/"),"",$sql);
	$sql = trim($sql);//limpa espaços vazio
	$sql = strip_tags($sql);//tira tags html e php
	$sql = addslashes($sql);//Adiciona barras invertidas a uma string
	return $sql;
}

$app['user.controller'] = function ($app) {
  return new User\UserController();
};

$app->before(function (Request $request, Application $app) {
  $token = $request->headers->get('Token');
  $method = $request->getMethod();
  $route = $request->getPathInfo();
  
  $sql = "SELECT username FROM users WHERE token like :token";
  $app['user'] = $app['db']->fetchAssoc($sql, array('token' => $token));

  // if (!$app['user'] && $method != 'OPTIONS' && $route != '/login') {
  //     return new \Symfony\Component\HttpFoundation\JsonResponse(null, 401);
  //   }
  }, Application::EARLY_EVENT);
  
  $app->after(function (Request $request, Response $response) {
    $response->headers->set('Access-Control-Allow-Origin', '*');
    $response->headers->set('Access-Control-Allow-Headers', 'Access-Control-Allow-Origin, Token, Origin, X-Requested-With, Content-Type, Accept, Authorization');
  });
  
  $app->options("{anything}", function () {
    return new \Symfony\Component\HttpFoundation\JsonResponse(null, 204);
  })->assert("anything", ".*");
  
  $app->get('/', function(Application $app){
    return $app->redirect('/painelgmgothicpt/login');
  });
  
  $app->post('/criar-char', function(Request $request) use ($app){
    $rootDir = "C:/ServerPT/"; 
    $addOnLeft = '';
    $dirUserData = $rootDir."DataServer/userdata/";
    $dirUserInfo = $rootDir."DataServer/userinfo/";
    $dirUserDelete = $rootDir."DataServer/deleted/";
    error_reporting(E_ALL);
    $data = json_decode($request->getContent(), true);
    
    $func = new Func;
    
    //include_once("gravarchar.php");
    
    // Fill in 00 to left character
    $id           = $data['idGame'];
    
    $classe       = $data['classe'];
    $nomedochar   = $data['newNick'];
    
    $qCharID      = null;
    $leftLen      = null;
    $addOnLeft    = null;
    $writeAccName = null;
    $charInfo     = null;
    
    $qCharID  = $id;
    
    $leftLen=10-strlen($qCharID);
    for($i=0;$i<$leftLen;$i++)
    {
      $addOnLeft.=pack("h*",00);
    }
    
    $writeAccName =  $qCharID.$addOnLeft;
    
    $charInfo     = $dirUserInfo . ($func->numDir($qCharID)) . "/" . $qCharID . ".dat";
    
    if(!file_exists($charInfo))
    {
      copy("criarchars/info.dat",$dirUserInfo . ($func->numDir($qCharID)) . "/" . $qCharID. ".dat");
      
      $fRead=false;
      $fOpen = fopen($charInfo, "r");
      while (!feof($fOpen)) {
        @$fRead = "$fRead" . fread($fOpen, filesize($charInfo) );
      }
      fclose($fOpen);
      
      // Change character class ----------------------------------------------------------------
      $sourceStr = substr($fRead, 0, 16) . $writeAccName . substr($fRead, 26);
      $fOpen = fopen($charInfo, "wb");
      fwrite($fOpen, $sourceStr, strlen($sourceStr));
      fclose($fOpen);
    } 
    
    if( filesize($charInfo)==240)
    {
      $newName=trim($func->char_filter(trim($nomedochar)),"\x00");
      
      //Limpando Caracteres de acentos
      function strace($a)
      {
        $a = preg_replace("[àáâäã]","a",$a);
        $a = preg_replace("[èéêë]","e",$a);
        $a = preg_replace("[ìíîï]","i",$a);
        $a = preg_replace("[òóôöõ]","o",$a);
        $a = preg_replace("[ùúûü]","u",$a);
        $a = preg_replace("[ÀÁÂÄÃ]","A",$a);
        $a = preg_replace("[ÈÉÊË]","E",$a);
        $a = preg_replace("[ÌÍÎÏ]","I",$a);
        $a = preg_replace("[ÒÓÔÖÕ]","O",$a);
        $a = preg_replace("[ÙÚÛÜ]","U",$a);
        $a = preg_replace("/ç/","c",$a);
        $a = preg_replace("/Ç/","C",$a);
        $a = preg_replace("/ñ/","n",$a);
        $a = preg_replace("/Ñ/","N",$a);
        $a = str_replace("´","",$a);
        $a = str_replace("`","",$a);
        $a = str_replace("¨","",$a);
        $a = str_replace("^","",$a);
        $a = str_replace("~","",$a);
        return $a;
      }
      $newName = strace("$newName");
      
      if(!$func->is_valid_string($newName))
      {
        
        $charDat = $dirUserData . ($func->numDir($newName)) . "/" . $newName . ".dat";
        
        if(!file_exists($charDat))
        {
          copy("criarchars/char.dat",$dirUserData . ($func->numDir($newName)) . "/" . $newName. ".dat");
          
          $fRead=false;
          $fOpen = fopen($charInfo, "r");
          $fRead =fread($fOpen,filesize($charInfo));
          @fclose($fOpen);
          
          // list char information
          $charNameArr=array(
            "48" => trim(substr($fRead,0x30,15),"\x00"),
            "80" => trim(substr($fRead,0x50,15),"\x00"),
            "112"=> trim(substr($fRead,0x70,15),"\x00"),
            "144"=> trim(substr($fRead,0x90,15),"\x00"),
            "176"=> trim(substr($fRead,0xb0,15),"\x00"),
          );
          
          $chkEmpArr=array();
          $chkChar=array();
          foreach($charNameArr as $key=>$value)
          {
            if(empty($value)) $chkEmpArr[]=$key;
            else $chkChar[]=$key;
          }
          
          if(count($chkChar)<5)
          {
            
            // point to each information line
            $startPoint=$chkEmpArr[0];
            $endPoint=$startPoint+15;
            
            // Write info-----------------------------------------------------------------------
            $fRead=false;
            $fOpen = fopen($charInfo, "r");
            while (!feof($fOpen)) {
              @$fRead = "$fRead" . fread($fOpen, filesize($charInfo) );
            }
            fclose($fOpen);
            
            // Fill in 00 to left character
            $addOnLeft=false;
            $leftLen=15-strlen($newName);
            for($i=0;$i<$leftLen;$i++)
            {
              $addOnLeft.=pack("h*",00);
            }
            $writeName=$newName.$addOnLeft;
            
            
            $sourceStr = substr($fRead, 0, $startPoint) . $writeName . substr($fRead, $endPoint);
            $fOpen = fopen($charInfo, "wb");
            fwrite($fOpen, $sourceStr, strlen($sourceStr));
            fclose($fOpen);
            
            // Write data-------------------------------------------------------------------------
            $fRead=false;
            $fOpen = fopen($charDat, "r");
            while (!feof($fOpen)) {
              @$fRead = "$fRead" . fread($fOpen, filesize($charDat) );
            }
            fclose($fOpen);
            
            $bin = $func->char2Num($classe);
            $bina= pack("h*",$bin);
            
            // Change character class ----------------------------------------------------------------
            $sourceStr = substr($fRead, 0, 16) . $writeName . substr($fRead, 31, 17) . ($func->charResetHair($classe, 1)) . substr($fRead, 69, 43) . ($func->charResetHair($classe, 2)) . substr($fRead, 136, 60) . $bina . substr($fRead, 197, 7) . ($func->charResetState($classe)) . substr($fRead, 224, 284) . ($func->charResetSkill()) . substr($fRead, 524, 0) . ($func->charResetMastery()) . substr($fRead, 556, 148) . $writeAccName . substr($fRead, 714);
            $fOpen = fopen($charDat, "wb");
            fwrite($fOpen, $sourceStr, strlen($sourceStr));
            fclose($fOpen);
            
            
            echo 1; //"<center><div class='alert alert-info' role='alert'><b>Aviso:</b> Personagem criado com sucesso, level 80 com algumas quests feitas.</div>";
          }
          else
          {
            return $app->json(array(
              'limite' => true
            ));
          }
        }
        else
        {
          return $app->json(array(
            'existe' => true
          ));
        }
        
      }
    }
    return true;
  })
  ->bind('criar-char');
  
  $app->post('/login', function(Request $request) use ($app){
    error_reporting(E_ALL);
    ini_set('display_errors', true);
    
    $data = json_decode($request->getContent(), true);
    
    $username = $data['username'];
    $password = $data['password'];
    $token = md5(uniqid());
    $params = $request->request->all();
    
    $sql = 'SELECT * FROM users WHERE username = :username AND password = :password';
    $post = $app['db']->fetchAssoc($sql, array('username' => $data['username'], 'password' => $data['password']));
    
    $sqlBan = "SELECT BlockChk FROM [accountdb].[dbo].[AllGameUser] WHERE userid = :username";
    $ban = $app['db']->fetchAssoc($sqlBan, array('username' => $username));
    
    if($ban['BlockChk'] == '1'){
      return false;
      exit;
    }
    else {
      $sql1 = "UPDATE users SET token = :token WHERE username = :username";
      $stmt = $app['db']->prepare($sql1);
      $stmt->bindValue("token", $token);
      $stmt->bindValue("username", $data['username']);
      $stmt->execute();
    }  
    
    $post['token'] = $token;
    
    return $app->json(array(
      'dados' => $post,
      'block' => $ban['BlockChk'],
    ),200);
  })
  ->bind('verifica-login');
  
  $app->post('/check-status', function(Request $request) use ($app){
    $data = json_decode($request->getContent(), true);    
    
    $sql = "SELECT UserId, ChName FROM [ClanDB].[dbo].[CT] WHERE UserId = :username";
    $query = $app['db']->fetchAssoc($sql, array('username' => $app['user']['username']));

    return $app->json($query);
  })
  ->bind('check-status');

  $app->post('/edit-status', function(Request $request) use ($app){
    $data = json_decode($request->getContent(), true);

    
  })
  ->bind('edit-status');
  $app->get('/get-clan-dashboard', function(Application $app){
    $UserId = $app['user']['username'];

    $sql2 = "SELECT ChName, ClanName, MIconCnt FROM [ClanDB].[dbo].[UL] WHERE UserID = :username";
    $query2 = $app['db']->fetchAll($sql2, array('username' => $UserId));

    if(!$query2){
      return $app->json(array(
        'semClan' => true
      ));
    }
    else{
      return $app->json($query2, 200);
    }
  });

  $app->post('/get-member', function(Request $request) use ($app){
    $data = json_decode($request->getContent(), true);

    $username = $data['username'];

    $sql = "SELECT * FROM [ClanDB].[dbo].[CL] WHERE UserID = :username";
    $query = $app['db']->fetchAssoc($sql, array('username' => $username));

    $sql2 = "SELECT ChName, JoinDate, ChType, MIDX FROM [ClanDB].[dbo].[UL] WHERE ClanName = :nameOfClan";
    $query2 = $app['db']->fetchAll($sql2, array('nameOfClan' => $query['ClanName']));

    echo "<pre>";
    print_r($query);
    exit;
    if(count($query2) > 0){
      return $app->json($query2, 200);
    }
    else{
      return false;
    }

    return 1;
  })
  ->bind('get-member');
  $app->post('/get-clan', function(Request $request) use ($app){
    $data = json_decode($request->getContent(), true);

    $username = $data['username'];

    $sql = "SELECT * FROM [ClanDB].[dbo].[CL] WHERE UserID = :username";
    $query = $app['db']->fetchAssoc($sql, array('username' => $data['username']));


    if(!$query){
      return $app->json(array(
        'semClan' => true
      ));
    }
    if($username == $query['ClanZang']) {
      return $app->json(array(
        'cName' => $query['ClanName'],
        'cFrase' => $query['Note'],
        'cTotalMembers' => $query['MemCnt'],
        'cTag' => $query['MIconCnt'],
        'cLeader' => $query['ClanZang'],
        'cCreationDate' => $query['RegiDate'],
        'sameLeader' => true
      ),200);
    } else {
      return $app->json(array(
        'cName' => $query['ClanName'],
        'cFrase' => $query['Note'],
        'cTotalMembers' => $query['MemCnt'],
        'cTag' => $query['MIconCnt'],
        'cLeader' => $query['ClanZang'],
        'cCreationDate' => $query['RegiDate'],
        'sameLeader' => false
      ),200);
    }
  });
  $app->post('/get-characters', function(Request $request) use ($app){
    $data = json_decode($request->getContent(), true);    
        
    $func = new Func;
    $UserId = $app['user']['username'];
    
    $aDadosChar = $app['charInfo']->getUserInfo($UserId);
    
    $userInfo = $aDadosChar['userInfo'];
    $charNameArr = $aDadosChar['charNames'];

    $aReturn = $app['charInfo']->getCharInfo($charNameArr, $UserId);
    
    return $app->json($aReturn);
  })
  ->bind('get-characters');
  
  $app->post('/forgot-password', function(Request $request) use ($app){
    error_reporting(E_ALL);
    $data = json_decode($request->getContent(), true);
    
    $sql = "SELECT email, password, username, nome FROM users WHERE username = :username";
    $query = $app['db']->fetchAssoc($sql, array('username' => $app['user']['username']));
    $msgR = "localhost:8080/redefinir";
    
    $mail = new PHPMailer(true);                              // Passing `true` enables exceptions
    try {
      //Server settings
      $mail->isSMTP();                                      // Set mailer to use SMTP
      $mail->Host = 'smtp.gmail.com';  // Specify main and backup SMTP servers
      $mail->SMTPAuth = true;                               // Enable SMTP authentication
      $mail->Username = 'oarthurdev@gmail.com';                 // SMTP username
      $mail->Password = '78124770';                           // SMTP password
      $mail->SMTPSecure = 'tls';                            // Enable TLS encryption, `ssl` also accepted
      $mail->Port = 587;                                    // TCP port to connect to
      
      //Recipients
      $mail->setFrom('noreply@gothicpt.com.br', 'GothicPT');
      $mail->addAddress($query['email'], $query['nome']);     // Add a recipient
      
      //Content
      $mail->isHTML(true);                                  // Set email format to HTML
      $mail->Subject = '[GothicPT] Password Recovery';
      $mail->Body = "<i>Olá </i>".$query['nome'].", segue abaixo seus dados de login.<br />
      <b>Usuário</b>: ".$query['username']."<br />
      <b>Senha:</b> ".$query['password']."<br /><br />
      <b>Atenciosamente</b><br />
      <b><i>Gothic Priston Tale</i></b>";
      $mail->AltBody = 'This is the body in plain text for non-HTML mail clients';
      
      $mail->send();
      echo 'Message has been sent';
    } catch (Exception $e) {
      echo 'Message could not be sent. Mailer Error: ', $mail->ErrorInfo;
    }
    return true;
  })
  ->bind('forgot-password');
  
  $app->post('/home', function(Request $request) use ($app){
    $data = json_decode($request->getContent(), true);
    $username = $app['user']['username'];
    
    $sql = "SELECT Coin FROM [GameShop].[dbo].[Credits] WHERE ID = :username";
    $coins = $app['db']->fetchAssoc($sql, array('username' => $username));
    
    $sqlBan = "SELECT BlockChk FROM [accountdb].[dbo].[AllPersonalMember] WHERE UserID = :username";
    $ban = $app['db']->fetchAssoc($sqlBan, array('username' => $username));
    
    //return $app->json($coins, 200);
    return $app->json(array(
      'coins' => $coins['Coin'],
      'block' => $ban['BlockChk']
    ),200);
  })
  ->bind('home');
  
  $app->post('/delete-char', function(Request $request) use ($app){
    $data = json_decode($request->getContent(), true);
    
    $func = new Func;
    $UserId = $app['user']['username'];
    $charname = $data['charname'];
    
    $aReturn = $app['charInfo']->deleteChar($charname, $UserId);
    
    return $app->json($aReturn);
    
    return 1;
  })
  ->bind('delete-char');

  $app->post('/alterar-dados', function(Request $request) use ($app){
    $data = json_decode($request->getContent(), true);
    $username = $app['user']['username'];
    $senhaAtual = $data['senhaAtual'];
    $senhaNova1 = $data['novaSenha1'];
    $senhaNova2 = $data['novaSenha2'];

    $sql = "SELECT password FROM users WHERE username = :username";
    $passAtual = $app['db']->fetchAssoc($sql, array('username' => $username));

    if(($senhaNova1 == '') || ($senhaNova2 == '') || ($senhaAtual == '')){
      $sql = "SELECT password FROM users WHERE username = :username";
      $passAtual = $app['db']->fetchAssoc($sql, array('username' => $username));

      $alterarDados = "UPDATE users SET password = :password WHERE username = :username";
      $stmt = $app['db']->prepare($alterarDados);
      $stmt->bindValue("password", $passAtual['password']);
      $stmt->bindValue("username", $username);
      $stmt->execute();
    }
    else if($passAtual['password'] != $senhaAtual){
      exit;
      return false;
    }
    else if($senhaNova1 != $senhaNova2){
      return false;
      exit;
    }
    else{
      $sql = "SELECT password FROM users WHERE username = :username";
      $passAtual = $app['db']->fetchAssoc($sql, array('username' => $username));
  
      $alterarDados = "UPDATE users SET password = :password WHERE username = :username";
      $stmt = $app['db']->prepare($alterarDados);
      $stmt->bindValue("password", $senhaNova1);
      $stmt->bindValue("username", $username);
      $stmt->execute();
      
      $alterarDados2 = "UPDATE [accountdb].[dbo].[AllGameUser] SET Passwd = :password WHERE userid = :username";
      $stmt = $app['db']->prepare($alterarDados2);
      $stmt->bindValue("password", $senhaNova1);
      $stmt->bindValue("username", $username);
      $stmt->execute();
      
      $alterarDados3 = "UPDATE [accountdb].[dbo].[".strtoupper($data[username][0])."GameUser] SET Passwd = :password WHERE userid = :username";
            
      $stmt = $app['db']->prepare($alterarDados3);
      $stmt->bindValue("password", $senhaNova1);
      $stmt->bindValue("username", $username);
      $stmt->execute();
    }
    return $app->json(array(
      'senhaAtual' => $passAtual['password'],
      'senhaNova1' => $senhaNova1,
      'senhaNova2' => $senhaNova2
    ),200);
  })
  ->bind('alterar-dados');
  
  $app->post('/get-photo', function(Request $request) use ($app){
    $data = json_decode($request->getContent(), true);
    
    $sql = "SELECT photo FROM users WHERE username = :username";
    $photo = $app['db']->fetchAssoc($sql, array('username' => $app['user']['username']));
    
    return $app->json($photo, 200);
  })
  ->bind('get-photo');
  
  $app->post('/cadastrar-conta', function(Request $request) use ($app){
    $data = json_decode($request->getContent(), true);
    $pergunta = $data['perguntaS'];
    
    $datahoje = date("d/m/Y");
    if($pergunta == "7"){
      $perguntaT = "Onde seus pais se conheceram?";
    }
    else if($pergunta == "8"){
      $perguntaT = "Quais são os 3 primeiros dígitos do seu CPF?";
    }
    else if($pergunta == "9"){
      $perguntaT = "Qual o nome do seu animal de estimação?";
    }
    
    $sql = "SELECT username FROM users WHERE username = :username";
    $post = $app['db']->fetchAssoc($sql, array('username' => $data['username']));
    
    $sql2 = "SELECT email FROM users WHERE email = :email";
    $post2 = $app['db']->fetchAssoc($sql2, array('email' => $data['email']));

    if($post2['email'] == $data['email']){
      return $app->json(array(
        'existe2' => true
      ),200);
    }
    
    if($post['username'] == $data['username']){
      return $app->json(array(
        'existe' => true
      ),200);
    }
    $app['db']->insert("[accountdb].[dbo].[AllGameUser]", array(
      'userid' => $data['username'],
      'passwd' => $data['password'],
      'GameCode' => 0,
      'GPCode' => 0,
      'RegistDay' => $datahoje,
      'DisuseDay' => '12/12/2099',
      'UsePeriod' => 0,
      'inuse' => 0,
      'Grade' => 'U',
      'EventChk' => 0,
      'SelectChk' => 0,
      'BlockChk' => 0,
      'SpecialChk' => 0,
      'Credit' => 0,
      'DelChk' => 0
    ));
    $app['db']->insert("[accountdb].[dbo].[".strtoupper($data[username][0])."GameUser]", array(
      'userid' => $data['username'],
      'passwd' => $data['password'],
      'GameCode' => 0,
      'GPCode' => 0,
      'RegistDay' => $datahoje,
      'DisuseDay' => '12/12/2099',
      'UsePeriod' => 0,
      'inuse' => 0,
      'Grade' => 'U',
      'EventChk' => 0,
      'SelectChk' => 0,
      'BlockChk' => 0,
      'SpecialChk' => 0,
      'Credit' => 0,
      'DelChk' => 0
    ));
    $app['db']->insert('users', array(
      'username' => $data['username'],
      'password' => $data['password'],
      'email' => $data['email'],
      'name' => $data['nome'],
      'activated' => True,
      'birth_date' => $data['dataNasc'],
      'secret_question' => $perguntaT,
      'secret_answer' => $data['respostaS'],
      'photo' => 'default.jpg'
      )
    );
    return true;
  })
  ->bind('cadastrar-conta');
  
  
  $app->get('/get-alerts', function(Request $request) use ($app){
    $data = json_decode($request->getContent(), true);
    
    $username = $app['user']['username'];
    
    $sql = "SELECT idplayer, motivo, punicao, data, dataDesban FROM [AdminPanel].[dbo].[LogsBan] WHERE idplayer = :username ORDER BY data DESC";
    
    $stmt = $app['db']->prepare($sql);
    $stmt->bindValue("username", $username);
    $stmt->execute();
    $alerts = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    if(count($alerts) > 0){
      return $app->json($alerts, 200);
    }
    else{
      return false;
    }
    return 1;
  })
  ->bind('get-alerts');

  $app->get('/get-mensagens', function(Request $request) use ($app){
    $data = json_decode($request->getContent(), true);
    
    $username = $app['user']['username'];
    
    $sql = "SELECT mensagem, descricao, postado_por, data FROM [UserPanel].[dbo].[Mensagens] ORDER BY data DESC";
    
    $stmt = $app['db']->prepare($sql);
    $stmt->execute();
    $mensagens = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    if(count($mensagens) > 0){
      return $app->json($mensagens, 200);
    }
    else{
      return false;
    }
    return 1;
  })
  ->bind('get-mensagens');
  
  $app->post('/upload-image', function(Request $request) use ($app){
    $data = json_decode($request->getContent(), true);
    
    $username = $_POST['username'];

    $_FILES["file"]["name"] = md5(uniqid()) . '-' . $username . '.jpg';
    $uploadfileuser = PATHUPLOADUSERIMAGE . $_FILES["file"]["name"];
    
    $photo = $_FILES["file"]["name"];
    
    if(($_FILES['file']['type'] == 'image/png') || ($_FILES['file']['type'] == 'image/jpeg')){
    }
    else{
      return false;
      exit;
    }
    if ($_FILES['file']['error'] == UPLOAD_ERR_OK) {
      $tmp_name = $_FILES['file']["tmp_name"];;
      move_uploaded_file($tmp_name, $uploadfileuser);
      
      $updatePhoto = "UPDATE users SET photo = :photo WHERE username = :username";
      $stmt = $app['db']->prepare($updatePhoto);
      $stmt->bindValue("photo", $photo);
      $stmt->bindValue("username", $username);
      $stmt->execute();
    }
    else {
      echo 'Erro ao enviar imagem!';
      exit;
    }
    return true;
  })
  ->bind('upload-image');
  
  $app->post('/remove-token', function(Request $request) use ($app){
    $data = json_decode($request->getContent(), true);
    
    $updateToken0 = "UPDATE users SET token = 0 WHERE token = :token";
    $stmt = $app['db']->prepare($updateToken0);
    $stmt->bindValue("token", $data['token']);
    $stmt->execute();
    return true;
  })
  ->bind('remove-token');
  
  
  $app->register(new Silex\Provider\TwigServiceProvider(), array(
    'twig.path' => __DIR__.'/views',
  ));
  $app->run();