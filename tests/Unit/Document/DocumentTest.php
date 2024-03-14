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

namespace App\Tests\Unit\Document;

use App\Tests\TestTrait;
use Carbon\Carbon;
use DateTime;
use Doctrine\ODM\MongoDB\DocumentManager;
use Doctrine\ODM\MongoDB\Mapping\ClassMetadata;
use Exception;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\HttpKernel\KernelInterface;

/**
 * Class DocumentTest - Unit tests for all Documents
 * without connecting to external services, such as databases, message brokers, etc.
 * all calls to any external services are mute
 *
 * @package App\Tests\Unit\Document
 */
class DocumentTest extends KernelTestCase
{
    use TestTrait;

    public const LOG_ID = '63d96d04477661e2140f23a1';
    public const USER_ID = 5;

    /** @var KernelInterface $kernelTest */
    protected KernelInterface $kernelTest;

    /** @var ContainerInterface $container */
    protected ContainerInterface $container;

    /** @var array $documentsClassNames */
    protected array $documentsClassNames;

    /** @var DocumentManager $documentManager */
    protected DocumentManager $documentManager;

    /** @var array $viewsClassNames */
    public array $viewsClassNames = [
//        'App\Document\Log',
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
        $this->documentManager = $this->container->get(DocumentManager::class);
    }

    /**
     * @testCase - method testEntities - must be a success
     * Test all documents
     *
     * @return void
     * @throws Exception
     */
    public function testEntities(): void
    {
        $config = $this->documentManager->getConfiguration();
        $this->documentsClassNames = $config->getMetadataDriverImpl()->getAllClassNames();
        foreach ($this->documentsClassNames as $className) {
            $this->handleDocument($className);
        }
    }

    /**
     * @param string $className
     */
    protected function handleDocument(string $className): void
    {
        /** @var ClassMetadata $metaData */
        $metaData = $this->documentManager->getClassMetadata($className);
        $documentInfo = $this->getDocumentInfo($metaData);
        foreach ($documentInfo as $fieldName => $fieldType) {
            $setter = 'set' . ucfirst($fieldName);
            $getter = 'get' . ucfirst($fieldName);
            $mockValue = $this->getFieldValueMock($fieldType);
            if ($mockValue === null) {
                printf("Wrong field type. Document: %s, Fieldname: %s \n\r", $className, $fieldName);
            } else {
                $document = new $className();
                if (($metaData->isIdentifier($fieldName) && ! method_exists($document, $setter)) ||
                    in_array($className, $this->viewsClassNames)) {
                    $this->assertSame(
                        $document->{$getter}(),
                        null,
                        sprintf(' Document: %s, Fieldname: %s', $className, $fieldName)
                    );
                } else {
                    $document->{$setter}($mockValue);
                    $value = $document->{$getter}();
                    $this->assertSame(
                        $mockValue,
                        $value,
                        sprintf(' Document: %s, Fieldname: %s', $className, $fieldName)
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
    protected function getDocumentInfo(ClassMetadata $metaData): array
    {
        $fieldNames = $metaData->getFieldNames();
        $documentInfo = [];
        foreach ($fieldNames as $fieldName) {
            $documentInfo[$fieldName] = $metaData->getTypeOfField($fieldName);
        }

        return $documentInfo;
    }

    /**
     * @param string $fieldType
     *
     * @return bool|DateTime|float|int|string|array
     */
    private function getFieldValueMock(string $fieldType): bool|DateTime|float|int|string|array
    {
        switch ($fieldType) {
            case 'id':
                $value = self::LOG_ID;
                break;
            case 'collection':
                $value = ['currentUserId=' . self::USER_ID];
                break;
            case 'integer':
            case 'int':
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
            case 'date':
                $value = Carbon::now();
                break;
            case 'time':
                $value = Carbon::now()->format('H:i:s');
                break;
            case 'decimal':
                $value = '1.000';
                break;
            default:
                $value = null;
                break;
        }

        return $value;
    }
}
