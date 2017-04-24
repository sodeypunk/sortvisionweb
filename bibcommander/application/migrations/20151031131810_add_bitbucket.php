<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Add_bitbucket extends CI_Migration {

		public function up()
		{
			$data = array(
				array('option_name' => "enable_bitbucket", 'option_value' => "0", 'autoload' => "yes"),
				array('option_name' => "bitbucket_key", 'option_value' => "", 'autoload' => "yes"),
				array('option_name' => "bitbucket_secret", 'option_value' => "", 'autoload' => "yes"),
			);
			$this->db->insert_batch('options', $data);
			
			$fields = array(
				'bitbucket_id' => array(
					'type' => 'VARCHAR',
					'constraint' => '255',
					'null' => TRUE,
					)
			);
			$this->dbforge->add_column('user_profiles', $fields);
		}

        public function down()
        {
        }
}