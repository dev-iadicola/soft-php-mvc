<?php

declare(strict_types=1);

use App\Core\DataLayer\Seeder\Seeder;
use PHPUnit\Framework\TestCase;

require_once __DIR__ . '/../../../database/seed/stubs/article-demo-content.php';

class ArticleDemoSeederTest extends TestCase
{
    public function testArticleDemoDefinitionsProvideRichAndVariedContent(): void
    {
        $definitions = articleDemoDefinitions();

        $this->assertCount(10, $definitions);
        $this->assertContains('pillar', array_column($definitions, 'format'));
        $this->assertContains('quick-note', array_column($definitions, 'format'));

        $slugs = [];

        foreach ($definitions as $definition) {
            $this->assertLessThanOrEqual(50, mb_strlen($definition['title']));
            $this->assertNotEmpty($definition['subtitle']);
            $this->assertNotEmpty(strip_tags($definition['overview']));
            $this->assertNotEmpty($definition['tags']);
            $this->assertArrayNotHasKey($definition['slug'], $slugs);

            $slugs[$definition['slug']] = true;
        }
    }

    public function testArticlesSeederBuildsUniqueRowsWithDifferentCovers(): void
    {
        /** @var Seeder $seeder */
        $seeder = require __DIR__ . '/../../../database/seed/2026_02_19_100002_articles_seeder.php';
        $rows = $seeder->getRows();

        $this->assertCount(10, $rows);

        $covers = [];

        foreach ($rows as $row) {
            $this->assertSame(1, $row['is_active']);
            $this->assertStringStartsWith('data:image/svg+xml;utf8,', (string) $row['img']);
            $this->assertLessThanOrEqual(1000, strlen((string) $row['img']));
            $this->assertArrayHasKey('slug', $row);
            $this->assertArrayNotHasKey($row['img'], $covers);

            $covers[$row['img']] = true;
        }
    }

    public function testTagsSeederDerivesUniqueTagsFromArticles(): void
    {
        /** @var Seeder $seeder */
        $seeder = require __DIR__ . '/../../../database/seed/2026_03_31_140001_tags_seeder.php';
        $rows = $seeder->getRows();

        $this->assertGreaterThanOrEqual(10, count($rows));

        $slugs = [];

        foreach ($rows as $row) {
            $this->assertNotEmpty($row['name']);
            $this->assertNotEmpty($row['slug']);
            $this->assertArrayNotHasKey($row['slug'], $slugs);

            $slugs[$row['slug']] = true;
        }
    }
}
