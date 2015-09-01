<h3 class="subheading" style="border-bottom: 1px solid grey;">Search</h3>

<div class="row-fluid">
<!--    <form action="<?php // echo $webroot; ?>/search/results" method="GET">-->
    <form id="search_form" class="clearfix">
        <input type="hidden" name="<?php echo CSRF::getTokenID(); ?>" value="<?php echo CSRF::getTokenValue(); ?>" />
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
            </div>
            <div class="small-12 medium-4 columns">
                <button class="button tiny small-12" type="submit">Go</button>
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
        for(i in tagList) {
            if(tagList.hasOwnProperty(i)) {
                tagLabels += '<span style="font-size:0.9em" class="label radius success" data-tag="'+tagList[i]+'">'+tagList[i]+' <span style="cursor:pointer;" class="remove_this_tag fi-x"></span></span> ';
            }
        }
        $('#filtered_tag_list').html(tagLabels);
    }
    $('#filtered_tag_list').on('click', '.remove_this_tag', function() {
        var tag = $(this).parent().data('tag');
        $(this).parent().remove();
        removeTag(tag);
    });
    function removeTag(tag) {
        if(tagList[tag] != undefined) {
            delete tagList[tag];
        }
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
        addTag($(this).val());
        $(this).val('');
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
</script>