<h3 class="subheading" style="border-bottom: 1px solid grey;">Search</h3>

<div class="row-fluid">
<!--    <form action="<?php // echo $webroot; ?>/search/results" method="GET">-->
    <form id="search_form" class="clearfix">
        <input type="hidden" name="<?php echo CSRF::getTokenID(); ?>" value="<?php echo CSRF::getTokenValue(); ?>" />
		<div class="row-fluid">
			<div class="columns small-12 large-9">
				<div class="row-fluid">
					<div class="small-12 medium-8 columns">
						<input class="input-large" type="text" placeholder="Search term" name="q" id="q" autofocus/>
					</div>
					<div class="small-12 medium-4 columns">
						<?php echo Html::select("idx", $indexes, null, null, null, '-- Limit to --'); ?>
					</div>
				</div>
				<div class="row-fluid">
					&nbsp;
				</div>
				<div class="row-fluid">
					<div class="small-12 medium-3 columns">
						<?php echo Html::select("_tags", $tags, null, null, null, '-- Tags --'); ?>
						<select style="display:none;" class="real_tags" name="tags[]" multiple="multiple">
						</select>
					</div>
					<div class="small-12 medium-5 columns" id="filtered_tag_list">
						<span style="font-size:1em" class="label radius secondary">No tag</span>
					</div>
					<div class="small-12 medium-4 columns">
						<button class="button tiny small-12" type="submit">Go</button>
					</div>
				</div>
			</div>
			<div class="columns large-3 small-12">
				Saved searches..
			</div>
		</div>
    </form>
</div>
        
<div id="search_message" class="row hide">
    <div data-alert class="alert-box warning" id="message_box"></div>
</div>

<div id="result" class="row" style="display: none;">

</div>
        
<script>
    var tagList = {};
    function showTagList() {
        tagLabels = '';
		tagCount = 0;
        for(i in tagList) {
            if(tagList.hasOwnProperty(i)) {
                tagLabels += '<span style="font-size:1em" class="label radius success" data-tag="'+tagList[i]+'">'+tagList[i]+' <span style="cursor:pointer;" class="remove_this_tag fi-x"></span></span> ';
				tagCount ++;
            }
        }
		if('' == tagLabels) {
			tagLabels = '<span style="font-size:1em" class="label radius secondary">No tag</span>';
		} else if(tagCount > 1) {
			tagLabels += '<span style="font-size:1em;cursor:pointer" class="clear_all_tags label radius secondary">Clear all</span>';
		}
        $('#filtered_tag_list').html(tagLabels);
    }
    $('#filtered_tag_list').on('click', '.clear_all_tags', function() {
		 for(i in tagList) {
			 if(tagList.hasOwnProperty(i)) {
				 $('#_tags option[value="'+tagList[i]+'"]').removeAttr('disabled');
				 delete tagList[i];
			 }
		 }
		 sortSelect(document.getElementById('_tags'));
		 showTagList();
	});
    $('#filtered_tag_list').on('click', '.remove_this_tag', function() {
        var tag = $(this).parent().data('tag');
        $(this).parent().remove();
        removeTag(tag);
    });
    function removeTag(tag) {
        if(tagList[tag] != undefined) {
            delete tagList[tag];
        }
		$('#_tags option[value="'+tag+'"]').removeAttr('disabled');
		sortSelect(document.getElementById('_tags'));
        if($('.real_tags option[value="'+tag+'"').length > 0) {
            $('.real_tags option[value="'+tag+'"').removeAttr('selected');
        }
        showTagList();
    }
    function addTag(tag) {
        tagList[tag] = tag;
        if($('.real_tags option[value="'+tag+'"').length > 0) {
            $('.real_tags option[value="'+tag+'"').attr('selected', 'true');
        } else {
            $('.real_tags').append('<option value="'+tag+'" selected="true">'+tag+'</option>');
        }
        showTagList();
    }
    $('#_tags').change(function() {
		var v = $(this).val();
        $(this).val('');
		if('' == v) return false;
        addTag(v);
		$(this).find('option[value="'+v+'"]').attr('disabled','disabled');
		sortSelect(document.getElementById('_tags'));
    });
    $("#search_form").submit(function(event) {
        event.preventDefault();
        $("#search_message").hide();
        $("#result").hide();
        
        var data = $("#search_form").serialize();
        
        $.getJSON("/search/results", data,
            function(response) {
//                var j_response = JSON.parse(response);
                if (response.success === false) {
                    $("#message_box").html(response.data);
                    $("#search_message").show();
                } else {
                    var text_data = "<span style='padding-left: 20px;'>No results found</span>";
                    if (response.data) {
                        text_data = response.data;
                    }
                    $("#result").html(text_data).delay(100).fadeIn();
                }
            },
            function(response) {
                $("#message_box").html("Failed to receive a response from search");
                $("#search_message").show();
            }
        );
        
        return false;
    });
	function sortSelect(selElem) {
		var tmpAry = new Array(), disabledAry = new Array();
		for (var i=0;i<selElem.options.length;i++) {
			if(selElem.options[i] != undefined) {
				if(selElem.options[i].disabled) {
					disabledAry[i] = new Array();
					disabledAry[i][0] = selElem.options[i].text;
					disabledAry[i][1] = selElem.options[i].value;
				} else {
					tmpAry[i] = new Array();
					tmpAry[i][0] = selElem.options[i].text;
					tmpAry[i][1] = selElem.options[i].value;
					tmpAry[i][2] = selElem.options[i].selected;
				}
			}
		}
		tmpAry.sort();
		disabledAry.sort();
		while (selElem.options.length > 0) {
			selElem.options[0] = null;
		}
		for (var j=0;j<tmpAry.length;j++) {
			if(tmpAry[j] == undefined) continue;
			var op = new Option(tmpAry[j][0], tmpAry[j][1], false, tmpAry[j][2]);
			selElem.options[j] = op;
		}
		j = selElem.options.length;
		for (var i=0;i<disabledAry.length;i++) {
			if(disabledAry[i] == undefined) continue;
			var op = new Option(disabledAry[i][0], disabledAry[i][1]);
			selElem.options[j] = op;
			selElem.options[j].disabled = true;
			j++;
		}
		return;
	}
</script>