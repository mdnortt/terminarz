<?php
/**
 * Event service.
 */

namespace App\Service;

use App\Entity\Event;
use App\Repository\EventRepository;
use Knp\Component\Pager\Pagination\PaginationInterface;
use Knp\Component\Pager\PaginatorInterface;

/**
 * Class EventService.
 */
class EventService implements EventServiceInterface
{
    /**
     * Event repository.
     */
    private EventRepository $eventRepository;
    /**
     * Paginator.
     */
    private PaginatorInterface $paginator;

    /**
     * Constructor.
     *
     * @param CategoryServiceInterface $categoryService Category service
     * @param PaginatorInterface       $paginator       Paginator
     * @param EventRepository          $eventRepository Event repository
     */
    public function __construct(CategoryServiceInterface $categoryService, PaginatorInterface $paginator, EventRepository $eventRepository)
    {
        $this->categoryService = $categoryService;
        $this->paginator = $paginator;
        $this->eventRepository = $eventRepository;
    }

    /**
     * Get paginated list.
     *
     * @param int $page Page number
     *
     * @return PaginationInterface<string, mixed> Paginated list
     */
    public function getPaginatedList(int $page): PaginationInterface
    {
        return $this->paginator->paginate(
            $this->eventRepository->queryAll(),
            $page,
            EventRepository::PAGINATOR_ITEMS_PER_PAGE
        );
    }

    /**
     * @param Event $event
     *
     * @return void
     *
     */
    public function save(Event $event): void
    {
        if (null === $event->getId()) {
            $event->setCreatedAt(new \DateTimeImmutable());
        }
        $event->setUpdatedAt(new \DateTimeImmutable());
        $this->eventRepository->save($event);
    }

    /**
     * Delete entity.
     *
     * @param Event $event Event entity
     */
    public function delete(Event $event): void
    {
        $this->eventRepository->delete($event);
    }
}
