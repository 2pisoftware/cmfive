/**
 * 
 * @author Robert Lockerbie, robert@lockerbie.id.au, 2015
 **/
var uniTag = {
	buf:'',
	ready: function(reload) {
		uniTag.bindTags();
		if(reload) {
			$('.tag_selection:visible:first').trigger('click');
		}
	},
	/*
	 * Builds the tag dialog
	 * This is a list of all available tags and highlights which ones are already attached
	 */
	loadTagDialog: function(tags) {
		var url = $('.tag_list').data('url');
		$.get(url+'&cmd=getAll', function(availableTags) {
			uniTag.buf = '<div class="available_tags">';
			uniTag.buf += '<div class="available_tags_list">';
			if(availableTags.length == 0) {
				uniTag.buf += '<div class="custom_tag"><div class="label radius secondary"><span class="fi-price-tag">No tags found</span></div></div>';
			} else {
				for(tagId in availableTags) {
					if(availableTags.hasOwnProperty(tagId)) {
						var tag = availableTags[tagId];
						var cl = 'secondary';
						//tags here is the list of attached tags
						for(selectedTagId in tags) {
							if(tags.hasOwnProperty(selectedTagId)) {
								if(tags[selectedTagId].tag == tag.tag) {
									cl = 'primary';
									break;
								}
							}
						}
						uniTag.buf += '<div class="tag" data-id="'+tag.id+'" data-tag="'+tag.tag+'"><div class="label radius '+cl+'"><span ';
						if(tag.tag_color != '') {
							uniTag.buf += 'style="color:'+tag.tag_color+';';
						}
						uniTag.buf += ' class="fi-price-tag">'+tag.tag+'</span></div></div> ';
					}
				}
			}
			uniTag.buf += '</div>';
			uniTag.buildTagDialog();
		});
		return uniTag.buf;
	},
	buildTagDialog: function() {
		$('.tag_list_dialog .tag_list_body').html(uniTag.buf);
		$('.tag_list_dialog').show();
		if($('.tag_list_dialog').offset().left < 0) {
			$('.tag_list_dialog').css('left', Math.abs($('.tag_list_dialog').offset().left/2));
			$('.tag_list_dialog').css('right', 'auto');
		}
		$('.tag_list_dialog .tag').bind('click', function(e) {
			uniTag.setTag(this);
		});
		$('.tag_list_header .search_tags').keyup(function() {
			uniTag.filterTagDialog($(this).val());
		});
	},
	/*
	 * Filters the list of available tags
	 * If no tags found give option to create a new tag
	 */
	filterTagDialog: function(term) {
		$('.available_tags_list .tag').each(function() {
			var tag = $(this).data('tag');
			if(tag !== undefined) {
				if(term.length == 0) {
					$(this).show();
				} else {
					var rE = new RegExp('.*'+term+'.*', 'i');
					if(tag.match(rE)) {
						$(this).show();
					} else {
						$(this).hide();
					}
				}
			}
		});
		if(term.length == 0) {
			$('.available_tags_list .custom_tag').remove();
		} else {
			if($('.available_tags_list .tag:visible').length == 0) {
				if($('.available_tags_list .custom_tag').length == 0) {
					$('.available_tags_list').prepend('<div class="custom_tag"><div class="label radius success"><span class="fi-price-tag">'+term+'</span></div></div>');
				} else {
					if($('.available_tags_list .custom_tag .label').hasClass('secondary')) {
						$('.available_tags_list .custom_tag .label').removeClass('secondary').addClass('success');
					}
					$('.available_tags_list .custom_tag .fi-price-tag').text(term);
				}
				$('.available_tags_list .custom_tag').unbind('click');
				$('.available_tags_list .custom_tag').bind('click', function() {
					//Add new tag to this object
					var url = $('.tag_list').data('url');
					var tagText = $(this).find('.fi-price-tag').text();
					$.get(url+'&cmd=addTag&tag='+encodeURIComponent(tagText), function(result) {
						if(result == 'Invalid request') {
							alert('Placeholder error');
						} else {
							$('.tag_list').append('<span data-tag="'+tagText+'" class="label radius secondary tag_selection"><span class="fi-price-tag">'+tagText+'</span></span>&nbsp;');
							$('.tag_list .no_tags').hide();
							uniTag.ready(true);
						}
					});
					$(this).remove();
				});
			}
		}
	},
	/*
	 * Adds or removes a tag
	 */
	setTag: function(obj) {
		var label = $(obj).find('.label');
		var url = $('.tag_list').data('url');
		var tagId = $(obj).data('id');
		var tag = $(obj).data('tag');
		if(label.hasClass('primary')) {
			$('.tag_list .tag_selection[data-tag="'+tag+'"]').hide();
			if($('.tag_list .tag_selection:visible').length == 0) {
				$('.tag_list .no_tags').show();
			}
			label.removeClass('primary').addClass('secondary');
			$.get(url+'&cmd=removeTag&tagId='+tagId);
		} else {
			if($('.tag_list .tag_selection[data-tag="'+tag+'"]').length == 0) {
				$('.tag_list').append('<span data-tag="'+tag+'" class="label radius secondary tag_selection"><span class="fi-price-tag">'+tag+'</span></span>&nbsp;');
			} else {
				$('.tag_list .tag_selection[data-tag="'+tag+'"]').show();
			}
			$('.tag_list .no_tags').hide();
			$.get(url+'&cmd=setTag&tagId='+tagId);
			label.removeClass('secondary').addClass('primary');
		}
	},
	/*
	 * 
	 */
	bindTags: function() {
		$('.tag_selection').unbind('click');
		$('.tag_selection').bind('click',function(e) {
			$.get($(this).parent().data('url')+'&cmd=get', function(result) {
				uniTag.loadTagDialog(result);
			});
			e.preventDefault();
			e.stopImmediatePropagation();
			return false;
		});
		$('.hide_tag_list').bind('click', function(e) {
			$(this).parent().parent().parent().hide();
		});
	}
};
$(document).ready(function() {
	uniTag.ready(false);
});
