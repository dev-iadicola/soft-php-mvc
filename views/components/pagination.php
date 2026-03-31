<?php
/** @var \App\Core\DataLayer\PaginationResult $pagination */
if (!isset($pagination) || !$pagination->hasPages()) return;

$baseUrl = strtok($_SERVER['REQUEST_URI'] ?? '', '?');
$queryParams = $_GET;
unset($queryParams['page']);
$queryString = http_build_query($queryParams);
$separator = $queryString !== '' ? '&' : '';

$buildUrl = function(int $page) use ($baseUrl, $queryString, $separator): string {
    return $baseUrl . '?' . $queryString . $separator . 'page=' . $page;
};
?>

<nav aria-label="Paginazione">
    <ul class="pagination justify-content-center">
        <?php if ($pagination->hasPrevious()) : ?>
            <li class="page-item">
                <a class="page-link" href="<?= $buildUrl($pagination->previousPage()) ?>">&laquo; Precedente</a>
            </li>
        <?php else : ?>
            <li class="page-item disabled"><span class="page-link">&laquo; Precedente</span></li>
        <?php endif; ?>

        <?php foreach ($pagination->pageRange() as $page) : ?>
            <li class="page-item <?= $page === $pagination->currentPage ? 'active' : '' ?>">
                <a class="page-link" href="<?= $buildUrl($page) ?>"><?= $page ?></a>
            </li>
        <?php endforeach; ?>

        <?php if ($pagination->hasNext()) : ?>
            <li class="page-item">
                <a class="page-link" href="<?= $buildUrl($pagination->nextPage()) ?>">Successiva &raquo;</a>
            </li>
        <?php else : ?>
            <li class="page-item disabled"><span class="page-link">Successiva &raquo;</span></li>
        <?php endif; ?>
    </ul>
</nav>
