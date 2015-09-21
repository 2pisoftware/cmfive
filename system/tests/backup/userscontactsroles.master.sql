
SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";
TRUNCATE TABLE `contact`;
INSERT INTO `contact` (`id`, `firstname`, `lastname`, `othername`, `title`, `homephone`, `workphone`, `mobile`, `priv_mobile`, `fax`, `email`, `notes`, `dt_created`, `dt_modified`, `is_deleted`, `private_to_user_id`, `creator_id`) VALUES
(1, 'Admin User', 'er', NULL, '', NULL, NULL, NULL, NULL, NULL, 'admin@here.com', NULL, '2015-04-22 11:12:22', '2015-04-22 21:12:22', 0, NULL, 1),
(2, 'Anon User', 'er', NULL, '', NULL, NULL, NULL, NULL, NULL, '', NULL, '2015-04-22 11:12:22', '2015-04-22 21:12:22', 0, NULL, 1),
(3, 'User', 'Er', NULL, '', NULL, NULL, NULL, NULL, NULL, '', NULL, '2015-04-22 00:55:47', '2015-04-22 20:55:47', 0, NULL, 1),

(4, 'Task Admin', 'er', NULL, '', NULL, NULL, NULL, NULL, NULL, '', NULL, '2015-04-22 00:56:20', '2015-04-22 20:56:20', 0, NULL, 1),
(5, 'Task Group', 'Er', NULL, '', NULL, NULL, NULL, NULL, NULL, '', NULL, '2015-04-22 00:55:47', '2015-04-22 20:55:47', 0, NULL, 1),
(6, 'Task User', 'er', NULL, '', NULL, NULL, NULL, NULL, NULL, '', NULL, '2015-04-22 00:56:20', '2015-04-22 20:56:20', 0, NULL, 1),

(7, 'Staff Admin', 'Er', NULL, '', NULL, NULL, NULL, NULL, NULL, 'staffadmin@here.com', NULL, '2015-04-22 00:55:47', '2015-04-22 20:55:47', 0, NULL, 1),
(8, 'Staff User', 'er', NULL, '', NULL, NULL, NULL, NULL, NULL, '', NULL, '2015-04-22 00:56:20', '2015-04-22 20:56:20', 0, NULL, 1),

(9, 'Report Admin', 'Er', NULL, '', NULL, NULL, NULL, NULL, NULL, '', NULL, '2015-04-22 00:55:47', '2015-04-22 20:55:47', 0, NULL, 1),
(10, 'Report Editor', 'er', NULL, '', NULL, NULL, NULL, NULL, NULL, '', NULL, '2015-04-22 00:56:20', '2015-04-22 20:56:20', 0, NULL, 1),
(11, 'Report User', 'Er', NULL, '', NULL, NULL, NULL, NULL, NULL, '', NULL, '2015-04-22 00:55:47', '2015-04-22 20:55:47', 0, NULL, 1),

(12, 'Inbox Sender', 'er', NULL, '', NULL, NULL, NULL, NULL, NULL, '', NULL, '2015-04-22 00:56:20', '2015-04-22 20:56:20', 0, NULL, 1),
(13, 'Inbox Reader', 'Er', NULL, '', NULL, NULL, NULL, NULL, NULL, '', NULL, '2015-04-22 00:55:47', '2015-04-22 20:55:47', 0, NULL, 1),

(14, 'Help View', 'er', NULL, '', NULL, NULL, NULL, NULL, NULL, 'helpview@here.com', NULL, '2015-04-22 00:56:20', '2015-04-22 20:56:20', 0, NULL, 1),
(15, 'Help Contact', 'Er', NULL, '', NULL, NULL, NULL, NULL, NULL, '', NULL, '2015-04-22 00:55:47', '2015-04-22 20:55:47', 0, NULL, 1),

(16, 'File Upload', 'er', NULL, '', NULL, NULL, NULL, NULL, NULL, '', NULL, '2015-04-22 00:56:20', '2015-04-22 20:56:20', 0, NULL, 1),
(17, 'File Download', 'Er', NULL, '', NULL, NULL, NULL, NULL, NULL, '', NULL, '2015-04-22 00:55:47', '2015-04-22 20:55:47', 0, NULL, 1),

(18, 'Favorites User', 'er', NULL, '', NULL, NULL, NULL, NULL, NULL, '', NULL, '2015-04-22 00:56:20', '2015-04-22 20:56:20', 0, NULL, 1),

(19, 'CRM Quote Manager', 'Er', NULL, '', NULL, NULL, NULL, NULL, NULL, '', NULL, '2015-04-22 00:55:47', '2015-04-22 20:55:47', 0, NULL, 1),
(20, 'CRM Project User', 'er', NULL, '', NULL, NULL, NULL, NULL, NULL, '', NULL, '2015-04-22 00:56:20', '2015-04-22 20:56:20', 0, NULL, 1),
(21, 'CRM Project Manager', 'Er', NULL, '', NULL, NULL, NULL, NULL, NULL, '', NULL, '2015-04-22 00:55:47', '2015-04-22 20:55:47', 0, NULL, 1),
(22, 'CRM Opportunity Manager', 'er', NULL, '', NULL, NULL, NULL, NULL, NULL, '', NULL, '2015-04-22 00:56:20', '2015-04-22 20:56:20', 0, NULL, 1),
(23, 'CRM Invoice Manager', 'Er', NULL, '', NULL, NULL, NULL, NULL, NULL, '', NULL, '2015-04-22 00:55:47', '2015-04-22 20:55:47', 0, NULL, 1),
(24, 'CRM Expense User', 'er', NULL, '', NULL, NULL, NULL, NULL, NULL, '', NULL, '2015-04-22 00:56:20', '2015-04-22 20:56:20', 0, NULL, 1),
(25, 'CRM Expense Manager', 'Er', NULL, '', NULL, NULL, NULL, NULL, NULL, '', NULL, '2015-04-22 00:55:47', '2015-04-22 20:55:47', 0, NULL, 1),
(26, 'CRM Contact Editor', 'er', NULL, '', NULL, NULL, NULL, NULL, NULL, '', NULL, '2015-04-22 00:56:20', '2015-04-22 20:56:20', 0, NULL, 1),
(27, 'CRM Contact Viewer', 'Er', NULL, '', NULL, NULL, NULL, NULL, NULL, '', NULL, '2015-04-22 00:55:47', '2015-04-22 20:55:47', 0, NULL, 1),
(28, 'CRM Account Editor', 'er', NULL, '', NULL, NULL, NULL, NULL, NULL, '', NULL, '2015-04-22 00:56:20', '2015-04-22 20:56:20', 0, NULL, 1),
(29, 'CRM Account Viewer', 'Er', NULL, '', NULL, NULL, NULL, NULL, NULL, '', NULL, '2015-04-22 00:55:47', '2015-04-22 20:55:47', 0, NULL, 1),
(30, 'CRM Admin', 'er', NULL, '', NULL, NULL, NULL, NULL, NULL, '', NULL, '2015-04-22 00:56:20', '2015-04-22 20:56:20', 0, NULL, 1),

(31, 'Comment User', 'Er', NULL, '', NULL, NULL, NULL, NULL, NULL, '', NULL, '2015-04-22 00:55:47', '2015-04-22 20:55:47', 0, NULL, 1);

TRUNCATE TABLE `user`;
INSERT INTO `user` (`id`, `login`, `password`, `password_salt`, `contact_id`, `password_reset_token`, `dt_password_reset_at`, `redirect_url`, `is_admin`, `is_active`, `is_deleted`, `is_group`, `dt_created`, `dt_lastlogin`) VALUES
(1, 'admin', 'ca1e51f19afbe6e0fb51dde5bcf01ab73e52c7cd', '9b618fbc7f9509fc28ebea98becfdd58', 1, NULL, NULL, 'main/index', 1, 1, 0, 0, '2012-04-26 00:31:07', '2015-04-22 21:45:58'),
(2, 'anon', '9a890a1e28015df198bcc18a821f50a5f89fda2e', '88cca60e241968888669c8e45703df2f', 2, NULL, NULL, 'main/index', 0, 1, 0, 0, '2015-04-22 00:55:47', NULL),
(3, 'user', '46f7d68834f50a403616c4bb3b5d43f3dcce1381', '21a7f93250f9530a4777c76d17bffe04', 3, NULL, NULL, 'main/index', 0, 1, 0, 0, '2015-04-22 00:56:20', '2015-04-22 22:54:04'),
(4, 'taskadmin', '9a890a1e28015df198bcc18a821f50a5f89fda2e', '88cca60e241968888669c8e45703df2f', 4, NULL, NULL, 'main/index', 0, 1, 0, 0, '2015-04-22 00:55:47', NULL),
(5, 'taskgroup', '46f7d68834f50a403616c4bb3b5d43f3dcce1381', '21a7f93250f9530a4777c76d17bffe04', 5, NULL, NULL, 'main/index', 0, 1, 0, 0, '2015-04-22 00:56:20', '2015-04-22 22:54:04'),
(6, 'taskuser', '9a890a1e28015df198bcc18a821f50a5f89fda2e', '88cca60e241968888669c8e45703df2f', 6, NULL, NULL, 'main/index', 0, 1, 0, 0, '2015-04-22 00:55:47', NULL),
(7, 'staffadmin', '46f7d68834f50a403616c4bb3b5d43f3dcce1381', '21a7f93250f9530a4777c76d17bffe04', 7, NULL, NULL, 'main/index', 0, 1, 0, 0, '2015-04-22 00:56:20', '2015-04-22 22:54:04'),
(8, 'staffuser', '9a890a1e28015df198bcc18a821f50a5f89fda2e', '88cca60e241968888669c8e45703df2f', 8, NULL, NULL, 'main/index', 0, 1, 0, 0, '2015-04-22 00:55:47', NULL),
(9, 'reportadmin', '46f7d68834f50a403616c4bb3b5d43f3dcce1381', '21a7f93250f9530a4777c76d17bffe04', 9, NULL, NULL, 'main/index', 0, 1, 0, 0, '2015-04-22 00:56:20', '2015-04-22 22:54:04'),
(10, 'reporteditor', '46f7d68834f50a403616c4bb3b5d43f3dcce1381', '21a7f93250f9530a4777c76d17bffe04', 10, NULL, NULL, 'main/index', 0, 1, 0, 0, '2015-04-22 00:56:20', '2015-04-22 22:54:04'),
(11, 'reportuser', '9a890a1e28015df198bcc18a821f50a5f89fda2e', '88cca60e241968888669c8e45703df2f', 11, NULL, NULL, 'main/index', 0, 1, 0, 0, '2015-04-22 00:55:47', NULL),
(12, 'inboxsender', '46f7d68834f50a403616c4bb3b5d43f3dcce1381', '21a7f93250f9530a4777c76d17bffe04', 12, NULL, NULL, 'main/index', 0, 1, 0, 0, '2015-04-22 00:56:20', '2015-04-22 22:54:04'),
(13, 'inboxreader', '9a890a1e28015df198bcc18a821f50a5f89fda2e', '88cca60e241968888669c8e45703df2f', 13, NULL, NULL, 'main/index', 0, 1, 0, 0, '2015-04-22 00:55:47', NULL),
(14, 'helpview', '46f7d68834f50a403616c4bb3b5d43f3dcce1381', '21a7f93250f9530a4777c76d17bffe04', 14, NULL, NULL, 'main/index', 0, 1, 0, 0, '2015-04-22 00:56:20', '2015-04-22 22:54:04'),
(15, 'helpcontact', '9a890a1e28015df198bcc18a821f50a5f89fda2e', '88cca60e241968888669c8e45703df2f', 15, NULL, NULL, 'main/index', 0, 1, 0, 0, '2015-04-22 00:55:47', NULL),
(16, 'fileupload', '46f7d68834f50a403616c4bb3b5d43f3dcce1381', '21a7f93250f9530a4777c76d17bffe04', 16, NULL, NULL, 'main/index', 0, 1, 0, 0, '2015-04-22 00:56:20', '2015-04-22 22:54:04'),
(17, 'filedownload', '9a890a1e28015df198bcc18a821f50a5f89fda2e', '88cca60e241968888669c8e45703df2f', 17, NULL, NULL, 'main/index', 0, 1, 0, 0, '2015-04-22 00:55:47', NULL),
(18, 'favoritesuser', '46f7d68834f50a403616c4bb3b5d43f3dcce1381', '21a7f93250f9530a4777c76d17bffe04', 18, NULL, NULL, 'main/index', 0, 1, 0, 0, '2015-04-22 00:56:20', '2015-04-22 22:54:04'),
(19, 'crmquotemanager', '9a890a1e28015df198bcc18a821f50a5f89fda2e', '88cca60e241968888669c8e45703df2f', 19, NULL, NULL, 'main/index', 0, 1, 0, 0, '2015-04-22 00:55:47', NULL),
(20, 'crmprojectuser', '46f7d68834f50a403616c4bb3b5d43f3dcce1381', '21a7f93250f9530a4777c76d17bffe04',20, NULL, NULL, 'main/index', 0, 1, 0, 0, '2015-04-22 00:56:20', '2015-04-22 22:54:04'),
(21, 'crmprojectmanager', '46f7d68834f50a403616c4bb3b5d43f3dcce1381', '21a7f93250f9530a4777c76d17bffe04', 21, NULL, NULL, 'main/index', 0, 1, 0, 0, '2015-04-22 00:56:20', '2015-04-22 22:54:04'),
(22, 'crmopportunitymanager', '9a890a1e28015df198bcc18a821f50a5f89fda2e', '88cca60e241968888669c8e45703df2f', 22, NULL, NULL, 'main/index', 0, 1, 0, 0, '2015-04-22 00:55:47', NULL),
(23, 'crminvoicemanager', '46f7d68834f50a403616c4bb3b5d43f3dcce1381', '21a7f93250f9530a4777c76d17bffe04', 23, NULL, NULL, 'main/index', 0, 1, 0, 0, '2015-04-22 00:56:20', '2015-04-22 22:54:04'),
(24, 'crmexpenseuser', '9a890a1e28015df198bcc18a821f50a5f89fda2e', '88cca60e241968888669c8e45703df2f', 24, NULL, NULL, 'main/index', 0, 1, 0, 0, '2015-04-22 00:55:47', NULL),
(25, 'crmexpensemanager', '46f7d68834f50a403616c4bb3b5d43f3dcce1381', '21a7f93250f9530a4777c76d17bffe04', 25, NULL, NULL, 'main/index', 0, 1, 0, 0, '2015-04-22 00:56:20', '2015-04-22 22:54:04'),
(26, 'crmcontacteditor', '9a890a1e28015df198bcc18a821f50a5f89fda2e', '88cca60e241968888669c8e45703df2f', 26, NULL, NULL, 'main/index', 0, 1, 0, 0, '2015-04-22 00:55:47', NULL),
(27, 'crmcontactviewer', '46f7d68834f50a403616c4bb3b5d43f3dcce1381', '21a7f93250f9530a4777c76d17bffe04', 27, NULL, NULL, 'main/index', 0, 1, 0, 0, '2015-04-22 00:56:20', '2015-04-22 22:54:04'),
(28, 'crmaccounteditor', '9a890a1e28015df198bcc18a821f50a5f89fda2e', '88cca60e241968888669c8e45703df2f', 28, NULL, NULL, 'main/index', 0, 1, 0, 0, '2015-04-22 00:55:47', NULL),
(29, 'crmaccountviewer', '46f7d68834f50a403616c4bb3b5d43f3dcce1381', '21a7f93250f9530a4777c76d17bffe04', 29, NULL, NULL, 'main/index', 0, 1, 0, 0, '2015-04-22 00:56:20', '2015-04-22 22:54:04'),
(30, 'crmadmin', '9a890a1e28015df198bcc18a821f50a5f89fda2e', '88cca60e241968888669c8e45703df2f', 30, NULL, NULL, 'main/index', 0, 1, 0, 0, '2015-04-22 00:55:47', NULL),
(31, 'commentuser', 'b973ac8cc9e5b6fa9ba665bc9a1986da4c69787c', '234a3843bd95485224091356a3cd5132', 31, NULL, NULL, 'main/index', 0, 1, 0, 0, '2015-04-22 11:12:22', NULL);



TRUNCATE TABLE `user_role`;
/*
Everyone has user role plus one other role matching their username.
*/
INSERT INTO `user_role` (`id`, `user_id`, `role`) VALUES
(1, 3, 'user'),
(2, 4, 'user'),
(3, 5, 'user'),
(4, 6, 'user'),
(5, 7, 'user'),
(6, 8, 'user'),
(7, 9, 'user'),
(8, 10, 'user'),
(9, 11, 'user'),
(10, 12, 'user'),
(11, 13, 'user'),
(12, 14, 'user'),
(13, 15, 'user'),
(14, 16, 'user'),
(15, 17, 'user'),
(16, 18, 'user'),
(17, 19, 'user'),
(18, 20, 'user'),
(19, 21, 'user'),
(20, 22, 'user'),
(21, 23, 'user'),
(22, 24, 'user'),
(23, 25, 'user'),
(24, 26, 'user'),
(25, 27, 'user'),
(26, 28, 'user'),
(27, 29, 'user'),
(28, 30, 'user'),
(29, 31, 'user'),
(30, 31, 'comment'),
(31, 28, 'crm_account_editor'),
(32, 29, 'crm_account_viewer'),
(33, 30, 'crm_admin'),
(34, 26, 'crm_contact_editor'),
(35, 27, 'crm_contact_viewer'),
(36, 25, 'crm_expense_manager'),
(37, 24, 'crm_expense_user'),
(38, 23, 'crm_invoice_manager'),
(39, 22, 'crm_opportunity_manager'),
(40, 21, 'crm_project_manager'),
(41, 20, 'crm_project_user'),
(42, 19, 'crm_quote_manager'),
(43, 18, 'favorites_user'),
(44, 17, 'file_download'),
(45, 16, 'file_upload'),
(46, 15, 'help_contact'),
(47, 14, 'help_view'),
(48, 13, 'inbox_reader'),
(49, 12, 'inbox_sender'),
(50, 9, 'report_admin'),
(51, 10, 'report_editor'),
(52, 11, 'report_user'),
(53, 7, 'staff_admin'),
(54, 8, 'staff_user'),
(55, 4, 'task_admin'),
(56, 5, 'task_group'),
(57, 6, 'task_user');
