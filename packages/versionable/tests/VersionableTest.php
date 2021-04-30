<?php

declare(strict_types=1);

namespace Knp\DoctrineBehaviors\Versionable\Tests;

use Doctrine\ORM\Tools\SchemaTool;
use Knp\DoctrineBehaviors\Tests\AbstractBehaviorTestCase;
use Knp\DoctrineBehaviors\Versionable\Entity\ResourceVersion;
use Knp\DoctrineBehaviors\Versionable\EventSubscriber\VersionableEventSubscriber;
use Knp\DoctrineBehaviors\Versionable\Tests\Fixtures\Entity\BlogPost;
use Knp\DoctrineBehaviors\Versionable\VersionManager;

final class VersionableTest extends AbstractBehaviorTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $versionableEventSubscriber = new VersionableEventSubscriber($this->entityManager);
        $this->eventManager->addEventSubscriber($versionableEventSubscriber);

        $schemaTool = new SchemaTool($this->entityManager);

        $classMetadatas = [
            $this->entityManager->getClassMetadata(BlogPost::class),
            $this->entityManager->getClassMetadata(ResourceVersion::class),
        ];
        $schemaTool->dropSchema($classMetadatas);
        $schemaTool->createSchema($classMetadatas);
    }

    public function testMakeVersionSnapshot()
    {
        /** @var BlogPost $blogPost */
        $blogPost = $this->entityManager->find(BlogPost::class, 1);

        $blogPost->title = 'Foozbaz!';
        $blogPost->content = 'Oerr!';

        $this->entityManager->flush();

        $versionManager = new VersionManager($this->entityManager);
        $versions = $versionManager->getVersions($blogPost);
        $this->assertCount(1, $versions);

        $firstVersion = $versions[1];

        $this->assertInstanceOf(ResourceVersion::class, $firstVersion);

        /** @var ResourceVersion $firstVersion */
        $this->assertSame(BlogPost::class, $firstVersion->getResourceName());
        $this->assertSame('Hello World!', $firstVersion->getVersionedData('title'));
        $this->assertSame('Barbaz', $firstVersion->getVersionedData('content'));
        $this->assertSame(1, $firstVersion->getVersion());
    }

    public function testRevert()
    {
        /** @var BlogPost $blogPost */
        $blogPost = $this->entityManager->find(BlogPost::class, 2);

        $this->assertSame('bar', $blogPost->title);
        $this->assertSame('bar', $blogPost->content);

        $versionManager = new VersionManager($this->entityManager);
        $versionManager->revert($blogPost, 1);

        $this->assertSame('foo', $blogPost->title);
        $this->assertSame('foo', $blogPost->content);
    }
}
