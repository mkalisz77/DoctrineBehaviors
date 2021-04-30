<?php

declare(strict_types=1);

namespace Knp\DoctrineBehaviors\Versionable\Tests\Fixtures\Entity;

use Doctrine\ORM\Mapping as ORM;
use Knp\DoctrineBehaviors\Versionable\Contract\VersionableInterface;

/**
 * @ORM\Entity
 * @ORM\Table(name="posts")
 */
class BlogPost implements VersionableInterface
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue
     * @var int
     */
    public $id;

    /**
     * @ORM\Column(type="string")
     * @var string
     */
    public $title;

    /**
     * @ORM\Column(type="text")
     * @var string
     */
    public $content;

    /**
     * @ORM\Column(type="integer")
     * @var int
     * @version
     */
    public $version;
}
