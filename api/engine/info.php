<?php
function defaultProjectInfoPage()
{
    include_once __DIR__."/classes/AlefCore.php";
    if (defined("DIESEL_SERVER") && DIESEL_SERVER == 'client' && $_REQUEST['pass']!="alefalef") {
        die("В доступе отказано");
    }

    $html = <<<'EOTEOTEOTEOT'
<!DOCTYPE html>
<html lang="ru">
<head>
<meta charset="utf-8">
<link rel="shortcut icon" href="http://l.alef.im/img/fav.png" type="image/png">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1">
<!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
<title>Тестовый проект ()</title>

<!-- Bootstrap -->
<link href="resources/info/css/bootstrap.min.css" rel="stylesheet">
<link href="resources/info/css/styles.css" rel="stylesheet">

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
		   <div class="col-lg-2 col-md-3 col-sm-3 col-xs-7"><img src="resources/info/Logo.png" class="img-responsive logo" alt=""></div>
		   <div class="title">
			   <h1>Документация API<br>
				   <span>Тестовый проект ()</span>
			   </h1>
		   </div>
	   </div>
  </header>
  <div class="main">
   <div class="row row-second">
	   <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
			<h2>ОБЩАЯ ИНФОРМАЦИЯ</h2>
			<p>Это API сгенерировано автоматически на основании описания на <b>AlefApiScript</b>. Редактор доступен по адресу: <a
				   href="https://aq.alef.im">https://aq.alef.im</a></p>
		   <p>Клиентские библиотеки могут быть созданы в этом же генераторе, с использованием <b>AlefApiScript</b> данного протокола, который вы можете скопировать, нажав <a href="#" class="copyAAS">сюда</a>.</p>
		   <p>Вы можете воспользоваться функцией "красивое отображение ответа сервера", если добавить к имени метода восклицательный знак. Например, если метод API доступен по ссылке
			   <a href="#">?alef_action=test</a>, то вызвав <a href="#">?alef_action=test!</a> вы получите ответ в форматированном виде, с читаемыми русскими символами и с включенным отображением ошибок и предупреждений PHP.</p>
		<p>Так же для отладки сервера вы можете использовать:<br/> 
		<a href="engine/apitester.php%ALEF_PASS%">API Tester</a> — скрипт, который поозволяет удобно формировать запросы к серверу (включая загрузку файлов).<br/>
		<a href="engine/tamper.php%ALEF_PASS%">Tamper</a> — инструмент, позволяющий подставлять индивидуальные ответы на запросы.</p>

	   </div>
   </div>
   <div class="row row-second">
	   <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
			<h2>ОПИСАНИЕ ПРОЕКТА</h2>
			
	   </div>
   </div>
   <div class="row row-second">
	   <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
			<h2>ИСТОРИЯ ВЕРСИЙ</h2>
			<ul><li><b>1.0</b> — Первоначальная версия</li></ul>
	   </div>
   </div>

<pre id='aas_pre' style='display:none;'>{"name":"Тестовый проект","api_fcm_server_key":"","api_android_application_id":"","api_android_inapp_client_id":"","api_android_inapp_client_secret":"","api_android_inapp_client_refresh_token":"","api_ios_inapp_password":"","url":"dev ","description":"","settings":"","version":"","security":0,"git":{"old_server":"","new_server":"https://bitbucket.org/alefdevelopment/aq-arzhanov.git # /api","objc":"","swift":"","java":"","kotlin":""},"version_history":[{"version":"1.0","description":"Первоначальная версия"}],"models":[],"requests":[{"name":"Температура в Москве","action_id":"getTemperature","description":"Получаем температуру в Москве.","example_request":"","ttl":"0","example_response":{"status":0,"temperature":"16"},"auth":"1","method":"get","regular_or_login_or_logout":"0","params":[]},{"name":"Разлогинивание","action_id":"logout","description":"Разлогинивает пользователя","example_request":"","ttl":"0","example_response":[{"status":0},{"status":1,"message":"Invalid token"}],"auth":"1","method":"get","regular_or_login_or_logout":"2","params":[]},{"name":"Авторизация пользователя","action_id":"login","description":"Авторизует пользователя в системе","example_request":"","ttl":"0","example_response":[{"status":0,"id":1,"is_active":1},{"status":1,"message":"User not found"}],"auth":"0","method":"post","regular_or_login_or_logout":"1","params":[{"name":"login","type":"string","description":"Логин","example":"vasya"},{"name":"password","type":"string","description":"Пароль","example":"admin#12345"}]},{"name":"Регистрация нового пользователя","action_id":"register","description":"Регистрирует нового пользователя в системе.","example_request":"","ttl":"0","example_response":[{"id":1,"status":0},{"status":1,"message":"User exist"}],"auth":"0","method":"post","regular_or_login_or_logout":"1","params":[{"name":"login","type":"string","description":"Логин","example":"vasya"},{"name":"password","type":"string","description":"Пароль","example":"admin#12345"},{"name":"firstname","type":"string","description":"Имя","example":"Вася"},{"name":"lastname","type":"string","description":"Фамилия","example":"Пупкин"}]}]}</pre>


</div>
	  <div class="row bottom-row">
		  <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
			  <h2>ЗАПРОСЫ</h2>
			  <ul class="items">
				
						  <li><span>Температура в Москве</span><span>getTemperature()</span></li>
	                      <div class="in_li">
	                          <p><b>Требует авторизации</b>: Да</p>
	                          <p><b>Метод</b>: get</p>
	                          <p><b>Пример запроса</b>: <a href=""></a></p>
	                          <p><b>Описание</b>: Получаем температуру в Москве.</p>
							  <p class="request"><b>Параметры</b>: Нет</p>
	                          
	                          <p class="request"><b>Пример(-ы) ответа</b>:</p>
	                          <div class="code-div">
	                          	<pre>{
    &quot;status&quot;: 0,
    &quot;temperature&quot;: &quot;16&quot;
}</pre>
							  </div>

	                      </div>
						  <li><span>Разлогинивание</span><span>logout()</span></li>
	                      <div class="in_li">
	                          <p><b>Требует авторизации</b>: Да</p>
	                          <p><b>Метод</b>: get</p>
	                          <p><b>Пример запроса</b>: <a href=""></a></p>
	                          <p><b>Описание</b>: Разлогинивает пользователя</p>
							  <p class="request"><b>Параметры</b>: Нет</p>
	                          
	                          <p class="request"><b>Пример(-ы) ответа</b>:</p>
	                          <div class="code-div">
	                          	<pre>[
    {
        &quot;status&quot;: 0
    },
    {
        &quot;status&quot;: 1,
        &quot;message&quot;: &quot;Invalid token&quot;
    }
]</pre>
							  </div>

	                      </div>
						  <li><span>Авторизация пользователя</span><span>login($login, $password)</span></li>
	                      <div class="in_li">
	                          <p><b>Требует авторизации</b>: Нет</p>
	                          <p><b>Метод</b>: post</p>
	                          <p><b>Пример запроса</b>: <a href=""></a></p>
	                          <p><b>Описание</b>: Авторизует пользователя в системе</p>
							  <p class="request"><b>Параметры</b>: </p>
	                          						  <div class='code-div'><pre>login (string); // Логин. Пример: vasya
password (string); // Пароль. Пример: admin#12345</pre></div> 
	                          <p class="request"><b>Пример(-ы) ответа</b>:</p>
	                          <div class="code-div">
	                          	<pre>[
    {
        &quot;status&quot;: 0,
        &quot;id&quot;: 1,
        &quot;is_active&quot;: 1
    },
    {
        &quot;status&quot;: 1,
        &quot;message&quot;: &quot;User not found&quot;
    }
]</pre>
							  </div>

	                      </div>
						  <li><span>Регистрация нового пользователя</span><span>register($login, $password, $firstname, $lastname)</span></li>
	                      <div class="in_li">
	                          <p><b>Требует авторизации</b>: Нет</p>
	                          <p><b>Метод</b>: post</p>
	                          <p><b>Пример запроса</b>: <a href=""></a></p>
	                          <p><b>Описание</b>: Регистрирует нового пользователя в системе.</p>
							  <p class="request"><b>Параметры</b>: </p>
	                          						  <div class='code-div'><pre>login (string); // Логин. Пример: vasya
password (string); // Пароль. Пример: admin#12345
firstname (string); // Имя. Пример: Вася
lastname (string); // Фамилия. Пример: Пупкин</pre></div> 
	                          <p class="request"><b>Пример(-ы) ответа</b>:</p>
	                          <div class="code-div">
	                          	<pre>[
    {
        &quot;id&quot;: 1,
        &quot;status&quot;: 0
    },
    {
        &quot;status&quot;: 1,
        &quot;message&quot;: &quot;User exist&quot;
    }
]</pre>
							  </div>

	                      </div>
			 </ul>
		  </div>
	  </div>
  </div>
</div>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
<script src="resources/info/js/bootstrap.min.js"></script>
<script src="resources/info/js/main.js"></script>

</body>
</html>
EOTEOTEOTEOT;

    if (isset($_REQUEST['pass'])) {
        $html = str_replace("%ALEF_PASS%", "?pass=".$_REQUEST['pass'], $html);
    } else {
        $html = str_replace("%ALEF_PASS%", "", $html);
    }
    echo $html;
    exit;
};
