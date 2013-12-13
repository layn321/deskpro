<?php
/**************************************************************************\
| DeskPRO (r) has been developed by DeskPRO Ltd. http://www.deskpro.com/   |
| a British company located in London, England.                            |
|                                                                          |
| All source code and content Copyright (c) 2012, DeskPRO Ltd.             |
|                                                                          |
| The license agreement under which this software is released              |
| can be found at http://www.deskpro.com/license                           |
|                                                                          |
| By using this software, you acknowledge having read the license          |
| and agree to be bound thereby.                                           |
|                                                                          |
| Please note that DeskPRO is not free software. We release the full       |
| source code for our software because we trust our users to pay us for    |
| the huge investment in time and energy that has gone into both creating  |
| this software and supporting our customers. By providing the source code |
| we preserve our customers' ability to modify, audit and learn from our   |
| work. We have been developing DeskPRO since 2001, please help us make it |
| another decade.                                                          |
|                                                                          |
| Like the work you see? Think you could make it better? We are always     |
| looking for great developers to join us: http://www.deskpro.com/jobs/    |
|                                                                          |
| ~ Thanks, Everyone at Team DeskPRO                                       |
\**************************************************************************/

/**
 * DeskPRO
 *
 * @package DeskPRO
 * @subpackage
 */

namespace Application\InstallBundle\Upgrade\Build;

class Build1370363730 extends AbstractBuild
{
	public function run()
	{
		$this->out("Add custom_def_products and custom_data_product");
		$this->execMutateSql("CREATE TABLE custom_data_product (id INT AUTO_INCREMENT NOT NULL, product_id INT DEFAULT NULL, field_id INT DEFAULT NULL, root_field_id INT DEFAULT NULL, value INT NOT NULL, input LONGTEXT NOT NULL, INDEX IDX_CCC645474584665A (product_id), INDEX IDX_CCC64547443707B0 (field_id), INDEX IDX_CCC645473F6A6D56 (root_field_id), INDEX field_id_idx (field_id, product_id), PRIMARY KEY(id)) ENGINE = InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci");
		$this->execMutateSql("CREATE TABLE custom_def_products (id INT AUTO_INCREMENT NOT NULL, parent_id INT DEFAULT NULL, plugin_id VARCHAR(255) DEFAULT NULL, js_class VARCHAR(255) NOT NULL, has_form_template TINYINT(1) NOT NULL, has_display_template TINYINT(1) NOT NULL, title VARCHAR(255) NOT NULL, description LONGTEXT NOT NULL, handler_class VARCHAR(255) DEFAULT NULL, options LONGBLOB NOT NULL COMMENT '(DC2Type:array)', is_user_enabled TINYINT(1) NOT NULL, is_enabled TINYINT(1) NOT NULL, display_order INT NOT NULL, default_value VARCHAR(500) DEFAULT NULL, is_agent_field TINYINT(1) NOT NULL, INDEX IDX_AD0FC3DA727ACA70 (parent_id), INDEX IDX_AD0FC3DAEC942BCF (plugin_id), PRIMARY KEY(id)) ENGINE = InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci");
		$this->execMutateSql("ALTER TABLE custom_data_product ADD CONSTRAINT FK_CCC645474584665A FOREIGN KEY (product_id) REFERENCES products (id) ON DELETE CASCADE");
		$this->execMutateSql("ALTER TABLE custom_data_product ADD CONSTRAINT FK_CCC64547443707B0 FOREIGN KEY (field_id) REFERENCES custom_def_products (id) ON DELETE CASCADE");
		$this->execMutateSql("ALTER TABLE custom_data_product ADD CONSTRAINT FK_CCC645473F6A6D56 FOREIGN KEY (root_field_id) REFERENCES custom_def_products (id) ON DELETE CASCADE");
		$this->execMutateSql("ALTER TABLE custom_def_products ADD CONSTRAINT FK_AD0FC3DA727ACA70 FOREIGN KEY (parent_id) REFERENCES custom_def_products (id) ON DELETE CASCADE");
		$this->execMutateSql("ALTER TABLE custom_def_products ADD CONSTRAINT FK_AD0FC3DAEC942BCF FOREIGN KEY (plugin_id) REFERENCES plugins (id) ON DELETE SET NULL");
	}
}