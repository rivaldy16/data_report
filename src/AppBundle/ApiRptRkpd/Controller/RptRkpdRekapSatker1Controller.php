<?php
namespace AppBundle\ApiRptRkpd\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use AppBundle\Lib\LivedRestClient;

class RptRkpdRekapSatker1Controller extends Controller
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
        $kdPrioritas = $this->request->query->get("prioritas");
        $idRkpd = $this->request->query->get("id_rkpd");
        $format = $this->request->query->get("format");
        $tahun = $this->request->query->get("tahun");
        $param = [
            'jns_report' => $jnsRpt,
            'id_rkpd' => $idRkpd,
            'tahun' => $tahun ,
            'prioritas' => $kdPrioritas,
            'format' => $format
        ];
        
        $this->restClient->setBaseUri($this->uriRestRkpd);
        $rkpdReports = $this->restClient->getCollection("$tahun/rkpdreports", $param);
        return $rkpdReports;

        /*$tahun = $param['tahun'];
        
        $this->restClient->setBaseUri($this->uriRestRkpd);
        
        $rkpdReports = $this->restClient->getCollection("$tahun/rkpdreports", $param);
        //SET BASE URI TO SIKD DB
        $this->restClient->setBaseUri($this->uriRestSikd);
        
        $rkpdRepHandler = $rkpdReports;
        //CONTAINS SIKD SATKER ID
        $mapRkpdSikdSatkerId = [];
        $i = 0;
        foreach ($rkpdRepHandler as $rkpdRepId) {
            $mapRkpdSikdSatkerId[$i] =  $rkpdRepId->sikd_satker_id;
            $i++;
        }
        
        //CONTAINS SIKD'S SATKER INFO
        $ids3 = implode(',',  array_unique($mapRkpdSikdSatkerId));
        $paramIn3 = ['ids' => $ids3];
        $sikdSatkerRkpdMstr = $this->restClient->getCollection("$tahun/sikdsatkers", $paramIn3);
        $mapSikdSatkerMstr = $this->populateSikdSatkerInfo($sikdSatkerRkpdMstr);
        //GET SUB SKPD'S INFO
        foreach (array_unique($mapRkpdSikdSatkerId) as $sikdSatkerIds){
            $subSkpd = $this->restClient->getCollection("$tahun/sikdskpds/$sikdSatkerIds/sikdsubskpds");
            $mapSikdSubSkpd = $this->populateSikdSubSkpdInfo($subSkpd);
        }
                
        foreach ($rkpdReports as &$value1) {
            $value1->sikd_satker_id_sikd_satker	= $value1->sikd_satker_id;
            $value1->sikd_satker_kode		    = $mapSikdSatkerMstr['satker'][$value1->sikd_satker_id]['kode'];
            $value1->sikd_satker_nama		    = $mapSikdSatkerMstr['satker'][$value1->sikd_satker_id]['nama'];
            if($value1->sikd_sub_skpd_id==''){
                $value1->sikd_sub_skpd_nama     = $this->sikd_sub_skpd_nama;
                $value1->sikd_sub_skpd_kode     = $mapSikdSatkerMstr['satker'][$value1->sikd_satker_id]['kode'];
            } else {
                $value1->sikd_sub_skpd_nama     = $mapSikdSubSkpd['sub_skpd'][$value1->sikd_satker_id][$value1->sikd_sub_skpd_id]['nama'];
                $value1->sikd_sub_skpd_kode     = $mapSikdSubSkpd['sub_skpd'][$value1->sikd_satker_id][$value1->sikd_sub_skpd_id]['kode'];
            }
            if(array_key_exists($value1->sikd_satker_id_sikd_satker,$mapSikdSubSkpd['sub_skpd'])){
                $value1->jml_sub_unit = 1;
            } else {
                $value1->jml_sub_unit = 0;
            }
        }
        return $rkpdReports;*/
    }
    
    /*private function populateSikdSatkerInfo($sikdInfoList){
        $mapSikdInfo = []; $satkerList=[];
        foreach ($sikdInfoList as $sikdInfoBlock) {
            //PROGRAM
            $infoList['id_sikd_satker'] = $sikdInfoBlock->nama;
            $infoList['kode'] = $sikdInfoBlock->kode;
            $infoList['nama'] = $sikdInfoBlock->nama;
            $satkerList[$sikdInfoBlock->id_sikd_satker] = $infoList;
        }
        $mapSikdInfo['satker'] = $satkerList;
        return $mapSikdInfo;
    }
    
    private function populateSikdSubSkpdInfo($sikdInfoList){
        $mapSikdInfo = []; $subList=[];
        foreach ($sikdInfoList as $sikdInfoBlock) {
            //PROGRAM
            $infoList['id_sikd_sub_skpd'] = $sikdInfoBlock->id_sikd_sub_skpd;
            $infoList['kode'] = $sikdInfoBlock->kode;
            $infoList['nama'] = $sikdInfoBlock->nama;
            $subList[$sikdInfoBlock->sikd_satker_id][$sikdInfoBlock->id_sikd_sub_skpd] = $infoList;
        }
        $mapSikdInfo['sub_skpd'] = $subList;
        return $mapSikdInfo;
    }*/
}