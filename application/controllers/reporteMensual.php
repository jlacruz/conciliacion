<?php

class ReporteMensual extends CI_Controller {

    // private $db_sistema_base;       
    function __construct() {
        parent::__construct();
        $this->load->library('form_validation');
        $this->load->database();
        $this->load->helper('form');
        $this->load->helper('url');
        $this->load->model('fmodel');
        $this->load->helper('date');
        $this->load->library('calendar');
        $this->output->set_header("Expires: Tue, 01 Jan 2000 00:00:00 GMT");
        $this->output->set_header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
        $this->output->set_header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
        $this->output->set_header("Cache-Control: post-check=0, pre-check=0", false);
        $this->output->set_header("Pragma: no-cache");
    }

    public function gestion_factura() {

        $arrayData = array();
        $this->db_conciliacion = $this->load->database('default', true);
        $data['lista_proveedores'] = $this->fmodel->consultas('consultar_proveedores', $arrayData);

        $this->load->template('consultareporteMensual_view', $data);
    }

    public function consulta_movimiento() {
           
        //print_r($_POST);

        if ($this->uri->segment(3) == "") {
            $desde = 0;
        } else {
            $desde = $this->uri->segment(3);
        }

        $pagination = 1;
        $hasta = $pagination;
        
        $arrayData = array();
        extract($_POST);

        $arrayData1[] = $id_proveedor;
        $arrayData1[] = $periodo;
        
        $arrayData2[] = $id_proveedor;
  
        //$mes1=2;
        //$mes2=5;
                
        $arrayData[] = $id_proveedor;
        $arrayData[] = $periodo;        
        $arrayData[] = $mes1;
        $arrayData[] = $mes2;
        
        
        if($id_proveedor=='0'){
			echo'<script>
  alert("Debe seleccionar un Proveedor");
</script>';
			
		}   
        elseif($periodo==''){
			echo'<script>
  alert("Debe seleccionar un Periodo");
</script>';
			
		}
		elseif($mes1==''){
			echo'<script>
  alert("Debe seleccionar un Mes Inicial");
</script>';
			
		}   
		elseif($mes2==''){
			echo'<script>
  alert("Debe seleccionar un Mes Final");
</script>';
			
		} 
		elseif($mes1 > $mes2){
			echo'<script>
  alert("Mes Inicial debe ser menor al Mes Final");
</script>';
			
		}     
			
		else{

        $this->db_conciliacion = $this->load->database('default', true);
        $data = $this->fmodel->consultas('consultar_factura_mes', $arrayData);
        //print_r($data);
        $data2 = $this->fmodel->consultas('nombre_proveedorservicio', $arrayData2);
       // print_r($data);

        $cantidadTuplas = $this->fmodel->consultas('consultar_factura_mes', $arrayData);
         //print_r($cantidadTuplas);

        //$datos=count($datos_usuario);


        $cantidad = $cantidadTuplas[0][0];

        $variables['data'] = $data;

        $variables['v_f_numero'] = $data[0][0];
        $variables['v2_nom_empresa'] = $data2[0][0];
        $variables['v2_descripcion'] = $data2[0][1];
        $variables['idProveedor'] = $id_proveedor;
        $variables['periodo'] = $periodo;
        $variables['mes1'] = $mes1;
        $variables['mes2'] = $mes2;

        $this->load->view('consultareporteMensualDetalle_view', $variables);
    }
}
    public function buscarDatos() {

        if ($this->uri->segment(3) == "") {
            $desde = 0;
        } else {
            $desde = $this->uri->segment(3);
        }

        $pagination = 1;
        $hasta = $pagination;

        //print_r($_POST);
        extract($_GET);
        $arrayData = array();
        $this->db_conciliacion = $this->load->database('default', true);
        $datos_usuario = $this->pmodel->consultas('usuarios_todos', $arrayData);

        $cantidadTuplas = $this->pmodel->consultas('usuarios_todos', $arrayData);
        $datos = count($datos_usuario);


        $cantidad = $cantidadTuplas[0][0];


        ///////////////listar/////////////////////
        $config['base_url'] = base_url() . 'index.php/listarusuarioscontroler/buscarDatos';
        $config['total_rows'] = $cantidad;
        $config['uri_segment'] = 3;

        $config['per_page'] = $pagination;
        $config['is_ajax_paging'] = TRUE; // default FALSE
        $config['paging_function'] = 'ajax_paging'; // Your jQuery paging


        $config['full_tag_open'] = '<div><ul class="pagination">';
        $config['full_tag_close'] = '</ul></div><!--pagination-->';

        $config['first_link'] = '&laquo; Primero';
        $config['first_tag_open'] = '<li class="prev page">';
        $config['first_tag_close'] = '</li>';

        $config['last_link'] = 'Ultimo &raquo;';
        $config['last_tag_open'] = '<li class="next page">';
        $config['last_tag_close'] = '</li>';

        $config['next_link'] = 'siguiente &rarr;';
        $config['next_tag_open'] = '<li class="next page">';
        $config['next_tag_close'] = '</li>';

        $config['prev_link'] = '&larr; Anterior';
        $config['prev_tag_open'] = '<li class="prev page">';
        $config['prev_tag_close'] = '</li>';

        $config['cur_tag_open'] = '<li class="active"><a href="">';
        $config['cur_tag_close'] = '</a></li>';

        $config['num_tag_open'] = '<li class="page">';
        $config['num_tag_close'] = '</li>';

        $config['anchor_class'] = 'follow_link';
        $config['anchor_class'] = 'follow_link';

        $this->pagination->initialize($config);
        $vars['resultados'] = $datos_usuario;

        if ($this->uri->segment(3) == "") {
            $this->load->template('listarusuarios_view', $vars);
        } else {
            $this->load->view('listarusuarios_view', $vars);
        }
    }

    public function reporteConciliacionMensual() {

        $this->load->library('pdf');
        $pdf = $this->pdf->load();
        $arrayData = array();
        extract($_POST);

        $arrayData[] = $id_proveedor;
        $arrayData1[] = $id_proveedor;
        $arrayData2[] = $id_proveedor;
        $arrayData[] = $periodo;
        $arrayData1[] = $periodo;
        
        //$mes1=1;
        //$mes2=12;
        
        $arrayData[] = $mes1;
        $arrayData[] = $mes2;

        $this->db_conciliacion = $this->load->database('default', true);
        $data = $this->fmodel->consultas('consultar_factura_mes', $arrayData);
        //print_r($data );
        //exit;
        $data2 = $this->fmodel->consultas('nombre_proveedorservicio', $arrayData2);

        $cantidadTuplas = $this->fmodel->consultas('consultar_factura_mes', $arrayData);
        //$datos=count($datos_usuario);
        //exit;

        $cantidad = $cantidadTuplas[0][0];

        $variables['data'] = $data;
        $variables['v_f_numero'] = $data[0][0];
        $variables['v2_nom_empresa'] = $data2[0][0];
        $variables['v2_descripcion'] = $data2[0][1];
        $variables['idProveedor'] = $id_proveedor;
        $variables['periodo'] = $periodo;
        $variables['mes1'] = $mes1;
        $variables['mes2'] = $mes2;

        $html = $this->load->view('consultaReporteMensualPDF', $variables, true);
        
        $pdf->SetHtmlHeader('<img width="900px" height="200px" src="' . base_url() . APPPATH . 'imagenes/cabecera.png">');
        $pdf->AddPage('','','','','',20,20,20,35,10,10);
        $pdf->WriteHTML($html);

        $pdf->Output();
        $pdf->WriteHTML($html);
    }

}

?>