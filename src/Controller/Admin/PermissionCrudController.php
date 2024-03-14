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

namespace App\Controller\Admin;

use App\Entity\Permission;
use App\Enum\Roles;
use App\Helper\ApplicationGlobals;
use App\Service\RolePermissionServiceInterface;
use App\Util\LoggerTrait;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;
use EasyCorp\Bundle\EasyAdminBundle\Contracts\Field\FieldInterface;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\CollectionField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Filter\EntityFilter;
use Exception;
use Psr\Container\ContainerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Security\Http\Attribute\IsGranted;

/**
 * Class PermissionCrudController
 * @package App\Controller\Admin
 */
#[IsGranted('ROLE_ADMIN')]
class PermissionCrudController extends AbstractCrudController
{
    use LoggerTrait;

    /** @var Request|null $request */
    protected ?Request $request;

    /**
     * @param LoggerInterface $logger
     * @param ContainerInterface $container
     * @param RequestStack $requestStack
     * @param RolePermissionServiceInterface $rolePermission
     * @param ApplicationGlobals $appGlobals
     */
    public function __construct(
        LoggerInterface $logger,
        ContainerInterface $container,
        private readonly RequestStack $requestStack,
        private readonly RolePermissionServiceInterface $rolePermission,
        private readonly ApplicationGlobals $appGlobals
    ) {
        $this->logger = $logger;
        $this->container = $container;
        $this->debugConstruct(self::class);
        $this->request = $this->requestStack->getCurrentRequest();
    }

    /**
     * @return string
     */
    public static function getEntityFqcn(): string
    {
        return Permission::class;
    }

    /**
     * @param Crud $crud
     *
     * @return Crud
     */
    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('Permission')
            ->setEntityLabelInPlural('Permissions')
            ->setSearchFields(['id', 'name', 'description', 'dateCreated', 'roles.name'])
            ->setDefaultSort(['id' => 'DESC'])
            ->setPaginatorPageSize(14)
            ->setEntityPermission(Roles::ROLE_ADMIN);
    }

    /**
     * @param Filters $filters
     *
     * @return Filters
     */
    public function configureFilters(Filters $filters): Filters
    {
        return $filters
            ->add('id')
            ->add('name')
            ->add('description')
            ->add('dateCreated')
            ->add(EntityFilter::new('roles'));
    }

    /**
     * @param Actions $actions
     *
     * @return Actions
     */
    public function configureActions(Actions $actions): Actions
    {
        return $actions->add(Crud::PAGE_INDEX, Action::DETAIL);
    }

    /**
     * @param string $pageName
     *
     * @return iterable<mixed, FieldInterface>
     */
    public function configureFields(string $pageName): iterable
    {
        $id = IdField::new('id')->hideWhenCreating();
        if (Crud::PAGE_EDIT === $pageName) {
            yield $id->setFormTypeOption('disabled', true);
        } else {
            yield $id;
        }

        yield TextField::new('name');
        yield TextField::new('description');

        if ($this->appGlobals->getType() == ApplicationGlobals::TYPE_APP_WORK) {
            try {
                if (Crud::PAGE_EDIT === $pageName || Crud::PAGE_NEW === $pageName) {
                    yield AssociationField::new('roles');
                } else {
                    yield CollectionField::new('roles');
                }
            // @codeCoverageIgnoreStart
            } catch (Exception $ex) {
                $mess = self::class . ' configureFields roles ' . $ex->getMessage();
                $this->exception($mess, $ex);
            }
            // @codeCoverageIgnoreEnd
        }

        $createdAt = DateField::new('dateCreated')->hideWhenCreating()
            ->setFormTypeOptions([
                'html5' => true,
                'years' => range(date('Y'), date('Y') + 5),
                'widget' => 'single_text',
            ]);
        if (Crud::PAGE_EDIT === $pageName) {
            yield $createdAt->setFormTypeOption('disabled', true);
        } else {
            yield $createdAt;
        }
    }

    /**
     * @param EntityManagerInterface $entityManager
     * @param object $entityInstance
     */
    public function updateEntity(EntityManagerInterface $entityManager, $entityInstance): void
    {
        try {
            $this->debugFunction(self::class, 'updateEntity');
            if ($entityInstance instanceof Permission) {
                $entityManager = $this->rolePermission->persistRolePermissionByPermission(
                    $entityManager,
                    $entityInstance,
                    'update'
                );
                // @codeCoverageIgnoreStart
                parent::updateEntity($entityManager, $entityInstance);
                $this->addFlash('success', 'Permission edit');
            }
        } catch (Exception $ex) {
            $mess = self::class . ' updateEntity ' . $ex->getMessage();
            $this->exception($mess, $ex);
            $this->addFlash('danger', 'Error Permission edit');
        }
        // @codeCoverageIgnoreEnd
    }

    /**
     * @param EntityManagerInterface $entityManager
     * @param object $entityInstance
     */
    public function persistEntity(EntityManagerInterface $entityManager, $entityInstance): void
    {
        try {
            $this->debugFunction(self::class, 'persistEntity');
            if ($entityInstance instanceof Permission) {
                parent::persistEntity($entityManager, $entityInstance);
                $entityManager = $this->rolePermission->persistRolePermissionByPermission(
                    $entityManager,
                    $entityInstance
                );
                parent::persistEntity($entityManager, $entityInstance);
                $this->addFlash('success', 'Permission add');
            }
        // @codeCoverageIgnoreStart
        } catch (Exception $ex) {
            $mess = self::class . ' persistEntity ' . $ex->getMessage();
            $this->exception($mess, $ex);
            $this->addFlash('danger', 'Error Permission add');
        }
        // @codeCoverageIgnoreEnd
    }
}
