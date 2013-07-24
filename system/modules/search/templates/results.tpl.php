<?
$currentIndex = "";
if ($results):
?>
    <?foreach ($results as $res):?>

    		<?if ($res['class_name'] != $currentIndex):
    			$currentIndex = $res['class_name'];?>
            <div class="search-index"><?=$currentIndex?></div>
            <?endif;?>
                <?
                $object = $w->Search->getObject($res['class_name'],$res['object_id']);
                if ($object && $object->canList($w->Auth->user())):?>
                    <div class="search-result">
                        <?if ($object->canView($w->Auth->user())):?>
                            <a class="search-title" href="<?=$webroot?>/<?=$object->printSearchUrl()?>">
                                <?=$object->printSearchTitle()?>
                            </a>
                            <div class="search-listing">
                                <?=$object->printSearchListing()?>
                            </div>
                        <?else:?>
                            <div class="search-title"><?=$object->printSearchTitle()?></div>
                            <div class="search-listing">(restricted)</div>
                        <?endif;?>
                    </div>
                <?endif;?>

            <hr/>
      
    <?endforeach;?>
<?else:?>
    <div class="search-result">
        No documents found.
    </div>
<?endif;?>
