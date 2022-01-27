<h2>Hello <?= $name ?></h2>
<h2>You won at the lottery:</h2>
<a href="<?= env('FRONTEND_URL') ?>/lot/<?= $lot ?>">
    <?= $lotName ?>
</a>
