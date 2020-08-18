<?php
namespace AppBundle\ApiRptRenja\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use AppBundle\Lib\LivedRestClient;

class RptRenjaBelanjaLangsungController extends Controller
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
        $jns_report = $this->request->query->get("jns_report");
        $jnsRenja = $this->request->query->get("jns_renja");
        $idRenjaKgtn = $this->request->query->get("id_renja_kgtn");
        $idRenja = $this->request->query->get("id_renja");
        $idSatker = $this->request->query->get("sikd_satker_id");
        $tahun = $this->request->query->get("tahun");
        $idKgtn = $this->request->query->get("id_kgtn");
        $idSubUnit = $this->request->query->get("sikd_sub_skpd_id");
        $idBidang = $this->request->query->get("id_bidang");
        $formatRpt = $this->request->query->get("format");
        $idRenjaAnggaran = $this->request->query->get("id_renja_anggaran");
        $param = [
            'id_renja' => $idRenja,
            'id_renja_kgtn' => $idRenjaKgtn,
            'tahun' => $tahun,
            'sikd_satker_id' => $idSatker,
            'sikd_sub_skpd_id' => $idSubUnit,
            'sikd_bidang_id' => $idBidang,
            'jns_renja' => $jnsRenja,
            'id_kgtn' => $idKgtn,
            'format_rpt' => $formatRpt,
            'jns_report' => $jns_report,
            'id_renja_anggaran' => $idRenjaAnggaran
        ];
        
        $this->restClient->setBaseUri($this->uriRestRenja);
        $renjaReports = $this->restClient->getCollection("$tahun/renjareports", $param);
        return $renjaReports;


        //$tahun = $param['tahun'];
       //$idSatker = $param['sikd_satker_id'];
        //$idSubUnit = $param['sikd_sub_skpd_id'];
        //$idBidang = $param['sikd_bidang_id'];
        //$formatRpt = $param['format_rpt'];

         //GET SASTKER'S INFO
        /*$nmSatker = ''; $kdSatker = '';
        $this->restClient->setBaseUri($this->uriRestSikd);
        $satker = $this->restClient->getCollection("$tahun/sikdsatkers/$idSatker");
        if ($satker){
            $nmSatker = $satker->nama;
            $kdSatker = $satker->kode;
        }
        
        
        $this->restClient->setBaseUri($this->uriRestRenja);
        $renjaReports = $this->restClient->getCollection("$tahun/renjareports", $param);
//         return $renjaReports;
        $renjaRepHandler = $renjaReports;
        
       
        //GET SUB SKPD'S INFO
        $nmSubSkpd = 'Semua Sub Unit'; $kdSubSkpd = '';
        if($idSubUnit != ''){
            $this->restClient->setBaseUri($this->uriRestSikd);
            $subSkpd = $this->restClient->getCollection("$tahun/sikdskpds/$idSatker/sikdsubskpds/$idSubUnit");
            if ($subSkpd && sizeof($subSkpd) > 1){
                $nmSubSkpd = $subSkpd->nama;
                $kdSubSkpd = $subSkpd->kode;
            }
        }
        
        //CONTAINS RENJA MATA ANGG SIKD REK RINCI OBJ ID
        $mapRenjaMtAggSikdRekRinciObjId = [];
        $i = 0;
        foreach ($renjaRepHandler as $renjaMtAggSikdRekRinciObjId) {
            $mapRenjaMtAggSikdRekRinciObjId[$i] =  $renjaMtAggSikdRekRinciObjId->renja_mata_anggaran_sikd_rek_rincian_obj_id;
            $i++;
        }
        
        //CONTAINS SIKD PROG ID
        $mapRenjaProgSikdProgId = [];
        $i = 0;
        foreach ($renjaRepHandler as $renjaProgIdHandler) {
            $mapRenjaProgSikdProgId[$i] =  $renjaProgIdHandler->renja_program_sikd_prog_id;
            $i++;
        }
        
        //CONTAINS SIKD BIDANG ID
        $mapRenjaSikdBidangId = [];
        $i = 0;
        foreach ($renjaRepHandler as $renjaBidangIdHandler) {
            $mapRenjaSikdBidangId[$i] =  $renjaBidangIdHandler->renja_anggaran_sikd_bidang_id;
            $i++;
        }
        
        //CONTAINS SIKD RINCIAN OBJ'S INFO
        $param2 ['sikd_rek_rinc_obj'] = $mapRenjaMtAggSikdRekRinciObjId;
        $ids = implode(',', $param2["sikd_rek_rinc_obj"]);
        $paramIn = ['ids' => $ids];$paramIn ['parents']=4;
        $sikdRenja = $this->restClient->getCollection("$tahun/sikdrekrincianobjs", $paramIn);
        $mapSikdRekRincObj = $this->populateSikdInfo($sikdRenja);
        
        
        //CONTAINS SIKD'S INFO BIDANG
        $param2 ['sikd_bidang_id'] = $mapRenjaSikdBidangId;
        $ids = implode(',', $param2["sikd_bidang_id"]);
        $paramIn = ['ids' => $ids];
        $sikdRenjaBidang = $this->restClient->getCollection("$tahun/sikdbidangs", $paramIn);
        $mapSikdBidang = $this->populateSikdMstrBidangInfo($sikdRenjaBidang);
        
        //CONTAINS SIKD'S INFO PROG
        $param2 ['sikd_prog_id'] = $mapRenjaProgSikdProgId;
        $ids = implode(',', $param2["sikd_prog_id"]);
        $paramIn = ['ids' => $ids];
        $sikdRenjaProg = $this->restClient->getCollection("$tahun/sikdprogs", $paramIn);
        $mapSikdProg = $this->populateSikdMstrProgInfo($sikdRenjaProg);
        
        $renjaAnggaranForm = array();
        foreach ($renjaReports as &$value1) {
            $value1->sikd_satker_id_sikd_satker = $value1->renja_renja_sikd_satker_id;
            $value1->sikd_satker_kode = $kdSatker;
            $value1->sikd_satker_nama = $nmSatker;
            $value1->sikd_sub_skpd_id_sikd_sub_skpd = $value1->renja_renja_sikd_sub_skpd_id;
            $value1->sikd_sub_skpd_kode = $kdSubSkpd;
            $value1->sikd_sub_skpd_nama = $nmSubSkpd;
            $value1->sikd_bidang_id_sikd_bidang = $mapSikdBidang['bidang'][$value1->renja_anggaran_sikd_bidang_id]['id_sikd_bidang'];
            $value1->sikd_bidang_kd_bidang =$mapSikdBidang['bidang'][$value1->renja_anggaran_sikd_bidang_id]['kd_bidang'];
            $value1->sikd_bidang_nm_bidang = $mapSikdBidang['bidang'][$value1->renja_anggaran_sikd_bidang_id]['nm_bidang'];
            $value1->sikd_rek_akun_id_sikd_rek_akun = $mapSikdRekRincObj['rek_rinc_obj'][$value1->renja_mata_anggaran_sikd_rek_rincian_obj_id]['id_sikd_rek_akun'];
            $value1->sikd_rek_akun_kd_rek_akun = $mapSikdRekRincObj['rek_rinc_obj'][$value1->renja_mata_anggaran_sikd_rek_rincian_obj_id]['kd_rek_akun'];
            $value1->sikd_rek_akun_nm_rek_akun = $mapSikdRekRincObj['rek_rinc_obj'][$value1->renja_mata_anggaran_sikd_rek_rincian_obj_id]['nm_rek_akun'];
            $value1->sikd_rek_kelompok_id_sikd_rek_kelompok = $mapSikdRekRincObj['rek_rinc_obj'][$value1->renja_mata_anggaran_sikd_rek_rincian_obj_id]['id_sikd_rek_klpk'];
            $value1->sikd_rek_kelompok_kd_rek_kelompok = $mapSikdRekRincObj['rek_rinc_obj'][$value1->renja_mata_anggaran_sikd_rek_rincian_obj_id]['kd_rek_klpk'];
            $value1->sikd_rek_kelompok_nm_rek_kelompok = $mapSikdRekRincObj['rek_rinc_obj'][$value1->renja_mata_anggaran_sikd_rek_rincian_obj_id]['nm_rek_klpk'];
            $value1->sikd_rek_jenis_id_sikd_rek_jenis = $mapSikdRekRincObj['rek_rinc_obj'][$value1->renja_mata_anggaran_sikd_rek_rincian_obj_id]['id_sikd_rek_jenis'];
            $value1->sikd_rek_jenis_kd_rek_jenis = $mapSikdRekRincObj['rek_rinc_obj'][$value1->renja_mata_anggaran_sikd_rek_rincian_obj_id]['kd_rek_jenis'];
            $value1->sikd_rek_jenis_nm_rek_jenis = $mapSikdRekRincObj['rek_rinc_obj'][$value1->renja_mata_anggaran_sikd_rek_rincian_obj_id]['nm_rek_jenis'];
            $value1->sikd_rek_obj_id_sikd_rek_obj = $mapSikdRekRincObj['rek_rinc_obj'][$value1->renja_mata_anggaran_sikd_rek_rincian_obj_id]['id_sikd_rek_obj'];
            $value1->sikd_rek_obj_kd_rek_obj = $mapSikdRekRincObj['rek_rinc_obj'][$value1->renja_mata_anggaran_sikd_rek_rincian_obj_id]['kd_rek_obj'];
            $value1->sikd_rek_obj_nm_rek_obj = $mapSikdRekRincObj['rek_rinc_obj'][$value1->renja_mata_anggaran_sikd_rek_rincian_obj_id]['nm_rek_obj'];
            $value1->sikd_rek_rincian_obj_id_sikd_rek_rincian_obj = $mapSikdRekRincObj['rek_rinc_obj'][$value1->renja_mata_anggaran_sikd_rek_rincian_obj_id]['id_sikd_rek_rincian_obj'];
            $value1->sikd_rek_rincian_obj_kd_rek_rincian_obj = $mapSikdRekRincObj['rek_rinc_obj'][$value1->renja_mata_anggaran_sikd_rek_rincian_obj_id]['kd_rek_rincian_obj'];
            $value1->sikd_rek_rincian_obj_nm_rek_rincian_obj = $mapSikdRekRincObj['rek_rinc_obj'][$value1->renja_mata_anggaran_sikd_rek_rincian_obj_id]['nm_rek_rincian_obj'];
            $value1->sikd_prog_nm_prog = $mapSikdProg['prog'][$value1->renja_program_sikd_prog_id]['nm_prog'];
            $value1->renja_mata_anggaran_harga = doubleval($value1->renja_mata_anggaran_harga);
            $value1->renja_mata_anggaran_jumlah = doubleval($value1->renja_mata_anggaran_jumlah);
            $value1->renja_mata_anggaran_volume = doubleval($value1->renja_mata_anggaran_volume);
        }
        return $renjaReports;*/
    }
    
    /*private function populateSikdInfo($sikdInfoList){
        $mapSikdInfo = []; $rekRincObjList=[];
        foreach ($sikdInfoList as $sikdInfoBlock) {
            //RINCIAN OBJEK - REK OBJEK - REK AKUN - REK JENIS - REK KELOMPOK
            $infoListRincObj['id_sikd_rek_rincian_obj'] = $sikdInfoBlock->id_sikd_rek_rincian_obj;
            $infoListRincObj['kd_rek_rincian_obj'] = $sikdInfoBlock->kd_rek_rincian_obj;
            $infoListRincObj['nm_rek_rincian_obj'] = $sikdInfoBlock->nm_rek_rincian_obj;
            //REKENING OBJEK
            $infoListRincObj['id_sikd_rek_obj'] = $sikdInfoBlock->sikd_rek_obj_id;
            $infoListRincObj['kd_rek_obj'] = $sikdInfoBlock->sikd_rek_obj_kd_rek_obj;
            $infoListRincObj['nm_rek_obj'] = $sikdInfoBlock->sikd_rek_obj_nm_rek_obj;
            //REKENING AKUN
            $infoListRincObj['id_sikd_rek_akun'] = $sikdInfoBlock->sikd_rek_akun_id;
            $infoListRincObj['nm_rek_akun'] = $sikdInfoBlock->sikd_rek_akun_nm_rek_akun;
            $infoListRincObj['kd_rek_akun'] = $sikdInfoBlock->sikd_rek_akun_kd_rek_akun;
            //REKENING JENIS
            $infoListRincObj['id_sikd_rek_jenis'] = $sikdInfoBlock->sikd_rek_jenis_id;
            $infoListRincObj['nm_rek_jenis'] = $sikdInfoBlock->sikd_rek_jenis_nm_rek_jenis;
            $infoListRincObj['kd_rek_jenis'] = $sikdInfoBlock->sikd_rek_jenis_kd_rek_jenis;
            //REKENING KELOMPOK
            $infoListRincObj['id_sikd_rek_klpk'] = $sikdInfoBlock->sikd_rek_kelompok_id;
            $infoListRincObj['nm_rek_klpk'] = $sikdInfoBlock->sikd_rek_kelompok_nm_rek_kelompok;
            $infoListRincObj['kd_rek_klpk'] = $sikdInfoBlock->sikd_rek_kelompok_kd_rek_kelompok;
            $rekRincObjList[$sikdInfoBlock->id_sikd_rek_rincian_obj] = $infoListRincObj;
        }
        $mapSikdInfo['rek_rinc_obj'] = $rekRincObjList;
        return $mapSikdInfo;
    }
    
    private function populateSikdMstrBidangInfo($sikdInfoList){
        $mapSikdInfo = []; $bidangList=[]; $urusanList=[];
        foreach ($sikdInfoList as $sikdInfoBlock) {
            //BIDANG
            $infoListBidang['id_sikd_bidang'] = $sikdInfoBlock->id_sikd_bidang;
            $infoListBidang['kd_bidang'] = $sikdInfoBlock->kd_bidang;
            $infoListBidang['nm_bidang'] = $sikdInfoBlock->nm_bidang;
            $bidangList[$sikdInfoBlock->id_sikd_bidang] = $infoListBidang;
        }
        $mapSikdInfo['bidang'] = $bidangList;
        return $mapSikdInfo;
    }
    
    private function populateSikdMstrProgInfo($sikdInfoList){
        $mapSikdInfo = []; $progList=[];
        foreach ($sikdInfoList as $sikdInfoBlock) {
            //PROGRAM
            $infoListProg['id_sikd_prog'] = $sikdInfoBlock->id_sikd_prog;
            $infoListProg['kd_prog'] = $sikdInfoBlock->kd_prog;
            $infoListProg['nm_prog'] = $sikdInfoBlock->nm_prog;
            $progList[$sikdInfoBlock->id_sikd_prog] = $infoListProg;
        }
        $mapSikdInfo['prog'] = $progList;
        return $mapSikdInfo;
    }*/
    
}