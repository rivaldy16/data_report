<?php
namespace AppBundle\ApiRptRenja\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use AppBundle\Lib\LivedRestClient;

class RptRenjaAnggaranSubController extends Controller
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
    }
    
    public function getDataReport()
    {        
        $idRenjaMataAnggaran = $this->request->query->get("id_renja_mata_anggaran");
	    $tahun = $this->request->query->get("tahun");
        $jnsRpt = $this->request->query->get("jns_report");
		$param = [
            'id_renja_mata_anggaran' => $idRenjaMataAnggaran,
            'tahun' => $tahun,
            'jns_report' => $jnsRpt
        ];
		
        $this->restClient->setBaseUri($this->uriRestRenja);
        $renjaReports = $this->restClient->getCollection("$tahun/renjareports", $param);
        return $renjaReports;
    }

   
}