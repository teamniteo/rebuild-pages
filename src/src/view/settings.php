<div class="wrap">

	<h2>Rebuild Pages from Majestic</h2>

	<form class="add:the-list: validate" method="post" enctype="multipart/form-data">

		<table class="form-table">
			<tbody>
			<tr valign="top">
				<th scope="row"><label for="ebn_import">Upload CSV from Majestic</label></th>
				<td>
					<!-- File input -->
					<input name="ebn_import" id="ebn_import" required type="file"/>
				</td>
			</tr>
			</tbody>
		</table>

		<!-- Nonce value -->
		<input type="hidden" name="ebn_nonce" id="ebn_nonce" value="<?php if ( ! empty( $nonce ) ) {
			echo $nonce;
		} ?>">
		<p class="submit"><input type="submit" class="button" name="submit" value="Rebuild Pages"/></p>

	</form>

</div>