<?php
/**
 * This file is part of the Simple Web Demo Free Lottery Management Application.
 *
 * This project is no longer maintained.
 * The project is written in Symfony Framework Release.
 *
 * @link https://github.com/scorpion3dd
 * @author Denis Puzik <scorpion3dd@gmail.com>
 * @copyright Copyright (c) 2023-2024 scorpion3dd
 */

declare(strict_types=1);

namespace App\Tests\Unit\Entity;

use App\Tests\TestTrait;
use Carbon\Carbon;
use DateTime;
use Doctrine\ORM\Mapping\ClassMetadata;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\HttpKernel\KernelInterface;

/**
 * Class EntityTest - Unit tests for all Entities
 * without connecting to external services, such as databases, message brokers, etc.
 * all calls to any external services are mute
 *
 * @package App\Tests\Unit\Entity
 */
class EntityTest extends KernelTestCase
{
    use TestTrait;

    /** @var KernelInterface $kernelTest */
    protected KernelInterface $kernelTest;

    /** @var ContainerInterface $container */
    protected ContainerInterface $container;

    /** @var array $entitiesClassNames */
    protected array $entitiesClassNames;

    /** @var EntityManagerInterface $entityManager */
    protected EntityManagerInterface $entityManager;

    /** @var array $viewsClassNames */
    public array $viewsClassNames = [
//        'App\Entity\UserRole',
    ];

    /**
     * @return void
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->kernelTest = self::bootKernel();
        $this->container = static::getContainer();
        $this->entityManager = $this->container->get(EntityManagerInterface::class);
    }

    /**
     * @testCase - method testEntities - must be a success
     * Test all entities
     *
     * @return void
     * @throws Exception
     */
    public function testEntities(): void
    {
        $config = $this->entityManager->getConfiguration();
        $this->entitiesClassNames = $config->getMetadataDriverImpl()->getAllClassNames();
        foreach ($this->entitiesClassNames as $className) {
            $this->handleEntity($className);
        }
    }

    /**
     * @param string $className
     */
    protected function handleEntity(string $className): void
    {
        /** @var ClassMetadata $metaData */
        $metaData = $this->entityManager->getClassMetadata($className);
        $entityInfo = $this->getEntityInfo($metaData);
        foreach ($entityInfo as $fieldName => $fieldType) {
            $setter = 'set' . ucfirst($fieldName);
            $getter = 'get' . ucfirst($fieldName);
            $mockValue = $this->getFieldValueMock($fieldType);
            if ($mockValue === null) {
                printf("Wrong field type. Entity: %s, Fieldname: %s \n\r", $className, $fieldName);
            } else {
                $entity = new $className();
                if (($metaData->isIdentifier($fieldName) && ! method_exists($entity, $setter)) ||
                    in_array($className, $this->viewsClassNames)) {
                    $this->assertSame(
                        $entity->{$getter}(),
                        null,
                        sprintf(' Entity: %s, Fieldname: %s', $className, $fieldName)
                    );
                } else {
                    $entity->{$setter}($mockValue);
                    $value = $entity->{$getter}();
                    $this->assertSame(
                        $mockValue,
                        $value,
                        sprintf(' Entity: %s, Fieldname: %s', $className, $fieldName)
                    );
                }
            }
        }
    }

    /**
     * @param ClassMetadata $metaData
     *
     * @return array
     */
    protected function getEntityInfo(ClassMetadata $metaData): array
    {
        $fieldNames = $metaData->getFieldNames();
        $entityInfo = [];
        foreach ($fieldNames as $fieldName) {
            $entityInfo[$fieldName] = $metaData->getTypeOfField($fieldName);
        }

        return $entityInfo;
    }

    /**
     * @param string $fieldType
     *
     * @return bool|DateTime|float|int|string|array
     */
    private function getFieldValueMock(string $fieldType): bool|DateTime|float|int|string|array
    {
        switch ($fieldType) {
            case 'integer':
            case 'bigint':
                $value = 9;
                break;
            case 'smallint':
                $value = 1;
                break;
            case 'float':
                $value = 9.99;
                break;
            case 'text':
            case 'string':
                $value = 'Example text';
                break;
            case 'boolean':
                $value = true;
                break;
            case 'datetime':
                $value = Carbon::now();
                break;
            case 'date':
                $value = Carbon::now()->format('Y-m-d');
                break;
            case 'time':
                $value = Carbon::now()->format('H:i:s');
                break;
            case 'decimal':
                $value = '1.000';
                break;
            case 'json':
                $value = json_decode('["ROLE_ADMIN","ROLE_USER"]');
                break;
            case 'simple_array':
                $value = ["ROLE_ADMIN","ROLE_USER"];
                break;
            default:
                $value = null;
                break;
        }

        return $value;
    }
}
