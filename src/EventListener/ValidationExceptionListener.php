<?php declare(strict_types=1);

namespace App\EventListener;


use App\Util\ValidationListParser;
use HttpException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\Validator\Exception\ValidationFailedException;

/**
 * Class ValidationExceptionListener
 * @package App\EventListener
 */
class ValidationExceptionListener
{
    /**
     * Provides custom json response for failed validation
     *
     * @param ExceptionEvent $event
     * @return void
     */
    public function onKernelException(ExceptionEvent $event): void
    {
        $exception = $event->getThrowable();
        if (!$exception instanceof HttpException && !$exception->getPrevious() instanceof ValidationFailedException) {
            return;
        }

        /** @var ValidationFailedException $previous */
        $previous = $exception->getPrevious();
        $errors = $previous->getViolations();
        $response = new JsonResponse(['errors' => ValidationListParser::toArray($errors)], 400);
        $event->setResponse($response);
    }
}
