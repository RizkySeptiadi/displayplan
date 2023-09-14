<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Home extends CI_Controller
{

	public function __construct()
	{
		parent::__construct();
		$this->load->database();
		$this->timbangan_ds = $this->load->database('timbangan_ds', TRUE);
		$this->timbangan_ax = $this->load->database('timbangan_ax', TRUE);

		$this->load->model('m_home');
	}

	public function index()
	{
		$this->load->view('home/index');
	}

	public function readData($keyword=null)
	{
			$data['pagination'] = $this->m_home->pagination($keyword);
			echo $this->load->view('home/table', $data, TRUE);
	}

	public function updateState() {
		$this->db->select('Dyelot, Text11');
		$this->db->from('dyelots');
		$this->db->where('State =', 25);
		$orgatex = $this->db->get()->result();
		
		// $today = date('Y-m-d H:i:s');
		// $kemarin = date('Y-m-d 00:00:00', strtotime('0 days ago'));
		// $test = $this->timbangan_ax->query("SELECT TOP 100 * FROM dbo.領料檔 ORDER BY 開始時間 DESC")->result();

		foreach($orgatex as $data) {
			$ds = str_replace('/', '', $data->Dyelot) . 'KP' . $data->Text11;
			$dsResults = $this->timbangan_ds->query("SELECT * FROM dbo.領料檔 WHERE 唯一編號 LIKE '%$ds%'");
			
			$ax = str_replace('/', '', $data->Dyelot) . 'KP' . $data->Text11;
			$axResults = $this->timbangan_ax->query("SELECT * FROM dbo.領料檔 WHERE 唯一編號 LIKE '%$ax%'");

			$dsTotal = $this->db->query("SELECT * FROM Dyelot_recipe WHERE Dyelot = '" .$orgatex['Dyelot']. "' AND RecipeUnit = '%'")->num_rows();
			$axTotal = $this->db->query("SELECT * FROM Dyelot_recipe WHERE Dyelot = '" .$orgatex['Dyelot']. "' AND RecipeUnit = 'g/l'")->num_rows();

			if($dsResults->num_rows() == $dsTotal && $axResults->num_rows() == $axTotal) {
				$this->db->where('Dyelot', $data->Dyelot);
        $this->db->update('Dyelots', ['State' => 27]);

				$this->output->set_content_type('application/json');
        echo json_encode(['UpdateState' => 'success!']);
			}

			if($dsResults->num_rows() > 0) {
				$idwokp = $dsResults->row()->唯一編號;
				$idwo  	= substr($idwokp, 0, 2) . '/' . substr($idwokp, 2, 4) . '/' . substr($idwokp, 6, 4);
				
				$this->db->where('Dyelot', $idwo);
				$this->db->where('ProductCode', $dsResults->row()->藥劑編號);
        $this->db->update('Dyelot_Recipe', ['ActualAmount' => $dsResults->row()->實際重量]);
			}

			if($axResults->num_rows() > 0) {
				$idwokp = $axResults->row()->唯一編號;
				$idwo  	= substr($idwokp, 0, 2) . '/' . substr($idwokp, 2, 4) . '/' . substr($idwokp, 6, 4);

				$this->db->where('Dyelot', $idwo);
				$this->db->where('ProductCode', $axResults->row()->藥劑編號);
        $this->db->update('Dyelot_Recipe', ['ActualAmount' => $axResults->row()->實際重量]);
			}
		}
	}

	public function updateStateTest() {
		$this->db->select('Dyelot, Text11');
		$this->db->from('dyelots');
		$this->db->where('State =', 25);
		$orgatex = $this->db->get()->result();
		
		// $today = date('Y-m-d H:i:s');
		// $kemarin = date('Y-m-d 00:00:00', strtotime('0 days ago'));
		$test = $this->timbangan_ds->query("SELECT TOP 100 * FROM dbo.領料檔 WHERE 唯一編號 LIKE '%KP3773%' ORDER BY 開始時間 DESC")->result();
		var_dump($test); die();

		foreach($orgatex as $data) {
			$ds = str_replace('/', '', $data->Dyelot) . 'KP' . $data->Text11 . 'D';
			$dsResults = $this->timbangan_ds->query("SELECT * FROM dbo.領料檔 WHERE 唯一編號 = '$ds'");
			
			$ax = str_replace('/', '', $data->Dyelot) . 'KP' . $data->Text11 . 'X';
			$axResults = $this->timbangan_ax->query("SELECT * FROM dbo.領料檔 WHERE 唯一編號 = '$ax'");

			if($dsResults->num_rows() > 0 && $axResults->num_rows() > 0) {
				$this->db->where('Dyelot', $data->Dyelot);
        $this->db->update('Dyelots', ['State' => 27]);

				$this->output->set_content_type('application/json');
        echo json_encode(['UpdateState' => 'success!']);
			}

			if($dsResults->num_rows() > 0) {
				$idwokp = $dsResults->row()->唯一編號;
				$idwo  	= substr($idwokp, 0, 2) . '/' . substr($idwokp, 2, 4) . '/' . substr($idwokp, 6, 4);
				
				$this->db->where('Dyelot', $idwo);
				$this->db->where('ProductCode', $dsResults->row()->藥劑編號);
        $this->db->update('Dyelot_Recipe', ['ActualAmount' => $dsResults->row()->實際重量]);
			}

			if($axResults->num_rows() > 0) {
				$idwokp = $dsResults->row()->唯一編號;
				$idwo  	= substr($idwokp, 0, 2) . '/' . substr($idwokp, 2, 4) . '/' . substr($idwokp, 6, 4);

				$this->db->where('Dyelot', $idwo);
				$this->db->where('ProductCode', $axResults->row()->藥劑編號);
        $this->db->update('Dyelot_Recipe', ['ActualAmount' => $axResults->row()->實際重量]);
			}
		}
	}
}
