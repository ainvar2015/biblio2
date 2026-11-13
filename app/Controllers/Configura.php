<?php

namespace App\Controllers;

use App\Models\ConfiguraModel;
use App\Models\ComunesModel;

class Configura extends BaseController
{
    protected $configuraModel;
    protected $comunesModel;
    protected $session;

    public function __construct()
    {
        $this->configuraModel = new ConfiguraModel();
        $this->comunesModel   = new ComunesModel();
        $this->session        = session();

        // Helpers (equivalente a $this->load->helper)
        helper(['MY_menu']);
    }

    public function index()
    {
        if ($this->session->get('is_logued_in') === true) {

            $configura = $this->comunesModel->_configura();

            $data = [
                'data_config' => $configura,
                'titulo'      => $configura->titulo_pagina,
                'modulos'     => $this->comunesModel->_modulos(),
            ];

            return view('admin/head_view', $data)
            . view('admin/cabecera_view', $data)
            . view('admin/sidebar_view', $data)
            . view('admin/settings_view', $data)
            . view('admin/pie_view', $data);

        } else {
            return redirect()->to(base_url('login'));
        }
    }

    public function guardar()
    {
        $data['data_config'] = $this->comunesModel->_configura();

        if ($this->configuraModel->_guardar()) {

            $data += [
                'titulo'     => $data['data_config']->titulo_pagina,
                'pageHead'   => 'MODIFICAR CONFIGURACIÓN',
                'widgetHead' => 'RESULTADO DE LA EDICIÓN',
                'guardado'   => '<div class="alert alert-success">
                                    <i class="fa fa-thumbs-up fa-lg"></i>
                                    La configuración ha sido guardada correctamente.
                                 </div>',
                'titulores'  => '<i class="fa fa-cogs"></i> Editar Configuración',
                'linksHead'  => '<div class="pull-left">
                                    Resultado de la Edición
                                    <span class="pull-right ml15">
                                        <a class="btn btn-xs btn-default" href="' . base_url('configura') . '">
                                            <i class="fa fa-angle-double-left"></i> Volver a Ver la Configuración
                                        </a>
                                    </span>
                                 </div>',
            ];

            return view('admin/head_view', $data)
            	. view('admin/cabecera_view', $data)
            	. view('admin/sidebar_view')
            	. view('admin/resultado_view')
            	. view('admin/pie_view');

        } else {

            $data += [
                'titulo'     => $data['data_config']->titulo_pagina,
                'pageHead'   => 'MODIFICAR CONFIGURACIÓN',
                'widgetHead' => 'RESULTADO DE LA EDICIÓN',
                'titulores'  => '',
                'linksHead'  => '<div class="pull-left">
                                    Resultado de la Edición
                                    <span class="pull-right ml15">
                                        <a class="btn btn-xs btn-default" href="' . base_url('configura') . '">
                                            <i class="fa fa-angle-double-left"></i> Volver a Ver la Configuración
                                        </a>
                                    </span>
                                 </div>',
                'guardado'   => '<div class="alert alert-danger">
                                    <i class="fa fa-thumbs-down fa-lg"></i>
                                    Ha ocurrido un error. Vuelva a intentarlo más tarde.
                                 </div>',
            ];

            return view('admin/head_view', $data)
				. view('admin/cabecera_view', $data)
				. view('admin/sidebar_view')
				. view('admin/resultado_view')
				. view('admin/pie_view');
        }
    }
}
