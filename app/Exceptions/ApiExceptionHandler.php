<?php

namespace App\Exceptions;

use App\Traits\ApiResponses;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Exceptions\ThrottleRequestsException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Throwable;

class ApiExceptionHandler
{
    use ApiResponses;

    public function handleException(Throwable $e, Request $request): JsonResponse
    {
        if ($e instanceof ValidationException) {
            return $this->handleValidationException($e);
        }

        if ($e instanceof ModelNotFoundException) {
            return $this->handleModelNotFoundException($e);
        }

        if ($e instanceof AuthenticationException) {
            return $this->handleAuthenticationException($e);
        }

        if ($e instanceof NotFoundHttpException) {
            return $this->handleNotFoundHttpException($e);
        }

        if ($e instanceof AccessDeniedHttpException) {
            return $this->handleAccessDeniedHttpException($e);
        }

        if ($e instanceof ThrottleRequestsException) {
            return $this->handleThrottleRequestsException($e);
        }

        return $this->handleGenericException($e);
    }

    protected function handleValidationException(ValidationException $e): JsonResponse
    {
        $errors = [];
        foreach ($e->errors() as $key => $messages) {
            foreach ($messages as $message) {
                $errors[] = [
                    'status' => $e->status ?? Response::HTTP_UNPROCESSABLE_ENTITY,
                    'message' => $message,
                    'source' => $key,
                ];
            }
        }

        return $this->error($errors, Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    protected function handleModelNotFoundException(ModelNotFoundException $e): JsonResponse
    {
        return $this->error([
            [
                'status' => Response::HTTP_NOT_FOUND,
                'message' => empty($e->getMessage()) ? 'The resource cannot be found.' : $e->getMessage(),
                'source' => $e->getModel(),
            ],
        ], Response::HTTP_NOT_FOUND);
    }

    protected function handleAuthenticationException(AuthenticationException $e): JsonResponse
    {
        return $this->error([
            [
                'status' => Response::HTTP_UNAUTHORIZED,
                'message' => empty($e->getMessage()) ? 'Unauthenticated.' : $e->getMessage(),
                'source' => '',
            ],
        ], Response::HTTP_UNAUTHORIZED);
    }

    protected function handleNotFoundHttpException(NotFoundHttpException $e): JsonResponse
    {
        return $this->error([
            [
                'status' => Response::HTTP_NOT_FOUND,
                'message' => empty($e->getMessage()) ? 'The requested resource was not found.' : $e->getMessage(),
                'source' => '',
            ],
        ], Response::HTTP_NOT_FOUND);
    }

    protected function handleAccessDeniedHttpException(AccessDeniedHttpException $e): JsonResponse
    {
        return $this->error([
            [
                'status' => Response::HTTP_FORBIDDEN,
                'message' => empty($e->getMessage()) ? 'This action is unauthorized.' : $e->getMessage(),
                'source' => '',
            ],
        ], Response::HTTP_FORBIDDEN);
    }

    protected function handleThrottleRequestsException(ThrottleRequestsException $e): JsonResponse
    {
        return $this->error([
            [
                'status' => Response::HTTP_TOO_MANY_REQUESTS,
                'message' => empty($e->getMessage()) ? 'Too Many Attempts.' : $e->getMessage(),
                'source' => '',
            ],
        ], Response::HTTP_TOO_MANY_REQUESTS);
    }

    protected function handleGenericException(Throwable $e): JsonResponse
    {
        return $this->error([
            [
                'type' => class_basename($e),
                'status' => Response::HTTP_INTERNAL_SERVER_ERROR,
                'message' => $e->getMessage(),
                'source' => 'Line: '.$e->getLine().' in '.$e->getFile(),
            ],
        ], Response::HTTP_INTERNAL_SERVER_ERROR);
    }
}
