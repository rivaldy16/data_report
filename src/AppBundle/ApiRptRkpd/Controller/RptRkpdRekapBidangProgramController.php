<?php
namespace AppBundle\ApiRptRkpd\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use AppBundle\Lib\LivedRestClient;

class RptRkpdRekapBidangProgramController extends Controller
{
    private $uriRestRkpd;
    private $uriRestSikd;
    private $restClient;
    private $kdTenant;
    private $uriRestRenja;
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
        
        $this->restClient->setBaseUri($this->uriRestSikd);
        
        $rkpdRepHandler = $rkpdReports;
        //CONTAINS SIKD PROG ID
        $mapRkpdSikdProgId = [];
        $i = 0;
        foreach ($rkpdRepHandler as $rkpdRepId) {
            $mapRkpdSikdProgId[$i] =  $rkpdRepId->sikd_prog_id;
            $i++;
        }
        //CONTAINS SIKD BIDANG ID
        $mapRkpdSikdBidangId = [];
        $i = 0;
        foreach ($rkpdRepHandler as $rkpdRepId) {
            $mapRkpdSikdBidangId[$i] =  $rkpdRepId->sikd_bidang_id;
            $i++;
        }
        //CONTAINS SIKD'S INFO
        $ids = implode(',', array_unique($mapRkpdSikdProgId));
        $paramIn = ['ids' => $ids];$paramIn ['parents']=3;
        $sikdRkpd = $this->restClient->getCollection("$tahun/sikdprogs", $paramIn);
        $mapSikd = $this->populateSikdInfo($sikdRkpd);
//         return $mapSikd;
        
        $ids2 = implode(',',  array_unique($mapRkpdSikdBidangId));
        $paramIn2 = ['ids' => $ids2]; $paramIn2 ['parents']=3;
        $sikdRkpdBidMstr = $this->restClient->getCollection("$tahun/sikdbidangs", $paramIn2);
        $mapSikdMstr = $this->populateSikdMstrInfo($sikdRkpdBidMstr);
        
        foreach ($rkpdReports as &$value1) {
            $value1->sikd_bidang_id_sikd_bidang	= $mapSikdMstr['bidang'][$value1->sikd_bidang_id]['id_sikd_bidang'];
            $value1->sikd_bidang_kd_bidang		= $mapSikdMstr['bidang'][$value1->sikd_bidang_id]['kd_bidang'];
            $value1->sikd_bidang_nm_bidang		= $mapSikdMstr['bidang'][$value1->sikd_bidang_id]['nm_bidang'];
            $value1->sikd_prog_id_sikd_prog		= $mapSikd['prog'][$value1->sikd_prog_id]['id_sikd_prog'];
            $value1->sikd_prog_kd_prog			= $mapSikd['prog'][$value1->sikd_prog_id]['kd_prog'];
            $value1->sikd_prog_nm_prog			= $mapSikd['prog'][$value1->sikd_prog_id]['nm_prog'];
            $value1->sikd_urusan_id_sikd_urusan	= $mapSikdMstr['bidang'][$value1->sikd_bidang_id]['id_sikd_urusan'];
            $value1->sikd_urusan_kd_urusan		= $mapSikdMstr['bidang'][$value1->sikd_bidang_id]['kd_urusan'];
            $value1->sikd_urusan_nm_urusan		= $mapSikdMstr['bidang'][$value1->sikd_bidang_id]['nm_urusan'];
            $value1->rkpd_program_tgt_anggaran_rpjmd		= doubleval($value1->rkpd_program_tgt_anggaran_rpjmd);
            $value1->rkpd_program_rls_anggaran_sd_thn_lalu	= doubleval($value1->rkpd_program_rls_anggaran_sd_thn_lalu);
            $value1->rkpd_program_pagu_indikatif1			= doubleval($value1->rkpd_program_pagu_indikatif1);
            $value1->rkpd_kegiatan_jml_anggaran_rkpd		= doubleval($value1->rkpd_kegiatan_jml_anggaran_rkpd);
            $value1->rkpd_program_pagu_indikatif			= doubleval($value1->rkpd_program_pagu_indikatif);
       }
       return $rkpdReports;*/
    }

    /*private function populateSikdInfo($sikdInfoList){
        $mapSikdInfo = []; $kgtnList=[]; $progList=[]; $bidangList=[]; $urusanList=[];
        foreach ($sikdInfoList as $sikdInfoBlock) {
            //PROGRAM
            $infoListProg['id_sikd_prog'] = $sikdInfoBlock->id_sikd_prog;
            $infoListProg['kd_prog'] = $sikdInfoBlock->kd_prog;
            $infoListProg['nm_prog'] = $sikdInfoBlock->nm_prog;
            $progList[$sikdInfoBlock->id_sikd_prog] = $infoListProg;
        }
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
    }*/
}