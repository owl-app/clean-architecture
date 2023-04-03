<?php

declare(strict_types=1);

namespace Owl\Tests\Fixtures\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity]
class Dummy
{
    /** @var int|null The id */
    #[ORM\Column(type: 'integer', nullable: true)]
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'AUTO')]
    private $id;

    /** @var string The dummy name */
    #[ORM\Column]
    #[Assert\NotBlank]
    private string $name;

    /** @var string|null The dummy name alias */
    #[ORM\Column(nullable: true)]
    private $alias;

    /** @var array foo */
    private ?array $foo = null;

    /** @var string|null A short description of the item */
    #[ORM\Column(nullable: true)]
    public $description;

    /** @var string|null A dummy */
    #[ORM\Column(nullable: true)]
    public $dummy;

    /** @var bool|null A dummy boolean */
    #[ORM\Column(type: 'boolean', nullable: true)]
    public ?bool $dummyBoolean = null;

    /** @var \DateTime|null A dummy date */
    #[ORM\Column(type: 'datetime', nullable: true)]
    public $dummyDate;

    /** @var float|null A dummy float */
    #[ORM\Column(type: 'float', nullable: true)]
    public $dummyFloat;

    /** @var string|null A dummy price */
    #[ORM\Column(type: 'decimal', precision: 10, scale: 2, nullable: true)]
    public $dummyPrice;

    /** @var array|null serialize data */
    #[ORM\Column(type: 'json', nullable: true)]
    public $jsonData = [];

    /** @var array|null */
    #[ORM\Column(type: 'simple_array', nullable: true)]
    public $arrayData = [];

    /** @var string|null */
    #[ORM\Column(nullable: true)]
    public $nameConverted;

    public static function staticMethod(): void
    {
    }

    public function getId()
    {
        return $this->id;
    }

    public function setId($id): void
    {
        $this->id = $id;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setAlias($alias): void
    {
        $this->alias = $alias;
    }

    public function getAlias()
    {
        return $this->alias;
    }

    public function setDescription($description): void
    {
        $this->description = $description;
    }

    public function getDescription()
    {
        return $this->description;
    }

    public function fooBar($baz): void
    {
    }

    public function getFoo(): ?array
    {
        return $this->foo;
    }

    public function setFoo(array $foo = null): void
    {
        $this->foo = $foo;
    }

    public function setDummyDate(\DateTime $dummyDate = null): void
    {
        $this->dummyDate = $dummyDate;
    }

    public function getDummyDate()
    {
        return $this->dummyDate;
    }

    public function setDummyPrice($dummyPrice)
    {
        $this->dummyPrice = $dummyPrice;

        return $this;
    }

    public function getDummyPrice()
    {
        return $this->dummyPrice;
    }

    public function setJsonData($jsonData): void
    {
        $this->jsonData = $jsonData;
    }

    public function getJsonData()
    {
        return $this->jsonData;
    }

    public function setArrayData($arrayData): void
    {
        $this->arrayData = $arrayData;
    }

    public function getArrayData()
    {
        return $this->arrayData;
    }

    public function isDummyBoolean(): ?bool
    {
        return $this->dummyBoolean;
    }

    /**
     * @param bool $dummyBoolean
     */
    public function setDummyBoolean($dummyBoolean): void
    {
        $this->dummyBoolean = $dummyBoolean;
    }

    public function setDummy($dummy = null): void
    {
        $this->dummy = $dummy;
    }

    public function getDummy()
    {
        return $this->dummy;
    }
}
