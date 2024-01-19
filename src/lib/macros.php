<?php


use App\Helpers\Token;
use App\Models\TokenModel;
use App\Enums\TokenType;


use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Slim\Psr7\Response;
use Slim\Routing\RouteContext;



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

function redirect(string $path, int $status=302) : Response {
    if ($status < 300 || 399 < $status) {
        throw new Error("redirect(\$path, \$status = $status) \$status must be a 3XX status code;");
    }

    $res = new Response($status);
    return $res->withHeader('Location', $path);
}


/**
 * Composes a reasonably fully qualified URL based on $_SERVER properties
 * @source https://www.geeksforgeeks.org/get-the-full-url-in-php/
 * @return string The URL of the current page
 */
function getRoute(Request $req) {
    $ctx = RouteContext::fromRequest($req);
    return $ctx->getRoute();
}



// /**
//  * Composes a reasonably fully qualified URL based on $_SERVER properties
//  * @source https://www.geeksforgeeks.org/get-the-full-url-in-php/
//  * @return string The URL of the current page
//  */
// function getRoute($includePath = true) {
//     $url = 'http';
//     if (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on') {
//         $url .= 's';
//     }

//     $url .= "://";
//     $url .= $_SERVER['HTTP_HOST'];

//     if ($includePath) {
//         $url .= $_SERVER['REQUEST_URI'];
//     }

//     return $url;
// }





function jsonResponse($data, int $status = 200, $res = null) {
    $data['success'] = $status < HTTP_MOVED_PERMANENTLY;

    $body = json_encode($data);

    $res = $res ?? new Response($status);

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



function findTokensByUser(string $user_uuid, string $type) {
    return R::find('token', 'user_uuid = :user_uuid AND type = :type AND deleted_at IS NULL',
        [
            'user_uuid' => $user_uuid,
            'type'  => $type,
            // 'now'   => time(),
        ]);
}


function findToken(string $token, string $type) {
    return R::findOne('token', 'token = :token AND type = :type AND expires_at > :now AND deleted_at IS NULL',
        [
            'token' => $token,
            'type'  => $type,
            'now'   => time(),
        ]);
}


/**
 * Creates a token and persists to database
 *
 * @param      string  $type     The type
 * @param      int     $expires  The expires time to live in minutes
 */
function createToken(array $options) {

    $options = array_replace([
        'user_uuid'     => null,
        'type'          => null,
        'token'         => null,
        'expires'       => minutes(10),
        'max_attempts'  => 5,
    ], $options);

    $options = (object) array_allow_keys($options, [
        'expires',
        'max_attempts',
        'token',
        'type',
        'user_uuid',
    ]);

    $bean = R::dispense('token');

    $bean->type         = $options->type;
    $bean->token        = $options->token;
    $bean->userUuid     = $options->user_uuid;
    $bean->expiresAt    = now() + $options->expires;
    $bean->maxAttempts  = $options->max_attempts;

    R::store($bean);

    return $bean->token;
}


function createPasswordResetToken(string $user_uuid) {
    return createToken([
        'user_uuid'     => $user_uuid,
        'type'          => TokenType::PASSWORD_RESET,
        'token'         => Token::generateSHA256([
            'secret' => 'PasswordResetTokenSecretPhraseHere',
        ]),
        'expires'       => now() + minutes(10),
        'max_attempts'  => 3,
    ]);
}


function deleteToken(RedBeanPHP\OODBBean $bean) :bool {
    return R::trash($bean);
    // $bean = getToken($token, $type);
    // return R::trash($bean);
}


// function checkToken(string $token, string $type) :bool {
//     return !!getToken($token, $type);
// }


function attemptToken(string $token, string $type) :bool {
    $bean = getToken($token, $type);

    if ((int) $bean->attempts >= (int) $bean->maxAttempts) {
        $bean->deletedAt = now();
        R::store($bean);
        return false;
    }

    return true;
}



function clearOldPasswordResetTokens(string $user_uuid) {
    $tokens = findTokensByUser($user_uuid, TokenType::PASSWORD_RESET);

    if (empty(count($tokens))) { return; }

    R::begin();
    try{
        foreach ($tokens as $token) {
            // $token->expiresAt = now();
            R::trash($token);
            // $token->deletedAt = now();
            // R::store($token);
        }
        R::commit();
    } catch(\Exception $e) {
        R::rollback();
    }
}
