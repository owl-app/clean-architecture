<?php

declare(strict_types=1);

namespace Owl\Shared\Domain\DataProvider\Builder;

interface PaginationBuilderInterface extends BuilderInterface
{
    public const NAME = 'pagination';

    public function getParamPerPageName(): string;

    public function setParamPerPageName(string $paramPerPageName): self;

    public function getParamPageName(): string;

    public function setParamPageName(string $paramPageName): self;

    public function getDefaultPerPage(): int;

    public function setDefaultPerPage(int $defaultPerPage): self;

    public function getAllowedPerPage(): array;

    public function setAllowedPerPage(array $allowedPerPage): self;

    public function hasPagination(): bool;

    public function setHasPagination(bool $hasPagination): self;

    public function getFetchJoinCollection(): bool;

    public function setFetchJoinCollection(bool $fetchJoinCollection): self;

    public function getUseOutputWalkers(): bool;

    public function setUseOutputWalkers(bool $useOutputWalkers): self;

    public function getPage(): int;

    public function getPerPage(): int;

    public function getOffset(): int;
}
