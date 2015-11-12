/**
 * 
 * @author Robert Lockerbie, robert@lockerbie.id.au, 2015
 * 
 *  amended by Ged Nash November 2015 to allow multiple objects to have tags
 *  on page. 
 *  
 **/
var uniTag = {
	buf:'',
	ready: function(reload) {
            
                // There can be multiple tag lists on the page, so bind tags for
                // each individually
                $('.tag_list').each(function (index) {
                    uniTag.bindTags($(this).get()[0].id);

                });       

                if(reload) {
			$('.tag_selection:visible:first').trigger('click');
		}
	},
	/*
	 * Builds the tag dialog
	 * This is a list of all available tags and highlights which ones are already attached
	 */
	loadTagDialog: function(tags,parent_id) {
            
                // Clicking tags for current dialog will close the dialog
		$("#"+parent_id+' .tag_selection').unbind('click');
		$("#"+parent_id+' .tag_selection').bind('click',function(e) {
                        $('.tag_list_dialog').hide();
                        uniTag.bindTags(parent_id);
                });
		var url = $('#'+parent_id).data('url');
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
		 	uniTag.buildTagDialog(parent_id);
});
		return uniTag.buf;
	},
	buildTagDialog: function(parent_id) {
		$('#'+parent_id+' .tag_list_dialog .tag_list_body').html(uniTag.buf);
                $('#'+parent_id+' .tag_list_dialog').show();
		if($('#'+parent_id+' .tag_list_dialog').offset().left < 0) {
			$('#'+parent_id+' .tag_list_dialog').css('left', Math.abs($('#'+parent_id+' .tag_list_dialog').offset().left/2));
			$('#'+parent_id+' .tag_list_dialog').css('right', 'auto');
		}
		$('#'+parent_id+' .tag_list_dialog .tag').bind('click', function(e) {
			uniTag.setTag(this,parent_id);
		});
		$('#'+parent_id+' .tag_list_header .search_tags').keyup(function() {
			uniTag.filterTagDialog(parent_id, $(this).val());
		});
	},
	/*
	 * Filters the list of available tags
	 * If no tags found give option to create a new tag
	 */
	filterTagDialog: function(parent_id, term) {
                $('#'+parent_id+' .available_tags_list .tag').each(function() {
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
			$('#'+parent_id+' .available_tags_list .custom_tag').remove();
		} else {
			var showNewTag = true;
			// Show new tag option unless there is an exact match for an existing tag
			$('#'+parent_id+' .available_tags_list .tag:visible').each(function() {
				if( $(this).data('tag') == term.trim() ) {
					showNewTag = false;
				}
			});
			if(showNewTag) {
				if($('#'+parent_id+' .available_tags_list .custom_tag').length == 0) {
					$('#'+parent_id+' .available_tags_list').prepend('<div class="custom_tag"><div class="label radius success"><span class="fi-price-tag" data-tag="'+term+'">Create tag "'+term+'"</span></div></div>');
				} else {
					if($('#'+parent_id+' .available_tags_list .custom_tag .label').hasClass('secondary')) {
						$('#'+parent_id+' .available_tags_list .custom_tag .label').removeClass('secondary').addClass('success');
					}
					$('#'+parent_id+' .available_tags_list .custom_tag .fi-price-tag').text('Create tag "'+term+'"').data('tag', term);
				}
				$('#'+parent_id+' .available_tags_list .custom_tag').unbind('click');
				$('#'+parent_id+' .available_tags_list .custom_tag').bind('click', function() {
					//Add new tag to this object
					var url = $('#'+parent_id).data('url');
					var tagText = $(this).find('.fi-price-tag').data('tag');
					$.get(url+'&cmd=addTag&tag='+encodeURIComponent(tagText), function(result) {
						if(result == 'Invalid request') {
							alert('Placeholder error');
						} else {
							$('#'+parent_id).append('<span data-tag="'+tagText+'" class="label radius secondary tag_selection"><span class="fi-price-tag">'+tagText+'</span></span>&nbsp;');
							$('#'+parent_id+' .no_tags').hide();
							uniTag.ready(true);
						}
					});
					$(this).remove();
				});
			} else {
				$('#'+parent_id+' .available_tags_list .custom_tag').remove();
			}
		}
	},
	/*
	 * Adds or removes a tag
	 */
	setTag: function(obj, parent_id) {
		var label = $(obj).find('.label');
		var url = $('#'+parent_id).data('url');
		var tagId = $(obj).data('id');
		var tag = $(obj).data('tag');
		if(label.hasClass('primary')) {
                    console.log("Removing tag:"+$('#'+parent_id+' .tag_selection:visible').length+";")
			$('#'+parent_id+' .tag_selection[data-tag="'+tag+'"]').hide();
                        // If there are no more tags then show no tag
			if($('#'+parent_id+' .tag_selection:visible').length == 0) {
				$('#'+parent_id+' .no_tags').show();
			}
			label.removeClass('primary').addClass('secondary');
			$.get(url+'&cmd=removeTag&tagId='+tagId);
		} else {
			if($('#'+parent_id+' .tag_selection[data-tag="'+tag+'"]').length == 0) {
				$('#'+parent_id).append('<span data-tag="'+tag+'" class="label radius secondary tag_selection"><span class="fi-price-tag">'+tag+'</span></span>&nbsp;');
                                // Bind click action to tag    
                                uniTag.bindTags(parent_id);
			} else {
				$('#'+parent_id+' .tag_selection[data-tag="'+tag+'"]').show();
			}
			$('#'+parent_id+' .no_tags').hide();
			$.get(url+'&cmd=setTag&tagId='+tagId);
			label.removeClass('secondary').addClass('primary');
		}
	},
	/*
	 * 
	 */
	bindTagsOld: function() {
		$('.tag_selection').unbind('click');
		$('.tag_selection').bind('click',function(e) {
			$.get($(this).parent().data('url')+'&cmd=get', function(result) {
                                uniTag.loadTagDialog(result,$("#"+e.currentTarget.parentNode.id+" .tag_list_dialog"));
			});
			e.preventDefault();
			e.stopImmediatePropagation();
			return false;
		});
		$('.hide_tag_list').bind('click', function(e) {
			$(this).parent().parent().parent().hide();
		});
	},
	bindTags: function(parent_id) {
		$("#"+parent_id+' .tag_selection').unbind('click');
		$("#"+parent_id+' .tag_selection').bind('click',function(e) {
                        // First, hide any other open dialog
                        $('.tag_list_dialog').hide();
			$.get($("#"+parent_id).data('url')+'&cmd=get', function(result) {
                                uniTag.loadTagDialog(result,parent_id);
			});
			e.preventDefault();
			e.stopImmediatePropagation();
			return false;
		});
		$("#"+parent_id+' .hide_tag_list').bind('click', function(e) {
			$("#"+parent_id+' .tag_list_dialog').hide();
		});
	}
};
$(document).ready(function() {
	uniTag.ready(false);
});
