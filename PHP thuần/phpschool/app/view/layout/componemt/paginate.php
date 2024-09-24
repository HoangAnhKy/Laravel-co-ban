<?php
$url = BASE_URL . $_SERVER['REQUEST_URI'];
$page = (int)($_GET['page'] ?? 1);

$position = strpos($url, 'page=' . $page);
if ($position !== false) {
    $length = strlen('page=' . $page) + 1;
    $position -= 1;
    $url = substr_replace($url, '', $position, $length);
}

$ampersand = strpos($url, "?") !== false ? "&" : "?" ;
?>

<?php if (isset($row) && $row >= 2): ?>
    <nav>
        <ul class="pagination">
            <li class="page-item <?= $page === 1 ? 'disabled' : '' ?>">
                <a class="page-link" href="<?= $page > 1 ? ($url . "{$ampersand}page=" . ($page - 1)) : '' ?>">Previous</a>
            </li>
            <?php for ($i = 1; $i <= $row; $i += 1) { ?>
                <li class="page-item"><a class="page-link <?= ($page === $i) ? 'active' : ''; ?>"
                                         href="<?= $url . "{$ampersand}page=" . $i ?>"><?= $i ?></a></li>
            <?php } ?>
            <li class="page-item <?= $page < $row ? '' : 'disabled' ?>">
                <a class="page-link" href="<?= $page < $row ? ($url . "{$ampersand}page=" . ($page + 1)) : '' ?>">Next</a>
            </li>
        </ul>
    </nav>
<?php endif; ?>