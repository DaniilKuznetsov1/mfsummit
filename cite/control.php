<?php
//Определяем класс, обрабатывающий POST и get запросы пользователя. 
//Класс содержит базовый метод анализа запроса и методы исполнения запросов
/* 
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=db_autority
DB_USERNAME=autodba
DB_PASSWORD=D3YT52jrNs95!
*/

session_start();

const DB_NAME = 'db_autority';
const USER_NAME = 'autodba';
const PASS = 'D3YT52jrNs95!';

class TAutority {

  protected $db = null;
  protected $errorCode = '';
  protected $postData = null;

  protected $userName = null;
  protected $checkUser = null;
  protected $password = null;
  protected $confirmPassword = null;

  public function __construct() 
  {
    //Здесь инициализируем соединение с БД   protected $db
    $connect_string = "mysql:host=localhost;dbname=".DB_NAME;
    $this->db = new PDO($connect_string, USER_NAME, PASS);
    $this->errorCode = $this->db->errorCode();

    $tempData = file_get_contents('php://input');
    $this->postData = json_decode($tempData, true);

    if (isset($this->postData['username'])) {
      $this->userName = $this->postData['username'];
    } else {
      $this->userName = null;
    }
    
    if (isset($this->postData['checkUser'])) {
      $this->checkUser = $this->postData['checkUser'];
    } else {
      $this->checkUser = null;
    }

    if (isset($this->postData['password'])) {
      $this->password = $this->postData['password'];
    } else {
      $this->password = null;
    }

    if (isset($this->postData['confirmPassword'])) {
      $this->confirmPassword = $this->postData['confirmPassword'];
    } else {
      $this->confirmPassword = null;
    }
  }

  protected function truncPassword(string $password)
  {
    $tempStr = '';
    if (mb_strlen($password) > 100) {//Обрезаем строку
      $tempStr = mb_substr($password,0,100);
    } else {
      $tempStr = $password;
    }
    return $tempStr;
  }

  protected function userNameFilter(string $userName) 
  {
    $tempStr = '';
    if (mb_strlen($userName) > 100) {//Обрезаем строку
      $tempStr = mb_substr($userName,0,100);
    } else {
      $tempStr = $userName;
    }
    $arrS = ['/','\\','.',',','?',':','<','>',"'",'"','`','~','&','[',']','{','}','№'];//Символы из этого массива
    $arrR = [' ',' ',' ',' ',' ',' ',' ',' '," ",' ',' ',' ',' ',' ',' ',' ',' ',' '];//Меняем на символы из этого

    return str_replace($arrS,$arrR,$tempStr);
  }

  public function booleanToString(bool $data)
  {
    if ($data) {
      return 'true';
    } else {
      return 'false';
    }
  }

  public function dispatch() 
  {
    //Инициализация переменных
    
    $ret1 = ['id'=>-1,'userName'=>'', 'autorization'=>false, 'error'=>''];
    $debug1 = '';

    $debug1 = 'Загрузка входных данных='.$this->errorCode;

    $userName = '';
    $checkUser = '';
    $password = '';
    $confirmPassword = '';

    //Загрузка входных данных
    if (isset($this->userName)) {
      $userName = htmlspecialchars($this->userName);
      $userName = $this->userNameFilter($userName);
    }
    if (isset($this->checkUser)) {
      $checkUser = htmlspecialchars($this->checkUser);
      $checkUser = $this->userNameFilter($checkUser);
    }
    if (isset($this->password)) {
      $password = htmlspecialchars($this->password);
      $password = $this->truncPassword($password);
    }
    if (isset($this->confirmPassword)) {
      $confirmPassword = htmlspecialchars($this->confirmPassword); //register or autorization
      $confirmPassword = $this->truncPassword($confirmPassword);
    }

    $debug1 = $debug1.'_Первоначальная обработка';

    //$debug1 = $debug1.'_Маршрутизатор='.$userName.'='.$checkUser.'='.$password.'='.$confirmPassword.'-';

    //Маршрутизатор, в зависимости от типа запроса
    
    $debug1 = $debug1.'_checkUser='.$this->booleanToString(isset($this->checkUser));
    $debug1 = $debug1.'_confirmPassword='.$this->booleanToString(isset($this->confirmPassword));
    $debug1 = $debug1.'_username0password='.$this->booleanToString(isset($this->userName) && (isset($this->password)));
    $debug1 = $debug1.'_username='.$this->booleanToString(isset($this->userName));

    if (isset($this->checkUser)) { //Режим проверки имени пользователя
      $debug1 = $debug1.'_Проверка пользователя';
      $ret1 = $this->checkUser($checkUser);
    } else if (isset($this->confirmPassword)) {//Режим регистрации пользователя
      $debug1 = $debug1.'_Регистрация пользователя';
      $ret1 = $this->registration($userName, $password, $confirmPassword);
    } else if (isset($this->userName) && (isset($this->password))) {//Запрос авторизации
      $debug1 = $debug1.'_Авторизация';
      $ret1 = $this->autorization($userName, $password);
    } else if (isset($this->userName) && (isset($this->password) == false)) {//Режим выхода из сессии
      $debug1 = $debug1.'_Выход из сессии';
      $ret1 = $this->exitSession();
    };

    $ret1['debug'] = $debug1;
    return $ret1;

  }

  public function checkUser(string $userName) 
  {
    $ret = ['id'=>-1,'userName'=>'', 'autorization'=>false, 'error'=>''];//Возвращается в случае пустого набора данных
    $res1 = $this->db->query("SELECT id, user_name FROM db_autority.users WHERE user_name = '$userName';");
    if ($res1 === false) {//В случае оишбки в запросе
      $ret['error'] = 'Error: Ошибка в запросе';
    } else {              //В случае возврата значения в виде строки из БД
      foreach ($res1 as $row)
      {
        $ret['id'] = $row['id'];
        $ret['userName'] = $row['user_name'];
      }
    }

    return $ret;
  }

  public function autorization(string $userName, string $password) 
  {
    $ret = ['id'=>-1,'userName'=>'', 'autorization'=>false, 'error'=>''];
    $hpass = password_hash($password, PASSWORD_DEFAULT);//Хешируем пароль встроенной функцией
    //Ищем совпадающую запись в БД
    $res1 = $this->db->query("SELECT `id`, `user_name`, `password` FROM db_autority.users WHERE user_name = '$userName'");
    if ($res1 === false) {//В случае оишбки в запросе
      $ret['autorization'] = false;
      $ret['error'] = 'Error: Ошибка в запросе';
    } else {  //Если вернуло что либо, проверяем на соответствие (может вернуть пустую строку)
      $row = $res1->fetch(PDO::FETCH_ASSOC);
      $ret['error'] = $userName.':'.$hpass;
      if ($row) {
        //password_verify
        if ($row['user_name'] == $userName && password_verify($password,$row['password'])) {
          $_SESSION["auth"] = true;
          $_SESSION["userName"] = $userName;
          $ret['autorization'] = true;
        } else {
          $_SESSION["auth"] = false;
          $ret['autorization'] = false;
        }
      } else {
        $_SESSION["auth"] = false;
        $ret['autorization'] = false;
      }
    }
    return $ret;
  }

  public function registration(string $userName, string $password, string $confirmPassword) 
  {
    $ret = ['id'=>-1,'userName'=>'', 'autorization'=>false, 'error'=>''];
    if ($password == $confirmPassword) {
      $hpass = password_hash($password, PASSWORD_DEFAULT);//Хешируем пароль встроенной функцией
      //Ищем совпадающую запись в БД
      $res1 = $this->db->query("SELECT `id`, `user_name`, `password` FROM db_autority.users WHERE user_name = '$userName' and `password` = '$hpass'");
      if ($res1 === false) {//В случае оишбки в запросе
        $ret['error'] = 'Error: Ошибка в запросе выборки';
      } else {              //В случае возврата значения в виде строки из БД проверяем на соответствие (может вернуть пустую строку)
        $row = $res1->fetch(PDO::FETCH_ASSOC);
        if ($row && $row['user_name'] == $userName) {
          $ret['error'] = 'Error: Логин уже используется, задайте другое имя';
        } else {
          $res2 = $this->db->exec("INSERT INTO db_autority.users (`user_name`, `password`, `created_at`) VALUES ('$userName', '$hpass', NOW())");
          if ($res2 === false) {
            $ret['error'] = 'Error: Ошибка в запросе';
          } else if ($res2 == 0) {
            $ret['error'] = 'Error: Ошибка вставки - вставлено 0 строк';
          } else {
            $res1 = $this->db->query("SELECT `id`, `user_name` FROM db_autority.users WHERE user_name = '$userName'");
            if ($res1 === false) {//В случае оишбки в запросе
              $ret['error'] = 'Error: Ошибка в запросе getId';
            } else {
              $row = $res1->fetch(PDO::FETCH_ASSOC);
              if ($row) {
                $ret['id'] = $row['id'];
              } else {
                $ret['id'] = -1;
              }
              $_SESSION["auth"] = true;
              $_SESSION["userName"] = $userName;
              $ret['autorization'] = true;
            }
          }
        }
      }
    } else {
      $ret['error'] = 'Error: Пароли не совпадают';
    }
    return $ret;
  }

  public function exitSession() 
  {
    //$_SESSION["auth"] = false;
    unset($_SESSION["auth"]);
    unset($_SESSION["userName"]);
  }

  function __destruct() 
  {
    //Завершаем работу с БД  protected $db
    $this->db = null;
  }

}


$autority = new TAutority();
$res = $autority->dispatch();
echo json_encode($res);