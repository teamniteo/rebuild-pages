<div class="wrap">
    <?php if ($show_error): ?>
        <div class="error">

            <?php foreach ($this->log['error'] as $error): ?>
                <p><?php echo $error; ?></p>
            <?php endforeach; ?>

        </div>
    <?php endif; ?>

    <?php if ($show_notice): ?>
        <div class="updated fade">

            <?php foreach ($this->log['notice'] as $notice): ?>
                <p><?php echo $notice; ?></p>
            <?php endforeach; ?>

        </div>
    <?php endif; ?>
</div>
