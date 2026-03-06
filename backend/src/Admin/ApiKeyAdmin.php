<?php
declare(strict_types=1);

namespace Pulecal\Service\Admin;

use DateTime;
use Pulecal\Service\Entity\ApiKey;
use Pulecal\Service\Entity\User;
use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\ProxyQueryInterface;
use Sonata\AdminBundle\Form\FormMapper;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class ApiKeyAdmin extends AbstractAdmin {


    public function __construct(private TokenStorageInterface $ts)
    {
        
    }
    protected function configureListFields(ListMapper $lm): void {
        $lm->add('name', null, [
            'label' => 'Name'
        ])->add('createdAt', 'datetime', [
            'label' => 'Created at'
        ])->add("key", null, [
            'label' => 'API Key',
            'template' => 'admin/fields/list__field_copy.html.twig', 
        ])->add(ListMapper::NAME_ACTIONS, null, [
            'actions' => [
                'delete' => [],
            ],
        ]);
    }

    protected function configureFormFields(FormMapper $fm): void {
        $fm->add('name', null, [
            'label' => 'Name'
        ])->add('master', HiddenType::class, [
            'data' => true
        ]);
    }

    protected function prePersist(object $object): void
    {
        /** @var ApiKey $object */

        $object->setKey(bin2hex(random_bytes(16)));
        $object->setExpiresAt(new DateTime("+1 year"));

        $token = $this->ts->getToken();
        if ($token && $token->getUser() instanceof User) {
            $object->setUser($token->getUser());
        }
    }

    protected function configureQuery(ProxyQueryInterface $query): ProxyQueryInterface
    {
        /** @var \Sonata\DoctrineORMAdminBundle\Datagrid\ProxyQuery $query */
        $query = parent::configureQuery($query);
        $rootAlias = current($query->getRootAliases());

        $token = $this->ts->getToken();
        $user = $token?->getUser();

        if ($user instanceof User) {
            $query->andWhere($query->expr()->eq($rootAlias . '.user', ':user'))
                  ->setParameter('user', $user);
        }

        return $query;
    }
}