<?php
namespace AppBundle\ApiRptRkpd\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use AppBundle\Lib\LivedRestClient;

class RptRkpdLampPermen231eController extends Controller
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
        $format = $this->request->query->get("format");
        $idRkpd = $this->request->query->get("id_rkpd");
        $tahun = $this->request->query->get("tahun");
        $jnsRkpd = $this->request->query->get("jns_rkpd");
        $param = [
            'jns_report' => $jnsRpt,
            'id_rkpd' => $idRkpd,
            'tahun' => $tahun,
            'format' => $format,
            'jns_rkpd' => $jnsRkpd
        ];
        
        $tahun = $param['tahun'];
        
        $this->restClient->setBaseUri($this->uriRestRkpd);
        $rkpdReports = $this->restClient->getCollection("$tahun/rkpdreports", $param);
        return $rkpdReports;
        
        /*$this->restClient->setBaseUri($this->uriRestRkpd);
        $rkpdReports = $this->restClient->getCollection("$tahun/rkpdreports", $param);
        //return $rkpdReports;

        $rkpdRepHandler = $rkpdReports;

        $mapRkpdRenjaAnggId = [];
        $i = 0;
        foreach ($rkpdRepHandler as $rkpdRepId) {
            $mapRkpdRenjaAnggId[$i] =  $rkpdRepId->renja_kegiatan_id_renja_kegiatan;
            $i++;
        }
        //print_r($mapRkpdRenjaAnggId);exit;
        $this->restClient->setBaseUri($this->uriRestRenja);
        
        $ids4 = implode(',',  array_unique($mapRkpdRenjaAnggId));
        $paramIn4 = ['ids' => $ids4];
        $renjaRkpdInfo = $this->restClient->getCollection("$tahun/renjaanggarans", $paramIn4);
        //print_r($renjaRkpdInfo);exit;
        $mapRkpdRenjaRenjaId = [];
        $i = 0;
        foreach ($renjaRkpdInfo as $rkpdRepId2) {
            $mapRkpdRenjaRenjaId[$i] =  $rkpdRepId2->renja_renja_id;
            $i++;
        }
        //print_r($mapRkpdRenjaRenjaId);exit;
        if ($renjaRkpdInfo!=null){
            $ids5 = implode(',',  array_unique($mapRkpdRenjaRenjaId));
            $renjaRkpdInfo2 = $this->restClient->getCollection("$tahun/renjarenjas/$ids5/renjablnjlangsungs");
            //print_r($renjaRkpdInfo2);exit;

            $mapRkpdSikdSatkerId = [];
            $i = 0;
            foreach ($renjaRkpdInfo2 as $rkpdRepId4) {
                $mapRkpdSikdSatkerId[$i] =  $rkpdRepId4->renja_renja_sikd_satker_id;
                $i++;
            }
            //print_r($mapRkpdSikdSatkerId);exit;

            $this->restClient->setBaseUri($this->uriRestSikd);
            $ids6 = implode(',',  array_unique($mapRkpdSikdSatkerId));
            $paramIn6 = ['ids' => $ids6];
            $renjaRkpdInfo3 = $this->restClient->getCollection("$tahun/sikdsatkers", $paramIn6);

            $mapRkpdSikdSubSkpdId = [];
            $i = 0;
            foreach ($renjaRkpdInfo2 as $rkpdRepId6) {
                $mapRkpdSikdSubSkpdId[$i] =  $rkpdRepId6->renja_renja_sikd_sub_skpd_id;
                $i++;
            }
        }


        foreach ($rkpdReports as &$value1) {
            if ($renjaRkpdInfo != null){
                foreach ($renjaRkpdInfo2 as $rkpdRepId3) {
                    $value1->tgt_anggaran_thn_ini =  doubleval($rkpdRepId3->tgt_anggaran_thn_ini);
                    $value1->tgt_anggaran_thn_dpn =  doubleval($rkpdRepId3->tgt_anggaran_thn_dpn);
                    $value1->tgt_anggaran_renstra =  $rkpdRepId3->tgt_anggaran_renstra;
                    $value1->rls_anggaran_sd_thn_lalu =  $rkpdRepId3->rls_anggaran_sd_thn_lalu;
                    $value1->jml_anggaran_rkpd =  $rkpdRepId3->jml_anggaran_rkpd;
                    $value1->jns_kgtn =  $rkpdRepId3->jns_kgtn;
                    $value1->lokasi_kgtn =  $rkpdRepId3->lokasi_kgtn;
                }

                foreach ($renjaRkpdInfo3 as $rkpdRepId5) {
                    $value1->sikd_satker_id_sikd_satker = $rkpdRepId5->id_sikd_satker;
                    $value1->sikd_satker_kode = $rkpdRepId5->kode;
                    $value1->sikd_satker_nama = $rkpdRepId5->nama;
                    $value1->sikd_sub_skpd_kode = $rkpdRepId5->kode;
                }
            
                $value1->sikd_sub_skpd_id_sikd_sub_skpd = $rkpdRepId6->renja_renja_sikd_sub_skpd_id;
            }
            else{
                $value1->tgt_anggaran_thn_ini = null;
                $value1->tgt_anggaran_thn_dpn = null; 
                $value1->tgt_anggaran_renstra = null;
                $value1->rls_anggaran_sd_thn_lalu = null;
                $value1->jml_anggaran_rkpd = null; 
                $value1->jns_kgtn = null; 
                $value1->lokasi_kgtn = null; 
                $value1->sikd_satker_id_sikd_satker = null;
                $value1->sikd_satker_kode = null;
                $value1->sikd_satker_nama = null;
                $value1->sikd_sub_skpd_kode = null;
                $value1->sikd_sub_skpd_id_sikd_sub_skpd = null;
            }
            $value1->sikd_sub_skpd_id_nama = 'SKPD INDUK';
        }

        return $rkpdReports;*/
    }
}