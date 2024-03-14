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

use App\Entity\Comment;
use App\Enum\Roles;
use App\Helper\ApplicationGlobals;
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
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Filter\ChoiceFilter;
use Exception;
use Psr\Container\ContainerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\Security\Http\Attribute\IsGranted;

/**
 * Class CommentCrudController
 * @package App\Controller\Admin
 */
#[IsGranted('ROLE_ADMIN')]
class CommentCrudController extends AbstractCrudController
{
    use LoggerTrait;

    /**
     * @param LoggerInterface $logger
     * @param ContainerInterface $container
     * @param ApplicationGlobals $appGlobals
     */
    public function __construct(
        LoggerInterface $logger,
        ContainerInterface $container,
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
        return Comment::class;
    }

    /**
     * @param Crud $crud
     *
     * @return Crud
     */
    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('User Comment')
            ->setEntityLabelInPlural('User Comments')
            ->setSearchFields(['author', 'text', 'email'])
            ->setDefaultSort(['createdAt' => 'DESC'])
            ->setPaginatorPageSize(13)
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
            ->add('user')
            ->add('author')
            ->add('email')
            ->add('text')
            ->add(ChoiceFilter::new('state')->setChoices(Comment::getStateChoices()))
            ->add('createdAt');
    }

    /**
     * @param Actions $actions
     *
     * @return Actions
     */
    public function configureActions(Actions $actions): Actions
    {
        return $actions
            ->add(Crud::PAGE_INDEX, Action::DETAIL)
            ->remove(Crud::PAGE_INDEX, Action::NEW);
    }

    /**
     * @psalm-suppress
     * @param string $pageName
     *
     * @return iterable<mixed, FieldInterface>
     */
    public function configureFields(string $pageName): iterable
    {
        if ($this->appGlobals->getType() == ApplicationGlobals::TYPE_APP_WORK) {
            try {
                yield AssociationField::new('user')->setFormTypeOption('disabled', true);
            // @codeCoverageIgnoreStart
            } catch (Exception $ex) {
                $mess = self::class . ' configureFields user ' . $ex->getMessage();
                $this->exception($mess, $ex);
            }
            // @codeCoverageIgnoreEnd
        }
        yield TextField::new('author')->setFormTypeOption('disabled', true);
        yield EmailField::new('email')->setFormTypeOption('disabled', true);
        yield TextareaField::new('text')->setFormTypeOption('disabled', true);
        yield ImageField::new('photoFilename')
            ->setBasePath('/uploads/photos')
            ->setLabel('Photo')
            ->setUploadDir('public/uploads')
            ->setFormTypeOption('disabled', true);
        yield ChoiceField::new('state')->setChoices(
            static fn (?Comment $comment): array => isset($comment) ? $comment->getStateChoices() : Comment::getStateChoices()
        );

        $createdAt = DateField::new('createdAt')->setFormTypeOptions([
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
            if ($entityInstance instanceof Comment) {
                parent::updateEntity($entityManager, $entityInstance);
                $this->addFlash('success', 'Comment edit');
            }
        // @codeCoverageIgnoreStart
        } catch (Exception $ex) {
            $mess = self::class . ' updateEntity ' . $ex->getMessage();
            $this->exception($mess, $ex);
            $this->addFlash('danger', 'Error Comment edit');
        }
        // @codeCoverageIgnoreEnd
    }
}
