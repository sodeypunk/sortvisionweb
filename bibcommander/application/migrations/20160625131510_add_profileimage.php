<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Add_profileimage extends CI_Migration {

		public function up()
		{
			$fields = array(
				'profile_image' => array(
					'type' => 'TEXT',
					'null' => TRUE,
					)
			);
			$this->dbforge->add_column('user_profiles', $fields);
		}

        public function down()
        {
        }
}