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

use App\Entity\User;
use App\Enum\Roles;
use App\Form\CommentFormType;
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
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\CollectionField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateField;
use EasyCorp\Bundle\EasyAdminBundle\Field\EmailField;
use EasyCorp\Bundle\EasyAdminBundle\Field\FormField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Filter\ChoiceFilter;
use EasyCorp\Bundle\EasyAdminBundle\Filter\EntityFilter;
use Exception;
use Psr\Container\ContainerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\Security\Http\Attribute\IsGranted;

/**
 * Class UserCrudController
 * @package App\Controller\Admin
 */
#[IsGranted('ROLE_ADMIN')]
class UserCrudController extends AbstractCrudController
{
    use LoggerTrait;

    /**
     * @param LoggerInterface $logger
     * @param ContainerInterface $container
     * @param RolePermissionServiceInterface $rolePermission
     * @param ApplicationGlobals $appGlobals
     */
    public function __construct(
        LoggerInterface $logger,
        ContainerInterface $container,
        private readonly RolePermissionServiceInterface $rolePermission,
        private readonly ApplicationGlobals $appGlobals
    ) {
        $this->logger = $logger;
        $this->container = $container;
        $this->debugConstruct(self::class);
    }

    /**
     * @return string
     */
    public static function getEntityFqcn(): string
    {
        return User::class;
    }

    /**
     * @param Crud $crud
     *
     * @return Crud
     */
    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('User')
            ->setEntityLabelInPlural('Users')
            ->setSearchFields(['uid', 'username', 'fullName', 'gender', 'email', 'description',
                'status', 'access', 'dateBirthday', 'createdAt', 'updatedAt',
                'rolePermissions.role.name', 'rolePermissions.permission.name'])
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
            ->add('uid')
            ->add('username')
            ->add(ChoiceFilter::new('gender')->setChoices(User::getGenderChoices()))
            ->add('fullName')
            ->add('email')
            ->add('description')
            ->add(ChoiceFilter::new('status')->setChoices(User::getStatusChoices()))
            ->add(ChoiceFilter::new('access')->setChoices(User::getAccessChoices()))
            ->add('dateBirthday')
            ->add('createdAt')
            ->add('updatedAt')
            ->add(EntityFilter::new('rolePermissions'))
            ->add(EntityFilter::new('comments'));
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
        yield FormField::addFieldset('User details');
        yield IdField::new('id')->hideWhenCreating()->setFormTypeOption('disabled', true);
        yield TextField::new('uid')->hideOnIndex();
        yield TextField::new('username');
        yield TextField::new('fullName');
        yield ChoiceField::new('gender')->setChoices(
            static fn (?User $user): array => isset($user) ? $user->getGenderChoices() : User::getGenderChoices()
        );
        yield EmailField::new('email');
        yield TextEditorField::new('description')->hideOnIndex();
        yield ChoiceField::new('status')->setChoices(
            static fn (?User $user): array => isset($user) ? $user->getStatusChoices() : User::getStatusChoices()
        );
        yield ChoiceField::new('access')->setChoices(
            static fn (?User $user): array => isset($user) ? $user->getAccessChoices() : User::getAccessChoices()
        );


        try {
            yield ChoiceField::new('rolesId')->setLabel('Roles Application')
                ->setChoices(
                    static fn (?User $user): array => isset($user) ? $user->getRolesChoices() : User::getRolesChoicesStatic()
                )->hideOnIndex()->setFormTypeOption('disabled', true);
        // @codeCoverageIgnoreStart
        } catch (Exception $ex) {
            $mess = self::class . ' configureFields rolesId ' . $ex->getMessage();
            $this->exception($mess, $ex);
        }
        // @codeCoverageIgnoreEnd


        try {
            if ($this->appGlobals->getType() == ApplicationGlobals::TYPE_APP_WORK) {
                if (Crud::PAGE_EDIT === $pageName || Crud::PAGE_NEW === $pageName) {
                    yield AssociationField::new('rolePermissions');
                } else {
                    yield CollectionField::new('rolePermissions')->setColumns(8);
                }
            }
        // @codeCoverageIgnoreStart
        } catch (Exception $ex) {
            $mess = self::class . ' configureFields rolePermissions ' . $ex->getMessage();
            $this->exception($mess, $ex);
        }
        // @codeCoverageIgnoreEnd


        yield DateField::new('dateBirthday')->hideOnIndex();

        $createdAt = DateField::new('createdAt')->hideWhenCreating()
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

        yield DateField::new('updatedAt')->hideOnIndex()->hideWhenCreating()->hideWhenUpdating();


        yield FormField::addFieldset('Comments details');
        try {
            yield AssociationField::new('comments')->hideWhenCreating();
            yield CollectionField::new('comments')->hideOnIndex()->hideWhenCreating()
                ->allowAdd(false)->allowDelete(false)
                ->renderExpanded()->setEntryType(CommentFormType::class);
        // @codeCoverageIgnoreStart
        } catch (Exception $ex) {
            $mess = self::class . ' configureFields comments ' . $ex->getMessage();
            $this->exception($mess, $ex);
        }
        // @codeCoverageIgnoreEnd
    }

    /**
     * @param EntityManagerInterface $entityManager
     * @param object $entityInstance
     */
    public function updateEntity(EntityManagerInterface $entityManager, $entityInstance): void
    {
        $this->debugFunction(self::class, 'updateEntity');
        if ($entityInstance instanceof User) {
            try {
                parent::updateEntity($entityManager, $entityInstance);
                $this->addFlash('success', 'User edit');
            // @codeCoverageIgnoreStart
            } catch (Exception $ex) {
                $mess = self::class . ' updateEntity ' . $ex->getMessage();
                $this->exception($mess, $ex);
                $this->addFlash('danger', 'Error User edit');
            }
            // @codeCoverageIgnoreEnd
        }
    }

    /**
     * @param EntityManagerInterface $entityManager
     * @param object $entityInstance
     */
    public function persistEntity(EntityManagerInterface $entityManager, $entityInstance): void
    {
        $this->debugFunction(self::class, 'persistEntity');
        if ($entityInstance instanceof User) {
            try {
                parent::persistEntity($entityManager, $entityInstance);
                // @codeCoverageIgnoreStart
                $entityManager = $this->rolePermission->persistRolePermissionByUser($entityManager, $entityInstance);
                parent::persistEntity($entityManager, $entityInstance);
                $this->addFlash('success', 'User add');
                // @codeCoverageIgnoreEnd
            } catch (Exception $ex) {
                $mess = self::class . ' persistEntity ' . $ex->getMessage();
                $this->exception($mess, $ex);
                $this->addFlash('danger', 'Error User add');
            }
        }
    }
}
