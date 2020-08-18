<?php
namespace AppBundle\ApiRptRkpd\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use AppBundle\Lib\LivedRestClient;

class RptRkpdRekapUrusanSatkerController extends Controller
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
        
        $tahun = $param['tahun'];
        
        $this->restClient->setBaseUri($this->uriRestRkpd);
        $rkpdReports = $this->restClient->getCollection("$tahun/rkpdreports", $param);
        return $rkpdReports;

        /*$this->restClient->setBaseUri($this->uriRestRkpd);
        
        $rkpdReports = $this->restClient->getCollection("$tahun/rkpdreports", $param);
        //SET BASE URI TO SIKD DB
        $this->restClient->setBaseUri($this->uriRestSikd);
        
        $rkpdRepHandler = $rkpdReports;
        //CONTAINS SIKD SATKER ID
        $mapRkpdSikdSatkerId = [];
        $i = 0;
        foreach ($rkpdRepHandler as $rkpdRepId) {
            $mapRkpdSikdSatkerId[$i] =  $rkpdRepId->sikd_satker_id_sikd_satker;
            $i++;
        }
        //CONTAINS SIKD BIDANG ID
        $mapRkpdSikdBidangId = [];
        $i = 0;
        foreach ($rkpdRepHandler as $rkpdRepId) {
            $mapRkpdSikdBidangId[$i] =  $rkpdRepId->sikd_bidang_id_sikd_bidang;
            $i++;
        }
        //CONTAINS RENJA ANGGARAN / KGTN ID
        $mapRkpdRenjaAnggId = [];
        $i = 0;
        foreach ($rkpdRepHandler as $rkpdRepId) {
            $mapRkpdRenjaAnggId[$i] =  $rkpdRepId->renja_kegiatan_id_renja_kegiatan;
            $i++;
        }
        
        //CONTAINS SIKD'S BIDANG INFO
        $ids2 = implode(',',  array_unique($mapRkpdSikdBidangId));
        $paramIn2 = ['ids' => $ids2]; $paramIn2 ['parents']=3;
        $sikdRkpdBidMstr = $this->restClient->getCollection("$tahun/sikdbidangs", $paramIn2);
        $mapSikdMstr = $this->populateSikdMstrInfo($sikdRkpdBidMstr);
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
        

        

        //SET BASE URI TO RENJA DB
        $this->restClient->setBaseUri($this->uriRestRenja);
        //CONTAINS SIKD'S BIDANG INFO
        $ids4 = implode(',',  array_unique($mapRkpdRenjaAnggId));
        $paramIn4 = ['ids' => $ids4];
        $renjaRkpdInfo = $this->restClient->getCollection("$tahun/renjablnjlangsungs", $paramIn4);
        $mapRenjaRkpdInfo = $this->populateRenjaInfo($renjaRkpdInfo);
        //         return $mapRenjaRkpdInfo;
        
        foreach ($rkpdReports as &$value1) {
            $value1->sikd_bidang_id_sikd_bidang	= $mapSikdMstr['bidang'][$value1->sikd_bidang_id_sikd_bidang]['id_sikd_bidang'];
            $value1->sikd_bidang_kd_bidang		= $mapSikdMstr['bidang'][$value1->sikd_bidang_id_sikd_bidang]['kd_bidang'];
            $value1->sikd_bidang_nm_bidang		= $mapSikdMstr['bidang'][$value1->sikd_bidang_id_sikd_bidang]['nm_bidang'];
            $value1->sikd_satker_kode		    = $mapSikdSatkerMstr['satker'][$value1->sikd_satker_id_sikd_satker]['kode'];
            $value1->sikd_satker_nama		    = $mapSikdSatkerMstr['satker'][$value1->sikd_satker_id_sikd_satker]['nama'];
            $value1->sikd_urusan_id_sikd_urusan	= $mapSikdMstr['bidang'][$value1->sikd_bidang_id_sikd_bidang]['id_sikd_urusan'];
            $value1->sikd_urusan_kd_urusan		= $mapSikdMstr['bidang'][$value1->sikd_bidang_id_sikd_bidang]['kd_urusan'];
            $value1->sikd_urusan_nm_urusan		= $mapSikdMstr['bidang'][$value1->sikd_bidang_id_sikd_bidang]['nm_urusan'];
            if(array_key_exists($value1->renja_kegiatan_id_renja_kegiatan,$mapRenjaRkpdInfo['renja'])){
                $value1->renja_kegiatan_tgt_anggaran_thn_ini=doubleval($mapRenjaRkpdInfo['renja'][$value1->renja_kegiatan_id_renja_kegiatan]['tgt_anggaran_thn_ini']);
            } else {
                $value1->renja_kegiatan_tgt_anggaran_thn_ini= 0;
            }
            if($value1->sikd_sub_skpd_id==''){
                $value1->sikd_sub_skpd_nama     = $this->sikd_sub_skpd_nama;
                $value1->sikd_sub_skpd_kode     = $mapSikdSatkerMstr['satker'][$value1->sikd_satker_id_sikd_satker]['kode'];
            } else {
                $value1->sikd_sub_skpd_nama     = $mapSikdSubSkpd['sub_skpd'][$value1->sikd_satker_id_sikd_satker][$value1->sikd_sub_skpd_id]['nama'];
                $value1->sikd_sub_skpd_kode     = $mapSikdSubSkpd['sub_skpd'][$value1->sikd_satker_id_sikd_satker][$value1->sikd_sub_skpd_id]['kode'];
            }
            $value1->rkpd_program_pagu_indikatif = doubleval($value1->rkpd_kegiatan_jml_anggaran_rkpd);
            if(array_key_exists($value1->sikd_satker_id_sikd_satker,$mapSikdSubSkpd['sub_skpd'])){
                $value1->jml_sub_unit = 1;
            } else {
                $value1->jml_sub_unit = 0;
            }
            $value1->renja_kegiatan_tgt_anggaran_thn_ini = doubleval('99999'); //this is a dummy date - configure how to get summary on groups
        }
        return $rkpdReports;*/
    }
    
    /*private function populateSikdInfo($sikdInfoList){
        $mapSikdInfo = []; $kgtnList=[]; $progList=[]; $bidangList=[]; $urusanList=[];
        foreach ($sikdInfoList as $sikdInfoBlock) {
            //KEGIATAN
            $infoListKgtn['id_sikd_kgtn'] = $sikdInfoBlock->id_sikd_kgtn;
            $infoListKgtn['nm_kgtn'] = $sikdInfoBlock->nm_kgtn;
            if(strval($sikdInfoBlock->kd_kgtn)==4){
                $infoListKgtn['kd_kgtn'] = substr($kdKgtnTemp, -2);
            } else {
                $infoListKgtn['kd_kgtn'] = substr($kdKgtnTemp, -3);
            }
            $kgtnList[$sikdInfoBlock->id_sikd_kgtn] = $infoListKgtn;
            //PROGRAM
            $infoListProg['id_sikd_prog'] = $sikdInfoBlock->id_sikd_prog;
            $infoListProg['kd_prog'] = $sikdInfoBlock->kd_prog;
            $infoListProg['nm_prog'] = $sikdInfoBlock->nm_prog;
            $progList[$sikdInfoBlock->id_sikd_prog] = $infoListProg;
        }
        $mapSikdInfo['kgtn'] = $kgtnList;
        $mapSikdInfo['prog'] = $progList;
        return $mapSikdInfo;
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
    
    private function populateSikdSatkerInfo($sikdInfoList){
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
    
    private function populateRenjaInfo($sikdInfoList){
        $mapSikdInfo = []; $renjaList=[];
        foreach ($sikdInfoList as $sikdInfoBlock) {
            //RENJA
            $infoList['id_renja_anggaran'] = $sikdInfoBlock->id_renja_anggaran;
            $infoList['tgt_anggaran_thn_ini'] = $sikdInfoBlock->tgt_anggaran_thn_ini;
            $infoList['tgt_anggaran_thn_dpn'] = $sikdInfoBlock->tgt_anggaran_thn_dpn;
            $infoList['tgt_anggaran_renstra'] = $sikdInfoBlock->tgt_anggaran_renstra;
            $infoList['rls_anggaran_sd_thn_lalu'] = $sikdInfoBlock->rls_anggaran_sd_thn_lalu;
            $infoList['jml_anggaran_rkpd'] = $sikdInfoBlock->jml_anggaran_rkpd;
            $renjaList[$sikdInfoBlock->id_renja_anggaran] = $infoList;
        }
        $mapSikdInfo['renja'] = $renjaList;
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