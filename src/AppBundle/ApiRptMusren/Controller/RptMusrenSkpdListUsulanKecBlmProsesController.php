<?php
namespace AppBundle\ApiRptMusren\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use AppBundle\Lib\LivedRestClient;

class RptMusrenSkpdListUsulanKecBlmProsesController extends Controller
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
        $tahun = $this->request->query->get("tahun");
        $idSkpd = $this->request->query->get("id_skpd");
        $idBidang = $this->request->query->get("id_bidang");
        $prioritas = $this->request->query->get("prioritas");
        
        //print_r($kec);exit;

        $param = [
            'jns_report' => $jnsRpt,
            'id_skpd' => $idSkpd,
            'id_bidang' => $idBidang,
            'prioritas' => $prioritas,
            'tahun' => $tahun
        ];
        
        $this->restClient->setBaseUri($this->uriRestMusren);
    
        $musrenReports = $this->restClient->getCollection("$tahun/musrenreports", $param);

        return $musrenReports;
    }    

}