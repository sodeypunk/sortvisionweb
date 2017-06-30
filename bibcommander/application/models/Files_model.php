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

	public function get_by_fileId($fileId, $numberOfRecords = 0) {

		$this->db->select('f.FILE_PATH, f.FILE_STATUS, f.DRAW_IMAGES, f.UPDT');
		$this->db->from('FILES f');
		$this->db->where('f.IDFILE', $fileId);
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

	public function get_completed_files_by_api_key($apiKey, $numberOfRecords)
	{
		$this->db->select('f.IDFILE, f.EC2_STATE, f.EC2_INSTANCE_ID, f.EC2_HOSTNAME, f.EC2_INSTANCE_TYPE, f.FILE_PATH, f.FILE_STATUS, f.UPDT, f.START_TIME, f.END_TIME, f.IMG_COUNT, COUNT(rc.IDFILE) AS IMAGES_COMPLETED');
		$this->db->from('FILES f');
		$this->db->join('RESULTS_CLIENT rc', 'f.IDFILE = rc.IDFILE', 'left');
		$where = "(f.API_KEY='$apiKey' AND (f.FILE_STATUS = 'COMPLETED' OR f.FILE_STATUS = 'ERROR' or f.FILE_STATUS = 'REMOVED'))";
		$this->db->where($where);
		$this->db->group_by(array('EC2_INSTANCE_ID', 'EC2_STATE', 'FILE_PATH', 'FILE_STATUS', 'UPDT', 'IMG_COUNT'));
		$this->db->order_by('f.IDFILE', 'desc');
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
			return array();
		}
	}

	public function get_in_progress_files_status($fileid, $apiKey)
	{
		$this->db->select('f.IDFILE, f.EC2_STATE, f.EC2_INSTANCE_ID, f.EC2_HOSTNAME, f.EC2_INSTANCE_TYPE, f.FILE_PATH, f.FILE_STATUS, f.UPDT, f.START_TIME, f.END_TIME, f.IMG_COUNT, COUNT(rc.IDFILE) AS IMAGES_COMPLETED');
		$this->db->from('FILES f');
		$this->db->join('RESULTS_CLIENT rc', 'f.IDFILE = rc.IDFILE', 'left');
		$this->db->where('f.IDFILE', $fileid);
		$this->db->where('f.API_KEY', $apiKey);
		$this->db->group_by(array('EC2_INSTANCE_ID', 'EC2_STATE', 'FILE_PATH', 'FILE_STATUS', 'UPDT', 'IMG_COUNT'));
		$this->db->order_by('f.IDFILE', 'desc');

		$query = $this->db->get();

		if($query->num_rows() != 0)
		{
			return $query->result_array();
		}
		else
		{
			return array();
		}
	}

	public function get_in_progress_files_by_api_key($apiKey, $numberOfRecords)
	{
		$this->db->select('f.IDFILE, f.EC2_STATE, f.EC2_INSTANCE_ID, f.EC2_HOSTNAME, f.EC2_INSTANCE_TYPE, f.FILE_PATH, f.FILE_STATUS, f.UPDT, f.START_TIME, f.END_TIME, f.IMG_COUNT, COUNT(rc.IDFILE) AS IMAGES_COMPLETED');
		$this->db->from('FILES f');
		$this->db->join('RESULTS_CLIENT rc', 'f.IDFILE = rc.IDFILE', 'left');
		$where = "(f.API_KEY='$apiKey' AND (f.FILE_STATUS = 'NEW' OR f.FILE_STATUS = 'IN PROGRESS'))";
		$this->db->where($where);
		$this->db->group_by(array('EC2_INSTANCE_ID', 'EC2_STATE', 'FILE_PATH', 'FILE_STATUS', 'UPDT', 'IMG_COUNT'));
		$this->db->order_by('f.IDFILE', 'desc');
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
			return array();
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

	public function delete_file($fileid)
	{
		$fileData = array(
			'FILE_STATUS' => 'REMOVED',
			'UPDT' => util::CurrentDateTime()
		);

		$this->db->where('IDFILE', $fileid);
		$rowsAffected = $this->db->update('FILES', $fileData);

		if ($rowsAffected > 0)
		{
			return true;
		}
		else
		{
			return false;
		}
	}

	public function get_gpu_information($fileid)
	{
		$this->db->select('G.EC2_HOSTNAME, G.GPU_SLOT, G.GPU_STATUS, G.START_TIME, G.END_TIME, COUNT(J.IDFILE) as IMAGES_PROCESSED, ' .
		'(SELECT IMAGE_PATH FROM JOB_IMAGES WHERE IDFILE = ' . $fileid . ' AND IMAGE_STATUS <> \'NEW\' AND GPU_ID = G.GPU_SLOT ORDER BY UPDT DESC LIMIT 1) AS LAST_IMAGE_PROCESSED ');
		$this->db->from('JOB_GPUS G');
		$this->db->join('JOB_IMAGES J', 'G.IDFILE = J.IDFILE AND J.IMAGE_STATUS <> \'NEW\' AND J.GPU_ID = G.GPU_SLOT', 'left');
		$this->db->where('G.IDFILE', $fileid);
		$this->db->group_by(array('G.EC2_HOSTNAME', 'G.GPU_SLOT', 'G.GPU_STATUS', 'G.START_TIME', 'G.END_TIME'));
		$this->db->order_by('G.GPU_SLOT', 'asc');

		$query = $this->db->get();

		if($query->num_rows() != 0)
		{
			return $query->result_array();
		}
		else
		{
			return array();
		}
	}
}