<?= '<?xml version="1.0" encoding="UTF-8"?>' ?>

<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
<?php foreach ($staticPages as $page) : ?>
    <url>
        <loc><?= htmlspecialchars($baseUrl . $page['url']) ?></loc>
        <changefreq><?= $page['changefreq'] ?></changefreq>
        <priority><?= $page['priority'] ?></priority>
    </url>
<?php endforeach; ?>
<?php foreach ($projects as $project) : ?>
    <url>
        <loc><?= htmlspecialchars($baseUrl . '/progetti/' . ($project->slug ?? urlencode($project->title))) ?></loc>
        <changefreq>monthly</changefreq>
        <priority>0.7</priority>
<?php if ($project->updated_at) : ?>
        <lastmod><?= date('Y-m-d', strtotime($project->updated_at)) ?></lastmod>
<?php endif; ?>
    </url>
<?php endforeach; ?>
<?php foreach ($articles as $article) : ?>
<?php if ($article->link) : ?>
    <url>
        <loc><?= htmlspecialchars($article->link) ?></loc>
        <changefreq>monthly</changefreq>
        <priority>0.6</priority>
    </url>
<?php endif; ?>
<?php endforeach; ?>
</urlset>
