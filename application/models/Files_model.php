<?php
class Files_model extends CI_Model {
	
	public function __construct() {
		$this->load->database ();
		$this->load->model ( 'system_model' );
	}
	
	public function insert($fileName, $ezRefString, $userSystem = 4) {
		$systemsArray = $this->system_model->get_system_by_id($userSystem);
		if (empty($systemsArray->DOWNLOAD_DIR))
		{
			throw new Exception("Download directory cannot be empty");
		}
		$data = array (
				'FILE_NAME' => $fileName,
				'STATUS' => 'UPLOADED',
				'EZ_REF_STRING' => $ezRefString,
				'IDSYSTEM' => $userSystem, // NEED TO SET THIS TO APPROPRIATE MACHINE WHEN TRANSFERRING TO SERVER
				'FILE_PATH' => $systemsArray->DOWNLOAD_DIR . "/"
		);
		
		return $this->db->insert ( 'FILES', $data );
		
	}
	
	public function get_by_ezRefString($ezRefString, $numberOfRecords = 0) {
		
		//select * from FILES f
		//LEFT JOIN FILES_HISTORY h
		//on f.ID = h.FILES_ID
		//WHERE EZ_REF_STRING = 'yasedo'
		//ORDER BY h.UPDT ASC
				
		$this->db->select('*');
		$this->db->from('FILES f');
		$this->db->join('FILES_HISTORY h', 'f.IDFILE = h.IDFILE', 'left');
		$this->db->where('f.EZ_REF_STRING', $ezRefString);
		$this->db->order_by('h.IDFILE_HIST', 'asc');
        if ($numberOfRecords > 0)
        {
            $this->db->limit($numberOfRecords);
        }

		$query = $this->db->get();
		
		if($query->num_rows() != 0)
		{
			return $query->result_array();
		}
		else
		{
			return false;
		}
		
	}

	public function get_files($numberOfRecords)
	{
		$this->db->select('*');
		$this->db->from('FILES f');
		$this->db->order_by('f.IDFILE', 'desc');
		$this->db->limit($numberOfRecords);
		$query = $this->db->get();

		if($query->num_rows() != 0)
		{
			return $query->result_array();
		}
		else
		{
			return false;
		}
	}
}