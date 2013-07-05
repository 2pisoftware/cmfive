<form action="<?=$webroot?>/file/attach" method="POST" enctype="multipart/form-data">
    <table class="form" width="100%">
        <tr><td class='section' colspan='2'>Attach a File</td></tr>
        <?php
        	if ($types && sizeof($types)) {
        		?>
        <tr><td nowrap='true'>Attachment Type</td><td><?=Html::select("type_code",$types) ?></td></tr>
        		<?
        	}
        ?>
        <tr><td nowrap='true'>File</td><td><input type="file" name="file" /></td></tr>
        <tr><td nowrap='true'>Title</td><td><input type="text" name="title" /></td></tr>
        <tr><td colspan='2'>Description</td></tr>
        <tr><td colspan='2'><textarea name="description" cols="30" rows="5"></textarea></td></tr>
        <tr><td colspan='2'><input type="submit" name="" value="upload"/></td></tr>

        <input type="hidden" name="table" value="<?=$table?>"/>
        <input type="hidden" name="id" value="<?=$id?>"/>
        <input type="hidden" name="url" value="<?=$url?>"/>

    </table>
</form>
