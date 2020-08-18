<?php
namespace AppBundle\ApiRptMusren\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use AppBundle\Lib\LivedRestClient;

class RptMusrenDesaUsulanPrioritasController extends Controller
{
    private $uriRestRkpd;
    private $uriRestSikd;
    private $restClient;
    private $kdTenant;
    private $uriRestRenja;
    private $uriRestMusren;
    
    static private $pathMusrenReport = "musrenreports";
    
    public function __construct($request_stack, $rest_client, $uri_rest_rkpd, $uri_rest_renja, $uri_rest_musren ,$uri_rest_setup)
    {
        $this->request = $request_stack->getCurrentRequest();
        $this->restClient = $rest_client;
        $this->kdTenant = $this->request->headers->get("tenant");
        $this->uriRestRkpd = $uri_rest_rkpd;
        $this->uriRestRenja = $uri_rest_renja;
        $this->uriRestMusren = $uri_rest_musren;
        $this->uriRestSikd = $uri_rest_setup;
    }
    
    public function getDataReport()
    {        
        //print_r("ok");exit;
        $jnsRpt = $this->request->query->get('jns_report');
        $status = $this->request->query->get("status");
        $kec = $this->request->query->get("kd_wilayah_kec");
        $desa = $this->request->query->get("kd_wilayah_desa");
        $quota = $this->request->query->get("quota");
        $bidang = $this->request->query->get("bidang");
        $priortas = $this->request->query->get("priortas");
        $tahun = $this->request->query->get("tahun");
        
        $param = [
            'jns_report' => $jnsRpt, 
            'status' => $status,
            'kode_kec' => $kec,
            'kode_desa' => $kec,
            'quota' => $quota,
            'bidang' => $bidang,
            'priortas' => $priortas,
            'tahun' => $tahun
        ];
        
        $this->restClient->setBaseUri($this->uriRestMusren);
        //print_r($this->uriRestMusren);exit;
        $musrenReports = $this->restClient->getCollection("$tahun/musrenreports", $param);
        
        $musrenReports = array();
        foreach ($renjaReports as &$value1) {
            $value1->vw_appl_kode_wilayah_kode_wilayah= "";
            $value1->vw_appl_kode_wilayah_klasifikasi= "";
            $value1->vw_appl_kode_wilayah_nama_wilayah= "";
            $value1->vw_appl_kode_wilayah_induk_kode_wilayah= "";
            $value1->vw_appl_kode_wilayah_induk_klasifikasi= "";
            $value1->vw_appl_kode_wilayah_induk_nama_wilayah= "";
        }

        return $musrenReports;
    }    

}