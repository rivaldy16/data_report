<?php
namespace AppBundle\ApiRptPpas\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use AppBundle\Lib\LivedRestClient;

class RptPpasHeaderLandscapeController extends Controller
{
    private $uriRestRkpd;
    private $uriRestSikd;
    private $restClient;
    private $kdTenant;
    private $uriRestRenja;
    private $uriRestPpas;
    
    static private $pathPpasReport = "ppasreports";
    
    public function __construct($request_stack, $rest_client, $uri_rest_rkpd, $uri_rest_renja, $uri_rest_ppas ,$uri_rest_setup)
    {
        $this->request = $request_stack->getCurrentRequest();
        $this->restClient = $rest_client;
        $this->kdTenant = $this->request->headers->get("tenant");
        $this->uriRestRkpd = $uri_rest_rkpd;
        $this->uriRestRenja = $uri_rest_renja;
        $this->uriRestPpas = $uri_rest_ppas;
        $this->uriRestSikd = $uri_rest_setup;
    }
    
    public function getDataReport()
    {        
        $jnsRpt = $this->request->query->get('jns_report');//"renja_lamp_permen54_6c0_sub2";//
    	$idPpas = $this->request->query->get("id_ppas");
        $tahun = $this->request->query->get("tahun");
	    
		$param = [
            'jns_report' => $jnsRpt, 
            'id_ppas' => $idPpas,
            'tahun' => $tahun
        ];

        
        $this->restClient->setBaseUri($this->uriRestPpas);
        $ppasReports = $this->restClient->getCollection("$tahun/ppasreports", $param);

        return $ppasReports;
    }    
}