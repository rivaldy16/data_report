<?php
namespace AppBundle\ApiRptRkpd\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use AppBundle\Lib\LivedRestClient;

class RptRkpdLampPermen231cController extends Controller
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
        $tahun = $this->request->query->get("tahun");
        $param = [
            'jns_report' => $jnsRpt,
        ];
        
        /*$this->restClient->setBaseUri($this->uriRestRenja);
        $rkpdReports = $this->restClient->getCollection("$tahun/renjareports", $param);
        $this->restClient->setBaseUri($this->uriRestSikd);

        $rkpdRepHandler = $rkpdReports;

        //CONTAINS SIKD SATKER ID
        $mapRkpdSikdSatkerId = [];
        $i = 0;
        foreach ($rkpdRepHandler as $rkpdRepId) {
            $mapRkpdSikdSatkerId[$i] =  $rkpdRepId->sikd_satker_id_sikd_satker;
            $i++;
        }
        $ids3 = implode(',',  array_unique($mapRkpdSikdSatkerId));
        $paramIn3 = ['ids' => $ids3];
        $sikdSatkerRkpdMstr = $this->restClient->getCollection("$tahun/sikdsatkers", $paramIn3);
        $mapSikdSatkerMstr = $this->populateSikdSatkerInfo($sikdSatkerRkpdMstr);


        foreach ($rkpdReports as &$value1) {

            $value1->sikd_satker_kode = $mapSikdSatkerMstr['satker'][$value1->sikd_satker_id_sikd_satker]['kode'];
            $value1->sikd_satker_nama = $mapSikdSatkerMstr['satker'][$value1->sikd_satker_id_sikd_satker]['nama'];
           
        }*/

        $this->restClient->setBaseUri($this->uriRestRkpd);
        $rkpdReports = $this->restClient->getCollection("$tahun/rkpdreports", $param);

        return $rkpdReports;
    }

    /*private function populateSikdSatkerInfo($sikdInfoList){
        $mapSikdInfo = []; $satkerList=[];
        foreach ($sikdInfoList as $sikdInfoBlock) {
            //PROGRAM
            $infoList['kode'] = $sikdInfoBlock->kode;
            $infoList['nama'] = $sikdInfoBlock->nama;
            $satkerList[$sikdInfoBlock->id_sikd_satker] = $infoList;
        }
        $mapSikdInfo['satker'] = $satkerList;
        return $mapSikdInfo;
    }*/
}