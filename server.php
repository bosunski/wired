<?php
require "vendor/autoload.php";
//require_once "helpers.php";
Pre\Plugin\process(__DIR__ . "/helpers.pre");

use Aerys\Request;
use Aerys\Response;
use Aerys\Router;
use Aerys\Host;
use Aerys\Root;

$root = new Root(__DIR__ . "/public");
$host = new Host;
$host->expose("*", "8888");

$router = new Router;

use Amp\File;
$router->route("GET", "/register", function (Request $request, Response $response) {
//	$template = yield File\get(__DIR__ . "/resources/views/register.html");
	$view = yield view("register");
	$response->end($view);
});

$router->route("POST", "/register", function (Request $request, Response $response) {
	$fields = yield fields($request);

//	for ($i = 0; $i <= 20000, $i++;) {
		yield prepare(
			"INSERT INTO users (email, password) VALUES (:email, :password)",
			["email" => $fields["email"], "password" => $fields["password"]]
		);
//	}

	$result = yield prepare("SELECT * FROM users");

	while (yield $result->advance()) {
		print_r($result->getCurrent());
	}

	$response->end(json_encode($fields));
});

use Aerys\Websocket;
use Aerys\Websocket\Endpoint;
use Aerys\Websocket\Message;
$socket = Aerys\websocket(new class implements Websocket {
	public function onStart(Endpoint $endpoint ) {
		// TODO: Implement onStart() method.
	}

	public function onHandshake( Request $request, Response $response ) {
		// TODO: Implement onHandshake() method.
	}

	public function onOpen(int $clientId, $handshakeData) {
		// TODO: Implement onOpen() method.
	}


	public function onData( int $clientId, Message $msg ) {
		$text = yield $msg;
	}


	public function onClose( int $clientId, int $code, string $reason ) {
		// TODO: Implement onClose() method.
	}


	public function onStop() {
		// TODO: Implement onStop() method.
	}
});

$router->route("GET", "/socket", $socket);

use App\Responder\HomePageResponder;
$router->route("GET", "/", new HomePageResponder);

$host->use($router);
$host->use($root);

return $host;