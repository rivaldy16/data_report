<?php
namespace AppBundle\ApiRptRenja\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use AppBundle\Lib\LivedRestClient;

class RptRenjaLampPermen546c0Sub1Controller extends Controller
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
        $idKgtn = $this->request->query->get("id_kgtn");
        $jnsRpt = $this->request->query->get("jns_report");
        $tahun = $this->request->query->get("tahun");
		$param = [
            'id_kgtn' => $idKgtn,
            'jns_report' => $jnsRpt
        ];

        $this->restClient->setBaseUri($this->uriRestRenja);
        $renjaReports = $this->restClient->getCollection("$tahun/renjareports", $param);
        return $renjaReports;
		
        /*$this->restClient->setBaseUri($this->uriRestRenja);
        $renjaReports = $this->restClient->getCollection("$tahun/renjareports", $param);


        $renjaRepHandler = $renjaReports;

        $mapRenjaSikdKlpkId = [];
        $i = 0;
        foreach ($renjaRepHandler as $MapRenjaSikdKlpkId) {
            $mapRenjaSikdKlpkId[$i] =  $MapRenjaSikdKlpkId->sikd_klpk_indikator_id;
            $i++;
        }
        $param2 ['id_sikd_klpk_indikator'] = $mapRenjaSikdKlpkId;
        $ids = implode(',', $param2["id_sikd_klpk_indikator"]);
        $paramIn = ['ids' => $ids];$paramIn ['parents']=1;
        $this->restClient->setBaseUri($this->uriRestSikd);
        $sikdKlpkInd = $this->restClient->getCollection("$tahun/sikdklpkindikators", $paramIn);
        $mapSikdKlpkId = $this->populateSikdInfo($sikdKlpkInd);


        foreach ($renjaReports as &$value1) {
            if ($value1->sikd_klpk_indikator_id != null){
                $value1->sikd_klpk_indikator_nm_klpk_indikator = $mapSikdKlpkId['sikd_klpk_indikator'][$value1->sikd_klpk_indikator_id]['nm_klpk_indikator'];
            }else{
                $value1->sikd_klpk_indikator_nm_klpk_indikator = '';
            }
        }

        return $renjaReports;*/
    }

    /*private function populateSikdInfo($sikdInfoList){
        $mapSikdInfo = []; $rekRincObjList=[];
        foreach ($sikdInfoList as $sikdInfoBlock) {
            //RINCIAN OBJEK - REK OBJEK - REK AKUN - REK JENIS - REK KELOMPOK
            $infoListRincObj['id_sikd_klpk_indikator'] = $sikdInfoBlock->id_sikd_klpk_indikator;
            $infoListRincObj['kd_klpk_indikator'] = $sikdInfoBlock->kd_klpk_indikator;
            $infoListRincObj['nm_klpk_indikator'] = $sikdInfoBlock->nm_klpk_indikator;
            $rekRincObjList[$sikdInfoBlock->id_sikd_klpk_indikator] = $infoListRincObj;
        }
        $mapSikdInfo['sikd_klpk_indikator'] = $rekRincObjList;
        return $mapSikdInfo;
    }*/

   
}