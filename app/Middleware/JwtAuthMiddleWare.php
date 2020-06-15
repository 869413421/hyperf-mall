<?php

declare(strict_types=1);

namespace App\Middleware;

use App\Constants\ResponseCode;
use App\Model\User;
use Carbon\Carbon;
use Hyperf\Utils\Context;
use Phper666\JwtAuth\Exception\TokenValidException;
use Phper666\JwtAuth\Jwt;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Hyperf\Di\Annotation\Inject;

class JwtAuthMiddleWare implements MiddlewareInterface
{
    /**
     * @var ContainerInterface
     */
    protected $container;

    /**
     * @Inject
     * @var Jwt
     */
    protected $jwt;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        try
        {
            $token = $this->jwt->getTokenObj();
            if (!$this->jwt->checkToken())
            {
                throw new TokenValidException('JWT验证失败', ResponseCode::UNAUTHORIZED);
            }

            $userId = $token->getClaim('id');
            $user = User::getFirstById($userId);

            if (!$user)
            {
                throw new TokenValidException('JWT验证失败', ResponseCode::UNAUTHORIZED);
            }

            if ($user->status == User::DISABLES)
            {
                throw new TokenValidException('JWT验证失败', ResponseCode::UNAUTHORIZED);
            }
            $user->last_login_at = Carbon::now();
            $user->save();

            //将user放置到request中

            //在协程上下文内是有存储最原始的 PSR-7 请求对象 和 响应对象 的，
            //且根据 PSR-7 对相关对象所要求的 不可变性(immutable)，
            //也就意味着我们在调用 $response = $response->with***()
            //所调用得到的 $response，并非为改写原对象，
            //而是一个 Clone 出来的新对象，
            //也就意味着我们储存在协程上下文内的 请求对象 和 响应对象 是不会改变的
            //那么当我们在中间件内的某些逻辑改变了 请求对象 或 响应对象
            //而且我们希望对后续的 非传递性的 代码再获取改变后的 请求对象 或 响应对象
            //那么我们便可以在改变对象后，将新的对象设置到上下文中，如代码所示：
            $request = $request->withAttribute('user', $user);
            Context::set(ServerRequestInterface::class, $request);
        }
        catch (\Exception $exception)
        {
            throw new TokenValidException('JWT验证失败', ResponseCode::UNAUTHORIZED);
        }

        return $handler->handle($request);
    }
}