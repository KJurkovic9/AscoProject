<?php

namespace App\Controller;

use App\Entity\Offer;
use App\Entity\Company;
use App\Entity\Project;
use App\Form\OfferType;
use OpenApi\Attributes as OA;
use App\Utils\HelperFunctions;
use App\Repository\UserRepository;
use Symfony\Component\Mime\Address;
use App\Repository\CompanyRepository;
use App\Repository\OfferRepository;
use App\Repository\ProjectRepository;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Lexik\Bundle\JWTAuthenticationBundle\Encoder\JWTEncoderInterface;
use Symfony\Component\Serializer\SerializerInterface;
use Psr\Log\LoggerInterface;

#[Route('/api/offer', name: "api_offer_")]
class OfferController extends AbstractController
{
    use HelperFunctions;

    public function __construct(
        private JWTEncoderInterface $jwtEncoder,
        private UserRepository $userRepository,
        private ProjectRepository $projectRepository,
        private CompanyRepository $companyRepository,
        private EntityManagerInterface $entityManager,
        private SerializerInterface $serializer,
        private OfferRepository $offerRepository
    ) {
    }

    /**
     * Used to create offer to companies
     *
     * @param Request $request
     * @return JsonResponse
     */
    #[Route("/create", name: "create", methods: ["POST"])]
    #[OA\Post(
        path: "/api/offer/create",
        description: "Used to create offers to a company",
        tags: ['offer'],
        parameters: [
            new OA\Parameter(
                name: 'JWT_TOKEN - HTTP cookie',
                description: "This cookie is required for user to log him out if cookie is not present then user can't be logged out",
                example: "sjowdasfjASODJSSAdjasakfpa2t4252",
            ),
        ],
        requestBody: new OA\RequestBody(
            description: 'There is companies id and projectId id',
            content: new OA\JsonContent(
                properties: [
                    new OA\Property(property: 'projectId', type: 'string', example: "1"),
                    new OA\Property(property: 'companies', type: 'object', example: [
                        "1",
                        "2"
                    ]),
                ]
            )
        )
    )]
    #[OA\Response(
        response: 201,
        description: 'If everything went well the API will return a offers in response',
        content: new OA\JsonContent(
            type: 'object',
            properties: [
                new OA\Property(property: 'message', type: 'string', example: "Offers create success"),
                new OA\Property(property: 'offer', type: 'object', example: [
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
                            "project" => [
                                "id" => 1,
                                "name" => "Naziv projekta",
                                "calculation" =>
                                [
                                    "id" => 1,
                                    "roofSurface" => 25,
                                    "roofPitch" => 45,
                                    "roofOrientation" => "J",
                                    "lat" => 45.771045,
                                    "lng" => 15.995479,
                                    "yearlyConsumption" => 1000,
                                    "projectPrice" => 394491,
                                    "profitabiltyYears" => 46448,
                                    "effectiveness" => 0.999,
                                    "location" => "Travno,Zagreb",
                                    "profitabiltyMonthly" => [
                                        "1" => 2340.9514889237503,
                                        "2" => 2847.6640416512505,
                                        "3" => 4174.76834641375,
                                        "4" => 6293.896325935001,
                                        "5" => 4946.9017600937495,
                                        "6" => 5160.15144444625,
                                        "7" => 6107.3843695900005,
                                        "8" => 5351.88051845125,
                                        "9" => 4819.08237742375,
                                        "10" => 2909.29124401,
                                        "11" => 994.28299293625,
                                        "12" => 502.5696534812504
                                    ],
                                    "paybackPeroid" => 8.4931751636238,
                                    "installationPrice" => 120000,
                                    "equipmentPrice" => 274491,
                                    "potentialPower" => 3431
                                ],
                                "user" => [
                                    "id" => 3,
                                    "email" => "macko@gmail.com",
                                    "role" => "ROLE_ADMIN",
                                    "userProfile" => [
                                        "id" => 4,
                                        "firstName" => "Kiki",
                                        "lastName" => "Miki",
                                        "postalCode" => "10000",
                                        "city" => [
                                            "name" => "Zagreb"
                                        ]
                                    ]
                                ]
                            ]
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
    public function create(Request $request, MailerInterface $mailer, LoggerInterface $logger): JsonResponse
    {
        $offerIncomingData = json_decode($request->getContent(), true);

        if (!isset($offerIncomingData["projectId"]) || !isset($offerIncomingData["companies"])) {
            return $this->json(["message" => "Offer create failed", "errors" => ["All data was not provided"]], 400);
        }

        $userData = $this->getUserFromToken($request, $this->jwtEncoder, $this->userRepository);

        $foundProject = $this->projectRepository->findOneBy(["id" => $offerIncomingData["projectId"], "user" => $userData]);
        if (!$foundProject || !($foundProject instanceof Project)) {
            return $this->json([
                "message" => "Offer create failed",
                "errors" => [
                    "Project was not found, check user and try again"
                ]
            ], 404);
        }

        $offersData = [];
        foreach ($offerIncomingData["companies"] as $companyID) {
            $foundCompany = $this->companyRepository->findOneBy(["id" => $companyID]);

            if ($foundCompany && $foundCompany instanceof Company) {

                $offer = new Offer();

                $offer->setState(OfferState::SENT);
                $offer->setCompany($foundCompany);
                $offer->setProject($foundProject);

                $this->entityManager->persist($offer);
                $this->entityManager->flush();

                $offersData[$offer->getId()] = $offer;

                $calculation = $foundProject->getCalculation();

                $calculation = $this->serializeData($this->serializer, $calculation, ["calculation"]);

                $calculation["lat"] = $calculation["lat"] + (rand(0, 100) / 1000);
                $calculation["lng"] = $calculation["lng"] + (rand(0, 100) / 1000);

                $calculation["location"] = explode(",", $calculation["location"]);
                $calculation["location"] = $calculation["location"][count($calculation["location"]) - 2] . ", " . $calculation["location"][count($calculation["location"]) - 1];


                $jwtToken = $this->jwtEncoder->encode([
                    "projectID" => $foundProject->getId(),
                    "companyID" => $foundCompany->getId(),
                    "offerID" => $offer->getId(),
                    "calculation" => $calculation
                ]);

                $projectLink = $_ENV["FRONTEND_LINK"] . "/form" . "?token=" . $jwtToken;

                $logger->info("Sending email to company: " . $foundCompany->getEmail());
                $logger->info($projectLink);

                $title = "[ASCO] Ponuda br. " . $foundProject->getId() . "";

                $email = (new TemplatedEmail())
                    ->from(new Address('asco@asco.com', 'Asco'))
                    ->to($foundCompany->getEmail())
                    ->subject($title)
                    ->htmlTemplate('send_offer.html.twig')
                    ->context([
                        "first_name" => $foundProject->getUser()->getUserProfile()->getFirstName(),
                        "last_name" => $foundProject->getUser()->getUserProfile()->getLastName(),
                        "potental_power" => intval(round($foundProject->getCalculation()->getPotentialPower() / 1000, 3)),
                        "address" => $calculation["location"],
                        "orientation" => $foundProject->getCalculation()->getRoofOrientation(),
                        "roof_surface" => $foundProject->getCalculation()->getRoofSurface(),
                        "roof_pitch" => $foundProject->getCalculation()->getRoofPitch(),
                        "power_consumption" => $foundProject->getCalculation()->getYearlyConsumption(),
                        "frontend_link" => $projectLink,
                        "logo" => $_ENV["FRONTEND_LINK"] . "/images/logo.png"
                    ]);

                $mailer->send($email);
            }
        }

        $offers = $this->serializeData($this->serializer, $offersData, [
            "company",
            "user",
            "user_user_profile",
            "user_profile",
            "user_profile_city",
            "city",
            "project",
            "calculation",
            "project_calculation",
            "project_user",
            "offer",
            "offer_company",
            "offer_project"
        ]);

        return $this->json(["message" => "Offer create success", "offers" => $offers], 201);
    }


    /**
     * Route that is uset to update existing offer
     *
     * @param Request $request
     * @return JsonResponse
     */
    #[Route("/edit", name: "edit_offer", methods: ["POST"])]
    #[OA\Post(
        path: "/api/offer/edit",
        description: "Used to edit offers",
        tags: ['offer'],
        parameters: [
            new OA\Parameter(
                name: 'JWT_TOKEN - HTTP cookie',
                description: "This cookie is required for user to log him out if cookie is not present then user can't be logged out",
                example: "sjowdasfjASODJSSAdjasakfpa2t4252",
            ),
        ],
        requestBody: new OA\RequestBody(
            description: 'There is offer exepcted the state type can be SENT, ACCEPTED, REJECTED, DONE and CHOSEN',
            content: new OA\JsonContent(
                properties: [
                    new OA\Property(property: "JWT", type: "string", example: "djaepFJASDPQWJPDFASFPQWDsadwqdšpj"),
                    new OA\Property(property: 'offer', type: 'object', example: [
                        "id" => 1,
                        "state" => "ACCEPTED",
                        "price (this is 5$)" => 500,
                        "description" => "The project is bla bla bla bla bla",
                        "offerDate" => "1983-05-22T00:00:00",
                    ]),
                ]
            )
        )
    )]
    #[OA\Response(
        response: 200,
        description: 'If everything went well the API will return a offers in response',
        content: new OA\JsonContent(
            type: 'object',
            properties: [
                new OA\Property(property: 'message', type: 'string', example: "Offers create success"),
                new OA\Property(property: 'offer', type: 'object', example: [
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
                            "project" => [
                                "id" => 1,
                                "name" => "Naziv projekta",
                                "calculation" =>
                                [
                                    "id" => 1,
                                    "roofSurface" => 25,
                                    "roofPitch" => 45,
                                    "roofOrientation" => "J",
                                    "lat" => 45.771045,
                                    "lng" => 15.995479,
                                    "yearlyConsumption" => 1000,
                                    "projectPrice" => 394491,
                                    "profitabiltyYears" => 46448,
                                    "effectiveness" => 0.999,
                                    "location" => "Travno,Zagreb",
                                    "profitabiltyMonthly" => [
                                        "1" => 2340.9514889237503,
                                        "2" => 2847.6640416512505,
                                        "3" => 4174.76834641375,
                                        "4" => 6293.896325935001,
                                        "5" => 4946.9017600937495,
                                        "6" => 5160.15144444625,
                                        "7" => 6107.3843695900005,
                                        "8" => 5351.88051845125,
                                        "9" => 4819.08237742375,
                                        "10" => 2909.29124401,
                                        "11" => 994.28299293625,
                                        "12" => 502.5696534812504
                                    ],
                                    "paybackPeroid" => 8.4931751636238,
                                    "installationPrice" => 120000,
                                    "equipmentPrice" => 274491,
                                    "potentialPower" => 3431
                                ],
                                "user" => [
                                    "id" => 3,
                                    "email" => "macko@gmail.com",
                                    "role" => "ROLE_ADMIN",
                                    "userProfile" => [
                                        "id" => 4,
                                        "firstName" => "Kiki",
                                        "lastName" => "Miki",
                                        "postalCode" => "10000",
                                        "city" => [
                                            "name" => "Zagreb"
                                        ]
                                    ]
                                ]
                            ]
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
    public function updateOffer(Request $request): JsonResponse
    {

        try {
            $offerIncomingData = json_decode($request->getContent(), true);

            $JWT = $offerIncomingData["JWT"];

            $companyID = $this->jwtEncoder->decode($JWT)["companyID"];
            $projectID = $this->jwtEncoder->decode($JWT)["projectID"];
            $offerID = $this->jwtEncoder->decode($JWT)["offerID"];

            $foundOffer = $this->offerRepository->findOneBy(["id" => $offerID]);

            if (!$foundOffer && !($foundOffer instanceof Offer)) {
                return $this->json(["message" => "Offer edit failed", "errors" => ["No such offer was found"]], 404);
            } elseif ($foundOffer->getCompany()->getId() !== $companyID || $foundOffer->getProject()->getId() !== $projectID) {
                return $this->json(["message" => "Offer edit failed", "errors" => ["No such offer was found"]], 404);
            }

            $form = $this->createForm(OfferType::class, $foundOffer);

            $form->submit($offerIncomingData);

            if (!$form->isValid()) {
                $this->extractFormErrors($form);
            }

            $this->entityManager->flush();

            $offer = $this->serializeData($this->serializer, $foundOffer, [
                "company",
                "user",
                "user_user_profile",
                "user_profile",
                "user_profile_city",
                "city",
                "project",
                "calculation",
                "project_calculation",
                "project_user",
                "offer",
                "offer_company",
                "offer_project"
            ]);

            return $this->json(["message" => "Offer was successfuly updated", "offer" => $offer], 200);
        } catch (\Exception $e) {
            return $this->json(["message" => "Offer update failed", "errors" => ["Could not verify user integrety, there was no token present", $e->getMessage()]], 400);
        }
    }

    /**
     * Route that takes jwt as a parameter and checks if offer is done
     *
     * @param Request $request
     * @return JsonResponse
     */
    #[Route("/check", name: "check_offer", methods: ["GET"])]
    #[OA\Get(
        path: "/api/offer/check",
        description: "Used to check if offer is done",
        tags: ['offer'],
        parameters: [
            new OA\Parameter(
                name: 'token',
                description: "JWT token",
            ),
        ],
    )]
    #[OA\Response(
        response: 200,
        description: 'If everything went well the API will return a offers in response',
        content: new OA\JsonContent(
            type: 'object',
            properties: [
                new OA\Property(property: 'message', type: 'string', example: "Offers check success"),
                new OA\Property(property: 'done', type: 'string', example: "true")
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
        description: 'This response will be dispatched if there is no such error',
        content: new OA\JsonContent(
            type: 'object',
            properties: [
                new OA\Property(property: 'message', type: 'string', example: "Offer check failed"),
                new OA\Property(property: "errors", type: "string", example: ["No such offer was found"])
            ]
        )
    )]
    public function checkOffer(Request $request): JsonResponse
    {
        try {
            $JWT = $request->query->get("token");
            $offerID = $this->jwtEncoder->decode($JWT)["offerID"];
            $foundOffer = $this->offerRepository->findOneBy(["id" => $offerID]);

            if (!$foundOffer && !($foundOffer instanceof Offer)) {
                return $this->json(["message" => "Offer check failed", "errors" => ["No such offer was found"]], 404);
            }

            if ($foundOffer->getState() !== OfferState::SENT) {
                return $this->json(["message" => "Check offer success", "done" => true], 200);
            }

            return $this->json(["message" => "Check offer success", "done" => false], 200);
        } catch (\Exception $e) {
            return $this->json(["message" => "Check offer failed", "errors" => ["There was an error with jwt", $e->getMessage()]], 400);
        }
    }


    /**
     * Route that takes offer id sets that offer to chosen and all other offers to declined
     *
     * @param Request $request
     * @return JsonResponse
     */
    #[Route("/choose", name: "choose_offer", methods: ["POST"])]
    #[OA\POST(
        path: "/api/offer/choose",
        description: "Used to choose offer",
        tags: ['offer'],
        requestBody: new OA\RequestBody(
            description: 'There is offer id exepcted',
            content: new OA\JsonContent(
                properties: [
                    new OA\Property(property: "JWT", type: "string", example: "djaepFJASDPQWJPDFASFPQWDsadwqdšpj"),
                ]
            )
        )
    )]
    #[OA\Response(
        response: 200,
        description: 'If everything went well the API will return a offers in response',
        content: new OA\JsonContent(
            type: 'object',
            properties: [
                new OA\Property(property: 'message', type: 'string', example: "Offers choose success"),
            ]
        )
    )]
    #[OA\Response(
        response: 404,
        description: 'This response will be dispatched if there is no such error',
        content: new OA\JsonContent(
            type: 'object',
            properties: [
                new OA\Property(property: 'message', type: 'string', example: "Offer choose failed"),
                new OA\Property(property: "errors", type: "string", example: ["No such offer was found"])
            ]
        )
    )]
    public function chooseOffer(Request $request): JsonResponse
    {
        $offerIncomingData = json_decode($request->getContent(), true);

        $offerID = $offerIncomingData["offerId"];

        $foundOffer = $this->offerRepository->findOneBy(["id" => $offerID]);

        if (!$foundOffer && !($foundOffer instanceof Offer)) {
            return $this->json(["message" => "Offer choose failed", "errors" => ["No such offer was found"]], 404);
        }

        // $foundOffer->getProject()->setUpdatedAt(new \DateTime());

        $foundOffer->setState(OfferState::CHOSEN);

        $this->entityManager->flush();

        $offers = $this->offerRepository->findBy(["project" => $foundOffer->getProject()]);

        foreach ($offers as $offer) {
            if ($offer->getId() !== $foundOffer->getId()) {
                if ($offer->getState() !== OfferState::REJECTED && $offer->getState() !== OfferState::CHOSEN) {
                    $offer->setState(OfferState::DECLINED);
                }
            }
        }

        $this->entityManager->flush();

        return $this->json(["message" => "Offer was successfuly chosen"], 200);
    }
}
