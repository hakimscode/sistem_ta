<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
	/**
	* 
	*/
	class Seminar extends CI_Model{
		private $id;
		private $tanggal;
		private $judulid;
		private $mhsid;
		private $pembimbing;
		private $penguji1;
		private $penguji2;
		private $nilai_pembimbing;
		private $nilai_penguji1;
		private $nilai_penguji2;
		private $status;
		private $status_pengajuan;

		function __construct(){
			parent::__construct();
		}

		function setID($id){ $this->id = $id; }
		function getID(){ return $this->id; }
		function setTanggal($tanggal){ $this->tanggal = $tanggal; }
		function getTanggal(){ return $this->tanggal; }
		function setJudulID($judulid){ $this->judulid = $judulid; }
		function getJudulID(){ return $this->judulid; }
		function setMhsID($mhsid){ $this->mhsid = $mhsid; }
		function getMhsID(){ return $this->mhsid; }
		function setPembimbing($pembimbing){ $this->pembimbing = $npembimbing; }
		function getPembimbing(){ return $this->pembimbing; }
		function setPenguji1($penguji1){ $this->penguji1 = $penguji1; }
		function getPenguji1(){ return $this->penguji1; }
		function setPenguji2($penguji2){ $this->penguji2 = $penguji2; }
		function getPenguji2(){ return $this->penguji2; }
		function setNilaiPembimbing($nilai_pembimbing){ $this->nilai_pembimbing = $nilai_pembimbing; }
		function getNilaiPembimbing(){ return $this->nilai_pembimbing; }
		function setNilaiPenguji1($nilai_penguji1){ $this->nilai_penguji1 = $nilai_penguji1; }
		function getNilaiPenguji1(){ return $this->nilai_penguji1; }
		function setNilaiPenguji2($nilai_penguji2){ $this->nilai_penguji2 = $nilai_penguji2; }
		function getNilaiPenguji2(){ return $this->nilai_penguji2; }
		function setStatus($status){ $this->status = $status; }
		function getStatus(){ return $this->status; }
		function setStatusPengajuan($status_pengajuan){ $this->status_pengajuan = $status_pengajuan; }
		function getStatusPengajuan(){ return $this->status_pengajuan; }

		function list_judul_acc_by_mahasiswa(){
			$this->db->select('
				judul.id,
				judul.judul,
				judul.pembimbing,
				judul_detail.metode,
				judul_detail.ringkas_masalah,
				judul_detail.deskripsi,
				dosen.nama_dosen,
				judul.penguji1,
				judul.penguji2,
				judul.status,
				judul.keterangan
				');
			$this->db->from('judul');
			$this->db->join('dosen', 'dosen.id = judul.pembimbing', 'left');
			$this->db->join('judul_detail', 'judul_detail.id_judul = judul.id');
			$this->db->where('judul.mhsid', $this->getMhsID());
			$this->db->where('judul.status', 3);

			$result = $this->db->get();

			if($result->num_rows() > 0){
				return $result->result_array();
			}else{
				return NULL;
			}
		}

		function list_seminar_by_akademik(){
			$this->db->select('
				seminar.id,
				seminar.judulid,
				seminar.mhsid,
				seminar.status_pengajuan,
				judul.judul,
				judul.pembimbing,
				judul.mhsid,
				mahasiswa.nim,
				mahasiswa.nama_mhs,
				dosen.nama_dosen
				');
			$this->db->from('seminar');
			$this->db->join('judul', 'judul.id = seminar.judulid');
			$this->db->join('mahasiswa', 'mahasiswa.id = judul.mhsid');
			$this->db->join('dosen', 'dosen.id = judul.pembimbing', 'left');
			$this->db->where('seminar.status_pengajuan', 2);
			$this->db->or_where('seminar.status_pengajuan', 3);

			$result = $this->db->get();

			if($result->num_rows() > 0){
				return $result->result_array();
			}else{
				return NULL;
			}
		}

		function list_ready_seminar_akademik(){
			$this->db->select('
				seminar.id,
				seminar.judulid,
				seminar.mhsid,
				seminar.status_pengajuan,
				judul.judul,
				judul.pembimbing,
				(
					SELECT
						dosen.nama_dosen 
					FROM
						seminar
						JOIN judul ON judul.id = seminar.judulid
						JOIN dosen ON dosen.id = judul.penguji1
					WHERE seminar.status_pengajuan = 3 
				) as penguji1,
				(
					SELECT
						dosen.nama_dosen 
					FROM
						seminar
						JOIN judul ON judul.id = seminar.judulid
						JOIN dosen ON dosen.id = judul.penguji2
					WHERE seminar.status_pengajuan = 3 
				) as penguji2,
				judul.mhsid,
				mahasiswa.nim,
				mahasiswa.nama_mhs,
				dosen.nama_dosen
				');
			$this->db->from('seminar');
			$this->db->join('judul', 'judul.id = seminar.judulid');
			$this->db->join('mahasiswa', 'mahasiswa.id = judul.mhsid');
			$this->db->join('dosen', 'dosen.id = judul.pembimbing');
			$this->db->where('seminar.status_pengajuan', 3);

			$result = $this->db->get();

			if($result->num_rows() > 0){
				return $result->result_array();
			}else{
				return NULL;
			}
		}

		function get_jadwal_seminar_akademik(){
			$this->db->select('
				seminar.id,
				seminar.judulid,
				seminar.mhsid,
				seminar.status_pengajuan,
				seminar.tanggal,
				seminar.nilai_pembimbing,
				seminar.nilai_penguji1,
				seminar.nilai_penguji2,
				judul.judul,
				judul.pembimbing,
				judul.mhsid,
				mahasiswa.nim,
				mahasiswa.nama_mhs,
				dosen.nama_dosen,
				(
					SELECT
						dosen.nama_dosen 
					FROM
						seminar
						JOIN judul ON judul.id = seminar.judulid
						JOIN dosen ON dosen.id = judul.penguji1
					WHERE seminar.id = '.$this->getID().' 
				) as penguji1,
				(
					SELECT
						dosen.nama_dosen 
					FROM
						seminar
						JOIN judul ON judul.id = seminar.judulid
						JOIN dosen ON dosen.id = judul.penguji2
					WHERE seminar.id = '.$this->getID().' 
				) as penguji2
				');
			$this->db->from('seminar');
			$this->db->join('judul', 'judul.id = seminar.judulid');
			$this->db->join('mahasiswa', 'mahasiswa.id = judul.mhsid');
			$this->db->join('dosen', 'dosen.id = judul.pembimbing', 'left');
			$this->db->where('seminar.id', $this->getID());

			$result = $this->db->get();

			if($result->num_rows() > 0){
				return $result->result_array();
			}else{
				return NULL;
			}
		}

		// ## Cek judul yang telah di ACC untuk pengecekan pengajuan seminar
		function cek_judul_acc(){
			$this->db->select('id, mhsid, judul');
			$this->db->from('judul');
			$this->db->where('mhsid', $this->getMhsID());
			$this->db->where('status', 3);

			$result = $this->db->get();
			if($result->num_rows() > 0){
				return $result->result_array();
			}else{
				return NULL;
			}
		}

		function cek_status_seminar(){
			$this->db->select('id, status_pengajuan');
			$this->db->from('seminar');
			$this->db->where('mhsid', $this->getMhsID());

			$result = $this->db->get();
			
			if($result->num_rows() > 0){
				return $result->result_array();
			}else{
				return NULL;
			}
		}

		function add_seminar_mhs(){
			$data = array(
				'mhsid' => $this->getMhsID(),
				'judulid' => $this->getJudulID(),
				'status_pengajuan' => '2'
			);
			$this->db->insert('seminar', $data);
			return $this->db->insert_id();
		}

		function edit_penguji_judul(){
			$data = array(
				'penguji1' => $this->getPenguji1(),
				'penguji2' => $this->getPenguji2(),
			);

			$this->db->update('judul', $data, array('id'=>$this->getJudulID()));
			return $this->db->affected_rows();
		}

		function edit_seminar(){
			$data = array(
				'tanggal' => $this->getTanggal(),
				'status_pengajuan' => $this->getStatusPengajuan(),
			);

			$this->db->update('seminar', $data, array('id'=>$this->getID()));
			return $this->db->affected_rows();
		}

		function edit_seminar_nilai(){
			$data = array(
				'nilai_pembimbing' => $this->getNilaiPembimbing(),
				'nilai_penguji1' => $this->getNilaiPenguji1(),
				'nilai_penguji2' => $this->getNilaiPenguji2(),
				'status' => $this->getStatus()
			);

			$this->db->update('seminar', $data, array('id'=>$this->getID()));
			return $this->db->affected_rows();
		}

		function get_tanggal_seminar_mhs(){
			$this->db->select('
				seminar.id,
				seminar.judulid,
				seminar.mhsid,
				seminar.tanggal,
				seminar.status_pengajuan,
				judul.judul,
				judul.penguji1,
				judul.pembimbing,
				(
					SELECT
						dosen.nama_dosen 
					FROM
						seminar
						JOIN judul ON judul.id = seminar.judulid
						JOIN dosen ON dosen.id = judul.penguji1
					WHERE
						seminar.mhsid = '.$this->getMhsID().' 
						AND seminar.status_pengajuan = 3 
				) as penguji1,
				(
					SELECT
						dosen.nama_dosen 
					FROM
						seminar
						JOIN judul ON judul.id = seminar.judulid
						JOIN dosen ON dosen.id = judul.penguji2
					WHERE
						seminar.mhsid = '.$this->getMhsID().' 
						AND seminar.status_pengajuan = 3 
				) as penguji2,
				judul.mhsid,
				mahasiswa.nim,
				mahasiswa.nama_mhs,
				dosen.nama_dosen
				');
			$this->db->from('seminar');
			$this->db->join('judul', 'judul.id = seminar.judulid');
			$this->db->join('mahasiswa', 'mahasiswa.id = judul.mhsid');
			$this->db->join('dosen', 'dosen.id = judul.pembimbing');
			$this->db->where('seminar.mhsid', $this->getMhsID());
			$this->db->where('seminar.status_pengajuan', 3);

			$result = $this->db->get();

			if($result->num_rows() > 0){
				return $result->result_array();
			}else{
				return NULL;
			}
		}

		function nilai_seminar_mhs(){
			$this->db->select('
				seminar.id,
				seminar.judulid,
				seminar.mhsid,
				seminar.tanggal,
				seminar.status_pengajuan,
				seminar.nilai_pembimbing,
				seminar.nilai_penguji1,
				seminar.nilai_penguji2,
				judul.judul,
				judul.penguji1,
				judul.pembimbing,
				(
					SELECT
						dosen.nama_dosen 
					FROM
						seminar
						JOIN judul ON judul.id = seminar.judulid
						JOIN dosen ON dosen.id = judul.penguji1
					WHERE
						seminar.mhsid = '.$this->getMhsID().' 
						AND seminar.status_pengajuan = 3 
				) as penguji1,
				(
					SELECT
						dosen.nama_dosen 
					FROM
						seminar
						JOIN judul ON judul.id = seminar.judulid
						JOIN dosen ON dosen.id = judul.penguji2
					WHERE
						seminar.mhsid = '.$this->getMhsID().' 
						AND seminar.status_pengajuan = 3 
				) as penguji2,
				judul.mhsid,
				mahasiswa.nim,
				mahasiswa.nama_mhs,
				dosen.nama_dosen
				');
			$this->db->from('seminar');
			$this->db->join('judul', 'judul.id = seminar.judulid');
			$this->db->join('mahasiswa', 'mahasiswa.id = judul.mhsid');
			$this->db->join('dosen', 'dosen.id = judul.pembimbing');
			$this->db->where('seminar.mhsid', $this->getMhsID());
			$this->db->where('seminar.status_pengajuan', 3);

			$result = $this->db->get();

			if($result->num_rows() > 0){
				return $result->result_array();
			}else{
				return NULL;
			}
		}

	}
?>