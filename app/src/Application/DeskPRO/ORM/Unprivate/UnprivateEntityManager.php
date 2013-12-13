<?php
/* This file has been auto-generated. See build-vendors-mutate.php */
namespace Application\DeskPRO\ORM\Unprivate;
use Doctrine\ORM\Configuration, Doctrine\ORM\ORMException, Doctrine\ORM\UnitOfWork, Doctrine\ORM\Query, Doctrine\ORM\Internal, Doctrine\ORM\NativeQuery, Doctrine\ORM\QueryBuilder;
use Closure, Exception,
    Doctrine\Common\EventManager,
    Doctrine\Common\Persistence\ObjectManager,
    Doctrine\DBAL\Connection,
    Doctrine\DBAL\LockMode,
    Doctrine\ORM\Mapping\ClassMetadata,
    Doctrine\ORM\Mapping\ClassMetadataFactory,
    Doctrine\ORM\Query\ResultSetMapping,
    Doctrine\ORM\Proxy\ProxyFactory,
    Doctrine\ORM\Query\FilterCollection;
class UnprivateEntityManager extends \Doctrine\ORM\EntityManager
{
    protected $config;
    protected $conn;
    protected $metadataFactory;
    protected $repositories = array();
    protected $unitOfWork;
    protected $eventManager;
    protected $hydrators = array();
    protected $proxyFactory;
    protected $expressionBuilder;
    protected $closed = false;
    protected $filterCollection;
    protected function __construct(Connection $conn, Configuration $config, EventManager $eventManager)
    {
        $this->conn = $conn;
        $this->config = $config;
        $this->eventManager = $eventManager;
        $metadataFactoryClassName = $config->getClassMetadataFactoryName();
        $this->metadataFactory = new $metadataFactoryClassName;
        $this->metadataFactory->setEntityManager($this);
        $this->metadataFactory->setCacheDriver($this->config->getMetadataCacheImpl());
        $this->unitOfWork = new UnitOfWork($this);
        $this->proxyFactory = new ProxyFactory(
            $this,
            $config->getProxyDir(),
            $config->getProxyNamespace(),
            $config->getAutoGenerateProxyClasses()
        );
    }
    public function getConnection()
    {
        return $this->conn;
    }
    public function getMetadataFactory()
    {
        return $this->metadataFactory;
    }
    public function getExpressionBuilder()
    {
        if ($this->expressionBuilder === null) {
            $this->expressionBuilder = new Query\Expr;
        }
        return $this->expressionBuilder;
    }
    public function beginTransaction()
    {
        $this->conn->beginTransaction();
    }
    public function transactional(Closure $func)
    {
        $this->conn->beginTransaction();
        try {
            $return = $func($this);
            $this->flush();
            $this->conn->commit();
            return $return ?: true;
        } catch (Exception $e) {
            $this->close();
            $this->conn->rollback();
            throw $e;
        }
    }
    public function commit()
    {
        $this->conn->commit();
    }
    public function rollback()
    {
        $this->conn->rollback();
    }
    public function getClassMetadata($className)
    {
        return $this->metadataFactory->getMetadataFor($className);
    }
    public function createQuery($dql = "")
    {
        $query = new Query($this);
        if ( ! empty($dql)) {
            $query->setDql($dql);
        }
        return $query;
    }
    public function createNamedQuery($name)
    {
        return $this->createQuery($this->config->getNamedQuery($name));
    }
    public function createNativeQuery($sql, ResultSetMapping $rsm)
    {
        $query = new NativeQuery($this);
        $query->setSql($sql);
        $query->setResultSetMapping($rsm);
        return $query;
    }
    public function createNamedNativeQuery($name)
    {
        list($sql, $rsm) = $this->config->getNamedNativeQuery($name);
        return $this->createNativeQuery($sql, $rsm);
    }
    public function createQueryBuilder()
    {
        return new QueryBuilder($this);
    }
    public function flush($entity = null)
    {
        $this->errorIfClosed();
        $this->unitOfWork->commit($entity);
    }
    public function find($entityName, $identifier, $lockMode = LockMode::NONE, $lockVersion = null)
    {
        return $this->getRepository($entityName)->find($identifier, $lockMode, $lockVersion);
    }
    public function getReference($entityName, $id)
    {
        $class = $this->metadataFactory->getMetadataFor(ltrim($entityName, '\\'));
        if ( ! is_array($id)) {
            $id = array($class->identifier[0] => $id);
        }
        $sortedId = array();
        foreach ($class->identifier as $identifier) {
            if (!isset($id[$identifier])) {
                throw ORMException::missingIdentifierField($class->name, $identifier);
            }
            $sortedId[$identifier] = $id[$identifier];
        }
                if ($entity = $this->unitOfWork->tryGetById($sortedId, $class->rootEntityName)) {
            return ($entity instanceof $class->name) ? $entity : null;
        }
        if ($class->subClasses) {
            return $this->find($entityName, $sortedId);
        }
        if ( ! is_array($sortedId)) {
            $sortedId = array($class->identifier[0] => $sortedId);
        }
        $entity = $this->proxyFactory->getProxy($class->name, $sortedId);
        $this->unitOfWork->registerManaged($entity, $sortedId, array());
        return $entity;
    }
    public function getPartialReference($entityName, $identifier)
    {
        $class = $this->metadataFactory->getMetadataFor(ltrim($entityName, '\\'));
                if ($entity = $this->unitOfWork->tryGetById($identifier, $class->rootEntityName)) {
            return ($entity instanceof $class->name) ? $entity : null;
        }
        if ( ! is_array($identifier)) {
            $identifier = array($class->identifier[0] => $identifier);
        }
        $entity = $class->newInstance();
        $class->setIdentifierValues($entity, $identifier);
        $this->unitOfWork->registerManaged($entity, $identifier, array());
        $this->unitOfWork->markReadOnly($entity);
        return $entity;
    }
    public function clear($entityName = null)
    {
        $this->unitOfWork->clear($entityName);
    }
    public function close()
    {
        $this->clear();
        $this->closed = true;
    }
    public function persist($entity)
    {
        if ( ! is_object($entity)) {
            throw new \InvalidArgumentException(gettype($entity));
        }
        $this->errorIfClosed();
        $this->unitOfWork->persist($entity);
    }
    public function remove($entity)
    {
        if ( ! is_object($entity)) {
            throw new \InvalidArgumentException(gettype($entity));
        }
        $this->errorIfClosed();
        $this->unitOfWork->remove($entity);
    }
    public function refresh($entity)
    {
        if ( ! is_object($entity)) {
            throw new \InvalidArgumentException(gettype($entity));
        }
        $this->errorIfClosed();
        $this->unitOfWork->refresh($entity);
    }
    public function detach($entity)
    {
        if ( ! is_object($entity)) {
            throw new \InvalidArgumentException(gettype($entity));
        }
        $this->unitOfWork->detach($entity);
    }
    public function merge($entity)
    {
        if ( ! is_object($entity)) {
            throw new \InvalidArgumentException(gettype($entity));
        }
        $this->errorIfClosed();
        return $this->unitOfWork->merge($entity);
    }
    public function copy($entity, $deep = false)
    {
        throw new \BadMethodCallException("Not implemented.");
    }
    public function lock($entity, $lockMode, $lockVersion = null)
    {
        $this->unitOfWork->lock($entity, $lockMode, $lockVersion);
    }
    public function getRepository($entityName)
    {
        $entityName = ltrim($entityName, '\\');
        if (isset($this->repositories[$entityName])) {
            return $this->repositories[$entityName];
        }
        $metadata = $this->getClassMetadata($entityName);
        $repositoryClassName = $metadata->customRepositoryClassName;
        if ($repositoryClassName === null) {
            $repositoryClassName = $this->config->getDefaultRepositoryClassName();
        }
        $repository = new $repositoryClassName($this, $metadata);
        $this->repositories[$entityName] = $repository;
        return $repository;
    }
    public function contains($entity)
    {
        return $this->unitOfWork->isScheduledForInsert($entity)
            || $this->unitOfWork->isInIdentityMap($entity)
            && ! $this->unitOfWork->isScheduledForDelete($entity);
    }
    public function getEventManager()
    {
        return $this->eventManager;
    }
    public function getConfiguration()
    {
        return $this->config;
    }
    protected function errorIfClosed()
    {
        if ($this->closed) {
            throw ORMException::entityManagerClosed();
        }
    }
    public function isOpen()
    {
        return (!$this->closed);
    }
    public function getUnitOfWork()
    {
        return $this->unitOfWork;
    }
    public function getHydrator($hydrationMode)
    {
        if ( ! isset($this->hydrators[$hydrationMode])) {
            $this->hydrators[$hydrationMode] = $this->newHydrator($hydrationMode);
        }
        return $this->hydrators[$hydrationMode];
    }
    public function newHydrator($hydrationMode)
    {
        switch ($hydrationMode) {
            case Query::HYDRATE_OBJECT:
                return new Internal\Hydration\ObjectHydrator($this);
            case Query::HYDRATE_ARRAY:
                return new Internal\Hydration\ArrayHydrator($this);
            case Query::HYDRATE_SCALAR:
                return new Internal\Hydration\ScalarHydrator($this);
            case Query::HYDRATE_SINGLE_SCALAR:
                return new Internal\Hydration\SingleScalarHydrator($this);
            case Query::HYDRATE_SIMPLEOBJECT:
                return new Internal\Hydration\SimpleObjectHydrator($this);
            default:
                if ($class = $this->config->getCustomHydrationMode($hydrationMode)) {
                    return new $class($this);
                }
        }
        throw ORMException::invalidHydrationMode($hydrationMode);
    }
    public function getProxyFactory()
    {
        return $this->proxyFactory;
    }
    public function initializeObject($obj)
    {
        $this->unitOfWork->initializeObject($obj);
    }
    public static function create($conn, Configuration $config, EventManager $eventManager = null)
    {
        if ( ! $config->getMetadataDriverImpl()) {
            throw ORMException::missingMappingDriverImpl();
        }
        switch (true) {
            case (is_array($conn)):
                $conn = \Doctrine\DBAL\DriverManager::getConnection(
                    $conn, $config, ($eventManager ?: new EventManager())
                );
                break;
            case ($conn instanceof Connection):
                if ($eventManager !== null && $conn->getEventManager() !== $eventManager) {
                     throw ORMException::mismatchedEventManager();
                }
                break;
            default:
                throw new \InvalidArgumentException("Invalid argument: " . $conn);
        }
        return new static($conn, $config, $conn->getEventManager());
    }
    public function getFilters()
    {
        if (null === $this->filterCollection) {
            $this->filterCollection = new FilterCollection($this);
        }
        return $this->filterCollection;
    }
    public function isFiltersStateClean()
    {
        return null === $this->filterCollection
           || $this->filterCollection->isClean();
    }
    public function hasFilters()
    {
        return null !== $this->filterCollection;
    }
}
