<?php

include_once(__DIR__ . "/classes/AlefCore.php");
if (defined("DIESEL_SERVER") && DIESEL_SERVER == 'client' && $_REQUEST['pass']!="alefalef") {
    die("В доступе отказано");
}

$api = json_decode('{"name":"Тестовый проект","api_fcm_server_key":"","api_android_application_id":"","api_android_inapp_client_id":"","api_android_inapp_client_secret":"","api_android_inapp_client_refresh_token":"","api_ios_inapp_password":"","url":"dev ","description":"","settings":"","version":"","security":0,"git":{"old_server":"","new_server":"https://bitbucket.org/alefdevelopment/aq-arzhanov.git # /api","objc":"","swift":"","java":"","kotlin":""},"version_history":[{"version":"1.0","description":"Первоначальная версия"}],"models":[],"requests":[{"name":"Температура в Москве","action_id":"getTemperature","description":"Получаем температуру в Москве.","example_request":"","ttl":"0","example_response":{"status":0,"temperature":"16"},"auth":"1","method":"get","regular_or_login_or_logout":"0","params":[]},{"name":"Разлогинивание","action_id":"logout","description":"Разлогинивает пользователя","example_request":"","ttl":"0","example_response":[{"status":0},{"status":1,"message":"Invalid token"}],"auth":"1","method":"get","regular_or_login_or_logout":"2","params":[]},{"name":"Авторизация пользователя","action_id":"login","description":"Авторизует пользователя в системе","example_request":"","ttl":"0","example_response":[{"status":0,"id":1,"is_active":1},{"status":1,"message":"User not found"}],"auth":"0","method":"post","regular_or_login_or_logout":"1","params":[{"name":"login","type":"string","description":"Логин","example":"vasya"},{"name":"password","type":"string","description":"Пароль","example":"admin#12345"}]},{"name":"Регистрация нового пользователя","action_id":"register","description":"Регистрирует нового пользователя в системе.","example_request":"","ttl":"0","example_response":[{"id":1,"status":0},{"status":1,"message":"User exist"}],"auth":"0","method":"post","regular_or_login_or_logout":"1","params":[{"name":"login","type":"string","description":"Логин","example":"vasya"},{"name":"password","type":"string","description":"Пароль","example":"admin#12345"},{"name":"firstname","type":"string","description":"Имя","example":"Вася"},{"name":"lastname","type":"string","description":"Фамилия","example":"Пупкин"}]}]}', true);
if (!$api) {
    die("JSON error");
}


$words=[];

$html=getHeader($api);

foreach ($api['requests'] as $request) {
    $html.=getRequest($request);

    $keywords = preg_split("/(?=[A-Z_])/", $request['action_id']);
    foreach ($keywords as $keyword) {
        $keyword = mb_strtolower($keyword);
        $keyword = (substr($keyword, -1)==='s')?substr($keyword, 0, -1):$keyword;
        $words[$keyword][] = $request['action_id'];
    }
}
ksort($words);

$tags='<li><span>Группы</span><span>Группировка запросов</span></li><div class="in_li">';
foreach ($words as $key=>$value) {
    if (count($value)<2) {
        continue;
    }
    $t="<p><b><i>".$key."</i></b></p><p class='request'>";
    foreach ($value as $w) {
        $t.='<b><a href="apitester.php#'.$w.'">'.$w.'</a></b>,&nbsp;';
    }
    $t.="</p></p><br>";
    $tags.=$t;
}
$tags.="</div>";
//var_dump($words);
$html.=$tags;

$html.=getFooter();

die($html);


function getRequest($request)
{
    $parameters="";
    foreach ($request['params'] as $parameter) {
        switch ($parameter["type"]) {
            case "json":
                $parameters.=getTypeText($parameter);
                break;
            case "string":
                $parameters.=getTypeText($parameter);
                break;
            case "number":
                $parameters.=getTypeText($parameter);
                break;
            case "float":
                $parameters.=getTypeText($parameter);
                break;
            case "file":
                $parameters.=getTypeFile($parameter);
                break;
            case "filearr":
                $parameters.=getTypeFileArr($parameter);
                break;
            case "image":
                $parameters.=getTypeImage($parameter);
                break;
            case "purchases":
                $parameters.=getTypeText($parameter);
                break;
            default:
                $parameters.=getTypeText($parameter);
                // var_dump($parameter);
                // die($parameter["type"]);

        }
    }

    $example_response=var_export($request['example_response'], true);
    $example_response=json_encode($request['example_response'], JSON_PRETTY_PRINT);

    $example_response = htmlspecialchars($example_response); // без этой строки ломается отображение документации, когда внутри json есть html

    $ts = time();
    $sec_hash = hash("sha256", "jolene-sulawesi" . $ts);


    $html = <<<HTML
<a name="{$request['action_id']}"></a><li><span>{$request['name']}</span><span>{$request['action_id']}</span></li>
<div class="in_li">
       <div class="row">
           <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
                <div class="code-div">
                    <pre>{$example_response}</pre>
                </div>
            </div>
           <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
            <p><b>Описание</b>: {$request['description']}</p>
            <p class="request"><b>Параметры</b>:</p>
                <form enctype="multipart/form-data" method="post" action="../index.php?alef_action={$request['action_id']}!">
                  {$parameters}
				  <input type="hidden" name="alef_security_timeout_hash" value="{$sec_hash}"/>
		  	      <input type="hidden" name="alef_security_timeout_timestamp" value="{$ts}"/>
				  <br>
                  <p class="request"><input type="submit" value="Отправить"></p>
              </form>
           </div>
       </div>
</div>
HTML;
    return $html;
}

function getHeader($api)
{
    $html = <<<HTML
<!DOCTYPE html>
<html lang="ru">
  <head>
    <meta charset="utf-8">
    <link rel="shortcut icon" href="http://l.alef.im/img/fav.png" type="image/png">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
	<base href="./">
    <title>Api Tester</title>

    <!-- Bootstrap -->
    <link href="../resources/info/css/bootstrap.min.css" rel="stylesheet">
    <link href="../resources/info/css/styles.css" rel="stylesheet">

    <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
    <!--[if lt IE 9]>
      <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
      <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
  </head>
  <body>
  <div class="container">
      <header>
           <div class="row row-first">
               <div class="col-lg-2 col-md-3 col-sm-3 col-xs-7"><img src="../resources/info/Logo.png" class="img-responsive logo" alt=""></div>
               <div class="title">
                   <h1>Запросы к API<br>
                       <span>{$api['name']}</span>
                   </h1>
               </div>
           </div>
      </header>
      <div class="main">
       <div class="row row-second">
           <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                <h2>ОБЩАЯ ИНФОРМАЦИЯ</h2>
               <p>Запросы для API сервера</p>
           </div>
       </div>
          <div class="row bottom-row">
              <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                  <h2>ЗАПРОСЫ</h2>
                  <ul class="items">
HTML;
    return $html;
}

function getFooter()
{
    $html = <<< HTML
                 </ul>
              </div>
          </div>
      </div>
  </div>

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
    <script src="../resources/info/js/bootstrap.min.js"></script>
    <script src="../resources/info/js/main.js"></script>

  </body>
</html>
HTML;
    return $html;
}


function getTypeText($parameter)
{
    return '<p class="request"><input type="text" name="'.$parameter["name"].'" placeholder="'.$parameter["name"].'" value="'.htmlspecialchars($parameter["example"]).'"> - <b>'.$parameter["name"].'</b>, '.$parameter["description"].'</p>';
}

function getTypeImage($parameter)
{
    return '<p class="request"><input type="file" name="'.$parameter["name"].'" multiple accept="image/*,image/jpeg"> - <b>'.$parameter["name"].'</b>, '.$parameter["description"].'</p>';
}

function getTypeFile($parameter)
{
    return '<p class="request"><input type="file" name="'.$parameter["name"].'" multiple accept="video/*,video/mp4"> - <b>'.$parameter["name"].'</b>, '.$parameter["description"].'</p>';
}

function getTypeFileArr($parameter)
{
    return '<p class="request"><input type="file" name="'.$parameter["name"].'[]" multiple> - <b>'.$parameter["name"].'</b>, '.$parameter["description"].'</p>';
}
