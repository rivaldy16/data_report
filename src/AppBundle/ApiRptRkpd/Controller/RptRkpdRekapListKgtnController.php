<?php
namespace AppBundle\ApiRptRkpd\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use AppBundle\Lib\LivedRestClient;

class RptRkpdRekapListKgtnController extends Controller
{
    private $uriRestRkpd;
    private $uriRestSikd;
    private $restClient;
    private $kdTenant;
    private $uriRestRenja;
    private $sikd_sub_skpd_nama = 'SKPD INDUK';
    private $uriRestPpas;
    private $sub_flag = 'sub_rekap_list_kgtn_skpd';
    
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
        //$idSubSkpd = $this->request->query->get("sikd_sub_skpd_id");
        $idSatker = $this->request->query->get("sikd_satker_id");
        //$format = $this->request->query->get("format");
        $tahun = $this->request->query->get("tahun");
        //$sub_flag = $this->sub_flag;
        $param = [
            'jns_report' => $jnsRpt,
            'id_rkpd' => $idRkpd,
            'sikd_satker_id' => $idSatker,
            /*'sikd_sub_skpd_id' => $idSubSkpd,*/
            'tahun' => $tahun ,
            'prioritas' => $kdPrioritas,
            /*'format' => $format*/
        ];
        
        $tahun = $param['tahun'];
        
        $this->restClient->setBaseUri($this->uriRestRkpd);
        
        $rkpdReports = $this->restClient->getCollection("$tahun/rkpdreports", $param);
        //print_r($rkpdReports);exit;
        /*$param['jns_report'] = $sub_flag;
        $rkpdReportsSubQ = $this->populateSubRkpd($this->restClient->getCollection("$tahun/rkpdreports", $param));
        $rkpdReportsSubQ['default']='-';
        //SET BASE URI TO SIKD DB
        $this->restClient->setBaseUri($this->uriRestSikd);
        
        $rkpdRepHandler = $rkpdReports;
        
        //CONTAINS SIKD BIDANG ID
        $mapRkpdSikdBidangId = [];
        $i = 0;
        foreach ($rkpdRepHandler as $rkpdRepId) {
            $mapRkpdSikdBidangId[$i] =  $rkpdRepId->sikd_bidang_id_sikd_bidang;
            $i++;
        }
        
        //CONTAINS SIKD KGTN ID
        $mapRkpdSikdKgtnId = [];
        $i = 0;
        foreach ($rkpdRepHandler as $rkpdRepId) {
            $mapRkpdSikdKgtnId[$i] =  $rkpdRepId->sikd_kgtn_id_sikd_kgtn;
            $i++;
        }
        
        //GET SASTKER'S INFO
        $nmSatker = ''; $kdSatker = ''; $kdIndukBidangSatker = ''; $nmBidangIndukSatker = '';
        $this->restClient->setBaseUri($this->uriRestSikd);
        $satker = $this->restClient->getCollection("$tahun/sikdsatkers/$idSatker");
        if ($satker){
            $nmSatker               = $satker->nama;
            $kdSatker               = $satker->kode;
            $kdIndukBidangSatker    = $satker->kd_bidang_induk;
            $nmBidangIndukSatker    = $satker->sikd_bidang_nm_bidang;
        }
        //GET SUB SKPD'S INFO
        $nmSubSkpd = 'SEMUA UNIT'; $kdSubSkpd = '';
        if($idSubSkpd != ''){
            $this->restClient->setBaseUri($this->uriRestSikd);
            $subSkpd = $this->restClient->getCollection("$tahun/sikdskpds/$idSatker/sikdsubskpds/$idSubSkpd");
            if ($subSkpd && sizeof($subSkpd) > 1){
                $nmSubSkpd = $subSkpd->nama;
                $kdSubSkpd = $subSkpd->kode;
            }
        }
        
        //CONTAINS SIKD'S BIDANG INFO
        $ids2 = implode(',',  array_unique($mapRkpdSikdBidangId));
        $paramIn2 = ['ids' => $ids2]; $paramIn2 ['parents']=3;
        $sikdRkpdBidMstr = $this->restClient->getCollection("$tahun/sikdbidangs", $paramIn2);
        $mapSikdBidMstr = $this->populateSikdMstrInfo($sikdRkpdBidMstr);
        
        //CONTAINS SIKD'S KGTN INFO
        $ids3 = implode(',',  array_unique($mapRkpdSikdKgtnId));
        $paramIn3 = ['ids' => $ids3]; $paramIn3 ['parents']=3;
        $sikdRkpdKgtnMstr = $this->restClient->getCollection("$tahun/sikdkgtns", $paramIn3);
        $mapSikd = $this->populateSikdInfo($sikdRkpdKgtnMstr);
        
        foreach ($rkpdReports as &$value1) {
            $value1->sikd_satker_id_sikd_satker	= $value1->sikd_satker_id;
            $value1->sikd_satker_kode		    = $kdSatker;
            $value1->sikd_satker_nama		    = $nmSatker;
            if($idSatker!=''){
                if($idSubSkpd!='%'){
                    $value1->sikd_sub_skpd_id_sikd_sub_skpd     = $idSubSkpd;
                }
                if($idSubSkpd!=''&&$idSubSkpd!='%'){
                    $value1->sikd_sub_skpd_nama     = $nmSubSkpd;
                    $value1->sikd_sub_skpd_kode     = $kdSubSkpd;
                } elseif($idSubSkpd!=''&&$idSubSkpd=='%'){
                    $value1->sikd_sub_skpd_kode                 = '';
                    $value1->sikd_sub_skpd_nama                 = 'SEMUA UNIT';    
                } elseif($idSubSkpd==''){
                    $value1->sikd_sub_skpd_kode                 = $kdSatker;
                    $value1->sikd_sub_skpd_nama                 = 'SKPD INDUK';
                }
            } else {
                $value1->sikd_sub_skpd_id_sikd_sub_skpd     = '';
                $value1->sikd_sub_skpd_kode                 = '';
                $value1->sikd_sub_skpd_nama                 = '';
            }
            $value1->sikd_sub_skpd_kode_     = $kdSubSkpd;
            $value1->sikd_sub_skpd_nama_     = $nmSubSkpd;
            
            $value1->sikd_bidang_induk_kd_bidang    = $kdIndukBidangSatker;
            $value1->sikd_bidang_induk_nm_bidang    = $nmBidangIndukSatker;
            $value1->sikd_urusan_id_sikd_urusan     = $mapSikdMstr['bidang'][$value1->sikd_bidang_id_sikd_bidang]['id_sikd_urusan'];
            $value1->sikd_urusan_kd_urusan          = $mapSikdMstr['bidang'][$value1->sikd_bidang_id_sikd_bidang]['kd_urusan'];
            $value1->sikd_urusan_nm_urusan          = $mapSikdMstr['bidang'][$value1->sikd_bidang_id_sikd_bidang]['nm_urusan'];
            $value1->sikd_bidang_kd_bidang          = $mapSikdMstr['bidang'][$value1->sikd_bidang_id_sikd_bidang]['kd_bidang'];
            $value1->sikd_bidang_nm_bidang          = $mapSikdMstr['bidang'][$value1->sikd_bidang_id_sikd_bidang]['nm_bidang'];
            $value1->sikd_prog_kd_prog              = $mapSikdMstr['bidang'][$value1->sikd_bidang_id_sikd_bidang]['kd_bidang'].'.'.$mapSikd['prog'][$value1->sikd_prog_id_sikd_prog]['kd_prog'];
            $value1->sikd_prog_nm_prog              = $mapSikd['prog'][$value1->sikd_prog_id_sikd_prog]['nm_prog'];
            $value1->sikd_kgtn_kd_kgtn              = $mapSikdMstr['bidang'][$value1->sikd_bidang_id_sikd_bidang]['kd_bidang'].'.'.$mapSikd['prog'][$value1->sikd_prog_id_sikd_prog]['kd_prog'].'.'.$mapSikd['kgtn'][$value1->sikd_kgtn_id_sikd_kgtn]['kd_kgtn'];
            $value1->sikd_kgtn_nm_kgtn              = $mapSikd['kgtn'][$value1->sikd_kgtn_id_sikd_kgtn]['nm_kgtn'];
            $value1->rekap_rkpd_kgtn_target_kgtn    = $rkpdReportsSubQ['default'];
            if (array_key_exists($value1->kd_kegiatan, $rkpdReportsSubQ) ) {
                if (array_key_exists($value1->prioritas, $rkpdReportsSubQ[$value1->kd_kegiatan]) ) {
                    if (array_key_exists($value1->sikd_satker_id, $rkpdReportsSubQ[$value1->kd_kegiatan][$value1->prioritas]) ) {
                        if (array_key_exists($value1->sikd_sub_skpd_id, $rkpdReportsSubQ[$value1->kd_kegiatan][$value1->prioritas][$value1->sikd_satker_id]) ) {
                            if (array_key_exists($value1->target_kgtn, $rkpdReportsSubQ[$value1->kd_kegiatan][$value1->prioritas][$value1->sikd_satker_id][$value1->sikd_sub_skpd_id]) ) {
                                $value1->rekap_rkpd_kgtn_target_kgtn    = $rkpdReportsSubQ[$value1->kd_kegiatan][$value1->prioritas][$value1->sikd_satker_id][$value1->sikd_sub_skpd_id];
                            }
                        }
                    }
                }
            }
            $value1->jml_plafon                     = doubleval($value1->jml_plafon);
        }*/

        
        return $rkpdReports;
    }
    
    private function populateSikdMstrInfo($sikdInfoList){
        $mapSikdInfo = []; $bidangList=[]; $urusanList=[];
        foreach ($sikdInfoList as $sikdInfoBlock) {
            //BIDANG
            $infoListBidang['id_sikd_bidang'] = $sikdInfoBlock->id_sikd_bidang;
            $infoListBidang['kd_bidang'] = $sikdInfoBlock->kd_bidang;
            $infoListBidang['nm_bidang'] = $sikdInfoBlock->nm_bidang;
            $infoListBidang['id_sikd_urusan'] = $sikdInfoBlock->sikd_urusan_id;
            $infoListBidang['nm_urusan'] = $sikdInfoBlock->sikd_urusan_nm_urusan;
            $infoListBidang['kd_urusan'] = $sikdInfoBlock->sikd_urusan_kd_urusan;
            $bidangList[$sikdInfoBlock->id_sikd_bidang] = $infoListBidang;
        }
        $mapSikdInfo['bidang'] = $bidangList;
        return $mapSikdInfo;
    }
    
    private function populateSikdInfo($sikdInfoList){
        $mapSikdInfo = []; $kgtnList=[]; $progList=[]; $bidangList=[]; $urusanList=[];
        foreach ($sikdInfoList as $sikdInfoBlock) {
            //KEGIATAN
            $infoListKgtn['id_sikd_kgtn'] = $sikdInfoBlock->id_sikd_kgtn;
            $infoListKgtn['nm_kgtn'] = $sikdInfoBlock->nm_kgtn;
            $infoListKgtn['kd_kgtn'] = substr($sikdInfoBlock->kd_kgtn, 3, 3);
            $kgtnList[$sikdInfoBlock->id_sikd_kgtn] = $infoListKgtn;
            //PROGRAM
            $infoListProg['id_sikd_prog'] = $sikdInfoBlock->sikd_prog_id;
            $infoListProg['kd_prog'] = $sikdInfoBlock->sikd_prog_kd_prog;
            $infoListProg['nm_prog'] = $sikdInfoBlock->sikd_prog_nm_prog;
            $progList[$sikdInfoBlock->sikd_prog_id] = $infoListProg;
        }
        $mapSikdInfo['kgtn'] = $kgtnList;
        $mapSikdInfo['prog'] = $progList;
        return $mapSikdInfo;
    }
    
    private function populateSubRkpd($sikdInfoList){
        $mapSikdInfo = []; $rkpdList=[];
        foreach ($sikdInfoList as $sikdInfoBlock) {
            //SUB RKPD
            $rkpdList[$sikdInfoBlock->kd_kegiatan][$sikdInfoBlock->prioritas][$sikdInfoBlock->sikd_satker_id][$sikdInfoBlock->sikd_sub_skpd_id][$sikdInfoBlock->target_kgtn] = $sikdInfoBlock->group_details;
        }
        $mapSikdInfo = $rkpdList;
        return $mapSikdInfo;
    }
}