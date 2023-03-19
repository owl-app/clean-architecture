<?php

declare(strict_types=1);

namespace Owl\Article\Domain\Model;

use Owl\Shared\Domain\Aggregate\AggregateRoot;

final class Article extends AggregateRoot
{
    private string $id;

    public function __construct(
        private string $title,
        private string $description
    ) {
    }

    public static function create(string $title, string $description): self
    {
        $course = new self($title, $description);

        // $course->record(new CourseCreatedDomainEvent($id->value(), $name->value(), $duration->value()));

        return $course;
    }

    public function toPrimitives(): array
    {
        return [
            'id'       => $this->id,
            'title'     => $this->title,
            'description' => $this->description,
        ];
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function getDescription(): string
    {
        return $this->description;
    }
}
