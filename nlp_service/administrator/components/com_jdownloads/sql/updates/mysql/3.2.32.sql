ALTER TABLE `#__jdownloads_usergroups_limits` ADD `uploads_view_upload_icon` TINYINT( 1 ) NOT NULL DEFAULT '1' AFTER `uploads_default_access_level`;
ALTER TABLE `#__jdownloads_usergroups_limits` ADD `uploads_allow_custom_tags` TINYINT( 1 ) NOT NULL DEFAULT '1' AFTER `uploads_view_upload_icon`;
ALTER TABLE `#__jdownloads_usergroups_limits` ADD `form_tags` TINYINT( 1 ) NOT NULL DEFAULT '0' AFTER `form_robots`;