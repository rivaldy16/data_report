<?php
namespace AppBundle\ApiRptRkpd\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use AppBundle\Lib\LivedRestClient;

class RptRkpdRekapProyeksiController extends Controller
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
        $idRkpd = $this->request->query->get("id_rkpd");
        $tahun = $this->request->query->get("tahun");
        
        $param = [
            'jns_report' => $jnsRpt,
            'id_rkpd' => $idRkpd,
            'tahun' => $tahun
        ];
        
        $this->restClient->setBaseUri($this->uriRestRkpd);
        
        $rkpdReports = $this->restClient->getCollection("$tahun/rkpdreports", $param);
        //SET BASE URI TO SIKD DB
        /*$this->restClient->setBaseUri($this->uriRestSikd);
        
        $rkpdRepHandler = $rkpdReports;
        //CONTAINS SIKD SATKER ID
        $mapRkpdSikdSatkerId = [];
        $i = 0;
        foreach ($rkpdRepHandler as $rkpdRepId) {
            $mapRkpdSikdSatkerId[$i] =  $rkpdRepId->sikd_satker_id;
            $i++;
        }
        //CONTAINS RENJA ANGGARAN / KGTN ID
        $mapRkpdRenjaAnggId = [];
        $i = 0;
        foreach ($rkpdRepHandler as $rkpdRepId) {
            $mapRkpdRenjaAnggId[$i] =  $rkpdRepId->renja_anggaran_id;
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
        
        //SET BASE URI TO RENJA DB
        $this->restClient->setBaseUri($this->uriRestRenja);
        //CONTAINS SIKD'S BIDANG INFO
        $ids4 = implode(',',  array_unique($mapRkpdRenjaAnggId));
        $paramIn4 = ['ids' => $ids4];
        $renjaRkpdInfo = $this->restClient->getCollection("$tahun/renjablnjlangsungs", $paramIn4);
        $mapRenjaRkpdInfo = $this->populateRenjaInfo($renjaRkpdInfo);
        //         return $mapRenjaRkpdInfo;
        foreach ($rkpdReports as &$value1) {
            $value1->sikd_satker_kode		    = $mapSikdSatkerMstr['satker'][$value1->sikd_satker_id]['kode'];
            $value1->sikd_satker_nama		    = $mapSikdSatkerMstr['satker'][$value1->sikd_satker_id]['nama'];
            if($value1->sikd_sub_skpd_id==''){
                $value1->sikd_sub_skpd_nama     = $this->sikd_sub_skpd_nama;
                $value1->sikd_sub_skpd_kode     = $mapSikdSatkerMstr['satker'][$value1->sikd_satker_id]['kode'];
            } else {
                $value1->sikd_sub_skpd_nama     = $mapSikdSubSkpd['sub_skpd'][$value1->sikd_satker_id][$value1->sikd_sub_skpd_id]['nama'];
                $value1->sikd_sub_skpd_kode     = $mapSikdSubSkpd['sub_skpd'][$value1->sikd_satker_id][$value1->sikd_sub_skpd_id]['kode'];
            }
            if(array_key_exists($value1->renja_anggaran_id,$mapRenjaRkpdInfo['renja'])){
                $value1->renja_kegiatan_tgt_anggaran_thn_ini=doubleval($mapRenjaRkpdInfo['renja'][$value1->renja_anggaran_id]['tgt_anggaran_thn_ini']);
            } else {
                $value1->renja_kegiatan_tgt_anggaran_thn_ini= 0;
            }
            $value1->rkpd_kegiatan_jml_anggaran_rkpd = doubleval($value1->rkpd_kegiatan_jml_anggaran_rkpd);
            if(array_key_exists($value1->sikd_satker_id,$mapSikdSubSkpd['sub_skpd'])){
                $value1->jml_sub_unit = 1;
            } else {
                $value1->jml_sub_unit = 0;
            }
        }*/
        return $rkpdReports;
    }
    
   
   
}