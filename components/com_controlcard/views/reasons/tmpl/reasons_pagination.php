<?php
/**
 * Created by PhpStorm.
 * User: User
 * Date: 09.01.2019
 * Time: 23:23
 */
?>

<?php if ($this->params->get('show_pagination', 2)) : ?>
    <div class="pagination">
        <?php if ($this->params->def('show_pagination_results', 1)) : ?>
            <p class="counter">
                <?php echo $this->pagination->getPagesCounter(); ?>
            </p>
        <?php endif; ?>
        <?php echo $this->pagination->getPagesLinks(); ?>
    </div>
<?php endif;
