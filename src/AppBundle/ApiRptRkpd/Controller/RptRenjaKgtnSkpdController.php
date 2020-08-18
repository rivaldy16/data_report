<?php
namespace AppBundle\ApiRptRkpd\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use AppBundle\Lib\LivedRestClient;

class RptRenjaKgtnSkpdController extends Controller
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
        $idRenja = $this->request->query->get("id_renja");
        $idRenjaAnggaran = $this->request->query->get("id_renja_anggaran");
        $tahun = $this->request->query->get("tahun");

        $param = [
            'jns_report' => $jnsRpt,
            'tahun' => $tahun,
            'id_renja' => $idRenja,
            'id_renja_anggaran' => $idRenjaAnggaran
        ];
        
        $tahun = $param['tahun'];
        
        $this->restClient->setBaseUri($this->uriRestRenja);
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


        //CONTAINS SIKD BIDANG ID
        $mapRkpdSikdBidangId = [];
        $i = 0;
        foreach ($rkpdRepHandler as $rkpdRepId) {
            $mapRkpdSikdBidangId[$i] =  $rkpdRepId->sikd_bidang_id_sikd_bidang;
            $i++;
        }
        //print_r($mapRkpdSikdBidangId);exit;
        $ids2 = implode(',',  array_unique($mapRkpdSikdBidangId));
        $paramIn2 = ['ids' => $ids2]; $paramIn2 ['parents']=3;
        $sikdRkpdBidMstr = $this->restClient->getCollection("$tahun/sikdbidangs", $paramIn2);//return  $sikdRkpdBidMstr;
        $mapSikdMstr = $this->populateSikdMstrInfo($sikdRkpdBidMstr);


        //CONTAINS SIKD'S KGTN AND PROG INFO 
        $mapRkpdSikdKgtnId = [];
        $i = 0;
        foreach ($rkpdRepHandler as $rkpdRepId) {
            $mapRkpdSikdKgtnId[$i] =  $rkpdRepId->sikd_kgtn_id_sikd_kgtn;
            $i++;
        }
        //print_r($rkpdRepId->sikd_kgtn_id_sikd_kgtn);exit;
        $ids = implode(',', array_unique($mapRkpdSikdKgtnId));
        $paramIn = ['ids' => $ids];$paramIn ['parents']=3;
        $sikdRkpd = $this->restClient->getCollection("$tahun/sikdkgtns", $paramIn); //return  $sikdRkpd;
        $mapSikd = $this->populateSikdInfo($sikdRkpd);


        //CONTAINS RENJA ANGGARAN / KGTN ID
        $mapRkpdRenjaAnggId = [];
        $i = 0;
        foreach ($rkpdRepHandler as $rkpdRepId) {
            if ($rkpdRepId->renja_kegiatan_id_renja_kegiatan != null)
            $mapRkpdRenjaAnggId[$i] =  $rkpdRepId->renja_kegiatan_rkpd_sasaran_id;
            $i++;
        }
        //print_r($mapRkpdRenjaAnggId);exit;
        //SET BASE URI TO RENJA DB
        $this->restClient->setBaseUri($this->uriRestRenja);
        $ids4 = implode(',',  array_unique($mapRkpdRenjaAnggId));
        $paramIn4 = ['ids' => $ids4];
        $renjaRkpdInfo = $this->restClient->getCollection("$tahun/renjablnjlangsungs", $paramIn4);
        //print_r($renjaRkpdInfo);exit;
        $mapRenjaRkpdInfo = $this->populateRenjaRkpdInfo($renjaRkpdInfo);


        foreach ($rkpdReports as &$value1) {

            $value1->sikd_satker_kode = $mapSikdSatkerMstr['satker'][$value1->sikd_satker_id_sikd_satker]['kode'];
            $value1->sikd_satker_nama = $mapSikdSatkerMstr['satker'][$value1->sikd_satker_id_sikd_satker]['nama'];
            if($value1->sikd_bidang_id_sikd_bidang==''){
                $value1->sikd_sub_skpd_nama = $this->sikd_sub_skpd_nama;
                $value1->sikd_sub_skpd_kode = $mapSikdSatkerMstr['satker'][$value1->sikd_satker_id]['kode'];
            } else {
                $value1->sikd_sub_skpd_nama     = 'Kode SKPD';
                $value1->sikd_sub_skpd_kode     = 'Nama SKPD';
            }
            $value1->sikd_bidang_kd_bidang = $mapSikdMstr['bidang'][$value1->sikd_bidang_id_sikd_bidang]['kd_bidang'];
            $value1->sikd_bidang_nm_bidang = $mapSikdMstr['bidang'][$value1->sikd_bidang_id_sikd_bidang]['nm_bidang'];
            $value1->sikd_prog_nm_prog  = $mapSikd['prog'][$value1->sikd_kgtn_id_sikd_kgtn]['nm_prog'];
            $value1->sikd_prog_kd_kgtn  = $mapSikd['kgtn'][$value1->sikd_kgtn_id_sikd_kgtn]['kd_kgtn'];
            $value1->sikd_prog_nm_kgtn  = $mapSikd['kgtn'][$value1->sikd_kgtn_id_sikd_kgtn]['nm_kgtn'];
            if ($value1->renja_kegiatan_rkpd_sasaran_id!=''){
                $value1->rkpd_sasaran_no_sasaran = $mapRenjaRkpdInfo['rkpd_sasaran'][$value1->renja_kegiatan_rkpd_sasaran_id]['no_sasaran'];
                $value1->rkpd_sasaran_uraian_sasaran = $mapRenjaRkpdInfo['rkpd_sasaran'][$value1->renja_kegiatan_rkpd_sasaran_id]['uraian_sasaran'];
            }else{
                $value1->rkpd_sasaran_no_sasaran = '';
                $value1->rkpd_sasaran_uraian_sasaran = '';
            }

        }

        return $rkpdReports;
    }

    private function populateSikdSatkerInfo($sikdInfoList){
        $mapSikdInfo = []; $satkerList=[];
        foreach ($sikdInfoList as $sikdInfoBlock) {
            //PROGRAM
            $infoList['id_sikd_satker'] = $sikdInfoBlock->id_sikd_satker;
            $infoList['kode'] = $sikdInfoBlock->kode;
            $infoList['nama'] = $sikdInfoBlock->nama;
            $satkerList[$sikdInfoBlock->id_sikd_satker] = $infoList;
        }
        $mapSikdInfo['satker'] = $satkerList;
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

    private function populateSikdInfo($sikdInfoList){
        $mapSikdInfo = []; $kgtnList=[]; $progList=[]; $bidangList=[]; $urusanList=[];
        foreach ($sikdInfoList as $sikdInfoBlock) {
            //KEGIATAN
            $infoListKgtn['id_sikd_kgtn'] = $sikdInfoBlock->id_sikd_kgtn;
            $infoListKgtn['nm_kgtn'] = $sikdInfoBlock->nm_kgtn;
            if(strval($sikdInfoBlock->kd_kgtn)==4){
                $infoListKgtn['kd_kgtn'] = substr($sikdInfoBlock->kd_kgtn, -2);
            } else {
                $infoListKgtn['kd_kgtn'] = substr($sikdInfoBlock->kd_kgtn, -3);
            }
            $kgtnList[$sikdInfoBlock->id_sikd_kgtn] = $infoListKgtn;
            //PROGRAM
            $infoListProg['id_sikd_prog'] = $sikdInfoBlock->sikd_prog_id;
            $infoListProg['kd_prog'] = $sikdInfoBlock->sikd_prog_kd_prog;
            $infoListProg['nm_prog'] = $sikdInfoBlock->sikd_prog_nm_prog;
            $progList[$sikdInfoBlock->id_sikd_kgtn] = $infoListProg;
        }
        $mapSikdInfo['kgtn'] = $kgtnList;
        $mapSikdInfo['prog'] = $progList;
        return $mapSikdInfo;
    }

    private function populateRenjaRkpdInfo($sikdInfoList){
        $mapSikdInfo = []; $renjaList=[]; $rkpdList=[]; $rkpdListSasaran=[];
        //print_r($sikdInfoList);exit;
        foreach ($sikdInfoList as $sikdInfoBlock) {
            //RENJA
            $infoListRenja['id_renja_anggaran'] = $sikdInfoBlock->id_renja_anggaran;
            $infoListRenja['tgt_anggaran_thn_ini'] = $sikdInfoBlock->tgt_anggaran_thn_ini;
            $infoListRenja['tgt_anggaran_thn_dpn'] = $sikdInfoBlock->tgt_anggaran_thn_dpn;
            $infoListRenja['tgt_anggaran_renstra'] = $sikdInfoBlock->tgt_anggaran_renstra;
            $infoListRenja['rls_anggaran_sd_thn_lalu'] = $sikdInfoBlock->rls_anggaran_sd_thn_lalu;
            $infoListRenja['jml_anggaran_rkpd'] = $sikdInfoBlock->jml_anggaran_rkpd;
            $infoListRenja['jns_kgtn'] = $sikdInfoBlock->jns_kgtn;
            $infoListRenja['lokasi_kgtn'] = $sikdInfoBlock->lokasi_kgtn;
            $renjaList[$sikdInfoBlock->id_renja_anggaran] = $infoListRenja;

            $infoListRkpd['id_rkpd_prioritas_kab'] = $sikdInfoBlock->rkpd_prioritas_kab_id;
            $infoListRkpd['no_prioritas'] = $sikdInfoBlock->no_prioritas;
            $infoListRkpd['nm_program'] = $sikdInfoBlock->nm_program;
            $rkpdList[$sikdInfoBlock->rkpd_prioritas_kab_id] = $infoListRkpd;

            $infoListRkpd2['id_rkpd_sasaran'] = $sikdInfoBlock->rkpd_sasaran_id;
            $infoListRkpd2['no_sasaran'] = $sikdInfoBlock->no_sasaran;
            $infoListRkpd2['uraian_sasaran'] = $sikdInfoBlock->uraian_sasaran;
            $rkpdListSasaran[$sikdInfoBlock->rkpd_sasaran_id] = $infoListRkpd2;

        }
        $mapSikdInfo['renja'] = $renjaList;
        $mapSikdInfo['rkpd'] = $rkpdList;
        $mapSikdInfo['rkpd_sasaran'] = $rkpdListSasaran;

        return $mapSikdInfo;
    }

}