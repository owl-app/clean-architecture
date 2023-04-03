<?php

declare(strict_types=1);

namespace Owl\Shared\Domain\DataProvider\Builder;

class PaginationBuilder implements PaginationBuilderInterface
{
    private string $paramPerPageName;

    private string $paramPageName;

    private int $defaultPerPage;

    private array $allowedPerPage;

    private bool $hasPagination;

    private bool $fetchJoinCollection;

    private bool $useOutputWalkers;

    public function __construct(private readonly array $defaultParameters, private readonly array $queryParams)
    {
        $this->paramPerPageName = $defaultParameters['param_per_page_name'] ?? 'per-page';
        $this->paramPageName = $defaultParameters['param_page_name'] ?? 'page';
        $this->defaultPerPage = $defaultParameters['default_per_page'] ?? 10;
        $this->allowedPerPage = $defaultParameters['allowed_per_page'] ?? [10, 20, 50, 100];
        $this->fetchJoinCollection = $defaultParameters['fetch_join_collection'] ?? true;
        $this->useOutputWalkers = $defaultParameters['use_output_walkers'] ?? true;

        $this->hasPagination = true;
    }

    public function getName(): string
    {
        return self::NAME;
    }

    public function getParamPerPageName(): string
    {
        return $this->paramPerPageName;
    }

    public function setParamPerPageName(string $paramPerPageName): self
    {
        $this->paramPerPageName = $paramPerPageName;

        return $this;
    }

    public function getParamPageName(): string
    {
        return $this->paramPageName;
    }

    public function setParamPageName(string $paramPageName): self
    {
        $this->paramPageName = $paramPageName;

        return $this;
    }

    public function getDefaultPerPage(): int
    {
        return $this->defaultPerPage;
    }

    public function setDefaultPerPage(int $defaultPerPage): self
    {
        $this->defaultPerPage = $defaultPerPage;

        return $this;
    }

    public function getAllowedPerPage(): array
    {
        return $this->allowedPerPage;
    }

    public function setAllowedPerPage(array $allowedPerPage): self
    {
        $this->allowedPerPage = $allowedPerPage;

        return $this;
    }

    public function hasPagination(): bool
    {
        return $this->hasPagination;
    }

    public function setHasPagination(bool $hasPagination): self
    {
        $this->hasPagination = $hasPagination;

        return $this;
    }

    public function getFetchJoinCollection(): bool
    {
        return $this->fetchJoinCollection;
    }

    public function setFetchJoinCollection(bool $fetchJoinCollection): self
    {
        $this->fetchJoinCollection = $fetchJoinCollection;

        return $this;
    }

    public function getUseOutputWalkers(): bool
    {
        return $this->useOutputWalkers;
    }

    public function setUseOutputWalkers(bool $useOutputWalkers): self
    {
        $this->useOutputWalkers = $useOutputWalkers;

        return $this;
    }

    public function getPage(): int
    {
        if (isset($this->queryParams[$this->paramPageName])) {
            return (int) $this->queryParams[$this->paramPageName];
        }

        return 1;
    }

    public function getPerPage(): int
    {
        if (isset($this->queryParams[$this->paramPerPageName])) {
            return (int) $this->queryParams[$this->paramPerPageName];
        }

        return $this->getDefaultPerPage();
    }

    public function getOffset(): int
    {
        return ($this->getPage() - 1) * $this->getPerPage();
    }
}
