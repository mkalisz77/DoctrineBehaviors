<?php

declare(strict_types=1);

namespace Knp\DoctrineBehaviors\Versionable\Entity;

use DateTimeInterface;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="versionable_resources")
 */
class ResourceVersion
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue
     * @var int
     */
    private $id;

    /**
     * @ORM\Column(name="resource_name", type="string")
     * @var string
     */
    private $resourceName;

    /**
     * @ORM\Column(name="resource_id", type="string")
     * @var string
     */
    private $resourceId;

    /**
     * @ORM\Column(name="versioned_data", type="array")
     * @var mixed[]
     */
    private $versionedData = [];

    /**
     * @ORM\Column(name="snapshot_version_id", type="integer")
     * @var int
     */
    private $version;

    /**
     * @ORM\Column(name="snapshot_date", type="datetime")
     * @var DateTimeInterface
     */
    private $snapshotDate;

    public function __construct(string $resourceName, string $resourceId, array $versionedData, int $version)
    {
        $this->resourceName = $resourceName;
        $this->resourceId = $resourceId;
        $this->versionedData = $versionedData;
        $this->version = $version;
        $this->snapshotDate = new \DateTime('now');
    }

    public function getId()
    {
        return $this->id;
    }

    public function getResourceName()
    {
        return $this->resourceName;
    }

    public function getResourceId()
    {
        return $this->resourceId;
    }

    public function getVersionedData($key = null)
    {
        if ($key !== null) {
            return $this->versionedData[$key];
        }
        return $this->versionedData;
    }

    public function getVersion()
    {
        return $this->version;
    }

    public function getSnapshotDate()
    {
        return $this->snapshotDate;
    }
}
