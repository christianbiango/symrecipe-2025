<?php

namespace App\DataFixtures;

use App\Entity\Contact;
use App\Entity\Ingredient;
use App\Entity\Mark;
use App\Entity\Recipe;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Faker\Generator;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
    /**
     * @var Generator
     */
    private Generator $faker;
    public function __construct(){
        $this->faker = Factory::create("fr_FR");
    }

    public function load(ObjectManager $manager): void
    {
        // Users
        $users = [];
        for ($j = 0; $j < 25; $j++) {
            $user = new User();
            $user->setFullName($this->faker->name())
                ->setEmail($this->faker->email())
                ->setPassword("password")
                ->setRoles(["ROLE_USER"])
                ->setPlainPassword("password")
                ->setPseudo(mt_rand(0,1) == 1 ? $this->faker->firstName() : null);

            $users[] = $user;
            $manager->persist($user);
        }

        // ingredient
        $ingredients = [];
        for ($i = 0; $i <= 50; $i++) {
            $ingredient = new Ingredient();
            $ingredient->setName($this->faker->word())
                ->setPrice(mt_rand(0, 100))
                ->setUserIngredient($users[mt_rand(0, count($users) - 1)]);
            $ingredients[] = $ingredient;
            $manager->persist($ingredient);
        }

        // recipes
        $recipes = [];
        for ($j = 0; $j < 25; $j++) {
            $recipe = new Recipe();
            $recipe
                ->setName($this->faker->word())
                ->setTime(mt_rand(0,1) == 1 ? mt_rand(1,1440) : null)
                ->setNbPeople(mt_rand(0,1) == 1 ? mt_rand(0,50) : null)
                ->setDifficulty(mt_rand(0,1) == 1 ? mt_rand(1,5) : null)
                ->setDescription($this->faker->paragraph())
                ->setPrice(mt_rand(0, 1) == 1 ? mt_rand(1,1000) : null)
                ->setIsFavorite(mt_rand(0,1) == 1)
                ->setUserRecipes($users[mt_rand(0, count($users) - 1)])
                ->setIsPublic(mt_rand(0,1) == 1)
            ;

            for ($k = 0; $k < mt_rand(5, 15); $k++) {
                $recipe->addIngredient($ingredients[mt_rand(0, count($ingredients) - 1)]);
            }
            $recipes[] = $recipe;
            $manager->persist($recipe);
        }

        // Marks
        foreach ($recipes as $recipe) {
            for($i=0;$i<mt_rand(0,4); $i++){
                $mark = new Mark();
                $mark->setMark(mt_rand(1,5))
                    ->setUserMark($users[mt_rand(0, count($users) - 1)])
                    ->setRecipe($recipe);
                $manager->persist($mark);
            }
        }
        // Contact
        for ($j = 0; $j < 5; $j++) {
            $contact = new Contact();
            $contact
                ->setFullName($this->faker->name())
                ->setEmail($this->faker->email())
                ->setSubject("Demande n°" . $j + 1)
                ->setMessage($this->faker->paragraph());

            $manager->persist($contact);
        }

        $manager->flush();

    }
}
