<?php

declare(strict_types=1);

namespace App\DataFixtures;

use App\Entity\Business;
use App\Entity\BusinessType;
use App\Entity\Category;
use App\Entity\Consumer;
use App\Entity\Order;
use App\Entity\Package;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
    public function __construct(
        private readonly UserPasswordHasherInterface $passwordHasher,
    ) {
    }

    public function load(ObjectManager $manager): void
    {
        // --- Business Types ---
        $restaurantType = new BusinessType();
        $restaurantType->setName('Restaurant');
        $manager->persist($restaurantType);

        $bakeryType = new BusinessType();
        $bakeryType->setName('Bakery');
        $manager->persist($bakeryType);

        $cafeType = new BusinessType();
        $cafeType->setName('Cafe');
        $manager->persist($cafeType);

        $groceryType = new BusinessType();
        $groceryType->setName('Grocery Store');
        $manager->persist($groceryType);

        // --- Categories ---
        $mealsCategory = new Category();
        $mealsCategory->setName('Meals');
        $manager->persist($mealsCategory);

        $bakedGoodsCategory = new Category();
        $bakedGoodsCategory->setName('Baked Goods');
        $manager->persist($bakedGoodsCategory);

        $dessertsCategory = new Category();
        $dessertsCategory->setName('Desserts');
        $manager->persist($dessertsCategory);

        $groceriesCategory = new Category();
        $groceriesCategory->setName('Groceries');
        $manager->persist($groceriesCategory);

        // --- Businesses ---
        $businesses = [];

        $businessData = [
            ['Green Bistro', 'Craiova', 'Calea Bucuresti', '12A', '0740123456', $restaurantType],
            ['Sunrise Bakery', 'Craiova', 'Str. Unirii', '5', '0741987654', $bakeryType],
            ['Coffee Corner', 'Craiova', 'Str. Traian', '22', '0745112233', $cafeType],
            ['Fresh Market', 'Craiova', 'Bd. Decebal', '101', '0746998877', $groceryType],
            ['La Mama Trattoria', 'Craiova', 'Str. Brestei', '9', '0747556644', $restaurantType],
        ];

        foreach ($businessData as $data) {
            $business = new Business();
            $business->setName($data[0]);
            $business->setCity($data[1]);
            $business->setStreet($data[2]);
            $business->setHouseNumber($data[3]);
            $business->setPhoneNumber($data[4]);
            $business->setBusinessType($data[5]);

            $manager->persist($business);
            $businesses[] = $business;
        }

        [
            $business1,
            $business2,
            $business3,
            $business4,
            $business5
        ] = $businesses;

        // --- Consumers ---
        $consumers = [];

        $consumerData = [
            ['Andrei', 'Popescu', '0722111222'],
            ['Maria', 'Ionescu', '0733444555'],
            ['Ioana', 'Dumitrescu', '0744667788'],
            ['Radu', 'Stanescu', '0755223344'],
            ['Elena', 'Vasilescu', '0766778899'],
            ['Cristian', 'Marin', '0777889900'],
        ];

        foreach ($consumerData as $data) {
            $consumer = new Consumer();
            $consumer->setFirstName($data[0]);
            $consumer->setLastName($data[1]);
            $consumer->setPhoneNumber($data[2]);

            $manager->persist($consumer);
            $consumers[] = $consumer;
        }

        [
            $consumer1,
            $consumer2,
            $consumer3,
            $consumer4,
            $consumer5,
            $consumer6
        ] = $consumers;

        // --- Users ---
        foreach ($consumers as $index => $consumer) {
            $user = new User();
            $user->setEmail('consumer' . ($index + 1) . '@example.com');
            $user->setRoles(['ROLE_CONSUMER']);
            $user->setPassword(
                $this->passwordHasher->hashPassword($user, 'password123')
            );
            $user->setConsumer($consumer);

            $manager->persist($user);
        }

        foreach ($businesses as $index => $business) {
            $user = new User();
            $user->setEmail('business' . ($index + 1) . '@example.com');
            $user->setRoles(['ROLE_BUSINESS']);
            $user->setPassword(
                $this->passwordHasher->hashPassword($user, 'password123')
            );
            $user->setBusiness($business);

            $manager->persist($user);
        }

        $adminUser = new User();
        $adminUser->setEmail('admin@example.com');
        $adminUser->setRoles(['ROLE_ADMIN']);
        $adminUser->setPassword(
            $this->passwordHasher->hashPassword($adminUser, 'password123')
        );
        $manager->persist($adminUser);

        // --- Packages ---
        $packagesData = [
            ['Surprise Lunch Box', 'Leftover lunch dishes at a discounted price.', 19.99, $mealsCategory, $business1],
            ["Chef's Mystery Box", "A mix of today's unsold specials.", 24.00, $mealsCategory, $business1],
            ['Evening Dinner Bag', 'End-of-day dinner portions, chef selection.', 22.50, $mealsCategory, $business1],
            ['Pasta Special Box', 'Unsold pasta dishes from lunch service.', 18.00, $mealsCategory, $business1],

            ['End of Day Pastries', "Assorted pastries from today's batch.", 12.50, $bakedGoodsCategory, $business2],
            ['Bread Basket Deal', 'Unsold artisan bread from the day.', 9.99, $bakedGoodsCategory, $business2],
            ['Sweet Treats Box', 'Mixed cookies, croissants and muffins.', 14.00, $dessertsCategory, $business2],
            ['Cake Slice Bundle', 'Leftover cake slices, various flavors.', 11.00, $dessertsCategory, $business2],

            ['Coffee & Snack Box', 'Pastry and coffee combo, end of day.', 8.50, $bakedGoodsCategory, $business3],
            ['Sandwich Surprise', "Today's unsold sandwiches, chef's pick.", 10.50, $mealsCategory, $business3],
            ['Muffin Mix Bag', 'Assorted muffins from the display case.', 7.50, $dessertsCategory, $business3],
            ['Coffee Beans Bundle', 'Fresh coffee products nearing best before date.', 15.00, $groceriesCategory, $business3],

            ['Veggie Box Deal', 'Assorted vegetables nearing best-by date.', 15.00, $groceriesCategory, $business4],
            ['Fruit Rescue Box', 'Fresh fruit close to expiry, mixed selection.', 13.50, $groceriesCategory, $business4],
            ['Dairy Surprise Bag', 'Dairy products nearing sell-by date.', 16.00, $groceriesCategory, $business4],
            ['Pantry Essentials Box', 'Mixed pantry items close to date.', 20.00, $groceriesCategory, $business4],

            ['Trattoria Family Box', 'Family-size portions from dinner service.', 29.99, $mealsCategory, $business5],
            ['Antipasto Leftover Box', 'Assorted antipasti from today.', 17.50, $mealsCategory, $business5],
            ['Pizza Rescue Box', 'Unsold pizza slices, mixed toppings.', 16.50, $mealsCategory, $business5],
            ['Pasta Family Deal', 'Large pasta portions left from service.', 25.00, $mealsCategory, $business5],
        ];

        $packages = [];

        foreach ($packagesData as $data) {
            $package = new Package();
            $package->setName($data[0]);
            $package->setDescription($data[1]);
            $package->setPrice($data[2]);
            $package->setPhoto(null);
            $package->setCreatedAt(new \DateTimeImmutable());
            $package->setCategory($data[3]);
            $package->setBusiness($data[4]);

            $manager->persist($package);
            $packages[] = $package;
        }

        // --- Orders ---
        // Only order some packages so most remain available.
        shuffle($packages);

        $consumerList = [
            $consumer1,
            $consumer2,
            $consumer3,
            $consumer4,
            $consumer5,
            $consumer6,
        ];

        // 6 sold packages, 14 available
        for ($i = 0; $i < 6; $i++) {
            $order = new Order();
            $order->setCreatedAt(
                new \DateTimeImmutable(sprintf('-%d hours', $i * 5))
            );
            $order->setPackage($packages[$i]);
            $order->setConsumer($consumerList[$i % count($consumerList)]);

            $manager->persist($order);
        }

        $manager->flush();
    }
}
