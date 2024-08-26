<?php

namespace App\Controller;

use App\Entity\Guide;
use App\Entity\Roles;
use App\Entity\Section;
use App\Form\SectionType;
use OpenApi\Attributes as OA;
use App\Utils\HelperFunctions;
use App\Repository\UserRepository;
use App\Repository\GuideRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Lexik\Bundle\JWTAuthenticationBundle\Encoder\JWTEncoderInterface;

#[Route('/api/section', name: "api_section_")]
class SectionController extends AbstractController
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
    #[Route('/{id}', name: 'create', methods: ["POST"])]
    #[OA\Post(
        path: "/api/section/{id}",
        description: "Used to create section (ONLY admin can create section)",
        tags: ['section'],
        parameters: [
            new OA\Parameter(
                name: 'JWT_TOKEN - HTTP cookie',
                description: "This cookie is required for user to log him out if cookie is not present then user can't be logged out",
                example: "sjowdasfjASODJSSAdjasakfpa2t4252",
            ),
        ],
        requestBody: new OA\RequestBody(
            description: 'There is section title,text and optionaly image, WARRNING! Request must be multipart/formData because of an image',
            content: new OA\JsonContent(
                properties: [
                    new OA\Property(property: 'title', type: 'string', example: "Najjaci post baja"),
                    new OA\Property(property: 'text', type: 'string', example: "Post je najjaci"),
                    new OA\Property(property: 'image', type: 'string', example: "epicSlika.jpg"),
                ]
            )
        )
    )]
    #[OA\Response(
        response: 200,
        description: 'If everything went well the API will return a section in response',
        content: new OA\JsonContent(
            type: 'object',
            properties: [
                new OA\Property(property: 'message', type: 'string', example: "Section creation success"),
                new OA\Property(property: 'section', type: 'object', example: [
                    "section" => [
                        "id" => 1,
                        "title" => "Kul title",
                        "text" => "Epic text",
                        "image" => "epicSlika.jpg",
                        "guide" => ["id" => 1, "title" => "Kul guide"]
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
                new OA\Property(property: 'message', type: 'string', example: "Section creation failed"),
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
    public function createSection(Request $request): JsonResponse
    {
        $sectionData = new Section();

        $form = $this->createForm(SectionType::class, $sectionData);

        if (!$request->get("id") || !is_numeric($request->get("id"))) {
            return $this->json(["message" => "Section create failed", "errors" => ["There was no number present in params"]], 400);
        }

        $form->submit($request->request->all());

        if (!$form->isValid()) {
            return $this->extractFormErrors($form);
        }

        $foundGuide = $this->guideRepository->findOneBy(["id" => $request->get("id")]);
        if (!$foundGuide || !($foundGuide instanceof Guide)) {
            return $this->json([
                "message" => "Section creation failed",
                "errors" =>
                    ["No such guide has been found"]
            ], 404);
        }

        $userData = $this->getUserFromToken($request, $this->jwtEncoder, $this->userRepository);

        if ($userData->getRole() !== Roles::admin->value) {
            return $this->json([
                "message" => "Section creation failed",
                "errors" =>
                    ["You are not allowed to perform this action, only admins can add guides"]
            ], 403);
        }

        if ($request->files->get('image')) {

            $sectionImage = $request->files->get('image');
            $originalImageName = $sectionImage->getClientOriginalName();

            $safeImageName = $this->sluggerInterface->slug($originalImageName);

            $newImageName = $safeImageName . '-' . uniqid() . '.' . $sectionImage->guessExtension();

            $sectionImage->move($this->getParameter('section_images_directory'), $newImageName);

            $sectionData->setImage($newImageName);
        }

        $sectionData->setGuide($foundGuide);

        $this->entityManager->persist($sectionData);
        $this->entityManager->flush();

        $section = $this->serializeData($this->serializer, $sectionData, ["section", "section_guide", "guide"]);

        return $this->json(["message" => "Section creation succes", "section" => $section], 201);
    }
}
