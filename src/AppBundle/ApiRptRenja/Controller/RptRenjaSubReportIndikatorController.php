<?php
namespace AppBundle\ApiRptRenja\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use AppBundle\Lib\LivedRestClient;

class RptRenjaSubReportIndikatorController extends Controller
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
        $idRenjaKgtn = $this->request->query->get("id_renja_kgtn");
        $param = [
            'jns_report' => $jnsRpt,
            'id_renja_kgtn' => $idRenjaKgtn,
            'tahun' => $tahun
        ];
        
        $tahun = $param['tahun'];


        
        $this->restClient->setBaseUri($this->uriRestRenja);
        $renjaReports = $this->restClient->getCollection("$tahun/renjareports", $param);
//         return $renjaReports;
        $renjaRepHandler = $renjaReports;
        
        //CONTAINS SIKD KGTN ID
        $mapRenjaSikdKlpkIndktrId = []; $idSatker = ''; $idSubUnit='';
        $i = 0;
        foreach ($renjaRepHandler as $renjaSikdKlpkIndktr) {
            $mapRenjaSikdKlpkIndktrId[$i] =  $renjaSikdKlpkIndktr->renja_kegiatan_indikator_sikd_klpk_indikator_id;
            $i++;
        }
        //CONTAINS SIKD'S INFO
        $this->restClient->setBaseUri($this->uriRestSikd);
        $param2 ['sikd_klpk_indikator_id'] = $mapRenjaSikdKlpkIndktrId;
        $ids = implode(',', $param2["sikd_klpk_indikator_id"]);
        $paramIn = ['ids' => $ids];
        $sikdRenja = $this->restClient->getCollection("$tahun/sikdklpkindikators", $paramIn);
        $mapSikd = $this->populateSikdMstrInfo($sikdRenja);
        
        $renjaRekapProgram = array();
        foreach ($renjaReports as &$value1) {
            $value1->renja_kegiatan_indikator_no_indikator = doubleval($value1->renja_kegiatan_indikator_no_indikator) ;
            $value1->sikd_klpk_indikator_id_sikd_klpk_indikator = $value1->renja_kegiatan_indikator_sikd_klpk_indikator_id;
            $value1->sikd_klpk_indikator_nm_klpk_indikator = $mapSikd['klpk_indikator'][$value1->renja_kegiatan_indikator_sikd_klpk_indikator_id]['nm_klpk_indikator'];
            $value1->renja_kegiatan_indikator_target_thn_ini = doubleval($value1->renja_kegiatan_indikator_target_thn_ini);
        }
        return $renjaReports;
    }
    
    private function populateSikdMstrInfo($sikdInfoList){
        $mapSikdInfo = []; $klpkIdktrList=[];
        foreach ($sikdInfoList as $sikdInfoBlock) {
            //BIDANG
            $infoListKlpkIdktr['id_sikd_klpk_indikator'] = $sikdInfoBlock->id_sikd_klpk_indikator;
            $infoListKlpkIdktr['kd_klpk_indikator'] = $sikdInfoBlock->kd_klpk_indikator;
            $infoListKlpkIdktr['nm_klpk_indikator'] = $sikdInfoBlock->nm_klpk_indikator;
            $klpkIdktrList[$sikdInfoBlock->id_sikd_klpk_indikator] = $infoListKlpkIdktr;
        }
        $mapSikdInfo['klpk_indikator'] = $klpkIdktrList;
        return $mapSikdInfo;
    }
}