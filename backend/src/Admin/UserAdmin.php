<?php
namespace Pulecal\Service\Admin;
use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Show\ShowMapper;
use Sonata\AdminBundle\Route\RouteCollectionInterface;

class UserAdmin extends AbstractAdmin {
    protected function configureRoutes(RouteCollectionInterface $collection): void {
        $collection
            ->add('create')
            ->add('edit')
            ->add('delete');
    }

    protected function configureFormFields(FormMapper $formMapper): void {
        $formMapper
            ->add('username')
            ->add('email')
            ->add('calendars', null, [
                'by_reference' => false,
                'multiple' => true,
                'expanded' => false,
            ])
            ->add('events', null, [
                'by_reference' => false,
                'multiple' => true,
                'expanded' => false,
            ])
            ->add('ownedCalendars', null, [
                'by_reference' => false,
                'multiple' => true,
                'expanded' => false,
            ]);
    }

    protected function configureListFields(ListMapper $listMapper): void {
        $listMapper
            ->addIdentifier('id')
            ->add('username')
            ->add('email')
            ->add('_actions', null, [
                'actions' => [
                    'show' => [],
                    'edit' => [],
                    'delete' => [],
                ],
            ]);
    }

    protected function configureShowFields(ShowMapper $showMapper): void {
        $showMapper
            ->add('id')
            ->add('username')
            ->add('email')
            ->add('calendars')
            ->add('events')
            ->add('ownedCalendars');
    }
}
