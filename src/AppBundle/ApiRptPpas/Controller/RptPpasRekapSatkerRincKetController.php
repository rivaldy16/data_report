<?php
namespace AppBundle\ApiRptPpas\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use AppBundle\Lib\LivedRestClient;

class RptPpasRekapSatkerRincKetController extends Controller
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
        $idSatker = $this->request->query->get("id_satker");
        $idSubUnit = $this->request->query->get("id_sub_unit");
	    
		$param = [
            'jns_report' => $jnsRpt, 
            'id_ppas' => $idPpas,
            'tahun' => $tahun,
            'id_satker' => $idSatker,
            'id_sub_unit' => $idSubUnit
        ];

        $this->restClient->setBaseUri($this->uriRestPpas);
        $ppasReports = $this->restClient->getCollection("$tahun/ppasreports", $param);
        return $ppasReports;

        /*$this->restClient->setBaseUri($this->uriRestPpas);
        $ppasReports = $this->restClient->getCollection("$tahun/ppasreports", $param);

        $PpasRepHandler = $ppasReports;
        $mapRkpdKgtnId = [];
        $i = 0;
        foreach ($PpasRepHandler as $RkpdKgtnId) {
            $mapRkpdKgtnId[$i] =  $RkpdKgtnId->ppas_anggaran_kgtn_id;
            $i++;
        }

        $this->restClient->setBaseUri($this->uriRestRkpd);

        $param2 ['rkpd_kgtn_id'] = $mapRkpdKgtnId;
        $ids = implode(',', $param2["rkpd_kgtn_id"]);
        $paramIn = ['ids' => $ids];$paramIn ['parents']=2;
        $RkpdKgtn = $this->restClient->getCollection("$tahun/rkpdblnjlangsungs", $paramIn);
        $mapRkpdKgtn = $this->populateRkpdInfo($RkpdKgtn);

        foreach ($ppasReports as &$value1) {
            if (sizeof($mapRkpdKgtn['rkpd_kgtn_id']) > 0){
                $value1->jml_anggaran_rkpd    = $mapRkpdKgtn['rkpd_kgtn_id'][$value1->ppas_anggaran_kgtn_id]['rkpd_kgtn_id'];
            }else{
                $value1->jml_anggaran_rkpd    = '0';
            }
            
        }
        return $ppasReports;*/
    } 

    /*private function populateRkpdInfo($rkpdInfoList){
        $mapRkpdInfo = []; $RkpdObjList=[];
        foreach ($rkpdInfoList as $rkpdInfoBlock) {
            //RINCIAN OBJEK - REK OBJEK - REK AKUN - REK JENIS - REK KELOMPOK
            $infoListRincObj['rkpd_kgtn_id'] = $rkpdInfoBlock->id_rkpd_anggaran;
            $RkpdObjList[$rkpdInfoBlock->id_rkpd_anggaran] = $infoListRincObj;
        }
        $mapRkpdInfo['rkpd_kgtn_id'] = $RkpdObjList;
        return $mapRkpdInfo;
    }   */
}