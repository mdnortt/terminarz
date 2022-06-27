<?php

namespace Controller;

/**
 * Contact Controller test.
 */

use App\Entity\Contact;
use App\Entity\Enum\UserRole;
use App\Entity\User;
use App\Repository\ContactRepository;
use App\Repository\UserRepository;
use DateTimeImmutable;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use DateTime;

/**
 * Class ContactControllerTest.
 */
class ContactControllerTest extends WebTestCase
{
    /**
     * Test route.
     *
     * @const string
     */
    public const TEST_ROUTE = '/contact';

    /**
     * Create user.
     *
     * @param array $roles User roles
     *
     * @return User User entity
     *
     */
    protected function createUser(array $roles, string $email): User
    {
        $passwordHasher = static::getContainer()->get('security.password_hasher');
        $user = new User();
        $user->setEmail($email);
        $user->setRoles($roles);
        $user->setPassword(
            $passwordHasher->hashPassword(
                $user,
                'p@55w0rd'
            )
        );
        $userRepository = static::getContainer()->get(UserRepository::class);
        $userRepository->save($user);

        return $user;
    }


    /**
     * Set up tests.
     */
    public function setUp(): void
    {
        $this->httpClient = static::createClient();
    }



    /**
     * @return void
     */
    public function testIndexRouteAnonymousUser(): void
    {
        // given
        $expectedStatusCode = 200;
        $user = null;
        $user = $this->createUser([ UserRole::ROLE_USER->value], 'contact_1@example.com');
        $this->httpClient->loginUser($user);
        // when
        $this->httpClient->request('GET', self::TEST_ROUTE);
        $resultStatusCode = $this->httpClient->getResponse()->getStatusCode();

        // then
        $this->assertEquals($expectedStatusCode, $resultStatusCode);
    }

    /**
     * Test index route for admin user.
     *
     * @throws ContainerExceptionInterface|NotFoundExceptionInterface|ORMException|OptimisticLockException
     */
    public function testIndexRouteAdminUser(): void
    {
        // given
        $expectedStatusCode = 200;
        $user = null;
        $user = $this->createUser([ UserRole::ROLE_USER->value, UserRole::ROLE_ADMIN->value], 'contact_2@example.com');
        $this->httpClient->loginUser($user);

        // when
        $this->httpClient->request('GET', self::TEST_ROUTE);
        $resultStatusCode = $this->httpClient->getResponse()->getStatusCode();

        // then
        $this->assertEquals($expectedStatusCode, $resultStatusCode);
    }

    /**
     * Test index route for non-authorized user.
     *
     */
    public function testIndexRouteNonAuthorizedUser(): void
    {
        // given
        $user = null;
        $user = $this->createUser([ UserRole::ROLE_USER->value], 'contact_3@example.com');
        $this->httpClient->loginUser($user);

        // when
        $this->httpClient->request('GET', self::TEST_ROUTE);
        $resultStatusCode = $this->httpClient->getResponse()->getStatusCode();

        // then
        $this->assertEquals(200, $resultStatusCode);
    }

    //create contact
    public function testCreateContact(): void
    {
        // given
        $user = $this->createUser([ UserRole::ROLE_USER->value, UserRole::ROLE_ADMIN->value], 'contact_5@example.com');

        $this->httpClient->loginUser($user);
        $contactContactTitle = "createdCategor";
        $contactRepository = static::getContainer()->get(ContactRepository::class);

        $this->httpClient->request('GET', self::TEST_ROUTE . '/create');
        // when
        $this->httpClient->submitForm(
            'Zapisz',
            ['contact' => [
                'name' => $contactContactTitle,
                'phone' => 123456789,
                'email' => 'jakikolwiek@exampl.com',
                'adress' => 'krakow'
            ]
            ]
        );

        // then
        $savedContact = $contactRepository->findOneByName($contactContactTitle);
        $this->assertEquals(
            $contactContactTitle,
            $savedContact->getName()
        );


        $result = $this->httpClient->getResponse();
        $this->assertEquals(302, $result->getStatusCode());
    }

    /**
     * @return void
     */
    public function testEditContactUnauthorizedUser(): void
    {
        // given
        $expectedHttpStatusCode = 200;
        $user = $this->createUser([ UserRole::ROLE_USER->value, UserRole::ROLE_ADMIN->value], 'contact_6@example.com');
        $this->httpClient->loginUser($user);
        $contact = new Contact();
        $contact->setName('TestContact');
        $contact->setCreatedAt(new DateTimeImmutable('now'));
        $contact->setUpdatedAt(new DateTimeImmutable('now'));
        $contact->setAdress("new_adres");
        $contact->setEmail($user->getEmail());
        $contact->setPhone(123456789);
        $contactRepository =
            static::getContainer()->get(ContactRepository::class);
        $contactRepository->save($contact);

        // when
        $this->httpClient->request('GET', self::TEST_ROUTE . '/' .
            $contact->getId() . '/edit');
        $actual = $this->httpClient->getResponse();

        // then

        $this->assertEquals(
            $expectedHttpStatusCode,
            $actual->getStatusCode()
        );
    }


    /**
     * @return void
     */
    public function testEditContact(): void
    {
        // given
        $user = $this->createUser([ UserRole::ROLE_USER->value, UserRole::ROLE_ADMIN->value], 'contact_7@example.com');
        $this->httpClient->loginUser($user);

        $contactRepository =
            static::getContainer()->get(ContactRepository::class);
        $testContact = new Contact();
        $testContact->setName('TestContact');
        $testContact->setCreatedAt(new DateTimeImmutable('now'));
        $testContact->setUpdatedAt(new DateTimeImmutable('now'));
        $testContact->setAdress("new_adres");
        $testContact->setEmail($user->getEmail());
        $testContact->setPhone(123456789);
        $contactRepository->save($testContact);
        $testContactId = $testContact->getId();
        $expectedNewContactTitle = 'TestContactEdit';

        $this->httpClient->request('GET', self::TEST_ROUTE . '/' .
            $testContactId . '/edit');

        // when
        $this->httpClient->submitForm(
            'Zapisz',
            ['contact' => [
                    'name' => $expectedNewContactTitle,
                    'phone' => 123456789,
                    'email' => 'jakikolwiek@exampl.com',
                    'adress' => 'krakow'
                ]
            ]
        );

        // then
        $savedContact = $contactRepository->findOneById($testContactId);
        $this->assertEquals(
            $expectedNewContactTitle,
            $savedContact->getName()
        );
    }


    /**
     * @throws OptimisticLockException
     * @throws NotFoundExceptionInterface
     * @throws ORMException
     * @throws ContainerExceptionInterface
     */
    public function testNewRoutAdminUser(): void
    {
        $user = null;
        try {
            $user = $this->createUser([ UserRole::ROLE_USER->value, UserRole::ROLE_ADMIN->value], 'contact_8@example.com');
        } catch (OptimisticLockException|ContainerExceptionInterface|ORMException $e) {
        }
        $this->httpClient->loginUser($user);
        $this->httpClient->request('GET', self::TEST_ROUTE . '/');
        $this->assertEquals(301, $this->httpClient->getResponse()->getStatusCode());
    }

    /**
     * @return void
     */
    public function testDeleteContact(): void
    {
        // given

        $user = $this->createUser([ UserRole::ROLE_USER->value, UserRole::ROLE_ADMIN->value], 'contact_9@example.com');
        $this->httpClient->loginUser($user);

        $contactRepository =
            static::getContainer()->get(ContactRepository::class);
        $testContact = new Contact();
        $testContact->setName('TestContact');
        $testContact->setCreatedAt(new DateTimeImmutable('now'));
        $testContact->setUpdatedAt(new DateTimeImmutable('now'));
        $testContact->setAdress("new_adres");
        $testContact->setEmail($user->getEmail());
        $testContact->setPhone(123456789);
        $contactRepository->save($testContact);
        $testContactId = $testContact->getId();

        $this->httpClient->request('GET', self::TEST_ROUTE . '/' . $testContactId . '/delete');

        //when
        $this->httpClient->submitForm(
            'Usuń'
        );

        // then
        $this->assertNull($contactRepository->findOneByName('TestContactCreated'));
    }

}
