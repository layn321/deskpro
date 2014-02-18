<?php

$queries = array('create' => array(), 'alter' => array(), 'index' => array(), 'fk' => array(), 'trigger' => array());

$queries['create'][0] = 'CREATE TABLE agent_activity (date_active DATETIME NOT NULL, agent_id INT NOT NULL, INDEX IDX_9AA510CE3414710B (agent_id), INDEX date_created_idx (date_active), PRIMARY KEY(date_active, agent_id)) ENGINE = InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci';
$queries['create'][1] = 'CREATE TABLE agent_alerts (id INT AUTO_INCREMENT NOT NULL, person_id INT NOT NULL, typename VARCHAR(255) NOT NULL, data LONGBLOB NOT NULL COMMENT \'(DC2Type:array)\', date_created DATETIME DEFAULT NULL, is_dismissed TINYINT(1) NOT NULL, INDEX IDX_A99D974D217BBB47 (person_id), INDEX date_created_idx (date_created), PRIMARY KEY(id)) ENGINE = InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci';
$queries['create'][2] = 'CREATE TABLE agent_teams (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id)) ENGINE = InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci';
$queries['create'][3] = 'CREATE TABLE agent_team_members (team_id INT NOT NULL, person_id INT NOT NULL, INDEX IDX_CC952C03296CD8AE (team_id), INDEX IDX_CC952C03217BBB47 (person_id), PRIMARY KEY(team_id, person_id)) ENGINE = InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci';
$queries['create'][4] = 'CREATE TABLE api_keys (id INT AUTO_INCREMENT NOT NULL, person_id INT DEFAULT NULL, code VARCHAR(25) NOT NULL, note LONGTEXT NOT NULL, INDEX IDX_9579321F217BBB47 (person_id), PRIMARY KEY(id)) ENGINE = InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci';
$queries['create'][5] = 'CREATE TABLE api_key_rate_limit (api_key_id INT NOT NULL, hits INT DEFAULT NULL, created_stamp INT DEFAULT NULL, reset_stamp INT DEFAULT NULL, PRIMARY KEY(api_key_id)) ENGINE = InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci';
$queries['create'][6] = 'CREATE TABLE api_token (person_id INT NOT NULL, token VARCHAR(25) NOT NULL, date_expires DATETIME DEFAULT NULL, PRIMARY KEY(person_id)) ENGINE = InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci';
$queries['create'][7] = 'CREATE TABLE api_token_rate_limit (person_id INT NOT NULL, hits INT DEFAULT NULL, created_stamp INT DEFAULT NULL, reset_stamp INT DEFAULT NULL, PRIMARY KEY(person_id)) ENGINE = InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci';
$queries['create'][8] = 'CREATE TABLE articles (id INT AUTO_INCREMENT NOT NULL, person_id INT DEFAULT NULL, language_id INT DEFAULT NULL, date_end DATETIME DEFAULT NULL, end_action VARCHAR(10) DEFAULT NULL, slug VARCHAR(100) NOT NULL, title VARCHAR(255) NOT NULL, content LONGTEXT NOT NULL, view_count INT NOT NULL, total_rating INT NOT NULL, num_comments INT NOT NULL, num_ratings INT NOT NULL, status VARCHAR(15) NOT NULL, hidden_status VARCHAR(15) DEFAULT NULL, date_created DATETIME NOT NULL, date_published DATETIME DEFAULT NULL, date_updated DATETIME DEFAULT NULL, date_last_comment DATETIME DEFAULT NULL, INDEX IDX_BFDD3168217BBB47 (person_id), INDEX IDX_BFDD316882F1BAF4 (language_id), INDEX date_published_idx (date_published), INDEX date_updated_idx (date_updated), INDEX date_last_comment_idx (date_last_comment), INDEX status_idx (status), PRIMARY KEY(id)) ENGINE = InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci';
$queries['create'][9] = 'CREATE TABLE article_to_categories (article_id INT NOT NULL, category_id INT NOT NULL, INDEX IDX_9A1B4BB07294869C (article_id), INDEX IDX_9A1B4BB012469DE2 (category_id), PRIMARY KEY(article_id, category_id)) ENGINE = InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci';
$queries['create'][10] = 'CREATE TABLE article_to_product (article_id INT NOT NULL, product_id INT NOT NULL, INDEX IDX_610BE8D97294869C (article_id), INDEX IDX_610BE8D94584665A (product_id), PRIMARY KEY(article_id, product_id)) ENGINE = InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci';
$queries['create'][11] = 'CREATE TABLE article_attachments (id INT AUTO_INCREMENT NOT NULL, article_id INT DEFAULT NULL, person_id INT DEFAULT NULL, blob_id INT DEFAULT NULL, date_created DATETIME NOT NULL, INDEX IDX_DD4790B17294869C (article_id), INDEX IDX_DD4790B1217BBB47 (person_id), INDEX IDX_DD4790B1ED3E8EA5 (blob_id), PRIMARY KEY(id)) ENGINE = InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci';
$queries['create'][12] = 'CREATE TABLE article_categories (id INT AUTO_INCREMENT NOT NULL, parent_id INT DEFAULT NULL, is_agent TINYINT(1) NOT NULL, is_book TINYINT(1) NOT NULL, template_suffix VARCHAR(100) DEFAULT NULL, title VARCHAR(255) NOT NULL, display_order INT NOT NULL, depth INT NOT NULL, root INT DEFAULT NULL, INDEX IDX_62A97E9727ACA70 (parent_id), PRIMARY KEY(id)) ENGINE = InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci';
$queries['create'][13] = 'CREATE TABLE article_category2usergroup (category_id INT NOT NULL, usergroup_id INT NOT NULL, INDEX IDX_6AD8B03212469DE2 (category_id), INDEX IDX_6AD8B032D2112630 (usergroup_id), PRIMARY KEY(category_id, usergroup_id)) ENGINE = InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci';
$queries['create'][14] = 'CREATE TABLE article_comments (id INT AUTO_INCREMENT NOT NULL, article_id INT DEFAULT NULL, person_id INT DEFAULT NULL, visitor_id INT DEFAULT NULL, ip_address VARCHAR(30) NOT NULL, email VARCHAR(255) DEFAULT NULL, name VARCHAR(255) DEFAULT NULL, website VARCHAR(255) DEFAULT NULL, content LONGTEXT NOT NULL, status VARCHAR(30) NOT NULL, validating VARCHAR(35) DEFAULT NULL, is_reviewed TINYINT(1) NOT NULL, date_created DATETIME NOT NULL, INDEX IDX_A7662417294869C (article_id), INDEX IDX_A766241217BBB47 (person_id), INDEX IDX_A76624170BEE6D (visitor_id), INDEX status_idx (status, is_reviewed), PRIMARY KEY(id)) ENGINE = InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci';
$queries['create'][15] = 'CREATE TABLE article_pending_create (id INT AUTO_INCREMENT NOT NULL, person_id INT DEFAULT NULL, ticket_id INT DEFAULT NULL, ticket_message_id INT DEFAULT NULL, comment LONGTEXT NOT NULL, date_created DATETIME NOT NULL, INDEX IDX_27A971C3217BBB47 (person_id), INDEX IDX_27A971C3700047D2 (ticket_id), INDEX IDX_27A971C3C5E9817D (ticket_message_id), PRIMARY KEY(id)) ENGINE = InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci';
$queries['create'][16] = 'CREATE TABLE article_revisions (id INT AUTO_INCREMENT NOT NULL, article_id INT DEFAULT NULL, person_id INT DEFAULT NULL, title VARCHAR(255) NOT NULL, content LONGTEXT NOT NULL, status VARCHAR(30) NOT NULL, date_created DATETIME NOT NULL, INDEX IDX_538472A17294869C (article_id), INDEX IDX_538472A1217BBB47 (person_id), PRIMARY KEY(id)) ENGINE = InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci';
$queries['create'][17] = 'CREATE TABLE ban_emails (banned_email VARCHAR(255) NOT NULL, is_pattern TINYINT(1) NOT NULL, PRIMARY KEY(banned_email)) ENGINE = InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci';
$queries['create'][18] = 'CREATE TABLE ban_ips (banned_ip VARCHAR(100) NOT NULL, ip_start BIGINT NOT NULL, ip_end BIGINT NOT NULL, PRIMARY KEY(banned_ip)) ENGINE = InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci';
$queries['create'][19] = 'CREATE TABLE blobs (id INT AUTO_INCREMENT NOT NULL, original_blob_id INT DEFAULT NULL, sys_name VARCHAR(100) DEFAULT NULL, storage_loc VARCHAR(50) DEFAULT NULL, storage_loc_pref VARCHAR(50) DEFAULT NULL, save_path VARCHAR(255) DEFAULT NULL, file_url VARCHAR(255) DEFAULT NULL, filename VARCHAR(120) NOT NULL, filesize INT NOT NULL, content_type VARCHAR(50) NOT NULL, authcode VARCHAR(50) NOT NULL, blob_hash VARCHAR(40) NOT NULL, is_media_upload TINYINT(1) NOT NULL, title VARCHAR(255) NOT NULL, dim_w INT NOT NULL, dim_h INT NOT NULL, date_created DATETIME NOT NULL, is_temp TINYINT(1) NOT NULL, date_cleanup DATETIME DEFAULT NULL, INDEX IDX_896C3E356BBE2052 (original_blob_id), INDEX authcode_idx (authcode), INDEX storage_loc_idx (storage_loc, storage_loc_pref), PRIMARY KEY(id)) ENGINE = InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci';
$queries['create'][20] = 'CREATE TABLE blobs_storage (id INT AUTO_INCREMENT NOT NULL, blob_id INT NOT NULL, data LONGBLOB NOT NULL, INDEX blob_id_idx (blob_id), PRIMARY KEY(id)) ENGINE = InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci';
$queries['create'][21] = 'CREATE TABLE cache (id VARCHAR(100) NOT NULL, data LONGBLOB NOT NULL COMMENT \'(DC2Type:array)\', date_expire DATETIME DEFAULT NULL, INDEX date_expire_idx (date_expire), PRIMARY KEY(id)) ENGINE = InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci';
$queries['create'][22] = 'CREATE TABLE chat_blocks (id INT AUTO_INCREMENT NOT NULL, visitor_id INT DEFAULT NULL, by_person_id INT DEFAULT NULL, ip_address VARCHAR(255) NOT NULL, reason LONGTEXT NOT NULL, date_created DATETIME NOT NULL, INDEX IDX_A931A25970BEE6D (visitor_id), INDEX IDX_A931A259B5BE2AA2 (by_person_id), PRIMARY KEY(id)) ENGINE = InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci';
$queries['create'][23] = 'CREATE TABLE chat_conversations (id INT AUTO_INCREMENT NOT NULL, department_id INT DEFAULT NULL, agent_id INT DEFAULT NULL, agent_team_id INT DEFAULT NULL, person_id INT DEFAULT NULL, session_id INT DEFAULT NULL, visitor_id INT DEFAULT NULL, subject VARCHAR(255) NOT NULL, status VARCHAR(15) NOT NULL, person_name VARCHAR(255) NOT NULL, person_email VARCHAR(255) NOT NULL, rating_response_time INT DEFAULT NULL, rating_overall INT DEFAULT NULL, rating_comment LONGTEXT NOT NULL, is_agent TINYINT(1) NOT NULL, is_window TINYINT(1) NOT NULL, date_created DATETIME NOT NULL, date_user_waiting DATETIME DEFAULT NULL, date_assigned DATETIME DEFAULT NULL, date_first_agent_message DATETIME DEFAULT NULL, date_ended DATETIME DEFAULT NULL, should_send_transcript TINYINT(1) NOT NULL, date_transcript_sent DATETIME DEFAULT NULL, total_to_ended INT NOT NULL, ended_by VARCHAR(15) NOT NULL, INDEX IDX_5813432EAE80F5DF (department_id), INDEX IDX_5813432E3414710B (agent_id), INDEX IDX_5813432EFB3FBA04 (agent_team_id), INDEX IDX_5813432E217BBB47 (person_id), INDEX IDX_5813432E613FECDF (session_id), INDEX IDX_5813432E70BEE6D (visitor_id), INDEX status_idx (status), INDEX should_send_transcript_idx (should_send_transcript), PRIMARY KEY(id)) ENGINE = InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci';
$queries['create'][24] = 'CREATE TABLE chat_conversation_to_person (conversation_id INT NOT NULL, person_id INT NOT NULL, INDEX IDX_1CA5AE439AC0396 (conversation_id), INDEX IDX_1CA5AE43217BBB47 (person_id), PRIMARY KEY(conversation_id, person_id)) ENGINE = InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci';
$queries['create'][25] = 'CREATE TABLE chat_conversation_pings (id INT AUTO_INCREMENT NOT NULL, chat_id INT NOT NULL, ping_time INT NOT NULL, INDEX chat_id_idx (chat_id), PRIMARY KEY(id)) ENGINE = InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci';
$queries['create'][26] = 'CREATE TABLE chat_messages (id INT AUTO_INCREMENT NOT NULL, conversation_id INT DEFAULT NULL, author_id INT DEFAULT NULL, tag VARCHAR(255) DEFAULT NULL, origin VARCHAR(50) NOT NULL, person_name VARCHAR(255) NOT NULL, content LONGTEXT NOT NULL, is_sys TINYINT(1) NOT NULL, is_user_hidden TINYINT(1) NOT NULL, is_html TINYINT(1) NOT NULL, metadata LONGBLOB NOT NULL COMMENT \'(DC2Type:array)\', date_created DATETIME NOT NULL, date_received DATETIME DEFAULT NULL, INDEX IDX_EF20C9A69AC0396 (conversation_id), INDEX IDX_EF20C9A6F675F31B (author_id), PRIMARY KEY(id)) ENGINE = InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci';
$queries['create'][27] = 'CREATE TABLE chat_page_display (id INT AUTO_INCREMENT NOT NULL, department_id INT DEFAULT NULL, zone VARCHAR(50) NOT NULL, options LONGBLOB NOT NULL COMMENT \'(DC2Type:array)\', section VARCHAR(50) NOT NULL, data LONGBLOB NOT NULL COMMENT \'(DC2Type:array)\', INDEX IDX_85AF0B7AE80F5DF (department_id), PRIMARY KEY(id)) ENGINE = InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci';
$queries['create'][28] = 'CREATE TABLE client_messages (id INT AUTO_INCREMENT NOT NULL, for_person_id INT DEFAULT NULL, channel VARCHAR(255) NOT NULL, auth VARCHAR(15) NOT NULL, handler_class VARCHAR(255) NOT NULL, data LONGBLOB NOT NULL COMMENT \'(DC2Type:array)\', created_by_client VARCHAR(255) NOT NULL, for_client VARCHAR(255) DEFAULT NULL, date_created DATETIME NOT NULL, INDEX IDX_F5E42E53D2872966 (for_person_id), PRIMARY KEY(id))  AUTO_INCREMENT=2 ENGINE = InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci';
$queries['create'][29] = 'CREATE TABLE content_search_attribute (object_type VARCHAR(100) NOT NULL, object_id INT NOT NULL, attribute_id VARCHAR(200) NOT NULL, content VARCHAR(200) NOT NULL, PRIMARY KEY(object_type, object_id, attribute_id, content)) ENGINE = InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci';
$queries['create'][30] = 'CREATE TABLE content_subscriptions (id INT AUTO_INCREMENT NOT NULL, person_id INT DEFAULT NULL, article_id INT DEFAULT NULL, download_id INT DEFAULT NULL, feedback_id INT DEFAULT NULL, news_id INT DEFAULT NULL, use_email TINYINT(1) NOT NULL, last_dismiss_date DATETIME NOT NULL, last_email_date DATETIME NOT NULL, updated_date DATETIME NOT NULL, INDEX IDX_5FADAC10217BBB47 (person_id), INDEX IDX_5FADAC107294869C (article_id), INDEX IDX_5FADAC10C667AEAB (download_id), INDEX IDX_5FADAC10D249A887 (feedback_id), INDEX IDX_5FADAC10B5A459A0 (news_id), PRIMARY KEY(id)) ENGINE = InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci';
$queries['create'][31] = 'CREATE TABLE custom_data_article (id INT AUTO_INCREMENT NOT NULL, article_id INT DEFAULT NULL, field_id INT DEFAULT NULL, root_field_id INT DEFAULT NULL, value INT NOT NULL, input LONGTEXT NOT NULL, INDEX IDX_1DB64F8C7294869C (article_id), INDEX IDX_1DB64F8C443707B0 (field_id), INDEX IDX_1DB64F8C3F6A6D56 (root_field_id), INDEX field_id_idx (field_id, article_id), PRIMARY KEY(id)) ENGINE = InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci';
$queries['create'][32] = 'CREATE TABLE custom_data_chat (id INT AUTO_INCREMENT NOT NULL, conversation_id INT DEFAULT NULL, field_id INT DEFAULT NULL, root_field_id INT DEFAULT NULL, value INT NOT NULL, input LONGTEXT NOT NULL, INDEX IDX_94E84EEE9AC0396 (conversation_id), INDEX IDX_94E84EEE443707B0 (field_id), INDEX IDX_94E84EEE3F6A6D56 (root_field_id), INDEX field_id_idx (field_id, conversation_id), PRIMARY KEY(id)) ENGINE = InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci';
$queries['create'][33] = 'CREATE TABLE custom_data_feedback (id INT AUTO_INCREMENT NOT NULL, feedback_id INT DEFAULT NULL, field_id INT DEFAULT NULL, root_field_id INT DEFAULT NULL, value INT NOT NULL, input LONGTEXT NOT NULL, INDEX IDX_92E9C37FD249A887 (feedback_id), INDEX IDX_92E9C37F443707B0 (field_id), INDEX IDX_92E9C37F3F6A6D56 (root_field_id), INDEX field_id_idx (field_id, feedback_id), PRIMARY KEY(id)) ENGINE = InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci';
$queries['create'][34] = 'CREATE TABLE custom_data_organizations (id INT AUTO_INCREMENT NOT NULL, organization_id INT DEFAULT NULL, field_id INT DEFAULT NULL, root_field_id INT DEFAULT NULL, value INT NOT NULL, input LONGTEXT NOT NULL, INDEX IDX_20C5B8AC32C8A3DE (organization_id), INDEX IDX_20C5B8AC443707B0 (field_id), INDEX IDX_20C5B8AC3F6A6D56 (root_field_id), INDEX field_id_idx (field_id, organization_id), PRIMARY KEY(id)) ENGINE = InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci';
$queries['create'][35] = 'CREATE TABLE custom_data_person (id INT AUTO_INCREMENT NOT NULL, person_id INT DEFAULT NULL, field_id INT DEFAULT NULL, root_field_id INT DEFAULT NULL, value INT NOT NULL, input LONGTEXT NOT NULL, INDEX IDX_621E55A5217BBB47 (person_id), INDEX IDX_621E55A5443707B0 (field_id), INDEX IDX_621E55A53F6A6D56 (root_field_id), INDEX field_id_idx (field_id, person_id), PRIMARY KEY(id)) ENGINE = InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci';
$queries['create'][36] = 'CREATE TABLE custom_data_product (id INT AUTO_INCREMENT NOT NULL, product_id INT DEFAULT NULL, field_id INT DEFAULT NULL, root_field_id INT DEFAULT NULL, value INT NOT NULL, input LONGTEXT NOT NULL, INDEX IDX_CCC645474584665A (product_id), INDEX IDX_CCC64547443707B0 (field_id), INDEX IDX_CCC645473F6A6D56 (root_field_id), INDEX field_id_idx (field_id, product_id), PRIMARY KEY(id)) ENGINE = InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci';
$queries['create'][37] = 'CREATE TABLE custom_data_ticket (id INT AUTO_INCREMENT NOT NULL, ticket_id INT DEFAULT NULL, field_id INT DEFAULT NULL, root_field_id INT DEFAULT NULL, value INT NOT NULL, input LONGTEXT NOT NULL, INDEX IDX_C1622970700047D2 (ticket_id), INDEX IDX_C1622970443707B0 (field_id), INDEX IDX_C16229703F6A6D56 (root_field_id), INDEX field_id_idx (field_id, ticket_id), PRIMARY KEY(id)) ENGINE = InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci';
$queries['create'][38] = 'CREATE TABLE custom_def_article (id INT AUTO_INCREMENT NOT NULL, parent_id INT DEFAULT NULL, plugin_id VARCHAR(255) DEFAULT NULL, js_class VARCHAR(255) NOT NULL, has_form_template TINYINT(1) NOT NULL, has_display_template TINYINT(1) NOT NULL, title VARCHAR(255) NOT NULL, description LONGTEXT NOT NULL, handler_class VARCHAR(255) DEFAULT NULL, options LONGBLOB NOT NULL COMMENT \'(DC2Type:array)\', is_user_enabled TINYINT(1) NOT NULL, is_enabled TINYINT(1) NOT NULL, display_order INT NOT NULL, default_value VARCHAR(500) DEFAULT NULL, is_agent_field TINYINT(1) NOT NULL, INDEX IDX_B651E6F4727ACA70 (parent_id), INDEX IDX_B651E6F4EC942BCF (plugin_id), PRIMARY KEY(id)) ENGINE = InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci';
$queries['create'][39] = 'CREATE TABLE custom_def_chat (id INT AUTO_INCREMENT NOT NULL, parent_id INT DEFAULT NULL, plugin_id VARCHAR(255) DEFAULT NULL, js_class VARCHAR(255) NOT NULL, has_form_template TINYINT(1) NOT NULL, has_display_template TINYINT(1) NOT NULL, title VARCHAR(255) NOT NULL, description LONGTEXT NOT NULL, handler_class VARCHAR(255) DEFAULT NULL, options LONGBLOB NOT NULL COMMENT \'(DC2Type:array)\', is_user_enabled TINYINT(1) NOT NULL, is_enabled TINYINT(1) NOT NULL, display_order INT NOT NULL, default_value VARCHAR(500) DEFAULT NULL, is_agent_field TINYINT(1) NOT NULL, INDEX IDX_2DE86CE5727ACA70 (parent_id), INDEX IDX_2DE86CE5EC942BCF (plugin_id), PRIMARY KEY(id)) ENGINE = InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci';
$queries['create'][40] = 'CREATE TABLE custom_def_feedback (id INT AUTO_INCREMENT NOT NULL, parent_id INT DEFAULT NULL, plugin_id VARCHAR(255) DEFAULT NULL, sys_name VARCHAR(100) DEFAULT NULL, js_class VARCHAR(255) NOT NULL, has_form_template TINYINT(1) NOT NULL, has_display_template TINYINT(1) NOT NULL, title VARCHAR(255) NOT NULL, description LONGTEXT NOT NULL, handler_class VARCHAR(255) DEFAULT NULL, options LONGBLOB NOT NULL COMMENT \'(DC2Type:array)\', is_user_enabled TINYINT(1) NOT NULL, is_enabled TINYINT(1) NOT NULL, display_order INT NOT NULL, default_value VARCHAR(500) DEFAULT NULL, is_agent_field TINYINT(1) NOT NULL, INDEX IDX_CC9CDDD8727ACA70 (parent_id), INDEX IDX_CC9CDDD8EC942BCF (plugin_id), PRIMARY KEY(id)) ENGINE = InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci';
$queries['create'][41] = 'CREATE TABLE custom_def_organizations (id INT AUTO_INCREMENT NOT NULL, parent_id INT DEFAULT NULL, plugin_id VARCHAR(255) DEFAULT NULL, js_class VARCHAR(255) NOT NULL, has_form_template TINYINT(1) NOT NULL, has_display_template TINYINT(1) NOT NULL, title VARCHAR(255) NOT NULL, description LONGTEXT NOT NULL, handler_class VARCHAR(255) DEFAULT NULL, options LONGBLOB NOT NULL COMMENT \'(DC2Type:array)\', is_user_enabled TINYINT(1) NOT NULL, is_enabled TINYINT(1) NOT NULL, display_order INT NOT NULL, default_value VARCHAR(500) DEFAULT NULL, is_agent_field TINYINT(1) NOT NULL, INDEX IDX_240601E7727ACA70 (parent_id), INDEX IDX_240601E7EC942BCF (plugin_id), PRIMARY KEY(id)) ENGINE = InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci';
$queries['create'][42] = 'CREATE TABLE custom_def_people (id INT AUTO_INCREMENT NOT NULL, parent_id INT DEFAULT NULL, plugin_id VARCHAR(255) DEFAULT NULL, js_class VARCHAR(255) NOT NULL, has_form_template TINYINT(1) NOT NULL, has_display_template TINYINT(1) NOT NULL, title VARCHAR(255) NOT NULL, description LONGTEXT NOT NULL, handler_class VARCHAR(255) DEFAULT NULL, options LONGBLOB NOT NULL COMMENT \'(DC2Type:array)\', is_user_enabled TINYINT(1) NOT NULL, is_enabled TINYINT(1) NOT NULL, display_order INT NOT NULL, default_value VARCHAR(500) DEFAULT NULL, is_agent_field TINYINT(1) NOT NULL, INDEX IDX_4840CFDA727ACA70 (parent_id), INDEX IDX_4840CFDAEC942BCF (plugin_id), PRIMARY KEY(id)) ENGINE = InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci';
$queries['create'][43] = 'CREATE TABLE custom_def_products (id INT AUTO_INCREMENT NOT NULL, parent_id INT DEFAULT NULL, plugin_id VARCHAR(255) DEFAULT NULL, js_class VARCHAR(255) NOT NULL, has_form_template TINYINT(1) NOT NULL, has_display_template TINYINT(1) NOT NULL, title VARCHAR(255) NOT NULL, description LONGTEXT NOT NULL, handler_class VARCHAR(255) DEFAULT NULL, options LONGBLOB NOT NULL COMMENT \'(DC2Type:array)\', is_user_enabled TINYINT(1) NOT NULL, is_enabled TINYINT(1) NOT NULL, display_order INT NOT NULL, default_value VARCHAR(500) DEFAULT NULL, is_agent_field TINYINT(1) NOT NULL, INDEX IDX_AD0FC3DA727ACA70 (parent_id), INDEX IDX_AD0FC3DAEC942BCF (plugin_id), PRIMARY KEY(id)) ENGINE = InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci';
$queries['create'][44] = 'CREATE TABLE custom_def_ticket (id INT AUTO_INCREMENT NOT NULL, parent_id INT DEFAULT NULL, plugin_id VARCHAR(255) DEFAULT NULL, js_class VARCHAR(255) NOT NULL, has_form_template TINYINT(1) NOT NULL, has_display_template TINYINT(1) NOT NULL, title VARCHAR(255) NOT NULL, description LONGTEXT NOT NULL, handler_class VARCHAR(255) DEFAULT NULL, options LONGBLOB NOT NULL COMMENT \'(DC2Type:array)\', is_user_enabled TINYINT(1) NOT NULL, is_enabled TINYINT(1) NOT NULL, display_order INT NOT NULL, default_value VARCHAR(500) DEFAULT NULL, is_agent_field TINYINT(1) NOT NULL, INDEX IDX_F7F6085F727ACA70 (parent_id), INDEX IDX_F7F6085FEC942BCF (plugin_id), PRIMARY KEY(id)) ENGINE = InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci';
$queries['create'][45] = 'CREATE TABLE datastore (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(100) DEFAULT NULL, auth VARCHAR(15) NOT NULL, data LONGBLOB NOT NULL COMMENT \'(DC2Type:array)\', INDEX name_idx (name), PRIMARY KEY(id)) ENGINE = InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci';
$queries['create'][46] = 'CREATE TABLE departments (id INT AUTO_INCREMENT NOT NULL, parent_id INT DEFAULT NULL, email_gateway_id INT DEFAULT NULL, rate NUMERIC(11, 2) NOT NULL, title VARCHAR(255) NOT NULL, user_title VARCHAR(255) NOT NULL, is_tickets_enabled TINYINT(1) NOT NULL, is_chat_enabled TINYINT(1) NOT NULL, display_order INT NOT NULL, INDEX IDX_16AEB8D4727ACA70 (parent_id), INDEX IDX_16AEB8D4FBCC7CDF (email_gateway_id), PRIMARY KEY(id)) ENGINE = InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci';
$queries['create'][47] = 'CREATE TABLE department_permissions (id INT AUTO_INCREMENT NOT NULL, department_id INT DEFAULT NULL, usergroup_id INT DEFAULT NULL, person_id INT DEFAULT NULL, app VARCHAR(50) NOT NULL, name VARCHAR(50) NOT NULL, value LONGTEXT DEFAULT NULL, INDEX IDX_84C36B30AE80F5DF (department_id), INDEX IDX_84C36B30D2112630 (usergroup_id), INDEX IDX_84C36B30217BBB47 (person_id), PRIMARY KEY(id)) ENGINE = InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci';
$queries['create'][48] = 'CREATE TABLE downloads (id INT AUTO_INCREMENT NOT NULL, category_id INT DEFAULT NULL, blob_id INT DEFAULT NULL, person_id INT DEFAULT NULL, language_id INT DEFAULT NULL, num_downloads INT NOT NULL, slug VARCHAR(100) NOT NULL, title VARCHAR(255) NOT NULL, fileurl VARCHAR(255) DEFAULT NULL, filename VARCHAR(255) DEFAULT NULL, filesize INT DEFAULT NULL, content LONGTEXT NOT NULL, view_count INT NOT NULL, total_rating INT NOT NULL, num_comments INT NOT NULL, num_ratings INT NOT NULL, status VARCHAR(15) NOT NULL, hidden_status VARCHAR(15) DEFAULT NULL, date_created DATETIME NOT NULL, date_published DATETIME DEFAULT NULL, INDEX IDX_4B73A4B512469DE2 (category_id), INDEX IDX_4B73A4B5ED3E8EA5 (blob_id), INDEX IDX_4B73A4B5217BBB47 (person_id), INDEX IDX_4B73A4B582F1BAF4 (language_id), INDEX date_published_idx (date_published), INDEX status_idx (status), PRIMARY KEY(id)) ENGINE = InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci';
$queries['create'][49] = 'CREATE TABLE download_categories (id INT AUTO_INCREMENT NOT NULL, parent_id INT DEFAULT NULL, title VARCHAR(255) NOT NULL, display_order INT NOT NULL, depth INT NOT NULL, root INT DEFAULT NULL, INDEX IDX_3317F15727ACA70 (parent_id), PRIMARY KEY(id)) ENGINE = InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci';
$queries['create'][50] = 'CREATE TABLE download_category2usergroup (category_id INT NOT NULL, usergroup_id INT NOT NULL, INDEX IDX_53A2246F12469DE2 (category_id), INDEX IDX_53A2246FD2112630 (usergroup_id), PRIMARY KEY(category_id, usergroup_id)) ENGINE = InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci';
$queries['create'][51] = 'CREATE TABLE download_comments (id INT AUTO_INCREMENT NOT NULL, download_id INT DEFAULT NULL, person_id INT DEFAULT NULL, visitor_id INT DEFAULT NULL, ip_address VARCHAR(30) NOT NULL, email VARCHAR(255) DEFAULT NULL, name VARCHAR(255) DEFAULT NULL, website VARCHAR(255) DEFAULT NULL, content LONGTEXT NOT NULL, status VARCHAR(30) NOT NULL, validating VARCHAR(35) DEFAULT NULL, is_reviewed TINYINT(1) NOT NULL, date_created DATETIME NOT NULL, INDEX IDX_B43CDE14C667AEAB (download_id), INDEX IDX_B43CDE14217BBB47 (person_id), INDEX IDX_B43CDE1470BEE6D (visitor_id), INDEX status_idx (status, is_reviewed), PRIMARY KEY(id)) ENGINE = InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci';
$queries['create'][52] = 'CREATE TABLE download_revisions (id INT AUTO_INCREMENT NOT NULL, download_id INT DEFAULT NULL, blob_id INT DEFAULT NULL, person_id INT DEFAULT NULL, title VARCHAR(255) NOT NULL, content LONGTEXT NOT NULL, status VARCHAR(30) NOT NULL, date_created DATETIME NOT NULL, INDEX IDX_483B9D66C667AEAB (download_id), INDEX IDX_483B9D66ED3E8EA5 (blob_id), INDEX IDX_483B9D66217BBB47 (person_id), PRIMARY KEY(id)) ENGINE = InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci';
$queries['create'][53] = 'CREATE TABLE drafts (id INT AUTO_INCREMENT NOT NULL, person_id INT DEFAULT NULL, content_type VARCHAR(50) NOT NULL, content_id INT NOT NULL, date_created DATETIME NOT NULL, message LONGTEXT NOT NULL, message_html LONGTEXT NOT NULL, extras LONGBLOB NOT NULL COMMENT \'(DC2Type:array)\', INDEX IDX_EC2AE4C0217BBB47 (person_id), INDEX content_idx (content_type, content_id), INDEX date_idx (date_created), UNIQUE INDEX person_content_idx (person_id, content_type, content_id), PRIMARY KEY(id)) ENGINE = InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci';
$queries['create'][54] = 'CREATE TABLE email_gateways (id INT AUTO_INCREMENT NOT NULL, linked_transport_id INT DEFAULT NULL, department_id INT DEFAULT NULL, title TINYTEXT NOT NULL, connection_type VARCHAR(15) NOT NULL, connection_options LONGBLOB NOT NULL COMMENT \'(DC2Type:array)\', gateway_type VARCHAR(15) NOT NULL, is_enabled TINYINT(1) NOT NULL, start_date_limit DATETIME DEFAULT NULL, keep_read TINYINT(1) NOT NULL, date_last_check DATETIME DEFAULT NULL, processor_extras LONGBLOB DEFAULT NULL COMMENT \'(DC2Type:array)\', INDEX IDX_D0C6423237308465 (linked_transport_id), INDEX IDX_D0C64232AE80F5DF (department_id), PRIMARY KEY(id)) ENGINE = InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci';
$queries['create'][55] = 'CREATE TABLE email_gateway_addresses (id INT AUTO_INCREMENT NOT NULL, email_gateway_id INT DEFAULT NULL, match_type VARCHAR(15) NOT NULL, match_pattern VARCHAR(255) NOT NULL, run_order INT NOT NULL, INDEX IDX_EC270D12FBCC7CDF (email_gateway_id), PRIMARY KEY(id)) ENGINE = InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci';
$queries['create'][56] = 'CREATE TABLE email_sources (id INT AUTO_INCREMENT NOT NULL, blob_id INT DEFAULT NULL, gateway_id INT DEFAULT NULL, uid VARCHAR(100) DEFAULT NULL, object_type VARCHAR(50) NOT NULL, object_id INT NOT NULL, headers LONGTEXT NOT NULL, header_to LONGTEXT NOT NULL, header_from LONGTEXT NOT NULL, header_subject LONGTEXT NOT NULL, status VARCHAR(15) NOT NULL, error_code VARCHAR(80) DEFAULT NULL, source_info LONGBLOB DEFAULT NULL COMMENT \'(DC2Type:array)\', date_status DATETIME NOT NULL, date_created DATETIME NOT NULL, INDEX IDX_6F9D0D3DED3E8EA5 (blob_id), INDEX IDX_6F9D0D3D577F8E00 (gateway_id), INDEX date_created (date_created), INDEX object_idx (object_type, object_id), INDEX status_idx (status), PRIMARY KEY(id)) ENGINE = InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci';
$queries['create'][57] = 'CREATE TABLE email_transports (id INT AUTO_INCREMENT NOT NULL, title VARCHAR(255) NOT NULL, match_type VARCHAR(15) NOT NULL, match_pattern VARCHAR(255) NOT NULL, transport_type VARCHAR(80) NOT NULL, transport_options LONGBLOB NOT NULL COMMENT \'(DC2Type:array)\', run_order INT NOT NULL, PRIMARY KEY(id)) ENGINE = InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci';
$queries['create'][58] = 'CREATE TABLE email_uids (id VARCHAR(100) NOT NULL, gateway_id INT DEFAULT NULL, date_created DATETIME NOT NULL, INDEX IDX_6D08D1BD577F8E00 (gateway_id), PRIMARY KEY(id)) ENGINE = InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci';
$queries['create'][59] = 'CREATE TABLE feedback (id INT AUTO_INCREMENT NOT NULL, status_category_id INT DEFAULT NULL, category_id INT DEFAULT NULL, person_id INT DEFAULT NULL, language_id INT DEFAULT NULL, hidden_status VARCHAR(15) DEFAULT NULL, validating VARCHAR(35) DEFAULT NULL, popularity INT NOT NULL, slug VARCHAR(100) NOT NULL, title VARCHAR(255) NOT NULL, content LONGTEXT NOT NULL, view_count INT NOT NULL, total_rating INT NOT NULL, num_comments INT NOT NULL, num_ratings INT NOT NULL, status VARCHAR(15) NOT NULL, date_created DATETIME NOT NULL, date_published DATETIME DEFAULT NULL, INDEX IDX_D2294458169CE813 (status_category_id), INDEX IDX_D229445812469DE2 (category_id), INDEX IDX_D2294458217BBB47 (person_id), INDEX IDX_D229445882F1BAF4 (language_id), INDEX date_published_idx (date_published), INDEX status_idx (status), PRIMARY KEY(id)) ENGINE = InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci';
$queries['create'][60] = 'CREATE TABLE feedback_attachments (id INT AUTO_INCREMENT NOT NULL, feedback_id INT DEFAULT NULL, person_id INT DEFAULT NULL, blob_id INT DEFAULT NULL, date_created DATETIME NOT NULL, INDEX IDX_CC264F12D249A887 (feedback_id), INDEX IDX_CC264F12217BBB47 (person_id), INDEX IDX_CC264F12ED3E8EA5 (blob_id), PRIMARY KEY(id)) ENGINE = InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci';
$queries['create'][61] = 'CREATE TABLE feedback_categories (id INT AUTO_INCREMENT NOT NULL, parent_id INT DEFAULT NULL, title VARCHAR(255) NOT NULL, display_order INT NOT NULL, depth INT NOT NULL, root INT DEFAULT NULL, INDEX IDX_66FE6832727ACA70 (parent_id), PRIMARY KEY(id)) ENGINE = InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci';
$queries['create'][62] = 'CREATE TABLE feedback_category2usergroup (category_id INT NOT NULL, usergroup_id INT NOT NULL, INDEX IDX_B304B93C12469DE2 (category_id), INDEX IDX_B304B93CD2112630 (usergroup_id), PRIMARY KEY(category_id, usergroup_id)) ENGINE = InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci';
$queries['create'][63] = 'CREATE TABLE feedback_comments (id INT AUTO_INCREMENT NOT NULL, feedback_id INT DEFAULT NULL, person_id INT DEFAULT NULL, visitor_id INT DEFAULT NULL, ip_address VARCHAR(30) NOT NULL, email VARCHAR(255) DEFAULT NULL, name VARCHAR(255) DEFAULT NULL, website VARCHAR(255) DEFAULT NULL, content LONGTEXT NOT NULL, status VARCHAR(30) NOT NULL, validating VARCHAR(35) DEFAULT NULL, is_reviewed TINYINT(1) NOT NULL, date_created DATETIME NOT NULL, INDEX IDX_10D03D58D249A887 (feedback_id), INDEX IDX_10D03D58217BBB47 (person_id), INDEX IDX_10D03D5870BEE6D (visitor_id), INDEX status_idx (status, is_reviewed), PRIMARY KEY(id)) ENGINE = InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci';
$queries['create'][64] = 'CREATE TABLE feedback_revisions (id INT AUTO_INCREMENT NOT NULL, feedback_id INT DEFAULT NULL, person_id INT DEFAULT NULL, title VARCHAR(255) NOT NULL, content LONGTEXT NOT NULL, status VARCHAR(30) NOT NULL, date_created DATETIME NOT NULL, INDEX IDX_37F57C3ED249A887 (feedback_id), INDEX IDX_37F57C3E217BBB47 (person_id), PRIMARY KEY(id)) ENGINE = InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci';
$queries['create'][65] = 'CREATE TABLE feedback_status_categories (id INT AUTO_INCREMENT NOT NULL, status_type VARCHAR(255) NOT NULL, title VARCHAR(255) NOT NULL, display_order INT NOT NULL, PRIMARY KEY(id)) ENGINE = InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci';
$queries['create'][66] = 'CREATE TABLE glossary_words (id INT AUTO_INCREMENT NOT NULL, definition_id INT NOT NULL, word VARCHAR(255) NOT NULL, UNIQUE INDEX UNIQ_1A8003DAC3F17511 (word), INDEX IDX_1A8003DAD11EA911 (definition_id), PRIMARY KEY(id)) ENGINE = InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci';
$queries['create'][67] = 'CREATE TABLE glossary_word_definitions (id INT AUTO_INCREMENT NOT NULL, definition LONGTEXT NOT NULL, PRIMARY KEY(id)) ENGINE = InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci';
$queries['create'][68] = 'CREATE TABLE import_datastore (typename VARBINARY(80) NOT NULL, data LONGBLOB NOT NULL COMMENT \'(DC2Type:array)\', PRIMARY KEY(typename)) ENGINE = InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci';
$queries['create'][69] = 'CREATE TABLE import_map (typename VARBINARY(80) NOT NULL, old_id VARBINARY(80) NOT NULL, new_id VARBINARY(80) NOT NULL, PRIMARY KEY(typename, old_id)) ENGINE = InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci';
$queries['create'][70] = 'CREATE TABLE kb_subscriptions (id INT AUTO_INCREMENT NOT NULL, person_id INT DEFAULT NULL, article_id INT DEFAULT NULL, category_id INT DEFAULT NULL, INDEX IDX_1F05AAF5217BBB47 (person_id), INDEX IDX_1F05AAF57294869C (article_id), INDEX IDX_1F05AAF512469DE2 (category_id), PRIMARY KEY(id)) ENGINE = InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci';
$queries['create'][71] = 'CREATE TABLE labels_articles (article_id INT NOT NULL, `label` VARCHAR(255) NOT NULL, INDEX IDX_2F30AF707294869C (article_id), INDEX label_idx (label), PRIMARY KEY(article_id, label)) ENGINE = InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci';
$queries['create'][72] = 'CREATE TABLE labels_blobs (blob_id INT NOT NULL, `label` VARCHAR(255) NOT NULL, INDEX IDX_EC63B2F0ED3E8EA5 (blob_id), INDEX label_idx (label), PRIMARY KEY(blob_id, label)) ENGINE = InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci';
$queries['create'][73] = 'CREATE TABLE labels_chat_conversations (chat_id INT NOT NULL, `label` VARCHAR(255) NOT NULL, INDEX IDX_99205D121A9A7125 (chat_id), INDEX label_idx (label), PRIMARY KEY(chat_id, label)) ENGINE = InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci';
$queries['create'][74] = 'CREATE TABLE label_defs (label_type VARCHAR(50) NOT NULL, `label` VARCHAR(255) NOT NULL, total INT NOT NULL, INDEX type_total_idx (label_type, total), PRIMARY KEY(label_type, label)) ENGINE = InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci';
$queries['create'][75] = 'CREATE TABLE labels_downloads (download_id INT NOT NULL, `label` VARCHAR(255) NOT NULL, INDEX IDX_588FD17DC667AEAB (download_id), INDEX label_idx (label), PRIMARY KEY(download_id, label)) ENGINE = InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci';
$queries['create'][76] = 'CREATE TABLE labels_feedback (feedback_id INT NOT NULL, `label` VARCHAR(255) NOT NULL, INDEX IDX_42C4DA40D249A887 (feedback_id), INDEX label_idx (label), PRIMARY KEY(feedback_id, label)) ENGINE = InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci';
$queries['create'][77] = 'CREATE TABLE labels_news (news_id INT NOT NULL, `label` VARCHAR(255) NOT NULL, INDEX IDX_A2869A08B5A459A0 (news_id), INDEX label_idx (label), PRIMARY KEY(news_id, label)) ENGINE = InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci';
$queries['create'][78] = 'CREATE TABLE labels_organizations (organization_id INT NOT NULL, `label` VARCHAR(255) NOT NULL, INDEX IDX_9F089F4232C8A3DE (organization_id), INDEX label_idx (label), PRIMARY KEY(organization_id, label)) ENGINE = InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci';
$queries['create'][79] = 'CREATE TABLE labels_people (person_id INT NOT NULL, `label` VARCHAR(255) NOT NULL, INDEX IDX_C37D5395217BBB47 (person_id), INDEX label_idx (label), PRIMARY KEY(person_id, label)) ENGINE = InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci';
$queries['create'][80] = 'CREATE TABLE labels_tasks (task_id INT NOT NULL, `label` VARCHAR(255) NOT NULL, INDEX IDX_3557E9528DB60186 (task_id), INDEX label_idx (label), PRIMARY KEY(task_id, label)) ENGINE = InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci';
$queries['create'][81] = 'CREATE TABLE labels_tickets (ticket_id INT NOT NULL, `label` VARCHAR(255) NOT NULL, INDEX IDX_6C514FB700047D2 (ticket_id), INDEX label_idx (label), PRIMARY KEY(ticket_id, label)) ENGINE = InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci';
$queries['create'][82] = 'CREATE TABLE languages (id INT AUTO_INCREMENT NOT NULL, sys_name VARCHAR(100) NOT NULL, lang_code VARCHAR(3) NOT NULL, title VARCHAR(255) NOT NULL, base_filepath VARCHAR(255) DEFAULT NULL, locale VARCHAR(8) NOT NULL, flag_image VARCHAR(50) NOT NULL, is_rtl TINYINT(1) NOT NULL, has_user TINYINT(1) NOT NULL, has_agent TINYINT(1) NOT NULL, has_admin TINYINT(1) NOT NULL, PRIMARY KEY(id)) ENGINE = InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci';
$queries['create'][83] = 'CREATE TABLE login_log (id INT AUTO_INCREMENT NOT NULL, person_id INT DEFAULT NULL, area VARCHAR(20) NOT NULL, is_success TINYINT(1) NOT NULL, ip_address VARCHAR(20) NOT NULL, hostname VARCHAR(20) NOT NULL, user_agent VARCHAR(255) NOT NULL, note VARCHAR(1000) NOT NULL, via_cookie TINYINT(1) NOT NULL, date_created DATETIME NOT NULL, INDEX IDX_F16D9FFF217BBB47 (person_id), PRIMARY KEY(id)) ENGINE = InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci';
$queries['create'][84] = 'CREATE TABLE log_items (id INT AUTO_INCREMENT NOT NULL, log_name VARCHAR(50) NOT NULL, session_name VARCHAR(100) DEFAULT NULL, flag VARCHAR(50) DEFAULT NULL, priority INT NOT NULL, priority_name VARCHAR(25) NOT NULL, message LONGTEXT NOT NULL, data LONGBLOB DEFAULT NULL COMMENT \'(DC2Type:array)\', date_created DATETIME NOT NULL, INDEX log_name_idx (log_name, session_name), INDEX flag_idx (flag), PRIMARY KEY(id)) ENGINE = InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci';
$queries['create'][85] = 'CREATE TABLE news (id INT AUTO_INCREMENT NOT NULL, category_id INT DEFAULT NULL, person_id INT DEFAULT NULL, language_id INT DEFAULT NULL, slug VARCHAR(100) NOT NULL, title VARCHAR(255) NOT NULL, content LONGTEXT NOT NULL, view_count INT NOT NULL, total_rating INT NOT NULL, num_comments INT NOT NULL, num_ratings INT NOT NULL, status VARCHAR(15) NOT NULL, hidden_status VARCHAR(15) DEFAULT NULL, date_created DATETIME NOT NULL, date_published DATETIME DEFAULT NULL, INDEX IDX_1DD3995012469DE2 (category_id), INDEX IDX_1DD39950217BBB47 (person_id), INDEX IDX_1DD3995082F1BAF4 (language_id), INDEX date_published_idx (date_published), INDEX status_idx (status), PRIMARY KEY(id)) ENGINE = InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci';
$queries['create'][86] = 'CREATE TABLE news_categories (id INT AUTO_INCREMENT NOT NULL, parent_id INT DEFAULT NULL, title VARCHAR(255) NOT NULL, display_order INT NOT NULL, depth INT NOT NULL, root INT DEFAULT NULL, INDEX IDX_D68C9111727ACA70 (parent_id), PRIMARY KEY(id)) ENGINE = InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci';
$queries['create'][87] = 'CREATE TABLE news_category2usergroup (category_id INT NOT NULL, usergroup_id INT NOT NULL, INDEX IDX_6336075D12469DE2 (category_id), INDEX IDX_6336075DD2112630 (usergroup_id), PRIMARY KEY(category_id, usergroup_id)) ENGINE = InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci';
$queries['create'][88] = 'CREATE TABLE news_comments (id INT AUTO_INCREMENT NOT NULL, news_id INT DEFAULT NULL, person_id INT DEFAULT NULL, visitor_id INT DEFAULT NULL, ip_address VARCHAR(30) NOT NULL, email VARCHAR(255) DEFAULT NULL, name VARCHAR(255) DEFAULT NULL, website VARCHAR(255) DEFAULT NULL, content LONGTEXT NOT NULL, status VARCHAR(30) NOT NULL, validating VARCHAR(35) DEFAULT NULL, is_reviewed TINYINT(1) NOT NULL, date_created DATETIME NOT NULL, INDEX IDX_16A0357BB5A459A0 (news_id), INDEX IDX_16A0357B217BBB47 (person_id), INDEX IDX_16A0357B70BEE6D (visitor_id), INDEX status_idx (status, is_reviewed), PRIMARY KEY(id)) ENGINE = InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci';
$queries['create'][89] = 'CREATE TABLE news_revisions (id INT AUTO_INCREMENT NOT NULL, news_id INT DEFAULT NULL, person_id INT DEFAULT NULL, title VARCHAR(255) NOT NULL, content LONGTEXT NOT NULL, status VARCHAR(30) NOT NULL, date_created DATETIME NOT NULL, INDEX IDX_95947D44B5A459A0 (news_id), INDEX IDX_95947D44217BBB47 (person_id), PRIMARY KEY(id)) ENGINE = InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci';
$queries['create'][90] = 'CREATE TABLE object_lang (id INT AUTO_INCREMENT NOT NULL, language_id INT DEFAULT NULL, ref VARCHAR(200) NOT NULL, ref_type VARCHAR(100) DEFAULT NULL, ref_id INT DEFAULT NULL, prop_name VARCHAR(100) NOT NULL, value LONGTEXT NOT NULL, INDEX IDX_AC1CB87182F1BAF4 (language_id), INDEX prop_ref_type (ref_type, ref_id), UNIQUE INDEX prop_ref (ref, prop_name, language_id), PRIMARY KEY(id)) ENGINE = InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci';
$queries['create'][91] = 'CREATE TABLE organizations (id INT AUTO_INCREMENT NOT NULL, picture_blob_id INT DEFAULT NULL, name VARCHAR(255) NOT NULL, summary LONGTEXT NOT NULL, importance INT NOT NULL, date_created DATETIME NOT NULL, UNIQUE INDEX UNIQ_427C1C7FF0187A77 (picture_blob_id), PRIMARY KEY(id)) ENGINE = InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci';
$queries['create'][92] = 'CREATE TABLE organization2usergroups (organization_id INT NOT NULL, usergroup_id INT NOT NULL, INDEX IDX_EA8C676432C8A3DE (organization_id), INDEX IDX_EA8C6764D2112630 (usergroup_id), PRIMARY KEY(organization_id, usergroup_id)) ENGINE = InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci';
$queries['create'][93] = 'CREATE TABLE organizations_auto_cc (organization_id INT NOT NULL, person_id INT NOT NULL, INDEX IDX_864B966432C8A3DE (organization_id), INDEX IDX_864B9664217BBB47 (person_id), PRIMARY KEY(organization_id, person_id)) ENGINE = InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci';
$queries['create'][94] = 'CREATE TABLE organizations_contact_data (id INT AUTO_INCREMENT NOT NULL, organization_id INT DEFAULT NULL, contact_type VARCHAR(80) NOT NULL, comment LONGTEXT NOT NULL, field_1 LONGTEXT NOT NULL, field_2 LONGTEXT NOT NULL, field_3 LONGTEXT NOT NULL, field_4 LONGTEXT NOT NULL, field_5 LONGTEXT NOT NULL, field_6 LONGTEXT NOT NULL, field_7 LONGTEXT NOT NULL, field_8 LONGTEXT NOT NULL, field_9 LONGTEXT NOT NULL, field_10 LONGTEXT NOT NULL, INDEX IDX_25B60D5032C8A3DE (organization_id), PRIMARY KEY(id)) ENGINE = InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci';
$queries['create'][95] = 'CREATE TABLE organization_email_domains (domain VARCHAR(255) NOT NULL, organization_id INT DEFAULT NULL, INDEX IDX_2CCB20C232C8A3DE (organization_id), PRIMARY KEY(domain)) ENGINE = InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci';
$queries['create'][96] = 'CREATE TABLE organization_notes (id INT AUTO_INCREMENT NOT NULL, organization_id INT DEFAULT NULL, agent_id INT DEFAULT NULL, date_created DATETIME NOT NULL, note VARCHAR(255) NOT NULL, INDEX IDX_8F9C404B32C8A3DE (organization_id), INDEX IDX_8F9C404B3414710B (agent_id), PRIMARY KEY(id)) ENGINE = InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci';
$queries['create'][97] = 'CREATE TABLE organizations_twitter_users (id INT AUTO_INCREMENT NOT NULL, organization_id INT NOT NULL, screen_name VARCHAR(50) NOT NULL, is_verified TINYINT(1) NOT NULL, oauth_token VARCHAR(4000) DEFAULT NULL, oauth_token_secret VARCHAR(4000) DEFAULT NULL, twitter_user_id BIGINT NOT NULL, INDEX IDX_268948132C8A3DE (organization_id), INDEX screen_name_idx (screen_name), UNIQUE INDEX unique_key_idx (organization_id, screen_name), PRIMARY KEY(id)) ENGINE = InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci';
$queries['create'][98] = 'CREATE TABLE page_view_log (id INT AUTO_INCREMENT NOT NULL, object_type INT NOT NULL, object_id INT NOT NULL, view_action INT NOT NULL, person_id INT DEFAULT NULL, date_created DATETIME NOT NULL, INDEX object_idx (object_type, object_id), INDEX date_created_idx (date_created), PRIMARY KEY(id)) ENGINE = InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci';
$queries['create'][99] = 'CREATE TABLE permissions (id INT AUTO_INCREMENT NOT NULL, usergroup_id INT DEFAULT NULL, person_id INT DEFAULT NULL, name VARCHAR(50) NOT NULL, value LONGTEXT DEFAULT NULL, INDEX IDX_2DEDCC6FD2112630 (usergroup_id), INDEX IDX_2DEDCC6F217BBB47 (person_id), PRIMARY KEY(id)) ENGINE = InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci';
$queries['create'][100] = 'CREATE TABLE permissions_cache (name VARCHAR(255) NOT NULL, usergroup_key VARCHAR(32) NOT NULL, usergroup_ids LONGTEXT NOT NULL, perms LONGTEXT NOT NULL COMMENT \'(DC2Type:object)\', INDEX usergroup_key_idx (usergroup_key), PRIMARY KEY(name, usergroup_key)) ENGINE = InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci';
$queries['create'][101] = 'CREATE TABLE people (id INT AUTO_INCREMENT NOT NULL, picture_blob_id INT DEFAULT NULL, language_id INT DEFAULT NULL, organization_id INT DEFAULT NULL, primary_email_id INT DEFAULT NULL, gravatar_url LONGTEXT NOT NULL, disable_picture TINYINT(1) NOT NULL, is_contact TINYINT(1) NOT NULL, is_user TINYINT(1) NOT NULL, is_agent TINYINT(1) NOT NULL, was_agent TINYINT(1) NOT NULL, can_agent TINYINT(1) NOT NULL, can_admin TINYINT(1) NOT NULL, can_billing TINYINT(1) NOT NULL, can_reports TINYINT(1) NOT NULL, is_vacation_mode TINYINT(1) NOT NULL, disable_autoresponses TINYINT(1) NOT NULL, disable_autoresponses_log LONGTEXT DEFAULT NULL, is_confirmed TINYINT(1) NOT NULL, is_agent_confirmed TINYINT(1) NOT NULL, is_deleted TINYINT(1) NOT NULL, is_disabled TINYINT(1) NOT NULL, importance INT NOT NULL, creation_system VARCHAR(20) NOT NULL, name LONGTEXT NOT NULL, first_name LONGTEXT DEFAULT NULL, last_name LONGTEXT DEFAULT NULL, title_prefix VARCHAR(50) NOT NULL, override_display_name VARCHAR(200) NOT NULL, summary LONGTEXT NOT NULL, secret_string VARCHAR(40) NOT NULL, organization_position VARCHAR(100) NOT NULL, organization_manager TINYINT(1) NOT NULL, timezone VARCHAR(50) NOT NULL, password VARCHAR(100) DEFAULT NULL, password_scheme VARCHAR(20) DEFAULT NULL, salt VARCHAR(40) NOT NULL, date_created DATETIME NOT NULL, date_last_login DATETIME DEFAULT NULL, date_picture_check DATETIME DEFAULT NULL, INDEX IDX_28166A26F0187A77 (picture_blob_id), INDEX IDX_28166A2682F1BAF4 (language_id), INDEX IDX_28166A2632C8A3DE (organization_id), UNIQUE INDEX UNIQ_28166A26894DAC38 (primary_email_id), INDEX is_agent_idx (is_agent), INDEX is_confirmed_idx (is_confirmed), PRIMARY KEY(id)) ENGINE = InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci';
$queries['create'][102] = 'CREATE TABLE person2usergroups (person_id INT NOT NULL, usergroup_id INT NOT NULL, INDEX IDX_356C969ED2112630 (usergroup_id), PRIMARY KEY(person_id, usergroup_id)) ENGINE = InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci';
$queries['create'][103] = 'CREATE TABLE person_activity (id INT AUTO_INCREMENT NOT NULL, person_id INT DEFAULT NULL, action_type VARCHAR(255) NOT NULL, details LONGBLOB NOT NULL COMMENT \'(DC2Type:array)\', date_created DATETIME NOT NULL, INDEX IDX_3832AC6D217BBB47 (person_id), INDEX date_created_idx (date_created), PRIMARY KEY(id)) ENGINE = InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci';
$queries['create'][104] = 'CREATE TABLE people_contact_data (id INT AUTO_INCREMENT NOT NULL, person_id INT DEFAULT NULL, contact_type VARCHAR(80) NOT NULL, comment LONGTEXT NOT NULL, field_1 LONGTEXT NOT NULL, field_2 LONGTEXT NOT NULL, field_3 LONGTEXT NOT NULL, field_4 LONGTEXT NOT NULL, field_5 LONGTEXT NOT NULL, field_6 LONGTEXT NOT NULL, field_7 LONGTEXT NOT NULL, field_8 LONGTEXT NOT NULL, field_9 LONGTEXT NOT NULL, field_10 LONGTEXT NOT NULL, INDEX IDX_14604ED8217BBB47 (person_id), PRIMARY KEY(id)) ENGINE = InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci';
$queries['create'][105] = 'CREATE TABLE people_emails (id INT AUTO_INCREMENT NOT NULL, person_id INT DEFAULT NULL, email VARCHAR(255) NOT NULL, email_domain VARCHAR(255) NOT NULL, is_validated TINYINT(1) NOT NULL, comment TINYTEXT NOT NULL, date_created DATETIME NOT NULL, date_validated DATETIME DEFAULT NULL, INDEX IDX_3A96CAB8217BBB47 (person_id), INDEX email_domain_idx (email_domain), UNIQUE INDEX email_idx (email), PRIMARY KEY(id)) ENGINE = InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci';
$queries['create'][106] = 'CREATE TABLE people_emails_validating (id INT AUTO_INCREMENT NOT NULL, person_id INT DEFAULT NULL, email VARCHAR(255) NOT NULL, auth VARCHAR(20) NOT NULL, date_created DATETIME NOT NULL, validating_content LONGBLOB NOT NULL COMMENT \'(DC2Type:array)\', INDEX IDX_3277575C217BBB47 (person_id), UNIQUE INDEX email_idx (email), PRIMARY KEY(id)) ENGINE = InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci';
$queries['create'][107] = 'CREATE TABLE people_notes (id INT AUTO_INCREMENT NOT NULL, person_id INT DEFAULT NULL, agent_id INT DEFAULT NULL, date_created DATETIME NOT NULL, note VARCHAR(255) NOT NULL, INDEX IDX_CA78DCCC217BBB47 (person_id), INDEX IDX_CA78DCCC3414710B (agent_id), PRIMARY KEY(id)) ENGINE = InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci';
$queries['create'][108] = 'CREATE TABLE people_prefs (person_id INT NOT NULL, name VARCHAR(255) NOT NULL, value_str LONGTEXT DEFAULT NULL, value_array LONGBLOB DEFAULT NULL COMMENT \'(DC2Type:array)\', date_expire DATETIME DEFAULT NULL, INDEX IDX_8112E0E9217BBB47 (person_id), PRIMARY KEY(person_id, name)) ENGINE = InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci';
$queries['create'][109] = 'CREATE TABLE people_twitter_users (id INT AUTO_INCREMENT NOT NULL, person_id INT NOT NULL, screen_name VARCHAR(50) NOT NULL, is_verified TINYINT(1) NOT NULL, oauth_token VARCHAR(4000) DEFAULT NULL, oauth_token_secret VARCHAR(4000) DEFAULT NULL, twitter_user_id BIGINT NOT NULL, INDEX IDX_E13A49D0217BBB47 (person_id), INDEX screen_name_idx (screen_name), INDEX twitter_user_id_idx (twitter_user_id), UNIQUE INDEX unique_key_idx (person_id, screen_name), PRIMARY KEY(id)) ENGINE = InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci';
$queries['create'][110] = 'CREATE TABLE person_usersource_assoc (id INT AUTO_INCREMENT NOT NULL, person_id INT DEFAULT NULL, usersource_id INT DEFAULT NULL, identity VARCHAR(255) NOT NULL, identity_friendly VARCHAR(255) NOT NULL, data LONGBLOB NOT NULL COMMENT \'(DC2Type:array)\', created_at DATETIME NOT NULL, INDEX IDX_72215949217BBB47 (person_id), INDEX IDX_722159495B71BD01 (usersource_id), INDEX identity_idx (identity), PRIMARY KEY(id)) ENGINE = InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci';
$queries['create'][111] = 'CREATE TABLE phrases (id INT AUTO_INCREMENT NOT NULL, language_id INT DEFAULT NULL, name VARCHAR(255) NOT NULL, groupname VARCHAR(255) DEFAULT NULL, phrase LONGTEXT NOT NULL, original_phrase LONGTEXT NOT NULL, original_hash VARCHAR(40) NOT NULL, is_outdated TINYINT(1) NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, INDEX IDX_121AC8C682F1BAF4 (language_id), INDEX name_idx (groupname, name), PRIMARY KEY(id)) ENGINE = InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci';
$queries['create'][112] = 'CREATE TABLE plugins (id VARCHAR(255) NOT NULL, title VARCHAR(255) NOT NULL, description LONGTEXT NOT NULL, version VARCHAR(100) NOT NULL, package_class VARCHAR(255) NOT NULL, package_class_file VARCHAR(255) NOT NULL, resources_path VARCHAR(255) NOT NULL, date_created DATETIME NOT NULL, enabled TINYINT(1) NOT NULL, PRIMARY KEY(id)) ENGINE = InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci';
$queries['create'][113] = 'CREATE TABLE plugin_listeners (id INT AUTO_INCREMENT NOT NULL, plugin_id VARCHAR(255) DEFAULT NULL, event_name VARCHAR(255) DEFAULT NULL, event_options LONGBLOB NOT NULL COMMENT \'(DC2Type:array)\', description VARCHAR(255) NOT NULL, run_order INT NOT NULL, listener_class VARCHAR(255) NOT NULL, date_created DATETIME NOT NULL, INDEX IDX_FEEE2572EC942BCF (plugin_id), PRIMARY KEY(id)) ENGINE = InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci';
$queries['create'][114] = 'CREATE TABLE portal_page_display (id INT AUTO_INCREMENT NOT NULL, type VARCHAR(255) NOT NULL, display_order INT NOT NULL, is_enabled TINYINT(1) NOT NULL, section VARCHAR(50) NOT NULL, data LONGBLOB NOT NULL COMMENT \'(DC2Type:array)\', PRIMARY KEY(id)) ENGINE = InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci';
$queries['create'][115] = 'CREATE TABLE pretickets_content (id INT AUTO_INCREMENT NOT NULL, person_id INT DEFAULT NULL, visitor_id INT DEFAULT NULL, ip_address VARCHAR(30) NOT NULL, email VARCHAR(255) DEFAULT NULL, name VARCHAR(255) DEFAULT NULL, department_id INT NOT NULL, subject VARCHAR(255) NOT NULL, message LONGTEXT NOT NULL, data LONGBLOB NOT NULL COMMENT \'(DC2Type:array)\', is_solved TINYINT(1) NOT NULL, unsolved_content LONGBLOB NOT NULL COMMENT \'(DC2Type:array)\', object_type VARCHAR(100) DEFAULT NULL, object_id INT DEFAULT NULL, date_created DATETIME NOT NULL, INDEX IDX_E1110A25217BBB47 (person_id), INDEX IDX_E1110A2570BEE6D (visitor_id), INDEX email_idx (email), INDEX object_idx (object_type, object_id), PRIMARY KEY(id)) ENGINE = InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci';
$queries['create'][116] = 'CREATE TABLE products (id INT AUTO_INCREMENT NOT NULL, parent_id INT DEFAULT NULL, title VARCHAR(255) NOT NULL, display_order INT NOT NULL, depth INT NOT NULL, root INT DEFAULT NULL, INDEX IDX_B3BA5A5A727ACA70 (parent_id), PRIMARY KEY(id)) ENGINE = InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci';
$queries['create'][117] = 'CREATE TABLE queue_items (id INT AUTO_INCREMENT NOT NULL, groupname VARCHAR(255) DEFAULT NULL, priority INT NOT NULL, delay_until DATETIME DEFAULT NULL, ttr INT NOT NULL, is_ready TINYINT(1) NOT NULL, is_dataonly TINYINT(1) NOT NULL, is_ignored TINYINT(1) NOT NULL, reserved_at DATETIME DEFAULT NULL, timeout_at DATETIME DEFAULT NULL, created_at DATETIME NOT NULL, data LONGBLOB DEFAULT NULL COMMENT \'(DC2Type:array)\', INDEX priority_idx (priority), PRIMARY KEY(id)) ENGINE = InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci';
$queries['create'][118] = 'CREATE TABLE ratings (id INT AUTO_INCREMENT NOT NULL, searchlog_id INT DEFAULT NULL, person_id INT DEFAULT NULL, visitor_id INT DEFAULT NULL, object_type VARCHAR(100) NOT NULL, object_id INT NOT NULL, ip_address VARCHAR(30) NOT NULL, email VARCHAR(255) DEFAULT NULL, name VARCHAR(255) DEFAULT NULL, rating INT NOT NULL, date_created DATETIME NOT NULL, INDEX IDX_CEB607C9546A72F3 (searchlog_id), INDEX IDX_CEB607C9217BBB47 (person_id), INDEX IDX_CEB607C970BEE6D (visitor_id), INDEX object_idx (object_type, object_id), PRIMARY KEY(id)) ENGINE = InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci';
$queries['create'][119] = 'CREATE TABLE related_content (object_type VARCHAR(100) NOT NULL, object_id INT NOT NULL, rel_object_type VARCHAR(100) NOT NULL, rel_object_id INT NOT NULL, PRIMARY KEY(object_type, object_id, rel_object_type, rel_object_id)) ENGINE = InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci';
$queries['create'][120] = 'CREATE TABLE report_builder (id INT AUTO_INCREMENT NOT NULL, parent_id INT DEFAULT NULL, unique_key VARCHAR(50) DEFAULT NULL, title VARCHAR(255) NOT NULL, description LONGTEXT NOT NULL, query LONGTEXT NOT NULL, is_custom TINYINT(1) NOT NULL, category VARCHAR(25) DEFAULT NULL, display_order INT NOT NULL, INDEX IDX_B6BED249727ACA70 (parent_id), UNIQUE INDEX unique_key_idx (unique_key), PRIMARY KEY(id)) ENGINE = InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci';
$queries['create'][121] = 'CREATE TABLE report_builder_favorite (id INT AUTO_INCREMENT NOT NULL, report_builder_id INT DEFAULT NULL, person_id INT DEFAULT NULL, params VARCHAR(100) NOT NULL, INDEX IDX_CCD5CB1186DD4ADF (report_builder_id), INDEX IDX_CCD5CB11217BBB47 (person_id), UNIQUE INDEX unique_key_idx (report_builder_id, person_id, params), PRIMARY KEY(id)) ENGINE = InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci';
$queries['create'][122] = 'CREATE TABLE result_cache (id INT AUTO_INCREMENT NOT NULL, person_id INT DEFAULT NULL, criteria LONGBLOB NOT NULL COMMENT \'(DC2Type:array)\', results LONGBLOB NOT NULL COMMENT \'(DC2Type:array)\', extra LONGBLOB NOT NULL COMMENT \'(DC2Type:array)\', num_results INT NOT NULL, date_created DATETIME NOT NULL, results_type VARCHAR(50) DEFAULT NULL, INDEX IDX_D0B33C6B217BBB47 (person_id), PRIMARY KEY(id)) ENGINE = InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci';
$queries['create'][123] = 'CREATE TABLE searchlog (id INT AUTO_INCREMENT NOT NULL, person_id INT DEFAULT NULL, visitor_id INT DEFAULT NULL, ip_address VARCHAR(30) NOT NULL, email VARCHAR(255) DEFAULT NULL, name VARCHAR(255) DEFAULT NULL, query LONGTEXT NOT NULL, num_results INT NOT NULL, date_created DATETIME NOT NULL, INDEX IDX_8C79CD5C217BBB47 (person_id), INDEX IDX_8C79CD5C70BEE6D (visitor_id), INDEX searchlog_query_idx (query (15)), INDEX num_results_idx (num_results), PRIMARY KEY(id)) ENGINE = InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci';
$queries['create'][124] = 'CREATE TABLE search_sticky_result (word VARCHAR(150) NOT NULL, object_type VARCHAR(100) NOT NULL, object_id INT NOT NULL, PRIMARY KEY(word, object_type, object_id)) ENGINE = InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci';
$queries['create'][125] = 'CREATE TABLE search_term_boosters (object_type VARCHAR(100) NOT NULL, object_id INT NOT NULL, is_user TINYINT(1) NOT NULL, boosted_terms VARCHAR(255) NOT NULL, PRIMARY KEY(object_type, object_id)) ENGINE = InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci';
$queries['create'][126] = 'CREATE TABLE sendmail_logs (id INT AUTO_INCREMENT NOT NULL, ticket_id INT DEFAULT NULL, ticket_message_id INT DEFAULT NULL, person_id INT DEFAULT NULL, code VARCHAR(30) NOT NULL, to_address VARCHAR(255) NOT NULL, subject VARCHAR(255) NOT NULL, from_address VARCHAR(255) NOT NULL, date_created DATETIME NOT NULL, date_process DATETIME DEFAULT NULL, date_deliver DATETIME DEFAULT NULL, reason_deliver LONGTEXT DEFAULT NULL, date_open DATETIME DEFAULT NULL, date_click DATETIME DEFAULT NULL, clicked_urls LONGTEXT DEFAULT NULL, count_open INT NOT NULL, count_click INT NOT NULL, date_defer DATETIME DEFAULT NULL, reason_defer LONGTEXT DEFAULT NULL, date_bounce DATETIME DEFAULT NULL, bounce_code VARCHAR(10) DEFAULT NULL, bounce_type VARCHAR(10) DEFAULT NULL, reason_bounce LONGTEXT DEFAULT NULL, date_drop DATETIME DEFAULT NULL, reason_drop LONGTEXT DEFAULT NULL, date_spam DATETIME DEFAULT NULL, INDEX IDX_D9E8157F700047D2 (ticket_id), INDEX IDX_D9E8157FC5E9817D (ticket_message_id), INDEX IDX_D9E8157F217BBB47 (person_id), UNIQUE INDEX code (code, to_address), PRIMARY KEY(id)) ENGINE = InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci';
$queries['create'][127] = 'CREATE TABLE sendmail_queue (id INT AUTO_INCREMENT NOT NULL, blob_id INT DEFAULT NULL, subject VARCHAR(255) NOT NULL, to_address LONGTEXT NOT NULL, from_address LONGTEXT NOT NULL, attempts INT NOT NULL, date_next_attempt DATETIME DEFAULT NULL, date_created DATETIME NOT NULL, date_sent DATETIME DEFAULT NULL, has_sent TINYINT(1) NOT NULL, log LONGTEXT NOT NULL, priority INT NOT NULL, INDEX IDX_DDB369C2ED3E8EA5 (blob_id), INDEX has_sent_idx (has_sent, date_next_attempt), PRIMARY KEY(id)) ENGINE = InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci';
$queries['create'][128] = 'CREATE TABLE sessions (id INT AUTO_INCREMENT NOT NULL, person_id INT DEFAULT NULL, visitor_id INT DEFAULT NULL, auth VARCHAR(15) NOT NULL, interface VARCHAR(50) NOT NULL, user_agent VARCHAR(255) DEFAULT NULL, ip_address VARCHAR(80) DEFAULT NULL, data LONGTEXT NOT NULL, is_person TINYINT(1) NOT NULL, is_bot TINYINT(1) NOT NULL, is_helpdesk TINYINT(1) NOT NULL, active_status VARCHAR(15) NOT NULL, is_chat_available TINYINT(1) NOT NULL, date_created DATETIME NOT NULL, date_last DATETIME NOT NULL, INDEX IDX_9A609D13217BBB47 (person_id), INDEX IDX_9A609D1370BEE6D (visitor_id), INDEX date_last_idx (date_last, is_person), PRIMARY KEY(id)) ENGINE = InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci';
$queries['create'][129] = 'CREATE TABLE settings (name VARCHAR(255) NOT NULL, value BLOB DEFAULT NULL, PRIMARY KEY(name)) ENGINE = InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci';
$queries['create'][130] = 'CREATE TABLE slas (id INT AUTO_INCREMENT NOT NULL, warning_trigger_id INT DEFAULT NULL, fail_trigger_id INT DEFAULT NULL, apply_priority_id INT DEFAULT NULL, apply_trigger_id INT DEFAULT NULL, title VARCHAR(100) NOT NULL, sla_type VARCHAR(50) NOT NULL, active_time VARCHAR(50) NOT NULL, work_start INT DEFAULT NULL, work_end INT DEFAULT NULL, work_days LONGBLOB DEFAULT NULL COMMENT \'(DC2Type:array)\', work_timezone VARCHAR(50) DEFAULT NULL, work_holidays LONGBLOB DEFAULT NULL COMMENT \'(DC2Type:array)\', apply_type VARCHAR(25) NOT NULL, INDEX IDX_ACE9984A91D0B882 (warning_trigger_id), INDEX IDX_ACE9984A55EA90D4 (fail_trigger_id), INDEX IDX_ACE9984A13CC0145 (apply_priority_id), INDEX IDX_ACE9984AED1A7B28 (apply_trigger_id), PRIMARY KEY(id)) ENGINE = InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci';
$queries['create'][131] = 'CREATE TABLE sla_people (sla_id INT NOT NULL, person_id INT NOT NULL, INDEX IDX_14ABD6A37A2CC8C4 (sla_id), INDEX IDX_14ABD6A3217BBB47 (person_id), PRIMARY KEY(sla_id, person_id)) ENGINE = InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci';
$queries['create'][132] = 'CREATE TABLE sla_organizations (sla_id INT NOT NULL, organization_id INT NOT NULL, INDEX IDX_A7F081987A2CC8C4 (sla_id), INDEX IDX_A7F0819832C8A3DE (organization_id), PRIMARY KEY(sla_id, organization_id)) ENGINE = InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci';
$queries['create'][133] = 'CREATE TABLE styles (id INT AUTO_INCREMENT NOT NULL, parent_id INT DEFAULT NULL, logo_blob_id INT DEFAULT NULL, css_blob_id INT DEFAULT NULL, css_blob_rtl_id INT DEFAULT NULL, title VARCHAR(255) NOT NULL, note LONGTEXT NOT NULL, css_dir VARCHAR(255) NOT NULL, css_updated DATETIME NOT NULL, options LONGBLOB NOT NULL COMMENT \'(DC2Type:array)\', created_at DATETIME NOT NULL, INDEX IDX_B65AFAF5727ACA70 (parent_id), UNIQUE INDEX UNIQ_B65AFAF5D91464D5 (logo_blob_id), UNIQUE INDEX UNIQ_B65AFAF58AB530EF (css_blob_id), UNIQUE INDEX UNIQ_B65AFAF5FEED6A62 (css_blob_rtl_id), PRIMARY KEY(id)) ENGINE = InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci';
$queries['create'][134] = 'CREATE TABLE tasks (id INT AUTO_INCREMENT NOT NULL, person_id INT DEFAULT NULL, assigned_agent_id INT DEFAULT NULL, assigned_agent_team_id INT DEFAULT NULL, is_completed TINYINT(1) NOT NULL, title LONGTEXT NOT NULL, visibility INT NOT NULL, date_due DATETIME DEFAULT NULL, date_created DATETIME NOT NULL, date_completed DATETIME DEFAULT NULL, INDEX IDX_50586597217BBB47 (person_id), INDEX IDX_5058659749197702 (assigned_agent_id), INDEX IDX_50586597410D1341 (assigned_agent_team_id), PRIMARY KEY(id)) ENGINE = InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci';
$queries['create'][135] = 'CREATE TABLE task_associations (id INT AUTO_INCREMENT NOT NULL, task_id INT DEFAULT NULL, person_id INT DEFAULT NULL, ticket_id INT DEFAULT NULL, organization_id INT DEFAULT NULL, assoc_type VARCHAR(50) NOT NULL, INDEX IDX_41B0E09C8DB60186 (task_id), INDEX IDX_41B0E09C217BBB47 (person_id), INDEX IDX_41B0E09C700047D2 (ticket_id), INDEX IDX_41B0E09C32C8A3DE (organization_id), PRIMARY KEY(id)) ENGINE = InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci';
$queries['create'][136] = 'CREATE TABLE task_comments (id INT AUTO_INCREMENT NOT NULL, task_id INT NOT NULL, person_id INT DEFAULT NULL, content LONGTEXT NOT NULL, date_created DATETIME NOT NULL, INDEX IDX_1F5E7C668DB60186 (task_id), INDEX IDX_1F5E7C66217BBB47 (person_id), PRIMARY KEY(id)) ENGINE = InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci';
$queries['create'][137] = 'CREATE TABLE task_queue (id INT AUTO_INCREMENT NOT NULL, runner_class VARCHAR(255) NOT NULL, task_data LONGBLOB NOT NULL COMMENT \'(DC2Type:array)\', date_runnable DATETIME NOT NULL, task_group VARCHAR(50) DEFAULT NULL, status VARCHAR(25) NOT NULL, date_started DATETIME DEFAULT NULL, date_completed DATETIME DEFAULT NULL, error_text LONGTEXT NOT NULL, run_status LONGTEXT NOT NULL, PRIMARY KEY(id)) ENGINE = InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci';
$queries['create'][138] = 'CREATE TABLE task_reminder_logs (id INT AUTO_INCREMENT NOT NULL, task_id INT DEFAULT NULL, person_id INT DEFAULT NULL, date_sent DATE DEFAULT NULL, INDEX IDX_A264D7248DB60186 (task_id), INDEX IDX_A264D724217BBB47 (person_id), PRIMARY KEY(id)) ENGINE = InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci';
$queries['create'][139] = 'CREATE TABLE templates (id INT AUTO_INCREMENT NOT NULL, style_id INT DEFAULT NULL, name VARCHAR(255) NOT NULL, variant_of VARCHAR(255) DEFAULT NULL, template_code LONGTEXT NOT NULL, template_compiled LONGTEXT NOT NULL, date_created DATETIME NOT NULL, date_updated DATETIME NOT NULL, INDEX IDX_6F287D8EBACD6074 (style_id), PRIMARY KEY(id)) ENGINE = InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci';
$queries['create'][140] = 'CREATE TABLE text_snippets (id INT AUTO_INCREMENT NOT NULL, person_id INT DEFAULT NULL, category_id INT DEFAULT NULL, shortcut_code VARCHAR(255) NOT NULL, INDEX IDX_5B6379CE217BBB47 (person_id), INDEX IDX_5B6379CE12469DE2 (category_id), PRIMARY KEY(id)) ENGINE = InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci';
$queries['create'][141] = 'CREATE TABLE text_snippet_categories (id INT AUTO_INCREMENT NOT NULL, person_id INT DEFAULT NULL, typename VARCHAR(30) NOT NULL, is_global TINYINT(1) NOT NULL, INDEX IDX_F3B50AF1217BBB47 (person_id), PRIMARY KEY(id)) ENGINE = InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci';
$queries['create'][142] = 'CREATE TABLE tickets (id INT AUTO_INCREMENT NOT NULL, parent_ticket_id INT DEFAULT NULL, language_id INT DEFAULT NULL, department_id INT DEFAULT NULL, category_id INT DEFAULT NULL, priority_id INT DEFAULT NULL, workflow_id INT DEFAULT NULL, product_id INT DEFAULT NULL, person_id INT DEFAULT NULL, person_email_id INT DEFAULT NULL, person_email_validating_id INT DEFAULT NULL, agent_id INT DEFAULT NULL, agent_team_id INT DEFAULT NULL, organization_id INT DEFAULT NULL, linked_chat_id INT DEFAULT NULL, email_gateway_id INT DEFAULT NULL, email_gateway_address_id INT DEFAULT NULL, locked_by_agent INT DEFAULT NULL, ref VARCHAR(100) NOT NULL, auth VARCHAR(20) NOT NULL, sent_to_address VARCHAR(200) NOT NULL, notify_email VARCHAR(200) NOT NULL, notify_email_name VARCHAR(200) NOT NULL, notify_email_agent VARCHAR(200) NOT NULL, notify_email_name_agent VARCHAR(200) NOT NULL, creation_system VARCHAR(100) NOT NULL, creation_system_option VARCHAR(1000) NOT NULL, ticket_hash VARCHAR(40) NOT NULL, status VARCHAR(30) NOT NULL, hidden_status VARCHAR(30) DEFAULT NULL, validating VARCHAR(35) DEFAULT NULL, is_hold TINYINT(1) NOT NULL, urgency INT NOT NULL, count_agent_replies INT NOT NULL, count_user_replies INT NOT NULL, feedback_rating INT DEFAULT NULL, date_feedback_rating DATETIME DEFAULT NULL, date_created DATETIME NOT NULL, date_resolved DATETIME DEFAULT NULL, date_closed DATETIME DEFAULT NULL, date_first_agent_assign DATETIME DEFAULT NULL, date_first_agent_reply DATETIME DEFAULT NULL, date_last_agent_reply DATETIME DEFAULT NULL, date_last_user_reply DATETIME DEFAULT NULL, date_agent_waiting DATETIME DEFAULT NULL, date_user_waiting DATETIME DEFAULT NULL, date_status DATETIME NOT NULL, total_user_waiting INT NOT NULL, total_to_first_reply INT NOT NULL, date_locked DATETIME DEFAULT NULL, has_attachments TINYINT(1) NOT NULL, subject VARCHAR(255) NOT NULL, original_subject VARCHAR(255) NOT NULL, properties LONGBLOB DEFAULT NULL COMMENT \'(DC2Type:array)\', worst_sla_status VARCHAR(20) DEFAULT NULL, waiting_times LONGBLOB DEFAULT NULL COMMENT \'(DC2Type:array)\', INDEX IDX_54469DF4814B683C (parent_ticket_id), INDEX IDX_54469DF482F1BAF4 (language_id), INDEX IDX_54469DF4AE80F5DF (department_id), INDEX IDX_54469DF412469DE2 (category_id), INDEX IDX_54469DF4497B19F9 (priority_id), INDEX IDX_54469DF42C7C2CBA (workflow_id), INDEX IDX_54469DF44584665A (product_id), INDEX IDX_54469DF4217BBB47 (person_id), INDEX IDX_54469DF43C7464FE (person_email_id), INDEX IDX_54469DF4581A624E (person_email_validating_id), INDEX IDX_54469DF43414710B (agent_id), INDEX IDX_54469DF4FB3FBA04 (agent_team_id), INDEX IDX_54469DF432C8A3DE (organization_id), INDEX IDX_54469DF471D249B2 (linked_chat_id), INDEX IDX_54469DF4FBCC7CDF (email_gateway_id), INDEX IDX_54469DF4F2598614 (email_gateway_address_id), INDEX IDX_54469DF4428359E2 (locked_by_agent), INDEX date_created_idx (date_created), INDEX date_locked_idx (date_locked), INDEX status_idx (status), UNIQUE INDEX ref_idx (ref), PRIMARY KEY(id)) ENGINE = InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci';
$queries['create'][143] = 'CREATE TABLE ticket_access_codes (id INT AUTO_INCREMENT NOT NULL, ticket_id INT DEFAULT NULL, person_id INT DEFAULT NULL, auth VARCHAR(50) NOT NULL, INDEX IDX_CCEE41B5700047D2 (ticket_id), INDEX IDX_CCEE41B5217BBB47 (person_id), PRIMARY KEY(id)) ENGINE = InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci';
$queries['create'][144] = 'CREATE TABLE tickets_attachments (id INT AUTO_INCREMENT NOT NULL, ticket_id INT DEFAULT NULL, person_id INT DEFAULT NULL, blob_id INT DEFAULT NULL, message_id INT DEFAULT NULL, is_agent_note TINYINT(1) NOT NULL, is_inline TINYINT(1) NOT NULL, INDEX IDX_F06B468D700047D2 (ticket_id), INDEX IDX_F06B468D217BBB47 (person_id), INDEX IDX_F06B468DED3E8EA5 (blob_id), INDEX IDX_F06B468D537A1329 (message_id), PRIMARY KEY(id)) ENGINE = InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci';
$queries['create'][145] = 'CREATE TABLE ticket_categories (id INT AUTO_INCREMENT NOT NULL, parent_id INT DEFAULT NULL, title VARCHAR(255) NOT NULL, display_order INT NOT NULL, INDEX IDX_AC60D43C727ACA70 (parent_id), PRIMARY KEY(id)) ENGINE = InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci';
$queries['create'][146] = 'CREATE TABLE ticket_changetracker_logs (id INT AUTO_INCREMENT NOT NULL, ticket_id INT DEFAULT NULL, log LONGTEXT NOT NULL, date_created DATETIME NOT NULL, INDEX IDX_F2205216700047D2 (ticket_id), INDEX date_created (date_created), PRIMARY KEY(id)) ENGINE = InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci';
$queries['create'][147] = 'CREATE TABLE ticket_charges (id INT AUTO_INCREMENT NOT NULL, ticket_id INT DEFAULT NULL, person_id INT DEFAULT NULL, organization_id INT DEFAULT NULL, agent_id INT DEFAULT NULL, charge_time INT DEFAULT NULL, amount NUMERIC(10, 2) DEFAULT NULL, comment VARCHAR(255) NOT NULL, date_created DATETIME NOT NULL, INDEX IDX_36230948700047D2 (ticket_id), INDEX IDX_36230948217BBB47 (person_id), INDEX IDX_3623094832C8A3DE (organization_id), INDEX IDX_362309483414710B (agent_id), PRIMARY KEY(id)) ENGINE = InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci';
$queries['create'][148] = 'CREATE TABLE tickets_deleted (ticket_id INT NOT NULL, by_person_id INT DEFAULT NULL, old_ptac VARCHAR(80) NOT NULL, old_ref VARCHAR(80) NOT NULL, new_ticket_id INT NOT NULL, date_created DATETIME NOT NULL, reason LONGTEXT NOT NULL, INDEX IDX_7EDF2278B5BE2AA2 (by_person_id), INDEX old_ref_idx (old_ref), PRIMARY KEY(ticket_id)) ENGINE = InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci';
$queries['create'][149] = 'CREATE TABLE ticket_feedback (id INT AUTO_INCREMENT NOT NULL, ticket_id INT DEFAULT NULL, message_id INT DEFAULT NULL, person_id INT DEFAULT NULL, rating INT NOT NULL, message LONGTEXT NOT NULL, date_created DATETIME NOT NULL, INDEX IDX_5740B8D9700047D2 (ticket_id), INDEX IDX_5740B8D9537A1329 (message_id), INDEX IDX_5740B8D9217BBB47 (person_id), PRIMARY KEY(id)) ENGINE = InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci';
$queries['create'][150] = 'CREATE TABLE ticket_filters (id INT AUTO_INCREMENT NOT NULL, person_id INT DEFAULT NULL, agent_team_id INT DEFAULT NULL, is_global TINYINT(1) NOT NULL, title VARCHAR(255) NOT NULL, is_enabled TINYINT(1) NOT NULL, sys_name VARCHAR(50) DEFAULT NULL, terms LONGBLOB NOT NULL COMMENT \'(DC2Type:array)\', group_by VARCHAR(255) NOT NULL, order_by VARCHAR(255) NOT NULL, INDEX IDX_74BB3EDF217BBB47 (person_id), INDEX IDX_74BB3EDFFB3FBA04 (agent_team_id), UNIQUE INDEX sys_name_unique (sys_name), PRIMARY KEY(id)) ENGINE = InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci';
$queries['create'][151] = 'CREATE TABLE ticket_filters_perms (id INT AUTO_INCREMENT NOT NULL, filter_id INT DEFAULT NULL, object_type VARCHAR(50) NOT NULL, object_id INT NOT NULL, INDEX IDX_93E8D427D395B25E (filter_id), INDEX object_idx (object_type, object_id), PRIMARY KEY(id)) ENGINE = InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci';
$queries['create'][152] = 'CREATE TABLE ticket_filter_subscriptions (id INT AUTO_INCREMENT NOT NULL, filter_id INT DEFAULT NULL, person_id INT DEFAULT NULL, email_created TINYINT(1) NOT NULL, email_new TINYINT(1) NOT NULL, email_leave TINYINT(1) NOT NULL, email_user_activity TINYINT(1) NOT NULL, email_agent_activity TINYINT(1) NOT NULL, email_agent_note TINYINT(1) NOT NULL, email_property_change TINYINT(1) NOT NULL, alert_created TINYINT(1) NOT NULL, alert_new TINYINT(1) NOT NULL, alert_leave TINYINT(1) NOT NULL, alert_user_activity TINYINT(1) NOT NULL, alert_agent_activity TINYINT(1) NOT NULL, alert_agent_note TINYINT(1) NOT NULL, alert_property_change TINYINT(1) NOT NULL, INDEX IDX_13669D98D395B25E (filter_id), INDEX IDX_13669D98217BBB47 (person_id), PRIMARY KEY(id)) ENGINE = InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci';
$queries['create'][153] = 'CREATE TABLE tickets_flagged (person_id INT NOT NULL, ticket_id INT NOT NULL, color VARCHAR(20) NOT NULL, PRIMARY KEY(person_id, ticket_id)) ENGINE = InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci';
$queries['create'][154] = 'CREATE TABLE tickets_logs (id INT AUTO_INCREMENT NOT NULL, parent_id INT DEFAULT NULL, ticket_id INT DEFAULT NULL, person_id INT DEFAULT NULL, sla_id INT DEFAULT NULL, action_type VARCHAR(40) NOT NULL, id_object INT DEFAULT NULL, id_before INT DEFAULT NULL, id_after INT DEFAULT NULL, trigger_id INT DEFAULT NULL, sla_status VARCHAR(20) DEFAULT NULL, details LONGBLOB NOT NULL COMMENT \'(DC2Type:array)\', date_created DATETIME NOT NULL, INDEX IDX_F5F41081727ACA70 (parent_id), INDEX IDX_F5F41081700047D2 (ticket_id), INDEX IDX_F5F41081217BBB47 (person_id), INDEX IDX_F5F410817A2CC8C4 (sla_id), PRIMARY KEY(id)) ENGINE = InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci';
$queries['create'][155] = 'CREATE TABLE ticket_macros (id INT AUTO_INCREMENT NOT NULL, person_id INT DEFAULT NULL, title VARCHAR(255) NOT NULL, is_enabled TINYINT(1) NOT NULL, is_global TINYINT(1) NOT NULL, actions LONGBLOB NOT NULL COMMENT \'(DC2Type:array)\', INDEX IDX_8E373A2C217BBB47 (person_id), PRIMARY KEY(id)) ENGINE = InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci';
$queries['create'][156] = 'CREATE TABLE ticket_macros_perms (id INT AUTO_INCREMENT NOT NULL, macro_id INT DEFAULT NULL, object_type VARCHAR(50) NOT NULL, object_id INT NOT NULL, INDEX IDX_EAB2E6D5F43A187E (macro_id), INDEX object_idx (object_type, object_id), PRIMARY KEY(id)) ENGINE = InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci';
$queries['create'][157] = 'CREATE TABLE tickets_messages (id INT AUTO_INCREMENT NOT NULL, ticket_id INT DEFAULT NULL, person_id INT DEFAULT NULL, email_source_id INT DEFAULT NULL, message_translated_id INT DEFAULT NULL, visitor_id INT DEFAULT NULL, date_created DATETIME NOT NULL, is_agent_note TINYINT(1) NOT NULL, creation_system VARCHAR(20) NOT NULL, ip_address VARCHAR(30) NOT NULL, geo_country VARCHAR(10) DEFAULT NULL, email VARCHAR(255) NOT NULL, message_hash VARCHAR(40) NOT NULL, message LONGTEXT NOT NULL, message_full LONGTEXT DEFAULT NULL, message_raw LONGTEXT DEFAULT NULL, lang_code VARCHAR(80) DEFAULT NULL, show_full_hint TINYINT(1) NOT NULL, INDEX IDX_3A9962E2700047D2 (ticket_id), INDEX IDX_3A9962E2217BBB47 (person_id), INDEX IDX_3A9962E29A37834A (email_source_id), INDEX IDX_3A9962E2251FB291 (message_translated_id), INDEX IDX_3A9962E270BEE6D (visitor_id), PRIMARY KEY(id)) ENGINE = InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci';
$queries['create'][158] = 'CREATE TABLE tickets_messages_raw (message_id INT NOT NULL, raw LONGBLOB NOT NULL, charset VARCHAR(100) NOT NULL, PRIMARY KEY(message_id)) ENGINE = InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci';
$queries['create'][159] = 'CREATE TABLE ticket_message_templates (id INT AUTO_INCREMENT NOT NULL, department_id INT DEFAULT NULL, title VARCHAR(255) NOT NULL, subject LONGTEXT NOT NULL, message LONGTEXT NOT NULL, date_created DATETIME NOT NULL, INDEX IDX_8C28E2ECAE80F5DF (department_id), PRIMARY KEY(id)) ENGINE = InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci';
$queries['create'][160] = 'CREATE TABLE tickets_messages_translated (id INT AUTO_INCREMENT NOT NULL, ticket_id INT DEFAULT NULL, message_id INT DEFAULT NULL, date_created DATETIME NOT NULL, from_lang_code VARCHAR(80) NOT NULL, lang_code VARCHAR(80) NOT NULL, message LONGTEXT NOT NULL, INDEX IDX_EDCD3BB3700047D2 (ticket_id), INDEX IDX_EDCD3BB3537A1329 (message_id), PRIMARY KEY(id)) ENGINE = InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci';
$queries['create'][161] = 'CREATE TABLE ticket_page_display (id INT AUTO_INCREMENT NOT NULL, department_id INT DEFAULT NULL, zone VARCHAR(50) NOT NULL, options LONGBLOB NOT NULL COMMENT \'(DC2Type:array)\', section VARCHAR(50) NOT NULL, data LONGBLOB NOT NULL COMMENT \'(DC2Type:array)\', INDEX IDX_3667659DAE80F5DF (department_id), PRIMARY KEY(id)) ENGINE = InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci';
$queries['create'][162] = 'CREATE TABLE tickets_participants (id INT AUTO_INCREMENT NOT NULL, ticket_id INT NOT NULL, person_id INT NOT NULL, access_code_id INT DEFAULT NULL, person_email_id INT DEFAULT NULL, default_on TINYINT(1) NOT NULL, INDEX IDX_8D675752700047D2 (ticket_id), INDEX IDX_8D675752217BBB47 (person_id), INDEX IDX_8D675752EFFF2402 (access_code_id), INDEX IDX_8D6757523C7464FE (person_email_id), PRIMARY KEY(id)) ENGINE = InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci';
$queries['create'][163] = 'CREATE TABLE ticket_priorities (id INT AUTO_INCREMENT NOT NULL, title VARCHAR(255) NOT NULL, priority INT NOT NULL, PRIMARY KEY(id)) ENGINE = InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci';
$queries['create'][164] = 'CREATE TABLE ticket_slas (id INT AUTO_INCREMENT NOT NULL, ticket_id INT DEFAULT NULL, sla_id INT DEFAULT NULL, sla_status VARCHAR(20) NOT NULL, warn_date DATETIME DEFAULT NULL, fail_date DATETIME DEFAULT NULL, is_completed TINYINT(1) NOT NULL, is_completed_set TINYINT(1) NOT NULL, completed_time_taken INT DEFAULT NULL, INDEX IDX_9E328D72700047D2 (ticket_id), INDEX IDX_9E328D727A2CC8C4 (sla_id), INDEX status_completed_warn_date_idx (sla_status, is_completed, warn_date), INDEX status_completed_fail_date_idx (sla_status, is_completed, fail_date), PRIMARY KEY(id)) ENGINE = InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci';
$queries['create'][165] = 'CREATE TABLE ticket_triggers (id INT AUTO_INCREMENT NOT NULL, title VARCHAR(255) NOT NULL, event_trigger VARCHAR(50) NOT NULL, event_trigger_options LONGBLOB DEFAULT NULL COMMENT \'(DC2Type:array)\', is_enabled TINYINT(1) NOT NULL, is_uneditable TINYINT(1) NOT NULL, terms LONGBLOB NOT NULL COMMENT \'(DC2Type:array)\', terms_any LONGBLOB NOT NULL COMMENT \'(DC2Type:array)\', actions LONGBLOB NOT NULL COMMENT \'(DC2Type:array)\', sys_name VARCHAR(50) DEFAULT NULL, run_order INT NOT NULL, date_created DATETIME NOT NULL, PRIMARY KEY(id)) ENGINE = InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci';
$queries['create'][166] = 'CREATE TABLE ticket_trigger_logs (id INT AUTO_INCREMENT NOT NULL, ticket_id INT NOT NULL, trigger_id INT NOT NULL, date_ran DATETIME NOT NULL, date_criteria DATETIME NOT NULL, INDEX ticket_id_idx (ticket_id, trigger_id, date_criteria), PRIMARY KEY(id)) ENGINE = InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci';
$queries['create'][167] = 'CREATE TABLE ticket_trigger_plugin_actions (id INT AUTO_INCREMENT NOT NULL, plugin_id VARCHAR(255) DEFAULT NULL, event_type VARCHAR(50) NOT NULL, setup_class VARCHAR(255) NOT NULL, action_class VARCHAR(255) NOT NULL, INDEX IDX_1D905890EC942BCF (plugin_id), UNIQUE INDEX event_type_idx (event_type), PRIMARY KEY(id)) ENGINE = InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci';
$queries['create'][168] = 'CREATE TABLE ticket_workflows (id INT AUTO_INCREMENT NOT NULL, title VARCHAR(255) NOT NULL, display_order INT NOT NULL, PRIMARY KEY(id)) ENGINE = InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci';
$queries['create'][169] = 'CREATE TABLE tmp_data (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(100) DEFAULT NULL, auth VARCHAR(15) NOT NULL, data LONGBLOB NOT NULL COMMENT \'(DC2Type:array)\', date_created DATETIME NOT NULL, date_expire DATETIME NOT NULL, INDEX name_idx (name), INDEX date_expire_idx (date_expire), PRIMARY KEY(id)) ENGINE = InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci';
$queries['create'][170] = 'CREATE TABLE twitter_accounts (id INT AUTO_INCREMENT NOT NULL, user_id BIGINT DEFAULT NULL, oauth_token VARCHAR(4000) NOT NULL, oauth_token_secret VARCHAR(4000) NOT NULL, last_processed_id BIGINT NOT NULL, UNIQUE INDEX UNIQ_D4051D30A76ED395 (user_id), PRIMARY KEY(id)) ENGINE = InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci';
$queries['create'][171] = 'CREATE TABLE twitter_accounts_person (account_id INT NOT NULL, person_id INT NOT NULL, INDEX IDX_BB12235C9B6B5FBA (account_id), INDEX IDX_BB12235C217BBB47 (person_id), PRIMARY KEY(account_id, person_id)) ENGINE = InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci';
$queries['create'][172] = 'CREATE TABLE twitter_accounts_followers (id INT AUTO_INCREMENT NOT NULL, account_id INT NOT NULL, user_id BIGINT NOT NULL, follow_order INT NOT NULL, is_archived TINYINT(1) NOT NULL, INDEX IDX_EB8452969B6B5FBA (account_id), INDEX IDX_EB845296A76ED395 (user_id), UNIQUE INDEX account_user_idx (account_id, user_id), PRIMARY KEY(id)) ENGINE = InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci';
$queries['create'][173] = 'CREATE TABLE twitter_accounts_friends (id INT AUTO_INCREMENT NOT NULL, account_id INT NOT NULL, user_id BIGINT NOT NULL, INDEX IDX_FADA774D9B6B5FBA (account_id), INDEX IDX_FADA774DA76ED395 (user_id), UNIQUE INDEX account_user_idx (account_id, user_id), PRIMARY KEY(id)) ENGINE = InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci';
$queries['create'][174] = 'CREATE TABLE twitter_accounts_searches (id INT AUTO_INCREMENT NOT NULL, account_id INT NOT NULL, term VARCHAR(255) NOT NULL, date_updated DATETIME DEFAULT NULL, max_id BIGINT DEFAULT NULL, min_id BIGINT DEFAULT NULL, INDEX IDX_5CC0E8CF9B6B5FBA (account_id), PRIMARY KEY(id)) ENGINE = InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci';
$queries['create'][175] = 'CREATE TABLE twitter_accounts_searches_statuses (account_status_id INT NOT NULL, search_id INT NOT NULL, date_created DATETIME NOT NULL, INDEX IDX_E52AFE3B498DD8E6 (account_status_id), INDEX IDX_E52AFE3B650760A9 (search_id), INDEX search_date_idx (search_id, date_created), PRIMARY KEY(account_status_id, search_id)) ENGINE = InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci';
$queries['create'][176] = 'CREATE TABLE twitter_accounts_statuses (id INT AUTO_INCREMENT NOT NULL, account_id INT DEFAULT NULL, status_id BIGINT DEFAULT NULL, agent_id INT DEFAULT NULL, agent_team_id INT DEFAULT NULL, action_agent_id INT DEFAULT NULL, retweeted_id INT DEFAULT NULL, in_reply_to_id INT DEFAULT NULL, date_created DATETIME NOT NULL, status_type VARCHAR(25) DEFAULT NULL, is_archived TINYINT(1) NOT NULL, is_favorited TINYINT(1) NOT NULL, INDEX IDX_7728CEC79B6B5FBA (account_id), INDEX IDX_7728CEC76BF700BD (status_id), INDEX IDX_7728CEC73414710B (agent_id), INDEX IDX_7728CEC7FB3FBA04 (agent_team_id), INDEX IDX_7728CEC7E3C3016D (action_agent_id), INDEX IDX_7728CEC754E76E81 (retweeted_id), INDEX IDX_7728CEC7DD92DAB8 (in_reply_to_id), INDEX account_type_archived_idx (account_id, status_type, is_archived), INDEX account_archived_idx (account_id, is_archived), PRIMARY KEY(id)) ENGINE = InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci';
$queries['create'][177] = 'CREATE TABLE twitter_accounts_statuses_notes (id INT AUTO_INCREMENT NOT NULL, account_status_id INT NOT NULL, person_id INT DEFAULT NULL, text VARCHAR(4000) NOT NULL, date_created DATETIME NOT NULL, INDEX IDX_E5D3CBA2498DD8E6 (account_status_id), INDEX IDX_E5D3CBA2217BBB47 (person_id), PRIMARY KEY(id)) ENGINE = InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci';
$queries['create'][178] = 'CREATE TABLE twitter_statuses (id BIGINT NOT NULL, user_id BIGINT DEFAULT NULL, in_reply_to_status_id BIGINT DEFAULT NULL, retweet_id BIGINT DEFAULT NULL, in_reply_to_user_id BIGINT DEFAULT NULL, recipient_id BIGINT DEFAULT NULL, text VARCHAR(4000) NOT NULL, is_truncated TINYINT(1) NOT NULL, date_created DATETIME NOT NULL, geo_latitude NUMERIC(10, 5) DEFAULT NULL, geo_longitude NUMERIC(10, 5) DEFAULT NULL, source VARCHAR(4000) DEFAULT NULL, INDEX IDX_553D9D8DA76ED395 (user_id), INDEX IDX_553D9D8D6B347969 (in_reply_to_status_id), INDEX IDX_553D9D8D72A1C5CA (retweet_id), INDEX IDX_553D9D8DD2347268 (in_reply_to_user_id), INDEX IDX_553D9D8DE92F8F78 (recipient_id), PRIMARY KEY(id)) ENGINE = InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci';
$queries['create'][179] = 'CREATE TABLE twitter_statuses_long (id INT AUTO_INCREMENT NOT NULL, status_id BIGINT DEFAULT NULL, for_user_id BIGINT DEFAULT NULL, text VARCHAR(4000) NOT NULL, is_public TINYINT(1) NOT NULL, date_created DATETIME NOT NULL, is_read TINYINT(1) NOT NULL, date_read DATETIME DEFAULT NULL, INDEX IDX_8B914BFB6BF700BD (status_id), INDEX IDX_8B914BFB9B5BB4B8 (for_user_id), PRIMARY KEY(id)) ENGINE = InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci';
$queries['create'][180] = 'CREATE TABLE twitter_statuses_mentions (id INT AUTO_INCREMENT NOT NULL, status_id BIGINT NOT NULL, user_id BIGINT NOT NULL, starts INT NOT NULL, ends INT NOT NULL, INDEX IDX_66912DD16BF700BD (status_id), INDEX IDX_66912DD1A76ED395 (user_id), PRIMARY KEY(id)) ENGINE = InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci';
$queries['create'][181] = 'CREATE TABLE twitter_statuses_tags (id INT AUTO_INCREMENT NOT NULL, status_id BIGINT NOT NULL, hash VARCHAR(255) NOT NULL, starts INT NOT NULL, ends INT NOT NULL, INDEX IDX_DFBA76B56BF700BD (status_id), PRIMARY KEY(id)) ENGINE = InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci';
$queries['create'][182] = 'CREATE TABLE twitter_statuses_urls (id INT AUTO_INCREMENT NOT NULL, status_id BIGINT NOT NULL, url VARCHAR(255) NOT NULL, display_url VARCHAR(255) NOT NULL, starts INT NOT NULL, ends INT NOT NULL, INDEX IDX_9A92D5326BF700BD (status_id), PRIMARY KEY(id)) ENGINE = InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci';
$queries['create'][183] = 'CREATE TABLE twitter_stream (id INT AUTO_INCREMENT NOT NULL, account_id INT NOT NULL, date_created DATETIME NOT NULL, event VARCHAR(50) NOT NULL, data LONGTEXT NOT NULL COMMENT \'(DC2Type:object)\', INDEX IDX_8D6AB9A89B6B5FBA (account_id), PRIMARY KEY(id)) ENGINE = InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci';
$queries['create'][184] = 'CREATE TABLE twitter_users (id BIGINT NOT NULL, name VARCHAR(40) NOT NULL, screen_name VARCHAR(20) NOT NULL, profile_image_url VARCHAR(200) NOT NULL, language VARCHAR(3) NOT NULL, url VARCHAR(200) NOT NULL, is_protected TINYINT(1) NOT NULL, is_verified TINYINT(1) NOT NULL, location VARCHAR(255) DEFAULT NULL, description VARCHAR(500) DEFAULT NULL, is_geo_enabled TINYINT(1) NOT NULL, is_stub TINYINT(1) NOT NULL, last_timeline_update DATETIME DEFAULT NULL, last_profile_update DATETIME DEFAULT NULL, last_follow_update DATETIME DEFAULT NULL, followers_count INT NOT NULL, friends_count INT NOT NULL, statuses_count INT NOT NULL, INDEX last_follow_update_idx (last_follow_update), PRIMARY KEY(id)) ENGINE = InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci';
$queries['create'][185] = 'CREATE TABLE twitter_users_followers (id INT AUTO_INCREMENT NOT NULL, user_id BIGINT NOT NULL, follower_user_id BIGINT NOT NULL, display_order INT NOT NULL, INDEX IDX_F37AF1BEA76ED395 (user_id), INDEX IDX_F37AF1BE70FC2906 (follower_user_id), UNIQUE INDEX user_follower_idx (user_id, follower_user_id), PRIMARY KEY(id)) ENGINE = InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci';
$queries['create'][186] = 'CREATE TABLE twitter_users_friends (id INT AUTO_INCREMENT NOT NULL, user_id BIGINT NOT NULL, friend_user_id BIGINT NOT NULL, display_order INT NOT NULL, INDEX IDX_77C2EDABA76ED395 (user_id), INDEX IDX_77C2EDAB93D1119E (friend_user_id), UNIQUE INDEX user_friend_idx (user_id, friend_user_id), PRIMARY KEY(id)) ENGINE = InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci';
$queries['create'][187] = 'CREATE TABLE usergroups (id INT AUTO_INCREMENT NOT NULL, title VARCHAR(255) NOT NULL, note LONGTEXT NOT NULL, is_agent_group TINYINT(1) NOT NULL, sys_name VARCHAR(50) DEFAULT NULL, is_enabled TINYINT(1) NOT NULL, PRIMARY KEY(id)) ENGINE = InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci';
$queries['create'][188] = 'CREATE TABLE user_rules (id INT AUTO_INCREMENT NOT NULL, add_organization_id INT DEFAULT NULL, add_usergroup_id INT DEFAULT NULL, email_patterns LONGBLOB NOT NULL COMMENT \'(DC2Type:array)\', run_order INT NOT NULL, INDEX IDX_6B5862642940B3FB (add_organization_id), INDEX IDX_6B586264A19F75EA (add_usergroup_id), PRIMARY KEY(id)) ENGINE = InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci';
$queries['create'][189] = 'CREATE TABLE usersources (id INT AUTO_INCREMENT NOT NULL, usersource_plugin_id INT DEFAULT NULL, title VARCHAR(255) NOT NULL, source_type VARCHAR(255) NOT NULL, lost_password_url VARCHAR(1000) NOT NULL, options LONGBLOB NOT NULL COMMENT \'(DC2Type:array)\', display_order INT NOT NULL, is_enabled TINYINT(1) NOT NULL, INDEX IDX_4E3C994CEB0D3362 (usersource_plugin_id), PRIMARY KEY(id)) ENGINE = InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci';
$queries['create'][190] = 'CREATE TABLE usersource_plugins (id INT AUTO_INCREMENT NOT NULL, plugin_id VARCHAR(255) DEFAULT NULL, unique_key VARCHAR(50) NOT NULL, title VARCHAR(255) NOT NULL, form_model_class VARCHAR(255) NOT NULL, form_type_class VARCHAR(255) NOT NULL, form_template VARCHAR(255) NOT NULL, adapter_class VARCHAR(255) NOT NULL, INDEX IDX_E484A367EC942BCF (plugin_id), UNIQUE INDEX unique_key_idx (unique_key), PRIMARY KEY(id)) ENGINE = InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci';
$queries['create'][191] = 'CREATE TABLE visitors (id INT AUTO_INCREMENT NOT NULL, person_id INT DEFAULT NULL, initial_track_id INT DEFAULT NULL, visit_track_id INT DEFAULT NULL, last_track_id INT DEFAULT NULL, last_track_id_soft INT DEFAULT NULL, auth VARCHAR(15) NOT NULL, user_token VARCHAR(8) DEFAULT NULL, name VARCHAR(255) NOT NULL, email VARCHAR(255) NOT NULL, page_title VARCHAR(255) NOT NULL, page_url VARCHAR(255) NOT NULL, ref_page_url VARCHAR(255) DEFAULT NULL, user_agent VARCHAR(255) NOT NULL, user_browser VARCHAR(255) NOT NULL, user_os VARCHAR(255) NOT NULL, ip_address VARCHAR(80) NOT NULL, geo_continent VARCHAR(2) DEFAULT NULL, geo_country VARCHAR(2) DEFAULT NULL, geo_region VARCHAR(2) DEFAULT NULL, geo_city VARCHAR(2) DEFAULT NULL, geo_long NUMERIC(16, 8) DEFAULT NULL, geo_lat NUMERIC(16, 8) DEFAULT NULL, hint_hidden TINYINT(1) NOT NULL, chat_invite LONGBLOB DEFAULT NULL COMMENT \'(DC2Type:array)\', page_count INT NOT NULL, date_created DATETIME NOT NULL, date_last DATETIME NOT NULL, INDEX IDX_7B74A43F217BBB47 (person_id), INDEX IDX_7B74A43F866B65F3 (initial_track_id), INDEX IDX_7B74A43F5B84E254 (visit_track_id), INDEX IDX_7B74A43F26B379DD (last_track_id), INDEX IDX_7B74A43F413BC2FF (last_track_id_soft), INDEX date_last_idx (date_last), PRIMARY KEY(id)) ENGINE = InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci';
$queries['create'][192] = 'CREATE TABLE visitor_tracks (id INT AUTO_INCREMENT NOT NULL, visitor_id INT DEFAULT NULL, is_new_visit TINYINT(1) NOT NULL, page_title VARCHAR(255) NOT NULL, page_url VARCHAR(255) NOT NULL, ref_page_url VARCHAR(255) DEFAULT NULL, user_agent VARCHAR(255) NOT NULL, user_browser VARCHAR(255) NOT NULL, user_os VARCHAR(255) NOT NULL, ip_address VARCHAR(80) NOT NULL, geo_continent VARCHAR(2) DEFAULT NULL, geo_country VARCHAR(2) DEFAULT NULL, geo_region VARCHAR(2) DEFAULT NULL, geo_city VARCHAR(2) DEFAULT NULL, geo_long NUMERIC(16, 8) DEFAULT NULL, geo_lat NUMERIC(16, 8) DEFAULT NULL, is_soft_track TINYINT(1) NOT NULL, data LONGBLOB DEFAULT NULL COMMENT \'(DC2Type:array)\', date_created DATETIME NOT NULL, INDEX IDX_E002459270BEE6D (visitor_id), INDEX idx1 (date_created, is_new_visit), PRIMARY KEY(id)) ENGINE = InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci';
$queries['create'][193] = 'CREATE TABLE web_hooks (id INT AUTO_INCREMENT NOT NULL, title VARCHAR(100) NOT NULL, url VARCHAR(100) NOT NULL, username VARCHAR(100) NOT NULL, password VARCHAR(100) NOT NULL, PRIMARY KEY(id)) ENGINE = InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci';
$queries['create'][194] = 'CREATE TABLE widgets (id INT AUTO_INCREMENT NOT NULL, plugin_id VARCHAR(255) DEFAULT NULL, description VARCHAR(255) NOT NULL, title VARCHAR(100) NOT NULL, html LONGTEXT NOT NULL, js LONGTEXT NOT NULL, css LONGTEXT NOT NULL, page VARCHAR(50) NOT NULL, page_location VARCHAR(50) NOT NULL, insert_position VARCHAR(50) NOT NULL, enabled TINYINT(1) NOT NULL, unique_key VARCHAR(50) DEFAULT NULL, INDEX IDX_9D58E4C1EC942BCF (plugin_id), UNIQUE INDEX unique_key_idx (unique_key), PRIMARY KEY(id)) ENGINE = InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci';
$queries['create'][195] = 'CREATE TABLE worker_jobs (id VARCHAR(50) NOT NULL, worker_group VARCHAR(50) DEFAULT NULL, title VARCHAR(100) NOT NULL, description VARCHAR(255) NOT NULL, job_class VARCHAR(100) NOT NULL, data LONGBLOB DEFAULT NULL COMMENT \'(DC2Type:array)\', run_interval INT NOT NULL, last_run_date DATETIME DEFAULT NULL, last_start_date DATETIME DEFAULT NULL, PRIMARY KEY(id)) ENGINE = InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci';
$queries['create'][196] = 'CREATE TABLE `ref_reserve` (   `obj_type` varchar(50) NOT NULL,   `ref` varchar(255) NOT NULL DEFAULT \'\',   PRIMARY KEY (`obj_type`,`ref`) ) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci';
$queries['create'][197] = 'CREATE TABLE `content_search` (   `object_type` varchar(15) NOT NULL DEFAULT \'\',   `object_id` int(11) NOT NULL,   `content` longtext NOT NULL,   PRIMARY KEY (`object_type`,`object_id`),   FULLTEXT KEY `content` (`content`) ) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci';
$queries['create'][198] = 'CREATE TABLE `tickets_search_active` (   `id` int(11) NOT NULL,   `language_id` int(11) DEFAULT NULL,   `department_id` int(11) DEFAULT NULL,   `category_id` int(11) DEFAULT NULL,   `priority_id` int(11) DEFAULT NULL,   `workflow_id` int(11) DEFAULT NULL,   `product_id` int(11) DEFAULT NULL,   `person_id` int(11) NOT NULL,   `agent_id` int(11) DEFAULT NULL,   `agent_team_id` int(11) DEFAULT NULL,   `organization_id` int(11) DEFAULT NULL,   `email_gateway_id` int(11) DEFAULT NULL,   `creation_system` varchar(20) NOT NULL,   `status` varchar(30) NOT NULL,   `urgency` int(11) NOT NULL,   `is_hold` tinyint(1) NOT NULL,   `date_created` datetime NOT NULL,   `date_resolved` datetime DEFAULT NULL,   `date_first_agent_reply` datetime DEFAULT NULL,   `date_last_agent_reply` datetime DEFAULT NULL,   `date_last_user_reply` datetime DEFAULT NULL,   `date_agent_waiting` datetime DEFAULT NULL,   `date_user_waiting` datetime DEFAULT NULL,   `total_user_waiting` int(11) NOT NULL,   `total_to_first_reply` int(11) NOT NULL,   PRIMARY KEY (`id`),   KEY `status` (`status`),   KEY `person_id` (`person_id`) ) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci';
$queries['create'][199] = 'CREATE TABLE `tickets_search_subject` (   `id` int(11) NOT NULL,   `subject` varchar(1000) NOT NULL,   PRIMARY KEY (`id`) ) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci';
$queries['create'][200] = 'CREATE TABLE `tickets_search_message` (   `id` int(11) NOT NULL,   `language_id` int(11) DEFAULT NULL,   `department_id` int(11) DEFAULT NULL,   `category_id` int(11) DEFAULT NULL,   `priority_id` int(11) DEFAULT NULL,   `workflow_id` int(11) DEFAULT NULL,   `product_id` int(11) DEFAULT NULL,   `person_id` int(11) NOT NULL,   `agent_id` int(11) DEFAULT NULL,   `agent_team_id` int(11) DEFAULT NULL,   `organization_id` int(11) DEFAULT NULL,   `email_gateway_id` int(11) DEFAULT NULL,   `creation_system` varchar(20) NOT NULL,   `status` varchar(30) NOT NULL,   `urgency` int(11) NOT NULL,   `is_hold` tinyint(1) NOT NULL,   `date_created` datetime NOT NULL,   `date_resolved` datetime DEFAULT NULL,   `date_first_agent_reply` datetime DEFAULT NULL,   `date_last_agent_reply` datetime DEFAULT NULL,   `date_last_user_reply` datetime DEFAULT NULL,   `date_agent_waiting` datetime DEFAULT NULL,   `date_user_waiting` datetime DEFAULT NULL,   `total_user_waiting` int(11) NOT NULL,   `total_to_first_reply` int(11) NOT NULL,   `content` longtext NOT NULL,   PRIMARY KEY (`id`),   KEY `status` (`status`),   KEY `person_id` (`person_id`),   FULLTEXT KEY `content` (`content`) ) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci';
$queries['create'][201] = 'CREATE TABLE `tickets_search_message_active` (   `id` int(11) NOT NULL,   `language_id` int(11) DEFAULT NULL,   `department_id` int(11) DEFAULT NULL,   `category_id` int(11) DEFAULT NULL,   `priority_id` int(11) DEFAULT NULL,   `workflow_id` int(11) DEFAULT NULL,   `product_id` int(11) DEFAULT NULL,   `person_id` int(11) NOT NULL,   `agent_id` int(11) DEFAULT NULL,   `agent_team_id` int(11) DEFAULT NULL,   `organization_id` int(11) DEFAULT NULL,   `email_gateway_id` int(11) DEFAULT NULL,   `creation_system` varchar(20) NOT NULL,   `status` varchar(30) NOT NULL,   `urgency` int(11) NOT NULL,   `is_hold` tinyint(1) NOT NULL,   `date_created` datetime NOT NULL,   `date_resolved` datetime DEFAULT NULL,   `date_first_agent_reply` datetime DEFAULT NULL,   `date_last_agent_reply` datetime DEFAULT NULL,   `date_last_user_reply` datetime DEFAULT NULL,   `date_agent_waiting` datetime DEFAULT NULL,   `date_user_waiting` datetime DEFAULT NULL,   `total_user_waiting` int(11) NOT NULL,   `total_to_first_reply` int(11) NOT NULL,   `content` longtext NOT NULL,   PRIMARY KEY (`id`),   KEY `status` (`status`),   KEY `person_id` (`person_id`),   FULLTEXT KEY `content` (`content`) ) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci';




$queries['alter'][0] = 'ALTER TABLE agent_activity ADD CONSTRAINT FK_9AA510CE3414710B FOREIGN KEY (agent_id) REFERENCES people (id) ON DELETE CASCADE';
$queries['alter'][1] = 'ALTER TABLE agent_alerts ADD CONSTRAINT FK_A99D974D217BBB47 FOREIGN KEY (person_id) REFERENCES people (id) ON DELETE CASCADE';
$queries['alter'][2] = 'ALTER TABLE agent_team_members ADD CONSTRAINT FK_CC952C03296CD8AE FOREIGN KEY (team_id) REFERENCES agent_teams (id) ON DELETE CASCADE, ADD CONSTRAINT FK_CC952C03217BBB47 FOREIGN KEY (person_id) REFERENCES people (id) ON DELETE CASCADE';
$queries['alter'][3] = 'ALTER TABLE api_keys ADD CONSTRAINT FK_9579321F217BBB47 FOREIGN KEY (person_id) REFERENCES people (id) ON DELETE CASCADE';
$queries['alter'][4] = 'ALTER TABLE api_key_rate_limit ADD CONSTRAINT FK_BBDD0D428BE312B3 FOREIGN KEY (api_key_id) REFERENCES api_keys (id) ON DELETE CASCADE';
$queries['alter'][5] = 'ALTER TABLE api_token ADD CONSTRAINT FK_7BA2F5EB217BBB47 FOREIGN KEY (person_id) REFERENCES people (id) ON DELETE CASCADE';
$queries['alter'][6] = 'ALTER TABLE api_token_rate_limit ADD CONSTRAINT FK_458445A9217BBB47 FOREIGN KEY (person_id) REFERENCES api_token (person_id) ON DELETE CASCADE';
$queries['alter'][7] = 'ALTER TABLE articles ADD CONSTRAINT FK_BFDD3168217BBB47 FOREIGN KEY (person_id) REFERENCES people (id) ON DELETE SET NULL, ADD CONSTRAINT FK_BFDD316882F1BAF4 FOREIGN KEY (language_id) REFERENCES languages (id) ON DELETE CASCADE';
$queries['alter'][8] = 'ALTER TABLE article_to_categories ADD CONSTRAINT FK_9A1B4BB07294869C FOREIGN KEY (article_id) REFERENCES articles (id) ON DELETE CASCADE, ADD CONSTRAINT FK_9A1B4BB012469DE2 FOREIGN KEY (category_id) REFERENCES article_categories (id) ON DELETE CASCADE';
$queries['alter'][9] = 'ALTER TABLE article_to_product ADD CONSTRAINT FK_610BE8D97294869C FOREIGN KEY (article_id) REFERENCES articles (id) ON DELETE CASCADE, ADD CONSTRAINT FK_610BE8D94584665A FOREIGN KEY (product_id) REFERENCES products (id) ON DELETE CASCADE';
$queries['alter'][10] = 'ALTER TABLE article_attachments ADD CONSTRAINT FK_DD4790B17294869C FOREIGN KEY (article_id) REFERENCES articles (id) ON DELETE CASCADE, ADD CONSTRAINT FK_DD4790B1217BBB47 FOREIGN KEY (person_id) REFERENCES people (id) ON DELETE SET NULL, ADD CONSTRAINT FK_DD4790B1ED3E8EA5 FOREIGN KEY (blob_id) REFERENCES blobs (id) ON DELETE CASCADE';
$queries['alter'][11] = 'ALTER TABLE article_categories ADD CONSTRAINT FK_62A97E9727ACA70 FOREIGN KEY (parent_id) REFERENCES article_categories (id) ON DELETE SET NULL';
$queries['alter'][12] = 'ALTER TABLE article_category2usergroup ADD CONSTRAINT FK_6AD8B03212469DE2 FOREIGN KEY (category_id) REFERENCES article_categories (id) ON DELETE CASCADE, ADD CONSTRAINT FK_6AD8B032D2112630 FOREIGN KEY (usergroup_id) REFERENCES usergroups (id) ON DELETE CASCADE';
$queries['alter'][13] = 'ALTER TABLE article_comments ADD CONSTRAINT FK_A7662417294869C FOREIGN KEY (article_id) REFERENCES articles (id) ON DELETE CASCADE, ADD CONSTRAINT FK_A766241217BBB47 FOREIGN KEY (person_id) REFERENCES people (id) ON DELETE SET NULL, ADD CONSTRAINT FK_A76624170BEE6D FOREIGN KEY (visitor_id) REFERENCES visitors (id) ON DELETE SET NULL';
$queries['alter'][14] = 'ALTER TABLE article_pending_create ADD CONSTRAINT FK_27A971C3217BBB47 FOREIGN KEY (person_id) REFERENCES people (id) ON DELETE CASCADE, ADD CONSTRAINT FK_27A971C3700047D2 FOREIGN KEY (ticket_id) REFERENCES tickets (id) ON DELETE CASCADE, ADD CONSTRAINT FK_27A971C3C5E9817D FOREIGN KEY (ticket_message_id) REFERENCES tickets_messages (id) ON DELETE CASCADE';
$queries['alter'][15] = 'ALTER TABLE article_revisions ADD CONSTRAINT FK_538472A17294869C FOREIGN KEY (article_id) REFERENCES articles (id) ON DELETE CASCADE, ADD CONSTRAINT FK_538472A1217BBB47 FOREIGN KEY (person_id) REFERENCES people (id) ON DELETE SET NULL';
$queries['alter'][16] = 'ALTER TABLE blobs ADD CONSTRAINT FK_896C3E356BBE2052 FOREIGN KEY (original_blob_id) REFERENCES blobs (id) ON DELETE SET NULL';
$queries['alter'][17] = 'ALTER TABLE chat_blocks ADD CONSTRAINT FK_A931A25970BEE6D FOREIGN KEY (visitor_id) REFERENCES visitors (id) ON DELETE SET NULL, ADD CONSTRAINT FK_A931A259B5BE2AA2 FOREIGN KEY (by_person_id) REFERENCES people (id) ON DELETE SET NULL';
$queries['alter'][18] = 'ALTER TABLE chat_conversations ADD CONSTRAINT FK_5813432EAE80F5DF FOREIGN KEY (department_id) REFERENCES departments (id) ON DELETE SET NULL, ADD CONSTRAINT FK_5813432E3414710B FOREIGN KEY (agent_id) REFERENCES people (id) ON DELETE SET NULL, ADD CONSTRAINT FK_5813432EFB3FBA04 FOREIGN KEY (agent_team_id) REFERENCES agent_teams (id) ON DELETE SET NULL, ADD CONSTRAINT FK_5813432E217BBB47 FOREIGN KEY (person_id) REFERENCES people (id) ON DELETE SET NULL, ADD CONSTRAINT FK_5813432E613FECDF FOREIGN KEY (session_id) REFERENCES sessions (id) ON DELETE SET NULL, ADD CONSTRAINT FK_5813432E70BEE6D FOREIGN KEY (visitor_id) REFERENCES visitors (id) ON DELETE SET NULL';
$queries['alter'][19] = 'ALTER TABLE chat_conversation_to_person ADD CONSTRAINT FK_1CA5AE439AC0396 FOREIGN KEY (conversation_id) REFERENCES chat_conversations (id) ON DELETE CASCADE, ADD CONSTRAINT FK_1CA5AE43217BBB47 FOREIGN KEY (person_id) REFERENCES people (id) ON DELETE CASCADE';
$queries['alter'][20] = 'ALTER TABLE chat_messages ADD CONSTRAINT FK_EF20C9A69AC0396 FOREIGN KEY (conversation_id) REFERENCES chat_conversations (id) ON DELETE CASCADE, ADD CONSTRAINT FK_EF20C9A6F675F31B FOREIGN KEY (author_id) REFERENCES people (id) ON DELETE SET NULL';
$queries['alter'][21] = 'ALTER TABLE chat_page_display ADD CONSTRAINT FK_85AF0B7AE80F5DF FOREIGN KEY (department_id) REFERENCES departments (id) ON DELETE CASCADE';
$queries['alter'][22] = 'ALTER TABLE client_messages ADD CONSTRAINT FK_F5E42E53D2872966 FOREIGN KEY (for_person_id) REFERENCES people (id) ON DELETE CASCADE';
$queries['alter'][23] = 'ALTER TABLE content_subscriptions ADD CONSTRAINT FK_5FADAC10217BBB47 FOREIGN KEY (person_id) REFERENCES people (id) ON DELETE CASCADE, ADD CONSTRAINT FK_5FADAC107294869C FOREIGN KEY (article_id) REFERENCES articles (id) ON DELETE CASCADE, ADD CONSTRAINT FK_5FADAC10C667AEAB FOREIGN KEY (download_id) REFERENCES downloads (id) ON DELETE CASCADE, ADD CONSTRAINT FK_5FADAC10D249A887 FOREIGN KEY (feedback_id) REFERENCES feedback (id) ON DELETE CASCADE, ADD CONSTRAINT FK_5FADAC10B5A459A0 FOREIGN KEY (news_id) REFERENCES news (id) ON DELETE CASCADE';
$queries['alter'][24] = 'ALTER TABLE custom_data_article ADD CONSTRAINT FK_1DB64F8C7294869C FOREIGN KEY (article_id) REFERENCES articles (id) ON DELETE CASCADE, ADD CONSTRAINT FK_1DB64F8C443707B0 FOREIGN KEY (field_id) REFERENCES custom_def_article (id) ON DELETE CASCADE, ADD CONSTRAINT FK_1DB64F8C3F6A6D56 FOREIGN KEY (root_field_id) REFERENCES custom_def_article (id) ON DELETE CASCADE';
$queries['alter'][25] = 'ALTER TABLE custom_data_chat ADD CONSTRAINT FK_94E84EEE9AC0396 FOREIGN KEY (conversation_id) REFERENCES chat_conversations (id) ON DELETE CASCADE, ADD CONSTRAINT FK_94E84EEE443707B0 FOREIGN KEY (field_id) REFERENCES custom_def_chat (id) ON DELETE CASCADE, ADD CONSTRAINT FK_94E84EEE3F6A6D56 FOREIGN KEY (root_field_id) REFERENCES custom_def_chat (id) ON DELETE CASCADE';
$queries['alter'][26] = 'ALTER TABLE custom_data_feedback ADD CONSTRAINT FK_92E9C37FD249A887 FOREIGN KEY (feedback_id) REFERENCES feedback (id) ON DELETE CASCADE, ADD CONSTRAINT FK_92E9C37F443707B0 FOREIGN KEY (field_id) REFERENCES custom_def_feedback (id) ON DELETE CASCADE, ADD CONSTRAINT FK_92E9C37F3F6A6D56 FOREIGN KEY (root_field_id) REFERENCES custom_def_feedback (id) ON DELETE CASCADE';
$queries['alter'][27] = 'ALTER TABLE custom_data_organizations ADD CONSTRAINT FK_20C5B8AC32C8A3DE FOREIGN KEY (organization_id) REFERENCES organizations (id) ON DELETE CASCADE, ADD CONSTRAINT FK_20C5B8AC443707B0 FOREIGN KEY (field_id) REFERENCES custom_def_organizations (id) ON DELETE CASCADE, ADD CONSTRAINT FK_20C5B8AC3F6A6D56 FOREIGN KEY (root_field_id) REFERENCES custom_def_organizations (id) ON DELETE CASCADE';
$queries['alter'][28] = 'ALTER TABLE custom_data_person ADD CONSTRAINT FK_621E55A5217BBB47 FOREIGN KEY (person_id) REFERENCES people (id) ON DELETE CASCADE, ADD CONSTRAINT FK_621E55A5443707B0 FOREIGN KEY (field_id) REFERENCES custom_def_people (id) ON DELETE CASCADE, ADD CONSTRAINT FK_621E55A53F6A6D56 FOREIGN KEY (root_field_id) REFERENCES custom_def_people (id) ON DELETE CASCADE';
$queries['alter'][29] = 'ALTER TABLE custom_data_product ADD CONSTRAINT FK_CCC645474584665A FOREIGN KEY (product_id) REFERENCES products (id) ON DELETE CASCADE, ADD CONSTRAINT FK_CCC64547443707B0 FOREIGN KEY (field_id) REFERENCES custom_def_products (id) ON DELETE CASCADE, ADD CONSTRAINT FK_CCC645473F6A6D56 FOREIGN KEY (root_field_id) REFERENCES custom_def_products (id) ON DELETE CASCADE';
$queries['alter'][30] = 'ALTER TABLE custom_data_ticket ADD CONSTRAINT FK_C1622970700047D2 FOREIGN KEY (ticket_id) REFERENCES tickets (id) ON DELETE CASCADE, ADD CONSTRAINT FK_C1622970443707B0 FOREIGN KEY (field_id) REFERENCES custom_def_ticket (id) ON DELETE CASCADE, ADD CONSTRAINT FK_C16229703F6A6D56 FOREIGN KEY (root_field_id) REFERENCES custom_def_ticket (id) ON DELETE CASCADE';
$queries['alter'][31] = 'ALTER TABLE custom_def_article ADD CONSTRAINT FK_B651E6F4727ACA70 FOREIGN KEY (parent_id) REFERENCES custom_def_article (id) ON DELETE CASCADE, ADD CONSTRAINT FK_B651E6F4EC942BCF FOREIGN KEY (plugin_id) REFERENCES plugins (id) ON DELETE SET NULL';
$queries['alter'][32] = 'ALTER TABLE custom_def_chat ADD CONSTRAINT FK_2DE86CE5727ACA70 FOREIGN KEY (parent_id) REFERENCES custom_def_chat (id) ON DELETE CASCADE, ADD CONSTRAINT FK_2DE86CE5EC942BCF FOREIGN KEY (plugin_id) REFERENCES plugins (id) ON DELETE SET NULL';
$queries['alter'][33] = 'ALTER TABLE custom_def_feedback ADD CONSTRAINT FK_CC9CDDD8727ACA70 FOREIGN KEY (parent_id) REFERENCES custom_def_feedback (id) ON DELETE CASCADE, ADD CONSTRAINT FK_CC9CDDD8EC942BCF FOREIGN KEY (plugin_id) REFERENCES plugins (id) ON DELETE SET NULL';
$queries['alter'][34] = 'ALTER TABLE custom_def_organizations ADD CONSTRAINT FK_240601E7727ACA70 FOREIGN KEY (parent_id) REFERENCES custom_def_organizations (id) ON DELETE CASCADE, ADD CONSTRAINT FK_240601E7EC942BCF FOREIGN KEY (plugin_id) REFERENCES plugins (id) ON DELETE SET NULL';
$queries['alter'][35] = 'ALTER TABLE custom_def_people ADD CONSTRAINT FK_4840CFDA727ACA70 FOREIGN KEY (parent_id) REFERENCES custom_def_people (id) ON DELETE CASCADE, ADD CONSTRAINT FK_4840CFDAEC942BCF FOREIGN KEY (plugin_id) REFERENCES plugins (id) ON DELETE SET NULL';
$queries['alter'][36] = 'ALTER TABLE custom_def_products ADD CONSTRAINT FK_AD0FC3DA727ACA70 FOREIGN KEY (parent_id) REFERENCES custom_def_products (id) ON DELETE CASCADE, ADD CONSTRAINT FK_AD0FC3DAEC942BCF FOREIGN KEY (plugin_id) REFERENCES plugins (id) ON DELETE SET NULL';
$queries['alter'][37] = 'ALTER TABLE custom_def_ticket ADD CONSTRAINT FK_F7F6085F727ACA70 FOREIGN KEY (parent_id) REFERENCES custom_def_ticket (id) ON DELETE CASCADE, ADD CONSTRAINT FK_F7F6085FEC942BCF FOREIGN KEY (plugin_id) REFERENCES plugins (id) ON DELETE SET NULL';
$queries['alter'][38] = 'ALTER TABLE departments ADD CONSTRAINT FK_16AEB8D4727ACA70 FOREIGN KEY (parent_id) REFERENCES departments (id) ON DELETE CASCADE, ADD CONSTRAINT FK_16AEB8D4FBCC7CDF FOREIGN KEY (email_gateway_id) REFERENCES email_gateways (id) ON DELETE SET NULL';
$queries['alter'][39] = 'ALTER TABLE department_permissions ADD CONSTRAINT FK_84C36B30AE80F5DF FOREIGN KEY (department_id) REFERENCES departments (id) ON DELETE CASCADE, ADD CONSTRAINT FK_84C36B30D2112630 FOREIGN KEY (usergroup_id) REFERENCES usergroups (id) ON DELETE CASCADE, ADD CONSTRAINT FK_84C36B30217BBB47 FOREIGN KEY (person_id) REFERENCES people (id) ON DELETE CASCADE';
$queries['alter'][40] = 'ALTER TABLE downloads ADD CONSTRAINT FK_4B73A4B512469DE2 FOREIGN KEY (category_id) REFERENCES download_categories (id) ON DELETE SET NULL, ADD CONSTRAINT FK_4B73A4B5ED3E8EA5 FOREIGN KEY (blob_id) REFERENCES blobs (id), ADD CONSTRAINT FK_4B73A4B5217BBB47 FOREIGN KEY (person_id) REFERENCES people (id) ON DELETE SET NULL, ADD CONSTRAINT FK_4B73A4B582F1BAF4 FOREIGN KEY (language_id) REFERENCES languages (id) ON DELETE CASCADE';
$queries['alter'][41] = 'ALTER TABLE download_categories ADD CONSTRAINT FK_3317F15727ACA70 FOREIGN KEY (parent_id) REFERENCES download_categories (id) ON DELETE SET NULL';
$queries['alter'][42] = 'ALTER TABLE download_category2usergroup ADD CONSTRAINT FK_53A2246F12469DE2 FOREIGN KEY (category_id) REFERENCES download_categories (id) ON DELETE CASCADE, ADD CONSTRAINT FK_53A2246FD2112630 FOREIGN KEY (usergroup_id) REFERENCES usergroups (id) ON DELETE CASCADE';
$queries['alter'][43] = 'ALTER TABLE download_comments ADD CONSTRAINT FK_B43CDE14C667AEAB FOREIGN KEY (download_id) REFERENCES downloads (id) ON DELETE CASCADE, ADD CONSTRAINT FK_B43CDE14217BBB47 FOREIGN KEY (person_id) REFERENCES people (id) ON DELETE SET NULL, ADD CONSTRAINT FK_B43CDE1470BEE6D FOREIGN KEY (visitor_id) REFERENCES visitors (id) ON DELETE SET NULL';
$queries['alter'][44] = 'ALTER TABLE download_revisions ADD CONSTRAINT FK_483B9D66C667AEAB FOREIGN KEY (download_id) REFERENCES downloads (id) ON DELETE CASCADE, ADD CONSTRAINT FK_483B9D66ED3E8EA5 FOREIGN KEY (blob_id) REFERENCES blobs (id), ADD CONSTRAINT FK_483B9D66217BBB47 FOREIGN KEY (person_id) REFERENCES people (id) ON DELETE SET NULL';
$queries['alter'][45] = 'ALTER TABLE drafts ADD CONSTRAINT FK_EC2AE4C0217BBB47 FOREIGN KEY (person_id) REFERENCES people (id) ON DELETE CASCADE';
$queries['alter'][46] = 'ALTER TABLE email_gateways ADD CONSTRAINT FK_D0C6423237308465 FOREIGN KEY (linked_transport_id) REFERENCES email_transports (id) ON DELETE SET NULL, ADD CONSTRAINT FK_D0C64232AE80F5DF FOREIGN KEY (department_id) REFERENCES departments (id) ON DELETE SET NULL';
$queries['alter'][47] = 'ALTER TABLE email_gateway_addresses ADD CONSTRAINT FK_EC270D12FBCC7CDF FOREIGN KEY (email_gateway_id) REFERENCES email_gateways (id) ON DELETE CASCADE';
$queries['alter'][48] = 'ALTER TABLE email_sources ADD CONSTRAINT FK_6F9D0D3DED3E8EA5 FOREIGN KEY (blob_id) REFERENCES blobs (id) ON DELETE CASCADE, ADD CONSTRAINT FK_6F9D0D3D577F8E00 FOREIGN KEY (gateway_id) REFERENCES email_gateways (id) ON DELETE CASCADE';
$queries['alter'][49] = 'ALTER TABLE email_uids ADD CONSTRAINT FK_6D08D1BD577F8E00 FOREIGN KEY (gateway_id) REFERENCES email_gateways (id) ON DELETE CASCADE';
$queries['alter'][50] = 'ALTER TABLE feedback ADD CONSTRAINT FK_D2294458169CE813 FOREIGN KEY (status_category_id) REFERENCES feedback_status_categories (id) ON DELETE SET NULL, ADD CONSTRAINT FK_D229445812469DE2 FOREIGN KEY (category_id) REFERENCES feedback_categories (id), ADD CONSTRAINT FK_D2294458217BBB47 FOREIGN KEY (person_id) REFERENCES people (id) ON DELETE SET NULL, ADD CONSTRAINT FK_D229445882F1BAF4 FOREIGN KEY (language_id) REFERENCES languages (id) ON DELETE CASCADE';
$queries['alter'][51] = 'ALTER TABLE feedback_attachments ADD CONSTRAINT FK_CC264F12D249A887 FOREIGN KEY (feedback_id) REFERENCES feedback (id) ON DELETE CASCADE, ADD CONSTRAINT FK_CC264F12217BBB47 FOREIGN KEY (person_id) REFERENCES people (id) ON DELETE SET NULL, ADD CONSTRAINT FK_CC264F12ED3E8EA5 FOREIGN KEY (blob_id) REFERENCES blobs (id) ON DELETE CASCADE';
$queries['alter'][52] = 'ALTER TABLE feedback_categories ADD CONSTRAINT FK_66FE6832727ACA70 FOREIGN KEY (parent_id) REFERENCES feedback_categories (id) ON DELETE SET NULL';
$queries['alter'][53] = 'ALTER TABLE feedback_category2usergroup ADD CONSTRAINT FK_B304B93C12469DE2 FOREIGN KEY (category_id) REFERENCES feedback_categories (id) ON DELETE CASCADE, ADD CONSTRAINT FK_B304B93CD2112630 FOREIGN KEY (usergroup_id) REFERENCES usergroups (id) ON DELETE CASCADE';
$queries['alter'][54] = 'ALTER TABLE feedback_comments ADD CONSTRAINT FK_10D03D58D249A887 FOREIGN KEY (feedback_id) REFERENCES feedback (id) ON DELETE CASCADE, ADD CONSTRAINT FK_10D03D58217BBB47 FOREIGN KEY (person_id) REFERENCES people (id) ON DELETE SET NULL, ADD CONSTRAINT FK_10D03D5870BEE6D FOREIGN KEY (visitor_id) REFERENCES visitors (id) ON DELETE SET NULL';
$queries['alter'][55] = 'ALTER TABLE feedback_revisions ADD CONSTRAINT FK_37F57C3ED249A887 FOREIGN KEY (feedback_id) REFERENCES feedback (id) ON DELETE CASCADE, ADD CONSTRAINT FK_37F57C3E217BBB47 FOREIGN KEY (person_id) REFERENCES people (id) ON DELETE SET NULL';
$queries['alter'][56] = 'ALTER TABLE glossary_words ADD CONSTRAINT FK_1A8003DAD11EA911 FOREIGN KEY (definition_id) REFERENCES glossary_word_definitions (id) ON DELETE CASCADE';
$queries['alter'][57] = 'ALTER TABLE kb_subscriptions ADD CONSTRAINT FK_1F05AAF5217BBB47 FOREIGN KEY (person_id) REFERENCES people (id) ON DELETE CASCADE, ADD CONSTRAINT FK_1F05AAF57294869C FOREIGN KEY (article_id) REFERENCES articles (id) ON DELETE CASCADE, ADD CONSTRAINT FK_1F05AAF512469DE2 FOREIGN KEY (category_id) REFERENCES article_categories (id) ON DELETE CASCADE';
$queries['alter'][58] = 'ALTER TABLE labels_articles ADD CONSTRAINT FK_2F30AF707294869C FOREIGN KEY (article_id) REFERENCES articles (id) ON DELETE CASCADE';
$queries['alter'][59] = 'ALTER TABLE labels_blobs ADD CONSTRAINT FK_EC63B2F0ED3E8EA5 FOREIGN KEY (blob_id) REFERENCES blobs (id) ON DELETE CASCADE';
$queries['alter'][60] = 'ALTER TABLE labels_chat_conversations ADD CONSTRAINT FK_99205D121A9A7125 FOREIGN KEY (chat_id) REFERENCES chat_conversations (id) ON DELETE CASCADE';
$queries['alter'][61] = 'ALTER TABLE labels_downloads ADD CONSTRAINT FK_588FD17DC667AEAB FOREIGN KEY (download_id) REFERENCES downloads (id) ON DELETE CASCADE';
$queries['alter'][62] = 'ALTER TABLE labels_feedback ADD CONSTRAINT FK_42C4DA40D249A887 FOREIGN KEY (feedback_id) REFERENCES feedback (id) ON DELETE CASCADE';
$queries['alter'][63] = 'ALTER TABLE labels_news ADD CONSTRAINT FK_A2869A08B5A459A0 FOREIGN KEY (news_id) REFERENCES news (id) ON DELETE CASCADE';
$queries['alter'][64] = 'ALTER TABLE labels_organizations ADD CONSTRAINT FK_9F089F4232C8A3DE FOREIGN KEY (organization_id) REFERENCES organizations (id) ON DELETE CASCADE';
$queries['alter'][65] = 'ALTER TABLE labels_people ADD CONSTRAINT FK_C37D5395217BBB47 FOREIGN KEY (person_id) REFERENCES people (id) ON DELETE CASCADE';
$queries['alter'][66] = 'ALTER TABLE labels_tasks ADD CONSTRAINT FK_3557E9528DB60186 FOREIGN KEY (task_id) REFERENCES tasks (id) ON DELETE CASCADE';
$queries['alter'][67] = 'ALTER TABLE labels_tickets ADD CONSTRAINT FK_6C514FB700047D2 FOREIGN KEY (ticket_id) REFERENCES tickets (id) ON DELETE CASCADE';
$queries['alter'][68] = 'ALTER TABLE login_log ADD CONSTRAINT FK_F16D9FFF217BBB47 FOREIGN KEY (person_id) REFERENCES people (id) ON DELETE SET NULL';
$queries['alter'][69] = 'ALTER TABLE news ADD CONSTRAINT FK_1DD3995012469DE2 FOREIGN KEY (category_id) REFERENCES news_categories (id) ON DELETE CASCADE, ADD CONSTRAINT FK_1DD39950217BBB47 FOREIGN KEY (person_id) REFERENCES people (id) ON DELETE SET NULL, ADD CONSTRAINT FK_1DD3995082F1BAF4 FOREIGN KEY (language_id) REFERENCES languages (id) ON DELETE CASCADE';
$queries['alter'][70] = 'ALTER TABLE news_categories ADD CONSTRAINT FK_D68C9111727ACA70 FOREIGN KEY (parent_id) REFERENCES news_categories (id) ON DELETE SET NULL';
$queries['alter'][71] = 'ALTER TABLE news_category2usergroup ADD CONSTRAINT FK_6336075D12469DE2 FOREIGN KEY (category_id) REFERENCES news_categories (id) ON DELETE CASCADE, ADD CONSTRAINT FK_6336075DD2112630 FOREIGN KEY (usergroup_id) REFERENCES usergroups (id) ON DELETE CASCADE';
$queries['alter'][72] = 'ALTER TABLE news_comments ADD CONSTRAINT FK_16A0357BB5A459A0 FOREIGN KEY (news_id) REFERENCES news (id) ON DELETE CASCADE, ADD CONSTRAINT FK_16A0357B217BBB47 FOREIGN KEY (person_id) REFERENCES people (id) ON DELETE SET NULL, ADD CONSTRAINT FK_16A0357B70BEE6D FOREIGN KEY (visitor_id) REFERENCES visitors (id) ON DELETE SET NULL';
$queries['alter'][73] = 'ALTER TABLE news_revisions ADD CONSTRAINT FK_95947D44B5A459A0 FOREIGN KEY (news_id) REFERENCES news (id) ON DELETE CASCADE, ADD CONSTRAINT FK_95947D44217BBB47 FOREIGN KEY (person_id) REFERENCES people (id) ON DELETE SET NULL';
$queries['alter'][74] = 'ALTER TABLE object_lang ADD CONSTRAINT FK_AC1CB87182F1BAF4 FOREIGN KEY (language_id) REFERENCES languages (id) ON DELETE CASCADE';
$queries['alter'][75] = 'ALTER TABLE organizations ADD CONSTRAINT FK_427C1C7FF0187A77 FOREIGN KEY (picture_blob_id) REFERENCES blobs (id) ON DELETE SET NULL';
$queries['alter'][76] = 'ALTER TABLE organization2usergroups ADD CONSTRAINT FK_EA8C676432C8A3DE FOREIGN KEY (organization_id) REFERENCES organizations (id) ON DELETE CASCADE, ADD CONSTRAINT FK_EA8C6764D2112630 FOREIGN KEY (usergroup_id) REFERENCES usergroups (id) ON DELETE CASCADE';
$queries['alter'][77] = 'ALTER TABLE organizations_auto_cc ADD CONSTRAINT FK_864B966432C8A3DE FOREIGN KEY (organization_id) REFERENCES organizations (id) ON DELETE CASCADE, ADD CONSTRAINT FK_864B9664217BBB47 FOREIGN KEY (person_id) REFERENCES people (id) ON DELETE CASCADE';
$queries['alter'][78] = 'ALTER TABLE organizations_contact_data ADD CONSTRAINT FK_25B60D5032C8A3DE FOREIGN KEY (organization_id) REFERENCES organizations (id) ON DELETE CASCADE';
$queries['alter'][79] = 'ALTER TABLE organization_email_domains ADD CONSTRAINT FK_2CCB20C232C8A3DE FOREIGN KEY (organization_id) REFERENCES organizations (id) ON DELETE CASCADE';
$queries['alter'][80] = 'ALTER TABLE organization_notes ADD CONSTRAINT FK_8F9C404B32C8A3DE FOREIGN KEY (organization_id) REFERENCES organizations (id) ON DELETE CASCADE, ADD CONSTRAINT FK_8F9C404B3414710B FOREIGN KEY (agent_id) REFERENCES people (id) ON DELETE SET NULL';
$queries['alter'][81] = 'ALTER TABLE organizations_twitter_users ADD CONSTRAINT FK_268948132C8A3DE FOREIGN KEY (organization_id) REFERENCES organizations (id) ON DELETE CASCADE';
$queries['alter'][82] = 'ALTER TABLE permissions ADD CONSTRAINT FK_2DEDCC6FD2112630 FOREIGN KEY (usergroup_id) REFERENCES usergroups (id) ON DELETE CASCADE, ADD CONSTRAINT FK_2DEDCC6F217BBB47 FOREIGN KEY (person_id) REFERENCES people (id) ON DELETE CASCADE';
$queries['alter'][83] = 'ALTER TABLE people ADD CONSTRAINT FK_28166A26F0187A77 FOREIGN KEY (picture_blob_id) REFERENCES blobs (id) ON DELETE SET NULL, ADD CONSTRAINT FK_28166A2682F1BAF4 FOREIGN KEY (language_id) REFERENCES languages (id) ON DELETE SET NULL, ADD CONSTRAINT FK_28166A2632C8A3DE FOREIGN KEY (organization_id) REFERENCES organizations (id) ON DELETE SET NULL, ADD CONSTRAINT FK_28166A26894DAC38 FOREIGN KEY (primary_email_id) REFERENCES people_emails (id) ON DELETE SET NULL';
$queries['alter'][84] = 'ALTER TABLE person2usergroups ADD CONSTRAINT FK_356C969E217BBB47 FOREIGN KEY (person_id) REFERENCES people (id) ON DELETE CASCADE, ADD CONSTRAINT FK_356C969ED2112630 FOREIGN KEY (usergroup_id) REFERENCES usergroups (id) ON DELETE CASCADE';
$queries['alter'][85] = 'ALTER TABLE person_activity ADD CONSTRAINT FK_3832AC6D217BBB47 FOREIGN KEY (person_id) REFERENCES people (id) ON DELETE CASCADE';
$queries['alter'][86] = 'ALTER TABLE people_contact_data ADD CONSTRAINT FK_14604ED8217BBB47 FOREIGN KEY (person_id) REFERENCES people (id) ON DELETE CASCADE';
$queries['alter'][87] = 'ALTER TABLE people_emails ADD CONSTRAINT FK_3A96CAB8217BBB47 FOREIGN KEY (person_id) REFERENCES people (id) ON DELETE CASCADE';
$queries['alter'][88] = 'ALTER TABLE people_emails_validating ADD CONSTRAINT FK_3277575C217BBB47 FOREIGN KEY (person_id) REFERENCES people (id) ON DELETE CASCADE';
$queries['alter'][89] = 'ALTER TABLE people_notes ADD CONSTRAINT FK_CA78DCCC217BBB47 FOREIGN KEY (person_id) REFERENCES people (id) ON DELETE CASCADE, ADD CONSTRAINT FK_CA78DCCC3414710B FOREIGN KEY (agent_id) REFERENCES people (id) ON DELETE SET NULL';
$queries['alter'][90] = 'ALTER TABLE people_prefs ADD CONSTRAINT FK_8112E0E9217BBB47 FOREIGN KEY (person_id) REFERENCES people (id) ON DELETE CASCADE';
$queries['alter'][91] = 'ALTER TABLE people_twitter_users ADD CONSTRAINT FK_E13A49D0217BBB47 FOREIGN KEY (person_id) REFERENCES people (id) ON DELETE CASCADE';
$queries['alter'][92] = 'ALTER TABLE person_usersource_assoc ADD CONSTRAINT FK_72215949217BBB47 FOREIGN KEY (person_id) REFERENCES people (id) ON DELETE CASCADE, ADD CONSTRAINT FK_722159495B71BD01 FOREIGN KEY (usersource_id) REFERENCES usersources (id) ON DELETE CASCADE';
$queries['alter'][93] = 'ALTER TABLE phrases ADD CONSTRAINT FK_121AC8C682F1BAF4 FOREIGN KEY (language_id) REFERENCES languages (id) ON DELETE CASCADE';
$queries['alter'][94] = 'ALTER TABLE plugin_listeners ADD CONSTRAINT FK_FEEE2572EC942BCF FOREIGN KEY (plugin_id) REFERENCES plugins (id) ON DELETE CASCADE';
$queries['alter'][95] = 'ALTER TABLE pretickets_content ADD CONSTRAINT FK_E1110A25217BBB47 FOREIGN KEY (person_id) REFERENCES people (id) ON DELETE SET NULL, ADD CONSTRAINT FK_E1110A2570BEE6D FOREIGN KEY (visitor_id) REFERENCES visitors (id) ON DELETE SET NULL';
$queries['alter'][96] = 'ALTER TABLE products ADD CONSTRAINT FK_B3BA5A5A727ACA70 FOREIGN KEY (parent_id) REFERENCES products (id)';
$queries['alter'][97] = 'ALTER TABLE ratings ADD CONSTRAINT FK_CEB607C9546A72F3 FOREIGN KEY (searchlog_id) REFERENCES searchlog (id) ON DELETE SET NULL, ADD CONSTRAINT FK_CEB607C9217BBB47 FOREIGN KEY (person_id) REFERENCES people (id) ON DELETE SET NULL, ADD CONSTRAINT FK_CEB607C970BEE6D FOREIGN KEY (visitor_id) REFERENCES visitors (id) ON DELETE SET NULL';
$queries['alter'][98] = 'ALTER TABLE report_builder ADD CONSTRAINT FK_B6BED249727ACA70 FOREIGN KEY (parent_id) REFERENCES report_builder (id) ON DELETE SET NULL';
$queries['alter'][99] = 'ALTER TABLE report_builder_favorite ADD CONSTRAINT FK_CCD5CB1186DD4ADF FOREIGN KEY (report_builder_id) REFERENCES report_builder (id) ON DELETE CASCADE, ADD CONSTRAINT FK_CCD5CB11217BBB47 FOREIGN KEY (person_id) REFERENCES people (id) ON DELETE CASCADE';
$queries['alter'][100] = 'ALTER TABLE result_cache ADD CONSTRAINT FK_D0B33C6B217BBB47 FOREIGN KEY (person_id) REFERENCES people (id) ON DELETE CASCADE';
$queries['alter'][101] = 'ALTER TABLE searchlog ADD CONSTRAINT FK_8C79CD5C217BBB47 FOREIGN KEY (person_id) REFERENCES people (id) ON DELETE SET NULL, ADD CONSTRAINT FK_8C79CD5C70BEE6D FOREIGN KEY (visitor_id) REFERENCES visitors (id) ON DELETE SET NULL';
$queries['alter'][102] = 'ALTER TABLE sendmail_logs ADD CONSTRAINT FK_D9E8157F700047D2 FOREIGN KEY (ticket_id) REFERENCES tickets (id) ON DELETE SET NULL, ADD CONSTRAINT FK_D9E8157FC5E9817D FOREIGN KEY (ticket_message_id) REFERENCES tickets_messages (id) ON DELETE SET NULL, ADD CONSTRAINT FK_D9E8157F217BBB47 FOREIGN KEY (person_id) REFERENCES people (id) ON DELETE SET NULL';
$queries['alter'][103] = 'ALTER TABLE sendmail_queue ADD CONSTRAINT FK_DDB369C2ED3E8EA5 FOREIGN KEY (blob_id) REFERENCES blobs (id) ON DELETE SET NULL';
$queries['alter'][104] = 'ALTER TABLE sessions ADD CONSTRAINT FK_9A609D13217BBB47 FOREIGN KEY (person_id) REFERENCES people (id) ON DELETE CASCADE, ADD CONSTRAINT FK_9A609D1370BEE6D FOREIGN KEY (visitor_id) REFERENCES visitors (id) ON DELETE SET NULL';
$queries['alter'][105] = 'ALTER TABLE slas ADD CONSTRAINT FK_ACE9984A91D0B882 FOREIGN KEY (warning_trigger_id) REFERENCES ticket_triggers (id) ON DELETE SET NULL, ADD CONSTRAINT FK_ACE9984A55EA90D4 FOREIGN KEY (fail_trigger_id) REFERENCES ticket_triggers (id) ON DELETE SET NULL, ADD CONSTRAINT FK_ACE9984A13CC0145 FOREIGN KEY (apply_priority_id) REFERENCES ticket_priorities (id) ON DELETE SET NULL, ADD CONSTRAINT FK_ACE9984AED1A7B28 FOREIGN KEY (apply_trigger_id) REFERENCES ticket_triggers (id) ON DELETE SET NULL';
$queries['alter'][106] = 'ALTER TABLE sla_people ADD CONSTRAINT FK_14ABD6A37A2CC8C4 FOREIGN KEY (sla_id) REFERENCES slas (id) ON DELETE CASCADE, ADD CONSTRAINT FK_14ABD6A3217BBB47 FOREIGN KEY (person_id) REFERENCES people (id) ON DELETE CASCADE';
$queries['alter'][107] = 'ALTER TABLE sla_organizations ADD CONSTRAINT FK_A7F081987A2CC8C4 FOREIGN KEY (sla_id) REFERENCES slas (id) ON DELETE CASCADE, ADD CONSTRAINT FK_A7F0819832C8A3DE FOREIGN KEY (organization_id) REFERENCES organizations (id) ON DELETE CASCADE';
$queries['alter'][108] = 'ALTER TABLE styles ADD CONSTRAINT FK_B65AFAF5727ACA70 FOREIGN KEY (parent_id) REFERENCES styles (id) ON DELETE CASCADE, ADD CONSTRAINT FK_B65AFAF5D91464D5 FOREIGN KEY (logo_blob_id) REFERENCES blobs (id) ON DELETE SET NULL, ADD CONSTRAINT FK_B65AFAF58AB530EF FOREIGN KEY (css_blob_id) REFERENCES blobs (id) ON DELETE SET NULL, ADD CONSTRAINT FK_B65AFAF5FEED6A62 FOREIGN KEY (css_blob_rtl_id) REFERENCES blobs (id) ON DELETE SET NULL';
$queries['alter'][109] = 'ALTER TABLE tasks ADD CONSTRAINT FK_50586597217BBB47 FOREIGN KEY (person_id) REFERENCES people (id) ON DELETE SET NULL, ADD CONSTRAINT FK_5058659749197702 FOREIGN KEY (assigned_agent_id) REFERENCES people (id) ON DELETE SET NULL, ADD CONSTRAINT FK_50586597410D1341 FOREIGN KEY (assigned_agent_team_id) REFERENCES agent_teams (id) ON DELETE CASCADE';
$queries['alter'][110] = 'ALTER TABLE task_associations ADD CONSTRAINT FK_41B0E09C8DB60186 FOREIGN KEY (task_id) REFERENCES tasks (id) ON DELETE CASCADE, ADD CONSTRAINT FK_41B0E09C217BBB47 FOREIGN KEY (person_id) REFERENCES people (id) ON DELETE CASCADE, ADD CONSTRAINT FK_41B0E09C700047D2 FOREIGN KEY (ticket_id) REFERENCES tickets (id) ON DELETE CASCADE, ADD CONSTRAINT FK_41B0E09C32C8A3DE FOREIGN KEY (organization_id) REFERENCES organizations (id) ON DELETE CASCADE';
$queries['alter'][111] = 'ALTER TABLE task_comments ADD CONSTRAINT FK_1F5E7C668DB60186 FOREIGN KEY (task_id) REFERENCES tasks (id) ON DELETE CASCADE, ADD CONSTRAINT FK_1F5E7C66217BBB47 FOREIGN KEY (person_id) REFERENCES people (id) ON DELETE SET NULL';
$queries['alter'][112] = 'ALTER TABLE task_reminder_logs ADD CONSTRAINT FK_A264D7248DB60186 FOREIGN KEY (task_id) REFERENCES tasks (id) ON DELETE CASCADE, ADD CONSTRAINT FK_A264D724217BBB47 FOREIGN KEY (person_id) REFERENCES people (id) ON DELETE CASCADE';
$queries['alter'][113] = 'ALTER TABLE templates ADD CONSTRAINT FK_6F287D8EBACD6074 FOREIGN KEY (style_id) REFERENCES styles (id) ON DELETE CASCADE';
$queries['alter'][114] = 'ALTER TABLE text_snippets ADD CONSTRAINT FK_5B6379CE217BBB47 FOREIGN KEY (person_id) REFERENCES people (id) ON DELETE SET NULL, ADD CONSTRAINT FK_5B6379CE12469DE2 FOREIGN KEY (category_id) REFERENCES text_snippet_categories (id) ON DELETE CASCADE';
$queries['alter'][115] = 'ALTER TABLE text_snippet_categories ADD CONSTRAINT FK_F3B50AF1217BBB47 FOREIGN KEY (person_id) REFERENCES people (id) ON DELETE SET NULL';
$queries['alter'][116] = 'ALTER TABLE tickets ADD CONSTRAINT FK_54469DF4814B683C FOREIGN KEY (parent_ticket_id) REFERENCES tickets (id) ON DELETE SET NULL, ADD CONSTRAINT FK_54469DF482F1BAF4 FOREIGN KEY (language_id) REFERENCES languages (id) ON DELETE SET NULL, ADD CONSTRAINT FK_54469DF4AE80F5DF FOREIGN KEY (department_id) REFERENCES departments (id) ON DELETE SET NULL, ADD CONSTRAINT FK_54469DF412469DE2 FOREIGN KEY (category_id) REFERENCES ticket_categories (id) ON DELETE SET NULL, ADD CONSTRAINT FK_54469DF4497B19F9 FOREIGN KEY (priority_id) REFERENCES ticket_priorities (id) ON DELETE SET NULL, ADD CONSTRAINT FK_54469DF42C7C2CBA FOREIGN KEY (workflow_id) REFERENCES ticket_workflows (id) ON DELETE SET NULL, ADD CONSTRAINT FK_54469DF44584665A FOREIGN KEY (product_id) REFERENCES products (id) ON DELETE SET NULL, ADD CONSTRAINT FK_54469DF4217BBB47 FOREIGN KEY (person_id) REFERENCES people (id) ON DELETE CASCADE, ADD CONSTRAINT FK_54469DF43C7464FE FOREIGN KEY (person_email_id) REFERENCES people_emails (id) ON DELETE SET NULL, ADD CONSTRAINT FK_54469DF4581A624E FOREIGN KEY (person_email_validating_id) REFERENCES people_emails_validating (id) ON DELETE SET NULL, ADD CONSTRAINT FK_54469DF43414710B FOREIGN KEY (agent_id) REFERENCES people (id) ON DELETE SET NULL, ADD CONSTRAINT FK_54469DF4FB3FBA04 FOREIGN KEY (agent_team_id) REFERENCES agent_teams (id) ON DELETE SET NULL, ADD CONSTRAINT FK_54469DF432C8A3DE FOREIGN KEY (organization_id) REFERENCES organizations (id) ON DELETE SET NULL, ADD CONSTRAINT FK_54469DF471D249B2 FOREIGN KEY (linked_chat_id) REFERENCES chat_conversations (id) ON DELETE SET NULL, ADD CONSTRAINT FK_54469DF4FBCC7CDF FOREIGN KEY (email_gateway_id) REFERENCES email_gateways (id) ON DELETE SET NULL, ADD CONSTRAINT FK_54469DF4F2598614 FOREIGN KEY (email_gateway_address_id) REFERENCES email_gateway_addresses (id) ON DELETE SET NULL, ADD CONSTRAINT FK_54469DF4428359E2 FOREIGN KEY (locked_by_agent) REFERENCES people (id) ON DELETE SET NULL';
$queries['alter'][117] = 'ALTER TABLE ticket_access_codes ADD CONSTRAINT FK_CCEE41B5700047D2 FOREIGN KEY (ticket_id) REFERENCES tickets (id) ON DELETE CASCADE, ADD CONSTRAINT FK_CCEE41B5217BBB47 FOREIGN KEY (person_id) REFERENCES people (id) ON DELETE CASCADE';
$queries['alter'][118] = 'ALTER TABLE tickets_attachments ADD CONSTRAINT FK_F06B468D700047D2 FOREIGN KEY (ticket_id) REFERENCES tickets (id) ON DELETE CASCADE, ADD CONSTRAINT FK_F06B468D217BBB47 FOREIGN KEY (person_id) REFERENCES people (id) ON DELETE SET NULL, ADD CONSTRAINT FK_F06B468DED3E8EA5 FOREIGN KEY (blob_id) REFERENCES blobs (id) ON DELETE CASCADE, ADD CONSTRAINT FK_F06B468D537A1329 FOREIGN KEY (message_id) REFERENCES tickets_messages (id) ON DELETE CASCADE';
$queries['alter'][119] = 'ALTER TABLE ticket_categories ADD CONSTRAINT FK_AC60D43C727ACA70 FOREIGN KEY (parent_id) REFERENCES ticket_categories (id) ON DELETE CASCADE';
$queries['alter'][120] = 'ALTER TABLE ticket_changetracker_logs ADD CONSTRAINT FK_F2205216700047D2 FOREIGN KEY (ticket_id) REFERENCES tickets (id) ON DELETE CASCADE';
$queries['alter'][121] = 'ALTER TABLE ticket_charges ADD CONSTRAINT FK_36230948700047D2 FOREIGN KEY (ticket_id) REFERENCES tickets (id) ON DELETE CASCADE, ADD CONSTRAINT FK_36230948217BBB47 FOREIGN KEY (person_id) REFERENCES people (id) ON DELETE CASCADE, ADD CONSTRAINT FK_3623094832C8A3DE FOREIGN KEY (organization_id) REFERENCES organizations (id) ON DELETE CASCADE, ADD CONSTRAINT FK_362309483414710B FOREIGN KEY (agent_id) REFERENCES people (id) ON DELETE SET NULL';
$queries['alter'][122] = 'ALTER TABLE tickets_deleted ADD CONSTRAINT FK_7EDF2278B5BE2AA2 FOREIGN KEY (by_person_id) REFERENCES people (id) ON DELETE SET NULL';
$queries['alter'][123] = 'ALTER TABLE ticket_feedback ADD CONSTRAINT FK_5740B8D9700047D2 FOREIGN KEY (ticket_id) REFERENCES tickets (id) ON DELETE CASCADE, ADD CONSTRAINT FK_5740B8D9537A1329 FOREIGN KEY (message_id) REFERENCES tickets_messages (id) ON DELETE CASCADE, ADD CONSTRAINT FK_5740B8D9217BBB47 FOREIGN KEY (person_id) REFERENCES people (id) ON DELETE CASCADE';
$queries['alter'][124] = 'ALTER TABLE ticket_filters ADD CONSTRAINT FK_74BB3EDF217BBB47 FOREIGN KEY (person_id) REFERENCES people (id) ON DELETE CASCADE, ADD CONSTRAINT FK_74BB3EDFFB3FBA04 FOREIGN KEY (agent_team_id) REFERENCES agent_teams (id) ON DELETE CASCADE';
$queries['alter'][125] = 'ALTER TABLE ticket_filters_perms ADD CONSTRAINT FK_93E8D427D395B25E FOREIGN KEY (filter_id) REFERENCES ticket_filters (id) ON DELETE CASCADE';
$queries['alter'][126] = 'ALTER TABLE ticket_filter_subscriptions ADD CONSTRAINT FK_13669D98D395B25E FOREIGN KEY (filter_id) REFERENCES ticket_filters (id) ON DELETE CASCADE, ADD CONSTRAINT FK_13669D98217BBB47 FOREIGN KEY (person_id) REFERENCES people (id) ON DELETE CASCADE';
$queries['alter'][127] = 'ALTER TABLE tickets_logs ADD CONSTRAINT FK_F5F41081727ACA70 FOREIGN KEY (parent_id) REFERENCES tickets_logs (id) ON DELETE CASCADE, ADD CONSTRAINT FK_F5F41081700047D2 FOREIGN KEY (ticket_id) REFERENCES tickets (id) ON DELETE CASCADE, ADD CONSTRAINT FK_F5F41081217BBB47 FOREIGN KEY (person_id) REFERENCES people (id) ON DELETE SET NULL, ADD CONSTRAINT FK_F5F410817A2CC8C4 FOREIGN KEY (sla_id) REFERENCES slas (id) ON DELETE SET NULL';
$queries['alter'][128] = 'ALTER TABLE ticket_macros ADD CONSTRAINT FK_8E373A2C217BBB47 FOREIGN KEY (person_id) REFERENCES people (id) ON DELETE SET NULL';
$queries['alter'][129] = 'ALTER TABLE ticket_macros_perms ADD CONSTRAINT FK_EAB2E6D5F43A187E FOREIGN KEY (macro_id) REFERENCES ticket_macros (id) ON DELETE CASCADE';
$queries['alter'][130] = 'ALTER TABLE tickets_messages ADD CONSTRAINT FK_3A9962E2700047D2 FOREIGN KEY (ticket_id) REFERENCES tickets (id) ON DELETE CASCADE, ADD CONSTRAINT FK_3A9962E2217BBB47 FOREIGN KEY (person_id) REFERENCES people (id) ON DELETE SET NULL, ADD CONSTRAINT FK_3A9962E29A37834A FOREIGN KEY (email_source_id) REFERENCES email_sources (id) ON DELETE SET NULL, ADD CONSTRAINT FK_3A9962E2251FB291 FOREIGN KEY (message_translated_id) REFERENCES tickets_messages_translated (id) ON DELETE SET NULL, ADD CONSTRAINT FK_3A9962E270BEE6D FOREIGN KEY (visitor_id) REFERENCES visitors (id) ON DELETE SET NULL';
$queries['alter'][131] = 'ALTER TABLE tickets_messages_raw ADD CONSTRAINT FK_672BCB3537A1329 FOREIGN KEY (message_id) REFERENCES tickets_messages (id) ON DELETE CASCADE';
$queries['alter'][132] = 'ALTER TABLE ticket_message_templates ADD CONSTRAINT FK_8C28E2ECAE80F5DF FOREIGN KEY (department_id) REFERENCES departments (id) ON DELETE SET NULL';
$queries['alter'][133] = 'ALTER TABLE tickets_messages_translated ADD CONSTRAINT FK_EDCD3BB3700047D2 FOREIGN KEY (ticket_id) REFERENCES tickets (id) ON DELETE CASCADE, ADD CONSTRAINT FK_EDCD3BB3537A1329 FOREIGN KEY (message_id) REFERENCES tickets_messages (id) ON DELETE CASCADE';
$queries['alter'][134] = 'ALTER TABLE ticket_page_display ADD CONSTRAINT FK_3667659DAE80F5DF FOREIGN KEY (department_id) REFERENCES departments (id) ON DELETE CASCADE';
$queries['alter'][135] = 'ALTER TABLE tickets_participants ADD CONSTRAINT FK_8D675752700047D2 FOREIGN KEY (ticket_id) REFERENCES tickets (id) ON DELETE CASCADE, ADD CONSTRAINT FK_8D675752217BBB47 FOREIGN KEY (person_id) REFERENCES people (id) ON DELETE CASCADE, ADD CONSTRAINT FK_8D675752EFFF2402 FOREIGN KEY (access_code_id) REFERENCES ticket_access_codes (id) ON DELETE CASCADE, ADD CONSTRAINT FK_8D6757523C7464FE FOREIGN KEY (person_email_id) REFERENCES people_emails (id) ON DELETE SET NULL';
$queries['alter'][136] = 'ALTER TABLE ticket_slas ADD CONSTRAINT FK_9E328D72700047D2 FOREIGN KEY (ticket_id) REFERENCES tickets (id) ON DELETE CASCADE, ADD CONSTRAINT FK_9E328D727A2CC8C4 FOREIGN KEY (sla_id) REFERENCES slas (id) ON DELETE CASCADE';
$queries['alter'][137] = 'ALTER TABLE ticket_trigger_plugin_actions ADD CONSTRAINT FK_1D905890EC942BCF FOREIGN KEY (plugin_id) REFERENCES plugins (id) ON DELETE CASCADE';
$queries['alter'][138] = 'ALTER TABLE twitter_accounts ADD CONSTRAINT FK_D4051D30A76ED395 FOREIGN KEY (user_id) REFERENCES twitter_users (id) ON DELETE CASCADE';
$queries['alter'][139] = 'ALTER TABLE twitter_accounts_person ADD CONSTRAINT FK_BB12235C9B6B5FBA FOREIGN KEY (account_id) REFERENCES twitter_accounts (id) ON DELETE CASCADE, ADD CONSTRAINT FK_BB12235C217BBB47 FOREIGN KEY (person_id) REFERENCES people (id) ON DELETE CASCADE';
$queries['alter'][140] = 'ALTER TABLE twitter_accounts_followers ADD CONSTRAINT FK_EB8452969B6B5FBA FOREIGN KEY (account_id) REFERENCES twitter_accounts (id) ON DELETE CASCADE, ADD CONSTRAINT FK_EB845296A76ED395 FOREIGN KEY (user_id) REFERENCES twitter_users (id) ON DELETE CASCADE';
$queries['alter'][141] = 'ALTER TABLE twitter_accounts_friends ADD CONSTRAINT FK_FADA774D9B6B5FBA FOREIGN KEY (account_id) REFERENCES twitter_accounts (id) ON DELETE CASCADE, ADD CONSTRAINT FK_FADA774DA76ED395 FOREIGN KEY (user_id) REFERENCES twitter_users (id) ON DELETE CASCADE';
$queries['alter'][142] = 'ALTER TABLE twitter_accounts_searches ADD CONSTRAINT FK_5CC0E8CF9B6B5FBA FOREIGN KEY (account_id) REFERENCES twitter_accounts (id) ON DELETE CASCADE';
$queries['alter'][143] = 'ALTER TABLE twitter_accounts_searches_statuses ADD CONSTRAINT FK_E52AFE3B498DD8E6 FOREIGN KEY (account_status_id) REFERENCES twitter_accounts_statuses (id) ON DELETE CASCADE, ADD CONSTRAINT FK_E52AFE3B650760A9 FOREIGN KEY (search_id) REFERENCES twitter_accounts_searches (id) ON DELETE CASCADE';
$queries['alter'][144] = 'ALTER TABLE twitter_accounts_statuses ADD CONSTRAINT FK_7728CEC79B6B5FBA FOREIGN KEY (account_id) REFERENCES twitter_accounts (id) ON DELETE SET NULL, ADD CONSTRAINT FK_7728CEC76BF700BD FOREIGN KEY (status_id) REFERENCES twitter_statuses (id) ON DELETE CASCADE, ADD CONSTRAINT FK_7728CEC73414710B FOREIGN KEY (agent_id) REFERENCES people (id) ON DELETE SET NULL, ADD CONSTRAINT FK_7728CEC7FB3FBA04 FOREIGN KEY (agent_team_id) REFERENCES agent_teams (id) ON DELETE SET NULL, ADD CONSTRAINT FK_7728CEC7E3C3016D FOREIGN KEY (action_agent_id) REFERENCES people (id) ON DELETE SET NULL, ADD CONSTRAINT FK_7728CEC754E76E81 FOREIGN KEY (retweeted_id) REFERENCES twitter_accounts_statuses (id) ON DELETE SET NULL, ADD CONSTRAINT FK_7728CEC7DD92DAB8 FOREIGN KEY (in_reply_to_id) REFERENCES twitter_accounts_statuses (id) ON DELETE SET NULL';
$queries['alter'][145] = 'ALTER TABLE twitter_accounts_statuses_notes ADD CONSTRAINT FK_E5D3CBA2498DD8E6 FOREIGN KEY (account_status_id) REFERENCES twitter_accounts_statuses (id) ON DELETE CASCADE, ADD CONSTRAINT FK_E5D3CBA2217BBB47 FOREIGN KEY (person_id) REFERENCES people (id) ON DELETE SET NULL';
$queries['alter'][146] = 'ALTER TABLE twitter_statuses ADD CONSTRAINT FK_553D9D8DA76ED395 FOREIGN KEY (user_id) REFERENCES twitter_users (id), ADD CONSTRAINT FK_553D9D8D6B347969 FOREIGN KEY (in_reply_to_status_id) REFERENCES twitter_statuses (id) ON DELETE SET NULL, ADD CONSTRAINT FK_553D9D8D72A1C5CA FOREIGN KEY (retweet_id) REFERENCES twitter_statuses (id) ON DELETE CASCADE, ADD CONSTRAINT FK_553D9D8DD2347268 FOREIGN KEY (in_reply_to_user_id) REFERENCES twitter_users (id), ADD CONSTRAINT FK_553D9D8DE92F8F78 FOREIGN KEY (recipient_id) REFERENCES twitter_users (id)';
$queries['alter'][147] = 'ALTER TABLE twitter_statuses_long ADD CONSTRAINT FK_8B914BFB6BF700BD FOREIGN KEY (status_id) REFERENCES twitter_statuses (id) ON DELETE CASCADE, ADD CONSTRAINT FK_8B914BFB9B5BB4B8 FOREIGN KEY (for_user_id) REFERENCES twitter_users (id) ON DELETE CASCADE';
$queries['alter'][148] = 'ALTER TABLE twitter_statuses_mentions ADD CONSTRAINT FK_66912DD16BF700BD FOREIGN KEY (status_id) REFERENCES twitter_statuses (id) ON DELETE CASCADE, ADD CONSTRAINT FK_66912DD1A76ED395 FOREIGN KEY (user_id) REFERENCES twitter_users (id) ON DELETE CASCADE';
$queries['alter'][149] = 'ALTER TABLE twitter_statuses_tags ADD CONSTRAINT FK_DFBA76B56BF700BD FOREIGN KEY (status_id) REFERENCES twitter_statuses (id) ON DELETE CASCADE';
$queries['alter'][150] = 'ALTER TABLE twitter_statuses_urls ADD CONSTRAINT FK_9A92D5326BF700BD FOREIGN KEY (status_id) REFERENCES twitter_statuses (id) ON DELETE CASCADE';
$queries['alter'][151] = 'ALTER TABLE twitter_stream ADD CONSTRAINT FK_8D6AB9A89B6B5FBA FOREIGN KEY (account_id) REFERENCES twitter_accounts (id) ON DELETE CASCADE';
$queries['alter'][152] = 'ALTER TABLE twitter_users_followers ADD CONSTRAINT FK_F37AF1BEA76ED395 FOREIGN KEY (user_id) REFERENCES twitter_users (id) ON DELETE CASCADE, ADD CONSTRAINT FK_F37AF1BE70FC2906 FOREIGN KEY (follower_user_id) REFERENCES twitter_users (id) ON DELETE CASCADE';
$queries['alter'][153] = 'ALTER TABLE twitter_users_friends ADD CONSTRAINT FK_77C2EDABA76ED395 FOREIGN KEY (user_id) REFERENCES twitter_users (id) ON DELETE CASCADE, ADD CONSTRAINT FK_77C2EDAB93D1119E FOREIGN KEY (friend_user_id) REFERENCES twitter_users (id) ON DELETE CASCADE';
$queries['alter'][154] = 'ALTER TABLE user_rules ADD CONSTRAINT FK_6B5862642940B3FB FOREIGN KEY (add_organization_id) REFERENCES organizations (id) ON DELETE CASCADE, ADD CONSTRAINT FK_6B586264A19F75EA FOREIGN KEY (add_usergroup_id) REFERENCES usergroups (id) ON DELETE CASCADE';
$queries['alter'][155] = 'ALTER TABLE usersources ADD CONSTRAINT FK_4E3C994CEB0D3362 FOREIGN KEY (usersource_plugin_id) REFERENCES usersource_plugins (id) ON DELETE CASCADE';
$queries['alter'][156] = 'ALTER TABLE usersource_plugins ADD CONSTRAINT FK_E484A367EC942BCF FOREIGN KEY (plugin_id) REFERENCES plugins (id) ON DELETE CASCADE';
$queries['alter'][157] = 'ALTER TABLE visitors ADD CONSTRAINT FK_7B74A43F217BBB47 FOREIGN KEY (person_id) REFERENCES people (id) ON DELETE SET NULL, ADD CONSTRAINT FK_7B74A43F866B65F3 FOREIGN KEY (initial_track_id) REFERENCES visitor_tracks (id) ON DELETE SET NULL, ADD CONSTRAINT FK_7B74A43F5B84E254 FOREIGN KEY (visit_track_id) REFERENCES visitor_tracks (id) ON DELETE SET NULL, ADD CONSTRAINT FK_7B74A43F26B379DD FOREIGN KEY (last_track_id) REFERENCES visitor_tracks (id) ON DELETE SET NULL, ADD CONSTRAINT FK_7B74A43F413BC2FF FOREIGN KEY (last_track_id_soft) REFERENCES visitor_tracks (id) ON DELETE SET NULL';
$queries['alter'][158] = 'ALTER TABLE visitor_tracks ADD CONSTRAINT FK_E002459270BEE6D FOREIGN KEY (visitor_id) REFERENCES visitors (id) ON DELETE CASCADE';
$queries['alter'][159] = 'ALTER TABLE widgets ADD CONSTRAINT FK_9D58E4C1EC942BCF FOREIGN KEY (plugin_id) REFERENCES plugins (id) ON DELETE CASCADE';









$queries['index']['agent_activity'] = array(
	'IDX_9AA510CE3414710B' => 'CREATE INDEX IDX_9AA510CE3414710B ON agent_activity (agent_id)',
	'primary' => 'ALTER TABLE agent_activity ADD PRIMARY KEY (date_active, agent_id)',
	'date_created_idx' => 'CREATE INDEX date_created_idx ON agent_activity (date_active)',
);
$queries['index']['agent_alerts'] = array(
	'IDX_A99D974D217BBB47' => 'CREATE INDEX IDX_A99D974D217BBB47 ON agent_alerts (person_id)',
	'primary' => 'ALTER TABLE agent_alerts ADD PRIMARY KEY (id)',
	'date_created_idx' => 'CREATE INDEX date_created_idx ON agent_alerts (date_created)',
);
$queries['index']['agent_teams'] = array(
	'primary' => 'ALTER TABLE agent_teams ADD PRIMARY KEY (id)',
);
$queries['index']['agent_team_members'] = array(
	'IDX_CC952C03296CD8AE' => 'CREATE INDEX IDX_CC952C03296CD8AE ON agent_team_members (team_id)',
	'IDX_CC952C03217BBB47' => 'CREATE INDEX IDX_CC952C03217BBB47 ON agent_team_members (person_id)',
	'primary' => 'ALTER TABLE agent_team_members ADD PRIMARY KEY (team_id, person_id)',
);
$queries['index']['api_keys'] = array(
	'IDX_9579321F217BBB47' => 'CREATE INDEX IDX_9579321F217BBB47 ON api_keys (person_id)',
	'primary' => 'ALTER TABLE api_keys ADD PRIMARY KEY (id)',
);
$queries['index']['api_key_rate_limit'] = array(
	'primary' => 'ALTER TABLE api_key_rate_limit ADD PRIMARY KEY (api_key_id)',
);
$queries['index']['api_token'] = array(
	'primary' => 'ALTER TABLE api_token ADD PRIMARY KEY (person_id)',
);
$queries['index']['api_token_rate_limit'] = array(
	'primary' => 'ALTER TABLE api_token_rate_limit ADD PRIMARY KEY (person_id)',
);
$queries['index']['articles'] = array(
	'IDX_BFDD3168217BBB47' => 'CREATE INDEX IDX_BFDD3168217BBB47 ON articles (person_id)',
	'IDX_BFDD316882F1BAF4' => 'CREATE INDEX IDX_BFDD316882F1BAF4 ON articles (language_id)',
	'primary' => 'ALTER TABLE articles ADD PRIMARY KEY (id)',
	'date_published_idx' => 'CREATE INDEX date_published_idx ON articles (date_published)',
	'date_updated_idx' => 'CREATE INDEX date_updated_idx ON articles (date_updated)',
	'date_last_comment_idx' => 'CREATE INDEX date_last_comment_idx ON articles (date_last_comment)',
	'status_idx' => 'CREATE INDEX status_idx ON articles (status)',
);
$queries['index']['article_to_categories'] = array(
	'IDX_9A1B4BB07294869C' => 'CREATE INDEX IDX_9A1B4BB07294869C ON article_to_categories (article_id)',
	'IDX_9A1B4BB012469DE2' => 'CREATE INDEX IDX_9A1B4BB012469DE2 ON article_to_categories (category_id)',
	'primary' => 'ALTER TABLE article_to_categories ADD PRIMARY KEY (article_id, category_id)',
);
$queries['index']['article_to_product'] = array(
	'IDX_610BE8D97294869C' => 'CREATE INDEX IDX_610BE8D97294869C ON article_to_product (article_id)',
	'IDX_610BE8D94584665A' => 'CREATE INDEX IDX_610BE8D94584665A ON article_to_product (product_id)',
	'primary' => 'ALTER TABLE article_to_product ADD PRIMARY KEY (article_id, product_id)',
);
$queries['index']['article_attachments'] = array(
	'IDX_DD4790B17294869C' => 'CREATE INDEX IDX_DD4790B17294869C ON article_attachments (article_id)',
	'IDX_DD4790B1217BBB47' => 'CREATE INDEX IDX_DD4790B1217BBB47 ON article_attachments (person_id)',
	'IDX_DD4790B1ED3E8EA5' => 'CREATE INDEX IDX_DD4790B1ED3E8EA5 ON article_attachments (blob_id)',
	'primary' => 'ALTER TABLE article_attachments ADD PRIMARY KEY (id)',
);
$queries['index']['article_categories'] = array(
	'IDX_62A97E9727ACA70' => 'CREATE INDEX IDX_62A97E9727ACA70 ON article_categories (parent_id)',
	'primary' => 'ALTER TABLE article_categories ADD PRIMARY KEY (id)',
);
$queries['index']['article_category2usergroup'] = array(
	'IDX_6AD8B03212469DE2' => 'CREATE INDEX IDX_6AD8B03212469DE2 ON article_category2usergroup (category_id)',
	'IDX_6AD8B032D2112630' => 'CREATE INDEX IDX_6AD8B032D2112630 ON article_category2usergroup (usergroup_id)',
	'primary' => 'ALTER TABLE article_category2usergroup ADD PRIMARY KEY (category_id, usergroup_id)',
);
$queries['index']['article_comments'] = array(
	'IDX_A7662417294869C' => 'CREATE INDEX IDX_A7662417294869C ON article_comments (article_id)',
	'IDX_A766241217BBB47' => 'CREATE INDEX IDX_A766241217BBB47 ON article_comments (person_id)',
	'IDX_A76624170BEE6D' => 'CREATE INDEX IDX_A76624170BEE6D ON article_comments (visitor_id)',
	'primary' => 'ALTER TABLE article_comments ADD PRIMARY KEY (id)',
	'status_idx' => 'CREATE INDEX status_idx ON article_comments (status, is_reviewed)',
);
$queries['index']['article_pending_create'] = array(
	'IDX_27A971C3217BBB47' => 'CREATE INDEX IDX_27A971C3217BBB47 ON article_pending_create (person_id)',
	'IDX_27A971C3700047D2' => 'CREATE INDEX IDX_27A971C3700047D2 ON article_pending_create (ticket_id)',
	'IDX_27A971C3C5E9817D' => 'CREATE INDEX IDX_27A971C3C5E9817D ON article_pending_create (ticket_message_id)',
	'primary' => 'ALTER TABLE article_pending_create ADD PRIMARY KEY (id)',
);
$queries['index']['article_revisions'] = array(
	'IDX_538472A17294869C' => 'CREATE INDEX IDX_538472A17294869C ON article_revisions (article_id)',
	'IDX_538472A1217BBB47' => 'CREATE INDEX IDX_538472A1217BBB47 ON article_revisions (person_id)',
	'primary' => 'ALTER TABLE article_revisions ADD PRIMARY KEY (id)',
);
$queries['index']['ban_emails'] = array(
	'primary' => 'ALTER TABLE ban_emails ADD PRIMARY KEY (banned_email)',
);
$queries['index']['ban_ips'] = array(
	'primary' => 'ALTER TABLE ban_ips ADD PRIMARY KEY (banned_ip)',
);
$queries['index']['blobs'] = array(
	'IDX_896C3E356BBE2052' => 'CREATE INDEX IDX_896C3E356BBE2052 ON blobs (original_blob_id)',
	'primary' => 'ALTER TABLE blobs ADD PRIMARY KEY (id)',
	'authcode_idx' => 'CREATE INDEX authcode_idx ON blobs (authcode)',
	'storage_loc_idx' => 'CREATE INDEX storage_loc_idx ON blobs (storage_loc, storage_loc_pref)',
);
$queries['index']['blobs_storage'] = array(
	'primary' => 'ALTER TABLE blobs_storage ADD PRIMARY KEY (id)',
	'blob_id_idx' => 'CREATE INDEX blob_id_idx ON blobs_storage (blob_id)',
);
$queries['index']['cache'] = array(
	'primary' => 'ALTER TABLE cache ADD PRIMARY KEY (id)',
	'date_expire_idx' => 'CREATE INDEX date_expire_idx ON cache (date_expire)',
);
$queries['index']['chat_blocks'] = array(
	'IDX_A931A25970BEE6D' => 'CREATE INDEX IDX_A931A25970BEE6D ON chat_blocks (visitor_id)',
	'IDX_A931A259B5BE2AA2' => 'CREATE INDEX IDX_A931A259B5BE2AA2 ON chat_blocks (by_person_id)',
	'primary' => 'ALTER TABLE chat_blocks ADD PRIMARY KEY (id)',
);
$queries['index']['chat_conversations'] = array(
	'IDX_5813432EAE80F5DF' => 'CREATE INDEX IDX_5813432EAE80F5DF ON chat_conversations (department_id)',
	'IDX_5813432E3414710B' => 'CREATE INDEX IDX_5813432E3414710B ON chat_conversations (agent_id)',
	'IDX_5813432EFB3FBA04' => 'CREATE INDEX IDX_5813432EFB3FBA04 ON chat_conversations (agent_team_id)',
	'IDX_5813432E217BBB47' => 'CREATE INDEX IDX_5813432E217BBB47 ON chat_conversations (person_id)',
	'IDX_5813432E613FECDF' => 'CREATE INDEX IDX_5813432E613FECDF ON chat_conversations (session_id)',
	'IDX_5813432E70BEE6D' => 'CREATE INDEX IDX_5813432E70BEE6D ON chat_conversations (visitor_id)',
	'primary' => 'ALTER TABLE chat_conversations ADD PRIMARY KEY (id)',
	'status_idx' => 'CREATE INDEX status_idx ON chat_conversations (status)',
	'should_send_transcript_idx' => 'CREATE INDEX should_send_transcript_idx ON chat_conversations (should_send_transcript)',
);
$queries['index']['chat_conversation_to_person'] = array(
	'IDX_1CA5AE439AC0396' => 'CREATE INDEX IDX_1CA5AE439AC0396 ON chat_conversation_to_person (conversation_id)',
	'IDX_1CA5AE43217BBB47' => 'CREATE INDEX IDX_1CA5AE43217BBB47 ON chat_conversation_to_person (person_id)',
	'primary' => 'ALTER TABLE chat_conversation_to_person ADD PRIMARY KEY (conversation_id, person_id)',
);
$queries['index']['chat_conversation_pings'] = array(
	'primary' => 'ALTER TABLE chat_conversation_pings ADD PRIMARY KEY (id)',
	'chat_id_idx' => 'CREATE INDEX chat_id_idx ON chat_conversation_pings (chat_id)',
);
$queries['index']['chat_messages'] = array(
	'IDX_EF20C9A69AC0396' => 'CREATE INDEX IDX_EF20C9A69AC0396 ON chat_messages (conversation_id)',
	'IDX_EF20C9A6F675F31B' => 'CREATE INDEX IDX_EF20C9A6F675F31B ON chat_messages (author_id)',
	'primary' => 'ALTER TABLE chat_messages ADD PRIMARY KEY (id)',
);
$queries['index']['chat_page_display'] = array(
	'IDX_85AF0B7AE80F5DF' => 'CREATE INDEX IDX_85AF0B7AE80F5DF ON chat_page_display (department_id)',
	'primary' => 'ALTER TABLE chat_page_display ADD PRIMARY KEY (id)',
);
$queries['index']['client_messages'] = array(
	'IDX_F5E42E53D2872966' => 'CREATE INDEX IDX_F5E42E53D2872966 ON client_messages (for_person_id)',
	'primary' => 'ALTER TABLE client_messages ADD PRIMARY KEY (id)',
);
$queries['index']['content_search_attribute'] = array(
	'primary' => 'ALTER TABLE content_search_attribute ADD PRIMARY KEY (object_type, object_id, attribute_id, content)',
);
$queries['index']['content_subscriptions'] = array(
	'IDX_5FADAC10217BBB47' => 'CREATE INDEX IDX_5FADAC10217BBB47 ON content_subscriptions (person_id)',
	'IDX_5FADAC107294869C' => 'CREATE INDEX IDX_5FADAC107294869C ON content_subscriptions (article_id)',
	'IDX_5FADAC10C667AEAB' => 'CREATE INDEX IDX_5FADAC10C667AEAB ON content_subscriptions (download_id)',
	'IDX_5FADAC10D249A887' => 'CREATE INDEX IDX_5FADAC10D249A887 ON content_subscriptions (feedback_id)',
	'IDX_5FADAC10B5A459A0' => 'CREATE INDEX IDX_5FADAC10B5A459A0 ON content_subscriptions (news_id)',
	'primary' => 'ALTER TABLE content_subscriptions ADD PRIMARY KEY (id)',
);
$queries['index']['custom_data_article'] = array(
	'IDX_1DB64F8C7294869C' => 'CREATE INDEX IDX_1DB64F8C7294869C ON custom_data_article (article_id)',
	'IDX_1DB64F8C443707B0' => 'CREATE INDEX IDX_1DB64F8C443707B0 ON custom_data_article (field_id)',
	'IDX_1DB64F8C3F6A6D56' => 'CREATE INDEX IDX_1DB64F8C3F6A6D56 ON custom_data_article (root_field_id)',
	'primary' => 'ALTER TABLE custom_data_article ADD PRIMARY KEY (id)',
	'field_id_idx' => 'CREATE INDEX field_id_idx ON custom_data_article (field_id, article_id)',
);
$queries['index']['custom_data_chat'] = array(
	'IDX_94E84EEE9AC0396' => 'CREATE INDEX IDX_94E84EEE9AC0396 ON custom_data_chat (conversation_id)',
	'IDX_94E84EEE443707B0' => 'CREATE INDEX IDX_94E84EEE443707B0 ON custom_data_chat (field_id)',
	'IDX_94E84EEE3F6A6D56' => 'CREATE INDEX IDX_94E84EEE3F6A6D56 ON custom_data_chat (root_field_id)',
	'primary' => 'ALTER TABLE custom_data_chat ADD PRIMARY KEY (id)',
	'field_id_idx' => 'CREATE INDEX field_id_idx ON custom_data_chat (field_id, conversation_id)',
);
$queries['index']['custom_data_feedback'] = array(
	'IDX_92E9C37FD249A887' => 'CREATE INDEX IDX_92E9C37FD249A887 ON custom_data_feedback (feedback_id)',
	'IDX_92E9C37F443707B0' => 'CREATE INDEX IDX_92E9C37F443707B0 ON custom_data_feedback (field_id)',
	'IDX_92E9C37F3F6A6D56' => 'CREATE INDEX IDX_92E9C37F3F6A6D56 ON custom_data_feedback (root_field_id)',
	'primary' => 'ALTER TABLE custom_data_feedback ADD PRIMARY KEY (id)',
	'field_id_idx' => 'CREATE INDEX field_id_idx ON custom_data_feedback (field_id, feedback_id)',
);
$queries['index']['custom_data_organizations'] = array(
	'IDX_20C5B8AC32C8A3DE' => 'CREATE INDEX IDX_20C5B8AC32C8A3DE ON custom_data_organizations (organization_id)',
	'IDX_20C5B8AC443707B0' => 'CREATE INDEX IDX_20C5B8AC443707B0 ON custom_data_organizations (field_id)',
	'IDX_20C5B8AC3F6A6D56' => 'CREATE INDEX IDX_20C5B8AC3F6A6D56 ON custom_data_organizations (root_field_id)',
	'primary' => 'ALTER TABLE custom_data_organizations ADD PRIMARY KEY (id)',
	'field_id_idx' => 'CREATE INDEX field_id_idx ON custom_data_organizations (field_id, organization_id)',
);
$queries['index']['custom_data_person'] = array(
	'IDX_621E55A5217BBB47' => 'CREATE INDEX IDX_621E55A5217BBB47 ON custom_data_person (person_id)',
	'IDX_621E55A5443707B0' => 'CREATE INDEX IDX_621E55A5443707B0 ON custom_data_person (field_id)',
	'IDX_621E55A53F6A6D56' => 'CREATE INDEX IDX_621E55A53F6A6D56 ON custom_data_person (root_field_id)',
	'primary' => 'ALTER TABLE custom_data_person ADD PRIMARY KEY (id)',
	'field_id_idx' => 'CREATE INDEX field_id_idx ON custom_data_person (field_id, person_id)',
);
$queries['index']['custom_data_product'] = array(
	'IDX_CCC645474584665A' => 'CREATE INDEX IDX_CCC645474584665A ON custom_data_product (product_id)',
	'IDX_CCC64547443707B0' => 'CREATE INDEX IDX_CCC64547443707B0 ON custom_data_product (field_id)',
	'IDX_CCC645473F6A6D56' => 'CREATE INDEX IDX_CCC645473F6A6D56 ON custom_data_product (root_field_id)',
	'primary' => 'ALTER TABLE custom_data_product ADD PRIMARY KEY (id)',
	'field_id_idx' => 'CREATE INDEX field_id_idx ON custom_data_product (field_id, product_id)',
);
$queries['index']['custom_data_ticket'] = array(
	'IDX_C1622970700047D2' => 'CREATE INDEX IDX_C1622970700047D2 ON custom_data_ticket (ticket_id)',
	'IDX_C1622970443707B0' => 'CREATE INDEX IDX_C1622970443707B0 ON custom_data_ticket (field_id)',
	'IDX_C16229703F6A6D56' => 'CREATE INDEX IDX_C16229703F6A6D56 ON custom_data_ticket (root_field_id)',
	'primary' => 'ALTER TABLE custom_data_ticket ADD PRIMARY KEY (id)',
	'field_id_idx' => 'CREATE INDEX field_id_idx ON custom_data_ticket (field_id, ticket_id)',
);
$queries['index']['custom_def_article'] = array(
	'IDX_B651E6F4727ACA70' => 'CREATE INDEX IDX_B651E6F4727ACA70 ON custom_def_article (parent_id)',
	'IDX_B651E6F4EC942BCF' => 'CREATE INDEX IDX_B651E6F4EC942BCF ON custom_def_article (plugin_id)',
	'primary' => 'ALTER TABLE custom_def_article ADD PRIMARY KEY (id)',
);
$queries['index']['custom_def_chat'] = array(
	'IDX_2DE86CE5727ACA70' => 'CREATE INDEX IDX_2DE86CE5727ACA70 ON custom_def_chat (parent_id)',
	'IDX_2DE86CE5EC942BCF' => 'CREATE INDEX IDX_2DE86CE5EC942BCF ON custom_def_chat (plugin_id)',
	'primary' => 'ALTER TABLE custom_def_chat ADD PRIMARY KEY (id)',
);
$queries['index']['custom_def_feedback'] = array(
	'IDX_CC9CDDD8727ACA70' => 'CREATE INDEX IDX_CC9CDDD8727ACA70 ON custom_def_feedback (parent_id)',
	'IDX_CC9CDDD8EC942BCF' => 'CREATE INDEX IDX_CC9CDDD8EC942BCF ON custom_def_feedback (plugin_id)',
	'primary' => 'ALTER TABLE custom_def_feedback ADD PRIMARY KEY (id)',
);
$queries['index']['custom_def_organizations'] = array(
	'IDX_240601E7727ACA70' => 'CREATE INDEX IDX_240601E7727ACA70 ON custom_def_organizations (parent_id)',
	'IDX_240601E7EC942BCF' => 'CREATE INDEX IDX_240601E7EC942BCF ON custom_def_organizations (plugin_id)',
	'primary' => 'ALTER TABLE custom_def_organizations ADD PRIMARY KEY (id)',
);
$queries['index']['custom_def_people'] = array(
	'IDX_4840CFDA727ACA70' => 'CREATE INDEX IDX_4840CFDA727ACA70 ON custom_def_people (parent_id)',
	'IDX_4840CFDAEC942BCF' => 'CREATE INDEX IDX_4840CFDAEC942BCF ON custom_def_people (plugin_id)',
	'primary' => 'ALTER TABLE custom_def_people ADD PRIMARY KEY (id)',
);
$queries['index']['custom_def_products'] = array(
	'IDX_AD0FC3DA727ACA70' => 'CREATE INDEX IDX_AD0FC3DA727ACA70 ON custom_def_products (parent_id)',
	'IDX_AD0FC3DAEC942BCF' => 'CREATE INDEX IDX_AD0FC3DAEC942BCF ON custom_def_products (plugin_id)',
	'primary' => 'ALTER TABLE custom_def_products ADD PRIMARY KEY (id)',
);
$queries['index']['custom_def_ticket'] = array(
	'IDX_F7F6085F727ACA70' => 'CREATE INDEX IDX_F7F6085F727ACA70 ON custom_def_ticket (parent_id)',
	'IDX_F7F6085FEC942BCF' => 'CREATE INDEX IDX_F7F6085FEC942BCF ON custom_def_ticket (plugin_id)',
	'primary' => 'ALTER TABLE custom_def_ticket ADD PRIMARY KEY (id)',
);
$queries['index']['datastore'] = array(
	'primary' => 'ALTER TABLE datastore ADD PRIMARY KEY (id)',
	'name_idx' => 'CREATE INDEX name_idx ON datastore (name)',
);
$queries['index']['departments'] = array(
	'IDX_16AEB8D4727ACA70' => 'CREATE INDEX IDX_16AEB8D4727ACA70 ON departments (parent_id)',
	'IDX_16AEB8D4FBCC7CDF' => 'CREATE INDEX IDX_16AEB8D4FBCC7CDF ON departments (email_gateway_id)',
	'primary' => 'ALTER TABLE departments ADD PRIMARY KEY (id)',
);
$queries['index']['department_permissions'] = array(
	'IDX_84C36B30AE80F5DF' => 'CREATE INDEX IDX_84C36B30AE80F5DF ON department_permissions (department_id)',
	'IDX_84C36B30D2112630' => 'CREATE INDEX IDX_84C36B30D2112630 ON department_permissions (usergroup_id)',
	'IDX_84C36B30217BBB47' => 'CREATE INDEX IDX_84C36B30217BBB47 ON department_permissions (person_id)',
	'primary' => 'ALTER TABLE department_permissions ADD PRIMARY KEY (id)',
);
$queries['index']['downloads'] = array(
	'IDX_4B73A4B512469DE2' => 'CREATE INDEX IDX_4B73A4B512469DE2 ON downloads (category_id)',
	'IDX_4B73A4B5ED3E8EA5' => 'CREATE INDEX IDX_4B73A4B5ED3E8EA5 ON downloads (blob_id)',
	'IDX_4B73A4B5217BBB47' => 'CREATE INDEX IDX_4B73A4B5217BBB47 ON downloads (person_id)',
	'IDX_4B73A4B582F1BAF4' => 'CREATE INDEX IDX_4B73A4B582F1BAF4 ON downloads (language_id)',
	'primary' => 'ALTER TABLE downloads ADD PRIMARY KEY (id)',
	'date_published_idx' => 'CREATE INDEX date_published_idx ON downloads (date_published)',
	'status_idx' => 'CREATE INDEX status_idx ON downloads (status)',
);
$queries['index']['download_categories'] = array(
	'IDX_3317F15727ACA70' => 'CREATE INDEX IDX_3317F15727ACA70 ON download_categories (parent_id)',
	'primary' => 'ALTER TABLE download_categories ADD PRIMARY KEY (id)',
);
$queries['index']['download_category2usergroup'] = array(
	'IDX_53A2246F12469DE2' => 'CREATE INDEX IDX_53A2246F12469DE2 ON download_category2usergroup (category_id)',
	'IDX_53A2246FD2112630' => 'CREATE INDEX IDX_53A2246FD2112630 ON download_category2usergroup (usergroup_id)',
	'primary' => 'ALTER TABLE download_category2usergroup ADD PRIMARY KEY (category_id, usergroup_id)',
);
$queries['index']['download_comments'] = array(
	'IDX_B43CDE14C667AEAB' => 'CREATE INDEX IDX_B43CDE14C667AEAB ON download_comments (download_id)',
	'IDX_B43CDE14217BBB47' => 'CREATE INDEX IDX_B43CDE14217BBB47 ON download_comments (person_id)',
	'IDX_B43CDE1470BEE6D' => 'CREATE INDEX IDX_B43CDE1470BEE6D ON download_comments (visitor_id)',
	'primary' => 'ALTER TABLE download_comments ADD PRIMARY KEY (id)',
	'status_idx' => 'CREATE INDEX status_idx ON download_comments (status, is_reviewed)',
);
$queries['index']['download_revisions'] = array(
	'IDX_483B9D66C667AEAB' => 'CREATE INDEX IDX_483B9D66C667AEAB ON download_revisions (download_id)',
	'IDX_483B9D66ED3E8EA5' => 'CREATE INDEX IDX_483B9D66ED3E8EA5 ON download_revisions (blob_id)',
	'IDX_483B9D66217BBB47' => 'CREATE INDEX IDX_483B9D66217BBB47 ON download_revisions (person_id)',
	'primary' => 'ALTER TABLE download_revisions ADD PRIMARY KEY (id)',
);
$queries['index']['drafts'] = array(
	'IDX_EC2AE4C0217BBB47' => 'CREATE INDEX IDX_EC2AE4C0217BBB47 ON drafts (person_id)',
	'primary' => 'ALTER TABLE drafts ADD PRIMARY KEY (id)',
	'content_idx' => 'CREATE INDEX content_idx ON drafts (content_type, content_id)',
	'date_idx' => 'CREATE INDEX date_idx ON drafts (date_created)',
	'person_content_idx' => 'CREATE UNIQUE INDEX person_content_idx ON drafts (person_id, content_type, content_id)',
);
$queries['index']['email_gateways'] = array(
	'IDX_D0C6423237308465' => 'CREATE INDEX IDX_D0C6423237308465 ON email_gateways (linked_transport_id)',
	'IDX_D0C64232AE80F5DF' => 'CREATE INDEX IDX_D0C64232AE80F5DF ON email_gateways (department_id)',
	'primary' => 'ALTER TABLE email_gateways ADD PRIMARY KEY (id)',
);
$queries['index']['email_gateway_addresses'] = array(
	'IDX_EC270D12FBCC7CDF' => 'CREATE INDEX IDX_EC270D12FBCC7CDF ON email_gateway_addresses (email_gateway_id)',
	'primary' => 'ALTER TABLE email_gateway_addresses ADD PRIMARY KEY (id)',
);
$queries['index']['email_sources'] = array(
	'IDX_6F9D0D3DED3E8EA5' => 'CREATE INDEX IDX_6F9D0D3DED3E8EA5 ON email_sources (blob_id)',
	'IDX_6F9D0D3D577F8E00' => 'CREATE INDEX IDX_6F9D0D3D577F8E00 ON email_sources (gateway_id)',
	'primary' => 'ALTER TABLE email_sources ADD PRIMARY KEY (id)',
	'date_created' => 'CREATE INDEX date_created ON email_sources (date_created)',
	'object_idx' => 'CREATE INDEX object_idx ON email_sources (object_type, object_id)',
	'status_idx' => 'CREATE INDEX status_idx ON email_sources (status)',
);
$queries['index']['email_transports'] = array(
	'primary' => 'ALTER TABLE email_transports ADD PRIMARY KEY (id)',
);
$queries['index']['email_uids'] = array(
	'IDX_6D08D1BD577F8E00' => 'CREATE INDEX IDX_6D08D1BD577F8E00 ON email_uids (gateway_id)',
	'primary' => 'ALTER TABLE email_uids ADD PRIMARY KEY (id)',
);
$queries['index']['feedback'] = array(
	'IDX_D2294458169CE813' => 'CREATE INDEX IDX_D2294458169CE813 ON feedback (status_category_id)',
	'IDX_D229445812469DE2' => 'CREATE INDEX IDX_D229445812469DE2 ON feedback (category_id)',
	'IDX_D2294458217BBB47' => 'CREATE INDEX IDX_D2294458217BBB47 ON feedback (person_id)',
	'IDX_D229445882F1BAF4' => 'CREATE INDEX IDX_D229445882F1BAF4 ON feedback (language_id)',
	'primary' => 'ALTER TABLE feedback ADD PRIMARY KEY (id)',
	'date_published_idx' => 'CREATE INDEX date_published_idx ON feedback (date_published)',
	'status_idx' => 'CREATE INDEX status_idx ON feedback (status)',
);
$queries['index']['feedback_attachments'] = array(
	'IDX_CC264F12D249A887' => 'CREATE INDEX IDX_CC264F12D249A887 ON feedback_attachments (feedback_id)',
	'IDX_CC264F12217BBB47' => 'CREATE INDEX IDX_CC264F12217BBB47 ON feedback_attachments (person_id)',
	'IDX_CC264F12ED3E8EA5' => 'CREATE INDEX IDX_CC264F12ED3E8EA5 ON feedback_attachments (blob_id)',
	'primary' => 'ALTER TABLE feedback_attachments ADD PRIMARY KEY (id)',
);
$queries['index']['feedback_categories'] = array(
	'IDX_66FE6832727ACA70' => 'CREATE INDEX IDX_66FE6832727ACA70 ON feedback_categories (parent_id)',
	'primary' => 'ALTER TABLE feedback_categories ADD PRIMARY KEY (id)',
);
$queries['index']['feedback_category2usergroup'] = array(
	'IDX_B304B93C12469DE2' => 'CREATE INDEX IDX_B304B93C12469DE2 ON feedback_category2usergroup (category_id)',
	'IDX_B304B93CD2112630' => 'CREATE INDEX IDX_B304B93CD2112630 ON feedback_category2usergroup (usergroup_id)',
	'primary' => 'ALTER TABLE feedback_category2usergroup ADD PRIMARY KEY (category_id, usergroup_id)',
);
$queries['index']['feedback_comments'] = array(
	'IDX_10D03D58D249A887' => 'CREATE INDEX IDX_10D03D58D249A887 ON feedback_comments (feedback_id)',
	'IDX_10D03D58217BBB47' => 'CREATE INDEX IDX_10D03D58217BBB47 ON feedback_comments (person_id)',
	'IDX_10D03D5870BEE6D' => 'CREATE INDEX IDX_10D03D5870BEE6D ON feedback_comments (visitor_id)',
	'primary' => 'ALTER TABLE feedback_comments ADD PRIMARY KEY (id)',
	'status_idx' => 'CREATE INDEX status_idx ON feedback_comments (status, is_reviewed)',
);
$queries['index']['feedback_revisions'] = array(
	'IDX_37F57C3ED249A887' => 'CREATE INDEX IDX_37F57C3ED249A887 ON feedback_revisions (feedback_id)',
	'IDX_37F57C3E217BBB47' => 'CREATE INDEX IDX_37F57C3E217BBB47 ON feedback_revisions (person_id)',
	'primary' => 'ALTER TABLE feedback_revisions ADD PRIMARY KEY (id)',
);
$queries['index']['feedback_status_categories'] = array(
	'primary' => 'ALTER TABLE feedback_status_categories ADD PRIMARY KEY (id)',
);
$queries['index']['glossary_words'] = array(
	'UNIQ_1A8003DAC3F17511' => 'CREATE UNIQUE INDEX UNIQ_1A8003DAC3F17511 ON glossary_words (word)',
	'IDX_1A8003DAD11EA911' => 'CREATE INDEX IDX_1A8003DAD11EA911 ON glossary_words (definition_id)',
	'primary' => 'ALTER TABLE glossary_words ADD PRIMARY KEY (id)',
);
$queries['index']['glossary_word_definitions'] = array(
	'primary' => 'ALTER TABLE glossary_word_definitions ADD PRIMARY KEY (id)',
);
$queries['index']['import_datastore'] = array(
	'primary' => 'ALTER TABLE import_datastore ADD PRIMARY KEY (typename)',
);
$queries['index']['import_map'] = array(
	'primary' => 'ALTER TABLE import_map ADD PRIMARY KEY (typename, old_id)',
);
$queries['index']['kb_subscriptions'] = array(
	'IDX_1F05AAF5217BBB47' => 'CREATE INDEX IDX_1F05AAF5217BBB47 ON kb_subscriptions (person_id)',
	'IDX_1F05AAF57294869C' => 'CREATE INDEX IDX_1F05AAF57294869C ON kb_subscriptions (article_id)',
	'IDX_1F05AAF512469DE2' => 'CREATE INDEX IDX_1F05AAF512469DE2 ON kb_subscriptions (category_id)',
	'primary' => 'ALTER TABLE kb_subscriptions ADD PRIMARY KEY (id)',
);
$queries['index']['labels_articles'] = array(
	'IDX_2F30AF707294869C' => 'CREATE INDEX IDX_2F30AF707294869C ON labels_articles (article_id)',
	'primary' => 'ALTER TABLE labels_articles ADD PRIMARY KEY (article_id, label)',
	'label_idx' => 'CREATE INDEX label_idx ON labels_articles (label)',
);
$queries['index']['labels_blobs'] = array(
	'IDX_EC63B2F0ED3E8EA5' => 'CREATE INDEX IDX_EC63B2F0ED3E8EA5 ON labels_blobs (blob_id)',
	'primary' => 'ALTER TABLE labels_blobs ADD PRIMARY KEY (blob_id, label)',
	'label_idx' => 'CREATE INDEX label_idx ON labels_blobs (label)',
);
$queries['index']['labels_chat_conversations'] = array(
	'IDX_99205D121A9A7125' => 'CREATE INDEX IDX_99205D121A9A7125 ON labels_chat_conversations (chat_id)',
	'primary' => 'ALTER TABLE labels_chat_conversations ADD PRIMARY KEY (chat_id, label)',
	'label_idx' => 'CREATE INDEX label_idx ON labels_chat_conversations (label)',
);
$queries['index']['label_defs'] = array(
	'primary' => 'ALTER TABLE label_defs ADD PRIMARY KEY (label_type, label)',
	'type_total_idx' => 'CREATE INDEX type_total_idx ON label_defs (label_type, total)',
);
$queries['index']['labels_downloads'] = array(
	'IDX_588FD17DC667AEAB' => 'CREATE INDEX IDX_588FD17DC667AEAB ON labels_downloads (download_id)',
	'primary' => 'ALTER TABLE labels_downloads ADD PRIMARY KEY (download_id, label)',
	'label_idx' => 'CREATE INDEX label_idx ON labels_downloads (label)',
);
$queries['index']['labels_feedback'] = array(
	'IDX_42C4DA40D249A887' => 'CREATE INDEX IDX_42C4DA40D249A887 ON labels_feedback (feedback_id)',
	'primary' => 'ALTER TABLE labels_feedback ADD PRIMARY KEY (feedback_id, label)',
	'label_idx' => 'CREATE INDEX label_idx ON labels_feedback (label)',
);
$queries['index']['labels_news'] = array(
	'IDX_A2869A08B5A459A0' => 'CREATE INDEX IDX_A2869A08B5A459A0 ON labels_news (news_id)',
	'primary' => 'ALTER TABLE labels_news ADD PRIMARY KEY (news_id, label)',
	'label_idx' => 'CREATE INDEX label_idx ON labels_news (label)',
);
$queries['index']['labels_organizations'] = array(
	'IDX_9F089F4232C8A3DE' => 'CREATE INDEX IDX_9F089F4232C8A3DE ON labels_organizations (organization_id)',
	'primary' => 'ALTER TABLE labels_organizations ADD PRIMARY KEY (organization_id, label)',
	'label_idx' => 'CREATE INDEX label_idx ON labels_organizations (label)',
);
$queries['index']['labels_people'] = array(
	'IDX_C37D5395217BBB47' => 'CREATE INDEX IDX_C37D5395217BBB47 ON labels_people (person_id)',
	'primary' => 'ALTER TABLE labels_people ADD PRIMARY KEY (person_id, label)',
	'label_idx' => 'CREATE INDEX label_idx ON labels_people (label)',
);
$queries['index']['labels_tasks'] = array(
	'IDX_3557E9528DB60186' => 'CREATE INDEX IDX_3557E9528DB60186 ON labels_tasks (task_id)',
	'primary' => 'ALTER TABLE labels_tasks ADD PRIMARY KEY (task_id, label)',
	'label_idx' => 'CREATE INDEX label_idx ON labels_tasks (label)',
);
$queries['index']['labels_tickets'] = array(
	'IDX_6C514FB700047D2' => 'CREATE INDEX IDX_6C514FB700047D2 ON labels_tickets (ticket_id)',
	'primary' => 'ALTER TABLE labels_tickets ADD PRIMARY KEY (ticket_id, label)',
	'label_idx' => 'CREATE INDEX label_idx ON labels_tickets (label)',
);
$queries['index']['languages'] = array(
	'primary' => 'ALTER TABLE languages ADD PRIMARY KEY (id)',
);
$queries['index']['login_log'] = array(
	'IDX_F16D9FFF217BBB47' => 'CREATE INDEX IDX_F16D9FFF217BBB47 ON login_log (person_id)',
	'primary' => 'ALTER TABLE login_log ADD PRIMARY KEY (id)',
);
$queries['index']['log_items'] = array(
	'primary' => 'ALTER TABLE log_items ADD PRIMARY KEY (id)',
	'log_name_idx' => 'CREATE INDEX log_name_idx ON log_items (log_name, session_name)',
	'flag_idx' => 'CREATE INDEX flag_idx ON log_items (flag)',
);
$queries['index']['news'] = array(
	'IDX_1DD3995012469DE2' => 'CREATE INDEX IDX_1DD3995012469DE2 ON news (category_id)',
	'IDX_1DD39950217BBB47' => 'CREATE INDEX IDX_1DD39950217BBB47 ON news (person_id)',
	'IDX_1DD3995082F1BAF4' => 'CREATE INDEX IDX_1DD3995082F1BAF4 ON news (language_id)',
	'primary' => 'ALTER TABLE news ADD PRIMARY KEY (id)',
	'date_published_idx' => 'CREATE INDEX date_published_idx ON news (date_published)',
	'status_idx' => 'CREATE INDEX status_idx ON news (status)',
);
$queries['index']['news_categories'] = array(
	'IDX_D68C9111727ACA70' => 'CREATE INDEX IDX_D68C9111727ACA70 ON news_categories (parent_id)',
	'primary' => 'ALTER TABLE news_categories ADD PRIMARY KEY (id)',
);
$queries['index']['news_category2usergroup'] = array(
	'IDX_6336075D12469DE2' => 'CREATE INDEX IDX_6336075D12469DE2 ON news_category2usergroup (category_id)',
	'IDX_6336075DD2112630' => 'CREATE INDEX IDX_6336075DD2112630 ON news_category2usergroup (usergroup_id)',
	'primary' => 'ALTER TABLE news_category2usergroup ADD PRIMARY KEY (category_id, usergroup_id)',
);
$queries['index']['news_comments'] = array(
	'IDX_16A0357BB5A459A0' => 'CREATE INDEX IDX_16A0357BB5A459A0 ON news_comments (news_id)',
	'IDX_16A0357B217BBB47' => 'CREATE INDEX IDX_16A0357B217BBB47 ON news_comments (person_id)',
	'IDX_16A0357B70BEE6D' => 'CREATE INDEX IDX_16A0357B70BEE6D ON news_comments (visitor_id)',
	'primary' => 'ALTER TABLE news_comments ADD PRIMARY KEY (id)',
	'status_idx' => 'CREATE INDEX status_idx ON news_comments (status, is_reviewed)',
);
$queries['index']['news_revisions'] = array(
	'IDX_95947D44B5A459A0' => 'CREATE INDEX IDX_95947D44B5A459A0 ON news_revisions (news_id)',
	'IDX_95947D44217BBB47' => 'CREATE INDEX IDX_95947D44217BBB47 ON news_revisions (person_id)',
	'primary' => 'ALTER TABLE news_revisions ADD PRIMARY KEY (id)',
);
$queries['index']['object_lang'] = array(
	'IDX_AC1CB87182F1BAF4' => 'CREATE INDEX IDX_AC1CB87182F1BAF4 ON object_lang (language_id)',
	'primary' => 'ALTER TABLE object_lang ADD PRIMARY KEY (id)',
	'prop_ref_type' => 'CREATE INDEX prop_ref_type ON object_lang (ref_type, ref_id)',
	'prop_ref' => 'CREATE UNIQUE INDEX prop_ref ON object_lang (ref, prop_name, language_id)',
);
$queries['index']['organizations'] = array(
	'UNIQ_427C1C7FF0187A77' => 'CREATE UNIQUE INDEX UNIQ_427C1C7FF0187A77 ON organizations (picture_blob_id)',
	'primary' => 'ALTER TABLE organizations ADD PRIMARY KEY (id)',
);
$queries['index']['organization2usergroups'] = array(
	'IDX_EA8C676432C8A3DE' => 'CREATE INDEX IDX_EA8C676432C8A3DE ON organization2usergroups (organization_id)',
	'IDX_EA8C6764D2112630' => 'CREATE INDEX IDX_EA8C6764D2112630 ON organization2usergroups (usergroup_id)',
	'primary' => 'ALTER TABLE organization2usergroups ADD PRIMARY KEY (organization_id, usergroup_id)',
);
$queries['index']['organizations_auto_cc'] = array(
	'IDX_864B966432C8A3DE' => 'CREATE INDEX IDX_864B966432C8A3DE ON organizations_auto_cc (organization_id)',
	'IDX_864B9664217BBB47' => 'CREATE INDEX IDX_864B9664217BBB47 ON organizations_auto_cc (person_id)',
	'primary' => 'ALTER TABLE organizations_auto_cc ADD PRIMARY KEY (organization_id, person_id)',
);
$queries['index']['organizations_contact_data'] = array(
	'IDX_25B60D5032C8A3DE' => 'CREATE INDEX IDX_25B60D5032C8A3DE ON organizations_contact_data (organization_id)',
	'primary' => 'ALTER TABLE organizations_contact_data ADD PRIMARY KEY (id)',
);
$queries['index']['organization_email_domains'] = array(
	'IDX_2CCB20C232C8A3DE' => 'CREATE INDEX IDX_2CCB20C232C8A3DE ON organization_email_domains (organization_id)',
	'primary' => 'ALTER TABLE organization_email_domains ADD PRIMARY KEY (domain)',
);
$queries['index']['organization_notes'] = array(
	'IDX_8F9C404B32C8A3DE' => 'CREATE INDEX IDX_8F9C404B32C8A3DE ON organization_notes (organization_id)',
	'IDX_8F9C404B3414710B' => 'CREATE INDEX IDX_8F9C404B3414710B ON organization_notes (agent_id)',
	'primary' => 'ALTER TABLE organization_notes ADD PRIMARY KEY (id)',
);
$queries['index']['organizations_twitter_users'] = array(
	'IDX_268948132C8A3DE' => 'CREATE INDEX IDX_268948132C8A3DE ON organizations_twitter_users (organization_id)',
	'primary' => 'ALTER TABLE organizations_twitter_users ADD PRIMARY KEY (id)',
	'screen_name_idx' => 'CREATE INDEX screen_name_idx ON organizations_twitter_users (screen_name)',
	'unique_key_idx' => 'CREATE UNIQUE INDEX unique_key_idx ON organizations_twitter_users (organization_id, screen_name)',
);
$queries['index']['page_view_log'] = array(
	'primary' => 'ALTER TABLE page_view_log ADD PRIMARY KEY (id)',
	'object_idx' => 'CREATE INDEX object_idx ON page_view_log (object_type, object_id)',
	'date_created_idx' => 'CREATE INDEX date_created_idx ON page_view_log (date_created)',
);
$queries['index']['permissions'] = array(
	'IDX_2DEDCC6FD2112630' => 'CREATE INDEX IDX_2DEDCC6FD2112630 ON permissions (usergroup_id)',
	'IDX_2DEDCC6F217BBB47' => 'CREATE INDEX IDX_2DEDCC6F217BBB47 ON permissions (person_id)',
	'primary' => 'ALTER TABLE permissions ADD PRIMARY KEY (id)',
);
$queries['index']['permissions_cache'] = array(
	'primary' => 'ALTER TABLE permissions_cache ADD PRIMARY KEY (name, usergroup_key)',
	'usergroup_key_idx' => 'CREATE INDEX usergroup_key_idx ON permissions_cache (usergroup_key)',
);
$queries['index']['people'] = array(
	'IDX_28166A26F0187A77' => 'CREATE INDEX IDX_28166A26F0187A77 ON people (picture_blob_id)',
	'IDX_28166A2682F1BAF4' => 'CREATE INDEX IDX_28166A2682F1BAF4 ON people (language_id)',
	'IDX_28166A2632C8A3DE' => 'CREATE INDEX IDX_28166A2632C8A3DE ON people (organization_id)',
	'UNIQ_28166A26894DAC38' => 'CREATE UNIQUE INDEX UNIQ_28166A26894DAC38 ON people (primary_email_id)',
	'primary' => 'ALTER TABLE people ADD PRIMARY KEY (id)',
	'is_agent_idx' => 'CREATE INDEX is_agent_idx ON people (is_agent)',
	'is_confirmed_idx' => 'CREATE INDEX is_confirmed_idx ON people (is_confirmed)',
);
$queries['index']['person2usergroups'] = array(
	'IDX_356C969E217BBB47' => 'CREATE INDEX IDX_356C969E217BBB47 ON person2usergroups (person_id)',
	'IDX_356C969ED2112630' => 'CREATE INDEX IDX_356C969ED2112630 ON person2usergroups (usergroup_id)',
	'primary' => 'ALTER TABLE person2usergroups ADD PRIMARY KEY (person_id, usergroup_id)',
);
$queries['index']['person_activity'] = array(
	'IDX_3832AC6D217BBB47' => 'CREATE INDEX IDX_3832AC6D217BBB47 ON person_activity (person_id)',
	'primary' => 'ALTER TABLE person_activity ADD PRIMARY KEY (id)',
	'date_created_idx' => 'CREATE INDEX date_created_idx ON person_activity (date_created)',
);
$queries['index']['people_contact_data'] = array(
	'IDX_14604ED8217BBB47' => 'CREATE INDEX IDX_14604ED8217BBB47 ON people_contact_data (person_id)',
	'primary' => 'ALTER TABLE people_contact_data ADD PRIMARY KEY (id)',
);
$queries['index']['people_emails'] = array(
	'IDX_3A96CAB8217BBB47' => 'CREATE INDEX IDX_3A96CAB8217BBB47 ON people_emails (person_id)',
	'primary' => 'ALTER TABLE people_emails ADD PRIMARY KEY (id)',
	'email_domain_idx' => 'CREATE INDEX email_domain_idx ON people_emails (email_domain)',
	'email_idx' => 'CREATE UNIQUE INDEX email_idx ON people_emails (email)',
);
$queries['index']['people_emails_validating'] = array(
	'IDX_3277575C217BBB47' => 'CREATE INDEX IDX_3277575C217BBB47 ON people_emails_validating (person_id)',
	'primary' => 'ALTER TABLE people_emails_validating ADD PRIMARY KEY (id)',
	'email_idx' => 'CREATE UNIQUE INDEX email_idx ON people_emails_validating (email)',
);
$queries['index']['people_notes'] = array(
	'IDX_CA78DCCC217BBB47' => 'CREATE INDEX IDX_CA78DCCC217BBB47 ON people_notes (person_id)',
	'IDX_CA78DCCC3414710B' => 'CREATE INDEX IDX_CA78DCCC3414710B ON people_notes (agent_id)',
	'primary' => 'ALTER TABLE people_notes ADD PRIMARY KEY (id)',
);
$queries['index']['people_prefs'] = array(
	'IDX_8112E0E9217BBB47' => 'CREATE INDEX IDX_8112E0E9217BBB47 ON people_prefs (person_id)',
	'primary' => 'ALTER TABLE people_prefs ADD PRIMARY KEY (person_id, name)',
);
$queries['index']['people_twitter_users'] = array(
	'IDX_E13A49D0217BBB47' => 'CREATE INDEX IDX_E13A49D0217BBB47 ON people_twitter_users (person_id)',
	'primary' => 'ALTER TABLE people_twitter_users ADD PRIMARY KEY (id)',
	'screen_name_idx' => 'CREATE INDEX screen_name_idx ON people_twitter_users (screen_name)',
	'twitter_user_id_idx' => 'CREATE INDEX twitter_user_id_idx ON people_twitter_users (twitter_user_id)',
	'unique_key_idx' => 'CREATE UNIQUE INDEX unique_key_idx ON people_twitter_users (person_id, screen_name)',
);
$queries['index']['person_usersource_assoc'] = array(
	'IDX_72215949217BBB47' => 'CREATE INDEX IDX_72215949217BBB47 ON person_usersource_assoc (person_id)',
	'IDX_722159495B71BD01' => 'CREATE INDEX IDX_722159495B71BD01 ON person_usersource_assoc (usersource_id)',
	'primary' => 'ALTER TABLE person_usersource_assoc ADD PRIMARY KEY (id)',
	'identity_idx' => 'CREATE INDEX identity_idx ON person_usersource_assoc (identity)',
);
$queries['index']['phrases'] = array(
	'IDX_121AC8C682F1BAF4' => 'CREATE INDEX IDX_121AC8C682F1BAF4 ON phrases (language_id)',
	'primary' => 'ALTER TABLE phrases ADD PRIMARY KEY (id)',
	'name_idx' => 'CREATE INDEX name_idx ON phrases (groupname, name)',
);
$queries['index']['plugins'] = array(
	'primary' => 'ALTER TABLE plugins ADD PRIMARY KEY (id)',
);
$queries['index']['plugin_listeners'] = array(
	'IDX_FEEE2572EC942BCF' => 'CREATE INDEX IDX_FEEE2572EC942BCF ON plugin_listeners (plugin_id)',
	'primary' => 'ALTER TABLE plugin_listeners ADD PRIMARY KEY (id)',
);
$queries['index']['portal_page_display'] = array(
	'primary' => 'ALTER TABLE portal_page_display ADD PRIMARY KEY (id)',
);
$queries['index']['pretickets_content'] = array(
	'IDX_E1110A25217BBB47' => 'CREATE INDEX IDX_E1110A25217BBB47 ON pretickets_content (person_id)',
	'IDX_E1110A2570BEE6D' => 'CREATE INDEX IDX_E1110A2570BEE6D ON pretickets_content (visitor_id)',
	'primary' => 'ALTER TABLE pretickets_content ADD PRIMARY KEY (id)',
	'email_idx' => 'CREATE INDEX email_idx ON pretickets_content (email)',
	'object_idx' => 'CREATE INDEX object_idx ON pretickets_content (object_type, object_id)',
);
$queries['index']['products'] = array(
	'IDX_B3BA5A5A727ACA70' => 'CREATE INDEX IDX_B3BA5A5A727ACA70 ON products (parent_id)',
	'primary' => 'ALTER TABLE products ADD PRIMARY KEY (id)',
);
$queries['index']['queue_items'] = array(
	'primary' => 'ALTER TABLE queue_items ADD PRIMARY KEY (id)',
	'priority_idx' => 'CREATE INDEX priority_idx ON queue_items (priority)',
);
$queries['index']['ratings'] = array(
	'IDX_CEB607C9546A72F3' => 'CREATE INDEX IDX_CEB607C9546A72F3 ON ratings (searchlog_id)',
	'IDX_CEB607C9217BBB47' => 'CREATE INDEX IDX_CEB607C9217BBB47 ON ratings (person_id)',
	'IDX_CEB607C970BEE6D' => 'CREATE INDEX IDX_CEB607C970BEE6D ON ratings (visitor_id)',
	'primary' => 'ALTER TABLE ratings ADD PRIMARY KEY (id)',
	'object_idx' => 'CREATE INDEX object_idx ON ratings (object_type, object_id)',
);
$queries['index']['related_content'] = array(
	'primary' => 'ALTER TABLE related_content ADD PRIMARY KEY (object_type, object_id, rel_object_type, rel_object_id)',
);
$queries['index']['report_builder'] = array(
	'IDX_B6BED249727ACA70' => 'CREATE INDEX IDX_B6BED249727ACA70 ON report_builder (parent_id)',
	'primary' => 'ALTER TABLE report_builder ADD PRIMARY KEY (id)',
	'unique_key_idx' => 'CREATE UNIQUE INDEX unique_key_idx ON report_builder (unique_key)',
);
$queries['index']['report_builder_favorite'] = array(
	'IDX_CCD5CB1186DD4ADF' => 'CREATE INDEX IDX_CCD5CB1186DD4ADF ON report_builder_favorite (report_builder_id)',
	'IDX_CCD5CB11217BBB47' => 'CREATE INDEX IDX_CCD5CB11217BBB47 ON report_builder_favorite (person_id)',
	'primary' => 'ALTER TABLE report_builder_favorite ADD PRIMARY KEY (id)',
	'unique_key_idx' => 'CREATE UNIQUE INDEX unique_key_idx ON report_builder_favorite (report_builder_id, person_id, params)',
);
$queries['index']['result_cache'] = array(
	'IDX_D0B33C6B217BBB47' => 'CREATE INDEX IDX_D0B33C6B217BBB47 ON result_cache (person_id)',
	'primary' => 'ALTER TABLE result_cache ADD PRIMARY KEY (id)',
);
$queries['index']['searchlog'] = array(
	'IDX_8C79CD5C217BBB47' => 'CREATE INDEX IDX_8C79CD5C217BBB47 ON searchlog (person_id)',
	'IDX_8C79CD5C70BEE6D' => 'CREATE INDEX IDX_8C79CD5C70BEE6D ON searchlog (visitor_id)',
	'primary' => 'ALTER TABLE searchlog ADD PRIMARY KEY (id)',
	'searchlog_query_idx' => 'CREATE INDEX searchlog_query_idx ON searchlog (query (15))',
	'num_results_idx' => 'CREATE INDEX num_results_idx ON searchlog (num_results)',
);
$queries['index']['search_sticky_result'] = array(
	'primary' => 'ALTER TABLE search_sticky_result ADD PRIMARY KEY (word, object_type, object_id)',
);
$queries['index']['search_term_boosters'] = array(
	'primary' => 'ALTER TABLE search_term_boosters ADD PRIMARY KEY (object_type, object_id)',
);
$queries['index']['sendmail_logs'] = array(
	'IDX_D9E8157F700047D2' => 'CREATE INDEX IDX_D9E8157F700047D2 ON sendmail_logs (ticket_id)',
	'IDX_D9E8157FC5E9817D' => 'CREATE INDEX IDX_D9E8157FC5E9817D ON sendmail_logs (ticket_message_id)',
	'IDX_D9E8157F217BBB47' => 'CREATE INDEX IDX_D9E8157F217BBB47 ON sendmail_logs (person_id)',
	'primary' => 'ALTER TABLE sendmail_logs ADD PRIMARY KEY (id)',
	'code' => 'CREATE UNIQUE INDEX code ON sendmail_logs (code, to_address)',
);
$queries['index']['sendmail_queue'] = array(
	'IDX_DDB369C2ED3E8EA5' => 'CREATE INDEX IDX_DDB369C2ED3E8EA5 ON sendmail_queue (blob_id)',
	'primary' => 'ALTER TABLE sendmail_queue ADD PRIMARY KEY (id)',
	'has_sent_idx' => 'CREATE INDEX has_sent_idx ON sendmail_queue (has_sent, date_next_attempt)',
);
$queries['index']['sessions'] = array(
	'IDX_9A609D13217BBB47' => 'CREATE INDEX IDX_9A609D13217BBB47 ON sessions (person_id)',
	'IDX_9A609D1370BEE6D' => 'CREATE INDEX IDX_9A609D1370BEE6D ON sessions (visitor_id)',
	'primary' => 'ALTER TABLE sessions ADD PRIMARY KEY (id)',
	'date_last_idx' => 'CREATE INDEX date_last_idx ON sessions (date_last, is_person)',
);
$queries['index']['settings'] = array(
	'primary' => 'ALTER TABLE settings ADD PRIMARY KEY (name)',
);
$queries['index']['slas'] = array(
	'IDX_ACE9984A91D0B882' => 'CREATE INDEX IDX_ACE9984A91D0B882 ON slas (warning_trigger_id)',
	'IDX_ACE9984A55EA90D4' => 'CREATE INDEX IDX_ACE9984A55EA90D4 ON slas (fail_trigger_id)',
	'IDX_ACE9984A13CC0145' => 'CREATE INDEX IDX_ACE9984A13CC0145 ON slas (apply_priority_id)',
	'IDX_ACE9984AED1A7B28' => 'CREATE INDEX IDX_ACE9984AED1A7B28 ON slas (apply_trigger_id)',
	'primary' => 'ALTER TABLE slas ADD PRIMARY KEY (id)',
);
$queries['index']['sla_people'] = array(
	'IDX_14ABD6A37A2CC8C4' => 'CREATE INDEX IDX_14ABD6A37A2CC8C4 ON sla_people (sla_id)',
	'IDX_14ABD6A3217BBB47' => 'CREATE INDEX IDX_14ABD6A3217BBB47 ON sla_people (person_id)',
	'primary' => 'ALTER TABLE sla_people ADD PRIMARY KEY (sla_id, person_id)',
);
$queries['index']['sla_organizations'] = array(
	'IDX_A7F081987A2CC8C4' => 'CREATE INDEX IDX_A7F081987A2CC8C4 ON sla_organizations (sla_id)',
	'IDX_A7F0819832C8A3DE' => 'CREATE INDEX IDX_A7F0819832C8A3DE ON sla_organizations (organization_id)',
	'primary' => 'ALTER TABLE sla_organizations ADD PRIMARY KEY (sla_id, organization_id)',
);
$queries['index']['styles'] = array(
	'IDX_B65AFAF5727ACA70' => 'CREATE INDEX IDX_B65AFAF5727ACA70 ON styles (parent_id)',
	'UNIQ_B65AFAF5D91464D5' => 'CREATE UNIQUE INDEX UNIQ_B65AFAF5D91464D5 ON styles (logo_blob_id)',
	'UNIQ_B65AFAF58AB530EF' => 'CREATE UNIQUE INDEX UNIQ_B65AFAF58AB530EF ON styles (css_blob_id)',
	'UNIQ_B65AFAF5FEED6A62' => 'CREATE UNIQUE INDEX UNIQ_B65AFAF5FEED6A62 ON styles (css_blob_rtl_id)',
	'primary' => 'ALTER TABLE styles ADD PRIMARY KEY (id)',
);
$queries['index']['tasks'] = array(
	'IDX_50586597217BBB47' => 'CREATE INDEX IDX_50586597217BBB47 ON tasks (person_id)',
	'IDX_5058659749197702' => 'CREATE INDEX IDX_5058659749197702 ON tasks (assigned_agent_id)',
	'IDX_50586597410D1341' => 'CREATE INDEX IDX_50586597410D1341 ON tasks (assigned_agent_team_id)',
	'primary' => 'ALTER TABLE tasks ADD PRIMARY KEY (id)',
);
$queries['index']['task_associations'] = array(
	'IDX_41B0E09C8DB60186' => 'CREATE INDEX IDX_41B0E09C8DB60186 ON task_associations (task_id)',
	'IDX_41B0E09C217BBB47' => 'CREATE INDEX IDX_41B0E09C217BBB47 ON task_associations (person_id)',
	'IDX_41B0E09C700047D2' => 'CREATE INDEX IDX_41B0E09C700047D2 ON task_associations (ticket_id)',
	'IDX_41B0E09C32C8A3DE' => 'CREATE INDEX IDX_41B0E09C32C8A3DE ON task_associations (organization_id)',
	'primary' => 'ALTER TABLE task_associations ADD PRIMARY KEY (id)',
);
$queries['index']['task_comments'] = array(
	'IDX_1F5E7C668DB60186' => 'CREATE INDEX IDX_1F5E7C668DB60186 ON task_comments (task_id)',
	'IDX_1F5E7C66217BBB47' => 'CREATE INDEX IDX_1F5E7C66217BBB47 ON task_comments (person_id)',
	'primary' => 'ALTER TABLE task_comments ADD PRIMARY KEY (id)',
);
$queries['index']['task_queue'] = array(
	'primary' => 'ALTER TABLE task_queue ADD PRIMARY KEY (id)',
);
$queries['index']['task_reminder_logs'] = array(
	'IDX_A264D7248DB60186' => 'CREATE INDEX IDX_A264D7248DB60186 ON task_reminder_logs (task_id)',
	'IDX_A264D724217BBB47' => 'CREATE INDEX IDX_A264D724217BBB47 ON task_reminder_logs (person_id)',
	'primary' => 'ALTER TABLE task_reminder_logs ADD PRIMARY KEY (id)',
);
$queries['index']['templates'] = array(
	'IDX_6F287D8EBACD6074' => 'CREATE INDEX IDX_6F287D8EBACD6074 ON templates (style_id)',
	'primary' => 'ALTER TABLE templates ADD PRIMARY KEY (id)',
);
$queries['index']['text_snippets'] = array(
	'IDX_5B6379CE217BBB47' => 'CREATE INDEX IDX_5B6379CE217BBB47 ON text_snippets (person_id)',
	'IDX_5B6379CE12469DE2' => 'CREATE INDEX IDX_5B6379CE12469DE2 ON text_snippets (category_id)',
	'primary' => 'ALTER TABLE text_snippets ADD PRIMARY KEY (id)',
);
$queries['index']['text_snippet_categories'] = array(
	'IDX_F3B50AF1217BBB47' => 'CREATE INDEX IDX_F3B50AF1217BBB47 ON text_snippet_categories (person_id)',
	'primary' => 'ALTER TABLE text_snippet_categories ADD PRIMARY KEY (id)',
);
$queries['index']['tickets'] = array(
	'IDX_54469DF4814B683C' => 'CREATE INDEX IDX_54469DF4814B683C ON tickets (parent_ticket_id)',
	'IDX_54469DF482F1BAF4' => 'CREATE INDEX IDX_54469DF482F1BAF4 ON tickets (language_id)',
	'IDX_54469DF4AE80F5DF' => 'CREATE INDEX IDX_54469DF4AE80F5DF ON tickets (department_id)',
	'IDX_54469DF412469DE2' => 'CREATE INDEX IDX_54469DF412469DE2 ON tickets (category_id)',
	'IDX_54469DF4497B19F9' => 'CREATE INDEX IDX_54469DF4497B19F9 ON tickets (priority_id)',
	'IDX_54469DF42C7C2CBA' => 'CREATE INDEX IDX_54469DF42C7C2CBA ON tickets (workflow_id)',
	'IDX_54469DF44584665A' => 'CREATE INDEX IDX_54469DF44584665A ON tickets (product_id)',
	'IDX_54469DF4217BBB47' => 'CREATE INDEX IDX_54469DF4217BBB47 ON tickets (person_id)',
	'IDX_54469DF43C7464FE' => 'CREATE INDEX IDX_54469DF43C7464FE ON tickets (person_email_id)',
	'IDX_54469DF4581A624E' => 'CREATE INDEX IDX_54469DF4581A624E ON tickets (person_email_validating_id)',
	'IDX_54469DF43414710B' => 'CREATE INDEX IDX_54469DF43414710B ON tickets (agent_id)',
	'IDX_54469DF4FB3FBA04' => 'CREATE INDEX IDX_54469DF4FB3FBA04 ON tickets (agent_team_id)',
	'IDX_54469DF432C8A3DE' => 'CREATE INDEX IDX_54469DF432C8A3DE ON tickets (organization_id)',
	'IDX_54469DF471D249B2' => 'CREATE INDEX IDX_54469DF471D249B2 ON tickets (linked_chat_id)',
	'IDX_54469DF4FBCC7CDF' => 'CREATE INDEX IDX_54469DF4FBCC7CDF ON tickets (email_gateway_id)',
	'IDX_54469DF4F2598614' => 'CREATE INDEX IDX_54469DF4F2598614 ON tickets (email_gateway_address_id)',
	'IDX_54469DF4428359E2' => 'CREATE INDEX IDX_54469DF4428359E2 ON tickets (locked_by_agent)',
	'primary' => 'ALTER TABLE tickets ADD PRIMARY KEY (id)',
	'date_created_idx' => 'CREATE INDEX date_created_idx ON tickets (date_created)',
	'date_locked_idx' => 'CREATE INDEX date_locked_idx ON tickets (date_locked)',
	'status_idx' => 'CREATE INDEX status_idx ON tickets (status)',
	'ref_idx' => 'CREATE UNIQUE INDEX ref_idx ON tickets (ref)',
);
$queries['index']['ticket_access_codes'] = array(
	'IDX_CCEE41B5700047D2' => 'CREATE INDEX IDX_CCEE41B5700047D2 ON ticket_access_codes (ticket_id)',
	'IDX_CCEE41B5217BBB47' => 'CREATE INDEX IDX_CCEE41B5217BBB47 ON ticket_access_codes (person_id)',
	'primary' => 'ALTER TABLE ticket_access_codes ADD PRIMARY KEY (id)',
);
$queries['index']['tickets_attachments'] = array(
	'IDX_F06B468D700047D2' => 'CREATE INDEX IDX_F06B468D700047D2 ON tickets_attachments (ticket_id)',
	'IDX_F06B468D217BBB47' => 'CREATE INDEX IDX_F06B468D217BBB47 ON tickets_attachments (person_id)',
	'IDX_F06B468DED3E8EA5' => 'CREATE INDEX IDX_F06B468DED3E8EA5 ON tickets_attachments (blob_id)',
	'IDX_F06B468D537A1329' => 'CREATE INDEX IDX_F06B468D537A1329 ON tickets_attachments (message_id)',
	'primary' => 'ALTER TABLE tickets_attachments ADD PRIMARY KEY (id)',
);
$queries['index']['ticket_categories'] = array(
	'IDX_AC60D43C727ACA70' => 'CREATE INDEX IDX_AC60D43C727ACA70 ON ticket_categories (parent_id)',
	'primary' => 'ALTER TABLE ticket_categories ADD PRIMARY KEY (id)',
);
$queries['index']['ticket_changetracker_logs'] = array(
	'IDX_F2205216700047D2' => 'CREATE INDEX IDX_F2205216700047D2 ON ticket_changetracker_logs (ticket_id)',
	'primary' => 'ALTER TABLE ticket_changetracker_logs ADD PRIMARY KEY (id)',
	'date_created' => 'CREATE INDEX date_created ON ticket_changetracker_logs (date_created)',
);
$queries['index']['ticket_charges'] = array(
	'IDX_36230948700047D2' => 'CREATE INDEX IDX_36230948700047D2 ON ticket_charges (ticket_id)',
	'IDX_36230948217BBB47' => 'CREATE INDEX IDX_36230948217BBB47 ON ticket_charges (person_id)',
	'IDX_3623094832C8A3DE' => 'CREATE INDEX IDX_3623094832C8A3DE ON ticket_charges (organization_id)',
	'IDX_362309483414710B' => 'CREATE INDEX IDX_362309483414710B ON ticket_charges (agent_id)',
	'primary' => 'ALTER TABLE ticket_charges ADD PRIMARY KEY (id)',
);
$queries['index']['tickets_deleted'] = array(
	'IDX_7EDF2278B5BE2AA2' => 'CREATE INDEX IDX_7EDF2278B5BE2AA2 ON tickets_deleted (by_person_id)',
	'primary' => 'ALTER TABLE tickets_deleted ADD PRIMARY KEY (ticket_id)',
	'old_ref_idx' => 'CREATE INDEX old_ref_idx ON tickets_deleted (old_ref)',
);
$queries['index']['ticket_feedback'] = array(
	'IDX_5740B8D9700047D2' => 'CREATE INDEX IDX_5740B8D9700047D2 ON ticket_feedback (ticket_id)',
	'IDX_5740B8D9537A1329' => 'CREATE INDEX IDX_5740B8D9537A1329 ON ticket_feedback (message_id)',
	'IDX_5740B8D9217BBB47' => 'CREATE INDEX IDX_5740B8D9217BBB47 ON ticket_feedback (person_id)',
	'primary' => 'ALTER TABLE ticket_feedback ADD PRIMARY KEY (id)',
);
$queries['index']['ticket_filters'] = array(
	'IDX_74BB3EDF217BBB47' => 'CREATE INDEX IDX_74BB3EDF217BBB47 ON ticket_filters (person_id)',
	'IDX_74BB3EDFFB3FBA04' => 'CREATE INDEX IDX_74BB3EDFFB3FBA04 ON ticket_filters (agent_team_id)',
	'primary' => 'ALTER TABLE ticket_filters ADD PRIMARY KEY (id)',
	'sys_name_unique' => 'CREATE UNIQUE INDEX sys_name_unique ON ticket_filters (sys_name)',
);
$queries['index']['ticket_filters_perms'] = array(
	'IDX_93E8D427D395B25E' => 'CREATE INDEX IDX_93E8D427D395B25E ON ticket_filters_perms (filter_id)',
	'primary' => 'ALTER TABLE ticket_filters_perms ADD PRIMARY KEY (id)',
	'object_idx' => 'CREATE INDEX object_idx ON ticket_filters_perms (object_type, object_id)',
);
$queries['index']['ticket_filter_subscriptions'] = array(
	'IDX_13669D98D395B25E' => 'CREATE INDEX IDX_13669D98D395B25E ON ticket_filter_subscriptions (filter_id)',
	'IDX_13669D98217BBB47' => 'CREATE INDEX IDX_13669D98217BBB47 ON ticket_filter_subscriptions (person_id)',
	'primary' => 'ALTER TABLE ticket_filter_subscriptions ADD PRIMARY KEY (id)',
);
$queries['index']['tickets_flagged'] = array(
	'primary' => 'ALTER TABLE tickets_flagged ADD PRIMARY KEY (person_id, ticket_id)',
);
$queries['index']['tickets_logs'] = array(
	'IDX_F5F41081727ACA70' => 'CREATE INDEX IDX_F5F41081727ACA70 ON tickets_logs (parent_id)',
	'IDX_F5F41081700047D2' => 'CREATE INDEX IDX_F5F41081700047D2 ON tickets_logs (ticket_id)',
	'IDX_F5F41081217BBB47' => 'CREATE INDEX IDX_F5F41081217BBB47 ON tickets_logs (person_id)',
	'IDX_F5F410817A2CC8C4' => 'CREATE INDEX IDX_F5F410817A2CC8C4 ON tickets_logs (sla_id)',
	'primary' => 'ALTER TABLE tickets_logs ADD PRIMARY KEY (id)',
);
$queries['index']['ticket_macros'] = array(
	'IDX_8E373A2C217BBB47' => 'CREATE INDEX IDX_8E373A2C217BBB47 ON ticket_macros (person_id)',
	'primary' => 'ALTER TABLE ticket_macros ADD PRIMARY KEY (id)',
);
$queries['index']['ticket_macros_perms'] = array(
	'IDX_EAB2E6D5F43A187E' => 'CREATE INDEX IDX_EAB2E6D5F43A187E ON ticket_macros_perms (macro_id)',
	'primary' => 'ALTER TABLE ticket_macros_perms ADD PRIMARY KEY (id)',
	'object_idx' => 'CREATE INDEX object_idx ON ticket_macros_perms (object_type, object_id)',
);
$queries['index']['tickets_messages'] = array(
	'IDX_3A9962E2700047D2' => 'CREATE INDEX IDX_3A9962E2700047D2 ON tickets_messages (ticket_id)',
	'IDX_3A9962E2217BBB47' => 'CREATE INDEX IDX_3A9962E2217BBB47 ON tickets_messages (person_id)',
	'IDX_3A9962E29A37834A' => 'CREATE INDEX IDX_3A9962E29A37834A ON tickets_messages (email_source_id)',
	'IDX_3A9962E2251FB291' => 'CREATE INDEX IDX_3A9962E2251FB291 ON tickets_messages (message_translated_id)',
	'IDX_3A9962E270BEE6D' => 'CREATE INDEX IDX_3A9962E270BEE6D ON tickets_messages (visitor_id)',
	'primary' => 'ALTER TABLE tickets_messages ADD PRIMARY KEY (id)',
);
$queries['index']['tickets_messages_raw'] = array(
	'primary' => 'ALTER TABLE tickets_messages_raw ADD PRIMARY KEY (message_id)',
);
$queries['index']['ticket_message_templates'] = array(
	'IDX_8C28E2ECAE80F5DF' => 'CREATE INDEX IDX_8C28E2ECAE80F5DF ON ticket_message_templates (department_id)',
	'primary' => 'ALTER TABLE ticket_message_templates ADD PRIMARY KEY (id)',
);
$queries['index']['tickets_messages_translated'] = array(
	'IDX_EDCD3BB3700047D2' => 'CREATE INDEX IDX_EDCD3BB3700047D2 ON tickets_messages_translated (ticket_id)',
	'IDX_EDCD3BB3537A1329' => 'CREATE INDEX IDX_EDCD3BB3537A1329 ON tickets_messages_translated (message_id)',
	'primary' => 'ALTER TABLE tickets_messages_translated ADD PRIMARY KEY (id)',
);
$queries['index']['ticket_page_display'] = array(
	'IDX_3667659DAE80F5DF' => 'CREATE INDEX IDX_3667659DAE80F5DF ON ticket_page_display (department_id)',
	'primary' => 'ALTER TABLE ticket_page_display ADD PRIMARY KEY (id)',
);
$queries['index']['tickets_participants'] = array(
	'IDX_8D675752700047D2' => 'CREATE INDEX IDX_8D675752700047D2 ON tickets_participants (ticket_id)',
	'IDX_8D675752217BBB47' => 'CREATE INDEX IDX_8D675752217BBB47 ON tickets_participants (person_id)',
	'IDX_8D675752EFFF2402' => 'CREATE INDEX IDX_8D675752EFFF2402 ON tickets_participants (access_code_id)',
	'IDX_8D6757523C7464FE' => 'CREATE INDEX IDX_8D6757523C7464FE ON tickets_participants (person_email_id)',
	'primary' => 'ALTER TABLE tickets_participants ADD PRIMARY KEY (id)',
);
$queries['index']['ticket_priorities'] = array(
	'primary' => 'ALTER TABLE ticket_priorities ADD PRIMARY KEY (id)',
);
$queries['index']['ticket_slas'] = array(
	'IDX_9E328D72700047D2' => 'CREATE INDEX IDX_9E328D72700047D2 ON ticket_slas (ticket_id)',
	'IDX_9E328D727A2CC8C4' => 'CREATE INDEX IDX_9E328D727A2CC8C4 ON ticket_slas (sla_id)',
	'primary' => 'ALTER TABLE ticket_slas ADD PRIMARY KEY (id)',
	'status_completed_warn_date_idx' => 'CREATE INDEX status_completed_warn_date_idx ON ticket_slas (sla_status, is_completed, warn_date)',
	'status_completed_fail_date_idx' => 'CREATE INDEX status_completed_fail_date_idx ON ticket_slas (sla_status, is_completed, fail_date)',
);
$queries['index']['ticket_triggers'] = array(
	'primary' => 'ALTER TABLE ticket_triggers ADD PRIMARY KEY (id)',
);
$queries['index']['ticket_trigger_logs'] = array(
	'primary' => 'ALTER TABLE ticket_trigger_logs ADD PRIMARY KEY (id)',
	'ticket_id_idx' => 'CREATE INDEX ticket_id_idx ON ticket_trigger_logs (ticket_id, trigger_id, date_criteria)',
);
$queries['index']['ticket_trigger_plugin_actions'] = array(
	'IDX_1D905890EC942BCF' => 'CREATE INDEX IDX_1D905890EC942BCF ON ticket_trigger_plugin_actions (plugin_id)',
	'primary' => 'ALTER TABLE ticket_trigger_plugin_actions ADD PRIMARY KEY (id)',
	'event_type_idx' => 'CREATE UNIQUE INDEX event_type_idx ON ticket_trigger_plugin_actions (event_type)',
);
$queries['index']['ticket_workflows'] = array(
	'primary' => 'ALTER TABLE ticket_workflows ADD PRIMARY KEY (id)',
);
$queries['index']['tmp_data'] = array(
	'primary' => 'ALTER TABLE tmp_data ADD PRIMARY KEY (id)',
	'name_idx' => 'CREATE INDEX name_idx ON tmp_data (name)',
	'date_expire_idx' => 'CREATE INDEX date_expire_idx ON tmp_data (date_expire)',
);
$queries['index']['twitter_accounts'] = array(
	'UNIQ_D4051D30A76ED395' => 'CREATE UNIQUE INDEX UNIQ_D4051D30A76ED395 ON twitter_accounts (user_id)',
	'primary' => 'ALTER TABLE twitter_accounts ADD PRIMARY KEY (id)',
);
$queries['index']['twitter_accounts_person'] = array(
	'IDX_BB12235C9B6B5FBA' => 'CREATE INDEX IDX_BB12235C9B6B5FBA ON twitter_accounts_person (account_id)',
	'IDX_BB12235C217BBB47' => 'CREATE INDEX IDX_BB12235C217BBB47 ON twitter_accounts_person (person_id)',
	'primary' => 'ALTER TABLE twitter_accounts_person ADD PRIMARY KEY (account_id, person_id)',
);
$queries['index']['twitter_accounts_followers'] = array(
	'IDX_EB8452969B6B5FBA' => 'CREATE INDEX IDX_EB8452969B6B5FBA ON twitter_accounts_followers (account_id)',
	'IDX_EB845296A76ED395' => 'CREATE INDEX IDX_EB845296A76ED395 ON twitter_accounts_followers (user_id)',
	'primary' => 'ALTER TABLE twitter_accounts_followers ADD PRIMARY KEY (id)',
	'account_user_idx' => 'CREATE UNIQUE INDEX account_user_idx ON twitter_accounts_followers (account_id, user_id)',
);
$queries['index']['twitter_accounts_friends'] = array(
	'IDX_FADA774D9B6B5FBA' => 'CREATE INDEX IDX_FADA774D9B6B5FBA ON twitter_accounts_friends (account_id)',
	'IDX_FADA774DA76ED395' => 'CREATE INDEX IDX_FADA774DA76ED395 ON twitter_accounts_friends (user_id)',
	'primary' => 'ALTER TABLE twitter_accounts_friends ADD PRIMARY KEY (id)',
	'account_user_idx' => 'CREATE UNIQUE INDEX account_user_idx ON twitter_accounts_friends (account_id, user_id)',
);
$queries['index']['twitter_accounts_searches'] = array(
	'IDX_5CC0E8CF9B6B5FBA' => 'CREATE INDEX IDX_5CC0E8CF9B6B5FBA ON twitter_accounts_searches (account_id)',
	'primary' => 'ALTER TABLE twitter_accounts_searches ADD PRIMARY KEY (id)',
);
$queries['index']['twitter_accounts_searches_statuses'] = array(
	'IDX_E52AFE3B498DD8E6' => 'CREATE INDEX IDX_E52AFE3B498DD8E6 ON twitter_accounts_searches_statuses (account_status_id)',
	'IDX_E52AFE3B650760A9' => 'CREATE INDEX IDX_E52AFE3B650760A9 ON twitter_accounts_searches_statuses (search_id)',
	'primary' => 'ALTER TABLE twitter_accounts_searches_statuses ADD PRIMARY KEY (account_status_id, search_id)',
	'search_date_idx' => 'CREATE INDEX search_date_idx ON twitter_accounts_searches_statuses (search_id, date_created)',
);
$queries['index']['twitter_accounts_statuses'] = array(
	'IDX_7728CEC79B6B5FBA' => 'CREATE INDEX IDX_7728CEC79B6B5FBA ON twitter_accounts_statuses (account_id)',
	'IDX_7728CEC76BF700BD' => 'CREATE INDEX IDX_7728CEC76BF700BD ON twitter_accounts_statuses (status_id)',
	'IDX_7728CEC73414710B' => 'CREATE INDEX IDX_7728CEC73414710B ON twitter_accounts_statuses (agent_id)',
	'IDX_7728CEC7FB3FBA04' => 'CREATE INDEX IDX_7728CEC7FB3FBA04 ON twitter_accounts_statuses (agent_team_id)',
	'IDX_7728CEC7E3C3016D' => 'CREATE INDEX IDX_7728CEC7E3C3016D ON twitter_accounts_statuses (action_agent_id)',
	'IDX_7728CEC754E76E81' => 'CREATE INDEX IDX_7728CEC754E76E81 ON twitter_accounts_statuses (retweeted_id)',
	'IDX_7728CEC7DD92DAB8' => 'CREATE INDEX IDX_7728CEC7DD92DAB8 ON twitter_accounts_statuses (in_reply_to_id)',
	'primary' => 'ALTER TABLE twitter_accounts_statuses ADD PRIMARY KEY (id)',
	'account_type_archived_idx' => 'CREATE INDEX account_type_archived_idx ON twitter_accounts_statuses (account_id, status_type, is_archived)',
	'account_archived_idx' => 'CREATE INDEX account_archived_idx ON twitter_accounts_statuses (account_id, is_archived)',
);
$queries['index']['twitter_accounts_statuses_notes'] = array(
	'IDX_E5D3CBA2498DD8E6' => 'CREATE INDEX IDX_E5D3CBA2498DD8E6 ON twitter_accounts_statuses_notes (account_status_id)',
	'IDX_E5D3CBA2217BBB47' => 'CREATE INDEX IDX_E5D3CBA2217BBB47 ON twitter_accounts_statuses_notes (person_id)',
	'primary' => 'ALTER TABLE twitter_accounts_statuses_notes ADD PRIMARY KEY (id)',
);
$queries['index']['twitter_statuses'] = array(
	'IDX_553D9D8DA76ED395' => 'CREATE INDEX IDX_553D9D8DA76ED395 ON twitter_statuses (user_id)',
	'IDX_553D9D8D6B347969' => 'CREATE INDEX IDX_553D9D8D6B347969 ON twitter_statuses (in_reply_to_status_id)',
	'IDX_553D9D8D72A1C5CA' => 'CREATE INDEX IDX_553D9D8D72A1C5CA ON twitter_statuses (retweet_id)',
	'IDX_553D9D8DD2347268' => 'CREATE INDEX IDX_553D9D8DD2347268 ON twitter_statuses (in_reply_to_user_id)',
	'IDX_553D9D8DE92F8F78' => 'CREATE INDEX IDX_553D9D8DE92F8F78 ON twitter_statuses (recipient_id)',
	'primary' => 'ALTER TABLE twitter_statuses ADD PRIMARY KEY (id)',
);
$queries['index']['twitter_statuses_long'] = array(
	'IDX_8B914BFB6BF700BD' => 'CREATE INDEX IDX_8B914BFB6BF700BD ON twitter_statuses_long (status_id)',
	'IDX_8B914BFB9B5BB4B8' => 'CREATE INDEX IDX_8B914BFB9B5BB4B8 ON twitter_statuses_long (for_user_id)',
	'primary' => 'ALTER TABLE twitter_statuses_long ADD PRIMARY KEY (id)',
);
$queries['index']['twitter_statuses_mentions'] = array(
	'IDX_66912DD16BF700BD' => 'CREATE INDEX IDX_66912DD16BF700BD ON twitter_statuses_mentions (status_id)',
	'IDX_66912DD1A76ED395' => 'CREATE INDEX IDX_66912DD1A76ED395 ON twitter_statuses_mentions (user_id)',
	'primary' => 'ALTER TABLE twitter_statuses_mentions ADD PRIMARY KEY (id)',
);
$queries['index']['twitter_statuses_tags'] = array(
	'IDX_DFBA76B56BF700BD' => 'CREATE INDEX IDX_DFBA76B56BF700BD ON twitter_statuses_tags (status_id)',
	'primary' => 'ALTER TABLE twitter_statuses_tags ADD PRIMARY KEY (id)',
);
$queries['index']['twitter_statuses_urls'] = array(
	'IDX_9A92D5326BF700BD' => 'CREATE INDEX IDX_9A92D5326BF700BD ON twitter_statuses_urls (status_id)',
	'primary' => 'ALTER TABLE twitter_statuses_urls ADD PRIMARY KEY (id)',
);
$queries['index']['twitter_stream'] = array(
	'IDX_8D6AB9A89B6B5FBA' => 'CREATE INDEX IDX_8D6AB9A89B6B5FBA ON twitter_stream (account_id)',
	'primary' => 'ALTER TABLE twitter_stream ADD PRIMARY KEY (id)',
);
$queries['index']['twitter_users'] = array(
	'primary' => 'ALTER TABLE twitter_users ADD PRIMARY KEY (id)',
	'last_follow_update_idx' => 'CREATE INDEX last_follow_update_idx ON twitter_users (last_follow_update)',
);
$queries['index']['twitter_users_followers'] = array(
	'IDX_F37AF1BEA76ED395' => 'CREATE INDEX IDX_F37AF1BEA76ED395 ON twitter_users_followers (user_id)',
	'IDX_F37AF1BE70FC2906' => 'CREATE INDEX IDX_F37AF1BE70FC2906 ON twitter_users_followers (follower_user_id)',
	'primary' => 'ALTER TABLE twitter_users_followers ADD PRIMARY KEY (id)',
	'user_follower_idx' => 'CREATE UNIQUE INDEX user_follower_idx ON twitter_users_followers (user_id, follower_user_id)',
);
$queries['index']['twitter_users_friends'] = array(
	'IDX_77C2EDABA76ED395' => 'CREATE INDEX IDX_77C2EDABA76ED395 ON twitter_users_friends (user_id)',
	'IDX_77C2EDAB93D1119E' => 'CREATE INDEX IDX_77C2EDAB93D1119E ON twitter_users_friends (friend_user_id)',
	'primary' => 'ALTER TABLE twitter_users_friends ADD PRIMARY KEY (id)',
	'user_friend_idx' => 'CREATE UNIQUE INDEX user_friend_idx ON twitter_users_friends (user_id, friend_user_id)',
);
$queries['index']['usergroups'] = array(
	'primary' => 'ALTER TABLE usergroups ADD PRIMARY KEY (id)',
);
$queries['index']['user_rules'] = array(
	'IDX_6B5862642940B3FB' => 'CREATE INDEX IDX_6B5862642940B3FB ON user_rules (add_organization_id)',
	'IDX_6B586264A19F75EA' => 'CREATE INDEX IDX_6B586264A19F75EA ON user_rules (add_usergroup_id)',
	'primary' => 'ALTER TABLE user_rules ADD PRIMARY KEY (id)',
);
$queries['index']['usersources'] = array(
	'IDX_4E3C994CEB0D3362' => 'CREATE INDEX IDX_4E3C994CEB0D3362 ON usersources (usersource_plugin_id)',
	'primary' => 'ALTER TABLE usersources ADD PRIMARY KEY (id)',
);
$queries['index']['usersource_plugins'] = array(
	'IDX_E484A367EC942BCF' => 'CREATE INDEX IDX_E484A367EC942BCF ON usersource_plugins (plugin_id)',
	'primary' => 'ALTER TABLE usersource_plugins ADD PRIMARY KEY (id)',
	'unique_key_idx' => 'CREATE UNIQUE INDEX unique_key_idx ON usersource_plugins (unique_key)',
);
$queries['index']['visitors'] = array(
	'IDX_7B74A43F217BBB47' => 'CREATE INDEX IDX_7B74A43F217BBB47 ON visitors (person_id)',
	'IDX_7B74A43F866B65F3' => 'CREATE INDEX IDX_7B74A43F866B65F3 ON visitors (initial_track_id)',
	'IDX_7B74A43F5B84E254' => 'CREATE INDEX IDX_7B74A43F5B84E254 ON visitors (visit_track_id)',
	'IDX_7B74A43F26B379DD' => 'CREATE INDEX IDX_7B74A43F26B379DD ON visitors (last_track_id)',
	'IDX_7B74A43F413BC2FF' => 'CREATE INDEX IDX_7B74A43F413BC2FF ON visitors (last_track_id_soft)',
	'primary' => 'ALTER TABLE visitors ADD PRIMARY KEY (id)',
	'date_last_idx' => 'CREATE INDEX date_last_idx ON visitors (date_last)',
);
$queries['index']['visitor_tracks'] = array(
	'IDX_E002459270BEE6D' => 'CREATE INDEX IDX_E002459270BEE6D ON visitor_tracks (visitor_id)',
	'primary' => 'ALTER TABLE visitor_tracks ADD PRIMARY KEY (id)',
	'idx1' => 'CREATE INDEX idx1 ON visitor_tracks (date_created, is_new_visit)',
);
$queries['index']['web_hooks'] = array(
	'primary' => 'ALTER TABLE web_hooks ADD PRIMARY KEY (id)',
);
$queries['index']['widgets'] = array(
	'IDX_9D58E4C1EC942BCF' => 'CREATE INDEX IDX_9D58E4C1EC942BCF ON widgets (plugin_id)',
	'primary' => 'ALTER TABLE widgets ADD PRIMARY KEY (id)',
	'unique_key_idx' => 'CREATE UNIQUE INDEX unique_key_idx ON widgets (unique_key)',
);
$queries['index']['worker_jobs'] = array(
	'primary' => 'ALTER TABLE worker_jobs ADD PRIMARY KEY (id)',
);




$queries['fk']['agent_activity'] = array(
	'FK_9AA510CE3414710B' => 'ALTER TABLE agent_activity ADD CONSTRAINT FK_9AA510CE3414710B FOREIGN KEY (agent_id) REFERENCES people (id) ON DELETE CASCADE',
);
$queries['fk']['agent_alerts'] = array(
	'FK_A99D974D217BBB47' => 'ALTER TABLE agent_alerts ADD CONSTRAINT FK_A99D974D217BBB47 FOREIGN KEY (person_id) REFERENCES people (id) ON DELETE CASCADE',
);
$queries['fk']['agent_team_members'] = array(
	'FK_CC952C03296CD8AE' => 'ALTER TABLE agent_team_members ADD CONSTRAINT FK_CC952C03296CD8AE FOREIGN KEY (team_id) REFERENCES agent_teams (id) ON DELETE CASCADE',
	'FK_CC952C03217BBB47' => 'ALTER TABLE agent_team_members ADD CONSTRAINT FK_CC952C03217BBB47 FOREIGN KEY (person_id) REFERENCES people (id) ON DELETE CASCADE',
);
$queries['fk']['api_keys'] = array(
	'FK_9579321F217BBB47' => 'ALTER TABLE api_keys ADD CONSTRAINT FK_9579321F217BBB47 FOREIGN KEY (person_id) REFERENCES people (id) ON DELETE CASCADE',
);
$queries['fk']['api_key_rate_limit'] = array(
	'FK_BBDD0D428BE312B3' => 'ALTER TABLE api_key_rate_limit ADD CONSTRAINT FK_BBDD0D428BE312B3 FOREIGN KEY (api_key_id) REFERENCES api_keys (id) ON DELETE CASCADE',
);
$queries['fk']['api_token'] = array(
	'FK_7BA2F5EB217BBB47' => 'ALTER TABLE api_token ADD CONSTRAINT FK_7BA2F5EB217BBB47 FOREIGN KEY (person_id) REFERENCES people (id) ON DELETE CASCADE',
);
$queries['fk']['api_token_rate_limit'] = array(
	'FK_458445A9217BBB47' => 'ALTER TABLE api_token_rate_limit ADD CONSTRAINT FK_458445A9217BBB47 FOREIGN KEY (person_id) REFERENCES api_token (person_id) ON DELETE CASCADE',
);
$queries['fk']['articles'] = array(
	'FK_BFDD3168217BBB47' => 'ALTER TABLE articles ADD CONSTRAINT FK_BFDD3168217BBB47 FOREIGN KEY (person_id) REFERENCES people (id) ON DELETE SET NULL',
	'FK_BFDD316882F1BAF4' => 'ALTER TABLE articles ADD CONSTRAINT FK_BFDD316882F1BAF4 FOREIGN KEY (language_id) REFERENCES languages (id) ON DELETE CASCADE',
);
$queries['fk']['article_to_categories'] = array(
	'FK_9A1B4BB07294869C' => 'ALTER TABLE article_to_categories ADD CONSTRAINT FK_9A1B4BB07294869C FOREIGN KEY (article_id) REFERENCES articles (id) ON DELETE CASCADE',
	'FK_9A1B4BB012469DE2' => 'ALTER TABLE article_to_categories ADD CONSTRAINT FK_9A1B4BB012469DE2 FOREIGN KEY (category_id) REFERENCES article_categories (id) ON DELETE CASCADE',
);
$queries['fk']['article_to_product'] = array(
	'FK_610BE8D97294869C' => 'ALTER TABLE article_to_product ADD CONSTRAINT FK_610BE8D97294869C FOREIGN KEY (article_id) REFERENCES articles (id) ON DELETE CASCADE',
	'FK_610BE8D94584665A' => 'ALTER TABLE article_to_product ADD CONSTRAINT FK_610BE8D94584665A FOREIGN KEY (product_id) REFERENCES products (id) ON DELETE CASCADE',
);
$queries['fk']['article_attachments'] = array(
	'FK_DD4790B17294869C' => 'ALTER TABLE article_attachments ADD CONSTRAINT FK_DD4790B17294869C FOREIGN KEY (article_id) REFERENCES articles (id) ON DELETE CASCADE',
	'FK_DD4790B1217BBB47' => 'ALTER TABLE article_attachments ADD CONSTRAINT FK_DD4790B1217BBB47 FOREIGN KEY (person_id) REFERENCES people (id) ON DELETE SET NULL',
	'FK_DD4790B1ED3E8EA5' => 'ALTER TABLE article_attachments ADD CONSTRAINT FK_DD4790B1ED3E8EA5 FOREIGN KEY (blob_id) REFERENCES blobs (id) ON DELETE CASCADE',
);
$queries['fk']['article_categories'] = array(
	'FK_62A97E9727ACA70' => 'ALTER TABLE article_categories ADD CONSTRAINT FK_62A97E9727ACA70 FOREIGN KEY (parent_id) REFERENCES article_categories (id) ON DELETE SET NULL',
);
$queries['fk']['article_category2usergroup'] = array(
	'FK_6AD8B03212469DE2' => 'ALTER TABLE article_category2usergroup ADD CONSTRAINT FK_6AD8B03212469DE2 FOREIGN KEY (category_id) REFERENCES article_categories (id) ON DELETE CASCADE',
	'FK_6AD8B032D2112630' => 'ALTER TABLE article_category2usergroup ADD CONSTRAINT FK_6AD8B032D2112630 FOREIGN KEY (usergroup_id) REFERENCES usergroups (id) ON DELETE CASCADE',
);
$queries['fk']['article_comments'] = array(
	'FK_A7662417294869C' => 'ALTER TABLE article_comments ADD CONSTRAINT FK_A7662417294869C FOREIGN KEY (article_id) REFERENCES articles (id) ON DELETE CASCADE',
	'FK_A766241217BBB47' => 'ALTER TABLE article_comments ADD CONSTRAINT FK_A766241217BBB47 FOREIGN KEY (person_id) REFERENCES people (id) ON DELETE SET NULL',
	'FK_A76624170BEE6D' => 'ALTER TABLE article_comments ADD CONSTRAINT FK_A76624170BEE6D FOREIGN KEY (visitor_id) REFERENCES visitors (id) ON DELETE SET NULL',
);
$queries['fk']['article_pending_create'] = array(
	'FK_27A971C3217BBB47' => 'ALTER TABLE article_pending_create ADD CONSTRAINT FK_27A971C3217BBB47 FOREIGN KEY (person_id) REFERENCES people (id) ON DELETE CASCADE',
	'FK_27A971C3700047D2' => 'ALTER TABLE article_pending_create ADD CONSTRAINT FK_27A971C3700047D2 FOREIGN KEY (ticket_id) REFERENCES tickets (id) ON DELETE CASCADE',
	'FK_27A971C3C5E9817D' => 'ALTER TABLE article_pending_create ADD CONSTRAINT FK_27A971C3C5E9817D FOREIGN KEY (ticket_message_id) REFERENCES tickets_messages (id) ON DELETE CASCADE',
);
$queries['fk']['article_revisions'] = array(
	'FK_538472A17294869C' => 'ALTER TABLE article_revisions ADD CONSTRAINT FK_538472A17294869C FOREIGN KEY (article_id) REFERENCES articles (id) ON DELETE CASCADE',
	'FK_538472A1217BBB47' => 'ALTER TABLE article_revisions ADD CONSTRAINT FK_538472A1217BBB47 FOREIGN KEY (person_id) REFERENCES people (id) ON DELETE SET NULL',
);
$queries['fk']['blobs'] = array(
	'FK_896C3E356BBE2052' => 'ALTER TABLE blobs ADD CONSTRAINT FK_896C3E356BBE2052 FOREIGN KEY (original_blob_id) REFERENCES blobs (id) ON DELETE SET NULL',
);
$queries['fk']['chat_blocks'] = array(
	'FK_A931A25970BEE6D' => 'ALTER TABLE chat_blocks ADD CONSTRAINT FK_A931A25970BEE6D FOREIGN KEY (visitor_id) REFERENCES visitors (id) ON DELETE SET NULL',
	'FK_A931A259B5BE2AA2' => 'ALTER TABLE chat_blocks ADD CONSTRAINT FK_A931A259B5BE2AA2 FOREIGN KEY (by_person_id) REFERENCES people (id) ON DELETE SET NULL',
);
$queries['fk']['chat_conversations'] = array(
	'FK_5813432EAE80F5DF' => 'ALTER TABLE chat_conversations ADD CONSTRAINT FK_5813432EAE80F5DF FOREIGN KEY (department_id) REFERENCES departments (id) ON DELETE SET NULL',
	'FK_5813432E3414710B' => 'ALTER TABLE chat_conversations ADD CONSTRAINT FK_5813432E3414710B FOREIGN KEY (agent_id) REFERENCES people (id) ON DELETE SET NULL',
	'FK_5813432EFB3FBA04' => 'ALTER TABLE chat_conversations ADD CONSTRAINT FK_5813432EFB3FBA04 FOREIGN KEY (agent_team_id) REFERENCES agent_teams (id) ON DELETE SET NULL',
	'FK_5813432E217BBB47' => 'ALTER TABLE chat_conversations ADD CONSTRAINT FK_5813432E217BBB47 FOREIGN KEY (person_id) REFERENCES people (id) ON DELETE SET NULL',
	'FK_5813432E613FECDF' => 'ALTER TABLE chat_conversations ADD CONSTRAINT FK_5813432E613FECDF FOREIGN KEY (session_id) REFERENCES sessions (id) ON DELETE SET NULL',
	'FK_5813432E70BEE6D' => 'ALTER TABLE chat_conversations ADD CONSTRAINT FK_5813432E70BEE6D FOREIGN KEY (visitor_id) REFERENCES visitors (id) ON DELETE SET NULL',
);
$queries['fk']['chat_conversation_to_person'] = array(
	'FK_1CA5AE439AC0396' => 'ALTER TABLE chat_conversation_to_person ADD CONSTRAINT FK_1CA5AE439AC0396 FOREIGN KEY (conversation_id) REFERENCES chat_conversations (id) ON DELETE CASCADE',
	'FK_1CA5AE43217BBB47' => 'ALTER TABLE chat_conversation_to_person ADD CONSTRAINT FK_1CA5AE43217BBB47 FOREIGN KEY (person_id) REFERENCES people (id) ON DELETE CASCADE',
);
$queries['fk']['chat_messages'] = array(
	'FK_EF20C9A69AC0396' => 'ALTER TABLE chat_messages ADD CONSTRAINT FK_EF20C9A69AC0396 FOREIGN KEY (conversation_id) REFERENCES chat_conversations (id) ON DELETE CASCADE',
	'FK_EF20C9A6F675F31B' => 'ALTER TABLE chat_messages ADD CONSTRAINT FK_EF20C9A6F675F31B FOREIGN KEY (author_id) REFERENCES people (id) ON DELETE SET NULL',
);
$queries['fk']['chat_page_display'] = array(
	'FK_85AF0B7AE80F5DF' => 'ALTER TABLE chat_page_display ADD CONSTRAINT FK_85AF0B7AE80F5DF FOREIGN KEY (department_id) REFERENCES departments (id) ON DELETE CASCADE',
);
$queries['fk']['client_messages'] = array(
	'FK_F5E42E53D2872966' => 'ALTER TABLE client_messages ADD CONSTRAINT FK_F5E42E53D2872966 FOREIGN KEY (for_person_id) REFERENCES people (id) ON DELETE CASCADE',
);
$queries['fk']['content_subscriptions'] = array(
	'FK_5FADAC10217BBB47' => 'ALTER TABLE content_subscriptions ADD CONSTRAINT FK_5FADAC10217BBB47 FOREIGN KEY (person_id) REFERENCES people (id) ON DELETE CASCADE',
	'FK_5FADAC107294869C' => 'ALTER TABLE content_subscriptions ADD CONSTRAINT FK_5FADAC107294869C FOREIGN KEY (article_id) REFERENCES articles (id) ON DELETE CASCADE',
	'FK_5FADAC10C667AEAB' => 'ALTER TABLE content_subscriptions ADD CONSTRAINT FK_5FADAC10C667AEAB FOREIGN KEY (download_id) REFERENCES downloads (id) ON DELETE CASCADE',
	'FK_5FADAC10D249A887' => 'ALTER TABLE content_subscriptions ADD CONSTRAINT FK_5FADAC10D249A887 FOREIGN KEY (feedback_id) REFERENCES feedback (id) ON DELETE CASCADE',
	'FK_5FADAC10B5A459A0' => 'ALTER TABLE content_subscriptions ADD CONSTRAINT FK_5FADAC10B5A459A0 FOREIGN KEY (news_id) REFERENCES news (id) ON DELETE CASCADE',
);
$queries['fk']['custom_data_article'] = array(
	'FK_1DB64F8C7294869C' => 'ALTER TABLE custom_data_article ADD CONSTRAINT FK_1DB64F8C7294869C FOREIGN KEY (article_id) REFERENCES articles (id) ON DELETE CASCADE',
	'FK_1DB64F8C443707B0' => 'ALTER TABLE custom_data_article ADD CONSTRAINT FK_1DB64F8C443707B0 FOREIGN KEY (field_id) REFERENCES custom_def_article (id) ON DELETE CASCADE',
	'FK_1DB64F8C3F6A6D56' => 'ALTER TABLE custom_data_article ADD CONSTRAINT FK_1DB64F8C3F6A6D56 FOREIGN KEY (root_field_id) REFERENCES custom_def_article (id) ON DELETE CASCADE',
);
$queries['fk']['custom_data_chat'] = array(
	'FK_94E84EEE9AC0396' => 'ALTER TABLE custom_data_chat ADD CONSTRAINT FK_94E84EEE9AC0396 FOREIGN KEY (conversation_id) REFERENCES chat_conversations (id) ON DELETE CASCADE',
	'FK_94E84EEE443707B0' => 'ALTER TABLE custom_data_chat ADD CONSTRAINT FK_94E84EEE443707B0 FOREIGN KEY (field_id) REFERENCES custom_def_chat (id) ON DELETE CASCADE',
	'FK_94E84EEE3F6A6D56' => 'ALTER TABLE custom_data_chat ADD CONSTRAINT FK_94E84EEE3F6A6D56 FOREIGN KEY (root_field_id) REFERENCES custom_def_chat (id) ON DELETE CASCADE',
);
$queries['fk']['custom_data_feedback'] = array(
	'FK_92E9C37FD249A887' => 'ALTER TABLE custom_data_feedback ADD CONSTRAINT FK_92E9C37FD249A887 FOREIGN KEY (feedback_id) REFERENCES feedback (id) ON DELETE CASCADE',
	'FK_92E9C37F443707B0' => 'ALTER TABLE custom_data_feedback ADD CONSTRAINT FK_92E9C37F443707B0 FOREIGN KEY (field_id) REFERENCES custom_def_feedback (id) ON DELETE CASCADE',
	'FK_92E9C37F3F6A6D56' => 'ALTER TABLE custom_data_feedback ADD CONSTRAINT FK_92E9C37F3F6A6D56 FOREIGN KEY (root_field_id) REFERENCES custom_def_feedback (id) ON DELETE CASCADE',
);
$queries['fk']['custom_data_organizations'] = array(
	'FK_20C5B8AC32C8A3DE' => 'ALTER TABLE custom_data_organizations ADD CONSTRAINT FK_20C5B8AC32C8A3DE FOREIGN KEY (organization_id) REFERENCES organizations (id) ON DELETE CASCADE',
	'FK_20C5B8AC443707B0' => 'ALTER TABLE custom_data_organizations ADD CONSTRAINT FK_20C5B8AC443707B0 FOREIGN KEY (field_id) REFERENCES custom_def_organizations (id) ON DELETE CASCADE',
	'FK_20C5B8AC3F6A6D56' => 'ALTER TABLE custom_data_organizations ADD CONSTRAINT FK_20C5B8AC3F6A6D56 FOREIGN KEY (root_field_id) REFERENCES custom_def_organizations (id) ON DELETE CASCADE',
);
$queries['fk']['custom_data_person'] = array(
	'FK_621E55A5217BBB47' => 'ALTER TABLE custom_data_person ADD CONSTRAINT FK_621E55A5217BBB47 FOREIGN KEY (person_id) REFERENCES people (id) ON DELETE CASCADE',
	'FK_621E55A5443707B0' => 'ALTER TABLE custom_data_person ADD CONSTRAINT FK_621E55A5443707B0 FOREIGN KEY (field_id) REFERENCES custom_def_people (id) ON DELETE CASCADE',
	'FK_621E55A53F6A6D56' => 'ALTER TABLE custom_data_person ADD CONSTRAINT FK_621E55A53F6A6D56 FOREIGN KEY (root_field_id) REFERENCES custom_def_people (id) ON DELETE CASCADE',
);
$queries['fk']['custom_data_product'] = array(
	'FK_CCC645474584665A' => 'ALTER TABLE custom_data_product ADD CONSTRAINT FK_CCC645474584665A FOREIGN KEY (product_id) REFERENCES products (id) ON DELETE CASCADE',
	'FK_CCC64547443707B0' => 'ALTER TABLE custom_data_product ADD CONSTRAINT FK_CCC64547443707B0 FOREIGN KEY (field_id) REFERENCES custom_def_products (id) ON DELETE CASCADE',
	'FK_CCC645473F6A6D56' => 'ALTER TABLE custom_data_product ADD CONSTRAINT FK_CCC645473F6A6D56 FOREIGN KEY (root_field_id) REFERENCES custom_def_products (id) ON DELETE CASCADE',
);
$queries['fk']['custom_data_ticket'] = array(
	'FK_C1622970700047D2' => 'ALTER TABLE custom_data_ticket ADD CONSTRAINT FK_C1622970700047D2 FOREIGN KEY (ticket_id) REFERENCES tickets (id) ON DELETE CASCADE',
	'FK_C1622970443707B0' => 'ALTER TABLE custom_data_ticket ADD CONSTRAINT FK_C1622970443707B0 FOREIGN KEY (field_id) REFERENCES custom_def_ticket (id) ON DELETE CASCADE',
	'FK_C16229703F6A6D56' => 'ALTER TABLE custom_data_ticket ADD CONSTRAINT FK_C16229703F6A6D56 FOREIGN KEY (root_field_id) REFERENCES custom_def_ticket (id) ON DELETE CASCADE',
);
$queries['fk']['custom_def_article'] = array(
	'FK_B651E6F4727ACA70' => 'ALTER TABLE custom_def_article ADD CONSTRAINT FK_B651E6F4727ACA70 FOREIGN KEY (parent_id) REFERENCES custom_def_article (id) ON DELETE CASCADE',
	'FK_B651E6F4EC942BCF' => 'ALTER TABLE custom_def_article ADD CONSTRAINT FK_B651E6F4EC942BCF FOREIGN KEY (plugin_id) REFERENCES plugins (id) ON DELETE SET NULL',
);
$queries['fk']['custom_def_chat'] = array(
	'FK_2DE86CE5727ACA70' => 'ALTER TABLE custom_def_chat ADD CONSTRAINT FK_2DE86CE5727ACA70 FOREIGN KEY (parent_id) REFERENCES custom_def_chat (id) ON DELETE CASCADE',
	'FK_2DE86CE5EC942BCF' => 'ALTER TABLE custom_def_chat ADD CONSTRAINT FK_2DE86CE5EC942BCF FOREIGN KEY (plugin_id) REFERENCES plugins (id) ON DELETE SET NULL',
);
$queries['fk']['custom_def_feedback'] = array(
	'FK_CC9CDDD8727ACA70' => 'ALTER TABLE custom_def_feedback ADD CONSTRAINT FK_CC9CDDD8727ACA70 FOREIGN KEY (parent_id) REFERENCES custom_def_feedback (id) ON DELETE CASCADE',
	'FK_CC9CDDD8EC942BCF' => 'ALTER TABLE custom_def_feedback ADD CONSTRAINT FK_CC9CDDD8EC942BCF FOREIGN KEY (plugin_id) REFERENCES plugins (id) ON DELETE SET NULL',
);
$queries['fk']['custom_def_organizations'] = array(
	'FK_240601E7727ACA70' => 'ALTER TABLE custom_def_organizations ADD CONSTRAINT FK_240601E7727ACA70 FOREIGN KEY (parent_id) REFERENCES custom_def_organizations (id) ON DELETE CASCADE',
	'FK_240601E7EC942BCF' => 'ALTER TABLE custom_def_organizations ADD CONSTRAINT FK_240601E7EC942BCF FOREIGN KEY (plugin_id) REFERENCES plugins (id) ON DELETE SET NULL',
);
$queries['fk']['custom_def_people'] = array(
	'FK_4840CFDA727ACA70' => 'ALTER TABLE custom_def_people ADD CONSTRAINT FK_4840CFDA727ACA70 FOREIGN KEY (parent_id) REFERENCES custom_def_people (id) ON DELETE CASCADE',
	'FK_4840CFDAEC942BCF' => 'ALTER TABLE custom_def_people ADD CONSTRAINT FK_4840CFDAEC942BCF FOREIGN KEY (plugin_id) REFERENCES plugins (id) ON DELETE SET NULL',
);
$queries['fk']['custom_def_products'] = array(
	'FK_AD0FC3DA727ACA70' => 'ALTER TABLE custom_def_products ADD CONSTRAINT FK_AD0FC3DA727ACA70 FOREIGN KEY (parent_id) REFERENCES custom_def_products (id) ON DELETE CASCADE',
	'FK_AD0FC3DAEC942BCF' => 'ALTER TABLE custom_def_products ADD CONSTRAINT FK_AD0FC3DAEC942BCF FOREIGN KEY (plugin_id) REFERENCES plugins (id) ON DELETE SET NULL',
);
$queries['fk']['custom_def_ticket'] = array(
	'FK_F7F6085F727ACA70' => 'ALTER TABLE custom_def_ticket ADD CONSTRAINT FK_F7F6085F727ACA70 FOREIGN KEY (parent_id) REFERENCES custom_def_ticket (id) ON DELETE CASCADE',
	'FK_F7F6085FEC942BCF' => 'ALTER TABLE custom_def_ticket ADD CONSTRAINT FK_F7F6085FEC942BCF FOREIGN KEY (plugin_id) REFERENCES plugins (id) ON DELETE SET NULL',
);
$queries['fk']['departments'] = array(
	'FK_16AEB8D4727ACA70' => 'ALTER TABLE departments ADD CONSTRAINT FK_16AEB8D4727ACA70 FOREIGN KEY (parent_id) REFERENCES departments (id) ON DELETE CASCADE',
	'FK_16AEB8D4FBCC7CDF' => 'ALTER TABLE departments ADD CONSTRAINT FK_16AEB8D4FBCC7CDF FOREIGN KEY (email_gateway_id) REFERENCES email_gateways (id) ON DELETE SET NULL',
);
$queries['fk']['department_permissions'] = array(
	'FK_84C36B30AE80F5DF' => 'ALTER TABLE department_permissions ADD CONSTRAINT FK_84C36B30AE80F5DF FOREIGN KEY (department_id) REFERENCES departments (id) ON DELETE CASCADE',
	'FK_84C36B30D2112630' => 'ALTER TABLE department_permissions ADD CONSTRAINT FK_84C36B30D2112630 FOREIGN KEY (usergroup_id) REFERENCES usergroups (id) ON DELETE CASCADE',
	'FK_84C36B30217BBB47' => 'ALTER TABLE department_permissions ADD CONSTRAINT FK_84C36B30217BBB47 FOREIGN KEY (person_id) REFERENCES people (id) ON DELETE CASCADE',
);
$queries['fk']['downloads'] = array(
	'FK_4B73A4B512469DE2' => 'ALTER TABLE downloads ADD CONSTRAINT FK_4B73A4B512469DE2 FOREIGN KEY (category_id) REFERENCES download_categories (id) ON DELETE SET NULL',
	'FK_4B73A4B5ED3E8EA5' => 'ALTER TABLE downloads ADD CONSTRAINT FK_4B73A4B5ED3E8EA5 FOREIGN KEY (blob_id) REFERENCES blobs (id)',
	'FK_4B73A4B5217BBB47' => 'ALTER TABLE downloads ADD CONSTRAINT FK_4B73A4B5217BBB47 FOREIGN KEY (person_id) REFERENCES people (id) ON DELETE SET NULL',
	'FK_4B73A4B582F1BAF4' => 'ALTER TABLE downloads ADD CONSTRAINT FK_4B73A4B582F1BAF4 FOREIGN KEY (language_id) REFERENCES languages (id) ON DELETE CASCADE',
);
$queries['fk']['download_categories'] = array(
	'FK_3317F15727ACA70' => 'ALTER TABLE download_categories ADD CONSTRAINT FK_3317F15727ACA70 FOREIGN KEY (parent_id) REFERENCES download_categories (id) ON DELETE SET NULL',
);
$queries['fk']['download_category2usergroup'] = array(
	'FK_53A2246F12469DE2' => 'ALTER TABLE download_category2usergroup ADD CONSTRAINT FK_53A2246F12469DE2 FOREIGN KEY (category_id) REFERENCES download_categories (id) ON DELETE CASCADE',
	'FK_53A2246FD2112630' => 'ALTER TABLE download_category2usergroup ADD CONSTRAINT FK_53A2246FD2112630 FOREIGN KEY (usergroup_id) REFERENCES usergroups (id) ON DELETE CASCADE',
);
$queries['fk']['download_comments'] = array(
	'FK_B43CDE14C667AEAB' => 'ALTER TABLE download_comments ADD CONSTRAINT FK_B43CDE14C667AEAB FOREIGN KEY (download_id) REFERENCES downloads (id) ON DELETE CASCADE',
	'FK_B43CDE14217BBB47' => 'ALTER TABLE download_comments ADD CONSTRAINT FK_B43CDE14217BBB47 FOREIGN KEY (person_id) REFERENCES people (id) ON DELETE SET NULL',
	'FK_B43CDE1470BEE6D' => 'ALTER TABLE download_comments ADD CONSTRAINT FK_B43CDE1470BEE6D FOREIGN KEY (visitor_id) REFERENCES visitors (id) ON DELETE SET NULL',
);
$queries['fk']['download_revisions'] = array(
	'FK_483B9D66C667AEAB' => 'ALTER TABLE download_revisions ADD CONSTRAINT FK_483B9D66C667AEAB FOREIGN KEY (download_id) REFERENCES downloads (id) ON DELETE CASCADE',
	'FK_483B9D66ED3E8EA5' => 'ALTER TABLE download_revisions ADD CONSTRAINT FK_483B9D66ED3E8EA5 FOREIGN KEY (blob_id) REFERENCES blobs (id)',
	'FK_483B9D66217BBB47' => 'ALTER TABLE download_revisions ADD CONSTRAINT FK_483B9D66217BBB47 FOREIGN KEY (person_id) REFERENCES people (id) ON DELETE SET NULL',
);
$queries['fk']['drafts'] = array(
	'FK_EC2AE4C0217BBB47' => 'ALTER TABLE drafts ADD CONSTRAINT FK_EC2AE4C0217BBB47 FOREIGN KEY (person_id) REFERENCES people (id) ON DELETE CASCADE',
);
$queries['fk']['email_gateways'] = array(
	'FK_D0C6423237308465' => 'ALTER TABLE email_gateways ADD CONSTRAINT FK_D0C6423237308465 FOREIGN KEY (linked_transport_id) REFERENCES email_transports (id) ON DELETE SET NULL',
	'FK_D0C64232AE80F5DF' => 'ALTER TABLE email_gateways ADD CONSTRAINT FK_D0C64232AE80F5DF FOREIGN KEY (department_id) REFERENCES departments (id) ON DELETE SET NULL',
);
$queries['fk']['email_gateway_addresses'] = array(
	'FK_EC270D12FBCC7CDF' => 'ALTER TABLE email_gateway_addresses ADD CONSTRAINT FK_EC270D12FBCC7CDF FOREIGN KEY (email_gateway_id) REFERENCES email_gateways (id) ON DELETE CASCADE',
);
$queries['fk']['email_sources'] = array(
	'FK_6F9D0D3DED3E8EA5' => 'ALTER TABLE email_sources ADD CONSTRAINT FK_6F9D0D3DED3E8EA5 FOREIGN KEY (blob_id) REFERENCES blobs (id) ON DELETE CASCADE',
	'FK_6F9D0D3D577F8E00' => 'ALTER TABLE email_sources ADD CONSTRAINT FK_6F9D0D3D577F8E00 FOREIGN KEY (gateway_id) REFERENCES email_gateways (id) ON DELETE CASCADE',
);
$queries['fk']['email_uids'] = array(
	'FK_6D08D1BD577F8E00' => 'ALTER TABLE email_uids ADD CONSTRAINT FK_6D08D1BD577F8E00 FOREIGN KEY (gateway_id) REFERENCES email_gateways (id) ON DELETE CASCADE',
);
$queries['fk']['feedback'] = array(
	'FK_D2294458169CE813' => 'ALTER TABLE feedback ADD CONSTRAINT FK_D2294458169CE813 FOREIGN KEY (status_category_id) REFERENCES feedback_status_categories (id) ON DELETE SET NULL',
	'FK_D229445812469DE2' => 'ALTER TABLE feedback ADD CONSTRAINT FK_D229445812469DE2 FOREIGN KEY (category_id) REFERENCES feedback_categories (id)',
	'FK_D2294458217BBB47' => 'ALTER TABLE feedback ADD CONSTRAINT FK_D2294458217BBB47 FOREIGN KEY (person_id) REFERENCES people (id) ON DELETE SET NULL',
	'FK_D229445882F1BAF4' => 'ALTER TABLE feedback ADD CONSTRAINT FK_D229445882F1BAF4 FOREIGN KEY (language_id) REFERENCES languages (id) ON DELETE CASCADE',
);
$queries['fk']['feedback_attachments'] = array(
	'FK_CC264F12D249A887' => 'ALTER TABLE feedback_attachments ADD CONSTRAINT FK_CC264F12D249A887 FOREIGN KEY (feedback_id) REFERENCES feedback (id) ON DELETE CASCADE',
	'FK_CC264F12217BBB47' => 'ALTER TABLE feedback_attachments ADD CONSTRAINT FK_CC264F12217BBB47 FOREIGN KEY (person_id) REFERENCES people (id) ON DELETE SET NULL',
	'FK_CC264F12ED3E8EA5' => 'ALTER TABLE feedback_attachments ADD CONSTRAINT FK_CC264F12ED3E8EA5 FOREIGN KEY (blob_id) REFERENCES blobs (id) ON DELETE CASCADE',
);
$queries['fk']['feedback_categories'] = array(
	'FK_66FE6832727ACA70' => 'ALTER TABLE feedback_categories ADD CONSTRAINT FK_66FE6832727ACA70 FOREIGN KEY (parent_id) REFERENCES feedback_categories (id) ON DELETE SET NULL',
);
$queries['fk']['feedback_category2usergroup'] = array(
	'FK_B304B93C12469DE2' => 'ALTER TABLE feedback_category2usergroup ADD CONSTRAINT FK_B304B93C12469DE2 FOREIGN KEY (category_id) REFERENCES feedback_categories (id) ON DELETE CASCADE',
	'FK_B304B93CD2112630' => 'ALTER TABLE feedback_category2usergroup ADD CONSTRAINT FK_B304B93CD2112630 FOREIGN KEY (usergroup_id) REFERENCES usergroups (id) ON DELETE CASCADE',
);
$queries['fk']['feedback_comments'] = array(
	'FK_10D03D58D249A887' => 'ALTER TABLE feedback_comments ADD CONSTRAINT FK_10D03D58D249A887 FOREIGN KEY (feedback_id) REFERENCES feedback (id) ON DELETE CASCADE',
	'FK_10D03D58217BBB47' => 'ALTER TABLE feedback_comments ADD CONSTRAINT FK_10D03D58217BBB47 FOREIGN KEY (person_id) REFERENCES people (id) ON DELETE SET NULL',
	'FK_10D03D5870BEE6D' => 'ALTER TABLE feedback_comments ADD CONSTRAINT FK_10D03D5870BEE6D FOREIGN KEY (visitor_id) REFERENCES visitors (id) ON DELETE SET NULL',
);
$queries['fk']['feedback_revisions'] = array(
	'FK_37F57C3ED249A887' => 'ALTER TABLE feedback_revisions ADD CONSTRAINT FK_37F57C3ED249A887 FOREIGN KEY (feedback_id) REFERENCES feedback (id) ON DELETE CASCADE',
	'FK_37F57C3E217BBB47' => 'ALTER TABLE feedback_revisions ADD CONSTRAINT FK_37F57C3E217BBB47 FOREIGN KEY (person_id) REFERENCES people (id) ON DELETE SET NULL',
);
$queries['fk']['glossary_words'] = array(
	'FK_1A8003DAD11EA911' => 'ALTER TABLE glossary_words ADD CONSTRAINT FK_1A8003DAD11EA911 FOREIGN KEY (definition_id) REFERENCES glossary_word_definitions (id) ON DELETE CASCADE',
);
$queries['fk']['kb_subscriptions'] = array(
	'FK_1F05AAF5217BBB47' => 'ALTER TABLE kb_subscriptions ADD CONSTRAINT FK_1F05AAF5217BBB47 FOREIGN KEY (person_id) REFERENCES people (id) ON DELETE CASCADE',
	'FK_1F05AAF57294869C' => 'ALTER TABLE kb_subscriptions ADD CONSTRAINT FK_1F05AAF57294869C FOREIGN KEY (article_id) REFERENCES articles (id) ON DELETE CASCADE',
	'FK_1F05AAF512469DE2' => 'ALTER TABLE kb_subscriptions ADD CONSTRAINT FK_1F05AAF512469DE2 FOREIGN KEY (category_id) REFERENCES article_categories (id) ON DELETE CASCADE',
);
$queries['fk']['labels_articles'] = array(
	'FK_2F30AF707294869C' => 'ALTER TABLE labels_articles ADD CONSTRAINT FK_2F30AF707294869C FOREIGN KEY (article_id) REFERENCES articles (id) ON DELETE CASCADE',
);
$queries['fk']['labels_blobs'] = array(
	'FK_EC63B2F0ED3E8EA5' => 'ALTER TABLE labels_blobs ADD CONSTRAINT FK_EC63B2F0ED3E8EA5 FOREIGN KEY (blob_id) REFERENCES blobs (id) ON DELETE CASCADE',
);
$queries['fk']['labels_chat_conversations'] = array(
	'FK_99205D121A9A7125' => 'ALTER TABLE labels_chat_conversations ADD CONSTRAINT FK_99205D121A9A7125 FOREIGN KEY (chat_id) REFERENCES chat_conversations (id) ON DELETE CASCADE',
);
$queries['fk']['labels_downloads'] = array(
	'FK_588FD17DC667AEAB' => 'ALTER TABLE labels_downloads ADD CONSTRAINT FK_588FD17DC667AEAB FOREIGN KEY (download_id) REFERENCES downloads (id) ON DELETE CASCADE',
);
$queries['fk']['labels_feedback'] = array(
	'FK_42C4DA40D249A887' => 'ALTER TABLE labels_feedback ADD CONSTRAINT FK_42C4DA40D249A887 FOREIGN KEY (feedback_id) REFERENCES feedback (id) ON DELETE CASCADE',
);
$queries['fk']['labels_news'] = array(
	'FK_A2869A08B5A459A0' => 'ALTER TABLE labels_news ADD CONSTRAINT FK_A2869A08B5A459A0 FOREIGN KEY (news_id) REFERENCES news (id) ON DELETE CASCADE',
);
$queries['fk']['labels_organizations'] = array(
	'FK_9F089F4232C8A3DE' => 'ALTER TABLE labels_organizations ADD CONSTRAINT FK_9F089F4232C8A3DE FOREIGN KEY (organization_id) REFERENCES organizations (id) ON DELETE CASCADE',
);
$queries['fk']['labels_people'] = array(
	'FK_C37D5395217BBB47' => 'ALTER TABLE labels_people ADD CONSTRAINT FK_C37D5395217BBB47 FOREIGN KEY (person_id) REFERENCES people (id) ON DELETE CASCADE',
);
$queries['fk']['labels_tasks'] = array(
	'FK_3557E9528DB60186' => 'ALTER TABLE labels_tasks ADD CONSTRAINT FK_3557E9528DB60186 FOREIGN KEY (task_id) REFERENCES tasks (id) ON DELETE CASCADE',
);
$queries['fk']['labels_tickets'] = array(
	'FK_6C514FB700047D2' => 'ALTER TABLE labels_tickets ADD CONSTRAINT FK_6C514FB700047D2 FOREIGN KEY (ticket_id) REFERENCES tickets (id) ON DELETE CASCADE',
);
$queries['fk']['login_log'] = array(
	'FK_F16D9FFF217BBB47' => 'ALTER TABLE login_log ADD CONSTRAINT FK_F16D9FFF217BBB47 FOREIGN KEY (person_id) REFERENCES people (id) ON DELETE SET NULL',
);
$queries['fk']['news'] = array(
	'FK_1DD3995012469DE2' => 'ALTER TABLE news ADD CONSTRAINT FK_1DD3995012469DE2 FOREIGN KEY (category_id) REFERENCES news_categories (id) ON DELETE CASCADE',
	'FK_1DD39950217BBB47' => 'ALTER TABLE news ADD CONSTRAINT FK_1DD39950217BBB47 FOREIGN KEY (person_id) REFERENCES people (id) ON DELETE SET NULL',
	'FK_1DD3995082F1BAF4' => 'ALTER TABLE news ADD CONSTRAINT FK_1DD3995082F1BAF4 FOREIGN KEY (language_id) REFERENCES languages (id) ON DELETE CASCADE',
);
$queries['fk']['news_categories'] = array(
	'FK_D68C9111727ACA70' => 'ALTER TABLE news_categories ADD CONSTRAINT FK_D68C9111727ACA70 FOREIGN KEY (parent_id) REFERENCES news_categories (id) ON DELETE SET NULL',
);
$queries['fk']['news_category2usergroup'] = array(
	'FK_6336075D12469DE2' => 'ALTER TABLE news_category2usergroup ADD CONSTRAINT FK_6336075D12469DE2 FOREIGN KEY (category_id) REFERENCES news_categories (id) ON DELETE CASCADE',
	'FK_6336075DD2112630' => 'ALTER TABLE news_category2usergroup ADD CONSTRAINT FK_6336075DD2112630 FOREIGN KEY (usergroup_id) REFERENCES usergroups (id) ON DELETE CASCADE',
);
$queries['fk']['news_comments'] = array(
	'FK_16A0357BB5A459A0' => 'ALTER TABLE news_comments ADD CONSTRAINT FK_16A0357BB5A459A0 FOREIGN KEY (news_id) REFERENCES news (id) ON DELETE CASCADE',
	'FK_16A0357B217BBB47' => 'ALTER TABLE news_comments ADD CONSTRAINT FK_16A0357B217BBB47 FOREIGN KEY (person_id) REFERENCES people (id) ON DELETE SET NULL',
	'FK_16A0357B70BEE6D' => 'ALTER TABLE news_comments ADD CONSTRAINT FK_16A0357B70BEE6D FOREIGN KEY (visitor_id) REFERENCES visitors (id) ON DELETE SET NULL',
);
$queries['fk']['news_revisions'] = array(
	'FK_95947D44B5A459A0' => 'ALTER TABLE news_revisions ADD CONSTRAINT FK_95947D44B5A459A0 FOREIGN KEY (news_id) REFERENCES news (id) ON DELETE CASCADE',
	'FK_95947D44217BBB47' => 'ALTER TABLE news_revisions ADD CONSTRAINT FK_95947D44217BBB47 FOREIGN KEY (person_id) REFERENCES people (id) ON DELETE SET NULL',
);
$queries['fk']['object_lang'] = array(
	'FK_AC1CB87182F1BAF4' => 'ALTER TABLE object_lang ADD CONSTRAINT FK_AC1CB87182F1BAF4 FOREIGN KEY (language_id) REFERENCES languages (id) ON DELETE CASCADE',
);
$queries['fk']['organizations'] = array(
	'FK_427C1C7FF0187A77' => 'ALTER TABLE organizations ADD CONSTRAINT FK_427C1C7FF0187A77 FOREIGN KEY (picture_blob_id) REFERENCES blobs (id) ON DELETE SET NULL',
);
$queries['fk']['organization2usergroups'] = array(
	'FK_EA8C676432C8A3DE' => 'ALTER TABLE organization2usergroups ADD CONSTRAINT FK_EA8C676432C8A3DE FOREIGN KEY (organization_id) REFERENCES organizations (id) ON DELETE CASCADE',
	'FK_EA8C6764D2112630' => 'ALTER TABLE organization2usergroups ADD CONSTRAINT FK_EA8C6764D2112630 FOREIGN KEY (usergroup_id) REFERENCES usergroups (id) ON DELETE CASCADE',
);
$queries['fk']['organizations_auto_cc'] = array(
	'FK_864B966432C8A3DE' => 'ALTER TABLE organizations_auto_cc ADD CONSTRAINT FK_864B966432C8A3DE FOREIGN KEY (organization_id) REFERENCES organizations (id) ON DELETE CASCADE',
	'FK_864B9664217BBB47' => 'ALTER TABLE organizations_auto_cc ADD CONSTRAINT FK_864B9664217BBB47 FOREIGN KEY (person_id) REFERENCES people (id) ON DELETE CASCADE',
);
$queries['fk']['organizations_contact_data'] = array(
	'FK_25B60D5032C8A3DE' => 'ALTER TABLE organizations_contact_data ADD CONSTRAINT FK_25B60D5032C8A3DE FOREIGN KEY (organization_id) REFERENCES organizations (id) ON DELETE CASCADE',
);
$queries['fk']['organization_email_domains'] = array(
	'FK_2CCB20C232C8A3DE' => 'ALTER TABLE organization_email_domains ADD CONSTRAINT FK_2CCB20C232C8A3DE FOREIGN KEY (organization_id) REFERENCES organizations (id) ON DELETE CASCADE',
);
$queries['fk']['organization_notes'] = array(
	'FK_8F9C404B32C8A3DE' => 'ALTER TABLE organization_notes ADD CONSTRAINT FK_8F9C404B32C8A3DE FOREIGN KEY (organization_id) REFERENCES organizations (id) ON DELETE CASCADE',
	'FK_8F9C404B3414710B' => 'ALTER TABLE organization_notes ADD CONSTRAINT FK_8F9C404B3414710B FOREIGN KEY (agent_id) REFERENCES people (id) ON DELETE SET NULL',
);
$queries['fk']['organizations_twitter_users'] = array(
	'FK_268948132C8A3DE' => 'ALTER TABLE organizations_twitter_users ADD CONSTRAINT FK_268948132C8A3DE FOREIGN KEY (organization_id) REFERENCES organizations (id) ON DELETE CASCADE',
);
$queries['fk']['permissions'] = array(
	'FK_2DEDCC6FD2112630' => 'ALTER TABLE permissions ADD CONSTRAINT FK_2DEDCC6FD2112630 FOREIGN KEY (usergroup_id) REFERENCES usergroups (id) ON DELETE CASCADE',
	'FK_2DEDCC6F217BBB47' => 'ALTER TABLE permissions ADD CONSTRAINT FK_2DEDCC6F217BBB47 FOREIGN KEY (person_id) REFERENCES people (id) ON DELETE CASCADE',
);
$queries['fk']['people'] = array(
	'FK_28166A26F0187A77' => 'ALTER TABLE people ADD CONSTRAINT FK_28166A26F0187A77 FOREIGN KEY (picture_blob_id) REFERENCES blobs (id) ON DELETE SET NULL',
	'FK_28166A2682F1BAF4' => 'ALTER TABLE people ADD CONSTRAINT FK_28166A2682F1BAF4 FOREIGN KEY (language_id) REFERENCES languages (id) ON DELETE SET NULL',
	'FK_28166A2632C8A3DE' => 'ALTER TABLE people ADD CONSTRAINT FK_28166A2632C8A3DE FOREIGN KEY (organization_id) REFERENCES organizations (id) ON DELETE SET NULL',
	'FK_28166A26894DAC38' => 'ALTER TABLE people ADD CONSTRAINT FK_28166A26894DAC38 FOREIGN KEY (primary_email_id) REFERENCES people_emails (id) ON DELETE SET NULL',
);
$queries['fk']['person2usergroups'] = array(
	'FK_356C969E217BBB47' => 'ALTER TABLE person2usergroups ADD CONSTRAINT FK_356C969E217BBB47 FOREIGN KEY (person_id) REFERENCES people (id) ON DELETE CASCADE',
	'FK_356C969ED2112630' => 'ALTER TABLE person2usergroups ADD CONSTRAINT FK_356C969ED2112630 FOREIGN KEY (usergroup_id) REFERENCES usergroups (id) ON DELETE CASCADE',
);
$queries['fk']['person_activity'] = array(
	'FK_3832AC6D217BBB47' => 'ALTER TABLE person_activity ADD CONSTRAINT FK_3832AC6D217BBB47 FOREIGN KEY (person_id) REFERENCES people (id) ON DELETE CASCADE',
);
$queries['fk']['people_contact_data'] = array(
	'FK_14604ED8217BBB47' => 'ALTER TABLE people_contact_data ADD CONSTRAINT FK_14604ED8217BBB47 FOREIGN KEY (person_id) REFERENCES people (id) ON DELETE CASCADE',
);
$queries['fk']['people_emails'] = array(
	'FK_3A96CAB8217BBB47' => 'ALTER TABLE people_emails ADD CONSTRAINT FK_3A96CAB8217BBB47 FOREIGN KEY (person_id) REFERENCES people (id) ON DELETE CASCADE',
);
$queries['fk']['people_emails_validating'] = array(
	'FK_3277575C217BBB47' => 'ALTER TABLE people_emails_validating ADD CONSTRAINT FK_3277575C217BBB47 FOREIGN KEY (person_id) REFERENCES people (id) ON DELETE CASCADE',
);
$queries['fk']['people_notes'] = array(
	'FK_CA78DCCC217BBB47' => 'ALTER TABLE people_notes ADD CONSTRAINT FK_CA78DCCC217BBB47 FOREIGN KEY (person_id) REFERENCES people (id) ON DELETE CASCADE',
	'FK_CA78DCCC3414710B' => 'ALTER TABLE people_notes ADD CONSTRAINT FK_CA78DCCC3414710B FOREIGN KEY (agent_id) REFERENCES people (id) ON DELETE SET NULL',
);
$queries['fk']['people_prefs'] = array(
	'FK_8112E0E9217BBB47' => 'ALTER TABLE people_prefs ADD CONSTRAINT FK_8112E0E9217BBB47 FOREIGN KEY (person_id) REFERENCES people (id) ON DELETE CASCADE',
);
$queries['fk']['people_twitter_users'] = array(
	'FK_E13A49D0217BBB47' => 'ALTER TABLE people_twitter_users ADD CONSTRAINT FK_E13A49D0217BBB47 FOREIGN KEY (person_id) REFERENCES people (id) ON DELETE CASCADE',
);
$queries['fk']['person_usersource_assoc'] = array(
	'FK_72215949217BBB47' => 'ALTER TABLE person_usersource_assoc ADD CONSTRAINT FK_72215949217BBB47 FOREIGN KEY (person_id) REFERENCES people (id) ON DELETE CASCADE',
	'FK_722159495B71BD01' => 'ALTER TABLE person_usersource_assoc ADD CONSTRAINT FK_722159495B71BD01 FOREIGN KEY (usersource_id) REFERENCES usersources (id) ON DELETE CASCADE',
);
$queries['fk']['phrases'] = array(
	'FK_121AC8C682F1BAF4' => 'ALTER TABLE phrases ADD CONSTRAINT FK_121AC8C682F1BAF4 FOREIGN KEY (language_id) REFERENCES languages (id) ON DELETE CASCADE',
);
$queries['fk']['plugin_listeners'] = array(
	'FK_FEEE2572EC942BCF' => 'ALTER TABLE plugin_listeners ADD CONSTRAINT FK_FEEE2572EC942BCF FOREIGN KEY (plugin_id) REFERENCES plugins (id) ON DELETE CASCADE',
);
$queries['fk']['pretickets_content'] = array(
	'FK_E1110A25217BBB47' => 'ALTER TABLE pretickets_content ADD CONSTRAINT FK_E1110A25217BBB47 FOREIGN KEY (person_id) REFERENCES people (id) ON DELETE SET NULL',
	'FK_E1110A2570BEE6D' => 'ALTER TABLE pretickets_content ADD CONSTRAINT FK_E1110A2570BEE6D FOREIGN KEY (visitor_id) REFERENCES visitors (id) ON DELETE SET NULL',
);
$queries['fk']['products'] = array(
	'FK_B3BA5A5A727ACA70' => 'ALTER TABLE products ADD CONSTRAINT FK_B3BA5A5A727ACA70 FOREIGN KEY (parent_id) REFERENCES products (id)',
);
$queries['fk']['ratings'] = array(
	'FK_CEB607C9546A72F3' => 'ALTER TABLE ratings ADD CONSTRAINT FK_CEB607C9546A72F3 FOREIGN KEY (searchlog_id) REFERENCES searchlog (id) ON DELETE SET NULL',
	'FK_CEB607C9217BBB47' => 'ALTER TABLE ratings ADD CONSTRAINT FK_CEB607C9217BBB47 FOREIGN KEY (person_id) REFERENCES people (id) ON DELETE SET NULL',
	'FK_CEB607C970BEE6D' => 'ALTER TABLE ratings ADD CONSTRAINT FK_CEB607C970BEE6D FOREIGN KEY (visitor_id) REFERENCES visitors (id) ON DELETE SET NULL',
);
$queries['fk']['report_builder'] = array(
	'FK_B6BED249727ACA70' => 'ALTER TABLE report_builder ADD CONSTRAINT FK_B6BED249727ACA70 FOREIGN KEY (parent_id) REFERENCES report_builder (id) ON DELETE SET NULL',
);
$queries['fk']['report_builder_favorite'] = array(
	'FK_CCD5CB1186DD4ADF' => 'ALTER TABLE report_builder_favorite ADD CONSTRAINT FK_CCD5CB1186DD4ADF FOREIGN KEY (report_builder_id) REFERENCES report_builder (id) ON DELETE CASCADE',
	'FK_CCD5CB11217BBB47' => 'ALTER TABLE report_builder_favorite ADD CONSTRAINT FK_CCD5CB11217BBB47 FOREIGN KEY (person_id) REFERENCES people (id) ON DELETE CASCADE',
);
$queries['fk']['result_cache'] = array(
	'FK_D0B33C6B217BBB47' => 'ALTER TABLE result_cache ADD CONSTRAINT FK_D0B33C6B217BBB47 FOREIGN KEY (person_id) REFERENCES people (id) ON DELETE CASCADE',
);
$queries['fk']['searchlog'] = array(
	'FK_8C79CD5C217BBB47' => 'ALTER TABLE searchlog ADD CONSTRAINT FK_8C79CD5C217BBB47 FOREIGN KEY (person_id) REFERENCES people (id) ON DELETE SET NULL',
	'FK_8C79CD5C70BEE6D' => 'ALTER TABLE searchlog ADD CONSTRAINT FK_8C79CD5C70BEE6D FOREIGN KEY (visitor_id) REFERENCES visitors (id) ON DELETE SET NULL',
);
$queries['fk']['sendmail_logs'] = array(
	'FK_D9E8157F700047D2' => 'ALTER TABLE sendmail_logs ADD CONSTRAINT FK_D9E8157F700047D2 FOREIGN KEY (ticket_id) REFERENCES tickets (id) ON DELETE SET NULL',
	'FK_D9E8157FC5E9817D' => 'ALTER TABLE sendmail_logs ADD CONSTRAINT FK_D9E8157FC5E9817D FOREIGN KEY (ticket_message_id) REFERENCES tickets_messages (id) ON DELETE SET NULL',
	'FK_D9E8157F217BBB47' => 'ALTER TABLE sendmail_logs ADD CONSTRAINT FK_D9E8157F217BBB47 FOREIGN KEY (person_id) REFERENCES people (id) ON DELETE SET NULL',
);
$queries['fk']['sendmail_queue'] = array(
	'FK_DDB369C2ED3E8EA5' => 'ALTER TABLE sendmail_queue ADD CONSTRAINT FK_DDB369C2ED3E8EA5 FOREIGN KEY (blob_id) REFERENCES blobs (id) ON DELETE SET NULL',
);
$queries['fk']['sessions'] = array(
	'FK_9A609D13217BBB47' => 'ALTER TABLE sessions ADD CONSTRAINT FK_9A609D13217BBB47 FOREIGN KEY (person_id) REFERENCES people (id) ON DELETE CASCADE',
	'FK_9A609D1370BEE6D' => 'ALTER TABLE sessions ADD CONSTRAINT FK_9A609D1370BEE6D FOREIGN KEY (visitor_id) REFERENCES visitors (id) ON DELETE SET NULL',
);
$queries['fk']['slas'] = array(
	'FK_ACE9984A91D0B882' => 'ALTER TABLE slas ADD CONSTRAINT FK_ACE9984A91D0B882 FOREIGN KEY (warning_trigger_id) REFERENCES ticket_triggers (id) ON DELETE SET NULL',
	'FK_ACE9984A55EA90D4' => 'ALTER TABLE slas ADD CONSTRAINT FK_ACE9984A55EA90D4 FOREIGN KEY (fail_trigger_id) REFERENCES ticket_triggers (id) ON DELETE SET NULL',
	'FK_ACE9984A13CC0145' => 'ALTER TABLE slas ADD CONSTRAINT FK_ACE9984A13CC0145 FOREIGN KEY (apply_priority_id) REFERENCES ticket_priorities (id) ON DELETE SET NULL',
	'FK_ACE9984AED1A7B28' => 'ALTER TABLE slas ADD CONSTRAINT FK_ACE9984AED1A7B28 FOREIGN KEY (apply_trigger_id) REFERENCES ticket_triggers (id) ON DELETE SET NULL',
);
$queries['fk']['sla_people'] = array(
	'FK_14ABD6A37A2CC8C4' => 'ALTER TABLE sla_people ADD CONSTRAINT FK_14ABD6A37A2CC8C4 FOREIGN KEY (sla_id) REFERENCES slas (id) ON DELETE CASCADE',
	'FK_14ABD6A3217BBB47' => 'ALTER TABLE sla_people ADD CONSTRAINT FK_14ABD6A3217BBB47 FOREIGN KEY (person_id) REFERENCES people (id) ON DELETE CASCADE',
);
$queries['fk']['sla_organizations'] = array(
	'FK_A7F081987A2CC8C4' => 'ALTER TABLE sla_organizations ADD CONSTRAINT FK_A7F081987A2CC8C4 FOREIGN KEY (sla_id) REFERENCES slas (id) ON DELETE CASCADE',
	'FK_A7F0819832C8A3DE' => 'ALTER TABLE sla_organizations ADD CONSTRAINT FK_A7F0819832C8A3DE FOREIGN KEY (organization_id) REFERENCES organizations (id) ON DELETE CASCADE',
);
$queries['fk']['styles'] = array(
	'FK_B65AFAF5727ACA70' => 'ALTER TABLE styles ADD CONSTRAINT FK_B65AFAF5727ACA70 FOREIGN KEY (parent_id) REFERENCES styles (id) ON DELETE CASCADE',
	'FK_B65AFAF5D91464D5' => 'ALTER TABLE styles ADD CONSTRAINT FK_B65AFAF5D91464D5 FOREIGN KEY (logo_blob_id) REFERENCES blobs (id) ON DELETE SET NULL',
	'FK_B65AFAF58AB530EF' => 'ALTER TABLE styles ADD CONSTRAINT FK_B65AFAF58AB530EF FOREIGN KEY (css_blob_id) REFERENCES blobs (id) ON DELETE SET NULL',
	'FK_B65AFAF5FEED6A62' => 'ALTER TABLE styles ADD CONSTRAINT FK_B65AFAF5FEED6A62 FOREIGN KEY (css_blob_rtl_id) REFERENCES blobs (id) ON DELETE SET NULL',
);
$queries['fk']['tasks'] = array(
	'FK_50586597217BBB47' => 'ALTER TABLE tasks ADD CONSTRAINT FK_50586597217BBB47 FOREIGN KEY (person_id) REFERENCES people (id) ON DELETE SET NULL',
	'FK_5058659749197702' => 'ALTER TABLE tasks ADD CONSTRAINT FK_5058659749197702 FOREIGN KEY (assigned_agent_id) REFERENCES people (id) ON DELETE SET NULL',
	'FK_50586597410D1341' => 'ALTER TABLE tasks ADD CONSTRAINT FK_50586597410D1341 FOREIGN KEY (assigned_agent_team_id) REFERENCES agent_teams (id) ON DELETE CASCADE',
);
$queries['fk']['task_associations'] = array(
	'FK_41B0E09C8DB60186' => 'ALTER TABLE task_associations ADD CONSTRAINT FK_41B0E09C8DB60186 FOREIGN KEY (task_id) REFERENCES tasks (id) ON DELETE CASCADE',
	'FK_41B0E09C217BBB47' => 'ALTER TABLE task_associations ADD CONSTRAINT FK_41B0E09C217BBB47 FOREIGN KEY (person_id) REFERENCES people (id) ON DELETE CASCADE',
	'FK_41B0E09C700047D2' => 'ALTER TABLE task_associations ADD CONSTRAINT FK_41B0E09C700047D2 FOREIGN KEY (ticket_id) REFERENCES tickets (id) ON DELETE CASCADE',
	'FK_41B0E09C32C8A3DE' => 'ALTER TABLE task_associations ADD CONSTRAINT FK_41B0E09C32C8A3DE FOREIGN KEY (organization_id) REFERENCES organizations (id) ON DELETE CASCADE',
);
$queries['fk']['task_comments'] = array(
	'FK_1F5E7C668DB60186' => 'ALTER TABLE task_comments ADD CONSTRAINT FK_1F5E7C668DB60186 FOREIGN KEY (task_id) REFERENCES tasks (id) ON DELETE CASCADE',
	'FK_1F5E7C66217BBB47' => 'ALTER TABLE task_comments ADD CONSTRAINT FK_1F5E7C66217BBB47 FOREIGN KEY (person_id) REFERENCES people (id) ON DELETE SET NULL',
);
$queries['fk']['task_reminder_logs'] = array(
	'FK_A264D7248DB60186' => 'ALTER TABLE task_reminder_logs ADD CONSTRAINT FK_A264D7248DB60186 FOREIGN KEY (task_id) REFERENCES tasks (id) ON DELETE CASCADE',
	'FK_A264D724217BBB47' => 'ALTER TABLE task_reminder_logs ADD CONSTRAINT FK_A264D724217BBB47 FOREIGN KEY (person_id) REFERENCES people (id) ON DELETE CASCADE',
);
$queries['fk']['templates'] = array(
	'FK_6F287D8EBACD6074' => 'ALTER TABLE templates ADD CONSTRAINT FK_6F287D8EBACD6074 FOREIGN KEY (style_id) REFERENCES styles (id) ON DELETE CASCADE',
);
$queries['fk']['text_snippets'] = array(
	'FK_5B6379CE217BBB47' => 'ALTER TABLE text_snippets ADD CONSTRAINT FK_5B6379CE217BBB47 FOREIGN KEY (person_id) REFERENCES people (id) ON DELETE SET NULL',
	'FK_5B6379CE12469DE2' => 'ALTER TABLE text_snippets ADD CONSTRAINT FK_5B6379CE12469DE2 FOREIGN KEY (category_id) REFERENCES text_snippet_categories (id) ON DELETE CASCADE',
);
$queries['fk']['text_snippet_categories'] = array(
	'FK_F3B50AF1217BBB47' => 'ALTER TABLE text_snippet_categories ADD CONSTRAINT FK_F3B50AF1217BBB47 FOREIGN KEY (person_id) REFERENCES people (id) ON DELETE SET NULL',
);
$queries['fk']['tickets'] = array(
	'FK_54469DF4814B683C' => 'ALTER TABLE tickets ADD CONSTRAINT FK_54469DF4814B683C FOREIGN KEY (parent_ticket_id) REFERENCES tickets (id) ON DELETE SET NULL',
	'FK_54469DF482F1BAF4' => 'ALTER TABLE tickets ADD CONSTRAINT FK_54469DF482F1BAF4 FOREIGN KEY (language_id) REFERENCES languages (id) ON DELETE SET NULL',
	'FK_54469DF4AE80F5DF' => 'ALTER TABLE tickets ADD CONSTRAINT FK_54469DF4AE80F5DF FOREIGN KEY (department_id) REFERENCES departments (id) ON DELETE SET NULL',
	'FK_54469DF412469DE2' => 'ALTER TABLE tickets ADD CONSTRAINT FK_54469DF412469DE2 FOREIGN KEY (category_id) REFERENCES ticket_categories (id) ON DELETE SET NULL',
	'FK_54469DF4497B19F9' => 'ALTER TABLE tickets ADD CONSTRAINT FK_54469DF4497B19F9 FOREIGN KEY (priority_id) REFERENCES ticket_priorities (id) ON DELETE SET NULL',
	'FK_54469DF42C7C2CBA' => 'ALTER TABLE tickets ADD CONSTRAINT FK_54469DF42C7C2CBA FOREIGN KEY (workflow_id) REFERENCES ticket_workflows (id) ON DELETE SET NULL',
	'FK_54469DF44584665A' => 'ALTER TABLE tickets ADD CONSTRAINT FK_54469DF44584665A FOREIGN KEY (product_id) REFERENCES products (id) ON DELETE SET NULL',
	'FK_54469DF4217BBB47' => 'ALTER TABLE tickets ADD CONSTRAINT FK_54469DF4217BBB47 FOREIGN KEY (person_id) REFERENCES people (id) ON DELETE CASCADE',
	'FK_54469DF43C7464FE' => 'ALTER TABLE tickets ADD CONSTRAINT FK_54469DF43C7464FE FOREIGN KEY (person_email_id) REFERENCES people_emails (id) ON DELETE SET NULL',
	'FK_54469DF4581A624E' => 'ALTER TABLE tickets ADD CONSTRAINT FK_54469DF4581A624E FOREIGN KEY (person_email_validating_id) REFERENCES people_emails_validating (id) ON DELETE SET NULL',
	'FK_54469DF43414710B' => 'ALTER TABLE tickets ADD CONSTRAINT FK_54469DF43414710B FOREIGN KEY (agent_id) REFERENCES people (id) ON DELETE SET NULL',
	'FK_54469DF4FB3FBA04' => 'ALTER TABLE tickets ADD CONSTRAINT FK_54469DF4FB3FBA04 FOREIGN KEY (agent_team_id) REFERENCES agent_teams (id) ON DELETE SET NULL',
	'FK_54469DF432C8A3DE' => 'ALTER TABLE tickets ADD CONSTRAINT FK_54469DF432C8A3DE FOREIGN KEY (organization_id) REFERENCES organizations (id) ON DELETE SET NULL',
	'FK_54469DF471D249B2' => 'ALTER TABLE tickets ADD CONSTRAINT FK_54469DF471D249B2 FOREIGN KEY (linked_chat_id) REFERENCES chat_conversations (id) ON DELETE SET NULL',
	'FK_54469DF4FBCC7CDF' => 'ALTER TABLE tickets ADD CONSTRAINT FK_54469DF4FBCC7CDF FOREIGN KEY (email_gateway_id) REFERENCES email_gateways (id) ON DELETE SET NULL',
	'FK_54469DF4F2598614' => 'ALTER TABLE tickets ADD CONSTRAINT FK_54469DF4F2598614 FOREIGN KEY (email_gateway_address_id) REFERENCES email_gateway_addresses (id) ON DELETE SET NULL',
	'FK_54469DF4428359E2' => 'ALTER TABLE tickets ADD CONSTRAINT FK_54469DF4428359E2 FOREIGN KEY (locked_by_agent) REFERENCES people (id) ON DELETE SET NULL',
);
$queries['fk']['ticket_access_codes'] = array(
	'FK_CCEE41B5700047D2' => 'ALTER TABLE ticket_access_codes ADD CONSTRAINT FK_CCEE41B5700047D2 FOREIGN KEY (ticket_id) REFERENCES tickets (id) ON DELETE CASCADE',
	'FK_CCEE41B5217BBB47' => 'ALTER TABLE ticket_access_codes ADD CONSTRAINT FK_CCEE41B5217BBB47 FOREIGN KEY (person_id) REFERENCES people (id) ON DELETE CASCADE',
);
$queries['fk']['tickets_attachments'] = array(
	'FK_F06B468D700047D2' => 'ALTER TABLE tickets_attachments ADD CONSTRAINT FK_F06B468D700047D2 FOREIGN KEY (ticket_id) REFERENCES tickets (id) ON DELETE CASCADE',
	'FK_F06B468D217BBB47' => 'ALTER TABLE tickets_attachments ADD CONSTRAINT FK_F06B468D217BBB47 FOREIGN KEY (person_id) REFERENCES people (id) ON DELETE SET NULL',
	'FK_F06B468DED3E8EA5' => 'ALTER TABLE tickets_attachments ADD CONSTRAINT FK_F06B468DED3E8EA5 FOREIGN KEY (blob_id) REFERENCES blobs (id) ON DELETE CASCADE',
	'FK_F06B468D537A1329' => 'ALTER TABLE tickets_attachments ADD CONSTRAINT FK_F06B468D537A1329 FOREIGN KEY (message_id) REFERENCES tickets_messages (id) ON DELETE CASCADE',
);
$queries['fk']['ticket_categories'] = array(
	'FK_AC60D43C727ACA70' => 'ALTER TABLE ticket_categories ADD CONSTRAINT FK_AC60D43C727ACA70 FOREIGN KEY (parent_id) REFERENCES ticket_categories (id) ON DELETE CASCADE',
);
$queries['fk']['ticket_changetracker_logs'] = array(
	'FK_F2205216700047D2' => 'ALTER TABLE ticket_changetracker_logs ADD CONSTRAINT FK_F2205216700047D2 FOREIGN KEY (ticket_id) REFERENCES tickets (id) ON DELETE CASCADE',
);
$queries['fk']['ticket_charges'] = array(
	'FK_36230948700047D2' => 'ALTER TABLE ticket_charges ADD CONSTRAINT FK_36230948700047D2 FOREIGN KEY (ticket_id) REFERENCES tickets (id) ON DELETE CASCADE',
	'FK_36230948217BBB47' => 'ALTER TABLE ticket_charges ADD CONSTRAINT FK_36230948217BBB47 FOREIGN KEY (person_id) REFERENCES people (id) ON DELETE CASCADE',
	'FK_3623094832C8A3DE' => 'ALTER TABLE ticket_charges ADD CONSTRAINT FK_3623094832C8A3DE FOREIGN KEY (organization_id) REFERENCES organizations (id) ON DELETE CASCADE',
	'FK_362309483414710B' => 'ALTER TABLE ticket_charges ADD CONSTRAINT FK_362309483414710B FOREIGN KEY (agent_id) REFERENCES people (id) ON DELETE SET NULL',
);
$queries['fk']['tickets_deleted'] = array(
	'FK_7EDF2278B5BE2AA2' => 'ALTER TABLE tickets_deleted ADD CONSTRAINT FK_7EDF2278B5BE2AA2 FOREIGN KEY (by_person_id) REFERENCES people (id) ON DELETE SET NULL',
);
$queries['fk']['ticket_feedback'] = array(
	'FK_5740B8D9700047D2' => 'ALTER TABLE ticket_feedback ADD CONSTRAINT FK_5740B8D9700047D2 FOREIGN KEY (ticket_id) REFERENCES tickets (id) ON DELETE CASCADE',
	'FK_5740B8D9537A1329' => 'ALTER TABLE ticket_feedback ADD CONSTRAINT FK_5740B8D9537A1329 FOREIGN KEY (message_id) REFERENCES tickets_messages (id) ON DELETE CASCADE',
	'FK_5740B8D9217BBB47' => 'ALTER TABLE ticket_feedback ADD CONSTRAINT FK_5740B8D9217BBB47 FOREIGN KEY (person_id) REFERENCES people (id) ON DELETE CASCADE',
);
$queries['fk']['ticket_filters'] = array(
	'FK_74BB3EDF217BBB47' => 'ALTER TABLE ticket_filters ADD CONSTRAINT FK_74BB3EDF217BBB47 FOREIGN KEY (person_id) REFERENCES people (id) ON DELETE CASCADE',
	'FK_74BB3EDFFB3FBA04' => 'ALTER TABLE ticket_filters ADD CONSTRAINT FK_74BB3EDFFB3FBA04 FOREIGN KEY (agent_team_id) REFERENCES agent_teams (id) ON DELETE CASCADE',
);
$queries['fk']['ticket_filters_perms'] = array(
	'FK_93E8D427D395B25E' => 'ALTER TABLE ticket_filters_perms ADD CONSTRAINT FK_93E8D427D395B25E FOREIGN KEY (filter_id) REFERENCES ticket_filters (id) ON DELETE CASCADE',
);
$queries['fk']['ticket_filter_subscriptions'] = array(
	'FK_13669D98D395B25E' => 'ALTER TABLE ticket_filter_subscriptions ADD CONSTRAINT FK_13669D98D395B25E FOREIGN KEY (filter_id) REFERENCES ticket_filters (id) ON DELETE CASCADE',
	'FK_13669D98217BBB47' => 'ALTER TABLE ticket_filter_subscriptions ADD CONSTRAINT FK_13669D98217BBB47 FOREIGN KEY (person_id) REFERENCES people (id) ON DELETE CASCADE',
);
$queries['fk']['tickets_logs'] = array(
	'FK_F5F41081727ACA70' => 'ALTER TABLE tickets_logs ADD CONSTRAINT FK_F5F41081727ACA70 FOREIGN KEY (parent_id) REFERENCES tickets_logs (id) ON DELETE CASCADE',
	'FK_F5F41081700047D2' => 'ALTER TABLE tickets_logs ADD CONSTRAINT FK_F5F41081700047D2 FOREIGN KEY (ticket_id) REFERENCES tickets (id) ON DELETE CASCADE',
	'FK_F5F41081217BBB47' => 'ALTER TABLE tickets_logs ADD CONSTRAINT FK_F5F41081217BBB47 FOREIGN KEY (person_id) REFERENCES people (id) ON DELETE SET NULL',
	'FK_F5F410817A2CC8C4' => 'ALTER TABLE tickets_logs ADD CONSTRAINT FK_F5F410817A2CC8C4 FOREIGN KEY (sla_id) REFERENCES slas (id) ON DELETE SET NULL',
);
$queries['fk']['ticket_macros'] = array(
	'FK_8E373A2C217BBB47' => 'ALTER TABLE ticket_macros ADD CONSTRAINT FK_8E373A2C217BBB47 FOREIGN KEY (person_id) REFERENCES people (id) ON DELETE SET NULL',
);
$queries['fk']['ticket_macros_perms'] = array(
	'FK_EAB2E6D5F43A187E' => 'ALTER TABLE ticket_macros_perms ADD CONSTRAINT FK_EAB2E6D5F43A187E FOREIGN KEY (macro_id) REFERENCES ticket_macros (id) ON DELETE CASCADE',
);
$queries['fk']['tickets_messages'] = array(
	'FK_3A9962E2700047D2' => 'ALTER TABLE tickets_messages ADD CONSTRAINT FK_3A9962E2700047D2 FOREIGN KEY (ticket_id) REFERENCES tickets (id) ON DELETE CASCADE',
	'FK_3A9962E2217BBB47' => 'ALTER TABLE tickets_messages ADD CONSTRAINT FK_3A9962E2217BBB47 FOREIGN KEY (person_id) REFERENCES people (id) ON DELETE SET NULL',
	'FK_3A9962E29A37834A' => 'ALTER TABLE tickets_messages ADD CONSTRAINT FK_3A9962E29A37834A FOREIGN KEY (email_source_id) REFERENCES email_sources (id) ON DELETE SET NULL',
	'FK_3A9962E2251FB291' => 'ALTER TABLE tickets_messages ADD CONSTRAINT FK_3A9962E2251FB291 FOREIGN KEY (message_translated_id) REFERENCES tickets_messages_translated (id) ON DELETE SET NULL',
	'FK_3A9962E270BEE6D' => 'ALTER TABLE tickets_messages ADD CONSTRAINT FK_3A9962E270BEE6D FOREIGN KEY (visitor_id) REFERENCES visitors (id) ON DELETE SET NULL',
);
$queries['fk']['tickets_messages_raw'] = array(
	'FK_672BCB3537A1329' => 'ALTER TABLE tickets_messages_raw ADD CONSTRAINT FK_672BCB3537A1329 FOREIGN KEY (message_id) REFERENCES tickets_messages (id) ON DELETE CASCADE',
);
$queries['fk']['ticket_message_templates'] = array(
	'FK_8C28E2ECAE80F5DF' => 'ALTER TABLE ticket_message_templates ADD CONSTRAINT FK_8C28E2ECAE80F5DF FOREIGN KEY (department_id) REFERENCES departments (id) ON DELETE SET NULL',
);
$queries['fk']['tickets_messages_translated'] = array(
	'FK_EDCD3BB3700047D2' => 'ALTER TABLE tickets_messages_translated ADD CONSTRAINT FK_EDCD3BB3700047D2 FOREIGN KEY (ticket_id) REFERENCES tickets (id) ON DELETE CASCADE',
	'FK_EDCD3BB3537A1329' => 'ALTER TABLE tickets_messages_translated ADD CONSTRAINT FK_EDCD3BB3537A1329 FOREIGN KEY (message_id) REFERENCES tickets_messages (id) ON DELETE CASCADE',
);
$queries['fk']['ticket_page_display'] = array(
	'FK_3667659DAE80F5DF' => 'ALTER TABLE ticket_page_display ADD CONSTRAINT FK_3667659DAE80F5DF FOREIGN KEY (department_id) REFERENCES departments (id) ON DELETE CASCADE',
);
$queries['fk']['tickets_participants'] = array(
	'FK_8D675752700047D2' => 'ALTER TABLE tickets_participants ADD CONSTRAINT FK_8D675752700047D2 FOREIGN KEY (ticket_id) REFERENCES tickets (id) ON DELETE CASCADE',
	'FK_8D675752217BBB47' => 'ALTER TABLE tickets_participants ADD CONSTRAINT FK_8D675752217BBB47 FOREIGN KEY (person_id) REFERENCES people (id) ON DELETE CASCADE',
	'FK_8D675752EFFF2402' => 'ALTER TABLE tickets_participants ADD CONSTRAINT FK_8D675752EFFF2402 FOREIGN KEY (access_code_id) REFERENCES ticket_access_codes (id) ON DELETE CASCADE',
	'FK_8D6757523C7464FE' => 'ALTER TABLE tickets_participants ADD CONSTRAINT FK_8D6757523C7464FE FOREIGN KEY (person_email_id) REFERENCES people_emails (id) ON DELETE SET NULL',
);
$queries['fk']['ticket_slas'] = array(
	'FK_9E328D72700047D2' => 'ALTER TABLE ticket_slas ADD CONSTRAINT FK_9E328D72700047D2 FOREIGN KEY (ticket_id) REFERENCES tickets (id) ON DELETE CASCADE',
	'FK_9E328D727A2CC8C4' => 'ALTER TABLE ticket_slas ADD CONSTRAINT FK_9E328D727A2CC8C4 FOREIGN KEY (sla_id) REFERENCES slas (id) ON DELETE CASCADE',
);
$queries['fk']['ticket_trigger_plugin_actions'] = array(
	'FK_1D905890EC942BCF' => 'ALTER TABLE ticket_trigger_plugin_actions ADD CONSTRAINT FK_1D905890EC942BCF FOREIGN KEY (plugin_id) REFERENCES plugins (id) ON DELETE CASCADE',
);
$queries['fk']['twitter_accounts'] = array(
	'FK_D4051D30A76ED395' => 'ALTER TABLE twitter_accounts ADD CONSTRAINT FK_D4051D30A76ED395 FOREIGN KEY (user_id) REFERENCES twitter_users (id) ON DELETE CASCADE',
);
$queries['fk']['twitter_accounts_person'] = array(
	'FK_BB12235C9B6B5FBA' => 'ALTER TABLE twitter_accounts_person ADD CONSTRAINT FK_BB12235C9B6B5FBA FOREIGN KEY (account_id) REFERENCES twitter_accounts (id) ON DELETE CASCADE',
	'FK_BB12235C217BBB47' => 'ALTER TABLE twitter_accounts_person ADD CONSTRAINT FK_BB12235C217BBB47 FOREIGN KEY (person_id) REFERENCES people (id) ON DELETE CASCADE',
);
$queries['fk']['twitter_accounts_followers'] = array(
	'FK_EB8452969B6B5FBA' => 'ALTER TABLE twitter_accounts_followers ADD CONSTRAINT FK_EB8452969B6B5FBA FOREIGN KEY (account_id) REFERENCES twitter_accounts (id) ON DELETE CASCADE',
	'FK_EB845296A76ED395' => 'ALTER TABLE twitter_accounts_followers ADD CONSTRAINT FK_EB845296A76ED395 FOREIGN KEY (user_id) REFERENCES twitter_users (id) ON DELETE CASCADE',
);
$queries['fk']['twitter_accounts_friends'] = array(
	'FK_FADA774D9B6B5FBA' => 'ALTER TABLE twitter_accounts_friends ADD CONSTRAINT FK_FADA774D9B6B5FBA FOREIGN KEY (account_id) REFERENCES twitter_accounts (id) ON DELETE CASCADE',
	'FK_FADA774DA76ED395' => 'ALTER TABLE twitter_accounts_friends ADD CONSTRAINT FK_FADA774DA76ED395 FOREIGN KEY (user_id) REFERENCES twitter_users (id) ON DELETE CASCADE',
);
$queries['fk']['twitter_accounts_searches'] = array(
	'FK_5CC0E8CF9B6B5FBA' => 'ALTER TABLE twitter_accounts_searches ADD CONSTRAINT FK_5CC0E8CF9B6B5FBA FOREIGN KEY (account_id) REFERENCES twitter_accounts (id) ON DELETE CASCADE',
);
$queries['fk']['twitter_accounts_searches_statuses'] = array(
	'FK_E52AFE3B498DD8E6' => 'ALTER TABLE twitter_accounts_searches_statuses ADD CONSTRAINT FK_E52AFE3B498DD8E6 FOREIGN KEY (account_status_id) REFERENCES twitter_accounts_statuses (id) ON DELETE CASCADE',
	'FK_E52AFE3B650760A9' => 'ALTER TABLE twitter_accounts_searches_statuses ADD CONSTRAINT FK_E52AFE3B650760A9 FOREIGN KEY (search_id) REFERENCES twitter_accounts_searches (id) ON DELETE CASCADE',
);
$queries['fk']['twitter_accounts_statuses'] = array(
	'FK_7728CEC79B6B5FBA' => 'ALTER TABLE twitter_accounts_statuses ADD CONSTRAINT FK_7728CEC79B6B5FBA FOREIGN KEY (account_id) REFERENCES twitter_accounts (id) ON DELETE SET NULL',
	'FK_7728CEC76BF700BD' => 'ALTER TABLE twitter_accounts_statuses ADD CONSTRAINT FK_7728CEC76BF700BD FOREIGN KEY (status_id) REFERENCES twitter_statuses (id) ON DELETE CASCADE',
	'FK_7728CEC73414710B' => 'ALTER TABLE twitter_accounts_statuses ADD CONSTRAINT FK_7728CEC73414710B FOREIGN KEY (agent_id) REFERENCES people (id) ON DELETE SET NULL',
	'FK_7728CEC7FB3FBA04' => 'ALTER TABLE twitter_accounts_statuses ADD CONSTRAINT FK_7728CEC7FB3FBA04 FOREIGN KEY (agent_team_id) REFERENCES agent_teams (id) ON DELETE SET NULL',
	'FK_7728CEC7E3C3016D' => 'ALTER TABLE twitter_accounts_statuses ADD CONSTRAINT FK_7728CEC7E3C3016D FOREIGN KEY (action_agent_id) REFERENCES people (id) ON DELETE SET NULL',
	'FK_7728CEC754E76E81' => 'ALTER TABLE twitter_accounts_statuses ADD CONSTRAINT FK_7728CEC754E76E81 FOREIGN KEY (retweeted_id) REFERENCES twitter_accounts_statuses (id) ON DELETE SET NULL',
	'FK_7728CEC7DD92DAB8' => 'ALTER TABLE twitter_accounts_statuses ADD CONSTRAINT FK_7728CEC7DD92DAB8 FOREIGN KEY (in_reply_to_id) REFERENCES twitter_accounts_statuses (id) ON DELETE SET NULL',
);
$queries['fk']['twitter_accounts_statuses_notes'] = array(
	'FK_E5D3CBA2498DD8E6' => 'ALTER TABLE twitter_accounts_statuses_notes ADD CONSTRAINT FK_E5D3CBA2498DD8E6 FOREIGN KEY (account_status_id) REFERENCES twitter_accounts_statuses (id) ON DELETE CASCADE',
	'FK_E5D3CBA2217BBB47' => 'ALTER TABLE twitter_accounts_statuses_notes ADD CONSTRAINT FK_E5D3CBA2217BBB47 FOREIGN KEY (person_id) REFERENCES people (id) ON DELETE SET NULL',
);
$queries['fk']['twitter_statuses'] = array(
	'FK_553D9D8DA76ED395' => 'ALTER TABLE twitter_statuses ADD CONSTRAINT FK_553D9D8DA76ED395 FOREIGN KEY (user_id) REFERENCES twitter_users (id)',
	'FK_553D9D8D6B347969' => 'ALTER TABLE twitter_statuses ADD CONSTRAINT FK_553D9D8D6B347969 FOREIGN KEY (in_reply_to_status_id) REFERENCES twitter_statuses (id) ON DELETE SET NULL',
	'FK_553D9D8D72A1C5CA' => 'ALTER TABLE twitter_statuses ADD CONSTRAINT FK_553D9D8D72A1C5CA FOREIGN KEY (retweet_id) REFERENCES twitter_statuses (id) ON DELETE CASCADE',
	'FK_553D9D8DD2347268' => 'ALTER TABLE twitter_statuses ADD CONSTRAINT FK_553D9D8DD2347268 FOREIGN KEY (in_reply_to_user_id) REFERENCES twitter_users (id)',
	'FK_553D9D8DE92F8F78' => 'ALTER TABLE twitter_statuses ADD CONSTRAINT FK_553D9D8DE92F8F78 FOREIGN KEY (recipient_id) REFERENCES twitter_users (id)',
);
$queries['fk']['twitter_statuses_long'] = array(
	'FK_8B914BFB6BF700BD' => 'ALTER TABLE twitter_statuses_long ADD CONSTRAINT FK_8B914BFB6BF700BD FOREIGN KEY (status_id) REFERENCES twitter_statuses (id) ON DELETE CASCADE',
	'FK_8B914BFB9B5BB4B8' => 'ALTER TABLE twitter_statuses_long ADD CONSTRAINT FK_8B914BFB9B5BB4B8 FOREIGN KEY (for_user_id) REFERENCES twitter_users (id) ON DELETE CASCADE',
);
$queries['fk']['twitter_statuses_mentions'] = array(
	'FK_66912DD16BF700BD' => 'ALTER TABLE twitter_statuses_mentions ADD CONSTRAINT FK_66912DD16BF700BD FOREIGN KEY (status_id) REFERENCES twitter_statuses (id) ON DELETE CASCADE',
	'FK_66912DD1A76ED395' => 'ALTER TABLE twitter_statuses_mentions ADD CONSTRAINT FK_66912DD1A76ED395 FOREIGN KEY (user_id) REFERENCES twitter_users (id) ON DELETE CASCADE',
);
$queries['fk']['twitter_statuses_tags'] = array(
	'FK_DFBA76B56BF700BD' => 'ALTER TABLE twitter_statuses_tags ADD CONSTRAINT FK_DFBA76B56BF700BD FOREIGN KEY (status_id) REFERENCES twitter_statuses (id) ON DELETE CASCADE',
);
$queries['fk']['twitter_statuses_urls'] = array(
	'FK_9A92D5326BF700BD' => 'ALTER TABLE twitter_statuses_urls ADD CONSTRAINT FK_9A92D5326BF700BD FOREIGN KEY (status_id) REFERENCES twitter_statuses (id) ON DELETE CASCADE',
);
$queries['fk']['twitter_stream'] = array(
	'FK_8D6AB9A89B6B5FBA' => 'ALTER TABLE twitter_stream ADD CONSTRAINT FK_8D6AB9A89B6B5FBA FOREIGN KEY (account_id) REFERENCES twitter_accounts (id) ON DELETE CASCADE',
);
$queries['fk']['twitter_users_followers'] = array(
	'FK_F37AF1BEA76ED395' => 'ALTER TABLE twitter_users_followers ADD CONSTRAINT FK_F37AF1BEA76ED395 FOREIGN KEY (user_id) REFERENCES twitter_users (id) ON DELETE CASCADE',
	'FK_F37AF1BE70FC2906' => 'ALTER TABLE twitter_users_followers ADD CONSTRAINT FK_F37AF1BE70FC2906 FOREIGN KEY (follower_user_id) REFERENCES twitter_users (id) ON DELETE CASCADE',
);
$queries['fk']['twitter_users_friends'] = array(
	'FK_77C2EDABA76ED395' => 'ALTER TABLE twitter_users_friends ADD CONSTRAINT FK_77C2EDABA76ED395 FOREIGN KEY (user_id) REFERENCES twitter_users (id) ON DELETE CASCADE',
	'FK_77C2EDAB93D1119E' => 'ALTER TABLE twitter_users_friends ADD CONSTRAINT FK_77C2EDAB93D1119E FOREIGN KEY (friend_user_id) REFERENCES twitter_users (id) ON DELETE CASCADE',
);
$queries['fk']['user_rules'] = array(
	'FK_6B5862642940B3FB' => 'ALTER TABLE user_rules ADD CONSTRAINT FK_6B5862642940B3FB FOREIGN KEY (add_organization_id) REFERENCES organizations (id) ON DELETE CASCADE',
	'FK_6B586264A19F75EA' => 'ALTER TABLE user_rules ADD CONSTRAINT FK_6B586264A19F75EA FOREIGN KEY (add_usergroup_id) REFERENCES usergroups (id) ON DELETE CASCADE',
);
$queries['fk']['usersources'] = array(
	'FK_4E3C994CEB0D3362' => 'ALTER TABLE usersources ADD CONSTRAINT FK_4E3C994CEB0D3362 FOREIGN KEY (usersource_plugin_id) REFERENCES usersource_plugins (id) ON DELETE CASCADE',
);
$queries['fk']['usersource_plugins'] = array(
	'FK_E484A367EC942BCF' => 'ALTER TABLE usersource_plugins ADD CONSTRAINT FK_E484A367EC942BCF FOREIGN KEY (plugin_id) REFERENCES plugins (id) ON DELETE CASCADE',
);
$queries['fk']['visitors'] = array(
	'FK_7B74A43F217BBB47' => 'ALTER TABLE visitors ADD CONSTRAINT FK_7B74A43F217BBB47 FOREIGN KEY (person_id) REFERENCES people (id) ON DELETE SET NULL',
	'FK_7B74A43F866B65F3' => 'ALTER TABLE visitors ADD CONSTRAINT FK_7B74A43F866B65F3 FOREIGN KEY (initial_track_id) REFERENCES visitor_tracks (id) ON DELETE SET NULL',
	'FK_7B74A43F5B84E254' => 'ALTER TABLE visitors ADD CONSTRAINT FK_7B74A43F5B84E254 FOREIGN KEY (visit_track_id) REFERENCES visitor_tracks (id) ON DELETE SET NULL',
	'FK_7B74A43F26B379DD' => 'ALTER TABLE visitors ADD CONSTRAINT FK_7B74A43F26B379DD FOREIGN KEY (last_track_id) REFERENCES visitor_tracks (id) ON DELETE SET NULL',
	'FK_7B74A43F413BC2FF' => 'ALTER TABLE visitors ADD CONSTRAINT FK_7B74A43F413BC2FF FOREIGN KEY (last_track_id_soft) REFERENCES visitor_tracks (id) ON DELETE SET NULL',
);
$queries['fk']['visitor_tracks'] = array(
	'FK_E002459270BEE6D' => 'ALTER TABLE visitor_tracks ADD CONSTRAINT FK_E002459270BEE6D FOREIGN KEY (visitor_id) REFERENCES visitors (id) ON DELETE CASCADE',
);
$queries['fk']['widgets'] = array(
	'FK_9D58E4C1EC942BCF' => 'ALTER TABLE widgets ADD CONSTRAINT FK_9D58E4C1EC942BCF FOREIGN KEY (plugin_id) REFERENCES plugins (id) ON DELETE CASCADE',
);




return $queries;
