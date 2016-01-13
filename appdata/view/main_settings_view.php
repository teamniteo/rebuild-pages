<div class="wrap">

    <h2>Rebuild Pages from Majestic</h2>

    <form class="add:the-list: validate" method="post" enctype="multipart/form-data">

        <table class="form-table">
            <tbody>
                <tr valign="top">   
                <th scope="row"><label for="ebn_min_backlinks">Minimum backlinks to create page</label></th>
                <td>
                    <!-- Min backlinks required -->
                    <input name="ebn_min_backlinks" id="ebn_min_backlinks" type="number" value="0" />
                </td>
                </tr>
                <tr valign="top">
                    <th scope="row"><label for="ebn_import">Upload CSV from Majestic</label></th>
                <td>
                    <!-- File input -->
                    <input name="ebn_import" id="ebn_import" type="file" value="" aria-required="true" />
                </td>
                </tr>
            </tbody>
        </table>

        <!-- Nonce value -->
        <input type="hidden" name="ebn_nonce" id="ebn_nonce" value="<?php echo $nonce; ?>">
        <p class="submit"><input type="submit" class="button" name="submit" value="Rebuild Pages" /></p>

    </form>

</div>