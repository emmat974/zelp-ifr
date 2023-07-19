<?php

namespace App\DataFixtures;

use App\Entity\Avis;
use App\Entity\Restaurant;
use App\Entity\User;
use App\Entity\Ville;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Faker;

class AppFixtures extends Fixture
{

    public function __construct(
        private UserPasswordHasherInterface $userPasswordHasher
    ) {
    }

    public function load(ObjectManager $manager): void
    {

        $faker = Faker\Factory::create('fr_FR');

        $villesData = [
            ['nom' => 'Saint-Denis', 'codePostal' => '97400'],
            ['nom' => 'Saint-Paul', 'codePostal' => '97460'],
            ['nom' => 'Saint-Pierre', 'codePostal' => '97410'],
            ['nom' => 'Le Tampon', 'codePostal' => '97430'],
            ['nom' => 'Saint-André', 'codePostal' => '97440'],
            ['nom' => 'Saint-Louis', 'codePostal' => '97421'],
            ['nom' => 'Saint-Joseph', 'codePostal' => '97480'],
            ['nom' => 'Le Port', 'codePostal' => '97420'],
            ['nom' => 'La Possession', 'codePostal' => '97419'],
            ['nom' => 'Sainte-Marie', 'codePostal' => '97438'],
            ['nom' => 'Saint-Benoît', 'codePostal' => '97470'],
            ['nom' => 'Saint-Leu', 'codePostal' => '97436'],
            ['nom' => 'Sainte-Suzanne', 'codePostal' => '97441'],
            ['nom' => 'La Plaine-des-Palmistes', 'codePostal' => '97431'],
            ['nom' => 'Petite-Île', 'codePostal' => '97429'],
            ['nom' => 'Les Avirons', 'codePostal' => '97425'],
            ['nom' => 'Entre-Deux', 'codePostal' => '97414'],
            ['nom' => 'Sainte-Rose', 'codePostal' => '97439'],
            ['nom' => 'Cilaos', 'codePostal' => '97413'],
            ['nom' => 'Salazie', 'codePostal' => '97433'],
            ['nom' => 'Bras-Panon', 'codePostal' => '97412'],
            ['nom' => 'Saint-Philippe', 'codePostal' => '97442'],
            ['nom' => 'Sainte-Anne', 'codePostal' => '97437'],
            ['nom' => 'Les Trois-Bassins', 'codePostal' => '97426'],
            ['nom' => 'La Rivière-Saint-Louis', 'codePostal' => '97421'],
            ['nom' => 'Étang Salé', 'codePostal' => '97427'],
        ];
        // On va générée des fausses data
        // Faux restaurateurs

        $clients = [];

        foreach ($villesData as $villeData) {
            $ville = new Ville();
            $ville->setName($villeData['nom']);
            $ville->setCodePostal($villeData['codePostal']);

            $manager->persist($ville);

            $lMax = rand(1, 5);
            for ($l = 0; $l < $lMax; $l++) {
                $client = new User();
                $client
                    ->setEmail($faker->email())
                    ->setVille($ville)
                    ->setFirstname($faker->firstName())
                    ->setLastname($faker->lastName())
                    ->setRoles(['ROLE_CLIENT']);

                $client->setPassword($this->userPasswordHasher->hashPassword($client, "client"));
                $manager->persist($client);

                $clients[] = $client;
            }

            for ($i = 0; $i < 5; $i++) {
                $r = new User();

                $r->setEmail($faker->email())
                    ->setVille($ville)
                    ->setFirstname($faker->firstName())
                    ->setLastname($faker->lastName())
                    ->setRoles(["ROLE_RESTAURATEUR"]);

                $r->setPassword($this->userPasswordHasher->hashPassword($r, "restaurateur"));

                $manager->persist($r);

                $yMax = rand(1, 10);
                for ($y = 0; $y < $yMax; $y++) {
                    $restaurant = new Restaurant();

                    $restaurant
                        ->setUser($r)
                        ->setName($faker->word())
                        ->setVille($ville)
                        ->setDescription($faker->sentence(200));

                    $manager->persist($restaurant);

                    $avisOrNot = rand(1, 10);
                    if ($avisOrNot > 5) {
                        for ($m = 0; $m < $avisOrNot; $m++) {
                            $cl = $clients[array_rand($clients)];

                            $avis = new Avis();

                            $rating = rand(1, 5);

                            $avis
                                ->setRestaurant($restaurant)
                                ->setUser($cl)
                                ->setRating($rating)
                                ->setMessage($faker->sentence(rand(10, 200)));

                            $manager->persist($avis);
                        }
                    }
                }
            }
        }

        // On génère des données qu'on peut manipulés
        //Client
        $client = new User();
        $client
            ->setEmail("client@client.com")
            ->setVille($ville)
            ->setFirstname("Client")
            ->setLastname("Client")
            ->setRoles(['ROLE_CLIENT']);

        $client->setPassword($this->userPasswordHasher->hashPassword($client, "client"));
        $manager->persist($client);


        $restaurateur = new User();
        $restaurateur
            ->setEmail("restaurateur@restaurateur.com")
            ->setVille($ville)
            ->setFirstname("Restaurateur")
            ->setLastname("Restaurateur")
            ->setRoles(["ROLE_RESTAURATEUR"]);

        $restaurateur->setPassword($this->userPasswordHasher->hashPassword($restaurateur, "restaurateur"));
        $manager->persist($restaurateur);


        $admin = new User();

        $admin
            ->setEmail("admin@admin.com")
            ->setVille($ville)
            ->setFirstname("Admin")
            ->setLastname("Admin")
            ->setRoles(["ROLE_ADMIN"]);

        $admin->setPassword($this->userPasswordHasher->hashPassword($admin, "admin"));

        $manager->persist($admin);



        // $product = new Product();
        // $manager->persist($product);

        $manager->flush();
    }
}
