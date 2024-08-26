<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Roles;
use App\Form\UserType;
use OpenApi\Attributes as OA;
use App\Utils\HelperFunctions;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Lexik\Bundle\JWTAuthenticationBundle\Encoder\JWTEncoderInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;

#[Route('/api', name: "api_user_")]
class UserController extends AbstractController
{
    use HelperFunctions;
    public function __construct(
        private UserPasswordHasherInterface $passwordHasher,
        private EntityManagerInterface $entityManager,
        private UserRepository $userRepository,
        private JWTTokenManagerInterface $jwtManager,
        private JWTEncoderInterface $jwtEncoder,
        private SerializerInterface $serializer,
        private MailerInterface $mailer,
    ) {
    }

    /**
     * Route that registers new user and returns a http cookie
     *
     * @param Request $request
     * @return JsonResponse
     */
    #[Route('/register', name: 'register', methods: ['POST'])]
    #[OA\Post(
        path: "/api/register",
        description: "Used to register user to the site it returns, a http only cookie on 
        successfuly response that will be automaticly stored and sends a password in a mail on register",
        tags: ['user'],
        parameters: [
            new OA\Parameter(
                name: 'none',
                description: "There are no parametars required for this request"
            )
        ],
        requestBody: new OA\RequestBody(
            description: 'There is user email expected in this request',
            content: new OA\JsonContent(
                properties: [
                    new OA\Property(property: 'email', type: 'string', example: "mirko@svemirko.com"),
                    new OA\Property(property: 'password', type: 'string', example: "#VelikiMirko8"),
                ]
            )
        )
    )]
    #[OA\Response(
        response: 201,
        description: 'If everything went well the API will return an user in response and cookie',
        headers: [
            new OA\Header(
                header: "Set-Cookie",
                description: "Cookie used for authorizing user be sure to include ```credintals: include``` in request",
                schema: new OA\Schema(type: "string")
            )
        ],
        content: new OA\JsonContent(
            type: 'object',
            properties: [
                new OA\Property(property: 'message', type: 'string', example: "User was successfuly registered and email was sent"),
                new OA\Property(property: 'user', type: 'object', example: [
                    "id" => 1,
                    "email" => "mikro@svemirko.com",
                ])
            ]
        )
    )]
    #[OA\Response(
        response: 400,
        description: 'This response will be dispatched if you are missing some creditanls in your request ie. password or email',
        content: new OA\JsonContent(
            type: 'object',
            properties: [
                new OA\Property(property: 'message', type: 'string', example: "User registration failed"),
                new OA\Property(
                    property: 'errors',
                    type: 'object',
                    description: 'Errors returned by the form',
                    example: [
                        "Email missing manditory field",
                    ]
                )
            ]
        )
    )]
    #[OA\Response(
        response: 409,
        description: 'This response will be dispatched if user with that email is already registered',
        content: new OA\JsonContent(
            type: 'object',
            properties: [
                new OA\Property(property: 'message', type: 'string', example: "There has been an error whilst trying to register a new user"),
                new OA\Property(
                    property: 'errors',
                    type: 'object',
                    description: 'Errors returned by the form',
                    example: [
                        "User with this email is already registered",
                    ]
                )
            ]
        )
    )]
    public function register(Request $request): JsonResponse
    {
        $userIncomingData = json_decode($request->getContent(), true);

        $userData = new User();

        $form = $this->createForm(UserType::class, $userData);

        $form->submit($userIncomingData);

        if (!$form->isValid()) {
            return $this->extractFormErrors($form);
        }

        $foundUser = $this->userRepository->findBy(['email' => $userIncomingData['email']]);
        if ($foundUser instanceof User) {
            return $this->json([
                'message' => "User registration failed",
                "errors" => ["User with this email is already registered"]
            ], 409);
        }

        $userData->setRole(Roles::user);

        $userData->setPassword($this->passwordHasher->hashPassword($userData, $userData->getPassword()));

        $this->entityManager->persist($userData);
        $this->entityManager->flush();

        $this->entityManager->flush();

        $user = $this->serializeData($this->serializer, $userData, ['user']);

        $jwtToken = $this->jwtManager->create($userData);

        $cookie = new Cookie(
            "JWT_TOKEN",
            $jwtToken,
            strtotime("+1 hour"),
            "/",
            null,
            true,
            true,
            true,
            "none"
        );

        $response = $this->json([
            'message' => "User was successfuly registered",
            "user" => $user
        ], 201);

        $response->headers->setCookie($cookie);

        return $response;
    }

    /**
     * Route that loggs in the user and creates new session
     *
     * @param Request $request
     * @return JsonResponse
     */
    #[Route('/login', name: 'login', methods: ['POST'])]
    #[OA\Post(
        path: "/api/login",
        description: "Used to login user, if credantials are incorrect an error will be dispatched",
        tags: ['user'],
        parameters: [
            new OA\Parameter(
                name: 'none',
                description: "This route dosen't expect any parameters"
            ),
        ],
        requestBody: new OA\RequestBody(
            description: 'There is user email, password and remember me expected in this request',
            content: new OA\JsonContent(
                properties: [
                    new OA\Property(property: 'email', type: 'string', example: "mirko@svemirko.com"),
                    new OA\Property(property: 'password', type: 'string', example: "#MirkovaSifra123"),
                    new OA\Property(property: 'rememberMe', type: 'boolean', example: "false"),
                ]
            )
        )
    )]
    #[OA\Response(
        response: 200,
        description: 'If everything went well the API will return a user in response and cookie',
        headers: [
            new OA\Header(
                header: "Set-Cookie",
                description: "Cookie used for authorizing user be sure to include ```credintals: include``` in request",
                schema: new OA\Schema(type: "string")
            )
        ],
        content: new OA\JsonContent(
            type: 'object',
            properties: [
                new OA\Property(property: 'message', type: 'string', example: "User was successfuly logged in"),
                new OA\Property(property: 'user', type: 'object', example: [
                    "id" => 1,
                    "email" => "mikro@svemirko.com",
                    "role" => [
                        "ROLE_USER",
                    ],
                ])
            ]
        )
    )]
    #[OA\Response(
        response: 400,
        description: 'This response will be dispatched if you are missing some creditanls in your request ie. password or email',
        content: new OA\JsonContent(
            type: 'object',
            properties: [
                new OA\Property(property: 'message', type: 'string', example: "User login failed"),
                new OA\Property(
                    property: 'errors',
                    type: 'object',
                    description: 'Errors returned by the form',
                    example: [
                        "Password Missing mandatory field",
                        "Email missing manditory field",
                    ]
                )
            ]
        )
    )]
    #[OA\Response(
        response: 403,
        description: 'This response will be dispatched if user is credintals are invalid in some way',
        content: new OA\JsonContent(
            type: 'object',
            properties: [
                new OA\Property(property: 'message', type: 'string', example: "User login failed"),
                new OA\Property(
                    property: 'errors',
                    type: 'object',
                    description: 'Errors encountered in validation',
                    example: [
                        "Invalid email or password",
                    ]
                )
            ]
        )
    )]
    public function login(Request $request): JsonResponse
    {
        $userIncomingData = json_decode($request->getContent(), true);

        if (!$userIncomingData["rememberMe"]) {
            $this->json([
                "message" => "User login failed",
                "errors" => ["Remember me field not provided in request"]
            ], 400);
        }

        $rememberMe = (bool) $userIncomingData['rememberMe'];

        unset($userIncomingData['rememberMe']);

        $userData = new User();

        $form = $this->createForm(UserType::class, $userData);

        $form->submit($userIncomingData);

        if (!$form->isValid()) {
            $this->extractFormErrors($form);
        }

        $foundUser = $this->userRepository->findOneBy(["email" => $userIncomingData["email"]]);

        if (!$foundUser instanceof User || !$this->passwordHasher->isPasswordValid($foundUser, $userData->getPassword())) {
            return $this->json([
                "message" => "User login failed",
                "errors" => [
                    "Invalid email or password"
                ]
            ], 401);
        }

        $user = $this->serializeData($this->serializer, $foundUser, ['user']);

        $jwtToken = $this->jwtManager->create($foundUser);
        if ($rememberMe) {
            $jwtToken = $this->jwtEncoder->decode($jwtToken);
            $jwtToken["exp"] = (new \DateTime("+1 month"))->getTimestamp();
            $jwtToken = $this->jwtEncoder->encode($jwtToken);
        }
        $expirationTime = $rememberMe ? strtotime("+1 month") : strtotime("+1 hour");

        $cookie = new Cookie(
            "JWT_TOKEN",
            $jwtToken,
            $expirationTime,
            "/",
            null,
            true,
            true,
            true,
            "none"
        );

        $response = $this->json([
            'message' => "User was successfuly logged in",
            "user" => $user
        ], 200);

        $response->headers->setCookie($cookie);

        return $response;
    }

    /**
     * Used to check if somebody is present in session or no
     *
     * @param Request $request
     * @return JsonResponse
     */
    #[Route("/check-session", name: "check_session", methods: ["POST"])]
    #[OA\Post(
        path: "/api/check-session",
        description: "Used to check if user is in session and if it is returns coresponding user",
        tags: ['user'],
        parameters: [
            new OA\Parameter(
                name: 'JWT_TOKEN - HTTP cookie',
                description: "This cookie is required for user to log him out if cookie is not present then user can't be logged out",
                example: "sjowdasfjASODJSSAdjasakfpa2t4252",
            ),
        ],
    )]
    #[OA\Response(
        response: 404,
        description: 'This response will be dispatched if cookie is not present',
        content: new OA\JsonContent(
            type: 'object',
            properties: [
                new OA\Property(property: 'message', type: 'string', example: "Check session failed"),
                new OA\Property(
                    property: 'errors',
                    type: 'object',
                    description: 'Errors returned by the exception',
                    example: [
                        "User cookie not present, nobody is in session",
                    ]
                )
            ]
        )
    )]
    #[OA\Response(
        response: 500,
        description: 'This response will be dispatched if cookie is invalid',
        content: new OA\JsonContent(
            type: 'object',
            properties: [
                new OA\Property(property: 'message', type: 'string', example: "Check session failed"),
                new OA\Property(
                    property: 'errors',
                    type: 'object',
                    description: 'Errors returned by the exception',
                    example: [
                        "Token invalid",
                    ]
                )
            ]
        )
    )]
    public function checkSession(Request $request): JsonResponse
    {
        if (!$request->cookies->has("JWT_TOKEN")) {
            return $this->json([
                "message" => "Check session failed",
                "errors" => ["User cookie not present, nobody is in session"]
            ], 404);
        }
        try {
            $decodedToken = $this->jwtEncoder->decode($request->cookies->get("JWT_TOKEN"));

            $foundUser = $this->userRepository->findOneBy(["email" => $decodedToken["username"]]);

            if (!($foundUser instanceof User)) {
                return $this->json([
                    "message" => "Check session failed",
                    "errors" => ["User is nonexistant"]
                ], 404);
            }

            $user = $this->serializeData($this->serializer, $foundUser, ["user"]);

            return $this->json([
                "message" => "Check session failed",
                "user" => $user,
            ], 200);

        } catch (\Exception $e) {
            return $this->json([
                "message" => "Check session failed",
                "errors" => [$e->getMessage()]
            ], 500);
        }
    }

    /**
     * Route that logs out the user and destroys session
     *
     * @param Request $request
     * @return JsonResponse
     */
    #[Route('/logout', name: 'logout', methods: ['DELETE'])]
    #[OA\Delete(
        path: "/api/logout",
        description: "Used to logout user and destroys session if user is not present in session error while be dispatched",
        tags: ['user'],
        parameters: [
            new OA\Parameter(
                name: 'JWT_TOKEN - cookie',
                description: "This cookie is required for user to log him out if cookie is not present then user can't be logged out",
                example: "sjowdasfjASODJSSAdjasakfpa2t4252",
            ),
        ],
    )]
    #[OA\Response(
        response: 200,
        description: 'If everything went well the API will return a message about user being successfully logged out',
        content: new OA\JsonContent(
            type: 'object',
            properties: [
                new OA\Property(property: 'message', type: 'string', example: "User was successfuly logged out"),
            ]
        )
    )]
    #[OA\Response(
        response: 500,
        description: 'This response will be dispatched if cookie is invalid',
        content: new OA\JsonContent(
            type: 'object',
            properties: [
                new OA\Property(property: 'message', type: 'string', example: "User logout failed"),
                new OA\Property(
                    property: 'errors',
                    type: 'object',
                    description: 'Errors returned by the exception',
                    example: [
                        "Token invalid",
                    ]
                )
            ]
        )
    )]
    public function logout(Request $request): JsonResponse
    {
        try {
            $request->cookies->remove("JWT_TOKEN");
            $response =  $this->json([
                "message" => "User was successfuly logged out",
            ], 200);
            $response->headers->clearCookie('JWT_TOKEN');
            return $response;

        } catch (\Exception $e) {
            return $this->json([
                "message" => "User logout failed",
                "errors" => [$e->getMessage()]
            ], 500);
        }
    }
}
