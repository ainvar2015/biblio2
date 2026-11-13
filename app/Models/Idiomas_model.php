<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Idiomas_model extends CI_Model {

	public $variable;

	public function __construct()
	{
		parent::__construct();
		
	}

	function _lista_idiomas()
	{
		$this->db->where('activo','1');
		$idiomas_items = $this->db->get('idiomas');
		$rsIdiomas = $idiomas_items->result();
		if($idiomas_items->num_rows() > 0){
			return $rsIdiomas;
		}else{
			return FALSE;
		}
	}

	function _todosIdiomas()
	{
		$activos = $this->_lista_idiomas();
		foreach($activos as $activo)
		{
			$this->db->where('id != ',$activo->id);
		}
		$this->db->order_by('original');
		$todos = $this->db->get('idiomasd');
		if($todos->num_rows() > 0){
			return $todos->result();
		}
	}

	function _datosidioma($id)
	{
		$this->db->where('id',$id);
		$datos = $this->db->get('idiomas');
		if($datos->num_rows() > 0)
		{
			return $datos->row();
		}else{
			return FALSE;
		}
	}
	function _nuevo()
	{
		$idi = $this->input->post('idioma');
		$this->db->where('id',$idi);
		$idiomas = $this->db->get('idiomasd');
		if($idiomas->num_rows() > 0)
		{
			$datosIdioma = $idiomas->row();
		}
//		print_r($datosIdioma);
		$data = array('idioma'=>$datosIdioma->original,'reducido'=>$datosIdioma->codigo2,'activo'=>'1','defecto'=>'0');

		if($this->db->insert('idiomas',$data))
		{
			return TRUE;
		}else{
			return $data;
		}
	}
	function _guarda($modo,$id)
	{
		$this->db->where('id',$id);
		$data = array('idioma'=>$this->input->post('idioma'),'reducido'=>$this->input->post('reducido'),'activo'=>$this->input->post('activo'));
		if($this->db->update('idiomas',$data))
		{
			return TRUE;
		}else{
			return FALSE;
		}
	}

	function _borra($id,$red)
	{
		$this->db->where('id',$id);
		if($this->db->delete('idiomas')){
			$this->db->where('idioma', $red);
			if($this->db->delete('menu')){
				return TRUE;
			}else{
				return FALSE;
			}
//			return TRUE;
		}else{
			return FALSE;
		}
	}

	function _defecto($id)
	{
		$data = array('defecto'=>'0');
		if($this->db->update('idiomas',$data))
		{
			$data = array('defecto'=>'1');
			$this->db->where('id',$id);
			$this->db->update('idiomas',$data);
			return TRUE;
		}
	}
}

/* End of file idiomas_model.php */
/* Location: ./application/models/idiomas_model.php */

/*
$this->db->where('idmenu',$id);
		$this->db->where('idioma',$idioma);
		$contenido = $this->db->get('menu');
		$content = $contenido->row();
*/