<div style="width: 80%; text-align: center;">
    <form action="<?php echo $webroot; ?>/search/results" method="GET">
    	<input type="hidden" name="<?php echo CSRF::getTokenID(); ?>" value="<?php echo CSRF::getTokenValue(); ?>" />
        <input style="width: 200px;" type="text" name="q" id="q"/>
        <input type="submit" value="Search!"/>
    </form>
</div>
