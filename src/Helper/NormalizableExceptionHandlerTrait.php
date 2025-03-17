<?php 
namespace App\Helper;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\Exception\NotNormalizableValueException;

trait NormalizableExceptionHandlerTrait{
    function handleNormalizableValueException(NotNormalizableValueException $e): JsonResponse
    {
        $path = $e->getPath();
        $currentType = $e->getCurrentType();
        $expectedTypes = $e->getExpectedTypes()[0];

        return $this->json(['message' =>"$path type must be $expectedTypes"], 400);
    }
}
?>