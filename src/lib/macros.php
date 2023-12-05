<?php



/**
 * { function_description }
 *
 * @param      string               $path    The path
 * @param      int                  $status  The status
 *
 * @throws     ErrorExeception      (description)
 *
 * @return     \Slim\Psr7\Response  ( description_of_the_return_value )
 */
use \Slim\Psr7\Response;

function redirect(string $path, int $status=302) : Response {
    if ($status < 300 || 399 < $status) {
        throw new Error("redirect(\$path, \$status = $status) \$status must be a 3XX status code;");
    }

    $res = new Response($status);
    return $res->withHeader('Location', $path);
}


use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;


function getRoute(Request $req) {
    $ctx = Slim\Routing\RouteContext::fromRequest($req);
    return $ctx->getRoute();
}


function jsonResponse($data, int $status = 200, $res = null) {
    $data['success'] = $status < HTTP_MOVED_PERMANENTLY;

    $body = json_encode($data);

    $res = $res ?? new \Slim\Psr7\Response($status);

    $res->getBody()->write($body);

    return $res
        ->withHeader('Content-Type', 'application/json')
        ->withStatus($status);
}


function errorResponse(string|array $message, int $status, array $context = null) {

    if ($status < HTTP_BAD_REQUEST) {
        throw new \Exception('HTTP Status value should be ' . HTTP_BAD_REQUEST . ' or greater for an error state');
    }

    $body = [];

    $body['message'] = $message;

    if ($context) {
        $body['context'] = $context;
    }

    return jsonResponse($body, $status);
}





define('OTP_ALPHANUMERIC',      00000001);
define('OTP_ALPHA',             00000010);
define('OTP_NUMERIC',           00000100);
define('OTP_MIXED_CASE',        00001000);
// define('OTP_ALLOW_PUNCTUATION', 00001000);

function generateOTP(int $length, int $flags = 0, int $charRange = null) {

    $length     = clamp($length, 2, 256);
    $chars      = "0123456789ABCDEFGHIKLMNOPQRSTUVWXYZ_-";
    $charRange  = $charRange ?? strlen($chars);
    $flags      = $flags ?? OTP_ALPHANUMERIC;
    $start      = $flags & OTP_ALPHA    ? 10 : 0;
    $end        = $flags & OTP_NUMERIC  ? 10 : $charRange;

    $charRange  = clamp($charRange, 2, $end);

    $otp = '';

    for ($i=0; $i < $length; $i++) {
        $ci = rand($start, $end - 1);
        $char = $chars[$ci];

        if ($flags & OTP_MIXED_CASE) {
            $char = !!rand(0,1) ? strtolower($char) : $char;
        }

        $otp .= $char;
    }

    return $otp;
}
