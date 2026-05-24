<?php
declare(strict_types=1);

namespace App\Controller\Api;

use App\DTO\Request\IndexationRequest;
use App\Exception\AllUrlsInvalidException;
use App\Exception\TitleFetchException;
use App\Exception\XmlStockClientException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\Exception\ExceptionInterface;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use App\Service\Indexation\IndexationService;
use OpenApi\Attributes as OA;
use Throwable;

class UrlIndexationCheckController extends AbstractController
{
    public function __construct(
        private readonly SerializerInterface $serializer,
        private readonly ValidatorInterface $validator,
        private readonly IndexationService $service
    ) {
    }

    /**
     * @throws ExceptionInterface
     */
    #[Route('/api/indexation/check', methods: ['POST'])]
    #[OA\RequestBody(
        description: 'Описания тела запроса',
        content: new OA\JsonContent(
            type: 'object',
            properties: [
                new OA\Property(
                    property: 'urls', // Имя ключа, которое ожидает конструктор вашего App\DTO\Request\IndexationRequest
                    type: 'array',    // Тип этого свойства — массив []
                    items: new OA\Items(type: 'string'), // Элементы массива — это обычные строки
                )
            ],
            example: [
                'urls' => [
                    'https://docker.com',
                    'https://haieronline.ru',
                    'test',
                    'https://docker.com/',
                    'HTTP://HAIERONLINE.RU',
                    'music.yandex.ru'
                ]
            ]
        )
    )]
    #[OA\Response(
        response: 200,
        description: 'Список url с флагом indexation - что означает что url индексирован если indexation = true',
        content: new OA\JsonContent(
            type: 'array',
            items: new OA\Items(
                properties: [
                    new OA\Property(property: 'url', type: 'string', example: 'https://docker.com'),
                    new OA\Property(property: 'indexed', type: 'bool', example: false),
                ]
            ),
            example: [
                ['url' => 'https://docker.com', 'indexed' => false],
                ['url' => 'https://haieronline.ru', 'indexed' => true],
            ]
        )
    )]
    public function index(Request $request): JsonResponse
    {
        /** @var IndexationRequest $dto */
        $dto = $this->serializer->deserialize(
            $request->getContent(),
            IndexationRequest::class,
            'json'
        );

        $violations = $this->validator
            ->validate($dto);

        if (count($violations) > 0) {
            return $this->json(
                [
                    'error' => 'Список URL должен содержать от 1 до 100 элементов'
                ],
                400
            );
        }

        try {
            $results = $this->service
                ->check($dto->urls);

            return $this->json($results);
        } catch (
            AllUrlsInvalidException
            | XmlStockClientException
            | TitleFetchException
            $exception
        ) {
            return $this->json(
                [
                    'error' => $exception->getMessage()
                ],
                400
            );
        } catch (
            Throwable $exception
        ) {
            return $this->json(
                [
                    'error' => 'Внутренняя ошибка сервера',
                    'message' => $exception->getMessage()
                ],
                500
            );
        }
    }

}
