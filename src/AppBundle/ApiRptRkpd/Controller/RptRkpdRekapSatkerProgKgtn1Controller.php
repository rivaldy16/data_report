<?php
namespace AppBundle\ApiRptRkpd\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use AppBundle\Lib\LivedRestClient;

class RptRkpdRekapSatkerProgKgtn1Controller extends Controller
{
    private $uriRestRkpd;
    private $uriRestSikd;
    private $restClient;
    private $kdTenant;
    private $uriRestRenja;
    private $sikd_sub_skpd_nama = 'SKPD INDUK';
    private $uriRestPpas;
    
    static private $pathRkpdReport = "rkpdreports";
    
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
        $jnsRpt = $this->request->query->get('jns_report');
        //$format = $this->request->query->get("format");
        $tahun = $this->request->query->get("tahun");
        $idRkpd = $this->request->query->get("id_rkpd");
        $prioritas = $this->request->query->get("prioritas");
        $param = [
            'jns_report' => $jnsRpt,
            'id_rkpd' => $idRkpd,
            'prioritas' => $prioritas/*,
            'format' => $format*/
        ];
        
        
        
        $this->restClient->setBaseUri($this->uriRestRkpd);
        $rkpdReports = $this->restClient->getCollection("$tahun/rkpdreports", $param);
         return $rkpdReports;
        /*foreach ($rkpdReports as &$value1) {
            $value1->renja_anggaran_id_renja_kegiatan    = '';
            $value1->rkpd_kegiatan_jml_anggaran_rkpd     = '0';
            $value1->renja_kegiatan_tgt_anggaran_thn_ini = '0';
        }

        return $rkpdReports;*/
    }
}