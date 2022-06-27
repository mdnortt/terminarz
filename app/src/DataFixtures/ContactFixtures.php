<?php
/**
 * Contact fixtures.
 */

namespace App\DataFixtures;

use App\Entity\Contact;
use DateTimeImmutable;

/**
 * Class ContactFixtures.
 *
 * @psalm-suppress MissingConstructor
 */
class ContactFixtures extends AbstractBaseFixtures
{
    /**
     * Load data.
     *
     * @psalm-suppress PossiblyNullReference
     * @psalm-suppress UnusedClosureParam
     */
    public function loadData(): void
    {
        $this->createMany(20, 'contacts', function (int $i) {
            $contact = new Contact();
            $contact->setName($this->faker->unique()->name);
            $contact->setEmail($this->faker->unique()->email);
            $contact->setAdress($this->faker->unique()->address);
            $contact->setPhone($this->faker->numerify('###-###-####'));

            $contact->setCreatedAt(
                DateTimeImmutable::createFromMutable(
                    $this->faker->dateTimeBetween('-100 days', '-1 days')
                )
            );
            $contact->setUpdatedAt(
                DateTimeImmutable::createFromMutable(
                    $this->faker->dateTimeBetween('-100 days', '-1 days')
                )
            );

            return $contact;
        });

        $this->manager->flush();
    }
}
