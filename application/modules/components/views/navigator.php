<div class="row navigator">
	<div class="col-xs-4 col-sm-6 text-left">
		<button type="button"
						id="btnAdd"
						class="btn btn-primary ripple<?php echo (empty($btn_add_label) ? ' hidden' : ''); ?>"
						data-toggle="modal"
						data-target="<?php echo $modal_name; ?>">
			<?php echo $btn_add_label; ?>
		</button>
	</div>
	<div class="col-xs-8 col-sm-6 text-right navigator-fields hidden">
		<span class="hidden-xs">Page</span>
		<span class="page_num badge">1</span> of <span class="total_pages badge">1</span>
		<span class="hidden-xs">
      (Total Records: <span class="total_records badge">1</span>)
    </span>
		<span class="navigator-buttons">&nbsp;</span>
	</div>
</div>
