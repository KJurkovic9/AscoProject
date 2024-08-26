<?php

namespace App\Controller;

use App\Entity\Roles;
use App\Entity\Company;
use App\Form\CompanyType;
use App\Repository\CompanyRepository;
use OpenApi\Attributes as OA;
use App\Utils\HelperFunctions;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Lexik\Bundle\JWTAuthenticationBundle\Encoder\JWTEncoderInterface;
use Symfony\Component\Serializer\SerializerInterface;

#[Route('/api/company', name: "api_comapny_")]
class CompanyController extends AbstractController
{
    use HelperFunctions;

    public function __construct(
        private EntityManagerInterface $entityManager,
        private SerializerInterface $serializerInterface,
        private JWTEncoderInterface $jwtManager,
        private UserRepository $userRepository,
        private CompanyRepository $companyRepository
    ) {
    }

    /**
     * Route that enables admin to add new company
     *
     * @param Request $request
     * @return JsonResponse
     */
    #[Route('/', name: 'create', methods: ["POST"])]
    #[OA\Post(
        path: "/api/company/",
        description: "Used to create company (ONLY admin can create company)",
        tags: ['company'],
        parameters: [
            new OA\Parameter(
                name: 'JWT_TOKEN - HTTP cookie',
                description: "This cookie is required for user to log him out if cookie is not present then user can't be logged out",
                example: "sjowdasfjASODJSSAdjasakfpa2t4252",
            ),
        ],
        requestBody: new OA\RequestBody(
            description: 'There is company name, email, lng, lat and radius expected in this request',
            content: new OA\JsonContent(
                properties: [
                    new OA\Property(property: 'name', type: 'string', example: "Najjaca firmetina"),
                    new OA\Property(property: 'email', type: 'string', example: "najjacafirmetina@gmail.com"),
                    new OA\Property(property: 'lat', type: 'string', example: "52.2524"),
                    new OA\Property(property: 'lng', type: 'string', example: "95.3423"),
                    new OA\Property(property: 'radius', type: 'string', example: "5.5"),
                ]
            )
        )
    )]
    #[OA\Response(
        response: 200,
        description: 'If everything went well the API will return a company in response',
        content: new OA\JsonContent(
            type: 'object',
            properties: [
                new OA\Property(property: 'message', type: 'string', example: "Company create success"),
                new OA\Property(property: 'company', type: 'object', example: [
                    "company" => [
                        "id" => 1,
                        "company" => [
                            "id" => 2,
                            "name" => "najjaca firmetina baja",
                            "email" => "najjacafirmetina2@gmail.com",
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
        response: 403,
        description: 'This response will be dispatched if user does not posess certain rights ie. is not admin',
        content: new OA\JsonContent(
            type: 'object',
            properties: [
                new OA\Property(property: 'message', type: 'string', example: "Company creation failed"),
                new OA\Property(
                    property: 'errors',
                    type: 'object',
                    description: 'Errors returned by the route',
                    example: [
                        "You are not allowed to perform this action, only admins can add companies",
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
        $companyIncomingData = json_decode($request->getContent(), true);

        $companyData = new Company();

        $form = $this->createForm(CompanyType::class, $companyData);

        $form->submit($companyIncomingData);

        if (!$form->isValid()) {
            return $this->extractFormErrors($form);
        }

        $userData = $this->getUserFromToken($request, $this->jwtManager, $this->userRepository);

        if ($userData->getRole() !== Roles::admin->value) {
            return $this->json([
                "message" => "Company creation failed",
                "errors" =>
                ["You are not allowed to perform this action, only admins can add companies"]
            ], 403);
        }

        $foundCompany = $this->companyRepository->findOneBy(["email" => $companyData->getEmail()]);
        if ($foundCompany && $foundCompany instanceof Company) {
            return $this->json(["message" => "Company create failed", "errors" => ["Company with that email already exists"]], 400);
        }

        $companyData->setReviewAverage(0.0);
        $this->entityManager->persist($companyData);
        $this->entityManager->flush();

        $company = $this->serializeData($this->serializerInterface, $companyData, ["company"]);

        return $this->json(["message" => "Company was successfully created", "company" => $company], 201);
    }

    /**
     * Route that is used to fetch all comapnies
     *
     * @return JsonResponse
     */
    #[Route("/get-all", name: "get_all", methods: ["GET"])]
    #[OA\Get(
        path: "/api/company/get-all",
        description: "Used to fetch all companies",
        tags: ['company'],
    )]
    #[OA\Response(
        response: 200,
        description: 'If everything went well the API will return all companies in response',
        content: new OA\JsonContent(
            type: 'object',
            properties: [
                new OA\Property(property: 'message', type: 'string', example: "Company get all success"),
                new OA\Property(property: 'company', type: 'object', example: [
                    "companies" => [
                        [
                            "id" => 1,
                            "name" => "najjaca firmetina baja",
                            "email" => "najjacafirmetina@gmail.com",
                            "lat" => 52.2345,
                            "lng" => 64.2412,
                            "radius" => 2.5,
                            "review" => [
                                "id" => 1,
                                "text" => "Dost dobr",
                                "rating" => 2.4
                            ]
                        ],
                        [
                            "id" => 2,
                            "name" => "najjaca firmetina baja",
                            "email" => "najjacafirmetina2@gmail.com",
                            "lat" => 52.2345,
                            "lng" => 64.2412,
                            "radius" => 2.5,
                            "review" => []
                        ]
                    ]
                ])
            ]
        )
    )]
    public function getAllCompanies(): JsonResponse
    {
        $companyData = $this->companyRepository->findAll();

        if (count($companyData) < 1) {
            return $this->json(["message" => "Company get all success, no comapnies are present"], 200);
        }

        $company = $this->serializeData($this->serializerInterface, $companyData, ["company", "company_reviews", "review", "review_user"]);
        return $this->json(["message" => "Company get all success", "companies" => $company], 200);
    }

    /**
     * Route that is used to fetch single comapny
     *
     * @param Request
     * @return JsonResponse
     */
    #[Route("/{id}", name: "get", methods: ["GET"])]
    #[OA\Get(
        path: "/api/company/{id}",
        description: "Used to fetch all companies",
        tags: ['company'],
    )]
    #[OA\Response(
        response: 200,
        description: 'If everything went well the API will return a company in response',
        content: new OA\JsonContent(
            type: 'object',
            properties: [
                new OA\Property(property: 'message', type: 'string', example: "Company get success"),
                new OA\Property(property: 'company', type: 'object', example: [
                    "company" => [
                        "id" => 1,
                        "name" => "najjaca firmetina baja",
                        "email" => "najjacafirmetina@gmail.com",
                        "lat" => 52.2345,
                        "lng" => 64.2412,
                        "radius" => 2.5,
                        "review" => [
                            "id" => 1,
                            "text" => "Dost dobr",
                            "rating" => 2.4
                        ]
                    ]
                ])
            ]
        )
    )]
    #[OA\Response(
        response: 400,
        description: 'You have not provided id as a parameter',
        content: new OA\JsonContent(
            type: 'object',
            properties: [
                new OA\Property(property: 'message', type: 'string', example: "Comapny get failed"),
                new OA\Property(
                    property: 'errors',
                    type: 'object',
                    description: 'Errors returned by the route',
                    example: [
                        "There was no number present",
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
                new OA\Property(property: 'message', type: 'string', example: "Company get failed"),
                new OA\Property(
                    property: 'errors',
                    type: 'object',
                    description: 'Errors returned by the route',
                    example: [
                        "No company found",
                    ]
                )
            ]
        )
    )]
    public function getSingleCompany(Request $request): JsonResponse
    {
        if (!$request->get("id") || !is_numeric($request->get("id"))) {
            return $this->json(["message" => "Company get failed", "errors" => ["There was no number present"]], 400);
        }

        $companyData = $this->companyRepository->findOneBy(["id" => $request->get("id")]);
        if (!$companyData || !($companyData instanceof Company)) {
            return $this->json(["message" => "Company get failed", "errors" => ["No comapny found"]], 404);
        }

        $company = $this->serializeData($this->serializerInterface, $companyData, ["company", "company_reviews", "review"]);
        return $this->json(["message" => "Company get success", "companies" => $company], 200);
    }
}
