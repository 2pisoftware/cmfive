

INSERT INTO `contact` (`id`, `firstname`, `lastname`, `othername`, `title`, `homephone`, `workphone`, `mobile`, `priv_mobile`, `fax`, `email`, `notes`, `dt_created`, `dt_modified`, `is_deleted`, `private_to_user_id`, `creator_id`) VALUES
(2, 'Anon User', 'er', NULL, '', NULL, NULL, NULL, NULL, NULL, '', NULL, '2015-04-22 11:12:22', '2015-04-22 21:12:22', 0, NULL, 1),
(3, 'User', 'Er', NULL, '', NULL, NULL, NULL, NULL, NULL, '', NULL, '2015-04-22 00:55:47', '2015-04-22 20:55:47', 0, NULL, 1);

INSERT INTO `user` (`id`, `login`, `password`, `password_salt`, `contact_id`, `password_reset_token`, `dt_password_reset_at`, `redirect_url`, `is_admin`, `is_active`, `is_deleted`, `is_group`, `dt_created`, `dt_lastlogin`) VALUES
(2, 'anon', '9a890a1e28015df198bcc18a821f50a5f89fda2e', '88cca60e241968888669c8e45703df2f', 2, NULL, NULL, 'main/index', 0, 1, 0, 0, '2015-04-22 00:55:47', NULL),
(3, 'user', '46f7d68834f50a403616c4bb3b5d43f3dcce1381', '21a7f93250f9530a4777c76d17bffe04', 3, NULL, NULL, 'main/index', 0, 1, 0, 0, '2015-04-22 00:56:20', '2015-04-22 22:54:04');

/*
Everyone has user role plus one other role matching their username.
*/
INSERT INTO `user_role` (`id`, `user_id`, `role`) VALUES
(1, 3, 'user');
