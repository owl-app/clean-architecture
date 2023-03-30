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
2. Data providers collection/item with resolving relations and DTO mapping.
3. Bus: Command and Query
4. Serialization
5. Request DTO resolver
6. Validation

## Project details

A simple application with articles, whose aim is to demonstrate the clean architecture in PHP using CQRS. It also includes several useful functionalities that can be used in target production applications.

### Clean Architecture

This structure using also modules and diffrent apps.

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
