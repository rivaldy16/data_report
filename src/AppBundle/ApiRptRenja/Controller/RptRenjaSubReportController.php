<?php
namespace AppBundle\ApiRptRenja\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use AppBundle\Lib\LivedRestClient;

class RptRenjaSubReportController extends Controller
{
    private $uriRestRenja;
    private $uriRestSikd;
    private $restClient;
    private $kdTenant;
    
    static private $pathRenjaReport = "renjareports";
    
    public function __construct($request_stack, $rest_client, $uri_rest_renja, $uri_rest_setup)
    {
        $this->request = $request_stack->getCurrentRequest();
        $this->restClient = $rest_client;
        $this->kdTenant = $this->request->headers->get("tenant");
        $this->uriRestRenja = $uri_rest_renja;
        $this->uriRestSikd = $uri_rest_setup;
    }
    
    public function getDataReport()
    {        
        $jnsRpt = $this->request->query->get('jns_report');
        $tahun = $this->request->query->get("tahun");
        $idRenjaKgtn = $this->request->query->get("renja_blnj_langsung_renja_kegiatan_id");
        $param = [
            'jns_report' => $jnsRpt,
            'id_renja' => $idRenjaKgtn,
            'tahun' => $tahun
        ];
        
        $tahun = $param['tahun'];
        
        $this->restClient->setBaseUri($this->uriRestRenja);
        $renjaReports = $this->restClient->getCollection("$tahun/renjareports", $param);
        foreach ($renjaReports as &$value1){
            $value1->renja_rincian_mata_anggaran_volume	= doubleval($value1->renja_rincian_mata_anggaran_volume);
            $value1->renja_rincian_mata_anggaran_harga	= doubleval($value1->renja_rincian_mata_anggaran_harga);
            $value1->renja_rincian_mata_anggaran_jumlah	= doubleval($value1->renja_rincian_mata_anggaran_jumlah);
        }
        
        return $renjaReports;
    }
}