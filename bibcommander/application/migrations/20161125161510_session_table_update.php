<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Session_table_update extends CI_Migration {

		public function up()
		{
			$fields = array(
				'id' => array(
					'name' => 'id',
					'type' => 'VARCHAR',
					'constraint' => '128',
					'null' => FALSE,
					)
			);
			$this->dbforge->modify_column('ci_sessions', $fields);
		}

        public function down()
        {
        }
}