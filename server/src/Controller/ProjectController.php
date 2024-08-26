<?php

namespace App\Controller;

use App\Entity\Offer;
use App\Entity\Project;
use App\Form\ProjectType;
use App\Entity\Calculation;
use App\Repository\CalculationRepository;
use App\Repository\OfferRepository;
use OpenApi\Attributes as OA;
use App\Utils\HelperFunctions;
use App\Repository\UserRepository;
use App\Repository\ProjectRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Lexik\Bundle\JWTAuthenticationBundle\Encoder\JWTEncoderInterface;

#[Route('/api/project', name: "api_project_")]
class ProjectController extends AbstractController
{
    use HelperFunctions;

    public function __construct(
        private JWTEncoderInterface $jwtEncoder,
        private UserRepository $userRepository,
        private ProjectRepository $projectRepository,
        private CalculationRepository $calculationRepository,
        private EntityManagerInterface $entityManager,
        private SerializerInterface $serializerInterface,
        private OfferRepository $offerRepository
    ) {
    }

    /**
     * Used to associate user and calculation
     *
     * @param Request $request
     * @return JsonResponse
     */
    #[Route(path: "/{id}", name: "create", methods: ["POST"])]
    #[OA\Post(
        path: "/api/project/{id}",
        description: "Used to create user project",
        tags: ['project'],
        parameters: [
            new OA\Parameter(
                name: 'JWT_TOKEN - HTTP cookie',
                description: "This cookie is required for user to log him out if cookie is not present then user can't be logged out",
                example: "sjowdasfjASODJSSAdjasakfpa2t4252",
            ),
            new OA\Parameter(
                name: "id - calculation's id",
                example: "2",
            ),
        ],
        requestBody: new OA\RequestBody(
            description: 'There is project name expected in this request',
            content: new OA\JsonContent(
                properties: [
                    new OA\Property(property: 'name', type: 'string', example: "Moj kul projekt"),
                ]
            )
        )
    )]
    #[OA\Response(
        response: 201,
        description: 'If everything went well the API will return an project in response',
        content: new OA\JsonContent(
            type: 'object',
            properties: [
                new OA\Property(property: 'message', type: 'string', example: "Project created successfuly"),
                new OA\Property(property: 'project', type: 'object', example: [
                    "project" => [
                        "name" => "Novi projket",
                        "user" => [
                            "id" => 1,
                            "email" => "mac@mac.com",
                            "role" => "ROLE_USER",
                            "userProfiles" => [
                                "id" => 2,
                                "firstName" => "Darko",
                                "lastName" => "Darac",
                                "postalCode" => 10000,
                                "city" => ["name" => "Zagreb"],
                            ]
                        ],
                        "calculation" => [
                            "id" => 1,
                            "roofSurface" => 50,
                            "roofPitch" => 45,
                            "roofOrientation" => "J",
                            "lat" => 45.771045,
                            "lng" => 15.995479,
                            "lifespan" => null,
                            "budget" => null,
                            "projectPrice" => 186040,
                            "profitabiltyYears" => 103052,
                            "profitabiltyMonthly" => [
                                "1" => 329.93595567862496,
                                "2" => 324.305816203875,
                                "3" => 309.56021281762503,
                                "4" => 286.0143463785,
                                "5" => 300.980952665625,
                                "6" => 298.61151172837504,
                                "7" => 288.086701449,
                                "8" => 296.481188683875,
                                "9" => 302.401168028625,
                                "10" => 323.62106951100003,
                                "11" => 344.898938967375,
                                "12" => 350.362420516875,
                            ],
                            "effectivness" => 0.999,
                            "yearlyConsumption" => 600,
                            "location" => "Travno,Zagreb",
                            "paybackPeroid" => 7.243750727787913,
                            "installationPrice" => 197500,
                            "equipmentPrice" => 548983,
                            "potentialPower" => 6862
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
                    description: 'Errors returned by the form',
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

        $projectName = json_decode($request->getContent(), true);

        if (!$request->get("id") || !is_numeric($request->get("id"))) {
            return $this->json(["message" => "Project create failed", "errors" => ["There was no number present"]], 400);
        }

        $projectData = new Project();

        $form = $this->createForm(ProjectType::class, $projectData);

        $form->submit($projectName);

        if (!$form->isValid()) {
            return $this->extractFormErrors($form);
        }

        $foundCalculation = $this->calculationRepository->findOneBy(["id" => $request->get("id")]);
        if ($foundCalculation && $foundCalculation instanceof Calculation) {

            $userData = $this->getUserFromToken($request, $this->jwtEncoder, $this->userRepository);

            $foundProject = $this->projectRepository->findOneBy(["user" => $userData, "calculation" => $foundCalculation]);
            if ($foundProject && $foundProject instanceof Project) {
                return $this->json(["message" => "Project create failed", "errors" => ["Project already exists"]], 400);
            }

            $userData->getUserProfile();

            $projectData->setCalculation($foundCalculation);
            $projectData->setUser($userData);

            $this->entityManager->persist($projectData);
            $this->entityManager->flush();

            $project = $this->serializeData($this->serializerInterface, $projectData, [
                "user",
                "user_user_profile",
                "user_profile",
                "user_profile_city",
                "city",
                "project",
                "calculation",
                "project_calculation",
                "project_user"
            ]);

            return $this->json(["message" => "Project create successfuly", "project" => $project], 201);
        } else {
            return $this->json(["message" => "Project create failed", "errors" => ["Calculation not found"]], 404);
        }
    }


    /**
     * Route that fetches user projects
     *
     * @param Request $request
     * @return JsonResponse
     */
    #[Route(path: "/get-user-projects", name: "get_user_projects", methods: ["GET"])]
    #[OA\Get(
        path: "/api/project/get-user-projects",
        description: "Used to fetch all user projects assoiciated with current user",
        tags: ['project'],
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
        description: 'If everything went well the API will return an project in response',
        content: new OA\JsonContent(
            type: 'object',
            properties: [
                new OA\Property(property: 'message', type: 'string', example: "Project fetch successfuly"),
                new OA\Property(property: 'project', type: 'object', example: [
                    "project" => [
                        "name" => "Novi projket",
                        "user" => [
                            "id" => 1,
                            "email" => "mac@mac.com",
                            "role" => "ROLE_USER",
                            "userProfiles" => [
                                "id" => 2,
                                "firstName" => "Darko",
                                "lastName" => "Darac",
                                "postalCode" => 10000,
                                "city" => ["name" => "Zagreb"],
                            ]
                        ],
                        "calculation" => [
                            "id" => 1,
                            "roofSurface" => 50,
                            "roofPitch" => 45,
                            "roofOrientation" => "J",
                            "lat" => 45.771045,
                            "lng" => 15.995479,
                            "lifespan" => null,
                            "budget" => null,
                            "projectPrice" => 186040,
                            "profitabiltyYears" => 103052,
                            "profitabiltyMonthly" => [
                                "1" => 329.93595567862496,
                                "2" => 324.305816203875,
                                "3" => 309.56021281762503,
                                "4" => 286.0143463785,
                                "5" => 300.980952665625,
                                "6" => 298.61151172837504,
                                "7" => 288.086701449,
                                "8" => 296.481188683875,
                                "9" => 302.401168028625,
                                "10" => 323.62106951100003,
                                "11" => 344.898938967375,
                                "12" => 350.362420516875,
                            ],
                            "effectivness" => 0.999,
                            "yearlyConsumption" => 600,
                            "location" => "Travno,Zagreb",
                            "paybackPeroid" => 7.243750727787913,
                            "installationPrice" => 197500,
                            "equipmentPrice" => 548983,
                            "potentialPower" => 6862
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
                    description: 'Errors returned by the form',
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
    public function getUserProjects(Request $request): JsonResponse
    {
        $userData = $this->getUserFromToken($request, $this->jwtEncoder, $this->userRepository);

        $projectData = $this->projectRepository->findBy(["user" => $userData]);
        if (!$projectData && !($projectData instanceof Project || (is_array($projectData) && !($projectData instanceof Project)))) {
            return $this->json(["message" => "Project fetch failed", "errors" => ["Project not found"]], 404);
        }

        $project = $this->serializeData($this->serializerInterface, $projectData, [
            "user",
            "user_user_profile",
            "user_profile",
            "user_profile_city",
            "city",
            "project",
            "calculation",
            "project_calculation",
            "project_user"
        ]);

        return $this->json(["message" => "Project fetch successfuly", "projects" => $project], 200);
    }

    /**
     * Route that fetches user projects
     *
     * @param Request $request
     * @return JsonResponse
     */
    #[Route(path: "/{id}", name: "get", methods: ["GET"])]
    #[OA\Get(
        path: "/api/project/{id}",
        description: "Used to fetch all user projects assoiciated with current user",
        tags: ['project'],
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
        description: 'If everything went well the API will return an project in response',
        content: new OA\JsonContent(
            type: 'object',
            properties: [
                new OA\Property(property: 'message', type: 'string', example: "Project fetch successfuly"),
                new OA\Property(property: 'project', type: 'object', example: [
                    "project" => [
                        "name" => "Novi projket",
                        "user" => [
                            "id" => 1,
                            "email" => "mac@mac.com",
                            "role" => "ROLE_USER",
                            "userProfiles" => [
                                "id" => 2,
                                "firstName" => "Darko",
                                "lastName" => "Darac",
                                "postalCode" => 10000,
                                "city" => ["name" => "Zagreb"],
                            ]
                        ],
                        "calculation" => [
                            "id" => 1,
                            "roofSurface" => 50,
                            "roofPitch" => 45,
                            "roofOrientation" => "J",
                            "lat" => 45.771045,
                            "lng" => 15.995479,
                            "lifespan" => null,
                            "budget" => null,
                            "projectPrice" => 186040,
                            "profitabiltyYears" => 103052,
                            "profitabiltyMonthly" => [
                                "1" => 329.93595567862496,
                                "2" => 324.305816203875,
                                "3" => 309.56021281762503,
                                "4" => 286.0143463785,
                                "5" => 300.980952665625,
                                "6" => 298.61151172837504,
                                "7" => 288.086701449,
                                "8" => 296.481188683875,
                                "9" => 302.401168028625,
                                "10" => 323.62106951100003,
                                "11" => 344.898938967375,
                                "12" => 350.362420516875,
                            ],
                            "effectivness" => 0.999,
                            "yearlyConsumption" => 600,
                            "location" => "Travno,Zagreb",
                            "paybackPeroid" => 7.243750727787913,
                            "installationPrice" => 197500,
                            "equipmentPrice" => 548983,
                            "potentialPower" => 6862
                        ]
                    ]
                ]),
                new OA\Property(property: "offer", type: "object", example: [
                    "offers" =>
                        [
                            "1" =>
                                [
                                    "id" => 1,
                                    "state" => "SENT",
                                    "price" => null,
                                    "description" => null,
                                    "offerDate" => null,
                                    "company" => [
                                        "id" => 1,
                                        "name" =>
                                            "najjaca firmetina baja",
                                        "email" => "imprayt@gmail.com",
                                        "lat" => 52.2345,
                                        "lng" => 64.2412,
                                        "radius" => 2.5
                                    ],
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
    public function getProject(Request $request): JsonResponse
    {

        if (!$request->get("id") || !is_numeric($request->get("id"))) {
            return $this->json(["message" => "Review create failed", "errors" => ["There was no number present"]], 400);
        }

        $userData = $this->getUserFromToken($request, $this->jwtEncoder, $this->userRepository);

        $projectData = $this->projectRepository->findOneBy(["id" => $request->get("id"), "user" => $userData]);
        if (!$projectData && !($projectData instanceof Project)) {
            return $this->json(["message" => "Project get single failed", "errors" => ["There was no such project found"]], 404);
        }

        $project = $this->serializeData($this->serializerInterface, $projectData, [
            "user",
            "user_user_profile",
            "user_profile",
            "user_profile_city",
            "city",
            "project",
            "calculation",
            "project_calculation",
            "project_user"
        ]);

        $offerData = $this->offerRepository->findBy(["project" => $projectData]);

        if ($offerData && ((is_array($offerData) && $offerData[0] instanceof Offer) || (!is_array($offerData) && $offerData instanceof Offer))) {
            $offers = $this->serializeData($this->serializerInterface, $offerData, ["offer", "company", "offer_company"]);
        } else {
            $offers = [];
        }

        return $this->json(["message" => "Project get single success", "project" => $project, "offers" => $offers], 200);
    }
}
