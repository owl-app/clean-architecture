<h1 align="center">
  Clean Architecture & CQRS with Symfony
</h1>
<p align="center">
    <a href="#"><img src="https://img.shields.io/badge/Symfony-6.2-purple.svg?style=flat-square&logo=symfony" alt="Symfony 6.2"/></a>
</p>

<p align="center">
  Example of a simple <strong>Symfony application using Clean Architecture and Command Query Responsibility Segregation
  (CQRS) principles</strong> with some additional functionalities.
</p>

## Features

1. API with Swagger
2. Data providers collection/item with resolving relations, filters and DTO mapping.
3. Bus: Command and Query
4. Request DTO resolver
5. Serialization
6. Validation

## Project details

A simple application with articles, whose aim is to demonstrate the clean architecture in PHP using CQRS. It also includes several useful functionalities that can be used in target production applications.

### Clean Architecture

This structure using also modules and diffrent apps.

#### Api app

```scala
$ tree -L 4 src

apps
|-- api
|    -- src
|       |-- Controller // Presentation layer, implements uses cases from article applications
|       |   |-- Article // CQRS
|       |   |   |-- ArticleGetController.php
|       |   |   |-- ArticleListController.php
|       |   |   |-- ArticlePostController.php
```

#### Article module

```scala
$ tree -L 4 src

src
|-- Article // Article module
|    -- Application // Use cases
|       |-- Create
|       |   |-- Command // CQRS
|       |   |   |-- SendEmailNewArticleHandler.php
|       |   |   |-- SendEmailNewArticle.php
|       |   |-- ArticleCreator.php
|       |   |-- CreateArticleRequest.php
|       |-- Get
|       |-- List
|       |-- CommentCreate
|    -- Domain
|       |-- Model
|       |   |-- Article.php
|       |-- Repository
|       |   |-- ArticleRepositoryInterface.php // Port of article repository
|    -- Infrastructure
|       |-- DataProvider
|       |   |-- ArticleCollectionDataProvider.php // Adapter data provider for list articles
|       |   |-- ArticleItemDataProvider.php // Adapter data provider for single article
|       |-- Persistence
|       |   |-- Doctrine
|       |   |   |-- Article.orm.xml // Doctrine mapping entity article
|       |   |-- ArticleRepository.php // Adapter of article repository
|       |-- Serialize
|       |   |-- Article.yaml // Serializer mapping article
|       |-- Validate
|       |   |-- Article.yaml // Validation mapping article
```

#### Shared module

```scala
$ tree -L 4 src

src
|-- Shared // Elements of application that are shared between various types of modules
|    -- Application
|       |-- Dto
|       |   |-- RequestDtoInterface.php // Port DTO to auto resolve from request
|    -- Domain
|       |-- Bus // Ports for command and query bus
|       |-- DataProvider // Ports and logic for collection/item data provider
|       |-- Persistence // Ports DB
|    -- Infrastructure
|       |-- Bus // Adapter for command and query bus
|       |-- DataProvider
|       |-- |-- Orm // Adapter for Doctrine data providers
|       |-- Persistence
|       |   |-- Doctrine // Adapter for Doctrine elements (Repository etc)
|       |-- Symfony // Adapter for various elements of application (e.g. Request DTO resolver)
```

### Data providers

Example of usage article collection data provider.

## Full Example

```php
<?php

declare(strict_types=1);

namespace Owl\Article\Infrastructure\DataProvider;

use Owl\Article\Application\List\ArticleCollectionDataProviderInterface;
use Owl\Shared\Domain\DataProvider\Builder\FilterBuilderInterface;
use Owl\Shared\Domain\DataProvider\Builder\SortBuilderInterface;
use Owl\Shared\Domain\DataProvider\Builder\PaginationBuilderInterface;
use Owl\Shared\Infrastructure\DataProvider\Orm\Type\AbstractCollectionType;
use Owl\Shared\Infrastructure\DataProvider\Orm\Filter\StringFilter;
use Owl\Shared\Infrastructure\DataProvider\Orm\Type\BuildableQueryBuilderInterface;

final class ArticleCollectionDataProvider extends AbstractCollectionType implements BuildableQueryBuilderInterface, ArticleCollectionDataProviderInterface
{
    public function buildQueryBuilder(QueryBuilder $queryBuilder): void
    {
        $queryBuilder->select('partial o.{id,title, description}');
    }

    public function buildFilters(FilterBuilderInterface $filterBuilder): void
    {
        $filterBuilder
            ->add('search', StringFilter::class, ['title', 'description'])
        ;
    }

    public function buildSort(SortBuilderInterface $sortBuilder): void
    {
        $sortBuilder
            ->setParamName('sort')
            ->setAvailable(['id', 'title'])
        ;
    }

    public function buildPagination(PaginationBuilderInterface $paginationBuilder): void
    {
    }
}
```