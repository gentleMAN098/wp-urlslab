<?php
?>
<div class="urlslab-wrap">
	<?php require URLSLAB_PLUGIN_DIR . '/admin/templates/partials/urlslab-admin-header.php'; ?>
	<section class="urlslab-content-container">
		<?php require plugin_dir_path( __FILE__ ) . 'urlslab-admin-offloading-subpage.php'; ?>
		<?php require plugin_dir_path( __FILE__ ) . 'urlslab-admin-lazyload-subpage.php'; ?>
	</section>
</div>

