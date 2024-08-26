<?php

namespace App\DataFixtures;

use App\Entity\Company;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    private $companies = [
        [
            'name' => 'SunPower Solutions',
            'email' => 'info@sunpower.hr',
            'longitude' => 16.3738,
            'latitude' => 45.8150,
            'radius' => 50,
            'location' => 'Zagreb, Hrvatska',
            'mobile' => '091 123 4567'
        ],
        [
            'name' => 'EcoEnergy Croatia',
            'email' => 'contact@ecoenergy.hr',
            'longitude' => 15.8700,
            'latitude' => 45.5600,
            'radius' => 60,
            'location' => 'Karlovac, Hrvatska',
            'mobile' => '091 234 5678'
        ],
        [
            'name' => 'GreenTech Solar',
            'email' => 'support@greentech.hr',
            'longitude' => 14.4422,
            'latitude' => 45.3271,
            'radius' => 70,
            'location' => 'Rijeka, Hrvatska',
            'mobile' => '091 345 6789'
        ],
        [
            'name' => 'Adriatic Solar Solutions',
            'email' => 'hello@adriaticsolar.hr',
            'longitude' => 15.2314,
            'latitude' => 44.1194,
            'radius' => 80,
            'location' => 'Zadar, Hrvatska',
            'mobile' => '091 456 7890'
        ],
        [
            'name' => 'Dalmatia Solar Power',
            'email' => 'info@dalmatiasolar.hr',
            'longitude' => 16.4402,
            'latitude' => 43.5081,
            'radius' => 100,
            'location' => 'Split, Hrvatska',
            'mobile' => '091 567 8901'
        ]
    ];

    public function load(ObjectManager $manager): void
    {
        foreach ($this->companies as $companyData) {
            $company = new Company();
            $company->setName($companyData["name"]);
            $company->setEmail($companyData["email"]);
            $company->setLng($companyData["longitude"]);
            $company->setLat($companyData["latitude"]);
            $company->setRadius($companyData["radius"]);
            $company->setMobile($companyData["mobile"]);
            $company->setLocation($companyData["location"]);
            $company->setReviewAverage(0.0);
            $manager->persist($company);
        }
        $manager->flush();
    }
}
