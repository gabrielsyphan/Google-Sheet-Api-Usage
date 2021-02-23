# Router @Stonks

[![Maintainer](http://img.shields.io/badge/maintainer-@giovannialoliveira-blue.svg?style=flat-square)](https://www.facebook.com/giovannialoliveira)
[![Source Code](http://img.shields.io/badge/source-stonks/router-blue.svg?style=flat-square)](https://github.com/giovannialo/router)
[![PHP from Packagist](https://img.shields.io/packagist/php-v/stonks/router.svg?style=flat-square)](https://packagist.org/packages/stonks/router)
[![Latest Version](https://img.shields.io/github/release/giovannialo/router.svg?style=flat-square)](https://github.com/giovannialo/router/releases)
[![Software License](https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square)](LICENSE)
[![Build](https://img.shields.io/scrutinizer/build/g/giovannialo/router.svg?style=flat-square)](https://scrutinizer-ci.com/g/giovannialo/router)
[![Quality Score](https://img.shields.io/scrutinizer/g/giovannialo/router.svg?style=flat-square)](https://scrutinizer-ci.com/g/giovannialo/router)
[![Total Downloads](https://img.shields.io/packagist/dt/stonks/router.svg?style=flat-square)](https://packagist.org/packages/stonks/router)

O Router é um componente de rotas PHP com abstração para MVC. Preparado com verbos RESTful (GET, POST, PUT, PATCH e DELETE), ele trabalha em sua própria camada de forma isolada.

### Destaques

- Classe Router com todos os verbos RESTful
- Despacho otimizado com controle total de decisões
- Falsificador (Spoofing) de requisição para verbalização local
- É muito simples criar rotas para sua aplicação ou API
- Gatilho e transportador de dados para o controlador
- Pronto para o composer
- Compatível com PSR-2

## Instalação

Router está disponível via Composer:

```bash
"stonks/router": "1.0.*"
```

ou execute

```bash
composer require stonks/router
```

## Documentação

Para obter mais detalhes sobre como usar o Router, consulte a pasta de amostra com detalhes no diretório do componente.

Para usar o Router, é preciso redirecionar sua navegação para o arquivo raiz de rotas (index.php) onde todo o tráfego deve ser tratado. O exemplo abaixo mostra como:

#### Apache

```apacheconfig
Options All -Indexes

RewriteEngine On

# ROUTER - Força o redirecionamento para WWW.
RewriteCond %{HTTP_HOST} !^www\. [NC]
RewriteRule ^ https://www.%{HTTP_HOST}%{REQUEST_URI} [L,R=301]

# ROUTER - Força o redirecionamento para HTTPS.
RewriteCond %{HTTP:X-Forwarded-Proto} !https
RewriteCond %{HTTPS} off
RewriteRule ^ https://%{HTTP_HOST}%{REQUEST_URI} [L,R=301]

# ROUTER - URL Amigável (Reescrita)
RewriteCond %{SCRIPT_FILENAME} !-f
RewriteCond %{SCRIPT_FILENAME} !-d
RewriteRule ^(.*)$ index.php?route=/$1 [L,QSA]
```

#### Rotas

```php
<?php
require __DIR__ . "/../vendor/autoload.php";

use Stonks\Router\Router;

$router = new Router("https://www.seudominio.com");

/**
 * Rotas
 * O controlador deve estar no namespace Test\Controller
 * isto produz rotas para: rota, rota/$id, rota/{$id}/perfil, etc.
 */
$router->namespace("Test");

$router->get("/rota", "Controller:method");
$router->post("/rota/{id}", "Controller:method");
$router->put("/rota/{id}/perfil", "Controller:method");
$router->patch("/rota/{id}/perfil/{foto}", "Controller:method");
$router->delete("/rota/{id}", "Controller:method");

/**
 * Agrupar por rotas e namespace
 * O controlador deve estar no namespace Dash\Controller
 * isto produz rotas para: /admin/rota e /admin/rota/$id
 */
$router->group("admin")->namespace("Dash");
$router->get("/rota", "Controller:method");
$router->post("/rota/{id}", "Controller:method");

/**
 * Grupo de erro
 * Isso monitora todos os erros do Router. São eles: 
 * 400 Pedido ruim (Bad Request)
 * 404 Não encontrado (Not Found)
 * 405 Método não permitido (Method Not Allowed)
 * 501 Não implementado (Not Implemented)
 */
$router->group("error")->namespace("Test");
$router->get("/{errcode}", "Stonk:notFound");

/**
 * Este método executa as rotas
 */
$router->dispatch();

/*
 * Redireciona todos os erros
 */
if ($router->error()) {
    $router->redirect("/error/{$router->error()}");
}
```

#### Nomeado

```php
<?php
require __DIR__ . "/../vendor/autoload.php";

use Stonks\Router\Router;

$router = new Router("https://www.seudominio.com");

/**
 * Rotas
 * O controlador deve estar no namespace Test\Controller
 */
$router->namespace("Test")->group("name");

$router->get("/", "Name:home", "name.home");
$router->get("/hello", "Name:hello", "name.hello");
$router->get("/redirect", "Name:redirect", "name.redirect");

/**
 * Este método executa as rotas
 */
$router->dispatch();

/*
 * Redireciona todos os erros
 */
if ($router->error()) {
    $router->redirect("name.hello");
}
```

#### Exemplo de controlador nomeado

```php
class Name
{
    // ...

    public function __construct($router)
    {
        $this->router = $router;
    }

    public function home(): void
    {
        echo "<h1>Home</h1>";
        echo "<p>", $this->router->route("name.home"), "</p>";
        echo "<p>", $this->router->route("name.hello"), "</p>";
        echo "<p>", $this->router->route("name.redirect"), "</p>";
    }

    public function redirect(): void
    {
        $this->router->redirect("name.hello");
    }
}
```

#### Parâmetros nomeados

```php
// Rota
$router->get("/params/{category}/page/{page}", "Name:params", "name.params");

$this->router->route("name.params", [
    "category" => 22,
    "page" => 2
]);

// Resultado
https://www.{}/name/params/22/page/2

$this->router->route("name.params", [
    "category" => 22,
    "page" => 2,
    "argument1" => "most filter",
    "argument2" => "most search"
]);

// Resultado
https://www.{}/name/params/22/page/2?argument1=most+filter&argument2=most+search
```

#### currentRoute

Obtém a url da rota que foi executada. Exemplo: _https://www.{}/contato_.

```php
$router->get('/perfil', 'Controller:profile');
$router->get('/contato', 'Controller:contact'); // Exemplo: esta foi a rota executada

$router->currentRoute();
```

#### isCurrentRoute

Retorna _true_ se a rota que foi executada possui o _name_ especificado no parâmetro. False, caso contrário. _Para utilizar este método, é obrigatório nomear a rota que deseja validar._

```php
$router->get('/perfil', 'Controller:profile', 'controller.profile');
$router->get('/contato', 'Controller:contact', 'controller.contact'); // Exemplo: esta foi a rota executada

if($router->isCurrentRoute('controller.contact')){
  echo 'A rota contato foi executada';
}
```

#### Callable

```php
/**
 * GET httpMethod
 */
$router->get("/", function ($data) {
    $data = ["realHttp" => $_SERVER["REQUEST_METHOD"]] + $data;
    echo "<h1>GET :: Spoofing</h1>", "<pre>", print_r($data, true), "</pre>";
});

/**
 * POST httpMethod
 */
$router->post("/", function ($data) {
    $data = ["realHttp" => $_SERVER["REQUEST_METHOD"]] + $data;
    echo "<h1>POST :: Spoofing</h1>", "<pre>", print_r($data, true), "</pre>";
});

/**
 * PUT spoofing e httpMethod
 */
$router->put("/", function ($data) {
    $data = ["realHttp" => $_SERVER["REQUEST_METHOD"]] + $data;
    echo "<h1>PUT :: Spoofing</h1>", "<pre>", print_r($data, true), "</pre>";
});

/**
 * PATCH spoofing e httpMethod
 */
$router->patch("/", function ($data) {
    $data = ["realHttp" => $_SERVER["REQUEST_METHOD"]] + $data;
    echo "<h1>PATCH :: Spoofing</h1>", "<pre>", print_r($data, true), "</pre>";
});

/**
 * DELETE spoofing e httpMethod
 */
$router->delete("/", function ($data) {
    $data = ["realHttp" => $_SERVER["REQUEST_METHOD"]] + $data;
    echo "<h1>DELETE :: Spoofing</h1>", "<pre>", print_r($data, true), "</pre>";
});

$router->dispatch();
```

#### Falsificador de formulário (Spoofing)

Esse exemplo mostra como acessar as rotas (PUT, PATCH, DELETE) a partir da aplicação. Você pode ver mais detalhes na pasta de exemplo. Dê uma atenção para o campo __method_, ele pode ser do tipo hidden.

```html
<form action="" method="POST">
    <select name="_method">
        <option value="POST">POST</option>
        <option value="PUT">PUT</option>
        <option value="PATCH">PATCH</option>
        <option value="DELETE">DELETE</option>
    </select>

    <input type="text" name="first_name" value="Giovanni"/>
    <input type="text" name="last_name" value="Oliveira"/>
    <input type="text" name="email" value="giovanni.al.oliveira@gmail.com"/>

    <button>Stonks</button>
</form>
```

#### Exemplo de PHP cURL

```php
<?php

$curl = curl_init();

curl_setopt_array($curl, array(
  CURLOPT_URL => "http://localhost/stonks/router/exemple/spoofing/",
  CURLOPT_RETURNTRANSFER => true,
  CURLOPT_ENCODING => "",
  CURLOPT_MAXREDIRS => 10,
  CURLOPT_TIMEOUT => 30,
  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
  CURLOPT_CUSTOMREQUEST => "PUT",
  CURLOPT_POSTFIELDS => "first_name=Giovanni&last_name=Oliveira&email=giovanni.al.oliveira@gmail.com",
  CURLOPT_HTTPHEADER => array(
    "Cache-Control: no-cache",
    "Content-Type: application/x-www-form-urlencoded"
  ),
));

$response = curl_exec($curl);
$err = curl_error($curl);

curl_close($curl);

if ($err) {
  echo "Error cURL #:" . $err;
} else {
  echo $response;
}
```

## Contribuindo

Envie relatórios de bugs, sugestões e solicitações de pull para o rastreador de problemas do GitHub.

## Suporte

Se você descobrir algum problema relacionado à segurança, use o rastreador de problemas do GitHub.

Agradecido (õ.~)

## Licença

A Licença do MIT. Por favor, veja o [Arquivo de Licença](https://github.com/giovannialo/router/blob/master/LICENSE) para maiores informações.
