<?if ($results):?>
    <?for($i = 0; $i < sizeof($results); $i++):?>
        <?
        if ($_REQUEST['idx']) {
            $x = recursiveArraySearch($allidx,$_REQUEST['idx']);
        } else {
            $x = $i;
        }
        ?>
        <?if ($results[$i]['matches']):?>

            <div class="search-index"><?=$allidx[$x][0]?></div>
            <?if (!$_REQUEST['idx'] && $results[$i]['total'] < $results[$i]['total_found']):?>
            <div class="search-result">
                ... <?=$results[$i]['total']?> of <?=$results[$i]['total_found']?> records displayed ...
                <?=Html::a($webroot."/search/results?q=".$_REQUEST['q']."&idx=".$allidx[$x][1],"Display all search results.")?>
            </div>
            <?endif;?>
            <?foreach($results[$i]['matches'] as $id => $match):?>
                <?
                $object = $w->service('Search')->getObjectForIndex($allidx[$x][1],$id);
                ?>
                <?if ($object && $object->canList($w->Auth->user())):?>
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
            <?endforeach;?>
            <?if ($results[$i]['total'] === 0):?>
            <div class="search-result">
                No documents found in this index.
            </div>
            <?endif;?>
            <hr/>
        <?endif;?>
    <?endfor;?>
<?else:?>
    <div class="search-result">
        No documents found.
    </div>
<?endif;?>
