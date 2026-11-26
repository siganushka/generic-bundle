# 问题响应

`ProblemJsonResponse` 用于返回标准化的 API 错误响应，遵循 [RFC 9457](https://datatracker.ietf.org/doc/html/rfc9457)，`Content-Type` 默认为 `application/problem+json`。

```php
use Siganushka\GenericBundle\Response\ProblemJsonResponse;
use Symfony\Component\HttpFoundation\Response;

return new ProblemJsonResponse('商品不存在', Response::HTTP_NOT_FOUND);
```

```json
{
    "type": "about:blank",
    "title": "Not Found",
    "status": 404,
    "detail": "商品不存在"
}
```

### 参数

| 参数 | 说明 |
| --- | --- |
| `detail` | 必填，面向调用方的问题说明 |
| `status` | 必填，`4xx` 或 `5xx` 状态码，其他状态码会抛出 `InvalidArgumentException` |
| `title` | 可选，问题简述，默认取状态码对应的标准文案 |
| `type` | 可选，问题类型的 `URI` 引用，默认 `about:blank` |
| `headers` | 可选，额外响应头 |

### 在异常监听器中使用

```php
use Siganushka\GenericBundle\Response\ProblemJsonResponse;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;

public function onKernelException(ExceptionEvent $event): void
{
    $throwable = $event->getThrowable();
    if ($throwable instanceof HttpExceptionInterface) {
        $event->setResponse(new ProblemJsonResponse($throwable->getMessage(), $throwable->getStatusCode()));
    }
}
```

如只需统一的数组结构而不直接构造响应，可使用 `ProblemJsonResponse::createAsArray()`。
