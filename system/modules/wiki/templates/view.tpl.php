<div class="tabs">
    <div class="tab-head">
        <a href="#view">View</a>
        <a href="#wiki-history">Wiki History</a>
        <a href="#page-history">Page History</a>
        <?php if ($wiki->canEdit($w->Auth->user())):?>
            <a href="#edit">Edit</a>
        <?php endif; ?>
        <?php if ($wiki->isOwner($w->Auth->user()) && $page->name == "HomePage"):?>
            <a href="#members">Members</a>
        <?php endif; ?>
        <a href="#comments">Comments</a>
        <a href="#attachments">Attachments</a>
    </div>
    <div class="tab-body">
        <div id="view">
            <ul class="breadcrumbs">
                <li <?php echo ($page->name === "HomePage" ? "class='current'" : ""); ?>>
                    <a href="<?php echo htmlentities(WEBROOT."/wiki/view/".$wiki->name."/HomePage"); ?>">Home</a>
                </li>
                <?php 
                    if (array_key_exists('wikicrumbs', $_SESSION) and array_key_exists($wiki->name, $_SESSION['wikicrumbs'])) { // $_SESSION['wikicrumbs'][$wiki->name]) {
                        foreach(array_keys($_SESSION['wikicrumbs'][$wiki->name]) as $pn) : ?>
                            <li <?php echo ($page->name === "HomePage" ? "class='current'" : ""); ?>>
                                <a href="<?php echo htmlentities(WEBROOT . "/wiki/view/{$wiki->name}/{$pn}"); ?>"><?php echo $pn; ?></a>
                            </li>
                        <?php endforeach;
                    }
                ?>
            </ul>
            <div>
                <?php echo $body?>
            </div>
        </div>
        <div id="wiki-history">
            <?php 
        	$wiki_hist = $wiki->getHistory();
                $table = array();
        	if (!empty($wiki_hist)){
                    $table[] = array("Date", "Page", "User");
                    foreach($wiki_hist as $wh) {
                        $table[]=array(
                            formatDateTime($wh["dt_created"]),
                            Html::a(WEBROOT."/wiki/view/".$wiki->name."/".$wh['name'],"<b>".$wh['name']."</b>"),
                            $w->Auth->getUser($wh['creator_id'])->getFullName()
                        );
                    }
                    echo Html::table($table,"history","tablesorter",true);
        	} else {
                    echo "No changes yet.";
        	}
            ?>
        </div>
        <div id="page-history">
            <?php 
        	$page_hist = $page->getHistory();
                $table = array();
        	if ($page_hist){
                    $table[]=array("Date", "User", "Action");
                    foreach($page_hist as $ph) {
                        $table[]=array(
                            $ph->getDateTime("dt_created","d/m/Y H:i"),
                            $w->Auth->getUser($ph->creator_id)->getFullName(),
                            Html::box(WEBROOT."/wiki/pageversion/".$wiki->name."/".$ph->id,"View",true),
                        );
                    }
                    echo Html::table($table,"history","tablesorter",true);
        	} else {
                    echo "No changes yet.";
        	}
            ?>
        </div>
        <?php if ($wiki->canEdit($w->Auth->user())):?>
            <div id="edit" class="clearfix">
                <?php echo $editForm; ?>
            </div>
        <?php endif; ?>
        <?php if ($wiki->isOwner($w->Auth->user()) && $page->name == "HomePage"):?>
            <!-- Ripped from wiki/templates/members.tpl.php -->
            <div id="members">
                <?php echo Html::box(WEBROOT."/wiki/editmember/".$wiki->id, "Add Member", true); ?>
                <?php if ($wiki->getUsers()): ?>
                    <table class="tablesorter">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Role</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach($wiki->getUsers() as $mem) : ?>
                                <tr>
                                    <td><?php echo $mem->getFullName(); ?></td>
                                    <td><?php echo $mem->role; ?></td>
                                    <td>
                                        <?php echo Html::box($webroot."/wiki/editmember/".$wiki->id."/".$mem->id, "Edit", true); ?>&nbsp;
                                        <?php echo Html::b($webroot."/wiki/delmember/".$wiki->id."/".$mem->id,"Delete"); ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php endif; ?>
            </div>
        <?php endif; ?>
        <div id="comments">
            <?php echo $w->partial("listcomments", array("object" => $page, "redirect" => "wiki/view/{$wiki->name}/{$page->name}#comments"), "admin"); ?>
        </div>
        <div id="attachments">
            <?php echo $w->partial("listattachments", array("object" => $page, "redirect" => "wiki/view/{$wiki->name}/{$page->name}#attachments"), "file"); ?>
        </div>
    </div>
</div>
