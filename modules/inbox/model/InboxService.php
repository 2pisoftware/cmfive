<?php
class InboxService extends DbService {
	
	function addMessage($subject, $message, $user_id=null, $sender_id=null,$parent_id = null) {
		if (!$user_id) {
			$user_id = $this->Auth->user()->id;
		}
		if (!$sender_id) {
			if ($this->Auth->user()) {
				$sender_id = $this->Auth->user()->id;
			}
		}
		if (!is_a($message,"DbObject")) {
			$mso = new Inbox_message($this->w);
			$mso->message = $message;
			$mso->insert();
		} else {
			$mso = $message;
		}
		$msg = new Inbox($this->w);
		$msg->user_id = $user_id;
		$msg->parent_message_id = $parent_id;
		$msg->subject = $subject;
		if ($sender_id) {
			$msg->sender_id = $sender_id;
		}
		$msg->message_id = $mso->id;
		$msg->dt_created = time();
		$msg->is_new = 1;
		$msg->is_archived = 0;
		$msg->insert();
		
		$receiver = $this->Auth->getUser($user_id);
		$this->sendMail($receiver->getContact()->email,null,null,null,null,$subject,$message);
	}

	function sendMail($to,$cc,$bcc,$from,$replyto,$subject,$message) {
		$mailconf = $this->w->moduleConf("inbox", "phpmailer");

		if ($mailconf) {
			require_once('PHPMailer/class.phpmailer.php');	
			$mail = new PHPMailer();
			$mail->IsSMTP(); // telling the class to use SMTP
			$mail->Host       = $mailconf['Host']; // SMTP server
			$mail->SMTPDebug  = 1;                     // enables SMTP debug information (for testing)
			// 1 = errors and messages
			// 2 = messages only
			$mail->SMTPAuth   = $mailconf['SMTPAuth'];                  // enable SMTP authentication
			$mail->SMTPSecure = $mailconf['SMTPSecure'];                 // sets the prefix to the servier
			$mail->Host       = $mailconf['Host'];      // sets GMAIL as the SMTP server
			$mail->Port       = $mailconf['Port'];                   // set the SMTP port for the GMAIL server
			$mail->Username   = $mailconf['Username'];  // GMAIL username
			$mail->Password   = $mailconf['Password'];            // GMAIL password
			
			if ($from) {
				$mail->SetFrom($from);
			} else {
				$mail->SetFrom($mailconf['Username']);
			}
			
			if($replyto){
				$mail->AddReplyTo($replyto);
			}
			
			if($subject){
				$mail->Subject    = $subject;
			}
			
			if ($message) {
				$mail->AltBody    = $message; // optional, comment out and test
				$mail->MsgHTML($message);
			}
			
			// add TO address(es)
			if ($to != null && is_array($to)) {
				foreach($to as $a){
					$mail->AddAddress($a);
				}
			} elseif ($to) {
				$mail->AddAddress($to);
			}
			
			// add CC address(es)
			if ($cc != null && is_array($cc)) {
				foreach($cc as $a){
					$mail->AddCC($a);
				}
			} elseif ($cc) {
				$mail->AddCC($cc);
			}
									
			// add BCC address(es)
			if ($bcc != null && is_array($bcc)) {
				foreach($bcc as $a){
					$mail->AddBCC($a);
				}
			} elseif ($bcc) {
				$mail->AddBCC($bcc);
			}
			
			if(!$mail->Send()) {
				$this->w->error("Mailer Error: " . $mail->ErrorInfo,"/main/index");
				return false;
			}
			return true;
		}
	}
	
	function inboxCountMarker($user_id=null) {
		if (!$user_id) {
			$user_id = $this->Auth->user()->id;
		}
		if (!$user_id) {
			return null;
		}
		$count = $this->_db->sql("select count(*) as count from inbox where user_id = ".$user_id." and is_new = 1")->fetch_element("count");
		if ($count) {
			$count = " (".$count.")";
		} else {
			$count = "";
		}
		return $count;
	}

	function & getMessages($page,$page_size,$user_id,$is_new,$is_arch=0,$is_del=0,$has_parent=0) {
		$offset = $page * $page_size;
		if ($is_arch == 0 && $is_del == 0){
			$rows = $this->_db->get("inbox")
			->where("user_id",$user_id)
			->and("is_new",$is_new)
			->and("is_archived",$is_arch)
			->and("is_deleted",$is_del)
			->and("has_parent",$has_parent)
			->and("del_forever",0)
			->order_by("dt_created")
			->limit($offset,$page_size)
			->fetch_all();
			return $this->fillObjects("Inbox", $rows);
		} else if ($is_arch == 1){
			$rows = $this->_db->get("inbox")
			->where("user_id",$user_id)
			->and("is_archived",$is_arch)
			->and("is_deleted",$is_del)
			->and("has_parent",$has_parent)
			->and("del_forever",0)
			->order_by("dt_created")
			->limit($offset,$page_size)
			->fetch_all();
			return $this->fillObjects("Inbox", $rows);
		} else if ($is_arch == 0 && $is_del == 1){
			$rows = $this->_db->get("inbox")
			->where("user_id",$user_id)
			->and("is_deleted",$is_del)
			->and("has_parent",$has_parent)
			->and("del_forever",0)
			->order_by("dt_created")
			->limit($offset,$page_size)
			->fetch_all();
			return $this->fillObjects("Inbox", $rows);
		}
	}

	function & getDelMessageCount($user){
		$sql = "SELECT COUNT(*) FROM `inbox` WHERE is_deleted = 1 AND user_id = ".$user." AND del_forever = 0";
		$result = $this->_db->sql($sql)->fetch_row();
		$result ? $result = $result['COUNT(*)'] : $result = 0;
		return $result;
	}
	
	function & getNewMessageCount($user){
		$sql = "SELECT COUNT(*) FROM `inbox` WHERE is_deleted = 0 AND is_new = 1 AND is_archived = 0 AND user_id = ".$user." AND del_forever = 0";
		return $this->_db->sql($sql)->fetch_row();
	}

	function & getReadMessageCount($user){
		$sql = "SELECT COUNT(*) FROM `inbox` WHERE is_deleted = 0 AND is_new = 0 AND is_archived = 0 AND user_id = ".$user." AND del_forever = 0";
		return $this->_db->sql($sql)->fetch_row();
	}

	function & getArchCount($user){
		$sql = "SELECT COUNT(*) FROM `inbox` WHERE is_deleted = 0 AND is_new = 1 AND is_archived = 1 AND user_id = ".$user." AND del_forever = 0";
		$newarch = $this->_db->sql($sql)->fetch_row();
		$newarch ? $newarch = $newarch['COUNT(*)'] : $newarch = 0;
		$sql = "SELECT COUNT(*) FROM `inbox` WHERE is_deleted = 0 AND is_new = 0 AND is_archived = 1 AND user_id = ".$user." AND del_forever = 0";
		$arch = $this->_db->sql($sql)->fetch_row();
		$arch ? $arch = $arch['COUNT(*)'] : $arch = 0;
		$total = ($newarch*1) + ($arch*1);
		return $total;
	}

	function & getMessage($id) {
		return $this->getObject("Inbox", $id);
	}

	function notifyRoleUsers($role,$subject,$message,$sender_id=null) {
		$users = $this->auth->getUsersForRole($role);

		// no notification for current user:
		$logged_uid = $this->w->Auth->user()->id;

		while (!is_null($key = key($users) ) ) {
				
			if($users[$key]->id == $logged_uid) unset($users[$key]);
				
			next($users);
		}


		// notify the rest:
		if ($users) {
			$mso = new Inbox_message($this->w);
			$mso->message = $message;
			$mso->insert();
			 

			foreach ($users as $u) {
				$this->addMessage($subject, $mso, $u->id, $sender_id);
			}
		}
	}

	function markAllMessagesRead() {
		$user_id = $this->Auth->user()->id;
		return $this->_db->sql("update inbox set is_new = 0, dt_read = NOW() where user_id = $user_id and is_new = 1")->execute();
	}
}
