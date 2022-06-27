<?php
///*
//namespace Controller;
//
///**
// * Event Controller test.
// */
//
//use App\Entity\Event;
//use App\Entity\Enum\UserRole;
//use App\Entity\User;
//use App\Repository\EventRepository;
//use App\Repository\UserRepository;
//use DateTimeImmutable;
//use Doctrine\ORM\OptimisticLockException;
//use Doctrine\ORM\ORMException;
//use Psr\Container\ContainerExceptionInterface;
//use Psr\Container\NotFoundExceptionInterface;
//use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
//use DateTime;
//
///**
// * Class EventControllerTest.
// */
//class EventControllerTest extends WebTestCase
//{
//    /**
//     * Test route.
//     *
//     * @const string
//     */
//    public const TEST_ROUTE = '/event';
//
//    /**
//     * Create user.
//     *
//     * @param array $roles User roles
//     *
//     * @return User User entity
//     *
//     */
//    protected function createUser(array $roles, string $email): User
//    {
//        $passwordHasher = static::getContainer()->get('security.password_hasher');
//        $user = new User();
//        $user->setEmail($email);
//        $user->setRoles($roles);
//        $user->setPassword(
//            $passwordHasher->hashPassword(
//                $user,
//                'p@55w0rd'
//            )
//        );
//        $userRepository = static::getContainer()->get(UserRepository::class);
//        $userRepository->save($user);
//
//        return $user;
//    }
//
//
//    /**
//     * Set up tests.
//     */
//    public function setUp(): void
//    {
//        $this->httpClient = static::createClient();
//    }
//
//
//
//    /**
//     * @return void
//     */
//    public function testIndexRouteAnonymousUser(): void
//    {
//        // given
//        $expectedStatusCode = 200;
//        $user = null;
//        $user = $this->createUser([ UserRole::ROLE_USER->value], 'event_1@example.com');
//        $this->httpClient->loginUser($user);
//        // when
//        $this->httpClient->request('GET', self::TEST_ROUTE);
//        $resultStatusCode = $this->httpClient->getResponse()->getStatusCode();
//
//        // then
//        $this->assertEquals($expectedStatusCode, $resultStatusCode);
//    }
//
//    /**
//     * Test index route for admin user.
//     *
//     * @throws ContainerExceptionInterface|NotFoundExceptionInterface|ORMException|OptimisticLockException
//     */
//    public function testIndexRouteAdminUser(): void
//    {
//        // given
//        $expectedStatusCode = 200;
//        $user = null;
//        $user = $this->createUser([ UserRole::ROLE_USER->value, UserRole::ROLE_ADMIN->value], 'event_2@example.com');
//        $this->httpClient->loginUser($user);
//
//        // when
//        $this->httpClient->request('GET', self::TEST_ROUTE);
//        $resultStatusCode = $this->httpClient->getResponse()->getStatusCode();
//
//        // then
//        $this->assertEquals($expectedStatusCode, $resultStatusCode);
//    }
//
//    /**
//     * Test index route for non-authorized user.
//     *
//     */
//    public function testIndexRouteNonAuthorizedUser(): void
//    {
//        // given
//        $user = null;
//        $user = $this->createUser([ UserRole::ROLE_USER->value], 'event_3@example.com');
//        $this->httpClient->loginUser($user);
//
//        // when
//        $this->httpClient->request('GET', self::TEST_ROUTE);
//        $resultStatusCode = $this->httpClient->getResponse()->getStatusCode();
//
//        // then
//        $this->assertEquals(200, $resultStatusCode);
//    }
//
//
//    //create event
//    public function testCreateEvent(): void
//    {
//        // given
//        $user = $this->createUser([ UserRole::ROLE_USER->value, UserRole::ROLE_ADMIN->value], 'event_5@example.com');
//
//        $this->httpClient->loginUser($user);
//        $eventEventTitle = "createdEvent";
//        $eventRepository = static::getContainer()->get(EventRepository::class);
//
//        $this->httpClient->request('GET', self::TEST_ROUTE . '/create');
//        // when
//        $this->httpClient->submitForm(
//            'Zapisz',
//            ['event' => [
//                'title' => $eventEventTitle,
//                'category' => '1'
//
//            ]
//            ]
//        );
//
//        // then
//        $savedEvent = $eventRepository->findOneByTitle($eventEventTitle);
//        $this->assertEquals(
//            $eventEventTitle,
//            $savedEvent->getTitle()
//        );
//
//
//        $result = $this->httpClient->getResponse();
//        $this->assertEquals(302, $result->getStatusCode());
//    }
//
//
//
//}*/
