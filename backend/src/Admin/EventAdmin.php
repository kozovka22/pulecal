<?php
namespace Pulecal\Service\Admin;
use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Show\ShowMapper;
use Sonata\AdminBundle\Route\RouteCollectionInterface;

class EventAdmin extends AbstractAdmin {
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
            ->add('startTime')
            ->add('endTime')
            ->add('eventType', null, [
                'required' => false,
            ])
            ->add('owner')
            ->add('calendars', null, [
                'by_reference' => false,
                'multiple' => true,
                'expanded' => false,
            ])
            ->add('users', null, [
                'by_reference' => false,
                'multiple' => true,
                'expanded' => false,
            ])
            ->add('repeats')
            ->add('repeatInterval', null, [
                'required' => false,
            ])
            ->add('private');
    }

    protected function configureListFields(ListMapper $listMapper): void {
        $listMapper
            ->addIdentifier('id')
            ->add('name')
            ->add('startTime')
            ->add('endTime')
            ->add('eventType')
            ->add('owner')
            ->add('repeats')
            ->add('private')
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
            ->add('startTime')
            ->add('endTime')
            ->add('eventType')
            ->add('owner')
            ->add('calendars')
            ->add('users')
            ->add('repeats')
            ->add('repeatInterval')
            ->add('private');
    }
}