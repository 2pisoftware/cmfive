/* Replace this file with actual dump of your database */
TRUNCATE TABLE  staff_leave_application;

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

TRUNCATE TABLE `contact`;
INSERT INTO `contact` (`id`, `firstname`, `lastname`, `othername`, `title`, `homephone`, `workphone`, `mobile`, `priv_mobile`, `fax`, `email`, `notes`, `dt_created`, `dt_modified`, `is_deleted`, `private_to_user_id`, `creator_id`) VALUES
(2, 'Staff User', 'Er', NULL, '', NULL, NULL, NULL, NULL, NULL, '', NULL, '2015-04-22 00:55:47', '2015-04-22 20:55:47', 0, NULL, 1),
(3, 'Staff Admin User', 'er', NULL, '', NULL, NULL, NULL, NULL, NULL, '', NULL, '2015-04-22 00:56:20', '2015-04-22 20:56:20', 0, NULL, 1),
(4, 'Anon User', 'er', NULL, '', NULL, NULL, NULL, NULL, NULL, '', NULL, '2015-04-22 11:12:22', '2015-04-22 21:12:22', 0, NULL, 1);

TRUNCATE TABLE `staff_employee`;
INSERT INTO `staff_employee` (`id`, `dt_created`, `dt_modified`, `creator_id`, `modifier_id`, `is_deleted`, `user_id`, `d_birthdate`, `tfn`, `private_email`, `supervisor_staff_id`, `d_start_date`, `d_end_date`, `is_employed`, `address`, `town`, `postcode`, `state`, `rate_normal`, `rate_weekend`, `rate_holiday`, `rate_private_travel`, `superfund_name`, `superfund_number`, `bank_name`, `bank_bsb`, `bank_account_name`, `bank_account_number`) VALUES
(1, '2015-03-22 16:58:31', '2015-04-22 21:14:58', 1, 1, 0, 1, '2015-03-09 00:00:00', '8768768767', '', 0, NULL, NULL, 0, '', '', '', '', '5', '7', '7', '5', '', '', '', '', '', ''),
(2, '2015-03-23 11:00:18', '2015-04-22 21:14:44', 1, 1, 0, 13, NULL, '', '', 1, '2015-03-01 00:00:00', NULL, 1, '', '', '', '', '5', '6', '7', '3', '', '', '', '', '', ''),
(3, '2015-04-22 21:15:07', '2015-04-22 21:15:07', 1, 1, 0, 14, NULL, '', '', 0, NULL, NULL, 0, '', '', '', '', '', '', '', '', '', '', '', '', '', ''),
(4, '2015-04-22 21:15:17', '2015-04-22 21:15:17', 1, 1, 0, 15, NULL, '', '', 0, NULL, NULL, 0, '', '', '', '', '', '', '', '', '', '', '', '', '', '');

TRUNCATE TABLE `user`;
INSERT INTO `user` (`id`, `login`, `password`, `password_salt`, `contact_id`, `password_reset_token`, `dt_password_reset_at`, `redirect_url`, `is_admin`, `is_active`, `is_deleted`, `is_group`, `dt_created`, `dt_lastlogin`) VALUES
(1, 'admin', 'ca1e51f19afbe6e0fb51dde5bcf01ab73e52c7cd', '9b618fbc7f9509fc28ebea98becfdd58', 1, NULL, NULL, 'main/index', 1, 1, 0, 0, '2012-04-26 00:31:07', '2015-04-22 21:45:58'),
(13, 'staffuser', '9a890a1e28015df198bcc18a821f50a5f89fda2e', '88cca60e241968888669c8e45703df2f', 2, NULL, NULL, 'main/index', 0, 1, 0, 0, '2015-04-22 00:55:47', NULL),
(14, 'staffadminuser', '46f7d68834f50a403616c4bb3b5d43f3dcce1381', '21a7f93250f9530a4777c76d17bffe04', 3, NULL, NULL, 'main/index', 0, 1, 0, 0, '2015-04-22 00:56:20', '2015-04-22 22:54:04'),
(15, 'anon', 'b973ac8cc9e5b6fa9ba665bc9a1986da4c69787c', '234a3843bd95485224091356a3cd5132', 4, NULL, NULL, 'main/index', 0, 1, 0, 0, '2015-04-22 11:12:22', NULL);

TRUNCATE TABLE `user_role`;

INSERT INTO `user_role` (`id`, `user_id`, `role`) VALUES
(59, 13, 'comment'),
(76, 13, 'crm_account_editor'),
(75, 13, 'crm_account_viewer'),
(74, 13, 'crm_admin'),
(78, 13, 'crm_contact_editor'),
(77, 13, 'crm_contact_viewer'),
(83, 13, 'crm_expense_manager'),
(82, 13, 'crm_expense_user'),
(80, 13, 'crm_invoice_manager'),
(79, 13, 'crm_opportunity_manager'),
(85, 13, 'crm_project_manager'),
(84, 13, 'crm_project_user'),
(81, 13, 'crm_quote_manager'),
(60, 13, 'favorites_user'),
(62, 13, 'file_download'),
(61, 13, 'file_upload'),
(64, 13, 'help_contact'),
(63, 13, 'help_view'),
(65, 13, 'inbox_reader'),
(66, 13, 'inbox_sender'),
(68, 13, 'report_admin'),
(69, 13, 'report_editor'),
(70, 13, 'report_user'),
(29, 13, 'staff_user'),
(71, 13, 'task_admin'),
(73, 13, 'task_group'),
(72, 13, 'task_user'),
(67, 13, 'user'),
(31, 14, 'comment'),
(48, 14, 'crm_account_editor'),
(47, 14, 'crm_account_viewer'),
(46, 14, 'crm_admin'),
(50, 14, 'crm_contact_editor'),
(49, 14, 'crm_contact_viewer'),
(55, 14, 'crm_expense_manager'),
(54, 14, 'crm_expense_user'),
(52, 14, 'crm_invoice_manager'),
(51, 14, 'crm_opportunity_manager'),
(57, 14, 'crm_project_manager'),
(56, 14, 'crm_project_user'),
(53, 14, 'crm_quote_manager'),
(33, 14, 'favorites_user'),
(35, 14, 'file_download'),
(34, 14, 'file_upload'),
(37, 14, 'help_contact'),
(36, 14, 'help_view'),
(38, 14, 'inbox_reader'),
(39, 14, 'inbox_sender'),
(40, 14, 'report_admin'),
(41, 14, 'report_editor'),
(42, 14, 'report_user'),
(30, 14, 'staff_admin'),
(58, 14, 'staff_user'),
(43, 14, 'task_admin'),
(45, 14, 'task_group'),
(44, 14, 'task_user'),
(32, 14, 'user'),
(86, 15, 'comment'),
(103, 15, 'crm_account_editor'),
(102, 15, 'crm_account_viewer'),
(101, 15, 'crm_admin'),
(105, 15, 'crm_contact_editor'),
(104, 15, 'crm_contact_viewer'),
(110, 15, 'crm_expense_manager'),
(109, 15, 'crm_expense_user'),
(107, 15, 'crm_invoice_manager'),
(106, 15, 'crm_opportunity_manager'),
(112, 15, 'crm_project_manager'),
(111, 15, 'crm_project_user'),
(108, 15, 'crm_quote_manager'),
(87, 15, 'favorites_user'),
(89, 15, 'file_download'),
(88, 15, 'file_upload'),
(91, 15, 'help_contact'),
(90, 15, 'help_view'),
(92, 15, 'inbox_reader'),
(93, 15, 'inbox_sender'),
(95, 15, 'report_admin'),
(96, 15, 'report_editor'),
(97, 15, 'report_user'),
(98, 15, 'task_admin'),
(100, 15, 'task_group'),
(99, 15, 'task_user'),
(94, 15, 'user');
