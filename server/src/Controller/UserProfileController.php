<?php

namespace App\Controller;

use App\Entity\City;
use App\Form\CityType;
use App\Entity\UserProfile;
use App\Form\UserProfileType;
use App\Repository\CityRepository;
use App\Repository\UserProfileRepository;
use OpenApi\Attributes as OA;
use App\Utils\HelperFunctions;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Lexik\Bundle\JWTAuthenticationBundle\Encoder\JWTEncoderInterface;

#[Route('/api/user-profile', name: "api_user_profile_")]
class UserProfileController extends AbstractController
{
    use HelperFunctions;

    public function __construct(
        private UserRepository $userRepository,
        private UserProfileRepository $userProfileRepository,
        private CityRepository $cityRepository,
        private SerializerInterface $serializerInterface,
        private EntityManagerInterface $entityManager,
        private JWTEncoderInterface $jwtEncoder,
    ) {
    }

    /**
     * Creates user profile
     *
     * @param Request $request
     * @return JsonResponse
     */
    #[Route(path: "/create", name: "create", methods: ["POST"])]
    #[OA\Post(
        path: "/api/user-profile/create",
        description: "Used to create a profile a for a user",
        tags: ['user_profile'],
        parameters: [
            new OA\Parameter(
                name: 'JWT_TOKEN - HTTP cookie',
                description: "This cookie is required for user to log him out if cookie is not present then user can't be logged out",
                example: "sjowdasfjASODJSSAdjasakfpa2t4252",
            ),
        ],
        requestBody: new OA\RequestBody(
            description: 'There is user email expected in this request',
            content: new OA\JsonContent(
                properties: [
                    new OA\Property(property: 'message', type: 'string', example: "User profile created successfully"),
                    new OA\Property(property: 'userProfile', type: 'object', example: [
                        "firstName" => "Darko",
                        "lastName" => "Darac",
                        "postalCode" => 10000,
                        "city" => ["name" => "Zagreb"],
                    ])
                ],
            )
        )
    )]
    #[OA\Response(
        response: 201,
        description: 'If everything went well the API will return an user in response and cookie',
        content: new OA\JsonContent(
            type: 'object',
            properties: [
                new OA\Property(property: 'message', type: 'string', example: "User profile created successfully"),
                new OA\Property(property: 'userProfile', type: 'object', example: [
                    "id" => 2,
                    "firstName" => "Darko",
                    "lastName" => "Darac",
                    "user" => ["id" => 1, "email" => "mac@mac.com", "role" => "ROLE_USER"],
                    "city" => ["name" => "Zagreb", "postalCode" => 10000],
                ])
            ]
        )
    )]
    #[OA\Response(
        response: 400,
        description: 'This response will be dispatched if you are missing some creditanls in your request ie. firstName or lastName,
        or if there has been error whilist trying to verify a cookie',
        content: new OA\JsonContent(
            type: 'object',
            properties: [
                new OA\Property(property: 'message', type: 'string', example: "Error while proccesing data"),
                new OA\Property(
                    property: 'errors',
                    type: 'object',
                    description: 'Errors returned by the form',
                    example: [
                        "First name missing manditory field",
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

        $userProfileIncomingData = json_decode($request->getContent(), true);
        $userData = $this->getUserFromToken($request, $this->jwtEncoder, $this->userRepository);

        $foundUserProfile = $this->userProfileRepository->findOneBy(["user" => $userData]);
        if ($foundUserProfile instanceof UserProfile) {
            return $this->json(["message" => "User profile creation failed", "errors" => ["User profile already exists"]], 400);
        }

        $userProfileData = new UserProfile();

        $userProfileForm = $this->createForm(UserProfileType::class, $userProfileData);
        $userProfileForm->submit($userProfileIncomingData);

        if (!$userProfileForm->isValid()) {
            return $this->extractFormErrors($userProfileForm);
        }

        $userProfileData->setUser($userData);
        $this->entityManager->persist($userProfileData);
        $this->entityManager->flush();

        $userProfile = $this->serializeData($this->serializerInterface, $userProfileData, [
            "user",
            "user_profile",
            "user_profile_user",
            "user_profile_city",
            "city"
        ]);

        return $this->json(["message" => "User profile created successfully", "userProfile" => $userProfile], 200);
    }

    /**
     * Used to get all user profiles
     *
     * @param Request $request
     * @return JsonResponse
     */
    #[Route(path: "/get", name: "get", methods: ["GET"])]
    #[OA\Get(
        path: "/api/user-profile/get",
        description: "Used to fetch all user profiles assoiciated with current user",
        tags: ['user_profile'],
        parameters: [
            new OA\Parameter(
                name: 'JWT_TOKEN - HTTP cookie',
                description: "This cookie is required for user to log him out if cookie is not present then user can't be logged out",
                example: "sjowdasfjASODJSSAdjasakfpa2t4252",
            ),
        ],
    )]
    #[OA\Response(
        response: 201,
        description: 'If everything went well the API will return an user in response and cookie',
        content: new OA\JsonContent(
            type: 'object',
            properties: [
                new OA\Property(property: 'message', type: 'string', example: "User profile fetched all successfully"),
                new OA\Property(property: 'user', type: 'object', example: [
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
                ]),
                new OA\Property(property: 'reviewCount', type: 'string', example: "1"),
                new OA\Property(property: 'projectCount', type: 'string', example: "5"),
                new OA\Property(property: 'offerCount', type: 'string', example: "0"),
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
    public function get(Request $request): JsonResponse
    {
        $userData = $this->getUserFromToken($request, $this->jwtEncoder, $this->userRepository);

        $reviewCount = count($userData->getReviews());
        $projects = $userData->getProjects();
        $projectCount = count($projects);
        $offerCount = 0;

        foreach ($projects as $project) {
            $projectOffers = $project->getOffers();
            foreach ($projectOffers as $offer) {
                if (OfferState::ACCEPTED === $offer->getState()) {
                    $offerCount++;
                }
            }
        }

        $user = $this->serializeData($this->serializerInterface, $userData, ["user", "user_profile", "user_user_profile", "user_profile_city", "city"]);

        return $this->json([
            "message" => "User profile fetched successfully",
            "user" => $user,
            "reviewCount" => $reviewCount,
            "projectCount" => $projectCount,
            "offerCount" => $offerCount
        ], 200);
    }
}
