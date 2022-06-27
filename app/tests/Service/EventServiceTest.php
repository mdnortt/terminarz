<?php
/**
 * Event service tests.
 */

Namespace Service;

use App\Entity\Event;
use App\Service\EventService;
use App\Service\EventServiceInterface;
use DateTime;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\ORMException;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use DateTimeImmutable;
/**
 * Class EventServiceTest.
 */
class EventServiceTest extends KernelTestCase
{
    /**
     * Event repository.
     */
    private ?EntityManagerInterface $entityManager;

    /**
     * Event service.
     */
    private ?EventServiceInterface $eventService;

    /**
     * Set up test.
     *
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function setUp(): void
    {
        $container = static::getContainer();
        $this->entityManager = $container->get('doctrine.orm.entity_manager');
        $this->eventService = $container->get(EventService::class);
    }

    /**
     * Test save.
     *
     * @throws ORMException
     */
    public function testSave(): void
    {
        // given
        $expectedEvent = new Event();
        $expectedEvent->setTitle('Test Event');

        // when
        $this->eventService->save($expectedEvent);

        // then
        $expectedEventId = $expectedEvent->getId();
        $resultEvent = $this->entityManager->createQueryBuilder()
            ->select('event')
            ->from(Event::class, 'event')
            ->where('event.id = :id')
            ->setParameter(':id', $expectedEventId, Types::INTEGER)
            ->getQuery()
            ->getSingleResult();

        $this->assertEquals($expectedEvent, $resultEvent);
    }

    /**
     * Test delete.
     *
     * @throws ORMException
     */
    public function testDelete(): void
    {
        // given
        $eventToDelete = new Event();
        $eventToDelete->setTitle('Test Event');
        $eventToDelete->setCreatedAt(new DateTimeImmutable('now'));
        $eventToDelete->setUpdatedAt(new DateTimeImmutable('now'));
        $this->entityManager->persist($eventToDelete);
        $this->entityManager->flush();
        $deletedEventId = $eventToDelete->getId();

        // when
        $this->eventService->delete($eventToDelete);

        // then
        $resultEvent = $this->entityManager->createQueryBuilder()
            ->select('event')
            ->from(Event::class, 'event')
            ->where('event.id = :id')
            ->setParameter(':id', $deletedEventId, Types::INTEGER)
            ->getQuery()
            ->getOneOrNullResult();

        $this->assertNull($resultEvent);
    }

    /**
     * Test pagination empty list.
     */
    public function testGetPaginatedList(): void
    {
        // given
        $page = 1;
        $dataSetSize = 15;
        $expectedResultSize = 0;

        $counter = 0;
        while ($counter < $dataSetSize) {
            $event = new Event();
            $event->setTitle('Test event #' . $counter);
            $event->setCreatedAt(new DateTimeImmutable('now'));
            $event->setUpdatedAt(new DateTimeImmutable('now'));
            $this->eventService->save($event);

            ++$counter;
        }

        // when
        $result = $this->eventService->getPaginatedList($page);

        // then
        $this->assertEquals($expectedResultSize, $result->count());
    }


}