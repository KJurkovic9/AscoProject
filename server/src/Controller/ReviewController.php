<?php

namespace App\Controller;

use App\Entity\Review;
use App\Entity\Company;
use App\Form\ReviewType;
use OpenApi\Attributes as OA;
use App\Utils\HelperFunctions;
use App\Repository\UserRepository;
use App\Repository\CompanyRepository;
use App\Repository\ReviewRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Lexik\Bundle\JWTAuthenticationBundle\Encoder\JWTEncoderInterface;

#[Route('/api/review', name: "api_review_")]
class ReviewController extends AbstractController
{
    use HelperFunctions;
    public function __construct(
        private EntityManagerInterface $entityManager,
        private SerializerInterface $serializerInterface,
        private JWTEncoderInterface $jwtManager,
        private UserRepository $userRepository,
        private ReviewRepository $reviewRepository,
        private CompanyRepository $companyRepository
    ) {
    }

    /**
     * Route used to create user review
     *
     * @param Request $request
     * @return JsonResponse
     */
    #[Route('/{id}', name: 'create', methods: ["POST"])]
    #[OA\Post(
        path: "/api/review/{id}",
        description: "Used to create reviews",
        tags: ['review'],
        parameters: [
            new OA\Parameter(
                name: 'JWT_TOKEN - HTTP cookie',
                description: "This cookie is required for user to log him out if cookie is not present then user can't be logged out",
                example: "sjowdasfjASODJSSAdjasakfpa2t4252",
            ),
        ],
        requestBody: new OA\RequestBody(
            description: 'There is Review text and rating expected in this request',
            content: new OA\JsonContent(
                properties: [
                    new OA\Property(property: 'text', type: 'string', example: "Very nice company me like"),
                    new OA\Property(property: 'rating', type: 'string', example: "2.5"),
                ]
            )
        )
    )]
    #[OA\Response(
        response: 200,
        description: 'If everything went well the API will return a review in response',
        content: new OA\JsonContent(
            type: 'object',
            properties: [
                new OA\Property(property: 'message', type: 'string', example: "Review create success"),
                new OA\Property(property: 'review', type: 'object', example: [
                    "review" => [
                        "id" => 1,
                        "text" => "Dost dobr",
                        "rating" => 2.4,
                        "owner" => [
                            "id" => 3,
                            "email" => "macko@gmail.com",
                            "role" => "ROLE_ADMIN"
                        ],
                        "company" => [
                            "id" => 1,
                            "name" => "najjaca firmetina baja",
                            "email" => "najjacafirmetina@gmail.com",
                            "lat" => 52.2345,
                            "lng" => 64.2412,
                            "radius" => 2.5
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
        response: 404,
        description: 'This response will be dispatched if you are missing some token',
        content: new OA\JsonContent(
            type: 'object',
            properties: [
                new OA\Property(property: 'message', type: 'string', example: "Token is nonexistant please login"),
            ]
        )
    )]
    public function create(Request $request): JsonResponse
    {

        $reviewIncomingData = json_decode($request->getContent(), true);

        if (!$request->get("id") || !is_numeric($request->get("id"))) {
            return $this->json(["message" => "Review create failed", "errors" => ["There was no number present"]], 400);
        }

        $reviewData = new Review();

        $form = $this->createForm(ReviewType::class, $reviewData);

        $form->submit($reviewIncomingData);

        if (!$form->isValid()) {
            $this->extractFormErrors($form);
        }

        $foundCompany = $this->companyRepository->findOneBy(["id" => $request->get("id")]);
        if (!$foundCompany || !($foundCompany instanceof Company)) {
            return $this->json(["message" => "Review creation failed", "errors" => ["Company not found"]], 404);
        }

        $foundReviews = $this->reviewRepository->findBy(["company" => $foundCompany]);
        $foundReviewsSum = 0;
        foreach ($foundReviews as $review) {
            $foundReviewsSum += $review->getRating();
        }
        $foundCompany->setReviewAverage(($foundReviewsSum + $reviewData->getRating()) / (count($foundReviews) + 1));

        $userData = $this->getUserFromToken($request, $this->jwtManager, $this->userRepository);

        $reviewData->setOwner($userData);
        $reviewData->setCompany($foundCompany);

        $this->entityManager->persist($reviewData);
        $this->entityManager->flush();

        $review = $this->serializeData($this->serializerInterface, $reviewData, ["company", "user", "review", "review_company", "review_user"]);

        return $this->json(["message" => "Review creation success", "review" => $review], 201);
    }

    /**
     * Route used to check if user has already reviewed company for a given projectId (company id is from a company which has state CHOSEN)
     * @param Request $request
     * @return JsonResponse
     * */
    #[Route('/{id}', name: 'check', methods: ["GET"])]
    #[OA\Get(
        path: "/api/review/{id}",
        description: "Used to check reviews",
        tags: ['review'],
        parameters: [
            new OA\Parameter(
                name: 'JWT_TOKEN - HTTP cookie',
                description: "This cookie is required for user to log him out if cookie is not present then user can't be logged out",
                example: "sjowdasfjASODJSSAdjasakfpa2t4252",
            ),
        ],
    )]
    #[OA\Response(
        response: 200,
        description: 'If everything went well the API will return a review in response',
        content: new OA\JsonContent(
            type: 'object',
            properties: [
                new OA\Property(property: 'message', type: 'string', example: "Review check success"),
                new OA\Property(property: 'review', type: 'object', example: "true")
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
        response: 404,
        description: 'This response will be dispatched if you are missing some token',
        content: new OA\JsonContent(
            type: 'object',
            properties: [
                new OA\Property(property: 'message', type: 'string', example: "Token is nonexistant please login"),
            ]
        )
    )]
    public function check(Request $request): JsonResponse
    {
        $userData = $this->getUserFromToken($request, $this->jwtManager, $this->userRepository);
        $foundCompany = $this->companyRepository->findOneBy(["id" => $request->get("id")]);
        if (!$foundCompany || !($foundCompany instanceof Company)) {
            return $this->json(["message" => "Review check failed", "errors" => ["Company not found"]], 404);
        }
        $foundReview = $this->reviewRepository->findOneBy(["owner" => $userData, "company" => $foundCompany]);


        if ($foundReview && $foundReview instanceof Review) {
            // serialize review
            $foundReview = $this->serializeData($this->serializerInterface, $foundReview, [
                "review",
            ]);
            return $this->json(["message" => "Review check success", "review" => $foundReview], 200);
        }

        return $this->json(["message" => "Review check success", "review" => null], 200);
    }

    #[Route('/edit', name: 'edit', methods: ["PATCH"])]
    public function edit(Request $request): JsonResponse
    {
        $reviewIncomingData = json_decode($request->getContent(), true);

        if (!isset($reviewIncomingData["id"]) || !is_numeric($reviewIncomingData["id"])) {
            return $this->json(["message" => "Review edit failed", "errors" => ["There was no number present"]], 400);
        }

        $reviewData = $this->reviewRepository->findOneBy(["id" => $reviewIncomingData["id"]]);

        if (!$reviewData || !($reviewData instanceof Review)) {
            return $this->json(["message" => "Review edit failed", "errors" => ["Review not found"]], 404);
        }

        $form = $this->createForm(ReviewType::class, $reviewData);

        $form->submit($reviewIncomingData);

        if (!$form->isValid()) {
            $this->extractFormErrors($form);
        }

        $foundCompany = $reviewData->getCompany();

        $this->entityManager->persist($reviewData);
        $this->entityManager->flush();
        $this->companyRepository->recalculateRating($foundCompany);

        $review = $this->serializeData($this->serializerInterface, $reviewData, ["company", "user", "review", "review_company", "review_user"]);

        return $this->json(["message" => "Review edit success", "review" => $review], 200);
    }
}
