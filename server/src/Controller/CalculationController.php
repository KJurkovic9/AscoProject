<?php

namespace App\Controller;

use App\Entity\Calculation;
use App\Form\CalculationType;
use App\Repository\CalculationRepository;
use Knp\Bundle\SnappyBundle\Snappy\Response\PdfResponse;
use Knp\Snappy\Pdf;
use OpenApi\Attributes as OA;
use App\Utils\HelperFunctions;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/api/calculation', name: "api_calculation_")]
class CalculationController extends AbstractController
{
    use HelperFunctions;

    public function __construct(
        private HttpClientInterface $client,
        private EntityManagerInterface $entityManager,
        private Pdf $pdf,
        private CalculationRepository $calculationRepository,
    ) {
    }

    /**
     * Used to calculate user's solar panel profitabilty
     *
     * @param Request $request
     * @return JsonResponse
     */
    #[Route('/calculate', name: 'calculate', methods: ["POST"])]
    #[OA\Post(
        path: "/api/calculation/calculate",
        description: "Used to calculate user's accesabilty to solar panel based on provided information",
        tags: ['calculation'],
        parameters: [
            new OA\Parameter(
                name: 'none',
                description: "There are no parametars required for this request"
            )
        ],
        requestBody: new OA\RequestBody(
            description: 'There is lot of expected in this request every one finishing with ? is optional',
            content: new OA\JsonContent(
                properties: [
                    new OA\Property(property: 'roofSurface', type: 'float', example: "25.2"),
                    new OA\Property(property: 'roofPitch', type: 'integer', example: "45"),
                    new OA\Property(property: 'roofOrientation', type: 'string', example: "J", maxLength: 2),
                    new OA\Property(property: 'lat', type: 'float', example: "54.32543"),
                    new OA\Property(property: 'lng', type: 'float', example: "23.43534"),
                    new OA\Property(property: 'location', type: 'string', example: "Travno, Zagreb", maxLength: 255),
                    new OA\Property(property: "yearlyConsumption", type: "intger", example: "325"),
                    new OA\Property(property: 'lifespan?', type: 'integer', example: "21"),
                    new OA\Property(property: 'budget?', type: 'integer', example: "45.000.050"),
                ]
            )
        )
    )]
    #[OA\Response(
        response: 201,
        description: 'If everything went well the API will send an a calculation and all its data, exepct data
        data is required api will give you project price with installation included beaware each price is int*100 so
        if you recive 1200000 it is actually 12000,00, next I also supplied you with some data that can easily be shown
        to user so you have profitabilty years and months which basically refers to how much user saves in month or year
        (beware this is money so *100), effectivnes refers to how effective the solar panels are based on roof pitch and orientation',
        content: new OA\JsonContent(
            type: 'object',
            properties: [
                new OA\Property(property: 'message', type: 'string', example: "Calculation was successful"),
                new OA\Property(property: 'calculation', type: 'object', example: [
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
                    "location" => "Travno, Zagreb",
                    "paybackPeroid" => 7.243750727787913,
                    "installationPrice" => 197500,
                    "equipmentPrice" => 548983,
                    "potentialPower" => 6862
                ])
            ]
        )
    )]
    #[OA\Response(
        response: 400,
        description: 'This response will be dispatched if you are missing some manditory values in request body',
        content: new OA\JsonContent(
            type: 'object',
            properties: [
                new OA\Property(property: 'message', type: 'string', example: "Error while proccessing data"),
                new OA\Property(
                    property: 'errors',
                    type: 'object',
                    description: 'Errors returned by the form',
                    example: [
                        "Roof orientation is missing manditory field",
                    ]
                )
            ]
        )
    )]
    public function calculate(Request $request): JsonResponse
    {
        $preCalculation = json_decode($request->getContent(), true);

        $calculationData = new Calculation();

        $form = $this->createForm(CalculationType::class, $calculationData);

        $form->submit($preCalculation);

        if (!$form->isValid()) {
            return $this->extractFormErrors($form);
        }

        $calculationData->setEffectiveness(
            (new Effectivnes())->get(
                $calculationData->getRoofOrientation(),
                $calculationData->getRoofPitch(),
            )
        );

        #Poteintal power
        #Power=Effective Roof Area×Solar Irradiance×Efficiency => get strength in W


        /* $response = $this->client->request(
             'GET',
             'https://api.weatherbit.io/v2.0/history/subhourly?lat='. $calculationData->getLat() .'&lon='. $calculationData->getLng() .'&start_date=2024-03-20&end_date=2024-04-18&key='. $_ENV["SOLAR_IRRADIANCE_API"]
         );

         //dump($response->toArray()["data"][36]["dhi"]);

         if($response->getStatusCode() === 200) {
             $dhiSum = 0;
             foreach($response->toArray()["data"] as $data){
                 $dhiSum += $data["dhi"];
             }
             $solarIrradiance = $dhiSum / count($response->toArray()["data"]);
         }else {
             $solarIrradiance = ((float) 6.06 / 24) * 1000;
         }
         */

        $allYearDHI = [];
        foreach (MonthlyDHI::$monthlyDHI["outputs"]["monthly"] as $month) {
            $allYearDHI[$month["month"]] = $month["H(i)_m"];
        }

        $DHIsum = 0;
        foreach ($allYearDHI as $month => $DHI) {
            $DHIsum += $DHI;
        }

        $solarIrradiance = $DHIsum / 12;

        if (isset($preCalculation["budget"]) && $calculationData->getBudget()) {

            $calculationData->setRoofSurface($calculationData->getBudget() / ($solarIrradiance * $calculationData->getEffectiveness() * 0.8));

        }

        $potentialPower = $calculationData->getRoofSurface() * $solarIrradiance / 1000;

        $calculationData->setPotentialPower($potentialPower);

        $realPower = $potentialPower * $calculationData->getEffectiveness();

        #This is the price for polycrystaline 0.8

        $panelPrice = $potentialPower * 0.8;

        $calculationData->setInstallationPrice($this->getInstallationPrice($calculationData) * 100);

        $calculationData->setEquipmentPrice($panelPrice * 100);

        $calculationData->setProjectPrice($calculationData->getEquipmentPrice() + $calculationData->getInstallationPrice());

        $monthlyProduction = [];

        foreach ($allYearDHI as $month => $DHI) {
            $monthlyProduction[$month] = ($DHI * $calculationData->getRoofSurface() * $calculationData->getEffectiveness() * 6) * 30;
        }

        $monthlyConsumption = $calculationData->getYearlyConsumption() / 12;

        $profitabiltyMonthly = [];
        $profitabilityYearly = 0;
        foreach ($monthlyProduction as $month => $power) {
            $monthlyProfit = (abs($monthlyConsumption - ($power / 1000)) * 0.0725325) * 100;
            $profitabilityYearly += $monthlyProfit;
            $profitabiltyMonthly[$month] = $monthlyProfit;
        }

        $calculationData->setProfitabiltyMonthly($profitabiltyMonthly);
        $calculationData->setProfitabiltyYears($profitabilityYearly);
        $calculationData->setPaybackPeroid(($calculationData->getProjectPrice() / 100) / ($calculationData->getProfitabiltyYears() / 100));

        $this->entityManager->persist($calculationData);
        $this->entityManager->flush();

        return $this->json(["message" => "Calculation was successfuly", "calculation" => $calculationData], 201);
    }

    /**
     * Route that is used to generate pdf documment of user's calculation
     * @return mixed
     */
    #[Route("/generate-pdf/{id}", "generate_report", methods: ["POST"])]
    #[OA\Post(
        path: "/api/calculation/generate-pdf/{id}",
        description: "Used to generate pdf of a calculation",
        tags: ['calculation'],
    )]
    #[OA\Response(
        response: 200,
        description: 'If everything went well the API will return a application/pdf response',
    )]
    #[OA\Response(
        response: 400,
        description: 'This response will be dispatched calculation id has not been provided',
        content: new OA\JsonContent(
            type: 'object',
            properties: [
                new OA\Property(property: 'message', type: 'string', example: "Calculation pdf generation failed"),
                new OA\Property(
                    property: 'errors',
                    type: 'object',
                    description: 'Errors returned by the route',
                    example: [
                        "No id in params",
                    ]
                )
            ]
        )
    )]
    #[OA\Response(
        response: 404,
        description: 'This response will be dispatched calculation has not been found',
        content: new OA\JsonContent(
            type: 'object',
            properties: [
                new OA\Property(property: 'message', type: 'string', example: "Calculation pdf generation failed"),
                new OA\Property(
                    property: 'errors',
                    type: 'object',
                    description: 'Errors returned by the route',
                    example: [
                        "No such calculation has been found",
                    ]
                )
            ]
        )
    )]
    public function generateReport(Request $request): PdfResponse|JsonResponse
    {
        if (!$request->get("id") || !is_numeric($request->get("id"))) {
            return $this->json(["message" => "Calculation pdf generation failed", "errors" => ["No id in params"]], 400);
        }

        $foundCalculation = $this->calculationRepository->findOneBy(["id" => $request->get("id")]);
        if (!$foundCalculation || !($foundCalculation instanceof Calculation)) {
            return $this->json([
                "message" => "Calculation pdf generation failed",
                "errors" => ["No such calculation has been found"]
            ], 404);
        }

        $html = $this->renderView(
            'calculation_pdf.html.twig',
            [
                #Ovdje stavi parametere koji ti trebaju iz kalkulacije i onda tipa da
                #Accesas ovo u templateu stavio bi <p>{{some}}</p>
                #Samo prati konvenciju da je tu snake case umjesto camel case-a
                'some' => "Custom string",
            ]
        );

        return new PdfResponse(
            $this->pdf->getOutputFromHtml($html),
            'calculation.pdf'
        );
    }
}
