<?php

declare(strict_types=1);

namespace Knp\DoctrineBehaviors\Versionable;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Mapping\ClassMetadata;
use Knp\DoctrineBehaviors\Versionable\Contract\VersionableInterface;
use Knp\DoctrineBehaviors\Versionable\Entity\ResourceVersion;
use Knp\DoctrineBehaviors\Versionable\Exception\VersionableException;

final class VersionManager
{
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * Return all versions of an versionable entity
     *
     * @return ResourceVersion[]
     */
    public function getVersions(VersionableInterface $versionable): array
    {
        $versionableClassName = get_class($versionable);
        $versionableClass = $this->entityManager->getClassMetadata($versionableClassName);
        $resourceId = current($versionableClass->getIdentifierValues($versionable));

        // INDEX BY bug?
        $query = $this->entityManager->createQuery(
            'SELECT v FROM ' . ResourceVersion::class . ' v INDEX BY v.version ' .
            'WHERE v.resourceName = ?1 AND v.resourceId = ?2 ORDER BY v.version DESC');
        $query->setParameter(1, $versionableClassName);
        $query->setParameter(2, $resourceId);

        $newVersions = [];
        foreach ($query->getResult() as $version) {
            $newVersions[$version->getVersion()] = $version;
        }
        return $newVersions;
    }

    public function revert(VersionableInterface $versionable, int $targetVersionNumber): void
    {
        $resourceVersions = $this->getVersions($versionable);

        if (! isset($resourceVersions[$targetVersionNumber])) {
            $errorMessage = sprintf('Trying to access an unknown version "%s"', $targetVersionNumber);
            throw new VersionableException($errorMessage);
        }

        $resourceVersion = $resourceVersions[$targetVersionNumber];

        $versionableClass = $this->entityManager->getClassMetadata(get_class($versionable));

        foreach ($resourceVersion->getVersionedData() as $key => $value) {
            if (! isset($versionableClass->reflFields[$key])) {
                continue;
            }

            $versionableClass->reflFields[$key]->setValue($versionable, $value);
        }

        if ($versionableClass->changeTrackingPolicy === ClassMetadata::CHANGETRACKING_DEFERRED_EXPLICIT) {
            $this->entityManager->persist($versionable);
        }
    }
}
