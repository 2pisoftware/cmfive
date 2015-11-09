<div class="tag_list" data-url="/tag/ajaxTag/<?php echo ($object_class ? "?class=" . $object_class : '') . ($object_id ? '&id=' . $object_id : ''); ?>">
	<?php if (!empty($tags)) : ?>
		<?php foreach($tags as $tag) : ?>
			<span data-tag="<?php echo $tag->tag; ?>" class="label radius secondary tag_selection">
				<span <?php echo (!empty($tag->tag_color) ? 'style="color: '.$tag->tag_color.'"' : '') ?> class="fi-price-tag"><?php echo $tag->tag; ?></span>
			</span>
		<?php endforeach; ?>
	<?php else: ?>
		<span class="label radius secondary no_tags tag_selection"><span class="fi-price-tag">No tag</span></span>
	<?php endif; ?>
		
	<?php if (!empty($user)) : ?>
		<div class="tag_list_dialog" id="tag_list_<?php echo ($object_class ? : '') . ($object_id ? : ''); ?>">
			<div class="tag_list_modal">
				<div class="tag_list_header">
					Available tags <span class="fi-x hide_tag_list"></span>
					<div>
						<input type="text" placeholder="<?php echo ($user->hasRole("tag_admin") ? 'Add / ': '') . 'Filter tags'; ?>" class="search_tags" />
					</div>
				</div>
				<div class="tag_list_body"></div>
			</div>
		</div>
	<?php endif; ?>
</div>
