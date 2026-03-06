<?php
namespace Pulecal\Service\Admin;
use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Show\ShowMapper;
use Sonata\AdminBundle\Route\RouteCollectionInterface;

class CalendarAdmin extends AbstractAdmin {
    protected function configureRoutes(RouteCollectionInterface $collection): void {
        $collection
            ->add('create')
            ->add('edit')
            ->add('delete');
    }

    protected function configureFormFields(FormMapper $formMapper): void {
        $formMapper
            ->add('name')
            ->add('description', null, [
                'required' => false,
            ])
            ->add('adminDescription', null, [
                'required' => false,
            ])
            ->add('owner')
            ->add('users', null, [
                'by_reference' => false,
                'multiple' => true,
                'expanded' => false,
            ])
            ->add('events', null, [
                'by_reference' => false,
                'multiple' => true,
                'expanded' => false,
            ])
            ->add('private')
            ->add('active')
            ->add('deactivatedAt', null, [
                'required' => false,
            ]);
    }

    protected function configureListFields(ListMapper $listMapper): void {
        $listMapper
            ->addIdentifier('id')
            ->add('name')
            ->add('private')
            ->add('active')
            ->add('deactivatedAt')
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
            ->add('name')
            ->add('description')
            ->add('adminDescription')
            ->add('owner')
            ->add('users')
            ->add('events')
            ->add('private')
            ->add('active')
            ->add('deactivatedAt');
    }
}