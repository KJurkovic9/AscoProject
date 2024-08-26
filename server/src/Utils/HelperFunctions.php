<?php

namespace App\Utils;

use App\Entity\Calculation;
use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\SerializerInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Encoder\JWTEncoderInterface;

trait HelperFunctions
{
    /**
     * It serves to remove boilerplate from extracting function
     *
     * @param FormInterface $form
     * @return JsonResponse
     */
    public function extractFormErrors(FormInterface $form): JsonResponse
    {
        $errorMessages = [];

        foreach ($form->getErrors(true) as $error) {
            $errorMessages[] = $error->getMessage();
        }

        return $this->json([
            "message" => "Error while proccesing data",
            "errors" => $errorMessages,
        ], 400);
    }

    /**
     * It serves to make seriazlizing user data more robust
     *
     * @param SerializerInterface $serializer
     * @param mixed $data
     * @param array $groups
     * @return mixed
     */
    public function serializeData(SerializerInterface $serializer, mixed $data, array $groups): mixed
    {
        return json_decode($serializer->serialize(
            $data,
            "json",
            [
                'groups' => [
                    ...$groups
                ]
            ]
        ), true);
    }

    /**
     * Function that is used for installation price calculation
     * @param \App\Entity\Calculation $calculation
     * @return float
     */
    public function getInstallationPrice(Calculation $calculation)
    {
        $baseCostPerSquareMeter = 92.17;
        $baseCostPerKilowatt = 460.87;

        $roofTypeCoefficient = [
            'flat' => 1.0,
            'pitched' => 1.2,
        ];

        $roofType = $calculation->getRoofPitch() === 0 ? "flat" : "pitched";

        $installationCost = ($baseCostPerSquareMeter * $calculation->getRoofSurface() * $roofTypeCoefficient[$roofType]) +
            ($baseCostPerKilowatt * ($calculation->getPotentialPower() / 1000));

        return $installationCost;
    }

    /**
     * Get user from JWT cookie
     *
     * @param Request $request
     * @param JWTEncoderInterface $jwtManager
     * @param UserRepository $userRepository
     * @return User
     */
    public function getUserFromToken(Request $request, JWTEncoderInterface $jwtManager, UserRepository $userRepository): User
    {
        $decodedToken = $jwtManager->decode($request->cookies->get("JWT_TOKEN"));
        $user = $userRepository->findOneBy(["email" => $decodedToken["username"]]);
        return $user;
    }

}
