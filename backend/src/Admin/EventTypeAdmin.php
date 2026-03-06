<?php
namespace Pulecal\Service\Admin;
use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Show\ShowMapper;
use Sonata\AdminBundle\Route\RouteCollectionInterface;

class EventTypeAdmin extends AbstractAdmin {
    protected function configureRoutes(RouteCollectionInterface $collection): void {
        $collection
            ->add('create')
            ->add('edit')
            ->add('delete');
    }

    protected function configureFormFields(FormMapper $formMapper): void {
        $formMapper
            ->add('name')
            ->add('private')
            ->add('whitelist')
            ->add('blacklist')
            ->add('timeslot');
    }

    protected function configureListFields(ListMapper $listMapper): void {
        $listMapper
            ->addIdentifier('id')
            ->add('name')
            ->add('private')
            ->add('whitelist')
            ->add('blacklist')
            ->add('timeslot')
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
            ->add('private')
            ->add('whitelist')
            ->add('blacklist')
            ->add('timeslot')
            ->add('events');
    }
}