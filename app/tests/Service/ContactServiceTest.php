<?php
/**
 * Contact service tests.
 */

namespace Service;

use App\Entity\Contact;
use App\Service\ContactService;
use App\Service\ContactServiceInterface;
use DateTime;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\ORMException;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use DateTimeImmutable;
/**
 * Class ContactServiceTest.
 */
class ContactServiceTest extends KernelTestCase
{
    /**
     * Contact repository.
     */
    private ?EntityManagerInterface $entityManager;

    /**
     * Contact service.
     */
    private ?ContactServiceInterface $contactService;

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
        $this->contactService = $container->get(ContactService::class);
    }

    /**
     * Test save.
     *
     * @throws ORMException
     */
    public function testSave(): void
    {
        // given
        $expectedContact = new Contact();
        $expectedContact->setName('Test Contact');

        // when
        $this->contactService->save($expectedContact);

        // then
        $expectedContactId = $expectedContact->getId();
        $resultContact = $this->entityManager->createQueryBuilder()
            ->select('contact')
            ->from(Contact::class, 'contact')
            ->where('contact.id = :id')
            ->setParameter(':id', $expectedContactId, Types::INTEGER)
            ->getQuery()
            ->getSingleResult();

        $this->assertEquals($expectedContact, $resultContact);
    }

    /**
     * Test delete.
     *
     * @throws ORMException
     */
    public function testDelete(): void
    {
        // given
        $contactToDelete = new Contact();
        $contactToDelete->setName('Test Contact');
        $contactToDelete->setCreatedAt(new DateTimeImmutable('now'));
        $contactToDelete->setUpdatedAt(new DateTimeImmutable('now'));
        $this->entityManager->persist($contactToDelete);
        $this->entityManager->flush();
        $deletedContactId = $contactToDelete->getId();

        // when
        $this->contactService->delete($contactToDelete);

        // then
        $resultContact = $this->entityManager->createQueryBuilder()
            ->select('contact')
            ->from(Contact::class, 'contact')
            ->where('contact.id = :id')
            ->setParameter(':id', $deletedContactId, Types::INTEGER)
            ->getQuery()
            ->getOneOrNullResult();

        $this->assertNull($resultContact);
    }

    /**
     * Test pagination empty list.
     */
    public function testGetPaginatedList(): void
    {
        // given
        $page = 1;
        $dataSetSize = 15;
        $expectedResultSize = 3;

        $counter = 0;
        while ($counter < $dataSetSize) {
            $contact = new Contact();
            $contact->setName('Test Contact #' . $counter);
            $contact->setCreatedAt(new DateTimeImmutable('now'));
            $contact->setUpdatedAt(new DateTimeImmutable('now'));
            $this->contactService->save($contact);

            ++$counter;
        }

        // when
        $result = $this->contactService->getPaginatedList($page);

        // then
        $this->assertEquals($expectedResultSize, $result->count());
    }


}