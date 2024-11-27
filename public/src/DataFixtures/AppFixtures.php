<?php

namespace App\DataFixtures;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use App\Factory\CongeFactory;
use App\Factory\UserFactory;
use Faker\Factory;


class AppFixtures extends Fixture
{
    /**
     * Cette méthode charge des données fictives pour la gestion des congés.
     */
    public function load(ObjectManager $manager): void
    {
        // Crée des utilisateurs fictifs
        $users = UserFactory::new()->createMany(10, function () {
            $faker = Factory::create();
            return [
                'nom' => $faker->lastName('fr_FR'),
                'prenom' => $faker->firstName('fr_FR'),
                'email' => $faker->unique()->safeEmail,
                'password' => password_hash('password', PASSWORD_BCRYPT),
            ];
        });

        // Crée des groupes fictifs
        $groupes = [
            'Administrateurs',
            'Employés',
            'Managers'
        ];

        $groupeEntities = [];
        foreach ($groupes as $groupeNom) {
            $groupe = new Groupe();
            $groupe->setNom($groupeNom);
            $manager->persist($groupe);
            $groupeEntities[] = $groupe; // Stocke les groupes créés
        }

        // Associe chaque utilisateur à des groupes fictifs
        foreach ($users as $user) {
            // Ajout aléatoire de groupes à chaque utilisateur
            $randomGroups = array_rand($groupeEntities, rand(1, count($groupeEntities)));
            if (!is_array($randomGroups)) {
                $randomGroups = [$randomGroups];
            }

            foreach ($randomGroups as $groupIndex) {
                $user->addGroupe($groupeEntities[$groupIndex]);
            }


            // Crée des types de congés
            $typesDeConges = [
                'Congé annuel',
                'Congé maladie',
                'Congé sans solde',
                'Congé maternité/paternité',
                'RTT',
                'Congé sabbatique'
            ];

            // Associe chaque utilisateur à des congés fictifs
            foreach ($users as $user) {
                $totalDays = 0; // Initialise le total des jours de congé pour l'utilisateur
                foreach ($typesDeConges as $typeDeConge) {
                    $days = rand(1, 30); // Génère un nombre aléatoire de jours de congé entre 1 et 30
                    if ($totalDays + $days > 30) {
                        break; // Arrête d'ajouter des congés si le total dépasse 30 jours
                    }
                    $dateDebut = new \DateTime(sprintf('-%d days', rand(1, 30))); // Génère une date de début aléatoire
                    CongeFactory::new()->create([
                        'type' => $typeDeConge, // Type de congé
                        'dateDebut' => $dateDebut, // Date de début du congé
                        'dateFin' => (clone $dateDebut)->modify(sprintf('+%d days', $days)), // Date de fin du congé
                        'statut' => rand(0, 1) ? 'approuvé' : 'en attente', // Statut aléatoire du congé
                        'user' => $user // Associe le congé à l'utilisateur
                    ]);
                    $totalDays += $days; // Ajoute les jours de congé au total
                }
            }
        }
    }

}
