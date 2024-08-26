<?php

namespace App\Controller;

use App\Entity\Guide;
use App\Entity\Roles;
use App\Form\GuideType;
use App\Repository\GuideRepository;
use OpenApi\Attributes as OA;
use App\Utils\HelperFunctions;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Lexik\Bundle\JWTAuthenticationBundle\Encoder\JWTEncoderInterface;

#[Route('/api/guide', name: "api_guide_")]
class GuideController extends AbstractController
{
    use HelperFunctions;

    public function __construct(
        private JWTEncoderInterface $jwtEncoder,
        private UserRepository $userRepository,
        private EntityManagerInterface $entityManager,
        private SerializerInterface $serializer,
        private SluggerInterface $sluggerInterface,
        private GuideRepository $guideRepository,
    ) {
    }

    /**
     * Route used to create user guides
     *
     * @param Request $request
     * @return JsonResponse
     */
    #[Route('/', name: 'create', methods: ["POST"])]
    #[OA\Post(
        path: "/api/guide/",
        description: "Used to create guide (ONLY admin can create guides)",
        tags: ['guide'],
        parameters: [
            new OA\Parameter(
                name: 'JWT_TOKEN - HTTP cookie',
                description: "This cookie is required for user to log him out if cookie is not present then user can't be logged out",
                example: "sjowdasfjASODJSSAdjasakfpa2t4252",
            ),
        ],
        requestBody: new OA\RequestBody(
            description: 'There is guide title',
            content: new OA\JsonContent(
                properties: [
                    new OA\Property(property: 'title', type: 'string', example: "Najjaci guide baja"),
                ]
            )
        )
    )]
    #[OA\Response(
        response: 200,
        description: 'If everything went well the API will return a guide in response',
        content: new OA\JsonContent(
            type: 'object',
            properties: [
                new OA\Property(property: 'message', type: 'string', example: "Guide creation success"),
                new OA\Property(property: 'guide', type: 'object', example: [
                    "guide" => [
                        "id" => 1,
                        "title" => "Kul title",
                    ]
                ])
            ]
        )
    )]
    #[OA\Response(
        response: 400,
        description: 'If there has been error whilist trying to verify a cookie',
        content: new OA\JsonContent(
            type: 'object',
            properties: [
                new OA\Property(property: 'message', type: 'string', example: "Your token is invalid"),
                new OA\Property(
                    property: 'errors',
                    type: 'object',
                    description: 'Errors returned by the route',
                    example: [
                        "Token is invalid, try again",
                    ]
                )
            ]
        )
    )]
    #[OA\Response(
        response: 403,
        description: 'This response will be dispatched if user does not posess certain rights ie. is not admin',
        content: new OA\JsonContent(
            type: 'object',
            properties: [
                new OA\Property(property: 'message', type: 'string', example: "Guide creation failed"),
                new OA\Property(
                    property: 'errors',
                    type: 'object',
                    description: 'Errors returned by the route',
                    example: [
                        "You are not allowed to perform this action, only admins can add sections",
                    ]
                )
            ]
        )
    )]
    #[OA\Response(
        response: 404,
        description: 'This response will be dispatched if you are missing some token',
        content: new OA\JsonContent(
            type: 'object',
            properties: [
                new OA\Property(property: 'message', type: 'string', example: "Token is nonexistant please login"),
            ]
        )
    )]
    public function createGuide(Request $request): JsonResponse
    {

        $guideIncomingData = json_decode($request->getContent(), true);

        $guideData = new Guide();

        $form = $this->createForm(GuideType::class, $guideData);

        $form->submit($guideIncomingData);

        if (!$form->isValid()) {
            return $this->extractFormErrors($form);
        }

        $userData = $this->getUserFromToken($request, $this->jwtEncoder, $this->userRepository);

        if ($userData->getRole() !== Roles::admin->value) {
            return $this->json([
                "message" => "Guide creation failed",
                "errors" =>
                    ["You are not allowed to perform this action, only admins can add guides"]
            ], 403);
        }

        $this->entityManager->persist($guideData);
        $this->entityManager->flush();

        $guide = $this->serializeData($this->serializer, $guideData, ["guide"]);

        return $this->json(["message" => "Guide creation succes", "guide" => $guide], 201);
    }

    /**
     * Route used to get all user guides
     *
     * @return JsonResponse
     */
    #[Route('/', name: 'get_all', methods: ["GET"])]
    #[OA\Get(
        path: "/api/guide/",
        description: "Used to fetch all guides",
        tags: ['guide'],
    )]
    #[OA\Response(
        response: 200,
        description: 'If everything went well the API will return a guides in response',
        content: new OA\JsonContent(
            type: 'object',
            properties: [
                new OA\Property(property: 'message', type: 'string', example: "Guide get all success"),
                new OA\Property(property: 'guide', type: 'object', example: [
                    "guides" => [
                        [
                            "id" => 1,
                            "title" => "Kul title 1",
                            "sections" => [
                                "id" => 1,
                                "title" => "Kul title",
                                "text" => "Epic text",
                                "image" => "epicSlika.jpg",
                            ],
                        ],
                        [
                            "id" => 2,
                            "title" => "Kul title 2",
                            "sections" => [],
                        ]
                    ]
                ])
            ]
        )
    )]
    #[OA\Response(
        response: 404,
        description: 'This response will be dispatched guide has not been found',
        content: new OA\JsonContent(
            type: 'object',
            properties: [
                new OA\Property(property: 'message', type: 'string', example: "Guide get all error"),
                new OA\Property(
                    property: 'errors',
                    type: 'object',
                    description: 'Errors returned by the route',
                    example: [
                        "No guides found",
                    ]
                )
            ]
        )
    )]
    public function getAllGuides(): JsonResponse
    {
        $guidesData = $this->guideRepository->findAll();

        if (!$guidesData || !($guidesData[0] instanceof Guide)) {
            return $this->json(["message" => "Guide get all error", "errors" => ["No guides found"]], 404);
        }

        $guides = $this->serializeData($this->serializer, $guidesData, ["guide", "guide_section", "section"]);

        return $this->json(["message" => "Guide get all success", "guides" => $guides], 200);
    }

    /**
     * Route used to find specific guide
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    #[Route('/{id}', name: 'get', methods: ["GET"])]
    #[OA\Get(
        path: "/api/guide/{id}",
        description: "Used to fetch single guide",
        tags: ['guide'],
    )]
    #[OA\Response(
        response: 200,
        description: 'If everything went well the API will return a guide in response',
        content: new OA\JsonContent(
            type: 'object',
            properties: [
                new OA\Property(property: 'message', type: 'string', example: "Guide find success"),
                new OA\Property(property: 'guide', type: 'object', example: [
                    "guides" => [
                        [
                            "id" => 1,
                            "title" => "Kul title 1",
                            "sections" => [
                                "id" => 1,
                                "title" => "Kul title",
                                "text" => "Epic text",
                                "image" => "epicSlika.jpg",
                            ],
                        ],
                        [
                            "id" => 2,
                            "title" => "Kul title 2",
                            "sections" => [],
                        ]
                    ]
                ])
            ]
        )
    )]
    #[OA\Response(
        response: 400,
        description: 'If there has been error whilist trying to verify a cookie',
        content: new OA\JsonContent(
            type: 'object',
            properties: [
                new OA\Property(property: 'message', type: 'string', example: "Guide find failed"),
                new OA\Property(
                    property: 'errors',
                    type: 'object',
                    description: 'Errors returned by the route',
                    example: [
                        "There was no number present in params",
                    ]
                )
            ]
        )
    )]
    #[OA\Response(
        response: 404,
        description: 'This response will be dispatched guide has not been found',
        content: new OA\JsonContent(
            type: 'object',
            properties: [
                new OA\Property(property: 'message', type: 'string', example: "Guide find failed"),
                new OA\Property(
                    property: 'errors',
                    type: 'object',
                    description: 'Errors returned by the route',
                    example: [
                        "No such guide has been found",
                    ]
                )
            ]
        )
    )]
    public function findGuide(Request $request): JsonResponse
    {
        if (!$request->get("id") || !is_numeric($request->get("id"))) {
            return $this->json(["message" => "Guide find failed", "errors" => ["There was no number present in params"]], 400);
        }

        $foundGuide = $this->guideRepository->findOneBy(["id" => $request->get("id")]);
        if (!$foundGuide || !($foundGuide instanceof Guide)) {
            return $this->json([
                "message" => "Guide find failed",
                "errors" =>
                    ["No such guide has been found"]
            ], 404);
        }

        $guide = $this->serializeData($this->serializer, $foundGuide, ["guide", "guide_section", "section"]);

        return $this->json(["message" => "Guide find success", "guides" => $guide], 200);
    }
}
