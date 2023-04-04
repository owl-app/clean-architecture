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

## Installation

### Docker

1. Make sure you have installed Docker on your local machine
2. Clone this project: `git clone git@github.com:owl-app/clean-architecture.git`
3. Execute `docker compose up -d` or `make docker-build` in your terminal and wait some time until the services will be ready
4. Then you will have [API app](apps/api) docs available on http://localhost:8080/api/doc in your browser

### Manually
```bash
$ git clone git@github.com:owl-app/clean-architecture.git
$ cd clean-architecture
$ wget http://getcomposer.org/composer.phar
$ php composer.phar install
$ cp .env .env.local // setup DB
$ php apps/api/bin/console doctrine:database:create
$ php apps/api/bin/console doctrine:schema:create
$ symfony serve --dir=apps/api/public --port=8080
```

## Project details

A simple application with articles, whose aim is to demonstrate the clean architecture in PHP using CQRS.
It also includes several useful functionalities that can be used in target production applications.

### Clean Architecture

This structure using also modules and diffrent apps.

#### Api app

```scala
$ tree -L 4 src

apps
|-- api
|    -- src
|       |-- Controller // Presentation layer
|       |   |-- Article // Implements uses cases from article applications
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
|       |   |-- ArticleRepositoryInterface.php
|    -- Infrastructure
|       |-- DataProvider
|       |   |-- ArticleCollectionDataProvider.php // Implementation data provider for list articles
|       |   |-- ArticleItemDataProvider.php // Implementation data provider for single article
|       |-- Persistence
|       |   |-- Doctrine
|       |   |   |-- Article.orm.xml // Doctrine mapping entity article
|       |   |-- ArticleRepository.php
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
|       |   |-- RequestDtoInterface.php // DTO to auto resolve from request
|    -- Domain
|       |-- Bus
|       |-- DataProvider // Logic for collection/item data provider
|       |-- Persistence
|    -- Infrastructure
|       |-- Bus // Implementation for command and query bus
|       |-- DataProvider
|       |-- |-- Orm // Implementation for Doctrine data providers
|       |-- Persistence
|       |   |-- Doctrine // Implementations for Doctrine elements (Repository etc)
|       |-- Symfony // Implementations for various elements of application (e.g. Request DTO resolver)
```
## Examples

### Query Bus data provider with mapper

Example of usage query bus collection data provider with mapper.

```php
<?php

declare(strict_types=1);

namespace Owl\Apps\Api\Controller\Article;

use OpenApi\Attributes as OA;
use Nelmio\ApiDocBundle\Annotation\Model;
use Owl\Article\Application\List\ArtliceListMapper;
use Owl\Article\Domain\Model\Article;
use Owl\Article\Infrastructure\DataProvider\ArticleCollectionDataProvider;
use Owl\Shared\Domain\DataProvider\Request\CollectionRequestParams;
use Owl\Shared\Infrastructure\DataProvider\Orm\Bus\Query\CollectionQuery;
use Owl\Shared\Infrastructure\Symfony\ApiController;
use Symfony\Component\HttpFoundation\JsonResponse;

final class ArticleListController extends ApiController
{
    #[OA\Get(
        summary: "List articles",
    )]
    #[OA\Response(
        response: 200,
        description: 'Successful response',
        content: new OA\JsonContent(
            type: 'array',
            items: new OA\Items(ref: new Model(type: Article::class))
        )
    )]
    #[OA\Parameter(
        name: "filters[search][type]",
        in: "query",
        description: "Type search",
        required: false,
        schema: new OA\Schema(
            enum: ['equal'],
        )
    )]
    #[OA\Parameter(
        name: "filters[search][value]",
        in: "query",
        description: "Value ",
        required: false
    )]
    #[OA\Tag(name: 'Articles', description: 'Articles in system')]
    public function __invoke(CollectionRequestParams $collectionRequestParams): JsonResponse
    {
        $data = $this->query(new CollectionQuery(
            Article::class,
            new ArticleCollectionDataProvider(),
            $collectionRequestParams,
            new ArtliceListMapper()
        ));

        return $this->responseJson($data);
    }
}
```

### Data providers

Example of usage collection data provider.

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
        $queryBuilder->select('partial o.{id,title,description}');
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
        $paginationBuilder
            ->setAllowedPerPage([10,25,50,100])
        ;
    }
}
```
## Resources
- [CodelyTV/php-ddd-example](https://github.com/CodelyTV/php-ddd-example)
- [API Platform](https://api-platform.com)
