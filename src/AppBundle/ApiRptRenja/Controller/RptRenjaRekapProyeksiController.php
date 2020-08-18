<?php
namespace AppBundle\ApiRptRenja\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use AppBundle\Lib\LivedRestClient;

class RptRenjaRekapProyeksiController extends Controller
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
        $jnsRpt = $this->request->query->get('jns_report');
    	$jnsRenja = $this->request->query->get("jns_renja");
        $idRenja = $this->request->query->get("id_renja");
        $idSatker = $this->request->query->get("sikd_satker_id");
        $tahun = $this->request->query->get("tahun");
        $idSubUnit = $this->request->query->get("sikd_sub_skpd_id");
		$jnsRek = $this->request->query->get("jns_rek");
		$param = [
            'jns_report' => $jnsRpt, 
            'id_renja' => $idRenja, 
            'tahun' => $tahun , 
            'sikd_satker_id' => $idSatker, 
            'sikd_sub_skpd_id' => $idSubUnit, 
            'jns_renja' => $jnsRenja, 
            'jns_rek' => $jnsRek
        ];


        $this->restClient->setBaseUri($this->uriRestRenja);
        $renjaReports = $this->restClient->getCollection("$tahun/renjareports", $param);
        return $renjaReports;
		
        /*$tahun = $param['tahun'];
        $idSatker = $param['sikd_satker_id'];
        $idSubUnit = $param['sikd_sub_skpd_id'];
        $jnsRek = $param['jns_rek'];

        //print_r($param);exit;
        
        $this->restClient->setBaseUri($this->uriRestRenja);
        $renjaReports = $this->restClient->getCollection("$tahun/renjareports", $param);
        //print_r($renjaReports);exit;
        $renjaRepHandler = $renjaReports;
        
        //CONTAINS RENJA MATA ANGG SIKD REK RINCI OBJ ID
        $mapRenjaMtAggSikdRekRinciObjId = [];
        $i = 0;
        foreach ($renjaRepHandler as $renjaMtAggSikdRekRinciObjId) {
            $mapRenjaMtAggSikdRekRinciObjId[$i] =  $renjaMtAggSikdRekRinciObjId->renja_mata_anggaran_sikd_rek_rincian_obj_id;
            $i++;
        }

        //print_r($mapRenjaMtAggSikdRekRinciObjId);exit;

        $nmSatker = '';
        $this->restClient->setBaseUri($this->uriRestSikd);
        $satker = $this->restClient->getCollection("$tahun/sikdsatkers/$idSatker");
        if ( $satker){
            $nmSatker = $satker->nama;
        }
        
        //GET SUB SKPD'S INFO
        $nmSubSkpd = 'Semua Sub Unit'; $kdSubSkpd = '';
        if($idSubUnit != ''){
            $this->restClient->setBaseUri($this->uriRestSikd);
            $subSkpd = $this->restClient->getCollection("$tahun/sikdskpds/$idSatker/sikdsubskpds/$idSubUnit");
            if ($subSkpd && sizeof($subSkpd) > 0){
                $nmSubSkpd = $subSkpd->nama;
                $kdSubSkpd = $subSkpd->kode;
            }
        }
        $satkerType = 'SikdSkpd';
        
        //return $satker;
//         $mapKdRek = [];
//         foreach ($renjaReports as $kdrek) {
//             $mapKdRek[] = substr($kdrek->kd_rekening,0,1);
//         }
//         $param2 = ['id' => $mapKdRek]; return $param2;

//         $sikdRenja = $this->restClient->getCollection("$tahun/sikdrenjareports", $param2);
        //CONTAINS SIKD RINCIAN OBJ'S INFO
        //print_r($this->uriRestSikd);exit;
        $param2 ['sikd_rek_rinc_obj'] = $mapRenjaMtAggSikdRekRinciObjId;
        $ids = implode(',', $param2["sikd_rek_rinc_obj"]);
        $paramIn = ['ids' => $ids];$paramIn ['parents']=4;
        $sikdRenja = $this->restClient->getCollection("$tahun/sikdrekrincianobjs");
        //print_r($sikdRenja);exit;
        $mapSikdRekRincObj = $this->populateSikdInfo($sikdRenja);
        
        $renjaRekapProyeksi = array();
        foreach ($renjaReports as &$value1) {
                    $value1->sikd_rek_rincian_obj_id_sikd_rek_rincian_obj = $value1->kd_rekening;
                    //$value1->id_renja_renja = $value1->id_renja_renja;
                    $value1->sikd_satker_nama = $nmSatker;
                    $value1->sikd_sub_skpd_nama = $nmSubSkpd;
                    $value1->satker_type = $satkerType;
                    $value1->renja_pendapatan_akun = $mapSikdRekRincObj['rek_rinc_obj'][$value1->renja_mata_anggaran_sikd_rek_rincian_obj_id]['kd_rek_akun'];
                    $value1->renja_pendapatan_nm_akun = $this->getRenjaPndptnNmAkun(substr($value1->kd_rekening, 0,1),'');
                    $value1->sikd_rek_akun_kd_rek_akun = $mapSikdRekRincObj['rek_rinc_obj'][$value1->renja_mata_anggaran_sikd_rek_rincian_obj_id]['kd_rek_akun'];
                    $value1->sikd_rek_akun_nm_rek_akun = $mapSikdRekRincObj['rek_rinc_obj'][$value1->renja_mata_anggaran_sikd_rek_rincian_obj_id]['nm_rek_akun'];
                    $value1->jns_rek = $this->getRenjaPndptnNmAkun(substr($value1->kd_rekening, 0,1),'jns_rek');
                    $value1->sikd_rek_kelompok_id_sikd_rek_kelompok = $mapSikdRekRincObj['rek_rinc_obj'][$value1->renja_mata_anggaran_sikd_rek_rincian_obj_id]['id_sikd_rek_klpk'];
                    $value1->sikd_rek_kelompok_kd_rek_kelompok = substr($mapSikdRekRincObj['rek_rinc_obj'][$value1->renja_mata_anggaran_sikd_rek_rincian_obj_id]['kd_rek_klpk'],0,1).'.'
                        .substr($mapSikdRekRincObj['rek_rinc_obj'][$value1->renja_mata_anggaran_sikd_rek_rincian_obj_id]['kd_rek_klpk'],1,1);
                    $value1->sikd_rek_kelompok_nm_rek_kelompok = $mapSikdRekRincObj['rek_rinc_obj'][$value1->renja_mata_anggaran_sikd_rek_rincian_obj_id]['nm_rek_klpk'];
                    $value1->sikd_rek_jenis_id_sikd_rek_jenis = $mapSikdRekRincObj['rek_rinc_obj'][$value1->renja_mata_anggaran_sikd_rek_rincian_obj_id]['id_sikd_rek_jenis'];
                    $value1->sikd_rek_jenis_kd_rek_jenis = substr($mapSikdRekRincObj['rek_rinc_obj'][$value1->renja_mata_anggaran_sikd_rek_rincian_obj_id]['kd_rek_jenis'],0,1).'.'.
                        substr($mapSikdRekRincObj['rek_rinc_obj'][$value1->renja_mata_anggaran_sikd_rek_rincian_obj_id]['kd_rek_jenis'],1,1).'.'.
                        substr($mapSikdRekRincObj['rek_rinc_obj'][$value1->renja_mata_anggaran_sikd_rek_rincian_obj_id]['kd_rek_jenis'],2,1);
                    $value1->sikd_rek_jenis_nm_rek_jenis = $mapSikdRekRincObj['rek_rinc_obj'][$value1->renja_mata_anggaran_sikd_rek_rincian_obj_id]['nm_rek_jenis'];
                    $value1->sikd_rek_obj_id_sikd_rek_obj = $mapSikdRekRincObj['rek_rinc_obj'][$value1->renja_mata_anggaran_sikd_rek_rincian_obj_id]['id_sikd_rek_obj'];
                    $value1->sikd_rek_obj_kd_rek_obj = substr($mapSikdRekRincObj['rek_rinc_obj'][$value1->renja_mata_anggaran_sikd_rek_rincian_obj_id]['kd_rek_obj'],0,1).'.'.
                        substr($mapSikdRekRincObj['rek_rinc_obj'][$value1->renja_mata_anggaran_sikd_rek_rincian_obj_id]['kd_rek_obj'],1,1).'.'.
                        substr($mapSikdRekRincObj['rek_rinc_obj'][$value1->renja_mata_anggaran_sikd_rek_rincian_obj_id]['kd_rek_obj'],2,1).'.'.
                        substr($mapSikdRekRincObj['rek_rinc_obj'][$value1->renja_mata_anggaran_sikd_rek_rincian_obj_id]['kd_rek_obj'],3,2);
                    $value1->sikd_rek_obj_nm_rek_obj = $mapSikdRekRincObj['rek_rinc_obj'][$value1->renja_mata_anggaran_sikd_rek_rincian_obj_id]['nm_rek_obj'];
                    $value1->sikd_rek_rincian_obj_id_sikd_rek_rincian_obj = $mapSikdRekRincObj['rek_rinc_obj'][$value1->renja_mata_anggaran_sikd_rek_rincian_obj_id]['id_sikd_rek_rincian_obj'];
                    $value1->sikd_rek_rincian_obj_kd_rek_rincian_obj = substr($mapSikdRekRincObj['rek_rinc_obj'][$value1->renja_mata_anggaran_sikd_rek_rincian_obj_id]['kd_rek_rincian_obj'],0,1).'.'.
                        substr($mapSikdRekRincObj['rek_rinc_obj'][$value1->renja_mata_anggaran_sikd_rek_rincian_obj_id]['kd_rek_rincian_obj'],1,1).'.'.
                        substr($mapSikdRekRincObj['rek_rinc_obj'][$value1->renja_mata_anggaran_sikd_rek_rincian_obj_id]['kd_rek_rincian_obj'],2,1).'.'.
                        substr($mapSikdRekRincObj['rek_rinc_obj'][$value1->renja_mata_anggaran_sikd_rek_rincian_obj_id]['kd_rek_rincian_obj'],3,2).'.'.
                        substr($mapSikdRekRincObj['rek_rinc_obj'][$value1->renja_mata_anggaran_sikd_rek_rincian_obj_id]['kd_rek_rincian_obj'],5,2);
                    $value1->sikd_rek_rincian_obj_nm_rek_rincian_obj = $mapSikdRekRincObj['rek_rinc_obj'][$value1->renja_mata_anggaran_sikd_rek_rincian_obj_id]['nm_rek_rincian_obj'];
                    $value1->jumlah = doubleval($value1->jumlah);
                    $value1->jml_surplus = doubleval($value1->jml_surplus);
                    $value1->jml_pembiayaan = doubleval($value1->jml_pembiayaan);
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
    
    private function getRenjaPndptnNmAkun ($kdRekAkun,$mode){
        $pndptnDaerah = 'Pendapatan Daerah';
        $blnjDaerah = 'Belanja Daerah';
        $defaultPndptnNmAkun = 'Penerimaan Pembiayaan';
        $jnsRek45 = 'rek_45';
        $jnsRekNon45 = 'rek_6';
        
        $rnjPndptnNmAkun['4']['nmPdptAkun'] = $pndptnDaerah;
        $rnjPndptnNmAkun['4']['jnsRek'] = $jnsRek45;
        $rnjPndptnNmAkun['5']['nmPdptAkun'] = $blnjDaerah;
        $rnjPndptnNmAkun['5']['jnsRek'] = $jnsRek45;
        
        if(array_key_exists($kdRekAkun,$rnjPndptnNmAkun)){
            if($mode!='jns_rek'){
                return $rnjPndptnNmAkun[$kdRekAkun]['nmPdptAkun'];
            }
            return $rnjPndptnNmAkun[$kdRekAkun]['jnsRek'];
        } else {
            if($mode!='jns_rek'){
                return $defaultPndptnNmAkun;
            }
            return $jnsRekNon45;
        }
    }*/
    
}