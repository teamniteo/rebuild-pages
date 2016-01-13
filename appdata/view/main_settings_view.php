<div class="wrap">
    <h2>Rebuild Pages from Majestic</h2>
    <form class="add:the-list: validate" method="post" enctype="multipart/form-data">
        <!-- Min backlinks required -->
        <p>
            Minimum backlinks to create page: <input name="ebn_min_backlinks" id="ebn_min_backlinks" type="number" value="0" />
        </p>

        <!-- File input -->
        <p>
            <label for="ebn_import">Upload CSV from Majestic: </label>
            <input name="ebn_import" id="ebn_import" type="file" value="" aria-required="true" />
        </p>
        <p class="submit"><input type="submit" class="button" name="submit" value="Rebuild Pages" /></p>
    </form>
</div>