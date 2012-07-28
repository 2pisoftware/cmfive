<?php
/**
* Add new members to a group
*
* @param <type> $w
*/
function groupmember_GET(Web &$w)
{
	$option = $w->pathMatch("group_id");

	$users = $w->Auth->getUsers();

	foreach ($users as $user)
	{
		$name = $user->is_group == 1 ? strtoupper($user->login) : $user->getContact()->getFullName();

		$select[$user->is_group][$name] = array($name,$user->id);
	}
	ksort($select[0]);
	ksort($select[1]);

	$template['New Member'] = array(array(array("Select Member: ","multiSelect","title",null,array_merge($select[0],$select[1])),
	array("","hidden","group_id",$option['group_id'])));
	if ($w->Auth->user()->is_admin)
	{
		$template['New Member'][0][] = array("Owner","checkbox","is_owner");
	}
		
	$w->out(Html::multiColForm($template,"/admin/groupmember","POST","Save"));

	$w->setLayout(null);
}

function groupmember_POST(Web &$w)
{
	$groupUsers = $w->Auth->getUser($_REQUEST['group_id'])->isInGroups();

	if ($groupUsers)
	{
		foreach ($groupUsers as $groupUser)
		{
			$groupUser->getParents();
		}
	}

	foreach ($_REQUEST['title'] as $member_id)
	{
		$existUser = $w->Auth->getUser($member_id)->isInGroups($_REQUEST['group_id']);

		if (!$existUser)
		{
			if (!$w->session('parents') || !in_array($member_id, $w->session('parents')))
			{
				$groupMember = new GroupUser($w);
				$groupMember->group_id = $_REQUEST['group_id'];
				$groupMember->user_id = $member_id;
				$groupMember->role = ($_REQUEST['is_owner'] && $_REQUEST['is_owner'] == 1) ? "owner" : "member";
				$groupMember->insert();
			}
				
			if ($w->session('parents') && in_array($member_id, $w->session('parents')))
			{
				$exceptions[] = $w->Auth->getUser($member_id)->login;
			}
		}
		else
		{
			$user = $existUser[0]->getUser();
				
			$exceptions[] = $user->is_group == 1 ? $user->login : $user->getContact()->getFullName();
		}
	}
	$w->sessionUnset('parents');

	if ($exceptions)
	$w->error(implode(", ", $exceptions)." can not be added!", "/admin/moreInfo/".$_REQUEST['group_id']);
	else
	$w->msg("New members are added!", "/admin/moreInfo/".$_REQUEST['group_id']);
}
